# DoR-Votum — A-43 „Kennungsmuster mehrgliedrig"

```yaml
auftrag: "A-43"
blatt: docs/auftraege/aktiv/A-43-kennungsmuster-mehrgliedrig.md
blatt_sha: 352900f3
pruefer: plan-pruefer
auftrag_id: DOR-plan-pruefer-kennungsmuster
generation: 8
votum: ERTEILT_MIT_AUFLAGE   # Runde 2 gegen 47dfbfb2; Runde 1 war NICHT_ERTEILT gegen 352900f3
restpunkte: 0   # alle drei behoben; eine Auflage neu (A-43-11)
zeit: "22.08.2026, Runde nach A-37-ABGENOMMEN"
```

## Ergebnis: **NICHT ERTEILT** — ein Posten von zweien fehlt vollständig

Mein Auftrag (gen 8) verlangt die Prüfung **beider** Posten:

> *„beide Posten (Kennungsmuster 7 Formen + Grundmenge; **Aktionsvokabular je Rolle**, `warten*` = Pause,
> unbekannt = 7, EINE Liste)"*

Das Blatt liefert **Posten 1 vollständig und gut**. **Posten 2 kommt darin nicht vor.**

```
Aktionsvokabular  0     aktion  0     warten_dann  0     spezifizieren  0     rueckweg  0     Pause  0
Gegenprobe: 'Kennung' 48   -> das Verfahren greift, die Nullen sind echt
```

---

## Restpunkt 1 — Posten 2 (Aktionsvokabular) fehlt

**Und die Ursache ist kein Versäumnis am Blatt, sondern ein Generationswechsel mitten in der Arbeit.**
Gemessen:

```
12:03:49   gen 13 erstellt — nur Kennungsmuster
12:10:49   planner-ack                 gen 13
12:13:23   planner-AUFTRAG_GESTARTET   gen 13
12:17:32   gen 14 erstellt — "Posten 2 Aktionsvokabular ins SELBE Blatt" + Hinweg
12:20:46   planner-frage-…             gen 13
12:22:45   planner-CODE_FERTIG         gen 13     kein planner-BASIS_NACHGEZOGEN (0)
```

**Alle vier Ereignisse des Planners tragen `generation: 13`.** Die Quelle stand ab 12:17:32 auf 14.

**Erschwerend, und das ist ein eigener Befund:** das Feld `erstellt:` in `rollen/planner.yaml` lautet
**`2026-08-22T12:03:49+02:00`** — der Zeitpunkt von **gen 13**. Nur der Kommentar hinter `generation:`
nennt 12:17:32. **Wer die Quelle über `erstellt:` auf Aktualität prüft, sieht den Wechsel nicht.**
Das ist dieselbe Klasse wie §301: die Information existiert, erreicht aber den Gebrauchsort nicht.

**Der Restpunkt bleibt trotzdem bestehen** — mein Auftrag verlangt beide Posten, und einer ist nicht da.
Er ist an den Planner zu geben, nicht an das Blatt: die Arbeit fehlt, nicht die Qualität.

## Restpunkt 2 — A-43-4: die Vergleichszahl hat keinen Messbefehl

Das Kriterium verlangt, *„die Differenz zur ungeankerten Zählung zu benennen"*, und nennt
**„ungeankert 29"**. Der **Messbefehl dafür fehlt**; angegeben ist nur der geankerte Lauf.

```
mein Lauf,  grep -c 'zustand: '            -> 71      (mein Baum UND Planner-Baum, beide 162 Refs)
Blatt                                       -> 29
```

**Ich kann die 29 nicht reproduzieren, weil nicht dasteht, wie sie entstanden ist.** Die Absage-Regel
nennt `grep -c 'zustand: Z'` — ein *drittes* Muster. Eine Zahl, deren Befehl fehlt, ist genau das,
was A-43-4 selbst bekämpft.

## Restpunkt 3 — A-43-4: die Grundmenge nennt keinen Stand

```
Blatt:                  Grundmenge 22
heute, mein Baum:       23
heute, Planner-Baum:    23
neu hinzugekommen:      aec713a6 "integrator: zustand: A-37 · ABGENOMMEN · release-pruefer · …" (12:20:02)
```

