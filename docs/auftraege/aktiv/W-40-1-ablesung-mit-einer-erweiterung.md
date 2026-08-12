# W-40/1 — Nachbesserung: W-40 ist eine Ablesung mit EINER Erweiterung, und der Träger ist das Paket

```yaml
auftrag: "W-40/1"
werkzeug: "W-40 Gültigkeitsstatus"
art: "NACHBESSERUNG nach §12. W-40 ist BETRIEBSBESTAETIGT und inhaltlich überholt: Yamas
      Entscheidung vom 12.08. ordnet drei der vier Stufen dem vorhandenen Code zu und benennt
      NUR blocked als Erweiterung. Das Blatt gibt vor, was zu drei Vierteln existiert."
spur: A
heimat_app: ticket
dor_beleg: "steht aus — plan-pruefer."
status_steht_in: docs/STATUS.md
basis_sha: 2e7504ec
prioritaet: P1
anlass: "Yamas Antwort auf beide Fachfragen, eingetragen in 2e7504ec. Der Release-Prüfer hat den
         Ball ausdrücklich an mich gegeben: 'der Reifegrad ENTWORFEN trägt nach Yamas Entscheidung
         nicht mehr … ich ändere die Registerzeile NICHT selbst: das Register ist seine Arbeit.'"
ballbesitz: "plan-pruefer (DoR)"
claim: "planner 12.08. — Claim VOR dem Schnitt."
grundlage: "Yamas Entscheidung 12.08. (STATUS.md, W-40-Datensatz) · geometry/configuratorPackage.ts
            · W-40s sieben Blätter als nachzubessernder Gegenstand"
```

## 1 — Was Yama entschieden hat, und meine Belege dazu selbst nachgemessen

```text
ENTSCHEIDUNG 1   review-required ist KEINE Zahlenluecke: die vier und die drei liegen
                 nicht auf derselben Achse, 4+3 muss nicht 8 ergeben.
                 review-required  = checked    (existiert, samt Uebergaengen)
                 confirmed        = approved   (existiert, kannIntegrieren prueft darauf)
                 outdated         = outdated   (existiert, markiereVeraltet setzt es)
                 blocked          = DIE EINZIGE Erweiterung, 0 Treffer
  -> W-40 ist eine ABLESUNG MIT EINER ERWEITERUNG und keine Vorgabe.

SELBST NACHGEMESSEN, jede Stelle geoeffnet:
  configuratorPackage.ts:107   checked: ['draft', 'approved', 'generated']
                               -> der einzige Weg nach approved fuehrt ueber checked
  :120-122                     kannIntegrieren(paket) => paket.status === 'approved'
  'blocked' im ganzen Hausplaner   0 Treffer
```

## 2 — Der Träger ist das PAKET, und W-40s Blatt sagt SCHRITT

**Die überholte Stelle, wörtlich aus `2-FUNKTION.md`:**

```text
RICHTIG  zwei Felder nebeneinander:
           fortschritt: SchrittStatus        (W-38, gebaut)
           gueltigkeit: Gueltigkeitsstatus   (W-40, Vorgabe)
         -> ein Schritt kann ok UND confirmed sein, oder ok UND outdated.
```

> **Nach Yamas Zuordnung hängt die Gültigkeitsachse am PAKET, nicht am Schritt.** *Der
> Release-Prüfer hat es benannt und dabei seine eigene frühere Ablesung zurückgezogen: **die
> Messung steht** — die beiden Träger sind im Code getrennt, Import-Zähler 0 in beide Richtungen —
> **aber der Schluss trägt nicht mehr**, weil Yama `confirmed` dem `approved` zuordnet und damit der
> Achse am Paket.*

**Und daraus folgt das Baurisiko, wörtlich von ihm:** *„Wer das Blatt liest und baut, ohne diese
Zeile zu kennen, baut die Achse ein **zweites Mal am falschen Träger** — und das wäre die zweite
Wahrheit, die es heute noch nicht gibt. Der Satz gehört ins Blatt, **bevor** gebaut wird."*

## 3 — Yamas zwei Auflagen für den Bau, und seine Namenswarnung

```text
AUFLAGE 1   blocked traegt seinen GRUND mit.
            Ein blocked ohne blockiert_durch ist eine Absage ohne Erklaerung.

AUFLAGE 2   blocked wird NIE von Hand gesetzt oder geloest.
            Wer das will, meint DECISION_BLOCKED und gehoert in die Rollenkette
            statt ins Modell.

DIE UNTERSCHEIDUNG, die beides traegt — WORAUF gewartet wird:
  DECISION_BLOCKED   wartet auf einen MENSCHEN · Ebene Prozess · Ort STATUS.md
                     Aufhebung NUR durch Yamas Entscheidung, nie maschinell
  blocked            wartet auf eine BEDINGUNG · Ebene Produkt · Ort Gebaeudemodell
                     Adressat das naechste Werkzeug
                     Aufhebung AUTOMATISCH, sobald die Vorbedingung messbar erfuellt ist

Yamas Beispiel: PV-Belegung auf einer Dachflaeche ohne bestaetigte Geometrie —
niemand entscheidet etwas, die Sperre faellt von selbst, wenn die Geometrie approved ist.
```

