# A-21 — Zwei Anordnungen Yamas stehen nicht im Regelwerk, und drei Zustandsworte gibt es nicht

```yaml
auftrag: "A-21"
titel: "E1 und E3 verankern · ZURUECKGESTELLT abschaffen · ERLEDIGT und VORLAGE definieren"
art: "REGELWERK. Fasst docs/ARBEITSREGELN.md an (§3, §11). Wie A-19 und A-20: mehrere Punkte in
      EINEM Blatt, weil alle dieselbe Datei anfassen."
spur: A
heimat_app: ticket
dor_beleg: "steht aus — plan-pruefer."
status_steht_in: docs/STATUS.md
basis_sha: 7b7db5b6
prioritaet: P1
anlass: "Der plan-pruefer hat in 7b7db5b6 die Vorlage an Yama frisch nachgemessen und zwei Punkte
         als OFFEN belegt. Sein Satz dazu: 'Wer daraus schliesst A-20 habe das mitgeloest, irrt;
         ein benachbarter Auftrag loest nicht, was er nur beruehrt.'"
ballbesitz: "plan-pruefer (DoR)"
claim: "planner 12.08. — Claim VOR dem Schnitt."
```

## 1 — Die Messung (frisch, und die Stellen sind geöffnet)

```text
YAMAS DREI ANORDNUNGEN vom 10.08.        Wirkung heute
  E1  Bau-Aussagen am COMMIT messen      0× in ARBEITSREGELN.md · 0× in den fuenf
      git show HEAD:<pfad> | diff - <pfad>   Rollenblaettern — steht NIRGENDS
      vor jedem CODE_FERTIG
  E2  §3-Kriterium in allen W-Blaettern   14 von 14 — wird gelebt, NICHT Teil dieses Auftrags
  E3  Spalte Unterformen mit Barriere     4× in STATUS.md · 0× in ARBEITSREGELN.md

DREI ZUSTANDSWORTE IM GEBRAUCH           im Regelwerk
  ERLEDIGT           A-06, Zeile 14      0 Treffer
  VORLAGE            P-02, Zeile 31      0 Treffer
  ZURUECKGESTELLT    W-21L, Zeile 51     0 Treffer
```

**Die drei Stellen geöffnet und gelesen** (Pflichtprüfung 7 — sie existiert, weil ich in A-20
17 Fälle gemeldet habe, ohne einen zu öffnen):

```text
Tafelzeile A-06   | **A-06** Probedaten Arbeits-DB | **ERLEDIGT** | – | ausgefuehrt 880eb726 …
Tafelzeile P-02   | **P-02** parallele Instanzen | `VORLAGE` | Plan-Pruefer | c2de1eec |
                    kein Bauauftrag, zaehlt nicht i…
Tafelzeile W-21L  | **W-21L** Lattung | `ZURUECKGESTELLT` | – | 717eb11c | OPERANDEN-GATE …
```

> **Hier standen zuerst Zeilennummern (`STATUS.md:14/:31/:51`), und sie waren beim ersten
> Gegenlesen schon falsch.** *Der Generator hat es an sich selbst gemessen: er notierte zehn
> Zeilennummern, und **alle zehn schlugen fehl** — `docs/STATUS.md` wuchs zwischen Messung und
> Gegenprobe von 6.779 auf 6.815 Zeilen, der A-21-Block wanderte um 37 Zeilen. **In einer Datei,
> in die fünf Rollen gleichzeitig schreiben, ist eine Zeilennummer kein Beleg, sondern ein
> Verfallsdatum.** Belegt wird ab hier über die Auftragskennung und den Feldnamen.*

> **P-02 definiert seine eigene Bedeutung in der Tafelzeile** — *„kein Bauauftrag, zählt nicht in
> §3". **Damit steht eine Zustandsregel im Kommentarfeld einer Tabellenzeile**, und niemand außer
> dem Leser genau dieser Zeile erfährt sie. Das ist derselbe Fehlertyp wie die vier Zustandsorte
> aus A-20: eine Regel an einem Ort, an dem keine Regel steht.*

## 2 — `ZURUECKGESTELLT` braucht keine Definition, sondern eine Abschaffung

**§3 hat den Zustand längst** — wörtlich aus dem Regelwerk:

```text
- `DECISION_BLOCKED`: eine ausdruecklich Yama vorbehaltene Entscheidung fehlt.
```

