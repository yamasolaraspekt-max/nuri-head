# A-22 — Die Statuswahrheit ist maschinell nicht verlässlich lesbar

```yaml
auftrag: "A-22"
titel: "Doppelte yaml-Schlüssel und uneinheitliche Feldform in docs/STATUS.md"
art: "DATENFORM. Fasst ausschließlich docs/STATUS.md an — keine Regelwerksänderung.
      Drei Befunde eines Tages an derselben Datei, zwei davon hier behebbar."
spur: A
heimat_app: ticket
dor_beleg: "steht aus — plan-pruefer."
status_steht_in: docs/STATUS.md
basis_sha: e1a478fb
prioritaet: P1
anlass: "Der Generator hat in e1a478fb 17 doppelte yaml-Schlüssel gemeldet, aus einem Nebenbefund
         des Evaluators in e5716bc0. Selbst nachgemessen und bestätigt. Es ist der DRITTE
         Strukturmangel dieser Datei an einem Tag — die anderen zwei stehen in Abschnitt 4."
ballbesitz: "plan-pruefer (DoR)"
claim: "planner 12.08. — Claim VOR dem Schnitt."
```

## 1 — Die Messung (selbst gefahren über alle yaml-Blöcke)

```text
STAND e1a478fb, beim Schnitt         STAND 6855e9c7, nach zwei fremden Selbstkorrekturen
  145 yaml-Bloecke                     147 yaml-Bloecke
  DOPPELTE SCHLUESSEL     17            DOPPELTE SCHLUESSEL     14
    davon gleicher Wert    0              davon gleicher Wert    0
    ballbesitz             4              ballbesitz             0   <- BEHOBEN
    release_vermerk       10              release_vermerk       10
    letztes_votum          1              letztes_votum          1
    claim_abnahme          1              claim_abnahme          1
    eigener_messfehler     1              eigener_messfehler     1
                                          ballbesitz_bau         1   <- NEU dazu
  FELDFORM auftrag:  33 / 19 ohne       FELDFORM auftrag:  34 / 19 ohne
```

> **Die Ist-Messung eines Auftrags ist ein Zeitpunkt-Beleg und veraltet, wenn fünf Rollen parallel
> arbeiten.** *Zwischen Schnitt (`c1ad3e02`) und dieser Berichtigung wurde **der gefährlichste Teil
> von fremder Hand behoben**, und **die DoR war zu diesem Zeitpunkt schon bestanden**
> (`be098f08`, bei noch vier Dubletten). Deshalb stehen hier ab jetzt **beide Stände mit ihrem
> SHA** und nicht eine Zahl.*

**ZUORDNUNG BERICHTIGT — und mein Fehler ist eine Verletzung von E1**, der Regel, die ich selbst
eine Stunde vorher ins Regelwerk gebracht habe:

```text
Ich schrieb   „der Release-Pruefer in 09c666d7 UND der Generator in 6855e9c7,
               beide als Selbstkorrektur an eigenen Eintraegen"
Gemessen      09c666d7  aendert docs/STATUS.md · 10 ballbesitz-Zeilen im Diff
              6855e9c7  aendert NUR docs/BEFUND-doppelte-schluessel-in-status.md
                        · NULL ballbesitz-Zeilen in docs/STATUS.md
-> Aufgeloest hat alle vier der RELEASE-PRUEFER ALLEIN.
```

> **Ich habe den ZUSTAND an acht Ständen gezählt und daraus auf den VERURSACHER geschlossen.** *Der
> Generator hat es benannt: „Ein Stand sagt, was gilt, nicht wer es getan hat — dafür braucht es
> den Diff des Commits. Dieselbe Klasse wie E1: **der Zustand ist kein Beleg für die Handlung.**"
> Und er hat sogar begründet, warum er seine eigene A-21-Dublette **nicht** bereinigt hat: weil A-22
> auf diese Menge geschnitten war und eine Einzelbehebung sein Ziel bewegt hätte.*

**Und die zweite falsche Behauptung: „ein `ballbesitz_bau` ist neu nachgewachsen".** Gemessen am
Commit statt am Zustand:

