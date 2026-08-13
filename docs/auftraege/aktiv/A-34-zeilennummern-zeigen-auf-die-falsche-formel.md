# A-34 — Meine F-004-Berichtigung hat 52 Verweise verschoben. Einer steht im Produktivcode und zeigt auf die falsche Formel

```yaml
auftrag: "A-34"
werkzeug: "—  (Verweise auf die FORMELSAMMLUNG)"
art: "BAU — die WIRKSAMEN Fundstellen-Verweise auf Anker umstellen. Ein Kommentar im Insel-Code,
      die Kriterien der aktiven Blaetter. Kein Verhalten, keine Formel."
spur: A
heimat_app: ticket
dor_beleg: "steht aus — plan-pruefer."
status_steht_in: docs/STATUS.md
basis_sha: 876403f4
prioritaet: P2
ballbesitz: "plan-pruefer (DoR)"
claim: "planner 13.08. — Claim VOR dem Schnitt."
kennung_geprueft: "A-34 hat NULL Treffer in docs/STATUS.md und NULL Blaetter in
                   docs/auftraege/aktiv/. A-01 bis A-33 sind vergeben. Frei."
anlass: "DEN SCHADEN HABE ICH VERURSACHT. Meine F-004-Berichtigung (136ebca1) hat rund 35 Zeilen in
         die FORMELSAMMLUNG eingefuegt — noetig, denn die Zaehlerzeile war falsch. Nebenwirkung:
         jeder Verweis auf eine Zeile NACH der Einfuegung zeigt seither auf eine ANDERE Formel.
         Gefunden hat es der Evaluator bei der A-32-Abnahme (7994539d), als er alle sieben
         Fundstellen des Baus geoeffnet hat."
grundlage: "docs/rollenkette/werkbank/01-MATHEMATIK/FORMELSAMMLUNG.md ·
            resources/planner/hausplaner/geometry/geradenGeometrie.ts:133 und :146 ·
            Commit 136ebca1 (die Einfuegung) · 7994539d (der Befund)"
```

## 1 — Der Befund, gemessen statt geschätzt

```text
JEDER VERWEIS DER FORM 'FORMELSAMMLUNG:<Zahl>' wurde eingesammelt und gegen den
HEUTIGEN Stand gehalten — also: welche Formel steht heute an dieser Zeile?
  Verweise mit Zeilennummer gesamt                52
  davon in resources/planner/** (Produktivcode)    2
  Rest in docs/ (Blaetter, STATUS.md, Befunde)    50

DIE VERSCHIEBUNG IST SICHTBAR, drei Beispiele mit Beleg:
  :120  gemeint war F-013 Selbstschnitt-Pruefung   zeigt heute auf F-004
  :139  gemeint war F-020 Kantenversatz           zeigt heute auf F-011
  :141  gemeint war F-020 Kantenversatz           zeigt heute auf F-011
  :75   gemeint war F-004                          zeigt auf F-004  ✔
  :80   gemeint war F-004                          zeigt auf F-004  ✔
-> Verweise VOR der Einfuegung (~Zeile 88) stimmen weiter. Alles danach ist
   um rund 35 Zeilen verschoben.
```

> ***Das Gefährliche daran ist, dass es lautlos ist.*** *Ein Verweis auf `:141` sieht genauso aus wie
> vorher — er führt nur jetzt zu **F-011 Polygonfläche** statt zu **F-020 Kantenversatz**. **Wer ihm
> folgt, liest eine andere Formel und merkt es nur, wenn er den Namen mitliest.** Der Evaluator hat es
> genau so gefunden: beim Öffnen **aller sieben** Fundstellen des Baus.*

## 2 — Warum NICHT alle 52 berichtigt werden

*Der naheliegende Gedanke ist, alle 52 Zahlen zu korrigieren. **Das wäre die falsche Abhilfe:*

```text
(a) Es behebt nichts dauerhaft. Die naechste Einfuegung in dieselbe Datei
    verschiebt sie wieder — und der Fehler ist wieder lautlos.
(b) Die meisten der 50 Doku-Verweise stehen in BEFUNDEN und BELEGEN, also in
    Texten, die einen STAND beschreiben. Dort ist eine Zeilennummer legitim:
    sie belegt, was zu einem Zeitpunkt dastand. Der sechste Schritt von
    Pflichtpruefung 7 sagt es so: „ein Befund wird gelesen, ein Kriterium wird
    BEFOLGT."
(c) Und viele stehen in ABGESCHLOSSENEN Blaettern (BETRIEBSBESTAETIGT). Sie
    dort anzufassen hiesse, abgenommene Vorgaenge zu aendern.

WAS STATTDESSEN GILT: berichtigt wird, wo der Verweis WIRKT —
  im Produktivcode (ein Leser folgt ihm beim Bauen)
  in KRITERIEN aktiver Blaetter (ein Bauender muss ihm folgen)
und zwar auf einen ANKER statt auf eine Nummer.
```

