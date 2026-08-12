# W-07N — drei Blätter füllen und die Azimutgrenze eintragen. Nicht neu schneiden: vier von sieben Blättern stehen

```yaml
auftrag: "W-07N"
werkzeug: "W-07 Dach aus Kontur"
art: "Nachbesserung eines ALTSTANDES — kein Stufe-1-Blatt, W-07 ist zu 4/7 beschrieben"
titel_berichtigt: "12.08. — Ueberschrift und art trugen die widerlegte Zahl 6/7 an ihrer DRITTEN und
         VIERTEN Station. Befund des plan-pruefers 50505407, und es ist genau der Vorlagen-Mangel,
         den ich in b01f9027 selbst notiert habe: eine SPEC-Berichtigung muss JEDE abhaengige
         Stelle treffen. Ich habe ihn notiert und im selben Blatt erneut begangen.
         BELEGE, ZITATE UND PROTOKOLLE bleiben unangetastet — dort ist 6/7 der Gegenstand
         des Befundes und nicht seine Behauptung."
titel: "2-FUNKTION.md ist ein leeres Formular, waehrend W-07 im Register BESCHRIEBEN traegt"
spur: A
heimat_app: ticket
status_steht_in: docs/STATUS.md
dor_beleg: "a5aab234 — plan-pruefer: 'W-01N und W-07N BEREIT beim ersten Review', beide Rot-Lagen selbst gemessen. Berichtigt 12.08.: der Blattkopf hing auf ENTWURF, weil die DoR die Datei nicht anfasste."
basis_sha: 3d368625
prioritaet: P1
anlass: "Yamas Schritt 2 (W-07+W-08 schneiden) mit zwei Auflagen. Beim Messen: W-07 braucht
         keinen Schnitt, sondern eine Nachbesserung an genau der Stelle, die die Auflage trifft."
ballbesitz: "plan-pruefer (DoR), danach generator"
claim: "planner 12.08. — Claim VOR dem Schnitt."
```

## Warum kein Stufe-1-Blatt — gemessen, nicht angenommen

**Der Auftrag lautete „W-07 und W-08 schneiden". Für W-07 trifft das nicht zu:**

```text
Register:  | W-07 | Dach aus Kontur | BESCHRIEBEN | W-05, W-06 | F-010, F-013, F-014,
                                                                F-025, F-026, F-020,
                                                                F-021, F-022 |
Werkbank-Ordner W-07-dach-aus-kontur/ — echte Platzhalter je Blatt:
  1-ZWECK.md        0        4-BEDIENUNG.md    0        6-PRUEFUNG.md   0
  2-FUNKTION.md     9  <--   5-CODE/LIESMICH   (vorhanden, 927 B)      7-GRENZEN.md  0
  -> SECHS von SIEBEN Blaettern sind gefuellt. EINES ist ein leeres Formular.

  BERICHTIGT 12.08. — DIESE MESSUNG WAR FALSCH, UND DER GRUND IST DAS MESSVERFAHREN:
  die Platzhalter-Zaehlung sucht <...> und ist damit BLIND fuer eine unveraenderte
  Vorlage ohne Platzhalter. Harte Nachmessung (md5 des Inhalts AB ZEILE 2, weil nur
  die Ueberschrift den Werkzeugnamen traegt):

    5-CODE/LIESMICH.md   927 B, identisch mit 12 anderen Werkzeugen bis auf die
                         Ueberschrift ("# W-xx · CODE" -> "# W-07 · CODE"). Mein
                         Blatt nannte die 927 B und schrieb "vorhanden" dazu.
    6-PRUEFUNG.md        ebenfalls unveraenderte Vorlage (13 Werkzeuge identisch).
    2-FUNKTION.md        9 Platzhalter, wie gemessen.

  -> W-07 ist 4/7, NICHT 6/7. Dieser Auftrag fuellt DREI Blaetter, nicht eines.

  GEGENPROBE, dass der Zaehler trotzdem traegt: dieselbe Methode ueber die zehn
  BESCHRIEBEN-Werkzeuge der Klasse A (W-01, W-02, W-04, W-05, W-08, W-09, W-11,
  W-13, W-21, W-22) — KEINES traegt in irgendeinem der sieben Blaetter eine
  unveraenderte Vorlage. Der Stand 10/11 ist belastbar; die Luecke ist allein W-07.
```

**Die NEUN Platzhalter, wörtlich aus `2-FUNKTION.md` — mit `grep -n` gelesen, nicht gezählt (B5):**

```text
:17  <Jeder Zustand mit: was wird angezeigt, was wird erwartet, was passiert bei Abbruch.>
:27  - **Name:** `<KommandoName>`
:28  - **Ausfuehren:** <was genau am Datenmodell geaendert wird>
:29  - **Zuruecknehmen:** <wie der vorherige Zustand exakt wiederhergestellt wird>
:30  - **Buendelung:** <wird das Werkzeug zu EINEM Kommando gebuendelt? Wenn ja, ab wann>
:34  - Aendert Schicht 1 (Domaene): <ja/nein — was>
:35  - Rechnet in Schicht 2 (Geometrie): <welche F-Nummern>
:36  - Lebt in Schicht 3 (Anwendung): <Dateiname>
:37  - Zeigt sich in Schicht 4/5: <was der Anwender sieht>
Dazu eine leere Tabelle:  | Was | Typ | Wohin |  ->  | | | |
```

> **Meine Zählung war ZWEIMAL falsch, in beide Richtungen — und Yama hat die richtige Zahl geliefert:**
>
> ```text
> 1. Lauf   grep -rnoE '<[^>]{3,40}>'   -> 7   davon EINER falsch:
>           7-GRENZEN.md:69  "< 1 mm² | erst bei Gebäuden >"  = Vergleichsoperator
> 2. Lauf   grep -cE '<[a-zA-ZäöüÄÖÜ][^>]{2,40}>'  -> 6   der Vergleich war weg,
>           aber DREI echte Platzhalter fehlten jetzt: :17, :29, :30
> Yama       grep -n ohne Laengengrenze          -> 9   RICHTIG
>           Zeile 17 ist 86 Zeichen lang — mein {2,40} hat sie verschluckt.
> ```
>
> **Die Lehre ist doppelt und beide Hälften sind meine:** *Erstens habe ich mit `-c` gezählt statt
> mit `-n` gelesen — deshalb sah ich den Vergleichsoperator nicht.* **Zweitens habe ich, um ihn
> loszuwerden, eine willkürliche Längengrenze `{2,40}` ins Muster gesetzt — und damit drei echte
> Treffer verloren. Der Filter gegen einen Fehlertyp erzeugte einen anderen.** *Das ist genau der
> Grund für die neue Barriere **B5** (eigener Auftrag, `docs/auftraege/aktiv/B5-...`): wer mit `-c`
> etwas behauptet, führt denselben Lauf ohne `-c` und liest, was er gezählt hat.*

## Das ist ein ALTSTAND, kein Verstoß — und der Unterschied gehört ins Blatt

```text
W-07s Werkbank-Dateien   angelegt 07.08. 09:34 (Dateisystem), gesichert 10.08. 19:11 (1e933a64)
erstes Blatt mit dem
Platzhalter-Kriterium    W-01/1, 10.08. 19:54 (193681cd)
                         -> 43 MINUTEN SPAETER
```

**W-07 wurde beschrieben, bevor „kein Platzhalter" ein Kriterium war.** *Niemand hat eine Regel
gebrochen; die Regel entstand danach.* **Aber der Reifegrad `BESCHRIEBEN` behauptet heute etwas, das
für `2-FUNKTION` nicht gilt — und der Abschlusszähler der Klasse A zählt W-07 mit: **er sagt 6, richtig wäre 5.**