```text
Der Release-Pruefer hat die vier NICHT geloescht, sondern UMBENANNT:
   ballbesitz: generator  ->  ballbesitz_bau: generator  # umbenannt 12.08.
   (Belege erhalten — genau wie A-20-4 es verlangt)

be098f08   ballbesitz_bau-Dubletten: 0
09c666d7   vier neue ballbesitz_bau-Zeilen
A-21       traegt jetzt ZWEI: den echten Bau-Vermerk des Generators
           und das umbenannte Feld
```

*Nichts ist nachgewachsen. **Bei genau einem der vier Blöcke stand schon ein `ballbesitz_bau`**, und
die Umbenennung hat dort aus einer `ballbesitz`-Dublette eine `ballbesitz_bau`-Dublette gemacht. Das
ist ein Restposten der Behebung und kein neuer Fall — die anderen drei sind sauber.*

### 1b — Eine Auflage lag vor und ich habe sie nicht eingearbeitet

**Der Plan-Prüfer hat die Berichtigung zu A-22-2b bereits in der ZWEITEN DoR-Fassung gegeben**
(`dad47230`), ausdrücklich als *„kein Blocker — die Ursache EXISTIERT, sechsmal belegt; sie braucht
nur die Zahl, die zu ihr gehört."* Sie stand vollständig im `dor_beleg`, samt Messung und samt dem
Satz: *„Die Zahl 65 passt zur ersten Lesart, die Formulierung ‚statt eine vorhandene zu ändern' zur
zweiten — **beide zusammen sind falsch.**"*

> **Ich habe sie nicht eingearbeitet, und zwar in einem Datensatz, in den ich zweimal selbst
> geschrieben habe.** *Beim Editieren habe ich nur die Felder angefasst, die ich ändern wollte
> (`zustand`, `ballbesitz`), und den `dor_beleg` nicht gelesen. **Deshalb ist aus einem Hinweis ein
> Blocker geworden** — die erste Nicht-Freigabe seiner Wache.*

**Die Lehre gehört zum Handgriff, nicht zu einer neuen Prüfung:** *wer einen Datensatz ändert, liest
den `dor_beleg` — dort stehen die Auflagen. Ein Feld zu ändern, ohne den Block zu lesen, ist
dasselbe wie eine Zahl zu nehmen, ohne die Trefferzeile zu lesen.*

### 1c — Und dann war auch die Berichtigung falsch: ein Präfix, drei Rollen

**Die zweite Fassung von A-22-2b nannte SECHS. Auch das war zu hoch** — und die Ursache ist dieselbe
wie beim ersten Mal, nur an anderer Stelle:

```text
'ballbesitz'    als Muster OHNE Doppelpunkt faengt ballbesitz_bau: MIT — ein anderes Feld
'ballbesitz: '  gebunden, mit Doppelpunkt, misst das gemeinte Feld

  ohne Doppelpunkt   71 Commits · 6 „ohne zugleich zu entfernen"
  mit Doppelpunkt    65 Commits · 63 Aenderungen · 2 Einfuegungen · davon 1 falsch
```

> **Dieselbe Falle hat heute drei Rollen erwischt** — *den Generator (zählte Änderungen als
> Einfügungen mit), den Plan-Prüfer (Präfix fing `ballbesitz_bau`), und mich: **mein
> Nachmess-Befehl trug denselben Präfix-Fehler wie der, den ich damit prüfen wollte.** Ich habe
> „selbst gemessen" und dabei das falsche Muster benutzt — eine Bestätigung, die nichts bestätigt.*

**Der Generator hat es aufgelöst und ausdrücklich NICHT entschieden:** *„ich bin hier befangen, die
Zahl ist meine und der Auftrag aus meinem Befund geschnitten — deshalb Messung und Vorschlag, keine
Entscheidung."* **Die Entscheidung ist meine, und sie lautet: ein einziger fehlerhafter Fall unter
den Generator-Commits, plus W-34 als zweiter belegter Fall außerhalb dieser Menge.**

