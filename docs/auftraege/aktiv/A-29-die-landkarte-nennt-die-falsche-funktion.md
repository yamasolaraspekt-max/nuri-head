# A-29 — Die Landkarte nennt bei `versatz` die falsche Funktion. Und ihr Kopf zählt 110, wo 111 stehen

```yaml
auftrag: "A-29"
werkzeug: "—  (Selbstauskunft im Hausplaner-Code)"
art: "BAU — zwei Berichtigungen in app/tools/werkzeugLandkarte.ts. Kein Verhalten, keine Marke."
spur: A
heimat_app: ticket
dor_beleg: "steht aus — plan-pruefer."
status_steht_in: docs/STATUS.md
basis_sha: 84c57085
prioritaet: P2
ballbesitz: "plan-pruefer (DoR)"
claim: "planner 13.08. — Claim VOR dem Schnitt."
kennung_geprueft: "A-01 bis A-28 sind vergeben (aus docs/STATUS.md erhoben). A-29 hat NULL Treffer
                   in docs/STATUS.md und NULL Blaetter in docs/auftraege/aktiv/. Frei.
                   Diese Pruefung ist neu in Pflichtpruefung 1, seit W-05/1 mich erwischt hat."
kein_laufender_auftrag_deckt_es_ab: "grep ueber docs/auftraege/aktiv/ nennt werkzeugLandkarte nur in
                   W-18-1:79, und dort in einer Aufzaehlung von Dateien mit dem Wort 'kontur' —
                   kein Scope-Eintrag. W-18/1 ist BEREIT beim Generator; kein Konflikt."
anlass: "Der erste Punkt hat MICH in die Falle gefuehrt: ich habe am 13.08. auf Grund dieser einen
         Begruendung drei Werkzeuge als 'blosse Anschlussarbeit' in den Fahrplan geschrieben UND es
         Yama zugesagt. Beides musste ich zurueckziehen (485004c4). Was mich getaeuscht hat, taeuscht
         die naechste Rolle genauso."
grundlage: "app/tools/werkzeugLandkarte.ts:80 (versatz) und :25-31 (Kopf, P-04-Absatz) ·
            geometry/editierGeometrie.ts:19-27 (versetzteWand) ·
            __tests__/werkzeugLandkarte.test.ts:53/:90/:119 · Commit 1fba9a1d (Kontur-Vertrag)"
```

## 1 — Der tragende Punkt: `versetzen` und `Versatz` sind dasselbe Wort und zwei Sachen

```text
DIE BEGRUENDUNG, app/tools/werkzeugLandkarte.ts:80, im Wortlaut:
  { werkzeugId: 'versatz', marke: 'fehlt', begruendung: 'Parallelversatz erzeugt
    eine NEUE Wand im Abstand d — die Geometrie dafuer liegt bereits in
    `editierGeometrie.versetzteWand`, der Befehl, der sie anlegt, fehlt.' }

WAS versetzteWand WIRKLICH TUT, geometry/editierGeometrie.ts:20, Rumpf geoeffnet:
  return { start: versetzePunkt(start, dx, dy),
           end:   versetzePunkt(end,   dx, dy) };
  -> BEIDE Endpunkte um DENSELBEN Vektor (dx,dy). Das ist eine TRANSLATION.
  Und der Doc-Kommentar in :19 sagt es selbst:
     "Wand-Endpunkte um (dx,dy) versetzen (bewegen/duplizieren mit Versatz)."

WARUM DAS NICHT DERSELBE VORGANG IST:
  Translation      verschiebt die Wand; Richtung und Laenge bleiben, die
                   Achse wandert um (dx,dy).
  Parallelversatz  legt eine NEUE Wand im SENKRECHTEN Abstand d neben die
                   Achse. Braucht die NORMALE der Achse und d — Groessen, die
                   in versetzteWand nicht vorkommen.
```

> **Die zweite Hälfte der Begründung ist richtig, die erste kippt sie.** *„Der Befehl, der sie anlegt,
> fehlt" trifft zu. Aber davor steht, die Geometrie liege „bereits" vor — und **wer das liest, hält
> `versatz` für eine Verdrahtung und plant eine halbe Stunde ein, wo eine neue Rechnung nötig ist.***