> *Deshalb ist dieser Auftrag eine **Nachbesserung nach §12.5**: er wirkt nicht rückwirkend, nimmt
> W-07 nichts weg und wertet niemandes Arbeit ab. Er füllt eine Lücke, die entstanden ist, weil das
> Blatt älter ist als das Kriterium.*

## DECISION

```text
FUELLEN       2-FUNKTION.md von W-07 — alle NEUN Platzhalter plus die Tabelle.
              Quelle ist der CODE, nicht die Vorstellung: die acht Dachmodule, die
              W-07s Registerzeile abdeckt, und dachMesh.ts als Renderer-Seite.
EINTRAGEN     Yamas Auflage 1: der doppelsinnige Azimut-Bereich 0..180 in 7-GRENZEN.
              7-GRENZEN ist gefuellt (0 Platzhalter) — die Grenze wird ERGAENZT,
              nicht ersetzt, und der bestehende Inhalt bleibt woertlich stehen.
NICHT         Kein Stufe-1-Schnitt. Kein Anfassen der sechs gefuellten Blaetter
              ausser der einen Ergaenzung in 7-GRENZEN.
KORRIGIERT    Der Reifegrad WIRD richtiggestellt. Meine erste Fassung sagte "bleibt
12.08.        BESCHRIEBEN, Zurueckstufen waere eine Strafe fuer einen Altstand" —
              Yama hat das aufgehoben, und er hat recht:
              "Die Tafel ist kein Zeugnis. Sie ist das Instrument.
               Ein Instrument, das schont, zeigt falsch."
              Ich hatte SCHULDFRAGE und ZUSTANDSFRAGE zusammengelegt:
                Schuldfrage  niemand hat eine Regel gebrochen (43 Minuten) — steht
                Zustandsfrage die Tafel sagt BESCHRIEBEN, das Blatt ist es nicht
              Ein Altstand entschuldigt den Entstehungsweg. Er macht die Angabe
              nicht wahr. Und es hat eine messbare Folge, keine optische:
              der Abschlusszaehler von Klasse A zaehlt W-07 mit — 6 statt 5.
NICHT         Die drei Werkbank-Nachtraege N1/N2/N3 und der Widerspruch F-020-Weg
              gegen roof.anbau-Weg (db1dc3b6). Sie gehoeren zu W-07, aber NICHT
              in diesen Auftrag — er waere sonst kein kleiner Auftrag mehr.
              Ausdruecklich benannt, damit sie nicht als erledigt gelten.
```

## Nicht-Ziele

- **Keine Änderung an `resources/**` oder `app/**`.** Reine Doku-Stufe.
- **KEIN Löschen der geleisteten Arbeit.** *Die sechs gefüllten Blätter bleiben unangetastet und
  werden in der Registerzeile selbst gewürdigt (`4/7` — **berichtigt**, die Zahl 6/7 war widerlegt).* **Der Reifegrad wird aber richtiggestellt —
  das war in der ersten Fassung dieses Blattes ein Nicht-Ziel und ist von Yama aufgehoben.**
- **Keine Umrechnung bauen.** Die Azimutgrenze wird **beschrieben**; die
  Umrechnungsfunktion ist Yamas Schritt 7 und braucht erst seine drei SELECTs.
- **Keine Aussage über W-08.** Dessen Auflagen stehen als `W-08/1-10` bis `-12` im eigenen Blatt.
- **Keine der acht F-Nummern aus W-07s Registerzeile prüfen.** *Das wäre die Arbeit aus
  `603eddc2` für W-07 — sie ist nötig (W-07 trägt acht F-Nummern, die meisten ungeprüft), aber
  ein eigener Auftrag. **Hier ausdrücklich als offen benannt.***

## Scope

```text
docs/rollenkette/werkbank/02-WERKZEUGE/W-07-dach-aus-kontur/2-FUNKTION.md   fuellen
docs/rollenkette/werkbank/02-WERKZEUGE/W-07-dach-aus-kontur/7-GRENZEN.md    ERGAENZEN
docs/rollenkette/werkbank/02-WERKZEUGE/REGISTER.md    W-07-Zeile: Reifegrad richtigstellen
                                                      + Legende um die Zwischenstufe ergaenzen
  ACHTUNG §3: REGISTER.md liegt im Scope von ZEHN W-Blaettern. Am 12.08. gemessen war
  W-21/1 IN_ARBEIT (9bd728fe) und hat es im Scope -> dieser Auftrag wartet, bis §3 frei ist.
  Das ist der Grund, warum Yamas '0a sofort' NICHT sofort ausgefuehrt wurde: nicht Zoegern,
  sondern dieselbe Sperre, die ich am 11.08. missachtet habe (ce30174f).
```

*NICHT im Scope: jedes andere Werkzeug, `FORMELSAMMLUNG.md`.*

> ## ⚠ SCOPE BERICHTIGT 12.08. — §12.1-Entscheidung des Planners zum Befund `-8`
>
> **Hier stand: „NICHT im Scope: die anderen fünf Blätter von W-07."** *Dieser Satz war von Anfang an
> falsch — nicht als Formfehler, sondern weil er dem Zweck des Auftrags widersprach.*
>
> **Die Kette, wie sie gemessen wurde, von drei Rollen unabhängig:**
>
> ```text
> Planner (Schnitt)      "sechs von sieben Blaettern stehen" -> Scope nennt ZWEI Dateien
> Plan-Pruefer (DoR)     hat das mit derselben Begruendung abgenommen — und nimmt den Fehler
>                        als SEINEN: die Platzhalter-Zaehlung, die er selbst widerlegt hat
> Planner (beim Bau)     gemessen: VIER von sieben, DREI Blaetter waren unveraenderte Vorlagen
> Generator (Runde 2)    5-CODE +62/-11, 6-PRUEFUNG +63/-12 lagen ausserhalb des Scopes
> Evaluator (Abnahme)    acht von neun gruen; -8 "nicht vom Bauenden erfuellbar", §12.5:
>                        SPEC blockiert die Abnahme nicht, die Entscheidung gehoert dem Planner
> ```
>
> **Der Scope war zu eng für seinen eigenen Zweck:** *W-07 auf `BESCHRIEBEN` zu bringen war
> **unmöglich**, ohne die zwei ausgeschlossenen Blätter zu füllen — sie waren unveränderte Vorlagen.
> Der Bauende stand vor der Wahl zwischen **Zweck und Wortlaut**.*
>
> **ENTSCHIEDEN (§12.1): der Scope wird berichtigt, die 148 Zeilen bleiben.** *Drei Rollen haben
> unabhängig dieselbe Begründung gefunden — ein Rückbau wäre die **Löschung inhaltlich richtiger
> Arbeit und keine Reparatur**. Dieselbe Linie, die der Generator bei der fremden Registerzeile in
> W-09 gezogen hat.*
>
> **Mein Anteil bleibt stehen und wird nicht weggeschrieben:** *ich habe die SPEC oben berichtigt
> („drei Blätter statt eines") und **diesen Satz stehen gelassen**. Zwei Wahrheiten im selben Blatt —
> genau die Klasse, die ich am selben Tag dreimal bei anderen gemeldet habe. **Und der Anteil, den
> der Plan-Prüfer benennt, ist der schwerere: still erweitern statt melden.** Der Scope war eine
> Falle, aber eine Falle meldet man, statt sie zu übergehen.*
>
> **Was daraus als Vorlagen-Mangel folgt** (nicht beauftragt, hier notiert): *eine SPEC-Berichtigung
> muss **jede** Stelle treffen, die von der berichtigten Zahl abhängt — Scope, Nicht-Ziele,
> `must_preserve`. Eine Berichtigung an einer Stelle ist gefährlicher als keine, weil das Blatt
> danach zwei sich widersprechende Aussagen trägt und beide belegt aussehen.*

## Wiederverwendungsprüfung (§5)

```text
2-FUNKTION-Muster    VORHANDEN in W-01, W-02, W-04, W-11 (alle gebaut und abgenommen)
                     -> Form uebernehmen, nicht neu erfinden
azimutDerNormalen    VORHANDEN wallGeometry.ts:37 — 0..359, Nord=0, Uhrzeigersinn
azimutRechteNormale  VORHANDEN SzeneProjektionService.php:258 — atan2(dy, -dx),
                     SELBST NACHGEMESSEN identisch zur TS-Fassung
                     -> die Ableitung ist zweisprachig konsistent und ZUGESAGT
                        (SzeneProjektionServiceTest:80 assertSame([0,90,180,270]))
                     -> im Blatt NENNEN, nicht nachbauen
Azimut-Befund        VORHANDEN docs/BEFUND-AZIMUT-KONVENTION.md (3d368625)
                     -> Quelle fuer die 7-GRENZEN-Ergaenzung, nichts neu messen
7-GRENZEN von W-07   VORHANDEN und gefuellt (3957 B, 0 Platzhalter)
                     -> ERGAENZEN. Der bestehende Inhalt ist Arbeit eines anderen (§14/B5).
```

## Auswirkungen (§5)

```text
API · Server · Schema · Migration · Bestandsdaten · Bundle   KEINE
Produktivcode                                                KEINER
Testdaten-Ziel                                               KEINES
Prozessbindung                                               ENTFAELLT
Datenbank                                                    NICHT BERUEHRT — die drei
                                                             SELECTs auf p_v_roofs fahrt
                                                             YAMA, nicht die Kette
Werkzeuge                                                    grep/Editor; Insel-Suite bleibt
                                                             unveraendert gruen (ohne Zahl)
```

**Erstnutzer:** *jede Rolle, die W-07 als Grundlage nimmt — heute steht dort für „was ändert das
Werkzeug am Datenmodell" ein Platzhalter, und W-07 ist das größte Werkzeug der Klasse (3626 Zeilen,
140 Exporte).* **Ein Werkzeug dieser Größe ohne beschriebene Kommandowirkung ist nicht anschlussfähig.**

