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