**Der Beleg, dass das nicht theoretisch ist:** *genau das ist am 13.08. passiert. Ich habe daraus im
Fahrplan „Cluster 3 — die Geometrie IST DA, es fehlt der ANSCHLUSS, sofort machbar" gemacht und es
Yama in der Vorlage als „was ich ohne dich weitermache" zugesagt. **Zurückgezogen in `485004c4`,
bevor gebaut wurde** — aber nur, weil ich die Funktion nachträglich geöffnet habe.*

## 2 — Reichweite gemessen, BEVOR verallgemeinert wird: es ist EINE von achtzehn

```text
Von den 21 fehlt-Marken nennen 18 eine konkrete Codestelle in Backticks.
Alle 18 durchgesehen, die genannten Stellen geprueft:

  FALSCH   1  versatz (:80)   nennt versetzteWand als vorhandene Geometrie
  RICHTIG 17  Befehle    MOVE_NODE (:62), UPDATE_NODE (:63, :77),
                         ADD_NODE (:110), UPDATE_SETTINGS (:114)
                         — alle in commands/applyCommand.ts vorhanden
              Feldpfade  transform.rotation und transform.scale
                         (domain/scene.types.ts:195/:196), allowScaling
              Typen      ObjectNode.objectType (:178, elf Werte)
              Dateien    geometry/bemassung.ts (existiert)
              Verweise   ausrichten, teilen, trimmen (Querverweise auf
                         andere Eintraege derselben Datei)

EIN HINWEIS FUER DEN, DER DAS NACHMISST: transform.rotation findet man NICHT
als Zeichenkette. Die Felder stehen verschachtelt (transform: { position,
rotation, scale }), scene.types.ts:194-196. Mein erstes Muster suchte den
Punkt-Pfad und lieferte 0 — die Landkarte hatte recht, mein Muster war falsch
gesetzt. H-9 auch hier.
```

> **Das ist KEIN systematischer Mangel, und das gehört dazu.** *Die Landkarte ist überwiegend genau —
> bei `trimmen` hatte **sie** recht und **ich** unrecht. Ein Auftrag, der sie pauschal in Frage stellt,
> wäre falsch. **Es ist eine Stelle, und sie hat eine benennbare Ursache: eine Wortfalle (H-9).***

## 3 — Der zweite Punkt: der Kopf zählt 110, gemessen sind 111 — und er war einmal richtig

```text
DER KOPF, app/tools/werkzeugLandkarte.ts:25-31, sinngemaess:
  "Der Auftrag nennt 111 Vertraege … grep -c \"werkzeugId: '\" liefert 110 …
   Es sind 110 Vertraege — der urspruengliche Stufenplan hatte recht, die
   Korrektur auf 111 war der Zaehlfehler."

HEUTE GEMESSEN, vier Muster mit Traeger:
  app/tools/werkzeugVertrag.ts, Zeilen mit werkzeugId       111
  app/tools/werkzeugVertrag.ts, Objektliterale              111
  Landkarten-Eintraege (Objektliterale)                     111
  Landkarten-Marken (deckt+fehlt+ohne-modell+stillgelegt)   111
  (die ROHE grep-Zahl der Landkarte ist 112 — eine Nennung
   steht im Kopfkommentar selbst, Zeile 27. Deshalb braucht
   die Zahl hier zwingend das Muster dazu.)

DIE URSACHE IST BELEGT, nicht vermutet:
  git log -S"Es sind 110" -> e903ce36 (AUF-50-S1, die Landkarte entsteht)
  git show e903ce36:werkzeugVertrag.ts | Objektliterale zaehlen -> 110
  -> DER KOPF WAR AN SEINEM TAG KORREKT.
  git log -S"werkzeugId: 'kontur'" -> 1fba9a1d (Z-05-N1) fuegt den
     Kontur-Vertrag ein  -> seitdem 111.
```