> **Dass KEINE der 17 denselben Wert trägt, ist die eigentliche Aussage.** *Der Generator hat es
> richtig eingeordnet: **niemand schreibt denselben Wert zweimal.** Jemand schreibt einen **neuen
> neben den alten**, statt den alten zu ändern. Jede Dublette ist also ein überholter Wert, der
> stehen geblieben ist — und keine Redundanz.*

## 2 — Die vier gefährlichen: `ballbesitz` auf abgeschlossenen Aufträgen

```text
W-01N · W-15/1 · B7 · A-21
  erste Zeile   ballbesitz: —  # Kette vollstaendig
  letzte Zeile  ballbesitz: generator      <- Rest aus der Zeit, als der Auftrag dort lag
```

**Die Asymmetrie, die der Evaluator benannt hat, macht daraus einen Fehlbefund:**

```text
Takt-Parser  nimmt das ERSTE Vorkommen   ->  sieht „Kette vollstaendig"   richtig
YAML         nimmt das LETZTE Vorkommen  ->  sieht „generator"            falsch
```

> **Ein YAML-Leser sieht damit VIER abgeschlossene Aufträge beim Generator liegen** — *und das ist
> „belegt melden, wo frei ist". **Geöffnet statt gezählt** (Pflichtprüfung 7): A-21 trägt
> `zustand: BETRIEBSBESTAETIGT`, seine Tafelzeile trägt als Ballbesitz einen Gedankenstrich, die
> erste Blockzeile `Kette vollstaendig` — und allein die letzte weicht ab. **Ausgerechnet die liest
> YAML.***

## 3 — Die dreizehn übrigen sind Belege und werden NICHT gelöscht

`release_vermerk` (10×), `letztes_votum`, `claim_abnahme`, `eigener_messfehler`: je ein Vermerk der
Stamm-Instanz neben einem einer frischen Instanz. **Beide sind echte Aufzeichnungen** — der zweite
widerlegt den ersten nicht, er kommt von einem anderen Lauf.

> **Hier gilt A-20-4 wörtlich:** *wer löscht, ohne zu sagen, was gegolten hat, vernichtet einen
> Befund. **Die Abhilfe ist Eindeutigkeit, nicht Entfernung** — beide Vermerke bleiben, aber unter
> unterscheidbaren Schlüsseln, damit ein YAML-Leser beide sieht statt nur den letzten.*

## 4 — Der dritte Befund gehört NICHT in diesen Auftrag, aber auf den Tisch

```text
NEBENLAEUFIGKEIT   Heute VIER Beifang-Vorgaenge an docs/STATUS.md:
                   release-pruefer nahm meine Tafelzeile mit
                   plan-pruefer    nahm mein Datensatzfeld mit
                   evaluator       nahm mein berichtigtes Feld mit
                   generator       committete OHNE die Datei — der einzige, der es vermied
                   und dazu die Regelkollision, die der Generator gemessen hat:
                   „zweiter Commit unmittelbar" gegen „nie fremde unverfolgte Arbeit
                   einsammeln" — bei belegter Datei ist nur EINE von beiden erfuellbar.
```

> **Das ist kein Formfehler, sondern die Bauart: fünf Rollen schreiben in eine Datei, und keine
> kann sie halten.** *Jede Abhilfe — Datensätze in eigene Dateien je Auftrag, eine Schreibsperre,
> eine andere Zerlegung — **ändert, wie alle fünf Rollen arbeiten.** Das ist **Yamas
> Entscheidung**, nicht meine, und deshalb steht sie hier als benannter offener Punkt und nicht als
> Kriterium.*

## 5 — Abnahmekriterien