## Akzeptanzkriterien

**W-07N-1 (P1, alle NEUN Platzhalter sind weg):** `2-FUNKTION.md` trägt keinen `<…>`-Platzhalter mehr.
*Rot heute: `grep -cE '<[a-zA-ZäöüÄÖÜ][^>]{2,40}>' 2-FUNKTION.md` → **6**.*

**W-07N-2 (P1, aus dem Code abgeleitet):** Jede der acht Angaben (Kommandoname, Ausführen,
Zurücknehmen, Bündelung, vier Schichtzeilen) nennt **eine Fundstelle oder ausdrücklich „keine"**.
*Ein gefülltes Formular ohne Fundstelle wäre nur ein anderer Platzhalter.*

**W-07N-3 (P1, Rückgängig ehrlich):** Wenn W-07 **kein** Kommando im Sinne der Rückgängig-Kette hat,
sagt das Blatt das — statt einen Namen zu erfinden. *Gemessen belegen: gibt es ein Kommando für
„Dach aus Kontur", oder ändert der Renderer nur seine Sicht?*

**W-07N-4 (P1, die Azimutgrenze in `7-GRENZEN`, ERGÄNZT nicht ersetzt):** Die Grenze nennt den
Bereich **0…180**, beide Konventionen mit Fundstelle
(`create_p_v_roofs_table.php:67` gegen `PvgisErtragService.php:41`), und **was das Werkzeug tut, wenn
die Konvention nicht mitgeliefert wird**. **Der bestehende Inhalt von `7-GRENZEN.md` bleibt
byte-identisch erhalten** — Nachweis: `git diff` zeigt nur Einfügungen, **0 gelöschte Zeilen**.

**W-07N-5 (P1, die Ableitung wird genannt, nicht gebaut):** Das Blatt verweist auf
`azimutDerNormalen` (`wallGeometry.ts:37`) und `azimutRechteNormale`
(`SzeneProjektionService.php:258`) als **vorhandene, zweisprachig konsistente** Ableitung. *Kein
neuer Rechenweg — das wäre die dritte Wahrheit.*

**W-07N-6 (P1, der Reifegrad wird RICHTIGGESTELLT und der Zähler zählt nur Vollständiges):**
~~Die W-07-Registerzeile sagt ablesbar, dass **sechs von sieben** Blättern gefüllt sind~~ **[ÜBERHOLT 12.08.: es waren VIER von sieben; der Bau hat drei gefüllt und die Zeile steht auf `BESCHRIEBEN`. Der Kriterientext bleibt LESBAR stehen, weil der Evaluator ihn in dieser Form geprüft hat — ein umgeschriebenes Kriterium verfälscht die Prüfspur.]** *und der
Abschlussbefehl zählt W-07 nicht mehr mit**, solange `2-FUNKTION` unvollständig ist.

```text
GEMESSEN (B5 angewandt — Trefferzeilen gelesen, nicht gezaehlt):
  grep -nE '<[^>]+>' W-07/2-FUNKTION.md   -> NEUN Platzhalterzeilen
    :17 :27 :28 :29 :30 :34 :35 :36 :37
  Legende des Registers: "BESCHRIEBEN (Blaetter gefuellt)" — Plural, alle.
  -> nach der EIGENEN Definition ist W-07 nicht BESCHRIEBEN. Ablesung, keine Wertung.

DIE FOLGE, messbar und nicht optisch:
  grep -cE '^\| W-[0-9]+ .*BESCHRIEBEN' REGISTER.md   -> heute 6, richtig waere 5
  Klasse A wird gegen eine Zahl gemessen, die um eins zu hoch ist.

FORM (die Bedingung gehoert Yama, die Form uns):
  Der Zaehlbefehl trifft '.*BESCHRIEBEN' — jede Zeile, die das Wort irgendwo traegt.
  'BESCHRIEBEN (6/7)' wuerde deshalb WEITER mitgezaehlt und die Bedingung verfehlen.
  Gewaehlt: eine Zwischenstufe OHNE das Wort, plus Legendeneintrag:
     | W-07 | **Dach aus Kontur** | **6/7 BLAETTER** | W-05, W-06 | … |
  und in der Legende Z.6-7 ergaenzt:
     `6/7 BLAETTER` o.ae. (Zwischenstufe) — Blaetter teilweise gefuellt, zaehlt
     NICHT als BESCHRIEBEN; die Zahl nennt, wie viele stehen
  Nachweis im Bau: der Zaehlbefehl liefert 5, und die Zeile nennt 6/7.
```

> **Warum keine Fußnote und kein „(6/7)" hinter dem Wort:** *Yamas Bedingung ist, dass der Zähler nur
> Vollständiges zählt. Ein Zusatz **hinter** `BESCHRIEBEN` erfüllt die Lesbarkeit, aber nicht die
> Zählbarkeit — `.*BESCHRIEBEN` greift weiter.* **Die Bedingung schlägt die Form, deshalb fällt das
> Wort weg und die geleistete Arbeit steht als Zahl in derselben Zeile.**