> **Und die Sache ist an zwei von drei Orten gepflegt worden — nur hier nicht.** *Die Landkarten-**Daten**
> sind mitgezogen (`ohne-modell` 42 → 43), und `__tests__/werkzeugLandkarte.test.ts:119` hält die
> Verteilung hart **und kommentiert die Änderung**: „Z-05-N1: `kontur` liefert Punkte und schreibt
> nichts — ohne-modell 42 -> 43." **Der Bau war sauber. Stehen geblieben ist die Prosa im Kopf** — und
> die ist der erste Text, den ein Leser sieht, und sie **begründet ausführlich, warum 111 falsch sei.***

## 4 — Scope

```text
A-29 IST  (1) die versatz-Begruendung in app/tools/werkzeugLandkarte.ts:80
              berichtigen: die falsche Zuschreibung raus, die Wortfalle
              benannt, der richtige Teil bleibt.
          (2) den P-04-Absatz im Kopf (:25-31) mit einem NACHTRAG versehen,
              der die heutige Zahl und die Ursache nennt.

A-29 IST NICHT
          eine Aenderung an einer MARKE. Keine. werkzeugLandkarte.test.ts:119
          haelt die Verteilung hart und sagt selbst, das sei gewollt.
          eine Aenderung an versetzteWand oder an editierGeometrie.ts. Die
          Funktion ist richtig, nur falsch zitiert.
          der BAU von versatz/trimmen/verlaengern. Das ist Cluster 3 im
          Fahrplan und ein eigener Vorgang mit eigener Groesse.
          eine Durchsicht der uebrigen 17 Begruendungen mit dem Ziel, weitere
          zu aendern. Sie sind in Abschnitt 2 geprueft und tragen.
          das Loeschen des alten Wortlauts. An BEIDEN Stellen bleibt der alte
          Satz lesbar und wird als ueberholt gekennzeichnet — die Bedingung,
          die der plan-pruefer an W-33-5 gesetzt hat (baa785a2).
```

## 5 — Abnahmekriterien

```text
A-29-1 (P1, TRAGEND) Die versatz-Begruendung sagt nicht mehr, die Geometrie liege
       "bereits" in versetzteWand. Sie sagt stattdessen, WAS fehlt: eine Rechnung,
       die aus Achse und Abstand d eine parallele Wand erzeugt (Normale), UND der
       Befehl, der sie anlegt. Der alte Wortlaut bleibt an derselben Stelle als
       ueberholt lesbar.
       WARUM P1: aus dieser einen Zuschreibung ist am 13.08. eine falsche
       Fahrplan-Einordnung UND eine falsche Zusage an Yama entstanden.
A-29-2 (P1) Die WORTFALLE ist benannt, nicht nur die Korrektur: versetzen (bewegen)
       und Versatz (offset) sind dasselbe Wort und zwei Vorgaenge. Ohne diesen Satz
       traegt jemand dieselbe Verwechslung beim naechsten Anlass wieder ein — H-9.
A-29-3 (P1) Der Kopf-Nachtrag nennt DREI Dinge: die heutige Zahl, die Ursache mit
       Commit, und dass der Kopf an SEINEM Tag korrekt war. Der P-04-Absatz wird
       NICHT umgeschrieben — er ist ein datierter Messbefund und war richtig.
       Die Zahl wird AM BAU-STAND erhoben und nicht aus diesem Blatt uebernommen
       (E1). Wer sie erhebt, nennt DEN TRAEGER: welche Datei, welches Muster.
       Der Grund dafuer steht in Abschnitt 3 — die rohe grep-Zahl der Landkarte
       ist 112 und nicht 111, weil eine Nennung im Kopfkommentar selbst steht.
       Eine Zahl ohne Datei und Muster ist hier nachweislich zweideutig.
A-29-4 KEINE Marke geaendert. Gegenprobe, die ROT werden kann: der Testlauf von
       __tests__/werkzeugLandkarte.test.ts ist gruen, und zwar EINSCHLIESSLICH des
       Waechters in :119, der die Verteilung hart prueft. Rohausgabe in den Bericht.
       Und der Waechter in :90 verlangt bei fehlt-Marken mindestens 40 Zeichen
       Begruendung — die neue Fassung muss das halten, sonst wird er rot.
A-29-5 KEIN Verhalten geaendert. Gegenprobe mit Traeger: WERKZEUG_LANDKARTE wird
       ausserhalb der eigenen Datei nur von __tests__/werkzeugLandkarte.test.ts
       gelesen, und .begruendung kommt in KEINER .tsx-Datei vor. Beides am
       Bau-Stand nachmessen, nicht aus diesem Blatt uebernehmen — wenn die
       Messung etwas anderes ergibt, ist DAS der Befund.
A-29-6 Die 17 uebrigen Begruendungen mit Codestelle sind UNBERUEHRT. Gegenprobe:
       der Bau-Commit fasst in werkzeugLandkarte.ts genau zwei Bereiche an, den
       Kopf und die versatz-Zeile.
```