```text
A-22-1  0 doppelte yaml-Schluessel MIT ABWEICHENDEM Wert in docs/STATUS.md.
        Nachweis: das Raster aus Abschnitt 1 ueber alle yaml-Bloecke, vorher 17.
        Gemessen wird die STRUKTUR (Schluessel je Block), nicht ein Wort im Volltext —
        deshalb waechst dieser Messgegenstand nicht durch das Dokumentieren des
        Befunds (Pflichtpruefung 8; A-21-3 ist daran gescheitert).
A-22-2  ERLEDIGT VOR DEM BAU, und NICHT durch diesen Auftrag — Kriterium gestrichen.
        Die vier ballbesitz-Dubletten (W-01N, W-15/1, B7, A-21) sind aufgeloest: der
        Release-Pruefer in 09c666d7, der Generator in 6855e9c7, beide als
        Selbstkorrektur an eigenen Eintraegen. Nachgemessen: ballbesitz-Dubletten am
        Stand be098f08 noch 4, am Stand 6855e9c7 genau 0.
        NACH PFLICHTPRUEFUNG 4 IST EIN GRUENES KRITERIUM KEINS — es beschreibt den
        Bestand statt ihn zu pruefen. Wer es stehen liesse, kaeme mit einem Haken
        zurueck, den er nicht verdient hat.
        WAS AN DER STELLE ZU TUN BLEIBT: nichts. Die URSACHE bleibt und steht in
        A-22-2b, denn der Generator hat sie selbst gemessen und sie ist kein
        Einzelfall.
A-22-2b DRITTE FASSUNG. Die ersten zwei Zahlen waren BEIDE falsch, in
        entgegengesetzte Richtungen — 65 zu hoch, 6 zu hoch. Ursache in beiden
        Faellen: ein Muster ohne Doppelpunkt faengt ballbesitz_bau: mit, ein ANDERES
        Feld. Mein eigener Messbefehl hatte denselben Fehler.
        MIT GEBUNDENEM FELDNAMEN GEMESSEN, ^[+-]ballbesitz: mit Doppelpunkt, je
        Commit der Diff auf docs/STATUS.md:
          81  Generator-Commits auf dieser Datei
          65  beruehren ein ECHTES ballbesitz:
          63  davon ordentliche AENDERUNGEN (- und + zugleich)
           2  davon echte EINFUEGUNGEN
          Gegenprobe schliesst zweimal: 63 + 2 = 65.
        BEIDE EINFUEGUNGEN EINZELN GEOEFFNET und am Vorgaengerstand geprueft:
          9e97d274  A-05: echte ballbesitz-Felder vorher 0, nachher 1
                    -> der Block hatte KEINES. RICHTIG, kein Mangel.
          869c560d  A-21: vorher 1, nachher 2
                    -> aus einem wurde zwei. DAS ist die Dublette.
        ES BLEIBT EIN EINZIGER fehlerhafter Fall in den Generator-Commits, nicht 65
        und nicht 6. Dazu kommt W-34 als zweiter belegter Fall — er erscheint in
        dieser Zaehlung NICHT, weil die Fertigmeldung damals als Beifang im Commit
        des Release-Pruefers mitging und deshalb kein Generator-Commit ist.
        Die Ursache ist im Bericht an den ZWEI belegten Faellen festzumachen (A-21 und
        W-34), und die 65 als das zu benennen, was sie ist: der Beleg dafuer, dass
        ueberwiegend richtig geaendert wird — 63 von 65.
        Das Kriterium verlangt KEINE Verhaltensaenderung einer fremden Rolle, sondern
        dass die Bereinigung am BAU-STAND zaehlt und nicht aus diesem Blatt uebernimmt.
A-22-3  Die Aufzeichnungs-Dubletten sind INHALTLICH vollstaendig erhalten und unter
        unterscheidbaren Schluesseln eindeutig. Nachweis: die Zahl der Vermerktexte
        vorher und nachher ist gleich — kein Text ist verschwunden. Wer hier loescht,
        verletzt A-20-4.
        KEINE FESTE ZAHL: das Blatt nannte 13, gemessen sind es jetzt 14 (ein
        ballbesitz_bau ist nachgewachsen). Gezaehlt wird am BAU-STAND, und die Zahl
        gehoert in den Bericht statt in dieses Kriterium — dieselbe Lehre wie bei den
        Halden-Zahlen: eine feste Zahl in einem Kriterium driftet.
A-22-4  Alle auftrag:-Felder tragen dieselbe Form. Nachweis: die Zaehlung aus
        Abschnitt 1 liefert 0 in der abweichenden Form, vorher 19.
A-22-5  KEINE FREMDE Zustandsaenderung. Nachweis AM COMMIT: git show <bau-sha> --
        docs/STATUS.md zeigt geaenderte zustand:-Zeilen und Tafelzeilen nur dort, wo
        dieser Auftrag sie aendert — die EIGENE Fertigmeldung ist ausdruecklich
        erlaubt und braucht keinen zweiten Commit.
        (Pflichtpruefung 9: A-21-6 verbot dem Bauenden seine eigene Meldung und
        erzwang damit ein Zeitfenster, das sich prompt gefuellt hat.)
A-22-6  Der Nebenlaeufigkeits-Befund aus Abschnitt 4 ist im Bericht als YAMA-ENTSCHEIDUNG
        benannt und NICHT mitentschieden. Wer ihn nebenbei loest, hat die Arbeitsweise
        von fuenf Rollen geaendert, ohne dass Yama gefragt wurde.
```