**Die 22 war beim Schreiben richtig** — sie ist inzwischen 23, weil der A-37-Zustandscommit gefallen
ist. **A-43-1 macht es vor** (*„am Endstand `c82df498`"*), A-43-4 nicht. Eine wachsende Grundmenge ohne
Standangabe ist beim nächsten Commit falsch, und der Bauende weiß nicht, gegen welche Zahl er misst.

---

## Was am Blatt gut ist — und selbst nachgemessen

**Die N3-Matrix ist vorhanden und vollständig.** Sieben Zeilen bei sieben Kriterien, je mit
Arbeitspaket, `Commit-SHA` und `Testbeleg` auf `n.U.`:

```
| A-43-1 acht Formen | AP-1 Muster in KERN erweitern | n.U. | n.U. |    … 7 Zeilen, 7 Kriterien
```

**Das ist das erste Blatt in `docs/auftraege/aktiv/` mit einer Matrix** — bei meiner Messung heute
Vormittag waren es 0 von 89 (§308). Der Punkt ist damit nicht nur entschieden, sondern gebaut.

**Zwei Rot-Lagen habe ich exakt bestätigt, über den vom Kriterium vorgeschriebenen Weg** — nicht über
ein nachgebautes Muster, sondern durch Auslesen und Ausführen des `KERN`-Ausschnitts:

```
A-43-1   A-42 · A-37 · W-17/1 · P-05   ERKANNT       Z0-I1 · Z1-W1-1 · Z2-W0-1 · A-37-22b   NICHT
         -> 4 zu 4, genau wie behauptet
A-43-2   grep -c 'P<kennung>' -> 2, Zeilen 190 und 521, genau wie behauptet
```

**Zwei Fallen hat der Planner selbst gefunden und benannt**, und beide sind ernst:

1. **Das Muster steht zweimal** (`KERN` :190, `BEINAHE` :521). Wer nur `KERN` erweitert, nimmt
   Z-Aufträgen die Warnung vor **verpassten** Zustandsmeldungen.
2. **Die saubere Lösung dagegen schaltet das Tor still ab.** Das Tor importiert die Datei nicht, es
   führt einen **Textausschnitt** aus; eine gemeinsame Konstante davor liegt außerhalb — Ergebnis
   `UNGEPRÜFT` bei **Rückgabewert 0**. Sein Satz: *„Der Musterfehler ist sichtbar. Ein still
   abgeschaltetes Tor ist es nicht."* **A-43-3 misst deshalb den Rückgabewert, nicht den Text.**

**Der Widerspruch ist sauber aufgelöst:** „100 % der Grundmenge" stünde gegen A-37-26, weil die drei
echten Z-Betreffs Mehrfach- bzw. Bereichskennungen tragen. Gemessen wird die **Kennungsform**, nicht
der Altbetreff — *„Der Bau repariert das Muster, nicht die Vergangenheit."*

**Und er meldet seinen vierten Zählfehler selbst** (`W-17/1` 5 statt 6, gefangen von der Summenprobe
13+6+3=22) sowie den Kennungsvorbehalt (A-43 wäre der fünfte offene Regelbau bei einem Maximum von
vier — als eigene Frage abgelegt, nicht still gelöst).

---

## Was der Bauende NICHT tun soll

Der Planner warnt ausdrücklich: **nicht zuerst das Muster erweitern und danach die Doppelung
aufräumen.** Beide Fallen greifen ineinander — die halbe Korrektur erzeugt zwei Wahrheiten, die ganze
in der naheliegenden Form schaltet das Tor ab.

## Ball

**Planner** — Posten 2 (Aktionsvokabular) nachtragen, gen 14 lesen, Hinweg fahren; dazu die zwei
Messpunkte an A-43-4. **Danach neue DoR-Runde.** Die sieben Kriterien zu Posten 1 sind aus meiner
Sicht tragfähig und müssen nicht neu geschrieben werden.

---

## Nachtrag zum Votum — zwei Dirigenten-Antworten, die ich vor dem Schreiben nicht gelesen habe

**Das Votum NICHT ERTEILT und seine drei Restpunkte bleiben unverändert.** Nachgetragen wird, was
zwischen meiner Ereignismessung und meinem Votum eingegangen ist:

```
12:26:03  dirigent-antwort-kennungsmaximum.yaml
12:26:27  dirigent-antwort-kennungsmaximum-berichtigung.yaml
12:30:11  mein Votum
```

**Beide lagen vor.** Meine Ereignisliste war die von 12:23:11; zwischen Messung und Schreiben habe ich
nicht erneut gelesen. **Das ist §301 an mir selbst** — dort war es der Bau-Nachtrag des Generators,
hier sind es zwei Antworten zum geprüften Blatt.

### Was sich dadurch ändert — und was nicht

**Restpunkt 1 wächst.** Der Dirigent ergänzt Posten 2 um einen Punkt, den mein Votum nicht nennt:

> *„WICHTIG für Posten 2: ins Blatt gehört auch die **Rückabwicklung der Übergangsregel README 6f**
> (Tor-Wörter zurücknehmen, sobald das Vokabular transportiert ist) — als Folgeposten für den
> Dirigenten, nicht als Kriterium."*

Das trifft mich unmittelbar: **mein eigenes `aktion: bauen` ist so ein Tor-Wort.** Solange die
Rückabwicklung nicht im Blatt steht, gibt es keinen benannten Weg zurück — und eine „befristete"
Regel ohne Rücknahmepunkt ist eine dauerhafte. **Posten 2 umfasst damit: Aktionsvokabular je Rolle
*und* die Rücknahme der Übergangswörter.**

**Der Kennungsvorbehalt ist gegenstandslos geworden** — und zwar durch eine Messung, nicht durch eine
Entscheidung. Der Dirigent hat die Zählung des Planners nachgemessen:

```
A-42 in docs/STATUS.md @ Integration 96643116 -> ABGENOMMEN (Zustandscommit 3b2e5334, 11/11)
offen: A-38 BEREIT · A-39 BEREIT · A-40 ENTWURF = DREI
A-43 ist die VIERTE -> Yamas Maximum (43771e3b) NICHT überschritten
```

**Kein Governance-Konflikt, kein Vorbehalt nötig, Weg A ohne Bedingung.** Das Blatt trägt den
Vorbehalt trotzdem — **kein Mangel**: der Planner hat richtig gehandelt, als er die Frage stellte
statt sie still zu lösen, und die Antwort kam nach seinem Blatt. Beim nächsten Anfassen kann der
Vorbehalt entfallen; ein Restpunkt ist er nicht.

**Und es ist bemerkenswert, wie die Antwort zustande kam:** die erste Fassung (12:26:03) übernahm die
Zählung des Planners und sprach von einem Vorbehalt; die Berichtigung 24 Sekunden später misst nach
und hebt ihn auf. *„gemessen statt übernommen"* steht im Bezugsfeld. **Vier Rollen haben heute in
derselben Stunde eine eigene Zahl nachgemessen statt sie zu übernehmen** — Planner, Evaluator,
Integrator, Dirigent. Das ist die Gewohnheit, um die es die ganze Zeit geht.

**Ball unverändert: Planner** — Posten 2 jetzt mit drei Teilen (Aktionsvokabular je Rolle,
`warten*` = Pause / unbekannt = 7 in EINER Liste, Rücknahme der Tor-Wörter), dazu die zwei Messpunkte
an A-43-4.

---

# DoR-Runde 2 — A-43 gegen `47dfbfb2`

**Prüfstand:** Blatt `47dfbfb2` (639 Zeilen, 12 Kriterien), mein HEAD `675bf079`, Baum sauber,
gemessen 12:44–12:51. Auslöser: `planner-CODE_FERTIG-nachtrag-restpunkte.yaml` (12:47:33) —
gelesen nach der Berichtigung des Dirigenten (12:45:37), dass mein Auslöser die 6a-Musterliste
ist und `*-CODE_FERTIG*.yaml` trifft; **kein** `*-SPEZIFIZIERT*`-Dateiname wird erwartet.

## Votum: ERTEILT MIT AUFLAGE

Alle drei Restpunkte aus Runde 1 sind behoben. Eine Auflage kommt neu hinzu; sie betrifft die
**Abnahme** von A-43-11, nicht den Bau, und blockiert ihn deshalb nicht.

## Restpunkt 1 — Posten 2 (Aktionsvokabular): BEHOBEN

Fünf Kriterien A-43-8…12. **Ich habe jede Rot-Lage selbst gefahren**, nicht übernommen: die
`case`-Anweisung `scripts/rollen-tor.sh:360-378` extrahiert (19 Zeilen, Gegenprobe auf `esac`)
und isoliert als Funktion gefahren, je Wort Rückgabewert **und** Meldung.

| geprüft | Ergebnis |
|---|---|
| A-43-8 · neun Arbeitsverben | **9 von 9 → RC 7 `unbekannte aktion`**; `bauen`/`nachbessern` → 0 ohne Meldung |
| A-43-9 · Zwillingsfalle | `warten` → 7 *keine Arbeitsanweisung* · `warten_dann_nachpruefen` → 7 *unbekannte aktion* — am RC ununterscheidbar |
| A-43-10 · unbekannt/Leerfall | `quatsch`·`bauenX`·`rueckweg_x` → 7 · Leerfall `""` → **RC 0** mit `HINWEIS … UNGEPRUEFT` |
| A-43-12 · Grundmenge | `aktion` 5× `bauen` (0), 2× `parken` (7, korrekt als Pause) · **`taetigkeit` 7 von 7 abgewiesen**, davon 5× *unbekannte aktion*, 2× *keine Arbeitsanweisung* |

Die Zwillingsfalle ist real und die Begründung für A-43-3/A-43-9 trägt: **zweimal in einem Auftrag
trägt der Rückgabewert allein die Antwort nicht.**

## Restpunkte 2 und 3 — A-43-4: BEHOBEN

Drei Zählweisen, je mit ihrem Befehl, plus Stand. **Mit den Befehlen des Blattes nachgemessen,
nicht nachgebaut:**

    A ungeankert     grep -c 'zustand: '                             -> 71   (Blatt 71)  OK
    B halb geankert  grep -cE '(^|: )zustand:'                       -> 30   (Blatt 30)  OK
    C Grundmenge     grep -cE '^([a-z-]+(-[0-9]+)?: )?zustand: '     -> 23   (Blatt 23)  OK
    Aufteilung isoliert extrahiert: A- 14 · W-17/1 6 · Z 3 = 23      (Blatt identisch)  OK

Die von ihm gemeldete Zählfalle ist reproduzierbar: `grep -c 'zustand: A-'` auf der **Zeile**
gibt **15**, die **isolierte** Extraktion **14** — eine Zeile trägt die Kennung zusätzlich im
Belegteil. Dass die isolierte Extraktion jetzt **im Messbefehl** steht und nicht nur in der
Warnung daneben, ist die richtige Konsequenz; sein Satz *„eine Warnung im Text verhindert nichts,
ein geänderter Messweg tut es"* trifft.

Zu Restpunkt 2 hatte ich in Runde 1 gemessen, dass **keine** von fünf Lesarten × vier Bereichen
die Zahl 29 trifft (Bereiche: Planner-Zweig 21/69, `--all` 23/71, Integration 23/71, `5c9afbc7`
18/66 — die Differenz ist überall konstant **48**, im Blatt stand **7**). Seine Auflösung erklärt
das vollständig: 29 war **halb geankert** gezählt und als *ungeankert* bezeichnet. Damit ist meine
Zahl 71 und seine 29 dieselbe Messung unter zwei Namen — **kein Rechenfehler, ein Benennungsfehler.**

## Auflage (neu) — A-43-11: der Messbefehl verfehlt seine eigene Erwartung

Selbst gefahren, mit dem **Rohbefehl des Blattes**:

    grep -rnE 'bauen\|nachbessern|pausieren\|angehalten' scripts/ .githooks/
      scripts/rollen-tor.sh:361:    bauen|nachbessern) ;;
      scripts/rollen-tor.sh:362:    pausieren|angehalten|angehalten_eingefroren|parken|warten)
      ---- Fundstellen: 2        Erwartung im Blatt: "-> genau 1 Fundstelle"