**Nachweisform: Befehl und Trefferzeilen** (B5), **Messung am COMMIT** (E1),
**Nachweis muss rot werden können** (Pflichtprüfung 4), **jede Zahl mit Träger** (Prüfung 7).

```yaml
was_die_pflichtpruefungen_hier_verhindert_haben: "Pruefung 6, die Reichweite. Mein erster Impuls war
        'die Landkarte ist unzuverlaessig' — nach EINEM Fund. Gemessen sind es 1 von 18, und bei
        trimmen hatte SIE recht und ich unrecht. Ein Auftrag mit der ersten Lesart haette eine
        genaue Datei pauschal in Frage gestellt und dem Bauenden 18 Pruefungen aufgehalst, von
        denen 17 gruen ausgehen."
warum_dieser_auftrag_klein_bleibt: "Die Versuchung war, versatz gleich mitzubauen — die Rechnung ist
        keine Kunst. Aber das ist Cluster 3 im Fahrplan, es beruehrt Modellbefehle und hat eine
        eigene Groesse. Hier wird eine Selbstauskunft berichtigt, damit die naechste Rolle nicht in
        dieselbe Falle laeuft. Nicht mehr."
A_29_nimmt_keinen_paragraf3_platz: "ENTWURF, nicht IN_ARBEIT."
```

## Votum des Evaluators (§11) — NACHBESSERN

```yaml
votum: NACHBESSERN
fehlerklasse: CODE   # §12.1 -> Generator
umfang: "EIN Punkt (§12.2). Fuenf der sechs Kriterien sind erfuellt, das sechste in seinen
  geforderten Zusagen ebenfalls — der Befund trifft eine ZUSATZZAHL, die der Bau freiwillig
  aufgenommen und nicht am eigenen Stand gemessen hat."
geprueft_am: "13.08.2026, evaluator"
bau_commit: "4654687f (22:11) — GESUCHT: der einzige Bau-Commit, eine Datei, zwei Hunks."
elter: "1de986de"
```

### Der Befund: die Roh-Zahl im Nachtrag stimmt am Bau-Stand nicht — und der Nachtrag hat sie selbst verschoben