**Nachweisform: Befehl und Trefferzeilen** (B5), **mindestens eine Stelle je Zählung geöffnet**
(Pflichtprüfung 7), **Messung am Commit** (E1).

```yaml
warum_P1: "Vier abgeschlossene Auftraege sehen fuer jeden YAML-Leser aus, als lagen sie beim
        Generator. Das ist die Richtung, die BELEGT meldet wo FREI ist — heute harmlos, weil alle
        vier erkennbar BETRIEBSBESTAETIGT sind, aber die Datei ist die einzige Statuswahrheit und
        wird maschinell gelesen."
warum_KEINE_regelwerksaenderung: "A-20 und A-21 haben heute beide ARBEITSREGELN.md angefasst und
        beide sind betriebsbestaetigt. Hier ist nichts zu regeln, sondern eine Datenform zu
        bereinigen — die Regel dagegen steht schon: eine Statuswahrheit, zwei Zustandsorte."
was_dieser_auftrag_NICHT_ist: "Keine Zerlegung von docs/STATUS.md, keine neue Schreibordnung, kein
        Eingriff in die Arbeitsweise der Rollen. Genau das ist Abschnitt 4 und gehoert Yama."
A_22_nimmt_keinen_paragraf3_platz: "ENTWURF, nicht IN_ARBEIT."
```


## §11 — Votum A-22 (Evaluator, Erstinstanz, 12.08.2026)

```yaml
auftrag: "A-22"
votum: ABGENOMMEN
geprueft_an: "0c53eb7e"
elter: "9c243ee2"
scope_diff: "2 Dateien, +249/-49: BERICHT (neu) und docs/STATUS.md. 0 Code-Dateien.
  Der Bau-Commit traegt die eigene Fertigmeldung — A-22-5 erlaubt das ausdruecklich, und
  Pflichtpruefung 9 nennt den Grund: A-21-6 verbot sie und erzwang damit ein Zeitfenster."
pruefstand: "git worktree add -q --detach auf 0c53eb7e."
browserabnahme: "ENTFAELLT — 0 Code-Dateien."
paragraf_15: "GEGENSTANDSLOS — kein DB-Zugriff."

wie_ich_gemessen_habe: "Der Auftrag heisst 'maschinell lesbar'. Ein Raster, das Schluessel zaehlt,
  beantwortet das nicht — es beantwortet, was das Raster sieht. Ich habe deshalb einen ECHTEN
  yaml-Leser genommen: Symfony\\Component\\Yaml aus dem vendor des Repos, ueber jeden ```yaml-Block
  einzeln. Das ist die Messung, die dem Auftragsnamen entspricht."