> **Seine Namenswarnung ist der Kern der zweiten Auflage:** *zwei Zustände, die beide „blockiert"
> heißen und **gegensätzlich** aufgelöst werden, werden verwechselt. **Beim Bau trägt `blocked` im
> Blatt und im Code den Satz mit: „nicht `DECISION_BLOCKED`, dieser hier löst sich ohne mich."***

## 4 — Der Reifegrad folgt dem Blatt, nicht umgekehrt

```text
HEUTE    Registerzeile 127 traegt ENTWORFEN.
         Yamas Entscheidung sagt: Ablesung mit einer Erweiterung.
         BESCHRIEBEN waere aber HEUTE falsch, denn das Blatt liest nichts ab —
         es gibt vor.

DESHALB  die Registerzeile bleibt ENTWORFEN, BIS das Blatt tatsaechlich abliest.
         Sie wird als LETZTER Schritt dieses Auftrags nachgezogen, nicht als erster.
         Wer sie vorher aendert, behauptet eine Ablesung, die es nicht gibt.
```

*Ich habe den Reifegrad in `5028375e` ausdrücklich nicht geändert, bis Yamas Antwort da ist. **Sie
ist da — und sie sagt, dass zuerst das Blatt zu ändern ist.** Der Reifegrad ist die Folge, nicht die
Ursache.*

## 5 — Abnahmekriterien

```text
W-40/1-1  (P1, TRAGEND) Der TRAEGER ist berichtigt: die Gueltigkeitsachse haengt am
          PAKET (ConfiguratorPackage), nicht am Schritt. Die ueberholte Stelle in
          2-FUNKTION wird NICHT geloescht, sondern als ueberholt gekennzeichnet, mit
          Yamas Zuordnung und dem Datum — ein nachtraeglich umgeschriebenes Blatt ist
          kein Beleg mehr.
W-40/1-2  (P1) Die DREI vorhandenen Stufen sind als ABLESUNG beschrieben, je mit
          Fundstelle am Bau-Stand: review-required als checked, confirmed als approved
          mit kannIntegrieren, outdated als outdated mit markiereVeraltet.
W-40/1-3  (P1) blocked ist als EINZIGE Erweiterung gekennzeichnet, mit der Messung
          0 Treffer. Alles andere ist Ablesung.
W-40/1-4  (P1) Yamas ZWEI AUFLAGEN stehen im Blatt: blocked traegt blockiert_durch;
          blocked wird nie von Hand gesetzt oder geloest. Beide woertlich.
W-40/1-5  (P1) Die Unterscheidung blocked gegen DECISION_BLOCKED steht in 7-GRENZEN
          mit allen vier Merkmalen: worauf gewartet wird, Ebene, Ort, Aufhebung. Dazu
          Yamas Namenswarnung und der mitzufuehrende Satz.
W-40/1-6  Zwei Pfadangaben aus Yamas Belegen sind im Blatt korrekt: statusAus liegt in
          app/dashboard/fahrschritte.ts (der dashboard-Teil fehlte), und der
          Uebergangsblock beginnt bei :103 und nicht bei :101. Der INHALT beider
          Angaben stimmt — nur die Fundstelle wird geradegezogen.
W-40/1-7  Die REGISTERZEILE wird als LETZTER Schritt nachgezogen, wenn das Blatt
          abliest: von ENTWORFEN auf BESCHRIEBEN. Nachweis: die Zeile vorher und
          nachher, und der Satz, dass die Erweiterung blocked darin genannt ist.
W-40/1-8  Kein Produktivcode. Diese Nachbesserung aendert Blaetter und die
          Registerzeile — nicht configuratorPackage.ts und nicht studioDaten.ts.
```

**Nachweisform: Befehl und Trefferzeilen** (B5), **Fundstellen am Bau-Stand** (Pflichtprüfung 8),
**jede Zählung mit geöffneter Stelle** (Pflichtprüfung 7).

```yaml
warum_P1: "Das Blatt gibt heute vor, was zu drei Vierteln existiert, und es haengt die Achse an den
        falschen Traeger. Wer danach baut, erzeugt die zweite Wahrheit, die es heute noch nicht gibt —
        der Release-Pruefer hat das gemessen und ausdruecklich gesagt, der Satz gehoere ins Blatt
        BEVOR gebaut wird. Solange W-40 so steht, ist jeder Bau darauf riskant."
mein_anteil: "Ich habe W-40 als Vorgabe geschnitten und dabei die Praemisse kein Code aus der Quelle
        UEBERNOMMEN statt sie zu messen — H-6, und heute der vierte Fall dieser Klasse. Der Traeger
        ist die zweite Haelfte desselben Fehlers: ich habe die Achse an den Schritt gehaengt, weil
        W-38s SchrittStatus dort haengt, ohne zu pruefen woran die VORHANDENE Achse haengt."
was_dieser_auftrag_NICHT_tut: "Er baut nichts und er loescht nichts. Die ueberholte Stelle bleibt als
        Beleg stehen und wird gekennzeichnet — dieselbe Form, die der Generator bei W-23 vorgemacht
        hat und die A-20-4 verlangt."
W_40_1_nimmt_den_paragraf3_platz: "Sobald gezogen: IN_ARBEIT. §3 steht bei 0."
```
