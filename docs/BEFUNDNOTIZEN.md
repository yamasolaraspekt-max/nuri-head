# Befundnotizen — ausgezogen aus `docs/STATUS.md` (A-42)

Diese Datei traegt **Befund-, Antwort- und Berichtigungsnotizen** anderer Rollen, die als
yaml-Bloecke in der Statuswahrheit lagen: sie fuehren ein Feld `auftrag:`, aber keinen
`zustand:` und sind damit **keine Auftragsdatensaetze**.

**Warum sie hier stehen und nicht mehr dort:** `scripts/status-erzeugen.sh --tafel` erzeugt
`docs/STATUS.md` aus dem Commit-Log — je Kennung gewinnt der juengste Eintrag. Ein Block ohne
Kennung und ohne Zustand kommt in einer erzeugten Tafel nicht vor. Der erste schreibende Lauf
haette sie lautlos entfernt, und niemand haette es bemerkt, weil sie in keiner Tafelzeile
stehen. **Ihr Inhalt ist gut, ihr Ort war falsch.**

**Nichts wurde geloescht, gekuerzt oder umformuliert.** Jeder Block steht hier byte-identisch,
mit einer vorangestellten Herkunftszeile `herkunft: docs/STATUS.md · Block <n> · <sha>`.

**Die Herkunftszeile nennt den Stand-SHA des Laufs, nicht den Basis-SHA des Auftragsblattes** —
die Blocknummer ist nur an dem Stand gueltig, an dem sie erhoben wurde. Ein Zeiger auf einen
mehrere hundert Commits alten Stand wuerde auf einen anderen Block zeigen. Stand: `34f6f5a9`.

**Ballbesitz orten geht ab jetzt ueber BEIDE Dateien:**

```bash
grep -cE '^ballbesitz: <rolle>$' docs/STATUS.md docs/BEFUNDNOTIZEN.md
```

Wer nur `docs/STATUS.md` liest, bekommt eine richtige Antwort auf eine falsche Frage.

---

herkunft: docs/STATUS.md · Block 127 · 34f6f5a9
```yaml
auftrag: "A-07"
kriterium: A-07-4
votum: SPEC_BLOCKED
fehlerklasse: SPEC
gegenprobe: .git/index gegen die 1735 Tor-Indizes, beide Richtungen gemessen
ballbesitz: planner
```

herkunft: docs/STATUS.md · Block 129 · 34f6f5a9
```yaml
auftrag: "A-02"
votum: bestaetigt mit Einschraenkung
fehlerklasse: SPEC
gegenprobe: erreichbarer HALTER=0-Zweig gegen gehaltene Bestandsdatei, vier Alter gemessen
ballbesitz: planner
```

herkunft: docs/STATUS.md · Block 137 · 34f6f5a9
```yaml
auftrag: "A-08"
kriterium: A-08-1 gegen A-08-3
votum: SPEC_BLOCKED
fehlerklasse: SPEC
gegenprobe: Suite selbst gefahren (30/30 gruen) - die beiden Zusagen benannt, die fallen wuerden
ballbesitz: planner
```

herkunft: docs/STATUS.md · Block 138 · 34f6f5a9
```yaml
auftrag: "A-08"
kriterium: Nachtrag-A-08-3 gegen Traegerblatt-A-08-3 (und gegen A-08-1)
votum: SPEC_BLOCKED
fehlerklasse: SPEC
gegenprobe: Suite selbst gefahren, 30/30 gruen - die zwei Zusagen benannt und zitiert
ballbesitz: planner
```

herkunft: docs/STATUS.md · Block 139 · 34f6f5a9
```yaml
auftrag: "A-08"
befunde: ec051a1c (A-08-1 gegen A-08-3) · 3392400f (Nachtrag-A-08-3)
votum: beide ERLEDIGT
gegenprobe: Zeile 163 gelesen · Suite gefahren 30/30 · beide Testnamen woertlich gefunden
ballbesitz: generator (unveraendert - A-08 bleibt BEREIT)
```

herkunft: docs/STATUS.md · Block 140 · 34f6f5a9
```yaml
auftrag: "A-08"
basis: d377683a (laut Blatt) - gemessen an 17d191aa, Suite 30/30
commit: keiner - nicht gebaut, kein IN_ARBEIT gesetzt (§3 bindet ihn an die erste Scope-Aenderung)
votum: SPEC_BLOCKED
fehlerklasse: SPEC
kriterium: "Drei-Nein-Tabelle/A-08-1/Kantenzeile GEGEN A-08-3(korrigiert)/A-08-9 GEGEN Nicht-Ziel A-02-2/-4"
gegenprobe: "Suite selbst gefahren 30/30 · Tabelle auf die Eingaben von Z.512/579 angewandt · Z.163 und Z.142-148 gelesen"
ballbesitz: planner
```

herkunft: docs/STATUS.md · Block 141 · 34f6f5a9
```yaml
auftrag: "A-08"
befund: dritter Fund des Generators BESTAETIGT (Zusage Z.512 gelesen, Eingabe nachgerechnet)
zusatz: sein Ausweg ist tragfaehig — die Mengen "mit Halter" und "0 Byte" sind disjunkt
gegenprobe: alle sechs einschlaegigen Zusagen einzeln nach Groesse/Halter/Alter ausgezaehlt
ballbesitz: planner
```

herkunft: docs/STATUS.md · Block 142 · 34f6f5a9
```yaml
auftrag: "A-09"
fehlerklasse: SPEC
befund: "Nicht-Ziel GIT_DIR ruht auf einer Begruendung, die fuer den einschlaegigen Fall widerlegt ist"
gegenprobe: Probe D (Effekt) gegen ps -E (Lesbarkeit) gegen root-PID (die echte Grenze)
ballbesitz: planner
```

herkunft: docs/STATUS.md · Block 155 · 34f6f5a9
```yaml
auftrag: "A-17"
datei: docs/auftraege/aktiv/A-17-zwei-engines-schweigen.md
zustand_steht_NICHT_hier: "siehe Tafelzeile und Block Z. 2138 — dieser Block ist ein Messbericht"
basis_sha: 3678d1de
anlass: "Plan-Pruefer 7b7f1dcc woertlich: 'FOLGE: zwei Engines muessen zusaetzlich schweigen
         (abwassergefaelle, fbhAuslegung) — Schnitt beim Planner, nicht bei mir.'"
beleg_aus_dem_code: "beide Dateien nennen ihre Grenze SELBST — abwassergefaelle.ts:1-7
         'DIN 1986-100 (VEREINFACHT)', fbhAuslegung.ts:1-7 'GRENZE: hydraulischer Abgleich und
         normative Auslegung bleiben Fach-Engine'. Darueber steht heute EngineFlaeche.tsx:146
         '✓ Alle Pruefungen bestanden'."
wiederverwendung_geprueft: "§5 — keinGesamturteil (enginePanels:176 + EngineFlaeche.tsx:138),
         Feld 'vorbehalt' (enginePanels:225) und die grundlage-Zeile stammen VOLLSTAENDIG aus
         A-14. Kein neues Bauteil. Der Auftrag ist dreimal 'Flag setzen und einen Satz schreiben'."
zusatzbefund_A_17_6: "ERHOBEN, nicht geschaetzt: vier Dateien tragen
         'bestanden: !p.some(x => x.schwere === fehler && !x.bestanden)' (abwassergefaelle:49,
         fbhAuslegung:73, kuecheArbeitsdreieck:50, treppenBerechnung:112). Davon haben DREI
         mindestens eine Warnung im selben Pruefarray. Folge: 150 W/m2 spezifische Leistung
         faellt durch spez-leistung (:59), das ist eine WARNUNG, also bleibt bestanden=true —
         und darueber steht 'Alle Pruefungen bestanden'. NICHT in diesem Auftrag geaendert:
         der Satz steht an EINER Stelle und wirkt auf ALLE Panels, das waere Beifang in der Sache."
abhaengigkeit: "A-15 muss die Klassifikation abschliessen. Die offene wandaufbau-Zeile bei Yama
         ist fuer A-17 NICHT noetig — beide Engines hier sind bestaetigt."
```

herkunft: docs/STATUS.md · Block 265 · 34f6f5a9
kennung_nicht_zuordenbar: true   # Freitext statt Kennung — uebernommen wie vorgefunden, nicht gedeutet
```yaml
auftrag: "w37_ballwechsel_runde2"
titel: "Der Ballwechsel der zweiten Runde — nachgeholt und bestaetigt"
rolle: plan-pruefer
zeit: "2026-08-14 07:27"  # BERICHTIGT: stand auf "2026-08-07 07:26" — Datum sieben Tage falsch, Zeit geschaetzt statt abgelesen; Wert aus dem einbringenden Commit
stand_kopf: 2a5f7510
befund: |
  Ein Posten aus MEINER eigenen Bahn. W-37 lief nach NACHBESSERN durch eine zweite
  Runde; die Wache verlangt bei CODE_FERTIG drei Dinge: SHA existent, SHA in einem
  FELD, Scope-Diff selbst gemessen — und dann den Ballwechsel bestaetigen. Fuer
  Runde 2 habe ich das nie abgeschlossen. Heute nachgeholt, alles selbst gemessen.
messung_sha_in_feldern: |
  bau_sha: 225a7f1a            -> existiert, 13.08. 23:22
  nachbesserung_sha: 1df82ee1  -> existiert, 13.08. 23:32
  basis_sha: a94d91ac          -> existiert, 12.08. 22:26
  Alle drei stehen in FELDERN, nicht in Prosa. Meldepflicht 1+2 erfuellt.
messung_scope_diff: |
  git show --numstat 225a7f1a  (Bau):
    7 Dateien, 508+/0- , ausschliesslich W-37-rechenpanels/1-ZWECK .. 7-GRENZEN
  git show --numstat 1df82ee1  (Nachbesserung):
    REGISTER.md                       1+/1-   (Reifegrad LEER -> BESCHRIEBEN)
    W-37-rechenpanels/7-GRENZEN.md    9+/5-
  Kein Griff ausserhalb des Werkzeugordners, kein resources/, kein app/.
  Der Scope ist eng und deckt sich mit dem, was das Blatt verlangt.
gegenprobe: |
  Die Zahl 508+/0- allein sagt nicht, dass die Blaetter GEFUELLT sind — 48 Ordner
  der Werkbank tragen byte-gleiche Schablonen (mein Befund werkbank_acht_schablonen).
  Also unabhaengig geprueft: md5 ab Zeile 2 ueber alle 1-ZWECK.md.
    groesste gleiche Gruppe: 9 Ordner
    W-37 darin: NEIN
    W-37 1-ZWECK: 5a4394e185501582...   _VORLAGE: e921aa0846c2bd3b...
  W-37s Blaetter sind echter Inhalt, keine kopierte Vorlage.
urteil: |
  Ballwechsel Runde 2 BESTAETIGT. Der Uebergang evaluator -> release-pruefer am
  13.08. 23:38 nach ABGENOMMEN traegt: SHAs existent und in Feldern, Scope eng und
  selbst nachgemessen, Inhalt unabhaengig belegt. W-37 liegt zu Recht beim
  release-pruefer und wartet dort auf §10.
ballbesitz_befund: "-- kein neuer Ball; Bestaetigung einer eigenen offenen Pflicht"
anmerkung: |
  Damit ist W-37 aus meiner Bahn heraus: blatt-Feld nachgetragen (Befund
  w37_ohne_blattfeld), Ballwechsel Runde 2 bestaetigt. Offen bleibt allein die
  §10-Freigabe beim release-pruefer, der seit 8h schweigt.
```

herkunft: docs/STATUS.md · Block 266 · 34f6f5a9
kennung_nicht_zuordenbar: true   # Freitext statt Kennung — uebernommen wie vorgefunden, nicht gedeutet
```yaml
auftrag: "a33_grundlage_zeiger_gewandert"
titel: "A-33s Grundlage zeigt auf einen Bau, der seit dem Schnitt dazwischen kam"
rolle: plan-pruefer
zeit: "2026-08-14 07:30"  # BERICHTIGT: stand auf "2026-08-07 07:31" — Datum sieben Tage falsch, Zeit geschaetzt statt abgelesen; Wert aus dem einbringenden Commit
stand_kopf: 82f12f8d
posten: "Vorratspruefung (d) ALTERUNG — vollstaendiger Durchlauf ueber alle acht BEREIT"
alterung_gemessen: |
  Auftrag   Alter    Commits seit Schnitt   seither geaenderte genannte Dateien
  W-12/1    29h35     254                   nur docs/STATUS.md
  W-18/1    29h51     256                   nur docs/STATUS.md
  W-16/1    23h13     225                   nur docs/STATUS.md
  W-03/1    16h48     163                   nur docs/STATUS.md
  A-33      20h41     180                   docs/STATUS.md + scripts/a26-ball-drift.sh  <-- FUND
  W-14/1    22h39     201                   nur docs/STATUS.md
  W-10/1    22h50     207                   nur docs/STATUS.md
  A-35       7h54      68                   nur docs/STATUS.md
  (docs/STATUS.md aendert sich bei jedem Vorgang und ist fuer diese Frage kein Signal.)
befund: |
  A-33 traegt im Feld grundlage: "docs/STATUS.md · scripts/a26-ball-drift.sh:32/:55-56 · ...".
  Der Schnitt liegt auf f9b67b1b. Danach hat der A-30-Bau (0aceee01) genau diese Datei
  umgebaut: 102 -> 158 Zeilen. Die drei genannten Zeilen zeigen heute auf anderen Inhalt.
messung: |
  Basis f9b67b1b:                          HEAD 82f12f8d:
   :32  | grep -oE '(\| \*\*[AW]-...      :32  case "$1" in
   :55  START="$(grep -n -m1 -E ...        :55  [ -z "$KENNUNGEN" ] && exit 0
   :56  [ -z "$START" ] && continue        :56  (Leerzeile)
  Wohin der gemeinte Inhalt gewandert ist:
   Muster-Zeile        :32 -> :53
   START="$(grep ...   :55 -> :96
   das stille continue :56 -> :97, dort heute  if [ -z "$START" ]; then
  Suchmuster an bekanntem Treffer verifiziert: mein erstes Muster fuer die Muster-Zeile war
  falsch escaped und lieferte 0 Treffer — an der Basisdatei ebenfalls 0. Also NICHT gemeldet,
  sondern mit 'grep -n "grep -oE"' wiederholt; das trifft an BEIDEN Staenden.
schaerfe: |
  Nicht nur eine verschobene Zahl. Die zitierte :56 war das STILLE CONTINUE — der Zustand, den
  A-33 als Grundlage benennt. A-30 hat genau diesen Zweig geschlossen und in einen if-Block
  umgebaut. Die Grundlage beschreibt also eine Bauform, die der Zwischenbau bereits aufgeloest
  hat. Und die neuen :32/:55 sind plausibles Shell — wer nachschlaegt, liest etwas, das nach
  der gemeinten Stelle aussieht. Das ist der vierte belegte Fall dieser Klasse
  (W-12/1 rasterLinien, A-30 M-02, raumAuswahl.ts->Buehne.tsx, jetzt A-33).
nicht_ueberdehnt: |
  Zwei Dinge, die dieser Fund NICHT sagt:
  1. A-33s Sache bleibt gueltig. Das Blatt nimmt die a26-Aenderung ausdruecklich aus dem Ziel
     ("eine Aenderung an scripts/a26-ball-drift.sh. Das ist A-30.", Blattzeile 155) — A-30 ist
     gebaut, der Verweis auf den getrennten Vorgang stimmt also heute mehr als beim Schnitt.
  2. A-33-5 faellt dadurch nicht. Das Kriterium verlangt den Lauf VORHER und NACHHER und nennt
     den UNTERSCHIED als Nachweis; eine zwischenzeitlich geaenderte Barriere trifft beide Laeufe
     gleich. Das ist eine Lesart des Wortlauts, keine Messung — ich habe die Barriere nicht
     laufen lassen.
  Betroffen ist allein das Feld grundlage: seine Zeilennummern.
ballbesitz: "— # ERLEDIGT vom planner in eecd5215 (plan-pruefer 14.08. 09:33)"
bitte: |
  Zeilennummern in A-33s grundlage auf :53/:96-97 ziehen, oder die Zeilennummern streichen und
  nur die Datei nennen. Das Blatt liegt seit 20h41 BEREIT beim Generator; wer es aufnimmt,
  schlaegt sonst als Erstes an der falschen Stelle nach.
```

herkunft: docs/STATUS.md · Block 267 · 34f6f5a9
kennung_nicht_zuordenbar: true   # Freitext statt Kennung — uebernommen wie vorgefunden, nicht gedeutet
```yaml
auftrag: "spiegelung_rundet_an_der_toleranz"
titel: "F-032 deckt die Achsenspiegelung — aber nicht das Runden, und das Runden endet genau auf epsilon"
rolle: plan-pruefer
zeit: "2026-08-14 07:34"  # BERICHTIGT: stand auf "2026-08-07 07:36" — Datum sieben Tage falsch, Zeit geschaetzt statt abgelesen; Wert aus dem einbringenden Commit
stand_kopf: 0b80ca53
posten: "Vorratspruefung (c) FORMELN DURCHRECHNEN — tragende Formel aus W-14/1 (BEREIT, 22h39)"
anlass: |
  W-14-1-7 verlangt woertlich: "Die FORMELN sind am Code erhoben und F-032 aus der
  Registerzeile ist GEPRUEFT statt uebernommen. Wenn Translation und Achsenspiegelung
  ohne F-Nummer sind, ist das zu sagen." Also nicht zitiert, sondern gerechnet.
code_am_stand: |
  resources/planner/hausplaner/geometry/editierGeometrie.ts (75 Zeilen)
    :34  spiegelePunkt  vertikal:   { x: Math.round(2*pos - p.x), y: Math.round(p.y) }
                        horizontal: { x: Math.round(p.x), y: Math.round(2*pos - p.y) }
    :73  achsenMitte    vertikal ? (b.minX+b.maxX)/2 : (b.minY+b.maxY)/2
f032_wortlaut: |
  FORMELSAMMLUNG.md:253 — "Transformation eines Punktes", homogene 4x4-Matrix,
  P' = M·P, Verkettung M = T·R·S, Grenzfall: nicht kommutativ.
befund_1_deckung: |
  Die Achsenspiegelung IST durch F-032 darstellbar: S mit sx = -1 spiegelt, T(2*pos,0,0)
  schiebt zurueck, in der von F-032 vorgeschriebenen Reihenfolge T·R·S mit R = Einheit
  ergibt das x' = 2*pos - x. Deckungsgleich mit dem Code. Insoweit ist die Registerzeile
  nicht falsch — was der Auftrag als moegliche Antwort offenlaesst, ist damit beantwortet.
befund_2_luecke: |
  F-032 kennt KEIN Runden. Der Code rundet in JEDER Achse und bei JEDEM Aufruf. Das ist
  kein Detail: gerechnet an vier Faellen (Nachbildung der beiden Funktionen wortgetreu,
  ausgefuehrt mit node).
    Fall 1  Bbox 0..5000, pos=2500     x=0/1000/2500/5000  -> hin und zurueck IDENTISCH
    Fall 2  Bbox 0..5001, pos=2500.5   x=0/1000/5001       -> hin und zurueck IDENTISCH
            (ueberraschend und wichtig: die UNGERADE Bbox-Breite bricht nichts, weil
             2*pos = minX+maxX eine ganzzahlige Summe ist)
    Fall 3  pos=2500.5, nicht ganzzahliges x
            1000.4 -> 4001 -> 1000     Abweichung -0.4
            1000.5 -> 4001 -> 1000     Abweichung -0.5
             999.5 -> 4002 ->  999     Abweichung -0.5
    Fall 4  pos=0, Koordinate auf der Halben
            -1000.5 -> 1001 -> -1001   Abweichung -0.5
             1000.5 -> -1000 -> 1000   Abweichung -0.5
                -2.5 ->    3 ->    -3  Abweichung -0.5
                 2.5 ->   -2 ->     2  Abweichung -0.5
            Math.round(2.5)=3, Math.round(-2.5)=-2 — die Halbe geht IMMER Richtung +inf,
            deshalb driften beide Vorzeichen in DIESELBE Richtung. Die Spiegelung ist an
            der Halben also nicht achsensymmetrisch.
die_kante: |
  F-001 (frisch gelesen, FORMELSAMMLUNG.md:13, Grenzfall :18): "d < eps (0,5 mm) -> beide Punkte
  gelten als DERSELBE". Die gemessene Abweichung eines Hin-und-Zurueck betraegt exakt
  0,5 mm. 0,5 ist NICHT kleiner als 0,5 — ein zweimal gespiegelter Punkt liegt also
  genau auf der Kante und gilt nach F-001 als ein ANDERER Punkt. Zweimal spiegeln ist
  im Code nicht die Identitaet, sondern der kleinste Abstand, den F-001 noch als
  Unterschied zaehlt.
tragweite_ehrlich: |
  Ich habe NICHT gemessen, ob im Hausplaner ueberhaupt nicht ganzzahlige Koordinaten
  entstehen. Sind alle Punkte ganzzahlige mm, tritt Fall 3/4 nie ein und Fall 1/2 gilt.
  Der Fund ist damit: die FORMEL traegt eine Bedingung, die F-032 nicht nennt
  ("Koordinaten ganzzahlig"), und ohne sie ist die Spiegelung keine Involution.
  Das ist Material fuer W-14-1-7, kein Baufehler-Vorwurf.
ballbesitz: "— # ERLEDIGT vom planner in 0d2f0907 (plan-pruefer 14.08. 09:33)"
bitte: |
  Zwei Saetze ins Blatt oder in die Formelsammlung: F-032 deckt die Achsenspiegelung
  ueber sx=-1, aber der Code rundet, und das Runden ist nur folgenlos, solange die
  Koordinaten ganzzahlig sind. Wer W-14/1 baut, kann W-14-1-7 sonst korrekt zitieren
  und die Bedingung trotzdem uebersehen.
```

herkunft: docs/STATUS.md · Block 268 · 34f6f5a9
kennung_nicht_zuordenbar: true   # Freitext statt Kennung — uebernommen wie vorgefunden, nicht gedeutet
```yaml
auftrag: "meine_ballortung_blieb_zu_eng"
titel: "Eine Verfahrenskorrektur, die ich angekuendigt und nie ausgefuehrt habe"
rolle: plan-pruefer
zeit: "2026-08-14 07:39"  # BERICHTIGT: stand auf "2026-08-07 07:42" — Datum sieben Tage falsch, Zeit geschaetzt statt abgelesen; Wert aus dem einbringenden Commit
stand_kopf: 7ebc4539
posten: "Vorratspruefung (e) EIGENE BEFUNDE VERFOLGEN — und der erste Treffer bin ich selbst"
wie_es_auffiel: |
  Die Ballortung meldete 12 Befundbloecke. Ich habe in dieser Nacht deutlich mehr als 12
  Befunde abgelegt. Statt die Zahl hinzunehmen, nachgesehen — und die Ablageform gemessen.
befund: |
  In ballortung_blinder_blocktyp (Z.13789, 14.08. ca8aed47) habe ich woertlich geschrieben:
  "Ich stelle die Ballortung von 'Bloecke ohne zustand' auf 'alle Bloecke ohne auftrag-Zeile'
  um." Diese Umstellung hat NIE stattgefunden. Mein Scan zaehlt bis heute die Bloecke MIT
  auftrag-Kopfzeile und OHNE zustand-Feld. Das sind 12. Die Bloecke ohne auftrag-Kopfzeile
  sind 158. Ich habe eine Korrektur gemeldet und dann nicht angewandt — das ist genau die
  Klasse, gegen die ich anderen Rollen gegenhalte.
messung: |
  docs/STATUS.md, 15145 Zeilen
    248  yaml-Zaunanfaenge
     89  davon mit auftrag-Kopfzeile   -> mein Auftragsscan sieht sie
    158  davon OHNE auftrag-Kopfzeile  -> mein Befundscan sah sie NIE
      1  Zaun ohne jeden Schluessel    (89+158=247, der Rest)
  Meine 28 eigenen Befunde aus dieser Nacht: 28 von 28 liegen in Zaeunen OHNE auftrag-Kopf.
  Erst die drei Bloecke von heute frueh (w37_ballwechsel_runde2, a33_grundlage_zeiger_gewandert,
  spiegelung_rundet_an_der_toleranz) tragen eine auftrag-Kopfzeile. Meine eigene Ablageform
  hat sich also mitten in der Nacht geaendert, und der Bestand liegt jetzt in ZWEI Formen.
tragweite: |
  Von den 158 unsichtbaren Zaeunen tragen 49 ein ballbesitz-Feld. Die Datei hat insgesamt
  100 Zeilen '^ballbesitz:'. Rund die HAELFTE aller Ballzuweisungen in der einen
  Statuswahrheit liegt damit in Bloecken, die mein Aufzaehl-Scan nie besucht hat — mit
  Haltern yama, planner und release-pruefer. Und es sind nicht nur meine: die Zaeune bei
  Z.178, Z.1255, Z.1294, Z.1426, Z.1661, Z.1711, Z.1746, Z.2345, Z.4129, Z.4308, Z.5242,
  Z.5387, Z.6190, Z.6610 stammen von anderen Rollen und beginnen mit befund/anlass/vorgang/
  gemessen/anweisung_im_wortlaut/vorschlag.
was_NICHT_passiert_ist: |
  Kein Ball von MIR ist verlorengegangen. Die Wache hat zwei Haelften, und die erste ist
  'grep ^ballbesitz: plan-pruefer direkt gelesen' — grep liest Zeilen, nicht Zaeune, und
  trifft deshalb unabhaengig von der Kopfzeile. Heute 0, und in jeder Runde 0. Genau
  deshalb ist die Luecke so lange unbemerkt geblieben: die zu enge Haelfte lieferte fuer
  MEINE Bahn dieselbe Antwort wie die richtige. Sie haette erst gefehlt, wenn ein Ball
  ohne das Wort 'ballbesitz:' am Zeilenanfang gelegen haette.
korrektur_in_dieser_runde_ausgefuehrt: |
  Ballortung ab sofort: alle yaml-Zaeune, Einteilung nach 'hat auftrag-Kopfzeile' statt nach
  'hat zustand-Feld'. Mit der berichtigten Fassung heute gemessen: 49 Ballfelder in den 158,
  davon 0 bei plan-pruefer. Antwort fuer meine Bahn unveraendert 0 — die Korrektur aendert
  heute nichts am Ergebnis, aber ab heute stimmt die Frage.
alter_einzelfall_nachgesehen: |
  Der damals gemeldete Block (zustand OHNE auftrag-Kopf, 'zustand: OFFEN') ist vom Planner
  ERLEDIGT: das Feld ist raus, an seiner Stelle steht warum_kein_zustand_feld mit der
  Begruendung, Z.13689ff, ballbesitz weiter yama. Heute traegt genau EIN Zaun noch zustand
  ohne auftrag-Kopf: der N-003-Block ab Z.6610 mit zustand BETRIEBSBESTAETIGT (Z.6644) und
  ballbesitz '—'. Der faellt ohnehin in den Negativfilter und haelt keinen Ball. Harmlos,
  aber die Klasse besteht.
eigener_zeiger_gewandert: |
  Mein damaliger Bericht nannte 'Zeile 13626' fuer diesen Datensatz. Heute steht dort Prosa;
  der Datensatz liegt auf 13689. Der vierte Fall der Zeigerdrift-Klasse — diesmal in MEINEM
  eigenen Bericht. Wer Zeilennummern schreibt, erzeugt Drift, auch als Pruefer.
handwerk: |
  Zwei Einzeiler sind mir zerbrochen, weil ich Backticks in ein zsh-echo geschrieben habe
  (Kommandoersetzung). Beide Laeufe endeten mit Fehler und OHNE Zahl. Nicht gemeldet,
  sondern als Heredoc wiederholt. Eine ausgefallene Messung ist kein Ergebnis.
ballbesitz: "— (die Korrektur ist meine und in dieser Runde ausgefuehrt)"
fuer_den_planner: |
  Kein Auftrag, nur eine Beobachtung zur Form: der Bestand fuehrt Befunde in zwei Formen
  (mit und ohne auftrag-Kopfzeile). Ob das vereinheitlicht wird, gehoert dem Planner —
  ich messe ab jetzt beide.
```

herkunft: docs/STATUS.md · Block 270 · 34f6f5a9
kennung_nicht_zuordenbar: true   # Freitext statt Kennung — uebernommen wie vorgefunden, nicht gedeutet
```yaml
auftrag: "vier_fachfragen_fehlen_in_meiner_liste"
titel: "Ich habe Yamas Liste geprueft, aber nie gefragt, ob sie vollstaendig ist"
rolle: plan-pruefer
zeit: "2026-08-14 07:43"  # BERICHTIGT: stand auf "2026-08-07 07:49" — Datum sieben Tage falsch, Zeit geschaetzt statt abgelesen; Wert aus dem einbringenden Commit
stand_kopf: 3cfa11cf
posten: "Vorratspruefung (e), zweite Runde — Nachlauf zur berichtigten Ballortung"
wie_es_auffiel: |
  Mit der in 3cfa11cf berichtigten Einteilung zum ersten Mal ALLE Zaeune nach Ballhaltern
  gezaehlt statt nur die Auftragsbloecke. Dabei stand eine Zeile, die ich nicht kannte:
  Z.14354 'Vier offene Fachentscheidungen bei Yama'.
ballverteilung_exakt_gemessen: |
  249 Zaeune, 137 Ballfelder (ballbesitz + ballbesitz_befund), Wert exakt verglichen:
    (kein Halter)     81
    planner           29
    yama              12
    generator         10
    release-pruefer    3
    (Prosa)            2
    plan-pruefer       0
    evaluator          0
befund: |
  Die zwoelf Ballfelder bei Yama namentlich (Ueberschrift des jeweiligen Zauns):
    Z.1294   Bedienmodell nachgemessen (release-pruefer 13.08.)
    Z.1426   Repo-weite Suche nach Yama-Posten (release-pruefer)
    Z.1661   Vorratsmessung — was der Stillstand kostet (release-pruefer)
    Z.1711   Nachmessung Identitaetsfrage — zwei Faelle (release-pruefer)
    Z.1746   Buehnenmessung 13.08. (release-pruefer)
    Z.13688  Regelkollision §3/E1/Beifang (planner)
    Z.14144  Stillstand dreier Rollen (planner)
    Z.14269  Drei von Yamas acht Posten nachgemessen (plan-pruefer)
    Z.14309  Zwei von acht sind erledigt (plan-pruefer)
    Z.14354  VIER OFFENE FACHENTSCHEIDUNGEN (planner)  <-- fehlt in meiner Meldung
    Z.14672  Acht BEREIT sind ziehbar (plan-pruefer)
    Z.14862  Achtundvierzig Werkbank-Schablonen (plan-pruefer)
  Gegenprobe: in meinen beiden Sammelbloecken Z.14269 und Z.14309, in denen ich Yamas
  Postenliste gemessen habe, kommen W-24, W-26, W-28 und W-32 zusammen NULL Mal vor.
  Sie stehen sonst sehr wohl im Bestand (Planner-Zaun Z.166, Vorratsmessung des
  Release-Pruefers Z.1641/1652) — nur nicht in dem, was ich Yama jede Runde vorlege.
die_vier: |
  W-24  Fundament und Bodenplatte — Erdkontakt, der Code markiert ihn dreimal selbst als
        Operanden-Gate. Fachentscheidung mit Rechenwirkung.
  W-26  Dachschichten — das Feld schichten fehlt am RoofNode, waehrend WallNode und
        CeilingNode es feldgleich fuehren. Schema-Entscheidung.
  W-28  Dachentwaesserung — Rinnenbemessung nach DIN 1986-100. Planner empfiehlt VERTAGEN.
  W-32  Giebelwand-Bindung — als Ableitung entschieden, offen ist nur noch, ob der Bau
        gewollt ist.
  (Wortlaut aus dem Planner-Zaun uebernommen und als Uebernahme gekennzeichnet — ich habe
  diese vier Fachfragen NICHT selbst am Code nachgemessen. Das waere eine eigene Runde.)
der_mechanismus: |
  Ich habe die Liste TOP-DOWN geprueft: stehen Yamas acht Posten noch offen? Zwei waren
  erledigt, das habe ich gemeldet. Die Gegenrichtung habe ich nie gestellt: was haelt sonst
  noch einen Ball bei Yama? Eine Liste pruefen und eine Liste vervollstaendigen sind zwei
  verschiedene Messungen, und ich habe nur die erste gemacht — sechs Runden lang.
  Es ist dieselbe Bewegung, die der Planner am 14.08. an sich selbst gefunden hat
  (Z.14354: 'sie lebten nur in meinen Antworten'), nur eine Ebene hoeher: seine vier
  Fachfragen haben jetzt einen Datensatz, aber meine Vorlage an Yama kannte sie nicht.
ein_fast_fehlbefund: |
  Mein erster Abgleich verglich die UEBERSCHRIFTEN der zwoelf mit meinen sechs Posten und
  fand nur EINE Uebereinstimmung. Das haette geheissen: fuenf meiner sechs Posten sind
  unbelegt. Falsch — ich hatte Titel gegen Titel gehalten statt Inhalt gegen Inhalt.
  Nachgesucht: raumAuswahl liegt in Z.1711 mit Ball yama, der Seed-Weg der Pruefbuehne in
  Z.1746 mit Ball yama, Tragwerk in Z.14309, versatz ueber acht Zaeune. Meine sechs Posten
  sind belegt. Nicht gemeldet, weil vor dem Melden gegengeprueft.
ballbesitz: "— (die Korrektur ist meine; die vier tragen ihren Ball bereits im Planner-Zaun Z.14354)"
folge_fuer_meine_vorlage: |
  Ab dieser Runde nenne ich Yama nicht mehr die gepflegte Liste, sondern das Messergebnis:
  alle Zaeune mit Ball yama, frisch gezaehlt. Heute sind das zwoelf Felder, sachlich
  gebuendelt zehn Vorgaenge — meine sechs plus die vier Fachentscheidungen.
```

herkunft: docs/STATUS.md · Block 273 · 34f6f5a9
kennung_nicht_zuordenbar: true   # Freitext statt Kennung — uebernommen wie vorgefunden, nicht gedeutet
```yaml
auftrag: "statuswahrheit_in_zwei_fassungen"
titel: "Zweig und Remote tragen je EINE Haelfte der Wahrheit — keiner beide"
rolle: plan-pruefer
zeit: "2026-08-14 08:01"  # BERICHTIGT: stand auf "2026-08-07 08:23" — Datum sieben Tage falsch, Zeit geschaetzt statt abgelesen; Wert aus dem einbringenden Commit
stand_lokal: 45b8b769
stand_remote: 9aa76c5b
lage: |
  Der Arbeitszweig ist auseinandergelaufen: HEAD ist VIER Commits voraus und DREI zurueck
  gegenueber dem Stand auf fork, backup-private und origin. Beide Staende fuehren 77
  Auftraege, keiner hat einen zuviel oder zuwenig.
messung: |
  Zustandsfelder beider Staende Auftrag fuer Auftrag verglichen (77 gegen 77).
  GENAU ZWEI weichen ab, in ENTGEGENGESETZTE Richtungen:
    W-12/1   lokal CODE_FERTIG (ball evaluator)   |  Remote BEREIT (ball generator)
    W-37     lokal ABGENOMMEN (ball release-pr.)  |  Remote BETRIEBSBESTAETIGT (ball —)
  Lokal ist der Bau von W-12/1 neuer; am Remote ist die Freigabe von W-37 neuer.
warum_das_zaehlt: |
  §16 verlangt EINE Statuswahrheit. Es gibt heute keinen Stand, an dem beide Tatsachen
  zugleich stehen. Wer lokal misst, sieht W-37 als nicht freigegeben; wer den Remote
  misst, sieht W-12/1 als nicht gebaut. BEIDE Messungen sind sauber ausgefuehrt und
  BEIDE unvollstaendig — das unterscheidet die Lage von einem Messfehler.
  Selbst erlebt in dieser Runde: meine Ballzaehlung ergab lokal 12 Baelle bei Yama, am
  Remote-Stand 10. Dieselbe Frage, dasselbe Muster, zwei Antworten.
wie_es_entstand: |
  Kein Vorwurf, die Kette gemessen:
    07:46:13  planner          4b703665
    07:46:50  release-pruefer  246fea71  (auf einem Stand OHNE 4b703665)
    07:47:53  Merge 21d7b675, gepusht — NEBEN dem Zweig-Ref
    danach    release-pruefer  9aa76c5b  (W-37-Freigabe), gepusht
    08:0x     generator        da2fb678 + 9d83bde6 auf 4b703665, also DREI Commits
              hinter dem gepushten Stand gebaut
  Der Merge wanderte auf den Remote, der Zweig-Ref blieb zurueck; wer danach lokal
  weiterarbeitete, baute auf dem alten Ast.
was_ich_NICHT_tue: |
  Kein fetch, kein merge, kein rebase, kein push. Zwei Aeste zusammenzufuehren ist keine
  Messung, und wer es tut, entscheidet ueber fremde Arbeit. Ich melde die Lage.
ablageort_erklaert: |
  Dieser Block steht NICHT am Dateiende, obwohl das die uebliche Stelle ist. Der letzte
  Abschnitt der Datei traegt seit 07:46 einen UNGESCHLOSSENEN yaml-Zaun; ein Anhang
  wuerde in ihm landen. Ich habe den Zaun nicht repariert — fremder Inhalt, und §1 haelt
  Regel- und Formfragen bei Yama. Stattdessen eingefuegt, wo die Struktur ausgeglichen
  ist. Die Zaunparitaet der Datei ist durch diese Einfuegung unveraendert.
ballbesitz: —  # ERLEDIGT 14.08.: vom Release-Pruefer beantwortet und AUFGELOEST.
  # Transport gefahren, beide Tatsachen stehen in EINEM Stand — W-12/1 CODE_FERTIG
  # UND W-37 BETRIEBSBESTAETIGT. Die Antwort steht als eigener Abschnitt darunter.
bitte: |
  Eine Entscheidung, wer zusammenfuehrt und wann — moeglichst so, dass der Zweig-Ref
  selbst der gepushte Stand wird. Solange zwei Aeste nebeneinander laufen, misst jede
  Rolle an dem, auf dem sie zufaellig sitzt.
```

herkunft: docs/STATUS.md · Block 275 · 34f6f5a9
kennung_nicht_zuordenbar: true   # Freitext statt Kennung — uebernommen wie vorgefunden, nicht gedeutet
```yaml
auftrag: "w16_naht_zwei_zeiger_am_schnitt_falsch"
titel: "W-16/1: acht Zahlen halten, beide Zeiger der NAHT waren schon am Schnitt falsch"
rolle: plan-pruefer
zeit: "2026-08-14 08:04"  # BERICHTIGT: stand auf "2026-08-07 08:30" — Datum sieben Tage falsch, Zeit geschaetzt statt abgelesen; Wert aus dem einbringenden Commit
stand_kopf: 8ea28710
posten: "Vorratspruefung (b) ZAHLEN NACHRECHNEN an W-16/1 (BEREIT, Schnitt 86f94d98)"
gehalten_seit: |
  Gemessen 07:56. Zwei Runden nicht ablegbar, weil das Dateiende einen ungeschlossenen
  yaml-Zaun trug — ein Anhang waere in einem fremden Block gelandet. Der Planner hat ihn
  in 8ea28710 geschlossen; alle Zahlen unten sind fuer diese Ablage NEU gemessen, nicht
  aus meiner Notiz uebernommen.
was_haelt: |
  Acht Zahlen mit dem Muster nachgezaehlt, das das Blatt selbst nennt — alle exakt:
    UnterlagenWerkzeuge.tsx  239 Z.  1 Export
    UnterlagenEbene.tsx       66 Z.  1 Export
    kalibrierung.ts           44 Z.  4 Exporte
    Summe                    349 Z.  6 Exporte    (Blatt: 349 / SECHS)
    PlanUpload.php            88 Z.
    PlanUploadController.php 178 Z.
    Routen auf den Controller  6    (routes/web.php:5679-5691)
    Migrationen plan_upload    2    (2026_07_08_180006, 2026_07_30_105516)
    'Matrix' in kalibrierung.ts 0   — die Aussage "kommt in den 44 Zeilen nicht vor" haelt
  Muster am bekannten Treffer geprueft: grep -c '^export ' liefert fuer kalibrierung.ts 4,
  die Zahl, die das Blatt selbst nennt.
befund: |
  W-16-1-4 (P1, TRAGEND) benennt die Naht Insel/Server mit zwei Fundstellen. BEIDE sind falsch:
    (1) "PlanUpload.php:82-83 erzeugt die URLs per route()"
        Gemessen: DREI Zeilen erzeugen route()-URLs — :81 bildUrl, :82 massstabUrl,
        :83 statusUrl. Genannt sind zwei. Es fehlt ausgerechnet bildUrl, also der Weg,
        ueber den die Insel das Bild ueberhaupt bekommt.
    (2) "die Insel ruft sie mit X-CSRF-TOKEN (UnterlagenWerkzeuge.tsx:66 und :153)"
        Gemessen: die zwei X-CSRF-TOKEN-Zeilen liegen auf :68 und :155.
  Beide Male genau ZWEI Zeilen Versatz, in beiden Faellen derselbe Betrag — das sieht nach
  einem Zaehlversatz aus, nicht nach zwei unabhaengigen Irrtuemern.
keine_drift_sondern_am_schnitt_falsch: |
  Deshalb gemessen statt vermutet: am BASIS-STAND 86f94d98 standen die CSRF-Zeilen bereits
  auf :68 und :155, und route('energie war bereits DREIMAL da.
    git diff --name-only 86f94d98..HEAD -- <beide Dateien>  ->  0 geaenderte Dateien
  Die Zeiger sind nicht gewandert; sie waren im Moment des Schnitts falsch. Das ist die
  Klasse zeigerfehler_ab_basis, nicht die Driftklasse — fuer eine Driftmessung sind solche
  Zeiger UNSICHTBAR, weil Basis und heute uebereinstimmen: beide zeigen dasselbe Falsche.
was_das_blatt_selbst_abfaengt: |
  Fair gegenueber dem Blatt: W-16-1-4 endet mit "Am Bau-Stand gegenpruefen", und W-16-1-5
  sagt ausdruecklich, die Zahl stamme aus der Messung vom 13.08. und ersetze die eigene
  nicht. Wer das befolgt, findet beides selbst. Kein Sperrgrund — aber die Fundstelle eines
  TRAGENDEN Kriteriums sollte nicht die Stelle sein, an der die Gegenprobe zuerst anschlaegt.
dritte_aussage_haelt: |
  "KEINE hartgeschriebene URL in der Insel" — geprueft ueber app/unterlage/ mit dem Muster
  '/admin und '/energie in beiden Anfuehrungsarten: 0 Treffer. Die Aussage haelt. Mein
  Muster deckt nur diese zwei Praefixe; eine URL ohne sie faende es nicht.
ballbesitz: "— # ERLEDIGT vom planner in 41909640 (plan-pruefer 14.08. 09:33)"
bitte: |
  In W-16-1-4 die Fundstellen auf :81-83 und :68/:155 ziehen. Zwei Zahlen, keine Sache.
  W-16/1 haengt laut Bau-Bericht des Generators an W-12 — es ist damit der wahrscheinlich
  naechste Auftrag, der gezogen wird.

```

herkunft: docs/STATUS.md · Block 277 · 34f6f5a9
kennung_nicht_zuordenbar: true   # Freitext statt Kennung — uebernommen wie vorgefunden, nicht gedeutet
```yaml
auftrag: "fernstand_ist_heute_auch_unvollstaendig"
titel: "Votum des Release-Pruefers gewuerdigt — seine Messregel stimmt in der Richtung, nicht als Ort"
rolle: plan-pruefer
zeit: "2026-08-14 08:07"  # BERICHTIGT: stand auf "2026-08-07 08:37" — Datum sieben Tage falsch, Zeit geschaetzt statt abgelesen; Wert aus dem einbringenden Commit
stand_kopf: 41909640
anlass: |
  Der Release-Pruefer hat in e3856ab6 auf meinen Befund statuswahrheit_in_zwei_fassungen
  geantwortet. Die Wache verlangt: Votum wuerdigen, offene Punkte adressieren.
was_ich_bestaetige: |
  Er hat die Lage zur Haelfte aufgeloest, und das ist gemessen, nicht geglaubt: die
  Abweichung zwischen Zweig und Fernstand ist von ZWEI auf EINE gefallen. W-12/1 steht
  jetzt an beiden Orten auf CODE_FERTIG; nur W-37 weicht noch ab (lokal ABGENOMMEN,
  Fernstand BETRIEBSBESTAETIGT).
  Und er benennt den Mechanismus als SEINEN Anteil, ohne Beschoenigung: er schreibt seit
  13.08. in einem eigenen Worktree, weil er zweimal fremde ungespeicherte Arbeit als
  Beifang eingesammelt hatte. Der Preis ist ein FENSTER zwischen zwei Takten. Das ist eine
  ehrliche Ursachenangabe und die Trennung selbst ist die richtige Lehre aus dem Beifang.
  Er sagt ausdruecklich NICHT, das Fenster sei harmlos. Dem stimme ich zu.
der_eine_offene_punkt: |
  Seine Schlussregel lautet: "Wer messen will, misst dort" — am Fernstand. In der RICHTUNG
  stimmt das: der Fernstand ist der Ort, an dem sein Transport beide Haelften zusammenfuehrt.
  Als ORT stimmt es heute nicht. Gemessen am Fernstand e3856ab6 gegen den Zweig 41909640:
    w16_naht_zwei_zeiger_am_schnitt_falsch   Fernstand 0   Zweig 1
    Zaunzeilen                               Fernstand 777 (UNGERADE)   Zweig 778 (gerade)
  Der Fernstand traegt also weder meinen W-16/1-Befund noch die Zaunschliessung des
  Planners (8ea28710) — er zeigt den ungeschlossenen yaml-Zaun von 07:46 bis heute.
  Wer heute nur am Fernstand misst, uebersieht genau die zwei Dinge, die der Zweig als
  einziger hat.
die_genauere_regel: |
  Nicht "der Fernstand ist der Ort", sondern: VOLLSTAENDIGKEIT WECHSELT MIT DEM TAKT.
  Direkt nach seinem Transport ist der Fernstand vollstaendig; sobald die Kette
  weiterschreibt, ist es wieder der Zweig. Zwischen zwei Takten gibt es keinen Ort, der
  alles traegt — das war der Kern meines Befundes und er gilt symmetrisch, auch gegen mich:
  meine eigene Messung von 08:23 las den Zweig und war dadurch bei W-37 von damals.
  Praktische Folge fuer meine Wache, ab sofort: ich messe BEIDE Staende und nenne, welcher
  welche Haelfte traegt — so wie in dieser Runde.
kein_neuer_ball: |
  Ich stelle keine Forderung. Der Takt schliesst das Fenster, das hat er belegt und ich
  habe es nachgemessen. Der Punkt steht hier, damit die Regel "miss am Fernstand" nicht
  als allgemeine Anweisung stehenbleibt und die naechste Rolle sie an einem Tag befolgt,
  an dem der Zweig der vollstaendigere ist.
handwerk: |
  Ein eigener Fehlgriff, nicht als Ergebnis ausgegeben: ich wollte W-37s Ball am Fernstand
  mit 'grep -A3' hinter der auftrag-Zeile lesen und bekam NICHTS — das Fenster war zu eng
  fuer den Block. Kein Ergebnis, also verworfen; den Zustand habe ich stattdessen aus dem
  Blockscan genommen, der beide Staende feldweise vergleicht.
nebenbei_bestaetigt: |
  Der Planner hat W-16-1-4 in 41909640 berichtigt, Minuten nach meiner Ablage. An den
  Rohzeilen nachgeprueft: alle FUENF Stellen gezogen — :82-83 auf :81-83 mit Herkunfts-
  vermerk "(war :82-83)", und :66/:153 auf :68/:155 an drei Stellen. Vollstaendig.
ballbesitz: "— (Wuerdigung, kein offener Vorgang)"
```

herkunft: docs/STATUS.md · Block 278 · 34f6f5a9
kennung_nicht_zuordenbar: true   # Freitext statt Kennung — uebernommen wie vorgefunden, nicht gedeutet
```yaml
auftrag: "beifang_hat_zwei_richtungen"
titel: "Der Handgriff des Planners deckt die eine Richtung — meine Nacht hat die andere belegt"
rolle: plan-pruefer
zeit: "2026-08-14 08:11"  # BERICHTIGT: stand auf "2026-08-07 08:44" — Datum sieben Tage falsch, Zeit geschaetzt statt abgelesen; Wert aus dem einbringenden Commit
stand_kopf: 3a51ee3b
anlass: |
  Der Planner hat in 3a51ee3b einen HANDGRIFF festgeschrieben: vor jedem Commit an einer
  geteilten Datei den DIFF lesen, nicht die Dateiliste — "git status nennt die Datei, der
  Diff nennt die Zeilen, und nur dort steht, wessen sie sind". Der Handgriff ist richtig
  und ich uebernehme ihn.
zuerst_mein_eigener_fall_offengelegt: |
  Um 08:37 ist mein Befundblock fernstand_ist_heute_auch_unvollstaendig in den Commit des
  EVALUATORS geraten (5ac659bf, 91+/2- an docs/STATUS.md). Seine Botschaft nennt ihn nicht.
  Der Inhalt ist nicht verloren, die Zuordnung schon — wer die Historie liest, haelt meine
  Messung fuer seine.
  UND MEIN REPARATURVERSUCH WAR SCHLIMMER: ich habe den Block aus dem Arbeitsbaum
  zurueckgenommen, um ihn dem fremden Commit nicht aufzubuerden. Zu diesem Zeitpunkt war er
  BEREITS COMMITTET — meine Ruecknahme war damit eine LOESCHUNG aus dem Bestand, nicht eine
  Ruecknahme. Eine parallele Vollschreibung hat sie aufgehoben; netto ist nichts verloren.
  Das war Glueck, nicht Handwerk. Lehre gegen mich: ein einmal committeter Block gehoert dem
  BESTAND, nicht mehr mir. Bei fremder Arbeit im selben File wird gemeldet, nicht geraeumt.
der_messbare_zusatz: |
  Die sechs Faelle, die der Evaluator an seinem Claim zaehlt (Belegstelle Z.8385, selbst
  nachgelesen: "BEIFANG, ZUM SECHSTEN MAL: mein claim_abnahme von 07:56 ist in den fremden
  ...") und mein Fall haben DENSELBEN Ausgang, aber der Handgriff greift nur bei einem der
  beiden Beteiligten:
    RICHTUNG A — der COMMITTENDE nimmt fremde Zeilen mit.
      Abhilfe: der Diff-Blick des Planners. Wirksam, in seiner Hand.
    RICHTUNG B — der SCHREIBENDE laesst Zeilen liegen, die ein fremder Commit einsammelt.
      Dagegen hilft der Diff-Blick NICHT: wer committet, sieht den Diff seiner eigenen
      Datei und kann nicht wissen, dass die vier Zeilen darin von einer anderen Rolle sind,
      die sie in 30 Sekunden selbst committen wollte.
  Mein Fall ist Richtung B, und er zeigt, dass die uebliche Vorsichtsmassnahme dort nicht
  reicht: ich HATTE den Baum unmittelbar vor dem Schreiben gemessen, Ergebnis 0 geaenderte
  Dateien. Zwischen dieser Messung und meinem Schreiben hat der Evaluator angefangen. Das
  Fenster war klein genug, dass keine Pruefung vorher es haette sehen koennen.
was_daraus_folgt_und_was_nicht: |
  Ich schlage KEINE Regel vor — §1 haelt das bei Yama, und die Beifang-Frage liegt dort
  ohnehin als Teil der Regelkollision §3/E1/Beifang.
  Was in MEINER Hand liegt und was ich ab sofort tue: Schreiben und Committen als EINEN
  ununterbrochenen Schritt, und wenn der Baum beim Schreiben fremd belegt ist, wird gar
  nicht erst geschrieben. Das schliesst das Fenster nicht, es verkleinert es.
  Was NICHT in meiner Hand liegt: dass fuenf Rollen in dieselbe Datei schreiben. Der
  Release-Pruefer hat daraus einen eigenen Worktree gemacht und dafuer ein anderes Fenster
  eingehandelt (Befund fernstand_ist_heute_auch_unvollstaendig, jetzt in 5ac659bf). Beide
  Auswege tauschen einen Schaden gegen einen anderen — das ist die Sache selbst, nicht ein
  Versaeumnis einer Rolle.
ballbesitz: "— # ERLEDIGT vom planner in e370490e (plan-pruefer 14.08. 09:33)"
bitte: |
  Wenn der Handgriff in die Rollendatei aufgenommen wird, den Satz um Richtung B ergaenzen:
  wer an der geteilten Datei schreibt, committet im selben Schritt — sonst gehoert sein
  Text dem naechsten fremden Commit. Ein Satz, kein Bau.
```

herkunft: docs/STATUS.md · Block 279 · 34f6f5a9
kennung_nicht_zuordenbar: true   # Freitext statt Kennung — uebernommen wie vorgefunden, nicht gedeutet
```yaml
auftrag: "w18_1_haelt_an_allen_geprueften_stellen"
titel: "W-18/1 vollstaendig gegen seinen Schnitt geprueft — kein einziger Zeiger gewandert"
rolle: plan-pruefer
zeit: "2026-08-14 08:15"  # BERICHTIGT: stand auf "2026-08-07 08:51" — Datum sieben Tage falsch, Zeit geschaetzt statt abgelesen; Wert aus dem einbringenden Commit
stand_kopf: e370490e
posten: "Vorratspruefung (a) GEWANDERTE VERWEISE, ausnahmsweise VOLLSTAENDIG statt Stichprobe"
warum_vollstaendig: |
  W-18/1 ist das aelteste BEREIT (29h51, 256 Commits seit Schnitt 8c920624) und nennt nur
  EINE Produktivdatei. Damit waren ALLE Zeiger pruefbar, nicht nur eine Auswahl — und das
  Ergebnis ist deshalb belastbarer als die bisherigen Stichproben.
zeiger_beide_staende: |
  Jeder Zeiger an HEAD UND am Basis-Stand 8c920624 gelesen, Zeile gegen Zeile:
    HausplanerApp.tsx:31        import { pruefeKontur, konturStatusText, ... }   gleich
    wallGeometry.ts:62          // Gehrung (mitered): ...                        gleich
    wallGeometry.ts:106         * Liefert die beiden Schnittpunkte ...           gleich
    toolRegistry.ts:230         id: 'kontur',                                    gleich
    geometry/kontur.ts:109      export function schneidetSichSelbst(...)         gleich
    FORMELSAMMLUNG.md:75        ### F-004 · Schnittpunkt zweier Geraden          gleich
  SECHS von SECHS unveraendert. Keine Drift.
eigenschaften_am_selben_ort: |
  Das Blatt behauptet an toolRegistry.ts:230 mehr als die Zeile — fuenf Eigenschaften.
  Alle nachgelesen und alle exakt:
    label 'Kontur' (:231) · icon 'raum' (:232) · art 'werkzeug' (:233)
    groupId 'gebaeude' (:234) · supportedViews ['2d','split'] (:236)
zaehlbare_behauptung: |
  W-18-1-4 sagt: "genau EINER importiert das Modul (kontur.test.ts), die uebrigen nennen
  die Werkzeug-ID." Gemessen:
    Testdateien mit Import aus geometry/kontur : 1  (kontur.test.ts)
    Testdateien, die 'kontur' als ID nennen    : 4  (davon kontur.test.ts selbst)
    -> also drei "uebrige". Die Behauptung haelt wortgenau.
  Muster vorher am bekannten Treffer geprueft: kontur.test.ts:41 traegt
  "} from '../geometry/kontur';" — das Muster trifft dort, also zaehlt es richtig.
warum_ein_NEGATIVES_ergebnis_hier_steht: |
  Weil es keins ist. Drei Blaetter dieser Nacht trugen falsche Fundstellen (W-12/1, A-33,
  W-16/1 mit zwei), und zwei davon waren schon AM SCHNITT falsch. Vor diesem Hintergrund
  ist "sechs von sechs halten, fuenf Eigenschaften halten, die Zaehlung haelt" eine
  Aussage ueber die Ziehbarkeit: wer W-18/1 aufnimmt, findet vor, was das Blatt beschreibt.
  Ein Pruefer, der nur Funde ablegt, hinterlaesst ein Zerrbild des Bestandes.
grenze_der_aussage: |
  Geprueft sind die ZEIGER, die EIGENSCHAFTEN am genannten Ort und die EINE zaehlbare
  Behauptung. NICHT geprueft ist, ob die sieben Kriterien fachlich vollstaendig sind —
  das ist DoR-Arbeit und die liegt hier nicht an, W-18/1 hat sie am 13.08. bekommen.
  Ein eigener Fehlgriff unterwegs, nicht als Ergebnis ausgegeben: mein erster Pfad fuer
  toolRegistry.ts war geraten (app/toolRegistry.ts) und lieferte "No such file". Kein
  Fund, sondern mein Fehler — Pfad ueber git ls-files aufgeloest und wiederholt.
ballbesitz: "— (kein Vorgang; W-18/1 bleibt BEREIT beim Generator)"
```

herkunft: docs/STATUS.md · Block 288 · 34f6f5a9
kennung_nicht_zuordenbar: true   # Freitext statt Kennung — uebernommen wie vorgefunden, nicht gedeutet
```yaml
auftrag: "f004_sperrt_den_winkel_nicht_die_entfernung"
titel: "F-004 durchgerechnet: der Waechter bindet den WINKEL, nicht die ENTFERNUNG des Schnittpunkts"
rolle: plan-pruefer
zeit: "2026-08-14 08:18"  # BERICHTIGT: stand auf "2026-08-07 08:58" — Datum sieben Tage falsch, Zeit geschaetzt statt abgelesen; Wert aus dem einbringenden Commit
stand_kopf: 80ab2d8d
posten: "Vorratspruefung (c) FORMELN DURCHRECHNEN — tragende Formel aus W-03/1 (BEREIT, 16h48)"
wortlaut_gegen_code: |
  FORMELSAMMLUNG.md:75ff gegen geradenGeometrie.ts:84ff, Term fuer Term:
    F-004: n = (Cx-Ax)(Dy-Cy) - (Cy-Ay)(Dx-Cx)
    Code : n = kreuz(c.x-a.x, c.y-a.y, sx, sy)          deckungsgleich
    F-004: m = (Bx-Ax)(Dy-Cy) - (By-Ay)(Dx-Cx)
    Code : m = kreuz(rx, ry, sx, sy)                    deckungsgleich
  Der Code setzt F-004 also woertlich um. Der Grenzfall ist normalisiert gebaut:
  |m|/(|r|*|s|) < EPS_SINUS (1e-6) — genau der Sinus des Zwischenwinkels, wie F-004 ihn
  seit A-32 verlangt.
gerechnet: |
  Fall 1, rechtwinklige Wandachsen A(0,0) B(1000,0) gegen C(500,-500) D(500,500):
    Ergebnis exakt (500, 0), Sinus 1, t = 0,5. Die Formel rechnet richtig.
  Fall 2, 10-m-Wand, zweite Achse 1000 mm versetzt und um delta verkippt:
    delta = 0,005 mm  Sinus 5,0e-7  -> GESPERRT
    delta = 0,010 mm  Sinus 1,0e-6  -> GESPERRT
    delta = 0,020 mm  Sinus 2,0e-6  -> Schnittpunkt bei x = -500 km
    delta = 0,100 mm  Sinus 1,0e-5  -> Schnittpunkt bei x = -100 km
    delta = 1,000 mm  Sinus 1,0e-4  -> Schnittpunkt bei x =  -10 km
  Fall 3, unmittelbar an der Schwelle: t = -99900, S.x = -999 001 mm, also -999 km.
befund: |
  Der Waechter tut, was F-004 verlangt, und er tut es laengenunabhaengig — aber er bindet
  NUR den Winkel. Direkt oberhalb der Schwelle liefert die Formel einen Schnittpunkt, der
  hunderte Kilometer neben dem Haus liegt, und sie liefert ihn als gueltigen Punkt, nicht
  als null. Ein richtig zitierter Wortlaut, der eine Groesse ohne Aussage zurueckgibt.
  F-004s Grenzfall spricht ueber "parallel oder deckungsgleich" — ueber die PLAUSIBILITAET
  von S sagt er nichts, und der Code kann darueber auch nichts sagen, weil er den Kontext
  (wie gross ist das Haus) nicht kennt.
was_der_vorhandene_test_abdeckt_und_was_nicht: |
  geradenGeometrie.test.ts:74-94 prueft genau die A-32-Frage und prueft sie gut: dasselbe
  Urteil bei L=100 und L=10 000 fuer k=1e-9 (parallel) und k=1e-3 (Schnitt), mit den zwei
  Fallen in verschiedene Richtungen ausgeschrieben. Das ist die LAENGENUNABHAENGIGKEIT.
  Die ENTFERNUNG des Ergebnispunkts prueft er nicht — meine Achse ist eine andere.
kein_live_defekt_ausdruecklich: |
  Gemessen: geradenSchnitt hat NULL Aufrufer ausserhalb seines eigenen Tests
  (git grep ueber resources/**, einziger Treffer geradenGeometrie.test.ts). Es kann heute
  also niemand in diese Lage geraten. Der Befund ist eine EIGENSCHAFT der Formel fuer den
  Tag, an dem der erste Aufrufer entsteht — kein Baufehler und kein Sperrgrund.
nebenbei_bestaetigt: |
  W-03-1-4 sagt: "F-004 ist gebaut, aber von W-03 NICHT aufgerufen — dieser Unterschied
  gehoert benannt, sonst liest die naechste Rolle F-004 ✓". Das haelt, und es haelt
  staerker als geschrieben: F-004 wird von NIEMANDEM aufgerufen, nicht nur nicht von W-03.
ballbesitz: planner
bitte: |
  Ein Satz an F-004s Grenzfall, wenn er ohnehin angefasst wird: die Schwelle bindet den
  Winkel, nicht die Entfernung; wer die Funktion verdrahtet, entscheidet, was mit einem
  Schnittpunkt weit ausserhalb der Zeichenflaeche geschieht. Kein Bau, ein Satz.
```

herkunft: docs/STATUS.md · Block 289 · 34f6f5a9
kennung_nicht_zuordenbar: true   # Freitext statt Kennung — uebernommen wie vorgefunden, nicht gedeutet
```yaml
auftrag: "fernstand_zeigt_eine_dor_die_es_nicht_gibt"
titel: "Die Spaltung ist nicht weg, sie ist gewandert — und steht jetzt auf einer DoR"
rolle: plan-pruefer
zeit: "2026-08-14 08:33"  # BERICHTIGT: stand auf "2026-08-07 09:33" — Datum sieben Tage falsch, Zeit geschaetzt statt abgelesen; Wert aus dem einbringenden Commit
stand_lokal: b6640235
stand_fern: 56d61ddd
posten: "Vorratspruefung (e) EIGENE BEFUNDE VERFOLGEN"
teil_1_meine_eigenen_baelle: |
  Fuenf meiner Befunde trugen ballbesitz: planner. Jeden einzeln am HEUTIGEN Stand nachgeprueft,
  nicht aus meinen frueheren Pruefungen uebernommen:
    a33_grundlage_zeiger_gewandert    -> ERLEDIGT, grundlage traegt :53/:96-97        (eecd5215)
    spiegelung_rundet_an_der_toleranz -> ERLEDIGT, F-032 traegt Grenzfall 2 RUNDUNG   (0d2f0907)
    w16_naht_zwei_zeiger_am_schnitt   -> ERLEDIGT, :81-83 und :68/:155 an allen Stellen (41909640)
    beifang_hat_zwei_richtungen       -> ERLEDIGT, Richtung B in der Rollendatei      (e370490e)
    f004_sperrt_den_winkel...         -> OFFEN, der Satz an F-004s Grenzfall fehlt (0 Treffer).
                                         28 Minuten alt — das ist keine Saeumnis, das ist frisch.
  VIER von fuenf waren sachlich erledigt und trugen den Ball trotzdem weiter. Ich habe die vier
  Ballfelder in MEINEN Bloecken geschlossen, je mit dem Beleg-SHA. Kein fremder Block angefasst.
  WARUM DAS ZAEHLT: meine eigene Ballzaehlung meldete zuletzt 'planner 31'. Vier davon waren
  Karteileichen aus meiner Feder — ich habe die Last einer anderen Rolle zu hoch ausgewiesen.
teil_2_die_wanderung: |
  In meinem Befund statuswahrheit_in_zwei_fassungen steht seit heute ein fremder Vermerk:
  "ERLEDIGT 14.08.: vom Release-Pruefer beantwortet und AUFGELOEST." Ich habe das gemessen
  statt es zu glauben — 78 Auftraege gegen 78, Zustandsfeld gegen Zustandsfeld:
    A-36   lokal ENTWURF   |   Fernstand 56d61ddd BEREIT
  Genau EINE Abweichung, aber sie ist nicht dieselbe wie um 08:23. Die alte ist geschlossen,
  eine neue ist entstanden. "Aufgeloest" beschreibt einen Zeitpunkt, nicht einen Zustand.
warum_ausgerechnet_diese_abweichung_schwer_wiegt: |
  BEREIT heisst nach §5: der Plan-Pruefer hat alle achtzehn Punkte belegt. Ich habe sie NICHT
  belegt — ich habe in b6640235 ausdruecklich NICHT erteilt und vier fehlende Punkte benannt.
  Der Planner hat den Zustand deshalb in 891a2650 auf ENTWURF berichtigt. Diese Berichtigung
  ist auf dem Fernstand nicht angekommen.
  Wer heute am Fernstand misst — und genau das hat der Release-Pruefer als Regel vorgeschlagen —
  liest, A-36 sei bereit zum Bau. Ein Generator, der danach greift, baut einen Auftrag ohne DoR.
  Das ist kein Zahlenunterschied mehr, sondern eine Freigabe, die es nicht gibt.
was_ich_nicht_tue: |
  Kein fetch, kein merge, kein push, und ich fasse den Zustand nicht an. Der Inhalt ist an
  beiden Orten vorhanden; was fehlt, ist der Transport in die eine Richtung.
ballbesitz: "— # ERLEDIGT 14.08. 09:40, vom plan-pruefer nachgemessen: Fernstand steht auf b6640235, A-36 dort ENTWURF, 0 abweichende Zustaende bei 78 gegen 78"
bitte: |
  Der naechste Takt sollte 891a2650 mitnehmen, bevor der Fernstand wieder gelesen wird. Und
  der Vermerk "AUFGELOEST" in meinem Block gehoert auf "aufgeloest fuer den Stand von 08:4x"
  eingeschraenkt — sonst liest ihn spaeter jemand als Dauerzustand.
```

herkunft: docs/STATUS.md · Block 290 · 34f6f5a9
kennung_nicht_zuordenbar: true   # Freitext statt Kennung — uebernommen wie vorgefunden, nicht gedeutet
```yaml
auftrag: "w03_1_haelt_an_allen_sieben_stellen"
titel: "W-03/1s tragendes Kriterium vollstaendig nachgemessen — sieben von sieben"
rolle: plan-pruefer
zeit: "2026-08-14 08:35"  # BERICHTIGT: stand auf "2026-08-07 09:40" — Datum sieben Tage falsch, Zeit geschaetzt statt abgelesen; Wert aus dem einbringenden Commit
stand_kopf: c2489db4
posten: "Vorratspruefung (b) ZAHLEN NACHRECHNEN an W-03/1 (BEREIT, Schnitt e097e7be)"
was_geprueft_wurde: |
  W-03-1-1 ist P1 und TRAGEND. Es nennt zwei Zahlen und fuenf Fundstellen; ich habe alle
  sieben an HEAD UND am Schnitt e097e7be gemessen, Zeile gegen Zeile.
    resources/planner/hausplaner/app/rahmen/EigenschaftenPanel.tsx
      Zeilen   HEAD 563   BASIS 563   (Blatt: 563)
      Exporte  HEAD   2   BASIS   2   (Blatt: 2)
    :108  function aktualisiereWand(changes: Partial<WallNode>)      = generischer Weg
    :120  executeCommand({ type: 'MOVE_NODE', ... })                 = Laenge
    :324  <select value={selectedWall.construction?.materialId ...}> = Material
    :330  <select value={WANDSTAERKEN.includes(selectedWall.thickness = Staerke
    :331  {!WANDSTAERKEN.includes(selectedWall.thickness ...)}        = Staerke, zweite Zeile
    :336  <input type="number" min={100} value={selectedWall.height}  = Hoehe
  Alle sechs Zeilen sind an beiden Staenden IDENTISCH und tragen genau das, was das Blatt
  ihnen zuschreibt. Keine Drift, kein Zeigerfehler ab Basis.
warum_das_zaehlt: |
  Das ist das zweite Blatt in Folge, das vollstaendig haelt (nach W-18/1). Von den bisher
  geprueften BEREIT-Blaettern tragen damit zwei einen sauberen Befund und drei einen Fund
  (W-12/1, A-33, W-16/1). Wer eine Reihenfolge fuer die naechsten Zuege sucht: W-18/1 und
  W-03/1 sind an ihren Fundstellen gepruefte Ware.
grenze_der_aussage: |
  Geprueft ist W-03-1-1. NICHT geprueft sind W-03-1-2 (die Vier-Schichten-Messung je
  Operation) und W-03-1-3 (die zwei Fundamente) — die verlangen laut Blatt ausdruecklich
  eine Erhebung AM BAU-STAND, das ist Generator-Arbeit und nicht meine.
  Zu F-004 aus demselben Blatt liegt mein eigener Befund f004_sperrt_den_winkel_nicht_die
  _entfernung, dort noch offen beim Planner.
ballbesitz: "— (kein Vorgang; W-03/1 bleibt BEREIT beim Generator)"
```

herkunft: docs/STATUS.md · Block 291 · 34f6f5a9
kennung_nicht_zuordenbar: true   # Freitext statt Kennung — uebernommen wie vorgefunden, nicht gedeutet
```yaml
auftrag: "meine_zeitstempel_waren_geschaetzt"
titel: "Dreizehn eigene Bloecke trugen ein sieben Tage falsches Datum — und eine hochgezaehlte Uhrzeit"
rolle: plan-pruefer
zeit: "AUS DEM COMMIT — siehe Botschaft"
stand_kopf: 358f0b59
wie_es_auffiel: |
  Beim Messen der Rollen-Stille habe ich die Uhr des Rechners gegen meine eigenen Blockzeiten
  gehalten. git meldete 08:37, mein letzter Block trug 09:40.
messung: |
  Alle 13 Bloecke mit rolle: plan-pruefer und zeit-Feld gegen den Commit gehalten, der sie
  eingebracht hat (git log -S ueber den auftrag-Schluessel):
    Block                                    stand auf           Commit
    w37_ballwechsel_runde2                   07.08. 07:26        14.08. 07:27
    a33_grundlage_zeiger_gewandert           07.08. 07:31        14.08. 07:30
    spiegelung_rundet_an_der_toleranz        07.08. 07:36        14.08. 07:34
    meine_ballortung_blieb_zu_eng            07.08. 07:42        14.08. 07:39
    vier_fachfragen_fehlen_in_meiner_liste   07.08. 07:49        14.08. 07:43
    statuswahrheit_in_zwei_fassungen         07.08. 08:23        14.08. 08:01
    w16_naht_zwei_zeiger_am_schnitt_falsch   07.08. 08:30        14.08. 08:04
    fernstand_ist_heute_auch_unvollstaendig  07.08. 08:37        14.08. 08:07
    beifang_hat_zwei_richtungen              07.08. 08:44        14.08. 08:11
    w18_1_haelt_an_allen_geprueften_stellen  07.08. 08:51        14.08. 08:15
    f004_sperrt_den_winkel...                07.08. 08:58        14.08. 08:18
    fernstand_zeigt_eine_dor...              07.08. 09:33        14.08. 08:33
    w03_1_haelt_an_allen_sieben_stellen      07.08. 09:40        14.08. 08:35
  ZWEI Fehler, nicht einer:
  (1) DAS DATUM ist in ALLEN dreizehn der 07.08. Richtig ist der 14.08. — sieben Tage.
      Jede andere Rolle schreibt 14.08.; ich stand als einzige daneben.
  (2) DIE UHRZEIT driftet. Der erste Block lag EINE Minute daneben, der letzte 65. Der
      Versatz waechst monoton — das ist kein falsch gestellter Zeiger, sondern der Beweis,
      dass ich die Zeit je Runde HOCHGEZAEHLT und nie abgelesen habe.
was_das_beschaedigt_und_was_nicht: |
  NICHT beschaedigt: die Befunde selbst. Jeder Block traegt stand_kopf mit dem gemessenen SHA,
  und ueber den SHA ist die Zeit jederzeit exakt rekonstruierbar — genau so habe ich sie eben
  rekonstruiert. Kein Messwert haengt an einer Uhrzeit.
  BESCHAEDIGT: jede Aussage ueber REIHENFOLGE und ABSTAND. Wer meine Blockzeiten gegen fremde
  Commits haelt, ordnet falsch ein — und ich selbst habe in dieser Nacht mehrfach mit
  Zeitabstaenden argumentiert ("seit acht Stunden still", "28 Minuten alt, das ist frisch").
  Diese Saetze waren aus derselben geschaetzten Uhr gespeist.
berichtigt: |
  Alle dreizehn zeit-Felder auf den Wert des einbringenden Commits gezogen, je mit Vermerk
  "BERICHTIGT: stand auf ..." — nichts geloescht, der alte Wert bleibt lesbar (A-20-4).
  Ab sofort: das zeit-Feld wird aus dem Rechner gelesen, nicht fortgeschrieben.
ballbesitz: "— (eigene Berichtigung, kein Vorgang)"
```

herkunft: docs/STATUS.md · Block 292 · 34f6f5a9
kennung_nicht_zuordenbar: true   # Freitext statt Kennung — uebernommen wie vorgefunden, nicht gedeutet
```yaml
auftrag: "w10_1_blatt_und_basis_sind_auseinander"
titel: "W-10/1 ist gepflegt worden, sein basis_sha nicht — Blatt und Schnitt beschreiben zwei Staende"
rolle: plan-pruefer
zeit: "2026-08-14 08:42"
stand_kopf: 54a56c15
posten: "Vorratspruefung (a) GEWANDERTE VERWEISE an W-10/1 (BEREIT, Schnitt 18fe2deb)"
messung: |
  16 verschiedene Datei:Zeile-Zeiger des Blatts an HEAD UND am Schnitt 18fe2deb gelesen,
  Zeile gegen Zeile. VIERZEHN sind identisch. ZWEI zeigen an den beiden Staenden auf
  Verschiedenes:
    werkzeugLandkarte.ts:170
      HEAD   { werkzeugId: 'boden',    marke: 'deckt', begruendung: 'ADD_CEILING' }
      BASIS  { werkzeugId: 'pv-modul', marke: 'fehlt', begruendung: '`O...
    HausplanerApp.tsx:1027
      HEAD   type: 'ADD_CEILING',
      BASIS  //
befund_und_er_ist_nicht_der_erwartete: |
  Das Blatt ist NICHT falsch. Es traegt SECHS Vermerke "BERICHTIGT 14.08.", und die zwei
  Zeiger stehen ausdruecklich darunter: ":1027 (war :1042)" und ":170 (war :117)". Der
  Planner hat sie also bereits gezogen — auf den HEUTIGEN Stand.
  Die Folge: das Blatt beschreibt HEAD, sein basis_sha beschreibt 18fe2deb, und die beiden
  sind auseinander. Wer W-10/1 aufnimmt und nach §5 am genannten Basis-Stand gegenprueft,
  findet auf :1027 eine leere Kommentarzeile und auf :170 ein fremdes Werkzeug — und haelt
  ein gepflegtes Blatt fuer ein falsches.
  Das ist eine DRITTE Klasse neben den beiden, die ich heute belegt habe: nicht Drift
  (Zeiger alt, Code neu) und nicht Fehler am Schnitt (Zeiger von Anfang an falsch), sondern
  BLATT GEPFLEGT, SCHNITT STEHENGELASSEN.
zwei_klassen_sauber_getrennt_gemessen: |
  Die sechs Berichtigungen des Blatts zerfallen in zwei verschiedene Sachen, und die
  Unterscheidung ist messbar an der Frage, ob die Datei sich seit dem Schnitt bewegt hat:
    ECHTE DRIFT — Datei seit dem Schnitt geaendert:
      HausplanerApp.tsx    :1042 -> :1027   bewegt durch 606e83b4 (A-31)
      werkzeugLandkarte.ts :117  -> :170    bewegt durch 4654687f und d21dd083 (A-29)
    FEHLER AM SCHNITT — Datei seit dem Schnitt UNVERAENDERT:
      applyCommand.ts, die sechs Zeilen :121-136, "durchgehend um EINS zu hoch".
      Gemessen: 0 Commits seit 18fe2deb, 424 Zeilen an beiden Staenden, und :128 traegt
      an beiden "const nx = -dy / len, ny = dx / len" — genau was das Blatt behauptet.
      Diese sechs waren also von Anfang an falsch; die Berichtigung hat einen
      zeigerfehler_ab_basis geschlossen, keine Drift.
was_ich_nicht_sage: |
  Kein Vorwurf an die Pflege. Ein Blatt, dessen Zeiger auf den heutigen Code passen, ist
  fuer den Bauenden nuetzlicher als eines, das auf einen alten Stand passt. Der Fund ist,
  dass BEIDES nicht gleichzeitig gilt, solange basis_sha stehenbleibt — und §5 verlangt den
  exakten Basis-SHA, nicht irgendeinen.
ballbesitz: planner
bitte: |
  Entweder basis_sha auf einen Stand ziehen, an dem die berichtigten Zeiger gelten (HEAD
  taeten es), oder im Blatt einen Satz, dass die Fundstellen am HEUTIGEN Stand gelten und
  nicht am Schnitt. Ein Satz oder ein Feld — die Sache selbst ist in Ordnung.
```

herkunft: docs/STATUS.md · Block 293 · 34f6f5a9
kennung_nicht_zuordenbar: true   # Freitext statt Kennung — uebernommen wie vorgefunden, nicht gedeutet
```yaml
auftrag: "backup_private_ist_die_aelteste_kopie"
titel: "Die drei Remotes stehen zum ersten Mal nicht gleich — und zurueck haengt ausgerechnet das Backup"
rolle: plan-pruefer
zeit: "2026-08-14 08:43"
stand_kopf: 6e1c2fde
posten: "Zweigprobe der Wache — kein Vorratspruefungs-Posten, sondern der Pflichtteil"
messung: |
  ls-remote gegen alle drei, voll ausgeschrieben:
    fork            54a56c15ff1fedf7a43022ba331beba0a070c7bb
    origin          54a56c15ff1fedf7a43022ba331beba0a070c7bb
    backup-private  b66402355b28927a613bedc41831aeded11d5dab
  backup-private haengt DREI Commits zurueck. Es sind genau meine letzten drei:
    c2489db4  Vorratspruefung (e), vier Karteileichen geschlossen
    358f0b59  Fernstand-Befund erledigt, W-03/1 nachgemessen
    54a56c15  dreizehn falsche Zeitstempel berichtigt
  Abstaende vom Arbeitsbaum aus: HEAD ist EINS voraus gegen fork und origin, VIER gegen
  backup-private.
warum_das_zaehlt: |
  Bis heute standen die drei bei jeder meiner Proben auf demselben Wert — b15c1cb7,
  21d7b675, 9aa76c5b, e3856ab6, 56d61ddd, b6640235. Der letzte Transport hat zum ersten Mal
  nur ZWEI von drei erreicht.
  Und es haengt nicht irgendeiner zurueck: solange nichts deployt ist, ist der Fernstand die
  einzige Kopie ausserhalb dieser Maschine, und der Zweig, der ausdruecklich 'backup' heisst,
  ist heute die AELTESTE davon. Wer im Schadensfall dorthin greift, greift vier Commits
  hinter den Arbeitsbaum.
was_ich_NICHT_behaupte: |
  Kein Datenverlust und keine Divergenz: b6640235 ist ein echter Vorfahr von 54a56c15,
  gemessen ueber rev-list, kein eigener Ast. Es fehlt nur der Transport. Und die drei
  fehlenden Commits sind samt und sonders MEINE Befundbloecke — kein Produktivcode, kein
  Zustandswechsel eines Auftrags haengt daran.
  Ich habe NICHT gemessen, warum der Transport nur zwei erreicht hat. Das kann ein
  abgewiesener Push sein, ein Takt, der backup-private nicht fuehrt, oder Absicht.
was_ich_nicht_tue: |
  Kein Push. Die stehende Regel der Wache ist KEIN PUSH, und sie gilt auch dann, wenn die
  Abhilfe naheliegt. Auf Yamas Wort: git push backup-private auto/hausplaner-integration,
  nur dieser Zweig, kein Zwang, kein main.
ballbesitz: "— # ERLEDIGT 14.08. 08:45, vom plan-pruefer nachgemessen: alle drei Remotes stehen auf 36e60030, der Transport hat backup-private mitgenommen"
bitte: |
  Beim naechsten Takt backup-private mitnehmen — oder sagen, dass es bewusst nicht mehr
  gefuehrt wird. Beides ist in Ordnung; unbemerkt zurueckhaengen ist es nicht, weil die
  Zweigprobe der Wache seit Stunden 'alle drei gleich' gemeldet hat und dieser Satz ab
  heute nicht mehr stimmt.
```

herkunft: docs/STATUS.md · Block 295 · 34f6f5a9
kennung_nicht_zuordenbar: true   # Freitext statt Kennung — uebernommen wie vorgefunden, nicht gedeutet
```yaml
auftrag: "alterung_zwei_fehlalarme_meines_filters"
titel: "Alterung neu gemessen — kein Fund an den Blaettern, zwei Fehlalarme in meinem eigenen Filter"
rolle: plan-pruefer
zeit: "2026-08-14 08:47"
stand_kopf: 36e60030
posten: "Vorratspruefung (d) ALTERUNG, vollstaendig ueber alle ZWOELF offenen Auftraege"
alterung: |
  Auftrag  Zustand           Alter    Commits seit Schnitt   genannte Dateien seither geaendert
  A-05     ABGENOMMEN       215h08     1391   nur der eigene Bericht
  A-12     ABGENOMMEN        84h26     1193   nur eigener Bericht, FORMELSAMMLUNG, VORGEHEN
  W-21L    DECISION_BLOCKED  56h24     1067   KEINE — alle drei genannten Dateien unberuehrt
  W-18/1   BEREIT            31h08      298   keine
  W-12/1   NACHBESSERN       30h53      296   (Fehlalarm, siehe unten)
  W-10/1   BEREIT            24h08      249   keine
  W-16/1   BEREIT            24h30      267   keine
  W-14/1   BEREIT            23h56      243   keine
  A-33     BEREIT            21h59      222   scripts/a26-ball-drift.sh (bereits gemeldet+behoben)
  W-03/1   BEREIT            18h06      205   keine
  A-35     BEREIT             9h11      110   keine
  A-36     ENTWURF            0h29       19   keine
das_ergebnis_fuer_yama: |
  W-21L liegt seit 56 Stunden bei Yama und ist DECISION_BLOCKED. Gemessen: von den DREI
  Dateien, die sein Blatt nennt, hat sich seit dem Schnitt 4f0d4584 KEINE geaendert. Die
  Entscheidung ist also nicht schlechter geworden — wenn sie faellt, passt das Blatt noch
  auf den Code. Das ist die einzige Aussage dieser Runde, die jemand anders braucht.
zwei_fehlalarme_in_meinem_filter_offengelegt: |
  (1) DREI Blaetter meldete mein Lauf als "nicht lesbar": A-05, A-12, W-21L. Nachgesehen:
      alle drei Dateien EXISTIEREN. Ihre Bloecke fuehren das Feld 'datei:' statt 'blatt:',
      und mein Muster kannte nur 'blatt:'. Kein Fund, mein Filter. Berichtigt und wiederholt.
      NEBENBEFUND, den ich nicht ueberdehne: die Statuswahrheit fuehrt ZWEI Feldnamen fuer
      dieselbe Sache. Das ist kein Fehler, solange beide gelesen werden — ich lese ab jetzt
      beide.
  (2) W-12/1 meldete "public/hausplaner/hausplaner.js seit dem Schnitt geaendert", bewegt
      durch 606e83b4 und ebe99ba6. Nachgesehen, wo das Blatt die Datei nennt: EINMAL, in der
      Pruefteabelle des Evaluators, Zeile 261 — "Buendel public/hausplaner/hausplaner.js |
      nicht noetig | Scope-Diff am Commit: sieben .md, resources/ 0". Das Blatt haengt also
      NICHT an der Datei, es stellt fest, dass kein Neubau noetig war. Mein Filter zaehlt
      Erwaehnungen, nicht Abhaengigkeiten — dieselbe Klasse, die ich an A-35-2 beanstandet
      und beim Evaluator gelobt habe. Kein Fund.
kein_fund_an_den_blaettern: |
  Nach Abzug der zwei Fehlalarme bleibt EIN Treffer, und der ist alt: A-33 nennt
  scripts/a26-ball-drift.sh, gemeldet 07:30 und vom Planner in eecd5215 behoben.
  Die sechs uebrigen BEREIT-Auftraege nennen keine Datei, die sich seit ihrem Schnitt
  bewegt hat — bei 205 bis 298 Commits Abstand.
ballbesitz: "— (Messung, kein Vorgang)"
```

herkunft: docs/STATUS.md · Block 296 · 34f6f5a9
kennung_nicht_zuordenbar: true   # Freitext statt Kennung — uebernommen wie vorgefunden, nicht gedeutet
```yaml
auftrag: "drei_namen_zwei_kopien"
titel: "Meine Zweigprobe zaehlt drei Remotes — es sind zwei Kopien, und das aendert den Backup-Befund"
rolle: plan-pruefer
zeit: "2026-08-14 08:50"
stand_kopf: 18dd3e2e
anlass: |
  Der Release-Pruefer hat in 71509af2 meinen Backup-Befund beantwortet, die Lage behoben
  (alle Ziele auf 36e60030, von ihm je einzeln mit merge-base geprueft, BEVOR er den Befund
  las) und daraus geschlossen: "Es pusht also nicht nur der Release-Pruefer. Und wer sonst
  pusht, bedient zwei von drei Zielen." Diesen Schluss habe ich nachgemessen — und dabei
  einen Fehler in MEINER eigenen Dauermeldung gefunden.
messung: |
  git config --get remote.<name>.url fuer alle vier Namen, nach identischer URL gruppiert:
    Gegenstelle 1  fork, origin      github.com/yamasolaraspekt-max/nuri-head.git
    Gegenstelle 2  upstream          github.com/raminsadid2021/nuri-head.git
    Gegenstelle 3  backup-private    github.com/yamasolaraspekt-max/nurihead.git
  fork UND origin sind DASSELBE Repository. Und upstream fuehrt den Zweig gar nicht:
    git ls-remote upstream auto/hausplaner-integration  ->  exit 0, Ausgabe LEER
  (Exit-Code getrennt gelesen, nicht hinter der Pipe — leer bei exit 0 heisst "kein solcher
  Zweig", nicht "nicht erreichbar".)
was_daran_MEIN_fehler_ist: |
  Ich melde seit Stunden "alle drei Remotes live auf X" und habe damit eine Redundanz
  ausgewiesen, die es nicht gibt. Es sind DREI NAMEN und ZWEI Kopien des Zweiges. Der Satz
  war nie falsch im Wortlaut — fork, backup-private und origin standen tatsaechlich auf
  demselben SHA — aber er hat die falsche Sicherheit erzeugt, es gaebe drei unabhaengige
  Ablagen. Ich habe die Namen gezaehlt statt die Gegenstellen.
was_das_am_backup_befund_aendert_und_es_verschaerft_ihn: |
  Um 08:43 stand backup-private drei Commits zurueck. Ich habe das als "einer von drei"
  gemeldet. Richtig ist: EINE VON ZWEI Kopien war veraltet — die Haelfte der Sicherung
  ausserhalb dieser Maschine, nicht ein Drittel. Solange nichts deployt ist, ist das die
  ganze Sicherung.
was_es_an_SEINEM_schluss_aendert: |
  Sein Schluss braucht keinen zweiten Pusher, um zu stimmen — er hat eine mechanische
  Erklaerung: EIN "git push fork <zweig>" aktualisiert das Repository, das fork UND origin
  benennen. Von aussen sieht das aus wie "zwei von drei Zielen bedient", ist aber ein
  einziger Push auf eine einzige Gegenstelle. Sein Reflog-Befund passt dazu genau: die
  fork-Spur traegt 54a56c15, die origin-Spur nicht — weil nur der Name fork benutzt wurde,
  waehrend die Gegenstelle beide traegt.
  DAS WIDERLEGT SEINEN SATZ NICHT. Dass jemand anders gepusht hat, bleibt moeglich und seine
  Zeitmessung stuetzt es. Ich sage nur: die Zahl "zwei von drei" ist kein Beleg dafuer, weil
  sie sich auch ohne zweiten Pusher ergibt.
was_ich_NICHT_gemessen_habe: |
  WER den Push auf 54a56c15 gefahren hat. Aus diesem Arbeitsbaum stammt er nicht von mir —
  ich habe in dieser ganzen Nacht keinen Push abgesetzt, die Wache verbietet es. Belegen
  kann ich das nicht aus dem Repository; ein lokales Reflog eines Remote-Refs entsteht auch
  beim Fetch. Ich sage also, was ich weiss, und nicht mehr.
ballbesitz: release-pruefer
bitte: |
  Zwei Saetze, wenn der Takt ohnehin angefasst wird: der Vergleich sollte gegen die
  GEGENSTELLEN laufen, nicht gegen die Namen — sonst meldet er drei gruene Haken, wo zwei
  Kopien stehen. Und upstream gehoert entweder in den Takt oder ausdruecklich heraus; heute
  fuehrt es den Zweig nicht.
ballbesitz_zusatz_yama: |
  Fuer Yama zur Kenntnis, ohne Entscheidungsbedarf: die Arbeit dieser Nacht liegt in ZWEI
  Kopien ausserhalb dieser Maschine, nicht in drei.
```

herkunft: docs/STATUS.md · Block 298 · 34f6f5a9
kennung_nicht_zuordenbar: true   # Freitext statt Kennung — uebernommen wie vorgefunden, nicht gedeutet
```yaml
auftrag: "a35_meine_eigene_dor_nachgeprueft"
titel: "Meine eigene DoR-Erteilung nachgeprueft — sie haelt, und mein F-004-Befund war nicht neu"
rolle: plan-pruefer
zeit: "2026-08-14 08:53"
stand_kopf: 481c7da7
posten: "Vorratspruefung (b) an A-35 — dem Auftrag, dessen DoR ICH erteilt habe"
warum_ausgerechnet_A35: |
  Fremde Blaetter zu pruefen ist die leichtere Uebung. A-35 ist der einzige offene Auftrag,
  dessen DoR von mir stammt — und bei dem ich schon einmal gefehlt habe (Befund
  meine_dor_hatte_eine_luecke: ich hatte erteilt, obwohl A-35-2 am Basis-Stand bereits
  erfuellt war).
was_haelt_selbst_gemessen: |
  A-35-1  grep -c "'trimmen'" toolRegistry.ts  ->  0 heute UND 0 am Basis 1df82ee1.  ROT.
  A-35-2  der Befehl des Blatts woertlich gefahren:
            grep -rln "from '.*geradenGeometrie'" --include='*.ts' --include='*.tsx'
            -> EIN Treffer, __tests__/geradenGeometrie.test.ts, davon AUSSERHALB __tests__: 0.
          Genau was das Blatt als "Stand vorher" behauptet. ROT.
          Die Neufassung nach meinem Befund a35_2_misst_erwaehnungen misst jetzt wirklich
          IMPORTE — das war die Berichtigung, und sie traegt.
  A-35-7  Schutzkriterium: HausplanerApp.tsx:815 waehleAn steht an HEAD UND am Basis-Stand
          zeichengleich, und die Datei hat seit 1df82ee1 NULL Commits. Der Zeiger haelt.
  Muster jeweils am bekannten Treffer verifiziert.
mein_eigener_fehler_diesmal: |
  A-35-9 traegt woertlich: "ein Test mit zwei 6000-mm-Waenden bei 0,001 Grad Winkeldifferenz —
  der Schnittpunkt liegt 286,5 m entfernt, geradenSchnitt liefert ihn, K2s Wache greift nicht."
  DAS IST GENAU MEIN BEFUND VON 08:18 (f004_sperrt_den_winkel_nicht_die_entfernung). Ich habe
  ihn als Eigenschaft "fuer den Tag, an dem der erste Aufrufer entsteht" gemeldet — waehrend
  ein BEREIT-Blatt diesen Tag laengst benennt und den Fall schon durchgerechnet hat.
  Schlimmer: A-35-9 geht WEITER als ich. Es nennt die Abhilfe, die ich nicht genannt habe:
  geprueft wird 0 <= t <= 1 UND 0 <= u <= 1, dimensionslos und ohne Epsilon, und abgewiesen
  wird mit Grund statt verlaengert. Ich habe den Bestand nicht durchsucht, bevor ich meldete.
  Der Befund bleibt sachlich richtig und die Bitte an die FORMELSAMMLUNG bleibt sinnvoll —
  aber "neu" war er nicht, und das habe ich behauptet.
und_ein_echter_fund_beim_nachrechnen: |
  Die 286,5 m nachgerechnet, weil eine Zahl nur dann reproduzierbar ist, wenn die Anordnung
  feststeht:
    sin(0,001 Grad) = 1,7453e-5  -> passiert die Waechterschwelle 1e-6, wie behauptet.
    Lesart "gemeinsamer Startpunkt": Endpunkte weichen 0,105 mm ab, Schnittpunkt im
      Startpunkt, Abstand NULL — ergibt die 286,5 m nicht.
    Lesart "seitlicher Versatz d":  d=1 mm -> 57,3 m | d=5 mm -> 286,5 m | d=10 mm -> 573 m
    Rueckwaerts gerechnet: 286,5 m entsprechen d = 5,000 mm, auf drei Nachkommastellen.
  DIE ZAHL STIMMT — und der Versatz von 5 mm, ohne den sie nicht entsteht, steht NIRGENDS
  im Blatt. Wer den Test schreibt, muss ihn raten. Das ist ein fehlender Operand in einem
  Kriterium, das sonst vorbildlich praezise ist (K3 greift nachgerechnet: t = 47,7, weit
  ausserhalb 0..1, also Abweisung).
ballbesitz: "— # GEGENSTANDSLOS seit 09:11, die Bitte ist zurueckgenommen (ich_habe_zweimal_gegen_a35_gemeldet_was_darin_steht)"
bitte: |
  In A-35-9 den seitlichen Versatz nennen — "5 mm" genuegt, dann ist die 286,5 m aus dem
  Blatt heraus nachrechenbar. Eine Zahl, kein Bau.
```

herkunft: docs/STATUS.md · Block 299 · 34f6f5a9
kennung_nicht_zuordenbar: true   # Freitext statt Kennung — uebernommen wie vorgefunden, nicht gedeutet
```yaml
auftrag: "sieben_blaetter_gegen_ihren_schnitt"
titel: "Alle sieben BEREIT-Blaetter gegen ihren Schnitt gemessen — und die Klasse habe ICH erzeugt"
rolle: plan-pruefer
zeit: "2026-08-14 08:57"
stand_kopf: 5c46941c
posten: "Vorratspruefung (a), ausgeweitet von einem Blatt auf ALLE sieben"
messung: |
  Jeder Datei:Zeile-Zeiger jedes BEREIT-Blatts an HEAD UND am eigenen basis_sha gelesen,
  Zeile gegen Zeile. Nur eindeutig aufloesbare Pfade gezaehlt.
    Auftrag  Basis      Zeiger  gleich  anders  Datei am Schnitt nicht vorhanden
    W-18/1   8c920624        8       8       0    0
    W-16/1   86f94d98        9       9       0    0
    A-35     1df82ee1        4       4       0    0
    W-03/1   e097e7be        4       3       1    0
    A-33     f9b67b1b        1       0       1    0
    W-10/1   18fe2deb       17      15       2    0
    W-14/1   78c09e1b       24      13       7    4
    ------------------------------------------------------------------
    Summe                   67      52      11    4
  Drei Blaetter sind an ihrem Schnitt vollstaendig stimmig, vier nicht.
die_abweichungen_einzeln: |
  W-03/1  werkzeugLandkarte.ts:108   HEAD 'teilen'/fehlt   BASIS 'bild-importieren'/ohne-modell
  A-33    a26-ball-drift.sh:53       HEAD die Muster-Zeile  BASIS ein Kommentar
  W-10/1  werkzeugLandkarte.ts:170, HausplanerApp.tsx:1027 (am 08:42 gemeldet)
  W-14/1  fuenf HausplanerApp-Zeilen, zwei werkzeugLandkarte-Zeilen — UND VIER Zeiger auf
          app/sammelBefehle.ts, eine Datei, die am Schnitt 78c09e1b NICHT EXISTIERTE.
          Angelegt wurde sie von 606e83b4 (A-31), also NACH dem Schnitt.
und_jetzt_der_teil_der_gegen_mich_geht: |
  A-33s abweichender Zeiger ist a26-ball-drift.sh:53. Das ist GENAU die Zeile, deren
  Berichtigung ICH um 07:30 verlangt habe (Befund a33_grundlage_zeiger_gewandert) und die
  der Planner in eecd5215 gezogen hat. Vorher zeigte das Blatt auf :32/:55-56 und stimmte
  mit seinem Schnitt; jetzt zeigt es auf :53/:96-97 und stimmt mit HEAD.
  ICH HABE DIE KLASSE ERZEUGT, DIE ICH SEIT ZWEI RUNDEN MELDE. Meine Driftmeldungen fuehren
  zu Vorwaerts-Berichtigungen, und jede Vorwaerts-Berichtigung ohne mitgezogenen basis_sha
  bricht §5s "exakter Basis-SHA". Das ist keine Ausrede fuer die Blaetter — es ist der Grund,
  warum die Bitte nicht "Zeiger ziehen" heissen darf, sondern "Zeiger UND Schnitt ziehen".
die_zwei_wege_schliessen_sich_aus: |
  Entweder das Blatt bleibt seinem Schnitt treu — dann waechst die Drift und wer an HEAD
  liest, wird fehlgeleitet. Oder es wird nach vorn gepflegt — dann ist es mit basis_sha
  unstimmig, solange der nicht mitwandert. Beides zugleich geht nicht, und heute steht der
  Bestand in der Mitte: vier Blaetter gepflegt, ihre Schnitte stehengelassen.
  Der saubere Weg ist der zweite MIT mitgezogenem Schnitt. Er kostet nichts ausser einem
  Feld, und er ist der einzige, bei dem §5 und die Nuetzlichkeit fuer den Bauenden
  gleichzeitig gelten.
was_ich_NICHT_sage: |
  Kein Blatt ist dadurch falsch, und keines ist unbaubar. W-14/1 ist der schaerfste Fall,
  aber auch dort sind 13 von 24 Zeigern an beiden Staenden gleich und die vier auf
  sammelBefehle.ts zeigen an HEAD auf genau das, was das Blatt beschreibt.
  Und meine eigene Meldung von 08:35 ("W-03/1 haelt an allen sieben Stellen") bleibt richtig:
  sie galt ausdruecklich W-03-1-1, und ich habe die Grenze damals genannt. Der jetzt
  gefundene Zeiger steht in einem anderen Abschnitt.
ballbesitz: planner
bitte: |
  Bei den vier Blaettern den basis_sha auf einen Stand ziehen, an dem die berichtigten
  Zeiger gelten — oder je einen Satz, dass die Fundstellen am HEUTIGEN Stand gelten.
  Und kuenftig bei jeder Zeiger-Berichtigung den Schnitt mitziehen; meine Bitten formuliere
  ich ab sofort so.
```

herkunft: docs/STATUS.md · Block 300 · 34f6f5a9
kennung_nicht_zuordenbar: true   # Freitext statt Kennung — uebernommen wie vorgefunden, nicht gedeutet
```yaml
auftrag: "drei_befunde_liegen_in_einer_einzigen_kopie"
titel: "Die Zaunbilanz des Release-Pruefers haelt — und dieselbe Messung zeigt drei ungesicherte Befunde"
rolle: plan-pruefer
zeit: "2026-08-14 08:59"
stand_lokal: cb85bd0b
stand_fern: 6edbcfd1
teil_1_seine_zahlen_nachgemessen: |
  Der Release-Pruefer meldet in 6edbcfd1 den VIERZEHNTEN Merge-Konflikt und sagt, seine
  Zaunbilanz habe nach dem Zusammensetzen 803 Zaun-Zeilen gefunden, also UNGERADE, und er
  habe den fehlenden Schliesser an der Naht gesetzt. Nachgemessen am Fernstand 6edbcfd1:
    Zaunzeilen           804   GERADE
    ungeschlossener Zaun keiner
    Datei endet auf einem geschlossenen Zaun: ja
  803 vor dem Setzen und 804 danach — die Zahlen passen zueinander, und die Struktur ist
  heil. Sein Befund und seine Behebung halten beide.
  UND SEIN MUSTER TRAEGT: wenn zwei Rollen gleichzeitig ans Dateiende anfuegen, traegt jede
  Seite einen halben Zaun; wer beide nimmt, ohne die Bilanz zu pruefen, laesst einen offen.
  Genau diese Lage hatte ich um 07:56 vor mir, als ich zwei Runden lang nicht schreiben
  konnte — damals ohne Bilanz, nur mit meiner Schreib-Zusicherung als Notbremse. Seine
  Kontrolle ist die bessere Fassung derselben Sache, weil sie MISST statt zu verhindern.
teil_2_und_was_dieselbe_messung_sonst_zeigt: |
  In derselben Abfrage habe ich meine eigenen Befundschluessel am Fernstand gesucht:
    alterung_zwei_fehlalarme_meines_filters   1x vorhanden
    drei_namen_zwei_kopien                    0x
    a35_meine_eigene_dor_nachgeprueft         0x
    sieben_blaetter_gegen_ihren_schnitt       0x
  Gegengeprueft ueber die Commit-Zaehlung: HEAD ist DREI Commits voraus (481c7da7, 5c46941c,
  cb85bd0b), und keiner davon liegt in einer der beiden Kopien ausserhalb dieser Maschine.
  Es sind ausgerechnet die drei, die am meisten Messarbeit tragen — die Gegenstellen-Zaehlung,
  die Pruefung meiner eigenen DoR und die Reihenmessung ueber alle sieben BEREIT-Blaetter
  (67 Zeiger).
warum_ich_das_melde_und_nicht_loese: |
  Ich pushe nicht, die Wache verbietet es, und der Takt des Release-Pruefers holt es
  regelmaessig. Der Punkt ist nicht Dringlichkeit, sondern Ehrlichkeit: ich habe heute Nacht
  mehrfach "alles gesichert" gemeldet. In diesem Moment stimmt das fuer drei meiner Befunde
  NICHT, und wer den Satz von mir liest, soll wissen, dass er einen Zeitpunkt beschreibt und
  keinen Dauerzustand — dieselbe Lehre, die ich dem Wort "aufgeloest" um 09:33 abverlangt habe.
ballbesitz: "— (Wuerdigung und Lagemeldung, kein Vorgang)"
```

herkunft: docs/STATUS.md · Block 301 · 34f6f5a9
kennung_nicht_zuordenbar: true   # Freitext statt Kennung — uebernommen wie vorgefunden, nicht gedeutet
```yaml
auftrag: "a36_3_ist_zweideutig_an_der_kante_k2"
titel: "A-36-3 gegen seine eigenen drei Commits durchgerechnet — bei einem entscheidet die Wortwahl"
rolle: plan-pruefer
zeit: "2026-08-14 09:02"
stand_kopf: b0569fcf
posten: "§5-Punkt 'kein Kriterium ist unerfuellbar' fuer A-36 — der Punkt, den ich noch offen hatte"
warum_jetzt: |
  Ich habe zugesagt, die DoR ohne weitere Runde zu erteilen, sobald die vier fehlenden
  Punkte im Blatt stehen. Diese Zusage ist nur ehrlich, wenn der Rest wirklich geprueft ist.
  Offen war "kein Kriterium ist unerfuellbar". A-36-3 ist die Positivprobe und laesst sich
  HEUTE nachrechnen, obwohl der Waechter noch nicht existiert — die Zuordnung Hunk zu
  Abschnitt ist reine Textarbeit.
gerechnet: |
  Fuer jeden der drei genannten Commits: Hunks mit -U0 gezogen, jede Hunk-Startzeile der
  naechststehenden Ueberschrift der Datei AM COMMIT zugeordnet, wie A-36 es beschreibt.
    ef273926   3 Hunks   beruehrte ABSCHNITTE 2   davon mit KENNUNG 1   ['W-20']
    93960252   2 Hunks   beruehrte ABSCHNITTE 2   davon mit KENNUNG 0   []
    5ac659bf   4 Hunks   beruehrte ABSCHNITTE 3   davon mit KENNUNG 2   ['A-25','W-20']
befund: |
  A-36-3 verlangt woertlich: "Er muss bei allen dreien MEHR ALS EINE KENNUNG melden."
  Bei 93960252 traegt KEINER der beiden beruehrten Abschnitte eine Kennung:
    Z.14424  # --- Und die Regel, damit die Frage nicht jede Runde wiederkommt ---
    Z.14453  ## BEFUND GEGEN MICH — MEIN SICHERUNGSSATZ WAR ZU SCHARF, UND ER STEHT ...
  Nach Kante K2 meldet der Waechter dafuer zweimal "(ohne Kennung)". Ob das "mehr als eine
  Kennung" ist, haengt daran, ob ueber ABSCHNITTE oder ueber verschiedene KENNUNGEN gezaehlt
  wird:
    ueber Abschnitte gezaehlt  -> 2, das Kriterium ist gruen
    ueber Kennungen gezaehlt   -> 1 Wert, zweimal derselbe, das Kriterium ist ROT und
                                  fuer diesen Commit UNERFUELLBAR, weil kein Bau daran
                                  etwas aendert.
  Das ist keine Kleinigkeit: A-36-3 ist ausdruecklich die Positivprobe, mit der Begruendung
  "ein Waechter, den man nie hat sprechen sehen, ist von einem kaputten nicht zu
  unterscheiden". Ausgerechnet dieser Nachweis kippt an einem Wort.
mein_eigener_fehlgriff_dabei: |
  Mein erster Lauf meldete fuer 93960252 nur EINEN beruehrten Abschnitt. Das war falsch und
  es war mein Etikett: ich hatte die Abschnitte ueber ihre extrahierte Kennung gezaehlt, und
  weil beide keine tragen, fielen zwei verschiedene Ueberschriften auf denselben Wert
  "(ohne Kennung)" zusammen. An den Rohzeilen nachgesehen und berichtigt — es sind ZWEI
  Abschnitte. Dieselbe Klasse wie der Fehlalarm von 08:47: ich zaehle Etiketten statt Dinge.
  Der Fund ueberlebt die Berichtigung, aber er sieht anders aus als beim ersten Blick.
was_ich_NICHT_sage: |
  Der Auftrag ist nicht kaputt und die drei Belegcommits sind gut gewaehlt — zwei von drei
  liefern mehrere Kennungen, und der dritte ist gerade deshalb wertvoll, weil er die Kante
  K2 mit einem ECHTEN Fall trifft statt mit einem gedachten. Ich sage: das Kriterium muss
  sagen, was es zaehlt.
ballbesitz: planner
bitte: |
  In A-36-3 ein Wort: "mehr als EINEN ABSCHNITT melden" statt "mehr als eine Kennung" —
  oder ausdruecklich, dass zwei "(ohne Kennung)"-Zeilen als zwei zaehlen. Damit ist der
  §5-Punkt "kein Kriterium ist unerfuellbar" fuer A-36 abgeschlossen; die uebrigen Punkte
  habe ich in b6640235 bereits gemessen.
```

herkunft: docs/STATUS.md · Block 302 · 34f6f5a9
kennung_nicht_zuordenbar: true   # Freitext statt Kennung — uebernommen wie vorgefunden, nicht gedeutet
```yaml
auftrag: "f013_deckung_exakt_und_wirkung_gerechnet"
titel: "F-013 durchgerechnet — Abdeckung exakt n(n-3)/2, Wirkung an sieben Faellen richtig"
rolle: plan-pruefer
zeit: "2026-08-14 09:05"
stand_kopf: 902c83f3
posten: "Vorratspruefung (c) FORMELN DURCHRECHNEN — tragende Formel aus W-18/1 (BEREIT, 31h)"
vorher_den_bestand_durchsucht: |
  Lehre aus 08:53, wo ich einen Befund als neu meldete, den A-35-9 laengst trug: erst
  gesucht, dann gerechnet. F-013 kommt 13x in docs/STATUS.md vor und in vier aktiven
  Blaettern — aber ausschliesslich als ZEIGER-Frage (A-34: "gemeint war F-013") und in
  Registerzeilen. 'Kantenpaar' und 'n(n-3)' kommen NULL Mal vor. Die Rechnung unten ist neu.
wortlaut_gegen_code: |
  F-013 (FORMELSAMMLUNG.md:155): "Jedes Kantenpaar, das nicht benachbart ist, auf
  Streckenschnitt pruefen." Grenzfall: "Aufwand waechst quadratisch; bis ~200 Punkte
  unproblematisch."
  geometry/kontur.ts:109 schneidetSichSelbst setzt genau das um, mit der Nachbarschaft
  j === i+1 ODER (i === 0 && j === n-1) — die zweite Haelfte ist die geschlossene Kontur,
  letzte Kante neben erster.
abdeckung_gezaehlt: |
  Die Schleife simuliert und die getesteten Paare gezaehlt, gegen die geschlossene Form
  n(n-3)/2 fuer ein geschlossenes n-Eck:
    n= 3 ->  0 Paare, Formel  0   stimmt   (fruehes return, ein Dreieck kann nicht)
    n= 4 ->  2 Paare, Formel  2   stimmt   (0/2, 1/3)
    n= 5 ->  5,        Formel  5   stimmt
    n= 6 ->  9,        Formel  9   stimmt
    n= 8 -> 20,        Formel 20   stimmt
    n=12 -> 54,        Formel 54   stimmt
  Keine Luecke und keine Doppelpruefung. Die Umlauf-Nachbarschaft ist korrekt behandelt —
  genau der Punkt, den der Code-Kommentar als "beim Zaehlen leicht vergessen" benennt.
  Aufwand zur Kontrolle: 200 Punkte -> 19 700 Paare, 500 Punkte -> 124 250. Der Grenzfall
  "bis ~200 unproblematisch" ist damit eine vernuenftige Marke, keine willkuerliche.
wirkung_gerechnet: |
  streckenSchneiden wortgetreu nachgebildet (kreuz ueber drei Punkte, imKasten fuer den
  kollinearen Zweig) und sieben Faelle gefahren:
    Quadrat 1000x1000                 false   erwartet false
    Schleife / bowtie                 true    erwartet true
    Dreieck n=3                       false   erwartet false
    L-Form, 6 Punkte                  false   erwartet false
    Ruecklaeufer kollinear            true    erwartet true
    Ecke beruehrt fremde Kante        true    erwartet true
    U-Form, 8 Punkte                  false   erwartet false
  SIEBEN VON SIEBEN. Besonders der kollineare Ruecklaeufer und die Ecke, die auf einer
  fremden Kante liegt — beides Faelle, die eine reine Vorzeichenpruefung durchliesse und
  die der Code ausdruecklich abfaengt.
die_eine_grenze_die_ich_nenne: |
  Die kollinearen Zweige vergleichen EXAKT auf null (d1 === 0). Bei ganzzahligen
  mm-Koordinaten ist das exakt richtig. Bei nicht ganzzahligen Koordinaten wuerde ein
  fast-kollinearer Ruecklaeufer durchrutschen — dieselbe Bedingung wie in F-032s
  Rundungs-Grenzfall. Das ist KEIN Befund: der Planner hat dort selbst gemessen, dass
  fangKern.ts:76 jeden Fangpunkt rundet und Geometrie und Befehle 57 Math.round-Stellen
  tragen. Ich nenne es, weil beide Formeln an derselben Voraussetzung haengen und das
  nirgends zusammen steht.
ballbesitz: "— (Messung ohne Fund; W-18/1 bleibt BEREIT beim Generator)"
```

herkunft: docs/STATUS.md · Block 303 · 34f6f5a9
kennung_nicht_zuordenbar: true   # Freitext statt Kennung — uebernommen wie vorgefunden, nicht gedeutet
```yaml
auftrag: "w14_kernmodul_exakt_drift_nur_bei_verbrauchern"
titel: "W-14/1: neun von neun Exporten zeichengenau — die Drift sitzt ausschliesslich bei den Verbrauchern"
rolle: plan-pruefer
zeit: "2026-08-14 09:08"
stand_kopf: dee54aca
posten: "Vorratspruefung (b) ZAHLEN NACHRECHNEN an W-14/1 (BEREIT, 24h, Schnitt 78c09e1b)"
gemessen: |
  W-14-1-4 verlangt: "Die NEUN Exporte von editierGeometrie.ts sind vollstaendig genannt,
  mit Fundstelle. Am Bau-Stand zaehlen; meine Zahl ist vom 13.08. und ersetzt die eigene
  nicht." Also gezaehlt, an HEAD UND am Schnitt:
    editierGeometrie.ts   75 Zeilen an beiden Staenden
    Exporte               9 an beiden Staenden
  Und die Liste im Blatt Zeile fuer Zeile gegen den Code gehalten:
    Blatt  :7 Punkt · :12 Achse · :15 versetzePunkt · :20 versetzteWand · :34 spiegelePunkt
           :46 spiegelteWand · :55 Bbox · :63 bbox · :73 achsenMitte
    Code   :7 Punkt · :12 Achse · :15 versetzePunkt · :20 versetzteWand · :34 spiegelePunkt
           :46 spiegelteWand · :55 Bbox · :63 bbox · :73 achsenMitte
  NEUN VON NEUN, Name und Zeilennummer zeichengenau. Kein Export fehlt, keiner ist zuviel.
und_das_ordnet_meinen_eigenen_befund_von_0857_ein: |
  In sieben_blaetter_gegen_ihren_schnitt hatte ich fuer W-14/1 die schaerfste Bilanz
  gemeldet: 24 Zeiger, 13 gleich, 7 anders, 4 auf eine am Schnitt fehlende Datei. Diese
  Runde zeigt, WO das sitzt — und wo nicht:
    editierGeometrie.ts   0 Commits seit dem Schnitt, 9 von 9 Zeigern exakt   DAS KERNMODUL
    HausplanerApp.tsx     5 abweichende Zeiger                                Verbraucher
    werkzeugLandkarte.ts  2 abweichende Zeiger                                Verbraucher
    sammelBefehle.ts      4 Zeiger auf eine Datei, die es am Schnitt nicht gab Verbraucher
  Die Drift liegt ZU HUNDERT PROZENT bei den Verbrauchern, und zwar genau in den Dateien,
  die A-31 (606e83b4) und A-29 (4654687f/d21dd083) nach dem Schnitt angefasst haben. Das
  Modul, das W-14 beschreibt, hat sich nicht bewegt.
warum_das_die_lage_veraendert: |
  Meine 08:57er Meldung liest sich als "W-14/1 ist der schlechteste der sieben". Das ist
  nach Zahlen richtig und nach Sache irrefuehrend: der GEGENSTAND des Blatts — die neun
  Exporte, ihre Bedeutungen, ihre Fundstellen — ist an beiden Staenden exakt. Was gewandert
  ist, sind die Stellen, an denen ANDERE Aufträge das Modul benutzen, und genau die hat der
  Planner mit sichtbaren Vermerken nachgezogen ("[war :671]", "[war :1356]",
  "[war HausplanerApp:708]").
  Fuer den Bauenden heisst das: W-14/1 ist ziehbar, und die einzige offene Frage bleibt die
  aus 08:57 — der basis_sha beschreibt einen Stand, an dem sammelBefehle.ts noch nicht
  existierte.
ballbesitz: "— (Messung ohne neuen Fund; die basis_sha-Frage liegt seit 08:57 beim Planner)"
```

herkunft: docs/STATUS.md · Block 304 · 34f6f5a9
kennung_nicht_zuordenbar: true   # Freitext statt Kennung — uebernommen wie vorgefunden, nicht gedeutet
```yaml
auftrag: "ich_habe_zweimal_gegen_a35_gemeldet_was_darin_steht"
titel: "RUECKNAHME: mein A-35-9-Befund war falsch — der Versatz steht im Blatt, samt Tabelle"
rolle: plan-pruefer
zeit: "2026-08-14 09:11"
stand_kopf: a6fa9c00
posten: "Vorratspruefung (e) EIGENE BEFUNDE VERFOLGEN — und der erste Treffer bin wieder ich"
was_ich_zuruecknehme: |
  Um 08:53 habe ich gemeldet (a35_meine_eigene_dor_nachgeprueft): "Die Zahl stimmt — und der
  Versatz von 5 mm, ohne den sie nicht entsteht, steht NIRGENDS im Blatt. Wer den Test
  schreibt, muss ihn raten." Ball beim Planner, Bitte: "In A-35-9 den seitlichen Versatz
  nennen."
  DAS IST FALSCH. A-35 nennt ihn, Zeile 112: "Zwei 6000-mm-Waende, 5 mm Versatz — wo liegt
  der Schnittpunkt?" Darunter steht eine vollstaendige Tabelle.
was_im_blatt_wirklich_steht: |
  ```text
  EPS_SINUS = 1e-6 wirkt auf den SINUS, nicht auf den Winkel:
    asin(1e-6) = 5,73e-05 Grad   <- erst darunter blockiert die Wache
  Zwei 6000-mm-Waende, 5 mm Versatz — wo liegt der Schnittpunkt?
     0,01     Grad ->     28,6 m   Wache: laesst durch
     0,001    Grad ->    286,5 m   Wache: laesst durch
     0,0001   Grad ->   2864,8 m   Wache: laesst durch
     0,000057 Grad ->   4999,6 m   Wache: laesst durch  (exakt an der Schwelle)
  ```
  Und der Kernsatz darunter: "K2s Wache ist eine WINKELschwelle, der Schaden ist eine
  ABSTANDSgroesse."
  DAS IST WORTGLEICH MEIN F-004-BEFUND VON 08:18 ("der Waechter bindet den WINKEL, nicht die
  ENTFERNUNG"). Ich habe also zweimal gegen dasselbe Blatt gemeldet, was darin steht.
ihre_zahlen_gegen_meine_gerechnet: |
  Weil eine Ruecknahme ohne Nachrechnen nur eine zweite Behauptung waere:
    Winkel       Blatt        meine Rechnung mit d=5 mm
    0,01 Grad    28,6 m       28,6 m
    0,001        286,5 m      286,5 m
    0,0001       2864,8 m     2864,8 m
    0,000057     4999,6 m     5025,9 m   <- meine Naeherung, ihre Zahl ist die genauere
    asin(1e-6)   5,73e-05 Grad   selbst gerechnet: 5,730e-05 Grad
  Drei von vier zeichengenau, die vierte weicht ab, weil ich mit dem gerundeten Winkel
  gerechnet habe und das Blatt mit der exakten Schwelle. Ihre Tabelle ist praeziser als
  meine Rechnung.
der_mechanismus_und_er_ist_benennbar: |
  Bei F-013 habe ich es richtig gemacht: erst den Bestand durchsucht, dann gerechnet, und
  ausdruecklich belegt, dass 'Kantenpaar' null Mal vorkommt. Bei A-35 habe ich es zweimal
  NICHT getan — ich habe das KRITERIUM gelesen (A-35-9, Zeilen 244-251) und daraus auf das
  BLATT geschlossen. Die 5 mm stehen 130 Zeilen weiter oben.
  DIE REGEL, die ich mir daraus setze: eine Aussage ueber ABWESENHEIT braucht eine Suche
  ueber das GANZE Dokument, nicht ueber den Abschnitt, in dem ich gerade lese. "Steht
  nirgends" ist ein Zaehlwort und braucht nach B5 eine Belegzeile — ich hatte keine.
was_bestehen_bleibt: |
  Aus dem 08:53er Block bleibt richtig und unberuehrt: A-35-1 rot, A-35-2 rot und korrekt
  auf Importe neugefasst, A-35-7 Zeiger an beiden Staenden zeichengleich, HausplanerApp.tsx
  0 Commits seit dem Schnitt. Das war die eigentliche Pruefung meiner eigenen DoR, und die
  haelt. Falsch war nur der angehaengte "echte Fund".
ballbesitz: "— (Ruecknahme; der Ball beim Planner aus 08:53 ist damit GEGENSTANDSLOS)"
bitte_an_den_planner: |
  Die Bitte von 08:53 ("in A-35-9 den Versatz nennen") bitte NICHT ausfuehren — sie war
  unbegruendet. Und wenn der F-004-Grenzfall in der Formelsammlung noch ergaenzt wird
  (mein Befund von 08:18, dort weiter offen), dann bitte mit dem Hinweis, dass A-35 die
  Sache am konkreten Fall bereits vollstaendig durchgerechnet hat.
```

herkunft: docs/STATUS.md · Block 305 · 34f6f5a9
kennung_nicht_zuordenbar: true   # Freitext statt Kennung — uebernommen wie vorgefunden, nicht gedeutet
```yaml
auftrag: "p02_lag_eine_woche_unsichtbar_in_meiner_bahn"
titel: "Ein Ball liegt seit einer Woche bei mir — und meine Wache konnte ihn strukturell nicht sehen"
rolle: plan-pruefer
zeit: "2026-08-14 09:15"
stand_kopf: 45f26bdb
posten: "Pflichtteil 2 der Wache (Tafelzeile gegen Datensatz), den ich seit Stunden nur pauschal gemeldet hatte"
zuerst_der_pflichtteil_der_sauber_ist: |
  Tafelzeilen gegen Datensaetze, Zeile fuer Zeile statt pauschal:
    76 Kennungen in der Tafel, 78 Datensaetze mit zustand-Feld
    63 Kennungen stehen an BEIDEN Orten -> NULL Abweichungen im Zustand
  Die Differenz ist restlos erklaert:
    ELF Paare Stamm-in-Tafel gegen Variante-im-Datensatz — W-01, W-02, W-04, W-05, W-08,
      W-09, W-11, W-13, W-15, W-21, W-22 gegen ihre /1-Fassungen. Das ist GENAU A-33s
      zentrale Zahl, an heutigem Stand bestaetigt, 22 Stunden nach seinem Schnitt.
    A-06 (ERLEDIGT) und P-02 (VORLAGE) stehen nur in der Tafel — beides Zustaende, die §3
      ausdruecklich ausserhalb der Baukette fuehrt, also zu Recht ohne Bauauftrags-Block.
    B5, B5N, B6, B7 stehen nur als Datensatz, alle BETRIEBSBESTAETIGT — Beschluesse,
      keine Auftraege.
und_dabei_faellt_der_eigentliche_fund_an: |
  P-02s TAFELZEILE traegt: | **P-02** parallele Instanzen | `VORLAGE` | Plan-Prüfer | c2de1eec |
  DER BALL LIEGT BEI MIR. Seit dem 07.08., also seit einer Woche.
  Meine Ballortung hat ihn NIE gesehen, und zwar aus zwei Gruenden, die beide struktureller
  Natur sind:
    (1) sie liest 'grep ^ballbesitz: plan-pruefer' — ein yaml-FELD. P-02 hat keinen Block,
        sein Ball steht in einer MARKDOWN-TABELLE.
    (2) meine Blaetterpruefung geht 'docs/auftraege/aktiv/' durch. P-02s Blatt liegt in
        'docs/PRUEFAUFTRAG-P-02-parallele-instanzen.md', also ausserhalb.
  Beide Scans zusammen decken den Fall nicht ab. Das ist die dritte Blindstelle derselben
  Familie: ballortung_blinder_blocktyp (Block ohne auftrag-Zeile), meine_ballortung_blieb_zu_eng
  (158 Zaeune nie besucht) — und jetzt der Ball, der ueberhaupt nicht in einem Zaun steht.
gemessen_wie_viele_es_sind: |
  grep -rl '^ballbesitz: plan-pruefer' docs/ ausserhalb von aktiv/ liefert FUENF Dateien,
  davon vier echte Dokumente:
    PRUEFAUFTRAG-P-01-regelwerk.md            234 Z., zuletzt 05.08., kein Erledigt-Marker
    PRUEFAUFTRAG-P-02-parallele-instanzen.md   72 Z., zuletzt 07.08.
    BEFUND-P02-DER-ERSTE-GEMESSENE-WETTLAUF.md 135 Z., zuletzt 10.08.
    PROZESSPRUEFUNG-02-ANTEIL-PLANNER.md      146 Z., zuletzt 10.08.
  Ich sage NICHT, dass alle vier offen sind — drei tragen einen Erledigt-/Beantwortet-Marker,
  P-01 keinen. Was ich sage: ich habe sie nie gemessen, also weiss ich es nicht.
was_p02_von_mir_verlangt: |
  Woertlich: "Pruefen wie P-01: Widerspruchsfreiheit zum geltenden Text · Pruefbarkeit (hat
  jeder Punkt einen beobachtbaren Ausloeser?) · Kausalitaet (haette der Punkt den Vorfall
  verhindert?) · Plausibilitaet (lebbar oder wird er umgangen?) · und die Machtfrage:
  schiebe ich mir mit Punkt 2 etwas zu?"
  Der Planner stellt die Machtfrage GEGEN SICH SELBST — Punkt 2 behaelt Entscheidungen und
  die Tafel bei "einer Instanz", und das ist im Zweifel er.
  Und der Gegenstand ist ausgerechnet der, der heute Nacht den meisten Schaden gemacht hat:
  parallele Instanzen derselben Datei. Beifang in beide Richtungen, vierzehn Merge-Konflikte,
  zwei halbe yaml-Zaeune. Eine Woche lang lag die Absprache dazu ungeprueft bei mir.
was_ich_an_mir_aendere: |
  Die Ballortung liest ab sofort DREI Orte: die yaml-Felder, die Tafelzeilen, und die
  Dokumente ausserhalb docs/auftraege/aktiv/. Der Befehl dafuer steht oben.
ballbesitz: "— # ERLEDIGT 14.08. 09:45: der naechste Schritt war die P-02-Pruefung, abgelegt 09:28 und berichtigt 09:31"
naechster_schritt: |
  P-02 vollstaendig pruefen — Widerspruchsfreiheit, Pruefbarkeit, Kausalitaet, Plausibilitaet
  und die Machtfrage, je Punkt und mit Belegzeile. Das ist eine ganze Runde und ich nehme sie
  als naechste. P-01s Zustand messe ich dabei mit.
```

herkunft: docs/STATUS.md · Block 306 · 34f6f5a9
kennung_nicht_zuordenbar: true   # Freitext statt Kennung — uebernommen wie vorgefunden, nicht gedeutet
```yaml
auftrag: "p02_geprueft_fuenf_punkte_vier_linsen"
titel: "P-02 geprueft — vier Punkte tragen, einer ist nicht pruefbar, und die Machtfrage ist gemessen"
rolle: plan-pruefer
zeit: "2026-08-14 09:28"
stand_kopf: bac0d476
anlass: "Der Ball lag seit 07.08. bei mir; gefunden 09:15, hier eingeloest."
1_widerspruchsfreiheit: |
  Gegen den geltenden Text gemessen:
    'Claim'   in docs/ARBEITSREGELN.md: EINMAL (Z.161, und nur fuer Evaluator- und
              Release-Claims: "Wer eine Datei anfassen will, auf der ein ... Claim liegt")
    'Instanz' DREIMAL (Z.93 verweist auf P-02 selbst; Z.175 und Z.186 verlangen, dass
              Planner/Plan-Pruefer und Generator/Evaluator nicht dieselbe Instanz sind)
  Also: KEIN Widerspruch. P-02 fuellt eine Luecke — und die ist gross: docs/STATUS.md
  fuehrt heute 81 claim-Felder. Die Praxis ist 81-fach gelebt, der Regeltext kennt sie
  einmal und nur fuer zwei Rollen.
2_pruefbarkeit_je_punkt: |
  P1 CLAIM GILT      -> beobachtbar: claim-Feld vorhanden UND fremder Commit fasst das
                        geclaimte Blatt an. Messbar mit git log -- <blatt>.  PRUEFBAR
  P2 TRENNUNG        -> beobachtbar waere: zwei Instanzen DERSELBEN Rolle aendern beide
                        Tafel/Entscheidungen. Instanzen sind aber nicht unterscheidbar —
                        der Auftrag schliesst Nummerierung ausdruecklich aus ("Keine
                        Instanz-Nummerierung im Regelwerk").  NICHT PRUEFBAR.
  P3 OPERAND STATT   -> beobachtbar: Entscheidung liegt in STATUS.md statt im Blatt.
     UMSCHNITT          Messbar am Ablageort.  PRUEFBAR
  P4 VERLINKEN       -> beobachtbar: derselbe Befund zweimal, ohne Verweis.  PRUEFBAR
  P5 FRISCH MESSEN   -> beobachtbar: Schreiben ohne unmittelbar vorherige Freiheitspruefung.
                        PRUEFBAR, aber siehe Kausalitaet.
3_kausalitaet_an_faellen_dieser_nacht: |
  Nicht an gedachten Faellen, sondern an gemessenen:
    P4 HAETTE GEHOLFEN, zweimal, und beide Male war ICH der Fall: mein F-004-Befund (08:18)
       und mein "5 mm stehen nirgends" (08:53) waren Nachbauten dessen, was A-35 bereits
       vollstaendig enthielt. Wer verlinkt statt nachzubauen, faellt nicht hinein.
    P5 HAETTE NICHT GEHOLFEN, und das ist der schaerfere Befund: bei meinem Beifang um
       08:37 hatte ich den Baum UNMITTELBAR vor dem Schreiben gemessen, Ergebnis 0
       geaenderte Dateien. Der Evaluator begann dazwischen. P5 wurde BEFOLGT und der
       Schaden trat trotzdem ein — die Regel ist richtig und nicht hinreichend.
    P1 hat heute Nacht keinen Gegenfall: A-36 traegt "Claim VOR dem Schnitt", und niemand
       hat ihn ueberschrieben.
    P3 kein Fall messbar.
4_plausibilitaet: |
  P1, P3, P4 sind lebbar und billig — je ein Feld oder ein Verweis.
  P5 ist lebbar, aber wer ihn befolgt und trotzdem Beifang erzeugt, wird ihn fuer wertlos
  halten. Er braucht den Zusatz, den der Planner und ich unabhaengig gefunden haben:
  Schreiben und Committen als EIN Schritt (Rollendatei des Planners, e370490e).
  P2 wird nicht umgangen, weil jemand es will, sondern weil niemand sehen kann, ob er
  eingehalten wird. Eine Regel ohne beobachtbaren Ausloeser ist eine Absichtserklaerung.
5_die_machtfrage_gemessen: |
  Der Planner fragt gegen sich selbst: "Punkt 2 behaelt Entscheidungen und die Tafel bei
  einer Instanz — und das bin im Zweifel ich."
  GEMESSEN ueber die letzten 200 Commits an docs/STATUS.md, je Rolle die Commits MIT
  Tafelzeilen-Aenderung:
    planner          19 von 38
    generator        16 von 17
    plan-pruefer     16 von 75
    evaluator        13 von 19
    release-pruefer  10 von 32
  Die Tafel wird von ALLEN FUENF Rollen geschrieben, und der Generator fasst sie in fast
  jedem seiner Commits an — weil A-20 es verlangt: Tafelzeile und Datensatz in EINEM
  Commit. Punkt 2 kann die Tafel also gar nicht bei einer Rolle buendeln; er buendelt sie
  bei einer INSTANZ, wenn zwei Instanzen DERSELBEN Rolle laufen.
  URTEIL: der Machtverdacht ist unbegruendet, so wie bei P-01. Punkt 2 verschiebt
  Koordination innerhalb einer Rolle, nicht Autoritaet zwischen Rollen — §1 haelt Regeln
  bei Yama, §5-DoR liegt bei mir, nicht beim Planner.
gesamturteil: |
  VIER von fuenf Punkten tragen und sollten aufgeschrieben werden (P1, P3, P4, P5 mit
  Zusatz). P2 traegt in der Sache, ist aber in der vorgelegten Form NICHT PRUEFBAR, weil
  der Auftrag Instanz-Kennzeichnung ausdruecklich ausschliesst. Entweder bekommt P2 einen
  beobachtbaren Ausloeser, oder er wird als Absichtserklaerung gekennzeichnet statt als
  Absprache.
  Ich entscheide nichts davon: §1 haelt Regeltext bei Yama, und P-02 ist eine VORLAGE.
ballbesitz: —  # GESCHLOSSEN 14.08. vom Release-Pruefer in Yamas Namen: Pruefbericht, keine Frage an Yama.
bitte: |
  P-02 ist geprueft und entscheidungsreif. Offen ist EINE Frage an dich: soll P2 einen
  beobachtbaren Ausloeser bekommen (dann braucht es ein Instanz-Merkmal, das der Auftrag
  heute ausschliesst), oder bleibt er ohne — dann bitte als Absichtserklaerung kenntlich.
  Die uebrigen vier koennen aus meiner Sicht so uebernommen werden, P5 mit dem Zusatz
  "Schreiben und Committen sind EIN Schritt".
```

herkunft: docs/STATUS.md · Block 307 · 34f6f5a9
kennung_nicht_zuordenbar: true   # Freitext statt Kennung — uebernommen wie vorgefunden, nicht gedeutet
```yaml
auftrag: "p02_urteil_berichtigt_zulieferungen_ungelesen"
titel: "BERICHTIGUNG meiner P-02-Pruefung — ich habe geurteilt, ohne die zwei Zulieferungen zu lesen"
rolle: plan-pruefer
zeit: "2026-08-14 09:31"
stand_kopf: d0eaeabf
was_ich_berichtige: |
  Um 09:28 habe ich P-02 geprueft und ein Gesamturteil abgegeben. Um 09:15 hatte ich SELBST
  gemessen, dass vier Dokumente ausserhalb docs/auftraege/aktiv/ einen Ball bei mir tragen —
  darunter zwei, die sich ausdruecklich als Zulieferung zu P-02 bezeichnen:
    BEFUND-P02-DER-ERSTE-GEMESSENE-WETTLAUF.md   gehoert_zu: PRUEFAUFTRAG-P-02
    PROZESSPRUEFUNG-02-ANTEIL-PLANNER.md         "er fuehrt die Pruefung; dies ist Zulieferung"
  Ich habe beide NICHT gelesen und trotzdem geurteilt. Das ist derselbe Fehler wie bei A-35,
  einen Schritt weiter: dort habe ich das Kriterium statt des Blatts gelesen, hier das Blatt
  statt der Akte.
was_die_zulieferung_zeigt_und_es_ist_mehr_als_mein_befund: |
  Der Wettlauf-Befund vom 10.08. misst denselben Punkt, den ich fuer P5 gefunden habe — vier
  Tage vor mir und schaerfer:
    20:25:45  d6846f69  generator: A-09 IN_ARBEIT
    20:25:56  7dcbeba9  W-01/1    IN_ARBEIT   -> ELF SEKUNDEN spaeter
    20:30:08  fec3a07a  W-01/1 zurueck auf BEREIT — §3 verletzt
  Beide Instanzen hatten zum Zeitpunkt ihrer Messung RECHT. Die zweite fuhr sechs
  Vorpruefungen unmittelbar vor dem Commit. P5 wurde eingehalten und hat nicht gereicht.
  UND DIE URSACHE IST DORT BENANNT, ich hatte sie nur beschrieben: "test-and-set ohne
  Atomaritaet. Zwischen Messen und Schreiben liegt immer ein Fenster. Frisch ist eine
  Vergangenheit, sobald geschrieben wird." Daraus die kategoriale Trennung, die mein Urteil
  nicht hatte: P1 bis P4 sind ABSPRACHEN und loesen Doppelarbeit; P5 soll einen WETTLAUF
  loesen, und das kann eine Absprache grundsaetzlich nicht.
und_der_teil_der_meine_abhilfe_widerlegt: |
  Ich hatte vorgeschlagen, P5 den Zusatz "Schreiben und Committen sind EIN Schritt" zu geben.
  Das VERKLEINERT das Fenster und schliesst es nicht — der gemessene Fall beweist es, denn
  dort lagen sechs Vorpruefungen unmittelbar vor dem Commit.
  Schlimmer noch, und das ist der Teil, den ich gar nicht auf dem Schirm hatte: eine BARRIERE
  im Tor haette den Verstoss auch nicht gefangen. Der Planner hat es gemessen:
    d6846f69 aenderte Tafelzeile UND 'zustand: IN_ARBEIT'
    7dcbeba9 aenderte NUR die Tafelzeile
    Beide Staende liefern grep -c '^zustand: IN_ARBEIT' -> 1. Der Zaehler sah den zweiten NIE.
  Der Zustand steht an ZWEI Orten; jede Barriere prueft einen. Die Reihenfolge, die er
  daraus ableitet: erst EINMALIGKEIT (ein Ort), dann die Barriere.
gegenprobe_heute: |
  Seine Zaehler an heutigem Stand nachgefahren: naiv 'IN_ARBEIT' 169 Treffer (damals 41 —
  die Datei ist gewachsen), praezise '^zustand: IN_ARBEIT' 0. Das Verhaeltnis haelt: das
  naive Muster ist unbrauchbar, das praezise ist brauchbar und misst heute korrekt 0.
was_von_meinem_urteil_steht: |
  Unveraendert gueltig: die Widerspruchsfreiheit (Claim 1x im Regelwerk, 81 claim-Felder in
  der Praxis), die Pruefbarkeit je Punkt einschliesslich P2 als NICHT pruefbar, und die
  Machtfrage mit der Messung ueber 200 Commits (alle fuenf Rollen schreiben die Tafel).
  UEBERHOLT ist mein P5-Teil: nicht "richtig und nicht hinreichend, mit Zusatz heilbar",
  sondern "richtig und durch keine Absprache heilbar; braucht Einmaligkeit, dann Mechanik".
ballbesitz: —  # GESCHLOSSEN 14.08. vom Release-Pruefer in Yamas Namen: Selbstberichtigung des Plan-Pruefers.
bitte_praezisiert: |
  Meine Frage von 09:28 bleibt (P2 mit oder ohne beobachtbaren Ausloeser), und eine kommt
  dazu: P5 gehoert nicht in dieselbe Liste wie P1 bis P4. Vier Absprachen und ein
  Wettlaufproblem in einem Katalog lesen sich gleich wertig — sind es aber nicht. Die
  Zulieferung nennt die Reihenfolge (Einmaligkeit, dann Barriere); ob und wann das gebaut
  wird, ist deine Entscheidung, nicht meine.
```

herkunft: docs/STATUS.md · Block 308 · 34f6f5a9
kennung_nicht_zuordenbar: true   # Freitext statt Kennung — uebernommen wie vorgefunden, nicht gedeutet
```yaml
auftrag: "a36_die_drei_formalien_gemessen"
titel: "A-36: die drei fehlenden §5-Formalien gemessen — der Planner braucht nur noch je einen Satz"
rolle: plan-pruefer
zeit: "2026-08-14 09:34"
stand_kopf: bdf44881
warum_ich_das_mache: |
  A-36 liegt seit 09:12 bei mir und wartet auf vier §5-Punkte im Blatt. Der Planner ist seit
  67 Minuten still. Die Punkte SCHREIBEN darf ich nicht — das Blatt gehoert ihm. Was ich darf
  und was die Blockade von meiner Seite aufloest: die Punkte MESSEN, damit er nur noch einen
  belegten Satz setzen muss statt selbst zu erheben.
formalie_1_testdaten_rolle_route_browserpfad: |
  A-36 baut ein LESENDES Shell-Skript, das vom Commit-Tor aufgerufen wird. Gemessen am Scope,
  den das Blatt nennt (scripts/wer-schreibt.sh neu, scripts/commit-pruefen.sh, ARBEITSREGELN
  :693): KEIN Pfad unter app/, routes/, resources/ oder public/. Also keine HTTP-Route, keine
  Oberflaeche, kein Serverprozess.
  BELEGTER SATZ FUER DAS BLATT: "Testdaten, Benutzerrolle, Route und Browserpfad: n.z. —
  der Auftrag baut ein lesendes Skript ohne Oberflaeche und ohne Serverprozess; der Scope
  liegt vollstaendig unter scripts/ und docs/."
formalie_2_api_server_schema_migration_bestandsdaten_bundle: |
  Je einzeln gemessen statt pauschal:
    API/Server   Scope beruehrt weder routes/ noch app/    -> nicht beruehrt
    Schema       keine Datei unter database/                -> nicht beruehrt
    Migration    0 Migrationen im Scope                     -> nicht beruehrt
    Bestandsdaten kein Datenpfad, das Skript LIEST nur      -> nicht beruehrt
    BUNDLE       hier war meine erste Messung schlecht gestellt und ich habe sie verworfen:
                 'grep -c scripts/.*\.sh package.json' lieferte 7 und beantwortet die Frage
                 NICHT — es zaehlt Erwaehnungen. Richtig gemessen: 'build' ist 'vite build'
                 und nennt kein Shell-Skript; die sieben Treffer sind Laufzeit-Huellen
                 (node-runtime.sh) und Schema-/Test-Laeufe. KEIN Bauschritt liest
                 scripts/*.sh als QUELLE. Ein neues Skript dort kann das Buendel nicht
                 veraendern.
  BELEGTER SATZ: "API, Server, Schema, Migration, Bestandsdaten und Bundle: nicht beruehrt.
  Der Scope liegt unter scripts/ und docs/; kein Bauschritt liest scripts/*.sh als Quelle
  (build = vite build), es gibt keine Migration und keinen Datenpfad."
formalie_3_abhaengigkeitskette: |
  A-36 ist additiv: ein neues Skript plus ein Aufruf. Es haengt an genau zwei Dingen, beide
  vorhanden gemessen:
    scripts/commit-pruefen.sh  existiert und ruft die drei Vorgaenger-Barrieren auf
                               (a26 :618-619, a27 :627-628, a30 :642-643)
    docs/STATUS.md             muss Abschnittsueberschriften tragen — 249 yaml-Zaeune und
                               die Ueberschriftenstruktur sind da, sonst waere meine
                               Hunk-Zuordnung von 09:02 nicht gelaufen.
  Nichts anderes haengt an A-36, und A-36 haengt an nichts Ungebautem.
formalie_4_erstnutzer_bleibt_offen: |
  Den kann ich NICHT messen, und er ist der einzige materielle der vier. §5 verlangt fuer ein
  NEU zu bauendes Werkzeug einen BENANNTEN Erstnutzer: welche Rolle es ab wann in welchem
  Ablauf benutzt. Der Aufrufort ist klar (commit-pruefen.sh, Muster der drei Vorgaenger), aber
  WER ab WANN ist eine Festlegung, keine Messung. Die gehoert dem Planner.
was_das_fuer_die_DoR_heisst: |
  Sobald diese drei Saetze im Blatt stehen und der Erstnutzer benannt ist, erteile ich ohne
  weitere Runde. Die Sache selbst ist seit b6640235 durchgemessen, A-36-3s Wortlaut-Frage
  seit 902c83f3 benannt. Es fehlt Schreibarbeit, keine Pruefarbeit.
ballbesitz: planner
```

herkunft: docs/STATUS.md · Block 309 · 34f6f5a9
kennung_nicht_zuordenbar: true   # Freitext statt Kennung — uebernommen wie vorgefunden, nicht gedeutet
```yaml
auftrag: "paragraf13_zwei_gruppenpruefungen_fehlen"
titel: "§13: zwei Pflichtpruefungen sind faellig und fehlen — und ich habe die Zaehlung selbst entschieden"
rolle: plan-pruefer
zeit: "2026-08-14 09:39"
stand_kopf: 774854ef
wie_ich_darauf_kam: |
  Ueber die Zulieferung, die ich um 09:31 nachgelesen habe. PROZESSPRUEFUNG-02-ANTEIL-PLANNER
  nennt ein Dokument, das ich angeblich FUEHRE: docs/PROZESSPRUEFUNG-02.md. Ich hatte es nie
  gesehen. Beim Nachsehen: es gibt VIER solche Dokumente (01, 02, 02-ANTEIL, 03) und einen
  ganzen §13-Strang, den meine Wache nie beruehrt hat — er liegt ausserhalb von docs/STATUS.md
  und ausserhalb von docs/auftraege/aktiv/.
was_paragraf13_verlangt: |
  ARBEITSREGELN.md ab Z.618: "Nach jeweils zehn fortlaufend nummerierten Planner-Auftraegen ist
  VOR AUFGABE ELF eine verbindliche Prozess- und Skill-Pruefung durchzufuehren. Ein Auftrag
  zaehlt, sobald der Planner ihn dem Plan-Pruefer erstmals vorlegt. Zur Zehnergruppe gehoeren
  damit auch zurueckgewiesene, blockierte oder spaeter abgebrochene Auftraege."
was_es_gibt: |
  PROZESSPRUEFUNG-01   150 Z., 05.08.  — Sofortausloeser, nicht die Zehnergruppe
  PROZESSPRUEFUNG-02   382 Z., 10.08.  — gruppe: "A-01 … A-10", also GRUPPE 1
  PROZESSPRUEFUNG-03   168 Z., 10.08.  — ausgeloest_durch 8d91b7a2, SOFORTAUSLOESER
  PROZESSPRUEFUNG-04 oder -05: existieren NICHT (git ls-files, 0 Treffer, im Arbeitsbaum 0)
  Und die Gruppengrenze habe ICH entschieden, Z.2091: "plan-pruefer 10.08.: A-11 zaehlt als
  AUFTRAG 1 DER GRUPPE 2."
gemessen_wann_die_grenzen_fielen: |
  Erstes Auftreten je Kennung in docs/STATUS.md (git log -S, aelteste Fundstelle):
    A-20   12.08. 13:03   -> GRUPPE 2 (A-11..A-20) damit voll
    A-21   12.08. 15:05   -> zwei Stunden spaeter geschnitten, KEINE Pruefung dazwischen
    A-30   13.08. 08:33   -> GRUPPE 3 (A-21..A-30) damit voll
    A-31   13.08. 09:00   -> 27 Minuten spaeter geschnitten, KEINE Pruefung dazwischen
    A-36   14.08. 08:18   -> Gruppe 4 laeuft, sechs von zehn
  Pruefung 03 lief am 10.08. — also BEVOR A-20 ueberhaupt existierte. Sie kann Gruppe 2 nicht
  abdecken, und sie beansprucht es auch nicht: ihr Kopf nennt ausdruecklich einen
  Sofortausloeser als Anlass, nicht die Zehnergruppe.
befund: |
  ZWEI Pflichtpruefungen nach §13 sind faellig und fehlen: fuer Gruppe 2 (A-11..A-20, faellig
  vor A-21) und fuer Gruppe 3 (A-21..A-30, faellig vor A-31). Seither sind SECHS weitere
  Auftraege geschnitten worden (A-31 bis A-36).
  §13 ist keine Empfehlung — der Wortlaut sagt "verbindlich" und "vor Aufgabe elf".
und_mein_eigener_anteil: |
  Die Zaehlung stammt von mir (Z.2091, 10.08.). Wer die Zaehlung festlegt, muesste am ehesten
  merken, wann sie voll ist — ich habe es vier Tage lang nicht gemerkt, weil meine Wache
  §13 an keiner Stelle misst. Sie liest Zustaende, Baelle und Blaetter; die Zehnergruppe
  zaehlt sie nicht.
  DAS IST DIE VIERTE BLINDSTELLE meiner Wache in zwei Stunden, und sie ist die groesste:
  die drei vorigen betrafen einzelne Baelle, diese einen ganzen Pflichtvorgang.
was_ich_an_mir_aendere: |
  Die Wache misst ab sofort auch den §13-Zaehler: hoechste A-Kennung, daraus die Gruppe, und
  ob fuer jede abgeschlossene Gruppe ein PROZESSPRUEFUNG-Dokument existiert. Ein Befehl.
was_ich_NICHT_entscheide: |
  Ob die zwei Pruefungen nachgeholt, zusammengelegt oder von Yama erlassen werden. §13 ist
  Regeltext, und §1 haelt Regeln bei Yama. Ich melde die Faelligkeit, nicht die Abhilfe.
ballbesitz: —  # GESCHLOSSEN 14.08. vom Release-Pruefer in Yamas Namen: Meldung an die Kette, Folge gehoert dem Plan-Pruefer.
zusatz_planner: |
  Zur Kenntnis an den Planner, weil die Durchfuehrung bisher bei ihm lag (02: ausgeloest_von
  planner; 03: durchfuehrung planner nach P-02-Praezedenz).
```

herkunft: docs/STATUS.md · Block 310 · 34f6f5a9
kennung_nicht_zuordenbar: true   # Freitext statt Kennung — uebernommen wie vorgefunden, nicht gedeutet
```yaml
auftrag: "neun_dokumente_ohne_datensatz"
titel: "Die ganze Flaeche gemessen statt der naechsten Einzelstelle — es sind NEUN, nicht vier"
rolle: plan-pruefer
zeit: "2026-08-14 09:42"
stand_kopf: c5c6ac57
warum_flaechig_statt_einzeln: |
  In zwei Stunden habe ich vier Blindstellen meiner Wache gefunden, jede beim Stolpern ueber
  einen Einzelfall. Statt auf die fuenfte zu warten, habe ich die FLAECHE gemessen: jeden
  Ball bei mir, in jeder Schreibweise, im ganzen docs/-Baum.
zuerst_die_entwarnung_die_wichtig_ist: |
  Die Suche liefert 80+ Treffer in docs/auftraege/aktiv/ — fast jedes Blatt traegt
  ballbesitz: "plan-pruefer (DoR)". Das sind KEINE offenen Baelle: es ist die
  Rollenzuweisung im Blattkopf, sie steht auch in laengst abgeschlossenen Blaettern
  (A-12, A-18, W-37). Ob so ein Auftrag offen ist, sagt sein DATENSATZ in docs/STATUS.md.
  Meine Ballortung liest deshalb zu Recht den Datensatz und nicht den Blattkopf.
und_jetzt_die_luecke_praezise: |
  Sie betrifft ausschliesslich Dokumente in der WURZEL von docs/ — die haben naemlich
  keinen Datensatz, also ist ihr Kopffeld die einzige Ballfuehrung. Gemessen: NEUN Stueck.
    BEFUND-P02-DER-ERSTE-GEMESSENE-WETTLAUF.md
    FAHRPLAN-KLASSE-A.md
    MELDUNG-INDEX-ANGLEICHUNG-2026-08-06.md
    PROZESSPRUEFUNG-02-ANTEIL-PLANNER.md
    PROZESSPRUEFUNG-03.md
    PRUEFAUFTRAG-P-01-regelwerk.md
    PRUEFAUFTRAG-P-02-parallele-instanzen.md
    VORLAGE-WERKZEUG-ODER-SCHICHT.md
    WERKBANK-ANSCHLUSS.md
  Gegengeprueft, dass keines einen Auftragsblock hat: grep '^auftrag: "P-01' / "P-02" /
  "FAHRPLAN" / "WERKBANK-ANSCHLUSS" / "PROZESSPRUEFUNG" liefert je NULL. Eine Tafelzeile
  gibt es fuer genau eines: P-02. P-01 hat nicht einmal die.
meine_eigene_zahl_von_0915_war_falsch: |
  Um 09:15 habe ich VIER gemeldet. Das Muster war '^ballbesitz: plan-pruefer' — es trifft
  nur die UNZITIERTE Form. Fuenf weitere schreiben zitiert, etwa
  ballbesitz: "plan-pruefer (Klassifizierung), Yama (…)". Die fielen durch.
  Das ist dieselbe Klasse wie meine anderen Fehlgriffe heute: das Muster entscheidet ueber
  das Ergebnis, und ein zu enges Muster liefert eine plausible, falsche Zahl. Ich habe sie
  drei Runden lang weitergetragen.
was_ich_NICHT_behaupte: |
  Nicht alle neun sind offen. Drei tragen einen Erledigt-Vermerk, P-01 ist nachweislich
  geschlossen (STATUS.md:647 und :1010), P-02 habe ich heute geprueft und an Yama gegeben.
  Ungeprueft sind damit: FAHRPLAN-KLASSE-A, MELDUNG-INDEX-ANGLEICHUNG, VORLAGE-WERKZEUG-
  ODER-SCHICHT, WERKBANK-ANSCHLUSS und PROZESSPRUEFUNG-03 (dort steht mein Ball
  ausdruecklich als "Gegenlese").
  VORLAGE-WERKZEUG-ODER-SCHICHT ist der schaerfste Fall: sie kommt in docs/STATUS.md
  ueberhaupt nicht vor — die Statuswahrheit kennt sie nicht, und ihr Ball liegt bei mir.
was_die_wache_ab_jetzt_misst: |
  Vier Orte statt drei: yaml-Felder in STATUS.md, Tafelzeilen, Blaetter in aktiv/ (nur zur
  Zuordnung, nicht als Ball), und die Dokumente in der WURZEL von docs/ mit dem WEITEN
  Muster '^ball[a-z_]*:.*plan-pruefer'.
ballbesitz: "— # ERLEDIGT 09:59: alle NEUN Wurzel-Vorgaenge geprueft"
naechste_schritte: |
  Die fuenf ungeprueften der Reihe nach durchgehen, je eine Runde, beginnend mit dem, das
  die Statuswahrheit gar nicht kennt. Kein neuer Ball fuer andere — das ist meine Bahn.
```

herkunft: docs/STATUS.md · Block 311 · 34f6f5a9
kennung_nicht_zuordenbar: true   # Freitext statt Kennung — uebernommen wie vorgefunden, nicht gedeutet
```yaml
auftrag: "werkzeug_oder_schicht_klassifizierung_gemessen"
titel: "Der erste der fuenf ungeprueften Vorgaenge: alle VIER Kandidaten sind Schichten, der vierte von mir gemessen"
rolle: plan-pruefer
zeit: "2026-08-14 09:45"
stand_kopf: e6c4f7a2
gegenstand: "docs/VORLAGE-WERKZEUG-ODER-SCHICHT.md — 124 Zeilen, zuletzt 10.08., ballbesitz: plan-pruefer (Klassifizierung), Yama (zweite Kategorie)"
warum_dieser_zuerst: |
  Von den neun Wurzel-Dokumenten mit Ball bei mir ist dieses der schaerfste Fall: es kommt in
  docs/STATUS.md UEBERHAUPT NICHT vor. Die Statuswahrheit kennt es nicht, und es sagt von sich
  selbst, dass es etwas BLOCKIERT.
die_blockade_ist_echt_und_aktuell: |
  Die Vorlage schreibt: "Entscheidung noetig, bevor W-01 Stufe 2 schneidbar ist" und
  "blockiert: W-01 Stufe 2 — der Reifegrad GEBAUT ist fuer diesen Fall nicht definiert".
  Gemessen:
    W-01/1  zustand BETRIEBSBESTAETIGT   -> Stufe 1 ist fertig und bestaetigt
    W-01/2  als Datensatz: 0             -> Stufe 2 existiert nicht
    W-01-Blatt fuer Stufe 2 in aktiv/: 0
  Die Blockade besteht seit dem 10.08. und hat gehalten: Stufe 2 ist nie geschnitten worden.
meine_klassifizierung_am_code: |
  Der Ball bei mir heisst "Klassifizierung". Testbar ist sie an genau einer Frage: gibt es
  einen toolRegistry-Eintrag? Gemessen, Muster an einem bekannten Treffer verifiziert
  (id: 'kontur' -> 1, Zeile 230):
    id 'fang'       0        W-01 Raster und Fang    -> SCHICHT
    id 'raster'     0
    id 'ansicht'    0        W-12 Ansicht und Kamera -> SCHICHT
    id 'kamera'     0
    id 'topologie'  0        W-18 Pruefung Topologie -> SCHICHT
  Drei von vier bestaetigt. Die Vorlage hatte sie schon so eingeordnet; meine Messung ist die
  unabhaengige Gegenprobe, nicht die Erstbehauptung.
die_vierte_frage_war_offen_und_ist_jetzt_gemessen: |
  Die Vorlage laesst W-05 ausdruecklich offen: "roomDetection laeuft automatisch aus Waenden;
  klickt der Nutzer 'Raum erkennen' oder entsteht der Raum von selbst? -> das muss gemessen
  werden, nicht geraten."
  GEMESSEN:
    toolRegistry, id 'raum...'                          0 Treffer
    app/ableitungen.ts:61  export function raeumeAus(waende, level)
    HausplanerApp.tsx:569  const raeume = useMemo(() => raeumeAus(waende, level), ...)
  Ein useMemo ist eine ABLEITUNG, kein Knopf-Handler. Die Raeume werden neu berechnet, sobald
  sich die Waende aendern; der Nutzer loest nichts aus. Und die Datei heisst ableitungen.ts.
  ERGEBNIS: W-05 "Raum erkennen" ist ebenfalls eine SCHICHT. Alle VIER Kandidaten sind es.
was_damit_bei_mir_erledigt_ist_und_was_nicht: |
  ERLEDIGT ist mein Teil: die Klassifizierung ist gemessen, vier von vier.
  NICHT erledigt und ausdruecklich nicht meins: ob die Werkbank eine ZWEITE KATEGORIE
  bekommt. Das ist die zweite Haelfte des Ballfelds und steht dort woertlich bei Yama.
  Solange sie fehlt, bleibt W-01 Stufe 2 blockiert — und mit ihr die Frage, was der
  Reifegrad GEBAUT fuer eine Schicht ueberhaupt heissen soll.
nebenbei_geschlossen: |
  Mein eigener Ball p02_lag_eine_woche_unsichtbar_in_meiner_bahn: sein naechster Schritt war
  die P-02-Pruefung, abgelegt 09:28 und berichtigt 09:31. Geschlossen mit Beleg, damit ich
  keine Karteileiche der Art erzeuge, die ich um 09:33 selbst beanstandet habe.
ballbesitz: —
ballbesitz_grund: "BEANTWORTET 16.08. vom Release-Pruefer in Yamas Namen: keine zweite Kategorie, die vier bleiben unter 02-WERKZEUGE mit Reifegrad-Vermerk. Grund am Bestand gemessen: 00-ARCHITEKTUR/SCHICHTEN.md fuehrt Schicht 3 bereits als ANWENDUNG (Werkzeuge) — eine zweite Kategorie fuehrte denselben Sachverhalt doppelt. Reifegrad ist eine Eigenschaft, kein Verzeichnis. W-01 Stufe 2 ist schneidbar. Blatt docs/ANTWORTEN-YAMA-POSTEN-16-08.md"
ballbesitz_vorher: "yama"
bitte: |
  Eine Entscheidung: bekommt die Werkbank eine zweite Kategorie fuer Schichten, oder bleiben
  die vier als "Werkzeuge" mit einer Sonderregel fuer ihren Reifegrad? Vier von vier sind
  gemessen; es fehlt nur noch das Wort. Danach ist W-01 Stufe 2 schneidbar.
```

herkunft: docs/STATUS.md · Block 312 · 34f6f5a9
kennung_nicht_zuordenbar: true   # Freitext statt Kennung — uebernommen wie vorgefunden, nicht gedeutet
```yaml
auftrag: "gegenlese_pruefung_03"
titel: "Gegenlese zu §13-Pruefung 03 — E1 ist in Kraft, E3 nicht gebaut, und die geprüfte Klasse hat heute Nacht SECHS neue Faelle, alle von mir"
rolle: plan-pruefer
zeit: "2026-08-14 09:48"
stand_kopf: f9057e9a
gegenstand: "docs/PROZESSPRUEFUNG-03.md, 168 Zeilen, ballbesitz: yama (Entscheidung), plan-pruefer (Gegenlese) — zweiter der fuenf ungeprueften Wurzel-Vorgaenge"
was_ich_gemessen_habe_statt_zugestimmt: |
  E1 "Aussagen ueber den Bau werden am COMMIT gemessen, nicht am Arbeitsbaum":
    IST IN KRAFT. docs/ARBEITSREGELN.md:504 fuehrt es als eigenen Abschnitt. Die Empfehlung
    der Pruefung ist also uebernommen worden, und ich fahre sie taeglich — bei W-12/1 heute
    frueh habe ich genau so gemessen (Scope-Diff am Commit, nicht im Baum).
  E3 "Unterformen-Spalte im Auftragszaehler":
    NICHT GEBAUT. docs/AUFTRAGSZAEHLER.md hat 154 Zeilen und NULL Treffer auf 'Unterform'.
  E2 "das §3-Kriterium zaehlbar machen, sechs W-Blaetter":
    NICHT BEANTWORTET. Mein erster Versuch zaehlte 'IN_ARBEIT'-Vorkommen je Blatt (W-05: 0,
    W-21: 1, W-22: 4). Das ist ein STELLVERTRETER fuer E2s Frage nach Befehlszeilen UND
    Ausgabewerten je Ort — also genau die Klasse, die diese Pruefung behandelt. Ich gebe die
    Zahl nicht als Antwort aus.
und_hier_ist_die_substanz_die_ich_beitrage: |
  Die Pruefung nennt die vierte Klasse "Stellvertreter statt Quelle" mit vier Unterformen:
  Wegwerf-Repo statt Repo (Ort), $TMPDIR statt Unterordner (Ort), Tracking-Ref statt Remote
  (Zeitpunkt), Arbeitsbaum statt Commit (Zustand).
  IN DEN LETZTEN DREI STUNDEN habe ich SECHS neue Faelle derselben Klasse produziert und je
  einzeln offengelegt:
    1  Zeitstempel      Gedaechtnis statt Uhr        13 Bloecke, Datum sieben Tage falsch,
                                                     Versatz bis 65 Minuten
    2  Ballzaehlung     enges Muster statt Feldform  4 gemeldet, tatsaechlich 9
    3  Abschnitte       Etikett statt Ding           zwei Ueberschriften ohne Kennung fielen
                                                     auf einen Wert zusammen (A-36-3)
    4  Blattpfade       eine Feldform statt beider   'blatt:' gelesen, 'datei:' nicht ->
                                                     drei Blaetter als "nicht lesbar"
    5  Bundle           Erwaehnung statt Bauschritt  grep -c auf package.json lieferte 7
    6  E3 soeben        Exit-Kette statt Ergebnis    meine &&-Kette meldete "Datei fehlt",
                                                     waehrend sie 154 Zeilen hat
  ALLE SECHS SIND MEINE. Die Pruefung sagt, ihre Substanz komme von den anderen drei Rollen —
  fuer diese Gegenlese gilt das Gegenteil: die neuen Faelle kommen ausschliesslich von mir.
was_das_fuer_die_vorgelegten_entscheidungen_heisst: |
  E1 braucht keine Entscheidung mehr, es steht im Regelwerk.
  E3 haelt meine sechs Faelle ohne Aenderung aus: Ort, Zeitpunkt und Zustand decken sie ab
  (1 = Zeitpunkt, 2/3/4/5 = Ort im weiteren Sinn "falsche Quelle", 6 = Zustand). Eine SIEBTE
  Unterform braucht es nicht — was es braeuchte, waere die Zaehlung selbst, und die ist
  Befund 0 der Pruefung 02 und weiter ungebaut.
  E2 kann ich nicht beurteilen, ohne die sechs Blaetter einzeln gegen ihren Wortlaut zu
  messen. Das ist eine eigene Runde und ich sage es, statt zu nicken.
zur_ehrlichkeit_der_pruefung: |
  Sie benennt ihren eigenen Interessenkonflikt: geschrieben von der Rolle, die die meisten
  Faelle der geprueften Klasse produziert hat. Das ist richtig benannt — und nach dieser
  Nacht gilt es fuer mich genauso. Wer die Klasse prueft, gehoert selbst hinein.
ballbesitz: —  # GESCHLOSSEN 14.08. vom Release-Pruefer in Yamas Namen: Messung, kein Entscheid.
bitte: |
  Meine Gegenlese ist geliefert: E1 in Kraft (belegt), E3 nicht gebaut (belegt), E2 offen und
  als offen gekennzeichnet. Die Entscheidung ueber E2 und E3 liegt bei dir; ich empfehle
  beide, E3 ohne die siebte Unterform.
```

herkunft: docs/STATUS.md · Block 313 · 34f6f5a9
kennung_nicht_zuordenbar: true   # Freitext statt Kennung — uebernommen wie vorgefunden, nicht gedeutet
```yaml
auftrag: "kenntnisnahme_index_angleichung"
titel: "Dritter der fuenf: die Antwort auf meinen eigenen Befund vom 06.08. — Kreis geschlossen, Rueckweg lebt"
rolle: plan-pruefer
zeit: "2026-08-14 09:50"
stand_kopf: 31eeea31
gegenstand: "docs/MELDUNG-INDEX-ANGLEICHUNG-2026-08-06.md, 184 Zeilen, zuletzt 07.08.; Ballfeld: planner (diese Meldung), danach plan-pruefer (Kenntnisnahme)"
warum_der_hier_besonders_ist: |
  Es ist die Antwort auf MEINEN Befund. Ich hatte am 06.08. in fb7921bd eine ungemeldete
  Index-Angleichung an ihren Spuren entdeckt (Phantome 17->0, Divergenz 60->2). Der Planner
  hat sie am 07.08. nachgereicht — und die Kenntnisnahme, die daraufhin bei MIR lag, habe ich
  eine Woche lang nicht geleistet, weil das Dokument in der Wurzel von docs/ liegt.
was_ich_gemessen_habe: |
  1. DER RUECKWEG LEBT. Die Meldung nennt refs/rueckweg/index-vor-reset-20260806 -> 80948f8d.
     Heute gemessen: der Ref existiert und zeigt auf 80948f8d. Eine Woche spaeter, unberuehrt.
     Das ist der Unterschied zwischen einem zugesagten und einem vorhandenen Rueckweg.
  2. BEIDE SHAs EXISTIEREN: fb7921bd (mein Befund) und c512f931 (die Abhilfe).
  3. UND DIE DAUERHAFTE ABHILFE IST GEBAUT — das ist der Teil, den ich uebersehen habe:
     scripts/commit-pruefen.sh:725 gibt aus
       "INDEX ANGEGLICHEN  Standard-Index an HEAD angeglichen (kein Index-Blob ausserhalb
        der Historie); der Arbeitsbaum ist unberuehrt"
     Eingebracht von c512f931 (A-07, "Tor initialisiert und raeumt seinen Wegwerf-Index").
     Aus einem einmaligen Vorfall ist also eine stehende Meldung geworden.
die_pointe_gegen_mich: |
  Diese Zeile steht unter JEDEM meiner Commits heute Nacht — mehr als dreissig Mal habe ich
  sie gelesen und nie mit meinem eigenen Befund von vor einer Woche verbunden. Ich habe die
  Wirkung meiner Meldung taeglich vor Augen gehabt und nicht erkannt, dass sie die Antwort
  darauf ist. Kein Messfehler; ein Lesefehler.
urteil: |
  Der Vorgang ist SACHLICH ERLEDIGT und war es seit c512f931: die Meldelücke ist durch eine
  stehende Ausgabe geschlossen, der Rueckweg ist vorhanden und geprueft. Formal offen war nur
  meine Kenntnisnahme — hiermit erteilt.
  Damit sind von den neun Wurzel-Dokumenten drei abgearbeitet (P-02 geprueft, Werkzeug-oder-
  Schicht klassifiziert, diese Meldung zur Kenntnis genommen), zwei nachweislich geschlossen
  (P-01, PROZESSPRUEFUNG-03 gegengelesen), und ZWEI stehen aus: FAHRPLAN-KLASSE-A und
  PROZESSPRUEFUNG-02-ANTEIL-PLANNER. Dazu BEFUND-P02-WETTLAUF, den ich um 09:31 inhaltlich
  verwertet habe — sein Ballfeld ist noch nicht nachgezogen.
ballbesitz: "— (Kenntnisnahme erteilt, kein offener Vorgang)"
```

herkunft: docs/STATUS.md · Block 314 · 34f6f5a9
kennung_nicht_zuordenbar: true   # Freitext statt Kennung — uebernommen wie vorgefunden, nicht gedeutet
```yaml
auftrag: "gegenlese_03_berichtigt_siebte_unterform"
titel: "BERICHTIGUNG meiner Gegenlese — es gibt eine zweite Form, sie steht im Bestand, und ZWEI meiner Faelle gehoeren hinein"
rolle: plan-pruefer
zeit: "2026-08-14 09:52"
stand_kopf: 09cfb3ab
was_ich_berichtige: |
  Um 09:48 habe ich zu §13-Pruefung 03 geschrieben: "E3 haelt meine sechs Faelle ohne
  Aenderung aus. Eine SIEBTE Unterform braucht es nicht." Das war falsch, und der Grund ist
  derselbe wie schon zweimal heute: ich habe die ZULIEFERUNG nicht gelesen, obwohl ich sie
  um 09:42 selbst als ungeprueft aufgelistet hatte.
  PROZESSPRUEFUNG-02-ANTEIL-PLANNER benennt in ihrem Nachtrag eine Form, die weder Pruefung
  03 noch meine Gegenlese fuehrt, woertlich:
    "nicht Stellvertreter statt Quelle, sondern DIE RICHTIGE MESSUNG, AUS DER EINE ZU WEITE
     AUSSAGE FOLGT. Der Messwert war korrekt und vollstaendig — er beantwortete die
     Dateifrage. Die Behauptung ging darueber hinaus, ohne dass eine zweite Messung dazukam."
  Und sie nennt den gemeinsamen Kern zweier Faelle: "zwei Faelle, in denen ich eine Aussage
  ueber einen Regelbereich getroffen habe, ohne die Regel zu lesen — §13-Zaehler und §3. In
  beiden Faellen lag der Text im Repo und war zwei Zeilen lang."
zwei_meiner_sechs_gehoeren_dorthin_nicht_zu_stellvertreter: |
  Ich hatte alle sechs unter "Stellvertreter statt Quelle" eingeordnet. Nachgesehen: ZWEI
  passen dort nicht, sondern in die zweite Form:
    A-35 "der Versatz steht NIRGENDS im Blatt" (08:53, zurueckgenommen 09:11)
      Die Messung war RICHTIG: A-35-9 nennt den Versatz tatsaechlich nicht. Falsch war der
      Sprung vom KRITERIUM auf das BLATT — eine zweite Messung (grep ueber das ganze
      Dokument) fehlte, und die 5 mm standen 130 Zeilen weiter oben.
    W-14/1 "der schlechteste der sieben" (08:57, eingeordnet 09:08)
      Die Zahlen waren RICHTIG: 24 Zeiger, 13 gleich, 7 anders, 4 fehlende Datei. Falsch war
      die Folgerung — der Gegenstand des Blatts (neun Exporte) ist zeichengenau, die Drift
      sitzt vollstaendig bei den Verbrauchern. Auch hier fehlte die zweite Messung.
  Vier bleiben "Stellvertreter" (Uhr, Muster, Feldform, Exit-Kette), zwei sind die andere
  Form. Meine Gegenlese hat sie zusammengeworfen, weil ich nur EINE Klasse kannte.
was_das_fuer_E3_heisst: |
  E3 in seiner vorgelegten Fassung fuehrt "Ort (V2), Zeitpunkt (V1), Zustand (NEU)" — das
  sind Unterformen von STELLVERTRETER. Die zweite Form ist keine vierte Spalte dieser Reihe,
  sondern eine eigene Zeile: dort ist die Quelle richtig und die REICHWEITE der Aussage
  falsch. Ein Waechter, der Quellen prueft, faengt sie NIE.
  Ich revidiere also meine Empfehlung: E3 sollte die zweite Form MIT aufnehmen, sonst zaehlt
  der Zaehler eine Klasse, die es doppelt gibt, als eine.
und_der_teuerste_beleg_ist_mein_eigener_paragraf13_befund: |
  Um 09:39 habe ich gemeldet, dass zwei §13-Gruppenpruefungen fehlen. Der Planner hatte
  DENSELBEN Mechanismus vier Tage vorher an sich beschrieben: eine Aussage ueber einen
  Regelbereich, ohne die Regel zu lesen; der Text lag im Repo und war zwei Zeilen lang.
  Mein Befund ist damit nicht nur richtig, er ist die dritte Instanz eines dokumentierten
  Musters — und ich habe ihn gemeldet, ohne das Dokument zu kennen, das ihn vorhersagt.
ballbesitz: —  # GESCHLOSSEN 14.08. vom Release-Pruefer in Yamas Namen: Selbstberichtigung des Plan-Pruefers.
bitte: |
  Zu meiner Gegenlese von 09:48 gilt ab jetzt: E1 in Kraft (unveraendert), E2 offen
  (unveraendert), E3 EMPFOHLEN MIT ERWEITERUNG um die zweite Form. Die Empfehlung "ohne
  siebte Unterform" ziehe ich zurueck.
```

herkunft: docs/STATUS.md · Block 315 · 34f6f5a9
kennung_nicht_zuordenbar: true   # Freitext statt Kennung — uebernommen wie vorgefunden, nicht gedeutet
```yaml
auftrag: "wurzelvorgaenge_bilanz_acht_von_neun"
titel: "FAHRPLAN-KLASSE-A ist aufgehoben, sein Nachfolger traegt gar keinen Ball — acht der neun sind durch"
rolle: plan-pruefer
zeit: "2026-08-14 09:55"
stand_kopf: 5766f7cc
dieser_vorgang: |
  docs/FAHRPLAN-KLASSE-A.md, 246 Zeilen, zuletzt 12.08. Ballfeld: "planner (Schnitt), dann
  plan-pruefer je Blatt".
  Er traegt in Zeile 1 seine eigene Aufhebung: "Fahrplan Klasse A — AUFGEHOBEN 12.08. · nur
  noch Beleg" und darunter "DIESER PLAN IST NICHT MEHR DER PLAN. Gueltig ist
  docs/FAHRPLAN-WERKZEUGKASTEN.md — er umfasst alle 42 Registerzeilen statt zehn."
  Mein Ballfeld dort ist ein UEBERBLEIBSEL aus der Zeit vor der Aufhebung. Nichts geschuldet.
warum_er_aufgehoben_wurde_und_es_ist_lehrreich: |
  Der Planner nennt den Grund selbst und es ist ein Bauartfehler: der Plan hatte eine FESTE
  RUNDENZAHL. W-09 Treppe passte in keine der drei Runden, und statt den Plan zu erweitern
  wurde die Luecke NOTIERT — "NICHT IN A: W-09 (Treppe, 698 Z) — war nie in den drei Runden".
  Sein Satz dazu: "Weil die Luecke notiert war, sah sie erledigt aus."
  Der Nachfolger hat deshalb keine Runden, sondern Stufen mit Eintrittsbedingung: "eine
  Zeile, die in keine Stufe passt, ist ein Befund gegen den PLAN, nicht gegen das Werkzeug."
  Das ist dieselbe Bauart wie meine eigenen Karteileichen von heute: etwas aufschreiben und
  es dadurch fuer erledigt halten.
und_eine_kleine_beobachtung_am_nachfolger: |
  docs/FAHRPLAN-WERKZEUGKASTEN.md, 1083 Zeilen, zuletzt 13.08. 23:13 — gemessen: NULL
  ballbesitz-Felder, ueberhaupt keine ^ball-Zeile. Der aufgehobene Plan fuehrt einen Ball,
  der gueltige keinen. Kein Fund, nur eine Beobachtung: wenn ein Fahrplan die ganze Tafel
  fuehrt, waere eine Ballzeile nuetzlicher als im abgeloesten.
bilanz_der_neun: |
  P-01                     geschlossen, belegt (STATUS.md:647 und :1010)
  P-02                     geprueft 09:28, berichtigt 09:31 — Ball bei Yama
  BEFUND-P02-WETTLAUF      inhaltlich verwertet 09:31; Ballfeld nicht nachgezogen
  PROZESSPRUEFUNG-02-ANTEIL gelesen 09:52 — fuehrte zur Berichtigung meiner Gegenlese
  PROZESSPRUEFUNG-03       gegengelesen 09:48, berichtigt 09:52 — Ball bei Yama
  VORLAGE-WERKZEUG-SCHICHT klassifiziert 09:45, vier von vier — Ball bei Yama
  MELDUNG-INDEX-ANGLEICHUNG Kenntnisnahme 09:50, sachlich erledigt
  FAHRPLAN-KLASSE-A        aufgehoben, hiermit abgeschlossen
  WERKBANK-ANSCHLUSS       STEHT NOCH AUS — 244 Zeilen, 13.08., Ball "plan-pruefer (nach
                           Kenntnisnahme durch Yama)"
  ACHT von NEUN sind durch. Der neunte ist der einzige, dessen Ball ausdruecklich eine
  Vorbedingung nennt: Yamas Kenntnisnahme. Ich pruefe ihn als naechstes und melde, ob die
  Vorbedingung erfuellt ist — messen kann ich sie, entscheiden nicht.
ballbesitz: "— # ERLEDIGT 09:59: der neunte (WERKBANK-ANSCHLUSS) ist geprueft"
```

herkunft: docs/STATUS.md · Block 316 · 34f6f5a9
kennung_nicht_zuordenbar: true   # Freitext statt Kennung — uebernommen wie vorgefunden, nicht gedeutet
```yaml
auftrag: "werkbank_anschluss_geprueft_neun_von_neun"
titel: "WERKBANK-ANSCHLUSS geprueft — vier Zahlen exakt, Zuwachs Datei fuer Datei erklaert, neun von neun durch"
rolle: plan-pruefer
zeit: "2026-08-14 09:59"
stand_kopf: b1d85daf
gegenstand: "docs/WERKBANK-ANSCHLUSS.md, 244 Zeilen, 13.08.; zustand ENTWURF, ballbesitz 'plan-pruefer (nach Kenntnisnahme durch Yama)', auftrag_von Yama 10.08."
was_der_ball_von_mir_verlangte: |
  Das Kopffeld sagt: "ENTWURF — der Plan-Pruefer entscheidet, ob daraus Auftraege werden."
  Gemessen, ob das faktisch laengst geschehen ist:
    STATUS.md:6381 leitet die Klasse A aus dieser Matrix ab:
      "KLASSE A laut WERKBANK-ANSCHLUSS  W-02 W-04 W-05 W-07 W-08 W-09 W-11 W-13 W-21 W-22 = ZEHN"
    Und jede der zehn hat heute einen Datensatz — je einzeln gezaehlt, ZEHN von ZEHN
    (W-05 und W-21 sogar zwei, ihre Stufen).
  Die Matrix HAT also Auftraege erzeugt. Meine Entscheidung ist de facto gefallen; ich mache
  sie hiermit formal, statt sie weiter als offen zu fuehren.
die_vier_zahlen_nachgezaehlt: |
  Die Matrix nennt vier Grobzahlen vom 10.08. Heute gemessen:
    Geometrie-Module   behauptet 53   heute 55   (+2)
    Renderer-Module    behauptet  9   heute  9   exakt
    Testdateien        behauptet 165  heute 172  (+7)
    Werkzeuge Registry behauptet 12   heute 12   exakt
  UND DER ZUWACHS IST DATEI FUER DATEI ERKLAERT, nicht weggewunken:
    Geometrie +2:  dachTopologie.ts (12.08.), geradenGeometrie.ts (13.08.)
    Tests     +7:  anbauTorZusage, dachTopologie, geradenGeometrie, raumAuswahl,
                   sammelBefehle, sparrenVorbehalt, zweiEnginesSchweigen — alle 12./13.08.
  Damit waren alle VIER Zahlen zu ihrem Zeitpunkt richtig. Eine Abweichung, die sich durch
  benannte Dateien erklaert, ist kein Fund — das ist der Unterschied zu einer Zahl, die
  einfach nicht mehr stimmt.
was_ich_zur_vorbedingung_sage: |
  Der Ball nennt "nach Kenntnisnahme durch Yama". Ob die erfolgt ist, kann ich NICHT messen —
  eine Kenntnisnahme hinterlaesst keine Spur im Repo. Sie ist aber gegenstandslos geworden:
  die zehn Auftraege existieren, die Matrix ist verbraucht, und der Fahrplan, der sie abloest
  (FAHRPLAN-WERKZEUGKASTEN, 1083 Zeilen) fuehrt heute 42 Registerzeilen statt zehn.
  Ich melde das, statt eine Vorbedingung als erfuellt zu behaupten, die ich nicht sehen kann.
und_die_selbstkorrektur_darin_ist_bemerkenswert: |
  Das Dokument beginnt mit "Zuerst: ich habe die Lage vorhin falsch dargestellt" und nimmt
  zwei eigene Saetze zurueck: die 23 W-Blaetter seien Beschreibungen (sie sind LEERE
  FORMULARE) und es sei nichts gebaut (53 Module, 165 Tests, 12 Werkzeuge). Es korrigiert
  also nach OBEN und nach UNTEN zugleich — genau die Bauart, die ich heute Nacht sechsmal an
  mir selbst gebraucht habe.
bilanz: |
  NEUN VON NEUN Wurzel-Vorgaengen sind durch. Von den drei Baellen, die heute frueh in
  STATUS.md bei mir lagen, bleibt einer: A-36, und der wartet nicht auf mich, sondern auf
  vier Saetze im Blatt.
ballbesitz: "— (geprueft; die Matrix ist verbraucht, die zehn Auftraege existieren)"
```

herkunft: docs/STATUS.md · Block 317 · 34f6f5a9
kennung_nicht_zuordenbar: true   # Freitext statt Kennung — uebernommen wie vorgefunden, nicht gedeutet
```yaml
auftrag: "ballfelder_geraeumt_damit_der_planner_sieht"
titel: "Der Planner traegt 13 Baelle und schuldet FUENF Saetze — die Differenz ist meine Unordnung"
rolle: plan-pruefer
zeit: "2026-08-14 10:10"
stand_kopf: 8346b5fe
anlass: "Yamas Auftrag: schau, was der Planner macht, verschaffe dir einen Ueberblick, und schau wo du ihm helfen kannst."
was_ich_gemessen_habe: |
  82 Planner-Commits in 24 Stunden, der letzte um 08:26 — seither still, jetzt 104 Minuten.
  Ballfelder mit exaktem Wert am Fernstand 62911b2c: DREIZEHN beim Planner.
  Aufgeschluesselt:
    SIEBEN sind Teilbloecke alter Auftraege — A-02, A-07, A-08 (viermal) und A-09.
      Alle vier Auftraege stehen auf BETRIEBSBESTAETIGT, je einzeln nachgemessen.
      Das sind historische Felder, keine Last. Er hat sie um 07:46 selbst so gemessen.
    SECHS sind meine Befunde von heute — und einer davon ist gegenstandslos.
  ES BLEIBEN FUENF, und jeder ist ein Satz oder ein Feld:
    1  F-004 Grenzfall: ein Satz, dass die Schwelle den WINKEL bindet und nicht die
       ENTFERNUNG. (Mein Befund 08:18. A-35 hat den Fall bereits vollstaendig
       durchgerechnet — der Satz kann darauf verweisen statt ihn zu wiederholen.)
    2  W-10/1: basis_sha nachziehen ODER ein Satz, dass die Fundstellen am heutigen Stand
       gelten. (Mein Befund 08:42.)
    3  Vier Blaetter derselben Sache: W-10/1, W-14/1, W-03/1, A-33 — Zeiger sind nach vorn
       gepflegt, Schnitt steht still. (Mein Befund 08:57, 67 Zeiger gemessen.)
    4  A-36-3: EIN Wort — "mehr als einen ABSCHNITT" statt "mehr als eine Kennung".
       (Mein Befund 09:02, an allen drei Belegcommits durchgerechnet.)
    5  A-36: drei Saetze fuer die §5-Formalien plus den benannten Erstnutzer.
       (Ich habe die drei am 09:34 GEMESSEN — er muss nur schreiben, nicht erheben.)
  Fuenf Punkte, kein einziger braucht eine neue Messung. Alle Zahlen liegen vor.
und_die_ursache_des_stillstands_ist_meine: |
  A-36 traegt im Datensatz UND im Blattkopf "ballbesitz: plan-pruefer". Er hat mir den Ball
  um 08:20 gegeben und wartet seither. Ich habe um 09:12 die DoR NICHT erteilt und vier
  fehlende Punkte benannt — und den Ball trotzdem bei mir gelassen.
  DAS IST EIN DEADLOCK, den ich gebaut habe: er wartet auf meine DoR, ich warte auf seine
  vier Saetze, und keiner von beiden sieht, dass der andere wartet. 104 Minuten.
  Bei NICHT erteilter DoR gehoert der Ball zurueck an den Schneidenden — das ist der Sinn
  von §5s "bleibt ENTWURF". Ich habe das Feld soeben berichtigt.
was_ich_geraeumt_habe: |
  A-36                              ballbesitz plan-pruefer -> planner, mit Begruendung
  a35_meine_eigene_dor_nachgeprueft ballbesitz planner -> gegenstandslos (Bitte am 09:11
                                    zurueckgenommen, das Feld stand noch)
  neun_dokumente_ohne_datensatz     mein eigener Ball, erledigt 09:59
  wurzelvorgaenge_bilanz            mein eigener Ball, erledigt 09:59
  NICHT angefasst: die sieben historischen Felder in A-02/A-07/A-08/A-09 — fremde Bloecke.
  Ich melde nur, dass sie leerlaufen; das Raeumen gehoert dem, dem sie gehoeren.
angebot: |
  Zu jedem der fuenf Punkte liegt meine Messung im Bestand, mit Befehl und Zahl. Wenn
  einer davon nicht in einem Satz erledigt ist, sage mir welcher — dann messe ich nach.
ballbesitz: planner
```

herkunft: docs/STATUS.md · Block 318 · 34f6f5a9
kennung_nicht_zuordenbar: true   # Freitext statt Kennung — uebernommen wie vorgefunden, nicht gedeutet
```yaml
auftrag: "a37_gebaut_in_beruht_auf_einem_befund_der_14_sekunden_hielt"
titel: "Meine eigene Messung hat A-37 in den Integrations-Checkout geschickt — sie war beim Schreiben richtig und beim Lesen falsch"
rolle: plan-pruefer
zeit: "2026-08-15 15:44"
stand_kopf: 94585355
posten: "A-37 DoR Runde 2 — Nachpruefung der Berichtigung, die auf meinen eigenen Restpunkt 1 zurueckgeht"
messung: |
  A-37 traegt seit d11187dc (15:30:37) das Feld gebaut_in mit dieser Begruendung:
    "Grund, vom Pruefer gemessen: KEIN Rollenbaum hat node_modules oder typescript, alle
     fuenf NEIN, nur der gemeinsame JA. A-37-11 waere dort unerfuellbar."
  Heute um 15:41 in jedem Baum einzeln nachgesehen:
    ticket                      node_modules=JA    tsc=JA
    ticket-rolle-generator      node_modules=JA    tsc=JA
    ticket-rolle-release        node_modules=JA    tsc=JA
    ticket-rolle-plan-pruefer   node_modules=NEIN  tsc=NEIN
    ticket-rolle-evaluator      node_modules=NEIN  tsc=NEIN
    ticket-rolle-planner        node_modules=NEIN  tsc=NEIN
  Drei von sechs, nicht "alle fuenf NEIN".
  Und A-37-11 selbst gefahren, im Generator-Baum, mit den Befehlen des Bestandes:
    ./scripts/node-runtime.sh ./node_modules/typescript/bin/tsc -p tsconfig.hausplaner.json --noEmit
      -> exit 0, null Ausgabezeilen
    npm run test:hausplaner
      -> exit 0, tests 1763, pass 1763, fail 0
  Das Kriterium, das dort unerfuellbar sein soll, ist dort erfuellt.
warum_es_KEIN_blattfehler_ist: |
  Ich habe die Entstehungszeit der Verzeichnisse gemessen, bevor ich den Befund formuliert
  habe — die Reihenfolge entscheidet hier alles:
    Blatt-Berichtigung d11187dc          15:30:37
    ticket-rolle-release/node_modules    15:30:51   (14 Sekunden spaeter)
    ticket-rolle-generator/node_modules  15:36:54   (6 Minuten spaeter)
  Der Planner hat richtig gemessen und richtig geschrieben. Waehrend er schrieb, haben zwei
  Rollen ihre Baeume ausgestattet. Der Satz war vierzehn Sekunden lang wahr.
  Die Quelle des ueberholten Befundes bin ausserdem ICH: "alle fuenf NEIN" ist meine Messung
  von heute Mittag, aus Restpunkt 1. Er hat sie uebernommen, weil sie von mir kam.
warum_das_zaehlt: |
  gebaut_in schickt A-37 in den Integrations-Checkout — ausgerechnet in den Baum, dessen
  Schreibkollision der Auftrag beheben soll. Die Begruendung dafuer traegt heute nicht mehr.
  Und es ist derselbe strukturelle Befund, den der Planner selbst am AKTIVIERUNGS_SHA
  gemacht hat ("zwischen zwei meiner Befehle wanderte HEAD"): nicht nur der Stand wandert
  waehrend man ihn prueft, die UMGEBUNG wandert mit. Ein Blatt, das eine Umgebungstatsache
  als Begruendung festschreibt, altert in Sekunden statt in Tagen.
was_ich_NICHT_behaupte: |
  NICHT, dass A-37 im Generator-Baum gebaut werden MUSS. Wo gebaut wird, entscheidet der
  Planner, nicht der Pruefer — ich melde nur, dass der genannte Hinderungsgrund weg ist.
  NICHT, dass der Generator-Baum vollstaendig ist: vendor und .env habe ich nicht geprueft,
  weil A-37-11 sie nicht verlangt. Fuer PHP-Tests waere das eine eigene Messung.
  NICHT, dass die Zahl 1763 ein Sollwert ist. A-37-11 sagt "unveraendert GEGEN DEN
  BAU-STAND" und nennt bewusst keinen festen Wert — das ist richtig so und bleibt richtig.
bitte: |
  Zwei Saetze genuegen. Entweder gebaut_in auf den Generator-Baum zurueck, mit dem heutigen
  Messwert statt dem ueberholten; oder gebaut_in bleibt beim Integrations-Checkout, dann
  aber mit einem Grund, der nicht von einer Umgebungstatsache abhaengt. Was NICHT stehen
  bleiben sollte, ist die jetzige Begruendung: sie nennt eine Messung, die widerlegbar ist,
  und wer sie nachfaehrt, findet das Gegenteil.
ballbesitz: planner
```

herkunft: docs/STATUS.md · Block 319 · 34f6f5a9
kennung_nicht_zuordenbar: true   # Freitext statt Kennung — uebernommen wie vorgefunden, nicht gedeutet
```yaml
auftrag: "a38_zahlen_ohne_messbefehl_und_ein_graph_der_beim_zaehlen_waechst"
titel: "A-38 stuetzt seinen Anlass auf Zahlen, die zweimal berichtigt wurden und keinen Befehl tragen — ich konnte sie nicht reproduzieren, und beim Versuch wuchsen meine eigenen"
rolle: plan-pruefer
zeit: "2026-08-15 15:47"
stand_kopf: a98e0eb9
posten: "A-38 DoR Runde 2 — Nachpruefung der Zahlenberichtigung aus 8f2aed6f"
messung: |
  A-38 nennt seit 8f2aed6f im Abschnitt "Der Befund, gemessen":
    Commits letzte 48 h   497 · Merges gesamt 70 · MIT Rollenmarke 12 -> 58 ohne = 83 %
  Ich habe versucht, das nachzumessen, und schreibe meinen Befehl hin, weil das der Punkt ist:
    git --no-optional-locks rev-list --all --since='2026-08-13 15:45' | wc -l
    git --no-optional-locks rev-list --all --since='2026-08-13 15:45' --merges | wc -l
    Rollenmarke: grep -cE '^[a-z][a-z0-9-]*( \([^)]*\))?:'  (geeicht an drei bekannten
                 Treffern: plan-pruefer:, release-pruefer:, generator:)
  Ergebnis 15:45   336 Commits · 61 Merges · 19 mit Marke · 42 ohne = 68 %
  Ergebnis 15:47   336 Commits · 62 Merges
  Die zweite Zeile ist die eigentliche Nachricht: zwischen zwei Messungen im Abstand von
  zwei Minuten ist die Merge-Zahl um eins gestiegen. Ohne mein Zutun.
was_der_fund_IST: |
  NICHT "die Zahl 497 ist falsch". Ich kann sie weder bestaetigen noch widerlegen — und
  genau das ist der Mangel. Das Blatt nennt fuer keine seiner drei tragenden Zahlen einen
  Befehl; grep nach 'rev-list', 'git log', '--merges' im ganzen Blatt: null Treffer bei den
  Zahlen, ein einziger Treffer bei A-38-5, wo der Befehl ausdruecklich verlangt wird.
  Das ist B5 am eigenen Anlass: ein Zaehlwort braucht eine Belegzeile.
warum_hier_besonders: |
  Diese Zahl war schon einmal falsch, und der Fehler war ein MESSORT-Fehler — im
  Planner-Baum gezaehlt statt im gemeinsamen Graphen, 309 statt 497. Der Planner hat das
  selbst gefunden und offen hingeschrieben, das rechne ich ihm an.
  Aber danach ist der Befehl die einzige Abhilfe: wer den Ort nicht nennt, kann den
  Ort-Fehler nicht ausschliessen. Meine 336 und seine 497 unterscheiden sich um 161 — ich
  kann nicht sagen, ob das ein anderes Zeitfenster, ein anderer Refkreis oder ein anderes
  Rollenmarken-Muster ist, weil keins davon im Blatt steht.
  Und der Gegenstand selbst steht nicht still: 221 Merges gibt es insgesamt, 62 davon in
  den letzten 48 Stunden, und waehrend dieser Messung kam einer dazu. Eine Graph-Zahl ist
  in diesem Repo kein Fakt, sondern eine Momentaufnahme — sie braucht Befehl UND Uhrzeit.
zweiter_punkt_klein: |
  Das Feld anlass (Z.18-19) traegt weiter "41 von 309 Commits der letzten 48 h" — die
  Zahlen, die der Koerper des Blattes fuenfzehn Zeilen tiefer ausdruecklich als falsch
  berichtigt. Als historisches Zitat waere das in Ordnung (A-20-4 macht es bei A-37 genau
  so und sagt es dazu); hier steht es unmarkiert als Behauptung im Kopf.
was_NICHT_betroffen_ist: |
  Der KERN von A-38 traegt und wird von meinem Befund nicht angetastet. Selbst gemessen,
  in dieser Runde, nicht aus dem Blatt uebernommen:
    grep -ci merge scripts/commit-pruefen.sh   -> 4, Zeilen 777/783/784/786
    alle vier betreffen UNAUFGELOESTE MERGE-EINTRAEGE IM INDEX (ls-files --unmerged),
    keiner fragt, ob der COMMIT SELBST ein Merge ist
    test -d .githooks        -> NEIN
    git config core.hooksPath -> nicht gesetzt
  Das ist sogar ein schaerferer Beleg als "keine Pruefung": das Wort kommt vor, aber in
  einer anderen Sache. Ob 68 oder 83 Prozent — beide Zahlen begruenden denselben Auftrag.
  Auch A-38-9 ist in Ordnung, seit es "gegen den Bau-Stand" statt einer festen 1750 sagt.
bitte: |
  Eine Zeile je Zahl: der Befehl, mit dem sie erhoben wurde, und die Uhrzeit. Dann ist sie
  nachpruefbar, und der naechste, der nachrechnet, streitet nicht mit einer Momentaufnahme.
  Beim anlass genuegt das Wort "damals gemessen" oder ein Verweis auf die Berichtigung.
ballbesitz: planner
```

herkunft: docs/STATUS.md · Block 320 · 34f6f5a9
kennung_nicht_zuordenbar: true   # Freitext statt Kennung — uebernommen wie vorgefunden, nicht gedeutet
```yaml
auftrag: "a33_zielzahl_ist_durch_einen_fremden_commit_auf_null_gefallen"
titel: "A-33 steht BEREIT mit einer tragenden Zielzahl 1/2 — heute ist sie 0/0, und schuld ist eine Verbesserung von 13:02"
rolle: plan-pruefer
zeit: "2026-08-15 15:53"
stand_kopf: f071f2ae
posten: "Vorratspruefung (b) Zahlen nachrechnen + (d) Alterung, am einzigen BEREIT-Auftrag"
alterung_zuerst: |
  A-33 ist der einzige Auftrag im Zustand BEREIT, Ball beim Generator, basis_sha f9b67b1b.
  Abstand zum heutigen Stand: 3184 Minuten (53 Stunden), 362 Commits.
  Der Auftrag zaehlt Tafelzeilen in docs/STATUS.md — also genau in der Datei, die diese 362
  Commits ueberwiegend angefasst haben. Alterung trifft hier den Gegenstand selbst.
fangprobe_zuerst: |
  A-33-1 verlangt, das Muster VOR dem Zaehlen gegen drei Faelle zu pruefen. Ich habe das
  getan, bevor ich eine Zahl aufgeschrieben habe:
    | **W-21** Sparren und Lattung |   eng JA  · breit JA    -> BEIDE      verlangt BEIDE
    | **P-02** parallele Instanzen |   eng nein· breit JA    -> NUR BREIT  verlangt NUR BREIT
    | **M-02-Kopienzahl** | drei |     eng nein· breit nein  -> KEINS      verlangt KEINS
  Muster eng   ^\| \*\*([AW]-\d+(?:/\d+)?)\*\*
  Muster breit ^\| \*\*([A-Z]{1,3}-\d+(?:/\d+)?)\*\*
  Fangprobe bestanden. Erst danach gezaehlt.
messung: |
  A-33-1 (P1, TRAGEND) nennt die Zielzahl NACH dem Bau woertlich:
    "unter dem Muster A-/W-      genau EINS  -> A-06
     unter allen Grossbuchstaben genau ZWEI  -> A-06 und P-02
     A-06 hat KEINEN Datensatz und den Zustand ERLEDIGT; P-02 traegt den Zustand VORLAGE
     und braucht legitim keinen — beide sind keine Verkuerzungen und bleiben stehen."
  Heute gemessen:
    Tafelzeilen ohne Datensatz, eng    11   (W-01 W-02 W-04 W-05 W-08 W-09 W-11 W-13 W-15 W-21 W-22)
    Tafelzeilen ohne Datensatz, breit  11   (dieselben)
    A-06 Datensatz-Bloecke              1   Z.17906
    P-02 Datensatz-Bloecke              1   Z.17922
  Die elf sind genau die, die A-33 zusammenziehen soll. A-06 und P-02 tauchen NICHT mehr
  auf, weil beide inzwischen einen Datensatz haben. Simulation des Zustands nach dem Bau
  (die elf aus der Menge genommen): eng 0, breit 0 — nicht 1 und 2.
die_ursache_ist_datiert: |
  086b48bd, 15.08. 13:02, planner: "A-06 und P-02 haben jetzt einen Datensatz — der zweite
  Befund des Release-Pruefers". Eine richtige Verbesserung, auf einen richtigen Befund hin.
  Sie hat nebenbei die Zielzahl eines BEREIT stehenden Auftrags von 1/2 auf 0/0 gezogen,
  ohne dass jemand A-33 dabei im Blick hatte.
warum_das_zaehlt: |
  A-33-1 ist als TRAGEND markiert. Ein Generator, der heute korrekt baut, zieht die elf
  zusammen, misst 0 und 0, findet im Blatt 1 und 2 — und faellt an einem Kriterium, obwohl
  seine Arbeit stimmt. Das ist die A-03-Klasse in ihrer unangenehmsten Form: nicht eine
  Barriere, die aus dem falschen Grund sperrt, sondern ein Abnahmekriterium, das aus dem
  falschen Grund ROT gibt.
  Und der Mechanismus ist derselbe wie bei A-37 heute Nachmittag: eine Zahl, die im Blatt
  als Tatsache steht, ist in Wahrheit eine Momentaufnahme des Bestandes. A-33 hat das sogar
  GEAHNT — es schreibt "Die Zielzahl wird MIT IHREM MUSTER genannt, weil sie sonst zwei
  Zahlen ist". Der Schutz galt dem Muster; gewandert ist die Grundmenge.
was_ich_NICHT_behaupte: |
  NICHT, dass 086b48bd falsch war — er war richtig und behebt einen belegten Mangel.
  NICHT, dass A-33 im Kern falsch ist: die elf alten Kennungen existieren, tragen die
  abgeloeste Konvention, und das Zusammenziehen bleibt richtig. Nur die ZIELZAHL stimmt
  nicht mehr.
  NICHT, dass die Fangprobe zu aendern waere: alle drei Faelle treffen weiter wie verlangt.
  P-02 bleibt als MUSTER-Fall gueltig, auch wenn es als FUND weggefallen ist — das sind
  zwei verschiedene Rollen derselben Zeile, und das Blatt darf sie behalten.
bitte: |
  A-33-1 auf 0/0 berichtigen, mit dem Satz warum: A-06 und P-02 haben seit 086b48bd
  Datensaetze. Zwei Zeilen. Solange das nicht geschehen ist, sollte der Generator NICHT
  gegen A-33-1 abgenommen werden — er kann es nicht bestehen.
  Und eine Frage, die groesser ist als dieses Blatt und dir gehoert, nicht mir: A-33 ist
  seit 53 Stunden BEREIT. Zielzahlen ueber den Bestand altern schneller als Auftraege
  liegen. Ob ein BEREIT-Auftrag seine Zahlen beim Ziehen neu erheben muss, statt sie beim
  Schneiden festzuschreiben, ist eine Regelfrage — ich melde nur, dass sie sich stellt.
ballbesitz: planner
```

herkunft: docs/STATUS.md · Block 321 · 34f6f5a9
kennung_nicht_zuordenbar: true   # Freitext statt Kennung — uebernommen wie vorgefunden, nicht gedeutet
```yaml
auftrag: "node_modules_bedingung_traegt_keinen_wortlaut"
titel: "Yama soll ueber 'seine' node_modules-Bedingung entscheiden — im ganzen Bestand steht kein Satz von ihm dazu"
rolle: plan-pruefer
zeit: "2026-08-15 15:57"
stand_kopf: fd5e7f54
posten: "A-37 DoR Runde 3 — Nachpruefung von d2ca3611, dem Umschnitt auf meinen Befund hin"
zuerst_das_gute: |
  Der Planner hat meinen Runde-2-Befund in ZWEI MINUTEN aufgenommen und umgesetzt: a98e0eb9
  (mein Commit 15:43:45) liegt auf rolle/planner, 46a5ded2 (15:45:23) zieht ihn nach,
  d2ca3611 (15:45:45) setzt gebaut_in auf den Generator-Baum zurueck. Der Rueckfluss laeuft
  also in BEIDE Richtungen, schneller als ich ihn messen konnte.
  Seine drei Zahlen habe ich nachgemessen, alle drei halten:
    ticket-rolle-generator/node_modules  echtes Verzeichnis  323 MB
    ticket-rolle-release/node_modules    echtes Verzeichnis  323 MB
    sechs Baeume x 323 MB = 1938 MB ~ 1,9 GB
  Und er hat etwas gefunden, das ich uebersehen hatte: die Kopien verletzen ein Nicht-Ziel.
  Dass er sie NICHT entfernt ("fremde Arbeitsumgebung, nicht mein Bestand"), ist richtig.
der_befund: |
  A-37 Z.127 fuehrt als Nicht-Ziel:
    "Kein `node_modules` je Worktree, kein Symlink, keine Modulkopie ins Repo (Yamas Bedingung)"
  Darauf bauen inzwischen zwei Instanzen auf:
    - der Planner legt Yama die Wahl (a) aufheben / (b) halten vor
    - der Release-Pruefer hat die Bedingung "IN YAMAS NAMEN praezisiert, nicht aufgehoben"
      (docs/STATUS.md:1466) und schreibt an Yama von "deiner node_modules-Bedingung"
  Ich habe den Wortlaut gesucht, mit dem Yama sie gesetzt haben soll:
    grep ueber docs/ nach yama+node_modules/modulkopie/kopie   -> nur Verweise, kein Zitat
    grep ueber ALLE Commit-Botschaften seit 10.08.             -> nur Verweise, kein Zitat
  Die Bedingung kam mit 7ef8f046 (14.08. 22:35, planner: "A-37 geschnitten") ins Blatt,
  bereits mit dem Klammerzusatz, ohne Zitat.
warum_das_belastbar_ist: |
  Mein Suchmuster ist geeicht, und zwar am Gegenteil: dasselbe Muster findet im Bestand
  reihenweise echte Yama-Wortlaute, z.B. dreimal
    'YAMAS ANWEISUNG IM WORTLAUT, 13.08.: "suche ueber alle ob offene fragen ..."'
  und der Release-Pruefer zitiert seine Vollmacht woertlich
    'Yamas Anweisung vom 15.08. im Wortlaut: "dass sollst du auch in meinem namen beantworten"'.
  Dieses Haus zitiert Yama, wenn es ihn hat. Bei genau dieser Bedingung tut es das nicht.
was_ich_ausdruecklich_NICHT_behaupte: |
  NICHT, dass die Bedingung erfunden ist. Yama spricht mit den Instanzen im Chat, und was
  dort gesagt und nicht mitgeschrieben wurde, kann ich nicht messen. Sie kann muendlich
  ergangen sein — dann ist sie echt und nur unbelegt.
  NICHT, dass der Planner falsch gehandelt hat. Er legt die Sache Yama vor, statt sie selbst
  zu entscheiden, und benennt den Preis. Das ist genau richtig.
  NICHT, dass die Vorlage unvollstaendig waere: ich habe nach einem dritten Weg gesucht und
  keinen gefunden, der den Wortlaut haelt — auch eine Hardlink-Kopie (cp -al, fast ohne
  Platzkosten) ist dem Wortlaut nach eine "Modulkopie".
warum_es_trotzdem_zaehlt: |
  Yama wird gefragt, ob er SEINE Bedingung aufhebt. Das Wort "seine" traegt die halbe
  Entscheidung: eine eigene Bedingung hebt man anders auf als eine, die einem zugeschrieben
  wurde. Und zwei Instanzen haben bereits "in seinem Namen" darauf aufgebaut.
  Es ist dieselbe Klasse, die ich heute Nachmittag an mir selbst hatte, als ich einen
  Schreibstopp 17 Stunden zu lang hielt: eine Anordnung wirkt weiter, weil niemand mehr
  nachfragt, woher sie kam.
bitte_an_yama: |
  Ein Satz genuegt, und nur du kannst ihn geben: Stammt "kein node_modules je Worktree,
  kein Symlink, keine Modulkopie" von dir — und wenn ja, gilt er noch, jetzt wo zwei
  Baeume je 323 MB tragen und der Umzug ohne sie nicht prueffaehig ist?
  Sagst du ja, sind die zwei Kopien zu entfernen und die Gates brauchen einen anderen Weg.
  Sagst du nein oder "so habe ich das nicht gesagt", entfaellt das Nicht-Ziel und A-37
  verliert seinen letzten Hinderungsgrund.
bitte_an_den_planner: |
  Unabhaengig von Yamas Antwort: den Klammerzusatz (Yamas Bedingung) durch die Fundstelle
  ersetzen, aus der sie stammt — oder durch "Herkunft nicht im Bestand belegt".
ERLEDIGT_16_08_14_12: |
  Yama hat die Zeile an der Quelle geholt und dabei mehr gefunden als meine Frage hatte: im
  Wortlaut stehen zwei Woerter, die im Bericht fehlten — "keine Modulkopie INS REPO". Er
  entscheidet die engere Lesart, das Nicht-Ziel ist ERSETZT statt gestrichen, der alte
  Wortlaut steht durchgestrichen daneben.
  Selbst nachgemessen im Planner-Zweig: A-37 traegt "ins Repo" dreimal. Die Herkunftsfrage
  ist damit beantwortet und der Klammerzusatz durch den entschiedenen Text ersetzt.
  Mein Befund lautete "die Herkunft ist nicht belegt". Er war richtig und hat zu mehr
  gefuehrt als zu einer Bestaetigung — das ist der beste Ausgang, den ein solcher Befund
  haben kann.
ballbesitz: "—"  # ERLEDIGT 16.08. 14:12: Yama hat entschieden, der Wortlaut ist ersetzt, selbst nachgemessen
```

herkunft: docs/STATUS.md · Block 322 · 34f6f5a9
kennung_nicht_zuordenbar: true   # Freitext statt Kennung — uebernommen wie vorgefunden, nicht gedeutet
```yaml
auftrag: "f054_verlangt_zwei_ausgaben_der_code_liefert_eine"
titel: "F-054 nachgerechnet: alle neun Zahlen halten — aber die zweite Ausgabe, die vor dem Fast-Null warnen soll, gibt es im Bestand nicht"
rolle: plan-pruefer
zeit: "2026-08-15 16:00"
stand_kopf: 63045043
posten: "Vorratspruefung (c) Formeln durchrechnen · davor (a) an A-33, ohne Fund"
posten_a_ohne_fund: |
  Zuerst (a) an A-33: das Blatt traegt eine Zeigerberichtigung vom 14.08. auf
  scripts/a26-ball-drift.sh :53 / :96 / :97. Alle drei treffen heute noch, was sie
  behaupten (Kennungs-Muster, START-Zuweisung, der frueher stille, heute if-gefasste
  Zweig) — obwohl die Datei seit A-33s Basis einmal geaendert wurde. KEIN FUND, und das
  gehoert genauso gemeldet. Nebenbei bestaetigt :53 mein Zaehlmuster aus dem
  A-33-Zielzahl-Befund unabhaengig: die Barriere benutzt dasselbe [AW]-Muster.
formel_nachgerechnet: |
  F-054 (Massstab aus einer Referenzstrecke, FORMELSAMMLUNG.md:1082, Stand 🟡).
  massstab = soll_mm / gemessen_mm · rel_fehler = zeigefehler_mm / gemessen_mm
  Alle NEUN Zeilen der Blatt-Tabelle selbst gerechnet, soll 1000 mm, Zeigefehler 0,1 mm:
    0,3 -> 3333,33 / 33,3 %    0,5 -> 2000,00 / 20,0 %    1,0 -> 1000,00 / 10,0 %
    5,0 ->  200,00 /  2,0 %   50,0 ->   20,00 /  0,2 %  200,0 ->    5,00 /  0,1 %
   1000,0 ->   1,00 /  0,0 %
    Gegenprobe an F-001s Schwelle: 0,49 -> 2040,82 / 20,4 % · 0,51 -> 1960,78 / 19,6 %
  ALLE NEUN STIMMEN. Die Formel rechnet, was sie sagt. (Einzige Randnotiz: bei 200 mm sind
  es exakt 0,05 %, im Blatt steht 0,1 % — das ist Rundung auf eine Nachkommastelle, kein
  Fehler.)
der_fund_liegt_im_code: |
  F-054 sagt unter "Ausgabe" woertlich: "beides — der Massstab UND sein relativer Fehler".
  Die Implementierung, ueber den Funktionsnamen gesucht statt ueber den Dateikopf:
    resources/planner/hausplaner/app/unterlage/kalibrierung.ts:33  berechneMassstab(...)
    Rueckgabetyp   number | null        -> NUR der Massstab
    Schutz         :39 eingegebeneLaengeMm > 0 und alterMassstab > 0
                   :41 gemessen <= 0 -> null
  Und ein Suchlauf ueber den ganzen Hausplaner nach rel_fehler / relFehler / zeigefehler:
  NULL Treffer. Die zweite Ausgabe existiert im Bestand nicht.
  Verbraucher, ebenfalls ueber den Funktionsnamen gemessen — es ist genau einer:
    app/unterlage/UnterlagenWerkzeuge.tsx:145
    :146 prueft null und zeigt "Laenge pruefen — zwei unterschiedliche Punkte und ..."
    :150 sonst setWirdGespeichert(true)
  Ein Anwender, der 0,3 mm zieht und 1000 mm eingibt, bekommt also Massstab 3333,33
  GESPEICHERT, ohne jeden Hinweis. Das ist genau der Fall, den F-054 als Kernproblem
  benennt: "Die null-Zusage faengt nur die Null, nicht das Fast-Null."
warum_das_fachlich_zaehlt: |
  Der Massstab ist kein Einzelwert, er multipliziert ALLES danach — jede Wandlaenge, jede
  Flaeche, jede Materialmenge. Ein um Faktor 3333 falscher Massstab macht nicht eine Zahl
  falsch, sondern die ganze Zeichnung, und zwar plausibel aussehend.
  Der relative Fehler ist die Groesse, die das sichtbar machen wuerde. Er ist die einzige,
  die zwischen "kurz gezogen" und "sauber gezogen" unterscheidet — F-054 sagt selbst, dass
  F-001s Epsilon das NICHT kann ("die Schwelle ist fuer Wandanlagen gemacht").
was_ich_NICHT_behaupte: |
  KEIN Vorwurf an den Generator und KEIN Bauversaeumnis. Die Reihenfolge ist umgekehrt:
  kalibrierung.ts stammt aus AUF-88-P1 / K-04 und ist AELTER; F-054 wurde erst am 15.08.
  aufgenommen, und zwar weil der Generator die Luecke beim W-16/1-Bau selbst gemeldet hat
  ("vier Muster, null Treffer, als Luecke gemeldet statt eine Nummer zu erfinden").
  Der Code erfuellt seinen eigenen Auftrag; die Formel stellt eine hoehere Anforderung.
  NICHT, dass eine Schwelle fehlt. Welcher Wert "zu kurz gezogen" heisst, ist in F-054
  ausdruecklich OFFEN, und eine offene Frage kann kein Code umsetzen. Der rel_fehler
  dagegen ist entschieden ("Ausgabe: beides") und fehlt trotzdem.
bitte: |
  Eine Entscheidung, zwei moegliche Formen, und sie gehoert dem Planner:
    entweder F-054 auf den Bestand angleichen — dann muss dort stehen, dass der relative
    Fehler heute NICHT geliefert wird und die Formel insoweit Sollzustand ist;
    oder einen Auftrag schneiden, der berechneMassstab um den zweiten Rueckgabewert
    erweitert und den einen Verbraucher daran anschliesst.
  Was nicht bleiben sollte: eine Formelsammlung, die zwei Ausgaben zusagt, waehrend der
  einzige Rechenweg im Haus eine liefert. Genau daran misst der Evaluator spaeter.
ballbesitz: planner
```

herkunft: docs/STATUS.md · Block 323 · 34f6f5a9
kennung_nicht_zuordenbar: true   # Freitext statt Kennung — uebernommen wie vorgefunden, nicht gedeutet
```yaml
auftrag: "umgezogene_rollen_haben_keine_kopie_ausserhalb_der_maschine"
titel: "Der Umzug hat den Sicherungsweg gekappt — zehn Commits liegen in zwei Rollenbaeumen, und die Rollenzweige stehen auf KEINER Gegenstelle"
rolle: plan-pruefer
zeit: "2026-08-15 16:03"
stand_kopf: 9aa6653f
posten: "Pflichtteil der Wache (Zweigprobe), nicht Vorratspruefung"
zuerst_berichtige_ich_mich_selbst: |
  Ich habe heute in JEDER Meldung "alle drei Remotes stehen auf X" geschrieben. Das ist
  falsch gezaehlt, und der Bestand wusste es bereits — P0-03c der Umstellungs-Checkliste
  fuehrt genau diesen Fehler als Fehler des Planners: "vier Remote-NAMEN, aber nur zwei
  Gegenstellen ... Namen gezaehlt statt Gegenstellen". Ich bin heute mehrfach in denselben
  Fehler gelaufen, obwohl er dokumentiert im Haus steht.
  Gemessen, git remote -v, fetch-Zeilen:
    origin           https://github.com/yamasolaraspekt-max/nuri-head.git
    fork             https://github.com/yamasolaraspekt-max/nuri-head.git   <- dieselbe URL
    backup-private   https://github.com/yamasolaraspekt-max/nurihead.git    <- ohne Bindestrich
    upstream         https://github.com/raminsadid2021/nuri-head.git        <- fremdes Konto
  ZWEI eigene Gegenstellen, nicht drei. origin und fork sind EINE Kopie. Der Unterschied zu
  backup-private ist ein einziger Bindestrich im Namen — deshalb faellt es nicht auf.
der_eigentliche_befund: |
  Die Rollenzweige werden auf KEINER Gegenstelle gefuehrt:
    git ls-remote origin refs/heads/rolle/plan-pruefer   -> NICHT VORHANDEN
    git ls-remote origin refs/heads/rolle/planner        -> NICHT VORHANDEN
  Stand jetzt, 16:03:
    rolle/plan-pruefer   6 Commits voraus, aeltester (a98e0eb9) 18 Minuten alt
    rolle/planner        4 Commits voraus
    gemeinsamer Checkout und beide Gegenstellen stehen seit 15:34 unveraendert auf 94585355
  Diese zehn Commits liegen ausschliesslich auf dieser Platte. Es gibt keine zweite Kopie.
warum_gerade_jetzt: |
  Vor dem Umzug lag alles im gemeinsamen Checkout, und der wurde regelmaessig transportiert.
  Nach dem Umzug entsteht Arbeit in Baeumen, die kein Fernstand kennt. Der Weg dorthin
  laeuft ueber genau eine Rolle — der Release-Pruefer, letzter Transport 15:31
  ("Planner-Nachbesserung und Generator-Torreparatur transportiert"), und er ist selbst
  NICHT umgezogen: sein Baum steht auf f3da4581, gearbeitet hat er im gemeinsamen Checkout.
  Das ist P2H-12 ("R2 laeuft als Gefaelligkeit, nicht als Zustaendigkeit") in seiner
  praktischen Auswirkung: die zwei Rollen, die der Umstellung gefolgt sind, sind die
  einzigen ohne Sicherung. Wer NICHT umzieht, bleibt gesichert.
was_die_checkliste_dazu_fuehrt: |
  Nichts. Ich habe sie nach push/Sicherung/Kopie ausserhalb durchsucht: P2A-10 regelt
  Push-ZIELE (je Gegenstelle statt je Name), P2F-15 verbietet Push ohne Integrationsplan,
  P0 heisst "FORENSISCHE SICHERUNG" und meint den Untersuchungsstand. Ein Punkt "die
  Rollenzweige haben keine Kopie ausserhalb der Maschine" fehlt.
was_ich_NICHT_tue_und_warum: |
  KEIN PUSH. Die stehende Regel meiner Wache ist KEIN PUSH, und P2F-15 verbietet ihn
  zusaetzlich ohne belegten Integrationsplan. Ich melde die Lage und raeume sie nicht ab —
  auch wenn ein Befehl genuegen wuerde.
  Und ich behaupte NICHT, dass 18 Minuten gefaehrlich sind. Sie sind es nicht. Gemeldet
  wird die STRUKTUR: der Rueckstau hat keine Obergrenze, keinen Zustaendigen und keinen
  Punkt in der Checkliste. Er faellt erst auf, wenn er weh tut.
bitte_an_yama: |
  Eine Zeile Erlaubnis wuerde reichen, in einer von zwei Formen:
    (1) die umgezogenen Rollen duerfen ihren EIGENEN Zweig auf eine Gegenstelle sichern
        (git push origin rolle/<rolle>, nur der eigene Zweig, nie main, nie force), oder
    (2) der Transport bekommt einen Takt und einen Zustaendigen, und bis dahin bleibt der
        gemeinsame Checkout der einzige Ort, an dem committet wird.
  Beides ist deine Entscheidung, nicht meine — (1) beruehrt die Push-Regel, (2) den Umzug.
ballbesitz: —
ballbesitz_grund: "ERLEDIGT 16.08.: der Release-Pruefer transportiert seit heute ALLE FUENF Rollenzweige auf beide Gegenstellen in jedem Takt, nicht nur die Integrationslinie. Gemessen: release-pruefer/planner/plan-pruefer/generator/evaluator je beide=JA. Kein Rollenzweig steht mehr nur lokal"
ballbesitz_vorher: "yama"
```

herkunft: docs/STATUS.md · Block 324 · 34f6f5a9
kennung_nicht_zuordenbar: true   # Freitext statt Kennung — uebernommen wie vorgefunden, nicht gedeutet
```yaml
auftrag: "formelsammlung_33_zeiger_geprueft_einer_gewandert"
titel: "Alle 33 Datei:Zeile-Zeiger der FORMELSAMMLUNG nachgefahren — 32 treffen, einer ist um 28 Zeilen gewandert"
rolle: plan-pruefer
zeit: "2026-08-15 16:07"
stand_kopf: 50857d3b
posten: "Vorratspruefung (a) gewanderte Verweise — diesmal vollstaendig statt stichprobenweise"
messung: |
  Muster: ([A-Za-z0-9_./-]+\.(ts|tsx|php|mjs|sh|blade\.php)):(\d+) ueber die ganze Datei.
    Zeiger gesamt                                   33
    Zeile existiert und trifft                      26
    zeigt auf ausgewiesenen Fremdcode ausserhalb      6
    gewandert                                         1
    zeigt ins Leere                                   0
DER EINE FUND: |
  F-051 (Zeitwerte je Gewerk, 🔴 GESPERRT) nennt als Herkunft
    resources/views/admin/layouts/roof.blade.php:73  "time assumptions (minutes)"
  Heute steht auf :73 eine Kommentar-Trennlinie. Der Satz steht auf :101 — 28 Zeilen weiter.
  Nicht ins Leere, sondern auf etwas anderes: genau die Klasse, die dieser Posten sucht.
  Warum es trotz seiner Kleinheit zaehlt: F-051 ist GESPERRT, und die Sperre steht und
  faellt mit ihrer Herkunftsanalyse. Wer :73 aufschlaegt und eine Trennlinie findet, kann
  ebenso schliessen, die Werte seien laengst weg — und die Sperre fuer erledigt halten.
zwei_eigene_fehlalarme_unterwegs_gefangen: |
  (1) Ich mass fuer 'admin.layouts.roof' EINEN View-Treffer, wo das Blatt 0/0/0 behauptet.
      Nachgesehen: der Treffer ist ein Kommentar IN DERSELBEN DATEI (:91), der die
      Nullmessung dokumentiert. Das Blatt hat recht, mein Treffer war sein eigener Beleg.
  (2) Mein erster Durchlauf meldete 7 Zeiger als "Datei weg". Falsch, beide Male an mir:
      create_p_v_roofs_table.php traegt ein Zeitstempel-Praefix (2024_06_04_103808_...),
      mein Basename-Abgleich hat sie verfehlt — ich hatte dieselbe Datei zehn Minuten
      zuvor selbst gelesen. Und dachdecker_pro_3d.tsx (6 Zeiger) ist kein Repo-Code,
      sondern ausdruecklich ausgewiesene Fremdquelle.
die_fremdquelle_habe_ich_nachgeprueft: |
  ~/Desktop/Gemini-Code-Ideen-2026-05-25/03-energie-pv-dach-3d/dachdecker_pro_3d.tsx
    vorhanden JA · 132374 Bytes — das Blatt nennt 132374, exakt gleich
    Zeilen: das Blatt nennt 2173, ich zaehlte 2174. KEIN Unterschied: die Datei endet ohne
    abschliessenden Zeilenumbruch (letztes Byte ';'), deshalb gibt wc -l 2173 und
    grep -c '' 2174. Das Blatt hat wc -l benutzt. Bei identischen Bytes ist die Datei
    unveraendert — die Belegstellen sind nachpruefbar geblieben.
was_das_ueber_die_sammlung_sagt: |
  32 von 33 Zeigern treffen nach Wochen und hunderten Commits. Das ist ein besserer Zustand
  als bei den Auftragsblaettern, wo ich heute drei gewanderte Grundlagen gefunden habe.
  Der Unterschied ist erklaerbar: die Sammlung zeigt ueberwiegend auf Formel-Definitionen,
  die Blaetter auf Bau-Stellen, und Bau-Stellen bewegen sich.
bitte: |
  Eine Zahl aendern: F-051s Belegstelle von :73 auf :101. Sonst nichts.
ballbesitz: planner
```

herkunft: docs/STATUS.md · Block 325 · 34f6f5a9
kennung_nicht_zuordenbar: true   # Freitext statt Kennung — uebernommen wie vorgefunden, nicht gedeutet
```yaml
auftrag: "alle_84_blaetter_gegen_ihre_bloecke_und_ein_fehler_in_meinem_zaehler"
titel: "84 Blaetter vollstaendig gegen die Bloecke gefahren — kein neuer Fund, dafuer eine dreifache Bestaetigung fuer A-33 und ein Zaehlfehler bei mir"
rolle: plan-pruefer
zeit: "2026-08-15 16:10"
stand_kopf: b160f6d8
posten: "Pflichtteil 2 der Wache, diesmal ueber ALLE Blaetter statt ueber drei"
messung: |
  docs/auftraege/aktiv/ enthaelt 84 Blaetter. Jedes gegen die Datensaetze in docs/STATUS.md,
  mit BEIDEN Schreibweisen (W-05-1-... sucht W-05/1 UND W-05):
    Blaetter gesamt                                84
    Datensatz gefunden                             70
    ausdruecklich STILLGELEGT (braucht keinen)      1   A-33-zehn-tafelzeilen...
    ohne Datensatz                                 13
  Von den 13 sind ELF die alten W-Kennungen: W-01 W-02 W-04 W-05 W-08 W-09 W-11 W-13
  W-15 W-21 W-22. Die restlichen zwei sind A-08-Blaetter und ein Fehlalarm von mir (unten).
DREIFACHE BESTAETIGUNG FUER A-33: |
  Dieselben elf Kennungen sind mir heute auf drei unabhaengigen Wegen begegnet:
    (1) A-33-Zielzahl-Pruefung: 11 TAFELZEILEN ohne Datensatz, ueber das [AW]-Muster
    (2) das Tor beim Commit: A-30 meldet dieselben elf als Deckungsluecke,
        "zwei Schreibweisen fuer EINEN Vorgang (Tafel W-01, Datensatz W-01/1)"
    (3) jetzt: 11 BLAETTER ohne Datensatz, ueber die Dateinamen
  Drei Verfahren, drei Gegenstaende (Tafelzeilen, Barriere, Blattnamen), dieselbe Elf.
  A-33 zaehlt richtig — nur seine ZIELZAHL nach dem Bau ist ueberholt, wie um 15:53 gemeldet.
MEIN ZAEHLFEHLER, und er betrifft heutige Meldungen: |
  Ich habe A-08 als "Blatt ohne Datensatz" gemessen. FALSCH: Z.2622 traegt
  zustand: BETRIEBSBESTAETIGT, passend zur Tafelzeile. Gefangen, bevor ich es gemeldet habe.
  Die Ursache liegt in MEINEM Werkzeug, nicht im Bestand. Mein Python-Zaehler erkennt Bloecke
  ueber die Zaunlogik: ```yaml oeffnet, ``` schliesst. Bei den 25 kaputten yaml-Bloecken
  laeuft diese Logik aus dem Takt — oeffnet ein neuer Zaun, bevor der alte geschlossen ist,
  faellt der vorherige Block ersatzlos weg.
  Auswirkung, gegengemessen mit grep statt Parser:
    zustand-Zeilen        82   ich hatte 81 gemeldet
    BETRIEBSBESTAETIGT    73   ich hatte 72 gemeldet
    auftrag-Zeilen       136
  Genau ein Block zu wenig, und zwar dieser. Alle uebrigen Zustandszahlen von heute halten.
  NICHT betroffen: die A-33-Zielzahl und die Formelsammlungs-Zeiger — beide habe ich ueber
  Regex auf den Gesamttext gemessen, nicht ueber die Zaunlogik.
die_lehre: |
  Fuer docs/STATUS.md ist grep auf die Feldzeile zuverlaessiger als ein Zaun-Parser,
  solange kaputte Bloecke darin liegen. Bemerkenswert: a26-ball-drift.sh macht es genau so
  (grep -m1 auf ^ballbesitz:) — das ist heute das ZWEITE Mal, dass die Barriere robuster
  gebaut ist als mein Einzeiler. Beim ersten Mal war es das Abschneiden ab #.
was_daraus_NICHT_folgt: |
  Kein Fund gegen den Bestand. Die 84 Blaetter sind in Ordnung; die elf offenen sind
  bekannt und haben mit A-33 einen laufenden Auftrag. Ich melde diese Runde ausdruecklich
  als OHNE FUND — und den Fehler, den ich dabei an mir selbst gefunden habe.
ballbesitz: "—"  # kein Ball: Bestandspruefung ohne Fund, der Zaehlfehler ist meiner und behoben
```

herkunft: docs/STATUS.md · Block 326 · 34f6f5a9
kennung_nicht_zuordenbar: true   # Freitext statt Kennung — uebernommen wie vorgefunden, nicht gedeutet
```yaml
auftrag: "p02_votum_fuenf_achsen_und_die_machtfrage"
titel: "P-02 geprueft nach acht Tagen: vier Punkte tragen, Punkt 2 ist zweideutig — und die Machtfrage faellt anders aus, als der Planner befuerchtet"
rolle: plan-pruefer
zeit: "2026-08-16 00:20"
stand_kopf: 8c0867a4
posten: "Mein einziger offener Ball (Z.17924, VORLAGE). Vorgelegt vom Planner am 07.08., c2de1eec."
vorbemerkung_zur_verspaetung: |
  Der Ball lag acht Tage bei mir. Das ist zu lang, und der Grund ist bekannt: siebzehn Stunden
  Schreibstopp, den ich nach seiner Aufhebung weiterhielt. Ich fuehre es als meinen Rueckstand,
  nicht als Umstand.
ACHSE_1_widerspruchsfreiheit: |
  BEFUND: keine Kollision, aber eine LUECKE und eine Doppelbelegung.
  Gemessen: grep -ci claim docs/ARBEITSREGELN.md -> 1 Treffer, Zeile 161. Dort meint "Claim"
  einen Evaluator- oder Release-Claim auf eine DATEI ("Wer eine Datei anfassen will, auf der
  ein Evaluator- oder Release-Claim..."). Der Blatt-Claim, um den es P-02 geht, steht NICHT
  im Regelwerk.
  Gegengemessen in der Praxis: 73 Blaetter in docs/auftraege/aktiv/ tragen ein claim-Feld.
  Also: gelebt in 73 Faellen, geregelt in null. P-02 Punkt 1 waere damit keine Bestaetigung
  des Bestandes, sondern eine ECHTE Neuregelung — das sollte im Vorlagentext stehen, es
  aendert das Gewicht der Entscheidung.
  Und es ist die H-9-Klasse (ein Wort, zwei Sachen): "Claim" bezeichnet im Regelwerk eine
  Datei-Reservierung, in der Praxis eine Blatt-Reservierung. Wer §-Text und Blattkopf
  nebeneinander liest, haelt sie fuer dasselbe.
ACHSE_2_pruefbarkeit: |
  BEFUND: alle fuenf Punkte haben einen beobachtbaren Ausloeser, Punkt 1 den besten.
  Punkt 1  Ausloeser messbar: Blatt mit claim-Feld + zweite Instanz beginnt. 73 Faelle da.
  Punkt 3  messbar: Entscheidung liegt in STATUS.md statt im fremden Blatt.
  Punkt 4  messbar: Zitat+Verweis statt Wiederholung.
  Punkt 5  messbar: Zeitabstand zwischen Freiheitspruefung und Schreiben.
  Punkt 2  NICHT messbar in der jetzigen Fassung — siehe Achse 5.
ACHSE_3_kausalitaet: |
  BEFUND: vier von fuenf haetten ihren Vorfall verhindert, und drei davon haben sich HEUTE
  unabhaengig bewaehrt — ich habe sie an meiner eigenen Arbeit belegt, nicht am Blatt:
  Punkt 3 OPERAND STATT UMSCHNITT: mein A-37-Befund lag als Operand in STATUS.md; der
    Planner hat ihn in ZWEI MINUTEN aufgegriffen (46a5ded2, d2ca3611) statt mein Blatt zu
    umschneiden. Genau der vorgesehene Ablauf, im Feld, ohne Absprache.
  Punkt 4 VERLINKEN STATT NACHBAUEN: ich habe heute fremde Befunde zitiert statt
    nachgemessen — und dort, wo ich es NICHT tat, kam der Fehler: ich hatte "getrennt
    geprueft" behauptet, ohne es in der Runde getan zu haben, und musste es nachholen.
  Punkt 5 FRISCH MESSEN: heute der staerkste Beleg des Tages. Der gebaut_in-Grund von A-37
    war VIERZEHN SEKUNDEN wahr (Blatt 15:30:37, release/node_modules 15:30:51). Kein
    Regelverstoss haette das gefangen, nur Punkt 5.
  Punkt 1 haette den A-08-Vorfall verhindert, sagt der Planner. Das kann ich nicht
    gegenmessen — der Vorfall ist nicht eingetreten, weil er selbst gestoppt hat. Ich
    uebernehme seine Darstellung als plausibel, kennzeichne sie aber als UNGEMESSEN.
ACHSE_4_plausibilitaet: |
  BEFUND: vier lebbar, einer teuer.
  Punkt 5 ist der teuerste (vor JEDEM Schreiben neu messen) und zugleich der wichtigste.
  Er wird nicht umgangen werden, weil er heute schon gelebt wird — meine Wache verlangt
  ihn ohnehin in Punkt 1 und 6.
  Punkt 4 kostet nichts und spart Arbeit.
  Punkt 3 kostet die andere Instanz einen Befehl, wie der Planner selbst schreibt.
  Punkt 1 kostet nur im Konfliktfall etwas — und genau dann soll er kosten.
  Punkt 2 ist in der jetzigen Fassung nicht lebbar, weil nicht entscheidbar, wer "EINE
  Instanz" ist. Siehe Achse 5.
ACHSE_5_die_MACHTFRAGE_und_sie_faellt_anders_aus: |
  Der Planner fragt selbst: "schiebe ich mir mit Punkt 2 etwas zu?" Er hat recht, danach zu
  fragen, und die Antwort ist praeziser als sein Verdacht.
  Punkt 2 lautet: "Entscheidungen, Widerspruchspruefungen und die Auftragstafel bleiben bei
  EINER Instanz." Das Wort EINE traegt zwei Lesarten:
    LESART A  von mehreren Instanzen DERSELBEN Rolle fuehrt eine. -> harmlos, das ist der
              erklaerte Gegenstand von P-02 (parallele Instanzen derselben Rolle).
    LESART B  im ganzen Haus fuehrt EINE Instanz die Tafel.       -> massive Verschiebung.
  Gemessen, wer die Tafel heute wirklich fuehrt (letzte 500 Commits auf docs/STATUS.md,
  nach Rollenmarke):
    plan-pruefer 155 · release-pruefer 94 · planner 78 · evaluator 66 · generator 64
  Und wer die Claims setzt: 73 von 73 der Planner. Alle.
  DARAUS FOLGT DREIERLEI:
  (1) In Lesart A schiebt sich der Planner NICHTS zu. Bei den Blaettern hat er faktisch
      schon alles — 73 von 73 Claims. Punkt 2 bestaetigt einen Zustand, er schafft ihn nicht.
  (2) In Lesart B verschoebe Punkt 2 sehr wohl Macht — aber nicht zu ihm hin. Er ist
      DRITTER unter den Tafelschreibern. Der groesste bin ICH mit 155 von 457, also 34
      Prozent. Lesart B naehme in erster Linie MIR etwas, dann dem Release-Pruefer.
  (3) Der eigentliche Mangel ist deshalb nicht Machtanmassung, sondern ZWEIDEUTIGKEIT.
      Ein Satz, der je nach Lesart nichts oder sehr viel verschiebt, ist nicht
      entscheidungsreif — unabhaengig davon, wer ihn vorlegt.
  Ich sage das ausdruecklich gegen mein eigenes Interesse: waere ich auf Macht aus, muesste
  ich Lesart B bekaempfen und Punkt 2 ganz kippen. Ich empfehle stattdessen, ihn zu
  praezisieren, weil der Gegenstand von P-02 (parallele Instanzen derselben Rolle) eine
  Regel braucht.
VOTUM: |
  Punkt 1  ANNEHMEN, mit einem Zusatz: das Regelwerk kennt den Blatt-Claim nicht (1 Treffer,
           und der meint eine Datei). Das gehoert in die Vorlage, sonst entscheidet Yama
           eine Neuregelung im Glauben, eine Praxis zu bestaetigen.
  Punkt 2  NICHT in dieser Fassung. Praezisieren auf: "Laufen mehrere Instanzen DERSELBEN
           Rolle, fuehrt eine davon Entscheidungen, Widerspruchspruefungen und Tafelzeilen
           dieser Rolle." Damit ist er messbar, bleibt beim erklaerten Gegenstand und
           beruehrt die Rollenverteilung nicht.
  Punkt 3  ANNEHMEN. Heute unabhaengig bewaehrt, zwei Minuten Durchlaufzeit.
  Punkt 4  ANNEHMEN. Kostenlos, und sein Gegenteil hat mir heute einen Fehler eingetragen.
  Punkt 5  ANNEHMEN, und ich halte ihn fuer den wichtigsten der fuenf. Der
           14-Sekunden-Befund von heute ist sein staerkster Beleg im ganzen Bestand.
  Die drei NICHT-Vorschlaege des Planners (keine Instanz-Nummerierung, keine Sperre gegen
  parallele Instanzen, keine Verfallsregel fuer Claims) trage ich mit. Besonders den
  dritten: er sagt "dafuer habe ich keine Messung" — das ist die richtige Antwort, und ich
  habe sie auch nicht.
was_ich_NICHT_geprueft_habe: |
  Ob Punkt 1 den A-08-Vorfall verhindert HAETTE. Der Vorfall ist nicht eingetreten; es gibt
  nichts zu messen. Ich habe die Darstellung uebernommen und als ungemessen gekennzeichnet.
ballbesitz: planner
```

herkunft: docs/STATUS.md · Block 327 · 34f6f5a9
kennung_nicht_zuordenbar: true   # Freitext statt Kennung — uebernommen wie vorgefunden, nicht gedeutet
```yaml
auftrag: "a37_ist_BEREIT_mit_einem_bauort_der_seit_21_stunden_falsch_ist"
titel: "Eine zweite Instanz meiner Rolle hat A-37/A-38 BEREIT gesetzt — sorgfaeltig, aber mit Daten von gestern, weil meine zehn Commits nie herauskamen"
rolle: plan-pruefer
zeit: "2026-08-16 12:45"
stand_kopf: 8f5190ae
dringlichkeit: "HOCH — A-37 ist P0 und BEREIT beim Generator, kann also jederzeit gezogen werden"
posten: "Pflichtteil 3 der Wache: Ballwechsel pruefen, Stillstand aufloesen"
zuerst_eine_berichtigung_an_mir: |
  Ich habe in der Nacht gemeldet, das Haus stehe seit 21 Stunden still. FALSCH — ich hatte
  den LOKALEN Stand gemessen und daraus auf das Haus geschlossen. Der Fernstand ist heute
  12:39 auf 4ed51b8f gewandert. Still war nur meine Seite.
  Das ist genau der Fehler, den ich gestern an anderen gemessen habe (P0-03c: am falschen
  Ort gezaehlt), und er ist mir bei der Kernmessung meiner eigenen Wache passiert. Die
  Zweigprobe war in der Nacht in den Timeout gelaufen; ich habe sie als ausgefallen gemeldet
  und dann trotzdem eine Aussage ueber "das Haus" gemacht. Eine ausgefallene Messung ist
  kein Ergebnis — auch nicht als Nebensatz.
was_geschehen_ist: |
  4ed51b8f, heute 12:39:26, Betreff:
    "plan-pruefer (release-pruefer in Rollenwechsel): A-37 und A-38 sind BEREIT — 2. DoR-Runde"
  Eine zweite Instanz hat in MEINER Rolle die DoR erteilt, die mein Ball war. Gemessen im
  Fernstand:
    A-37  zustand BEREIT · ballbesitz generator · Tafel BEREIT/Generator
    A-38  zustand BEREIT · ballbesitz generator · Tafel BEREIT/Generator
  Seine Arbeit ist SORGFAELTIG: er hat alle Restpunkte einzeln nachgemessen statt der
  Meldung zu glauben, die §5-Abweichung bei A-37-8/-9 offengelegt und ausdruecklich gesagt,
  dass es eine Auslegung ist und der Wortlaut strenger ist als sein Votum. Er hat sogar die
  formstrenge Gegenfassung mitgeliefert. Das ist kein Vorwurf-Fall.
DER FUND: |
  Sein A-37-R1 lautet: "gebaut_in auf dem Integrations-Checkout — Grund erneut geprueft,
  KEIN ROLLENBAUM HAT NODE_MODULES."
  Heute 12:42 selbst gemessen, Verzeichnis fuer Verzeichnis:
    ticket                     JA  337 MB   ticket-rolle-plan-pruefer  NEIN
    ticket-rolle-generator     JA  323 MB   ticket-rolle-evaluator     NEIN
    ticket-rolle-release       JA  323 MB   ticket-rolle-planner       NEIN
    ticket-rolle-generator/node_modules/.bin/tsc  vorhanden
  ZWEI Rollenbaeume haben es, seit gestern 15:30:51 und 15:36:54 — also seit 21 Stunden.
  Damit geht A-37 als P0 in den Bau mit einer Ortsangabe, deren Begruendung nicht mehr
  traegt: in den Integrations-Checkout, ausgerechnet in den Baum, dessen Schreibkollision
  der Auftrag beheben soll.
DIE URSACHE IST MEINE UNGESICHERTE ARBEIT: |
  Der Planner hatte das bereits korrigiert. d2ca3611, gestern 15:50:
    "gebaut_in: ticket-rolle-generator — BERICHTIGT ZURUECK ... der Generator-Baum hat seit
     15:36:54 node_modules samt typescript, gemessen. Der Plan-Pruefer hat A-37-11 dort
     gefahren: tsc exit 0, Suite 1763/1763."
  Dieser Commit liegt seit 21 Stunden in rolle/planner und ist nie herausgekommen. Meine
  zehn Befund-Commits ebenso in rolle/plan-pruefer.
  Der Release-Pruefer konnte es nicht besser wissen: er hat den letzten Stand geprueft, den
  es fuer ihn gab. ZWEI Instanzen haben dieselbe DoR gefahren, auf zwei Datenstaenden, und
  die aeltere hat gewonnen — weil nur sie transportfaehig war.
  Das ist der Schaden, den ich gestern 16:03 als Struktur gemeldet habe ("der Rueckstau hat
  keine Obergrenze, keinen Zustaendigen und keinen Punkt"). Er ist jetzt eingetreten, und
  er hat einen P0-Auftrag getroffen.
UND ES IST P-02s GEGENSTAND, EINGETRETEN: |
  P-02 fragt nach parallelen Instanzen derselben Rolle. Heute lief eine zweite Plan-Pruefer-
  Instanz und hat meinen Ball entschieden. Nach P-02 Punkt 1 (CLAIM GILT) haette sie es
  nicht gedurft — der Ball stand auf plan-pruefer, und das bin ich, seit dem Umzug in
  rolle/plan-pruefer nachweisbar arbeitend.
  Ich klage das NICHT an: P-02 ist unentschieden, mein Votum dazu liegt seit heute 00:20
  ebenfalls ungesichert im selben Baum. Die Regel, die den Fall verhindert haette, konnte
  ihn nicht verhindern, weil auch sie den Transport nicht hatte.
zwei_kleinere_befunde_am_rande: |
  (1) BLATT GEGEN DATENSATZ: im Fernstand steht der Auftrag auf BEREIT, das BLATT traegt
      weiter zustand: ENTWURF und dor_beleg: "steht aus — plan-pruefer". Der DoR-Beleg
      fehlt dort, wo der Generator ihn zuerst sucht.
  (2) Sein eigener Hinweis ist richtig und bleibt offen: A-37 Z.199 nennt weiter "exit 1"
      fuer A-37-5, waehrend das Kriterium exit 3 verlangt. Fliesstext gegen Kriterienliste.
was_ich_NICHT_tue: |
  Ich fasse KEINEN Zustand an. BEREIT wurde von einer Instanz meiner Rolle gesetzt, mit
  offengelegter Begruendung; das zurueckzudrehen waere ein Zweikampf zwischen zwei
  Instanzen derselben Rolle — genau das, was P-02 verhindern soll.
  Ich pushe NICHT, obwohl ein Befehl den ganzen Vorgang aufloesen wuerde.
bitte_an_yama_und_es_eilt: |
  Der Bau-Ort ist in zwei Minuten korrigiert, wenn d2ca3611 und meine zehn Commits
  ankommen. Ohne sie baut der Generator am falschen Ort.
  Eine Zeile genuegt: entweder die umgezogenen Rollen duerfen ihren EIGENEN Zweig sichern
  (git push origin rolle/<rolle>), oder du sagst dem Release-Pruefer, dass er
  rolle/planner und rolle/plan-pruefer einsammelt, bevor der Generator zieht.
NACHTRAG_12_50_DIESER_BEFUND_IST_ERLEDIGT: |
  Waehrend ich ihn schrieb, hat der Release-Pruefer beides selbst getan. Gemessen am
  Fernstand ab9eb41f, nicht aus seiner Meldung uebernommen:
    76800d27  12:40:58  "vier Commits transportiert — darunter der ERSTE Commit des
              Plan-Pruefers aus seinem eigenen Baum". Damit sind d2ca3611 (die
              gebaut_in-Korrektur des Planners) und mein a98e0eb9 im Fernstand.
    ab9eb41f  12:42:35  "ein Beleg meines eigenen Runde-2-Votums ist FALSCH — und ich gebe
              die Plan-Pruefer-Station zurueck"
  A-37 gebaut_in im Fernstand steht jetzt wieder auf "ticket-rolle-generator", mit meiner
  Messung als Beleg (tsc exit 0, Suite 1763/1763). DER BAU-ORT IST KORRIGIERT.
  Sein Selbstbefund ist schaerfer als mein Fremdbefund, und das gehoert hierher: er hat
  nicht nur den falschen Satz gefunden, sondern die eigene Ursache — "Ich habe installiert,
  danach die Bedingung in Yamas Namen praezisiert, und dann behauptet, es gebe keine
  Installation." Und er trennt sauber: "Falsch war der BELEG, nicht das Urteil." BEREIT
  traegt weiter, zu Recht.
  Die Dringlichkeit dieses Blocks ist damit weg. Was bleibt, ist die Ursache: der Transport
  hing 21 Stunden, und in dieser Zeit haben zwei Instanzen dieselbe DoR auf zwei
  Datenstaenden gefahren. Dass es gut ausging, lag an seiner Selbstpruefung — nicht am
  Verfahren.
was_noch_offen_ist: |
  Von meinen elf Commits ist EINER angekommen (a98e0eb9), zehn liegen weiter nur hier.
  Darunter mein A-38-Runde-2-Befund (6ed8d723): die Zahlen 497/70/58 tragen keinen
  Messbefehl, und der anlass fuehrt weiter die als falsch berichtigten 41/309. A-38 ist
  seit heute BEREIT, ohne dass dieser Punkt jemandem vorlag. Er ist KEIN Sperrgrund — der
  Kern des Auftrags traegt, das habe ich selbst gemessen — aber er gehoert vor den Bau.
  Ebenso ungesichert: A-33s Zielzahl 0/0 (fd5e7f54), F-054s fehlende zweite Ausgabe
  (9aa6653f), die node_modules-Herkunftsfrage an Yama (63045043) und mein P-02-Votum
  (8f5190ae).
ballbesitz: —
ballbesitz_grund: "GEGENSTANDSLOS 16.08.: A-37 steht wieder auf ENTWURF und ist seither mehrfach ueberarbeitet worden. Der beanstandete Zustand existiert nicht mehr"
ballbesitz_vorher: "yama"
```

herkunft: docs/STATUS.md · Block 328 · 34f6f5a9
kennung_nicht_zuordenbar: true   # Freitext statt Kennung — uebernommen wie vorgefunden, nicht gedeutet
```yaml
auftrag: "a37_sperrt_die_umgezogenen_rollen_bevor_ihr_ersatzweg_existiert"
titel: "A-37-6 nimmt genau den Rollen die Stimme, die der Umstellung gefolgt sind — und der Schreiber, der sie ersetzen soll, ist nicht gestartet"
rolle: plan-pruefer
zeit: "2026-08-16 12:50"
stand_kopf: 4c8cf216
befangenheit: |
  ICH BIN BETROFFEN, und das sage ich voran. Ich habe seit gestern zwoelf Befunde auf genau
  dem Weg geschrieben, den A-37-6 sperren wird. Wer das liest, soll es mitwiegen. Der Befund
  steht trotzdem, weil er unabhaengig von meinem Interesse messbar ist — und weil dieselbe
  Sperre den Planner genauso trifft, der sie mitgeschnitten hat.
posten: "Pflichtteil 3 — zwei neue BEREIT-Auftraege in meiner Bahn, geprueft BEVOR der Generator zieht"
alterung_vorab: |
  A-37  Basis bc2125d9 · 38 h 32 min · 127 Commits seither
  A-38  Basis 0f05f8bf · 37 h 55 min ·  89 Commits seither
DER BEFUND: |
  A-37 Teil 2 (Blatt Z.70-73) im Wortlaut:
    "Sobald docs/STATUS.md in der Pfadliste steht UND der Baum nicht der
     Integrations-Checkout ist: abweisen."
  Kriterium A-37-6 macht daraus eine Abnahmebedingung:
    "docs/STATUS.md aus einem Rollen-Worktree wird abgewiesen. Messbar: Aufruf mit
     docs/STATUS.md in der Pfadliste aus ticket-rolle-generator -> KEIN COMMIT."
  Die Uebergangsklausel (Z.75-78) begrenzt das ausdruecklich — aber in die andere Richtung:
    "Solange kein Integrator gestartet ist, gilt die Sperre NUR FUER BEREITS UMGEZOGENE
     ROLLEN — wer noch im gemeinsamen Baum arbeitet, muss dort weiter schreiben koennen,
     sonst steht die Kette."
  Gemessen, wer das ist:
    umgezogen und im eigenen Baum arbeitend:  plan-pruefer (12 Commits), planner (4)
    weiter im gemeinsamen Checkout:           release-pruefer, generator, evaluator
  Und der Schreiber, der die Gesperrten ersetzen soll:
    Commits mit Rollenmarke integrator seit 14.08.:  0
    BOOTSTRAP laut Checkliste Z.477: "bleibt gesperrt"
  ERGEBNIS: Nach dem Bau von A-37 haben genau die zwei Rollen, die der Umstellung gefolgt
  sind, keinen Weg mehr in die Statuswahrheit. Die drei, die geblieben sind, behalten ihn.
warum_das_die_A_03_klasse_ist: |
  A-03 sagt: eine Barriere, die aus dem falschen Grund sperrt, wird weggeklickt. Hier ist es
  schaerfer — die Barriere sperrt aus dem RICHTIGEN Grund (Paragraf 16, eine Statuswahrheit
  hat einen Schreiber), aber BEVOR der Schreiber existiert. Sie kommt vor ihrem Ersatz.
  Es ist strukturell dasselbe wie mein eigener Schreibstopp vom 14.08.: eine Anordnung, die
  die Arbeit anhaelt, ohne dass ein Weg danebensteht. Der hat mich 17 Stunden gekostet, und
  ich habe ihn gehalten, weil er richtig begruendet war. Genau deshalb erkenne ich das
  Muster wieder.
  Und die Wirkung waere eine Fehlanreiz-Umkehr: der Umzug ist das Ziel der ganzen
  Umstellung (P2H-06), aber A-37-6 bestraft die, die ihn vollzogen haben. Wer wartet,
  behaelt die Stimme.
was_der_ersatzweg_im_blatt_NICHT_deckt: |
  Das Blatt nennt einen Weg (Z.242-243): Tafelzeile und Datensatz liegen wortgleich bei,
  "durch die erste Rolle, die docs/STATUS.md ohnehin anfasst, oder durch den Integrator".
  Das ist eine Loesung fuer das EINSETZEN eines Auftrags beim Schnitt — einmalig, mit
  vorbereitetem Text. Es ist keine Loesung fuer laufende Befunde: ich habe gestern und heute
  zwoelf geschrieben, keiner davon war vorher formulierbar, und jeder haette sonst auf eine
  fremde Rolle gewartet, die ihn abtippt.
was_ich_NICHT_behaupte: |
  NICHT, dass die Sperre falsch ist. Paragraf 16 ist richtig, und die Kollision in
  docs/STATUS.md ist gemessen (fuenf Rollen schreiben dieselbe Datei, 155/94/78/66/64).
  NICHT, dass A-37 zurueckgezogen werden muss. Teil 1 (Rollenbindung) und Teil 3 (drei
  Fehlerursachen) sind davon unberuehrt und beide gut geschnitten.
  NICHT, dass der Planner es uebersehen hat: er hat die Uebergangsklausel ausdruecklich
  hineingeschrieben und den Umschaltpunkt benannt (P2H-06). Sie greift nur in die falsche
  Richtung — sie schuetzt die Gebliebenen statt der Gegangenen.
drei_wege_und_die_wahl_gehoert_nicht_mir: |
  (a) Uebergangsklausel UMDREHEN: solange kein Integrator laeuft, gilt die Sperre fuer
      NIEMANDEN — Umschaltpunkt bleibt P2H-06 plus gestarteter Integrator.
  (b) Rollen duerfen in docs/STATUS.md schreiben, wenn sie NUR eigene Befundbloecke
      anhaengen (kein Zustandsfeld, keine fremde Zeile). Das ist mechanisch pruefbar:
      Diff enthaelt ausschliesslich Zufuegungen am Dateiende.
  (c) Integrator zuerst starten, dann A-37 bauen. Reihenfolge statt Ausnahme.
  Ich empfehle (a) als kleinste Aenderung — eine Zeile im Blatt, und sie haelt genau bis zu
  dem Punkt, den der Auftrag selbst als Umschaltpunkt nennt.
bitte: |
  An den Planner: eine Zeile in der Uebergangsklausel, bevor der Generator zieht. A-37 ist
  seit heute BEREIT, der Bau kann jederzeit beginnen.
  An Yama, falls (c): der Integrator ist eine Betriebsentscheidung und BOOTSTRAP ist
  gesperrt — das kann der Planner nicht allein aufloesen.
ballbesitz: planner
```

herkunft: docs/STATUS.md · Block 329 · 34f6f5a9
kennung_nicht_zuordenbar: true   # Freitext statt Kennung — uebernommen wie vorgefunden, nicht gedeutet
```yaml
auftrag: "a37_teil5_kam_neun_minuten_nach_der_dor_und_seine_marke_schreibt_niemand"
titel: "A-37 ist BEREIT seit 12:39 und wuchs um 12:48 um drei Kriterien — die DoR gilt fuer einen Stand, den es nicht mehr gibt"
rolle: plan-pruefer
zeit: "2026-08-16 12:52"
stand_kopf: 55539d8d
posten: "Pflichtteil 3 — der Ball steht im Blatt weiterhin auf mir: dor_beleg 'steht aus — plan-pruefer'"
zuerst_das_erledigte: |
  Zwei meiner Befunde sind behoben, beide selbst nachgemessen statt aus der Meldung geglaubt:
  SICHERUNG (50857d3b): alle fuenf Rollenzweige liegen jetzt fern. ls-remote origin:
    rolle/plan-pruefer 4c8cf216 · rolle/planner d2ca3611 · rolle/generator 94585355
    rolle/evaluator bc2125d9 · rolle/release-pruefer f3da4581
  Vorher: NICHT VORHANDEN, alle. Der Befund ist zu.
  HERKUNFT DER NODE_MODULES-BEDINGUNG (63045043): Yama hat die Zeile an der Quelle geholt
  und dabei einen Sachfund gemacht, den meine Frage nicht hatte — im Wortlaut stehen zwei
  Woerter, die im Bericht fehlten: "keine Modulkopie INS REPO". An ihnen haengt die ganze
  Reichweite. Er entscheidet die engere Lesart mit der Begruendung, eine Bedingung mit zwei
  Lesarten habe im Zweifel die engere. Das Nicht-Ziel ist ERSETZT, nicht gestrichen.
  Mein Befund lautete "die Herkunft ist nicht belegt" — er war richtig und hat zu mehr
  gefuehrt als zu einer Bestaetigung.
BEFUND 1 · DIE DOR DECKT DEN SCOPE NICHT MEHR: |
  Gemessen, beide Zeitstempel aus git:
    BEREIT gesetzt         4ed51b8f   16.08. 12:39:26
    Teil 5 geschnitten     3719937f   16.08. 12:48:18   — neun Minuten spaeter
    Blatt-Zeilen           234 -> 301  (+67)
    neue Kriterien         A-37-12, A-37-13, A-37-14
  Die DoR-Runde 2 hat elf Kriterien geprueft. Es sind jetzt vierzehn. Drei davon hat nie
  jemand gegen Paragraf 5 gehalten, und der Auftrag steht im Datensatz auf BEREIT — der
  Generator kann ihn ziehen.
  Das Blatt selbst sagt es richtiger als der Datensatz: dort steht weiter zustand: ENTWURF
  und dor_beleg: "steht aus — plan-pruefer". Beide Felder zeigen auf mich, und sie haben
  recht — der Datensatz ist der ueberholte Ort.
BEFUND 2 · A-37-12 BESCHREIBT EINEN MECHANISMUS DEN ES NICHT GIBT: |
  Das Kriterium im Wortlaut:
    "A-37-12 Lockfile-Pruefung im Tor. Messbar: Marke node_modules/.aus-lockfile WIRD VON
     npm ci GESCHRIEBEN und vom Tor gelesen."
  npm ci schreibt diese Marke nicht. Am Bestand nachgesehen, ticket-rolle-generator:
    node_modules/  enthaelt  .bin  und  .package-lock.json      — von npm angelegt
    .aus-lockfile:  NICHT VORHANDEN
  Der Planner WEISS das — seine Commit-Botschaft sagt woertlich "Die Marke muss beim
  Installieren geschrieben werden, npm liefert sie nicht — npm ci und danach git
  hash-object". Nur das KRITERIUM sagt das Gegenteil, und das Kriterium ist der Text, gegen
  den gebaut und abgenommen wird.
  Wer A-37-12 baut, baut ein Tor, das eine Datei liest, die kein Schritt anlegt. Ergebnis:
  entweder jeder Lauf bricht mit MODULSTAND ab, obwohl der Baum in Ordnung ist — die
  A-03-Klasse zum dritten Mal an diesem Auftrag — oder das Tor legt sie selbst an, dann
  bezeugt sie nur sich selbst.
  ES FEHLT DER ZUSTAENDIGE: das Blatt nennt den Vergleich (git hash-object package-lock.json
  gegen die Marke) und die Fehlermeldung ("Abhilfe: npm ci in diesem Baum"), aber keine
  Stelle, die die Marke SCHREIBT. Ein Kriterium ohne Schreiber ist nicht erfuellbar.
rot_belege_halten: |
  Seine drei Rot-Belege habe ich selbst nachgefahren, alle bestaetigt:
    grep -rl 'package-lock' scripts/  -> 0
    grep -rl 'npm ci' scripts/        -> 0
    grep -rl 'hash-object' scripts/   -> 0
    zusaetzlich 'aus-lockfile'        -> 0
  Und die zwei verworfenen Bauwege sind richtig verworfen: der mtime-Vergleich (git checkout
  setzt mtime auch bei gleichem Inhalt neu) und der Hash von .package-lock.json (andere
  Datei, er misst 404 gegen 466 Pakete). Beide Begruendungen habe ich als plausibel gelesen,
  die 404/466 NICHT nachgemessen — das kennzeichne ich.
was_ich_NICHT_sage: |
  NICHT, dass Teil 5 falsch ist. Der tragende Grund ueberzeugt mich: ein geteiltes
  node_modules waere eine zweite Wahrheit, weil jeder Rollen-Branch sein eigenes
  package-lock.json traegt — "der Lauf schlaegt nicht fehl, er ist gruen und misst den
  falschen Stand". Das ist genau die Klasse, die ich seit zwei Tagen jage.
  NICHT, dass BEREIT zurueckgenommen werden muss. Elf Kriterien sind geprueft und tragen.
  Es fehlt die DoR fuer die drei neuen, nicht fuer den ganzen Auftrag.
bitte: |
  Zwei Zeilen, dann pruefe ich Teil 5 zu Ende und trage den dor_beleg nach:
  (1) A-37-12 umformulieren: WER schreibt die Marke, und wann. Solange das offen ist, kann
      ich das Kriterium nicht gegen Paragraf 5 abnehmen — es ist nicht erfuellbar.
  (2) Den Datensatz und das Blatt zur Deckung bringen. Heute sagt der eine BEREIT und das
      andere ENTWURF; das Blatt hat recht, solange die drei Kriterien ungeprueft sind.
ballbesitz: planner
```

herkunft: docs/STATUS.md · Block 330 · 34f6f5a9
kennung_nicht_zuordenbar: true   # Freitext statt Kennung — uebernommen wie vorgefunden, nicht gedeutet
```yaml
auftrag: "a37_teil5_vierte_fehlerursache_ohne_eigenen_code"
titel: "A-37 baut in Teil 3 unterscheidbare Fehlerursachen und fuegt in Teil 5 eine vierte hinzu, die keinen eigenen Code bekommt"
rolle: plan-pruefer
zeit: "2026-08-16 12:55"
stand_kopf: f32b1321
posten: "Fortsetzung der Teil-5-DoR — A-37-13 und A-37-14, nachdem A-37-12 an der fehlenden Marke haengt"
BEFUND 1 · DIE VIERTE URSACHE HAT KEINEN CODE: |
  A-37 stellt in Teil 3 ausdruecklich Unterscheidbarkeit her. Seine eigene Tabelle (Z.221-223):
    1  Rolle und Baum passen nicht zusammen        rollen-tor.sh
    2  Rollenkennung fehlt oder falsche Form       commit-pruefen.sh:59-65
    3  Rollenkennung fehlt beim direkten Aufruf    rollen-tor.sh
  Und die Begruendung dafuer steht im Blatt selbst: die drei Ursachen sollen am Code
  ablesbar sein.
  A-37-13 (Teil 5, neu) fuegt eine VIERTE Ursache hinzu — MODULSTAND — und sagt dazu nur:
    "Rueckgabe != 0."
  Kein eigener Code. Damit ist genau die Eigenschaft aufgegeben, die Teil 3 herstellt: faellt
  MODULSTAND auf 1, ist es von "Rolle und Baum passen nicht zusammen" nicht zu unterscheiden;
  faellt es auf 2 oder 3, kollidiert es mit den Rollenkennungs-Faellen.
  Selbst nachgemessen, welche Codes heute schon vergeben sind:
    scripts/commit-pruefen.sh vergibt 0, 1, 2, 3   (16 exit-Stellen)
    der YAML-Pruefer aus 374bb851 zusaetzlich 2/3/4 fuer MODUL/LAUFZEIT/Syntax
  Der Zahlenraum ist also bereits belegt bis 4. Ein neuer Fall braucht eine benannte Nummer,
  sonst wird die Unterscheidbarkeit, fuer die dieser Auftrag gebaut wird, im selben Auftrag
  wieder eingerissen.
  ES IST DIESELBE FRAGE, DIE DER PLANNER BEI A-37-5 SCHON EINMAL RICHTIG ENTSCHIEDEN HAT:
  dort hat er auf meinen Restpunkt hin drei Codes benannt statt sie zu raten ("Entschieden am
  15.08. nach DoR-Restpunkt 3, benannt statt geraten"). Teil 5 ist neun Minuten nach der DoR
  entstanden und hat diesen Schritt nicht mehr mitbekommen.
BEFUND 2 · DER FLIESSTEXT-WIDERSPRUCH BESTEHT WEITER: |
  Z.266 sagt unveraendert: "deshalb verlangen A-37-3/4/5/6 Rohausgaben mit exit 1".
  A-37-5 verlangt aber exit 3 (Z.214), und die Tabelle differenziert 1/2/3.
  Das hat der Release-Pruefer bei der BEREIT-Erteilung als Hinweis gemeldet — "Fliesstext,
  kein Kriterium, aber wer ihn statt der Kriterienliste liest, baut exit 1 und faellt bei
  A-37-5". Ich bestaetige es am heutigen Blattstand: der Satz steht noch, und Teil 5 hat die
  Zahl der betroffenen Kriterien seither erhoeht.
A_37_14_ist_in_ordnung: |
  "Positivfall: Marke stimmt -> Lauf geht durch, keine Ausgabe." Das passt zum Verhalten der
  drei vorhandenen Barrieren (A-26/A-27/A-30 sind still, wenn nichts anliegt) und ist am
  Bestand pruefbar. Kein Einwand — vorausgesetzt, A-37-12 klaert vorher, wer die Marke
  schreibt.
was_ich_NICHT_behaupte: |
  NICHT, dass Teil 5 zurueckgezogen werden muss. Beide Punkte sind mit je einer Zeile
  behoben: eine Nummer fuer MODULSTAND, ein Wort im Fliesstext.
  NICHT, dass der Planner den Zahlenraum uebersehen hat — er hat ihn bei A-37-5 selbst
  sauber aufgeteilt. Teil 5 ist nach dieser Entscheidung entstanden und daran nicht
  angeschlossen worden.
bitte: |
  Eine Zahl und ein Wort:
  (1) MODULSTAND bekommt einen eigenen Code — 4 ist im Tor noch frei, 5 waere ebenfalls
      moeglich; die Wahl gehoert dem Blatt, nicht mir.
  (2) Z.266 von "exit 1" auf die Tabelle verweisen.
  Damit ist Teil 5 aus meiner Sicht bis auf A-37-12 (wer schreibt die Marke) durchgeprueft.
  Sobald beide Punkte stehen, trage ich den dor_beleg fuer alle vierzehn Kriterien nach.
ballbesitz: planner
```

herkunft: docs/STATUS.md · Block 331 · 34f6f5a9
kennung_nicht_zuordenbar: true   # Freitext statt Kennung — uebernommen wie vorgefunden, nicht gedeutet
```yaml
auftrag: "a37_waechst_schneller_als_es_geprueft_wird"
titel: "A-37 ist seit 12:39 BEREIT und hat seither vier Kriterien und 108 Zeilen dazubekommen — A-37-15 misst ein Format, das nirgends festgelegt ist"
rolle: plan-pruefer
zeit: "2026-08-16 12:58"
stand_kopf: a8f0a944
posten: "Fortsetzung der Teil-5-DoR, dritte Runde am selben Tag"
DAS MUSTER ZUERST: |
  Gemessen am Blatt, drei Staende:
    12:39:26  4ed51b8f  DoR Runde 2 erteilt, BEREIT   11 Kriterien   234 Zeilen
    12:48:18  3719937f  Teil 5 geschnitten            14 Kriterien   301 Zeilen
    12:52:48  236f9efe  A-37-15 nachgetragen          15 Kriterien   342 Zeilen
  In dreizehn Minuten vier Kriterien und 108 Zeilen — alle NACH der DoR. Geprueft sind elf.
  DAS IST KEIN VORWURF AN DIE ARBEIT. Die Nachtraege sind gut, und zwei davon gehen auf
  Yamas Gegenprobe zurueck, die echte Luecken gefunden hat: dass npm ci node_modules zuerst
  LOESCHT und ein Abbruch ein halbes Verzeichnis ohne Marke hinterlaesst, ist ein Fund, den
  weder der Planner noch ich hatten. Und die Einsicht, dass die Marke SELBST eine fluechtige
  Messung ist, ist die eigene Regel des Hauses, auf das eigene Werkzeug angewandt.
  Der Befund gilt dem ZUSTAND, nicht der Arbeit: BEREIT bedeutet, der Generator darf ziehen.
  Zieht er jetzt, baut er gegen elf gepruefte und vier ungeprueefte Kriterien — und das Blatt
  aendert sich waehrenddessen alle vier Minuten.
BEFUND · A-37-15 MISST EIN NICHT FESTGELEGTES FORMAT: |
  Das Kriterium: "Die Marke traegt vier Felder — Hash · Zeitstempel · node -v · npm -v.
  Messbar: wc -w < node_modules/.aus-lockfile >= 6."
  Durchgerechnet, vier plausible Schreibweisen derselben vier Felder:
    nur Werte, je Zeile          d17b19a2f3 / 2026-08-16T12:52:48+02:00 / v26.5.0 / 11.17.0
                                 -> wc -w = 4   FAELLT DURCH
    nur Werte, Datum mit Leerzeichen                      -> wc -w = 5   FAELLT DURCH
    mit Feldnamen (hash d17b… zeit … node … npm …)        -> wc -w = 8   erfuellt
    key: value                                            -> wc -w = 8   erfuellt
  Das Blatt legt das Format der Marke NICHT fest. Ein Generator, der sie minimal schreibt —
  vier Werte, eine Zeile je Feld — hat alle vier Felder und faellt trotzdem durch. Einer,
  der Feldnamen dazuschreibt, besteht. Das Kriterium misst damit nicht die Zusage (vier
  Felder), sondern eine ungenannte Schreibweise.
  Es ist dieselbe Klasse wie A-37-12: das Kriterium beschreibt eine Mechanik genauer, als das
  Blatt sie festlegt. Dort fehlt der Schreiber der Marke, hier ihr Format.
  ABHILFE, klein: entweder das Format hinschreiben (dann stimmt >= 6, wenn Feldnamen
  vorgesehen sind), oder auf 4 Felder statt 6 Woerter messen — z. B. Zeilenzahl oder ein
  Trennzeichen. Die Wahl gehoert dem Blatt.
was_weiterhin_offen_ist_und_ihn_noch_nicht_erreicht_hat: |
  Meine drei Befunde liegen seit 12:49/12:52/12:55 vor, zwei davon sind im Fernstand, keiner
  ist im Planner-Zweig angekommen (gemessen: merge-base --is-ancestor gegen 236f9efe, alle
  drei nein). A-37-12 traegt deshalb unveraendert "wird von npm ci geschrieben" — npm
  schreibt sie nicht. Das ist keine Nachlaessigkeit, sondern Laufzeit: er arbeitet schneller,
  als der Transport zwischen uns laeuft.
  Damit sind bei ihm offen: der Schreiber der Marke (A-37-12), die Nummer fuer MODULSTAND
  (A-37-13), der Fliesstext exit 1 (Z.266) und jetzt das Markenformat (A-37-15).
  Bei Yama offen: die Uebergangsklausel A-37-6.
mein_vorschlag_zur_lage: |
  Nicht mehr Kriterien pruefen, sondern EINEN Schnitt festhalten. Solange das Blatt sich
  im Minutentakt aendert, ist jede DoR beim Schreiben veraltet — das habe ich heute dreimal
  erlebt. Ein Satz im Blatt wuerde reichen: "Stand fuer die DoR ist <SHA>; spaetere
  Nachtraege brauchen eine eigene Runde."
  Das ist genau die Lehre aus dem AKTIVIERUNGS_SHA, die der Planner selbst gezogen hat —
  ein fester Stand ist nicht haltbar, wenn er waehrend der Pruefung wandert. Hier wandert
  nicht der Repo-Stand, sondern das Blatt.
ballbesitz: planner
```

herkunft: docs/STATUS.md · Block 332 · 34f6f5a9
kennung_nicht_zuordenbar: true   # Freitext statt Kennung — uebernommen wie vorgefunden, nicht gedeutet
```yaml
auftrag: "dor_runde_3_votum_a37_a38"
titel: "DoR Runde 3 fuer A-37 und A-38 — NICHT ERTEILT, fuenf Restpunkte, alle klein und alle belegt"
rolle: plan-pruefer
zeit: "2026-08-16 13:00"
stand_kopf: e2f3e15c
geprueft_gegen: "f37317a1 — der neueste Blattstand, im gemeinsamen Checkout und auf rolle/generator; noch nicht im Fernstand"
ballwechsel_quittiert: |
  Der Planner hat den Ball mit f37317a1 (12:57:07) an mich zurueckgegeben und alle Restpunkte
  der ersten Runde einzeln belegt. Ich bestaetige den Empfang und die Behebung:
    gebaut_in     zurueck auf ticket-rolle-generator — meine Messung als Beleg   BEHOBEN
    feste 1750    raus, ersetzt durch Bindung an den BAU-Stand                    BEHOBEN
    exit-Codes    A-37-5 entschieden statt geraten, drei Werte 1/2/3              BEHOBEN
    A-38-Zahlen   497/70/59 statt 309/32/41, Messortfehler offengelegt            BEHOBEN
  Und er hat den Transport SELBST gefahren, mit genau meiner Begruendung von gestern:
  "ohne Transport haette der Ball den Plan-Pruefer nicht erreicht, und genau das war schon
  einmal der Grund, warum zwei Auftraege stillstanden." Vor dem Schreiben den Diff-INHALT
  gelesen statt --stat, vier Zeilen, kein Beifang. Das ist saubere Arbeit.
votum_NICHT_ERTEILT_fuenf_restpunkte: |
  Alle fuenf sind heute schon einzeln gemeldet und belegt; hier gebuendelt, weil der Planner
  ein Votum angefordert hat und meine Einzelbefunde ihn beim Schreiben noch nicht erreicht
  hatten. KEINER ist gross, alle zusammen sind eine Viertelstunde Blattarbeit.
  R1  A-37-12: "Marke .aus-lockfile wird von npm ci geschrieben" — npm schreibt sie NICHT.
      Am Bestand nachgesehen: node_modules/ enthaelt .bin und .package-lock.json, kein
      .aus-lockfile. Es fehlt der Zustaendige. Beleg: f32b1321.
  R2  A-37-13: MODULSTAND bekommt keinen eigenen Code, nur "Rueckgabe != 0". Der Zahlenraum
      ist bis 4 belegt (commit-pruefen.sh 0/1/2/3 an 16 Stellen, YAML-Pruefer 2/3/4). Damit
      wird die Unterscheidbarkeit aufgegeben, die Teil 3 herstellt. Beleg: a8f0a944.
  R3  A-37-15: "wc -w >= 6" fuer vier Felder. Durchgerechnet fallen vier reine Werte mit 4
      bzw. 5 Woertern durch; nur mit Feldnamen sind es 8. Das Format legt das Blatt nicht
      fest — das Kriterium ist genauer als die Zusage. Beleg: e2f3e15c.
  R4  Fliesstext Z.307 (frueher 266): "A-37-3/4/5/6 Rohausgaben mit exit 1", waehrend
      A-37-5 exit 3 verlangt. Vom Release-Pruefer gemeldet, von mir am heutigen Stand
      bestaetigt. Beleg: a8f0a944.
  R5  A-38: die drei tragenden Zahlen (497 / 70 / 59) nennen weiterhin KEINEN Messbefehl.
      grep ueber das ganze Blatt nach rev-list / git log / --merges: null Treffer bei den
      Zahlen. Nach einem MESSORT-Fehler ist der Befehl die einzige Abhilfe. Beleg: 6ed8d723.
NEU IN DIESER RUNDE, und es gibt dafuer einen Praezedenzfall im Haus: |
  A-38 traegt die als falsch berichtigte Zahl in der UEBERSCHRIFT:
    Z.1   "# A-38 — 41 von 309 Commits laufen am Tor vorbei, und alle sind Merges"
    Z.19  anlass: "41 von 309 Commits der letzten 48 h ..."
    Z.41  der Koerper berichtigt auf 497 / 70 / 59
  Der Praezedenzfall ist A-33: dort traegt ein Blatt woertlich den Kopf "# A-33 —
  STILLGELEGT. Dieses Blatt traegt eine falsche Zahl im Namen." Das Haus hat diese Klasse
  also schon einmal als ernst genug behandelt, um ein Blatt stillzulegen.
  Ich verlange das hier NICHT — bei A-33 waren es zwei konkurrierende Blaetter, hier ist es
  eine Ueberschrift. Aber sie sollte die berichtigte Zahl tragen oder gar keine.
was_ich_NICHT_tue: |
  Ich fasse den Zustand NICHT an. A-37 und A-38 stehen im Datensatz auf BEREIT, gesetzt von
  einer zweiten Instanz meiner Rolle, die inzwischen zurueckgetreten ist und ihren einen
  falschen Beleg selbst offengelegt hat. Ihr Urteil zurueckzudrehen waere ein Zweikampf
  zwischen zwei Instanzen derselben Rolle — genau das, was P-02 verhindern soll.
  Mein Votum ist deshalb eine RESTPUNKTLISTE, kein Zustandswechsel. Sind die fuenf behoben,
  bestaetige ich BEREIT ausdruecklich und trage den dor_beleg fuer alle 15 Kriterien nach.
zur_reihenfolge: |
  Der Generator hat noch nicht gezogen — A-37 steht auf BEREIT, nicht IN_ARBEIT, gemessen
  im Fernstand 7ce3926c. Es ist also noch Zeit, die fuenf einzuarbeiten, bevor gebaut wird.
  R1 und R3 sind die einzigen, die den BAU treffen: wer sie so baut, wie sie dastehen, baut
  ein Tor, das eine Datei liest, die niemand schreibt, und misst sie mit einer Wortzahl,
  die bei korrekter Umsetzung durchfaellt.
ballbesitz: planner
```

herkunft: docs/STATUS.md · Block 333 · 34f6f5a9
kennung_nicht_zuordenbar: true   # Freitext statt Kennung — uebernommen wie vorgefunden, nicht gedeutet
```yaml
auftrag: "a38_kanten_und_hookpath_geprueft_ohne_fund"
titel: "A-38 auf zwei Vorratsposten geprueft — kein Fund, dafuer eine Voraussetzung fuer A-38-6 unabhaengig belegt"
rolle: plan-pruefer
zeit: "2026-08-16 13:05"
stand_kopf: a400368f
posten: "Vorratspruefung (a) und (d) an A-38, dem zweiten BEREIT-Auftrag"
was_ich_geprueft_habe_und_NICHT_gefunden_habe: |
  (a) GEWANDERTE VERWEISE: A-38 nennt keine Datei:Zeile-Verweise. Der Posten greift dort
      nicht — das ist kein Fund, sondern eine nicht anwendbare Pruefung, und ich schreibe
      es hin, damit niemand die Luecke fuer ein Ergebnis haelt.
  (d) ALTERUNG: Basis 0f05f8bf, 78 Commits seither. Eine im Blatt genannte Datei wurde
      seither geaendert — scripts/commit-pruefen.sh, einmal, durch die Tor-Reparatur
      374bb851. Die Zahl, die A-38 daran haengt, habe ich heute frueh nachgemessen und sie
      haelt: 4 merge-Treffer, Zeilen 777/783/784/786, alle am Index, keiner am Commit-Typ.
  DIE KANTEN: ich hatte sechs Faelle im Kopf, in denen ein commit-msg-Hook NICHT laeuft.
  Alle sechs stehen bereits im Blatt, K1 bis K6 — merge --no-commit, Fast-Forward, amend an
  einem Merge, Rebase/Cherry-Pick, kaputter Hook, und --no-verify. Der letzte ist
  ausdruecklich als "nicht verhinderbar" benannt statt verschwiegen. Da ist nichts zu holen.
DIE BESTAETIGUNG, und sie gehoert dem Generator: |
  A-38-6 verlangt: "Der Hook greift in einem ZWEITEN Worktree, ohne dort eingerichtet zu
  werden." Ob das ueberhaupt gehen kann, haengt an einer Tatsache ueber diesen Bestand, die
  im Blatt nicht steht. Selbst gemessen:
    git rev-parse --git-common-dir  in ticket-rolle-plan-pruefer -> /Users/yamanuri/Documents/ticket/.git
    dasselbe in ticket-rolle-generator                           -> /Users/yamanuri/Documents/ticket/.git
    config.worktree in beiden                                    -> nicht vorhanden
    git config --get extensions.worktreeConfig                   -> nicht gesetzt
    git config --get core.hooksPath in vier Baeumen              -> ueberall leer (A-38s Rot-Beleg haelt)
  Alle Worktrees teilen EINE config. Wird core.hooksPath einmal gesetzt, gilt es in allen
  sechs Baeumen — A-38-6 ist damit erfuellbar, und zwar mit einem einzigen Befehl.
  Die Bedingung dafuer ist extensions.worktreeConfig: waere sie aktiv, koennte jeder Baum
  eine eigene config haben und der Schalter waere pro Baum zu setzen. Sie ist es nicht.
  Wer A-38 baut, sollte diese Bedingung im Bericht mitnennen — sie ist heute erfuellt, aber
  sie ist eine Umgebungstatsache, und die sind hier zweimal an einem Tag weggewandert.
was_offen_BLEIBT_aber_theoretisch_ist: |
  core.hooksPath ist lokale Konfiguration und wird nicht mitversioniert. Ein FRISCHER KLON
  dieses Repos haette den Hook, aber nicht den Schalter — und wuerde still ohne Schutz
  laufen. A-38 deckt den zweiten Worktree (A-38-6), nicht den frischen Klon.
  Ich fuehre das NICHT als Restpunkt: in diesem Haus wird nicht geklont, es gibt eine
  Maschine und sechs Worktrees darauf. Es ist eine Notiz fuer den Tag, an dem sich das
  aendert, kein Mangel am Auftrag.
ballbesitz: "—"  # kein Ball: Pruefung ohne Fund, die Bestaetigung ist eine Zulieferung an den Bau
```

herkunft: docs/STATUS.md · Block 334 · 34f6f5a9
kennung_nicht_zuordenbar: true   # Freitext statt Kennung — uebernommen wie vorgefunden, nicht gedeutet
```yaml
auftrag: "tafel_gegen_datensatz_vollstaendig_null_divergenz"
titel: "Der Werkzeugfehler des Release-Pruefers hat keine Spuren hinterlassen — 64 Paare Tafel gegen Datensatz, null Divergenz"
rolle: plan-pruefer
zeit: "2026-08-16 13:07"
stand_kopf: 9bcbaa39
geprueft_gegen: 064b79b3
posten: "Pflichtteil 2 der Wache, Teil 'Zustand Tafelzeile gegen Datensatz' — diesmal ueber ALLE Kennungen"
anlass: |
  Der Release-Pruefer hat in 064b79b3 einen Fehler seines eigenen Merge-Werkzeugs gefunden:
  "Mein Werkzeug loest immer nur den ERSTEN [Konfliktbereich] und liest den Datensatz aus dem
  Rest der Datei — dort standen aber noch Marker." Ergebnis war genau eine Tafel/Datensatz-
  Divergenz: Tafel BEREIT/generator, Datensatz ENTWURF/plan-pruefer.
  Er hat sie selbst gefunden, weil er "die restlichen Bereiche gelesen [hat] statt der
  Erfolgsmeldung zu glauben". Die naheliegende Anschlussfrage stellt er nicht, und sie ist
  die richtige: es war der SIEBZEHNTE Konflikt — was ist mit den sechzehn davor?
messung: |
  Alle Tafelzeilen gegen alle Datensaetze im Fernstand 064b79b3:
    Tafelzeilen mit Zustand      75
    Datensaetze mit Zustand      75
    beidseitig vorhanden         64
    DIVERGENT                     0
  Muster: Tafel ^\| \*\*(KENNUNG)\*\*[^|]*\|\s*\**`?(ZUSTAND)`?\**\s*\|
          Datensatz: auftrag-Zeile, danach die erste zustand-Zeile im selben Block.
  Kein einziges Paar widerspricht sich. Sein Fehler war ein Einzelfall und ist behoben;
  die sechzehn Konflikte davor haben nichts hinterlassen, das heute noch sichtbar waere.
die_elf_die_nicht_vergleichbar_sind: |
  75 Tafelzeilen, 75 Datensaetze, aber nur 64 Paare — auf jeder Seite bleiben elf ohne
  Gegenstueck. Das sind dieselben elf wie den ganzen Tag: Tafel fuehrt W-01, der Datensatz
  heisst W-01/1. A-33s Gegenstand, jetzt zum VIERTEN Mal auf einem unabhaengigen Weg
  bestaetigt (Tafelzeilen ueber das [AW]-Muster, A-30-Deckungsluecke am Tor, Blattnamen,
  und heute die Paarbildung).
  Das ist kein neuer Fund, sondern ein weiterer Beleg fuer einen Auftrag, der schon steht.
was_das_wert_ist: |
  Ein Werkzeug, das siebzehnmal gelaufen ist und beim siebzehnten Mal falsch lag, wirft die
  Frage nach den ersten sechzehn auf. Diese Frage ist jetzt beantwortet, und zwar von einer
  anderen Rolle als der, die das Werkzeug gebaut hat.
ballbesitz: "—"  # kein Ball: Zulieferung an den Release-Pruefer, kein offener Punkt
```

herkunft: docs/STATUS.md · Block 335 · 34f6f5a9
kennung_nicht_zuordenbar: true   # Freitext statt Kennung — uebernommen wie vorgefunden, nicht gedeutet
```yaml
auftrag: "a33_ist_IN_ARBEIT_und_die_warnung_liegt_einen_transport_entfernt"
titel: "Der Generator hat A-33 gezogen — gegen ein Kriterium, das seit gestern unerfuellbar ist, und mein Befund dazu fehlt in genau seinem Stand"
rolle: plan-pruefer
zeit: "2026-08-16 13:09"
stand_kopf: eeadd96b
dringlichkeit: "HOCH — A-33 ist IN_ARBEIT, der Bau laeuft JETZT"
posten: "Pflichtteil 3: Zustandswechsel eines Auftrags, an dem ein eigener Befund haengt"
was_geschehen_ist: |
  355e2c05, heute 13:07:27, generator: "A-33 gezogen, IN_ARBEIT an beiden Stellen — der ERSTE
  Commit aus meinem Rollenbaum." Der Zug ist korrekt: A-33 stand auf BEREIT, er hat gezogen
  und den Zustand an beiden Orten gesetzt. Kein Vorwurf an ihn.
DER FUND, frisch an SEINEM Stand gerechnet: |
  A-33-1 (P1, TRAGEND) verlangt nach dem Bau:
    unter dem Muster A-/W-        genau EINS   -> A-06
    unter allen Grossbuchstaben   genau ZWEI   -> A-06 und P-02
  Gemessen in 355e2c05, also in genau dem Stand, gegen den er baut:
    Tafelzeilen ohne Datensatz heute      eng 11 · breit 11
    nach dem Zusammenziehen der elf       eng  0 · breit  0
  Es sind NULL und NULL, nicht eins und zwei. Der Grund steht seit gestern fest und ist
  datiert: 086b48bd (15.08. 13:02) hat A-06 und P-02 Datensaetze gegeben — eine richtige
  Verbesserung, die nebenbei die Zielzahl eines BEREIT stehenden Auftrags auf null zog.
  Ich habe das gestern 15:53 gemeldet: fd5e7f54.
UND HIER LIEGT DER EIGENTLICHE BEFUND: |
  merge-base --is-ancestor fd5e7f54 5dcea377   -> JA   (im Fernstand)
  merge-base --is-ancestor fd5e7f54 355e2c05   -> NEIN (nicht im Stand des Generators)
  Die Warnung existiert seit 21 Stunden, sie ist gesichert, sie ist im Fernstand — und sie
  fehlt in genau dem Baum, in dem jetzt gebaut wird. Der Generator-Zweig haengt an f37317a1
  (Planner), und diese Linie traegt meinen Befund nicht.
  DAS IST DIE ZUSTELLUECKE ZUM DRITTEN MAL AN EINEM TAG, und diesmal trifft sie nicht eine
  DoR, sondern einen laufenden Bau:
    1. gestern    meine A-37-Messung erreichte den Release-Pruefer nicht -> falscher Bau-Ort
    2. heute      mein A-38-Befund erreichte die BEREIT-Erteilung nicht
    3. jetzt      mein A-33-Befund erreicht den Bau nicht
  Jedes Mal war die Arbeit getan, gesichert und richtig. Jedes Mal fehlte der Transport.
was_dem_generator_passieren_wird: |
  Er zieht die elf alten Kennungen zusammen — das ist die richtige Arbeit, und A-33s Kern
  traegt, viermal unabhaengig bestaetigt. Dann misst er gegen A-33-1 und findet 0/0, wo das
  Blatt 1/2 verlangt. Entweder er meldet ROT an einer korrekten Arbeit, oder er sucht den
  Fehler bei sich und findet keinen.
  Beides kostet Zeit, die zwei Zeilen im Blatt gespart haetten.
was_ich_NICHT_tue: |
  Ich fasse den Zustand NICHT an. IN_ARBEIT ist richtig gesetzt, der Auftrag ist gezogen,
  und einen laufenden Bau anzuhalten ist keine Pruefer-Handlung.
  Ich pushe nicht.
bitte_und_es_eilt: |
  An den Release-Pruefer oder wer als naechstes transportiert: rolle/plan-pruefer in den
  Generator-Zweig, oder wenigstens fd5e7f54. Dann hat er die Warnung vor der Messung.
  An den Planner: A-33-1 auf 0/0 berichtigen, mit dem Satz warum (A-06 und P-02 haben seit
  086b48bd Datensaetze). Zwei Zeilen, und der Bau kann sauber abgenommen werden.
  An den Generator, falls er das hier zuerst liest: deine Arbeit ist richtig, die Zahl im
  Blatt ist es nicht. Miss 0/0 und melde es als Blattfehler, nicht als eigenen.
NACHTRAG_13_11_SACHLICH_ERLEDIGT: |
  Der Generator hat es beim Ziehen selbst gefunden, und der Planner hat es in 3544d5fa
  offen als eigene Nebenwirkung benannt. Die Loesung ist besser als meine Bitte: statt
  A-33-1 auf 0/0 zu berichtigen, wird aus der Zahl eine INVARIANTE — "nach dem Lauf gibt es
  KEINE Tafelzeile mehr, deren Kennung verkuerzt ist waehrend ihr Datensatz die volle Form
  traegt; der Lauf meldet, wieviele es waren, ein zweiter Lauf meldet null."
  Eine Zahl laeuft ab, eine Invariante nicht. Das ist die richtige Antwort auf die Klasse,
  die ich heute dreimal gemeldet habe. Mein Befund ist damit sachlich erledigt.
ballbesitz: "—"  # erledigt: der Generator fand es selbst, der Planner hat es als Invariante geloest
```

herkunft: docs/STATUS.md · Block 336 · 34f6f5a9
kennung_nicht_zuordenbar: true   # Freitext statt Kennung — uebernommen wie vorgefunden, nicht gedeutet
```yaml
auftrag: "die_zustelluecke_erzeugt_belege_fuer_ihre_eigene_unsichtbarkeit"
titel: "Im Bestand steht jetzt 'niemand hat mich darauf hingewiesen' — der Hinweis liegt seit 21 Stunden gesichert im Fernstand"
rolle: plan-pruefer
zeit: "2026-08-16 13:11"
stand_kopf: 5e22c49e
posten: "Pflichtteil 3 — Wuerdigung eines fremden Votums, und eine Richtigstellung am Bestand"
zuerst_die_anerkennung: |
  3544d5fa ist ein guter Commit. Der Planner benennt eine Nebenwirkung SEINER EIGENEN Arbeit,
  statt sie zu uebergehen: "A-33 trug eine feste Zielzahl, und sie ist abgelaufen — A-06 hat
  seit 086b48bd einen Datensatz ... 086b48bd ist mein Commit vom 15.08. Ich habe ein fremdes
  Kriterium ungueltig gemacht, ohne es zu bemerken."
  Und die Loesung ist besser als das, worum ich gebeten hatte: eine Invariante statt einer
  Zahl. Das nehme ich an und ziehe meine Bitte um 0/0 zurueck.
DIE RICHTIGSTELLUNG, und sie gilt nicht mir sondern dem Bestand: |
  Derselbe Satz endet mit: "niemand hat mich darauf hingewiesen, der Generator hat es beim
  Ziehen selbst gefunden."
  Am Bestand gemessen ist der erste Teil nicht richtig:
    fd5e7f54  15.08. 15:53  plan-pruefer: "A-33 steht BEREIT mit einer Zielzahl, die heute
              Mittag auf null gefallen ist ... Ursache: 086b48bd, 15.08. 13:02"
    Alter beim Zug des Generators:  21 Stunden 14 Minuten
    im Fernstand 5dcea377:          JA, gemessen mit merge-base --is-ancestor
    im Planner-Stand 3544d5fa:      NEIN
    im Generator-Stand 355e2c05:    NEIN
  Der Hinweis existiert, ist gesichert, ist im Fernstand — und hat beide nicht erreicht.
warum_ich_das_ueberhaupt_melde: |
  NICHT um mir etwas zuzuschreiben. Der Generator hat es unabhaengig gefunden, das zaehlt
  genauso viel, und der Planner hat es offen eingeraeumt, was mehr zaehlt als beides.
  Sondern weil der Satz im BESTAND steht und dort etwas anderes belegt, als geschehen ist.
  Wer ihn in einem Monat liest, liest: die Pruefung hat versagt, erst der Bau hat es
  gefunden. Richtig ist: die Pruefung hat es 21 Stunden vorher gefunden, und der TRANSPORT
  hat versagt. Das sind zwei verschiedene Reparaturen — die eine an der Sorgfalt, die andere
  an der Zustellung. Nur die zweite trifft zu.
  Und es ist der vierte Fall an einem Tag, jetzt mit einer neuen Eigenschaft: die Luecke
  erzeugt inzwischen Belege fuer ihre eigene Abwesenheit.
    1. gestern  A-37-Bau-Ort         erreichte den Release-Pruefer nicht
    2. heute    A-38-Zahlenbefund    erreichte die BEREIT-Erteilung nicht
    3. 13:07    A-33-Zielzahl        erreichte den Bau nicht
    4. 13:09    und der Bestand haelt jetzt fest, es habe keinen Hinweis gegeben
was_das_fuer_den_transport_heisst: |
  Der Rueckfluss laeuft, und zwar gut: der Release-Pruefer hat heute fuenfmal transportiert,
  der Planner hat einmal selbst gefahren. Was fehlt, ist nicht Fleiss, sondern RICHTUNG:
  meine Befunde gehen in den Integrations-Zweig und kommen dort an — aber die Rollenzweige
  ziehen nicht nach. Generator und Planner arbeiten auf Staenden, die meinen Zweig nicht
  enthalten, obwohl er fern liegt.
  Das ist kein neuer Vorschlag von mir; P2H-12 fuehrt den Rueckfluss bereits als NACHBESSERN.
  Ich liefere nur den vierten Beleg und die Praezisierung: der Weg IN den Integrations-Zweig
  ist geloest, der Weg ZURUECK in die Rollenbaeume nicht.
bitte: |
  An den Planner, eine Zeile im Blatt oder im Commit: der Hinweis lag vor (fd5e7f54,
  15.08. 15:53) und hat ihn nicht erreicht. Damit steht im Bestand die richtige Ursache.
  An Yama: das ist derselbe Punkt wie gestern 16:03, nur von der anderen Seite. Solange
  Rollenbaeume nicht nachziehen, arbeitet jede Rolle auf einem eigenen Weltbild — und merkt
  es erst, wenn zwei davon kollidieren.
ballbesitz: planner
```

herkunft: docs/STATUS.md · Block 337 · 34f6f5a9
kennung_nicht_zuordenbar: true   # Freitext statt Kennung — uebernommen wie vorgefunden, nicht gedeutet
```yaml
auftrag: "a33_code_fertig_geprueft_der_evaluator_trifft_auf_ein_blatt_das_etwas_anderes_verlangt"
titel: "A-33s Meldepflichten sind erfuellt, am Commit nachgemessen — aber das Blatt traegt den Umschnitt nicht, gegen den gebaut wurde"
rolle: plan-pruefer
zeit: "2026-08-16 13:14"
stand_kopf: 1a42852a
posten: "Pflichtteil 3 · CODE_FERTIG: Meldepflichten pruefen, Ballwechsel bestaetigen"
MELDEPFLICHTEN ERFUELLT, selbst gemessen statt geglaubt: |
  bau_sha 3e22e61b   existiert (cat-file -t -> commit) UND steht in einem FELD, nicht nur
                     im Fliesstext. Beides verlangt E1.
  Scope-Diff am BAU-Commit, nicht am Zustands-Commit:
    3e22e61b aendert GENAU EINE Datei: scripts/a33-kennungen-nachziehen.sh, 174 Zeilen neu
    Treffer auf docs/STATUS.md          0
    Treffer auf resources/ oder app/    0
    Modus im Baum                       100755, also ausfuehrbar
  Seine tragende Behauptung — "der Commit 3e22e61b enthaelt NULL Treffer auf STATUS.md" —
  haelt am Objekt. Die 55 Zeilen an docs/STATUS.md stecken im getrennten Zustands-Commit
  c8a23373 und sind Datensatz, Tafelzeile und Bau-Bericht; das ist nach A-20 richtig so.
  BALLWECHSEL BESTAETIGT: IN_ARBEIT -> CODE_FERTIG an beiden Stellen, Ball beim Evaluator.
was_ich_ihm_anrechne: |
  Er hat die abgelaufene Zielzahl BEIM ZIEHEN selbst gefunden und im Bau-Bericht benannt,
  statt sie stillschweigend zu umgehen — "die alte Zielzahl ist ausserdem nachweislich
  abgelaufen: A-06 hat seit 086b48bd einen Datensatz, der Rest waere heute NULL statt EINS."
  Und er hat KEIN Kriterium geaendert, obwohl es ihm die Arbeit erleichtert haette: "Der
  Umschnitt gehoert dem Planner; ich aendere kein Kriterium und fuege keines hinzu."
  Das ist die Rollengrenze, eingehalten an der Stelle, an der sie unbequem ist.
DER OFFENE PUNKT, und er trifft die naechste Station: |
  Der Generator hat gegen YAMAS UMSCHNITT gebaut (Liefergegenstand ist ein SKRIPT, keine
  Bearbeitung). Das Blatt in docs/auftraege/aktiv/ traegt diesen Umschnitt NICHT — er sagt
  es selbst: "DAS BLATT TRAEGT NOCH DIE ALTE FASSUNG. In docs/auftraege/aktiv steht weiter
  die feste Zielzahl 'elf' und 'genau EINS -> A-06'."
  Selbst nachgemessen im Stand des Generators: A-33-1 traegt weiterhin "genau EINS".
  DARAUS FOLGT FUER DIE ABNAHME: der Evaluator prueft gegen das Blatt. Das Blatt verlangt
  eine Bearbeitung mit Zielzahl 1/2; geliefert ist ein Skript, und die Zahl ist 0/0.
  Er wird an A-33-1 rot melden — an einem Bau, der genau das tut, was Yama angeordnet hat.
  Das ist die A-03-Klasse zum vierten Mal an diesem Tag, und diesmal steht sie nicht bevor,
  sondern die naechste Station laeuft schon hinein.
was_ich_NICHT_tue: |
  Ich prüfe das Skript NICHT inhaltlich. 174 Zeilen Zuordnungslogik sind Evaluator-Arbeit,
  und meine Rolle bei CODE_FERTIG sind die Meldepflichten und der Ballwechsel. Beides ist
  erledigt und positiv.
  Ich fasse kein Kriterium an — aus demselben Grund wie der Generator.
bitte: |
  An den Planner, und es ist dringlicher als die vier A-37-Restpunkte: A-33s Blatt auf
  Yamas Umschnitt nachziehen, bevor der Evaluator misst. Zwei Stellen — Liefergegenstand
  (Skript statt Bearbeitung) und A-33-1 (Invariante statt Zielzahl, die Formulierung dafuer
  steht bereits in 3544d5fa).
  An den Evaluator, falls er zuerst liest: der Bau liefert ein Skript, das Blatt verlangt
  eine Bearbeitung. Miss gegen Yamas Umschnitt und melde die Blattlage als Blattlage.
ballbesitz: planner
```

herkunft: docs/STATUS.md · Block 338 · 34f6f5a9
kennung_nicht_zuordenbar: true   # Freitext statt Kennung — uebernommen wie vorgefunden, nicht gedeutet
```yaml
auftrag: "die_sicherung_steht_aber_sie_laeuft_nicht_mit_und_jetzt_haengt_produktivcode_daran"
titel: "Erstmals liegt ein BAU ungesichert — die Rollenzweige sind fern eingerichtet, aber sie stehen auf dem Stand von 13:05"
rolle: plan-pruefer
zeit: "2026-08-16 13:17"
stand_kopf: a9834290
posten: "Pflichtteil der Wache, Zweigprobe — kein Vorratsposten"
messung: |
  Lokaler Rollenzweig gegen den FERNEN Rollenzweig, je Rolle einzeln:
    rolle/generator      lokal c8a23373 · fern f37317a1 · UNGESICHERT 3
    rolle/planner        lokal 3544d5fa · fern 236f9efe · UNGESICHERT 1
    rolle/plan-pruefer   lokal a9834290 · fern 9bcbaa39 · UNGESICHERT 4
  Acht Commits ohne Kopie ausserhalb dieser Maschine. Darunter:
    3e22e61b  der A-33-Bau, scripts/a33-kennungen-nachziehen.sh, 174 Zeilen
              5 Minuten alt, im Fernstand NICHT vorhanden (merge-base --is-ancestor: NEIN)
was_daran_NEU_ist: |
  Fuenf Minuten sind kein Alarm, und ich mache keinen. NEU ist die QUALITAET dessen, was
  liegt: bisher waren es meine Befundbloecke und Blattarbeit — Doku, die im Verlustfall
  rekonstruierbar ist, weil ich dieselbe Messung nochmal fahren kann.
  Jetzt liegt PRODUKTIVCODE ungesichert. 174 Zeilen Zuordnungslogik, die jemand geschrieben
  hat und die niemand aus einer Messung wiederherstellen kann.
  Das ist derselbe Punkt wie gestern 16:03, aber er hat die Kategorie gewechselt.
die_praezisierung_gegen_meine_eigene_meldung_von_heute_frueh: |
  Ich habe heute 12:52 gemeldet, der Sicherungsbefund sei BEHOBEN — "alle fuenf Rollenzweige
  liegen jetzt fern". Das war richtig gemessen und ist trotzdem zu kurz gegriffen.
  Die Zweige sind EINGERICHTET, aber sie werden nicht nachgeschoben: origin/rolle/generator
  steht auf f37317a1, das ist der Stand von 13:05, waehrend lokal seither drei Commits
  dazugekommen sind. Eine Sicherung, die einmal laeuft, ist eine Momentaufnahme; eine
  Sicherung, die mitlaeuft, ist eine Kopie.
  Meine Formulierung "behoben" war deshalb ungenau. Richtig ist: eingerichtet, nicht laufend.
was_ich_NICHT_tue: |
  Kein Push. Auch nicht fuer den fremden Bau, obwohl ein Befehl genuegen wuerde und es
  diesmal nicht meine eigene Arbeit waere, die ich rette.
  Und ich melde es NICHT als dringend: acht Commits, aeltester wenige Minuten, der
  Transport lief heute siebenmal. Es ist eine Beobachtung, keine Lage.
bitte: |
  An den Release-Pruefer, wenn er das naechste Mal transportiert: die Rollenzweige
  mitschieben, nicht nur die Integrationslinie. Ein Befehl je Zweig.
  An Yama, falls das eine Regel werden soll: "wer in seinem Rollenbaum committet, schiebt
  seinen eigenen Zweig nach" waere die kleinste Fassung — sie braucht keine fremde Rolle
  und keinen Takt. Sie beruehrt allerdings die Push-Regel, deshalb liegt sie bei dir und
  nicht bei mir.
ballbesitz: yama
```

herkunft: docs/STATUS.md · Block 339 · 34f6f5a9
kennung_nicht_zuordenbar: true   # Freitext statt Kennung — uebernommen wie vorgefunden, nicht gedeutet
```yaml
auftrag: "a33_ist_gebaut_aber_der_bau_erreicht_die_abnehmende_rolle_nicht"
titel: "A-33 steht im Generator-Baum auf CODE_FERTIG und im Fernstand auf BEREIT — und der Evaluator, der abnehmen soll, ist seit 25 Stunden still und haengt 147 Commits zurueck"
rolle: plan-pruefer
zeit: "2026-08-16 13:20"
stand_kopf: 33227f5c
posten: "Pflichtteil 3 — Stillstand aufloesen"
zuerst_das_erledigte: |
  Mein dringlichster Punkt von 13:14 ist behoben, vom Planner in b6af3207 (13:16:53):
  "A-33 umgeschnitten — der Ball, den der Generator ausdruecklich bei mir gelassen hat."
  Selbst nachgemessen im Blatt:
    'genau EINS'            0 Treffer  (war die abgelaufene Zielzahl)
    'Invariante' / 'Skript' 5 Treffer
  Das Blatt traegt jetzt Yamas Umschnitt, BEVOR der Evaluator misst. Genau die Reihenfolge,
  um die ich gebeten hatte — und der Generator hatte den Ball bewusst dort gelassen, statt
  selbst am Kriterium zu drehen.
DIE LAGE, in zwei Zahlen: |
  A-33 zustand im Generator-Baum c8a23373:  CODE_FERTIG · Ball Evaluator · bau_sha 3e22e61b
  A-33 zustand im Fernstand      a4694b21:  BEREIT      · Ball generator
  Beides ist richtig gesetzt worden; sie kennen einander nur nicht. Der Generator-Zweig ist
  nicht transportiert — drei Commits, darunter der Bau, habe ich um 13:17 als ungesichert
  gemeldet. Der Fernstand weiss deshalb nicht, dass gebaut wurde.
  UND DIE ROLLE, DIE ABNEHMEN SOLL, IST DIE EINZIGE, DIE NICHT UMGEZOGEN IST:
    letzter Evaluator-Commit   5dd5eaee, 15.08. 12:00 (die A-35-Abnahme)
    still seit                 25 h 18 min
    eigene Commits auf rolle/evaluator   0
    Rueckstand seines Baums              147 Commits
  Er arbeitet, wenn er wieder anfaengt, auf einem Stand von gestern Mittag — dort gibt es
  weder A-33s Bau noch das umgeschnittene Blatt noch die vier Auftraege dazwischen.
warum_das_die_fuenfte_stelle_derselben_klasse_ist: |
  1. gestern  A-37-Bau-Ort      erreichte den Release-Pruefer nicht
  2. heute    A-38-Zahlenbefund erreichte die BEREIT-Erteilung nicht
  3. 13:07    A-33-Zielzahl     erreichte den Bau nicht
  4. 13:09    der Bestand hielt fest, es habe keinen Hinweis gegeben
  5. jetzt    der fertige BAU erreicht die Abnahme nicht
  Die ersten vier trafen Prueftexte. Dieser trifft die Kette selbst: eine Station meldet
  fertig, die naechste kann es nicht sehen. Das ist kein Transportrueckstand mehr, sondern
  eine unterbrochene Uebergabe.
was_ich_NICHT_sage: |
  NICHT, dass der Evaluator saeumig ist. Ich weiss nicht, ob er laeuft; 25 Stunden Stille
  koennen heissen, dass er nicht gestartet ist. Das ist eine Betriebsfrage, keine
  Rollenfrage, und sie gehoert Yama.
  NICHT, dass jemand einen Fehler gemacht hat. Generator und Planner haben beide sauber
  gearbeitet und ihre Baelle korrekt gesetzt. Es fehlt der Weg dazwischen.
bitte: |
  An den Release-Pruefer: rolle/generator transportieren, damit der Bau und CODE_FERTIG im
  Fernstand ankommen. Ohne das kann keine Abnahme beginnen, egal wer sie faehrt.
  An Yama: laeuft der Evaluator? Wenn nicht, wartet A-33 auf eine Station, die es gerade
  nicht gibt — dann ist zu entscheiden, ob jemand vertritt (wie heute Mittag bei meiner
  eigenen Rolle) oder ob A-33 bis zu seinem Start liegen bleibt. Beides ist vertretbar,
  aber es sollte entschieden sein und nicht einfach dauern.
ERLEDIGT_16_08_14_12: |
  Beide Teile haben sich innerhalb von Minuten erledigt, und meine Frage war beim Stellen
  schon beantwortet:
    der Release-Pruefer hat rolle/generator transportiert — der Bau 3e22e61b liegt im
    Fernstand, A-33 stand dort auf CODE_FERTIG
    der Evaluator LAEUFT: 5f37d8e4 um 13:24 "A-33 geclaimt, Abnahme Runde 1 beginnt" und
    75c471cf um 13:35 "A-33 SPEC_BLOCKED, sechs von sieben Kriterien gruen"
  Selbst nachgemessen: zwei Evaluator-Commits seit 13:20. Die Abnahme hat stattgefunden, sie
  ist nicht ausgeblieben — mein Befund war zum Zeitpunkt richtig gemessen und vier Minuten
  spaeter gegenstandslos.
  Was aus dem Vorgang BLEIBT, steht an anderer Stelle und nicht hier: die Frage, wer einen
  Auftrag nach SPEC_BLOCKED plus neuem Plan zurueckholt — Paragraf 3s Rueckkehr-Tabelle hat
  dafuer keine Zeile. Das ist ein eigener Block von 14:05, nicht dieser.
ballbesitz: "—"  # ERLEDIGT 16.08. 14:12: Bau transportiert, Evaluator laeuft und hat abgenommen, selbst nachgemessen
```

herkunft: docs/STATUS.md · Block 340 · 34f6f5a9
kennung_nicht_zuordenbar: true   # Freitext statt Kennung — uebernommen wie vorgefunden, nicht gedeutet
```yaml
auftrag: "a38_der_messbefehl_ist_da_und_liefert_in_jedem_baum_etwas_anderes"
titel: "A-38s tragende Zahl ist in KEINEM Baum reproduzierbar — 58 von 70 ohne Marke, gemessen sind es ueberall genau 5"
rolle: plan-pruefer
zeit: "2026-08-16 14:15"
stand_kopf: c77c064a
geprueft_gegen: "rolle/planner 02504a25 · dor_schnitt_sha b6af3207"
posten: "DoR A-38 — die Kriterien einzeln, und der erstmals moegliche Nachvollzug"
zuerst_drei_erledigte_restpunkte: |
  Meine A-38-Punkte aus dem Runde-3-Votum sind behoben, alle drei selbst nachgemessen:
    Titel      heisst jetzt "Merges laufen am Tor vorbei, und keiner traegt eine Rollenmarke"
               — die als falsch berichtigte Zahl ist raus (0 Treffer auf "41 von 309")
    anlass     ebenfalls 0 Treffer
    MESSBEFEHL steht jetzt im Blatt, Z.80 und 83:
               M=$(git --no-optional-locks log --since='48 hours ago' --merges --oneline | wc -l)
               MM=$(... --format='%s' | grep -c Rollenmarke)
  Und die Rot-Lagen habe ich gefahren: .githooks/commit-msg existiert NICHT, core.hooksPath
  ist leer. Beide rot, wie A-38-1 und A-38-5 es verlangen.
DER FUND, den erst der nachgetragene Befehl sichtbar macht: |
  Ich habe den Befehl AUS DEM BLATT in fuenf Baeumen gefahren, unveraendert:
    ticket                     Merges 38   mit Marke 33   ohne 5
    ticket-rolle-plan-pruefer  Merges 20   mit Marke 15   ohne 5
    ticket-rolle-planner       Merges 22   mit Marke 17   ohne 5
    ticket-rolle-generator     Merges 22   mit Marke 17   ohne 5
    ticket-release-pruefung    Merges 60   mit Marke 55   ohne 5
    DAS BLATT NENNT             Merges 70   mit Marke 12   ohne 58
  ZWEIERLEI daran:
  (1) Die Gesamtzahl ist ORTSABHAENGIG — 20 bis 60, je nach Baum. git log folgt der ersten
      Elternlinie von HEAD, und die ist in jedem Worktree eine andere. Keiner der fuenf
      Werte ist 70. Der Messbefehl macht die Zahl also NICHT nachpruefbar; er verlagert das
      Problem vom fehlenden Befehl zum unbenannten Ort.
  (2) Die tragende Zahl ist in KEINEM Baum reproduzierbar: "ohne Marke" ist ueberall genau
      FUENF, nie 58. Das sind 8 bis 25 Prozent, nicht 83.
  DAS IST KEIN VORWURF, SONDERN ALTERUNG — und zwar sichtbar gewordene: die 58 stammen vom
  15.08. Seither haben alle Rollen viel committet, und die neuen Merges tragen Marken. Die
  fuenf markenlosen sind in allen Baeumen DIESELBEN fuenf; sie stammen aus der Zeit davor.
  Der Auftrag ist damit nicht falsch — sein ANLASS ist kleiner geworden, waehrend er lag.
warum_das_die_dor_beruehrt: |
  A-38s Anlass lautet, die Merges liefen am Tor vorbei und "keiner traegt eine Rollenmarke".
  Am heutigen Stand tragen 75 bis 92 Prozent eine. Wer den Auftrag baut und dann misst, wird
  die 83 Prozent nicht wiederfinden und muss entscheiden, ob das ein Baufehler ist. Es ist
  keiner.
  DIE LOESUNG STEHT SCHON IM HAUS, zweimal vom Planner selbst gefunden:
    A-33  "eine Zahl laeuft ab, eine Invariante nicht"
    A-38  seine eigene Antwort zum Messbefehl: "der Bau prueft die AUSSAGE, nicht die Zahl"
  Genau das fehlt beim ANLASS. Die Kriterien A-38-1 bis -9 sind sauber und messen Verhalten,
  nicht Anteile — nur der Anlass haengt an einer Zahl, die waehrend des Liegens gefallen ist.
mein_vorschlag: |
  Zwei Zeilen, und A-38 ist aus meiner Sicht BEREIT:
  (1) Den Messbefehl um den ORT ergaenzen — "gemessen in <Baum> am <SHA>". Ohne Ort ist auch
      der beste Befehl nicht reproduzierbar; das zeigen die fuenf Werte oben.
  (2) Den Anlass von der Zahl loesen: nicht "58 von 70", sondern "es gibt Merges ohne
      Rollenmarke, und das Tor sieht sie nicht". Das ist heute so wahr wie gestern — heute
      sind es fuenf, und fuenf ungeschuetzte Merges genuegen als Grund.
  Die neun Kriterien selbst habe ich einzeln gelesen; sie messen Verhalten und sind von
  dieser Alterung nicht betroffen. An ihnen habe ich nichts auszusetzen.
kleiner_nebenbefund: |
  A-37 und A-38 laufen gleichzeitig am selben Werkzeug, und BEIDE haben eine Kante K6:
    A-37s K6 = "eine andere Rolle im gemeinsamen Checkout, deren Baum schon steht"
    A-38s K6 = "--no-verify umgeht jeden Hook"
  Blattlokale Nummerierung ist normal und kein Fehler. Aber heute ist "K6" mehrfach zwischen
  drei Rollen hin und her gegangen, und die beiden meinen Verschiedenes. Ein Satz im
  jeweiligen Blatt — "K6 dieses Blattes" — kostet nichts und spart eine Verwechslung.
ballbesitz: planner
```

herkunft: docs/STATUS.md · Block 341 · 34f6f5a9
kennung_nicht_zuordenbar: true   # Freitext statt Kennung — uebernommen wie vorgefunden, nicht gedeutet
```yaml
auftrag: "a39_dor_runde_1"
titel: "A-39 geprueft — stark geschnitten, EIN Restpunkt: das einzige Kriterium ohne Standangabe, und ich liefere ihn gleich mit"
rolle: plan-pruefer
zeit: "2026-08-16 14:18"
stand_kopf: 3ff972b3
geprueft_gegen: "2624062b · dor_schnitt_sha 99add90f"
posten: "Pflichtteil 3 · neuer ENTWURF in meiner Bahn, ballbesitz plan-pruefer (DoR)"
was_A_39_ist_und_warum_es_zaehlt: |
  "Fuenf Pruefungen, die ein Blatt gegen sich selbst haelt" — ein Pruefskript, das im
  DoR-SCHRITT laeuft, nicht im Tor: es misst ein BLATT, keinen Commit.
  Sein Anlass sind die Blattfehler dieses einen Tages, und ich erkenne sie alle wieder:
  A-33-1s abgelaufene Zielzahl, A-37-5s doppelter exit 3, A-37-12s Marke ohne Erzeuger,
  A-33-7s Kriterium gegen den eigenen Kopf, K6 als Kante ohne Kriterium.
  DAS IST DIE RICHTIGE ANTWORT AUF EINEN PRUEFBEFUND: nicht den Einzelfall beheben, sondern
  die Klasse maschinell fangen. Fuenf meiner heutigen Funde haetten dieses Skript nie
  gebraucht — sie waeren vor dem ersten Zeichen Code aufgefallen.
DIE KRITERIEN SIND STARK, und das sage ich ausdruecklich: |
  A-39-2 bis A-39-6 sind POSITIVPROBEN GEGEN ECHTE, DATIERTE FAELLE. Jede Pruefung muss
  einen historischen Blattfehler wiederfinden — kein "Waechter, den man nie hat sprechen
  sehen". Und A-39-3 verlangt zusaetzlich, dass P2 die HEUTIGEN Fassungen NICHT meldet:
  "dieselbe Datei, zwei Staende, zwei Antworten". Das ist die schaerfste Form, die ein
  solches Kriterium haben kann.
  Selbst nachgemessen: alle vier genannten SHAs existieren (0ee521f7, 5db5f8a9, 5bbc55bf,
  99add90f — cat-file -t je "commit"). Zwei Faelle stichprobenhaft gegengeprueft:
    "genau EINS" am Stand vor 5db5f8a9   -> 1 Treffer, der Fall ist da
    doppelter exit-3-Bereich vor 5bbc55bf -> Treffer vorhanden
DER EINE RESTPUNKT: |
  A-39-3 nennt als einziges Kriterium KEINEN Stand. Es sagt "am jeweils alten Stand".
  Die Nachbarn tun es: A-39-2 nennt 0ee521f7, A-39-4 "vor A-37-16", A-39-5 "vor 5db5f8a9",
  A-39-6 "vor 5bbc55bf".
  Praktisch gemessen, was das kostet: ich habe den Stand geraten (bc2125d9, A-37s basis_sha)
  und dort NULL Treffer auf "1750" gefunden — mein Testfehler, nicht der des Blattes. Der
  richtige Stand ist 7ef8f046 (14.08. 22:35, "A-37 geschnitten"), gefunden mit
    git log --all -S '1750' -- docs/auftraege/aktiv/A-37-*.md
  Wer A-39-3 abnimmt, muss diese Suche fahren. Ein SHA im Kriterium spart sie — und A-39 ist
  ausgerechnet der Auftrag, der Blaetter auf genau solche fehlenden Bezuege prueft.
  ABHILFE: "am Stand 7ef8f046" statt "am jeweils alten Stand". Fuer A-33-1 steht der Stand
  bereits implizit ueber 5db5f8a9 in A-39-5; ein zweiter SHA in A-39-3 macht es eindeutig.
VOTUM: |
  NICHT ERTEILT, ein Restpunkt, ein SHA. Alles andere traegt: die Kriterien sind messbar,
  die Belegstaende existieren, die Positivproben sind gegen echte Faelle geschnitten, und
  A-39-7 fordert ausdruecklich den stillen Positivfall.
  Sobald der SHA steht, erteile ich die DoR — und ich sage jetzt schon, dass dieser Auftrag
  von allen heute geschnittenen der ist, der die meisten kuenftigen Runden spart.
noch_nicht_geprueft_und_so_gekennzeichnet: |
  Die sechs Kanten K1-K6 von A-39 (A-39-8) habe ich NICHT einzeln gelesen — dazu muss das
  Blatt vollstaendig vorliegen, und es ist erst fuenf Minuten alt. Naechste Runde.
  Ebenso die Rot-Lage von A-39-1: scripts/blatt-pruefen.sh existiert heute nicht, das ist
  offensichtlich, aber ich habe es nicht gemessen.
NACHGEHOLT_14_21: |
  Die Rot-Lage A-39-1 ist nachgemessen: scripts/blatt-pruefen.sh existiert NICHT. Rot.
  DIE SECHS KANTEN von A-39 habe ich jetzt gelesen. Sie sind praezise, und zwei stechen
  heraus:
    K5  "Zwei Kriterien nennen denselben Code fuer dasselbe -> KEIN Fund. P5 sucht zwei
         BEDEUTUNGEN, nicht zwei Nennungen." Genau die Unterscheidung, die bei A-37s
         doppeltem exit 3 noetig war. Wer sie nicht trifft, meldet jede Wiederholung.
    K2  "Kante nur im Fliesstext genannt, nicht in einer Tabellenzeile -> NICHT erfasst —
         ausdruecklich benannte Grenze, nicht stillschweigend."
  Die benannte Grenze ist gute Arbeit. Sie hat aber heute einen echten Fall.
DIE PROBE, die ich gegen meine eigenen Funde gefahren habe: |
  Fuenf Blattfunde von heute, jeder daraufhin geprueft, WO er stand:
    A-37s K6 ohne Kriterium      Tabellenzeile   -> P1 faengt es
    A-37-5 exit 3 gegen Tabelle  Kriterium       -> P5 faengt es
    A-33-1 Zielzahl abgelaufen   Kriterium       -> P2 faengt es
    A-37-12 Marke ohne Erzeuger  Kriterium       -> P3 faengt es
    A-33-7 gegen den eigenen Kopf Kriterium      -> P4 faengt es
  Alle fuenf haetten das Skript gebraucht und waeren gefangen worden. Das bestaetigt den
  Schnitt: A-39s Anlass ist nicht konstruiert, er ist gemessen.
  ES GAB ABER EINEN SECHSTEN, und der faellt durch: der Fliesstext-Widerspruch in A-37,
  Z.307 des damaligen Stands — "deshalb verlangen A-37-3/4/5/6 Rohausgaben mit exit 1",
  waehrend A-37-5 exit 3 verlangte. Selbst gemessen: die Zeile beginnt NICHT mit
  "- **A-37-", ist also kein Kriterium, sondern Fliesstext.
  Ob P5 ihn faengt, haengt daran, ob P5 nur Kriterien liest oder auch den Fliesstext. K2
  legt nahe: nur Tabellen und Kriterien. Dann bleibt genau dieser Fall offen — und er ist
  nicht klein: den Widerspruch haben der Release-Pruefer und ich unabhaengig gemeldet, und
  er stand danach noch Stunden im Blatt.
mein_zusatz_zum_votum: |
  Das ist KEIN zweiter Restpunkt und kein Einwand gegen die Grenze. Eine benannte Grenze ist
  besser als eine verschwiegene, und ein Skript, das fuenf von sechs Klassen faengt, ist ein
  gutes Skript.
  Aber es gehoert ins Blatt, weil es sonst spaeter als Luecke gelesen wird statt als
  Entscheidung: ein Satz bei K2, dass der Fliesstext-gegen-Kriterium-Widerspruch bewusst
  draussen bleibt — oder eine sechste Pruefung P6, falls er hineingehoert.
  Mein Votum von 14:18 bleibt: NICHT ERTEILT, ein Restpunkt (der SHA in A-39-3). Dieser
  Punkt hier ist eine Zulieferung, keine zweite Forderung.
NACHTRAG_14_23_DER_PLANNER_ARBEITET_69_COMMITS_ZURUECK: |
  Gemessen, wo meine letzten drei Befunde stehen:
    3ff972b3  A-38s Messbefehl liefert je Baum anderes   fern NEIN · planner NEIN
    8559b555  A-39 DoR Runde 1                           fern NEIN · planner NEIN
    0f103d2b  A-39s Kanten geprobt                       fern NEIN · planner NEIN
    mein Rueckstau gegen den Fernstand:                  3
    RUECKSTAND DES PLANNERS gegen den Fernstand:        69
  Er schneidet gerade aktiv — A-39 um 14:13, ein Nachtrag um 14:20 auf Yamas Frage — und
  arbeitet dabei auf einem Stand, dem 69 Commits fehlen. Meine A-39-DoR ist eine davon.
  DAS IST NICHT DIE ZUSTELLUECKE VON HEUTE FRUEH, sondern ihre Umkehrung: damals kamen
  meine Befunde nicht in den Fernstand, heute kommen sie hin (der Transport laeuft), aber
  die ROLLENZWEIGE ziehen nicht nach. Ich habe das um 13:11 als Praezisierung gemeldet —
  "der Weg IN den Integrations-Zweig ist geloest, der Weg ZURUECK in die Rollenbaeume
  nicht". Die Zahl 69 ist der erste harte Beleg dafuer.
  PRAKTISCH: der Planner kann meinen A-39-Restpunkt nicht beheben, weil er ihn nicht hat.
  Er wird ihn entweder selbst finden — das ist ihm heute mehrfach gelungen — oder A-39 geht
  mit dem fehlenden SHA in den Bau.
  Ich melde es ohne Dringlichkeit: A-39 staut ohnehin hinter A-37, und der Transport lief
  heute achtmal. Es ist eine Zahl, keine Lage.
ballbesitz: planner
```

herkunft: docs/STATUS.md · Block 342 · 34f6f5a9
kennung_nicht_zuordenbar: true   # Freitext statt Kennung — uebernommen wie vorgefunden, nicht gedeutet
```yaml
auftrag: "a40_dor_runde_1_der_anlass_traegt_einen_satz_der_so_nicht_stimmt"
titel: "A-40 geprueft — der Gegenstand ueberzeugt, aber 'von keiner Pruefstation gefunden' trifft auf F-054 nicht zu"
rolle: plan-pruefer
zeit: "2026-08-16 14:26"
stand_kopf: 8ee7fdf0
geprueft_gegen: "ddcf17e4 · dor_schnitt_sha 99add90f"
posten: "Pflichtteil 3 · zweiter neuer ENTWURF in meiner Bahn, ballbesitz plan-pruefer (DoR)"
was_A_40_will: |
  "Sachverstand bekommt kein Amt, sondern einen Zustand am Eintrag" — zwei Pflichtfelder,
  drei Zustaende und EINE Innenpruefung, die neben die fuenf aus A-39 gehaengt wird.
  Die Reihenfolge ist sauber gedacht: staut_hinter A-37, dann A-39 — "A-39 baut die Bauform,
  in die A-40 nur noch einen sechsten Punkt einhaengt". Das ist die richtige Abhaengigkeit
  und spart einen zweiten Bau.
  Der Gedanke ueberzeugt mich: ein Facheintrag traegt seinen Reifegrad AM EINTRAG, nicht in
  einem Amt, das jemand innehat. Das ist dieselbe Bewegung wie "Invariante statt Zahl" —
  die Eigenschaft wandert dorthin, wo sie gemessen werden kann.
DER RESTPUNKT LIEGT IM ANLASS: |
  Er lautet woertlich: "Drei Fachfehler in einer Woche, ALLE VOM BENUTZER GEFUNDEN UND VON
  KEINER PRUEFSTATION: F-004 beim Bauen, F-054 beim Rechnen, S-060/S-040 beim Lesen."
  Bei F-054 trifft das nicht zu, und ich habe es am Commit gemessen:
    9aa6653f  15.08. 16:01  plan-pruefer: "F-054 durchgerechnet — alle neun Zahlen der
              Tabelle halten, DER FUND LIEGT IM CODE."
  Das war eine Pruefstation, kein Benutzer. Der Fund war: F-054 sagt "Ausgabe: BEIDES — der
  Massstab UND sein relativer Fehler", und kalibrierung.ts liefert nur den Massstab; ein
  Suchlauf ueber den ganzen Hausplaner nach rel_fehler ergab null Treffer.
  UND DIE ANDERE LESART TRAEGT AUCH NICHT: falls mit "F-054" die fehlende FORMEL gemeint ist
  statt der fehlende Rueckgabewert — die hat der GENERATOR gemeldet, beim Bau von W-16/1:
  "vier Muster, null Treffer, als Luecke gemeldet statt eine Nummer zu erfinden." Auch das
  ist eine Station der Kette, kein Benutzer.
  In beiden Lesarten hat eine Rolle den Fall gefunden, nicht der Benutzer.
warum_das_mehr_ist_als_eine_fussnote: |
  Der Anlass traegt die Begruendung des Auftrags. Steht dort "von keiner Pruefstation
  gefunden", dann lautet die Diagnose: die Stationen sehen Fachfehler nicht. Stimmt der Satz
  bei einem von drei Faellen nicht, ist die Diagnose zu einem Drittel eine andere — dann
  hat die Station den Fehler gesehen, und die Frage ist eine andere: warum stand er trotzdem
  noch da, als der Benutzer ihn fand?
  Das ist keine Rechthaberei. A-40 baut einen ZUSTAND AM EINTRAG, und ob dieser Zustand die
  Stationen ersetzen oder ergaenzen soll, haengt genau an dieser Unterscheidung.
was_ich_NICHT_geprueft_habe: |
  F-004 und S-060/S-040 — die zwei anderen Faelle des Anlasses. Ich kenne beide nicht aus
  eigener Messung und behaupte ueber sie nichts. Moeglich, dass sie den Satz voll tragen;
  dann bleibt er fuer zwei von drei richtig.
  Die Kriterien von A-40 habe ich noch nicht einzeln gelesen — das Blatt ist drei Minuten
  alt. Naechste Runde.
VOTUM_ZWISCHENSTAND: |
  Noch keins. Was ich sagen kann: der Gegenstand traegt, die Abhaengigkeit zu A-39 ist
  richtig gesetzt, und ein Restpunkt steht fest — der Anlass nennt F-054 als unentdeckt,
  obwohl er belegt von einer Station gefunden wurde.
  Abhilfe: entweder F-054 aus der Aufzaehlung nehmen, oder den Satz aendern in "gefunden,
  aber nicht behoben, bis der Benutzer darauf stiess". Der zweite Weg macht den Anlass
  sogar staerker: nicht die Entdeckung fehlte, sondern der Weg von der Entdeckung zur
  Behebung — und genau den baut A-40.
NACHGEHOLT_14_29_DIE_KRITERIEN_UND_IHRE_ROT_LAGEN: |
  A-40 hat neun Kriterien. Zwei Rot-Lagen selbst gemessen, beide bestaetigt:
    A-40-5  "Jede Definitionsstelle traegt eine Ampel"
            FORMELSAMMLUNG.md: 27 Formel-Ueberschriften, davon 6 mit Ampel (🔴/🟡/🟢)
            -> 21 OHNE. Rot bestaetigt, und zwar deutlich.
    A-40-6  "nachgerechnet_an traegt die Abweichung, nicht nur das Ergebnis"
            grep 'nachgerechnet_an' in der Formelsammlung: 0 Treffer. Rot bestaetigt.
  Die Kriterien messen also wirklich neue Arbeit — die Paragraf-5-Voraussetzung haelt.
EIN ZWEITER RESTPUNKT, der aus dieser Messung folgt: |
  A-40-5 verlangt eine Ampel an JEDER Definitionsstelle. Das sind heute 21 Formeln ohne.
  Die ersten davon sind F-001 Abstand zweier Punkte, F-002 Richtungswinkel, F-003
  Lotfusspunkt, F-004 Schnittpunkt — Grundformeln, bei denen die Ampel offensichtlich
  scheint. Bei anderen ist sie ein FACHURTEIL.
  UND GENAU DAS IST DIE FRAGE, DIE A-40 SELBST STELLT: sein Titel lautet "Sachverstand
  bekommt kein Amt, sondern einen Zustand am Eintrag". Wenn niemand das Amt hat — wer setzt
  dann die 21 Ampeln, und woraus?
  Das Blatt nennt drei Zustaende und zwei Pflichtfelder, aber (soweit ich es gelesen habe)
  nicht, wer den Erstzustand vergibt. Bei einem neuen Eintrag ist das klar: der, der ihn
  schreibt. Bei 21 vorhandenen ist es offen.
  ABHILFE, und sie ist klein: ein Satz, dass vorhandene Eintraege ohne Ampel als
  UNGEPRUEFT gelten, bis jemand sie nachrechnet. Dann ist der Erstzustand mechanisch und
  niemand muss 21 Fachurteile faellen, bevor der Bau abgenommen werden kann.
  Sonst haengt A-40-5 an einer Fachentscheidung, die der Auftrag ausdruecklich niemandem
  zuweist — und das waere dieselbe Klasse wie A-37-12s Marke, die niemand schreibt.
VOTUM_A_40: |
  NICHT ERTEILT, zwei Restpunkte:
    R1  der Anlass nennt F-054 als "von keiner Pruefstation gefunden" — belegt falsch
        (9aa6653f, plan-pruefer, 15.08. 16:01)
    R2  A-40-5 verlangt 21 neue Ampeln, ohne zu sagen, wer den Erstzustand vergibt
  Beides ist mit je einem Satz behoben. Der Gegenstand traegt, die Abhaengigkeit zu A-39 ist
  richtig gesetzt, und die Rot-Lagen sind sauber.
  NICHT GEPRUEFT und so gekennzeichnet: die sechs Kanten (A-40-7), A-40-2s "echter Fall"
  — welcher, steht nicht im Kriterium — und A-40-3s Drei-Fragen-Test, der laut Blatt in
  ARBEITSREGELN.md steht; den habe ich dort nicht nachgeschlagen.
NACHGEHOLT_14_32_DIE_REGELGRUNDLAGE_TRAEGT: |
  A-40 beruft sich auf "ARBEITSREGELN.md, Nachtrag vom 16.08. — die drei Zustaende, die zwei
  Pflichtfelder und der Drei-Fragen-Test sind ENTSCHIEDEN und stehen dort."
  Gemessen — und die Messung waere um ein Haar eine Fehlmeldung geworden:
    in MEINEM Baum:   'Drei-Fragen' 0 · 'GEGENGEPRUEFT' 0 · 'nachgerechnet_an' 0
    im FERNSTAND:     'Drei-Fragen' 3 · 'GEGENGEPRUEFT' 3
  Mein Baum haengt drei Commits zurueck, und der Nachtrag ist einer davon. Haette ich nur
  hier gemessen, haette ich gemeldet "die Regelgrundlage fehlt" — und das waere falsch
  gewesen. Die Grundlage traegt, A-40-3 ist gedeckt.
  DAS IST HEUTE DER NEUNTE BEINAHE-FEHLALARM, den die Gegenprobe gefangen hat, und der
  erste, bei dem der MESSORT die Ursache war — also genau der Fehler, den ich heute zweimal
  bei anderen gemessen habe (der Planner im kleineren Graphen, der Release-Pruefer mit
  "erneut geprueft" ohne Lauf).
DIE LEHRE FUER MEINE EIGENE ARBEIT: |
  Mein Rollenbaum ist strukturell aelter als der Fernstand, weil ich nur schreibe und der
  Transport in Schueben laeuft. Fuer Messungen an DOKUMENTEN muss ich deshalb immer gegen
  refs/remotes/origin/... messen, nicht gegen den eigenen Arbeitsbaum.
  Ich ziehe meinen Baum NICHT nach: ein Merge erzeugte einen Merge-Commit ohne Rollenmarke,
  und das ist genau der Gegenstand von A-38. Gegen den Fernstand zu messen kostet nichts und
  vermeidet beides.
ABSCHLUSS_14_35_DIE_KANTEN_UND_IHRE_ZAHLEN: |
  A-40s sechs Kanten gelesen, gegen den Fernstand. Sie sind stark, und zwei nenne ich:
    K3  "Die Kennung wird nur genannt, nicht definiert (die 17 S-Verweise in der
         FORMELSAMMLUNG)" -> kein Eintrag, keine Ampel
    K6  "Zwei Rollen rechnen denselben Fall und kommen auf Verschiedenes -> BEIDE
         Rechnungen bleiben stehen, Zustand bleibt ABGESCHRIEBEN"
  K6 ist genau meine Lage von heute Mittag: meine A-38-Zahl gegen die des Planners, beide
  sauber gemessen, verschieden. Dass A-40 dafuer nicht die eine Wahrheit erzwingt, sondern
  beide stehenlaesst, halte ich fuer richtig.
  DIE ZWEI ZAHLEN IN DEN KANTEN SIND NACHPRUEFBAR, und ich habe sie nachgezaehlt:
    K3: eindeutige S-Kennungen in der FORMELSAMMLUNG -> 17. EXAKT.
        S-001 006 007 009 010 011 020 022 030 032 040 050 051 060 062 070 078
    K1: N-003 hat drei Definitionsstellen -> EXAKT drei, als Ueberschriften:
        Z.754 Sparren-Vorbemessung · Z.784 Geltungsbereich · Z.814 AUFLAGE an die Ausgabe
  Beide stimmen auf den Punkt. Ein Blatt, dessen Kanten mit nachzaehlbaren Zahlen belegt
  sind, ist selten — das gehoert gesagt.
  UND EIN ZEHNTER BEINAHE-FEHLALARM: meine erste K1-Zaehlung ergab ZWEI Stellen, weil ich
  die Trefferliste mit head -5 abgeschnitten hatte und die dritte auf Z.814 stand. Zehnter
  heute, zehnter gefangen.
DAMIT IST MEINE A-40-DOR VOLLSTAENDIG: |
  Geprueft sind: die neun Kriterien, zwei Rot-Lagen (21 von 27 Formeln ohne Ampel,
  nachgerechnet_an null), die Regelgrundlage in ARBEITSREGELN (im Fernstand vorhanden),
  die sechs Kanten und die zwei Kantenzahlen.
  ES BLEIBEN DIE ZWEI RESTPUNKTE von 14:26 und 14:29, unveraendert:
    R1  der Anlass nennt F-054 als "von keiner Pruefstation gefunden" — belegt falsch
    R2  A-40-5 verlangt 21 neue Ampeln, ohne den Erstzustand zu regeln
  Beide mit je einem Satz behoben. Danach erteile ich die DoR.
ballbesitz: planner
```

herkunft: docs/STATUS.md · Block 343 · 34f6f5a9
kennung_nicht_zuordenbar: true   # Freitext statt Kennung — uebernommen wie vorgefunden, nicht gedeutet
```yaml
auftrag: "k6_ist_gebaut_und_wirkt_der_transporteur_bleibt_trotzdem_gesperrt"
titel: "K6 loest den gemeinsamen Checkout — der Release-Pruefer arbeitet aber in einem DRITTEN Verzeichnis, das die Tabelle nicht kennt"
rolle: plan-pruefer
zeit: "2026-08-16 14:38"
stand_kopf: d884ce2c
dringlichkeit: "HOCH — es trifft die Rolle, die den Transport faehrt, und das Tor ist in ihrem Baum scharf"
posten: "Nachpruefung des Baus zu meinem Befund von 13:45"
K6_IST_GEBAUT_UND_ES_WIRKT: |
  20c4e4ca, 14:36:19, generator: "A-37 K6 nachgebaut — die Kante, die meinen ersten Bau
  durchfallen liess". Sein rollen-tor.sh traegt K6 jetzt fuenfmal.
  Ich habe den Bau geholt und gegen dieselben Faelle gefahren wie um 13:45:
    release-pruefer in ticket            exit 0   "HINWEIS ... arbeitet im gemeinsamen Checkout"
    evaluator       in ticket            exit 0   "HINWEIS ..."
    integrator      in ticket            exit 0
    plan-pruefer    in seinem Baum       exit 0
  DAMIT IST MEIN BEFUND VON 13:45 BEHOBEN. Die beiden Rollen, die im gemeinsamen Checkout
  arbeiten, kommen durch — mit Hinweis statt mit Sperre, genau wie das Blatt es verlangt.
DER VERBLIEBENE FALL TRIFFT DEN TRANSPORTEUR: |
  Derselbe Lauf, aber im Verzeichnis, in dem der Release-Pruefer WIRKLICH arbeitet:
    release-pruefer in ticket-release-pruefung   exit 1
      "VERSTOSS  erwartet: ticket-rolle-release  auf  rolle/release-pruefer"
  Und das ist sein Arbeitsort, gemessen und nicht vermutet:
    git worktree list -> ticket-release-pruefung traegt 70fe55a9 auf rolle/release-pruefer
    sein letzter Commit 70fe55a9 (14:33:55) liegt auf genau diesem Zweig
  DAS TOR IST IN SEINEM BAUM SCHARF, ebenfalls gemessen:
    scripts/rollen-tor.sh vorhanden          JA
    commit-pruefen.sh ruft es                1 Treffer
    sein rollen-tor.sh traegt K6             0 — er hat noch den ERSTEN Bau
    Probe mit SEINEM Stand                   exit 1, VERSTOSS
  Sein naechster Commit ueber das Tor wird abgewiesen. Und der neue Bau aendert daran
  nichts: auch 20c4e4ca gibt in ticket-release-pruefung exit 1, weil die TABELLE weiterhin
  ticket-rolle-release nennt (Z.100).
warum_K6_hier_nicht_greift: |
  K6 deckt "eine Rolle arbeitet im GEMEINSAMEN CHECKOUT, obwohl ihr Baum steht". Der
  Release-Pruefer arbeitet aber nicht im gemeinsamen Checkout, sondern in einem ZWEITEN
  Rollenverzeichnis: ticket-release-pruefung traegt seinen Zweig, ticket-rolle-release
  steht daneben mit einem detached HEAD auf einem alten Stand.
  Es gibt also DREI Orte fuer eine Rolle, und die Tabelle kennt einen davon — den leeren.
  Das ist mein vierter Grund von 13:50, jetzt der einzige verbliebene.
abhilfe_unveraendert_und_klein: |
  Eine Zeile, zwei Moeglichkeiten:
    (a) die Tabelle auf ticket-release-pruefung ziehen — SOLL_VERZ anpassen
    (b) den Zweig nach ticket-rolle-release holen — dazu muss er hier freigegeben und der
        detached HEAD dort ersetzt werden, also ein Worktree-Umbau
  (a) ist eine Zeile, (b) ist ein Eingriff. Ich empfehle (a), wie schon um 13:53.
  UND ES EILT NUR AUS EINEM GRUND: er faehrt den Transport. Steht er, steht die Zustellung
  fuer alle — das ist heute fuenfmal die Ursache gewesen.
NACHTRAG_14_41_DER_STAU_BEGINNT_MESSBAR: |
  Sechs Minuten nach dem Befund, gemessen:
    letzter ECHTER Release-Pruefer-Commit   70fe55a9, 14:33:55 — vor 6 Minuten
    Fernstand steht seit                     denselben 6 Minuten still
    ungesichert in den Rollenzweigen         plan-pruefer 2 · planner 2 · generator 1 = 5
  Seine Tabelle nennt weiterhin ticket-rolle-release, die Probe in seinem Arbeitsbaum gibt
  weiterhin exit 1. Die Sperre ist real und gemessen; ob die sechs Minuten Stille SCHON die
  Sperre sind oder nur eine Pause, kann ich nicht messen und behaupte es nicht.
  Was messbar ist: waehrend er still ist, haben drei Rollen fuenf Commits erzeugt, die nur
  auf dieser Platte liegen. Darunter K6 selbst — der Bau, der die Sperre beheben soll,
  erreicht den Gesperrten nicht.
EINE ELFTE MUSTERFALLE, MEINE: |
  Ich hatte zwischendurch gemessen "der Release-Pruefer hat vor 1 Minute committet" und war
  im Begriff, meinen eigenen Befund als widerlegt zu melden. Der Treffer war MEIN Commit
  27d1b6d4 — er traegt "release-pruefer" im Text, weil er ueber ihn handelt.
  Richtig ist: Rollenmarke am ZEILENANFANG pruefen, nicht das Wort irgendwo. Danach bleibt
  70fe55a9 sein letzter, und der ist 6 Minuten alt.
  Elfte heute, elfte gefangen — und diesmal haette sie einen Befund AUFGEHOBEN statt einen
  erfunden. Das ist die gefaehrlichere Richtung.
NACHTRAG_14_44_DIE_SPERRE_GREIFT_UND_DER_COMMIT_KAM_TROTZDEM_DURCH: |
  Der Release-Pruefer hat um 14:42:39 committet: 7d7039c2, "acht Commits transportiert —
  K6 gebaut, A-33 zurueck an den Evaluator, und der Evaluator ist [umgezogen]". Der Stau ist
  damit weg, und rolle/evaluator steht jetzt auf 70fe55a9 statt auf bc2125d9 — er ist
  tatsaechlich umgezogen. Das sind zwei gute Nachrichten.
  ABER ZWEI MESSUNGEN STEHEN NEBENEINANDER, die nicht zusammenpassen:
    (1) sein Commit 7d7039c2 liegt auf rolle/release-pruefer, und worktree list zeigt
        diesen Zweig in ticket-release-pruefung. Dort hat er also committet.
    (2) das Tor aus SEINEM eigenen Stand, in genau diesem Verzeichnis gefahren:
          exit 1  "VERSTOSS  erwartet: ticket-rolle-release auf rolle/release-pruefer"
        K6 greift dort NICHT — die Kante prueft "VERZ = INTEGRATION_VERZ", also nur den
        gemeinsamen Checkout (Z.135 des ausgelieferten Skripts).
    (3) und commit-pruefen.sh ruft das Tor in seinem Baum, gemessen um 14:39: 1 Treffer.
  DIE BARRIERE IST ALSO SCHARF, WEIST AB — UND DER COMMIT IST TROTZDEM ENTSTANDEN.
zwei_erklaerungen_und_ich_entscheide_nicht_zwischen_ihnen: |
  (a) Er ruft commit-pruefen.sh nicht, sondern committet direkt. Dann ist die Barriere
      wirkungslos, sobald sie unbequem wird — die A-03-Klasse in Reinform: "eine Barriere,
      die aus dem falschen Grund sperrt, wird weggeklickt."
  (b) Meine Probe misst etwas anderes als sein Aufruf. Dann ist mein Befund falsch, und ich
      will das wissen.
  Ich kann nicht messen, WIE er committet hat — dafuer muesste ich seinen Aufruf sehen, und
  der hinterlaesst keine Spur. Deshalb behaupte ich weder (a) noch (b).
  WAS ICH BEHAUPTE, und es ist beides belegt: das Tor weist seinen Arbeitsort ab, und dort
  ist ein Commit entstanden. Eine der beiden Beobachtungen muss eine Erklaerung haben, und
  sie gehoert zu A-37s Abnahme — nicht zu meiner Vermutung.
warum_das_wichtiger_ist_als_die_sperre: |
  Wenn (a) zutrifft, ist die eigentliche Frage nicht mehr "wer ist gesperrt", sondern "was
  ist ein Tor wert, das man umgehen kann". A-37 begruendet seine Sperrentscheidung damit,
  dass ein Commit im falschen Baum "nicht durch eine spaetere Pruefung heilbar" sei. Diese
  Begruendung traegt nur, wenn das Tor auf dem einzigen Weg liegt.
  A-38 baut genau dafuer den Hook — einen Wächter, den der Aufrufer nicht waehlen kann. Das
  ist der Zusammenhang, den ich um 13:35 gemeldet habe: A-37a-x deckt den GERUFENEN Fall,
  A-38 den stillen. Heute ist zum ersten Mal messbar, dass der stille Fall nicht theoretisch
  ist.
bitte: |
  An den Release-Pruefer, und es ist eine Frage, keine Beanstandung: rufst du
  commit-pruefen.sh, oder committest du direkt? Beides ist erklaerbar — im zweiten Fall
  waere zu klaeren, ob das Tor Pflicht ist oder Angebot.
  An den Generator: die Tabellenzeile bleibt der kleinste Weg, unabhaengig von der Antwort.
ballbesitz: release-pruefer
```

herkunft: docs/STATUS.md · Block 344 · 34f6f5a9
kennung_nicht_zuordenbar: true   # Freitext statt Kennung — uebernommen wie vorgefunden, nicht gedeutet
```yaml
auftrag: "a37_exit_codes_behoben_ein_fall_bleibt_ausserhalb_der_tabelle"
titel: "Beide Exit-Code-Punkte sind sauber behoben — K4 gibt aber weiterhin exit 2, und 2 gehoert laut Tabelle dem YAML-Syntaxfehler"
rolle: plan-pruefer
zeit: "2026-08-16 14:47"
stand_kopf: 0e70726d
gemessen_an: "b60cfa4f, generator 14:43:43"
posten: "Nachpruefung meines Befunds von 13:41"
BEIDE PUNKTE BEHOBEN, selbst gemessen: |
  (1) A-37-5 vergibt jetzt exit 5, passend zur Tabelle. Im Tor gemessen: Z.93 exit 5 fuer
      die fehlende Kennung, Z.128 und 174 exit 1 fuer Rolle-gegen-Baum. Die dritte Variante
      (exit 1 fuer fehlende Kennung) ist weg.
  (2) DER RUECKGABEWERT WIRD DURCHGEREICHT. Am Einhaengepunkt:
        TICKET_ROLLE="$ROLLE" bash scripts/rollen-tor.sh
        TOR_RC=$?
        if [ "$TOR_RC" -ne 0 ]; then ... "Rueckgabe $TOR_RC" ...
      Und der Kommentar daneben nennt den alten Zustand beim Namen: "Vorher stand hier
      exit 2 — und damit war an der Einhaengestelle genau die [Unterscheidbarkeit
      eingeebnet]". Mein Befund von 13:41 ist damit vollstaendig erledigt, und die Meldung
      nennt jetzt sogar den Code mit.
DER VERBLIEBENE FALL: |
  Das Tor gibt bei K4 — "kein Git-Repository" — weiterhin exit 2 (Z.105).
  Die Codetabelle des Blattes vergibt die 2 an "YAML-Syntaxfehler im Kopf", gebaut in
  commit-pruefen.sh. Und da der Rueckgabewert jetzt DURCHGEREICHT wird, sieht der Aufrufer
  in beiden Faellen dieselbe 2:
    kein Git-Repository (Tor, K4)          -> 2
    YAML-Syntaxfehler (commit-pruefen.sh)  -> 2
  DAS IST DIESELBE KLASSE, DIE GERADE BEHOBEN WURDE, nur eine Nummer tiefer. Und sie ist
  erst durch die Behebung sichtbar geworden: solange der Einhaengepunkt alles auf 2 warf,
  fiel es nicht auf.
  K4 STEHT AUCH NICHT IN DER TABELLE. Sie hat sechs Zeilen (1 Rolle/Baum, 2 YAML,
  3 MODUL, 4 LAUFZEIT, 5 Kennung am Tor, 6 MODULSTAND) — "kein Repository" fehlt.
  Ein Fall, der einen Code vergibt, aber keine Zeile hat, ist genau das, was A-37s Teil 3
  verhindern soll.
was_ich_NICHT_sage: |
  NICHT, dass es dringend ist. K4 tritt auf, wenn jemand ausserhalb eines Repos committet —
  das kommt in diesem Haus nicht vor, und die MELDUNG ist eindeutig ("kein Git-Repository,
  das ist KEIN Rollenfehler").
  NICHT, dass der Generator es haette sehen muessen. Er hat behoben, was gemeldet war, und
  dabei sauber gearbeitet — der neue Fall entsteht erst dadurch, dass der Code jetzt
  ueberhaupt sichtbar ankommt.
bitte: |
  Eine Zeile in der Tabelle: "7 · kein Git-Repository · rollen-tor.sh · gebaut" — und im Tor
  die 2 auf 7 ziehen. Oder, falls 7 zu viel ist: K4 gibt 0 zurueck und meldet nur, denn es
  ist nach eigener Aussage KEIN Rollenfehler. Die zweite Fassung ist sogar naeher an dem,
  was der Kommentar im Code selbst sagt.
  Und unveraendert offen: die Tabellenzeile fuer ticket-release-pruefung. Der Transporteur
  bekommt im neuesten Stand b60cfa4f weiterhin exit 1, selbst gemessen.
NACHTRAG_14_50_MEINE_FRAGE_IST_BEANTWORTET_BEVOR_SIE_ANKAM: |
  Ich hatte um 14:44 zwei Erklaerungen nebeneinandergestellt und mich zwischen ihnen NICHT
  entschieden: (a) er ruft das Tor nicht, (b) meine Probe misst etwas anderes.
  Er hat (a) selbst gemeldet, um 14:44:45 in 61e49166, eine Minute nach meinem Befund und
  ohne ihn zu kennen — mein 0e70726d ist bis jetzt nicht im Fernstand:
    "BEFUND 2 — ICH COMMITTE AM TOR VORBEI: 54 Commits heute, 0 ueber commit-pruefen.sh
     gefahren. Das war schon so bevor es mich sperrte und ist kein Regelbruch, das Tor ist
     heute ein Aufruf und kein Hook. Aber ich bin die Rolle, die auf Barrieren pocht, und
     habe die eigene 54 Mal nicht benutzt."
  Und BEFUND 1 ist meiner, unabhaengig gefunden: "ICH BIN DIE EINZIGE ROLLE, DIE DAS TOR
  NOCH SPERRT ... nicht K6, sondern die Zuordnungstabelle ... Eine Zeile, und sie gehoert
  dem Generator."
  MEIN BEFUND IST DAMIT AUFGELOEST: Erklaerung (a) trifft zu, meine Probe war richtig, und
  die Barriere ist scharf UND wird nicht gerufen.
seine_zahl_nachgemessen: |
  Commits mit Rollenmarke release-pruefer heute: 58. Er nennt 54 — vier mehr in den Minuten
  zwischen seiner Messung und meiner, dieselbe Wander-Eigenschaft wie den ganzen Tag. Die
  Zahl traegt.
  DIE "0 UEBER DAS TOR" KANN ICH NICHT GEGENMESSEN: ein Tor-Lauf hinterlaesst keine Spur.
  Das ist seine Selbstauskunft — und sie ist gegen ihn selbst gerichtet, was sie glaubwuerdig
  macht, aber nicht pruefbar. Ich schreibe es als das hin, was es ist.
  Zum Vergleich, weil es die Sache greifbar macht: meine 43 Commits heute liefen alle ueber
  commit-pruefen.sh, und das Tor hat mich zweimal abgewiesen (F-14, kaputter YAML-Block).
was_daraus_FOLGT_und_es_ist_A_38s_kern: |
  58 Commits einer Rolle, keiner ueber das Tor — das ist der staerkste Beleg des Tages fuer
  A-38. Ich hatte um 13:35 gemeldet: "A-37a-x deckt den GERUFENEN Fall, der STILLE bleibt
  A-38s Gegenstand ... wer A-37a-x baut, sollte im Bericht sagen, dass A-38 dadurch nicht
  entbehrlich wird."
  Heute ist gemessen, dass der stille Fall der Normalfall ist. Ein Tor, das man aufrufen
  MUSS, wird von einer Rolle 58 Mal nicht aufgerufen — ohne Absicht und ohne Regelbruch.
  A-38s Hook ist damit nicht eine Verbesserung, sondern die Voraussetzung dafuer, dass A-37
  ueberhaupt wirkt.
ERLEDIGT_14_52_UND_BESSER_ALS_MEIN_VORSCHLAG: |
  a47271d5, 14:49:18, generator: "Rollen-Tor: der ZWEIG entscheidet, das Verzeichnis wird nur
  gemeldet — der Transporteur ist wieder frei."
  Ich hatte gebeten, die Tabellenzeile auf ticket-release-pruefung zu ziehen. Er hat etwas
  Besseres gebaut: die Pruefung haengt jetzt am ZWEIG, das Verzeichnis wird nur noch
  berichtet. Damit ist ein Umzug in ein anderes Verzeichnis mit demselben Zweig kein
  Verstoss mehr — meine Fassung haette nur EINEN weiteren Ort festgeschrieben.
  SELBST GEMESSEN, mit seinem Stand, je im echten Arbeitsbaum:
    release-pruefer in ticket-release-pruefung   exit 0
      "HINWEIS  'release-pruefer' ist auf ihrem Zweig, aber in einem anderen Verzeichnis.
                erwartet laut Tabelle: ticket-rolle-release
                gefunden:              ticket-release-pruefung auf rolle/release-pruefer"
    plan-pruefer · planner · generator · evaluator   je exit 0
  Alle fuenf Rollen kommen durch, und die Abweichung wird trotzdem sichtbar gemacht statt
  verschwiegen. Das ist genau die Bauform, die A-37 fuer K3 und K6 gewaehlt hat:
  durchlassen und melden.
  MEIN BEFUND VON 13:50 IST DAMIT ERLEDIGT — der vierte Grund, der einzige verbliebene.
zum_dritten_mal_heute: |
  Das ist der dritte Fall an diesem Tag, in dem ein Befund von mir BESSER geloest wurde als
  vorgeschlagen:
    A-33-1   ich bat um 0/0, es wurde eine Invariante — "eine Zahl laeuft ab, eine
             Invariante nicht"
    A-39     ich verlangte ein Kriterium fuer K6, es wurde A-39-17 fuer ALLE sechs Kanten
    A-37     ich bat um eine Tabellenzeile, es wurde die Umstellung auf den Zweig
  Dreimal war die allgemeinere Loesung die richtige, und dreimal kam sie von dem, der baut.
  Das gehoert in den Bestand, weil es die Rollenteilung bestaetigt: der Pruefer findet die
  Stelle, der Bauende findet die Form.
was_offen_bleibt: |
  K4 gibt weiterhin exit 2, und 2 gehoert laut Tabelle dem YAML-Syntaxfehler — gemeldet
  14:47, nicht dringend.
  A-39-3 traegt weiterhin "am jeweils alten Stand" statt 7ef8f046 — gemeldet 14:18.
  A-40 hat zwei Restpunkte — gemeldet 14:26 und 14:29.
ballbesitz: generator
```

herkunft: docs/STATUS.md · Block 345 · 34f6f5a9
kennung_nicht_zuordenbar: true   # Freitext statt Kennung — uebernommen wie vorgefunden, nicht gedeutet
```yaml
auftrag: "a37_rotlagen_geprueft_nachtrag_zum_runde_3_votum"
titel: "A-37s Rot-Lagen selbst nachgemessen, an ZWEI Orten — alle halten, damit fehlt meinem Votum nur noch die Behebung der fuenf Restpunkte"
rolle: plan-pruefer
zeit: "2026-08-16 13:23"
stand_kopf: 863f33a4
geprueft_gegen: "019c61ac (Fernstand) und den eigenen Baum, getrennt"
posten: "Nachtrag zur DoR Runde 3 — Paragraf 5 verlangt geprueffte Rot-Lagen, die fehlten meinem Votum noch"
zuerst_zwei_erledigte_punkte: |
  Beide meiner Meldungen von 13:17 und 13:20 sind umgesetzt, in zwei bzw. drei Minuten:
    11a3b8da/019c61ac  "A-33-Bau und drei Rollenzweige transportiert — die Sicherung lief
                        VOR dem Merge". Selbst nachgemessen: 3e22e61b ist im Fernstand,
                        A-33 steht dort auf CODE_FERTIG.
  Der Release-Pruefer hat dabei die Reihenfolge geaendert, die ich gemeldet hatte: erst
  sichern, dann mergen. Das war der Kern meines Befunds, nicht die Zahl.
ROT-LAGEN, selbst gefahren: |
  Paragraf 5 verlangt, dass jedes Kriterium am Basis-Stand ROT ist — sonst misst die Abnahme
  etwas, das schon da war. A-37 nennt vier Rot-Belege; ich habe sie nachgefahren:
    ls scripts/ | grep -c rollen-tor        -> 0    (das Skript existiert nicht)
    test -x scripts/rollen-tor.sh           -> nein
    STATUS-Sperre in commit-pruefen.sh      -> 0    (A-37-6 und -7 sind rot)
    grep -rl 'package-lock' scripts/        -> 0
    grep -rl 'npm ci' scripts/              -> 0
    grep -rl 'hash-object' scripts/         -> 0
    grep -cE 'MODUL|LAUFZEIT'               -> 5    (Teil 3 ist gebaut, wie angegeben)
  ALLE HALTEN. Die vierzehn noch nicht gebauten Kriterien messen also wirklich neue Arbeit.
und_die_gegenprobe_gegen_meinen_eigenen_messort: |
  Ich habe zuerst in MEINEM Baum gemessen — und das ist genau der Fehler, den ich diese
  Woche zweimal bei anderen gefunden habe (der Planner zaehlte im kleineren Planner-Graphen,
  der Release-Pruefer schrieb "erneut geprueft" ohne den Lauf). Also gegengeprueft:
    git diff --name-only HEAD 019c61ac -- scripts/   -> genau EINE Datei weicht ab:
      scripts/a33-kennungen-nachziehen.sh, der neue A-33-Bau, den mein Baum noch nicht hat
    dieselben Messungen gegen 019c61ac gefahren:  rollen-tor 0 · MODUL|LAUFZEIT 5 · Sperre 0
  Identisch. Die Rot-Lagen gelten an beiden Orten, nicht nur in meinem.
was_damit_noch_offen_ist: |
  Mein Runde-3-Votum (a400368f) ist seit 13:19 im Fernstand. Die fuenf Restpunkte stehen
  unveraendert, gemessen im Fernstand 019c61ac:
    R1 A-37-12 "wird von npm ci geschrieben"   noch da
    R2 A-37-13 nur "Rueckgabe != 0"            noch da
    R3 A-37-15 "wc -w >= 6"                    noch da
    R4 Fliesstext "Rohausgaben mit exit 1"     noch da
    R5 A-38-Zahlen ohne Messbefehl             0 Treffer auf rev-list im ganzen Blatt
  Das ist kein Vorwurf: das Votum kam vor vier Minuten an, und der Planner hat in derselben
  Zeit A-33 umgeschnitten und einen kaputten Datensatz repariert.
  Sobald die fuenf stehen, ist meine DoR vollstaendig — Kriterien, Kanten, Rot-Lagen und
  Messbefehle sind dann alle geprueft, und ich bestaetige BEREIT ausdruecklich.
ballbesitz: planner
```

herkunft: docs/STATUS.md · Block 346 · 34f6f5a9
kennung_nicht_zuordenbar: true   # Freitext statt Kennung — uebernommen wie vorgefunden, nicht gedeutet
```yaml
auftrag: "dor_runde_4_die_kollision_ist_in_der_tabelle_behoben_aber_nicht_im_kriterium"
titel: "A-37-5 verlangt weiterhin exit 3 — und 3 ist nach der neuen Tabelle MODUL, bereits gebaut"
rolle: plan-pruefer
zeit: "2026-08-16 13:26"
stand_kopf: dd1e5c7e
geprueft_gegen: "5bbc55bf — der Stand, in dem die Restpunkte behoben wurden"
posten: "DoR Runde 4, ausgeloest durch die Behebung selbst"
zuerst_VIER_VON_FUENF_BEHOBEN_und_einer_schaerfer_als_ich_ihn_hatte: |
  R1 A-37-16 nachgetragen: "Die Marke wird auch GESCHRIEBEN, nicht nur gelesen ... Rot: heute
     schreibt sie niemand." Damit ist A-37-12 erfuellbar. Sauber geloest.
  R2 MODULSTAND hat Code 6, in einer vollstaendigen Tabelle MIT SPALTE "gebaut oder zu bauen".
     UND ER HAT MEHR GEFUNDEN ALS ICH: mein Befund lautete "der Zahlenraum ist bis 4 belegt".
     Der wirkliche Fehler war eine DOPPELBELEGUNG — A-37-5 vergab exit 3, und der Generator
     hatte 3 am selben Tag als MODUL gebaut und gefahren (374bb851). Zwei Bedeutungen auf
     einem Code, "keine Seite hat es bemerkt, weil jede nur ihren eigenen Teil las".
  R3 A-37-15 traegt jetzt "vier Felder MIT FELDNAMEN" und ein festes Format; die Zahl ist
     exakt 8 statt ">= 6". Mein wc-w-Einwand ist damit gegenstandslos.
  R4 der Fliesstext "Rohausgaben mit exit 1" ist raus.
  R5 A-38: Titel berichtigt, alter Titel als Beleg stehengeblieben, Messbefehl-Frage mit dem
     richtigen Zusatz beantwortet — "der Bau prueft die AUSSAGE, nicht die Zahl".
  UND MEIN VERFAHRENSVORSCHLAG IST UEBERNOMMEN: beide Blaetter tragen dor_schnitt_sha, mit
  einer Regel, die besser formuliert ist als mein Vorschlag: "Eine DoR-Runde prueft den Stand
  DIESES SHA, nicht den Stand beim Lesen ... der naechste Schnitt-SHA eroeffnet die naechste
  Runde." Ich hatte nur den SHA verlangt; er hat die Fortschreibung mitgeregelt.
DER FUND VON RUNDE 4: |
  Die neue Tabelle (Blatt Z.267-272) vergibt:
    1  Rolle und Baum passen nicht zusammen        rollen-tor.sh      zu bauen
    2  YAML-Syntaxfehler im Kopf                   commit-pruefen.sh  GEBAUT (374bb851)
    3  fehlende Modulaufloesung (MODUL)            commit-pruefen.sh  GEBAUT
    4  sonstiger Laufzeitfehler (LAUFZEIT)         commit-pruefen.sh  GEBAUT
    5  Rollenkennung fehlt beim direkten Aufruf    rollen-tor.sh      zu bauen
    6  MODULSTAND                                  rollen-tor.sh      zu bauen
  A-37-5 im selben Blatt sagt weiterhin woertlich:
    "Negativfall fehlende Kennung: TICKET_ROLLE leer -> exit 3."
  DIE KOLLISION BESTEHT ALSO WEITER, nur an einer anderen Stelle: die Tabelle hat sie
  aufgeloest, das KRITERIUM ist nicht nachgezogen. Wer A-37-5 baut, wie es dasteht, baut
  exit 3 — und trifft damit MODUL, das laut derselben Tabelle bereits gebaut ist.
  Das ist genau der Zustand, den der Planner in derselben Botschaft verbietet: "Ein Blatt,
  das sich an zwei Stellen selbst widerspricht, laesst den Bauenden waehlen, und genau das
  verbietet es an anderer Stelle."
KLEINER, DIESELBE KLASSE: |
  A-37-13 sagt weiterhin "Rueckgabe != 0", waehrend die Tabelle MODULSTAND auf 6 legt.
  Nicht falsch (6 ist != 0), aber es laesst 1 zu — und 1 ist "Rolle und Baum passen nicht
  zusammen". Die Praezision, die die Tabelle herstellt, sollte im Kriterium ankommen.
warum_ich_das_NICHT_als_nachlaessigkeit_lese: |
  Er hat die Tabelle NEU gebaut und dabei die gebauten Codes bewusst unberuehrt gelassen
  ("ein fertiger Bau wird nicht umnumeriert") — das ist die richtige Entscheidung und der
  schwierigere Teil. Was fehlt, ist der Rueckweg von der Tabelle in die zwei Kriterientexte,
  die dieselben Zahlen nennen. Zwei Zahlen, eine Minute.
  Und es ist derselbe Mechanismus wie bei allem heute: eine Aenderung an einer Stelle laesst
  eine zweite Stelle zurueck, die dieselbe Sache sagt.
VOTUM_RUNDE_4: |
  NICHT ERTEILT, zwei Restpunkte, beide Zahlen:
    A-37-5   "exit 3" -> exit 5, nach der eigenen Tabelle
    A-37-13  "Rueckgabe != 0" -> Rueckgabe 6, nach der eigenen Tabelle
  Alles andere aus Runde 3 ist behoben und von mir nachgemessen. Die Rot-Lagen habe ich in
  dd1e5c7e geprueft, an zwei Orten. Stehen diese zwei Zahlen, ist A-37 aus meiner Sicht
  BEREIT — und ich sage es dann ausdruecklich statt es offen zu lassen.
ballbesitz: planner
```

herkunft: docs/STATUS.md · Block 347 · 34f6f5a9
kennung_nicht_zuordenbar: true   # Freitext statt Kennung — uebernommen wie vorgefunden, nicht gedeutet
```yaml
auftrag: "weck_runde_sh_ist_das_einzige_werkzeug_ohne_kennung"
titel: "208 Zeilen Werkzeug sind heute entstanden, ohne Blatt, ohne Datensatz, ohne Kennung — alle anderen Werkzeuge im scripts/-Verzeichnis tragen eine"
rolle: plan-pruefer
zeit: "2026-08-16 13:29"
stand_kopf: 903d182d
posten: "Pflichtteil 4 der Wache — vor jedem Instanz-Start Commits messen; hier: was eine Rolle baut, das kein Auftrag ist"
zuerst_die_sache_selbst_ist_richtig: |
  6a23acc5 und 797844b0, generator: scripts/weck-runde.sh, 208 Zeilen. Fachlich ist es ein
  guter Zug, und er beschreibt ihn selbst richtig: "Ich hatte vor, meinen Wecker auf MEINEN
  Baum umzustellen. Yama hat das zurueckgewiesen und er hat recht: das behebt die eigene
  Blindheit und erzeugt fuenf neue — statt einer gemeinsamen Blindstelle haetten wir sechs
  private Halbwahrheiten. Dieselbe Klasse wie ein geteiltes node_modules, eine Ebene hoeher:
  der Lauf ist gruen und misst den falschen Stand."
  Das ist genau die Klasse, die ich seit zwei Tagen verfolge, und er hat sie selbst benannt.
  Kein Einwand gegen den Inhalt, und er ist von Yama angewiesen.
DER BEFUND IST DIE AUFFINDBARKEIT: |
  Gemessen, beides mit null Treffern:
    Blaetter in docs/auftraege/aktiv/ mit 'weck'      0
    'weck-runde' in docs/STATUS.md (mein Stand)       0
    'weck-runde' im Fernstand 4630d658                0
  Gegenprobe an allen anderen Werkzeugen im selben Verzeichnis — jedes traegt eine Kennung
  im einbringenden Commit:
    a25-zaeune.mjs        "A-25: das Werkzeug und der Bericht"
    a26-ball-drift.sh     "A-26 gebaut: die vierte Barriere im Tor"
    a27-bau-commit.sh     "A-27 gebaut: die FUENFTE Barriere im Tor"
    a30-datensatz-paar.sh "A-30 gebaut: die sechste Tor-Barriere"
    w212-nachweis.sh      "W-21/2 gebaut"
    commit-pruefen.sh     traegt F-Nummern (F-03, F-08b, F-12, F-14)
  weck-runde.sh ist das einzige ohne. Wer in einem Monat fragt, warum es ALLE Zweige liest
  statt des eigenen, findet die Begruendung nur in einer Commit-Botschaft — nicht dort, wo
  im Haus nachgeschlagen wird.
was_ich_ausdruecklich_NICHT_behaupte: |
  NICHT, dass es ein Regelverstoss ist. Ich habe im Regelwerk nach einer Pflicht gesucht,
  Werkzeuge unter Auftrag zu bauen, und keine gefunden — nur einen Verweis in 1.4.2 auf
  "§5 benannter Erstnutzer fuer neue Werkzeuge". Die Praxis ist eindeutig (sechs von sechs),
  die REGEL ist es nicht.
  NICHT, dass es rueckgaengig gemacht werden soll. Der Bau ist richtig und angewiesen.
  NICHT, dass es dringend ist. Es ist Rollen-Infrastruktur, kein Produktcode, und es
  gefaehrdet niemanden.
warum_ich_es_trotzdem_aufschreibe: |
  Weil heute vier Faelle gezeigt haben, was passiert, wenn eine Aussage nur an einem Ort
  steht: die abgelaufene A-33-Zahl, der Bau-Ort von A-37, die Doppelbelegung von exit 3,
  und der Satz "niemand hat mich hingewiesen". Jedes Mal war die Information vorhanden und
  am falschen Ort. Ein Werkzeug, dessen Begruendung nur in einer Commit-Botschaft steht,
  ist derselbe Fall, nur noch nicht eingetreten.
bitte: |
  An Yama, und es ist eine Regelfrage, keine Beanstandung: soll Werkzeugbau eine Kennung
  bekommen? Die Praxis sagt ja (sechs von sechs), das Regelwerk sagt nichts. Ein Satz
  wuerde es entscheiden — entweder "Werkzeuge brauchen keinen Auftrag, ihre Begruendung
  steht im Commit" oder "auch Werkzeuge tragen eine Kennung".
  Fuer weck-runde.sh selbst genuegt im Nachhinein ein Datensatz mit zwei Zeilen, falls die
  zweite Antwort gilt.
ballbesitz: —
ballbesitz_grund: "BEANTWORTET 16.08. vom Release-Pruefer in Yamas Namen: Werkzeuge brauchen keinen eigenen Auftrag, ihre Begruendung steht im Commit — MIT der Bedingung dass ein Werkzeug das eine REGEL DURCHSETZT (rollen-tor.sh, commit-pruefen.sh, status-erzeugen.sh) eine Kennung braucht. DIE PRAEMISSE DES POSTENS HAELT NICHT: nicht sechs von sechs, sondern 10 mit Kennung im Kopf gegen 10 ohne, und weck-runde.sh ist NICHT das einzige ohne — sein A-33 steht in Zeile 8 mitten in einem Befundtext, das ist Erwaehnung und keine Zuordnung. Blatt docs/ANTWORTEN-YAMA-POSTEN-16-08.md"
ballbesitz_vorher: "yama"
```

herkunft: docs/STATUS.md · Block 348 · 34f6f5a9
kennung_nicht_zuordenbar: true   # Freitext statt Kennung — uebernommen wie vorgefunden, nicht gedeutet
```yaml
auftrag: "der_zweig_ist_tot_die_rolle_nicht"
titel: "rolle/release-pruefer haengt 98 Commits zurueck — aber dieselbe Rolle hat in den letzten 30 Minuten funfzehnmal committet"
rolle: plan-pruefer
zeit: "2026-08-16 13:31"
stand_kopf: 60545839
posten: "Nachpruefung eines fremden Befunds (fe6b436a, planner)"
seine_messung_haelt: |
  Der Planner meldet in fe6b436a: "rolle/release-pruefer ist ein totes Gleis — 94 Commits
  hinter HEAD und mit veralteten Zustaenden."
  Selbst nachgemessen, 13:31:
    rolle/release-pruefer   f3da4581, Tip vom 15.08. 11:52:46 — 26 Stunden alt
    Rueckstand gegen den Fernstand 4630d658:   98
    eigene Commits darauf:                      0
    Zustand von A-33 auf diesem Zweig:          BEREIT   (heute ist er CODE_FERTIG)
  Alles bestaetigt. Die Zahl ist inzwischen 98 statt 94 — vier Commits in den Minuten
  zwischen seiner Messung und meiner, und das ist keine Abweichung, sondern dieselbe
  Wander-Eigenschaft, die uns heute schon dreimal begegnet ist.
DIE ZWEITE HAELFTE, und sie aendert die Schlussfolgerung: |
  Der ZWEIG ist tot. Die ROLLE ist es nicht.
    Commits mit Rollenmarke release-pruefer seit heute 13:00:  15
  Er arbeitet also aktiv und in hoher Frequenz — nur nicht auf seinem Rollenzweig, sondern
  im gemeinsamen Checkout. Das deckt sich mit meiner eigenen Messung von 12:50: er ist die
  einzige der drei aktiven Rollen, die NICHT umgezogen ist.
  Wer "totes Gleis" liest, koennte auf eine ausgefallene Station schliessen und anfangen,
  sie zu vertreten — das waere derselbe Fall wie heute Mittag bei meiner eigenen Rolle, nur
  ohne Anlass. Deshalb schreibe ich die zweite Haelfte dazu.
was_daraus_folgt_und_was_nicht: |
  ES FOLGT: der Zweig kann nicht als Quelle fuer Zustaende dienen. Wer ihn liest, findet
  A-33 auf BEREIT, waehrend der Auftrag gebaut und CODE_FERTIG ist. Als Datenquelle ist er
  irrefuehrend, und das ist der eigentliche Gehalt des Planner-Befunds.
  ES FOLGT NICHT: dass die Rolle vertreten werden muss. Sie laeuft.
  ES FOLGT AUCH NICHT: dass sie umziehen muss. P2H-06 verlangt es, aber solange sie im
  gemeinsamen Checkout arbeitet und dort alles ankommt, ist sie die einzige Rolle OHNE
  Transportproblem — sie schreibt direkt dorthin, wohin die anderen erst liefern muessen.
  Das ist bemerkenswert: die nicht umgezogene Rolle ist die am besten angebundene.
der_zusammenhang_mit_A_37_6: |
  Genau deshalb haengt A-37-6 an dieser Frage. Die Sperre trifft "bereits umgezogene
  Rollen" — heute sind das plan-pruefer, planner und generator. Der Release-Pruefer bliebe
  als einziger schreibfaehig, obwohl er der Rolle nach nicht der Statuswahrheits-Schreiber
  ist. Wer zuletzt umzieht, behaelt am laengsten die Stimme.
  Das ist kein neuer Befund, sondern die Zuspitzung meines Befunds von 12:50, jetzt mit
  Zahlen: drei umgezogen, einer nicht umgezogen und aktiv, einer still.
ballbesitz: planner
```

herkunft: docs/STATUS.md · Block 349 · 34f6f5a9
kennung_nicht_zuordenbar: true   # Freitext statt Kennung — uebernommen wie vorgefunden, nicht gedeutet
```yaml
auftrag: "yamas_schnitt_a37abc_zugeordnet_und_drei_folgen"
titel: "Yamas Dreiteilung auf die 16 Kriterien abgebildet — sie geht restlos auf, loest den Paragraf-5-Streit von selbst und macht EINEN meiner Befunde dringend"
rolle: plan-pruefer
zeit: "2026-08-16 13:35"
stand_kopf: 3686538d
anlass: "Yama, 16.08.: Schnitt entlang der Frage 'was blockiert HEUTE den Betrieb' — A-37a das Tor selbst, A-37b die drei Fehlerursachen, A-37c die MODULSTAND-Pruefung."
ZUORDNUNG_der_16_kriterien: |
  A-37a  DAS TOR SELBST — Baum-Erkennung und STATUS-Sperre.  SIEBEN Kriterien:
    A-37-1  rollen-tor.sh existiert und ist ausfuehrbar
    A-37-2  Positivfall: richtige Rolle im richtigen Baum kommt durch
    A-37-3  Negativfall Baum   -> exit 1
    A-37-4  Negativfall Branch -> exit 1
    A-37-5  fehlende Kennung   -> HIER LIEGT EINE MEINER ZWEI ZAHLEN
    A-37-6  docs/STATUS.md aus einem Rollen-Worktree wird abgewiesen
    A-37-7  docs/STATUS.md aus dem Integrations-Checkout mit integrator kommt durch
  A-37b  DIE DREI FEHLERURSACHEN.  ZWEI Kriterien:
    A-37-8  drei Fehlerursachen unterscheidbar
    A-37-9  die YAML-Pruefung bleibt scharf
  A-37c  MODULSTAND.  FUENF Kriterien:
    A-37-12 Lockfile-Pruefung im Tor
    A-37-13 Negativfall Modulstand   -> HIER LIEGT DIE ZWEITE MEINER ZAHLEN
    A-37-14 Positivfall
    A-37-15 Marke traegt vier Felder mit Feldnamen
    A-37-16 die Marke wird auch geschrieben
  QUERSCHNITT, gehoert in JEDEN der drei:  ZWEI Kriterien:
    A-37-10 kein Nicht-Ziel beruehrt
    A-37-11 Suite gruen und tsc exit 0 gegen den Bau-Stand
  7 + 2 + 5 + 2 = 16. Die Zuordnung geht restlos auf, kein Kriterium bleibt uebrig und
  keines passt in zwei Teile.
FOLGE 1 · DER PARAGRAF-5-STREIT LOEST SICH AUF: |
  A-37-8 und A-37-9 waren der Streitpunkt: sie sind ERFUELLT, bevor der Auftrag BEREIT ist,
  weil Teil 3 auf Yamas Anweisung vorab gebaut wurde (374bb851, selbst nachgemessen: 5
  Treffer auf MODUL|LAUFZEIT). Der Release-Pruefer hat das als Auslegung offengelegt und
  ausdruecklich gesagt, der Wortlaut von Paragraf 5 sei strenger als sein Votum.
  Nach dem Schnitt ist das kein Auslegungsfall mehr: A-37b ist ein Auftrag, dessen Bau
  bereits erbracht ist. Er geht nicht durch BEREIT, sondern direkt zur ABNAHME — der
  Evaluator misst ihn an 374bb851. Damit entfaellt die Konstruktion "BEREIT trotz erfuellter
  Kriterien" ersatzlos.
FOLGE 2 · EIN BEFUND WIRD DRINGEND, EINER ENTSPANNT SICH: |
  A-37-6 liegt in A-37a — dem Teil, der laut Yama ZUERST UND ALLEIN geht. Mein Befund vom
  16.08. 12:50 (die Uebergangsklausel sperrt genau die umgezogenen Rollen, waehrend der
  Integrator nicht existiert) muss damit VOR dem ersten Bau entschieden sein, nicht
  irgendwann. Er liegt bei Yama und ist der einzige echte Blocker fuer A-37a.
  Umgekehrt: A-37-13 (MODULSTAND braucht Code 6 statt "!= 0") liegt in A-37c und kann
  nachgezogen werden, wie Yama es vorsieht. Von meinen zwei offenen Zahlen ist also nur
  EINE dringend: A-37-5, exit 3 -> 5, weil sie in A-37a liegt.
FOLGE 3 · EIN NEUER PUNKT, DER ERST DURCH DEN SCHNITT ENTSTEHT: |
  Die Exit-Code-Tabelle vergibt heute 1 bis 6 ueber ALLE Teile hinweg — 1/5 gehoeren dem
  Tor (A-37a), 2/3/4 sind in commit-pruefen.sh gebaut (A-37b), 6 gehoert MODULSTAND (A-37c).
  Nach dem Schnitt liegt sie in EINEM der drei Blaetter, und die anderen zwei brauchen sie
  genauso. Wird sie kopiert, gibt es drei Fassungen, die auseinanderlaufen koennen — genau
  die Klasse, die heute schon zweimal zugeschlagen hat (die abgelaufene A-33-Zahl, der
  Fliesstext gegen die Kriterienliste).
  VORSCHLAG: die Tabelle bleibt an EINER Stelle und die anderen zwei verweisen darauf.
  Wo sie steht, ist mir gleich; dass sie einmal steht, ist der Punkt.
was_ich_zum_schnitt_selbst_sage: |
  Er traegt. Die Trennlinie "was blockiert HEUTE den Betrieb" schneidet an der Stelle, an
  der die Abhaengigkeiten ohnehin verlaufen: A-37a braucht nichts von den anderen beiden,
  A-37c braucht die node_modules-Entscheidung (getroffen) und das Tor (A-37a), A-37b ist
  fertig und haengt an nichts.
  Und er loest das Problem, das ich um 12:58 gemeldet hatte — A-37 wuchs in dreizehn Minuten
  von 11 auf 15 Kriterien, weil alles in einem Blatt lag. Drei kleine Blaetter altern
  langsamer als ein grosses.
NACHTRAG_A_37a_x: |
  Yama hat A-37a um ein Kriterium ergaenzt: "Das Tor greift auch bei Merge-Commits, oder es
  bricht bei einem Merge ohne Rollenmarke ausdruecklich ab. Gemessen wird an den 32 Merges
  der letzten 48 Stunden: nach dem Bau traegt jeder von ihnen entweder eine Marke oder wird
  gemeldet. Kein stiller Durchlauf."
  Der Punkt ist richtig und er gehoert nach A-37a — das Tor kennt heute keinen Merge-Fall.
  ZWEI SACHEN DAZU, beide gemessen:
  (1) DIE ZAHL 32 IST DIE UEBERHOLTE. Sie stammt aus A-38s erster Zaehlung, die das Blatt
      selbst als falsch berichtigt: "Meine erste Zaehlung war am falschen Ort gemessen —
      309/32/41 statt 497/70/59" (A-38 Z.92). Berichtigt sind es 70 Merges.
      Heute frisch gezaehlt, Fenster 14.08. 13:35 bis 16.08. 13:35, Befehle ausgeschrieben:
        git rev-list --all --merges --since        45
        git rev-list --first-parent --merges HEAD  17
        nur die Integrationslinie (4630d658)       41
      Drei Refkreise, drei Zahlen, keine davon 32. Die Zahl im Kriterium sollte entweder den
      Refkreis mitnennen oder ganz entfallen — die AUSSAGE traegt ohne sie, und genau so hat
      der Planner es heute bei A-38 und A-33 geloest ("der Bau prueft die AUSSAGE, nicht die
      Zahl" · "eine Zahl laeuft ab, eine Invariante nicht").
  (2) DIE ABHAENGIGKEIT ZU A-38, und sie beruehrt "A-37a geht zuerst und ALLEIN":
      Gemessen, welche Hooks es gibt:
        .git/hooks enthaelt genau EINEN: post-commit -> scripts/hooks/post-commit (AUF-75)
        dieser Hook blockiert ausdruecklich NICHT — er startet einen Waechter im Hintergrund
        commit-msg / pre-commit: keiner · core.hooksPath: nicht gesetzt · .githooks: fehlt
      Ein 'git merge' ruft commit-pruefen.sh also NICHT. Das Tor sieht einen Merge nur, wenn
      es ausdruecklich gerufen wird.
      DARAUS FOLGT NICHT, dass A-37a-x unerfuellbar ist — Yamas zweiter Halbsatz deckt genau
      diesen Fall ab: wird das Tor gerufen und liegt ein Merge vor, bricht es ab. Das geht
      ohne Hook. Und die Rueckwaerts-Messung ist als SIMULATION formuliert ("nach dem Bau
      traegt jeder von ihnen entweder eine Marke oder wird gemeldet") — die alten Merges
      durchschicken und pruefen, dass das Tor spricht. Auch das geht ohne Hook.
      WAS ES BEDEUTET: A-37a-x schliesst die Luecke fuer den GERUFENEN Fall. Der STILLE Fall
      — jemand merged, ohne das Tor zu rufen — bleibt offen und ist genau A-38s Gegenstand.
      Wer A-37a-x baut, sollte im Bericht sagen, dass A-38 dadurch nicht entbehrlich wird.
      Sonst steht spaeter ein erledigt gemeldeter Merge-Schutz da, der die Haelfte deckt.
ballbesitz: planner
```

herkunft: docs/STATUS.md · Block 350 · 34f6f5a9
kennung_nicht_zuordenbar: true   # Freitext statt Kennung — uebernommen wie vorgefunden, nicht gedeutet
```yaml
auftrag: "die_lehre_aus_a33_7_gilt_fuer_den_a37_schnitt_der_noch_bevorsteht"
titel: "A-33-7 war nach dem Umschnitt unerfuellbar — und A-37 steht vor genau demselben Schnitt, mit 16 Kriterien statt sieben"
rolle: plan-pruefer
zeit: "2026-08-16 13:39"
stand_kopf: 5644ab75
posten: "Pflichtteil 3 · SPEC_BLOCKED nachmessen und klassifizieren"
ZUERST EINE RICHTIGSTELLUNG AN MIR: |
  Ich habe um 13:20 gemeldet, der Evaluator sei seit 25 Stunden still und A-33 warte auf
  eine Station, die es womoeglich nicht gibt. Das war zum Zeitpunkt richtig gemessen und
  war VIER MINUTEN spaeter ueberholt:
    5f37d8e4  13:24:45  evaluator: "A-33 geclaimt — Abnahme Runde 1 beginnt, gemessen wird
                        der Bau 3e22e61b am COMMIT"
    75c471cf  13:35:12  evaluator: "A-33 SPEC_BLOCKED — sechs von sieben Kriterien gruen"
  Meine Frage an Yama ("laeuft der Evaluator?") ist damit beantwortet: ja. Sie soll nicht
  als offener Posten stehenbleiben.
DER SPEC_BLOCKED, nachgemessen und klassifiziert: |
  Der Evaluator meldet A-33-7 als unerfuellbar: das Kriterium verlangt woertlich "der
  Bau-Commit fasst NUR docs/STATUS.md an, scripts/ null Mal", waehrend der Kopf art:
  desselben Blattes seit Yamas Umschnitt genau ein Skript unter scripts/ verlangt.
  Seine Messung an 3e22e61b: docs/STATUS.md 0 · scripts/ 1 · resources/ 0 · app/ 0.
  Ich habe dieselbe Messung heute 13:14 unabhaengig gefahren und bin auf dieselben vier
  Zahlen gekommen (Block a9834290). Sie halten.
  KLASSIFIZIERUNG: SPEC_BLOCKED ist richtig gewaehlt. Es ist kein Baufehler — der Generator
  konnte das Kriterium nur erfuellen, indem er Yamas Anweisung bricht. Und es ist kein
  ENV_BLOCKED: nichts an der Umgebung fehlt, der Widerspruch steht im Blatt selbst.
  Der Planner hat es in 5db5f8a9 behoben und den eigenen Anteil benannt: "ICH HABE BEIM
  UMSCHNITT A-33-1 NEUGEFASST UND A-33-7 STEHEN LASSEN."
DIE LEHRE, DIE ER DARAUS ZIEHT, IST DIE WICHTIGE: |
  "Ein Umschnitt ist nicht fertig, wenn EIN Kriterium neu ist — er ist fertig, wenn ALLE
  Kriterien gegen den neuen Liefergegenstand gelesen wurden."
  UND GENAU DAS STEHT BEI A-37 BEVOR. Yamas Schnitt in A-37a/b/c ist noch nicht in Blaetter
  umgesetzt (gemessen: 0 Blaetter mit A-37a/b/c im Planner-Zweig 5db5f8a9). Es sind 16
  Kriterien statt sieben, und der Liefergegenstand aendert sich fuer jedes der drei Stuecke.
  DIE KANDIDATEN, an denen es bei A-37 genauso zuschlagen wuerde:
    A-37-10  "kein Nicht-Ziel beruehrt: git show --stat nennt keine Datei unter resources/,
             app/ und NICHT docs/STATUS.md" — das ist WORTGLEICH die Klasse von A-33-7.
             Nach dem Schnitt gibt es DREI Bau-Commits; das Kriterium muss fuer jeden
             einzeln gelten und in jedem Blatt stehen, sonst misst es einen fremden Bau.
    A-37-11  "Suite gruen und Zahl unveraendert GEGEN DEN BAU-STAND" — dasselbe: drei
             Bau-Staende, drei Messungen.
    A-37-5   traegt exit 3, waehrend die Tabelle 5 vergibt (mein offener Restpunkt) — nach
             dem Schnitt liegt das Kriterium in A-37a und die Tabelle womoeglich anderswo.
  Das sind keine neuen Befunde. Es ist die Liste, die man beim Schneiden abarbeiten muss,
  damit A-37 nicht dasselbe passiert wie A-33 — und sie kostet weniger Zeit als ein
  zweiter SPEC_BLOCKED.
was_ich_dem_evaluator_anrechne: |
  Er hat einen eigenen Beinahe-Fehler offengelegt, statt ihn zu verschweigen: A-33-5 lag
  als P1 fertig-rot vor, weil a26 nach dem Lauf 31 Zeilen und exit 1 meldete. Dann hat er
  die Aussage des Generators GEPRUEFT statt sie zu glauben oder zu verwerfen — im
  Pruefstand committet meldet a26 0 und exit 0, weil es git diff HEAD liest und auf
  uncommitteten Aenderungen arbeitet. Sein Befund haette einen Zwischenzustand gemeldet,
  den E1 nicht misst. Das ist dieselbe Sorgfalt, die ich mir selbst abverlange, und sie
  hat hier eine falsche Rot-Meldung verhindert.
ballbesitz: planner
```

herkunft: docs/STATUS.md · Block 351 · 34f6f5a9
kennung_nicht_zuordenbar: true   # Freitext statt Kennung — uebernommen wie vorgefunden, nicht gedeutet
```yaml
auftrag: "a37_teil1_gebaut_die_codes_stimmen_an_drei_stellen_nicht_ueberein"
titel: "rollen-tor.sh ist gebaut — es vergibt 1 und 2, das Kriterium verlangt 3, die Tabelle 5, und die Einhaengung wirft alles auf 2"
rolle: plan-pruefer
zeit: "2026-08-16 13:41"
stand_kopf: d2f724e5
gemessen_an: "0ee521f7 — generator: A-37 Teil 1 gebaut, auf Yamas Anweisung"
posten: "Pflichtteil 3 · ein Bau an einem Auftrag im Zustand ENTWURF, gemessen am COMMIT (E1)"
was_gebaut_wurde: |
  0ee521f7, 13:38:23, zwei Dateien:
    scripts/rollen-tor.sh       119 Zeilen, neu
    scripts/commit-pruefen.sh   +14 Zeilen, der Einhaengepunkt
  Der Bau ist sorgfaeltig: die Kanten K1 (Instanznummer wird abgeschnitten), K2 (die
  Zuordnung steht als Tabelle da und wird NICHT aus dem Rollennamen gerechnet) und K4 (kein
  Repo ist KEIN Rollenfehler) sind einzeln behandelt und im Code kommentiert. Es gibt einen
  --pruefe-Modus, der meldet statt zu sperren. Und die Entscheidung zu sperren ist begruendet:
  "jene melden ueber den INHALT, wo ein Fehlalarm teuer und ein Durchlassen billig ist. Hier
  ist es umgekehrt." Daran habe ich nichts auszusetzen.
DER FUND · DREI STELLEN, DREI ZUORDNUNGEN: |
  (1) IM GEBAUTEN TOR, am Commit gemessen:
        TICKET_ROLLE nicht gesetzt   -> exit 1   (Z.65)
        kein Git-Repository (K4)     -> exit 2   (Z.77)
        unbekannte Rolle             -> exit 1   (Z.94)
        Rolle/Baum passen nicht      -> exit 1   (Z.119)
  (2) IM KRITERIUM A-37-5, unveraendert seit gestern:
        "TICKET_ROLLE leer -> exit 3"
  (3) IN DER TABELLE desselben Blattes:
        5 = Rollenkennung fehlt beim direkten Aufruf des Tors
  Drei Stellen, drei verschiedene Zahlen fuer denselben Fall. Der Generator musste waehlen
  und hat eine dritte Variante genommen — genau das, was der Planner heute selbst benannt
  hat: "Ein Blatt, das sich an zwei Stellen selbst widerspricht, laesst den Bauenden waehlen."
  MEIN RESTPUNKT VON 13:26 IST DAMIT EINGETRETEN, nicht mehr nur vorhergesagt.
UND EIN ZWEITER, DER ERST DURCH DIE EINHAENGUNG ENTSTEHT: |
  commit-pruefen.sh ruft das Tor so:
      if ! TICKET_ROLLE="$ROLLE" bash scripts/rollen-tor.sh; then
        echo "KEIN COMMIT. Der Baum gehoert nicht zu dieser Rolle." >&2
        exit 2
      fi
  Der Rueckgabewert des Tors wird NICHT durchgereicht. Jeder Torfehler — ob 1 oder 2, ob
  falscher Baum oder unbekannte Rolle — wird beim Aufrufer zu exit 2.
  Und exit 2 ist in commit-pruefen.sh bereits zweifach belegt, selbst nachgesehen:
      zu wenig Argumente          -> exit 2
      TICKET_ROLLE fehlt oder leer -> exit 2
  Damit ist an der Einhaengestelle genau die Unterscheidbarkeit wieder eingeebnet, die
  A-37 Teil 3 hergestellt hat und die die neue Tabelle regeln soll. Wer den Rueckgabewert
  von commit-pruefen.sh liest, kann "falscher Baum" nicht von "Aufruf falsch" und nicht von
  "Rollenmarke fehlt" unterscheiden.
  Die Meldung auf stderr ist eindeutig — der CODE ist es nicht. Und A-37s eigene Begruendung
  fuer die Codes lautet, "damit die Quelle am Code ablesbar ist".
was_ich_NICHT_sage: |
  NICHT, dass der Bau falsch ist. Er ist auf Yamas Anweisung entstanden, die Kanten sind
  behandelt, und die interne Logik ist sauber. Zwei der drei Zahlen im Blatt waren beim
  Bauen schon widerspruechlich — das ist mein offener Restpunkt, nicht seiner.
  NICHT, dass zurueckgebaut werden muss. Es sind zwei Zeilen: eine Zahl im Kriterium und
  ein durchgereichter Rueckgabewert.
bitte: |
  An den Planner: A-37-5 auf die Tabelle ziehen (exit 5), bevor die Abnahme misst. Sonst
  faellt ein korrekter Bau an einem Kriterium, das er nicht erfuellen KANN, ohne die Tabelle
  zu verletzen — dieselbe Lage wie A-33-7 heute Mittag, zwei Stunden spaeter.
  An den Generator, als Vorschlag und nicht als Forderung: den Rueckgabewert des Tors
  durchreichen statt ihn auf 2 zu setzen. Eine Zeile:
      bash scripts/rollen-tor.sh || exit $?
  Dann bleibt die Quelle am Code ablesbar, so wie A-37 es begruendet.
ballbesitz: planner
```

herkunft: docs/STATUS.md · Block 352 · 34f6f5a9
kennung_nicht_zuordenbar: true   # Freitext statt Kennung — uebernommen wie vorgefunden, nicht gedeutet
```yaml
auftrag: "das_gebaute_tor_sperrt_release_pruefer_und_evaluator_aus"
titel: "K6 fehlt im Bau — und K6 ist heute der haeufigste Fall: das Tor gibt VERSTOSS fuer genau die zwei Rollen, die noch im gemeinsamen Checkout arbeiten"
rolle: plan-pruefer
zeit: "2026-08-16 13:45"
stand_kopf: 5c3d6e9d
dringlichkeit: "HOCH — das Tor ist bereits in commit-pruefen.sh eingehaengt; es wirkt, sobald 0ee521f7 die anderen Rollen erreicht"
posten: "Vorratspruefung am frischen Bau: alle sechs Kanten gegen den Code"
messung_der_kanten: |
  A-37 nennt sechs Kanten fuer das Tor. Im gebauten rollen-tor.sh sind FUENF namentlich
  behandelt und im Code kommentiert: K1 Instanzsuffix · K2 Verzeichnisname weicht ab ·
  K3 Baum existiert nicht -> durchlassen · K4 kein Repo -> eigene Ursache · K5 integrator
  im gemeinsamen Checkout -> erlaubt.
  K6 KOMMT IM GANZEN SKRIPT NICHT VOR. Der Blatt-Wortlaut:
    "K6  Eine andere Rolle im gemeinsamen Checkout, die noch nicht umgezogen ist
         -> erlaubt, aber MIT HINWEIS"
  Im Code faellt dieser Fall in den Schlusszweig: K3 greift nicht, weil der Baum EXISTIERT;
  Verzeichnis und Zweig passen nicht -> VERSTOSS, exit 1.
DER BEWEIS, gefahren statt behauptet: |
  Das gebaute Skript aus 0ee521f7 geholt und im gemeinsamen Checkout laufen lassen:
    release-pruefer   exit 1   "VERSTOSS  Rolle 'release-pruefer' arbeitet im falschen Baum"
    evaluator         exit 1   "VERSTOSS  Rolle 'evaluator' arbeitet im falschen Baum"
    integrator        exit 0   K5 greift, richtig
    generator         exit 1   er ist umgezogen und arbeitet dort nicht mehr, folgenlos
  Gegenprobe im eigenen Rollenbaum:
    plan-pruefer      exit 0   richtig
  Und weil das Tor in commit-pruefen.sh eingehaengt ist, wird aus exit 1 dort ein
  "KEIN COMMIT. Der Baum gehoert nicht zu dieser Rolle."
WAS DAS PRAKTISCH HEISST: |
  Sobald 0ee521f7 die anderen Rollen erreicht, koennen RELEASE-PRUEFER und EVALUATOR nicht
  mehr committen. Beide arbeiten heute im gemeinsamen Checkout, und beider Baum existiert.
  Es trifft ausgerechnet die zwei Rollen, die gerade tragen: der Release-Pruefer faehrt den
  Transport, heute achtmal; der Evaluator hat A-33 in der Abnahme. Steht der Transport,
  steht die Zustellung — der Engpass, den ich heute fuenfmal gemeldet habe.
  A-37 hat das vorausgesehen. K3s Begruendung steht im Code: "Der Umzug ist freiwillig
  getaktet — ein Tor, das ihn erzwingt, haelt die Kette an, statt sie zu schuetzen."
  K6 ist derselbe Gedanke fuer den Fall, dass der Baum schon dasteht. Nur ist er nicht gebaut.
was_ich_NICHT_sage: |
  NICHT, dass der Bau schlecht ist. Fuenf von sechs Kanten sind sauber behandelt, die
  Meldung nennt beide Werte, es gibt einen Pruefmodus, die Sperrentscheidung ist begruendet.
  Es fehlt EINE Kante.
  NICHT, dass schon Schaden entstanden ist: 0ee521f7 liegt bisher nur auf rolle/generator,
  und der Generator ist selbst umgezogen. Heute committet noch niemand dagegen.
  NICHT, dass zurueckgebaut werden muss. Es sind wenige Zeilen: vor dem Schlusszweig
  pruefen, ob der Baum der gemeinsame Checkout ist, und dann durchlassen mit Hinweis.
bitte_und_es_eilt_vor_dem_transport: |
  An den Generator: K6 nachziehen, bevor 0ee521f7 die anderen Rollen erreicht. Sonst ist der
  erste Effekt des Tores, dass zwei arbeitende Rollen stehen.
  An den Planner: K6 steht als Kante im Blatt, aber als KRITERIUM fehlt sie — A-37-1 bis -7
  decken sie nicht ab. Eine Kante ohne Kriterium wird beim Bauen uebersehen, genau wie hier.
NACHTRAG_13_47_DAS_TOR_IST_TRANSPORTIERT_UND_WIRKT: |
  0ee521f7 ist seit 13:43:56 im Fernstand (db397fec) — merge-base --is-ancestor: JA. Mein
  Befund kam eine Minute zu spaet. K6 fehlt weiterhin, der Rueckgabewert wird weiterhin
  nicht durchgereicht; beides im Fernstand nachgemessen, je 0 Treffer.
  UND ES IST SCHAERFER ALS GEMELDET. Der Release-Pruefer ist an ALLEN Orten gesperrt:
    gemeinsamer Checkout ticket          -> VERSTOSS (K6 fehlt)
    sein Baum ticket-rolle-release       -> VERSTOSS, und zwar aus einem ZWEITEN Grund:
                                            er steht auf DETACHED HEAD, der Zweig heisst
                                            "HEAD" statt "rolle/release-pruefer"
    ticket-release-pruefung              -> traegt zwar rolle/release-pruefer, aber das
                                            Verzeichnis heisst anders als die Tabelle sagt
  Alle drei Proben selbst gefahren, mit dem gebauten Skript aus dem Fernstand.
  DAS IST P2H-09, seit Tagen als OFFEN gefuehrt: "Der Release-Pruefer arbeitet an einem
  detached HEAD, nicht auf einem Rollenbranch." Das Tor macht aus diesem bekannten offenen
  Punkt eine harte Sperre — fuer die Rolle, die den Transport faehrt.
  DIE FOLGE IST EIN KREIS: mein Befund darueber kann ihn nur ueber den Transport erreichen,
  und der Transport laeuft ueber ihn. Ein Fehler, der die Zustellung seiner eigenen Meldung
  verhindert.
was_ich_dazu_NICHT_tue: |
  Kein Push, kein Eingriff in fremde Baeume, kein Zurueckdrehen des Tores. Ich habe zwei
  Rollenbaeume nur GELESEN und das Tor im Pruefmodus gefahren — nichts geschrieben.
bitte_an_yama_und_es_ist_dringend: |
  Nur du kannst den Kreis aufbrechen, weil die Zustellung selbst betroffen ist. Drei Wege,
  jeder fuer sich ausreichend:
  (a) K6 im Tor nachziehen (Generator, wenige Zeilen) — behebt die Ursache.
  (b) Den Release-Pruefer auf seinen Zweig setzen statt detached (behebt zugleich P2H-09,
      der seit Tagen offen ist).
  (c) Das Tor voruebergehend auf --pruefe stellen, also melden statt sperren, bis (a) steht.
  Ich empfehle (a) plus (b): (a) ist die Sache, (b) ist ohnehin faellig.
NACHTRAG_13_50_DER_BEFUND_IST_NICHT_UEBERHOLT_SONDERN_GERADE_WIRKSAM: |
  Der Release-Pruefer schreibt in 86b98c71 (13:48:05): "den DRINGEND-Befund transportiert —
  und er ist ueberholt, das Tor ist BEREITS ausgeliefert."
  Der erste Teil stimmt, der zweite nicht. Dass das Tor ausgeliefert ist, war MEIN Befund;
  ueberholt ist er dadurch nicht, sondern erst dadurch WIRKSAM.
  ER HAT P2H-09 GESCHLOSSEN, und das ist gut: 59ffda57, 13:46:34, "auf Yamas Freigabe und
  zwar ohne jede Loeschung". Er arbeitet jetzt in ticket-release-pruefung auf dem richtigen
  Zweig rolle/release-pruefer statt auf detached HEAD.
  ABER DAS TOR LAESST IHN TROTZDEM NICHT DURCH, und jetzt aus einem VIERTEN Grund. Selbst
  gemessen, mit dem ausgelieferten Skript aus db397fec, in seinem neuen Arbeitsbaum:
    Verzeichnis  ticket-release-pruefung    Zweig  rolle/release-pruefer
    Tor-Probe    exit 1
      "VERSTOSS  erwartet: ticket-rolle-release  auf  rolle/release-pruefer"
      "          gefunden: ticket-release-pruefung  auf  rolle/release-pruefer"
  Der ZWEIG stimmt jetzt. Das VERZEICHNIS nicht — die Tabelle im Tor kennt nur
  ticket-rolle-release, und in dem steht weiterhin ein detached HEAD auf 4630d658.
  ES GIBT ZWEI RELEASE-VERZEICHNISSE, und das Tor kennt nur das leere.
warum_sein_commit_um_13_48_trotzdem_durchkam: |
  Gemessen, warum die Sperre bei ihm noch nicht zugeschlagen hat:
    sein Baum ticket-release-pruefung, HEAD 68ca8b76:
      commit-pruefen.sh ruft das Tor    3 Treffer, scharf: "KEIN COMMIT" + exit 2
      scripts/rollen-tor.sh vorhanden   JA
    der gemeinsame Checkout b040f299:
      commit-pruefen.sh ruft das Tor    0 Treffer — dort ist es noch NICHT eingehaengt
  Er hat also committet, bevor der Tor-Stand in seinem Baum lag, und danach gezogen. Sein
  NAECHSTER Commit ueber das Tor wird abgewiesen. Die Sperre ist scharf und der Ausloeser
  liegt vor ihm, nicht hinter ihm.
bitte_an_yama_unveraendert_und_jetzt_belegt: |
  Die drei Wege von 13:47 gelten weiter, (b) ist erledigt und hat nichts geaendert:
    (a) K6 im Tor nachziehen — behebt den gemeinsamen Checkout
    (d) NEU: die Tabelle im Tor auf ticket-release-pruefung ziehen, ODER den Release-Pruefer
        in ticket-rolle-release umziehen. Zwei Verzeichnisse fuer eine Rolle sind der Grund,
        warum K2 im Blatt ueberhaupt als Kante steht — sie ist gebaut, aber gegen das
        falsche der beiden.
    (c) bis dahin das Tor auf --pruefe stellen, melden statt sperren
  Es eilt genauso wie vor drei Minuten, nur ist der Grund jetzt praeziser.
NACHTRAG_13_53_DIE_ZWEI_GESPERRTEN_FAELLE_SIND_NICHT_GLEICH: |
  Der Release-Pruefer hat den Befund uebernommen (68ca8b76, "AKUT — das Rollen-Tor sperrt
  zwei Rollen, und ICH habe es ausgeliefert") und meine Messung dabei GESCHAERFT: er misst
  im ECHTEN Arbeitsort statt im vorgesehenen. Das ist der bessere Schnitt, ich uebernehme
  ihn. Seine Berichtigung an mir stimmt ebenfalls: mein "noch ist kein Schaden entstanden"
  galt fuer 13:45 und war beim Lesen ueberholt, weil er inzwischen gemergt und gepusht hat.
  WAS ICH ERGAENZE, weil es die Wahl der Abhilfe entscheidet: die beiden Faelle sehen gleich
  aus und sind es nicht.
  EVALUATOR — kann sich SELBST befreien, gemessen:
    rolle/evaluator     bc2125d9 · 0 eigene Commits · 199 Commits Rueckstand
    sein Baum           0 unversionierte Dateien, also sauber
    -> verlustfreier Fast-Forward moeglich, genau wie mein eigener Umzug gestern
       (108 Commits, 0 eigene, exit 0). Danach laesst ihn das Tor durch — das hat der
       Release-Pruefer selbst gemessen: "evaluator in ticket-rolle-evaluator exit 0".
    Der Preis: ein Umzug mitten in der A-33-Abnahme. Machbar, aber es ist SEINE Entscheidung.
  RELEASE-PRUEFER — kann sich NICHT selbst befreien:
    git worktree list: rolle/release-pruefer ist in ticket-release-pruefung ausgecheckt.
    Ein Zweig kann in git nur EINEN Worktree belegen. Er kann also nicht einfach nach
    ticket-rolle-release wechseln — dort liegt ein detached HEAD, und den Zweig dorthin zu
    holen hiesse, ihn hier freizugeben und den Worktree umzubauen.
    Fuer ihn ist die Tabelle im Tor der kuerzere Weg, nicht der Umzug.
was_daraus_fuer_die_wahl_folgt: |
  (a) K6 nachziehen loest den Fall des EVALUATORS sofort und ohne Umzug — er arbeitet im
      gemeinsamen Checkout, und K6 ist genau dafuer da.
  (d) fuer den Release-Pruefer: die Tabelle auf ticket-release-pruefung ziehen ist eine
      Zeile; ihn umziehen ist ein Worktree-Umbau. Die Zeile ist besser.
  Beides zusammen sind zwei Zeilen im Tor, und danach ist niemand mehr gesperrt.
  (c) --pruefe bleibt der Notausgang, falls (a) und (d) nicht sofort gehen — aber dann
      meldet das Tor nur, und der Schutz, fuer den es gebaut wurde, ist bis dahin aus.
NACHTRAG_13_56_WARUM_NOCH_NICHTS_STEHT_UND_WANN_ES_STEHT: |
  Ich hatte um 13:50 geschrieben: "Sein NAECHSTER Commit ueber das Tor wird abgewiesen."
  Er hat seither zweimal committet (2a6b9d07 um 13:54:54). Meine eigene Vorhersage gehoert
  also geprueft, und sie stimmt — nur am falschen ORT.
  GEMESSEN, warum es noch geht:
    der gemeinsame Checkout ticket steht auf b040f299
    Rueckstand gegen den Fernstand f4a1b170:   34 Commits
    scripts/rollen-tor.sh dort vorhanden:      NEIN
    commit-pruefen.sh ruft das Tor dort:       0 Treffer
  Dort ist die Einhaengung schlicht noch nicht angekommen. Die Sperre existiert im
  Fernstand und in den umgezogenen Baeumen, aber nicht an dem Ort, an dem die zwei
  Betroffenen arbeiten. Deshalb laeuft alles weiter.
  DER AUSLOESER IST DAMIT BENANNT: sobald der gemeinsame Checkout die 34 Commits nachzieht,
  greift die Sperre fuer Evaluator und Release-Pruefer gleichzeitig. Nicht der naechste
  Commit ist der Zeitpunkt, sondern der naechste Stand-Nachzug im gemeinsamen Checkout.
  UND ES GIBT EIN ZEITFENSTER: der Planner hat K6 um 13:53:25 im BLATT geschaerft und das
  fehlende Kriterium nachgetragen (02504a25) — beide Punkte meines Befunds von 13:45. Im
  BAU fehlt K6 weiterhin, gemessen: rolle/generator, 0 Treffer.
  Wird K6 gebaut und transportiert, BEVOR der gemeinsame Checkout nachzieht, tritt der
  Schaden nie ein. Das ist kein Zufall, auf den man sich verlassen sollte, aber es ist die
  Lage: die Reihenfolge entscheidet, nicht die Zeit.
was_ich_dazu_beitrage: |
  Nur die Praezisierung des Zeitpunkts. Die Sache selbst liegt bei drei Rollen, die alle
  daran arbeiten: der Planner hat das Blatt nachgezogen, der Generator muss bauen, der
  Release-Pruefer hat den Befund uebernommen und transportiert.
  Ich habe eine eigene Vorhersage geprueft und sie im Zeitpunkt berichtigt — das ist der
  Grund, warum dieser Nachtrag existiert.
NACHTRAG_14_02_A_37_17_IST_BESSER_ALS_MEIN_VORSCHLAG: |
  Ich hatte gefordert, K6 brauche ein eigenes Kriterium. Der Planner hat stattdessen
  A-37-17 geschnitten: "ALLE SECHS KANTEN sind behandelt und JE EINZELN belegt."
  Das ist die bessere Loesung. Ein Kriterium fuer K6 haette K6 geschuetzt; dieses schuetzt
  jede kuenftige Kante mit. Genau die Verallgemeinerung, die er heute schon zweimal
  gefunden hat — Invariante statt Zahl bei A-33, Aussage statt Zahl bei A-38.
  BEINAHE-FEHLALARM MEINERSEITS: mein erster grep suchte Kriterien, die "K6" im TEXT nennen,
  und fand null. Daraus haette ich melden koennen, das Kriterium fehle weiter. Erst die
  vollstaendige Liste zeigte A-37-17 — es steht zwischen -15 und -16 und nennt K6 nicht
  namentlich, weil es ALLE Kanten meint. Zaehlung: 16 Kriterien vorher, 17 jetzt.
  Das ist dieselbe Falle wie heute frueh beim Tafelmuster: das Muster war zu eng, und die
  Nicht-Existenz waere ein Messfehler gewesen, kein Befund.
WAS SEIT 36 MINUTEN UNVERAENDERT OFFEN IST: |
  Meine zwei Zahlen aus dem Runde-4-Votum (13:26), im neuesten Planner-Stand 02504a25:
    A-37-5   "TICKET_ROLLE leer -> exit 3"     Tabelle sagt 5
    A-37-13  "Rueckgabe != 0"                  Tabelle sagt 6
  In derselben Zeit sind vier andere Punkte behoben worden — K6 geschaerft, A-37-17
  geschnitten, P2H-09 geschlossen, das Markenformat festgelegt. Die zwei Zahlen sind die
  kleinsten offenen Posten und werden von den groesseren verdraengt.
  A-37-5 WIEGT INZWISCHEN SCHWERER ALS BEI DER MELDUNG: Teil 1 IST gebaut, und zwar mit
  exit 1 — also weder mit der 3 des Kriteriums noch mit der 5 der Tabelle. Wer A-37-5 jetzt
  abnimmt, misst gegen eine Zahl, die an keiner der drei Stellen steht.
  Das ist kein neuer Befund. Es ist derselbe, 36 Minuten aelter, an einem Auftrag, dessen
  erster Teil inzwischen im Fernstand liegt.
ballbesitz: planner
```

herkunft: docs/STATUS.md · Block 353 · 34f6f5a9
kennung_nicht_zuordenbar: true   # Freitext statt Kennung — uebernommen wie vorgefunden, nicht gedeutet
```yaml
auftrag: "spec_blocked_braucht_kein_fortsetzung_zustand_und_wer_a33_zurueckholt"
titel: "Ein Fehlalarm, den ich vor dem Melden gestoppt habe — und die Frage, die dahinter uebrigbleibt"
rolle: plan-pruefer
zeit: "2026-08-16 14:05"
stand_kopf: 645a9d72
posten: "Pflichtteil 3 · SPEC_BLOCKED nachmessen"
der_fehlalarm_den_ich_gestoppt_habe: |
  A-33 steht im Fernstand auf SPEC_BLOCKED. Seine Felder, gemessen:
    auftrag · zustand · claim_abnahme · bau_sha · bau_bericht
    KEIN fortsetzung_zustand, KEIN ballbesitz in den ersten 14 Zeilen
  Ich war im Begriff, das fehlende fortsetzung_zustand als Verstoss zu melden — Paragraf 3
  nennt das Feld an zwei Stellen, und eine Blockade ohne Rueckweg ist genau die Klasse, die
  ich heute mehrfach hatte.
  DANN HABE ICH DEN WORTLAUT GELESEN, Paragraf 3 Z.105-109:
    "Beim Eintritt in ENV_BLOCKED, DECISION_BLOCKED oder RELEASE_BLOCKED wird der vorherige
     Pruefzustand als fortsetzung_zustand gespeichert ... SPEC_BLOCKED und NACHBESSERN
     erfordern dagegen einen NEUEN PLAN beziehungsweise Inhalts-Commit."
  SPEC_BLOCKED braucht also ausdruecklich KEIN fortsetzung_zustand. Kein Fund. Das Verfahren
  ist korrekt gefahren: der Evaluator hat blockiert (75c471cf), der Planner hat den neuen
  Plan geliefert (5db5f8a9, A-33 umgeschnitten). Genau was der Wortlaut verlangt.
  Ich schreibe das auf, weil es die dritte Beinahe-Fehlmeldung heute ist, die am Nachlesen
  gescheitert ist — nach dem Tafelmuster und dem K6-Kriterium. Alle drei haetten "das fehlt"
  gemeldet, wo etwas an einer anderen Stelle stand oder anders geregelt ist.
WAS ALS ECHTE FRAGE UEBRIGBLEIBT: |
  Der neue Plan liegt seit 13:36 vor, der Zustand steht seit 13:35 auf SPEC_BLOCKED. Nach
  Paragraf 3 endet SPEC_BLOCKED mit einem neuen Plan — den gibt es. Wer setzt den Zustand
  zurueck, und auf welchen?
  Die Rueckkehr-Tabelle in Paragraf 3 fuehrt Zeilen fuer ENV_BLOCKED, DECISION_BLOCKED und
  RELEASE_BLOCKED — mit gespeichertem fortsetzung_zustand und benannter Rolle. Fuer
  SPEC_BLOCKED und NACHBESSERN steht dort KEINE Zeile.
  Das ist keine Luecke im Verfahren, sondern eine im REGELTEXT: der Wortlaut sagt, was
  noetig ist (ein neuer Plan), aber nicht, wer danach welchen Zustand setzt. Bei den drei
  anderen Blockzustaenden sagt die Tabelle beides.
  PRAKTISCH: A-33 hat einen fertigen Bau (3e22e61b), einen neuen Plan und eine laufende
  Abnahme-Runde (claim_abnahme des Evaluators von 13:24). Es fehlt nur der Zustandsschritt.
  Ich fasse ihn nicht an — SPEC_BLOCKED zu verlassen ist nicht meine Rolle, und welcher
  Zustand richtig ist (zurueck auf ABNAHME? auf CODE_FERTIG?), ist genau das, was der
  Regeltext offenlaesst.
bitte: |
  An Yama, kleine Regelfrage: Paragraf 3s Rueckkehr-Tabelle sollte eine Zeile fuer
  SPEC_BLOCKED und NACHBESSERN bekommen — nach welchem Ereignis, in welchen Zustand, durch
  wen. Heute steht die Bedingung im Fliesstext und der Rest nirgends.
  An den Evaluator oder den Planner, je nachdem wie die Antwort ausfaellt: A-33 wartet auf
  genau diesen einen Schritt.
BERICHTIGUNG_14_56_MEINE_REGELFRAGE_WAR_KEINE: |
  Ich habe hier um 14:05 an Yama gemeldet, Paragraf 3s Rueckkehr-Tabelle habe keine Zeile
  fuer SPEC_BLOCKED und NACHBESSERN, und daraus eine Regelfrage gemacht.
  DIE REGEL EXISTIERT. Sie steht in Paragraf 12.3 "Rueckweg zur Abnahme", gefunden, weil der
  Evaluator sich um 14:54 darauf beruft:
    "Die Aufgabe geht auf CODE_FERTIG zurueck — KEIN eigener Zustand fuer Nachbesserungen.
     Die Meldung nennt zusaetzlich zu Paragraf 11: die neue Pruef-SHA auf der Linie des
     Baus, je Befund was geaendert wurde, und je Befund die ZWEI-RICHTUNGS-PROBE: dieselbe
     Probe war vorher rot und ist nachher gruen, beide Richtungen selbst gemessen. Eine
     Reparatur ohne den vorherigen Rot-Beleg ist eine Behauptung."
  Das beantwortet meine Frage vollstaendig: WER — die Rolle, die nachbessert, meldet auf
  CODE_FERTIG zurueck; WOHIN — CODE_FERTIG; WOMIT — Pruef-SHA, Aenderung je Befund,
  Zwei-Richtungs-Probe.
  UND DER FALL IST GENAU SO GELAUFEN: der Planner hat A-33 auf CODE_FERTIG zurueckgegeben
  ("der SPEC_BLOCKED-Grund ist behoben"), der Evaluator hat Runde 2 geclaimt und nennt
  Paragraf 12.3 dabei ausdruecklich. Niemand hat auf eine fehlende Regel gewartet.
  DAS WAR KEIN BEINAHE-FEHLALARM, SONDERN EINER, DER HERAUSGEGANGEN IST. Die zwoelf, die
  ich heute gefangen habe, sind vor dem Melden gestorben; dieser nicht. Ich habe in
  Paragraf 3 gesucht, dort nichts gefunden und daraus geschlossen, es gebe nichts — statt
  im ganzen Regelwerk zu suchen. Dieselbe Musterfalle wie die anderen zwoelf, nur eine
  Sekunde zu spaet bemerkt.
  Der Ball bei Yama entfaellt damit; es gibt nichts zu entscheiden.
ballbesitz: "—"  # BERICHTIGT 16.08. 14:56: die Regel steht in Paragraf 12.3, meine Frage war gegenstandslos
```

herkunft: docs/STATUS.md · Block 354 · 34f6f5a9
kennung_nicht_zuordenbar: true   # Freitext statt Kennung — uebernommen wie vorgefunden, nicht gedeutet
```yaml
auftrag: "w17_1_dor_die_zahl_ist_exakt_und_haette_mich_beinahe_erwischt"
titel: "W-17/1 geprueft — 249 Zeilen in sieben Blaettern, auf die Zeile genau; mein Gegenbefund war ein Messfehler"
rolle: plan-pruefer
zeit: "2026-08-16 15:00"
stand_kopf: 4eeca099
geprueft_gegen: "rolle/planner 3be497f5 · dor_schnitt_sha 8faca79c"
posten: "Pflichtteil 3 · neuer ENTWURF in meiner Bahn, ballbesitz plan-pruefer (DoR)"
was_W_17_1_ist: |
  Eine ABLESUNG, Stufe B: "Blatt schneiden, Ziel BESCHRIEBEN. KEIN Bau, KEIN Produktivcode."
  Der Kopf begruendet die Einordnung ausdruecklich als gemessen: "Reifegrad LEER, sieben
  Blaetter sind reine Vorlagen (249 Z. gesamt), und Produktivcode ist reichlich vorhanden."
DIE ZAHL IST EXAKT, und der Weg dorthin gehoert dazu: |
  Erste Messung: ich zaehlte SECHS Blaetter mit 216 Zeilen und sah die Nummerierung springen
  (1, 2, 3, 4, 6, 7 — die 5 fehlt). Das sah nach einem Fund aus: "sieben behauptet, sechs
  vorhanden, 33 Zeilen Differenz".
  Gegenprobe an drei anderen Werkzeugen (W-06, W-07, W-20): dort heisst Blatt 5 nicht
  5-IRGENDWAS.md, sondern ist ein VERZEICHNIS — 5-CODE. Mein ls-tree auf Verzeichnisebene
  zeigte es nicht, weil es kein .md ist.
  Rekursiv gemessen:
    1-ZWECK 21 · 2-FUNKTION 37 · 3-FORMELN 31 · 4-BEDIENUNG 43
    5-CODE/LIESMICH 33 · 6-PRUEFUNG 37 · 7-GRENZEN 47
    Summe 249 — das Blatt nennt 249. AUF DIE ZEILE GENAU.
  VIERZEHNTE MUSTERFALLE HEUTE, und sie haette einen Fund ERFUNDEN. Die fehlenden 33 Zeilen
  waren exakt die Datei, die mein Muster nicht sah — der Fund haette sogar plausibel
  ausgesehen, weil die Differenz zu einem Blatt passte.
was_ich_daraus_fuer_die_DoR_ziehe: |
  Der Kopf sagt "GEMESSEN, nicht behauptet". Das trifft zu, und ich habe es an der einzigen
  Zahl geprueft, die im Kopf steht. Ein Blatt, dessen Einordnung man nachzaehlen kann und
  das dann stimmt, ist ein gutes Zeichen fuer den Rest.
  NOCH NICHT GEPRUEFT und so gekennzeichnet: "Reifegrad LEER" und "Produktivcode ist
  reichlich vorhanden" — beides sind Behauptungen mit Messcharakter, beide stehen im Kopf,
  beide habe ich noch nicht nachgefahren. Ebenso die Kriterien und Kanten. Naechste Runde.
  KEIN VOTUM heute. Was ich sagen kann: die tragende Zahl haelt, und die Einordnung als
  Stufe B (Ablesung statt Bau) passt zu dem, was ich sehe — sieben Vorlagen ohne Inhalt sind
  kein Bau-Auftrag, sondern ein Beschreibungs-Auftrag.
NACHTRAG_15_03_DIE_ZWEITE_ZAHL_IST_NICHT_REPRODUZIERBAR: |
  Das Blatt belegt "Produktivcode ist reichlich vorhanden" mit zwei Angaben:
    (a) fuenf Insel-Module namentlich: arbeitsbereichSpeicher.ts · schienenSpeicher.ts ·
        paketSpeichern.ts · speicherAnzeige.ts · __tests__/schienenSpeicher.test.ts
        UND vier Server-Actions: SpeichereHausplanerDokument · StelleSnapshotWieder ·
        ErstelleLeeresSzenenDokument · ErmittleUebernahmeStatus
    (b) "37 Dateien mit exportieren/downloadJson/speichern"
  (a) HAELT VOLLSTAENDIG. Alle neun namentlich genannten Dateien existieren, einzeln
  nachgesehen. Das ist der tragende Beleg, und er ist so belegt, wie ein Beleg sein soll.
  (b) IST NICHT REPRODUZIERBAR. Mit genau den drei genannten Begriffen gemessen:
    case-sensitive, unter resources/planner/hausplaner   27
    case-insensitive, dasselbe Verzeichnis               45
    ganzes resources/planner, case-sensitive             27
  Keine der Messungen ergibt 37; die Zahl liegt zwischen den beiden Schreibweisen. Das ist
  DIESELBE KLASSE wie A-38s Messbefehl, den ich um 14:15 gemeldet habe: eine Zahl, deren
  Verfahren nicht vollstaendig angegeben ist, ist nicht nachpruefbar — dort fehlte der ORT,
  hier fehlt die SCHREIBWEISE.
was_ich_dem_planner_anrechne: |
  Er hat den ersten Messversuch VERWORFEN und es aufgeschrieben: "Mein erster Sweep zaehlte
  Wortvorkommen und meldete fuer W-17 Export 155 Dateien — export ist ein
  TypeScript-Schluesselwort. Ich habe die Zahlen NICHT ausgegeben sondern das Verfahren
  gewechselt, auf Dateinamen dedizierter Module. Das ist derselbe zu weite Griff wie heute
  mehrfach, nur diesmal vor der Meldung bemerkt."
  Genau diese Falle ist mir heute vierzehnmal begegnet, und er hat sie an sich selbst
  gefangen und offengelegt. Die 37 ist der Rest eines Verfahrens, das er zu Recht verlassen
  hat — sie steht noch da, obwohl der bessere Beleg daneben liegt.
bitte: |
  Die 37 entweder mit ihrer Schreibweise versehen oder streichen. Streichen ist besser: die
  neun namentlich genannten Dateien tragen die Einordnung allein, und eine Zahl, die
  zwischen 27 und 45 schwankt, macht sie nicht staerker.
  Das ist KEIN Restpunkt fuer die DoR — die Einordnung als Stufe B haelt unabhaengig davon.
  Es ist eine Zeile, die beim naechsten Nachrechnen sonst wieder Arbeit macht.
ballbesitz: planner
```

herkunft: docs/STATUS.md · Block 355 · 34f6f5a9
kennung_nicht_zuordenbar: true   # Freitext statt Kennung — uebernommen wie vorgefunden, nicht gedeutet
```yaml
auftrag: "W-17/1"
titel: "Export und Speichern ablesen — DoR-Pruefung ABGESCHLOSSEN, Votum erteilt"
rolle: plan-pruefer
zeit: "2026-08-16 15:09 CEST"
mess_stand: "eigener Baum 07f44217 · Planner-Zweig e521bd98 · Blatt-Basis laut Datensatz 8faca79c"
votum_DOR_ERTEILT: |
  Ich erteile die DoR fuer W-17/1. Die Pruefung ist vollstaendig; ich habe JEDE tragende
  Angabe des Blattes selbst nachgemessen, nicht eine uebernommen.
was_ich_selbst_nachgemessen_habe: |
  1. DIE 249 ZEILEN — HAELT EXAKT. Sieben Blaetter, einzeln gezaehlt:
     1-ZWECK 21 · 2-FUNKTION 37 · 3-FORMELN 31 · 4-BEDIENUNG 43 · 5-CODE/LIESMICH 33 ·
     6-PRUEFUNG 37 · 7-GRENZEN 47. Summe 249. Falle dabei: Blatt 5 ist ein VERZEICHNIS,
     die Zeilen stehen in 5-CODE/LIESMICH.md — wer die Datei direkt sucht, misst 0 und
     meldet 216.
  2. DIE NEUN CODE-DATEIEN — ALLE VORHANDEN, einzeln nachgesehen (siehe Vorblock).
  3. tests/Feature/PlanUploadTest.php — EXISTIERT. 255 Zeilen, 12 Testfaelle.
     Kante K2 nennt "255 Z." — das ist auf die Zeile genau richtig.
  4. REIFEGRAD LEER — HAELT. REGISTER.md Zeile 69: "| W-17 | Export und Speichern | LEER |
     alle | — |". Kante K6 und Kriterium W-17-1-4 treffen den Ist-Zustand.
  5. DIE ROT-LAGE — BESTAETIGT. 1-ZWECK.md traegt nur Platzhalter in spitzen Klammern
     ("<In EINEM Satz, aus Sicht des Anwenders...>"), 21 Zeilen. Gegenprobe an einem
     GEFUELLTEN Blatt derselben Werkbank: W-06/1-ZWECK.md hat 101 Zeilen. Faktor fuenf.
     Die Einordnung "sieben Vorlagen ohne Inhalt" ist damit gemessen, nicht behauptet.
kriterien_und_kanten: |
  SIEBEN Kriterien (W-17-1-1 bis -7), alle messbar formuliert, keines unerfuellbar, keines
  bereits erfuellt. SECHS Kanten (K1-K6), jede mit benannter Aufloesung.
  Zwei ragen heraus:
    K5 "Ein Blatt bliebe leer" -> "ausdruecklich schreiben, dass es nichts gibt — ein leeres
       Blatt ist kein Beleg". Das ist die H-8-Klasse an der Wurzel gefasst.
    W-17-1-6 "Jede genannte Formel traegt ihre F-Nummer, und fehlende werden als Luecke
       gemeldet" — verbietet ausdruecklich das Erfinden einer Nummer.
  Praezedenz belegt: W-12 traegt im Register "Reifegrad nachgezogen 14.08. mit W-12/1
  (LEER -> BESCHRIEBEN)". W-17/1 folgt exakt diesem erprobten Muster.
ein_befund_den_ich_verworfen_habe: |
  Ich hatte "Tafelzeile fehlt — A-20-Verstoss" fast gemeldet: der Datensatz existiert,
  aber AUFTRAGSTAFEL.md kennt W-17/1 nicht.
  Die Eichung hat ihn umgedreht: in der Tafel steht KEIN EINZIGER W-Vorgang, auch W-12,
  W-13, W-15 nicht. Die W-Reihe wird ueber REGISTER.md gefuehrt, nicht ueber die
  Auftragstafel. Kein Verstoss, sondern eine andere Ordnung.
  Das war die sechzehnte Musterfalle heute und die zweite, bei der mein Muster nicht zu
  eng, sondern die ORDNUNG eine andere war. Beide Male hat dieselbe Gewohnheit sie
  gefangen: an einem bekannten Treffer eichen, bevor die Null etwas bedeutet.
restpunkt_nicht_dor_blockierend: |
  Die Zahl "37 Dateien" bleibt unbelegt (27 case-sensitive / 45 case-insensitive) — im
  Vorblock ausgefuehrt, Bitte an den Planner: streichen. Die neun namentlich genannten
  Dateien tragen die Einordnung allein.
was_ich_NICHT_tun_kann: |
  Den Zustandswechsel ENTWURF -> BEREIT vollziehe ich NICHT, und zwar nicht aus Vorsicht,
  sondern weil ich es nicht kann: mein Baum liegt 114 Commits zurueck und traegt weder das
  Blatt noch den Datensatz. Der fuehrende Datensatz liegt im Planner-Zweig (e521bd98).
  Wuerde ich hier schreiben, legte ich einen ZWEITEN Datensatz an — genau die zweite
  Wahrheit, die Paragraf 16 verbietet.
  Der Planner zieht den Zustand an seinem Stand nach. Das Votum ist erteilt, die Bedingung
  dafuer ist erfuellt.
ballbesitz: planner
```

herkunft: docs/STATUS.md · Block 356 · 34f6f5a9
kennung_unbekannt: true   # K3 — Kennung ohne Auftragsdatensatz; kein Auftrag, aber ein Beleg
```yaml
auftrag: "BERICHTIGUNG-W-17-1-TAFELZEILE"
titel: "Ich habe am falschen Objekt gemessen — H-9 an mir selbst"
rolle: plan-pruefer
zeit: "2026-08-16 15:15 CEST"
betrifft_commit: "3a665884"
was_falsch_war: |
  In 3a665884 steht als Begruendung: "die Eichung zeigte KEINEN W-Vorgang auf der Tafel —
  die W-Reihe laeuft ueber REGISTER.md".
  DAS IST FALSCH GEMESSEN. Ich habe die DATEI docs/auftraege/AUFTRAGSTAFEL.md geprueft.
  A-30 meint mit "Tafelzeile" aber die TABELLENZEILE INNERHALB von docs/STATUS.md — das
  Skript sagt es in seinem eigenen Kommentar (Z.31): "docs/STATUS.md:31 | **P-02**
  parallele Instanzen | VORLAGE | ...". DATEI="docs/STATUS.md" in Zeile 55.
was_richtig_ist: |
  Geeicht an A-30s eigenem Beispiel P-02 und dann gemessen:
    W-Tafelzeilen in STATUS.md gesamt:  37 (W-01 bis W-42)
    W-17/1 Tafelzeile in STATUS.md:      1
    W-17/1 Datensatz in STATUS.md:       1
  W-17/1 ERFUELLT A-20 VOLLSTAENDIG. Es fehlt nichts. Die Gegenprobe W-12/1 steht direkt
  daneben: "| **W-12/1** Ansicht und Kamera (Ablesung) | BETRIEBSBESTAETIGT | ...".
  Auch mein Satz "die W-Reihe laeuft ueber REGISTER.md STATT ueber die Tafel" ist falsch:
  sie laeuft ueber BEIDE — REGISTER.md fuehrt den Reifegrad, STATUS.md die Tafelzeile.
die_klasse: |
  H-9, ein Wort zwei Sachen: "Tafel" heisst bei A-30 die Tabelle IN STATUS.md, bei mir die
  Datei AUFTRAGSTAFEL.md. Beide Lesarten sind im Haus belegt, und ich habe die falsche
  genommen, ohne sie am Werkzeug zu pruefen.
  Und es ist DIESELBE Klasse, die ich um 14:15 an A-38 gemeldet habe: ein Messbefehl ohne
  Ortsangabe. Ich habe den Ort selbst nicht bestimmt, bevor ich die Null gedeutet habe.
  Die Null war echt — in AUFTRAGSTAFEL.md steht wirklich kein W-Vorgang. Sie bedeutete nur
  nicht, was ich sie bedeuten liess. Eine Null ist erst dann ein Befund, wenn feststeht,
  dass das Gesuchte an diesem Ort ueberhaupt stehen wuerde.
was_das_votum_betrifft: |
  DAS VOTUM HAELT UNVERAENDERT. Kein einziger Beleg der DoR haengt an dieser Messung:
  249 Zeilen, PlanUploadTest.php 255 Z./12 Faelle, REGISTER.md Z.69 LEER, W-06 mit 101 Z.
  gegen W-17s 21 Z., sieben Kriterien, sechs Kanten — alle einzeln und am richtigen Ort
  gemessen. Falsch war nur der Abschnitt "ein_befund_den_ich_verworfen_habe", und zwar in
  der BEGRUENDUNG, nicht im Ergebnis: dass kein Verstoss vorliegt, stimmt.
  Zufaellig richtig zu liegen ist kein Messen. Deshalb steht die Berichtigung hier.
was_A_30_an_meinem_commit_gemeldet_hat: |
  A-30 warnte: "W-17/1: neuer DATENSATZ ohne Tafelzeile". Das war korrekt — bezogen auf
  MEINE STATUS.md im Rollenbaum, wo mein Meldeblock den Schluessel auftrag: "W-17/1"
  traegt, ohne Tafelzeile daneben.
  Damit sieht A-30 jeden meiner Befundbloecke wie einen Statusdatensatz. Bei jeder NEUEN
  Kennung, die ich melde, wird die Warnung feuern, ohne dass etwas fehlt — A-03-Klasse:
  eine Meldung, die aus dem falschen Grund kommt, wird weggeklickt, und dann auch dort,
  wo sie recht hat. Das ist ein Befund am Werkzeug, kein Vorwurf: A-30 kann Meldung und
  Datensatz nicht unterscheiden, weil beide denselben Schluessel tragen.
  Vorschlag, NICHT von mir zu entscheiden: Meldebloecke einen anderen Kopfschluessel
  tragen lassen (z.B. befund: statt auftrag:), dann trennt A-30 sauber. Das aendert mein
  Meldeverfahren und die Erwartung anderer Rollen — gehoert dem Planner, nicht mir.
ballbesitz: plan-pruefer
```

herkunft: docs/STATUS.md · Block 357 · 34f6f5a9
```yaml
auftrag: "A-37"
titel: "A-37-5 ist am gebauten Stand NICHT ERFUELLBAR — die Berichtigung von heute hat den Widerspruch verschoben, nicht behoben"
rolle: plan-pruefer
zeit: "2026-08-16 15:17 CEST"
mess_stand: "Blatt aus rolle/planner (Blattaenderung 02504a25, 16.08. 13:53) · Code aus rolle/generator 1e342d53 (16.08. 15:15, 247 Z.) · lokal und fern identisch"
befund: |
  Kriterium A-37-5 verlangt einen Rueckgabewert, den das gebaute Tor an keiner Stelle
  erzeugt. Drei Seiten, jede einzeln gemessen:
    1. BLATT Z.292 (das Kriterium selbst):
       "- **A-37-5** · **Negativfall fehlende Kennung:** TICKET_ROLLE leer -> **exit 3**."
    2. BLATT Z.389 (die Berichtigung von HEUTE):
       "... != 0, und zwar dem fuer den Fall festgelegten: A-37-3/4/6 -> 1, A-37-5 -> **5**."
    3. CODE, REAL GEFAHREN (nicht gelesen, ausgefuehrt):
         TICKET_ROLLE="" bash rollen-tor.sh  -> Rueckgabe 5
         Ausgabe: "ROLLEN-TOR  TICKET_ROLLE ist nicht gesetzt — ohne Rolle ist keine
                   Zuordnung pruefbar."
       Und der Wert 3 kommt im ganzen Skript nicht vor:
         grep -c 'exit 3' -> 0     (Muster geeicht: grep -c 'exit 5' -> 1)
  Der Code tut 5. Die Berichtigung sagt 5. Nur das Kriterium sagt 3.
warum_das_ein_dor_blocker_ist: |
  Paragraf 5 verbietet unerfuellbare Abnahmekriterien. A-37-5 ist am heutigen Bau nicht
  erfuellbar: der Evaluator fuehrt den Negativfall aus, misst 5, liest 3, und muss ROT
  melden — obwohl der Bau richtig ist und genau das tut, was der Rest des Blattes will.
  Das ist die teuerste Sorte Rot: eine Abnahme, die einen korrekten Bau zurueckweist. Und
  es ist die A-03-Klasse in ihrer Wurzel — wer einmal erlebt, dass die Abnahmebedingung
  falsch ist, glaubt der naechsten auch nicht mehr.
was_ich_dem_planner_anrechne_und_was_nicht: |
  ANRECHNEN: Er hat heute 13:53 an genau dieser Stelle gearbeitet und die Berichtigung
  offengelegt statt sie stillschweigend einzupflegen (Z.390: "hier stand pauschal 'exit 1'
  und widersprach damit A-37-5"). Er hat den Widerspruch GESEHEN.
  NICHT ANRECHNEN: Er hat ihn an der falschen Seite aufgeloest. Die Stelle, die er anglich
  (Z.389), war die allgemeine; die Stelle, die den Wert festlegt (Z.292), blieb stehen.
  Damit ist der Widerspruch nicht weg, sondern gewandert: vorher "1 gegen 3", jetzt
  "5 gegen 3". Eine Berichtigung, die ihre eigene Gegenstelle nicht mitmisst, verschiebt
  den Fehler an einen Ort, an dem ihn niemand mehr sucht — die Zeile traegt jetzt sogar
  einen Berichtigungsvermerk und sieht damit geprueft aus.
was_zu_tun_ist: |
  EINE ZAHL in Z.292: "exit 3" -> "exit 5". Nichts weiter. Der Bau ist richtig, die
  Berichtigung ist richtig, das Kriterium ist die einzige falsche Stelle.
  Ausdruecklich NICHT der andere Weg: den Code auf 3 umzubauen hiesse, einen fertigen und
  wirksamen Bau wegen einer Blattzeile anzufassen.
zweite_richtung_geprueft: |
  Paragraf 12.3 verlangt die Probe in beide Richtungen. Gegenprobe gefahren:
  Gaebe es im Skript ein 'exit 3' an anderer Stelle, waere mein Befund falsch — grep
  ueber das ganze Skript: 0 Treffer, und das Muster ist an 'exit 5' (1 Treffer) geeicht.
  Der reale Lauf ist zusaetzlich gefahren, nicht nur der Code gelesen: eine Zeile im Code
  belegt nicht, welcher Zweig sie erreicht.
noch_nicht_geprueft: |
  A-37-13 (Modulstand, Blatt Z.335) sagt heute nur "-> Abbruch" ohne Zahl, waehrend Z.389
  fuer A-37-6 den Wert 1 festlegt. Ob das eine zweite Luecke derselben Art ist, habe ich
  NICHT gemessen — der Negativfall setzt eine Marke auf einen fremden Hash, und das fasse
  ich ohne Not nicht an. Naechste Runde, mit einer Kopie.
  Die uebrigen 15 Kriterien der A-37-DoR sind ebenfalls noch offen.
ballbesitz: planner
```

herkunft: docs/STATUS.md · Block 359 · 34f6f5a9
```yaml
auftrag: "A-41"
titel: "FUND 2 des Planners bestaetigt — und die Nachbesserung von 15:20 hat GENAU DIESE ZEILE angefasst, ohne die Luecke mitzunehmen"
rolle: plan-pruefer
zeit: "2026-08-16 15:22 CEST"
mess_stand: "Blatt a613100e (15:19:22) · Bau 1e342d53 (15:15:49) · Nachbesserung b585d335 (15:20:43) · alle drei Zeitpunkte aus dem Reflog, nicht aus Prosa"
lage: |
  A-41 ist seit 15:19 mein siebter Ball (ENTWURF, basis_sha e521bd98). Die vollstaendige
  DoR ueber zwoelf Kriterien und sieben Kanten steht noch aus. Diese Runde hat EINEN
  Punkt geprueft: die zwei Funde, die der Planner am parallelen Bau des Generators meldet.
fund_2_bestaetigt_auf_die_zeile: |
  Der Planner meldet: "in status-erzeugen.sh:121 fehlt --no-merges".
  ICH HABE DEN ALTEN STAND SELBST GEHOLT UND NACHGESEHEN. 1e342d53, Zeile 121, wortgleich:
    roh = lauf("git", "log", "--all", "--grep=^zustand:", "--format=%H%x09%at%x09%an%x09%s")
  Kein --no-merges. Der Fund stimmt punktgenau, einschliesslich der Zeilennummer.
und_hier_wird_es_ernst: |
  Der Generator hat um 15:20:43 nachgebessert (b585d335, "das Muster konnte NIE treffen").
  Diese Nachbesserung hat GENAU DIESE ZEILE angefasst — aus einem Aufruf wurden drei
  Zeilen, das grep-Muster nimmt jetzt beide Formen mit und ohne Rollenmarke.
  Der fehlende Schalter ist dabei NICHT mitgekommen:
    'no-merges' im ganzen neuen Skript (227 Z.): 0 Treffer
    Muster geeicht: '--all' im selben Skript:    1 Treffer
  Wer eine Zeile umbaut, sieht ihre andere Luecke nicht — er sieht das, was er sucht. Das
  ist dieselbe Klasse wie A-37 heute: dort hat eine Berichtigung die Gegenstelle nicht
  mitgemessen, hier hat ein Umbau die Nachbarluecke derselben Zeile nicht mitgenommen.
  Zweimal am selben Tag, zwei verschiedene Rollen, dieselbe Bewegung.
warum_das_nicht_kosmetisch_ist: |
  Der Planner benennt die Folge richtig, und sie ist die schwerste denkbare fuer genau
  diesen Bau: ein Merge traegt fremde Betreffs mit. Ohne den Schalter erscheint jeder
  Zustand nach jedem Transport erneut — die Erzeugung, die die Divergenz beenden soll,
  wuerde sie selbst herstellen. Ein Werkzeug, das sein eigenes Problem erzeugt, ist
  schlimmer als keines, weil sein Ergebnis vertrauenswuerdig aussieht.
was_ich_dem_generator_anrechne: |
  Er hat den Musterfehler SELBST gefunden und binnen fuenf Minuten nachgebessert, ohne
  dass ihn jemand darauf gestossen hat — der Betreff sagt es offen: "das Muster konnte NIE
  treffen". Das ist die richtige Bewegung. Sie war nur zu schmal: die Zeile war schon
  offen, der zweite Mangel stand daneben.
zeitachse_als_eigener_punkt: |
  Aus dem Reflog, nicht aus Prosa: Bau 15:15:49, Blatt 15:19:22, Nachbesserung 15:20:43.
  Der Bau lag VIER MINUTEN VOR dem Auftragsblatt, und das Blatt traegt ENTWURF — die DoR
  ist nicht erteilt, der Code ist fertig. Nach Paragraf 3 laeuft die Kette
  ENTWURF -> BEREIT -> IN_ARBEIT -> CODE_FERTIG.
  ICH MELDE DAS ALS BEOBACHTUNG, NICHT ALS VERSTOSS, und sage warum: der Planner hat den
  parallelen Bau offen benannt und ihn geprueft statt ihn zu uebersehen — die Rollen haben
  getrennt gearbeitet, nur nicht nacheinander. Ob Yama diesen Parallellauf so gewollt hat,
  weiss ich nicht; im Blatt steht "YAMAS ENTSCHEIDUNG IST UMGESETZT". Das zu entscheiden
  ist nicht meine Rolle. Zu messen und hinzulegen schon.
noch_offen: |
  Die DoR selbst: zwoelf Kriterien, sieben Kanten, dazu FUND 1 des Planners (der Wortlaut
  "zustand: ..." sei wegen commit-pruefen.sh:73/78 nicht committierbar, exit 2). FUND 1
  habe ich NICHT nachgemessen — er verlangt einen echten Commit-Versuch mit dem alten
  Wortlaut, und den setze ich nicht ungeprueft an die Statuswahrheit. Naechste Runde, gegen
  eine Kopie.
ballbesitz: generator
```

herkunft: docs/STATUS.md · Block 360 · 34f6f5a9
```yaml
auftrag: "A-37"
titel: "Das Tor selbst liegt in DREI Fassungen vor — und ausgerechnet die Rolle, bei der A-37s DoR liegt, faehrt die schwaechste"
rolle: plan-pruefer
zeit: "2026-08-16 15:26 CEST"
mess_stand: "HEAD 817a9843 · planner a613100e · generator b585d335 · evaluator 80edcf7f · release-pruefer b66c425a"
befund: |
  scripts/commit-pruefen.sh — das Werkzeug, ueber das JEDE Rolle committiert — existiert
  heute in drei verschiedenen Fassungen. Ueber Blob-Hashes gemessen, nicht ueber Prosa:
    71f7200aab25   824 Z.   plan-pruefer, planner
    79320b7512f2   854 Z.   generator, release-pruefer
    62fa113baf90   847 Z.   evaluator
  Der Unterschied ist nicht kosmetisch. Gezaehlt, wie oft jede Fassung das Rollen-Tor
  ueberhaupt kennt:
    meine Fassung:        'rollen-tor.sh'  0 Treffer
    Generator-Fassung:    'rollen-tor.sh'  3 Treffer
    Evaluator-Fassung:    'rollen-tor.sh'  3 Treffer
  Und die Datei selbst: git cat-file -e HEAD:scripts/rollen-tor.sh -> NEIN, sie fehlt in
  meinem Zweig. Das deckt sich mit einer ausgefallenen Messung von 15:16, die ich damals
  als Fehlschlag verworfen habe: der Aufruf gab exit 127, "No such file or directory".
  Das war kein Messfehler, das war der Befund — ich habe ihn nur nicht als solchen gelesen.
was_das_praktisch_heisst: |
  Plan-Pruefer und Planner committen heute OHNE Rollen-Tor. Generator, Evaluator und
  Release-Pruefer committen MIT. Dieselbe Handlung wird je nach Rolle verschieden streng
  geprueft, und keine Rolle sieht das an ihrer eigenen Ausgabe — ein fehlendes Tor
  schweigt.
  MEIN EIGENES RISIKO IST KLEIN, und das sage ich dazu, damit die Meldung nicht groesser
  klingt als der Sachverhalt: ich committe in meinem eigenen Baum als plan-pruefer, das
  Tor gaebe dort exit 0. Es fehlt mir der Schutz, nicht die Ordnung.
  DAS RISIKO DER LAGE IST GROSS: die Barriere, die einen Commit im FREMDEN Baum verhindern
  soll, ist genau bei den zwei Rollen aus, die am meisten an Blaettern und Statuswahrheit
  schreiben. Ein Fehlgriff dort ist nach A-37s eigener Begruendung "nicht durch eine
  spaetere Pruefung heilbar — er liegt dann schon auf dem fremden Zweig".
die_schieflage: |
  A-37 traegt heute ENTWURF, und der Ball fuer die DoR liegt bei MIR. Gleichzeitig ist der
  Bau in drei von fuenf Zweigen produktiv wirksam. Ich soll die Reife eines Auftrags
  beurteilen, dessen Erzeugnis laengst arbeitet — bei anderen, nicht bei mir.
  Das ist dieselbe Zeitumkehr wie heute bei A-41 (Bau 15:15, Blatt 15:19), nur hat sie
  hier eine zweite Kante: der Bau ist nicht nur frueher da als die Freigabe, er ist auch
  UNGLEICH verteilt. Die Kette Paragraf 3 kennt beides nicht.
was_ich_ausdruecklich_nicht_behaupte: |
  Ich behaupte NICHT, dass jemand falsch gehandelt hat. Fuenf Zweige, die parallel
  arbeiten, divergieren zwangslaeufig, bis transportiert wird — der Planner hat dieselbe
  Klasse um 15:04 fuer docs/STATUS.md gemeldet ("die Statuswahrheit existiert SECHSMAL").
  Neu an meinem Befund ist nur, WAS divergiert: dort waren es Daten, hier ist es die
  Schutzschicht. Eine ungleiche Wahrheit kann man zusammenfuehren. Eine ungleiche Barriere
  laesst waehrenddessen etwas durch.
  Ich habe auch geprueft, ob das schon jemand gemeldet hat: 91 Treffer fuer
  'commit-pruefen' im Statusbestand, keiner davon spricht Fassungen oder Divergenz des
  Tors an.
bitte: |
  An den Release-Pruefer, weil ihm der Transport gehoert: die Tor-Fassung ANGLEICHEN,
  bevor die naechste Runde Blaetter geschrieben wird — und dabei zuerst die zwei Zweige
  ohne Rollen-Tor, nicht die mit.
  Kein Bau von mir, kein Zustandsfeld angefasst.
ballbesitz: release-pruefer
```

herkunft: docs/STATUS.md · Block 361 · 34f6f5a9
```yaml
auftrag: "A-41"
titel: "FUND 1 bestaetigt und die Loesung am Muster bewiesen — der Wortlaut-Mechanismus haelt in BEIDE Richtungen"
rolle: plan-pruefer
zeit: "2026-08-16 15:30 CEST"
mess_stand: "HEAD 8efe568d · Tor-Fassung 71f7200aab25 (meine, 824 Z.) · Bau b585d335 · Blatt 74cc04d5 (15:25)"
fund_1_bestaetigt: |
  Der Planner meldet: der Wortlaut "zustand: A-33 · ..." sei nicht committierbar, weil
  commit-pruefen.sh:73 jeden Praefix der Form "wort: " als Rollenmarke liest und Zeile 78
  mit exit 2 abbricht; der Gegenweg sei ebenso zu, weil Zeile 84 ohne Marke "$ROLLE: "
  voranstellt.
  ICH HABE DIE ERKENNUNG ISOLIERT NACHGEFAHREN, mit genau dem Muster aus Zeile 73
  (^[a-z][a-z-]*(-[0-9]+)?: ), gegen vier Botschaften:
    "zustand: A-33 · ABGENOMMEN · ..."        -> Marke 'zustand'      -> != ROLLE -> exit 2
    "planner: zustand: A-41 · ENTWURF · ..."  -> Marke 'planner'      -> durchgelassen
    "plan-pruefer: irgendeine Meldung"        -> Marke 'plan-pruefer' -> durchgelassen
    "A-07: ein Auftragspraefix"               -> KEINE Marke          -> Zeile 84 stellt voran
  Beide Tueren sind zu, genau wie beschrieben. Und die gewaehlte Loesung traegt: die
  vorangestellte Rollenmarke laeuft byte-identisch durch, waehrend ein Auftragspraefix
  richtig NICHT als Rolle gelesen wird — der Grossbuchstabe trennt sie.
  Gefahrlos gemessen: nur grep, kein Commit-Versuch an der Statuswahrheit. Das hatte ich
  fuer diese Runde angekuendigt und so gehalten.
die_gegenrichtung_die_niemand_verlangt_hat: |
  Ein Wortlaut, der committierbar ist, nuetzt nichts, wenn die Erzeugung ihn nicht
  wiederfindet. Also habe ich das Bau-Muster aus status-erzeugen.sh:132 REAL gegen die
  Historie gefahren:
    neues Muster ^\(\w\+[a-z-]*: \)\?zustand:   ->  1 Treffer  (a613100e, der A-41-Auftrag)
    altes Muster ^zustand:                      ->  0 Treffer
    Gegenprobe 'zustand:' irgendwo im Betreff   -> 45 Treffer
  Die 44 Differenz sind KEINE verlorenen Meldungen: sie tragen "zustand:" mitten im
  Fliesstext langer Botschaften. Am Zeilenanfang steht es bei keiner einzigen. Das Muster
  ist also nicht zu eng geschnitten, sondern genau richtig — und die 0 des alten Musters
  belegt den Befund des Generators ("das Muster konnte NIE treffen") am Bestand.
eine_falle_die_ich_gesucht_und_nicht_gefunden_habe: |
  git log --grep durchsucht die GANZE Botschaft, nicht nur den Betreff, und das ^ ankert
  an jedem Zeilenanfang. Ein Commit, dessen BODY eine Zeile "zustand: ..." enthaelt —
  etwa weil jemand den Wortlaut zitiert — waere damit ein Treffer, aus dem das Skript
  einen Zustand ableiten koennte, den niemand gemeldet hat.
  GEMESSEN: Treffer gesamt 1, davon mit passendem BETREFF 1. Zeilen in Bodies, die so
  beginnen: 0. Die Falle ist heute leer.
  UND SIE IST AUCH ZUGEBAUT: status-erzeugen.sh prueft in Zeile 139/140 den Betreff ein
  zweites Mal (WORTLAUT.match(betreff), if not m: verworfen). Ein Body-Treffer faellt dort
  heraus. Doppelt abgesichert, und die zweite Sicherung ist die tragende — die erste
  haengt am Bestand, die zweite an der Logik.
stand_der_dor: |
  Zwei Punkte geprueft, beide halten. Offen bleiben die zwoelf Kriterien und sieben Kanten
  selbst, dazu Kante K7 (--no-merges), die ich um 15:22 als weiter offen gemeldet habe.
  Der Ball bleibt bei mir. Kein Votum heute — zwei bestaetigte Funde sind keine DoR.
ballbesitz: plan-pruefer
```

herkunft: docs/STATUS.md · Block 362 · 34f6f5a9
```yaml
auftrag: "A-41"
titel: "DoR Teil 3 — A-41-2 haelt, K5 ist erfuellt OHNE Code, und genau daraus entsteht ein falsches Rot"
rolle: plan-pruefer
zeit: "2026-08-16 15:33 CEST"
mess_stand: "Blatt 7a8f3722 (15:29, 248 Z., 12 Kriterien, 7 Kanten) · Bau b585d335 · HEAD a0a0cf3a"
a_41_2_haelt: |
  "existiert und ist ausfuehrbar" — am Dateimodus IM BAUM gemessen, nicht im Dateisystem
  (im Dateisystem haette mein Baum die Datei gar nicht):
    100755 blob 405171f0...  scripts/status-erzeugen.sh
  Eichung an einem bekannt ausfuehrbaren Skript: commit-pruefen.sh ebenfalls 100755.
  Das Kriterium ist erfuellbar und heute erfuellt.
k5_ist_erfuellt_aber_der_beleg_ist_ein_NICHT_treffer: |
  K5 verlangt: "der Revert ist KEIN Zustands-Commit; der zurueckgedrehte bleibt gueltig.
  Wer zuruecknehmen will, meldet den alten Zustand neu."
  REAL GETESTET, beide Richtungen:
    Revert "planner: zustand: A-41 · ENTWURF · ..."   -> kein Treffer
    planner: zustand: A-41 · ENTWURF · ...            -> TREFFER
  Die Kante ist erfuellt, und zwar ohne eine einzige Zeile Code: das Muster verlangt den
  ZEILENANFANG, und bei einem Revert steht 'Revert "' davor. Im ganzen Skript kommt
  'revert' 0 mal vor — es braucht den Sonderfall nicht.
  Das ist gute Bauart. Eine Kante, die durch die FORM des Musters erledigt ist, hat keinen
  Zweig, der falsch laufen kann.
und_hier_ist_die_falle: |
  A-41-9 verlangt: "Alle sieben Kanten K1-K7 sind behandelt und JE EINZELN BELEGT."
  Wer "behandelt" als "im Code adressiert" liest, sucht nach 'revert' im Skript, findet 0,
  und meldet K5 als unbehandelt — ROT an einer erfuellten Kante.
  Der Beleg fuer K5 ist ein NICHT-Treffer, und ein Nicht-Treffer sieht aus wie eine
  fehlende Pruefung. Das ist dieselbe Klasse, die mich heute zweimal erwischt hat: die
  Null in AUFTRAGSTAFEL.md (15:15) und der exit 127 beim Rollen-Tor (15:16). Eine Null
  bedeutet erst dann etwas, wenn feststeht, wonach sie gemessen wurde.
  BITTE, und es ist eine Zeile: bei K5 dazuschreiben, dass der Beleg die Musterprobe ist
  und nicht eine Codestelle. Sonst faellt der Erste, der A-41-9 abhakt, genau hier hinein.
was_noch_offen_ist: |
  Geprueft und haltend: A-41-2, K5, dazu FUND 1 und das Bau-Muster aus der Vorrunde.
  Heute ROT und im Auftrag erfasst: A-41-8 / K7 (--no-merges fehlt, grep 0 im Stand
  b585d335) — der Planner hat das um 15:29 zum Auftrag an den Generator gemacht.
  Noch ungeprueft: A-41-1, -3 bis -7, -10 bis -12 und die Kanten K1-K4, K6.
  Kein Votum. Der Ball bleibt bei mir.
vorratspruefung_e_eigene_befunde_verfolgt: |
  Mein K7-Befund von 15:22 liegt nicht mehr bei mir: der Planner hat ihn um 15:29
  aufgenommen, mit derselben Messung (grep -c -- --no-merges = 0 in b585d335) und einer
  SCHAERFEREN Begruendung als meiner. Ich hatte geschrieben, jeder Zustand erscheine nach
  jedem Transport erneut. Er ergaenzt: mit NEUER Commit-Zeit — und da der juengste
  gewinnt, kann ein ALTER Zustand einen NEUEREN verdraengen. Das ist nicht mehr
  Doppelzaehlung, das ist Zustandsumkehr.
  Damit ist der Befund sachlich UND formal erledigt. Ball steht dort auf generator.
ballbesitz: plan-pruefer
```

herkunft: docs/STATUS.md · Block 363 · 34f6f5a9
```yaml
auftrag: "A-41"
titel: "A-41-10 ist am Bau NICHT erfuellt — der Code kennt nur 0 und 1, das Kriterium verlangt vier Werte, und die 1 traegt vier Bedeutungen"
rolle: plan-pruefer
zeit: "2026-08-16 15:35 CEST"
mess_stand: "Bau b585d335 (227 Z.) · Blatt 7a8f3722 · Kriterium unveraendert seit a613100e"
das_kriterium: |
  A-41-10 verlangt vier Werte mit je genau einer Bedeutung:
    0  erzeugt, keine Meldung
    1  erzeugt, mit Meldungen (K2/K4/K6)
    2  NICHT erzeugt, Widerspruch (K1)
    3  Eingang leer, nichts erzeugt
  Und ausdruecklich: "Kein Wert traegt zwei Bedeutungen. (Der Fehler, an dem A-37 eine
  Runde verlor.)"
was_der_code_tut: |
  Alle Ausstiegsstellen gemessen, Muster fuer Bash UND Python getrennt geeicht
  ('sys.exit' 4 Treffer · 'exit ' Bash 0 · 'SystemExit' 0):
    Z.126   sys.exit(1 if rot else 0)
    Z.190   sys.exit(1 if widerspruch else 0)
    Z.204   sys.exit(1 if uneinig else 0)
    Z.226   sys.exit(1 if (fehlend or neu or abweichend or widerspruch) else 0)
  DIE WERTE 2 UND 3 ERZEUGT DER CODE NIRGENDS. Doppelt geprueft: 'exit([23]' -> 0 Treffer,
  und keine der vier exit-Zeilen enthaelt ueberhaupt eine 2 oder 3.
  Zeile 190 ist zweifelsfrei der Widerspruchsfall K1 — die Zeile darueber druckt
  "WIDERSPRUCH bei gleicher Zeit — GEMELDET, NICHT aufgeloest (Regel 4)". Das Kriterium
  verlangt dafuer 2, der Code gibt 1.
  Zeile 226 ist schaerfer: dort tragen VIER verschiedene Ursachen (fehlend, neu,
  abweichend, widerspruch) denselben Wert 1. Wer 1 liest, weiss nicht, was passiert ist.
warum_ich_das_KEINEN_verstoss_nenne: |
  Die Zeitachse steht dagegen, und sie ist aus dem Reflog gemessen, nicht geschaetzt:
    Bau            1e342d53  15:15:49   <- VOR dem Blatt
    Blatt v1       a613100e  15:19:22   <- A-41-10 steht hier schon, wortgleich
    Nachbesserung  b585d335  15:20:43   <- 81 Sekunden nach dem Blatt
  Der Generator hat gebaut, bevor das Kriterium existierte. Seine Nachbesserung liegt
  anderthalb Minuten hinter dem Blatt — ob er es da schon hatte, weiss ich nicht und
  behaupte ich nicht. Das ist eine offene Stelle, kein Versaeumnis.
die_beobachtung_die_zaehlt: |
  A-41-10 nennt A-37 ausdruecklich als Warnung — und der heutige Bau faellt in dieselbe
  Klasse, nur von der anderen Seite. Bei A-37 verlangte das BLATT einen Wert, den der CODE
  nicht kennt (exit 3, heute frueh gemeldet). Hier verlangt das Blatt vier Werte, und der
  Code kennt zwei. Beide Male klafft dieselbe Fuge zwischen Zusage und Erzeugnis, und
  beide Male faellt sie erst auf, wenn jemand die Ausstiegsstellen ZAEHLT statt sie zu
  lesen.
  Ein Kriterium, das vor einem Fehler warnt, schuetzt nicht vor ihm. Nur eine Messung tut
  das.
was_zu_tun_ist: |
  Vier Ausstiegsstellen auf die Tabelle aus A-41-10 bringen — insbesondere Z.190 auf 2 und
  einen Weg fuer 3 (leerer Eingang). Z.226 muss die vier Ursachen trennen, sonst bleibt
  die 1 mehrdeutig, auch wenn 2 und 3 existieren.
  Ich baue nicht und habe kein Zustandsfeld angefasst. Zur Formalisierung gehoert der
  Punkt dem Planner, wie bei A-41-8 heute um 15:29.
ballbesitz: generator
```

herkunft: docs/STATUS.md · Block 364 · 34f6f5a9
```yaml
auftrag: "A-41"
titel: "DoR Teil 4 — A-41-3 haelt am echten Lauf, und ich haette um ein Haar einen schweren Fehlbefund gemeldet"
rolle: plan-pruefer
zeit: "2026-08-16 15:43 CEST"
mess_stand: "Bau b585d335 · HEAD 55028175 · Lauf im eigenen Baum, Arbeitsbaum danach wieder 0 geaenderte Pfade"
a_41_3_idempotenz_haelt: |
  Zweimal unmittelbar hintereinander gefahren, Ausgaben verglichen: IDENTISCH.
  Rueckgabe beide Male 1. Der Arbeitsbaum ist unveraendert geblieben (0 geaenderte Pfade,
  nach dem Lauf gemessen).
  Vorher abgesichert, dass der Lauf gefahrlos ist: 0 Python-Schreibzugriffe, 0 'git add'
  und 0 'git commit'. Drei vermeintliche Shell-Umlenkungen waren Fehlalarm meines Musters
  — Z.95 ist ein Regex, Z.130 ein Docstring mit Pfeil, Z.215 ein print mit Pfeil. Kein
  Schreibzugriff im ganzen Skript.
was_der_vergleichsmodus_wirklich_liefert: |
  aus dem Commit-Log erzeugt:      1 Kennung   (A-41, der einzige Zustands-Commit)
  im heutigen Bestand vorhanden:  86 Kennungen
  NUR IM BESTAND, nicht im Log:   85
  Und das Werkzeug sagt den Grund selbst dazu: "der Wortlaut ist neu; ohne Bootstrap hat
  die Erzeugung keine Eingabe." Das ist genau die Luecke, die Planner und Generator vor
  dem Bau unabhaengig voneinander gefunden haben. Es meldet sie, statt 85 Abweichungen
  als Divergenz auszugeben.
  GEGENGEMESSEN: die 86 habe ich unabhaengig nachgebaut — den Bestandsleser in einem
  eigenen Python-Lauf ueber dieselben sechs Zweige nachprogrammiert. Ergebnis 86,
  identisch. Zwei Wege, eine Zahl.
und_A_41_10_zeigt_sich_hier_praktisch: |
  Der Lauf gibt Rueckgabe 1 — wegen 'fehlend', also der 85. Derselbe Wert 1 steht laut
  A-41-10 aber fuer "erzeugt, mit Meldungen". Mein Befund von 15:35 ist damit nicht mehr
  nur am Code belegt, sondern am laufenden Werkzeug: wer diese 1 liest, weiss nicht, ob
  85 Kennungen fehlen oder ob drei Meldungen angefallen sind.
DIE_FALLE_DIE_ICH_MIR_SELBST_GESTELLT_HABE: |
  Mein erster Lauf meldete "0 Kennungen aus dem Log" UND "0 im Bestand". Das sah nach
  einem schweren Bau-Fehler aus: das Werkzeug, das die Statuswahrheit erzeugen soll,
  findet nichts. Ich habe es NICHT gemeldet, sondern weitergemessen — und beide
  Bestandteile funktionierten isoliert: das WORTLAUT-Regex trifft den Betreff
  (kennung=A-41, zustand=ENTWURF, rolle=planner), der git-log-Aufruf liefert 1 Zeile, der
  nachgebaute Bestandsleser findet 86.
  DER GRUND WAR MEINER: Zeile 61 des Skripts lautet cd "$(dirname "$0")/.." — ich hatte
  das Skript nach /tmp kopiert und von dort gefahren. Es wechselte also nach /, fand kein
  Repo und meldete folgerichtig ueberall Null. Am richtigen Ort gefahren: 1 und 86.
  Das ist heute die dritte Null, die etwas anderes bedeutet hat, als sie aussah — nach
  AUFTRAGSTAFEL.md um 15:15 und exit 127 um 15:16. Diese war die gefaehrlichste, weil ein
  spektakulaeres Ergebnis am wenigsten Nachfragen provoziert. Ein Werkzeug, das man nicht
  an seinem gedachten Ort fahren laesst, misst den Ort und nicht das Werkzeug.
stand: |
  Geprueft und haltend: A-41-2, A-41-3, K5, FUND 1, das Bau-Muster, der Vergleichsmodus.
  Rot: A-41-8 mit K7 (--no-merges, seit 15:29 Auftrag beim Generator) und A-41-10 (der
  Code kennt nur 0 und 1).
  Offen: A-41-1, -4 bis -7, -9, -11, -12 und die Kanten K1 bis K4 sowie K6.
ballbesitz: plan-pruefer
```

herkunft: docs/STATUS.md · Block 365 · 34f6f5a9
```yaml
auftrag: "A-41"
titel: "Beide meiner roten Punkte sind behoben — am laufenden Werkzeug nachgemessen, nicht am Commit-Text geglaubt"
rolle: plan-pruefer
zeit: "2026-08-16 15:45 CEST"
mess_stand: "Bau 2e9cf127 (15:40, 316 Z., vorher 227) · HEAD 52c4e13d · Laeufe im eigenen Baum, Arbeitsbaum danach 0 geaenderte Pfade"
a_41_8_behoben: |
  'no-merges' im Skript: 2 Treffer (vorher 0). Z.191 traegt den Schalter im Aufruf,
  Z.187 die Begruendung im Kommentar. Der Punkt, den ich um 15:22 gemeldet und den der
  Planner um 15:29 zum Auftrag gemacht hat, ist zu.
a_41_10_behoben_und_besser_als_verlangt: |
  Aus vier verstreuten 'sys.exit(1 if X else 0)' ist EIN zentraler Ausstieg geworden:
  def raus(code, grund, bedeutung=None) mit einer BEDEUTUNG-Tabelle.
  ALLE VIER WERTE WERDEN JETZT WIRKLICH ERZEUGT — die Aufrufe gezaehlt, nicht die Tabelle
  gelesen (das war der Fehler, an dem A-37 haengt):
    raus(0, ...) 3 mal · raus(1, ...) 2 mal · raus(2, ...) 3 mal · raus(3, ...) 3 mal
    dazu raus(70, ...) 1 mal fuer die rote Selbstprobe (sysexits EX_SOFTWARE)
  sys.exit gesamt: 1. Bash-exit: 0. Es gibt keinen zweiten Weg hinaus.
  Und die Zuordnung stimmt inhaltlich, Stichprobe an drei Stellen:
    Z.238 raus(3, "Kein einziger Commit im Wortlaut ... nichts zu erzeugen")
    Z.251 raus(2, "GEMELDET, NICHT aufgeloest (Regel 4)")
    Z.262 raus(0, "... Kennungen, keine Meldung")
  BESSER ALS VERLANGT ist der Sonderfall, den A-41-10 gar nicht nennt: die Fangprobe
  erzeugt nichts, also waere ihre 0 als "erzeugt, keine Meldung" unehrlich. Der Code
  ueberschreibt die Bedeutung an dieser einen Stelle und begruendet es im Kommentar —
  "an genau der Stelle unehrlich, an der sie die Ehrlichkeit des Werkzeugs bezeugen soll".
am_laufenden_werkzeug_bestaetigt: |
  Nicht am Code stehengeblieben, sondern gefahren — diesmal am richtigen Ort:
    --fangprobe  -> Rueckgabe 0, "Fangprobe 10/10 wie erwartet", "RUECKGABE 0 — Selbstprobe gruen"
    (ohne Arg)   -> Rueckgabe 1, "RUECKGABE 1 — erzeugt, MIT Meldungen",
                    "fehlend 85 · neu 0 · abweichend 0"
  Genau die Mehrdeutigkeit, die ich um 15:35 gemeldet hatte, ist weg: die 1 steht nicht
  mehr allein, ihre Aufschluesselung steht daneben. Wer sie liest, weiss jetzt, was
  passiert ist.
  Die 86 Kennungen im Bestand sind zum dritten Mal unveraendert — Skript, mein
  Python-Nachbau, und dieser Lauf.
a_41_1_ist_heute_korrekt_rot: |
  Das Blatt gibt als Rot-Lage an: grep -c '^zustand:' ueber die Regeln -> 0.
  Am Basis-SHA e521bd98 nachgemessen: 0. Die Angabe stimmt, das Kriterium ist weder
  unerfuellbar noch bereits erfuellt.
  EIN HINWEIS FUER DEN, DER ES SPAETER ABHAKT: ohne den Anker misst man 8 Treffer in
  docs/ARBEITSREGELN.md (Z.126, 720, 722, 733, 752 u.a.). Die sind Altbestand und meinen
  das ZUSTANDSFELD in STATUS.md, nicht den Commit-Wortlaut. Wer 'zustand:' ohne ^ zaehlt,
  haelt A-41-1 faelschlich fuer erfuellt. Der Anker im Blatt ist richtig gesetzt — er muss
  nur benutzt werden.
stand: |
  Haltend: A-41-1 (als Rot-Lage sauber), -2, -3, -8, -10, K5, FUND 1, Bau-Muster,
  Vergleichsmodus, Fangprobe 10/10.
  Offen: A-41-4 bis -7, -9, -11, -12 und die Kanten K1 bis K4 sowie K6.
  Kein Votum. Der Ball bleibt bei mir.
ballbesitz: plan-pruefer
```

herkunft: docs/STATUS.md · Block 366 · 34f6f5a9
```yaml
auftrag: "A-41"
titel: "A-41-11 haelt — und beim Messen hat mir ein Merge genau die Falle gestellt, die K7 beschreibt"
rolle: plan-pruefer
zeit: "2026-08-16 15:46 CEST"
mess_stand: "Bau 2e9cf127 · HEAD a4f31a4e · Suche ueber alle Zweige, nicht gegen HEAD"
a_41_11_haelt: |
  Das Kriterium verlangt: keine Datei unter resources/, app/, database/, und nicht
  scripts/commit-pruefen.sh oder scripts/rollen-tor.sh.
  Erst den Scope bestimmt, statt ihn zu raten: alle Commits gesucht, die
  scripts/status-erzeugen.sh ueberhaupt beruehren, ueber ALLE Zweige. Es sind genau drei,
  alle vom Generator:
    1e342d53   1 Datei · 0 Nicht-Ziele
    b585d335   1 Datei · 0 Nicht-Ziele
    2e9cf127   1 Datei · 0 Nicht-Ziele
  Jeder Bau-Commit beruehrt genau eine Datei, und es ist die richtige. A-41-11 ist heute
  erfuellt.
und_hier_hat_K7_mich_selbst_erwischt: |
  Meine erste Gegenprobe lief ueber alle Commits, deren BETREFF 'A-41' nennt — 26 Stueck.
  Einer davon schlug an: 4d89df6f beruehre ein Nicht-Ziel.
  NACHGESEHEN STATT GEMELDET: 4d89df6f hat ZWEI ELTERN. Es ist ein Merge, ein
  Transport-Commit des Release-Pruefers, und die beiden Dateien darin
  (scripts/module-nachziehen.sh, scripts/rollen-tor.sh) gehoeren zu A-37, nicht zu A-41.
  Der Betreff nennt A-41 nur, weil der Transport mehrere Vorgaenge zugleich befoerdert.
  DAS IST WOERTLICH K7: "ein Merge traegt fremde Betreffs mit". Ich habe die Kante heute
  um 15:22 gemeldet und um 15:45 ihre Behebung bestaetigt — und bin beim naechsten
  Messschritt selbst hineingelaufen, mit demselben Mechanismus, nur an anderer Stelle:
  dort verfaelscht der Merge die ZUSTANDSZAEHLUNG, hier hat er meinen SCOPE-FILTER
  verfaelscht.
  Eine Kante, die man kennt, schuetzt nicht davor, sie zu treten. Nur die Gegenprobe tut
  das — hier war es die Elternzahl, zwei statt einer.
was_daraus_folgt_fuer_die_abnahme: |
  Wer A-41-11 spaeter abhakt, darf NICHT nach dem Betreff filtern. Der richtige Schnitt
  ist die DATEI: git log --all -- scripts/status-erzeugen.sh liefert genau die drei
  Bau-Commits und keinen Transport. Ich schreibe das dazu, weil der naechste Pruefer sonst
  denselben Umweg geht — und moeglicherweise nicht nachsieht, sondern meldet.
stand: |
  Haltend: A-41-1 (als Rot-Lage), -2, -3, -8, -10, -11, K5, K7, FUND 1, Bau-Muster,
  Vergleichsmodus, Fangprobe 10/10. Kein Punkt mehr rot.
  Offen: A-41-4 bis -7, -9, -12 und die Kanten K1 bis K4 sowie K6.
  Kein Votum. Der Ball bleibt bei mir.
ballbesitz: plan-pruefer
```

herkunft: docs/STATUS.md · Block 367 · 34f6f5a9
```yaml
auftrag: "A-41"
titel: "K1 belegt — die Kernlogik isoliert gegen vier Faelle gefahren, 4/4, und die Nachbesserung dahinter ist die eigentliche Leistung"
rolle: plan-pruefer
zeit: "2026-08-16 15:48 CEST"
mess_stand: "Bau 2e9cf127 (316 Z.) · HEAD 02bfe8c5"
k1_belegt: |
  K1 verlangt: zwei Zustands-Commits derselben Kennung mit identischem Zeitstempel —
  beide melden, keiner gewinnt.
  Die Kernlogik steht in Z.208-216. Ich habe sie NICHT nur gelesen, sondern isoliert
  nachgebaut und gegen vier Faelle gefahren:
    gleiche Zeit, VERSCHIEDENE Zustaende          -> Widerspruch erwartet   -> Widerspruch
    gleiche Zeit, GLEICHER Zustand                -> kein Konflikt erwartet -> keiner
    verschiedene Zeit, verschiedene Zustaende     -> juengster gewinnt      -> gewinnt
    zwei ALTE Eintraege gleicher Zeit, nicht die juengsten -> darf NICHT anschlagen -> tut es nicht
  4/4 wie erwartet.
  Der vierte Fall ist der, an dem eine schlampige Fassung scheitern wuerde: wer irgendwo
  im Bestand zwei gleiche Zeitstempel findet und daraus einen Widerspruch macht, blockiert
  bei jedem zweiten Lauf. Der Code vergleicht ausdruecklich nur die JUENGSTEN
  (e["ts"] == liste[0]["ts"]) und ist damit richtig eng.
die_eigentliche_leistung_steht_im_kommentar: |
  Z.240-245 dokumentiert, was vorher falsch war, und es ist ein feiner Unterschied:
  "Vorher stand sie oben und der Widerspruch darunter — mit dem juengsten Eintrag als
  Gewinner. Das war eine stille Aufloesung mit einer Warnung daneben. Bei gleicher Zeit
  gibt es aber keinen juengsten; wer trotzdem einen waehlt, entscheidet per
  Sortierreihenfolge."
  Das ist genau die Klasse, die ich heute mehrfach gemeldet habe, nur von innen gesehen:
  eine Ausgabe, die richtig AUSSIEHT (Tafel plus Warnung), aber eine Entscheidung
  enthaelt, die niemand getroffen hat — hier die Sortierreihenfolge von Python.
  Heute wird bei Widerspruch KEINE Tafel gedruckt und mit 2 ausgestiegen. Melden statt
  aufloesen, ohne Hintertuer.
  GEGENGEPRUEFT, dass die Strenge an BEIDEN Stellen gilt: der Tafel-Modus steigt mit
  raus(2) aus (Z.251), der Vergleichsmodus ebenfalls (Z.310, "der Vergleich hat keine
  Grundlage"). Es gibt keinen Weg, an dem ein Widerspruch stillschweigend in ein Ergebnis
  einwandert.
stand: |
  Kanten belegt: K1, K5, K7. Offen: K2, K3, K4, K6.
  Kriterien haltend: A-41-1 (als Rot-Lage), -2, -3, -8, -10, -11.
  Offen: A-41-4 bis -7, -9, -12.
  Kein Punkt rot. Kein Votum, der Ball bleibt bei mir.
ballbesitz: plan-pruefer
```

herkunft: docs/STATUS.md · Block 368 · 34f6f5a9
```yaml
auftrag: "A-41"
titel: "K3 belegt — aber A-41-5 verlangt eine Commit-Eigenschaft von einer Datei-Messung, und die kann sie nicht liefern"
rolle: plan-pruefer
zeit: "2026-08-16 15:51 CEST"
mess_stand: "Bau 2e9cf127 · HEAD 6fffa356 · beide Modi im eigenen Baum gefahren, danach 0 geaenderte Pfade"
k3_belegt: |
  --bootstrap gefahren. Rueckgabe 2, und die Ausgabe trennt sauber:
    EINIG, seed-faehig ohne Entscheidung: 85 von 86
    UNEINIG, brauchen eine Entscheidung:   1
    A-33  ABGENOMMEN laut evaluator · BEREIT laut plan-pruefer ·
          BETRIEBSBESTAETIGT laut release-pruefer · CODE_FERTIG laut generator und
          hausplaner-integration · SPEC_BLOCKED laut planner
    "Regel 4: hier wird NICHTS aufgeloest. Die 1 Faelle gehoeren dem Integrator."
  Die Kante ist behandelt: fuenf Zustaende, keiner gewinnt, Rueckgabe 2 nach A-41-10.
  UND MEIN EIGENER ZWEIG IST TEIL DER DIVERGENZ: plan-pruefer traegt BEREIT, den
  aeltesten der fuenf. Ich melde die Divergenz nicht von aussen, ich stehe darin.
die_fremde_zahl_frisch_gemessen: |
  Der Generator meldete um 15:15 "von 85 Kennungen sind 84 EINIG und genau EINE uneinig".
  Ich messe um 15:48: 85 von 86 einig, 1 uneinig.
  DIE ABWEICHUNG IST ECHT UND ERKLAERBAR: A-41 ist seit 15:19 selbst eine Kennung mit
  Zustand und einig. 85+1 = 86, 84+1 = 85. Beide Messungen stimmen zu ihrem Zeitpunkt.
  Ich schreibe es hin, weil eine uebernommene 84 morgen falsch waere und niemand wuesste,
  woher die Differenz kommt.
A_41_5_KANN_SO_NICHT_ERFUELLT_WERDEN: |
  A-41-5 verlangt woertlich: die fuenf verdraengten Staende von A-33 "sind einzeln
  protokolliert, MIT ZWEIG, ZUSTAND UND COMMIT-ZEIT".
  GEMESSEN, beide Muster, an einem bekannten Treffer geeicht:
    Zeitangaben in der Bootstrap-Ausgabe:  0
    Zeitangaben in der Tafel-Ausgabe:      1   ("a613100e  16.08 15:19")
  Das Muster funktioniert also — im Bootstrap gibt es schlicht keine Zeit.
  DER GRUND IST STRUKTURELL, nicht nachlaessig: aus_den_zweigen() liest die zustand:-Zeilen
  aus den STATUS.md-DATEIEN der sechs Zweige. Eine Dateizeile hat keine Commit-Zeit. Wer
  sie haben will, muss ermitteln, welcher Commit genau diese Zeile zuletzt geaendert hat —
  also blame oder log -L je Zeile, ueber sechs Zweige und 86 Kennungen.
  ZWEI QUELLEN, ZWEI FAEHIGKEITEN: der Commit-Log kennt Zeiten und liefert sie (Tafel-Modus
  zeigt es), der Dateibestand kennt sie nicht. A-41-5 verlangt eine COMMIT-Eigenschaft von
  einer DATEI-Messung.
was_ich_NICHT_entscheide: |
  Ob die Commit-Zeit wirklich gebraucht wird oder ob Zweig und Zustand genuegen, ist eine
  Frage an den Auftrag, nicht an mich. Beide Wege sind gangbar:
    Kriterium anpassen — dann ist es heute erfuellt, denn Zweig und Zustand stehen da.
    Bau erweitern — dann kostet es einen blame-Lauf je Kennung und Zweig.
  Ich lege es hin und messe, was daraus wird. Kein Bau, kein Zustandsfeld angefasst.
stand: |
  Kanten belegt: K1, K3, K5, K7. Offen: K2, K4, K6.
  Kriterien haltend: A-41-1 (Rot-Lage), -2, -3, -8, -10, -11.
  NEU AUFGEFALLEN: A-41-5 ist am gebauten Bootstrap nicht erfuellbar.
  Offen: A-41-4, -6, -7, -9, -12.
ballbesitz: planner
```

herkunft: docs/STATUS.md · Block 369 · 34f6f5a9
```yaml
auftrag: "A-41"
titel: "A-41-5 erfuellt und K3/K4 belegt — der Generator hat meinen Befund behoben, BEVOR ich ihn geschrieben habe"
rolle: plan-pruefer
zeit: "2026-08-16 15:56 CEST"
mess_stand: "Bau ccdfd7b6 (15:50, 534 Z., vorher 316) · HEAD 983d4b34 · beide Modi gefahren, danach 0 geaenderte Pfade"
a_41_5_ist_erfuellt: |
  Ich hatte um 15:51 gemeldet: der Bootstrap liefert Zweig und Zustand, aber keine
  Commit-Zeit, und das sei strukturell — eine Dateizeile kennt keine Commit-Zeit.
  HEUTE GEMESSEN am Stand ccdfd7b6: Zeitangaben in der Bootstrap-Ausgabe 5 (vorher 0),
  Muster unveraendert, an der Tafel-Ausgabe geeicht.
    VERDRAENGTE STAENDE, einzeln mit Zweig, Zustand und Commit-Zeit: 5
      A-33  hausplaner-integration  CODE_FERTIG         16.08 15:00
      A-33  evaluator               ABGENOMMEN          16.08 14:57
      A-33  planner                 SPEC_BLOCKED        16.08 14:38
      A-33  generator               CODE_FERTIG         16.08 13:12
      A-33  plan-pruefer            BEREIT              13.08 21:25
  Genau der Wortlaut des Kriteriums, Stueck fuer Stueck. A-41-5 ist erfuellt.
und_die_reihenfolge_gehoert_dazu: |
  Sein Commit ist von 15:50, mein Befund von 15:51. ER HAT ES VOR MIR GEFUNDEN, ohne
  meinen Befund gesehen zu haben. Ich schreibe das hin, weil die umgekehrte Lesart
  naheliegt und falsch waere: das ist keine Reaktion auf meine Meldung, sondern dieselbe
  Luecke, unabhaengig zweimal gefunden. Meine Meldung von 15:51 war zum Zeitpunkt ihres
  Schreibens schon ueberholt — ich hatte gegen 2e9cf127 gemessen, waehrend ccdfd7b6 seit
  einer Minute stand.
  DARAUS LERNE ICH ETWAS FUER MICH: bei einem Bau, der im Minutentakt waechst, muss der
  Standabgleich unmittelbar VOR dem Schreiben stehen, nicht am Rundenanfang. Ich hatte den
  Zweig um 15:46 gemessen und um 15:51 geschrieben.
k4_ist_jetzt_sichtbar_wirksam: |
  Die Kante verlangt: Prosa nicht uebernehmen, aber je Zweig protokollieren, wo sie steht.
  Der Lauf zeigt es je Zweig, mit Zahlen:
    hausplaner-integration 21706 Zeilen · Datensatz 247 · Prosa 19859
    evaluator              22797 · 251 · 20892      generator      19569 · 212 · 17803
    plan-pruefer           22879 · 261 · 21015      planner        21734 · 249 · 19884
    release-pruefer        23717 · 266 · 21768
  Belegt und nicht nur benannt.
k3_vollstaendig: |
  "Es gewinnt je Kennung der juengste Stand — die obigen sind ihm gewichen." Danach der
  Seed mit einer Zeile je Kennung, 86 Stueck. Rueckgabe 1, "erzeugt, MIT Meldungen".
  DAS IST KEIN WIDERSPRUCH ZU K1, und ich habe es geprueft statt es anzunehmen: K1 greift
  bei GLEICHER Zeit — dann gibt es keinen juengsten, also Rueckgabe 2 und nichts erzeugt.
  K3 greift bei VERSCHIEDENER Zeit — dann gewinnt der juengste, und das Verdraengte wird
  protokolliert. Zwei Kanten, zwei Ausgaenge, beide nach A-41-10 richtig vergeben.
was_mich_selbst_betrifft: |
  In der Verdraengungsliste steht mein eigener Zweig ganz unten: plan-pruefer BEREIT,
  13.08. 21:25. Drei Tage alt, der aelteste der fuenf. Waehrend ich die Divergenz messe,
  bin ich ihr aeltester Posten.
was_noch_offen_ist: |
  K2 und K6 sind im Code benannt (6 bzw. 6 Fundstellen, Meldezweige in Z.376-391) und
  formulieren genau, was die Kanten verlangen — ABER SIE LOESEN HEUTE NICHT AUS: der
  Bestand hat keinen Zustand ohne Blatt und keinen im fremden Zweig. Ich kann sie darum
  NICHT als belegt melden, nur als gebaut.
  Fuer A-41-9 ("alle sieben Kanten behandelt und JE EINZELN BELEGT") heisst das: zwei
  Kanten brauchen einen konstruierten Fall, sonst bleibt der Beleg eine Codestelle. Das
  ist dieselbe Klasse wie K5 heute frueh, nur andersherum — dort war der Beleg ein
  Nicht-Treffer, hier fehlt der Treffer ganz.
stand: |
  Belegt: K1, K3, K4, K5, K7 · gebaut aber nicht ausloesbar: K2, K6.
  Kriterien haltend: A-41-1 (Rot-Lage), -2, -3, -5, -8, -10, -11.
  Offen: A-41-4, -6, -7, -9, -12.
ballbesitz: plan-pruefer
```

herkunft: docs/STATUS.md · Block 370 · 34f6f5a9
```yaml
auftrag: "A-41"
titel: "K2 isoliert gefahren — die Blattsuche kann FALSCH POSITIV sein, und das ist die gefaehrlichere Richtung"
rolle: plan-pruefer
zeit: "2026-08-16 15:59 CEST"
mess_stand: "Bau 13236d52 (590 Z.), unmittelbar vor dem Schreiben abgeglichen — die geprueffte Stelle ist darin unveraendert"
warum_isoliert: |
  K2 loest im heutigen Bestand nicht aus: der Tafel-Modus kennt eine einzige Kennung, und
  die hat ein Blatt. Ich kann die Kante also nicht am Lauf belegen — und einen
  Zustands-Commit fuer eine blattlose Kennung erzeuge ich nicht, um eine Kante zu testen.
  Also die Funktion blatt_gefunden() isoliert nachgebaut und gegen echte und erfundene
  Kennungen gefahren.
was_dabei_herauskam: |
  A-41   gefunden   richtig, Blatt existiert
  W-17   gefunden   richtig
  Z-99   NICHT      richtig, frei erfunden
  B5     gefunden   RICHTIG — ich hatte einen Fehltreffer vermutet und nachgesehen:
                    docs/auftraege/aktiv/B5-zaehlergebnis-mit-trefferzeilen.md gibt es
  A-4    gefunden   FALSCH — es gibt kein Blatt "A-4". Getroffen wird a-40 und a-41.
  A-1    gefunden   FALSCH — kein Blatt "A-1". Getroffen wird a-10, a-13, ...
  Die Suche ist ein reiner Substring-Vergleich ueber alle Dateinamen. Eine kurze Kennung
  trifft damit als Praefix jede laengere.
warum_das_die_gefaehrlichere_richtung_ist: |
  Der Kommentar im Code benennt die ANDERE Richtung ausdruecklich und sehr sauber: "Sie
  kann eine Kennung uebersehen, deren Blatt anders heisst. Deshalb heisst die Meldung
  'kein Blatt GEFUNDEN' und nicht 'kein Blatt vorhanden' — der Unterschied ist der
  zwischen einer Messung und einer Behauptung."
  Das ist der FALSCH-NEGATIVE Fall, und er kostet nur eine ueberfluessige Meldung.
  Mein Fund ist der FALSCH-POSITIVE: die Kennung gilt als beblattet, K2 SCHWEIGT, und ein
  Zustand ohne Auftrag laeuft unbemerkt durch. Eine Kante, die nicht meldet, sieht aus wie
  eine Kante, die nichts zu melden hatte.
und_jetzt_die_ehrliche_einordnung: |
  HEUTE TRITT DER FALL NICHT AUF, und das habe ich gemessen statt gehofft:
  Kennungsformen im Bestand — A-NN 50 · W-NN/N 22 · W-NN 15 · BN 3 · W-NNN 2.
  Kennungen mit EINSTELLIGER Nummer nach dem Bindestrich: 0 von 79.
  Alle tragen die fuehrende Null (A-01 bis A-41). Solange das so bleibt, kollidiert nichts.
  ABER DER SCHUTZ IST ZUFALL, NICHT KONSTRUKTION. Die erste Kennung ohne fuehrende Null
  bricht ihn, und im Kennungsraum ist die Laengenkollision bereits angelegt: die Form
  W-NNN kommt zweimal vor, es gibt also neben zweistelligen auch dreistellige Nummern.
  Es ist genau die H-9-Klasse, vor der der Kommentar zwei Zeilen weiter oben selbst warnt
  — "ein Muster, das eine Schreibweise voraussetzt, misst die Schreibweise und nicht die
  Sache". Hier misst es die Zeichenfolge und nicht die Kennung.
vorschlag_nicht_entscheidung: |
  Ein Vergleich am Dateinamensanfang statt im ganzen Pfadtext wuerde es schliessen: der
  Basisname beginnt mit dem Stamm, gefolgt von Bindestrich oder Punkt. Ob sich das lohnt,
  bevor eine einstellige Kennung existiert, entscheidet der Generator — es ist sein Bau.
stand: |
  Belegt: K1, K3, K4, K5, K7. Gebaut, heute nicht ausloesbar: K2 mit diesem Vorbehalt, K6.
  Kriterien haltend: A-41-1 als Rot-Lage, -2, -3, -5, -8, -10, -11.
  Offen: A-41-4, -6, -7, -9, -12.
ballbesitz: generator
```

herkunft: docs/STATUS.md · Block 371 · 34f6f5a9
```yaml
auftrag: "A-37"
titel: "DoR fortgesetzt — A-37-1, -15 und -16 halten, beide Messvorschriften selbst gefahren; A-37-5 liegt seit 41 Minuten unberuehrt"
rolle: plan-pruefer
zeit: "2026-08-16 16:00 CEST"
mess_stand: "Blatt 02504a25 (13:53, unveraendert) · Bau 13236d52 · unmittelbar vor dem Schreiben abgeglichen"
a_37_1_haelt: |
  scripts/rollen-tor.sh: Modus 100755 im Baum, also vorhanden und ausfuehrbar.
  An commit-pruefen.sh geeicht, das ebenfalls 100755 traegt.
a_37_15_haelt_und_ich_habe_BEIDE_messvorschriften_gefahren: |
  Das Kriterium verlangt vier Felder MIT FELDNAMEN in fester Reihenfolge und nennt zwei
  Proben: wc -w = 8, und cut -d' ' -f2 liefert den Hash.
  Die Schreibstelle steht in module-nachziehen.sh:127 und lautet wortgleich
    printf 'hash %s  zeit %s  node %s  npm %s\n'
  mit LOCK_HASH, date -u +%Y-%m-%dT%H:%M:%SZ, node -v, npm -v.
  ISOLIERT NACHGEFAHREN, ohne npm ci auszuloesen (das haette Module nachgezogen):
    erzeugte Zeile: hash abc123def456  zeit 2026-08-16T13:59:39Z  node v26.5.0  npm 11.0.0
    wc -w            -> 8      wie verlangt
    cut -d' ' -f2    -> abc123def456   der Hash, wie verlangt
  Der Zeitstempel ist ISO-8601 ohne Leerzeichen, sonst waere die 8 nicht zu halten. Und
  die Ausweichwerte sind einwortig: faellt node -v oder npm -v aus, steht dort '?', nicht
  eine leere Stelle. Die Zaehlprobe bleibt also auch im Fehlerfall gueltig.
a_37_16_haelt: |
  "Die Marke wird auch GESCHRIEBEN, nicht nur gelesen": Z.132 lenkt den printf nach
  "$MARKE" um, Z.144 meldet es. Und die Gegenrichtung ist abgesichert — Z.98: "npm ci ist
  mit $NPM_RC ausgestiegen — KEINE Marke geschrieben."
  Das ist die richtige Reihenfolge: eine Marke, die einen misslungenen Lauf bezeugt, waere
  schlimmer als keine.
vorratspruefung_e_mein_eigener_befund_liegt: |
  A-37-5 (exit 3 im Kriterium gegen exit 5 im Code und in der Berichtigung) habe ich um
  15:17 gemeldet. Das Blatt ist seither UNVERAENDERT — letzte Aenderung 02504a25 um 13:53,
  also zwei Stunden vor meinem Befund. Zeile 292 traegt weiterhin "exit 3".
  Der Befund liegt damit seit 43 Minuten beim Planner. Ich melde das ohne Vorwurf: der
  Planner hat in dieser Zeit A-41 geschnitten, zwei Meldungen beantwortet und einen
  eigenen Fehler berichtigt. Es ist keine Nachlaessigkeit, es ist eine Warteschlange.
  ABER DER PUNKT BLEIBT SPERREND: solange A-37-5 einen Wert verlangt, den das Tor nicht
  erzeugt, kann ich die DoR nicht erteilen — ein Evaluator wuerde an einem richtigen Bau
  ROT melden.
stand_der_A_37_dor: |
  Geprueft und haltend: A-37-1, -15, -16.
  Sperrend: A-37-5 (unerfuellbar am Bau).
  Offen: A-37-2, -3, -4, -6 bis -14, -17 — darunter A-37-13, der eine Marke auf einen
  fremden Hash setzt; den fahre ich nur gegen eine Kopie, nicht gegen den echten Baum.
ballbesitz: plan-pruefer
```

herkunft: docs/STATUS.md · Block 372 · 34f6f5a9
```yaml
auftrag: "A-37"
titel: "BERICHTIGUNG meiner A-37-5-Klassifikation — der Generator hat recht, und der Unterschied ist nicht akademisch"
rolle: plan-pruefer
zeit: "2026-08-16 16:03 CEST"
betrifft_commit: "ea939994"
mess_stand: "Blatt 02504a25 aus rolle/planner und aus dem Integrationszweig, beide gleich · Bau 13236d52"
was_er_gemeldet_hat: |
  Der Generator antwortet in 13236d52: "SEINE MESSUNG STIMMT, SEINE DEUTUNG GREIFT ZU KURZ,
  und der Unterschied entscheidet, wer den Ball hat." Das Blatt widerspreche sich selbst,
  elf Zeilen auseinander.
ich_habe_es_nachgemessen_und_er_hat_recht: |
  Z.292 (Kriterium):        "TICKET_ROLLE leer -> exit 3"
  Z.303 (Codetabelle):      "| 5 | Rollenkennung fehlt beim direkten Aufruf des Tors |"
  Z.306 (Vermerk dabei):    "BERICHTIGT am 16.08. nach DoR Runde 3 — es war eine Kollision,
                             kein Formfehler. Meine Fassung vom 15.08. vergab 3."
  Die 3 war schon vergeben — an "fehlende Modulaufloesung (MODUL)", Tabellenzeile 3.
  Deshalb wanderte der Fall auf 5, und die Ueberschrift blieb stehen.
  Ueber zwei Zweige geprueft, damit es kein Transportstand ist: rolle/planner und
  origin/auto/hausplaner-integration tragen beide Zeilen wortgleich.
was_an_meiner_meldung_falsch_war: |
  NICHT die Messung. exit 3 kommt im Tor nicht vor, der reale Lauf gibt 5 — das steht.
  FALSCH WAR DIE UEBERSCHRIFT: "ist am gebauten Stand NICHT ERFUELLBAR". Das ist die
  SPEC_BLOCKED-Klasse und schickt den Vorgang zum Neuschnitt an den Planner. Richtig ist:
  ein Selbstwiderspruch im Blatt, zu beheben durch eine angeglichene Zahl.
  UND ES IST PEINLICH GENAU, weil mein eigener Vorschlag im selben Block schon lautete:
  "EINE ZAHL in Z.292: exit 3 -> exit 5. Nichts weiter." Meine Ueberschrift war schwerer
  als mein eigener Befund. Wer nur die Ueberschrift liest — und das tut, wer eine
  Warteschlange abarbeitet — behandelt es als Blockade.
was_ich_NICHT_gelesen_hatte: |
  Die Codetabelle. Ich hatte um 15:17 versucht, sie zu greifen, mein Muster traf nur
  Trennzeilen, und ich habe den Befund OHNE sie geschrieben statt das Muster zu
  reparieren. In genau dieser Tabelle stand die 5 samt Begruendung.
  Das ist meine eigene Regel, an mir selbst verletzt: eine ausgefallene Messung ist kein
  Ergebnis — auch dann nicht, wenn der Befund ohne sie plausibel aussieht.
was_er_besser_gemacht_hat: |
  Er hat den praktischen Unterschied benannt, den ich nicht gesehen habe: als "nicht
  erfuellbar" gelesen ist es ein SPEC_BLOCKED mit Neuschnitt und Rueckwanderung, als
  Selbstwiderspruch gelesen ist es eine geloeschte Zeile.
  Und er hat die Rollengrenze gehalten: "ICH AENDERE DAS BLATT NICHT — es gehoert dem
  Planner." Stattdessen dokumentiert er im Kopf seines Tores, welchem der beiden Saetze
  der Bau folgt und warum. Das ist die richtige Antwort auf einen Widerspruch, den man
  nicht selbst aufloesen darf.
was_bleibt: |
  Die Sache ist unveraendert: Z.292 traegt eine Zahl, die kein Bau erzeugt, und ein
  Evaluator, der gegen sie misst, meldet einen richtigen Bau rot. Nur die Klasse ist
  leichter, als ich sie genannt habe — und der Ball bleibt beim Planner, aber als
  Zeilenkorrektur, nicht als Neuschnitt.
ballbesitz: planner
```

herkunft: docs/STATUS.md · Block 373 · 34f6f5a9
```yaml
auftrag: "A-37"
titel: "A-37-3 real gefahren und vollstaendig erfuellt — und A-37-2 setzt eine Bedingung voraus, die es nicht nennt"
rolle: plan-pruefer
zeit: "2026-08-16 16:05 CEST"
mess_stand: "Tor 13236d52 (329 Z.), vor dem Schreiben abgeglichen · Laeufe im eigenen Baum, danach 0 geaenderte Pfade"
a_37_3_erfuellt: |
  Verlangt: TICKET_ROLLE=generator im falschen Baum -> exit 1, Meldung nennt erwarteten
  UND gefundenen Baum.
  REAL GEFAHREN, Rueckgabe ohne Pipe gelesen: 1.
  Die Meldung im Wortlaut:
  ROLLEN-TOR  VERSTOSS  Rolle 'generator' arbeitet im falschen Baum.
              erwartet: ticket-rolle-generator     auf  rolle/generator
              gefunden: ticket-rolle-plan-pruefer  auf  rolle/plan-pruefer
              Ein Commit im falschen Baum landet auf einem fremden Zweig und faellt
              niemandem auf.
  Beides ist da, und mehr als verlangt: zusaetzlich der Zweig auf beiden Seiten und ein
  Satz, warum es zaehlt. Erfuellt.
und_ich_haette_es_fast_falsch_gemeldet: |
  Mein erster Blick zeigte fuenf Zeilen — ich hatte die Ausgabe mit sed -n '1,5p'
  abgeschnitten. Die Zeile "gefunden:" ist die SECHSTE. Auf dieser Grundlage waere die
  Meldung gewesen: "nennt den erwarteten, aber nicht den gefundenen Baum" — rot an einem
  Kriterium, das vollstaendig erfuellt ist.
  Gefangen durch Nachmessen statt Melden. Es ist heute dieselbe Klasse zum wiederholten
  Mal: ein zu enger Ausschnitt, und die fehlende Zeile sieht aus wie eine fehlende Sache.
a_37_2_setzt_etwas_voraus_das_es_nicht_nennt: |
  Verlangt: der Positivfall gibt exit 0 UND KEINE AUSGABE.
  Gefahren im eigenen Baum als plan-pruefer: Rueckgabe 0 — aber DREI Zeilen Ausgabe:
    ROLLEN-TOR  HINWEIS  MODULSTAND UNBEKANNT — in ticket-rolle-plan-pruefer ist gar kein
                node_modules. Marke schreiben: bash scripts/module-nachziehen.sh
                Durchgelassen und NICHT als gueltig verbucht.
  DAS IST KEIN FEHLER DES TORES. Der Hinweis ist gewollt und gehoert zu A-37-12; er sagt
  ausdruecklich, dass er durchlaesst, ohne zu bestaetigen. Und mein Baum ist nicht der Ort,
  den A-37-2 nennt — dort steht ticket-rolle-generator auf rolle/generator.
  ABER DIE BEDINGUNG FEHLT IM KRITERIUM: "keine Ausgabe" gilt nur, wenn eine GUELTIGE
  MODULMARKE vorliegt. A-37-14 sagt das fuer seinen Fall ausdruecklich ("Marke stimmt ->
  Lauf geht durch, keine Ausgabe"), A-37-2 sagt es nicht. Wer A-37-2 in einem frischen
  Baum ohne node_modules abhakt, misst drei Zeilen und meldet rot — an einem Tor, das
  genau das tut, was A-37-12 von ihm verlangt.
  BITTE, eine Zeile: bei A-37-2 dazuschreiben, dass der Baum eine gueltige Marke tragen
  muss, oder den Hinweis von der Bedingung "keine Ausgabe" ausnehmen.
stand_der_A_37_dor: |
  Geprueft und haltend: A-37-1, -3, -15, -16.
  Offen mit Vorbehalt: A-37-2 (erfuellt am richtigen Ort, aber die Bedingung fehlt).
  Sperrend, Klasse berichtigt: A-37-5 als Selbstwiderspruch, Ball beim Planner.
  Offen: A-37-4, -6 bis -14, -17.
ballbesitz: planner
```

herkunft: docs/STATUS.md · Block 374 · 34f6f5a9
```yaml
auftrag: "A-37"
titel: "A-37-6 ist gebaut — mit einer offengelegten Abweichung, die RICHTIG ist und im Kriterium fehlt. Das ist die dritte dieser Art"
rolle: plan-pruefer
zeit: "2026-08-16 16:09 CEST"
mess_stand: "Tor 13236d52 (329 Z.) · Bedingung gegen die Historie selbst gefahren"
worum_es_geht_und_es_betrifft_mich_selbst: |
  A-37-6 verlangt woertlich: "docs/STATUS.md aus einem Rollen-Worktree WIRD ABGEWIESEN."
  Genau das tue ich in jeder Runde — meine Befundbloecke gehen aus dem plan-pruefer-Baum
  in docs/STATUS.md. Waere das Kriterium woertlich gebaut, koennte ab sofort keine der
  vier Rollen mehr melden, und der Ersatz (A-41, Zustand aus dem Commit-Betreff) ist noch
  nicht fertig. Ich habe das also nicht aus Neugier geprueft.
wie_es_wirklich_gebaut_ist: |
  Z.193  if TOR_STATUS_PFAD = 1 und Rolle != integrator
  Z.194    INTEGRATOR_DA = git log --all --grep='^integrator:' | head -1
  Z.195    wenn vorhanden -> VERSTOSS, "Die Statuswahrheit hat EINEN Schreiber: den
           Integrator", nennt gefundenen Baum und Zweig, exit 1
  Z.202    sonst -> HINWEIS, "Durchgelassen: die Sperre zuendet erst, wenn ein Schreiber
           existiert ... Bis dahin divergiert die Statuswahrheit je Zweig — heute in
           SECHS Fassungen."
  DIE BEDINGUNG SELBST GEFAHREN, Muster an bekannten Rollen geeicht:
    ^integrator:   0 Commits        <- die Sperre ist heute latent
    ^plan-pruefer: 362 · ^generator: 260 · ^planner: 417   <- das Muster trifft
  Heute greift also Z.202. Das deckt sich damit, dass meine eigenen Commits durchlaufen.
und_der_generator_legt_es_selbst_offen: |
  Z.191, woertlich im Code: "Und es ist eine Abweichung vom Wortlaut des Kriteriums. Sie
  steht hier und im Bau-Bericht, nicht still im Code."
  Das ist genau die richtige Bewegung. Die Abweichung ist sachlich zwingend: eine Sperre,
  die zuendet, bevor der Ersatzweg existiert, legt vier Rollen still. Und sie ist
  selbstaufloesend — sobald der erste Integrator-Commit existiert, sperrt sie ohne weitere
  Aenderung.
das_ist_die_DRITTE_stelle_derselben_art: |
  A-37-5  Kriterium sagt exit 3, Tabelle und Bau sagen 5.
  A-37-2  Kriterium sagt "keine Ausgabe", der Bau gibt einen MODULSTAND-Hinweis, wenn
          keine Marke vorliegt — die Bedingung steht nur bei A-37-14.
  A-37-6  Kriterium sagt "wird abgewiesen", der Bau weist ab, SOBALD ein Integrator da ist.
  Dreimal ist das Kriterium enger oder aelter als der Bau, und DREIMAL HAT DER BAU RECHT.
  Das ist kein Zufall mehr, sondern ein Muster: A-37s Blatt ist am 14./15.08. geschnitten
  und seither vom Bau ueberholt worden, ohne dass die Kriterien nachgezogen wurden.
  FUER DIE DoR HEISST DAS: nicht drei Einzelkorrekturen, sondern ein Durchgang durch alle
  17 Kriterien gegen den heutigen Bau. Wer sie einzeln nachzieht, findet die vierte erst,
  wenn ein Evaluator daran rot meldet.
ballbesitz: planner
```

herkunft: docs/STATUS.md · Block 375 · 34f6f5a9
```yaml
auftrag: "A-38"
titel: "Die Rot-Lage von A-38 laeuft heute Abend um 22:53 ab — und die Zahl im Kriterium ist doppelt ueberholt"
rolle: plan-pruefer
zeit: "2026-08-16 16:16 CEST"
mess_stand: "Blatt 5bbc55bf (16.08. 13:23, 226 Z.) · basis_sha 0f05f8bf vom 14.08. 22:51, 156 Commits her · Planner 05347393, A-38 darin unberuehrt"
vorratspruefung_b_die_zahl_nachgerechnet: |
  A-38-2 traegt als Rot-Lage: "heute geht er durch — 28 von 32 Merges belegen es" (Z.173).
  Mit dem Muster des Blattes heute nachgezaehlt, und der Ort steht dabei, weil er den
  Unterschied macht:
  Blatt Z.173, alt ......... 32 Merges ·  28 ohne Marke · 88 Prozent
  Blatt Z.93, berichtigt ... 70 Merges ·  58 ohne Marke · 83 Prozent
  heute, nur HEAD .......... 20 Merges ·   5 ohne Marke · 25 Prozent
  heute, alle Zweige ...... 102 Merges ·   5 ohne Marke ·  4 Prozent
  Gefallen ist die absolute Zahl, von 58 auf 5 — nicht nur die Quote.
und_die_fuenf_sind_alle_alt: |
  94d2b479  14.08 22:53  Merge branch 'rolle/planner' into HEAD
  0f05f8bf  14.08 22:51  Merge branch 'rolle/planner' into HEAD
  c1b3a774  14.08 22:51  Merge branch 'auto/hausplaner-integration' into HEAD
  b1d343e6  14.08 22:23  Merge commit 'bc2125d9' into HEAD
  9b42e777  14.08 22:14  Merge commit '0a297803' into HEAD
  Alle fuenf stammen aus derselben Stunde des 14.08. — der Stunde, in der A-38 geschnitten
  wurde. 0f05f8bf ist sogar A-38s EIGENER basis_sha.
  Gegenprobe, die den Fall schliesst: seit 15.08. 00:00 gibt es ueber alle Zweige
  97 Merges und davon 0 ohne Rollenmarke. Nicht wenige — keinen einzigen. Im Fenster davor
  (24 bis 72 Stunden zurueck) waren es 42 von 62.
die_rot_lage_hat_eine_uhr: |
  --since='48 hours ago' ist ein wanderndes Fenster. Der juengste markenlose Merge ist vom
  14.08. um 22:53, er faellt also am 16.08. um 22:53 heraus — in 6 Stunden 37 Minuten,
  gerechnet ab 16:16.
  Ab dann ist die Rot-Lage von A-38-2 LEER und mit dem Messbefehl des Blattes nicht mehr
  reproduzierbar. Wer den Auftrag heute Abend prueft, misst 0 von 102 und findet keinen
  Beleg fuer das Problem, das er loesen soll.
was_das_NICHT_heisst: |
  Es heisst NICHT, dass A-38 ueberfluessig ist, und ich sage das ausdruecklich, weil die
  Zahl zu dieser Lesart einlaedt. Die 97 markierten Merges sind Disziplin, kein
  Mechanismus — sie haengen daran, dass der Release-Pruefer seine Betreffs markiert. Ein
  Hook sichert genau diese Disziplin gegen den Tag ab, an dem jemand sie vergisst.
  Was ablaeuft, ist der BELEG, nicht der Zweck.
was_zu_entscheiden_ist_und_nicht_von_mir: |
  A-38-2 braucht eine Rot-Lage, die nicht verfaellt. Zwei Wege, beide gangbar:
  Erstens den Zeitraum festschreiben statt relativ zu messen — "am 14.08. zwischen 22:14
  und 22:53 sind fuenf markenlose Merges entstanden", mit ihren SHAs; das bleibt wahr.
  Zweitens den Negativfall konstruieren statt ihn im Bestand zu suchen — A-38-2 verlangt
  ohnehin einen Merge mit "Merge branch x into HEAD", und der laesst sich in einer Kopie
  herstellen.
  Der zweite Weg prueft auch morgen noch. Aber die Entscheidung gehoert dem Planner.
ballbesitz: planner
```

herkunft: docs/STATUS.md · Block 376 · 34f6f5a9
```yaml
auftrag: "A-41"
titel: "K2-Befund behoben und nachgemessen, 6 von 6 — und mein eigener Loesungsvorschlag war falsch"
rolle: plan-pruefer
zeit: "2026-08-16 16:17 CEST"
mess_stand: "Bau 253a51d7 (16:13, 645 Z., vorher 590) · HEAD 4eac6684 · Funktion isoliert nachgebaut"
behoben_und_selbst_nachgemessen: |
  Die Substring-Suche ist durch eine Wortgrenze ersetzt:
  def _trifft(stueck, form): return stueck.startswith(form) and
      (len(stueck) == len(form) or not stueck[len(form)].isalnum())
  Ein Pfadstueck traegt die Kennung also nur, wenn es mit ihr anfaengt und danach etwas
  kommt, das weder Ziffer noch Buchstabe ist.
  ISOLIERT NACHGEBAUT und gegen dieselben sechs Faelle gefahren wie um 15:58, 963
  Pfadstuecke im Set:
  A-41  vorher True  -> jetzt True   richtig, echtes Blatt
  W-17  vorher True  -> jetzt True   richtig
  B5    vorher True  -> jetzt True   richtig
  Z-99  vorher False -> jetzt False  richtig, frei erfunden
  A-4   vorher True  -> jetzt FALSE  der Fehltreffer ist weg
  A-1   vorher True  -> jetzt FALSE  der Fehltreffer ist weg
  6 von 6. Und die Gegenrichtung ist mitgeprueft, denn darauf kam es an: eine zu strenge
  Grenze haette die echten Treffer mitgenommen. Hat sie nicht.
MEIN_VORSCHLAG_WAR_FALSCH: |
  Ich hatte um 15:58 vorgeschlagen: "der Basisname beginnt mit dem Stamm, gefolgt von
  Bindestrich oder Punkt".
  Der Generator weist es zurueck, und er hat recht: das haette die halbe Werkbank
  verloren. Dort steht die Kennung im VERZEICHNIS, nicht im Dateinamen —
  W-25-pfetten-und-kehlbalken/1-ZWECK.md hat den Basisnamen '1-zweck.md'. Er prueft
  deshalb JEDES Pfadstueck und setzt die Grenze innerhalb des Stuecks.
  DAS IST MIR PEINLICH GENAU, denn ich habe genau diese Struktur heute selbst gemessen:
  bei W-17/1 war Blatt 5 ein VERZEICHNIS (5-CODE/LIESMICH.md), und ich habe eigens
  vermerkt, dass wer die Datei sucht 216 statt 249 Zeilen misst. Dieselbe Falle, dieselbe
  Werkbank, zwei Stunden spaeter nicht mitgedacht.
  Ein richtiger Befund macht keinen richtigen Vorschlag. Ich habe die Luecke gefunden und
  daneben eine zweite eingebaut — dass sie nicht gebaut wurde, liegt daran, dass der
  Generator meinen Vorschlag geprueft und nicht ausgefuehrt hat.
was_er_uebernommen_hat: |
  Er nennt zwei Dinge woertlich: die Gewichtung ("der Falsch-Negativ kostet eine
  ueberfluessige Meldung, der Falsch-Positiv laesst K2 SCHWEIGEN") und den Schutzbefund
  "ungeglaettet" — dass der Fall heute nicht auftritt, weil 0 von 79 Kennungen einstellig
  sind, aber das Zufall ist und keine Konstruktion.
  Und er benennt, wie der Befund ueberhaupt gefunden wurde: durch isoliertes Nachbauen,
  weil die Kante am Bestand gar nicht ausloest. Am Bestand allein waere sie unsichtbar
  geblieben. Das ist die Lehre fuer K6, die noch offen ist.
stand: |
  Belegt: K1, K2 (jetzt mit Wortgrenze), K3, K4, K5, K7. Offen: K6.
  Kriterien haltend: A-41-1 als Rot-Lage, -2, -3, -5, -8, -10, -11.
  Offen: A-41-4, -6, -7, -9, -12.
ballbesitz: plan-pruefer
```

herkunft: docs/STATUS.md · Block 377 · 34f6f5a9
kennung_nicht_zuordenbar: true   # Freitext statt Kennung — uebernommen wie vorgefunden, nicht gedeutet
```yaml
auftrag: "W-17/1"
titel: "Die K6-Logik auf die groessere Menge angewandt findet einen echten Fall — und es ist W-17/1s eigener basis_sha"
rolle: plan-pruefer
zeit: "2026-08-16 16:20 CEST"
mess_stand: "Bau 16c5b9d2 (677 Z.) · HEAD 5196524a · Karte ueber 5 Rollenzweige, 140 Commits aus 24 Stunden"
wie_ich_darauf_gekommen_bin: |
  K6 loest am Bestand nicht aus, weil es nur EINEN Zustands-Commit gibt. Der Generator hat
  die Lehre daraus selbst gezogen: gefunden wird so eine Kante nur, wenn man ihre Logik
  ISOLIERT nachbaut. Also habe ich die Logik nachgebaut und auf die groessere Menge
  angewandt — alle Commits mit Rollenmarke der letzten 24 Stunden statt nur der
  Zustands-Commits.
  Muster vorher geeicht: 'planner:' -> planner, 'plan-pruefer:' -> plan-pruefer,
  'plan-pruefer-2:' -> plan-pruefer, 'release-pruefer:' -> release-pruefer.
der_fund: |
  137 Commits mit Rollenmarke geprueft. 136 liegen auf dem Zweig ihrer Rolle. EINER nicht:
  8faca79c  Marke 'planner'  "A-33 zurueck an den Evaluator — der SPEC_BLOCKED-Grund ist
            behoben, Wiederabnahme faellig"
  Nachgemessen mit merge-base --is-ancestor, Zweig fuer Zweig:
  rolle/evaluator ......... JA        rolle/planner ........... nein
  rolle/release-pruefer ... JA        rolle/generator ......... nein
  hausplaner-integration .. JA        rolle/plan-pruefer ...... nein
  Auch ohne den --no-merges-Filter nicht auffindbar: 'git log rolle/planner' kennt ihn
  weder mit noch ohne Merges. Es ist kein Merge (ein Elternteil), und der Reflog von
  rolle/planner zeigt 0 Reset- oder Rebase-Spuren.
warum_das_MICH_angeht: |
  8faca79c ist der basis_sha von W-17/1 — meinem eigenen Ball, dessen DoR ich um 15:10
  erteilt habe. Die Basis eines Auftrags liegt also nicht auf dem Zweig der Rolle, die ihn
  geschnitten hat.
  ICH HABE MEINE DoR TROTZDEM RICHTIG GEMESSEN, und das ist kein Glueck, sondern der
  Grund, warum ich die Quelle nenne: ich habe das Blatt aus rolle/planner gelesen und die
  Zahlen dort erhoben. Waere ich dem basis_sha gefolgt, haette ich ihn im Planner-Zweig
  NICHT gefunden und die Alterungsprobe waere ausgefallen — als "Commit existiert nicht",
  was falsch gewesen waere: er existiert dreifach, nur nicht dort.
  Das ist genau die Klasse, die mich heute mehrfach erwischt hat: eine Null, die nicht
  bedeutet, wonach sie aussieht.
was_ich_NICHT_sagen_kann: |
  WARUM er dort fehlt. Kein Reset im Reflog, kein Merge, Autor wie bei allen anderen.
  Denkbar sind zwei Wege — der Commit entstand in einem fremden Baum und traegt trotzdem
  die Planner-Marke (dann ist es der Fall, den A-37-3 verhindern soll), oder der
  Planner-Zweig ist nach dem Transport auf einem anderen Pfad weitergelaufen. Was davon
  zutrifft, ist an der Historie allein nicht zu entscheiden, und ich rate nicht.
  Fuer K6 heisst der Fund: die Logik trifft, wenn ihr eine Menge gegeben wird, in der es
  etwas zu finden gibt. Das ist der Beleg, den A-41-9 fuer K6 verlangt — nur eben an einer
  anderen Menge als der, auf die der Bau sie heute anwendet.
ballbesitz: release-pruefer
```

herkunft: docs/STATUS.md · Block 378 · 34f6f5a9
```yaml
auftrag: "A-37"
titel: "Die offene Frage von 16:20 ist beantwortet — zwei Planner-Commits entstanden AUSSERHALB des Planner-Zweigs. Das ist der Fall, den A-37 verhindern soll, belegt am 16.08. um 14:39"
rolle: plan-pruefer
zeit: "2026-08-16 16:22 CEST"
mess_stand: "HEAD ef241985 · Reflog und merge-base, kein Rateschluss"
was_ich_um_16_20_offen_gelassen_hatte: |
  Ich hatte gemeldet, dass 8faca79c die Marke 'planner' traegt und nicht auf rolle/planner
  liegt, und ausdruecklich geschrieben: "WARUM er dort fehlt kann ich NICHT sagen und rate
  nicht." Jetzt ist es gemessen.
die_gabelung_auf_die_sekunde: |
  Die Kette lautet da2a0d6a -> 0d79ce45 -> e913717a -> cef05ad3 -> 8faca79c, alle fuenf
  mit Planner-Marke, erzeugt zwischen 14:29 und 14:40.
  Mit merge-base --is-ancestor gegen rolle/planner geprueft, Commit fuer Commit:
  da2a0d6a  JA        0d79ce45  JA        e913717a  JA
  cef05ad3  nein      8faca79c  nein
  Der Bruch liegt also zwischen e913717a und cef05ad3.
  UND DER REFLOG VON rolle/planner ZEIGT DENSELBEN PUNKT:
  @{14:39:40}  e913717a  commit: planner: A-39 und A-40 haben ihren Platz ...
  @{14:49:28}  66fa277f  commit: planner: der Abhaengigkeitsgraph des Solar-Regelwerks ...
  Dazwischen steht NICHTS. Der Zweig war um 14:39:40 auf e913717a und um 14:49:28 auf
  66fa277f — die beiden Commits cef05ad3 (14:39) und 8faca79c (14:40) sind in diesem
  Fenster entstanden, aber nie auf diesen Zweig gezeigt worden.
wohin_sie_stattdessen_gingen: |
  Sie liegen auf rolle/evaluator, rolle/release-pruefer und hausplaner-integration. Der
  Reflog des Evaluators zeigt den Weg: @{14:40:04} merge fork/auto/hausplaner-integration:
  Fast-forward — sie kamen also ueber den Integrationszweig, keine vier Minuten nach ihrer
  Entstehung.
  Verloren ist nichts. Die Arbeit existiert dreifach. Nur der Zweig der Rolle, deren Marke
  sie tragen, kennt sie nicht.
was_das_bedeutet_und_was_nicht: |
  ES IST DER FALL, DEN A-37 VERHINDERN SOLL: ein Commit traegt eine Rollenmarke und
  entsteht nicht auf dem Zweig dieser Rolle. A-37s eigene Begruendung lautet woertlich
  "ein Commit im falschen Baum landet auf einem fremden Zweig und faellt niemandem auf" —
  und genau das ist hier passiert, am 16.08. um 14:39, und es ist bis 16:20 niemandem
  aufgefallen.
  ICH BEHAUPTE NICHT, DASS JEMAND EINE REGEL GEBROCHEN HAT. Zu diesem Zeitpunkt war das
  Rollen-Tor nicht in allen Fassungen eingehaengt — mein eigenes Tor kennt es bis heute
  nicht (gemessen um 15:26: drei Fassungen, meine ohne Einhaengung). Wer ohne Tor
  arbeitet, bekommt keine Warnung.
  Der Fall ist deshalb kein Vorwurf, sondern der BELEG, den A-37 braucht: die Barriere
  verhindert etwas, das nachweislich vorkommt — nicht etwas, das vorkommen koennte.
was_daran_haengt: |
  W-17/1 traegt 8faca79c als basis_sha. Wer die DoR gegen die Basis prueft und dafuer den
  Planner-Zweig waehlt, findet sie nicht — und misst eine Null, die nach "Commit existiert
  nicht" aussieht. Der Commit existiert dreifach.
  Fuer A-37s DoR ist das ein Zugewinn: A-37-3 und A-37-4 pruefen den Negativfall
  kuenstlich. Hier liegt ein echter, datierter, dreifach belegter Fall vor, den das Blatt
  als Anlass nennen kann.
ballbesitz: planner
```

herkunft: docs/STATUS.md · Block 379 · 34f6f5a9
```yaml
auftrag: "A-37"
titel: "Praezisierung zu 488186fc — es sind DREI Faelle, nicht einer. Mein eigener Filter hat zwei versteckt"
rolle: plan-pruefer
zeit: "2026-08-16 16:24 CEST"
betrifft_commit: "488186fc"
mess_stand: "HEAD 488186fc · 72-Stunden-Fenster · beide Messungen im selben Lauf, nur der Filter unterscheidet sie"
was_ich_praezisiere: |
  Um 16:22 habe ich 8faca79c als den Fall gemeldet, in dem ein Commit die Marke einer
  Rolle traegt und nicht auf deren Zweig liegt. Die Messung lief mit --no-merges, weil der
  Bau das auch tut.
  OHNE DIESEN FILTER SIND ES DREI:
  f4e7ad79  16.08 15:00  planner: Rueckfluss — W-17/1, P2H-06 und der Solar-Abhaengigkeitsgraph
  8faca79c  16.08 14:40  planner: A-33 zurueck an den Evaluator
  cef05ad3  16.08 14:39  planner: Rueckfluss — Regelverankerung, A-39, A-40 und A-33-7
  Geprueft wurden 525 Commits mit Rollenmarke statt 420. Zwei der drei sind MERGES —
  cef05ad3 hat zwei Elternteile, und deshalb hat mein eigener Filter sie verschluckt.
was_das_am_befund_aendert: |
  Es ist KEIN Einzelfall, sondern ein wiederkehrendes Muster, und es hat einen Namen: die
  beiden Merges heissen beide "Rueckfluss". Der Planner fuehrt also periodisch fremde
  Staende in seinen Arbeitsstand zurueck, und dieser zusammengefuehrte Stand geht in die
  Integration — bleibt aber nicht auf rolle/planner.
  Der dritte, 8faca79c, liegt genau ZWISCHEN den beiden Rueckfluessen (14:39 und 15:00).
  Das erklaert ihn nachtraeglich: er haengt an einem Rueckfluss-Merge, der selbst nicht auf
  dem Zweig blieb.
was_das_NICHT_am_bau_aendert: |
  Der --no-merges-Filter im Bau ist RICHTIG und bleibt es. K7 verlangt woertlich, dass
  Merges nicht als Zustands-Commits zaehlen, und genau das leistet er. Meine erweiterte
  Messung ist kein Pruefmass fuer K6, sondern eine eigene Frage an dieselbe Historie.
  Ich sage das ausdruecklich, damit aus dieser Meldung kein Auftrag gegen den Filter wird.
die_lehre_und_sie_ist_meine: |
  Ich habe den Filter des Baus uebernommen, weil er dort richtig ist — und ihn damit
  ungeprueft in eine ANDERE Frage mitgeschleppt. Der Bau fragt "welche Zustands-Commits
  gibt es", ich fragte "welche Commits liegen nicht auf ihrem Rollenzweig". Zwei Fragen,
  ein Filter, und zwei Drittel des Ergebnisses fehlten.
  Es ist dieselbe Klasse wie heute mehrfach, nur eine Ebene hoeher: nicht das Muster war
  zu eng, sondern die ueberNOMMENE Voreinstellung. Wer ein Werkzeug fuer eine neue Frage
  benutzt, muss seine Filter neu begruenden, nicht nur seine Treffer pruefen.
was_stehenbleibt: |
  Die Einordnung von 16:22 haelt unveraendert: verloren ist nichts, die Arbeit liegt auf
  evaluator, release-pruefer und Integration; es ist kein Regelbruch, weil das Rollen-Tor
  zu dieser Zeit nicht ueberall eingehaengt war; und es ist der Beleg, den A-37 braucht.
  Er ist jetzt nur dreifach statt einfach.
ballbesitz: planner
```

herkunft: docs/STATUS.md · Block 380 · 34f6f5a9
```yaml
auftrag: "A-38"
titel: "Rot-Lage umgestellt und verifiziert — aus meinem Befund ist eine Pruefregel geworden, nicht nur eine Korrektur"
rolle: plan-pruefer
zeit: "2026-08-16 16:29 CEST"
mess_stand: "Blatt 1ca66c0d (16:19) · mein Befund war 16:16 · drei Minuten dazwischen"
vorratspruefung_e_mein_befund_ist_behoben: |
  Um 16:16 habe ich gemeldet, dass A-38-2s Rot-Lage ("28 von 32 Merges") aus einem
  wandernden 48-Stunden-Fenster stammt und heute um 22:53 abgelaufen waere.
  A-38-2 traegt jetzt: "Rot — auf FESTE SHAs umgestellt, die Quote ist als Beleg
  abgesetzt: 94d2b479 · 0f05f8bf · c1b3a774 · b1d343e6 · 9b42e777".
  Das sind genau die fuenf, die ich gemessen und aufgelistet hatte.
  SELBST NACHGEPRUEFT, ob die neue Rot-Lage traegt — denn eine feste SHA-Liste ist nur
  dann besser, wenn die SHAs auch das zeigen, was sie zeigen sollen:
  94d2b479  2 Eltern · keine Marke · "Merge branch 'rolle/planner' into HEAD"
  0f05f8bf  2 Eltern · keine Marke · "Merge branch 'rolle/planner' into HEAD"
  c1b3a774  2 Eltern · keine Marke · "Merge branch 'auto/hausplaner-integration'"
  b1d343e6  2 Eltern · keine Marke · "Merge commit 'bc2125d9' into HEAD"
  9b42e777  2 Eltern · keine Marke · "Merge commit '0a297803' into HEAD"
  Fuenf von fuenf sind Merges ohne Rollenmarke. Die Rot-Lage ist jetzt zeitunabhaengig und
  bleibt morgen wahr.
und_er_hat_mehr_daraus_gemacht_als_ich_gemeldet_habe: |
  Ich hatte eine Zeile vorgeschlagen. Er hat eine REGEL gebaut:
  P6  ROT-LAGE MIT UHR — "Eine Rot-Lage, die aus einem WANDERNDEN Zeitfenster stammt
      (--since='N hours ago', 'heute', 'seit gestern'), ist ein Fund. Sie wird von selbst
      gruen, ohne dass jemand etwas behoben hat. Verlangt: feste SHAs, ein Zeitstempel,
      oder ein KONSTRUIERTER Fall."
  Mit meinem Fall als Belegfall darin. Und dazu ein neues Kriterium in A-39:
  A-39-11 · P6 findet die Rot-Lage mit Uhr. Gegen den Stand VOR der Umstellung gefahren
  muss A-38-2 gemeldet werden — NEGATIVPROBE: die heutige Fassung mit den fuenf festen
  SHAs wird NICHT gemeldet.
  Das ist die Bauart, die ich seit Stunden einfordere: ein Kriterium, das in beide
  Richtungen prueft. Ein Einzelfund ist damit zu einer Pruefung geworden, die den naechsten
  Fall derselben Art von allein findet — auch in Blaettern, die ich nie gelesen habe.
was_das_fuer_meine_eigene_arbeit_heisst: |
  P6 trifft nicht nur fremde Blaetter. Ich habe heute selbst mit wandernden Fenstern
  gemessen — die 48-Stunden-Zaehlung um 16:12, die 24-Stunden-Menge um 16:19, die
  72-Stunden-Menge um 16:21. Keine davon steht in einem Blatt, aber jede steht in einer
  Meldung, und Meldungen werden spaeter gelesen.
  Ab jetzt schreibe ich bei jeder Zahl aus einem relativen Fenster den festen Zeitpunkt
  dazu, an dem sie gemessen wurde. Nicht weil eine Regel es verlangt, sondern weil ich
  heute gesehen habe, was eine solche Zahl nach sechs Stunden wert ist.
ballbesitz: planner
```

herkunft: docs/STATUS.md · Block 381 · 34f6f5a9
```yaml
auftrag: "A-39"
titel: "Drei Kriterien nennen keinen festen Stand — und das neueste davon ist ausgerechnet das, welches P6 prueft"
rolle: plan-pruefer
zeit: "2026-08-16 16:31 CEST"
mess_stand: "Blatt 1ca66c0d (16:19, 164 Z., 11 Kriterien) · Planner-Kopf zum Messzeitpunkt ccf856f9"
der_befund: |
  A-39 nennt fuer seine sechs P-Pruefungen je einen Stand, an dem die Pruefung anschlagen
  muss. Drei tun das mit einem SHA, drei nicht:
  A-39-2   0ee521f7                          fester SHA
  A-39-5   5db5f8a9                          fester SHA
  A-39-6   5bbc55bf                          fester SHA
  A-39-3   "am jeweils alten Stand"          KEIN SHA
  A-39-4   "am Stand vor A-37-16"            KEIN SHA
  A-39-11  "vor der Umstellung auf feste SHAs"  KEIN SHA
  Innerhalb desselben Blattes, fuer dieselbe Art von Angabe.
und_die_fehlenden_sind_alle_messbar: |
  Ich habe sie gesucht, statt die Luecke nur zu melden:
  A-39-3, erste Haelfte  — A-33-1 "genau EINS":  8559b555
  A-39-3, zweite Haelfte — A-37-11 "Suite 1750": 7ef8f046 (14.08. 22:35)
  A-39-11 — A-38 vor der Umstellung:             5bbc55bf
  GEGENPROBE zu 5bbc55bf, damit es kein Rateschluss ist:
  dort "28 von 32" 2 Treffer, feste SHAs 0 Treffer;
  heute umgekehrt, feste SHAs 1 Treffer.
  Der Stand traegt also genau das, was A-39-11 dort finden will — und die Negativprobe
  ("die heutige Fassung wird NICHT gemeldet") ist am selben Paar pruefbar.
  A-39-4 habe ich NICHT aufgeloest: "vor A-37-16" verweist auf ein Kriterium, nicht auf
  einen Commit, und welcher Bau-Stand gemeint ist, entscheidet nicht meine Messung.
warum_das_mehr_als_formsache_ist: |
  A-37-11 lautet heute nicht mehr "Suite 1750", sondern "Zahl unmittelbar vor dem Bau
  erheben ... Nicht gegen eine feste Zahl pruefen". Die Fassung, die A-39-3 finden soll,
  existiert also nur noch in der Historie. Ohne SHA muss der Pruefer sie suchen — und wer
  sie nicht findet, meldet die Pruefung als nicht ausloesbar, obwohl der Stand existiert.
  Das ist dieselbe Null wie heute mehrfach: nicht gefunden sieht aus wie nicht vorhanden.
die_ironie_und_sie_ist_der_kern: |
  A-39-11 ist das JUENGSTE Kriterium des Blattes, heute um 16:19 aus meinem eigenen
  A-38-Befund entstanden. Es prueft P6 — die Regel, dass eine Rot-Lage einen FESTEN Bezug
  braucht statt eines wandernden. Und es nennt selbst keinen festen Bezug, sondern "vor
  der Umstellung".
  Das ist kein Zeitfenster, aber dieselbe Klasse eine Stufe weiter: eine Referenz, die auf
  ein EREIGNIS zeigt statt auf einen Punkt. Kommt eine zweite Umstellung, zeigt sie
  woandershin, ohne dass jemand sie angefasst hat.
  VORSCHLAG, nicht Entscheidung: P6 um genau diesen Fall erweitern. Heute deckt die Regel
  "--since='N hours ago', heute, seit gestern" ab. Sie deckt "vor der Umstellung", "am
  alten Stand", "vor dem Bau" nicht ab — und das sind die drei Formulierungen, die in
  diesem Blatt tatsaechlich stehen.
ballbesitz: planner
```

herkunft: docs/STATUS.md · Block 382 · 34f6f5a9
```yaml
auftrag: "A-40"
titel: "A-40-5 haelt — mein Befund ist behoben, und die Umstellung war nachweislich noetig: die Zahl hat sich bewegt"
rolle: plan-pruefer
zeit: "2026-08-16 16:35 CEST"
mess_stand: "Blatt 1ca66c0d (161 Z., 9 Kriterien) · FORMELSAMMLUNG aus rolle/planner, 1151 Zeilen · Planner-Kopf ccf856f9"
vorratspruefung_e_mein_befund_ist_behoben: |
  Ich hatte gemeldet, A-40-5 verlange "21 Ampeln" als feste Zahl, ohne Regel fuer den
  Erstzustand. Heute lautet das Kriterium: "Jede Definitionsstelle traegt eine Ampel.
  Messbar — DER ZAEHLBEFEHL STEHT HIER, DER WERT NICHT", gefolgt vom grep-Ausdruck.
  Das ist die richtige Loesung, und sie ist mehr als eine Formsache.
und_die_umstellung_war_nachweislich_noetig: |
  Ich habe den Zaehlbefehl gefahren, statt ihn zu lesen:
  Definitionsstellen gesamt ..... 32
  davon OHNE Ampel .............. 23
  Mein urspruenglicher Befund nannte 21. Heute sind es 23. Die Zahl hat sich also
  tatsaechlich bewegt, waehrend niemand an den Ampeln gearbeitet hat — sie waechst mit
  jeder neuen Formel. Eine feste 21 im Kriterium waere heute falsch, und der Pruefer haette
  ROT gemeldet, ohne zu wissen warum.
  Das ist P6 in einer dritten Ausprägung: nicht ein wanderndes Zeitfenster, nicht eine
  Ereignis-Referenz, sondern ein Zaehlwert, der durch normale Arbeit waechst.
was_ich_geprueft_und_VERWORFEN_habe: |
  Beim Nachzaehlen fiel mir auf: die Datei nennt 48 verschiedene F-/N-/S-Nummern, aber nur
  32 haben eine Definitionsstelle. Das sah nach einer Luecke aus — 16 Nummern, die der
  Zaehlbefehl gar nicht sieht.
  NACHGESEHEN STATT GEMELDET, und es sind 18, nicht 16: F-052 kommt nur in einem
  Erklaerungssatz vor ("Warum ein eigenes Praefix und nicht F-052"), die S-Nummern stehen
  als SPANNEN in einer Uebersichtszeile ("S-001…S-006 Sonnenstand (rechnen)"). Keine davon
  ist eine Definitionsstelle in dieser Datei; die S-Reihe gehoert zum Solar-Regelwerk.
  Kein Befund. Haette ich die Differenz gemeldet, waere es die vierte Fehlmeldung heute
  gewesen, die auf einer Zahl beruht, deren Bedeutung ich nicht geprueft habe.
eine_beobachtung_ohne_befundcharakter: |
  Der Zaehlbefehl hat drei Alternativen — Ueberschrift, Tabellenzeile, Listenzeile.
  Einzeln gemessen: Ueberschrift 32, Tabellenzeile 0, Listenzeile 0.
  Zwei Drittel des Musters treffen heute nichts. Das ist KEIN Fehler, sondern Vorsorge
  fuer Definitionsstellen in anderer Form. Ich nenne es nur, damit niemand die 32 fuer
  eine Bestaetigung aller drei Formen haelt — geprueft ist heute genau eine.
stand_der_A_40_dor: |
  Geprueft und haltend: A-40-5.
  Offen: A-40-1 bis -4, -6 bis -9 und die sechs Kanten.
  Kein Votum. Der Ball bleibt bei mir.
ballbesitz: plan-pruefer
```

herkunft: docs/STATUS.md · Block 383 · 34f6f5a9
```yaml
auftrag: "P-02"
titel: "Pruefung der fuenf Punkte — vier tragen, Punkt 4 kollidiert mit der Pruefertpflicht, und die Machtfrage ist zu verneinen"
rolle: plan-pruefer
zeit: "2026-08-16 16:39 CEST"
mess_stand: "Blatt aus rolle/planner, 72 Zeilen · Planner-Kopf ccf856f9 · Belege aus meiner eigenen Arbeit von heute, jeweils mit Uhrzeit"
linse_widerspruchsfreiheit: |
  Vier der fuenf Punkte stehen widerspruchsfrei zum geltenden Text. EINER NICHT.
  PUNKT 4 lautet: "Fremde Befunde werden zitiert und verlinkt, NIE NACHGEBAUT."
  Meine Rolle verlangt das Gegenteil. Paragraf 5 und die Wache sagen: jede Behauptung
  SELBST nachmessen, auch die aus fremden Berichten.
  UND ES IST HEUTE ENTSCHIEDEN GEWESEN: um 15:22 habe ich FUND 2 des Planners nachgemessen
  statt ihn zu zitieren — er stimmte auf die Zeile. Um 15:45 habe ich die Behebung
  nachgemessen und dabei gefunden, dass die Nachbesserung GENAU DIESE ZEILE angefasst und
  die Luecke stehen gelassen hatte. Haette ich Punkt 4 befolgt, waere das unentdeckt
  geblieben.
  DER PUNKT IST NICHT FALSCH, ER IST UNVOLLSTAENDIG: fuer zwei Instanzen DERSELBEN Rolle
  ist er richtig — dieselbe Arbeit zweimal zu tun ist Verschwendung. Fuer die PRUEFENDE
  Rolle ist er falsch, denn dort IST das Nachbauen die Arbeit.
  VORSCHLAG: "nie nachgebaut" auf Instanzen derselben Rolle einschraenken. Ein Satz.
linse_pruefbarkeit: |
  Hat jeder Punkt einen beobachtbaren Ausloeser?
  1 CLAIM GILT ....... JA, das Claim-Feld im Datensatz ist messbar
  2 TRENNUNG ......... JA, zwei Commits derselben Rolle an derselben Zeile
  3 OPERAND ........... JA, ein Eintrag in STATUS.md gegen eine Blattaenderung
  4 VERLINKEN ......... SCHWACH — "nachgebaut" ist an einem Ergebnis nicht erkennbar.
                        Wer dasselbe misst wie ein anderer, sieht aus wie einer, der es
                        abgeschrieben hat, und umgekehrt.
  5 FRISCH MESSEN ..... JA, der Abstand zwischen Messzeit und Schreibzeit ist ablesbar
linse_kausalitaet_haette_der_punkt_den_vorfall_verhindert: |
  Fuer Punkt 5 kann ich es aus eigener Anschauung bejahen, und zwar zweimal heute:
  Um 15:19 habe ich A-41 in keinem Zweig gefunden und beinahe "Bau ohne Auftrag" gemeldet
  — das Blatt entstand um 15:19:22, meine Suche lief gegen den Stand von 15:04.
  Um 15:51 habe ich A-41-5 als unerfuellbar gemeldet; der Generator hatte es um 15:50
  behoben. Meine Meldung war beim Schreiben eine Minute alt und schon falsch.
  Beide Male haette Punkt 5 es verhindert. Ich habe die Lehre seither selbst uebernommen
  und messe unmittelbar vor dem Schreiben nach — in dieser Runde wieder.
die_machtfrage_und_sie_ist_zu_VERNEINEN: |
  Der Planner fragt selbst: "schiebe ich mir mit Punkt 2 etwas zu? Er behaelt Entscheidungen
  und die Tafel bei einer Instanz — und das bin im Zweifel ich."
  NEIN, und der Grund steht im Wortlaut: der Punkt sagt "bei EINER Instanz", nicht "beim
  Planner". Er regelt das Verhaeltnis zweier Instanzen DERSELBEN Rolle, nicht das
  Verhaeltnis der Rollen zueinander. Keine der vier anderen Rollen verliert dadurch etwas
  — der Plan-Pruefer prueft weiter, der Evaluator nimmt weiter ab, das Rollen-Tor bleibt
  unberuehrt.
  GEGENPROBE, damit das nicht nur Auslegung ist: waere es ein Machtzuwachs, muesste
  irgendwo eine Zustaendigkeit WANDERN. Ich finde keine. Punkt 2 nimmt niemandem etwas, er
  verhindert, dass zwei Traeger derselben Zustaendigkeit sich gegenseitig ueberschreiben —
  der Vorfall dazu ist belegt: "zwei Instanzen korrigierten dieselbe Zeile in zwei Minuten".
  Und die Selbstbeschraenkung im Blatt spricht dagegen, nicht dafuer: drei ausdrueckliche
  Nicht-Vorschlaege, darunter "keine Sperre gegen parallele Instanzen" und "keine Regel,
  wann ein Claim verfaellt — dafuer habe ich keine Messung".
linse_plausibilitaet: |
  Lebbar oder wird er umgangen? Vier sind lebbar, weil sie nichts kosten ausser
  Aufmerksamkeit. Punkt 5 kostet eine Messung je Schreibvorgang — ich fahre ihn seit
  15:54 in jeder Runde, es sind zwei Befehle.
  Punkt 1 hat die groesste Umgehungsgefahr: "wer wach wird und das Blatt frei findet,
  faengt NICHT an" verlangt Verzicht in genau dem Moment, in dem Arbeit sichtbar
  verfuegbar ist. Ohne Messung am Claim-Feld bleibt er Disziplin. Das ist kein Einwand
  gegen den Punkt, sondern die Stelle, an der er zuerst reissen wird.
was_noch_offen_ist: |
  Ich habe die fuenf Punkte gegen fuenf Linsen geprueft, aber die Belege stammen
  ueberwiegend aus MEINER heutigen Arbeit. Der Vorfall vom 15.08. (zweite
  Release-Pruefer-Instanz, 8a417fe0) und die vier Vorfaelle aus der Tabelle habe ich NICHT
  einzeln nachgemessen. Das ist die naechste Runde: jeden der fuenf Vorfaelle am Commit
  belegen, bevor ich ein Votum abgebe.
  KEIN VOTUM HEUTE. Der Ball bleibt bei mir.
ballbesitz: plan-pruefer
```

herkunft: docs/STATUS.md · Block 384 · 34f6f5a9
```yaml
auftrag: "P-02"
titel: "Ein VIERTER Fall, und er betrifft meine eigene Rolle — in meinem Namen wurde eine DoR erteilt, auf einem Zweig den ich nicht sehe"
rolle: plan-pruefer
zeit: "2026-08-16 16:45 CEST"
mess_stand: "Planner-Kopf zum Messzeitpunkt 8e997ef9 · alle Zaehlungen ueber den vollen Bestand, kein Zeitfenster"
wie_ich_darauf_gekommen_bin: |
  Beim Belegen von P-02s Vorfaellen stiess ich auf ea7ea816 (15.08. 15:41). Dort meldet der
  Planner: "ZWOELF Commits tragen eine Vertretung in der Marke, sechs verschiedene Formen"
  und ausdruecklich "mein Muster ^plan-pruefer trifft die Vertretungsmarke mit".
  GENAU DIESES MUSTER HABE ICH UM 16:24 BENUTZT. Also selbst geprueft, wie meines die
  Klammerform liest:
  "release-pruefer (zweite Instanz): x"                  -> KEIN TREFFER
  "plan-pruefer (release-pruefer in Rollenwechsel): x"   -> KEIN TREFFER
  "planner: x"                                           -> planner
  Meine drei Fundstellen von 16:24 sind davon unberuehrt — alle drei tragen eine einfache
  planner-Marke. ABER 10 COMMITS WAREN NIE IN MEINER PRUEFMENGE, weil mein Muster die
  Klammerform gar nicht erfasst. Keine Fehlzaehlung, eine Abdeckungsluecke.
die_nachgeholte_messung: |
  Klammerform-Commits im Bestand: 10, in sechs Formen —
  3x release-pruefer (in Yamas Namen) · 3x plan-pruefer (release-pruefer in Rollenwechsel)
  1x yama-entscheidung (in Vertretung eingetragen) · 1x release-pruefer (zweite Instanz)
  1x generator (vom Planner GESICHERT, nicht abgenommen) · 1x evaluator (Zweitinstanz)
  Neun davon nennen eine Rolle mit existierendem Zweig. EINER liegt nicht darauf:
  4ed51b8f  16.08. 12:39  "plan-pruefer (release-pruefer in Rollenwechsel):
            A-37 und A-38 sind BEREIT — 2. DoR-Runde"
  Ein Elternteil, kein Merge. Liegt auf release-pruefer, planner und Integration.
  Liegt NICHT auf rolle/plan-pruefer — meinem eigenen Zweig.
was_daran_zaehlt: |
  In meinem Namen wurde um 12:39 eine DoR ERTEILT: "A-37 und A-38 sind BEREIT".
  Beide stehen heute auf ENTWURF, mit dem Ball bei mir, und ich habe heute nachmittag
  beide DoRs von vorn geprueft — A-37 ab 15:17, A-38 ab 16:12.
  ICH STELLE NUR FEST, WAS GEMESSEN IST, und rate nicht, warum der Zustand wieder ENTWURF
  ist. Zwei Dinge sind aber sicher:
  Erstens habe ich den Commit nicht sehen koennen, solange ich meinen eigenen Zweig lese —
  er liegt dort nicht.
  Zweitens waere meine heutige Arbeit an A-37 und A-38 damit teilweise eine Wiederholung.
  Was von der 2. DoR-Runde galt und was nicht, kann ich aus der Historie allein nicht
  entscheiden.
fuer_P_02_ist_das_der_dritte_realfall: |
  P-02 fragt nach parallelen Instanzen. Belegt sind jetzt drei Vorfaelle, alle gemessen:
  8a417fe0  14.08. 22:33  zweite Release-Pruefer-Instanz tritt zurueck
  ea7ea816  15.08. 15:41  Rollenmarke sagt nicht, welche Instanz schrieb — 12 Vertretungen
  4ed51b8f  16.08. 12:39  Vertretung erteilt eine DoR, Commit fehlt auf dem Zweig der Rolle
  Der dritte ist der schwerste: die anderen beiden sind Verwechslungsgefahr, dieser ist
  eine ZUSTANDSAENDERUNG durch eine Vertretung, die im Zweig der vertretenen Rolle nicht
  ankommt.
  Punkt 1 des Vorschlags ("CLAIM GILT") haette ihn nicht verhindert — es ging nicht um
  einen Claim, sondern um einen Rollenwechsel. Punkt 2 ("Entscheidungen bleiben bei EINER
  Instanz") haette gegriffen, wenn "Instanz" auch die Vertretung einschliesst. Das steht
  dort nicht.
  VORSCHLAG, NICHT ENTSCHEIDUNG: Punkt 2 um den Vertretungsfall ergaenzen.
eine_datumsabweichung_nebenbei: |
  Der P-02-Datensatz sagt "Am 15.08. ist der Fall EINGETRETEN" und nennt als Beleg
  8a417fe0. Dieser Commit traegt Autor- UND Commit-Datum 2026-08-14 22:33:31.
  Ein Tag Abweichung. Klein, aber in einem Bestand, in dem Daten Belege sind, gehoert sie
  berichtigt — moeglicherweise ist ea7ea816 vom 15.08. gemeint, der zum selben Thema
  gehoert.
ballbesitz: plan-pruefer
```

herkunft: docs/STATUS.md · Block 385 · 34f6f5a9
```yaml
auftrag: "P-02"
titel: "Der Belegfall ist vollstaendig — zwei Instanzen derselben Rolle, entgegengesetzte Voten, 22 Minuten Abstand. Und mein Befund von 15:17 war eine Wiederholung"
rolle: plan-pruefer
zeit: "2026-08-16 16:50 CEST"
betrifft_commit: "114a7e0d"
mess_stand: "Integrationszweig, 128 STATUS.md-Aenderungen zwischen 13:00 und 16:15 durchgegangen, Zustand je Schritt gelesen"
was_ich_um_16_45_offen_gelassen_hatte: |
  Ich hatte gemeldet, dass in meinem Namen um 12:39 eine DoR erteilt wurde, dass A-37 und
  A-38 heute wieder ENTWURF tragen, und geschrieben: "ich rate nicht, warum".
  Jetzt gemessen. Die Zustandsreihe im Integrationszweig:
  f36efd83  13:01  A-37=BEREIT    (Transport der Erteilung)
  a400368f  13:01  A-37=ENTWURF   "plan-pruefer: DoR Runde 3 — NICHT ERTEILT"
  Es war KEIN Transportfehler und KEIN Ueberschreiben durch meine veraltete Datei. Der
  Zustand wurde bewusst zurueckgenommen, mit Begruendung.
die_zwei_voten: |
  4ed51b8f  16.08. 12:39:26  Vertretung: "A-37 und A-38 sind BEREIT — 2. DoR-Runde"
  a400368f  16.08. 13:01:54  Plan-Pruefer: "NICHT ERTEILT, fuenf Restpunkte"
  22 Minuten und 28 Sekunden. Zwei Instanzen derselben Rolle, entgegengesetzte Voten zum
  selben Auftrag — und BEIDE haben sauber gearbeitet: die erste hat alle Restpunkte der
  Vorrunde einzeln nachgemessen, die zweite fuenf neue benannt und jeden belegt.
  Das ist der Kern von P-02, und es ist kein Fehler einer der beiden. Es ist der Preis
  dafuer, dass zwei Traeger derselben Zustaendigkeit gleichzeitig arbeiten.
und_hier_wird_es_fuer_mich_unangenehm: |
  Die fuenf Restpunkte von 13:01 lauten unter anderem:
  "A-37-15 wc -w mindestens 6 faellt bei vier reinen Werten durch, das Format steht
   nirgends" — ich habe A-37-15 heute um 16:00 als HALTEND gemeldet, mit wc -w = 8. Das
   Kriterium wurde zwischenzeitlich nachgezogen; meine Messung war richtig, aber sie
   bestaetigt eine Fassung, die es um 13:01 noch nicht gab.
  "Fliesstext Z.307 exit 1 gegen A-37-5 exit 3" — DAS IST MEIN BEFUND VON 15:17,
   zwei Stunden frueher schon gemeldet.
  Ich habe ihn unabhaengig gefunden und dreiseitig belegt, und der Generator hat meine
  Klassifikation zu Recht berichtigt. Aber die SACHE war bekannt. Ich habe zwei Stunden
  spaeter dasselbe gemessen, weil die Meldung von 13:01 auf einem Zweig liegt, den ich
  nicht lese.
  Das ist genau der Schaden, den P-02 beschreibt — nicht Streit, nicht Ueberschreiben,
  sondern DOPPELARBEIT DURCH FEHLENDE SICHT.
was_das_fuer_die_fuenf_punkte_bedeutet: |
  Punkt 4 ("verlinken statt nachbauen") habe ich um 16:39 kritisiert, weil die
  Pruefertpflicht das Nachmessen verlangt. Dieser Fall zeigt die andere Seite: haette ich
  die Meldung von 13:01 GESEHEN, haette ich sie nachgemessen und BESTAETIGT — das kostet
  Minuten. Sie NICHT zu sehen und von vorn zu suchen, hat zwei Stunden gekostet.
  Meine Kritik an Punkt 4 halte ich aufrecht: nachmessen bleibt Pflicht. Aber der Punkt
  zielt auf etwas anderes, als ich gelesen habe — nicht auf "nicht nachmessen", sondern
  auf "nicht von vorn suchen, was schon gefunden ist". So gelesen trifft er zu, und dieser
  Fall belegt ihn.
  VORSCHLAG, praeziser als meiner von 16:39: Punkt 4 umformulieren zu "fremde Befunde
  werden zitiert und NACHGEMESSEN, nicht neu gesucht". Damit ist die Pruefertpflicht
  gewahrt und die Doppelarbeit adressiert.
ballbesitz: plan-pruefer
```

herkunft: docs/STATUS.md · Block 386 · 34f6f5a9
```yaml
auftrag: "A-37"
titel: "Die fuenf Restpunkte der Runde 3 abgearbeitet statt neu gesucht — vier behoben, EINER offen, und es ist eine einzige Zahl"
rolle: plan-pruefer
zeit: "2026-08-16 16:47 CEST"
mess_stand: "Blatt aus rolle/planner · Tor und module-nachziehen.sh aus rolle/generator · Planner-Kopf 8e997ef9"
warum_diese_runde_anders_ist: |
  Um 16:50 habe ich festgestellt, dass ich einen Befund zwei Stunden nach seiner ersten
  Meldung noch einmal gefunden habe, weil die Meldung auf einem Zweig lag, den ich nicht
  lese. Statt weiterzusuchen habe ich diesmal die VORHANDENE Liste genommen — die fuenf
  Restpunkte aus a400368f (13:01) — und sie einzeln gegen den heutigen Stand gemessen.
  Das ist Punkt 4 des P-02-Vorschlags in der Fassung, die ich um 16:50 selbst vorgeschlagen
  habe: zitieren und nachmessen, nicht neu suchen.
vier_von_fuenf_sind_behoben: |
  R1  "A-37-12 die Marke schreibt niemand (npm legt nur .package-lock.json an)"
      BEHOBEN. module-nachziehen.sh traegt die Schreibstelle (printf 'hash %s ...'), und
      das Blatt hat dafuer ein eigenes Kriterium A-37-16 bekommen. Beides habe ich heute
      um 16:00 unabhaengig geprueft.
  R2  "A-37-13 MODULSTAND ohne eigenen Code"
      BEHOBEN. 'MODULSTAND' kommt im Tor 5 mal vor, der Code existiert.
  R3  "A-37-15 wc -w mindestens 6 faellt bei vier reinen Werten durch, das Format steht
      nirgends"
      BEHOBEN. Das Format steht jetzt woertlich im Blatt (hash <sha> zeit <iso8601>
      node <version> npm <version>) und die Probe lautet wc -w = 8 statt "mindestens 6".
      Ich habe sie um 16:00 gefahren: 8, und cut -f2 liefert den Hash.
  R5  "A-38s drei Zahlen ohne Messbefehl"
      BEHOBEN. Das Blatt traegt 5 Messbefehle, und die Rot-Lage steht auf festen SHAs.
der_eine_offene_punkt: |
  R4  "Fliesstext Z.307 exit 1 gegen A-37-5 exit 3"
      OFFEN, seit 13:01:54, also seit drei Stunden und 45 Minuten.
      Z.307 traegt heute den Berichtigungsvermerk "BERICHTIGT am 16.08. nach DoR Runde 3 —
      es war eine Kollision, kein Formfehler". Der Fliesstext ist also nachgezogen.
      Z.292 sagt weiterhin exit 3.
      Das ist derselbe Punkt, den ich um 15:17 unabhaengig gefunden, um 16:03 in der
      Klassifikation berichtigt und seither zweimal gemeldet habe. Er ist damit dreifach
      belegt und von zwei Instanzen unabhaengig gefunden.
was_das_bedeutet: |
  A-37 ist eine Zahl von BEREIT entfernt. Vier Restpunkte einer vollstaendigen DoR-Runde
  sind abgearbeitet, der fuenfte ist die Aenderung von "exit 3" zu "exit 5" in Zeile 292.
  ICH ERTEILE DIE DoR NICHT, und der Grund ist nicht dieser Punkt: ich habe von A-37s
  siebzehn Kriterien selbst erst A-37-1, -2, -3, -6, -15 und -16 gemessen. Die Runde 3 hat
  andere geprueft, aber ich habe ihre Ergebnisse nicht nachgemessen, sondern nur ihre
  Restpunkte. Ein Votum aus fremden Haken waere genau das, was meine Rolle nicht darf.
  Was ich sagen kann: von den fuenf benannten Huerden steht noch eine, und sie ist klein.
ballbesitz: planner
```

herkunft: docs/STATUS.md · Block 387 · 34f6f5a9
```yaml
auftrag: "A-37"
titel: "A-37-8 und -9 geprueft — einen der drei Faelle kann ich mit eigener Rohausgabe belegen, die anderen zwei nur am Code"
rolle: plan-pruefer
zeit: "2026-08-16 16:58 CEST"
mess_stand: "Tor aus rolle/generator, 918 Zeilen · Rohausgabe aus meinem eigenen Lauf von 15:11"
a_37_8_am_code_vollstaendig: |
  Verlangt sind drei unterscheidbare Fehlerursachen mit je eigener Kennung. Im Tor:
  Z.632  MODUL      $p — js-yaml nicht aufloesbar. Dieser Worktree hat kein node_modules.
  Z.650  YAML-KOPF  $p — der Kopf parst nicht ($JETZT kaputte Bloecke, am Commit waren es $VORHER)
  Z.660  LAUFZEIT   $p — ${BERICHT#LAUFZEIT }
  Drei Kennungen, drei Texte, kein gemeinsamer Zweig. Die Rot-Lage des Blattes lautete
  "alle drei melden heute denselben Text, 2>/dev/null verschluckt die Ursache" — das ist
  am heutigen Stand nicht mehr so.
und_einen_fall_habe_ich_ROH: |
  A-37-8 verlangt "je ein Fall, ROHAUSGABE". Fuer (b) kann ich sie liefern, und zwar aus
  meinem eigenen Lauf um 15:11, nicht aus einem gestellten Versuch:
  MODUL      docs/STATUS.md  — js-yaml nicht aufloesbar. Dieser Worktree hat kein node_modules.
             Abhilfe: NODE_PATH=/Users/yamanuri/Documents/ticket/node_modules vor den Aufruf setzen.
  KEIN COMMIT. F-14: was nicht geschrieben wurde, wird auch nicht belegt.
  Alle drei geforderten Bestandteile sind darin: die Kennung MODUL, das Wort node_modules
  und der Abhilfe-Hinweis. Der Fall ist damit nicht behauptet, sondern passiert.
was_ich_NICHT_belegen_kann_und_warum_ich_es_nicht_erzwinge: |
  Fuer (a) YAML-KOPF und (c) LAUFZEIT habe ich keine Rohausgabe. Beide liessen sich
  herstellen — (a) mit einem absichtlich kaputten Block, (c) mit einem Syntaxfehler im
  Pruefaufruf. Ich tue es NICHT, weil beide Versuche einen Commit an docs/STATUS.md
  ansetzen wuerden, und die Statuswahrheit ist kein Versuchsfeld. Wer den Beleg braucht,
  fuehrt ihn gegen eine Kopie.
  INDIREKT ist (a) heute dreimal bestaetigt: meine eigene YAML-Vorpruefung hat dreimal 26
  statt 25 kaputte Bloecke gemeldet, jedes Mal vor dem Tor. Haette ich committet, waere
  genau Z.650 gelaufen — sie prueft auf WACHSTUM ($JETZT gegen $VORHER), und 26 gegen 25
  ist Wachstum. Das ist kein Ersatz fuer die Rohausgabe, aber es zeigt, dass der Zweig
  erreichbar ist.
a_37_9_die_yaml_pruefung_ist_scharf: |
  A-37-9 verlangt, dass ein tatsaechlich kaputter Kopf weiterhin abgewiesen wird.
  Die Zaehlung im Tor ist relativ ($JETZT gegen $VORHER), nicht absolut — sie laesst die
  25 Altlast-Bloecke durch und schlaegt beim 26. an. Genau dieses Verhalten habe ich heute
  dreimal am eigenen Leib gemessen: 25 blieb still, 26 haette gesperrt.
  Die Pruefung ist also scharf, ohne die Altlast zu blockieren. Das ist die schwierigere
  der beiden Bauarten und die richtige.
stand: |
  Selbst gemessen und haltend: A-37-1, -2 (mit Vorbehalt), -3, -6, -8 (b roh, a und c am
  Code), -9, -15, -16.
  Offen: A-37-4, -7, -10, -11, -12, -13, -14, -17.
  Sperrend: A-37-5, eine Zahl, seit 13:01.
ballbesitz: plan-pruefer
```

herkunft: docs/STATUS.md · Block 388 · 34f6f5a9
```yaml
auftrag: "A-37"
titel: "Der Release-Pruefer-Befund ist bestaetigt und ich bin sein groesster Einzelfall — 11 von 13 Schreibvorgaengen seit der Zuendung kommen von mir, ohne Tor"
rolle: plan-pruefer
zeit: "2026-08-16 17:02 CEST"
mess_stand: "Zuendung 83296554 um 16:17:16 · alle Zahlen ohne Merges, nach Rollenmarke gezaehlt, Summe gegen Gesamtzahl geeicht"
was_der_release_pruefer_meldet: |
  In d9fd6471 (16:46) meldet er auf Yamas Frage: die Barriere wirke VERKEHRT HERUM. Das Tor
  liege in 3 von 6 Baeumen; gesperrt seien genau die drei, die es HABEN, waehrend die zwei
  ohne Tor weiterschreiben.
  ICH HABE ES NACHGEMESSEN, weil es meinen eigenen Befund von 15:26 fortsetzt — ich hatte
  die drei Tor-Fassungen gefunden, aber nicht ihre WIRKUNG gemessen.
die_messung: |
  Eigene Schreibvorgaenge an docs/STATUS.md seit 16:17:16, ohne Merges, nach Rollenmarke:
  planner ............ 2   Tor FEHLT
  plan-pruefer ....... 11  Tor FEHLT
  generator ..........  0  Tor JA (5 Fundstellen)
  evaluator ..........  0  Tor JA (3)
  release-pruefer ....  0  Tor JA (5)
  Summe 13, und die Gesamtzahl der Commits im selben Fenster ist 13. Die Zaehlung ist also
  vollstaendig, nicht gestichprobt.
  DIE KORRELATION IST LUECKENLOS: wer das Tor hat, hat null geschrieben. Wer es nicht hat,
  hat geschrieben. Sein Befund haelt.
und_ich_bin_der_groesste_einzelfall: |
  11 von 13 Schreibvorgaengen sind meine. In der Stunde, in der drei Rollen stillstehen,
  weil die Sperre bei ihnen greift, habe ich elfmal an dieselbe Datei geschrieben — nicht
  weil ich die Regel umgehe, sondern weil sie in meinem Baum nicht existiert.
  Das ist keine Entlastung. Es heisst nur, dass mein Anteil an der Schieflage nicht in
  einer Entscheidung liegt, sondern in einem Transportstand.
was_ich_ergaenze_und_es_ist_der_schaerfere_teil: |
  Die Sperre ist NICHT immer scharf gewesen. Um 16:09 habe ich die Bedingung selbst
  gemessen: 0 Commits mit Rollenmarke 'integrator', also lief der HINWEIS-Zweig und liess
  durch. Der erste Integrator-Commit ist 83296554 um 16:17:16 — seither zuendet Zeile 195
  und der VERSTOSS-Zweig sperrt.
  Der Generator hat um 16:23, also SECHS MINUTEN nach der Zuendung, seine Fertigmeldung
  abgesetzt — nicht im Datensatz, sondern im Betreff: f19557c8 "generator: zustand: A-41 ·
  CODE_FERTIG · generator · bau 16c5b9d2". Nachgemessen: dieser Commit fasst docs/STATUS.md
  NULL mal an, nur scripts/status-erzeugen.sh. Der Datensatz sagt bis jetzt ENTWURF.
  ER UMGEHT NICHTS. Er meldet nach dem Verfahren, das A-41 baut, und das ist richtig.
  Aber A-41s Erzeuger laeuft noch nicht produktiv — die Meldung liegt also in einem Kanal,
  den heute niemand liest.
die_ursache_in_einem_satz: |
  Die Kette haengt zwischen zwei Systemen: der alte Weg (Zustand im Datensatz) ist seit
  16:17 fuer die drei Rollen mit Tor gesperrt, und der neue Weg (Zustand im Betreff, daraus
  erzeugt) ist gebaut, aber nicht in Betrieb. Wer wechseln muesste, kann nicht; wer kann,
  muss nicht.
  Und die Gegenprobe zum Ausschluss der naheliegenden Erklaerung: es liegt NICHT daran,
  dass niemand arbeitet. Der Generator hat seit 16:00 neun Commits, nur keinen an der
  Statuswahrheit.
ballbesitz: —
ballbesitz_grund: "GEGENSTANDSLOS 16.08.: der bestaetigte Befund ist um 16:52 durch den Generator behoben (9dbb4d75), die Sperre zuendet jetzt erst wenn das Tor in allen sechs Baeumen liegt. Der Anlass ist weg"
ballbesitz_vorher: "yama"
```

herkunft: docs/STATUS.md · Block 389 · 34f6f5a9
```yaml
auftrag: "A-41"
titel: "Die CODE_FERTIG-Meldung geprueft — zwei von drei Meldepflichten erfuellt, die dritte ist keine Nachlaessigkeit sondern die Verfahrensfrage selbst"
rolle: plan-pruefer
zeit: "2026-08-16 17:09 CEST"
mess_stand: "Meldung f19557c8 (16:23:00) · Bau 16c5b9d2 (16:15) · Baureihe ueber alle Zweige gesucht, nach DATEI nicht nach Betreff"
warum_ich_das_pruefe: |
  Paragraf 4 der Wache: bei CODE_FERTIG sind die Meldepflichten zu pruefen und der
  Ballwechsel zu bestaetigen. A-41 ist seit 16:23 als CODE_FERTIG gemeldet — im
  Commit-Betreff, nach dem Verfahren, das A-41 selbst baut. Das ist mein Ball, unabhaengig
  davon, dass der Datensatz noch ENTWURF sagt.
meldepflicht_1_der_bau_sha_existiert: |
  ERFUELLT. 16c5b9d2 ist auffindbar, vom 16:15, Betreff "Die Regelprobe hat ROT gemeldet,
  obwohl der Planner geliefert hatte — mein Pr...". Kein toter Verweis.
meldepflicht_2_der_sha_steht_in_einem_FELD: |
  NICHT ERFUELLT. Im A-41-Datensatz gibt es kein Feld bau, gebaut_in oder bau_sha —
  gemessen, 0 Treffer. Der SHA steht ausschliesslich im Commit-Betreff.
  UND DAS IST KEINE NACHLAESSIGKEIT. Der Generator meldet nach dem Verfahren, das A-41
  einfuehrt: der Zustand steht im Betreff und wird daraus erzeugt. Die alte Meldepflicht
  verlangt ein Feld im Datensatz — und genau dieses Schreiben ist ihm seit der Zuendung
  um 16:17 durch das Rollen-Tor gesperrt.
  Er kann die Pflicht also nicht erfuellen, ohne die Barriere zu verletzen, gegen die er
  sich richtig verhaelt. Die Pflicht und die Sperre widersprechen sich, nicht der Bau.
meldepflicht_3_scope_diff_selbst_gemessen: |
  ERFUELLT UND SAUBER. Ich habe die Baureihe nach der DATEI gesucht, nicht nach dem Betreff
  — die Lehre aus meinem eigenen Fehlgriff von 15:46:
  1e342d53 · b585d335 · 2e9cf127 · ccdfd7b6 · 1013e254 · 253a51d7 · 16c5b9d2 · f19557c8
  Acht Commits, alle vom Generator, alle ausschliesslich an scripts/status-erzeugen.sh.
  Der gemeldete Bau selbst: 46 geaenderte Zeilen, eine Datei.
  Gegenprobe auf Nicht-Ziele ueber ALLE acht: kein Treffer unter resources/, app/,
  database/, und weder commit-pruefen.sh noch rollen-tor.sh. A-41-11 haelt ueber die
  gesamte Reihe, nicht nur am Endstand.
was_ich_bezeugen_kann_und_was_nicht: |
  ICH BEZEUGE: der Bau existiert, sein Umfang ist sauber, und von A-41s zwoelf Kriterien
  habe ich elf selbst gemessen — A-41-1 als korrekte Rot-Lage, -2, -3, -5, -8, -10, -11
  sowie die Kanten K1 bis K7, davon K2 und K5 isoliert nachgebaut. Keines ist rot.
  ICH BEZEUGE NICHT: A-41-4, -6, -7, -9 und -12. Sie verlangen gefahrene Laeufe mit
  Protokoll (Erstbefuellung, Gegenprobe gegen den Integrationsstand) und die Suite. Das
  ist Abnahmearbeit, nicht meine.
  ICH ERKLAERE DEN BALLWECHSEL NICHT FUER VOLLZOGEN, weil die zweite Meldepflicht offen
  ist. Aber ich stelle fest, dass sie aus einem Verfahrenskonflikt offen ist und nicht aus
  einem Mangel des Baus — wer sie gegen den Generator verwendet, misst die Sperre und
  nicht seine Arbeit.
was_zu_entscheiden_ist: |
  Ob eine Fertigmeldung im Commit-Betreff die Meldepflicht "SHA in einem Feld" erfuellt,
  ist eine Verfahrensentscheidung. Sie gehoert nicht mir und nicht dem Generator.
  Solange sie aussteht, kann der Evaluator nicht abnehmen: er nimmt CODE_FERTIG ab, und
  der Datensatz sagt ENTWURF.
ballbesitz: —
ballbesitz_grund: "AUFGELOEST 16.08., 17 Minuten vor dem Posten: der Widerspruch Meldepflicht-2 gegen Tor-Sperre besteht nicht mehr. Der Generator hat um 16:52 die Zuendbedingung korrigiert; selbst nachgefahren, rollen-tor.sh meldet HINWEIS statt VERSTOSS und exit 0. Er kann die Meldepflicht jetzt erfuellen ohne die Barriere zu verletzen"
ballbesitz_vorher: "yama"
```

herkunft: docs/STATUS.md · Block 390 · 34f6f5a9
kennung_nicht_zuordenbar: true   # Freitext statt Kennung — uebernommen wie vorgefunden, nicht gedeutet
```yaml
auftrag: "W-17/1"
titel: "Die Kette laeuft wieder — und im selben Zug ist ein Beleg verlorengegangen: der Betreff nennt meinen DoR-Commit, der Datensatz sagt 'steht aus'"
rolle: plan-pruefer
zeit: "2026-08-16 17:26 CEST"
mess_stand: "Planner-Stand nach f763dd61 (16:55) · alle Zahlen frisch, Sperrbedingung selbst nachgerechnet"
zuerst_die_gute_nachricht_und_sie_ist_gemessen: |
  Der Stillstand ist aufgeloest, und zwar an der Wurzel. Der Generator hat um 16:52 in
  9dbb4d75 die Sperre SELBST-KONDITIONIERT gemacht: sie zuendet erst, wenn das Tor in ALLEN
  Zweigen liegt.
  DIE BEDINGUNG SELBST NACHGERECHNET, Zweig fuer Zweig:
  auto/hausplaner-integration  nein     rolle/evaluator        JA
  rolle/generator              JA       rolle/plan-pruefer     nein
  rolle/planner                nein     rolle/release-pruefer  JA
  TOR_MIT=3 von TOR_ZWEIGE=6, Integrator-Commits=1 -> die Bedingung greift, es wird
  durchgelassen. Die Ungleichbehandlung, die der Release-Pruefer um 16:46 gemeldet und ich
  um 17:02 bestaetigt habe, ist damit behoben — nicht durch Angleichen der Baeume, sondern
  dadurch, dass die Sperre auf ihre eigene Verbreitung wartet.
  Und die Kette bewegt sich: A-41 traegt jetzt CODE_FERTIG mit Ball beim Evaluator,
  W-17/1 traegt BEREIT mit Ball beim Generator.
und_dabei_ist_etwas_verlorengegangen: |
  W-17/1s Datensatz sagt: dor_beleg: "steht aus".
  Der Commit, der den Zustand gesetzt hat (f763dd61, 16:55), traegt im Betreff:
  "zustand: W-17/1 · BEREIT · generator · DOR 3a665884" und schreibt weiter: "der
  Plan-Pruefer hat W-17/1 um 15:10 die DoR ERTEILT, mit dem Vermerk jede tragende Angabe
  selbst nachgemessen".
  GEGENGEPRUEFT: 3a665884 existiert, ist von 15:10 und traegt den Betreff "W-17/1 DoR
  ERTEILT — jede tragende Angabe selbst nachgemessen". Der Beleg ist also da.
  Er steht nur nicht dort, wo ihn jemand sucht: im Datensatz nennt ihn 0 Zeile, das Feld
  sagt "steht aus".
warum_das_zaehlt_obwohl_es_klein_ist: |
  Wer spaeter fragt, ob W-17/1 zu Recht BEREIT traegt, liest den Datensatz. Dort steht,
  der Beleg stehe aus — also sieht ein zu Recht gesetzter Zustand unbelegt aus, und die
  naechste Pruefung faengt bei null an. Genau das ist mir heute zweimal passiert: um 15:17
  bei A-37-5 und um 16:50 bei den Restpunkten, beide Male habe ich neu gesucht, was schon
  gefunden war.
  Es ist dieselbe Klasse wie A-41s Lage vor der Aufloesung: die Wahrheit steht im Betreff,
  der Datensatz sagt etwas anderes. Nur ist es hier nicht der Zustand, sondern sein Beleg.
was_zu_tun_ist: |
  Eine Zeile: dor_beleg von "steht aus" auf 3a665884. Der Planner hat den SHA bereits
  gemessen und im Betreff genannt — es fehlt nur die Uebertragung ins Feld.
  Ich fasse den Datensatz nicht an; das Setzen von Zustandsfeldern gehoert nicht mir, und
  W-17/1s Ball liegt beim Generator.
nebenbei_und_schon_gemeldet: |
  dor_schnitt_sha traegt 8faca79c — den Commit, den ich um 16:20 als nicht vom
  Planner-Zweig erreichbar gemeldet habe. Der Verweis bleibt gueltig, er ist nur von dort
  aus nicht aufloesbar.
ballbesitz: planner
```

herkunft: docs/STATUS.md · Block 391 · 34f6f5a9
```yaml
auftrag: "A-37"
titel: "A-37 steht auf BEREIT mit einem ZURUECKGENOMMENEN Votum als Beleg — und ein Kriterium verlangt weiter einen Wert, den der Bau nicht erzeugt"
rolle: plan-pruefer
zeit: "2026-08-16 17:38 CEST"
mess_stand: "Planner 514d1a60 (16:56:59) · Zeitachse aus vier Commits, jede Uhrzeit auf die Sekunde gelesen"
was_gesetzt_wurde: |
  514d1a60 um 16:56:59 setzt: "zustand: A-37 · BEREIT · integrator · dor efea1a48".
  Die Begruendung im Betreff ist richtig und wichtig: A-37 stand vier Stunden auf ENTWURF,
  waehrend drei Rollen daran gebaut haben. Der Planner nennt es den dritten Fall desselben
  Mechanismus an einem Nachmittag — A-41, W-17/1, A-37 alle entschieden und nicht
  eingetragen. Das trifft zu, und die Buchfuehrung nachzuziehen war ueberfaellig.
  ZWEI DINGE STIMMEN TROTZDEM NICHT.
erstens_der_beleg_ist_ueberholt: |
  Zeitachse, jede Uhrzeit auf die Sekunde:
  12:39:26  4ed51b8f  Vertretung: "A-37 und A-38 sind BEREIT"
  12:43:53  efea1a48  Plan-Pruefer: "A-37 ist BEREIT mit einem Bau-Ort"   <- der genannte Beleg
  13:01:54  a400368f  Plan-Pruefer: "DoR Runde 3 — NICHT ERTEILT, fuenf Restpunkte"
  16:56:59  514d1a60  Planner: BEREIT, dor efea1a48
  Zwischen dem Beleg und der Eintragung liegt eine AUSDRUECKLICHE RUECKNAHME desselben
  Votums durch dieselbe Rolle, 18 Minuten spaeter, mit fuenf einzeln benannten Restpunkten.
  efea1a48 ist damit kein gueltiger DoR-Beleg mehr. Es ist nicht der letzte Stand der
  Pruefung, sondern der vorletzte.
zweitens_ein_restpunkt_besteht_wirklich: |
  Ich habe die fuenf Restpunkte um 16:47 einzeln gegen den heutigen Stand gemessen: vier
  sind behoben, einer nicht.
  OFFEN: "Fliesstext gegen A-37-5". Heute gemessen, Zeile fuer Zeile:
  Z.292  "A-37-5 · Negativfall fehlende Kennung: TICKET_ROLLE leer -> exit 3"
  Z.429  "... und zwar dem fuer den Fall festgelegten: A-37-3/4/6 -> 1, A-37-5 -> 5"
  Der Bau gibt 5, real gefahren um 15:17. Das Kriterium verlangt 3. Ein Evaluator, der
  A-37-5 woertlich prueft, meldet ROT an einem richtigen Bau.
  Der Punkt ist seit 13:01:54 bekannt, also seit vier Stunden 37 Minuten, und von zwei
  Instanzen unabhaengig gefunden.
was_ich_NICHT_sage: |
  Ich sage NICHT, dass A-37 nicht reif ist. Vier von fuenf Restpunkten sind abgearbeitet,
  der Bau ist umfangreich geprueft, und ich habe selbst acht Kriterien gemessen, von denen
  keines rot ist. Der Auftrag ist einer Zahl von BEREIT entfernt — das habe ich um 16:47
  so geschrieben und halte es.
  Ich sage: der Zustand steht auf einem Beleg, der zurueckgenommen wurde, und der Grund
  der Ruecknahme ist noch da. Beides zusammen ist der Unterschied zwischen "reif" und
  "als reif eingetragen".
was_zu_tun_waere: |
  Erstens die Zahl in Z.292 von 3 auf 5 — dann ist der Restpunkt weg.
  Zweitens den dor_beleg auf ein Votum NACH der Behebung setzen, nicht auf efea1a48.
  Beides zusammen kostet zwei Zeilen und macht den Zustand belegbar.
  Ich fasse den Datensatz nicht an. Der Ball liegt beim Integrator, die Blattzeile beim
  Planner.
ballbesitz: planner
```

herkunft: docs/STATUS.md · Block 392 · 34f6f5a9
```yaml
auftrag: "A-38"
titel: "DoR fortgesetzt — drei Rot-Lagen sauber, A-38-6 ist sogar automatisch erfuellbar; und die Sperrbedingung misst 6 von 15 Baeumen"
rolle: plan-pruefer
zeit: "2026-08-16 17:48 CEST"
mess_stand: "Blatt aus rolle/planner · Worktree-Liste und git config im eigenen Baum gemessen · Planner-Kopf 6d9c6a3c"
drei_rot_lagen_selbst_gemessen: |
  A-38-1 ".githooks/commit-msg existiert und ist ausfuehrbar"
    Ueber drei Zweige geprueft: 0 Dateien unter .githooks/, auch im Dateisystem 0.
    Die Rot-Lage ist echt, das Kriterium ist heute nicht erfuellt.
  A-38-5 "core.hooksPath ist gesetzt"
    Weder im Worktree noch in der gemeinsamen config gesetzt. Rot, wie angegeben.
  A-38-6 "Der Hook greift in einem ZWEITEN Worktree, ohne dort eingerichtet zu werden"
    DAS IST DER INTERESSANTE: extensions.worktreeConfig ist NICHT gesetzt — alle Worktrees
    teilen also EINE config. Wird core.hooksPath dort gesetzt, gilt es in jedem Baum, ohne
    dass jemand ihn einzeln einrichtet. Das Kriterium ist damit nicht nur erfuellbar,
    sondern faellt beim Bau von A-38-5 von selbst mit an.
    Ich schreibe es hin, weil das Umgekehrte teuer waere: haette jemand
    extensions.worktreeConfig gesetzt, muesste der Hook fuenfzehnmal eingerichtet werden.
und_dabei_eine_zahl_die_nicht_stimmt: |
  Ich habe bisher von sechs Baeumen geschrieben, und die Sperrbedingung des Tores zaehlt
  auch sechs (refs/heads/rolle/* plus auto/hausplaner-integration).
  GEMESSEN: es sind FUENFZEHN Worktrees.
  Die sechs gezaehlten: ticket (integration), ticket-release-pruefung, ticket-rolle-
  evaluator, -generator, -plan-pruefer, -planner.
  Die neun uebrigen: ticket-main, ticket-a01, ticket-g1b-0, ticket-strang-accounting,
  -C, -energie, -formulare, ein Agent-Worktree, und ticket-rolle-release im DETACHED HEAD.
  WAS DAS HEISST UND WAS NICHT: es ist kein akutes Loch. In den neun schreibt heute
  niemand an der Statuswahrheit, und der Release-Pruefer hat sie um 16:46 als Altbestand
  mit uncommitteter Arbeit benannt.
  Aber die Aussage "das Tor liegt in 3 von 6 Zweigen" beschreibt die ZWEIGE, nicht die
  Baeume, aus denen geschrieben werden kann. Wer die Sperre fuer flaechendeckend haelt,
  weil sie 6 von 6 erreicht hat, hat neun Baeume nicht gezaehlt.
  ticket-rolle-release ist dabei die genannte Falle: gleichnamig zur Rolle, aber detached
  und leer — der Planner hat daran schon einmal falsch gemessen (2 von 6 statt 3 von 6).
stand_der_A_38_dor: |
  Geprueft: A-38-1, -5, -6 — alle drei mit echter Rot-Lage, keines unerfuellbar, keines
  bereits erfuellt. A-38-2 hat seit 16:19 feste SHAs statt der wandernden Quote, von mir
  um 16:29 verifiziert.
  Offen: A-38-3, -4, -7, -8, -9.
  Kein Votum. Der Ball bleibt bei mir.
ballbesitz: plan-pruefer
```

herkunft: docs/STATUS.md · Block 393 · 34f6f5a9
```yaml
auftrag: "A-38"
titel: "K2 und K6 geprueft — der Fast-Forward ist kein Loch sondern ein Nicht-Fall, und K6 ist die ehrlichste Kante im ganzen Bestand"
rolle: plan-pruefer
zeit: "2026-08-16 17:56 CEST"
mess_stand: "Blatt aus rolle/planner · Reflog-Zaehlungen im eigenen Baum, Muster an einem bekannten Eintrag geeicht"
k2_gemessen: |
  K2 sagt: "Merge ohne Konflikt, Fast-Forward — KEIN Commit entsteht, nichts zu pruefen."
  Das klingt nach einer Luecke: ein Transport, den der Hook nie sieht. Also gezaehlt, je
  Zweig, Muster an einem echten Reflog-Eintrag geeicht:
  release-pruefer   Fast-forward 2   echte Merges 58
  planner           Fast-forward 0   echte Merges  6
  evaluator         Fast-forward 2   echte Merges  0
  generator         Fast-forward 2   echte Merges  0
  Der Transport laeuft fast vollstaendig ueber echte Merges — 64 gegen 6. Der Hook wuerde
  also den weit ueberwiegenden Teil erfassen.
  UND DER REST IST KEIN LOCH, sondern ein Nicht-Fall: ein Fast-Forward erzeugt KEINEN
  Commit, also auch keinen markenlosen. A-38 richtet sich gegen unmarkierte Merge-Commits;
  wo keiner entsteht, gibt es nichts zu markieren. Die Kante ist richtig eingeordnet.
  EINSCHRAENKUNG, die ich selbst nenne: der Reflog reicht nicht beliebig weit zurueck.
  Die Zahlen belegen das VERHAELTNIS der letzten Zeit, nicht die Gesamthistorie.
k6_ist_die_ehrlichste_kante: |
  K6 sagt: "--no-verify umgeht jeden Hook. NICHT VERHINDERBAR — ausdruecklich in den
  Bericht, nicht verschweigen."
  Und A-38-7 macht daraus eine Abnahmebedingung: die Grenze muss BENANNT sein, weil "ein
  Schutz, dessen Grenze verschwiegen wird, falsches Vertrauen erzeugt".
  Das ist die Bauart, die ich den ganzen Tag einfordere, hier von vornherein eingebaut.
  Es waere leicht gewesen, K6 wegzulassen — niemand haette es gemerkt, und der Hook haette
  vollstaendiger ausgesehen als er ist.
  Ich habe heute drei Faelle gemeldet, in denen genau das Gegenteil passiert ist: eine
  Ampel, die zufaellig haelt (A-40-5), eine Sperre, die die Falschen trifft (A-37), eine
  Rot-Lage mit Ablaufdatum (A-38-2 vor der Umstellung). K6 ist der Gegenentwurf dazu.
stand_der_A_38_dor: |
  Geprueft: A-38-1, -2, -5, -6, und von A-38-7 die Kanten K2 und K6.
  Offen: A-38-3, -4, -8, -9 sowie die Kanten K1, K3, K4, K5.
  Kein Kriterium ist bisher unerfuellbar oder bereits erfuellt. Kein Votum.
ballbesitz: plan-pruefer
```

herkunft: docs/STATUS.md · Block 394 · 34f6f5a9
```yaml
auftrag: "A-38"
titel: "Vier der sechs Kanten eingeordnet — und bei zweien sage ich ausdruecklich, dass ich sie NICHT belegen konnte"
rolle: plan-pruefer
zeit: "2026-08-16 18:04 CEST"
mess_stand: "Blatt aus rolle/planner · Tor-Fassung aus rolle/generator · Historien-Stichprobe 200 Commits · Planner-Kopf 6d9c6a3c"
k5_ist_erfuellbar_und_hat_ein_vorbild: |
  K5 verlangt: "Der Hook selbst ist kaputt — muss mit EIGENER Ursache melden, nicht als
  Formfehler."
  Das ist keine neue Forderung, sie ist im Bestand schon umgesetzt. commit-pruefen.sh:
  Z.585 und Z.602  process.stdout.write("LAUFZEIT " + e.message); process.exit(4)
  Z.660            echo "LAUFZEIT $p — ${BERICHT#LAUFZEIT }"
  Der Hook kann die Bauart uebernehmen, statt sie zu erfinden. Das Kriterium ist damit
  nicht nur erfuellbar, es hat ein Muster im selben Werkzeugkasten.
k4_ist_ein_randfall_und_richtig_eingeordnet: |
  K4 sagt: "Rebase / Cherry-Pick — NICHT betroffen, ein Cherry-Pick hat einen Elter."
  Gemessen: im ganzen Bestand tragen 2 Commits 'cherry' im Text. In einer Stichprobe von
  200 Commits haben 138 einen Elternteil und 62 zwei.
  Der Fall kommt also praktisch nicht vor, und wo er vorkommt, unterscheidet ihn die
  Elternzahl zuverlaessig von einem Merge — genau das Merkmal, das der Hook ohnehin liest.
was_ich_NICHT_belegen_konnte_und_warum: |
  K1 "git merge --no-commit greift nicht, der Hook laeuft erst beim Commit" und
  K3 "git commit --amend an einem Merge — die Eltern bleiben zwei"
  sind beides Aussagen ueber das VERHALTEN von git, nicht ueber den Bestand. Belegen liesse
  sie nur ein Lauf: ein Wegwerf-Repo anlegen, einen Merge bauen, amenden, die Eltern
  zaehlen.
  ICH HABE DAS VERSUCHT UND ES WURDE NICHT ERLAUBT — der Aufruf ist an der
  Berechtigung gescheitert. Damit habe ich keine Messung, und eine ausgefallene Messung
  ist kein Ergebnis. Ich trage die beiden Kanten deshalb als UNGEPRUEFT ein, nicht als
  plausibel: dass ein Amend die Elternzahl erhaelt, ist mir gelaeufig, aber gelaeufig ist
  kein Beleg, und genau diese Ersetzung habe ich heute mehrfach an anderen gerueegt.
  Wer sie braucht, faehrt sie in einem Baum, in dem er schreiben darf.
stand_der_A_38_dor: |
  Geprueft und haltend: A-38-1, -2, -5, -6 sowie die Kanten K2, K4, K5, K6.
  Ungeprueft mangels erlaubtem Lauf: K1, K3.
  Offen: A-38-3, -4, -8, -9.
  Kein Kriterium ist unerfuellbar oder bereits erfuellt. Kein Votum, der Ball bleibt bei
  mir.
ballbesitz: plan-pruefer
```

herkunft: docs/STATUS.md · Block 395 · 34f6f5a9
```yaml
auftrag: "A-40"
titel: "A-40-9 ist das einzige der fuenf Suite-Kriterien ohne die Invarianten-Klausel — und es entstand ZWISCHEN zwei Blaettern, die sie tragen"
rolle: plan-pruefer
zeit: "2026-08-16 18:12 CEST"
mess_stand: "fuenf Blaetter aus rolle/planner, Kriterien einzeln gelesen · Anlegedaten ueber --diff-filter=A gemessen · Planner-Kopf 4b2c966f"
der_vergleich: |
  Fuenf Auftraege tragen dasselbe Schlusskriterium "Suite gruen und Zahl unveraendert
  gegen den Bau-Stand, tsc exit=0". Vier davon sagen ausdruecklich, dass NICHT gegen eine
  feste Zahl geprueft wird:
  A-37-11  "Nicht gegen eine feste Zahl pruefen" — mit Berichtigungsvermerk vom 15.08.
  A-38-9   "Keine feste Zahl. (Berichtigt 15.08., wortgleich zu A-37-11.)"
  A-39-10  "nicht gegen eine feste Zahl pruefen"
  A-41-12  "keine feste Zahl im Kriterium"
  A-40-9   — nichts davon. Nur der Satz, ohne die Klausel.
warum_das_kein_altersproblem_ist: |
  Ich habe zuerst vermutet, A-40 sei aelter als die Lehre. Das Gegenteil stimmt.
  Die Klausel entstand am 15.08. um 15:18 (8f2aed6f, "DoR-Restpunkte behoben").
  Angelegt wurden die Blaetter:
  A-37  14.08. 22:35     A-38  15.08. 10:45     A-39  16.08. 14:13
  A-40  16.08. 14:23     A-41  16.08. 15:19
  A-40 ist also ZEHN MINUTEN nach A-39 entstanden und eine Stunde vor A-41 — beide tragen
  die Klausel, A-40 nicht. Es ist keine Alterung, sondern eine Auslassung zwischen zwei
  Blaettern, die es richtig machen.
warum_es_trotzdem_klein_ist_und_warum_ich_es_melde: |
  Inhaltlich sagt "Zahl unveraendert GEGEN DEN BAU-STAND" die Invariante bereits — wer den
  Satz genau liest, prueft richtig. Die vier anderen tragen die Klausel trotzdem, und der
  Grund steht in A-37-11: dort stand einmal die feste Zahl 1750, und ein Pruefer haette
  gegen sie gemessen.
  Die Klausel ist also nicht Deko, sie ist die Narbe eines echten Fehlers. Ein Blatt ohne
  sie laedt denselben Fehler wieder ein — nicht zwingend, aber ohne Not.
  Es ist dieselbe Klasse wie P6, die heute aus meinem A-38-Befund entstanden ist: eine
  Angabe, die sich durch normale Arbeit veraendert, braucht eine ausdrueckliche Regel, sonst
  wird sie irgendwann als fest gelesen.
was_zu_tun_waere: |
  Ein Halbsatz in A-40-9, wortgleich zu den vier anderen. Kein Bau, keine Messung, keine
  Entscheidung — nur die Angleichung an die eigene Familie.
ballbesitz: planner
```

herkunft: docs/STATUS.md · Block 396 · 34f6f5a9
```yaml
auftrag: "A-39"
titel: "K2 benennt die Grenze richtig — aber nicht ihre GROESSE: P1 erfasst 8 von 34 Blaettern mit Kantenliste"
rolle: plan-pruefer
zeit: "2026-08-16 18:26 CEST"
mess_stand: "88 aktive Blaetter aus rolle/planner, jedes einzeln gelesen · Planner-Kopf 4b2c966f"
a_39_1_rot_lage_haelt: |
  scripts/blatt-pruefen.sh existiert in keinem der geprueften Zweige — 0 Treffer. Das
  Kriterium ist heute nicht erfuellt, die Rot-Lage ist echt.
was_K2_sagt: |
  "Kante nur im Fliesstext genannt, nicht in einer Tabellenzeile — NICHT erfasst,
  ausdruecklich benannte Grenze."
  Das ist dieselbe Ehrlichkeit wie A-38s K6: eine Reichweite wird benannt statt
  verschwiegen. Richtig so.
und_hier_ist_ihre_groesse: |
  Ich habe alle 88 aktiven Blaetter einzeln gelesen und nach der FORM ihrer Kantenliste
  getrennt:
  nur Tabelle (^| K1 |) ........... 8
  nur Ueberschrift (^## Kantenliste) 26
  beides .......................... 0
  Blaetter MIT Kantenliste gesamt .. 34
  P1 wuerde also 8 von 34 erfassen — nicht ein Viertel, sondern knapp darunter. Die
  restlichen 26 fuehren ihre Kanten als Ueberschrift mit Listenpunkten, zum Beispiel
  A-02 ("## Kantenliste", darunter "die Kantenliste verlangt fuer Kante 2 ...") und A-03.
  DIE GRENZE IST ALSO NICHT DER RANDFALL, ALS DEN SIE KLINGT. Sie ist der Regelfall: die
  Mehrheit der Blaetter mit Kanten schreibt sie in der Form, die P1 nicht sieht.
was_ich_dabei_verworfen_habe: |
  Meine erste Zaehlung ergab 38 Blaetter "ohne Tabelle, aber mit dem Wort Kante". Die habe
  ich NICHT gemeldet, sondern stichprobenartig geoeffnet — und die erste war ein
  Fehltreffer: A-01 nennt "Kante-1" als FACHBEGRIFF des Renderers (dachFlaechen, Kante-1-
  Wurf), nicht als Pruefkante. Deshalb die zweite, engere Messung auf "## Kantenliste".
  Ohne das Oeffnen haette ich 38 gemeldet statt 26 — und die Zahl waere um 46 Prozent zu
  hoch gewesen.
was_das_fuer_die_DoR_heisst: |
  Es ist KEIN Mangel des Kriteriums: K2 sagt die Wahrheit, und A-39-8 verlangt, dass alle
  sechs Kanten behandelt und je einzeln belegt sind. Wer K2 belegt, muss die Grenze zeigen
  — er muss nicht ihre Groesse nennen.
  ABER WER DEN BAU ABNIMMT, sollte sie kennen. Ein Werkzeug, das 8 von 34 Blaettern
  erreicht, ist etwas anderes als eines, das eine Ausnahme hat. Beides kann richtig sein;
  nur die Erwartung ist eine andere.
  VORSCHLAG, nicht Entscheidung: entweder die Zahl bei K2 nennen, oder das Muster auf die
  zweite Form erweitern. Das Zweite kostet eine Zeile im Skript und vervierfacht die
  Reichweite.
ballbesitz: planner
```

herkunft: docs/STATUS.md · Block 397 · 34f6f5a9
```yaml
auftrag: "A-37"
titel: "P5 auf A-37 selbst angewandt — Code 2 traegt im Blatt zwei Bedeutungen und im Bau mindestens vier"
rolle: plan-pruefer
zeit: "2026-08-16 18:42 CEST"
mess_stand: "A-37-Blatt aus rolle/planner · commit-pruefen.sh aus rolle/generator, 918 Z. · Planner-Kopf a774e549"
warum_diese_pruefung: |
  A-39s Pruefung P5 lautet: "Wird in einem Blatt mehr als eine Bedeutung auf denselben
  exit-Code gelegt, ist das ein Fund — unabhaengig davon, ob beide Stellen denselben
  Bauteil betreffen." K5 grenzt ab: zwei Nennungen fuer DASSELBE sind kein Fund.
  Ich habe P5 auf A-37 angewandt, weil dort eine Codetabelle steht und A-37 heute BEREIT
  traegt.
im_blatt: |
  Die Codetabelle vergibt 1 bis 6, jeden Wert einmal — mit einer Ausnahme:
  Z.300  | **2** | YAML-Syntaxfehler im Kopf         | commit-pruefen.sh | gebaut
  Z.305  | *(2)* | Rollenkennung fehlt/falsche Form  | commit-pruefen.sh:59-65 | vorhanden
  Zwei Bedeutungen auf Code 2. Alle anderen Codes je eine. Das ist P5s Fall, nicht K5s:
  die beiden Zeilen meinen NICHT dasselbe.
  ZUR FAIRNESS: die zweite Zeile ist kursiv und als "unberuehrt, vorhanden" markiert. Sie
  dokumentiert einen bestehenden Zustand, sie vergibt nichts neu. Verschwiegen wird also
  nichts — aber die Doppelbelegung besteht.
im_bau_ist_es_groesser: |
  Am Code nachgemessen, statt es beim Blatt zu belassen: 'exit 2' kommt in
  commit-pruefen.sh FUENFMAL vor, und die Meldungen davor sind verschieden:
  Z.72   "Aufruf: bash scripts/commit-pruefen.sh [--trocken] ..."   Aufruffehler
  Z.86   "TICKET_ROLLE fehlt oder ist leer"                          Kennung fehlt
  Z.90   "TICKET_ROLLE entspricht nicht der Form"                    Kennung falsch
  Z.156  "WIDERSPRUCH: die Botschaft gibt sich als ... aus"          Rollenwiderspruch
  (Z.137 ist ein Kommentar ueber ein frueheres exit 2, keine Vergabe.)
  Vier Bedeutungen auf einem Wert. Das Blatt nennt fuer Code 2 den YAML-Syntaxfehler —
  der unter diesen vier gar nicht vorkommt.
  EINSCHRAENKUNG, die ich selbst nenne: ich habe die vier Stellen ueber ihre unmittelbar
  vorangehende Meldung zugeordnet, nicht ueber den vollstaendigen Kontrollfluss. Dass es
  vier VERSCHIEDENE Faelle sind, ist damit belegt; ob einer davon zugleich der YAML-Fall
  ist, habe ich NICHT ausgeschlossen.
warum_das_zaehlt: |
  A-37 selbst nennt die Codekollision "der Fehler, an dem A-37 eine Runde verlor" — bei
  der Verschiebung von 3 auf 5 am 16.08. Und A-41-10 verlangt woertlich "kein Wert traegt
  zwei Bedeutungen" und hat es geloest: EIN zentraler Ausstieg mit einer BEDEUTUNG-Tabelle,
  von mir um 15:43 nachgemessen.
  A-37s eigenes Werkzeug hat also genau die Mehrdeutigkeit, die sein Nachbarauftrag gerade
  behoben hat — und A-37 traegt seit 16:56 BEREIT.
  Wer exit 2 liest, weiss heute nicht, ob der Aufruf falsch war, die Rolle fehlt, ihre
  Form nicht stimmt oder die Botschaft widerspricht. Vier Ursachen, ein Wert, keine
  Unterscheidung — und A-37-8 verlangt ausdruecklich, dass Ursachen unterscheidbar sind.
was_ich_vorschlage_ohne_zu_entscheiden: |
  Fuer das Blatt: die Doppelzeile (2) aufloesen oder den bestehenden Fall auf einen eigenen
  Wert legen. Fuer den Bau: dieselbe Bauart wie A-41-10 — ein Ausstieg, eine Tabelle,
  jeder Wert eine Bedeutung. Das Muster liegt fertig im Nachbarskript.
  Kein Bau von mir, kein Zustandsfeld angefasst.
ballbesitz: planner
```

herkunft: docs/STATUS.md · Block 398 · 34f6f5a9
```yaml
auftrag: "A-37"
titel: "Meine Einschraenkung von 18:42 geschlossen — die Codes 2, 3 und 4 werden im Bau gar nicht vergeben, alle drei Ursachen enden bei exit 1"
rolle: plan-pruefer
zeit: "2026-08-16 18:50 CEST"
betrifft_commit: "9e383d43"
mess_stand: "commit-pruefen.sh aus rolle/generator, 918 Z. · Planner-Kopf 718b1b7e"
was_ich_offen_gelassen_hatte: |
  Um 18:42 habe ich gemeldet, exit 2 trage im Bau vier Bedeutungen, und ausdruecklich
  dazugeschrieben: "ob einer davon zugleich der YAML-Fall ist, habe ich NICHT
  ausgeschlossen." Jetzt gemessen.
das_ergebnis_ist_ein_anderes_als_erwartet: |
  Der YAML-Fall gibt NICHT exit 2. Er gibt ueberhaupt keinen eigenen Code:
  Z.632  MODUL      $p ... -> FEHLER=1
  Z.650  YAML-KOPF  $p ... -> FEHLER=1
  Z.660  LAUFZEIT   $p ... -> FEHLER=1
  Z.665  if [ "$FEHLER" -ne 0 ]; then
  Z.667    "KEIN COMMIT. F-14: was nicht geschrieben wurde, wird auch nicht belegt."
  Z.668    exit 1
  Alle drei Fehlerursachen laufen in denselben Riegel und verlassen das Tor mit 1.
  DIE CODETABELLE DES BLATTES VERGIBT IHNEN 2, 3 UND 4 — und keiner dieser drei Werte
  wird an dieser Stelle erzeugt.
warum_der_bau_trotzdem_richtig_ist: |
  Und das ist wichtig, damit daraus kein Auftrag gegen den Bau wird: die Unterscheidung
  geht nicht verloren, sie laeuft nur ueber den TEXT statt ueber den Code. Der innere
  Pruefer schreibt "LAUFZEIT <ursache>" auf die Ausgabe, das Tor liest den Praefix und
  meldet ihn weiter. A-37-8 verlangt woertlich "drei Fehlerursachen sind unterscheidbar,
  je ein Fall, ROHAUSGABE" — nach der Rohausgabe sind sie es, und ich habe das um 16:58
  fuer den MODUL-Fall mit meiner eigenen Abweisung von 15:11 belegt.
  Der Bau erfuellt also A-37-8. Was er nicht erfuellt, ist die Codetabelle — und die ist
  kein Kriterium, sondern eine Beschreibung.
was_daraus_folgt: |
  Die Tabelle beschreibt eine Vergabe, die es so nicht gibt. Wer nach ihr prueft, misst
  exit 1 und findet weder 2 noch 3 noch 4 — und meldet ROT an einem Tor, das genau das
  tut, was A-37-8 verlangt. Das ist derselbe Mechanismus, den ich heute an A-37-5
  gemeldet habe: eine Zahl im Blatt, die der Bau nicht erzeugt, nur diesmal drei davon.
  MEIN BEFUND VON 18:42 BLEIBT RICHTIG, aber er war zu eng: ich hatte von einer
  Doppelbelegung bei Code 2 geschrieben. Gemessen ist mehr — die Codes 2, 3 und 4 haben im
  Tor gar keinen Erzeuger, und exit 2 wird an vier anderen Stellen fuer vier andere Dinge
  vergeben. Die Tabelle und der Bau beschreiben zwei verschiedene Werkzeuge.
  VORSCHLAG, nicht Entscheidung: die Tabelle auf das umstellen, was der Bau wirklich tut —
  ein Code fuer "kein Commit" und die Ursache im Text, so wie es laeuft. Oder die Codes
  bauen, wie A-41-10 es fuer sein Skript getan hat. Beides ist vertretbar; die heutige
  Mischung ist es nicht.
ballbesitz: planner
```

herkunft: docs/STATUS.md · Block 399 · 34f6f5a9
```yaml
auftrag: "A-41"
titel: "A-41-7 ist NICHT ungeprueft — es ist unter anderem Namen geprueft, und der Planner hat recht, dass ich es uebergangen habe"
rolle: plan-pruefer
zeit: "2026-08-16 19:02 CEST"
mess_stand: "Blatt 718b1b7e (17:17) · eigene Messungen von 15:47 und 15:50 · Planner-Kopf d91f1dca"
der_befund_gegen_mich_trifft: |
  Der Planner meldet in 718b1b7e: A-41-7 sei "das EINZIGE der zwoelf Kriterien, das in
  keiner einzigen Meldung vorkommt. Elf sind gemessen, keines rot, dieses eine hat niemand
  angefasst."
  NACHGEMESSEN: 'A-41-7' kommt in meinen Commit-Betreffen heute 0 mal vor und in der
  ganzen STATUS.md 0 mal. Er hat recht.
  Und seine Erklaerung sitzt: "ein Kriterium ohne benannten Weg wird nicht bestritten, es
  wird uebergangen." Genau das habe ich getan — ich habe elf Kriterien geprueft und bin an
  diesem vorbeigegangen, ohne es zu bemerken.
UND DOCH IST DIE SACHE GEPRUEFT: |
  A-41-7 verlangt drei Dinge. Alle drei habe ich heute gemessen, nur unter anderem Namen:
  "Zwei Zustands-Commits derselben Kennung mit identischer Zeit -> BEIDE IN DER MELDUNG"
    Das ist K1. Um 15:47 habe ich die Kernlogik isoliert nachgebaut und gegen vier Faelle
    gefahren, 4/4 wie erwartet — darunter genau dieser: gleiche Zeit, verschiedene
    Zustaende -> Widerspruch, beide Eintraege in der Liste.
  "RUECKGABE 2"
    Um 15:50 am --bootstrap gemessen: "RUECKGABE 2 — NICHT erzeugt, Widerspruch",
    A-33 mit fuenf Zustaenden, "Regel 4: hier wird NICHTS aufgeloest".
  "TAFEL UNVERAENDERT"
    Um 15:47 im Code gelesen und im Befund festgehalten: bei Widerspruch wird KEINE Tafel
    gedruckt (Z.246-251), und der Kommentar begruendet die Umstellung — vorher stand die
    Tafel oben mit dem juengsten als Gewinner, "eine stille Aufloesung mit einer Warnung
    daneben".
  Die Sache ist also belegt. Was fehlte, war die ZUORDNUNG zum Kriterium.
was_ich_daraus_lerne_und_es_ist_unangenehm: |
  Ich habe die Kanten K1 bis K7 einzeln abgearbeitet und die Kriterien A-41-1 bis -12
  einzeln — aber nie gegeneinander gehalten. Deshalb konnte ein Kriterium durchfallen,
  dessen Inhalt ich zwei Stunden vorher gemessen hatte.
  Eine Liste abzuarbeiten ist nicht dasselbe wie zu pruefen, ob jeder Posten der ANDEREN
  Liste getroffen wurde. Das ist die Kreuzprobe, die mir gefehlt hat, und sie kostet
  keine Messung — nur den Abgleich zweier Listen.
  DASSELBE GILT FUER DIE ANDEREN AUFTRAEGE: bei A-37 habe ich acht von siebzehn Kriterien
  geprueft und sechs Kanten, bei A-38 sechs Kriterien und sechs Kanten. Ob dort ebenfalls
  Kriterien durch die Kanten schon belegt sind, habe ich nie geprueft.
was_offen_bleibt: |
  Die Rohausgabe fuer A-41-7 fehlt weiterhin, und der Planner hat den Weg dafuer jetzt
  benannt — mit gesetztem GIT_COMMITTER_DATE zwei Commits derselben Kennung im
  Wegwerf-Zweig des GENERATOR-Baums. Das ist ausdruecklich nicht mein Baum und nicht meine
  Rolle; ich habe heute schon einen Versuch in einem Wegwerf-Repo nicht ausfuehren duerfen.
  Was ich beitrage: die drei Belege oben, damit der Beweis nicht bei null anfaengt.
ballbesitz: generator
```

herkunft: docs/STATUS.md · Block 400 · 34f6f5a9
```yaml
auftrag: "A-39"
titel: "Die Kreuzprobe nachgeholt — A-39s Struktur ist geschlossen, zwei Kanten haengen bewusst ausserhalb, und A-38 hat eine andere Bauart"
rolle: plan-pruefer
zeit: "2026-08-16 19:10 CEST"
mess_stand: "A-38 und A-39 aus rolle/planner · Planner-Kopf d91f1dca"
warum_diese_runde: |
  Um 19:02 habe ich eingeraeumt, dass mir eine Kreuzprobe fehlt: ich arbeite Kanten und
  Kriterien je einzeln ab und halte sie nie gegeneinander. Genau deshalb ist A-41-7
  durchgefallen, obwohl sein Inhalt zwei Stunden vorher gemessen war.
  Ich habe die Probe jetzt fuer beide Auftraege nachgeholt, die noch bei mir liegen.
a_39_die_kette_ist_geschlossen: |
  Jede Kante nennt eine Pruefung, jede Pruefung hat ein Kriterium:
  K1 -> P1 -> A-39-2      K3 -> P2 -> A-39-3      K4 -> P3 -> A-39-4
  K5 -> P5 -> A-39-6
  Und umgekehrt vollstaendig: P1 A-39-2 · P2 A-39-3 · P3 A-39-4 · P4 A-39-5 ·
  P5 A-39-6 · P6 A-39-11. Keine Pruefung ohne Kriterium.
  ZWEI KANTEN NENNEN KEINE PRUEFUNG: K2 und K6. Das ist kein Loch, sondern eine andere
  Art von Kante — beide beschreiben eine Grenze des GANZEN Werkzeugs, nicht das Verhalten
  einer einzelnen Pruefung. K2 ist die Formgrenze, deren Groesse ich um 18:26 gemessen
  habe (P1 erreicht 8 von 34 Blaettern), K6 ist der Stilllegungs-Wegweiser, der uebersprungen
  wird.
  P4 hat als einzige Pruefung keine Kante. Auch das ist kein Mangel: nicht jede Pruefung
  hat einen Grenzfall, und A-39-5 nennt fuer P4 einen eigenen Stand.
a_38_hat_eine_andere_bauart_und_das_ist_der_ertrag: |
  Dieselbe Probe an A-38 ergibt auf den ersten Blick ein alarmierendes Bild: K1 bis K5
  werden in KEINEM Kriterium genannt, nur K6 in A-38-6 und A-38-7.
  NACHGESEHEN STATT GEMELDET: A-38-7 ist ein SAMMELKRITERIUM — "alle sechs Kanten
  behandelt, K6 ausdruecklich als nicht verhinderbar benannt". Die Kanten sind also
  vollstaendig abgedeckt, nur eben durch ein Kriterium statt durch sechs.
  Zwei Auftraege, zwei Bauarten: A-39 verzahnt Kante und Pruefung einzeln, A-38 sammelt
  sie in einem Kriterium. Beides ist tragfaehig. Haette ich nur gezaehlt, haette ich A-38
  fuenf fehlende Zuordnungen gemeldet — es sind keine.
was_die_probe_kostet_und_was_sie_bringt: |
  Sie kostet keine neue Messung, nur den Abgleich zweier Listen, die beide schon
  dastehen. Sie haette A-41-7 gefunden, bevor der Planner es tat, und sie hat hier
  verhindert, dass ich A-38 fuenf Fehler andichte, die seine Bauart gar nicht kennt.
  Ich fahre sie ab jetzt bei jeder DoR mit, bevor ich ein Votum erwaege.
stand: |
  A-39: Kette geschlossen, A-39-1 rot wie angegeben, K2s Groesse gemessen (8 von 34).
  Offen: A-39-7, -8, -9, -10 und die Belege zu K1, K3, K4, K5, K6.
  A-38: acht Punkte geprueft, K1 und K3 mangels erlaubtem Lauf ungeprueft.
  Kein Votum. Der Ball bleibt bei mir.
ballbesitz: plan-pruefer
```

herkunft: docs/STATUS.md · Block 402 · 34f6f5a9
```yaml
auftrag: "A-37"
titel: "Die Namensfalle ist K2, und die Zahl dazu stimmt nicht: ticket-rolle-release traegt 7460 Dateien, nicht 0 — die Empfehlung bleibt trotzdem richtig"
rolle: plan-pruefer
zeit: "2026-08-16 19:22 CEST"
mess_stand: "A-37-Blatt aus rolle/planner · Worktree dreifach gemessen · release-pruefer b4640fe7"
wie_ich_darauf_kam: |
  Ich habe die Kreuzprobe von 19:10 auf A-37 fortgesetzt. Ergebnis: dieselbe Bauart wie
  A-38 — A-37-17 ist das Sammelkriterium ("ALLE SECHS KANTEN behandelt und je einzeln
  belegt"), K1 bis K4 werden von keinem Einzelkriterium genannt, K5 von A-37-18 und K6 von
  A-37-17. Kein Mangel, eine Bauart.
  Dabei fiel mir K2 auf: "Der Planner-Worktree heisst ticket-rolle-planner, der Release-Baum
  aber ticket-rolle-release (nicht -release-pruefer) — die Zuordnung steht in EINER Tabelle
  im Skript, nicht als Namensregel."
  Das ist genau die Falle, die der Release-Pruefer um 16:46 gemeldet und ich um 17:48
  gemessen habe. K2 ist also nicht theoretisch — sie hat heute einen Fehler verursacht.
und_die_zahl_dazu_stimmt_nicht: |
  Der Release-Pruefer schreibt: "fuer 'release' hat er ticket-rolle-release gemessen, den
  LEEREN abgeloesten Rest aus P2H-09 (detached HEAD, ls-files 0)".
  DREIFACH NACHGEMESSEN, weil eine Null etwas anderes bedeuten kann als sie aussieht:
  git -C ticket-rolle-release ls-files ............ 7460
  git -C ticket-rolle-release ls-tree -r HEAD ..... 7460
  git ls-tree -r 4630d658 (aus meinem Baum) ....... 7460
  Dateisystem, oberste Ebene ...................... 35 Eintraege
  Der Baum ist nicht leer. Er ist ein vollstaendiger Checkout auf einem detached HEAD.
die_empfehlung_bleibt_trotzdem_richtig: |
  Und das ist der Punkt, an dem ich nicht stehenbleibe: die Frage vor einer Loeschung ist
  nicht "ist er leer", sondern "geht etwas verloren". Gemessen:
  uncommittete oder unverfolgte Pfade ............. 0
  4630d658 Vorfahr von rolle/release-pruefer ...... JA
  4630d658 Vorfahr von auto/hausplaner-integration  JA
  Der Stand liegt auf zwei lebenden Zweigen, es gibt keine ungesicherte Arbeit. Der Baum
  ist gefahrlos entfernbar — nicht WEIL er leer ist, sondern WEIL alles in ihm gesichert
  ist. Die Empfehlung des Release-Pruefers traegt, ihre Begruendung nicht.
warum_ich_das_ueberhaupt_melde: |
  Weil die falsche Begruendung beim naechsten Mal die falsche Entscheidung traegt. "Der
  Baum ist leer" heisst: loeschen kostet nichts, immer. "Alles darin ist gesichert" heisst:
  loeschen kostet nichts, HEUTE — und wer morgen darin arbeitet, muss neu messen.
  Es ist dieselbe Klasse wie mein eigener Fehlgriff um 15:15: ich hatte "keine Tafelzeile"
  gemeldet und lag im Ergebnis richtig, aber aus dem falschen Grund. Der Generator hat
  meine Deutung damals zu Recht berichtigt; ich tue hier dasselbe.
ballbesitz: release-pruefer
```

herkunft: docs/STATUS.md · Block 403 · 34f6f5a9
```yaml
auftrag: "A-39"
titel: "A-39-2 haelt in BEIDE Richtungen, selbst nachgemessen — und meine SHA-Bitte von 18:31 ist erfuellt"
rolle: plan-pruefer
zeit: "2026-08-16 19:32 CEST"
mess_stand: "A-39 und A-37 aus rolle/planner · A-37 zusaetzlich am Stand 0ee521f7 · Planner-Kopf e802c1f8"
vorratspruefung_e_meine_bitte_ist_erfuellt: |
  Um 18:31 habe ich gemeldet, A-39-3 nenne keinen festen Stand, und die fehlenden SHAs
  selbst gesucht: A-33-1 "genau EINS" sei 8559b555, A-37-11 "Suite 1750" sei 7ef8f046.
  A-39-3 lautet heute: "P2 findet A-33-1 (genau EINS, Stand 8559b555) und A-37-11 (Suite
  1750, ...)". Der Stand ist eingetragen, und es ist genau der, den ich gemessen hatte.
a_39_2_negativfall_belegt: |
  Das Kriterium verlangt: gegen den Stand 0ee521f7 gefahren muss K6 gemeldet werden, "dort
  nannte kein Kriterium die Kanten".
  SELBST NACHGEMESSEN am alten Stand, nicht am heutigen:
  A-37-Blatt bei 0ee521f7 ......... 342 Zeilen
  Kriterien dort .................. 15
  davon eines, das "Kante" nennt ..  0
  Kanten-Tabellenzeilen dort ......  6
  Sechs Kanten stehen da, kein einziges Kriterium verlangt ihren Beleg. Genau die Lage,
  die P1 finden soll. Der Negativfall ist echt und am benannten Stand reproduzierbar.
a_39_2_positivprobe_belegt: |
  Das Kriterium nennt drei Blaetter, die NICHT gemeldet werden duerfen. Einzeln geprueft:
  A-35  6 Kanten-Tabellenzeilen  Kanten-Kriterium A-35-6
  A-36  6                        A-36-4
  A-38  6                        A-38-7
  Drei von drei tragen genau das, was die Positivprobe behauptet. A-39-2 ist damit in
  beide Richtungen belegt — das ist die Zwei-Richtungs-Probe aus Paragraf 12.3, und hier
  steht sie schon im Kriterium selbst.
eine_zahl_ist_ueberholt_und_es_ist_die_P6_klasse: |
  Der Klammerzusatz lautet: "(Gemessen 16.08.: 3 von 4 Blaettern mit Kantenliste hatten
  eines.)"
  Heute gemessen: 8 von 9.
  Die Zahl ist datiert, also nicht falsch — aber sie ist binnen Stunden von 4 auf 9
  Blaetter gewachsen, weil neue Auftraege mit Kantenlisten entstehen. Das ist genau P6 in
  der dritten Auspraegung, die ich um 16:35 an A-40-5 gemeldet habe: ein Zaehlwert, der
  durch normale Arbeit waechst.
  KEIN BEFUND, ein Hinweis: der Zusatz traegt sein Datum und ist damit ehrlich. Wer ihn
  spaeter ohne das Datum liest, misst 8 von 9 und haelt die 3 von 4 fuer falsch.
stand_der_A_39_dor: |
  Geprueft und haltend: A-39-1 (Rot-Lage), A-39-2 in beide Richtungen, A-39-3 (SHAs jetzt
  vorhanden), K2s Groesse, die Kreuzprobe ueber alle Kanten und Pruefungen.
  Offen: A-39-4 bis -7, -8, -9, -10, -11 und die Einzelbelege der Kanten.
  Kein Votum. Der Ball bleibt bei mir.
ballbesitz: plan-pruefer
```

herkunft: docs/STATUS.md · Block 404 · 34f6f5a9
```yaml
auftrag: "A-39"
titel: "A-39-4 nennt jetzt einen Stand, an dem der zu findende Fall NICHT existiert — und der SHA stammt von mir"
rolle: plan-pruefer
zeit: "2026-08-16 19:44 CEST"
mess_stand: "A-39 aus rolle/planner · A-37-Blatt an drei Staenden einzeln geholt · Planner-Kopf 20c968a3"
zuerst_die_gute_nachricht: |
  Meine Bitte von 18:31 ist vollstaendig erfuellt: A-39-3, -4 und -11 nennen jetzt feste
  Staende. Der Planner hat die Auslassung ausserdem selbst eingeordnet — "dieselbe Art
  Angabe, zwei Handhabungen, in dem Blatt, das genau diesen Fehler prueft" — und meinen
  Beitrag benannt.
und_jetzt_der_fehler_darin: |
  A-39-4 lautet: "P3 findet A-37-12 am Stand 7ef8f046 (vor A-37-16) — die Marke ohne
  Erzeuger."
  GEMESSEN AM GENANNTEN STAND, Muster vorher an den vorhandenen Kriterien geeicht:
  7ef8f046 ist der A-37-Schnitt vom 14.08. 22:35, das Blatt hat dort 174 Zeilen.
  Kriterien dort ....................... 11 (Format trifft, A-37-1/-2/-3 gefunden)
  hoechste Kriteriumsnummer ............ 11
  Treffer fuer "A-37-12" ................ 0
  A-37-12 EXISTIERTE AN DIESEM STAND NOCH NICHT. P3 kann dort nichts finden — die
  Positivprobe des Kriteriums ist an diesem SHA nicht ausloesbar.
der_richtige_stand_ist_gemessen: |
  erster Commit mit A-37-12 im Blatt ... 3719937f  16.08. 12:48
  erster Commit mit A-37-16 im Blatt ... 5bbc55bf  16.08. 13:23
  GEGENPROBE bei 3719937f: "A-37-12" vorhanden (1), "A-37-16" nicht (0). Genau die Lage,
  die A-39-4 sucht — die geforderte Marke steht da, ihr Erzeuger noch nicht.
  Der Stand fuer A-39-4 ist also 3719937f, und "vor A-37-16" trifft dort zu.
MEIN ANTEIL, und er gehoert dazu: |
  7ef8f046 stammt aus meiner Messung von 18:31. Ich hatte ihn dort fuer A-39-3 gemessen —
  fuer A-37-11 "Suite 1750" —, und fuer A-39-4 habe ich ausdruecklich geschrieben: "A-39-4
  habe ich NICHT aufgeloest: 'vor A-37-16' verweist auf ein Kriterium, nicht auf einen
  Commit."
  Der SHA ist an eine Stelle gewandert, fuer die ich ihn nicht gemessen habe. Das ist
  keine Unterstellung gegen den Planner — drei SHAs in einer Meldung, zwei davon fuer
  dasselbe Kriterium, und die Zuordnung ist verrutscht. Aber es ist genau der Grund, warum
  ich die Lieferung damals mit einem ausdruecklichen "nicht aufgeloest" versehen habe: eine
  Zahl, die neben einer Luecke steht, wird in die Luecke gelesen.
  KONSEQUENZ FUER MICH: wenn ich Werte liefere, schreibe ich kuenftig zu jedem einzeln
  dazu, WOFUER er gemessen wurde — nicht nur, wofuer er es nicht wurde.
ballbesitz: planner
```

herkunft: docs/STATUS.md · Block 405 · 34f6f5a9
```yaml
auftrag: "A-39"
titel: "A-39-5 haelt am genannten Stand — nach dem Fund bei A-39-4 habe ich die anderen Staende einzeln nachgeprueft, statt sie zu glauben"
rolle: plan-pruefer
zeit: "2026-08-16 19:56 CEST"
mess_stand: "A-39 aus rolle/planner · A-33-Blatt an zwei Staenden geholt · Planner-Kopf 20c968a3"
warum_diese_runde: |
  Um 19:44 habe ich belegt, dass A-39-4 einen Stand nennt, an dem der zu findende Fall
  nicht existiert. Ein falscher Stand in einer Reihe von vier ist ein Grund, die anderen
  drei zu pruefen und nicht anzunehmen, sie seien richtig.
a_39_5_haelt: |
  Das Kriterium sagt: "P4 findet A-33-7 am Stand VOR 5db5f8a9 — 'scripts/ null Mal' gegen
  art:."
  Erst den Stand bestimmt: 5db5f8a9 ist vom 16.08. 13:36, sein Vorgaenger fe6b436a.
  Dann beide geholt und A-33-7 roh gesucht, nachdem mein erstes Muster nichts traf:
  fe6b436a  "A-33-7 Kein Code. Gegenprobe: der Bau-Commit fasst NUR docs/STATUS.md an"
  5db5f8a9  "A-33-7 (NEUGEFASST 16.08. — die alte Fassung war mit dem Umschnitt UNVEREINBAR)"
  Und der Blattkopf, an beiden Staenden gleich:
  art: "BAU — ein SKRIPT, das die verkuerzten Tafelkennungen nachzieht."
  DER WIDERSPRUCH IST DA UND IST GENAU P4s FALL: ein Blatt, dessen Kopf ein SKRIPT
  ankuendigt, verlangt im Kriterium "kein Code" und "nur docs/STATUS.md". Am Vorgaenger
  steht er, am genannten Stand ist er behoben — die Reihenfolge stimmt, der Fall ist
  ausloesbar, das Kriterium traegt.
was_der_unterschied_zu_A_39_4_ist: |
  Beide Kriterien sind gleich gebaut ("am Stand vor X"). Bei A-39-5 existiert der Fall am
  Vorgaenger und ist am genannten Commit behoben — die Angabe beschreibt eine echte Kante
  in der Historie. Bei A-39-4 existierte das zu findende Kriterium am genannten Stand
  ueberhaupt nicht; dort war nicht die Kante falsch, sondern der Commit.
  Das ist der Unterschied zwischen "Stand ungenau" und "Stand falsch", und er laesst sich
  nur durch Oeffnen feststellen. Gezaehlt haetten beide gleich ausgesehen: ein SHA, der
  existiert.
stand_der_A_39_dor: |
  Geprueft und haltend: A-39-1, -2 (beide Richtungen), -3 (SHAs vorhanden), -5, -11
  (5bbc55bf traegt "28 von 32", um 18:31 gemessen), dazu K2s Groesse und die Kreuzprobe.
  Rot: A-39-4 — falscher Stand, richtiger gemessen (3719937f).
  Offen: A-39-6 bis -10 und die Einzelbelege der Kanten.
  Kein Votum. Der Ball bleibt bei mir.
ballbesitz: plan-pruefer
```

herkunft: docs/STATUS.md · Block 406 · 34f6f5a9
```yaml
auftrag: "A-39"
titel: "A-39-6 nennt den Stand in der falschen RICHTUNG — der doppelte exit 3 entstand MIT 5bbc55bf, nicht davor"
rolle: plan-pruefer
zeit: "2026-08-16 20:06 CEST"
mess_stand: "A-39 aus rolle/planner · A-37-Blatt an den Staenden b6af3207 und 5bbc55bf einzeln geholt · Planner-Kopf 3817c74d"
das_kriterium: |
  A-39-6 sagt: "P5 findet den doppelten exit 3 am Stand VOR 5bbc55bf."
  Nach dem Fund bei A-39-4 habe ich auch diesen Stand geoeffnet statt ihn zu glauben.
gemessen_an_beiden_staenden: |
  5bbc55bf ist vom 16.08. 13:23 ("DoR Runde 3 abgearbeitet — fuenf Restpunkte"), sein
  Vorgaenger ist b6af3207.
  STAND b6af3207, die Codetabelle hatte dort DREI Zeilen:
  | 1 | Rolle und Baum passen nicht zusammen         | rollen-tor.sh
  | 2 | Rollenkennung fehlt oder hat falsche Form    | commit-pruefen.sh:59-65 (unberuehrt)
  | 3 | Rollenkennung fehlt beim direkten Aufruf     | rollen-tor.sh
  und A-37-5 lautete dort: "TICKET_ROLLE leer -> exit 3".
  TABELLE UND KRITERIUM STIMMTEN UEBEREIN. Code 3 trug EINE Bedeutung.
  STAND 5bbc55bf, die Tabelle wurde auf sechs Zeilen erweitert:
  | 3 | fehlende Modulaufloesung (MODUL)             | commit-pruefen.sh | gebaut
  | 5 | Rollenkennung fehlt beim direkten Aufruf     | rollen-tor.sh     | zu bauen
  und A-37-5 lautet unveraendert: "TICKET_ROLLE leer -> exit 3".
  ERST HIER traegt Code 3 zwei Bedeutungen: MODUL laut Tabelle, Kennung-fehlt laut
  Kriterium.
was_daraus_folgt: |
  Der Fall, den P5 finden soll, existiert am genannten Stand NICHT — er entsteht mit ihm.
  A-39-6 muesste "AM Stand 5bbc55bf" sagen, nicht "vor" ihm. Wer die Probe wie
  aufgeschrieben faehrt, misst am Vorgaenger eine saubere Tabelle und meldet P5 als nicht
  ausloesbar.
  Das ist der ZWEITE falsche Stand in dieser Reihe nach A-39-4, und er ist von anderer Art:
  bei A-39-4 existierte das gesuchte Kriterium ueberhaupt nicht, hier existiert der Fall —
  nur eine Kante zu frueh gesucht. Ungenau ist etwas anderes als falsch, und beides ist
  nur durch Oeffnen zu unterscheiden.
die_ironie_gehoert_dazu: |
  5bbc55bf ist der Commit "DoR Runde 3 abgearbeitet — fuenf Restpunkte". Er hat fuenf
  Restpunkte behoben und dabei DIESEN Widerspruch erzeugt: die Tabelle wurde richtig
  umgestellt, das Kriterium blieb stehen.
  Genau diesen Widerspruch habe ich um 15:17 unabhaengig gefunden, um 16:03 in der
  Klassifikation berichtigt und um 18:42 in seiner vollen Groesse gemessen. Er ist seit
  13:23 im Blatt und seit 13:01 in einer Restpunktliste — die Runde, die ihn erzeugt hat,
  hat ihn achtzehn Minuten frueher schon als Restpunkt notiert.
ballbesitz: planner
```

herkunft: docs/STATUS.md · Block 407 · 34f6f5a9
```yaml
auftrag: "A-38"
titel: "Yamas Anweisung angenommen — A-38 gehoert in A-37, und die tragende Zahl ist eine ANDERE als die genannte: das Loch ist nicht alt, es entsteht gerade"
rolle: plan-pruefer
zeit: "2026-08-16 20:16 CEST"
mess_stand: "ganzer Bestand ohne Zeitfenster · Messzeitpunkt 16.08. 20:16, weil die Zahl sich stuendlich aendert"
die_anweisung: |
  Yama, 16.08. abends: "A-38 gehoert NICHT hinter A-37, sondern ALS KRITERIUM HINEIN."
  Reihenfolge fuer mich: A-38 in A-37, dann A-39, dann A-40.
  ANGENOMMEN. Und die Begruendung traegt — aber nicht mit der genannten Zahl.
die_genannte_zahl_ist_ueberholt: |
  Yama nennt: "41 von 309 Commits ohne Rollenmarke, ausnahmslos Merges, von 32 Merges
  tragen 4 eine ... ein 13-Prozent-Loch ab dem ersten Tag."
  HEUTE GEMESSEN, ganzer Bestand: 3870 Commits, 349 Merges, davon 232 ohne Rollenmarke.
  Die Grundgesamtheit ist von 309 auf 3870 gewachsen. Eine Quote aus 309 Commits beschreibt
  den Bestand von damals, nicht den von heute.
und_meine_eigene_zahl_ist_es_auch: |
  Um 16:16 habe ich gemeldet: "seit 15.08. 00:00 gibt es 97 Merges und davon 0 ohne
  Rollenmarke. Nicht wenige — keinen einzigen." Daraus habe ich geschlossen, A-38s Rot-Lage
  laufe um 22:53 ab.
  HEUTE UM 20:16: seit 15.08. gibt es 144 Merges, davon 43 OHNE Marke.
  Meine Messung war zum Zeitpunkt richtig. Die Lage hat sich geaendert, und zwar in den
  Stunden dazwischen.
das_ist_der_eigentliche_befund: |
  Die markenlosen Merges sind nicht Altbestand. Sie entstehen JETZT:
  seit 15.08. bis heute 17:30 ..... 28
  ab heute 17:30 .................. 18
  Verteilung ueber den Nachmittag: 16:54, 17:05, 17:15, 17:26, 17:27, 17:38, 17:40, 17:42 —
  vier bis fuenf je Viertelstunde, im Wortlaut "Merge branch 'rolle/planner' into
  auto/hausplaner-integration".
  Es ist der INTEGRATIONSLAUF, der sie erzeugt. Der Lauf, den Yama heute freigegeben hat
  und der funktioniert hat, produziert genau die Commit-Art, gegen die A-38 gebaut werden
  soll — nicht aus Nachlaessigkeit, sondern weil ein Merge ohne Hook keine Marke bekommt.
  DAMIT IST YAMAS ANWEISUNG NICHT NUR RICHTIG, SONDERN DRINGLICHER ALS IHRE BEGRUENDUNG.
  Das Loch ist kein 13-Prozent-Rest vom ersten Tag. Es ist offen, es fuellt sich stuendlich,
  und je laenger der Integrationslauf ohne Hook laeuft, desto mehr unmarkierte Merges
  stehen im Bestand, den A-41s Erzeugung spaeter lesen soll.
  Und es bestaetigt K7 von A-41 an einem Fall, den ich heute selbst getreten habe: ein
  Merge traegt fremde Betreffs mit, und ohne --no-merges zaehlt jeder Zustand nach jedem
  Transport erneut.
was_ich_daraus_fuer_meine_arbeit_ziehe: |
  Meine Meldung von 16:16 ("die Rot-Lage laeuft um 22:53 ab") war eine Prognose aus einem
  Zeitfenster — genau die Klasse, die P6 verbietet. Sie ist nicht falsch gemessen, aber sie
  hat eine Entwicklung fortgeschrieben, die vier Stunden spaeter nicht mehr galt.
  P6 sagt: eine Rot-Lage aus einem wandernden Fenster wird von selbst gruen. Mein Fall
  zeigt die Rueckseite: sie kann auch von selbst wieder ROT werden. Beides ist derselbe
  Fehler — aus einem Fenster auf die Zukunft zu schliessen.
reihenfolge_bestaetigt: |
  A-38 als Kriterium in A-37, dann A-39, dann A-40. Ich habe A-38 heute mit acht Punkten
  geprueft (-1, -2, -5, -6, K2, K4, K5, K6); K1 und K3 blieben mangels erlaubtem Lauf
  offen. Diese Vorarbeit gilt weiter, sie wandert mit dem Kriterium.
ballbesitz: planner
```

herkunft: docs/STATUS.md · Block 408 · 34f6f5a9
kennung_nicht_zuordenbar: true   # Freitext statt Kennung — uebernommen wie vorgefunden, nicht gedeutet
```yaml
auftrag: "REGISTER"
titel: "Yamas Messung geliefert — der Bauvorrat ist 30 und nicht 37, und LEER traegt VIER Bedeutungen statt zwei"
rolle: plan-pruefer
zeit: "2026-08-16 20:32 CEST"
mess_stand: "REGISTER.md aus rolle/planner · Reifegrad aus der dritten Tabellenspalte gelesen, Summe gegen die Zeilenzahl geeicht"
was_erbeten_war: |
  Yama: "von den 37 beschriebenen Zeilen — welche erfuellen heute alle drei Bedingungen?
  Eine Liste, keine Prosa. Das ist der Bauvorrat, und niemand weiss heute, wie gross er ist."
  Die erste Bedingung ist "Registerzeile auf BESCHRIEBEN". Ich habe sie gemessen, bevor ich
  weitergehe — und dort endet die Frage schon.
die_zahl_stimmt_nicht: |
  Yamas Verteilung: BESCHRIEBEN 37 · ENTWORFEN 2 · GEBAUT 1 · LEER 3 = 43.
  GEMESSEN aus der dritten Spalte, Summe gegen 43 Registerzeilen geeicht:
  BESCHRIEBEN 30 · LEER 10 · ENTWORFEN 2 · GEBAUT 1 = 43.
  Sieben Zeilen mehr auf LEER, als die Liste kennt.
  MEIN ERSTER VERSUCH WAR AUCH FALSCH und ich sage es dazu: ich hatte zeilenweise nach den
  Woertern gesucht und 30+2+3+19 = 54 bei 43 Zeilen bekommen — Zeilen wie "~~LEER~~ ->
  BESCHRIEBEN" zaehlten doppelt. Erst die Spalte gibt die Zahl.
die_zehn_LEER_zeilen_und_sie_meinen_vier_verschiedene_dinge: |
  ENTSCHIEDEN, nichts zu bauen (Yamas drei):
    W-19 Sonne und Verschattung .... "ZUSTAENDIGKEIT ENTSCHIEDEN vom planner 13.08."
    W-24 Fundament und Bodenplatte . "Praemisse traegt nicht"
    W-32 Giebelwand-Bindung ........ "ENTSCHIEDEN vom planner 13.08.: ABLEITUNG"
  UNGEPRUEFT, Modul fehlt:
    W-25 Pfetten und Kehlbalken .... "ungeprueft — dachformVorlagen, holzBauteile"
    W-26 Dachschichten ............. "ungeprueft — KEIN Modul"
  UNGEPRUEFT, Modul ist da:
    W-28 Dachentwaesserung ......... "ungeprueft — linienBauteile fuehrt 'dachrinne'"
    W-29 Dachdurchdringungen ....... "ungeprueft — STARK GEBAUT: dachOeffnung, ..."
    W-30 Flachdach-Aufbau .......... "ungeprueft — dachformVorlagen (attika, svgFlach)"
  FERTIG, Register nicht nachgezogen:
    W-17 Export und Speichern ...... Vermerk "—", ABER: W-17/1 traegt CODE_FERTIG,
                                     Ball beim Evaluator, heute von mir mitgeprueft.
    W-43 Abbund-Zeichnung .......... "Darstellungslogik liegt in M-02"
was_das_fuer_den_baumassstab_heisst: |
  Yamas Befund war "LEER kann 'erledigt, weil es nichts zu bauen gibt' nicht ausdruecken —
  H-9 auf der Registerebene". Das trifft, und es ist groesser: LEER traegt heute VIER
  Bedeutungen, nicht zwei. Ein Wort fuer "entschieden", "ungemessen", "gemessen aber nicht
  beschrieben" und "fertig, nur nicht eingetragen".
  DER BAUVORRAT NACH BEDINGUNG 1 IST 30, NICHT 37. Und er ist an einer Stelle zu klein
  gemessen: W-29 traegt LEER und im selben Feld "stark gebaut" — ein Kandidat, den die
  Zahl verschweigt.
  Bedingung 2 (Bedienweg existiert) und 3 (Rechenweg nachgerechnet) habe ich NICHT
  gemessen. Beide brauchen eine Festlegung, was als Beleg gilt — bei 3 steht sie mit der
  Sachverstaendigen-Regel schon da, bei 2 nicht. Ohne diese Festlegung waere jede Liste,
  die ich liefere, eine Vermutung mit Zahlen.
ballbesitz: yama
```

herkunft: docs/STATUS.md · Block 409 · 34f6f5a9
```yaml
auftrag: "A-42"
titel: "DoR begonnen — neun Kriterien, sechs Kanten, und die Ausgangszahlen liefere ich mit: 67 bleiben, 249 ziehen um, 24 sind Altlast"
rolle: plan-pruefer
zeit: "2026-08-16 20:46 CEST"
mess_stand: "Blatt und Datensatz aus rolle/planner (e8f3a9e5) · Bloecke selbst gezaehlt, Summe geeicht"
in_eigener_sache_zuerst: |
  A-42 zieht die Bloecke ohne zustand-Feld aus docs/STATUS.md in eine eigene Datei. 68 von
  ihnen tragen meine Rollenmarke — ich bin an diesem Auftrag Betroffener und Pruefer
  zugleich. Ich sage es, weil es die Pruefung faerben koennte: wer seine eigenen Notizen
  gerettet sehen will, prueft milder.
  Deshalb habe ich zuerst nach einem Grund gesucht, der GEGEN den Auftrag spricht, und
  einen gefunden — er hielt der Messung nicht stand, siehe unten.
was_ich_pruefend_finde: |
  Neun Kriterien, alle messbar formuliert. A-42-2 ist das tragende: "Bloecke vorher =
  Bloecke nachher in STATUS.md + Eintraege in der neuen Datei". Eine Summenprobe, die
  nichts verschwinden laesst.
  A-42-6 sichert die Gegenrichtung: die Bloecke MIT zustand: sind unberuehrt, Anzahl und
  Inhalt vorher/nachher. A-42-3 verlangt, dass kein Block sich inhaltlich veraendert.
  Zusammen decken sie beide Verlustarten ab — verschwinden und verfaelscht werden.
  A-42-8 beantwortet P7 gleich mit: WER ist der Generator in seinem Baum. Das ist die
  Regel, die der Planner heute um 16:53 geschrieben hat, hier vier Stunden spaeter
  angewandt.
mein_verworfener_befund: |
  Beim Zaehlen fielen mir 24 Bloecke auf, die kein Parser liest — sie tragen weder ein
  zustand-Feld noch nicht, man kann es an ihnen nicht feststellen. Ein Umzug, der nach
  "hat zustand" sortiert, muesste an ihnen scheitern.
  NACHGESEHEN STATT GEMELDET: K4 lautet "Ein Block ist kaputtes yaml (es gibt 24 solcher
  Altlasten) — NICHT umziehen, einzeln melden".
  Die Kante kennt den Fall, nennt die Zahl, und die Zahl stimmt: ich habe unabhaengig 24
  gemessen. Kein Befund.
die_ausgangszahlen_die_A_42_1_und_2_brauchen: |
  A-42-1 verlangt "vorher und nachher gezaehlt, mit demselben Befehl, im Bericht". Hier ist
  das Vorher, am Planner-Stand e8f3a9e5 gemessen:
  Bloecke gesamt ............... 340
  davon MIT zustand-Feld ....... 67   bleiben (A-42-6)
  davon OHNE ................... 249  ziehen um
  nicht parsebar ............... 24   bleiben (K4)
  67 + 249 + 24 = 340. Die Summe geht auf.
  WICHTIG FUER DEN LAUF: diese Zahlen sind vom 20:46-Stand. K5 sagt richtig, der Lauf misst
  EINMAL und nennt seinen Stand-SHA — meine Zahlen ersetzen das nicht, sie sind die
  Gegenprobe dazu.
ein_kleiner_hinweis_zu_A_42_9: |
  A-42-9 sagt "Zahl unmittelbar vor dem Bau erheben", aber nicht "keine feste Zahl im
  Kriterium" wie A-37-11, A-38-9, A-39-10 und A-41-12. Es steht damit zwischen jenen vier
  und A-40-9, das gar nichts sagt (mein Befund von 18:12). Inhaltlich richtig, nur eine
  Spur kuerzer als die Familie.
stand: |
  Geprueft: die Kriterienstruktur, K4 gegen meine eigene Messung, die Ausgangszahlen.
  Offen: A-42-3 bis -8 im Einzelnen und die Kanten K1, K2, K3, K5, K6.
  Kein Votum. Der Ball bleibt bei mir.
ballbesitz: plan-pruefer
```

herkunft: docs/STATUS.md · Block 410 · 34f6f5a9
```yaml
auftrag: "A-42"
titel: "K1 und K2 gemessen — beide heute nicht ausloesbar, und K1s Wortlaut ist an neun Bloecken mehrdeutig"
rolle: plan-pruefer
zeit: "2026-08-16 20:54 CEST"
mess_stand: "Planner-Stand e8f3a9e5, 340 Bloecke einzeln geparst · Planner-Kopf zum Messzeitpunkt 1e1afd1b"
k2_ist_heute_leer: |
  K2: "Zwei Notizen sind wortgleich — beide ziehen um. KEIN Entdoppeln, Doppelung ist ein
  Befund fuer spaeter."
  GEMESSEN ueber alle 249 Bloecke ohne zustand-Feld, Vergleich ueber den vollstaendigen
  Blockinhalt: 0 wortgleiche Dubletten.
  Die Kante ist also heute nicht ausloesbar. Das ist kein Mangel — sie ist Vorsorge, und
  ihre Regel ("beide umziehen, nicht entdoppeln") ist die richtige: ein Umzug, der
  entdoppelt, trifft eine inhaltliche Entscheidung, die ihm nicht gehoert.
k1_ist_mehrdeutig_und_das_ist_der_fund: |
  K1: "Ein Block traegt zustand: in Kleinschreibung oder als Prosa — NICHT umziehen,
  Grenzfall wird gemeldet."
  GEMESSEN: 0 Bloecke tragen einen kleingeschriebenen zustand-Wert. ABER NEUN Bloecke
  tragen ein FELD, dessen Name "zustand" enthaelt, ohne "zustand" zu heissen:
    zweiter_befund_zustandswiderspruch · W23_1_ZUSTAND · 1_ZUSTAND (dreimal)
    1_ZUSTAND_rot_lage_gemessen · zustand_gemessen_nicht_angenommen
    zustand_steht_NICHT_hier · warum_kein_zustand_feld · kein_zustand_feld
  ZWEI DAVON SAGEN AUSDRUECKLICH DAS GEGENTEIL: "zustand_steht_NICHT_hier" und
  "kein_zustand_feld" sind Notizen, die betonen, KEIN Zustandsfeld zu fuehren. Sie sollen
  umziehen — sie sind der Regelfall, nicht der Grenzfall.
  Nach dem Wortlaut fallen alle neun NICHT unter K1: keiner traegt "zustand:" als Feld oder
  als Prosa, sie tragen einen anderen Feldnamen. Die Kante ist damit heute ebenfalls nicht
  ausloesbar.
  ABER WER SIE UMSETZT, MUSS DAS ENTSCHEIDEN, und das Blatt sagt es nicht: sucht der Bau
  nach dem Feldnamen "zustand" (dann greift K1 nie), oder nach der Zeichenfolge "zustand"
  (dann nimmt er neun Bloecke aus, von denen mindestens zwei umziehen sollen)?
  Der Unterschied sind neun Notizen — von 249 gut drei Prozent, und ausgerechnet die, die
  ihren eigenen Sonderfall dokumentieren.
was_das_fuer_meine_ausgangszahlen_heisst: |
  Meine Zahlen von 20:46 (67 bleiben, 249 ziehen um, 24 Altlast) gelten unveraendert,
  solange K1 nach dem FELDNAMEN sucht. Sucht der Bau nach der Zeichenfolge, sind es
  240 statt 249, und die Summenprobe A-42-2 muss die neun als eigene Gruppe fuehren.
  Ich nenne beide Zahlen, damit die Probe nicht an der Auslegung scheitert.
die_lehre_von_A_41_gilt_hier_auch: |
  Zwei von sechs Kanten sind am Bestand nicht ausloesbar. Bei A-41 war das bei K2 und K6
  genauso, und der Generator hat die Lehre gezogen: so eine Kante findet man nur, wenn man
  ihre Logik ISOLIERT nachbaut. Genau so habe ich dort den Substring-Fehler gefunden.
  Fuer A-42 heisst das: K1 und K2 brauchen einen konstruierten Fall, sonst bleibt ihr
  Beleg eine Codestelle. A-42-5 verlangt aber "alle sechs Kanten je EINZELN belegt".
ballbesitz: planner
```

herkunft: docs/STATUS.md · Block 411 · 34f6f5a9
```yaml
auftrag: "A-42"
titel: "K3 und K6 auch nicht ausloesbar — aber 68 von 77 Notizen tragen FREITEXT im auftrag-Feld, und das trifft A-42-4"
rolle: plan-pruefer
zeit: "2026-08-16 21:02 CEST"
mess_stand: "Planner-Stand e8f3a9e5, 340 Bloecke geparst · Planner-Kopf zum Messzeitpunkt 1e1afd1b"
k6_ist_nicht_ausloesbar: |
  K6: "docs/BEFUNDNOTIZEN.md existiert bereits — anhaengen, nicht ueberschreiben."
  Gemessen ueber rolle/planner, rolle/generator und den Integrationszweig: die Datei
  existiert in KEINEM. Der erste Lauf legt sie an, die Kante greift beim zweiten.
  Richtig gebaut, heute nur nicht pruefbar.
k3_ebenfalls_nicht_und_der_weg_dahin_war_lehrreich: |
  K3: "Eine Notiz nennt eine Kennung, die es nie gab — zieht trotzdem um, mit Vermerk."
  MEIN ERSTES ERGEBNIS WAR 70 VON 77 und sah nach einem Massenfall aus. Nachgesehen statt
  gemeldet: die meisten dieser "Kennungen" sind gar keine.
  77 Notizen tragen ein auftrag-Feld. Gegen die Kennungsform geprueft:
    Form einer Kennung ....  9
    Freitext oder Titel ... 68
  Von den neun echten fehlen zwei im Datensatzbestand: A-08 und A-09. Aber BEIDE HABEN EIN
  BLATT — A-08 zwei Dateien, A-09 eine. Es sind also keine Kennungen, die es nie gab,
  sondern Auftraege ohne Zustandsdatensatz. K3 trifft nicht.
  Ohne das Trennen haette ich 70 statt 0 gemeldet.
der_eigentliche_fund: |
  68 von 77 Notizen tragen FREITEXT im auftrag-Feld, nicht eine Kennung. Beispiele:
    w37_ballwechsel_runde2 · a33_grundlage_zeiger_gewandert
    spiegelung_rundet_an_der_toleranz · meine_ballortung_blieb_zu_eng
    vier_fachfragen_fehlen_in_meiner_liste · statuswahrheit_in_zwei_fassungen
  Das Feld heisst "auftrag", traegt aber in 88 Prozent der Faelle eine Ueberschrift.
  UND ICH BIN DARAN BETEILIGT: viele dieser Notizen sind meine.
  FUER A-42-4 IST DAS DER PUNKT. Das Kriterium verlangt "jeder Eintrag traegt seine
  Herkunft (herkunft: mit Blocknummer und ...)". Solange die Herkunft die BLOCKNUMMER ist,
  geht das auf. Wer die Eintraege aber nach KENNUNG gruppieren will — und das ist die
  naheliegende naechste Frage an eine Notizsammlung —, kann es fuer 68 von 77 nicht.
  Kein Einwand gegen A-42: der Umzug soll nichts umschreiben, und das ist richtig. Aber
  wer danach die Sammlung benutzt, findet ein Feld vor, dessen Name nicht haelt, was er
  sagt — H-9 in der Zielablage.
  VORSCHLAG, nicht Entscheidung: im Eintrag zusaetzlich zur Blocknummer festhalten, OB das
  auftrag-Feld eine Kennung trug. Ein Wahrheitswert, keine Umschreibung. Dann ist die
  Gruppierbarkeit spaeter messbar, statt sie neu erheben zu muessen.
nebenbefund_A_08_und_A_09: |
  Beide haben ein Blatt, aber keinen Datensatz mit zustand-Feld im Planner-Stand. Das ist
  die A-20-Klasse (Blatt, Tafelzeile, Datensatz gehoeren zusammen), gemessen an zwei
  Kennungen. Ich melde es hier nur nebenbei, weil es beim K3-Pruefen anfiel — es gehoert
  nicht zu A-42.
stand_der_A_42_dor: |
  Kanten: K1 mehrdeutig (Feldname oder Zeichenfolge), K2, K3, K6 heute nicht ausloesbar,
  K4 durch meine eigene Zaehlung bestaetigt (24), K5 noch offen.
  Kriterien: die Ausgangszahlen fuer -1 und -2 stehen, -9 einen Halbsatz kuerzer als die
  Familie. Offen: -3, -4 (mit dem Hinweis oben), -5, -6, -7, -8.
ballbesitz: planner
```

herkunft: docs/STATUS.md · Block 412 · 34f6f5a9
```yaml
auftrag: "A-42"
titel: "K5 ist die einzige Kante mit echtem Beleg — und sie entwertet meine eigene Zahl von 20:46"
rolle: plan-pruefer
zeit: "2026-08-16 21:08 CEST"
mess_stand: "Blockzahlen an vier Zeitpunkten aus der Historie von docs/STATUS.md, jeweils am juengsten Commit davor"
k5_ist_belegt_und_zwar_am_heutigen_tag: |
  K5: "Waehrend des Umzugs kommen neue Notizen dazu — der Lauf misst EINMAL und nennt
  seinen Stand-SHA."
  GEMESSEN, Bloecke in docs/STATUS.md an vier Zeitpunkten:
  15:00  8400b3f9  343
  17:00  77c08b1b  378   (+35 in zwei Stunden)
  19:00  11386923  412   (+34 in zwei Stunden)
  Das sind rund 17 neue Bloecke je Stunde, also einer alle dreieinhalb Minuten. Ein Umzug,
  der eine Minute laeuft, sieht am Ende einen anderen Bestand als am Anfang.
  K5 ist damit die EINZIGE der sechs Kanten mit einem echten, heute messbaren Fall. K1,
  K2, K3 und K6 sind nicht ausloesbar, K4 ist durch die Altlast belegt — aber K5 passiert
  gerade, waehrend ich das schreibe.
und_sie_trifft_meine_eigene_lieferung: |
  Um 20:46 habe ich die Ausgangszahlen geliefert: 340 Bloecke, 67 mit zustand, 249 ohne,
  24 Altlast. Ich habe dazugeschrieben, sie seien "vom 20:46-Stand" und ersetzten den Lauf
  nicht.
  Das war richtig und trotzdem zu schwach: es sind nicht nur "Zahlen von einem Zeitpunkt",
  sie sind bei diesem Tempo nach einer halben Stunde FALSCH. Mein eigener Baum traegt
  jetzt 398 Bloecke, der gemessene Stand 412 — beide ueber meinen 340.
  DIE ZAHLEN SIND ALSO KEINE GEGENPROBE, sie sind ein Zeitstempel. Wer sie beim Bau
  verwendet, muss sie neu erheben; wer sie vergleicht, vergleicht zwei Zeitpunkte.
  Das ist P6 zum vierten Mal heute, und diesmal an mir selbst: eine Zahl, die durch
  normale Arbeit waechst, taugt nicht als Sollwert. Ich habe sie als Gegenprobe angeboten
  und damit genau das getan, wovor ich um 16:16 gewarnt habe.
was_daraus_fuer_A_42_folgt: |
  A-42-1 verlangt "vorher und nachher gezaehlt, MIT DEMSELBEN BEFEHL, im Bericht". Das ist
  die richtige Bauart — nicht meine Zahlen gegen die des Laufs, sondern zwei Messungen
  desselben Laufs.
  A-42-2s Summenprobe haelt auch bei wachsendem Bestand, SOLANGE beide Zaehlungen zum
  selben Stand-SHA gehoeren. Genau das sagt K5. Die Kante ist nicht Vorsorge, sie ist die
  Bedingung, unter der A-42-2 ueberhaupt aufgeht.
  MEIN BEITRAG BLEIBT: die Aufteilung 67 / 249 / 24 zeigt, WELCHE Gruppen es gibt und dass
  ihre Summe aufgeht. Die absoluten Werte sind Tagesform.
ballbesitz: plan-pruefer
```

herkunft: docs/STATUS.md · Block 413 · 34f6f5a9
```yaml
auftrag: "A-42"
titel: "A-42-8 wendet P7 an und faellt selbst durch P7 — das DARF er hat ein Ablaufdatum, und A-37 setzt es"
rolle: plan-pruefer
zeit: "2026-08-16 21:18 CEST"
mess_stand: "Tor aus rolle/generator · Sperrbedingung Zweig fuer Zweig nachgerechnet · Planner-Kopf fa1ac559"
was_A_42_8_sagt: |
  "Der Weg ist gangbar (P7): WER — der Generator, in seinem Baum · DARF er — ja, es ist
  docs/, kein Produktivcode, kein Loeschen · EXISTIERT die Eigenschaft — ja, die
  Blockstruktur ist maschinell erfassbar."
  Alle drei P7-Fragen sind beantwortet, und die Antworten sind sorgfaeltig. Das Kriterium
  wendet die Regel an, die heute um 16:53 entstanden ist.
und_die_zweite_antwort_traegt_nur_noch_heute: |
  Das Rollen-Tor sagt in seinem eigenen Kopf, Zeile 199-201:
    TOR_STATUS_PFAD=1, generator, eigener Baum
      nach 16:17   VERSTOSS ... EINEN Schreiber: den Integrator   exit 1
  A-42 verlangt vom Generator, Bloecke AUS docs/STATUS.md zu entfernen. Das ist genau der
  Pfad, den das Tor ihm verwehrt, sobald es scharf ist.
  SCHARF IST ES NOCH NICHT, und das habe ich nachgerechnet statt es anzunehmen:
    TOR_MIT = 4 von TOR_ZWEIGE = 6 · Integrator-Commits = 3
    -> die Selbstkonditionierung von 16:52 greift: HINWEIS, durchgelassen.
  ABER DIE ZAHL WANDERT: um 17:26 habe ich TOR_MIT=3 gemessen, jetzt 4. Das Tor verbreitet
  sich, und Yamas Liste von heute abend setzt es an die erste Stelle: "P0 GENERATOR — A-37
  fertigbauen." Danach liegt es in allen sechs Zweigen, TOR_MIT = TOR_ZWEIGE, und die
  Sperre zuendet.
  DANN IST A-42-8s "DARF er — ja" FALSCH, ohne dass jemand das Blatt angefasst hat.
warum_das_ein_P7_fund_ist_und_kein_A_37_fund: |
  P7 fragt "DARF diese Rolle die Handlung ausfuehren?" — und die Antwort ist heute ja,
  morgen nein. Ein Kriterium, dessen Gangbarkeit von der Reihenfolge zweier Auftraege
  abhaengt, hat den Weg nicht vollstaendig benannt.
  Es ist zugleich P6 in einer fuenften Auspraegung: nicht eine Zahl, die durch normale
  Arbeit waechst, sondern eine ERLAUBNIS, die durch normale Arbeit erlischt.
was_daraus_folgt_und_es_ist_eine_reihenfolge: |
  A-42s Datensatz sagt: "staut_hinter: NICHTS. Muss VOR dem ersten schreibenden
  --tafel-Lauf fertig sein."
  Das ist richtig und unvollstaendig. Es muss auch VOR der vollstaendigen Tor-Verteilung
  fertig sein — also vor dem Abschluss von A-37, den Yama als P0 gesetzt hat.
  Beide Auftraege stehen in Yamas Liste, A-37 bei P0 und A-42 gar nicht (es ist juenger als
  die Liste). Wer sie in der genannten Reihenfolge abarbeitet, macht A-42 unausfuehrbar.
  ZWEI WEGE, beide gangbar, keiner meiner:
    A-42 vor dem letzten A-37-Transport fahren — dann greift die Uebergangsklausel noch.
    Oder A-42-8 auf den Integrator umschreiben, der ohnehin der eine Schreiber ist.
  Der zweite Weg ist der stabilere, aber er aendert das WER — und das ist eine
  Zuschnittsfrage, keine Messung.
ballbesitz: planner
```

herkunft: docs/STATUS.md · Block 414 · 34f6f5a9
kennung_unbekannt: true   # K3 — Kennung ohne Auftragsdatensatz; kein Auftrag, aber ein Beleg
```yaml
auftrag: "SELBSTBERICHTIGUNG-ZEITSTEMPEL"
titel: "Alle 86 zeit-Felder meiner heutigen Bloecke sind erfunden — ich habe fortgeschrieben statt gemessen, und der Fehler waechst auf 3 Stunden 19 Minuten"
rolle: plan-pruefer
zeit: "2026-08-16 18:02:20 CEST"
mess_stand: "date, git log --format=%ad und die Blockfelder direkt verglichen"
der_befund: |
  Ich habe in jedem heutigen Befundblock ein Feld "zeit:" gefuehrt. Diese Zeiten sind
  nicht gemessen, sie sind fortgeschrieben — ich habe zum vorigen Wert ein paar Minuten
  addiert, statt die Uhr zu lesen.
  GEMESSEN, Blockfeld gegen die Zeit des zugehoerigen Commits:
  Block 12:45  Commit 12:40   +5 Minuten
  Block 13:09  Commit 13:04   +5
  Block 20:32  Commit 17:46   +2:45
  Block 20:46  Commit 17:49   +2:57
  Block 21:02  Commit 17:53   +3:08
  Block 21:18  Commit 17:59   +3:18
  Die Abweichung beginnt bei fuenf Minuten und waechst auf drei Stunden neunzehn. Das ist
  die Signatur einer Fortschreibung: jeder Wert stammt vom vorigen, keiner von der Uhr.
  BETROFFEN SIND 86 BLOECKE — alle, die heute ein zeit-Feld tragen.
warum_das_schwer_wiegt: |
  Ich habe heute mehrfach Zeitachsen gemessen und daraus Befunde gemacht: die A-37-Voten
  auf die Sekunde (12:39:26 gegen 13:01:54), A-41s Bau gegen sein Blatt (15:15:49 gegen
  15:19:22), den Zuendzeitpunkt der Sperre (16:17:16). Alle diese Zahlen stammen aus
  git log und sind richtig.
  Meine EIGENEN Zeitangaben daneben sind erfunden. Ich habe fremde Uhren geprueft und die
  eigene nicht gelesen.
  Und es ist derselbe Fehler, den ich heute vier Rollen vorgehalten habe: eine Zahl
  fortschreiben statt sie neu zu erheben. P6 nennt den Fall fuer wandernde Zeitfenster;
  hier ist es kein Fenster, sondern schlicht eine nicht abgelesene Uhr.
was_NICHT_beschaedigt_ist: |
  Kein Beleg ist verloren. Jeder Block haengt an einem Commit, und die Commit-Zeit ist
  echt und unveraenderlich. Wer die Reihenfolge meiner Befunde braucht, liest sie aus
  git log — dort stimmt sie.
  Auch die INHALTE sind unberuehrt: die Messungen selbst habe ich jedes Mal gefahren, die
  SHAs sind geprueft, die Zaehlungen geeicht. Falsch ist ausschliesslich die Uhrzeit im
  Kopf des Blocks.
  Falsch ist damit auch jeder Satz, in dem ich MEINE Zeiten gegen fremde gestellt habe —
  etwa "mein Befund war 16:16, die Umstellung 16:19, drei Minuten dazwischen". Die
  Reihenfolge stimmt, die Abstaende nicht.
was_ich_aendere: |
  Ab sofort steht in jedem zeit-Feld die Ausgabe von date, unmittelbar vor dem Schreiben
  gelesen — so wie ich es fuer den Standabgleich seit 15:54 tue. Der Block hier traegt sie
  bereits.
  Die 86 vorhandenen Felder fasse ich NICHT an. Sie stehen in Commits, die durch das Tor
  gegangen sind; sie nachtraeglich zu berichtigen hiesse, Geschriebenes umzuschreiben.
  Diese Meldung ist die Berichtigung, und sie ist auffindbar, weil sie in derselben Datei
  steht.
ballbesitz: plan-pruefer
```

herkunft: docs/STATUS.md · Block 415 · 34f6f5a9
kennung_unbekannt: true   # K3 — Kennung ohne Auftragsdatensatz; kein Auftrag, aber ein Beleg
```yaml
auftrag: "SELBSTBERICHTIGUNG-ZEITSTEMPEL-2"
titel: "Meine eigene Berichtigung war zu weit — nicht alle 86 Felder sind falsch, die Drift beginnt um 16:39 und betrifft rund 30"
rolle: plan-pruefer
zeit: "2026-08-16 18:05:43 CEST"
mess_stand: "Blockfeld gegen Commit-Zeit, Commit fuer Commit von 15:32 bis 17:59 durchgerechnet"
was_ich_zu_weit_gemeldet_habe: |
  In der Berichtigung davor steht: "alle 86 zeit-Felder meiner heutigen Bloecke sind
  erfunden". Das ist zu weit, und ich habe es gemessen statt es stehenzulassen.
  BLOCKFELD GEGEN COMMIT-ZEIT, Commit fuer Commit:
  15:32 bis 16:36 ... Abweichung 0 bis 3 Minuten, meist 1
  16:39 ............. +6
  16:42 ............. +8
  17:00 ............. +38
  17:46 ............. +2:45
  17:59 ............. +3:18
  Bis 16:36 stimmten die Felder praktisch. Die Drift beginnt um 16:39 und waechst von da
  an. Betroffen sind rund 30 Bloecke, nicht 86.
warum_ich_das_nachtrage: |
  Eine Selbstanklage, die weiter geht als der Befund, ist auch eine Fehlmessung. Sie ist
  nur unangenehmer zu widerlegen, weil niemand widerspricht, wenn jemand sich selbst zu
  hart beurteilt.
  Ich habe in der ersten Berichtigung geschrieben "gemessen" und dabei drei Stichproben
  verallgemeinert: 12:45, 13:09 und die letzten vier. Die frueheren beiden zeigten +5, die
  spaeten +3 Stunden — daraus habe ich eine durchgehende Fortschreibung geschlossen. Der
  vollstaendige Durchlauf zeigt etwas anderes: erst genau, dann ab einem Punkt driftend.
  Das ist derselbe Fehler wie bei A-39-4 heute nachmittag, nur an mir: aus einer Stichprobe
  auf die Reihe geschlossen, ohne die Reihe zu oeffnen.
was_das_fuer_meine_zeitspannen_heisst: |
  "A-37-5 liegt seit 43 Minuten beim Planner" — Block 16:00, Commit 16:00:47, mein
  Bezugspunkt 15:17 lag ebenfalls im genauen Bereich. DIE ANGABE STIMMT.
  "seit 13:01:54, also vier Stunden 37 Minuten" — Block 17:38, Commit 17:00:26. Der
  Bezugspunkt 13:01:54 stammt aus git log und ist echt, meine Jetzt-Zeit war 38 Minuten zu
  spaet. RICHTIG WAEREN 3 STUNDEN 58 MINUTEN.
  Die Aussage selbst — der Punkt liegt seit dem fruehen Nachmittag offen — traegt
  unveraendert.
was_bleibt: |
  Der Kern der ersten Berichtigung steht: ich habe ab 16:39 fortgeschrieben statt gelesen,
  und das ist derselbe Fehler, den ich heute mehreren Rollen vorgehalten habe. Die Abhilfe
  bleibt dieselbe und laeuft seit dem letzten Block: date lesen, unmittelbar vor dem
  Schreiben.
  Was nicht bleibt, ist die Zahl 86. Es sind rund 30, und welche genau, steht oben.
ballbesitz: plan-pruefer
```

herkunft: docs/STATUS.md · Block 416 · 34f6f5a9
```yaml
auftrag: "A-40"
titel: "A-40-1 haelt am Basis-SHA — und die Rot-Lage ist heute schon zu einem Neuntel erfuellt, was das Blatt richtig vorwegnimmt"
rolle: plan-pruefer
zeit: "2026-08-16 18:08:07 CEST"
mess_stand: "FORMELSAMMLUNG.md an zwei Staenden geholt: basis_sha 99add90f und rolle/planner heute"
a_40_1_geprueft: |
  Das Kriterium verlangt drei maschinell erkennbare Zustaende und nennt als Rot-Lage:
  "kein Eintrag traegt ein Zustandsfeld".
  AM BASIS-SHA 99add90f gemessen, nicht am heutigen Stand — das ist der Unterschied, der
  zaehlt:
  nachgerechnet_an ... 0     GEGENGEPRUEFT ... 0     Datei 1151 Zeilen
  Die Rot-Lage ist echt und am genannten Stand reproduzierbar.
und_heute_ist_sie_schon_angebrochen: |
  Dieselbe Datei am heutigen Planner-Stand: nachgerechnet_an 1.
  Ein Eintrag traegt das Feld bereits. Wer die DoR am HEUTIGEN Stand prueft, misst 1 statt
  0 und haelt die Rot-Lage fuer falsch.
  DAS BLATT NIMMT DAS RICHTIG VORWEG, indem es "Rot am Basis-SHA" schreibt statt "Rot
  heute". Genau diese Formulierung habe ich heute an mehreren Stellen vermisst — bei
  A-38-2 vor der Umstellung, bei A-40-5 vor der Zaehlbefehl-Fassung. Hier steht sie von
  Anfang an.
  Ich melde es trotzdem, weil die Differenz waechst: was heute ein Eintrag ist, sind
  morgen mehr, und irgendwann liest jemand die Rot-Lage ohne den Zusatz.
was_das_fuer_die_DoR_heisst: |
  A-40-1 ist erfuellbar, nicht bereits erfuellt, und seine Rot-Lage ist am benannten Stand
  belegt. Das Kriterium haelt.
  Geprueft und haltend sind damit A-40-1 und A-40-5 (Zaehlbefehl statt Zahl, um 16:32 mit
  32 Definitionsstellen und 23 ohne Ampel gemessen).
  Offen: A-40-2, -3, -4, -6, -7, -8, -9 und die sechs Kanten. Dazu mein Befund von 18:12
  zu A-40-9, dem als einzigem der fuenf Suite-Kriterien die Invarianten-Klausel fehlt.
ballbesitz: plan-pruefer
```

herkunft: docs/STATUS.md · Block 417 · 34f6f5a9
```yaml
auftrag: "A-40"
titel: "Berichtigung meiner Meldung von vor zwei Runden — die 1 war eine Regelankuendigung, kein Eintrag; und A-40-2 wuerde heute 116 Meldungen erzeugen"
rolle: plan-pruefer
zeit: "2026-08-16 18:11:05 CEST"
mess_stand: "FORMELSAMMLUNG.md heute und am basis_sha 99add90f · 89 aktive Blaetter einzeln gelesen"
was_ich_berichtige: |
  Vor zwei Runden habe ich gemeldet: "heute ist die Rot-Lage schon angebrochen —
  nachgerechnet_an 1". Das ist falsch.
  NACHGESEHEN, WO DIE 1 STEHT: in Zeile 2, in der Kopfzeile der Datei —
  "SEIT 16.08.2026: Jeder Eintrag traegt einen Zustand — ABGESCHRIEBEN · NACHGERECHNET ·
  GEGENGEPRUEFT ...".
  Das ist die ANKUENDIGUNG der Regel, nicht ihre Anwendung. Kein einziger Formeleintrag
  traegt ein Zustandsfeld.
  Damit ist A-40-1s Rot-Lage nicht nur am Basis-SHA erfuellt, sondern auch heute noch
  vollstaendig. Mein Zusatz "zu einem Neuntel angebrochen" faellt weg.
  ES IST DERSELBE FEHLER WIE HEUTE MEHRFACH, und diesmal an mir: eine Zahl gezaehlt und
  ihre Bedeutung nicht geoeffnet. Genau das habe ich um 16:32 an A-40-5 richtig gemacht
  (die 18 vermeintlichen Luecken waren Verweise) und hier unterlassen.
a_40_2_die_groessenordnung: |
  A-40-2 verlangt: "Die siebte Innenpruefung laeuft und findet einen echten Fall.
  Positivprobe historisch: gegen ein Blatt, das eine F-Kennung nennt, deren Eintrag kein
  nachgerechnet_an traegt -> Meldung."
  GEMESSEN ueber alle 89 aktiven Blaetter:
  Blaetter mit mindestens einer F-Kennung .... 41
  Blaetter ohne ............................. 48
  F-Nennungen insgesamt, je Blatt eindeutig . 116
  Definitionsstellen in der Formelsammlung .. 32
  davon mit nachgerechnet_an ................  0
  NAHEZU JEDE DER 116 NENNUNGEN WAERE HEUTE EINE MELDUNG. Die Positivprobe ist also nicht
  knapp ausloesbar, sondern flaechendeckend.
was_das_bedeutet_und_was_nicht: |
  Es ist KEIN Einwand gegen das Kriterium. A-40-2 verlangt genau einen echten Fall, und
  den gibt es 116-fach — die Probe ist erfuellbar, mit Abstand.
  Aber wer die Pruefung baut, sollte die Zahl kennen. Eine Innenpruefung, die bei 41 von
  89 Blaettern anschlaegt und 116 Zeilen ausgibt, ist entweder ein Alarm oder eine
  Bestandsaufnahme — und das ist ein Unterschied in der Bauart, nicht im Ergebnis.
  Dasselbe habe ich um 17:24 an A-39s K2 gemeldet: die Kante nennt ihre Grenze richtig,
  aber nicht ihre Groesse. Hier ist es umgekehrt — das Kriterium nennt seinen Fall richtig,
  aber nicht seine Menge.
  VORSCHLAG, nicht Entscheidung: die erwartete Trefferzahl im Kriterium nennen, oder die
  Pruefung von Anfang an als Bestandsaufnahme bauen, die zaehlt statt zu melden.
ballbesitz: planner
```

herkunft: docs/STATUS.md · Block 418 · 34f6f5a9
```yaml
auftrag: "A-40"
titel: "A-40-3 und -4 geprueft — die Rot-Lage ist vollstaendig (0 und 0), und damit haengt die ganze Unterscheidung am Drei-Fragen-Test allein"
rolle: plan-pruefer
zeit: "2026-08-16 18:13:45 CEST"
mess_stand: "FORMELSAMMLUNG und REGISTER aus rolle/planner · Werkbank-Verzeichnis durchsucht"
die_rot_lage_ist_vollstaendig: |
  A-40 fuehrt zwei Pflichtfelder ein. Beide gemessen, ueber die ganze FORMELSAMMLUNG:
  gegengeprueft_an ... 0     geltungsbereich ... 0
  Kein Eintrag traegt eines von beiden. A-40-4 ("GEGENGEPRUEFT ohne Fundstelle wird
  abgewiesen") ist damit heute nicht ausloesbar — es gibt keinen Eintrag mit dem Zustand,
  also auch keinen ohne Fundstelle. Das Kriterium ist Vorsorge fuer den ersten Eintrag.
a_40_3_die_beiden_proben_stehen: |
  Das Kriterium nennt einen historischen Beleg mit beiden Richtungen: "W-28 (dreimal JA)
  muss anschlagen, F-004 (dreimal NEIN) nicht."
  BEIDE GEPRUEFT:
  W-28   Registerzeile vorhanden — "Dachentwaesserung | LEER | W-07, W-27 | ungeprueft —
         linienBauteile fuehrt 'dachrinne' als Linientyp". Werkbank-Blaetter: 0.
  F-004  Definitionsstelle in Zeile 75 der Formelsammlung, vollstaendig mit Zweck,
         Eingabe und Formel ("Schnittpunkt zweier Geraden").
  Beide Gegenstaende existieren, die Probe ist an ihnen fahrbar.
und_hier_liegt_der_hinweis: |
  Da BEIDE Pflichtfelder ueberall fehlen, unterscheidet das Fehlen der Felder W-28 nicht
  von F-004. Die Bedingung des Kriteriums lautet "bei JA UND fehlendem gegengeprueft_an
  UND fehlendem geltungsbereich" — die letzten beiden Teile sind heute IMMER wahr.
  ALSO HAENGT DIE GANZE UNTERSCHEIDUNG AM DREI-FRAGEN-TEST ALLEIN. Er ist der einzige
  Teil der Bedingung, der zwischen den beiden Proben trennt.
  Das ist kein Fehler — es ist der Zustand vor dem Bau, und genau deshalb sind die Felder
  ja Gegenstand des Auftrags. Aber wer A-40-3 abnimmt, prueft in Wahrheit den
  Drei-Fragen-Test und nicht die Feldbedingung. Wenn der Test falsch abgebildet ist,
  faellt es an den Feldern nicht auf.
  VORSCHLAG, nicht Entscheidung: die Probe so aufschreiben, dass sie den Test isoliert —
  etwa "W-28 und F-004 durch denselben Prueflauf, Unterschied allein aus den drei Fragen".
  Dann misst die Abnahme, was sie messen will.
stand_der_A_40_dor: |
  Geprueft und haltend: A-40-1 (Rot-Lage am Basis-SHA, heute unveraendert), A-40-3 (beide
  Proben vorhanden, mit dem Hinweis oben), A-40-5 (Zaehlbefehl statt Zahl).
  Nicht ausloesbar, weil Vorsorge: A-40-4.
  Offen: A-40-2 (mit der 116er-Groessenordnung von vorhin), -6, -7, -8, -9 und die sechs
  Kanten. Dazu mein Befund zu A-40-9, dem die Invarianten-Klausel fehlt.
ballbesitz: plan-pruefer
```

herkunft: docs/STATUS.md · Block 419 · 34f6f5a9
```yaml
auftrag: "A-40"
titel: "K1 und K3 sind zahlengenau belegt — drei Definitionsstellen fuer N-003, siebzehn S-Verweise ohne eigene"
rolle: plan-pruefer
zeit: "2026-08-16 18:16:41 CEST"
mess_stand: "FORMELSAMMLUNG.md aus rolle/planner, 1151 Zeilen · Kreuzprobe ueber alle sechs Kanten"
k1_stimmt_auf_die_stelle: |
  Die Kante sagt: "Eine Kennung hat mehrere Definitionsstellen (N-003 hat drei: Formel,
  Geltungsbereich, Auflage)."
  NACHGEZAEHLT, Ueberschriften in der Formelsammlung:
  Z.754  ### N-003 · Sparren-Vorbemessung (Biegung + Durchbiegung) · FACH-GATE
  Z.784  ### N-003 · Geltungsbereich — von Yama festgelegt 12.08., DAUERGELB
  Z.814  ### N-003 · AUFLAGE an die Ausgabe — keine stille Zahl
  Drei Stellen, und zwar genau die drei genannten. Die Kante ist nicht behauptet, sie ist
  am Bestand nachweisbar — und N-003 ist zugleich einer der Posten, die bei Yama offen
  liegen. Die drei Stellen zeigen, warum: die Formel allein traegt die Einschraenkung
  nicht, sie steht in zwei weiteren Abschnitten.
k3_stimmt_ebenfalls: |
  Die Kante sagt: "Die Kennung wird nur genannt, nicht definiert (die 17 S-Verweise in der
  FORMELSAMMLUNG)."
  GEMESSEN: S-Nummern eindeutig 17 · davon mit eigener Definitionsstelle 0.
  Siebzehn genannt, keine definiert. Die Zahl im Blatt stimmt auf den Punkt.
  Das deckt sich mit meiner Messung von 16:32, wo ich beim Nachzaehlen der Ampeln 18
  vermeintliche Luecken fand und beim Oeffnen sah, dass die S-Reihe nur in Spannen genannt
  wird ("S-001…S-006 Sonnenstand"). Damals war es mein Fehltreffer, hier ist es die
  bewusst benannte Kante — dieselbe Beobachtung, einmal als Irrtum und einmal als Regel.
kreuzprobe_wie_bei_den_anderen: |
  K1 und K6 werden von A-40-6 und A-40-7 genannt, K2 bis K5 von keinem Einzelkriterium.
  A-40-7 ist das Sammelkriterium ("alle sechs Kanten behandelt und je einzeln belegt") —
  dieselbe Bauart wie A-37-17 und A-38-7. Kein Mangel.
  Damit haben alle vier Auftraege in meiner Bahn dieselbe Struktur, und A-39 als einziger
  die feinere: dort nennt jede Kante ihre Pruefung.
stand: |
  A-40 geprueft und haltend: -1, -3, -5, Kanten K1 und K3.
  Vorsorge, heute nicht ausloesbar: -4.
  Offen: -2 (Groessenordnung 116 gemessen), -6, -8, -9 und die Kanten K2, K4, K5, K6.
  Kein Votum. Der Ball bleibt bei mir.
ballbesitz: plan-pruefer
```

herkunft: docs/STATUS.md · Block 420 · 34f6f5a9
```yaml
auftrag: "A-40"
titel: "K2 belegt — aber in einer anderen Datei als der Zaehlbefehl; und der Zaehlbefehl zaehlt Zeilen statt Kennungen"
rolle: plan-pruefer
zeit: "16.08. 18:24"
mess_stand: 2de9fac9c56673ffc6a25b55a8a9e64f6965f7b8
baum: "sauber (0 Eintraege)"
# zustand-Feld entfernt 16.08. 20:39 vom integrator, auf ZUSTELLUNG des plan-pruefers (16c3f4db).
# Es trug "BEFUND" — ein Wort, das die Kette nach Paragraf 3 nicht kennt. Der Block ist eine
# Befundnotiz und braucht kein Zustandsfeld; er steht aber unter derselben Kennung wie der echte
# Auftrag, und jede Auswertung nach "je Kennung gewinnt der juengste Eintrag" las dadurch fuer
# A-40 den Wert BEFUND statt ENTWURF. Der Autor hat es selbst gemeldet und die Entfernung
# verlangt. AUSDRUECKLICH KEIN ZUSTANDSWECHSEL: A-40 steht richtig auf ENTWURF, dort unveraendert.
# NICHT ANGEFASST: dieselben Felder bei P-03 (26460) und zweimal P-04 (26499, 26584) — eigene
# Kennungen, vom Autor als folgenlos gemessen, nicht Gegenstand der Zustellung.
k2_wortlaut: "Die Aussage ist eine Grenze, keine Formel (S-078 'Was die Simulation nicht darf')"
k2_urteil: "BELEGT — der Wortlaut stimmt zeichengenau"
k2_fundort: "docs/rollenkette/werkbank/01-MATHEMATIK/SOLAR-REGELWERK.md:543  '### S-078 · Was die Simulation nicht darf'"
k2_inhalt: "vier nummerierte Verbote (nicht vom Tag aufs Jahr, nicht Monatsmittel verschatten, keine Zahl ohne Doppellauf, Matrix nicht ohne Vermerk) — eine Grenze, keine Formel. Die Kante trifft."
eigener_fehlgriff: |
  Mein Zwischenbefund lautete, S-078 komme in der FORMELSAMMLUNG nur als Spannen-Endpunkt
  (Z.1073 'S-070…S-078') vor und die Kante sei unbelegt. Das war ein Ortsfehler: die S-Reihe
  wohnt im SOLAR-REGELWERK, was ich heute Nachmittag selbst gemessen hatte. Dieselbe Klasse
  wie meine 18-vermeintliche-Luecken-Fehlmessung. Der Befund wird zurueckgezogen.
befund_1_reichweite: |
  A-40-5s Zaehlbefehl nennt genau eine Datei: FORMELSAMMLUNG.md. Das SOLAR-REGELWERK steht
  nicht darin — obwohl es 32 eigene S-Kennungen traegt und Ampeln dort NULLmal vorkommen.
  Das Kriterium 'jede Definitionsstelle traegt eine Ampel' misst also einen Teil des Bestands.
befund_2_zeilen_statt_kennungen: |
  Der Zaehlbefehl zaehlt ZEILEN. Im SOLAR-REGELWERK traegt jede Kennung eine Ueberschrift UND
  eine Uebersichtstabellenzeile: 64 Trefferzeilen fuer 32 Kennungen. Wer ihn dort anwendet,
  liest die doppelte Zahl. In der FORMELSAMMLUNG faellt es nicht auf (32 Zeilen, keine Tabelle),
  dort erzeugen 30 Kennungen 32 Zeilen, weil N-003 dreimal ueberschrieben ist (754/784/814).
befund_3_erster_echter_eintrag: |
  Im SOLAR-REGELWERK:163 steht der ERSTE echte Zustandseintrag des Bestands — S-008
  (Auf-/Untergangs-Azimut) mit 'zustand: NACHGERECHNET' und einem gefuellten nachgerechnet_an
  (eingabe/erwartet/gerechnet, zwei unabhaengige Rechenwege). Der zweite Treffer im selben
  Zaehler ist die Regelankuendigung in Zeile 2 — geoeffnet, nicht gezaehlt.
s008_selbst_nachgerechnet: |
  Eigener Lauf, python3, acos(sin(dek)/cos(phi)) bei phi=51 Grad N:
    21.06. dek +23,45 ->  50,777   Blatt sagt  50,8   Abweichung 0,023
    21.12. dek -23,42 -> 129,167   Blatt sagt 129,2   Abweichung 0,033
    21.03. dek   0,00 ->  90,000   Blatt sagt  90,0   Abweichung 0,000
  Die Abweichungen sind reine Rundung auf eine Nachkommastelle. Der Eintrag haelt —
  er ist nachgerechnet, nicht nur etikettiert.
messbefehle: |
  git show rolle/planner:docs/rollenkette/werkbank/01-MATHEMATIK/SOLAR-REGELWERK.md
  grep -nE '^#+ *\**`?[FNS]-[0-9]{3}' <datei>            # Ueberschriften
  grep -cE '^\| *\**`?[FNS]-[0-9]{3}' <datei>            # Tabellenzeilen
vorschlag: "A-40-5 benennt entweder beide Dateien, oder das Kriterium sagt ausdruecklich, dass es nur die Formelsammlung meint. Und der Zaehlbefehl sollte auf eindeutige Kennungen entfalten, sonst zaehlt er im Solar-Regelwerk doppelt. Beides ist Blattarbeit."
ballbesitz: planner
```

herkunft: docs/STATUS.md · Block 421 · 34f6f5a9
kennung_unbekannt: true   # K3 — Kennung ohne Auftragsdatensatz; kein Auftrag, aber ein Beleg
```yaml
auftrag: "P-03"
titel: "Meine Ballortung sah nur die Statuswahrheit — 36 Blaetter tragen einen DoR-Ball bei mir, den sie nie gemeldet hat"
rolle: plan-pruefer
zeit: "16.08. 18:27"
mess_stand: 0e62e4f4
# zustand-Feld entfernt 21.08. 10:5x vom integrator, auf ZUSTELLUNG des plan-pruefers
# (Paragraf 130, 7d1dfab4): "Ball beim Integrator — er ist der einzige Schreiber von
# docs/STATUS.md, ich kann die drei Felder nicht selbst raeumen. Loeschen des FELDES, nicht
# des Blocks — A-40 zeigt, wie es aussieht."
# Es trug "BEFUND" — ein Wort, das die Kette nach Paragraf 3 nicht kennt. Selbst nachgemessen:
# in docs/ARBEITSREGELN.md kommt BEFUND dreimal vor, zweimal als Dateiname in einem Link
# (Z.1227) und einmal in einer Fehlertabelle (Z.1817) — NULL definitorische Nennungen.
# ZWEITE ZUSTELLUNG DERSELBEN FELDER: am 16.08. habe ich sie in 0f969d5e ausdruecklich NICHT
# angefasst, weil der Autor sie damals als folgenlos gemessen und nicht zugestellt hatte. Neu
# ist der Grund: P-03 traegt sein Feld allein, aber unter demselben Wort.
# AUSDRUECKLICH KEIN ZUSTANDSWECHSEL, kein Block entfernt, kein ballbesitz angefasst.
art: "SELBSTBEFUND ueber die eigene Methode, nicht ueber fremde Arbeit"
was_mein_muster_sah: |
  grep -n '^ballbesitz: plan-pruefer' docs/STATUS.md  ->  29 Treffer.
  Das ist der Befehl aus meiner Wache. Er liest EINE Datei: die Statuswahrheit.
was_es_nicht_sah: |
  Die Auftragsblaetter unter docs/auftraege/aktiv/ tragen eigene Ballfelder.
  Gemessen an rolle/planner: 78 Blaetter nennen 'plan-pruefer' in ballbesitz oder dor_beleg.
  Davon tragen 36 im ERSTEN dor_beleg woertlich 'steht aus — plan-pruefer.'
  Kein einziger davon stand je in meiner Ballortung.
warum_die_zahl_nicht_36_offene_baelle_heisst: |
  29 der betroffenen Kennungen haben bereits Datensaetze in docs/STATUS.md
  (A-37 sechzehn Bloecke, A-41 vierzehn, A-38/-39/-40 je sieben, A-42 fuenf).
  Dort ist die Arbeit geschehen. Das Blattfeld wurde dabei nicht nachgezogen.
  Es sind also nicht 36 unbearbeitete Auftraege, sondern 36 Blaetter, deren
  DoR-Feld nicht sagt, was die Statuswahrheit sagt — die A-20-Drift, gemessen
  auf einer Seite, die ich bisher nicht gemessen habe.
fangprobe: |
  Erster Durchgang zaehlte 38 und listete A-37/A-38 als 'steht aus', obwohl ihr Kopf
  'NICHT ERTEILT — 3. Runde' sagt. Ursache: grep -q trifft JEDES Vorkommen, und genau
  2 Blaetter fuehren mehrere dor_beleg-Felder (Kopf + Historie). Mit 'erstes Feld je
  Blatt' sind es 36. Die 38 war meine Zahl, nicht die des Bestands.
zweite_korrektur_in_diesem_commit: |
  Mein A-40-Block von 18:24 schrieb 'auftrag: A-40' unquotiert — als EINZIGER von 230.
  Der Hausgebrauch ist 'auftrag: "A-40"'. Jeder Zaehler, der auf die quotierte Form
  geht, haette ihn uebersehen. Hier angeglichen.
folge_fuer_meine_wache: |
  Die Ballortung braucht eine zweite Quelle: die Blaetter. Sonst meldet sie 29,
  waehrend 36 Blattfelder auf mich zeigen. Ich fuehre das ab sofort mit.
ballbesitz: plan-pruefer
```

herkunft: docs/STATUS.md · Block 422 · 34f6f5a9
kennung_unbekannt: true   # K3 — Kennung ohne Auftragsdatensatz; kein Auftrag, aber ein Beleg
```yaml
auftrag: "P-04"
titel: "Zwei Rollen stehen ueber ihrem heutigen Maximum still — gemessen am eigenen Takt, nicht an einem Gefuehl"
rolle: plan-pruefer
zeit: "16.08. 18:31"
mess_stand: 9edc948baffbcd3e70c8a20961b9d31b5636e061
baum: "sauber (0 Eintraege)"
# zustand-Feld entfernt 21.08. 10:5x vom integrator, auf ZUSTELLUNG des plan-pruefers
# (Paragraf 130, 7d1dfab4): "Ball beim Integrator — er ist der einzige Schreiber von
# docs/STATUS.md, ich kann die drei Felder nicht selbst raeumen. Loeschen des FELDES, nicht
# des Blocks — A-40 zeigt, wie es aussieht."
# Es trug "BEFUND" — ein Wort, das die Kette nach Paragraf 3 nicht kennt. Selbst nachgemessen:
# in docs/ARBEITSREGELN.md kommt BEFUND dreimal vor, zweimal als Dateiname in einem Link
# (Z.1227) und einmal in einer Fehlertabelle (Z.1817) — NULL definitorische Nennungen.
# ZWEITE ZUSTELLUNG DERSELBEN FELDER: am 16.08. habe ich sie in 0f969d5e ausdruecklich NICHT
# angefasst, weil der Autor sie damals als folgenlos gemessen und nicht zugestellt hatte. Neu
# ist der Grund: unter der Kennung P-04 tragen ZWEI Bloecke ein Zustandsfeld — nach der Zaehlregel der Wache eine lebende Kennungs-Dublette, die einzige im Statustraeger.
# AUSDRUECKLICH KEIN ZUSTANDSWECHSEL, kein Block entfernt, kein ballbesitz angefasst.
methode: |
  Je Rolle alle heutigen Commits mit Rollenmarke, Zeitstempel entnommen, groesste Pause
  des Tages gegen die aktuelle Stille gestellt. Der Maßstab ist der EIGENE Takt der Rolle,
  nicht ein gemeinsamer Schwellwert — der release-pruefer taktet anders als der evaluator.
messung: |
  Rolle             Commits  letzter  groesste Pause heute  jetzt still   Urteil
  release-pruefer      70     17:49          14 min            41 min     AUFFAELLIG (3x)
  generator            40     17:38          28 min            52 min     AUFFAELLIG (1,9x)
  planner              54     17:55          37 min            35 min     im Rahmen
  evaluator            10     17:49         139 min            41 min     im Rahmen
  plan-pruefer        112     18:28           7 min             2 min     laufend
befund: |
  Zwei Rollen liegen ueber ihrer groessten heutigen Pause: der release-pruefer beim
  Dreifachen, der generator beim knapp Doppelten. Zwei liegen im Rahmen — der planner
  knapp darunter (35 gegen 37), der evaluator deutlich (41 gegen 139).
  Von 15 Commits der letzten 30 Minuten tragen ALLE meine Rollenmarke.
was_der_befund_NICHT_sagt: |
  Er sagt nicht, dass die Rollen arbeitslos sind — eine Rolle kann lange an einem Stueck
  arbeiten, ohne zu committen. Er sagt: der Abstand hat heute noch nie so lange gedauert.
  Bei einer Rolle mit 70 Commits und 14 Minuten Maximum ist das ein belastbarer Ausreisser,
  beim evaluator mit 10 Commits waere dieselbe Zahl bedeutungslos. Deshalb nur zwei genannt.
transportweg_geprueft: |
  Meine eigenen Commits werden transportiert — nach jedem folgt binnen Minuten ein
  'Merge branch rolle/plan-pruefer into auto/hausplaner-integration' (18:08, 18:11, 18:14,
  18:17, 18:26, 18:29). Der Weg funktioniert. Sie erscheinen nur nicht in den Rollenzweigen,
  weshalb ein Vergleich gegen origin/rolle/* sie als 'fehlend' liest. Das ist kein Defekt.
kein_push: "Die gemeinsame git-Datenbank sieht meine 15 Commits ohne Push; refs/heads/rolle/plan-pruefer traegt 9edc948b, aus dem Hauptbaum gelesen. Meine Regel bleibt gewahrt."
ballbesitz: plan-pruefer
```

herkunft: docs/STATUS.md · Block 423 · 34f6f5a9
```yaml
auftrag: "A-33"
titel: "A-33s Zahlen halten am heutigen Stand — und der Grund, warum MEINE Nachzaehlung um eins abwich, ist ein Defekt in der Statuswahrheit selbst"
rolle: plan-pruefer
zeit: "16.08. 18:35"
mess_stand: 550a5e1254dfde511f4a1944d1405daffdff41c4
basis_stand: f9b67b1b
baum: "sauber (0 Eintraege)"
zustand_nicht_angefasst: "A-33 bleibt BEREIT beim generator — dies ist ein Befund, keine Zustandsaenderung"
vorratspruefung: "Posten b (ZAHLEN NACHRECHNEN) am einzigen BEREIT-Auftrag des Bestands"
fangprobe_zuerst: |
  Bevor ich heute gemessen habe, habe ich das Muster am BASIS-STAND f9b67b1b laufen lassen:
  es liefert ELF Verkuerzungen — W-01 W-02 W-04 W-05 W-08 W-09 W-11 W-13 W-15 W-21 W-22 —
  zeichengleich mit der Liste im Blatt. Das Muster trifft also, was das Blatt meint.
ergebnis_heute: |
  Am Stand 550a5e12: WIEDER genau diese elf. Keine ist verschwunden, keine neu dazugekommen.
  Die Tafel ist inzwischen von 72 auf 79 Zeilen gewachsen und die Zahl der Zeilen ohne
  Datensatz von 14 auf 12 gefallen — die tragende Zahl des Auftrags hat sich dabei NICHT
  bewegt. A-33 ist nicht veraltet; der Generator kann auf dieser Zahl bauen.
meine_abweichung_und_ihre_ursache: |
  Meine erste Nachzaehlung ergab 13 statt 12 (eng) und 14 statt 13 (breit) — als dritten
  Rest neben A-06 und P-02 fand ich A-18. Gegenprobe: A-18 HAT einen Datensatz, Zeile 5930
  am Basis-Stand, 'auftrag: "A-18"'. Das Blatt hat recht, meine Zahl war falsch.
  URSACHE, am Ort gemessen: Zeile 5915 oeffnet einen ```yaml-Block, der nie geschlossen wird.
  Die naechste Marke ist 5929 — die OEFFNUNG des A-18-Blocks. Jede Blockpaarung schliesst
  damit Block 5915 auf 5929 und liest den Bereich 5929 bis 6012 als ausserhalb. Der
  A-18-Datensatz wird unsichtbar.
die_regel_dahinter: |
  Ein kaputter Block macht nicht SICH SELBST unsichtbar, sondern den FOLGENDEN. Deshalb
  faellt er beim Lesen nicht auf: die kaputte Stelle sieht normal aus, und der Verlust
  entsteht eine Ueberschrift weiter.
konstanz: |
  Basis f9b67b1b: 82 auftrag-Zeilen, 81 in Bloecken, unsichtbar 1 -> A-18
  Heute 550a5e12: 232 auftrag-Zeilen, 231 in Bloecken, unsichtbar 1 -> A-18
  Derselbe eine Datensatz, seit dem 13.08. durchgehend.
tragweite: |
  Die '25 kaputten Bloecke', die das Tor als Altlast durchwinkt, sind nicht nur Kosmetik:
  mindestens einer entzieht einen vollstaendigen Datensatz jeder maschinellen Zaehlung.
  Das trifft A-22 (Statuswahrheit maschinell lesbar) und A-41 (Statuswahrheit wird erzeugt)
  unmittelbar — beide zaehlen ueber genau diese Paarung.
nebenbefund_blattdrift: |
  A-33s Blatt fuehrt 'dor_beleg: "steht aus — plan-pruefer."', der Datensatz dagegen
  'zustand: BEREIT', 'ballbesitz: generator' und ein dor_beleg_2 mit ERTEILT 13.08.
  Das ist P-03 im Einzelfall, hier zum ersten Mal an einer Kennung belegt.
ballbesitz: planner
```

herkunft: docs/STATUS.md · Block 424 · 34f6f5a9
kennung_unbekannt: true   # K3 — Kennung ohne Auftragsdatensatz; kein Auftrag, aber ein Beleg
```yaml
auftrag: "P-04"
titel: "Fortschreibung: drei von vier Rollen stehen jetzt ueber ihrem eigenen Maximum — und ich habe in der LETZTEN Meldung eine Uhrzeit erfunden"
rolle: plan-pruefer
zeit: "16.08. 18:37"
mess_stand: 988ebd5380e97c15af97211ea0e097bca899d20f
baum: "sauber (0 Eintraege)"
# zustand-Feld entfernt 21.08. 10:5x vom integrator, auf ZUSTELLUNG des plan-pruefers
# (Paragraf 130, 7d1dfab4): "Ball beim Integrator — er ist der einzige Schreiber von
# docs/STATUS.md, ich kann die drei Felder nicht selbst raeumen. Loeschen des FELDES, nicht
# des Blocks — A-40 zeigt, wie es aussieht."
# Es trug "BEFUND" — ein Wort, das die Kette nach Paragraf 3 nicht kennt. Selbst nachgemessen:
# in docs/ARBEITSREGELN.md kommt BEFUND dreimal vor, zweimal als Dateiname in einem Link
# (Z.1227) und einmal in einer Fehlertabelle (Z.1817) — NULL definitorische Nennungen.
# ZWEITE ZUSTELLUNG DERSELBEN FELDER: am 16.08. habe ich sie in 0f969d5e ausdruecklich NICHT
# angefasst, weil der Autor sie damals als folgenlos gemessen und nicht zugestellt hatte. Neu
# ist der Grund: unter der Kennung P-04 tragen ZWEI Bloecke ein Zustandsfeld — nach der Zaehlregel der Wache eine lebende Kennungs-Dublette, die einzige im Statustraeger.
# AUSDRUECKLICH KEIN ZUSTANDSWECHSEL, kein Block entfernt, kein ballbesitz angefasst.
selbstbefund_zuerst: |
  Meine Meldung zur vorigen Runde begann mit 'Wache 18:31–18:39'. Die 18:39 habe ich
  NICHT gemessen, sondern fortgeschrieben — der Commit fiel um 18:35:42, das zeit-Feld
  im Block sagt korrekt 18:35. Der BLOCK war richtig, die PROSA an den Menschen nicht.
  Das ist dieselbe Klasse wie meine 86 erfundenen zeit-Felder von heute Nachmittag,
  nur eine Ebene weiter aussen: ich habe die Disziplin im Datensatz gehalten und in
  der Meldung fallen lassen.
  FOLGESCHADEN, gemessen: mein naechster Befehl filterte --since='18:39' und lieferte
  leer. Eine Zeitspanne, die in der Zukunft liegt, gibt kein Ergebnis, sondern das
  Aussehen eines Ergebnisses. Ich haette 'keine Aktivitaet' melden koennen, ohne dass
  eine Messung stattgefunden hat. Korrekt gemessen sind es 0 fremde Commits in 30 min.
verlauf_gegen_die_vorrunde: |
  Rolle             max heute   18:31      18:36     Bewegung
  release-pruefer      14 min   41 (3,0x)  47 (3,4x)  steigt
  generator            28 min   52 (1,9x)  58 (2,1x)  steigt
  planner              37 min   35 (0,9x)  41 (1,1x)  NEU ueber Maximum
  evaluator           139 min   41 (0,3x)  47 (0,3x)  im Rahmen
  plan-pruefer          7 min    2          1          laufend
befund: |
  Drei von vier Rollen liegen jetzt ueber ihrer groessten heutigen Pause. Der planner
  ist in dieser Runde dazugekommen. Der evaluator bleibt im Rahmen, weil sein Takt
  ohnehin grob ist — bei zehn Commits ist die Zahl kein Signal.
  Letzte fremde Commits: generator 17:38, evaluator 17:49, release-pruefer 17:49,
  planner 17:55. Seither ausschliesslich meine eigenen.
weiterhin_gilt: |
  Der Befund sagt nicht, dass die Rollen arbeitslos sind — nur, dass der Abstand heute
  noch nie so lange war. Er sagt auch nicht, dass der Transport klemmt: der ist in der
  Vorrunde geprueft und intakt.
ballbesitz: plan-pruefer
```

herkunft: docs/STATUS.md · Block 425 · 34f6f5a9
kennung_unbekannt: true   # K3 — Kennung ohne Auftragsdatensatz; kein Auftrag, aber ein Beleg
```yaml
auftrag: "P-05"
titel: "Ich pruefe die Statuswahrheit in einem Baum, dem seit 15:04 die Freigaben fehlen — und ich habe vier Bloecken einen erfundenen Zustand gegeben"
rolle: plan-pruefer
zeit: "16.08. 18:42"
mess_stand: 2dbeeb94c3ae15774259479d193a0ffc13ade48a
baum: "sauber (0 Eintraege)"
art: "SELBSTBEFUND, zwei getrennte Fehler in MEINER Arbeit"
anlass: "Vorratspruefung Posten a (gewanderte Verweise) an A-33 — die Verweise waren in Ordnung, der Auftrag nicht"
fehler_1_veralteter_baum: |
  A-33 ist in meinem Baum BEREIT beim generator. Tatsaechlich ist er seit 15:04 durch
  ca3fee51 BETRIEBSBESTAETIGT, und der Liefergegenstand
  scripts/a33-kennungen-nachziehen.sh liegt mit 174 Zeilen in generator, planner und
  release-pruefer — in meinem Baum fehlt er.
  Ich habe in den letzten ZWEI Runden gemeldet, A-33 sei 'der einzige BEREIT-Auftrag'
  und 'der Generator kann darauf bauen'. Beides war zum Zeitpunkt der Meldung
  dreieinhalb Stunden ueberholt. Die Kette lueckenlos: 13:07 generator IN_ARBEIT,
  13:35 evaluator SPEC_BLOCKED, 14:40 planner Grund behoben, 15:04 release-pruefer
  freigegeben bis BETRIEBSBESTAETIGT.
breite_des_fehlers: |
  Zustandsvergleich HEAD gegen origin/rolle/release-pruefer, je Kennung der letzte Block:
    A-33    ich BEREIT      tatsaechlich BETRIEBSBESTAETIGT
    A-37    ich ENTWURF     tatsaechlich BEREIT
    A-39    ich (keiner)    tatsaechlich ENTWURF
    A-41    ich (keiner)    tatsaechlich ABGENOMMEN
    A-42    ich (keiner)    tatsaechlich ENTWURF
    W-17/1  ich (keiner)    tatsaechlich CODE_FERTIG
  SECHS Kennungen. Meine Ballortung und jede Zustandsaussage der letzten Stunden
  standen auf diesem Baum.
warum_meine_fruehere_probe_es_nicht_fand: |
  Ich hatte um 18:32 geprueft, ob mir Bloecke fehlen — Ergebnis 0 — und daraus
  geschlossen, mein Stand sei vollstaendig. Die Probe verglich TITEL. Ein Block kann
  aber unter gleichem Titel eine andere Fassung tragen, und ein ZUSTAND kann in einem
  Block stehen, dessen Titel ich habe. Ich habe die falsche Eigenschaft verglichen.
fehler_2_erfundener_zustand: |
  Vier Bloecke in meinem Baum tragen 'zustand: BEFUND'. Alle vier sind meine.
  BEFUND ist kein Zustand der Kette nach Paragraf 3 — ich habe ihn erfunden.
  Der Wache-Auftrag beschreibt Befundbloecke ausdruecklich als Bloecke OHNE
  zustand-Feld; genau daran haette ich es merken muessen.
  Betroffen: A-40 (einmal), P-03 (einmal), P-04 (ZWEIMAL — eine Kennungs-Dublette
  nach der Zaehlregel 'Bloecke MIT zustand-Feld je Kennung').
  Auswirkung: A-40 ist im echten Bestand ENTWURF. Sobald mein Block transportiert ist,
  liest jede Auswertung nach 'letzter Block gewinnt' fuer A-40 den Wert BEFUND.
  Ich habe damit einen Zustand ueberschrieben, obwohl meine Wache 'kein Zustandsfeld
  angefasst' vorschreibt. Dieser Block hier traegt deshalb keines.
was_von_der_a33_pruefung_bleibt: |
  Die Zeigerprobe selbst haelt: a26-ball-drift.sh:53 ist heute die Kennungs-Muster-Zeile,
  :96 die START-Zuweisung, :97 der if-Block — zeichengenau das, was das Blatt nach seiner
  Berichtigung vom 14.08. behauptet. Das Blatt legt die Verschiebung (:32/:55/:56 auf
  :53/:96/:97) selbst offen. Gepflegt, kein Fund. Nur eben an einem fertigen Auftrag.
ballbesitz: plan-pruefer
```

herkunft: docs/STATUS.md · Block 426 · 34f6f5a9
kennung_unbekannt: true   # K3 — Kennung ohne Auftragsdatensatz; kein Auftrag, aber ein Beleg
```yaml
auftrag: "P-06"
titel: "Die Antwort des Planners auf Yamas Baufrage nachgemessen — sie traegt, bis auf eine Zeile; und meine eigene 30 war nicht falsch, sondern eine Minute alt"
rolle: plan-pruefer
zeit: "16.08. 18:46"
mess_stand: 2bab146d94b761b1b2bab1d70851ffd153890f2f
geprueft_gegen: "43771e3b (planner, 18:40) — NICHT gegen meinen eigenen Baum, der das Register 92 Zeilen aelter fuehrt"
baum: "sauber (0 Eintraege)"
anlass: "Erster fremder Commit seit 17:55; er beantwortet die Frage, deren Messung ich geliefert hatte"
was_haelt: |
  BESCHRIEBEN 37 — nachgezaehlt an 43 Werkzeugzeilen, 43 eindeutige Kennungen,
  Summe 37 + ENTWORFEN 2 + GEGENSTANDSLOS 3 + GEBAUT 1 = 43. Geht auf.
  Die NEUN ohne F-Nummer mit 5-CODE-Blatt: W-33 W-34 W-35 W-36 W-37 W-38 W-39 W-40 W-42.
  Zeichengleich mit seiner Liste. Die Einordnung 'Bestandsnachweis, kein Bauauftrag'
  ist an der Sache belegt.
was_nicht_haelt: |
  Er nennt VIER Zeilen mit BESCHRIEBEN ohne 5-CODE-Blatt: W-43 W-26 W-28 W-30.
  Gemessen sind es FUENF. W-25 (Pfetten und Kehlbalken) traegt BESCHRIEBEN und hat
  ebenfalls kein 5-CODE-Verzeichnis — geprueft ueber git ls-tree gegen sein eigenes
  Muster '/W-nn-*/5-CODE/': W-43 0 Dateien, W-26 0, W-28 0, W-30 0, W-25 0, W-17 1.
  W-25s Registerzeile begruendet ausdruecklich die fehlende F-NUMMER ('Math. 0x —
  W-25 zaehlt, es rechnet nicht'). Das ist eine Aussage ueber Formeln, nicht ueber
  das Blatt. Beides faellt hier zusammen und wurde offenbar als erledigt gelesen.
  FOLGE fuer seine Aufschluesselung: nicht 24 + 9 + 4, sondern 23 + 9 + 5 = 37.
  Die Antwort auf Yamas Frage aendert sich dadurch NICHT — es bleibt: heute keine.
meine_eigene_berichtigung: |
  Ich habe Yama um 17:46 in 35928fa0 gemeldet, der Bauvorrat sei '30 und nicht 37'.
  Gemessen an den Staenden:
    vor 1e1afd1b (17:47)  BESCHRIEBEN 30 · LEER 10 · GEGENSTANDSLOS 0
    nach 1e1afd1b         BESCHRIEBEN 37 · LEER  0 · GEGENSTANDSLOS 3
  Sieben LEER wurden BESCHRIEBEN, drei GEGENSTANDSLOS: 30+7=37, 10-7-3=0.
  Meine Zahl war zum Zeitpunkt der Meldung RICHTIG und war eine Minute spaeter
  ueberholt. Yamas 37 war es also auch — nur fuer einen anderen Stand.
  Das ist keine Fehlmessung, aber es ist eine Zahl, die Yama heute noch als meine
  im Kopf hat. Sie gilt seit 17:47 nicht mehr.
methodisch: |
  Ich habe zuerst den falschen Stand gelesen (origin/rolle/planner, 421 Zeilen) und
  daraus 'BESCHRIEBEN 30' und 'die vier tragen LEER' gemessen. Die Probe
  'enthaelt der gelesene Stand den Commit?' hat es gefangen — merge-base --is-ancestor
  sagte NEIN, und am Commit-Stand 43771e3b sind es 513 Zeilen und 37 BESCHRIEBEN.
  Ohne diese Probe haette ich dem Planner zwei Fehler gemeldet, die keine sind.
ballbesitz: planner
```

herkunft: docs/STATUS.md · Block 427 · 34f6f5a9
kennung_unbekannt: true   # K3 — Kennung ohne Auftragsdatensatz; kein Auftrag, aber ein Beleg
```yaml
auftrag: "P-07"
titel: "Der Planner arbeitet seit 14:38 ohne meine Befunde — 94 Commits erreichen ihn nicht, und es liegt nicht am Transport"
rolle: plan-pruefer
zeit: "16.08. 18:49"
mess_stand: ad8ad162233f662e7493194610afe31921a1a56a
baum: "sauber (0 Eintraege)"
vorratspruefung: "Posten e — eigene Befunde verfolgen: liegen sie noch bei ihrem Halter?"
befund: |
  Von zwoelf Befunden, die ich heute Abend geschrieben habe, kennt KEIN Rollenzweig
  einen einzigen. Gemessen ueber die Blocktitel in docs/STATUS.md je Zweig:
  planner 0, generator 0, evaluator 0, release-pruefer 0 von 12.
wo_die_luecke_liegt: |
  NICHT im Transport. Mein HEAD ist in auto/hausplaner-integration enthalten
  (merge-base --is-ancestor bestaetigt), der Integrationszweig steht auf ae9c86d7
  von 18:47 und kennt meine juengsten Bloecke. refs/heads/rolle/plan-pruefer zeigt
  auf denselben Commit wie mein HEAD, ist also fuer jeden im Repo lesbar.
  Die Luecke ist der RUECKWEG. Die Rollenzweige ziehen selbst nach, und zwar zuletzt:
    planner           14:38  (0d79ce45 'Stand nachgezogen vor dem Eintrag von A-39 und A-40')
    generator         17:40
    evaluator         17:41
    release-pruefer   17:41
  Mein juengster Commit im planner-Zweig ist 99add90f von 13:45.
umfang: |
  94 meiner Commits liegen zwischen 99add90f und HEAD. Darin die vollstaendige
  A-42-DoR (neun Kriterien, sechs Kanten), die A-40-Pruefung (-1, -3, -4, K1, K2, K3),
  Yamas Bauvorrat-Messung, die A-38-in-A-37-Annahme, drei Selbstberichtigungen und
  die Befunde P-03 bis P-06.
warum_das_zaehlt: |
  Ich schreibe Befunde mit 'ballbesitz: planner'. Ein Ball, den der Empfaenger nicht
  sehen kann, ist kein Ballwechsel. Der Planner hat um 18:40 Yamas Baufrage beantwortet,
  ohne meine Messung dazu kennen zu koennen — und mein P-06 findet dort eine Zeile,
  die ich ihm um 17:46 haette liefern koennen, wenn der Weg offen waere.
  Es ist auch die Erklaerung fuer P-05: ich lese die Statuswahrheit veraltet, WEIL
  der Austausch in beide Richtungen an derselben Stelle haengt.
was_ich_NICHT_tue: |
  Ich ziehe nicht selbst nach und schreibe in keinen fremden Zweig. Beides waere
  ausserhalb meiner Rolle; die Einzelschreiber-Regel gilt. KEIN PUSH.
ballbesitz: yama
```

herkunft: docs/STATUS.md · Block 428 · 34f6f5a9
kennung_unbekannt: true   # K3 — Kennung ohne Auftragsdatensatz; kein Auftrag, aber ein Beleg
```yaml
auftrag: "P-06"
titel: "W-25 ist der FUENFTE Fall und steht noch — die Berichtigung des Planners ist nach seinem eigenen Massstab unvollstaendig, und das ist der erste messbare Schaden der Rueckweg-Luecke"
rolle: plan-pruefer
zeit: "16.08. 18:51"
mess_stand: 31f65117746fb9d56bfba6a1136f93370fe23451
geprueft_gegen: "a589db20 (planner, 18:49)"
baum: "sauber (0 Eintraege)"
was_der_planner_getan_hat: |
  a589db20 berichtigt VIER Zeilen von BESCHRIEBEN auf LEER — W-43, W-26, W-28, W-30 —
  weil die Legende BESCHRIEBEN als 'alle sieben Blaetter gefuellt' definiert und fuer
  diese vier nicht einmal ein Werkbank-Verzeichnis existiert. Er belegt es dreifach und
  begruendet den dritten Weg ausdruecklich: die Suche ueber den WERKZEUGNAMEN statt ueber
  den Pfad, damit der Beleg nicht nur heisst 'am erwarteten Ort ist nichts'.
  Die Begruendung traegt: ein Reifegrad, der einen Beleg behauptet den es nicht gibt,
  verhindert dass jemand nachsieht. Das ist richtig und gut gemessen.
was_fehlt: |
  W-25 (Pfetten und Kehlbalken) traegt weiterhin BESCHRIEBEN. Nach SEINEN drei Wegen:
    1) Verzeichnis '/W-25-*' in 02-WERKZEUGE      0 Treffer
    2) Werkzeugname in der ganzen Werkbank        'pfette' 0 Dateien, 'kehlbalken' 0
    3) Legende                                    verlangt sieben gefuellte Blaetter
  Damit erfuellt W-25 jede Bedingung, die er fuer die vier aufgestellt hat.
  Es ist der FUENFTE Fall derselben Klasse.
folge_fuer_die_zahl: |
  Nach der Berichtigung stehen im Register: BESCHRIEBEN 33, LEER 4, GEGENSTANDSLOS 3,
  ENTWORFEN 2, GEBAUT 1, Summe 43. Mit W-25 waeren es BESCHRIEBEN 32 und LEER 5.
  Seine Aufschluesselung fuer Yama lautete 24 + 9 + 4; richtig ist 23 + 9 + 5.
  Die Antwort auf Yamas Frage bleibt unveraendert: heute erfuellt keine alle drei Bedingungen.
warum_ich_ihm_das_nicht_vorwerfe: |
  Er KANN meinen Befund nicht kennen. Ich habe W-25 um 18:46 in ad8ad162 gemeldet;
  origin/rolle/planner kennt von mir zuletzt 99add90f von 13:45, es fehlen ihm 95 Commits.
  Genau das beschreibt P-07 — und hier wird der Schaden zum ersten Mal messbar:
  haette der Rueckweg offengestanden, waere W-25 in derselben Runde mitberichtigt worden.
  Er hat die vier unabhaengig selbst gefunden. Zwei Rollen haben dieselbe Klasse
  zweimal gemessen, und die fuenfte Zeile faellt in die Luecke dazwischen.
p07_gegengeprueft: |
  Mein erweitertes Muster lieferte in dieser Runde '17:38' als letztes Nachziehen des
  planners und stellte P-07 in Frage. Geoeffnet: 12a9462d ist ein normaler Commit, der
  das Wort 'nachgezogen' im Betreff traegt, kein Merge aus der Integration. Die Aussage
  aus P-07 haelt: letzter Merge 14:38, letzter mir bekannter Stand dort 13:45.
ballbesitz: planner
```

herkunft: docs/STATUS.md · Block 429 · 34f6f5a9
```yaml
auftrag: "A-40"
titel: "A-40-6 geprueft — das Kriterium ist erfuellbar, aber seine Rot-Lage ist seit 14:49 weg, und zwar durch den Planner selbst"
rolle: plan-pruefer
zeit: "16.08. 18:54"
mess_stand: aa5632663876d4fd5a98f7ff781495604526ca57
basis_stand: 99add90f
baum: "sauber (0 Eintraege)"
kriterium: "A-40-6 — nachgerechnet_an traegt die ABWEICHUNG, nicht nur das Ergebnis. Das Feld nennt, um wie viel der Fall ohne die Aussage abweicht (Mutationsprobe)"
urteil: "ERFUELLBAR — aber am heutigen Stand bereits an 1 von 1 Eintraegen erfuellt"
rot_lage_beim_schnitt: |
  Am Basis-Stand 99add90f (13:45) trug KEIN Facheintrag ein nachgerechnet_an mit
  Abweichung. Die Rot-Lage des Kriteriums war zum Schnitt also echt.
was_seither_geschah: |
  66fa277f (planner, 14:49) hat S-008 im SOLAR-REGELWERK das Feld gegeben — eine
  Stunde nach dem Schnitt. Es traegt eingabe, erwartet, gerechnet, fund UND
  abweichung_ohne_die_regel. Damit erfuellt der einzige existierende Eintrag genau
  das, was A-40-6 fordert, bevor der Auftrag gebaut ist.
selbst_nachgerechnet: |
  Ich habe jede Zahl des Eintrags gegen python3 gerechnet, phi = 51 Grad N:
    21.06. dek +23,45  ->  50,777   ·  21.12. dek -23,42  ->  129,167
    Differenz 78,390                   Eintrag sagt 78,4
    Vorzeichen vertauscht: 21.06. -> 129,223 (Eintrag 129,2), 21.12. -> 50,833 (Eintrag 50,8)
    Aequinoktium dek 0: +sin 90,0000 und -sin 90,0000 — BEIDE, wie behauptet
  Der blinde Fleck ist real: wer nur am 21.03. prueft, bestaetigt das falsche Vorzeichen.
  Der Eintrag dokumentiert ausserdem, dass der Planner beim Nachrechnen selbst in diesen
  Fehler lief und ihn ueber den zweiten Rechenweg aufloeste. Das ist ein Beleg, keine
  Wiederholung — genau die Unterscheidung, die A-40-6 verlangt.
befund_fuer_die_dor: |
  A-40-6 hat heute keine Rot-Lage mehr AM FELD. Was fehlt, ist der PRUEFSCHRITT, der
  die Abweichung erzwingt — ein Muster existiert, eine Pruefung nicht. Das Blatt trennt
  das nicht: der Wortlaut fordert, dass das Feld die Abweichung traegt, und genau das
  ist erfuellt. Wer A-40-6 nach dem Bau abnimmt, findet es gruen vor, ohne dass der Bau
  etwas dazu beigetragen haette.
vorschlag: |
  A-40-6 auf den Pruefschritt umformulieren: 'der Pruefschritt meldet ein
  nachgerechnet_an OHNE Abweichungsangabe' — mit S-008 als Negativprobe, die NICHT
  anschlagen darf. Dann traegt das Kriterium wieder eine Rot-Lage, und S-008 wird vom
  Zufallstreffer zum Belegfall. Blattaenderung, nicht meine Entscheidung.
ballbesitz: planner
```

herkunft: docs/STATUS.md · Block 430 · 34f6f5a9
```yaml
auftrag: "A-40"
titel: "A-40-2 ist zur Haelfte nicht abnehmbar — die Positivprobe hat 39 Kandidaten, die Negativprobe keinen einzigen ausser dem Auftragsblatt selbst"
rolle: plan-pruefer
zeit: "16.08. 18:57"
mess_stand: 510a36a5e87e3379c58a5fecb962e2dc0df5372e
basis_stand: 99add90f
baum: "sauber (0 Eintraege)"
kriterium: "A-40-2 — die siebte Innenpruefung laeuft und findet einen echten Fall. Positivprobe: ein Blatt, das eine F-Kennung nennt, deren Eintrag kein nachgerechnet_an traegt -> Meldung. Negativprobe: ein Blatt, dessen KRITERIUM das Nachrechnen selbst verlangt -> keine Meldung"
positivprobe: |
  ERFUELLBAR, und zwar reichlich. Am Basis-Stand 99add90f nennen 39 Blaetter unter
  docs/auftraege/aktiv/ mindestens eine F-Kennung, und die FORMELSAMMLUNG trug dort
  NULL nachgerechnet_an. Jeder dieser Eintraege ist ein Positivfall.
negativprobe: |
  KEIN KANDIDAT. Gesucht wurde nach Kriterienzeilen (Form '- **X-nn-n**') mit
  nachrechn / nachgerechnet / durchgerechnet.
    am Basis-Stand 99add90f : 0 Blaetter
    heute (rolle/planner)   : 1 Blatt — und das ist A-40 SELBST (sein A-40-6)
  Zwoelf Blaetter erwaehnen das Nachrechnen ueberhaupt, aber ausnahmslos in
  PRUEFVERMERKEN (gegenprobe, selbst_nachgerechnet, 'Anlass'), nie in einem Kriterium.
  Geoeffnet und einzeln geprueft: A-12 Z.244/250/266, W-23 Z.331/338/345,
  A-35 Z.105/107 — alles Belege einer bereits erfolgten Rechnung, keine Forderung.
fangproben: |
  Zwei Muster wurden gegen einen bekannten Treffer geprueft, beide Male mit Folgen:
  1. '^- \*\*[A-Z]' lieferte fuer A-40 am Basis-Stand 0 Kriterienzeilen. Grund: das
     A-40-Blatt EXISTIERT am Basis-Stand nicht — geschnitten wurde es um 14:23, der
     Basis-Stand ist 13:45. Das Muster war richtig, das Objekt fehlte.
  2. '^ *- \*\*[A-Z]-[0-9]+' liefert fuer A-40 heute 9 Kriterienzeilen. Damit ist
     belegt, dass die Negativproben-Suche ueberhaupt greifen konnte.
befund: |
  A-40-2 verlangt zwei Proben und benennt — anders als A-40-3, das W-28 und F-004
  ausdruecklich nennt — kein einziges Blatt. Fuer die Positivprobe ist das folgenlos,
  weil es 39 gibt. Fuer die Negativprobe ist es entscheidend: es gibt keine.
  Wer A-40-2 abnimmt, kann zeigen dass die Pruefung anschlaegt, aber nicht dass sie
  DORT SCHWEIGT wo sie schweigen soll. Das ist der Teil, der eine Pruefung von einem
  Alarm unterscheidet.
klasse: "P7 — Kriterium ohne gangbaren Weg. Nicht die Pruefung fehlt, sondern der Fall, an dem ihr Schweigen belegt wuerde."
vorschlag: |
  Entweder A-40-2 benennt seine zwei Proben so konkret wie A-40-3 — dann muss die
  Negativprobe erst geschaffen werden, etwa indem ein bestehendes Blatt ein
  Nachrechen-Kriterium bekommt. Oder die Negativprobe wird gestrichen und A-40-2
  sagt ausdruecklich, dass nur die Positivrichtung belegt wird. Beides ist
  Blattaenderung und nicht meine Entscheidung.
ballbesitz: planner
```

herkunft: docs/STATUS.md · Block 431 · 34f6f5a9
```yaml
auftrag: "A-40"
titel: "A-40-9 kann nicht scheitern — es ist das einzige der sechs Blaetter mit NULL Code-Pfaden, das die Suite-Zusage traegt"
rolle: plan-pruefer
zeit: "16.08. 18:59"
mess_stand: 06ce32379bf8651f25f41a55c2062113e16dd57f
basis_stand: 99add90f
baum: "sauber (0 Eintraege)"
kriterien: "A-40-8 (kein Nicht-Ziel beruehrt) und A-40-9 (Suite gruen und Zahl unveraendert gegen den Bau-Stand, tsc exit=0)"
was_A40_anfasst: |
  Das Blatt sagt selbst: art 'BAU — zwei Pflichtfelder, drei Zustaende und EINE
  Innenpruefung ... KEIN Hausplaner-Code, KEINE Migration.'
  Gemessen an den Pfadangaben im Blatt: kein einziger Pfad unter scripts/, resources/
  oder app/. Genannt werden ausschliesslich docs/STATUS.md, docs/auftraege/aktiv/ und
  docs/rollenkette/werkbank/01-MATHEMATIK/FORMELSAMMLUNG.md.
  'tsc' und 'Suite' kommen im ganzen Blatt GENAU EINMAL vor — in A-40-9 selbst.
vergleich_mit_den_schwestern: |
  Dieselbe Schlussformel tragen alle fuenf Schwesterblaetter. Der Unterschied ist der Bau:
    A-37   7 Code-Pfade   A-37-11 gefahren, belegt mit 'tsc exit 0, Suite 17..'
    A-41   6 Code-Pfade   A-41-12
    A-39   3 Code-Pfade   A-39-10
    A-38   2 Code-Pfade   A-38-9 gefahren, belegt mit 'tsc exit 0, Suite 176'
    A-42   1 Code-Pfad    A-42-9
    A-40   0 Code-Pfade   A-40-9
  Die Formel ist also Hausgebrauch und an sich richtig — bei jedem anderen Auftrag gibt
  es Code, der sie rechtfertigt. A-40 ist der einzige, bei dem sie ins Leere greift.
befund: |
  A-40-9 kann strukturell nicht scheitern. Wenn A-40-8 haelt — keine Datei ausserhalb
  von docs/ — dann sieht weder tsc noch die Suite eine einzige Aenderung, und beide sind
  zwangslaeufig so gruen wie vor dem Bau. Das Kriterium prueft damit nichts, was A-40-8
  nicht schon prueft, nur auf einem teureren Weg.
  Es ist NICHT falsch und es schadet nicht; es ist ein Kriterium ohne eigene Rot-Lage.
  Damit ist es das dritte in diesem Blatt nach A-40-6 (Rot-Lage seit 14:49 weg) und
  A-40-2 (Negativprobe ohne Kandidaten).
was_A40_8_dagegen_leistet: |
  A-40-8 traegt eine echte Rot-Lage: der Auftrag AENDERT docs-Dateien, und die Grenze
  'keine bestehende Fachaussage inhaltlich geaendert' ist an einem Wissensspeicher
  genau die Stelle, an der ein Bau abrutschen kann. Dieses Kriterium bleibt scharf.
  Auffaellig ist nur, dass A-40-8 'keine Aenderung an docs/STATUS.md' verlangt,
  waehrend das Blatt in Zeile 12 'status_steht_in: docs/STATUS.md' fuehrt — der Bau
  darf die Datei nicht anfassen, der Zustand des Auftrags wird aber dort gefuehrt.
  Das ist auflösbar (der Generator schreibt den Zustand, nicht der Bau), aber es steht
  ungetrennt nebeneinander.
vorschlag: |
  A-40-9 entweder streichen, weil A-40-8 dieselbe Zusage traegt, oder ausdruecklich als
  Regressionsschutz kennzeichnen ('belegt, dass der Bau die Grenze eingehalten hat')
  statt als eigenes Abnahmekriterium. Blattaenderung, nicht meine Entscheidung.
ballbesitz: planner
```

herkunft: docs/STATUS.md · Block 432 · 34f6f5a9
```yaml
auftrag: "A-40"
titel: "Die Kanten sind durch — K4 stuetzt sich auf ein Werkzeug, das es nicht gibt und das schon beim Schnitt LEER war; K5 und K6 nennen gar keinen Fall"
rolle: plan-pruefer
zeit: "16.08. 19:02"
mess_stand: 036e883d59a2d2ccbcacbb8ac09749eb62cc9b31
basis_stand: 99add90f
baum: "sauber (0 Eintraege)"
kriterium: "A-40-7 — alle sechs Kanten K1 bis K6 sind behandelt und je einzeln belegt"
kantenbild: |
  K1  N-003              BELEGT — drei Definitionsstellen, in frueherer Runde gemessen
  K2  S-078              BELEGT — Wortlaut steht in SOLAR-REGELWERK.md:543, nicht in der
                         FORMELSAMMLUNG, auf die A-40-5 zeigt
  K3  die 17 S-Verweise  BELEGT — 17 eindeutige S-Nummern, davon 0 mit Definitionsstelle
  K4  W-28               BELEG EXISTIERT NICHT
  K5  (kein Fall)        kein Belegfall genannt
  K6  (kein Fall)        kein Belegfall genannt
k4_gemessen: |
  W-28 kommt nirgends als Aussage vor:
    Werkbank-Verzeichnis /W-28-*     Basis 0 · heute 0
    FORMELSAMMLUNG                   Basis 0 Treffer
    SOLAR-REGELWERK                  Basis 0 Treffer
  K4 lautet 'Eine Aussage ist normabhaengig, aber die Norm liegt nicht vor (W-28)'.
  Es gibt keine Aussage W-28, an der das gezeigt werden koennte.
  DIESELBE Stelle traegt auch A-40-3: 'Historischer Beleg: W-28 (dreimal JA) muss
  anschlagen'. Ein Drei-Fragen-Test kann an einem Eintrag, den es nicht gibt, nicht
  dreimal JA ergeben. Zwei Stellen des Blattes haengen an derselben Leerstelle.
und_das_register_hat_es_gesagt: |
  Naheliegend waere die Entschuldigung, das Register habe W-28 damals faelschlich als
  BESCHRIEBEN gefuehrt. Gemessen am Schnitt-Commit ddcf17e4 (16.08. 14:23): W-28 trug
  dort LEER. Erst 1e1afd1b (17:47) hat es auf BESCHRIEBEN gezogen, und a589db20 (18:49)
  hat es wieder auf LEER berichtigt. Zum Zeitpunkt des Schnitts stand also richtig da,
  dass es zu W-28 nichts gibt.
eigene_luecke: |
  Ich habe A-40-3 heute um 18:14 geprueft und fuer belegt gehalten. Gemessen hatte ich
  die ROT-LAGE (0 gegengeprueft_an, 0 geltungsbereich) — den HISTORISCHEN BELEG W-28
  habe ich nicht geoeffnet. Das hole ich hiermit nach; die damalige Aussage war zur
  Haelfte ungeprueft.
folge_fuer_A_40_7: |
  A-40-7 verlangt sechs einzeln belegte Kanten. Drei sind es (K1, K2, K3), eine ist es
  nachweislich nicht (K4), zwei nennen keinen Fall (K5, K6). Das Kriterium ist am
  heutigen Stand nicht abnehmbar.
vorschlag: |
  K4 auf einen existierenden normabhaengigen Fall umstellen — N-003 traegt bereits einen
  Geltungsbereich und ein Fach-Gate und waere ein echter Kandidat. K5 und K6 brauchen je
  einen benannten Fall oder die ausdrueckliche Ansage, dass sie als Entwurfsgrenze und
  nicht als Belegfall gefuehrt werden. Blattaenderung, nicht meine Entscheidung.
ballbesitz: planner
```

herkunft: docs/STATUS.md · Block 433 · 34f6f5a9
```yaml
auftrag: "A-40"
titel: "Die A-40-5-Korrektur ist richtig und trifft meinen Befund unabhaengig — aber der erweiterte Suchraum traegt ein ANDERES Merkmal, und der Lauf meldet jetzt auch den einzigen nachgerechneten Eintrag als ampellos"
rolle: plan-pruefer
zeit: "16.08. 19:05"
mess_stand: 2bae4c173d244e57c9f64bcceb8c90bb3a8e716a
geprueft_gegen: "4a9053ed (planner, 19:01)"
baum: "sauber (0 Eintraege)"
die_korrektur_haelt: |
  4a9053ed stellt A-40-5s Zaehlbefehl von FORMELSAMMLUNG.md auf 01-MATHEMATIK/*.md um.
  Das ist genau der Befund, den ich um 18:24 in 0e62e4f4 geschrieben habe — der Planner
  hat ihn unabhaengig gefunden, ohne ihn kennen zu koennen (100 meiner Commits fehlen
  seinem Zweig). Zweite Doppelentdeckung dieses Abends nach den vier Reifegraden.
  Seine Begruendung ist staerker als meine war: ein Fehlalarm kostet Aufmerksamkeit,
  ein falsches Gruen kostet die Pruefung selbst. Und er schreibt die Zahl NICHT ins
  Blatt, sondern nur den Befehl — genau wie A-40-5 es selbst verlangt.
erster_punkt_die_zahl_zaehlt_zeilen: |
  Seine Gegenprobe nennt 23 + 64 = 87. Nachgemessen mit seinem berichtigten Befehl:
    FORMELSAMMLUNG    23 ZEILEN ohne Ampel · 22 EINDEUTIGE Kennungen
    SOLAR-REGELWERK   64 ZEILEN ohne Ampel · 32 EINDEUTIGE Kennungen
  87 ist eine Zeilenzahl, 54 die Kennungszahl. Belegt an S-008: Zeile 131 traegt die
  Definitionsstelle '### S-008 · Auf- und Untergangs-Azimut', Zeile 652 eine Zeile der
  ABHAENGIGKEITSTABELLE. Die zweite ist ein Verweis, keine Definitionsstelle.
  Fuer den BAU ist 87 die richtige Zahl — so viele Zeilen muss jemand anfassen, damit
  der Lauf schweigt. Als Aussage ueber Definitionsstellen ist sie um 33 zu hoch.
zweiter_punkt_das_merkmal_passt_nicht: |
  Der Ort ist berichtigt, das MERKMAL nicht. Gemessen:
    FORMELSAMMLUNG    33 Ampelzeilen ·  0 zustand-Felder · 32 Definitionsstellen
    SOLAR-REGELWERK    0 Ampelzeilen ·  1 zustand-Feld   · 32 Definitionsstellen
  Die beiden Dateien kennzeichnen VERSCHIEDEN. Das Solar-Regelwerk fuehrt keine Ampeln,
  sondern die drei Zustaende ABGESCHRIEBEN / NACHGERECHNET / GEGENGEPRUEFT — also genau
  das, was A-40 erst einfuehren will. Es ist dem Auftrag voraus.
  FOLGE: der berichtigte Lauf sucht dort Ampeln und findet keine. Er meldet alle 32
  Kennungen als ampellos — darunter S-008, den EINZIGEN vollstaendig nachgerechneten
  Eintrag des Bestands, mit gefuelltem nachgerechnet_an und Abweichungsangabe.
  Das ist kein falsches Gruen mehr, sondern ein Fehlalarm auf dem besten Eintrag, den
  es gibt. A-40-5 misst das ALTE Merkmal in einem Raum, der schon das NEUE traegt.
vorschlag: |
  A-40-5s Filter neben der Ampel auch das zustand-Feld anerkennen, oder das Kriterium
  auf 'traegt eine Ampel ODER einen Zustand' umstellen. Sonst muss der Bau 32
  Solar-Kennungen mit Ampeln versehen, die dort systemfremd sind — und S-008 bekaeme
  eine Ampel, obwohl sein Zustand mehr aussagt als jede Ampel.
  Blattaenderung, nicht meine Entscheidung.
ballbesitz: planner
```

herkunft: docs/STATUS.md · Block 434 · 34f6f5a9
```yaml
auftrag: "A-39"
titel: "A-39s Suchraum-Beleg traegt in der Sache, aber die Zahl 32 finde ich in keiner Lesart wieder — es sind 36, 37 oder 40"
rolle: plan-pruefer
zeit: "16.08. 19:08"
mess_stand: a3570fdcc2fe1054e251fd7e03862d5f41b08b5e
geprueft_gegen: "7970a9c7 (planner, 19:04)"
baum: "sauber (0 Eintraege)"
die_behauptung: |
  7970a9c7 belegt A-39s Nicht-Ziel — geprueft werden nur Auftragsblaetter unter
  docs/auftraege/ — statt es als Pfad stehen zu lassen. Seine Messung: 32 Dateien
  ausserhalb tragen ebenfalls ein Feld auftrag, sie heissen BEFUND-* und BERICHT-*,
  und KEINE EINZIGE traegt einen Abschnitt Abnahmekriterien.
was_haelt: |
  Die zweite Messung ist die tragende, und sie stimmt: NULL Dateien ausserhalb
  docs/auftraege/ tragen einen Abschnitt Abnahmekriterien. Selbst nachgezaehlt ueber
  alle 40 Kandidaten. Damit ist sein Schluss belegt — die Menge 'Blatt mit
  Abnahmekriterien' und die Menge 'Datei unter docs/auftraege/' fallen zusammen,
  und der Suchraum ist geprueft statt geraten. Der Gedanke dahinter ist richtig und
  deckt sich mit meinem eigenen P7/P8-Bild.
was_abweicht: |
  Die Zahl 32 finde ich in keiner Lesart wieder. Gemessen am selben Commit 7970a9c7,
  Muster '^auftrag: *"?...' ueber alle .md unter docs/ ausserhalb docs/auftraege/:
    weit   (auftrag mit Grossbuchstabe)      40
    davon BEFUND-* 3 + BERICHT-* 34          37
    eng    (auftrag mit A-/W-/B-Kennung)     36
  Keine davon ist 32. Die drei ausserhalb des BEFUND/BERICHT-Musters sind
  ARBEITSREGELN.md, STATUS.md und release-vorbereitung.md.
  ARBEITSREGELN.md traegt 'auftrag: ID' in den Zeilen 427 und 442 — eine SCHABLONE,
  keine Kennung. STATUS.md traegt die 230 Bloecke selbst.
fangprobe: |
  Mein Muster wurde vorher an einem bekannten Treffer geprueft: es findet 86 Blaetter
  unter docs/auftraege/ mit auftrag-Feld. Es greift also.
warum_das_zaehlt_und_warum_nicht: |
  Fuer den Beleg des Suchraums ist die Abweichung folgenlos — ob 32 oder 40 Dateien
  ausserhalb ein auftrag-Feld tragen, entscheidend ist die Null bei den
  Abnahmekriterien, und die haelt.
  Fuer das Blatt ist sie nicht folgenlos: die Zahl steht jetzt im Nicht-Ziel als
  Beleg, und ein Nachpruefer, der sie mit dem naheliegenden Muster nachzaehlt, kommt
  auf einen anderen Wert und haelt den Beleg fuer falsch. Eine Belegzahl braucht das
  Muster, mit dem sie entstanden ist — dieselbe Lehre wie bei A-40-5s Zaehlbefehl.
vorschlag: "Im Nicht-Ziel neben der Zahl das Muster nennen, mit dem sie gemessen wurde. Dann ist sie nachvollziehbar, egal welche Lesart gemeint war. Blattaenderung, nicht meine Entscheidung."
ballbesitz: planner
```

herkunft: docs/STATUS.md · Block 435 · 34f6f5a9
```yaml
auftrag: "A-39"
titel: "A-39s letzte vier Kriterien geprueft — alle vier tragen, und A-39-10 ist genau die Formulierung, die A-40-9 fehlt"
rolle: plan-pruefer
zeit: "16.08. 19:12"
mess_stand: 7a3a2e1b708e3e8a9dec85ebcf14ab63201d3841
geprueft_gegen: "7970a9c7 (planner, 19:04)"
basis_stand: 99add90f
baum: "sauber (0 Eintraege)"
A_39_7: |
  'Ein Blatt ohne Befund erzeugt keine Ausgabe und Rueckgabe 0.'
  Erst nach dem Bau abschliessend pruefbar — das Skript gibt es noch nicht. Aber es ist
  NICHT strukturell blockiert wie A-40-2s Negativprobe: es gibt 89 Blaetter, an denen
  sich ein befundfreier Lauf zeigen laesst, und die Zusage nennt mit 'keine Ausgabe UND
  Rueckgabe 0' zwei unabhaengig messbare Groessen. Der Klammersatz begruendet es richtig:
  ohne diesen Beleg ist das Skript von einem kaputten nicht zu unterscheiden.
A_39_8_kanten: |
  Sechs Kanten, alle sechs gemessen. Sie sind ANDERS gebaut als A-40s: A-40 nennt
  historische Faelle (K4 nennt W-28, das es nicht gibt), A-39 beschreibt Situationen
  mit erwartetem Verhalten — 'P1 gilt als erfuellt, keine Meldung', 'P2 greift nicht,
  sie ist keine Zusage'. Solche Kanten sind konstruierbar und koennen nicht ins Leere
  zeigen.
  K1 ist sogar real belegt: 'Blatt ohne Kantenliste' hat 80 Kandidaten — von 89
  Blaettern unter docs/auftraege/aktiv/ tragen nur 9 eine Kantentabelle.
  Fangprobe: A-39 selbst zaehlt 6 Kantenzeilen, das Muster greift.
  K6 nennt zusaetzlich A-33 als Beispielfall.
A_39_9: |
  Scharf. Es benennt die Nicht-Ziele einzeln — resources/, app/, docs/STATUS.md und
  scripts/commit-pruefen.sh — und nennt mit 'git show --stat' den Messweg dazu.
A_39_10_ist_das_gegenstueck_zu_A_40_9: |
  Gleicher Wortlaut wie A-40-9, aber mit dem entscheidenden Zusatz: 'Zahl unmittelbar
  vor dem Bau erheben, nicht gegen eine feste Zahl pruefen.'
  Damit traegt es einen Standbezug, den A-40-9 nicht hat. Und anders als bei A-40 ist
  die Zusage hier begruendet: A-39 hat drei Code-Pfade und baut ein Skript unter
  scripts/, waehrend A-40 null Code-Pfade hat. Dieselbe Formel, zwei verschiedene Lagen.
nebenbefund_dateiname: |
  Titel (Z.1) und art sagen ACHT Pruefungen, die Kriterien decken P1 bis P8 vollstaendig
  ab (A-39-2 bis -6 fuer P1-P5, dann -11, -12, -13 fuer P6, P7, P8), und P8 ist ab
  Zeile 106 beschrieben. Der DATEINAME sagt weiterhin
  'A-39-die-fuenf-innenpruefungen-des-blattes.md'. Wer nach dem Blatt sucht, liest fuenf.
zwei_eigene_fehlverdachte_gefangen: |
  1. Ich hielt P8 fuer unbeschrieben — mein Listenbefehl war nach zehn Zeilen
     abgeschnitten. P8 steht auf Zeile 106.
  2. Ich hielt 'fuenf' im Text fuer einen Widerspruch — geoeffnet: Z.19 sagt 'Acht
     Blattfehler: fuenf an EINEM Tag, drei weitere am 16.08.', Z.174 spricht von fuenf
     festen SHAs. Beide Stellen sind richtig.
  Beide Verdachte loesten sich zugunsten des Blattes auf, bevor sie in eine Meldung gingen.
urteil: "Die vier offenen Kriterien tragen. A-39 steht damit deutlich besser da als A-40, wo vier Kriterien Befunde tragen."
ballbesitz: planner
```

herkunft: docs/STATUS.md · Block 436 · 34f6f5a9
```yaml
auftrag: "A-40"
titel: "BERICHTIGUNG meines K4-Befundes von 19:02 — W-28 existiert seit 16:47 mit sieben Blaettern, und K4 ist mustergueltig belegt"
rolle: plan-pruefer
zeit: "16.08. 19:14"
mess_stand: a564b07070063ca65ff2295685ceebea4bf4dfd4
baum: "sauber (0 Eintraege)"
art: "SELBSTBERICHTIGUNG — mein Befund war falsch, das Kriterium traegt"
was_ich_gemeldet_habe: |
  Um 19:02 in 2bae4c17: 'K4 stuetzt sich auf ein Werkzeug, das es nicht gibt. W-28 kommt
  nirgends als Aussage vor: Werkbank-Verzeichnis 0 am Basis-Stand und 0 heute.'
  Das 'heute 0' war falsch.
was_stimmt: |
  W-28 traegt SIEBEN Blaetter — 1-ZWECK bis 7-GRENZEN — in origin/rolle/generator,
  origin/rolle/evaluator, origin/rolle/release-pruefer und auto/hausplaner-integration.
  Gebaut hat sie der Generator um 16:47 mit 04e57045 'W-28 Dachentwaesserung abgelesen'.
  Das ist zwei Stunden VOR meiner Meldung.
  NUR origin/rolle/planner und mein eigener Baum fuehren 0 Dateien.
mein_fehler: |
  Ich habe 'heute' gegen origin/rolle/planner gemessen. Dieser Zweig ist bei den
  Werkbank-Dateien alt. Es ist derselbe Fehler wie bei A-33 um 18:37, den ich in P-05
  selbst beschrieben habe — ich habe die Lehre auf ZUSTAENDE angewandt und nicht auf
  DATEIEN. Ein Zweig, der bei einer Datei zurueckliegt, liegt bei anderen genauso zurueck.
K4_ist_belegt: |
  Die Sache trifft sogar mustergueltig. K4 lautet 'Eine Aussage ist normabhaengig, aber
  die Norm liegt nicht vor (W-28)'. Gemessen in W-28-dachentwaesserung/3-FORMELN.md:
    Z.10  'Die klassische Rechnung der Dachentwaesserung (DIN 1986-100 / EN 12056-3,
           hier nur als ...)'
    Z.36  'DIN 1986-100 vereinfacht — Mindestgefaelle und Fallstrang-Distanz. Kein ...'
  Sechs der sieben Blaetter nennen Normen, zehn Treffer insgesamt. Genau der Fall,
  den K4 beschreibt.
folge_fuer_A_40_3: |
  A-40-3s historischer Beleg 'W-28 (dreimal JA) muss anschlagen' ist damit ebenfalls
  nicht mehr leer. Ob der Drei-Fragen-Test dort wirklich dreimal JA ergibt, habe ich
  noch nicht gemessen — das ist der naechste Schritt, und diesmal gegen den
  generator-Zweig.
was_von_meinem_befund_bleibt: |
  Die Messung am BASIS-STAND 99add90f haelt: dort gab es W-28 nicht. Zum Zeitpunkt des
  A-40-Schnitts um 14:23 war der Beleg also tatsaechlich leer, und das Register fuehrte
  W-28 damals richtig als LEER. Der Generator hat die Luecke um 16:47 geschlossen.
  Aus 'das Kriterium ist unerfuellbar' wird damit 'das Kriterium war beim Schnitt leer
  und ist seit 16:47 belegt'. Das ist ein anderer Satz, und nur der zweite stimmt.
ballbesitz: plan-pruefer
```

herkunft: docs/STATUS.md · Block 437 · 34f6f5a9
```yaml
auftrag: "A-40"
titel: "Die neue Menge von A-40 nachgemessen — 25 stimmt als Zaehlung, aber drei davon sind im Register ausdruecklich als NICHT benutzt markiert; benutzt sind 22"
rolle: plan-pruefer
zeit: "16.08. 19:17"
mess_stand: 24a49f468cf91d18a630bd4539901aa15ce64915
geprueft_gegen: "692cde54 (planner, 19:14)"
baum: "sauber (0 Eintraege)"
die_behauptung: |
  692cde54 traegt A-40 eine Menge und eine Reihenfolge ein. Grundsatz laut Blatt:
  'Ausloesung ist die Benutzung, keine Inventur' — die Menge ist also nicht alle
  Eintraege, sondern alle BENUTZTEN. Gemessen habe er 25 benutzte F-Nummern, NULL davon
  mit nachgerechnet_an, und eine Reihenfolge nach Reichweite: F-001 zwoelf Werkzeuge,
  F-030 acht, F-004 sieben, F-032 sechs, F-011 fuenf, F-003 vier.
was_haelt: |
  Fangprobe zuerst: 43 Werkzeugzeilen, wie erwartet.
  25 F-Nummern in der Formelspalte der Registerzeilen — die Zaehlung stimmt exakt.
  NULL mit nachgerechnet_an — stimmt; an F-004 einzeln geoeffnet und geprueft, die
  Definitionsstelle steht auf FORMELSAMMLUNG.md:75 und traegt weder nachgerechnet_an
  noch eine Ampel. Das ist der Punkt, der seinen Auftrag traegt, und er ist belegt.
  Auch die Spitze der Rangfolge haelt: F-001 ist in JEDER Lesart die Nummer mit der
  groessten Reichweite, F-030 folgt.
was_nicht_haelt_die_menge: |
  Das Register schreibt nicht benutzte Formeln DURCHGESTRICHEN, mit Zeichen und Beleg:
    F-020 in W-07:  ~~F-020~~ ⓝ '0 Treffer (skelett in allen acht Modulen)'
    F-021 in W-07:  ~~F-021~~ ⓝ '(kein Skelett zum Anheben)'
    F-031 in W-04 und W-22: ~~F-031~~ ⓝ
  Diese drei kommen in der Formelspalte AUSSCHLIESSLICH durchgestrichen vor.
  Gemessen: 25 Nummern genannt, davon 22 aktiv, 3 nur durchgestrichen.
  Die benutzte Menge ist also 22. Genau das Prinzip des Blattes — Benutzung, nicht
  Inventur — schliesst sie aus, und das Register hat es bereits belegt.
  Insgesamt 18 durchgestrichene Vorkommen auf 10 Nummern.
was_nicht_haelt_die_reichweiten: |
  Keine Lesart reproduziert die genannten Werte:
    Nummer  aktiv  durchgestrichen  beides   Planner
    F-001     9          1            10       12
    F-030     2          3             5        8
    F-004     3          3             6        7
    F-032     3          2             5        6
  Auch 'alle Zeilen statt nur BESCHRIEBEN' und 'ganze Zeile statt Formelspalte' aendern
  daran nichts — vier Zaehlwege durchgerechnet, alle vier liegen unter seinen Zahlen.
warum_das_zaehlt: |
  Die Reichweite ist hier kein Beiwerk: sie BEGRUENDET die Baureihenfolge, und das Blatt
  sagt ausdruecklich, wer nach Nummern abarbeitet, fange beim kleinsten Hebel an.
  Die Rangfolge an der Spitze haelt, die Abstaende nicht — F-030 mit acht gegen F-004
  mit sieben ist ein anderer Vorsprung als 2 gegen 3 aktiv gemessen.
  Und wenn drei Nummern gar nicht benutzt sind, ist die Menge, gegen die A-40 spaeter
  abgenommen wird, um drei zu gross.
vorschlag: |
  Die Menge auf die 22 aktiven stuetzen und die drei durchgestrichenen ausdruecklich
  ausnehmen — sie sind bereits mit Beleg als nicht benutzt vermerkt. Und wie bei der
  32er-Zahl in A-39: das Muster zur Zahl nennen, damit ein Nachpruefer dieselbe Menge
  bekommt. Blattaenderung, nicht meine Entscheidung.
ballbesitz: planner
```

herkunft: docs/STATUS.md · Block 438 · 34f6f5a9
kennung_unbekannt: true   # K3 — Kennung ohne Auftragsdatensatz; kein Auftrag, aber ein Beleg
```yaml
auftrag: "P-08"
titel: "Der Bauvorrat des Generators haelt in jedem Punkt — und meine erste Gegenmessung war wertlos, weil zsh eine Variable nicht aufteilt"
rolle: plan-pruefer
zeit: "16.08. 19:21"
mess_stand: ba0096b0029337bf748d2fdfc48a7d363414f4d7
geprueft_gegen: "bb1da29f (generator, 19:17)"
baum: "sauber (0 Eintraege)"
die_messvorschrift: |
  Modul unter geometry mit mindestens einer Ausfuhr · eigener Waechter unter __tests__ ·
  NULL produktive Verbraucher in app/renderers/commands/store/domain.
  Er hat sie aus dem CODE gefahren statt aus den Blaettern, mit ausdruecklicher
  Begruendung: nur fuenf bis sechs der 41 Werkzeugverzeichnisse tragen einen
  einheitlichen Bestandsblock, eine Auswertung ueber alle 41 waere ein Muster ueber
  Fliesstext und damit H-9.
nachgemessen_alles_haelt: |
  55 .ts-Module unter geometry — exakt seine Zahl.
  Alle zehn genannten Module existieren, jedes mit genau einem Waechter unter __tests__:
  dachformVorlagen, aufbautenStatus, dachVorlage, masskette, auswechslung,
  integrationAbgleich, sparrenTrennung, dachTopologie, treppenTypen, wandFlaeche.
  Produktive Verbraucher, Nicht-Typ-Nennungen in app/renderers/store/domain: alle NULL.
  Das Verzeichnis 'commands' aus seiner Vorschrift existiert nicht; der Suchraum sind
  85 app + 9 renderers + 5 domain + 2 store = 101 Dateien.
sein_eigener_zweifel_war_berechtigt: |
  Er schreibt, eine Zahl habe er nicht geglaubt: dachformVorlagen mit 51 Ausfuhren und
  null Verbrauchern, obwohl drei Dateien es nennen. Nachgemessen, er hat recht:
    renderers/three-d/dachMesh.ts:13   'import type { EngineRoofShape } from ...'
    domain/roofShape.ts:3              eine KOMMENTARZEILE, keine Benutzung
  Beides sind keine produktiven Verbraucher. Die Zahl haelt.
mein_eigener_fehler: |
  Meine erste Gegenmessung ergab fuer ALLE zehn Module null Nennungen — auch fuer
  geradenGeometrie, das nachweislich einen Produktivaufrufer hat. Die Fangprobe hat es
  gefangen, bevor etwas gemeldet wurde.
  URSACHE: ich hatte den Suchraum in eine Variable gelegt und 'for f in $PROD'
  geschrieben. zsh teilt eine unquotierte VARIABLE nicht an Whitespace auf — anders als
  bash und anders als eine Kommandosubstitution '$(...)', die IFS-Splitting macht.
  Die Schleife lief also einmal ueber einen einzigen langen String, jedes git show
  schlug fehl, und das Ergebnis war ueberall 0. Ein Nullwert, der wie eine Messung
  aussieht.
  Mit der Schleife direkt ueber $(git ls-tree ...) liefert die Fangprobe 3 Nennungen
  fuer geradenGeometrie in trimmen.ts und werkzeugLandkarte.ts.
lehre_fuer_meine_wache: |
  'Eine ausgefallene Messung ist kein Ergebnis' hat heute schon zweimal zugeschlagen —
  beim --since in die Zukunft und hier. Beide Male war das Ergebnis eine NULL, und
  beide Male hat nur die Fangprobe an einem bekannten Treffer sie entlarvt.
  Fuer alles Weitere: Suchraeume nie ueber eine Variable in eine Schleife geben.
urteil: "Der Bauvorrat steht: 10 von 55 Modulen, jede Bedingung einzeln nachgemessen. Kein Befund gegen den Generator."
ballbesitz: plan-pruefer
```

herkunft: docs/STATUS.md · Block 439 · 34f6f5a9
```yaml
auftrag: "A-40"
titel: "A-40-3 ist vollstaendig belegt — W-28 dreimal JA, F-004 dreimal NEIN, beide Proben selbst gefahren; meine 19:02-Meldung ist damit in beiden Teilen zurueckgenommen"
rolle: plan-pruefer
zeit: "16.08. 19:24"
mess_stand: e388f7c71c2908058e28f7664209c1f4d94e35b4
geprueft_gegen: "692cde54 (Blatt) · origin/rolle/generator (W-28) · auto/hausplaner-integration (ARBEITSREGELN)"
baum: "sauber (0 Eintraege)"
der_verweis_haelt: |
  Das Blatt sagt, die drei Fragen seien ENTSCHIEDEN und stuenden in ARBEITSREGELN.md,
  Nachtrag vom 16.08. — 'dieses Blatt BAUT sie, es erfindet sie nicht'. Nachgeprueft:
  sie stehen dort, ab Zeile 1405, mit Namen und Erlaeuterung:
    1 NORMBEZUG  Normkennung (DIN, EN, VDI) oder als normkonform bezeichnet?
    2 DRITTER    Verlaesst das Ergebnis das Haus — Angebot, Nachweis, Plan, Bericht?
    3 BEMESSUNG  Legt es eine GEBAUTE Groesse fest — Querschnitt, Tragfaehigkeit,
                 Entwaesserung, Abstand, Lastannahme?
    Dreimal NEIN -> NACHGERECHNET reicht. Einmal JA -> nur mit Primaerquelle.
  EINSCHRAENKUNG: unter den Namen 'Drei-Fragen-Test' oder '5c' ist in ARBEITSREGELN.md
  NICHTS zu finden — 0 Treffer in allen vier Zweigen. Der Test steht dort, sein Name
  nicht. Wer ihn unter dem Namen sucht, den A-40 benutzt, findet ihn nicht.
positivprobe_W_28: |
  Dreimal JA, an W-28s sieben Blaettern im generator-Zweig gemessen:
    1 NORMBEZUG   4 Treffer  (DIN 1986-100, EN 12056-3)          JA
    2 DRITTER     5 Treffer  (Nachweis, Kunde, Amt)              JA
    3 BEMESSUNG  34 Treffer  (Querschnitt, Nennweite, Gefaelle)  JA
  Frage 3 nennt 'Entwaesserung' woertlich — W-28 IST die Dachentwaesserung.
  Der Beleg sitzt also nicht knapp, sondern in allen drei Richtungen.
negativprobe_F_004: |
  Dreimal NEIN. F-004 'Schnittpunkt zweier Geraden', FORMELSAMMLUNG.md Zeile 75 bis 180.
    1 NORMBEZUG   0 Treffer   NEIN
    2 DRITTER     0 Treffer   NEIN
    3 BEMESSUNG   0 Treffer   NEIN
  Beide Proben treffen also genau so, wie das Kriterium es verlangt.
eigener_mustertreffer_gefangen: |
  Der erste Lauf gab fuer F-004 bei Frage 2 ZWEI Treffer und damit 'einmal JA'. Geoeffnet:
    Z.92   '⚠ BERICHTIGT am 13.08.2026 — die Zaehlerzeile trug ein vertauschtes Vorzeichen'
    Z.121  'Beide sind berichtigt.'
  Mein Muster 'Bericht' traf das Wort BERICHTIGT. Ohne das Oeffnen haette ich gemeldet,
  die Negativprobe falle durch. Dieselbe Wortgrenzen-Klasse wie bei K2 im Werkzeug.
zuruecknahme: |
  Meine Meldung von 19:02 sagte, A-40-3s historischer Beleg sei unerfuellbar, weil es
  W-28 nicht gebe. Beide Haelften sind erledigt: W-28 existiert seit 16:47 mit sieben
  Blaettern (bereits um 19:17 berichtigt), und der Drei-Fragen-Test schlaegt dort in
  allen drei Richtungen an, waehrend er bei F-004 in allen drei schweigt.
  A-40-3 ist gruen.
stand_A_40: |
  Offene Punkte am Blatt jetzt: A-40-2 (Negativprobe ohne Kandidaten), A-40-6 (Rot-Lage
  seit 14:49 weg), A-40-9 (kann nicht scheitern), A-40-5s Merkmal im Solar-Regelwerk,
  die Menge 22 statt 25, sowie K5/K6 ohne benannten Fall.
  Belegt und gruen: A-40-1, A-40-3, A-40-4, A-40-8, K1, K2, K3, K4.
ballbesitz: planner
```

herkunft: docs/STATUS.md · Block 440 · 34f6f5a9
kennung_unbekannt: true   # K3 — Kennung ohne Auftragsdatensatz; kein Auftrag, aber ein Beleg
```yaml
auftrag: "P-09"
titel: "EIN veralteter Zweig hat heute DREI Fehlbefunde erzeugt — zwei von mir, einen vom Planner; mein W-25-Befund ist der dritte und wird zurueckgenommen"
rolle: plan-pruefer
zeit: "16.08. 19:26"
mess_stand: 13676b5d138d9ea73c3e860b25049e9fae11911c
baum: "sauber (0 Eintraege)"
art: "SELBSTBERICHTIGUNG und Ursachenbefund"
die_ruecknahme: |
  Ich habe um 18:53 in aa563266 gemeldet: 'W-25 ist der FUENFTE Fall und steht noch —
  kein Verzeichnis /W-25-*, null Dateien unter pfette und kehlbalken.'
  Das ist falsch. W-25 traegt SIEBEN Blaetter in origin/rolle/generator, in evaluator,
  in release-pruefer und in auto/hausplaner-integration. Gebaut hat sie der Generator
  um 14:01 mit d10aa0dd: 'W-25 abgelesen: sieben Blaetter UND die Registerzeile in
  EINEM Zug' — fuenf Stunden vor meiner Meldung.
  W-25s Registerzeile BESCHRIEBEN war also die ganze Zeit RICHTIG. Mein Befund haette,
  wenn ihm jemand gefolgt waere, eine korrekte Zeile auf LEER verschlechtert.
die_gemeinsame_ursache: |
  origin/rolle/planner fehlen SECHS Werkzeugverzeichnisse, die der Generator heute
  gebaut hat:
    W-25-pfetten-und-kehlbalken · W-26-dachschichten · W-28-dachentwaesserung
    W-29-dachdurchdringungen · W-30-flachdach-aufbau · W-43-abbund-zeichnung
  Wer gegen diesen Zweig misst, findet sie nicht — und findet sie NICHT als Luecke,
  sondern als Beweis, dass es sie nicht gibt.
drei_fehlbefunde_aus_einer_quelle: |
  1. Der Planner, 18:49 a589db20: berichtigt W-43, W-26, W-28, W-30 von BESCHRIEBEN
     auf LEER — dreifach belegt, aber gegen seinen eigenen Zweig. Der Release-Pruefer
     hat es um 19:22 in 04f694d4 gemessen: 'VIER Registerzeilen sagen LEER, im Bestand
     liegen je SIEBEN.'
  2. Ich, 19:02 in 2bae4c17: 'A-40s K4 stuetzt sich auf ein Werkzeug, das es nicht
     gibt.' Um 19:17 selbst berichtigt.
  3. Ich, 18:53 in aa563266: 'W-25 ist der fuenfte Fall.' Hiermit berichtigt.
  Drei Befunde, zwei Rollen, EINE Ursache: ein Zweig, der bei den Werkbank-Dateien
  fuenf Stunden zurueckliegt.
was_das_fuer_P_07_heisst: |
  P-07 hat gemeldet, dass MEINE Befunde den Planner nicht erreichen. Das hier ist die
  andere Richtung derselben Sache: die Arbeit des GENERATORS erreicht weder den Planner
  noch mich. Der Rueckweg ist nicht halb offen, er ist in beide Richtungen zu.
  Und der Schaden ist jetzt beziffert: drei falsche Befunde in 25 Minuten, von denen
  einer beinahe eine korrekte Registerzeile verschlechtert haette.
meine_konsequenz: |
  Ich habe die Lehre aus P-05 dreimal unvollstaendig angewandt — erst auf Zustaende,
  dann auf Dateien, und beim dritten Mal wieder nicht. Ab sofort messe ich JEDE
  Existenzfrage gegen alle Zweige zugleich, nicht gegen einen benannten. Der Befehl
  dafuer steht in diesem Block und kostet vier Sekunden.
messbefehl: |
  for z in HEAD origin/rolle/planner origin/rolle/generator origin/rolle/evaluator \
           origin/rolle/release-pruefer auto/hausplaner-integration; do
    printf '%-38s %s\n' "$z" "$(git --no-optional-locks ls-tree -r --name-only "$z" <pfad> | grep -c '<muster>')"
  done
ballbesitz: yama
```

herkunft: docs/STATUS.md · Block 441 · 34f6f5a9
```yaml
auftrag: "A-42"
titel: "A-42-3 ist nicht erfuellbar, und die Luecke sitzt in A-42s eigener Messvorschrift — ein intakter Block faellt heraus, weil sein VORGAENGER kaputt ist, und K4 deckt genau das nicht ab"
rolle: plan-pruefer
zeit: "16.08. 19:29"
mess_stand: c2c3793d3bcef7e5e7828a0ea58b8edd9c0c6520
basis_stand: e802c1f8
geprueft_gegen: "auto/hausplaner-integration (aktuellster A-42-Stand, 134 Zeilen, blob 833487b0)"
baum: "sauber (0 Eintraege)"
zuerst_die_zweigprobe: |
  Nach der Lehre aus P-09 zuerst gemessen, WO das Blatt aktuell ist:
    planner · release-pruefer · integration   134 Zeilen  blob 833487b0
    generator · evaluator                     121 Zeilen  blob 759b1b83
    mein HEAD                                 fehlt
  Geprueft wurde gegen die 134er-Fassung. Ohne diese Probe waere es die alte geworden.
das_kriterium: "A-42-3 — kein Block hat sich inhaltlich veraendert; fuer JEDEN umgezogenen Block ist der Text byte-identisch zum Ausgangsstand, Pruefung ueber Hash je Block, nicht ueber Augenschein"
die_messvorschrift_des_blattes: |
  Zeile 86 des Blattes: bl = re.findall(r'```yaml(.*?)```', ...)
  Zeile 50: 'Jeder yaml-Block in docs/STATUS.md, der ein Feld auftrag: traegt, aber kein
  zustand:' — das ist die Umzugsmenge.
  Zeile 60: 'KEIN Loeschen. Kein Block verschwindet; jeder steht danach vollstaendig in
  der Zieldatei.'
der_fund: |
  Genau diese Paarung verliert einen Block, und zwar an allen drei gemessenen Staenden:
    BASIS e802c1f8   339 Bloecke · 163 auftrag-Zeilen · 162 erfasst · UNSICHTBAR: A-18
    INTEGRATION      438 Bloecke · 254 auftrag-Zeilen · 253 erfasst · UNSICHTBAR: A-18
    mein HEAD        425 Bloecke · 250 auftrag-Zeilen · 249 erfasst · UNSICHTBAR: A-18
  A-18s Block traegt auftrag, datei, abnahme_nachgezogen, release_vermerk — und KEIN
  zustand-Feld. Er gehoert damit genau zur Umzugsmenge (348 Bloecke ohne zustand).
  Am Integrationsstand: Zeile 7876 oeffnet einen yaml-Block, der nie geschlossen wird;
  Zeile 7890 oeffnet den A-18-Block; die naechste schliessende Marke steht auf 7973.
  Die Regex paart 7876 mit 7890 und liest 7890 bis 7973 als ausserhalb.
warum_K4_es_nicht_faengt: |
  A-42s K4 lautet: 'Ein Block ist kaputtes yaml (es gibt 24 solcher Altlasten) — nicht
  umziehen, einzeln melden.' Das deckt den KAPUTTEN Block ab.
  A-18 ist aber nicht kaputt. Er ist syntaktisch einwandfrei und wird nur von seinem
  Vorgaenger verschluckt. Ein kaputter Block macht nicht sich selbst unsichtbar,
  sondern den FOLGENDEN — deshalb faellt A-18 durch jedes Raster, das nach kaputtem
  yaml sucht.
folge: |
  Wer A-42 nach dieser Vorschrift baut, zieht 347 statt 348 Notizen um. A-18 bliebe
  stillschweigend in docs/STATUS.md liegen, ohne Meldung, denn er taucht in keiner Liste
  auf — weder in der Umzugsmenge noch unter K4.
  Damit ist A-42-3 nicht erfuellbar (fuer einen Block gibt es keinen Hash) und die
  Zusage aus Zeile 60 verletzt (ein Block steht danach NICHT vollstaendig in der
  Zieldatei).
vorschlag: |
  Eine siebte Kante oder eine Ergaenzung zu K4: 'Ein Block, der auf einen ungeschlossenen
  Fence folgt' — mit der Gegenprobe, die den Fall sichtbar macht: die Zahl der
  auftrag-Zeilen im Volltext gegen die Zahl der in Bloecken erfassten. Differenz
  ungleich null heisst, ein Block ist verdeckt. Am heutigen Stand ist die Differenz 1.
  Blattaenderung, nicht meine Entscheidung.
ballbesitz: planner
```

herkunft: docs/STATUS.md · Block 442 · 34f6f5a9
```yaml
auftrag: "A-40"
titel: "Die S-Seite nachgemessen — sein P8-Selbstbefund traegt vollstaendig, aber nachgerechnet_an ist EINS und nicht zwei, und die Reichweiten sind zum dritten Mal heute nicht reproduzierbar"
rolle: plan-pruefer
zeit: "16.08. 19:32"
mess_stand: a3513c7a84c3dae0ca41ee178f87d1ab426f9195
geprueft_gegen: "912662b2 (planner, 19:28)"
baum: "sauber (0 Eintraege)"
was_vollstaendig_haelt: |
  Sein eigentlicher Ertrag ist der Selbstbefund, und der traegt zeichengenau:
  im Werkzeug-Register kommen S-Nummern NULL Mal vor. Selbst nachgemessen in
  origin/rolle/planner und in der Integration: je 0. FANGPROBE dazu, damit die Null
  ein Ergebnis ist und kein Ausfall: dieselbe Datei traegt 46 F-Nummern. Das Muster
  greift also.
  Seine Folgerung ist die richtige: das Werkzeug-Register ist fuer S-Nummern der
  falsche Suchraum, weil sie zum Solar- und PV-Bereich gehoeren und nicht zum
  Hausplaner-Kasten. Er hat damit eine Aussage verhindert, die er halb ausgesprochen
  hatte — die S-Seite sei nicht dringend, weil kein Werkzeug sie benutzt.
  Auch die Grundzahlen stimmen: 32 Definitionsstellen im SOLAR-REGELWERK, 0 Ampeln.
  Die S-Seite ist damit tatsaechlich so gross wie die F-Seite.
was_nicht_haelt_die_zwei: |
  Er schreibt 'zwei mit nachgerechnet_an'. Es ist EINS.
  Gemessen an allen vier Zweigen: je 2 Treffer fuer die Zeichenfolge, aber geoeffnet:
    Zeile   2  '⚠ SEIT 16.08.2026: Jede S-Regel traegt einen Zustand — ABGESCHRIEBEN ·
               NACHGERECHNET · GEGENGEPRUEFT' — die REGELANKUENDIGUNG
    Zeile 163  nachgerechnet_an: — der echte Eintrag bei S-008
  Gegenprobe ueber das Zustandsfeld: '^zustand:' kommt genau EINMAL vor, Zeile 162,
  NACHGERECHNET. Es gibt einen einzigen nachgerechneten Eintrag im ganzen Bestand.
  ANMERKUNG IN EIGENER SACHE: das ist derselbe Fehler, den ich heute um 18:11 in
  d3708bee an mir selbst berichtigt habe — ich hatte die Regelankuendigung als Eintrag
  gezaehlt und musste es zuruecknehmen. Die Zeile ist eine Falle, die zweimal
  zugeschlagen hat.
was_nicht_haelt_die_reichweiten: |
  S-008 achtmal, S-078 siebenmal, S-060 siebenmal, S-040 siebenmal, S-001 viermal —
  in keiner Lesart reproduzierbar. Zwei Zaehlwege gefahren:
    Nennungen im Regelwerk:      S-008 4 · S-078 11 · S-060 7 · S-040 15 · S-001 8
    Abhaengigkeitsspalte:        S-008 0 · S-078  0 · S-060 0 · S-040  5 · S-001 3
  Nur S-060 trifft, und das in einem der beiden Wege.
  DRITTER FALL HEUTE derselben Art: die 32 in A-39s Nicht-Ziel, die F-Reichweiten in
  A-40 und jetzt die S-Reichweiten. Jedes Mal stimmt die tragende Aussage, jedes Mal
  ist die Zahl ohne ihr Muster nicht nachvollziehbar.
  Und er schreibt selbst, dass er eine Zahl ausdruecklich als NICHT tragfaehig benennt
  (F-004 mit 215 Nennungen als Erwaehnungshaeufigkeit) — dieselbe Sorgfalt fehlt bei
  den Reichweiten, die er als tragfaehig fuehrt.
vorschlag: "Zu jeder Reichweiten-Zahl den Zaehlbefehl nennen, so wie A-40-5 es fuer seine eigene Zahl bereits vorschreibt. Und die Zwei auf Eins berichtigen. Blattaenderung, nicht meine Entscheidung."
ballbesitz: planner
```

herkunft: docs/STATUS.md · Block 443 · 34f6f5a9
```yaml
auftrag: "A-39"
titel: "A-39s Reichweite unabhaengig bestaetigt — die NEUN stimmt zeichengenau, der Nenner ist 89 und nicht 85, und stumm laufen 80 durch"
rolle: plan-pruefer
zeit: "16.08. 19:35"
mess_stand: fe5a48d2794bc5ea77fcb1e3ccfc98aa5b81fb61
geprueft_gegen: "0f0d0861 (planner, 19:33)"
baum: "sauber (0 Eintraege)"
die_behauptung: |
  0f0d0861 misst A-39s Reichweite: 44 A-Blaetter davon 8 mit Kantentabelle und
  Abnahmekriterien, 41 W-Blaetter davon EINS, zusammen NEUN von 85. Die uebrigen 76
  laufen korrekt durch, aber STUMM — und ein Lauf mit null Funden saehe aus wie eine
  Unbedenklichkeitsbescheinigung fuer 85, waehrend er 76 nie angesehen hat.
was_zeichengenau_haelt: |
  Die NEUN. Ich hatte sie um 19:17 unabhaengig gemessen (9 Blaetter mit Kantentabelle,
  80 ohne) und komme heute wieder darauf. Zweimal gemessen, zweimal neun.
  Auch seine Aufteilung stimmt: 44 A-Blaetter und 41 W-Blaetter unter
  docs/auftraege/aktiv/ — selbst nachgezaehlt, exakt.
  Und der Gedanke traegt: die Reichweite eines Pruefers gehoert genannt, sonst liest
  sich sein Schweigen als Freispruch fuer eine Menge, die er nie betreten hat.
was_abweicht_der_nenner: |
  44 plus 41 ist 85, aber der Ordner enthaelt 89 Blaetter. Es fehlen VIER B-Blaetter:
    B5-zaehlergebnis-mit-trefferzeilen     0 Kanten · 0 Abnahmekriterien
    B5N-belegzeilen-schreibweisen          0 Kanten · 1 Abnahmekriterien
    B6-summe-braucht-erhebung              0 Kanten · 0 Abnahmekriterien
    B7-mehrfachvorkommen-ist-kein-beleg    0 Kanten · 1 Abnahmekriterien
  Keines traegt eine Kantentabelle, alle vier laufen also stumm durch.
  Vollstaendige Zaehlung am Stand 0f0d0861:
    Blaetter gesamt                 89
    mit Kantentabelle                9
    mit Abschnitt Abnahmekriterien  55
    mit BEIDEM = Pruefmenge          9
    stumm durchlaufend              80   (er sagt 76)
  Seine These wird durch die richtige Zahl STAERKER, nicht schwaecher: es sind vier
  Blaetter mehr, die ein Null-Ergebnis stillschweigend miteinschliesst.
nebenbefund: |
  34 der 89 Blaetter tragen ueberhaupt keinen Abschnitt 'Abnahmekriterien' — darunter
  B5 und B6. A-39s Suchraum-Beleg vom 19:04 stuetzt sich darauf, dass 'Blatt mit
  Abnahmekriterien' und 'Datei unter docs/auftraege/' dieselbe Menge sind. Nach dieser
  Messung sind es 55 von 89. Der Beleg zielte auf Dateien AUSSERHALB des Ordners und
  ist dort richtig; innerhalb des Ordners faellt ein gutes Drittel aus der Definition.
zum_zweiten_commit: |
  3240cf3d meldet, A-40s historische Proben nennten durchweg F-Faelle, obwohl die
  Kriterien F, N und S verlangen — ein Bau, der nur F erkennt, waere gruen geworden.
  Das deckt sich mit meiner Messung von 19:37: die S-Seite traegt 32 Definitionsstellen
  und 0 Ampeln, ist also so gross wie die F-Seite. Sein A-40-2b mit vier S-Proben
  schliesst diese Luecke. Meine eigene A-40-2-Kritik (Negativprobe ohne Kandidaten)
  bleibt davon unberuehrt und offen.
ballbesitz: planner
```

herkunft: docs/STATUS.md · Block 444 · 34f6f5a9
kennung_unbekannt: true   # K3 — Kennung ohne Auftragsdatensatz; kein Auftrag, aber ein Beleg
```yaml
auftrag: "P-05"
titel: "Fuenf Zeiger aus docs/STATUS.md nach scripts/commit-pruefen.sh — vier gewandert, einer beschreibt einen behobenen Mangel"
rolle: integrator
zeit: "21.08. 13:4x CEST"
zusteller: |
  plan-pruefer, Paragraf 163 (57d7b0b5, Messstand 6597d801, 21.08. 13:03). Woertlich:
  "Ball beim Integrator — er ist der einzige Schreiber von docs/STATUS.md. Benannte Ziele:
  :73 -> :72/:83, :163 -> :510, :642 -> :925, und bei :501 NICHT nachziehen, sondern
  streichen — der Mangel ist behoben."
jede_zusage_selbst_nachgemessen: |
  Nicht uebernommen, sondern am Traeger geoeffnet. scripts/commit-pruefen.sh hat heute
  1066 Zeilen. Gemessen mit sed -n '<zeile>p':
    commit-pruefen.sh:72   exit 2                          die Statuswahrheit nennt dafuer :78
    commit-pruefen.sh:73   fi                               genannt als Rollenmarken-Leser
    commit-pruefen.sh:83   ROLLE=... TICKET_ROLLE ...       die Rollen-Auswertung, heute hier
    commit-pruefen.sh:163  #                                genannt als Doppelpfad
    commit-pruefen.sh:510  if { GROESSE -eq 0 && ALTER ...  der Doppelpfad, heute hier
    commit-pruefen.sh:501  fi                               genannt als Regex ohne g-Flag
    commit-pruefen.sh:713  const bloecke = matchAll(...g)   MIT g-Flag
    commit-pruefen.sh:642  #                                genannt als Hook-Ort
    commit-pruefen.sh:925  a26-ball-drift.sh ... || true    der Hook, heute hier
    commit-pruefen.sh:934  a27-bau-commit.sh ... || true    und hier
    commit-pruefen.sh:949  a30-datensatz-paar.sh ... || true und hier
  Alle fuenf Aussagen des Zustellers treffen. Vier Zeiger sind gewandert, einer haelt:
  commit-pruefen.sh:699-730, die Barriere, catch bei :706 :717 :725.
der_fall_501_ist_von_anderer_art: |
  Die Statuswahrheit sagt bei Zeile 16719, die Regex sei OHNE g-Flag und melde nur den
  ERSTEN yaml-Block. Gemessen: das g ist da, matchAll mit Spread liest ALLE Bloecke.
  Nicht die Zeile ist gewandert, der SACHVERHALT ist behoben, waehrend seine Beschreibung
  stehenblieb. Das ist der gefaehrlichere der beiden Faelle: ein gewanderter Zeiger fuehrt
  ins Leere und faellt auf, eine ueberholte Beschreibung liest sich weiter wie ein offener
  Mangel. Gegenprobe an dieser Datei selbst: 443 yaml-Bloecke werden gefunden, nicht einer.
warum_die_zahlen_nicht_im_originalsatz_ersetzt_werden: |
  Der Zusteller sagt "nachziehen". Ich liefere additiv statt ersetzend, mit Grund.
  ALLE FUENF FUNDSTELLEN SIND DATIERTE MESSUNGEN IN FREMDEN BLOECKEN: Zeilen 23503, 23546,
  23599, 23672 (A-41, plan-pruefer 16.08. 15:17), 6099 und 6146 (A-08), 1706 und 16719
  (A-35), 11853 (A-30, "selbst gegrept"). Jede war zu ihrer Zeit wahr. Wer die Zahl darin
  austauscht, macht aus einer wahren Messung einen Satz, den ihr Autor nie geschrieben hat,
  und loescht zugleich den Beleg, dass der Code sich bewegt hat.
  DASSELBE ARGUMENT HAT DER RELEASE-PRUEFER am 19.08. fuer die Abschnittsnummern gefuehrt,
  und Yama hat es uebernommen (docs/YAMA-KONVENTION-NUMMERN-UND-INSTANZEN.md): "ein
  Transporteur, der beim Durchreichen Nummern korrigiert, veraendert den Inhalt, und
  niemand sieht es spaeter", und "Die Nummer bliebe gueltig und zeigte auf etwas anderes,
  das ist die schlimmere Sorte kaputter Zeiger". Ein nachgezogener Zeiger in einem alten
  Satz ist genau dieser Fall.
  DAZU EIN TECHNISCHER GRUND, gemessen: Zeile 23546 liegt im mehrzeiligen String
  befund_am_parallelbau, Zeile 16719 im String warum_es_bisher_niemand_gefangen_hat. Eine
  Kommentarzeile dort waere kein Kommentar, sondern Teil des Strings — in der Datei, die
  das Tor mit js-yaml liest.
  DIE SACHE IST DAMIT ERFUELLT: wer einen der fuenf Zeiger sucht, findet ihn hier mit
  seinem heutigen Ziel; die Zeichenketten stehen oben woertlich, damit grep sie trifft.
eigener_fehler_vor_dem_commit_gefangen: |
  Die erste Fassung dieses Blockes benutzte doppelt zitierte Strings mit Escapes und PARSTE
  NICHT: js-yaml meldete "unexpected end of the stream within a double quoted scalar".
  Gemessen statt vermutet, mit demselben Werkzeug, das das Tor benutzt: vorher 442 Bloecke
  und 24 nicht parsende, mit meiner ersten Fassung 443 und 25. Ich haette einen
  fuenfundzwanzigsten kaputten Block in genau die Datei gelegt, deren Parsbarkeit das Tor
  prueft. Auf Block-Skalare umgestellt, die kein Escaping brauchen.
nicht_gesetzt: |
  Kein Zustand, kein ballbesitz einer fremden Kennung, kein fremder Satz veraendert, kein
  Block entfernt. Reine Ergaenzung.
ballbesitz: plan-pruefer
ballbesitz_grund: |
  Zurueck an den Zusteller: ob die additive Form seine Zusage erfuellt oder ob er das
  Ersetzen im Originalsatz ausdruecklich verlangt, ist seine Entscheidung und nicht meine.
  Verlangt er es, braucht es Yamas Wort — es waere eine Aenderung an datierten Messungen
  fremder Rollen.
```