Und W-21Ls Grund, wörtlich aus seiner Tafelzeile: das Operanden-Gate wartet auf **zwei
Fachfragen bei Yama** (Restausgleich und die Wahl des `n`, beide aus F-053).

> **Das ist `DECISION_BLOCKED`, Wort für Wort.** *Wir führen dafür ein Phantasiewort, während der
> definierte Zustand daneben liegt und ungenutzt bleibt. **Ein zweites Wort für dieselbe Sache ist
> eine zweite Wahrheit** — genau das, was der Wächter verbietet.*

```text
ENTSCHEIDUNG   ZURUECKGESTELLT wird ABGESCHAFFT, nicht definiert.
               W-21L traegt DECISION_BLOCKED.
```

## 3 — `ERLEDIGT` und `VORLAGE` werden definiert, nicht ersetzt

Für diese zwei gibt es **keinen** passenden Zustand in §3, und beide bezeichnen etwas Echtes:

```text
ERLEDIGT   A-06 war ein AUSFUEHRUNGSauftrag (Probedaten anlegen), kein Bauauftrag.
           Er ist ausgefuehrt und gegengeprueft — aber er hat nie Code erzeugt, den
           man abnehmen oder freigeben koennte. BETRIEBSBESTAETIGT waere falsch:
           es gibt keinen veroeffentlichten Stand.

VORLAGE    P-02 ist ein Verfahrensvorschlag, kein Auftrag. Er wartet auf Yama und
           belegt keinen §3-Platz. ENTWURF waere falsch: ein ENTWURF will BEREIT
           werden, eine VORLAGE will ENTSCHIEDEN werden.
```

**Für jeden der beiden muss im Regelwerk stehen, was heute nur P-02s Kommentarfeld sagt:**
belegt er einen `§3`-Platz oder nicht. *Das ist die Angabe, wegen der es die Definition
überhaupt braucht — ohne sie muss jede Rolle raten, ob ein Auftrag den einen `IN_ARBEIT`-Platz
blockiert.*

## 4 — Auswirkungen (§5)

```text
ARBEITSREGELN.md  §3 bekommt ERLEDIGT und VORLAGE mit §3-Platz-Angabe.
                  §11 (oder wo Bau-Messung steht) bekommt E1 mit dem Befehl.
                  E3 wird dort verankert, wo der Zaehler beschrieben ist.
STATUS.md         genau EINE Zustandsaenderung: W-21L auf DECISION_BLOCKED.
                  P-02s Ad-hoc-Definition wird zum Verweis oder als Beleg gekennzeichnet.
E2                wird NICHT angefasst — 14 von 14, wird gelebt.
REIHENFOLGE       Bau erst NACH A-20s Abnahme. A-20 fasst dieselbe Datei an und ist
                  CODE_FERTIG; findet der Evaluator ein Rot, bessert A-20 dort nach.
                  Zwei Auftraege gleichzeitig in ARBEITSREGELN.md waeren ein Konflikt,
                  den kein §3 abfaengt, weil §3 Auftraege zaehlt und nicht Dateien.
```

## 5 — Abnahmekriterien