## 3 — Scope

```text
A-34 IST  (1) resources/planner/hausplaner/geometry/geradenGeometrie.ts:133
              nennt `FORMELSAMMLUNG.md:141-143` fuer F-020s Normalform. Heute
              steht dort F-011. Umstellen auf den ANKER: „FORMELSAMMLUNG.md,
              F-020, Abschnitt Kantenversatz".
              Der zweite Verweis in :146 (`FORMELSAMMLUNG.md:4`) ist RICHTIG —
              Zeile 4 ist der Kopf und liegt vor der Einfuegung. Er wird
              trotzdem auf einen Anker gestellt, weil dieselbe Falle sonst beim
              naechsten Mal dort zuschlaegt.
          (2) die KRITERIEN der AKTIVEN Blaetter, die eine Zeilennummer
              verlangen: am Bau-Stand einsammeln und auf Anker stellen.
              Nur Kriterien — nicht Befund- und Belegtexte.

A-34 IST NICHT
          die Berichtigung aller 52 Verweise. Abschnitt 2 nennt die drei
          Gruende einzeln.
          eine Aenderung an BEFUND- oder BELEGTEXTEN. Dort belegt die Nummer
          einen Stand und ist richtig.
          eine Aenderung an ABGESCHLOSSENEN Blaettern (BETRIEBSBESTAETIGT,
          ABGENOMMEN). Abgenommene Vorgaenge werden nicht nachtraeglich
          umgeschrieben.
          eine Aenderung an der FORMELSAMMLUNG selbst. Keine Formel, keine
          Ueberschrift, keine Nummer.
          eine Aenderung am VERHALTEN von geradenGeometrie.ts. Nur Kommentar.
```

## 4 — Abnahmekriterien

```text
A-34-1 (P1, TRAGEND) geradenGeometrie.ts nennt fuer F-020s Normalform einen
       ANKER und keine Zeilennummer. Gegenprobe, die rot werden kann: grep auf
       `FORMELSAMMLUNG[^ ]*:[0-9]` ueber resources/planner/** liefert NULL
       Treffer. Vorher waren es zwei — beide Zahlen im Bericht nennen.
A-34-2 (P1) Der Anker ist AUFFINDBAR, nicht nur schoen: er nennt die F-Nummer
       UND die Abschnittsbezeichnung, sodass ein Leser mit einer Suche dorthin
       kommt. Nachweis: der genannte Text kommt in FORMELSAMMLUNG.md genau
       EINMAL vor. Am Bau-Stand pruefen.
A-34-3 (P1) KEINE Aenderung an der Formel oder am Verhalten. Gegenprobe: der
       Bau-Commit aendert in geradenGeometrie.ts NUR Kommentarzeilen, und die
       Insel-Suite bleibt gruen mit Zaehler vorher/nachher.
A-34-4 Die KRITERIEN der aktiven Blaetter sind AM BAU-STAND eingesammelt, nicht
       aus diesem Blatt uebernommen (E1). Welche es sind, entscheidet die
       Messung — meine Erhebung vom 13.08. nennt 52 Verweise insgesamt und 2 im
       Produktivcode; die Aufteilung Kriterium gegen Beleg ist beim Bauen zu
       treffen und im Bericht zu begruenden.
       WENN ein Verweis strittig ist (Kriterium oder Beleg?), wird er GEMELDET
       und nicht entschieden. Eine falsche Einordnung aendert ein abgenommenes
       Blatt.
A-34-5 Kein Beifang: der Bau-Commit fasst NICHT die FORMELSAMMLUNG an, nicht
       docs/STATUS.md und keine Blaetter mit Zustand BETRIEBSBESTAETIGT oder
       ABGENOMMEN. Gegenprobe am Diff.
```

**Nachweisform: Befehl und Trefferzeilen** (B5), **Messung am COMMIT** (E1),
**Nachweis muss rot werden können** (Pflichtprüfung 4), **Anker statt Nummer** (Prüfung 7, 6. Schritt).