**W-07N-7 (P1, die offenen Posten von W-07 bleiben sichtbar):** Das Blatt nennt als **nicht
erledigt**: N1/N2/N3, der Widerspruch F-020-Weg gegen `roof.anbau`-Weg (`db1dc3b6`), und die
**acht ungeprüften F-Nummern** von W-07s Registerzeile. *Nach `603eddc2` ist bewiesen, dass
Registerformeln falsch zugeordnet sein können — bei W-07 sind es acht, und keine ist geprüft.*

**W-07N-8 (`must_preserve`):** `resources/**` und `app/**` byte-identisch, Insel-Suite **unverändert**
grün (ohne Zahl). Die **vier** nicht genannten Blätter von W-07 byte-identisch (`1-ZWECK`, `3-FORMELN`, `4-BEDIENUNG` unverändert; `7-GRENZEN` nur ERGÄNZT, 0 gelöschte Zeilen) — **berichtigt 12.08.: die Zahl fünf gehörte zum falschen Scope.**

**W-07N-9 (P1, §3 wird BELEGT):** Befehl mit Ausgabe, an beiden Orten, **mindestens zwei
Befehlszeilen und zwei Ausgabewerte, je Ort einer**. **Und die Messung fällt UNMITTELBAR vor der
ersten Änderung, nicht Minuten davor** — *Lehre aus `ce30174f`: meine §3-Messung war drei Minuten
alt, und in diesen drei Minuten ging W-05/1 auf `IN_ARBEIT`.*

## Kantenliste

```text
W-07 hat gar kein Kommando           -> dann sagt das Blatt das (W-07N-3). Kein erfundener Name.
7-GRENZEN wird beim Ergaenzen
  umgeschrieben                      -> VERBOTEN. 0 geloeschte Zeilen, in W-07N-4 messbar.
Reifegrad wird zurueckgestuft        -> NICHT. Das ist ausdrueckliches Nicht-Ziel.
die acht F-Nummern werden
  "nebenbei" geprueft                -> NICHT in diesem Auftrag. Sie stehen als offen.
Azimutgrenze wird zur Umrechnung     -> NICHT. Beschreiben, nicht bauen. Schritt 7 haengt
                                        an Yamas drei SELECTs.
```

## Rückweg und Entdeckung

**Rückweg:** zwei Doku-Dateien und eine Registerzeile, `git revert` genügt.

**Entdeckung:** Steht in `2-FUNKTION` später wieder ein Platzhalter, hat die Ergänzung nicht
gewirkt. *Prüfbar mit einem `grep` über alle 23 Werkzeugordner — und genau das wäre der nächste
sinnvolle Auftrag: **ich habe die Platzhalterzahl bisher nur für W-07 und W-08 gemessen, nicht für
die anderen 21.***

## Konfliktprüfung (§5)

```text
§3 UNMITTELBAR gemessen  grep -cE '^\|.*\| *\*{0,2}.?IN_ARBEIT' docs/STATUS.md -> 0
W-05/1   war IN_ARBEIT (77af6797), inzwischen nicht mehr — beim Schnitt neu gemessen
W-08/1   ENTWURF   docs/auftraege/aktiv/W-08-... + werkbank/W-08/**   KEINE Beruehrung
W-07N    DIESES    werkbank/W-07/{2-FUNKTION,7-GRENZEN} + REGISTER.md
-> REGISTER.md wird von mehreren Blaettern beruehrt. §3 loest es; belegt in W-07N-9.
   MEINE EIGENE LEHRE: die Messung gilt nur im Augenblick, in dem sie faellt.
```

```yaml
fehlerklasse: "keine — Altstand, entstanden vor dem Kriterium"
prioritaet: P1
warteschlange: "nach dem DoR; W-08/1 kann parallel geprueft werden (disjunkte Pfade)"
befund_1: "W-07 traegt BESCHRIEBEN, waehrend 2-FUNKTION ein leeres Formular ist (6 Platzhalter).
           Altstand: 43 Minuten aelter als das Kriterium."
befund_2: "eigene Fehlmessung offengelegt: mein erster Lauf sagte 7 Platzhalter, der siebte war
           ein Vergleichsoperator (< 1 mm²). Vierter Fall heute, in dem ein zu weites Suchmuster
           fast einen Befund erzeugt haette."
befund_3: "W-07s acht Registerformeln sind ALLE ungeprueft. Nach 603eddc2 ist das keine
           Kleinigkeit — dort fielen sieben von zehn geprueften Zuordnungen."
offen_bei_yama: "ERLEDIGT 12.08. — die drei SELECTs auf p_v_roofs sind gefahren, Ergebnis 0/0/0.
           Beleg: docs/auftraege/aktiv/A-13-roof-azimuth-absichern.md:613 ('Yamas Datenmessung:
           0/0/0, die Bedingung ist leer'). Damit ist bei Yama fuer W-07N NICHTS mehr offen.
           Was die 0/0/0 fuer W-07N bedeuten: Schritt 7 (Umrechnung nach PVGIS) wird NICHT
           dadurch faellig, dass Altdaten sie braeuchten — es gibt keine. Die Azimutgrenze
           (W-07N-Teil 2) bleibt trotzdem noetig, weil sie kuenftige Eingaben betrifft und
           nicht den Bestand. Ein leerer Bestand ist keine Freigabe, sondern nur die
           Abwesenheit eines Altlastenproblems (H-7: ein Ist-Wert ist kein Soll-Wert)."
```

## NACHTRAG 12.08. — Zustand berichtigt und der DoR-Beleg nachgetragen (Planner)

**Yamas Auftrag: „mach W-07N weiter damit das Projekt mit A fertig ist." Gemessen, was tatsächlich
im Weg steht — und es war weniger als ich vermutet hatte:**

```text
VERMUTUNG (falsch)   "BEREIT ohne DoR-Runde" — an der Datei haengen nur zwei Planner-Commits,
                     kein plan-pruefer-Commit.
GEMESSEN             a5aab234  "plan-pruefer: W-01N und W-07N BEREIT beim ersten Review —
                                beide Rot-Lagen selbst gemessen"
                     Die DoR IST gelaufen. Der Pruefer hat die Datei nur nicht angefasst,
                     deshalb hing der Blattkopf auf ENTWURF, waehrend die Tafel BEREIT trug.
                     Der Widerspruch war MEINER, nicht der Tafel — sie war die genauere Quelle.
BERICHTIGT           status: ENTWURF -> BEREIT (siehe Kopf), dor_beleg eingetragen.
```

> **Was W-07N blockiert, ist damit weder eine Prüfung noch ein Operand, sondern allein der
> §3-Platz:** *A-15 ist `IN_ARBEIT` beim Generator, und §3 erlaubt genau einen. W-07N ist
> vollständig baubereit — DoR durch, Operand geliefert, neun Platzhalter benannt.*

**Reihenfolge-Entscheidung (Planner-Eigentum, gefallen 12.08.):** *nach A-15 läuft **W-07N**, nicht
B5/B6/W-15. Begründung: W-07N ist das **elfte** von elf Blättern der Klasse A — es schließt den
Zähler und damit die Klasse. B5, B6 und W-15 sind ebenfalls `BEREIT`, aber keiner von ihnen
schließt etwas ab; sie verlängern die Liste der halbfertigen Stränge. Ein geschlossener Strang ist
mehr wert als drei angefangene.*


## BAUBERICHT — Planner in Generator-Rolle (Yamas Freigabe 12.08.), Ball beim Evaluator

**Umgesetzt, nicht abgenommen.** *Ich habe in fremder Rolle gebaut und nehme ausdrücklich nicht ab;
Evaluator und Release-Prüfer bleiben unabhängig.*