```text
Der neue Kopf-Nachtrag stellt eine Tabelle auf, die zeigen soll, WARUM eine Zahl hier ihren
Traeger braucht. Die letzte Zeile lautet:

    diese Datei   ROH  grep -c "werkzeugId: '"   112   <- eine Nennung steht in
                                                          DIESEM Kommentar, Zeile 27

SELBST GEMESSEN, mit Befehl und Zahl:
    grep -c "werkzeugId: '"  am ELTER 1de986de  ->  112     die Zahl stimmt DORT
    grep -c "werkzeugId: '"  am BAU   4654687f  ->  116     am eigenen Stand nicht mehr

URSACHE, gemessen und nicht vermutet — der Nachtrag hat die Zahl selbst erhoeht, weil er das
Muster VIERMAL in seinen eigenen Text schreibt. Die Trefferzeilen ausserhalb der Eintraege:
    Z.27   `grep -c "werkzeugId: '"` liefert **110** …        (die eine, die der Nachtrag nennt)
    Z.39    werkzeugVertrag.ts   Zeilen mit  werkzeugId: '    111
    Z.40    werkzeugVertrag.ts   Eintraege   ^\s*werkzeugId: ' 111
    Z.43    diese Datei          ROH  grep -c "werkzeugId: '"  112
    Z.48   `git log -S"werkzeugId: 'kontur'"` auf …
-> Es sind FUENF Nennungen, nicht eine. Damit sind BEIDE Aussagen der Zeile falsch: die Zahl
   112 und der Zusatz "eine Nennung … Zeile 27".

WARUM DAS NICHT KLEINLICH IST — drei Gruende, und der dritte ist der eigentliche:
 (1) A-29-3 sagt woertlich: "Die Zahl wird AM BAU-STAND erhoben und nicht aus diesem Blatt
     uebernommen (E1)." Die 112 steht im Blatt (Abschnitt 3) und ist von dort uebernommen.
 (2) Der Nachtrag FORDERT ZUM NACHFAHREN AUF, indem er den grep-Befehl mitliefert. Wer ihn
     faehrt, bekommt 116 und muss der ganzen Tabelle misstrauen — auch den vier Zahlen, die
     stimmen.
 (3) A-29 ist ein Auftrag, dessen einziger Zweck die Berichtigung einer falschen Selbstauskunft
     im Code ist. Der Bau hinterlaesst an derselben Stelle eine neue falsche Selbstauskunft.
     Das ist derselbe Fehler in klein.

WAS ICH AUSDRUECKLICH NICHT BEANSTANDE: die vier tragenden Zahlen sind richtig und alle vier
von mir nachgezaehlt (unten). Der Nachtrag ist im Uebrigen mustergueltig — er nennt zu jeder
Zahl Datei und Muster, genau wie Pruefung 7 es verlangt.

VORSCHLAG ZUR BEHEBUNG, aber die Form entscheidet der Generator: die Zeile misst etwas
Selbstbezuegliches. Entweder die Zahl am Bau-Stand erheben UND sagen, dass der Kommentar selbst
mitzaehlt (dann wandert sie bei jeder weiteren Zeile wieder), oder die Aussage ohne feste Zahl
fuehren — etwa "die rohe grep-Zahl ist GROESSER als 111, weil dieser Kommentar das Muster selbst
enthaelt; deshalb zaehlt man Eintraege, nicht Nennungen". Die zweite Fassung kann nicht veralten.
```

### Messtisch — jede Kriterienzeile eine Zeile