```text
A-21-1  E1 steht in ARBEITSREGELN.md, mit dem Befehl woertlich
        (git show HEAD:<pfad> | diff - <pfad>) und mit Yamas Datum 10.08. als Herkunft.
        Nachweis: grep 'E1' liefert Treffer, vorher 0.
A-21-2  E3 steht dort, wo der Zaehler beschrieben ist. Nachweis wie A-21-1.
A-21-3  BERICHTIGT nach dem Befund des Generators (605fde3b) — die erste Fassung
        verlangte '0 Treffer von ZURUECKGESTELLT in docs/STATUS.md' und war damit
        unerfuellbar, ohne Belege zu vernichten.
        W-21L traegt DECISION_BLOCKED an BEIDEN ZUSTANDSORTEN: Tafelzeile und
        zustand:-Feld. Nachweis mit einem Muster, das den Zustandsort BINDET —
        keine Volltextsuche.
        Volltext ergibt 14 Treffer, selbst nachgemessen und jede Stelle geoeffnet:
        2 sind der Zustand, 12 sind Belege und Fliesstext (zwei
        vertretungsentscheid:-Felder, die Yamas Anweisung zitieren, Befunde,
        Vergleichsmessungen, der Titel und der dor_beleg DIESES Auftrags).
        DIE 12 BLEIBEN ALLE STEHEN. A-20-4 verlangt woertlich, nicht zu loeschen
        ohne zu sagen was gegolten hat, und der Evaluator hat in 99fc86cd aus
        demselben Grund die 17 Meldebloecke stehen gelassen. Ein Kriterium, das
        einen Auftrag zwingt, seinen eigenen Titel zu loeschen, ist kein Kriterium.
A-21-4  ERLEDIGT und VORLAGE sind in §3 definiert, JE MIT der Angabe, ob sie einen
        §3-Platz belegen. Ohne diese Angabe ist die Definition unbrauchbar.
A-21-5  P-02s Tafelzeile enthaelt keine eigene Zustandsregel mehr, sondern verweist
        auf §3 — oder die alte Fassung bleibt ausdruecklich als BELEG gekennzeichnet
        stehen. Geloescht wird sie nicht.
A-21-6  BERICHTIGT nach demselben Befund. KEIN anderer Auftragszustand wurde
        geaendert — Nachweis AM COMMIT: git show <bau-sha> -- docs/STATUS.md zeigt
        geaenderte zustand:-Zeilen und Tafelzeilen ausschliesslich bei W-21L.
        Die erste Fassung verlangte git diff, und das ist untauglich: git diff misst
        den ARBEITSBAUM und ist nach einem Commit zwangslaeufig leer, also auch bei
        zwanzig geaenderten Fremdzustaenden gruen. Es ist woertlich der Mangel, den
        der Evaluator in 99fc86cd an A-20-5 gefunden hat — und genau die Messung, die
        E1 vorschreibt. Ein Blatt, das E1 ins Regelwerk schreiben soll, darf sie nicht
        im eigenen Kriterienblock verfehlen.
A-21-7  BERICHTIGT und VERSCHAERFT. A-20 ist BETRIEBSBESTAETIGT, bevor dieses Blatt
        gezogen wird — nicht nur ABGENOMMEN. Grund: nach der Abnahme kann
        RELEASE_BLOCKED folgen, und dann bessert A-20 in DERSELBEN Datei nach.
        Nachweis: der IN_ARBEIT-Commit nennt A-20s Zustand, gemessen am ELTER
        (git show <elter>:docs/STATUS.md), nicht am Arbeitsbaum. Die erste Fassung
        sagte 'Zustand zum Zeitpunkt des IN_ARBEIT' ohne Messort — der Generator hat
        zu Recht angemerkt, dass ein Nachweis, der an einem nicht nachholbaren
        Zeitpunkt haengt, das Kriterium fuer immer rot macht, wenn man zu frueh zieht.
```

**Nachweisform: Befehl und Trefferzeilen** (B5), und **mindestens eine Stelle je Zählung
geöffnet** (Pflichtprüfung 7).

```yaml
warum_P1: "Zwei der drei Punkte sind ANORDNUNGEN YAMAS vom 10.08., die nirgends stehen, wo eine
        Rolle sie liest. Eine Entscheidung, die gilt und unauffindbar ist, ist praktisch keine —
        so hat es der plan-pruefer formuliert, und er hat es zweimal selbst nachgemessen."
warum_ein_blatt: "Alle drei Punkte fassen ARBEITSREGELN.md an, zwei davon denselben §3. Getrennt
        geschnitten kollidieren sie in derselben Datei. Dieselbe Begruendung wie A-19 und A-20."
was_dieser_auftrag_NICHT_ist: "Keine Aenderung an einem Auftragsstand ausser W-21L, und die ist
        eine Umbenennung auf einen bereits definierten Zustand, keine neue Bewertung. W-21L
        bleibt blockiert und wartet auf genau dieselben zwei Fachfragen."
E2_bleibt_unberuehrt: "14 von 14 gemessen vom plan-pruefer — eine Regel, die gelebt wird, braucht
        keinen Auftrag."
```


## §11 — Votum A-21 (Evaluator, Erstinstanz, 12.08.2026)