```yaml
den_schaden_habe_ich_verursacht: "Das steht hier, weil es die Einordnung aendert. Meine
        F-004-Berichtigung war NOETIG — die Zaehlerzeile lieferte einen Punkt, der kein Schnittpunkt
        ist. Aber sie hat rund 35 Zeilen eingefuegt, und seither zeigt jeder Verweis nach dieser
        Stelle auf eine andere Formel. Ich habe die Nebenwirkung beim Berichtigen NICHT bedacht und
        auch nicht gemessen; gefunden hat sie der Evaluator bei der A-32-Abnahme, als er alle sieben
        Fundstellen des Baus geoeffnet hat. Das ist kein Vorwurf an mich, sondern der Grund, warum
        der sechste Schritt von Pruefung 7 jetzt Anker statt Nummern verlangt."
warum_der_auftrag_klein_bleibt: "52 Verweise klingen nach viel Arbeit. Gemessen sind ZWEI im
        Produktivcode, und der Rest steht ueberwiegend in Befunden, Belegen und abgeschlossenen
        Blaettern, wo eine Zeilennummer legitim einen STAND belegt. Wer alle 52 anfasst, aendert
        abgenommene Vorgaenge und behebt trotzdem nichts dauerhaft — die naechste Einfuegung
        verschiebt sie wieder."
was_ich_gemessen_habe_und_was_nicht: "SELBST GEMESSEN: alle 52 Verweise eingesammelt und je gegen den
        HEUTIGEN Stand gehalten, also welche Formel an dieser Zeile steht; die Verschiebung an drei
        Beispielen belegt (:120 -> F-004 statt F-013, :139 und :141 -> F-011 statt F-020) und an zwei
        Gegenbeispielen, die weiter stimmen (:75 und :80). NICHT GEMESSEN: welche der 50
        Doku-Verweise Kriterien und welche Belege sind — das verlangt A-34-4 vom Bau, weil die
        Einordnung je Stelle zu treffen ist und eine falsche ein abgenommenes Blatt aendern wuerde."
A_34_nimmt_keinen_paragraf3_platz: "ENTWURF, nicht IN_ARBEIT."
```

## Votum des Evaluators (§11) — ABGENOMMEN

```yaml
votum: ABGENOMMEN
geprueft_am: "13.08.2026, evaluator"
bau_commit: "2f8cf32d (22:21) — GESUCHT, der einzige. Zwei Dateien, kein Nachtrag."
elter: "5c0a7e12"
BEFANGENHEIT_OFFENGELEGT: "DIESER AUFTRAG IST AUS MEINEM EIGENEN BEFUND ENTSTANDEN. Ich habe die
  verschobene Fundstelle bei der A-32-Abnahme gefunden (7994539d), weil ich alle sieben
  Fundstellen des Baus geoeffnet habe; der Planner hat daraus A-34 geschnitten. Ich pruefe hier
  also die Behebung meines eigenen Funds. Deshalb habe ich JEDE Zahl und JEDEN Anker neu gemessen
  statt sie aus meinem eigenen Befund zu uebernehmen — und die Gegenprobe von A-34-1 ausdruecklich
  mutiert, um zu sehen, ob sie ueberhaupt anschlagen kann."
```

### Messtisch — jede Kriterienzeile eine Zeile

```text
A-34-1 (P1, TRAGEND)  ERFUELLT — beide Zahlen selbst gemessen, nicht uebernommen
  am ELTER 5c0a7e12:  grep -rE 'FORMELSAMMLUNG[^ ]*:[0-9]' resources/planner/  -> 2 Treffer
                      geradenGeometrie.ts:133 (`FORMELSAMMLUNG.md:141-143`)
                      geradenGeometrie.ts:146 (`FORMELSAMMLUNG.md:4`)
  am BAU   2f8cf32d:  derselbe Befehl                                          -> 0 Treffer
  FANGPROBE (Anker 1x, md5 zurueck auf 06221d20): eine Zeilennummer wieder eingebaut
                   -> die Gegenprobe meldet 1 statt 0. Sie kann also rot werden.

A-34-2 (P1)           ERFUELLT — alle drei Anker SELBST in der Sammlung gezaehlt
  '### F-020 · Straight Skeleton (Grundgleichung)'   1x   (Z.167)
  'Formel (Kantenversatz zur Zeit t)'                1x   (Z.174)
  'Eine Formel steht genau einmal'                   1x   (Z.4)
  UND SIE FUEHREN ZUM RICHTIGEN: direkt unter dem Kantenversatz-Anker steht
    Kante als Gerade:  a·x + b·y + c = 0    mit a²+b² = 1
    Versetzte Kante:   a·x + b·y + c − t = 0
  — genau die Normalform, die der Kommentar meint. Der Anker ist auffindbar UND richtig.
  FANGPROBE: den Anker-Text verfaelscht (Grundgleichung -> Grundformel)
                   -> kommt 0x statt 1x in der Sammlung vor. Auch diese Probe traegt.

A-34-3 (P1)           ERFUELLT
  Alle geaenderten Zeilen in geradenGeometrie.ts ohne Kommentarzeichen aufgelistet: LEER.
  Es ist ausschliesslich Kommentar.
  Insel-Suite am Bau-Stand SELBST gefahren: tests 1750, pass 1750, fail 0. tsc exit=0.
  (1750 ist derselbe Stand wie bei A-32 und A-29 — der Bau aendert die Zahl nicht, und das ist
  die Zusage.)

A-34-4                ERFUELLT — und ich habe die Menge SELBST eingesammelt, nicht die Liste geprueft
  Alle Blaetter in docs/auftraege/aktiv/ mit einem Zeilenverweis: NEUN.
  Davon abgeschlossen und nach A-34-5 ausgeschlossen: A-14, A-20, A-32 (alle BETRIEBSBESTAETIGT).
  Von den sechs aktiven traegt GENAU EINES einen Verweis INNERHALB des Kriterien-Zauns:
    W-06  Z.184  'FORMELSAMMLUNG.md:218' fuer F-032   -> vom Bau auf den Anker umgestellt
  Die drei Behauptungen des W-06-Nachtrags einzeln nachgemessen:
    '### F-032 · Transformation eines Punktes' kommt 1x vor, auf Zeile 253  -> stimmt
    Zeile 218 traegt heute '- **Zweck:** PV-Ertrag, Verschattung'           -> stimmt woertlich
    F-032 liegt auf 253                                                     -> stimmt
  Die uebrigen Verweise (W-12/1, W-16/1, W-18/1, W-31, und A-34 selbst) stehen in
  grundlage-Feldern, Befundlisten und Belegtexten — dort belegt die Nummer einen STAND und ist
  nach Abschnitt 2 des Auftrags legitim. Unberuehrt, richtig so.

A-34-5                ERFUELLT
  Am Commit gemessen: FORMELSAMMLUNG 0 · docs/STATUS.md 0 · kein BETRIEBSBESTAETIGT- oder
  ABGENOMMEN-Blatt beruehrt. Der Commit fasst genau zwei Dateien an, und W-06 ist BEREIT.

Browser               NICHT GEFAHREN, mit Grund: reine Kommentaraenderung, kein Verhalten.
§15                   Kein Schreibvorgang gegen eine Datenbank im Pruefumfang.
```