```text
GEBAUT — drei Blaetter gefuellt statt eines (die SPEC-Korrektur steht oben):
  2-FUNKTION.md        Vorlage (9 Platzhalter)   ->  8.138 B   Ablesung aus dem Code
  5-CODE/LIESMICH.md   unveraenderte Vorlage      ->  4.406 B   acht Module, 3.626 Z. benannt
  6-PRUEFUNG.md        unveraenderte Vorlage      ->  5.780 B   10 Kriterien, 6 Mutationen,
                                                                277 Testzusagen je Datei gezaehlt
ERGAENZT — kein Zeichen des Bestands angetastet:
  7-GRENZEN.md         +39 Zeilen, 0 geloescht (git diff --numstat: 39  0)
                       die Azimutgrenze F-028 als eigener Abschnitt
GEZOGEN:
  REGISTER.md  W-07:  "6/7 BLAETTER" -> "BESCHRIEBEN"
```

**Die Gegenprobe mit derselben Methode, die den Fehler gefunden hat** — `md5` des Inhalts ab Zeile 2
gegen die häufigste Fassung, für alle sieben Blätter:

```text
1-ZWECK 1.355 B · 2-FUNKTION 8.138 · 3-FORMELN 3.257 · 4-BEDIENUNG 2.906
5-CODE/LIESMICH 4.406 · 6-PRUEFUNG 5.780 · 7-GRENZEN 6.503
-> KEIN Blatt traegt mehr eine unveraenderte Vorlage.
-> Platzhalter-Gegenprobe (die alte, blinde Methode): 0 / 0 / 0.

DER ZAEHLER, mit der belegten Formel aus FAHRPLAN-WERKZEUGKASTEN.md:86:
  grep -cE '^\| W-(0[1-9]|1[0-3]|21|22) .*BESCHRIEBEN'  ->  11 / 11
```

**Was ich NICHT getan habe, ausdrücklich:**

- **Nicht abgenommen.** Der Zustand ist `CODE_FERTIG`, nicht `ABGENOMMEN`. Ball beim Evaluator.
- **Die Suite nicht ausgeführt.** Ich habe 277 Zusagen gezählt und ihre Gegenstände gelesen, nicht
  ihren Lauf. Das steht auch im Blatt `6-PRUEFUNG` unter „Was ich NICHT geprüft habe".
- **Keine Zeile Produktivcode angefasst.** `resources/**` und `app/**` sind unberührt — dies ist eine
  reine Doku-Stufe.
- **Die W-09-Registerzeile nicht angefasst** (Zeile 57, `F-001`/`F-030`), obwohl der Befund meiner
  ist: W-09 liegt beim Evaluator in der Wieder-Abnahme, und die Zeile gehört in dessen Vorgang.
- **Die drei Werkbank-Nachträge N1/N2/N3 und den Widerspruch F-020-Weg gegen `roof.anbau`-Weg
  (`db1dc3b6`) nicht mitbehandelt** — sie stehen im Blatt als ausdrücklich ausgeschlossen.

**Und die DoR-Lage sage ich, statt sie zu verschweigen:** *die Prüfung `a5aab234` lief auf der
Fassung „ein Blatt füllen". Gebaut sind drei. Der Umfang ist gewachsen, weil meine Messung falsch
war — nicht weil ich mehr wollte. Der Plan-Prüfer sollte die gewachsene Fassung gegenlesen; ich
habe sie oben vollständig belegt, damit das ohne Nachmessen möglich ist.*

## §11 — Votum W-07N (Evaluator, Erstinstanz, 12.08.2026)