```text
A-29-1 (P1, TRAGEND)  ERFUELLT
  Die alte Zuschreibung ist raus; der neue Text sagt, `versetzteWand` verschiebe BEIDE Endpunkte
  um denselben Vektor (Translation) und sei NICHT dasselbe.
  Der alte Wortlaut steht als "UEBERHOLT:" an derselben Stelle lesbar — nicht geloescht.
  UND DER BAU GEHT WEITER ALS DAS KRITERIUM, zu Recht: er sagt nicht "die Rechnung fehlt",
  sondern dass sie seit A-32 in `geradenGeometrie.parallelVersatz` liegt und nur noch der
  Modellbefehl fehlt. SELBST GEPRUEFT: die Funktion existiert am Bau-Stand
  (geradenGeometrie.ts:157). Haette der Bau den Wortlaut des Kriteriums abgeschrieben, waere die
  Landkarte ab heute wieder falsch — ich werte das als richtig, nicht als Abweichung.

A-29-2 (P1)           ERFUELLT
  Die Wortfalle ist ausdruecklich benannt: "`versetzen` (bewegen) und `Versatz` (offset) sind
  DASSELBE WORT und ZWEI VORGAENGE", mit dem Grund (die Normale kommt in versetzteWand nicht vor)
  und mit dem Preis (Fahrplan-Einordnung und Zusage an Yama, zurueckgezogen in 485004c4).

A-29-3 (P1)           NICHT VOLLSTAENDIG — der Befund oben
  Die DREI geforderten Dinge sind da und richtig:
    heutige Zahl 111   — von mir an VIER Mustern nachgezaehlt, alle 111:
                          werkzeugVertrag.ts  "werkzeugId: '"            111
                          werkzeugVertrag.ts  ^\s*werkzeugId: '          111
                          werkzeugLandkarte.ts ^\s*\{ werkzeugId         111
                          Marken 41 deckt + 21 fehlt + 43 ohne-modell + 6 stillgelegt = 111
    Ursache mit Commit — BEIDE selbst nachgemessen: e903ce36 (30.07.) traegt 110 werkzeugId-
                          Zeilen; 1fba9a1d (01.08.) hebt 110 -> 111 und fuegt genau
                          `werkzeugId: 'kontur'` ein. Die Behauptung traegt.
    "an SEINEM Tag korrekt" — steht da, und der P-04-Absatz ist NICHT umgeschrieben.
  ABER die freiwillig aufgenommene ROH-Zahl 112 ist am Bau-Stand 116 (Befund oben).

A-29-4                ERFUELLT
  werkzeugLandkarte.test.ts SELBST gefahren: tests 12, pass 12, fail 0 — einschliesslich
  ':119 ERGEBNIS der Stufe' (die harte Verteilung) und ':90' (40-Zeichen-Schwelle).
  Marken gegen den Elter einzeln: deckt 41->41 · fehlt 21->21 · ohne-modell 43->43 ·
  stillgelegt 6->6. KEINE geaendert.
  Die neue Begruendung ist 362 Zeichen lang, die Schwelle ist 40.
  FANGPROBE, Anker je 1x, md5 zurueck auf cd8139a2:
    Marke versatz fehlt->deckt  -> ':119 ERGEBNIS der Stufe' ROT (und K-03 dazu)
    Begruendung auf 8 Zeichen   -> ':90 jede fehlt-Begruendung …' ROT
  Beide Waechter greifen also wirklich und sind nicht nur vorhanden.

A-29-5                ERFUELLT
  WERKZEUG_LANDKARTE wird ausserhalb der eigenen Datei NUR von __tests__/werkzeugLandkarte.test.ts
  gelesen (7 Fundstellen, alle im Test). `.begruendung` kommt in 0 .tsx-Dateien vor.
  Gegenbeleg von der anderen Seite: das Buendel public/hausplaner/hausplaner.js ist am Elter und
  am Bau byte-identisch (448d8653), und der neue Satz kommt 0x darin vor.

A-29-6                ERFUELLT
  Der Bau fasst genau EINE Datei an, mit GENAU ZWEI Hunks: @@ -28,6 +28,31 @@ (Kopf) und
  @@ -77,7 +102,28 @@ (versatz). Die 17 uebrigen Begruendungen sind unberuehrt.

Suite / tsc          Insel-Suite am Bau-Stand: tests 1750, pass 1750, fail 0. tsc exit=0.
                     (Kein Kriterium verlangt es; gefahren, weil der Bau in die Insel schreibt.)
Browser              NICHT GEFAHREN, mit Grund: keine sichtbare Wirkung. A-29-5 belegt es von
                     zwei Seiten — kein .tsx liest das Feld, das Buendel aendert sich nicht.
§15                  Kein Schreibvorgang gegen eine Datenbank im Pruefumfang.
```

### Meine eigenen Messfehler in diesem Durchgang — einer

```text
1  Beim ersten Blick auf die Roh-Zahl war meine Reaktion "der Bau zaehlt falsch". Erst die Messung
   am ELTER (112) hat gezeigt, dass die Zahl dort richtig WAR und der Bau sie selbst verschoben
   hat. Ohne diesen zweiten Schritt haette der Befund den falschen Vorwurf getragen — nicht
   "falsch gezaehlt", sondern "am eigenen Stand nicht nachgemessen". Das ist ein Unterschied, und
   er gehoert in den Befund.
```

**Weitergabe:** NACHBESSERN → **Generator**. Nach §12.3/§12.4 fahre ich bei der Wiederabnahme
ALLE sechs Kriterien erneut, nicht nur den Befund.
