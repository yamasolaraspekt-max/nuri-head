# B6 — eine Summe braucht eine Erhebung, keine Sammlung

```yaml
auftrag: "B6"
art: "siebte Barriere. NICHT dieselbe Klasse wie B5 — Yama hat sie ausdruecklich getrennt."
titel: "Wer eine Gesamtzahl ueber eine Menge meldet, definiert zuerst die MENGE"
spur: A
heimat_app: ticket
status: ENTWURF
status_steht_in: docs/STATUS.md
basis_sha: 1e09280d
prioritaet: P1
anlass: "Yamas Auflage 12.08. Abschnitt 3. Mein Fehler: 640 gemeldet, 1.593 erhoben."
verursacher: planner
ballbesitz: "plan-pruefer (DoR), danach generator"
claim: "planner 12.08. — Claim VOR dem Schnitt."
```

## Die Regel, wörtlich wie Yama sie gesetzt hat

> **B6 · Eine Summe braucht eine Erhebung, keine Sammlung.**
> *Wer eine Gesamtzahl über eine Menge meldet, definiert zuerst die Menge (Pfad, Muster,
> Abgrenzung), erhebt sie vollständig und meldet Menge **und** Summe. Was beim Suchen nebenbei
> aufgefallen ist, ist ein **Fund**, keine Summe — und wird als Fund gemeldet.*

## Der Vorfall — und warum es NICHT B5 ist

```text
GEMELDET   "ueber 640 Zeilen Prozessebene"   (783d47c1, an Yama)
ERHOBEN    1.593 Zeilen, ACHT Bausteine      (Nachtrag in VORLAGE-REGISTERZEILEN)
FEHLEND    StartView.tsx        267 Z   ganz uebersehen
           FaehigkeitenNavi.tsx  76 Z   ganz uebersehen
           EngineFlaeche.tsx    196 Z   als "(Teil)" gefuehrt statt gezaehlt
           studioDaten.ts       257 Z   als "(Teil)" gefuehrt statt gezaehlt
```

**Yamas Abgrenzung, und sie ist der Kern des Auftrags:**

> *„Das ist **nicht** B5. B5 fängt ein Zählergebnis ohne Trefferzeilen. Hier war das Zählen gar nicht
> das Problem — **die Menge war nie definiert.**"*

```text
B5  ich habe GEZAEHLT und die Zeilen nicht GELESEN
    -> Gegenmittel: denselben Lauf ohne -c fahren
B6  ich habe nie gesagt, WORUEBER ich zaehle
    -> Gegenmittel: die Menge zuerst benennen, dann erheben
    -> B5 haette hier NICHT geholfen: jede einzelne Zeilenzahl war richtig.
       Falsch war, dass fuenf von acht Dateien nie in der Menge waren.
```

> **Meine eigene Ursachenanalyse, die Yama präziser nennt als den Fehler:** *„Ich habe gezählt, was
> mir bei der Suche nach den drei Fragen **begegnet** ist, und daraus eine Summe gemacht — statt die
> Ebene zu **erheben**."* **Eine Sammlung ist ein Nebenprodukt einer anderen Suche. Eine Summe ist
> eine Behauptung über eine vollständige Menge. Ich habe das erste als das zweite gemeldet.**

## DECISION

```text
WO      scripts/commit-pruefen.sh — dasselbe Tor wie B4 (Rollenmarke) und B5.
WAS     Das Tor kann eine Menge nicht pruefen. Es kann pruefen, ob eine Botschaft eine
        SUMMENBEHAUPTUNG traegt ("insgesamt", "zusammen", "Summe", "ueber N Zeilen",
        "N Dateien") OHNE die Menge zu nennen (Pfad, Muster, Anzahl der Teile).
FORM    Warnung, nicht Abbruch — dieselbe Stufe wie B5, dieselbe Begruendung:
        eine Barriere, die bei jedem zweiten Commit falsch anschlaegt, wird
        abgeschaltet (A-03-Beleg: Riegel um artisan serve, benutzt wurde php -S).
DAZU    Eine Zeile im Pruefweg: "Summe? Dann Menge zuerst benennen und vollstaendig
        erheben. Sonst als FUND melden, nicht als Summe."
NICHT   Kein Verbot von Summen. Verboten ist die Summe OHNE Menge.
GEMEINSAM MIT B5   Beide Barrieren gehoeren in dasselbe Tor und sollten in EINEM Bau
        eingesetzt werden — sie teilen die Stelle, die Form (Warnung) und den Pruefweg.
        Der Generator entscheidet, ob ein Auftrag oder zwei; als Blaetter sind sie
        getrennt, weil die KLASSEN getrennt sind.
```