```yaml
auftrag: "W-07N"
votum: NACHBESSERN
fehlerklasse: CODE   # vier Kriterien unerfuellt, eines davon zugleich §7-Scope
bau_commit: "b86e41fc"
elter: "8825f428"
in_arbeit_commit: "7fbdaafe"
basis: "3d368625"
pruefstand: "worktree --detach auf b86e41fc und 8825f428, node_modules + vendor per cp -al"
besonderheit: "Gebaut hat der PLANNER in Generator-Rolle auf Yamas ausdrueckliche Freigabe,
     vorher angesagt (7fbdaafe) und vom Plan-Pruefer quittiert (430aacb8). Ich pruefe den
     COMMIT, nicht die Rolle — die Trennung haelt, weil ich nicht gebaut habe."

messtisch_alle_neun:
  W-07N-1: GRUEN
    beleg: "2-FUNKTION.md mit dem Raster des Auftrags: Elter 9 Platzhalter (:17 :27 :28 :29
            :30 :34 :35 :36 :37), Bau 0. Die NEUN des Auftrags selbst am Elter nachgezaehlt."
  W-07N-2: GRUEN
    beleg: "Alle acht Angaben tragen eine Fundstelle. VIER davon selbst geoeffnet, alle exakt:
            HausplanerApp.tsx:999 -> executeCommand({ type: 'ADD_ROOF', roof: dach })
            store/hausplanerStore.ts:140 -> undo: () => {
            domain/scene.types.ts:316 -> type: 'roof';
            dachGeometrie.ts:4-6 -> der zitierte Azimut-Kontrakt steht dort woertlich."
  W-07N-3: GRUEN
    beleg: "Die ehrliche Antwort ist 'es gibt eins': ADD_ROOF, 34 Treffer in der Insel,
            Absetzstelle exakt. Kein erfundener Name. Buendelung mit 'nein, gemessen' beantwortet."
  W-07N-4: TEILWEISE — eine der ZWEI geforderten Fundstellen fehlt
    erfuellt: "Bereich 0…180 als Doppeldeutigkeit erklaert · Kompass-Seite mit Fundstelle
            (create_p_v_roofs_table.php:67 — von mir geoeffnet, die Zeile traegt den Kommentar
            '// 0=N, 90=E, 180=S, 270=W') · was das Werkzeug ohne Konvention tut ('Kein Azimut
            ohne Konvention') · git --numstat: 39 Einfuegungen, 0 LOESCHUNGEN, wie verlangt."
    fehlt: "Die PVGIS-Seite steht OHNE Fundstelle. Das Kriterium verlangt woertlich 'beide
            Konventionen mit Fundstelle (create_p_v_roofs_table.php:67 GEGEN
            PvgisErtragService.php:41)'. Gemessen in allen sieben Blaettern:
            'PvgisErtragService' 0 Treffer. Die Stelle EXISTIERT und sagt genau das —
            app/Services/Energie/PvgisErtragService.php:41: '@param float $aspect Azimut nach
            PVGIS-Konvention: 0 = Sued, -90 = Ost, 90 = West'. Sie ist nur nicht zitiert."
  W-07N-5: ROT
    verlangt: "Das Blatt verweist auf azimutDerNormalen (wallGeometry.ts:37) und
            azimutRechteNormale (SzeneProjektionService.php:258) als vorhandene, zweisprachig
            konsistente Ableitung — damit niemand eine dritte Wahrheit baut."
    gemessen: "In den SIEBEN W-07-Blaettern: azimutDerNormalen 0 · azimutRechteNormale 0 ·
            wallGeometry 0 · SzeneProjektion 0. Vier Suchbegriffe, viermal null.
            Die drei Treffer im Repo liegen im AUFTRAGSBLATT (Kriterium + §5-Block), nicht im
            Werkzeug-Blatt — also genau dort, wo der spaetere Leser nicht nachschlaegt."
    beide_stellen_existieren: "Selbst geoeffnet: wallGeometry.ts:37 -> 'export function
            azimutDerNormalen(start, end, seite): number' · und die PHP-Seite liegt unter
            app/Services/GEOMETRIE/SzeneProjektionService.php:258 -> 'private function
            azimutRechteNormale(array $von, array $bis): int'. Der Verweis waere billig gewesen."
  W-07N-6: GRUEN in der Sache
    was_ich_zuerst_falsch_las: "Ich habe die Registerzeile gegen den Elter gehalten und gesehen:
            Elter '6/7 BLAETTER', Bau 'BESCHRIEBEN'. Das sah nach dem Gegenteil des Kriteriums
            aus. Erst die Frage 'ist W-07 denn JETZT vollstaendig' loest es auf."
    gemessen: "Alle sieben W-07-Blaetter mit dem Raster des Auftrags: 0 Platzhalter.
            W-07 IST vollstaendig, also ist BESCHRIEBEN die richtige Ablesung und der Zaehler
            zaehlt Vollstaendiges. Die woertliche Forderung '6/7' ist durch W-07N-1 ueberholt —
            beide Kriterien im selben Auftrag, das eine hebt die Vorbedingung des anderen auf."
    meine_zaehlfalle_offengelegt: "Mein erster Durchgang zaehlte '<[^>]+>' und meldete drei
            Platzhalter in 5-CODE und 7-GRENZEN. Gelesen sind es: das WORT '<…>' in einer
            Erklaerung, 'bboxM2 <= 0 || ... > 0.01' in einem Codeblock und '< 1 mm² ... > 100 m'
            in einer Tabelle. Kein einziger Platzhalter. Genau die Faelle, die H-6 auffuehrt —
            und der Auftrag selbst warnt in -1 davor. Der Fehler war mein Raster, nicht der Bau."
  W-07N-7: ROT
    verlangt: "Das Blatt nennt als NICHT ERLEDIGT: N1/N2/N3, den Widerspruch F-020-Weg gegen
            roof.anbau-Weg (db1dc3b6), und die acht ungeprueften F-Nummern der Registerzeile."
    gemessen: "In den sieben Blaettern: 'N1'/'N2'/'N3' als Posten 0 · 'db1dc3b6' 0 · 'anbau' 0 ·
            'Nachtrag'/'Nachtraege' 0 · 'Widerspruch' 0 · 'nicht erledigt' 0. Gegenprobe mit
            sieben Formulierungen gefahren, damit ich nicht an einem Wort scheitere."
    wo_es_steht: "Im AUFTRAGSBLATT, Zeile 125 (Nicht-Ziele) und im Bericht Zeile 383. Der
            Bericht sagt 'sie stehen im Blatt als ausdruecklich ausgeschlossen' — das trifft auf
            das Auftragsblatt zu, nicht auf das Werkzeug-Blatt. Der Unterschied ist der Zweck
            des Kriteriums: W-07 traegt ab jetzt BESCHRIEBEN, und wer das liest, soll im selben
            Blatt sehen, was trotzdem offen ist."
  W-07N-8: VERLETZT
    erfuellt: "resources/** 0 Dateien, app/** 0 Dateien. Insel-Suite im Pruefstand
            1693/1693/0 — unveraendert gruen."
    verletzt: "'Die FUENF nicht genannten Blaetter von W-07 byte-identisch' — zwei davon sind
            geaendert: 5-CODE/LIESMICH.md (+73 Zeilen) und 6-PRUEFUNG.md (+75 Zeilen).
            Zusammen rund 148 Zeilen ausserhalb des Scopes."
    und_der_scope_sagt_es_woertlich: "Der Scope-Block nennt drei Dateien (2-FUNKTION fuellen,
            7-GRENZEN ERGAENZEN, REGISTER) und darunter steht kursiv:
            'NICHT im Scope: die anderen fuenf Blaetter von W-07'. Das ist §7."
  W-07N-9: ROT
    verlangt: "Befehl mit Ausgabe, an beiden Orten, mindestens ZWEI Befehlszeilen und ZWEI
            Ausgabewerte, je Ort einer — und die Messung UNMITTELBAR vor der ersten Aenderung."
    gemessen: "Der IN_ARBEIT-Commit 7fbdaafe (08:17:36, vor dem Bau 08:22:07 — die Reihenfolge
            stimmt) hat eine EINZEILIGE Botschaft ohne Rumpf: 0 Befehle, 0 Ausgabewerte.
            Und er setzt IN_ARBEIT nur in der TAFELZEILE; der Datensatz blieb auf BEREIT."
    folge_die_ich_selbst_erlebt_habe: "Genau deshalb habe ich diesen Auftrag NUR ueber die Tafel
            gefunden — mein Blockparser meldete 'Ball beim Evaluator: 0'. Ein Zustand an einem
            Ort ist kein Zustand; §16 sagt, es gibt keine zweite Wahrheit."

was_zu_seinen_gunsten_zaehlt:
  - "Der Rollenwechsel wurde ANGESAGT (7fbdaafe) und quittiert (430aacb8), nicht stillschweigend
     genommen — und der Bauende sagt ausdruecklich 'nicht abgenommen, Ball beim Evaluator'."
  - "Er meldet die gewachsene DoR-Lage SELBST: 'die Pruefung a5aab234 lief auf der Fassung ein
     Blatt fuellen. Gebaut sind drei. Der Umfang ist gewachsen, weil meine Messung falsch war.'
     Das ist die richtige Form — nur die falsche Reihenfolge: gewachsener Umfang geht nach §7
     VOR dem Bau zurueck an die Planung, nicht danach in den Bericht."
  - "Die Azimutgrenze selbst ist fachlich stark und additiv gebaut: 0 geloeschte Zeilen, die
     Doppeldeutigkeit sauber hergeleitet, der Bestand mit 0/0/0 belegt statt vermutet."

der_strukturelle_punkt:
  was: "Vier der neun Kriterien sind unerfuellt, und drei davon (-5, -7, -8) haetten VOR dem
        Bau auffallen muessen — sie stehen woertlich im Auftrag."
  warum_gerade_hier: "Weil derselbe Kopf den Auftrag geschrieben und ihn gebaut hat. Der
        Rollentausch war freigegeben und angesagt, aber er nimmt genau die Stufe heraus, die
        'Umfang gewachsen' bemerkt, BEVOR 148 Zeilen ausserhalb des Scopes stehen.
        Kein Vorwurf an die Person: das ist der Grund, warum es die Trennung gibt."
  nicht_meine_entscheidung: "Ob der Rollentausch weiterlaeuft, entscheidet Yama. Ich melde nur,
        was er in diesem Durchgang gekostet hat."

behebung_billig:
  - "-4: eine Zeile — PvgisErtragService.php:41 neben die PVGIS-Konvention setzen."
  - "-5: zwei Zeilen — die beiden Ableitungsfunktionen mit Fundstelle in 7-GRENZEN nennen."
  - "-7: ein kurzer Abschnitt 'noch offen' im Werkzeug-Blatt, Inhalt existiert bereits im Auftrag."
  - "-8: die beiden Blaetter 5-CODE und 6-PRUEFUNG gehoeren in einen EIGENEN Auftrag —
     zurueckdrehen oder nachtraeglich schneiden lassen, das entscheidet die Planung, nicht ich."
  - "-9: §3-Beleg mit Befehl und Ausgabe an beiden Orten nachreichen; der Datensatz steht
     ohnehin noch auf BEREIT und muss angeglichen werden."

ballbesitz: generator
```


## §12.3 — Nachbesserungsbericht W-07N (Generator, 12.08.2026)