```yaml
auftrag: "A-21"
votum: ABGENOMMEN
geprueft_an: "74efb8c2"
elter: "3026456e"
scope_diff: "3 Dateien, +251/-3: ARBEITSREGELN.md, BERICHT-A-21 (neu), STATUS.md.
  0 Code-Dateien. Der Bau-Commit traegt AUSDRUECKLICH NICHT die Fertigmeldung — das ist
  richtig gedacht, denn A-21-6 misst genau diesen Commit, und die eigene CODE_FERTIG-Zeile
  waere darin eine geaenderte Zustandszeile ausserhalb von W-21L gewesen."
pruefstand: "git worktree add -q --detach auf 74efb8c2."
browserabnahme: "ENTFAELLT — keine sichtbare Wirkung, 0 Code-Dateien."
paragraf_15: "GEGENSTANDSLOS — kein DB-Zugriff im Scope."

messtisch:

  A-21-1_E1_in_ARBEITSREGELN:
    urteil: ERFUELLT
    vorher_nachher: "E1 im Regelwerk: Elter 3026456e 0 Treffer, Bau 2. Trefferzeilen Z.504
      (Ueberschrift) und Z.675 (Zeile der E3-Tabelle, die auf E1 verweist)."
    steht_es_im_richtigen_paragrafen: "Z.504 liegt unter '## 11. Kurze Beweisberichte' (Z.419) —
      ich habe die umgebende Ueberschrift maschinell bestimmt, nicht geschaetzt."
    der_befehl_woertlich: "`git show HEAD:<pfad> | diff - <pfad>` steht als Codeblock da, dazu
      die Umkehrung `git show <bau-sha> -- <pfad>` mit der Begruendung, dass auch ein LEERER
      git diff kein Beleg ist. Das ist mehr als A-21-1 verlangt und genau die Lehre aus meinem
      A-20-5-Befund."
    yamas_herkunft: "'Yamas Anordnung vom 10.08., erteilt durch den Release-Pruefer in seinem
      Namen (Prozesspruefung 03)'. Ich habe docs/PROZESSPRUEFUNG-03.md geoeffnet: 168 Zeilen,
      E1 in Z.131, E3 in Z.142, Empfehlung beider in Z.147. Die Herkunft traegt."

  A-21-2_E3_beim_zaehler:
    urteil: ERFUELLT
    vorher_nachher: "E3: Elter 0, Bau 1 (Z.663)."
    steht_es_beim_zaehler: "Z.663 liegt unter '## 13. Pflichtpruefung nach jeweils zehn Aufgaben'
      (Z.618) — das IST der Zaehler. Die Unterform steht als Spalte in einer Tabelle mit drei
      Zeilen (Ort/V2, Zeitpunkt/V1, Zustand/NEU), nicht als fuenfte Klasse."

  A-21-3_ZURUECKGESTELLT_am_zustandsort_abgeschafft:
    urteil: ERFUELLT
    teil_a_zustandsort: "Mit einem Muster gemessen, das den Zustandsort BINDET, wie das Kriterium
      es verlangt: Tafelzeilen mit ZURUECKGESTELLT in der Zustandsspalte und `^zustand:
      ZURUECKGESTELLT`. Elter 1 + 1, Bau 0 + 0. W-21L traegt DECISION_BLOCKED an beiden Orten."
    teil_b_die_belege_bleiben: "Das ist der zweite Teil des Kriteriums, und er ist der wichtigere.
      Gemessen am Diff: der Bau entfernt GENAU 2 Vorkommen von ZURUECKGESTELLT, und beide sind
      die Zustandsorte (Tafelzeile und zustand:-Feld von W-21L). Entfernte Belege oder
      Fliesstextstellen: 0. Kein Beleg ist vernichtet worden."
    BEFUND_OHNE_ROT_die_volltextzahl: "Bericht und Statusfeld behaupten 'Volltext bleibt bei 14'
      mit der Rechnung '15 am Elter − 2 umgestellte + 1 neuer = 14'. GEMESSEN: der Bau fuegt
      DREI neue Vorkommen ein, nicht eines — 15 − 2 + 3 = 16, und ich messe am Bau-Stand
      tatsaechlich 16. Die zwei nicht mitgezaehlten sind seine eigenen Erlaeuterungsfelder in
      docs/STATUS.md: `ZWEI_KRITERIEN_STOSSEN_ANEINANDER...` (enthaelt 'ZURUECKGESTELLT ->
      DECISION_BLOCKED') und — daran liegt die Pointe — das Feld
      `volltext_bleibt_14_und_das_ist_absicht` SELBST. Die Behauptung erhoeht den Zaehler, den
      sie beziffert.
      KEIN ROT, und zwar aus drei Gruenden: das Kriterium misst ausdruecklich NICHT ueber
      Volltext, sondern zustandsortgebunden; sein sachlicher Kern (die Belege bleiben stehen)
      ist gemessen richtig; und die Zahl steht im Bericht, nicht im Regelwerk. Ich habe
      dieselbe Klasse eine Runde zuvor bei A-20 als Hinweis behandelt (die '31 mit
      Anfuehrungszeichen', am eigenen Commit schon 32) und bleibe dabei.
      Der Planner hat es im selben Blatt schon richtig benannt: 'in einer Datei, in die fuenf
      Rollen gleichzeitig schreiben, ist eine Zeilennummer kein Beleg sondern ein
      Verfallsdatum'. Fuer eine Volltextzahl in derselben Datei gilt das genauso — die 14 des
      Blattes stimmte fuer den DoR-Stand 45babc3a, den ich nachgemessen habe, und war bei
      Baubeginn bereits 15."

  A-21-4_ERLEDIGT_und_VORLAGE_in_paragraf3:
    urteil: ERFUELLT
    vorher_nachher: "Im Regelwerk am Elter: ERLEDIGT 0, VORLAGE 0. Am Bau je 2."
    die_geforderte_angabe_ist_da: "Beide tragen sie woertlich und nicht als Anhang:
      ERLEDIGT — 'Belegt eine IN_ARBEIT-Stelle nach §3: NEIN', Realfall A-06 (ausgefuehrt
      880eb726). VORLAGE — 'Belegt eine IN_ARBEIT-Stelle nach §3: NEIN', dazu 'zaehlt auch nicht
      im §13-Zaehler', Realfall P-02 (c2de1eec). Das Kriterium sagt, ohne diese Angabe sei die
      Definition unbrauchbar — sie steht in beiden."

  A-21-5_P02_tafelzeile:
    urteil: ERFUELLT
    gemessen: "Elter: '| **P-02** … | `VORLAGE` | … | kein Bauauftrag, zaehlt nicht im
      §13-Zaehler · Machtfrage …'. Bau: '… **`VORLAGE` ist seit A-21 in §3 definiert** — dort
      steht, was es heisst und dass es keinen §3-Platz belegt · *als BELEG, hier stand die Regel
      vorher ad hoc: „kein Bauauftrag, zaehlt nicht im §13-Zaehler"* · Machtfrage …'.
      Die alte Fassung ist NICHT geloescht, sondern woertlich als BELEG gekennzeichnet stehen
      geblieben — genau die zweite Variante, die das Kriterium zulaesst."

  A-21-6_kein_anderer_auftragszustand_geaendert:
    urteil: "ERFUELLT — und die Kriterienkollision, die der Generator mir VORGELEGT hat, ist
      hiermit entschieden."
    was_er_vorgelegt_hat: "Er meldet, A-21-5 und A-21-6 seien woertlich zugleich unerfuellbar:
      A-21-5 fordert genau die P-02-Tafelzeilenaenderung, die A-21-6s Nachweissatz ausschliesst.
      Er hat nichts umgedeutet, beide Zahlen genannt und den Rueckweg beziffert."
    meine_eigene_messung_am_commit: "`git show 74efb8c2 -- docs/STATUS.md`:
        geaenderte zustand:-Zeilen   1 Paar, ausschliesslich W-21L (ZURUECKGESTELLT ->
                                     DECISION_BLOCKED). FREMDE zustand:-Aenderungen: 0 —
                                     ich habe jede geaenderte Zeile auf ihren umgebenden
                                     Auftragsblock zurueckgefuehrt, nicht nur gezaehlt.
        beruehrte Tafelzeilen        2: W-21L in der ZUSTANDSSPALTE, P-02 NUR in der
                                     KOMMENTARSPALTE. P-02s Zustand ist an beiden Orten
                                     unveraendert VORLAGE."
    ENTSCHEIDUNG: "A-21-6 ist ERFUELLT. Der erste Satz des Kriteriums nennt den Zweck — 'KEIN
      anderer Auftragszustand wurde geaendert' —, und dieser Zweck ist gemessen erfuellt: kein
      fremder Zustand ist beruehrt. Der Nachweissatz danach ('zustand:-Zeilen UND Tafelzeilen
      ausschliesslich bei W-21L') ist ZU WEIT GEFASST, weil eine Tafelzeile mehr traegt als
      einen Zustand: sie hat eine Kommentarspalte, und genau dort verlangt A-21-5 die Aenderung.
      DAS IST DIE WIEDERKEHRENDE FEHLERKLASSE, diesmal im KRITERIUM statt in der Zusage: der
      Nachweis traegt den Namen des Kriteriums, misst aber etwas anderes als der Kriteriensatz
      sagt. Ich loese den Widerspruch zugunsten des belegten Zwecks und nicht zugunsten des
      weiteren Wortlauts — der Rueckweg des Generators (P-02s Tafelzeile zurueck) haette
      A-21-5 rot gemacht, und er hat zu Recht angemerkt, dass es umgekehrt nicht geht.
      AN DEN PLANNER, ohne Rot: der Nachweissatz gehoert auf 'geaenderte ZUSTANDSANGABEN
      ausschliesslich bei W-21L' verengt. Eine Tafelzeilenberuehrung ist nicht dasselbe wie
      eine Zustandsaenderung."

  A-21-7_wartebedingung:
    urteil: ERFUELLT
    selbst_gemessen: "Ich habe den IN_ARBEIT-Commit SELBST bestimmt statt ihn zu uebernehmen:
      durchgehend rueckwaerts den A-21-Zustand je Commit gelesen — IN_ARBEIT wird in 96b588e0
      gesetzt, dessen Elter ist 877f81ee, und dort steht A-21 noch auf BEREIT. Das deckt sich
      mit seiner Angabe.
      Am Elter 877f81ee, BEIDE Zustandsorte: Tafelzeile '**A-20** … **`BETRIEBSBESTAETIGT`**'
      und Datensatzfeld 'zustand: BETRIEBSBESTAETIGT'. Die verschaerfte Wartebedingung
      (BETRIEBSBESTAETIGT statt ABGENOMMEN) ist erfuellt, gemessen am Commit."

ohne_kriterium_eingetragen_und_gemeldet:
  fortsetzung_zustand: "Er traegt bei W-21L `fortsetzung_zustand: ENTWURF` ein, weil §3 beim
    Eintritt in DECISION_BLOCKED den vorherigen Pruefzustand verlangt, und meldet es
    ausdruecklich als 'ohne Kriterium, gemeldet statt stillschweigend'. Ich habe nachgesehen:
    W-21L traegt in den letzten 200 STATUS.md-Commits NUR ZURUECKGESTELLT und DECISION_BLOCKED —
    einen frueheren Pruefzustand gibt es am Zustandsort gar nicht. ENTWURF ist die richtige
    Ableitung (der Blattkopf trug ENTWURF, belegt in meiner A-20-Messung), aber sie ist
    abgeleitet und nicht am Zustandsort gemessen. Kein Mangel: kein Kriterium verlangt es, die
    Ableitung ist sachlich richtig, und er hat sie offengelegt statt sie einzuschmuggeln."

meine_eigenen_messfehler_in_dieser_runde:
  - "`grep -c 'ZURUECKGESTELLT'` zaehlt ZEILEN, nicht Treffer. Ich habe damit zuerst 15 und 16
     gemeldet und erst danach mit `grep -o | wc -l` gegengeprueft. Hier war beides gleich, weil
     keine Zeile das Wort zweimal traegt — aber die Zahl war Glueck und nicht Messung, und
     genau darum geht es in diesem Auftrag."

was_dem_generator_zusteht: "Zweimal hat er in diesem Bau etwas getan, was ein Bauender nicht tun
  muss. Erstens hat er den Bau-Commit ohne die eigene Fertigmeldung geschnitten, weil A-21-6
  genau diesen Commit misst — er hat das Kriterium auf sich selbst angewandt, bevor er gemessen
  hat. Zweitens hat er die Kriterienkollision nicht aufgeloest, sondern mir vorgelegt, mit beiden
  Zahlen und einem bezifferten Rueckweg, und den Satz dazu geschrieben: welche Menge gemeint ist,
  entscheidet nicht der Bauende. Dass die Volltextzahl daneben liegt, aendert daran nichts — sie
  ist eine Nebenrechnung zu einem Kriterium, das ausdruecklich anders misst."
```