Das grüne Ergebnis des Blattes nennt **selbst beide Zeilen** (`:361` und `:362`). Gemeint ist
offensichtlich *eine `case`-Anweisung*; der Befehl zählt aber **Fundstellen**, und davon gibt es
zwei — eine je Alternative. **Ein Kriterium, dessen eigener Messbefehl heute die eigene Erwartung
verfehlt, ist bei der Abnahme nicht entscheidbar:** der Evaluator misst 2, liest „genau 1" und
müsste rot geben, obwohl das Blatt denselben Stand grün nennt.

**Auflage:** Erwartung an den Befehl angleichen (etwa *„genau eine `case`-Anweisung, hier zwei
Zeilen: `:361` Arbeit, `:362` Pause"*) oder den Befehl auf die Anweisung zählen lassen. **Ein
Satz, kein neues Kriterium.**

**Warum das den Bau nicht blockiert:** A-43-11 ist ausdrücklich **Regressionsschutz** und heute
grün. Der Bau muss dafür **nichts tun** — er darf nur keine zweite Liste anlegen. Der Mangel wirkt
erst im Abnahmeschritt. Deshalb Auflage und nicht Restpunkt.

*Eigene Berichtigung dazu:* Mein erster Lauf an A-43-11 war falsch, weil ich das Muster
**nachgebaut** habe (`grep -rnE 'bauen|nachbessern'` — die Wörter) statt den Rohbefehl zu nehmen
(`bauen\|nachbessern` — das **literale Pipe-Zeichen**, also die Wortliste selbst). Meine Fassung
gab 13 Fundstellen, darunter Meldungstexte und zwei fremde Dateien. Gegenprobe des echten Musters:
`echo 'bauen nachbessern'` → 0 Treffer, `echo 'bauen|nachbessern'` → 1. **P-02 Punkt 4 gilt auch
für Messbefehle: zitieren, nicht nachbauen.**

## Hinweis, ausdrücklich KEIN Mangel — die Abdeckungsgrenze desselben Musters

`scripts/rollen-tor.sh:374-375` wiederholt **beide Wortlisten** als Meldungstext, mit Leerzeichen
statt Pipe. Der Rohbefehl trifft dort **0**. Gemessen stimmen die Listen heute überein:

    case :361 [bauen nachbessern]                                        text :374 [identisch]  GLEICH
    case :362 [pausieren angehalten angehalten_eingefroren parken warten] text :375 [identisch]  GLEICH

Ich führe das **nicht** als Mangel: Meldungstexte steuern nichts, und das Kriterium spricht von
*Definition*. Aber die Absage-Regel des Kriteriums reicht weiter als sein Messbefehl — *„die
zweite altert unbemerkt, weil niemand sie liest"* trifft auf `:374-375` genau zu. Wer `:361` um
ein Wort erweitert und `:374` vergisst, bekommt eine Fehlermeldung, die eine falsche Liste nennt.
Zur Kenntnis, nicht zur Behebung in A-43.

## Weiteres selbst geprüft

- **N3-Matrix:** 12 Zeilen, 12 distinct IDs, deckungsgleich mit den 12 Kriterien. **Vollständig.**
- **Messbefehle:** alle 12 Kriterien tragen einen. **Keine Lücke.**
- **Absage-Regeln:** 11 von 12; **A-43-7** hat keine. Kein Mangel — es ist ein Schutzbeleg, dessen
  Rot/Grün über *„Diff leer / nicht leer"* eindeutig definiert ist.
- **README 6f:** die Rückabwicklung steht im **Blatt** (Folgeposten 4, Zeile 547), nicht nur in
  der Meldung — der Prüfauftrag des Dirigenten (12:43:07) ist damit erfüllt.
- **Kennungsmaximum:** A-42 ist ABGENOMMEN, offen A-38/A-39/A-40 = drei, A-43 die vierte.
  Maximum erreicht, nicht überschritten.

## Ball: Planner

Die Auflage an A-43-11 eintragen. Danach ist das Blatt aus meiner Sicht baureif; ein erneutes
DoR-Votum ist dafür **nicht** nötig, die Bestätigung der Auflage genügt.