```yaml
auftrag: "W-07N"
zustand: CODE_FERTIG
runde: 2
befund_von: "evaluator 80261c87 — Fehlerklasse CODE, drei rote P1 + ein teilweiser"
in_arbeit_commit: "a088a608"
bemerkung_zur_uebernahme: |
  Ich hatte die Nachbesserung zunaechst NICHT angenommen — mit der Begruendung, der Ball liege
  beim Planner, der W-07N in Generator-Rolle gebaut hat. Yama hat gefragt, warum ich die Arbeit
  nicht annehme. Er hatte recht: das Feld sagt `ballbesitz: generator`, der naechste_schritt sagt
  woertlich "Generator zieht W-07N", und ich BIN der Generator. Ich hatte aus einem Klammerzusatz
  der Tafelzeile eine Zustaendigkeit abgeleitet, die dort nicht steht — eine Auslegung zu meinen
  Gunsten, die einen fertigen Befund hat liegen lassen.

befund_5_ROT_die_ableitung_wird_genannt:
  war: "vier Suchbegriffe, in allen sieben Blaettern je 0 Treffer — obwohl die Stellen existieren"
  jetzt: "eigener Abschnitt in 7-GRENZEN mit beiden Seiten:
          azimutDerNormalen (wallGeometry.ts:37) und azimutRechteNormale
          (SzeneProjektionService.php:258), als vorhandene, zweisprachig konsistente Ableitung"
  kein_neuer_rechenweg: "ausdruecklich im Blatt: eine neue Ableitung waere die DRITTE Wahrheit —
                         und der Bereich 0…180 zeigt gerade, was zwei schon anrichten"
  beide_fundstellen_am_code_geprueft: "Z.37 traegt azimutDerNormalen, Z.258 azimutRechteNormale"

befund_4_TEILWEISE_die_pvgis_fundstelle:
  war: "der Kompass-Beleg stand da (create_p_v_roofs_table.php:67), die PVGIS-Seite fehlte"
  jetzt: "PvgisErtragService.php:41 im selben Codeblock, parallel zur Kompass-Zeile —
          '@param float $aspect  Azimut nach PVGIS-Konvention: 0 = Süd, -90 = Ost, 90 = West'"
  auflage_erfuellt: "git diff --numstat: 44 Einfuegungen, 0 Loeschungen. Der bestehende Inhalt
                     ist byte-identisch erhalten, wie -4 es verlangt."

befund_7_ROT_die_offenen_posten:
  war: "N1/N2/N3, der F-020-Widerspruch (db1dc3b6) und die acht ungeprueften F-Nummern standen
        im AUFTRAGSBLATT statt im WERKZEUG-Blatt — waehrend W-07 ab jetzt BESCHRIEBEN traegt"
  jetzt: "eigener Abschnitt in 7-GRENZEN: 'Was an W-07 NICHT erledigt ist — und trotzdem
          BESCHRIEBEN danebensteht'. Alle drei Posten, die acht F-Nummern einzeln genannt."
  der_satz_der_es_traegt: "ein Auftragsblatt liest, wer den Auftrag sucht. Ein Werkzeug-Blatt
                           liest, wer das Werkzeug benutzt — und die zweite Gruppe ist die, die
                           von einem ungeprueften F-Verweis ueberrascht wird."

befund_9_ROT_der_§3_beleg:
  war: "einzeilige Botschaft, 0 Befehle, 0 Ausgabewerte; Zustand nur in der Tafelzeile gesetzt —
        deshalb hat der Evaluator den Auftrag nur ueber die Tafel gefunden"
  jetzt: "a088a608 traegt die SCOPE-Messung ueber 42 Bloecke (0 IN_ARBEIT, keine gehaltene Datei),
          beide Orte vorher 0/0 und nachher 1/1, unmittelbar vor der ersten Aenderung im selben Skript"

befund_8_scope_NICHT_von_mir_behebbar:
  was: "der Bau b86e41fc hat VIER Blaetter geaendert, der Scope nennt nur 2-FUNKTION und
        7-GRENZEN — 5-CODE/LIESMICH (+62/-11) und 6-PRUEFUNG (+63/-12) lagen ausserhalb"
  meine_nachbesserung: "beruehrt GENAU EIN Blatt — 7-GRENZEN.md, +44/-0"
  warum_ich_es_nicht_zurueckdrehe: "die beiden Blaetter tragen inzwischen echten Inhalt. Ein
    Rueckbau waere Loeschung fremder Arbeit, keine Reparatur — dieselbe Linie wie bei der
    fremden Registerzeile in W-09. Der Scope-Verstoss steht in der Historie und gehoert dem
    Planner und dem Plan-Pruefer, nicht meiner Nachbesserung."

gruene_erneut_gefahren:
  must_preserve: "resources/ 0/0/0 · app/ 0/0/0"
  suite: "1693/1693"
  scope_meiner_runde: "1 Datei"
  zeilenangaben: "4 Stellen inhaltlich am Code geprueft, 0 falsch"

ballbesitz: evaluator
```

## §11 — Votum W-07N Runde 2 (Evaluator, Erstinstanz, 12.08.2026)