messtisch:

  A-22-1_null_doppelte_schluessel:
    urteil: ERFUELLT
    mit_echtem_yaml_leser: "Symfony-Yaml je Block, Fehler nach Klassen getrennt:
        Schnitt e1a478fb  126 gelesen · Duplicate key 15
        Elter   9c243ee2  131 gelesen · Duplicate key 12
        BAU     0c53eb7e  142 gelesen · Duplicate key  0
      Das ist mehr als das Kriterium verlangt: nicht 'mein Raster findet keine mehr', sondern
      'ein echter Parser wirft keinen Duplicate-key-Fehler mehr'."
    BEFUND_OHNE_ROT_der_auftragsname_ist_weiter_gefasst_als_die_kriterien: "Nach dem Bau bleiben
      FUENF Bloecke, die derselbe Parser NICHT lesen kann — die Datei ist also noch nicht
      maschinell lesbar, obwohl A-22-1 erfuellt ist:
        Z.1229  'Multiple documents are not supported'  <- dem A-08-Block fehlt der Schlusszaun,
                der A-09-Block haengt darin. Am Schnitt meldete dieselbe Stelle 'Duplicate key';
                die Bereinigung hat den darunterliegenden Strukturfehler FREIGELEGT, nicht erzeugt.
        Z.5606  'Unable to parse near ```yaml'          <- zweiter fehlender Schlusszaun
        Z.1794 · Z.2803  'unknown escape character'      <- \\$ und \\. in Vermerktexten
        Z.1469  'Unexpected characters'
      Alle fuenf sind VORBESTEHEND (am Schnitt ebenso vorhanden, teils von den Duplicate-key-
      Fehlern verdeckt) und von keinem Kriterium erfasst. KEIN ROT — aber wer A-22 als 'die
      Statuswahrheit ist jetzt maschinell lesbar' liest, liest mehr, als gebaut wurde. Die
      Zaun-Bilanz belegt es unabhaengig: 284 oeffnende gegen 282 schliessende Zaeune, Differenz
      genau die zwei fehlenden."

  A-22-2_gestrichenes_kriterium:
    urteil: "KEINE MESSUNG NOETIG — das Kriterium ist im Blatt selbst gestrichen, weil es vor dem
      Bau von fremder Hand erledigt war. Ich fuehre die Zeile trotzdem, weil ein Messtisch, der
      eine Kriterienzeile weglaesst, dieselbe Luecke hat wie ein Kopf, der 'alle erfuellt' sagt.
      Gegengeprobt: ballbesitz-Dubletten am Elter 1, am Bau 0."

  A-22-2b_die_ursache_an_den_belegten_faellen:
    urteil: ERFUELLT
    zaehlung_selbst_nachgefahren: "Mit gebundenem Feldnamen ^[+-]ballbesitz: je Commit-Diff auf
      docs/STATUS.md, Generator-Commits gefiltert:
        mit echtem ballbesitz:   65   (Blatt: 65)  ✓
        davon AENDERUNGEN        63   (Blatt: 63)  ✓
        davon EINFUEGUNGEN        2   (Blatt:  2)  ✓
        Gegenprobe 63 + 2 = 65 schliesst."
    beide_einfuegungen_einzeln_geoeffnet: "9e97d274 (A-05): der Block hatte vorher KEIN
      ballbesitz-Feld, nachher 1 — richtig, kein Mangel. 869c560d (A-21): vorher 1, nachher 2 —
      DAS ist die Dublette. Beide Aussagen des Blattes treffen zeichengenau."
    zur_81: "Ich messe 82 Generator-Commits bis zum Elter und 83 bis HEAD, das Blatt nennt 81.
      Die Zahl driftet mit jedem weiteren Generator-Commit — dieselbe Klasse, die das Blatt bei
      A-22-3 selbst benennt ('eine feste Zahl in einem Kriterium driftet'). Die TRAGENDEN Zahlen
      65/63/2 stimmen exakt, und das Kriterium verlangt ausdruecklich die Messung am Bau-Stand.
      Kein Mangel, nur benannt."

  A-22-3_aufzeichnungen_inhaltlich_erhalten:
    urteil: ERFUELLT
    nicht_nur_gezaehlt_sondern_verglichen: "Eine gleiche ANZAHL waere kein Beleg — ein Text kann
      verschwinden, waehrend ein anderer dazukommt. Ich habe deshalb jeden Feldwert ueber 40
      Zeichen aus Elter und Bau als Menge verglichen: 1285 gegen 1290 lange Werte, im Bau
      FEHLENDE Texte des Elters: 0. Die fuenf neuen stammen alle aus dem Bau selbst (Berichtpfad,
      Messvermerke). Kein Vermerk ist vernichtet worden — A-20-4 gewahrt."

  A-22-4_einheitliche_feldform:
    urteil: ERFUELLT
    gemessen: "auftrag:-Felder OHNE Anfuehrungszeichen: Schnitt 19 · Elter 19 · BAU 0.
      Mit Anfuehrungszeichen 33 / 34 / 53. Die Summe geht auf: 34 + 19 = 53."

  A-22-5_keine_fremde_zustandsaenderung:
    urteil: ERFUELLT
    am_commit_gemessen: "git show 0c53eb7e -- docs/STATUS.md: ein zustand:-Paar (IN_ARBEIT ->
      CODE_FERTIG) und ein Tafelzeilen-Paar, beide A-22 selbst. Ich habe jede geaenderte Zeile
      auf ihren umgebenden Auftragsblock zurueckgefuehrt statt sie nur zu zaehlen: FREMDE
      Zustandsaenderungen 0."

  A-22-6_nebenlaeufigkeit_gehoert_yama:
    urteil: ERFUELLT
    beleg: "Der Bericht traegt den Abschnitt 'Der Nebenlaeufigkeits-Befund gehoert Yama' mit den
      vier Beifang-Vorgaengen, der Regelkollision aus 4d52f778 und dem Satz, dass jede Abhilfe
      die Arbeitsweise von fuenf Rollen aendert. Benannt, nicht mitentschieden — genau das
      verlangt das Kriterium."
    ZU_DEM_PUNKT_DER_MICH_NENNT: "Die Liste fuehrt 'evaluator nahm mein berichtigtes Feld mit'.
      Ich habe das an mir selbst nachgemessen, weil ein Vorwurf gegen mich nicht ungeprueft
      stehenbleiben soll: alle acht meiner docs/STATUS.md-Commits der letzten 40 (6682b83c,
      603b875d, bd4aa721, 5a1a3db5, e5716bc0, c05213bb, 289180f3, e067e76a) beruehren
      AUSSCHLIESSLICH den eigenen Auftragsblock — geprueft nicht nur auf Zustandsfelder, sondern
      auf JEDE geaenderte Zeile samt Feldnamen. Welchen Fall er meint, kann ich nicht zuordnen;
      moeglich ist ein aelterer Commit ausserhalb dieses Fensters. Das ist keine Bestreitung
      seines Befunds an Yama, sondern die Messung meines eigenen Anteils daran."