> **Die Unterscheidung, die ins Blatt muss — sonst wird B6 als „keine Zahlen mehr" gelesen:**
>
> ```text
> ERLAUBT   "StartView.tsx 267 Zeilen"                 eine Zahl ueber EIN Ding
> ERLAUBT   "acht Bausteine, zusammen 1.593 Zeilen:    Summe MIT Menge
>            StartView 267 · ConfigWizard 271 · …"
> ERLAUBT   "gefunden beim Suchen: ConfigWizard (271 Z)  als FUND gekennzeichnet
>            und GuidedView (165 Z) — keine Erhebung"
> VERBOTEN  "ueber 640 Zeilen Prozessebene"            Summe ohne Menge
> ```

## Nicht-Ziele

- **Kein Abbruch am Tor.** Warnung.
- **Keine Prüfung, ob die Menge vollständig ist.** Das kann kein Tor — es kann nur fragen, ob eine
  genannt wurde.
- **Kein Anfassen von B1–B5.** B6 tritt daneben.
- **Keine Änderung an `resources/**`.**

## Scope

```text
scripts/commit-pruefen.sh    Warnzeile ergaenzen
docs/ARBEITSREGELN.md        B6 in die Barrierenliste, MIT der Abgrenzung zu B5
<Pruefweg>                   eine Zeile — der Generator MISST zuerst, wo er steht (wie B5-5)
```

## Wiederverwendungsprüfung (§5)

```text
B5-Blatt             VORHANDEN (docs/auftraege/aktiv/B5-...) — dieselbe Stelle, dieselbe Form,
                     dieselbe Begruendung fuer Warnung statt Abbruch
                     -> B6 uebernimmt die Struktur; NICHTS wird neu erfunden
scripts/commit-pruefen.sh   VORHANDEN, traegt Rollenmarke, Pfadpruefung, Index-Angleichung
A-10                 VORHANDEN — "sag es, wenn du nichts hast".
                     B5: sag WAS du gezaehlt hast. B6: sag WORUEBER.
                     Drei Geschwister derselben Familie.
```

## Auswirkungen (§5)

```text
API · Schema · Bestandsdaten · Bundle   KEINE
Produktivcode                           KEINER (scripts/, kein app/, kein resources/)
Prozessbindung                           JA — gilt fuer alle fuenf Rollen
Werkzeuge                                bash; die scripts-Suite bleibt gruen
```

**Erstnutzer:** *ich, beim nächsten Summenbefund. **Und wie bei B5 ist der Test, ob sie MICH fängt.***

## Akzeptanzkriterien

**B6-1 (P1, die Warnung feuert):** Eine Botschaft mit einer Summenbehauptung ohne genannte Menge
löst eine Warnung aus. **Rot heute:** `grep -cE 'B6|Summe.*Menge' scripts/commit-pruefen.sh` → **0**.

**B6-2 (P1, sie feuert NICHT bei einer Summe MIT Menge):** Eine Botschaft, die Menge **und** Summe
nennt, löst **keine** Warnung aus. *Nachweis: zwei Probeläufe, einer feuert, einer schweigt, beide
Ausgaben im Bericht.* **Ohne diesen Gegenbeleg ist die Barriere eine Belästigung.**

**B6-3 (P1, sie feuert NICHT bei Einzelzahlen):** `Suite 1692/1692`, `0 Platzhalter`,
`StartView.tsx 267 Zeilen` → **keine Warnung.** *Das sind Zahlen über ein Ding, keine Summen.*

**B6-4 (P1, kein Abbruch):** Rückgabewert unverändert. *Nachweis wie B5-3.*