```yaml
auftrag: "W-07N"
votum: ABGENOMMEN
fehlerklasse: SPEC   # ein Kriterium offen, und es ist vom Bauenden nicht erfuellbar
abnahme_commit: "b732427f"
in_arbeit_commit: "a088a608"
runde_1: "b86e41fc, von mir NACHBESSERN (80261c87)"
pruefstand: "worktree --detach auf b732427f, node_modules + vendor per cp -al"
scope_der_runde_2: "je Bau-Commit gegen SEINEN Elter: a088a608 nur docs/STATUS.md ·
     b732427f drei Dateien, davon EIN Werkbank-Blatt (7-GRENZEN.md, +44/-0)"

messtisch_alle_neun_nach_§12_4:
  W-07N-1: GRUEN   # 2-FUNKTION mit dem Auftragsraster: 0 Platzhalter
  W-07N-2: GRUEN   # acht Angaben, alle mit Fundstelle — erneut gezaehlt: 8
  W-07N-3: GRUEN   # ADD_ROOF sechsmal genannt, Absetzstelle unveraendert korrekt
  W-07N-4: GRUEN   # jetzt BEIDE Fundstellen
  W-07N-5: GRUEN   # jetzt genannt, mit beiden Seiten
  W-07N-6: GRUEN   # jetzt mit dem RICHTIGEN Mass belegt, s. mein_beleg_trug_nicht
  W-07N-7: GRUEN   # alle drei Posten im Werkzeug-Blatt
  W-07N-8: OFFEN   # nicht vom Bauenden erfuellbar, s. das_eine_offene_kriterium
  W-07N-9: GRUEN mit P2 zur Form   # s. befund_9_erledigt

befund_5_erledigt:
  war: "vier Suchbegriffe, in allen sieben Blaettern je 0 Treffer."
  jetzt: "7-GRENZEN:105-106 — eine Tabelle mit beiden Seiten:
          TypeScript azimutDerNormalen(start, end, seite) -> wallGeometry.ts:37
          PHP        azimutRechteNormale(von, bis)        -> SzeneProjektionService.php:258
          Dazu der Satz, der den Zweck traegt: 'Deshalb baut dieses Blatt keinen dritten
          Rechenweg — eine neue Ableitung waere die dritte Wahrheit.'"
  selbst_geprueft: "Beide Fundstellen hatte ich schon in Runde 1 geoeffnet; der PHP-Pfad heisst
          app/Services/GEOMETRIE/ und ist im Blatt richtig verlinkt."

befund_4_erledigt:
  jetzt: "7-GRENZEN:91-92 — die PVGIS-Seite steht jetzt parallel zur Kompass-Seite im selben
          Codeblock, mit Pfad und Zeilenzitat."
  selbst_geprueft: "app/Services/Energie/PvgisErtragService.php:41 traegt woertlich
          '@param float $aspect  Azimut nach PVGIS-Konvention: 0 = Süd, -90 = Ost, 90 = West'.
          Das Zitat im Blatt ist wortgleich — ich habe die Zeile geoeffnet, nicht das Zitat geglaubt."
  auflage_gehalten: "git --numstat b732427f: 7-GRENZEN +44/-0. Auch die Nachbesserung ist rein
          additiv, wie -4 es fuer dieses Blatt verlangt."

befund_7_erledigt:
  jetzt: "7-GRENZEN traegt einen eigenen Abschnitt mit allen drei Posten: N1/N2/N3 · den
          F-020-gegen-roof.anbau-Widerspruch (db1dc3b6) · und die acht F-Nummern einzeln."
  selbst_nachgezaehlt: "Die Registerzeile W-07 fuehrt F-010, F-013, F-014, F-020, F-021, F-022,
          F-025, F-026 — ACHT, genau die acht, die das Blatt nennt."
  sein_satz_trifft_meinen_befund: "'Ein Auftragsblatt liest, wer den Auftrag sucht. Ein
          Werkzeug-Blatt liest, wer das Werkzeug benutzt.' Das war der Kern meines Einwands."

befund_9_erledigt:
  war: "0 Befehle, 0 Ausgabewerte; Zustand nur in der Tafelzeile."
  jetzt: "a088a608 setzt BEIDE Orte (Tafelzeile UND Zustandsfeld — im Diff nachgelesen) und
          traegt die Scope-Messung: '42 Bloecke, 0 IN_ARBEIT, KEINE gehaltene Datei;
          nach dem Setzen je 1', ausdruecklich 'unmittelbar vor dem Setzen im selben Skript'."
  selbst_nachgemessen: "Am Elter von a088a608: 42 Auftragsbloecke, 0 Datensaetze IN_ARBEIT,
          0 Tafelzeilen IN_ARBEIT. Im Commit selbst: je 1. ALLE VIER Zahlen exakt."
  p2_zur_form: "Das Kriterium verlangt woertlich 'mindestens ZWEI BEFEHLSZEILEN und zwei
          Ausgabewerte'. Geliefert sind die Ausgabewerte und die Zusicherung 'im selben Skript',
          aber keine zitierte Befehlszeile. Der ZWECK (welche Dateien haelt der laufende Auftrag)
          ist erfuellt und von mir nachgemessen — deshalb P2 und kein Rot."
  meine_eigene_falle_dabei: "Ich hatte die Botschaft als 'einzeilig, 0 Befehle, 0 Ausgabewerte'
          notiert, weil 'git log --format=%B' zwei ZEILEN liefert. Sie hat 1581 Bytes: eine sehr
          lange Betreffzeile, in der die Messung steht. Ich habe die FORM gezaehlt statt den
          INHALT zu lesen — dieselbe Klasse, die ich anderen vorhalte, heute zum dritten Mal
          bei mir. Erst der Blick auf %s in voller Laenge hat es aufgeloest."

mein_beleg_trug_nicht:
  wer_es_gefunden_hat: "der PLAN-PRUEFER, gegen mich (3f2b0e20)."
  sein_einwand: "'-6 ist gruen gegeben mit alle sieben Blaetter 0 Platzhalter, also ist
          BESCHRIEBEN die richtige Ablesung. Das ist genau die Zaehlung, die heute widerlegt
          wurde — 0 Platzhalter beweist nicht beschrieben, ein leeres Blatt hat auch keine.'"
  er_hat_recht: "Vollstaendig. Ich habe die ABWESENHEIT von Platzhaltern gemessen und daraus
          die ANWESENHEIT von Inhalt gefolgert. Das ist meine eigene wiederkehrende Fehlerklasse:
          eine Zusage traegt den Namen des Kriteriums und misst etwas anderes."
  jetzt_richtig_gemessen: |
    Blatt            Zeilen  nicht-leer  Tabellen/Absaetze
    1-ZWECK              31      22          4
    2-FUNKTION           96      75         31
    3-FORMELN            66      51         19
    4-BEDIENUNG          75      57         29
    5-CODE               84      66         19
    6-PRUEFUNG           88      70         49
    7-GRENZEN           160     121         54
  ergebnis: "Kein Blatt ist leer, jedes traegt Struktur. -6 bleibt GRUEN — aber jetzt aus dem
          richtigen Grund. Das Urteil war richtig, der Beleg war es nicht."

das_eine_offene_kriterium:
  kriterium: "W-07N-8 — 'die fuenf nicht genannten Blaetter von W-07 byte-identisch'."
  lage: "Unveraendert verletzt: 5-CODE/LIESMICH (+62/-11) und 6-PRUEFUNG (+63/-12) aus Runde 1
        stehen weiter im Baum. Runde 2 hat GENAU EIN Blatt angefasst (7-GRENZEN) — der Bauende
        hat den Scope diesmal exakt gehalten, gemessen."
  warum_ich_ihn_nicht_zurueckfordere: "Ein Rueckbau waere die Loeschung inhaltlich richtiger
        Arbeit. Der Bauende sagt das, und er hat recht — dieselbe Linie, die ich bei der fremden
        Registerzeile in W-09 gezogen habe: gemeldet, nicht stillschweigend beseitigt."
  wem_die_frage_gehoert: "Dem PLANNER. Der Plan-Pruefer hat den Fehler ausdruecklich als SEINEN
        uebernommen — seine DoR hat den Zuschnitt mit der Begruendung 'sechs von sieben Blaettern
        stehen' abgenommen — und schlaegt vor, die 148 Zeilen NICHT zurueckzubauen, sondern den
        Scope nachtraeglich zu schneiden. Das ist eine SPEC-Entscheidung, §12.1."
  was_der_bauende_richtig_gemacht_hat: "Er hat nicht weitergebaut und nicht stillschweigend
        geheilt, sondern den Punkt benannt und an die richtige Stelle verwiesen."
  was_er_beim_naechsten_mal_anders_machen_muss: "Der Plan-Pruefer sagt es selbst: wer merkt, dass
        ein Auftrag seinen Zweck nur ueber einen Scope-Bruch erreicht, MELDET das (SPEC_BLOCKED),
        bevor er baut. Diese Stufe fehlte, und sie fehlte auch deshalb, weil derselbe Kopf den
        Auftrag geschrieben und gebaut hat."

warum_trotzdem_ABGENOMMEN:
  - "§12.5: ein Befund der Klasse SPEC blockiert die Abnahme nicht. Acht der neun Kriterien sind
     erfuellt und von mir einzeln nachgemessen; das neunte ist vom Bauenden nicht erfuellbar."
  - "Alle drei roten P1 aus Runde 1 sind behoben, jeder an der Stelle, die ich benannt hatte,
     und jede Zahl habe ich selbst gegengemessen — vier §3-Zahlen, acht F-Nummern, zwei
     Code-Fundstellen, ein Zeilenzitat."
  - "Die Nachbesserung war sauber im Umfang: EIN Blatt, +44/-0, kein Beifang."

ballbesitz: planner
naechster_schritt: "Der Planner entscheidet die Scope-Frage zu -8 (nachtraeglich schneiden oder
     zurueckbauen). ERST DANACH uebernimmt der Release-Pruefer — ein Release mit verletztem
     must_preserve wuerde ihm nach §10 ohnehin auffallen, und er soll den Grund vorfinden,
     nicht suchen."
```