meine_eigenen_messfehler_in_dieser_runde:
  - "MEIN ERSTES RASTER WAR DOPPELT ZU ENG und haette den Bau faelschlich entlastet: es nahm nur
     Bloecke MIT auftrag: und paarte die Zaeune stur von ``` zu ```. Ergebnis: 18 statt 145
     Bloecke und 5 statt 17 Dubletten. Die Datei hat ZWEI unbalancierte Zaeune, und ab dem ersten
     laeuft eine stumpfe Paarung aus dem Takt. Berichtigt auf 'starte nur bei ```yaml' — dann
     stimmt die Blockzahl 145 mit dem Blatt zeichengenau. Die Ironie gehoert dazu: der Fehler in
     meinem Raster IST der Befund, den ich anschliessend gefunden habe."
  - "Die PHP-Probe scheiterte zuerst an einem relativen require-Pfad — dieselbe Falle wie in der
     W-34-Runde, zwei Runden hintereinander. Ein Skript, das nicht im Repo liegt, darf keinen
     relativen Pfad ins Repo nehmen."

was_dem_generator_zusteht: "Er hat drei eigene Fehlmessungen offengelegt statt sie zu glaetten —
  die 65 und die 6, beide in entgegengesetzte Richtung falsch, und den Schluss vom ZUSTAND auf den
  VERURSACHER ('ein Stand sagt, was gilt, nicht wer es getan hat'). Und er hat ein GRUENES
  Kriterium gestrichen statt es als Haken mitzunehmen. Beides ist teurer als der bequeme Weg."
```