**B6-5 (P1, die ABGRENZUNG zu B5 steht im Regeltext):** `ARBEITSREGELN.md` nennt beide und den
Unterschied — *B5 fängt eine ungelesene Zählung, B6 eine undefinierte Menge.* **Ohne die Abgrenzung
verschmelzen sie und eine der beiden wird nicht angewandt.**

**B6-6 (P1, der Prüfweg wird GEMESSEN):** Wie `B5-5` — der Generator misst, wo der Prüfweg steht,
und sagt es, falls es keinen gemeinsamen gibt. *Ich habe es nicht gemessen und behaupte es nicht.*

**B6-7 (`must_preserve`):** `resources/**` und `app/**` byte-identisch; die bestehenden Torfunktionen
unverändert — **`git diff` zeigt nur Einfügungen, 0 gelöschte Zeilen.**

**B6-8 (P1, §3 wird BELEGT):** Befehl mit Ausgabe, an beiden Orten, **unmittelbar vor der ersten
Änderung**.

## Kantenliste

```text
"B6 heisst: keine Zahlen mehr"     -> FALSCHLESUNG. B6-3 misst die Gegenprobe.
Warnung bei jeder Zahl             -> B6-3 faengt es.
B5 und B6 verschmelzen             -> B6-5 verlangt die Abgrenzung im Regeltext.
"insgesamt" in einem Fliesstext
  ohne Zahlenbezug                 -> soll NICHT feuern. Der Generator waehlt das
                                      Muster so, dass eine Zahl in der Naehe stehen muss.
Pruefwegdatei existiert nicht      -> B6-6: dann sagt er das.
```

## Rückweg und Entdeckung

**Rückweg:** eine Warnzeile, eine Regelzeile, eine Prüfwegzeile. `git revert`; das Tor bleibt in
jeder Zwischenstufe funktionsfähig, weil die Warnung den Rückgabewert nicht ändert.

**Entdeckung:** Tritt ein siebter Fall auf — *eine Summe ohne Menge* —, hat die Warnung nicht
gewirkt. **Dann ist die nächste Stufe eine harte Sperre.** *Und der Preis dafür ist an sechs Fällen
belegt.*

## Konfliktprüfung (§5)

```text
§3 UNMITTELBAR gemessen   1 IN_ARBEIT -> W-22/1, Scope werkbank/W-22/** + REGISTER.md
B6 (dieses)               scripts/commit-pruefen.sh + ARBEITSREGELN.md + Pruefweg
                          -> disjunkt zu W-22
B5                        TEILT die Datei scripts/commit-pruefen.sh.
                          -> §3 loest es: beide sind ENTWURF, nur einer geht IN_ARBEIT.
                          -> EMPFEHLUNG an den Plan-Pruefer: B5 und B6 in EINEM Bau,
                             weil sie dieselbe Stelle, dieselbe Form und denselben
                             Pruefweg teilen. Zwei Bauten hintereinander an derselben
                             Zeile erzeugen den zweiten Diff umsonst.
A-09 / A-11               haben das Tor beruehrt, beide VEROEFFENTLICHT. Basis frei.
```

```yaml
fehlerklasse: "Messweg — sechster Fall, aber EIGENE Klasse (nicht B5)"
verursacher: planner
prioritaet: P1
warteschlange: "mit B5 zusammen, vor W-07N — beide sichern kuenftige Messungen ab"
abgrenzung_zu_b5: "B5 = gezaehlt, aber nicht gelesen. B6 = nie gesagt, worueber.
                   Bei meinem Vorfall war JEDE Einzelzahl richtig — falsch war, dass
                   fuenf von acht Dateien nie in der Menge waren. B5 haette nicht geholfen."
kern: "eine Sammlung ist ein Nebenprodukt einer anderen Suche. Eine Summe ist eine
       Behauptung ueber eine vollstaendige Menge. Ich habe das erste als das zweite gemeldet."
```

## §11 — Votum B6 (Evaluator, Erstinstanz, 12.08.2026)

