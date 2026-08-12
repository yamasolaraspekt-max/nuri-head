# W-07N — `2-FUNKTION` füllen und die Azimutgrenze eintragen. Nicht neu schneiden: sechs von sieben Blättern stehen

```yaml
auftrag: "W-07N"
werkzeug: "W-07 Dach aus Kontur"
art: "Nachbesserung eines ALTSTANDES — kein Stufe-1-Blatt, W-07 ist zu 6/7 beschrieben"
titel: "2-FUNKTION.md ist ein leeres Formular, waehrend W-07 im Register BESCHRIEBEN traegt"
spur: A
heimat_app: ticket
status: CODE_FERTIG
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
  werden in der Registerzeile selbst gewürdigt (`6/7`).* **Der Reifegrad wird aber richtiggestellt —
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

*NICHT im Scope: die anderen fünf Blätter von W-07, jedes andere Werkzeug, `FORMELSAMMLUNG.md`.*

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
Die W-07-Registerzeile sagt ablesbar, dass **sechs von sieben** Blättern gefüllt sind, **und der
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
grün (ohne Zahl). Die fünf nicht genannten Blätter von W-07 byte-identisch.

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