### Zwei Stellen, an denen der Bau mehr getan hat als abzuhaken — beide zu Recht

```text
1  DIE GEGENPROBE HAT IHN SELBST ERWISCHT. Nach der ersten Umstellung meldete
   grep -rE 'FORMELSAMMLUNG[^ ]*:[0-9]' noch EINEN Treffer: sein eigener Erklaersatz zitierte die
   alte Nummer in VERWEISFORM. Er haette '1 statt 0' melden und begruenden koennen — stattdessen
   hat er den Satz umformuliert, sodass die alte Zahl ausgeschrieben dasteht. AM BAU-STAND
   NACHGEPRUEFT: der Beleg ist erhalten ("zeigte auf die Zeilen 141 bis 143 jener Datei"), und die
   mechanische Gegenprobe traegt trotzdem. Das ist die bessere Loesung, nicht die bequemere.
2  DER ZWEITE VERWEIS WAR RICHTIG UND WURDE TROTZDEM UMGESTELLT. `FORMELSAMMLUNG.md:4` zeigte auf
   den Kopf und liegt VOR der Einfuegung — selbst nachgemessen, Zeile 4 traegt weiter
   "Eine Formel steht genau einmal". Der Scope verlangt die Umstellung ausdruecklich, damit
   dieselbe Falle dort nicht beim naechsten Mal zuschlaegt. Erfuellt.
```

### Mein eigener Messfehler — und er ist derselbe, den der Generator vor mir gemacht hat

```text
Beim Einsammeln der Kriterien-Verweise (A-34-4) meldete mein erstes Muster ZWEI Treffer in aktiven
Blaettern: W-06 und W-31. Ich war einen Schritt davon entfernt, "der Bau hat W-31 uebersehen" als
Befund zu schreiben.

URSACHE, an meinem eigenen Werkzeug gefunden: ich habe "liegt der Treffer nach der
Kriterien-Ueberschrift" gemessen — und in W-31 folgt dem Kriterienblock ein nachgestellter
yaml-Block. Die Trefferzeile Z.240 steht dort im Feld
`zur_sperre_und_was_der_plan_pruefer_dazu_gemessen_hat`, also in einem BELEGTEXT. Erst die
Abgrenzung am ```-ZAUN statt an der Ueberschrift hat es sauber getrennt: dann bleibt W-06 allein
uebrig.

DASS DER GENERATOR IN DIESELBE FALLE GELAUFEN IST UND SIE SELBST GEMELDET HAT, habe ich erst
DANACH gelesen — die Reihenfolge war eingehalten. Es ist trotzdem der Hinweis wert: dieselbe
Ueberschriften-Heuristik hat zwei Rollen unabhaengig voneinander in denselben Fehlgriff gefuehrt.
Wer kuenftig Kriterien von Belegen trennt, grenzt am Zaun ab, nicht an der Ueberschrift.
```

**Weitergabe:** ABGENOMMEN → **Release-Prüfer**.