```yaml
auftrag: "B6"
votum: ABGENOMMEN
fehlerklasse: KEINE
abnahme_commit: "6ecf911c"
elter: "7be1a381"
in_arbeit_commit: "30457e2b"
pruefstand: "worktree --detach auf 6ecf911c und 7be1a381, node_modules UND vendor an BEIDEN
     Staenden — die scripts-Suite braucht vendor, sonst faellt A-03-4 und ich melde meinen
     eigenen Aufbau als Regression (bei B5 passiert)."
pruefform: "Wie bei B5: die Barriere AUSLOESEN, nicht ihren Code lesen. Zusaetzlich zwei Fragen,
     die B5 nicht hatte — verdecken sich die beiden Waechter, und stimmt das Werkzeug mit dem
     Regeltext ueberein, der es beschreibt."

messtisch_alle_acht:
  B6-1: GRUEN
    rot_lage: "grep -cE 'B6|Summe.*Menge' scripts/commit-pruefen.sh — Elter 0 (wie das Blatt
            sagt), Bau 9."
    ausgeloest: |
      $ TICKET_ROLLE=evaluator bash scripts/commit-pruefen.sh \
          "insgesamt 47 Treffer im Haus, damit ist die Lage klar" docs/a.md
      B6-WARNUNG  Summenbehauptung ohne genannte Menge (Pfad, Muster, Aufzaehlung).
                  Eine Zahl ueber EIN Ding ist in Ordnung. Eine Zahl ueber eine MENGE
                  braucht die Menge dazu: worueber wurde erhoben, und war es vollstaendig?
                  Was beim Suchen nebenbei auffiel, ist ein FUND — dann sag Fund, nicht Summe.
                  Warnung, kein Abbruch — der Commit laeuft weiter.
      exit=0
  B6-2: GRUEN
    beleg: "'ueber alle 13 Dateien in geometry/ mit Muster bestanden gemessen: insgesamt 47
            Treffer, Trefferzeilen in wandaufbau.ts:41' -> B6 0 Warnungen (und B5 ebenfalls 0)."
  B6-3: GRUEN
    beleg: "Alle drei Einzelzahlen des Kriteriums einzeln gefahren:
              'Suite 1692/1692'          -> B6 0, exit 0
              '0 Platzhalter'            -> B6 0, exit 0
              'StartView.tsx 267 Zeilen' -> B6 0, exit 0"
  B6-4: GRUEN
    beleg: "exit=0 bei feuernder wie bei schweigender Botschaft, jeweils direkt nach dem
            Tor-Aufruf gemessen."
    mein_messfehler_dabei: "Mein erster Durchgang meldete exit=1 bei allen drei B6-3-Proben.
            Ursache war MEIN Aufbau: das $? stand in der printf-Zeile NACH zwei grep -c-Aufrufen
            und trug deren Rueckgabewert (grep gibt 1 bei null Treffern). Direkt nach dem Tor
            gemessen: exit=0. Beinahe haette ich 'die Barriere sperrt' gemeldet — der Befund
            waere frei erfunden gewesen."
  B6-5: GRUEN — und die Abgrenzung ist am Werkzeug nachgemessen, nicht nur gelesen
    beleg: "ARBEITSREGELN §18c, neu (Elter: 1 B6-Erwaehnung, Bau: der ganze Abschnitt).
            Die Trennung steht als Tabelle:
              B5 | ich habe GEZAEHLT und die Zeilen nicht GELESEN | denselben Lauf ohne -c
              B6 | ich habe nie gesagt, WORUEBER ich zaehle      | Menge zuerst benennen"
    die_vier_regelbeispiele_selbst_gefahren: |
      "StartView.tsx 267 Zeilen"                          ERLAUBT  -> B6 0  ✓
      "acht Bausteine, zusammen 1593 Zeilen: StartView…"  ERLAUBT  -> B6 0  ✓
      "gefunden beim Suchen: ConfigWizard (271 Z)"        ERLAUBT  -> B6 0  ✓
      "ueber 640 Zeilen Prozessebene"                     VERBOTEN -> B6 1  ✓
    warum_das_zaehlt: "Der Regeltext beschreibt vier Faelle. Alle vier verhalten sich am Tor
            genau so. Regel und Werkzeug sagen dasselbe — gemessen, nicht angenommen."
  B6-6: GRUEN
    beleg: "Die B6-Zeile steht in allen fuenf 4-WAS-ICH-ABLIEFERE.md (je 1 Treffer, einzeln
            gezaehlt). Beim Evaluator-Blatt Zeile 15: '| Menge je Summe | B6: Summe? Dann Menge
            zuerst benennen (Pfad, Muster, Abgrenzung) und vollstaendig erheben …'"
  B6-7: GRUEN
    beleg: "resources/ und app/ 0 Dateien · Tor +32/-0 · scripts-Suite Bau 107/107/0,
            Elter 107/107/0."
    b5_unversehrt: "Der B5-Block ist an Elter und Bau BYTEGLEICH (md5 ee42893e… beide).
            Zusaetzlich die Mutation: B6-Block entfernt (Anker genau 1x), dieselbe Botschaft
            -> B6 0 Warnungen, B5 feuert WEITER. Die beiden Waechter verdecken einander nicht.
            Zurueckgestellt, md5 identisch."
  B6-8: GRUEN mit P2 zur Form
    beleg: "30457e2b (09:36:28) setzt BEIDE Orte, 13,5 Minuten vor dem Bau (09:50:00).
            Die Botschaft nennt beide Zahlen UND beide Trefferzeilen mit Zeilennummer:
            Tafelzeile 1 (STATUS.md:36), Zustandsfeld 1 (STATUS.md:2011).
            SELBST nachgeschlagen im Commit: :36 ist die B6-Tafelzeile, :2011 ist
            'zustand: IN_ARBEIT'. Beide Zahlen nachgemessen: je 1. Alles exakt."
    p2: "Ein woertlicher BEFEHL ist nicht zitiert — die Botschaft nennt 'nach der frisch
            verankerten Methode gemessen' statt '$ grep -cE …'. Dieselbe schmale Luecke, die ich
            bei W-07N Runde 2 als P2 geführt habe; ich halte denselben Massstab. Der Zweck des
            Kriteriums ist erfuellt und von mir nachgemessen."

was_diesen_bau_heraushebt:
  - "Er hat die Barriere an ECHTEN Botschaften gemessen, nicht an erfundenen — und der Regeltext
     traegt vier Beispiele, die ich alle vier am Tor nachfahren konnte."
  - "Sein §3-Beleg ist der erste, bei dem das breite Muster benutzt wird — mit der Bemerkung, dass
     mit dem alten [AW]-Muster 'die Tafelzeile hier wieder 0 gemeldet haette und B6 unsichtbar
     gewesen waere wie B5'. Das ist derselbe Befund, den ich in meinem B5-Votum an meinem eigenen
     Takt-Werkzeug festgestellt habe, eine Runde spaeter unabhaengig bestaetigt."
  - "Die Reihenfolge B5-dann-B6 hat er GEMESSEN statt abgewartet: 'B5 steht seit b7ab49c5 auf
     ABGENOMMEN, damit ist die Datei frei'."

meine_eigene_falle:
  was: "exit=1 bei drei Proben gemeldet — Ursache war mein $? nach zwei grep-Aufrufen, nicht das
        Tor. Direkt gemessen: exit=0. Das waere ein Befund gegen B6-4 gewesen, also gegen das
        Kriterium 'kein Abbruch' — der schwerste, den man einer Warn-Barriere machen kann."
  lehre: "Bei jeder Abweichung zuerst den eigenen Aufbau. Heute zum dritten Mal, und jedes Mal
        war es mein Aufbau."

zusammenfassung: "Acht von acht. Die Barriere feuert bei der Summe ohne Menge, schweigt bei
     Summe MIT Menge und bei allen drei Einzelzahlen, und sperrt nicht — alles ausgeloest, nicht
     gelesen. Der Regeltext und das Werkzeug stimmen in allen vier dokumentierten Faellen
     ueberein. B5 ist bytegleich unberuehrt, und die Mutation zeigt, dass die beiden Waechter
     unabhaengig voneinander feuern. Ein P2 zur Belegform des §3, derselbe Massstab wie bei W-07N."

ballbesitz: release-pruefer
```
