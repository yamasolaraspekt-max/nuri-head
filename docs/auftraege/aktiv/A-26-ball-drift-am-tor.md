# A-26 — Dreimal an einem Tag, drei Rollen. Meine Regel hat die Fehlerklasse erzeugt, also gehört sie mir

```yaml
auftrag: "A-26"
titel: "Zustands- und Ball-Drift zwischen den zwei A-20-Orten am TOR fangen, nicht hinterher melden"
art: "BAU — eine Barriere in scripts/commit-pruefen.sh, Bauform wie F-14, B5 und B6."
spur: A
heimat_app: ticket
status_steht_in: docs/STATUS.md
basis_sha: d3d234a6
prioritaet: P1
ballbesitz: "plan-pruefer (DoR)"
claim: "planner 13.08. — Claim VOR dem Schnitt."
haengt_an: "A-25 — solange fünf Datensätze in EINEM yaml-Block liegen, kann kein Muster den Datensatz
            sicher dem Auftrag zuordnen. Genau daran ist der Takt-Scan des Evaluators gescheitert.
            A-26 wird NICHT vor A-25 gebaut."
anlass: "Der Release-Prüfer hat den dritten Fall an einem Tag nachgezogen (38bc5e12) und die Ursache
         benannt: 'seit A-20 gibt es ZWEI Zustandsorte, und der zweite liegt räumlich weit vom ersten
         entfernt. Wer im Auftragsblock arbeitet, sieht die Tafel nicht.' A-20 ist meine Regel."
grundlage: "38bc5e12 als dritter Fall · scripts/commit-pruefen.sh mit F-14/B5/B6 als Bauform ·
            docs/STATUS.md, Tabellenkopf und Datensatzfelder selbst gemessen"
```

## 1 — Der tragende Punkt: es ist keine Nachlässigkeit, es ist eine Folge meiner Regel

**Drei Fälle an einem Tag, drei verschiedene Rollen** *(aus `38bc5e12`, und der Schluss ist seiner)*:

```text
W-36   nach der Nicht-Freigabe   Datensatz gepflegt, Tafelzeile vergessen
W-33   nach dem Zug              dieselbe Bauart
W-31   nach der Nicht-Freigabe   dieselbe Bauart — und die Botschaft des
                                 Verursachers sagt sogar 'Ball und Botschaft
                                 nachgezogen'; am Diff gemessen kommt die
                                 Tafelzeile in seinem Diff nicht vor.
```

> **„Es trifft verschiedene Rollen, also ist es keine Nachlässigkeit einer einzelnen."** *Und der Grund
> ist meiner: **A-20 hat den Zustand von vier Orten auf zwei reduziert — aber zwei sind nicht eins.**
> A-20-2 verlangt Tafelzeile UND Datensatz im selben Commit; es gibt nichts, das das **prüft**. Wer im
> Auftragsblock arbeitet, sieht die Tafel nicht — sie liegt hunderte Zeilen entfernt in derselben Datei.*

**Der schwerste Teil ist der dritte Fall:** *der Verursacher **glaubte**, beide Orte gepflegt zu haben,
und schrieb es in die Botschaft. **Ein Vorsatz reicht hier nicht, weil der Fehler nicht im Willen
liegt** — er liegt darin, dass zwei Orte gepflegt werden müssen und nur einer im Blick ist. Deshalb ein
Handgriff am Tor und keine Mahnung im Regelwerk.*

## 2 — Was zu prüfen ist, selbst gemessen

```text
Tafelzeile, Kopf woertlich:  | Auftrag | Zustand | Ball | letzter Beleg | offen |
  Spalte 2 = Zustand      Beispiel A-25:  **`ENTWURF`**
  Spalte 3 = Ball         Beispiel A-25:  `plan-pruefer`

Datensatz im yaml-Block:
  zustand: ENTWURF
  ballbesitz: plan-pruefer  # DoR steht aus
```

**Die Barriere vergleicht je Auftrag beide Paare** — *Zustand gegen `zustand:`, Ball gegen
`ballbesitz:` — und meldet, wenn sie auseinanderlaufen.*

## 3 — Drei Fallen, die ein zu einfaches Muster fallen lassen

**Sie stehen hier, weil jede von ihnen die Barriere GRÜN und WIRKUNGSLOS machen würde:**

```text
(a) SCHREIBWEISE   Tafel traegt **`ENTWURF`**, Datensatz ENTWURF. Ein Vergleich
                   ohne Normalisierung meldet JEDE Zeile als Drift — die Barriere
                   wird weggeklickt und ist damit tot (A-03).
                   -> Backticks, Sterne und Randleerzeichen weg, dann vergleichen.
                   ERGAENZT 13.08., UND MEINE AUFZAEHLUNG WAR UNVOLLSTAENDIG:
                   Backticks und Sterne genuegen NICHT. Selbst gemessen an der
                   Ball-Spalte gegen die Datensatzfelder:
                     Tafel      **Generator** (4x) · **Evaluator** (3x)
                                Plan-Pruefer mit UMLAUT (1x)
                     Datensatz  generator (6x) · evaluator (3x) · planner (7x)
                                plan-pruefer OHNE Umlaut
                   Es unterscheiden sich also auch GROSS/KLEIN und UMLAUT gegen
                   Umschrift. Ohne diese zwei Normalisierungen meldete die Barriere
                   bei JEDER Zeile Drift — genau der Fall, den A-26-3 verbietet.
                   -> zusaetzlich kleinschreiben UND umlautfrei vergleichen
                      (ue/ae/oe fuer ue/ae/oe).
                   DER BAU HAT ES SCHON RICHTIG GEMACHT: scripts/a26-ball-drift.sh
                   vergleicht den Ball kleingeschrieben und umlautfrei. Diese
                   Ergaenzung verlangt also NICHTS NEUES — sie DECKT, was gebaut ist.
                   WARUM SIE TROTZDEM NOETIG IST: eine Zusage, die gebaut aber nicht
                   gefordert ist, wird beim naechsten Umbau nicht rot. Wer die
                   Kleinschreibung entfernt, faellt an keinem Kriterium — und die
                   Barriere warnt danach bei jeder Zeile, bis sie abgeschaltet wird.
                   Dieselbe Logik wie A-24-5: der Waechter haelt die KOPPLUNG, nicht
                   den Wortlaut.
(b) KOMMENTARE     `ballbesitz: plan-pruefer  # DoR steht aus`. Wer alles nach dem
                   Doppelpunkt nimmt, vergleicht den Kommentar mit.
                   -> alles ab # abschneiden.
(c) ZUORDNUNG      Liegen mehrere Datensaetze in EINEM yaml-Block, nimmt ein
                   Muster leicht das LETZTE Feld des Bereichs statt das des
                   gesuchten Auftrags. Genau daran ist der Takt-Scan des
                   Evaluators gescheitert (f017b6f9).
                   -> deshalb haengt A-26 an A-25.
```

> **Alle drei sind belegte Fälle und keine Vermutungen** — *(a) ist die A-03-Lehre („eine Barriere, die
> zu oft warnt, wird weggeklickt"), (b) steht in den Datensätzen, die ich heute selbst geschrieben habe,
> und (c) hat heute eine Prüfrolle getroffen.*

## 4 — Scope

```text
A-26 IST   eine BARRIERE in scripts/commit-pruefen.sh, die anspringt, wenn
           docs/STATUS.md im Commit liegt: je beruehrtem Auftrag Zustand und Ball
           an BEIDEN Orten vergleichen und Abweichungen melden.

A-26 IST NICHT
           ein ABBRUCH. Die Barriere WARNT, wie B5 — sie bricht nicht ab.
           Grund: eine Rueckgabe kann bewusst zwischen zwei Commits liegen, und
           ein Abbruch wuerde legitime Arbeit blockieren.
           eine Pruefung ALLER Auftraege bei jedem Commit -> nur die im Diff
           beruehrten. Das Tor laeuft bei jedem Commit; 56 Auftraege je Lauf zu
           parsen ist der Weg, auf dem eine Barriere langsam und dann abgeschaltet
           wird.
           die Zusammenfuehrung der zwei Orte -> das waere eine Rueckabwicklung
           von A-20 und gehoert Yama, nicht diesem Auftrag.
           der Takt-Scan des Evaluators -> sein Werkzeug, er hat es selbst
           umgestellt.

           UND EINE ZWEITE KLASSE, die diese Barriere NICHT faengt — benannt,
           weil sie mir am Tag des Schnitts zweimal passiert ist:
             BEIDE Orte stimmen ueberein und sind BEIDE veraltet.
             A-24 und W-31: ich habe das Blatt berichtigt, zurueckgegeben, und
             die Uebergabe nirgends vermerkt — nicht im Feld und nicht in der
             Botschaft. A-26 vergleicht die zwei Orte MITEINANDER; wo sie
             uebereinstimmen, sagt es nichts.
```

> **Und dafür habe ich keine tragfähige Barriere** — *das gehört hierher, statt eine schwache zu
> erfinden. Die naheliegenden Auslöser tragen nicht:*

```text
'Blatt geaendert, ballbesitz-Feld nicht im Diff'
  -> waere zu weit: Tippfehler und Nachtraege aendern Blaetter auch.
     Eine Barriere, die oft warnt, ist nach A-03 abgeschaltet (A-26-3).
'Uebergabewort in der Botschaft ohne Feldaenderung' (B5-Bauform)
  -> haette bei MIR nicht gegriffen: b39f3845 enthaelt gar kein
     Uebergabewort. Ich habe die Uebergabe nicht behauptet, sondern
     vergessen.
```

> **Die zweite Klasse bleibt damit offen, und sie ist benannt statt verschwiegen.** *Wer sie beheben
> will, braucht ein Signal, das das Tor nicht hat: **was der Commit inhaltlich tut.** Das ist ein eigener
> Gegenstand — und ich schneide ihn nicht, weil ich für ihn heute keine Nachweisform habe, die rot werden
> kann (Pflichtprüfung 4).*

## 5 — Abnahmekriterien

```text
A-26-1  (P1, TRAGEND) Die Barriere findet die DREI heutigen Faelle. Nachweis: die
        drei Staende aus der Historie herstellen (W-36, W-33, W-31 je nach dem
        Zustandswechsel) und zeigen, dass die Barriere je meldet. Wer sie nur an
        einem erfundenen Beispiel zeigt, hat nicht belegt, dass sie die echte
        Fehlerform trifft.
        DIE STAENDE STEHEN IN DER HISTORIE, also ist der Nachweis fahrbar: die
        drei Nachzuege sind 8c24b79f (W-36), 55cd13d8 (W-33) und 38bc5e12 (W-31)
        — je der Elter traegt die Drift. Am Bau-Stand die SHAs pruefen, nicht aus
        diesem Blatt uebernehmen.
A-26-2  (P1) Die drei Fallen aus Abschnitt 3 sind behandelt und je mit einer
        Zusage belegt: Normalisierung der Schreibweise, Abschneiden ab #, und
        Zuordnung des Datensatzes zum RICHTIGEN Auftrag.
        FUER DIE DRITTE gilt die Abhaengigkeit: A-26 wird nicht vor A-25 gebaut.
        Ist A-25 noch nicht betriebsbestaetigt, meldet die Barriere fuer Auftraege
        in verschmolzenen Bereichen 'nicht zuordenbar' statt zu raten — eine
        falsche Zuordnung ist schlimmer als eine ausgelassene.
A-26-3  (P1, WIRKSAMKEIT) Die Barriere ist an einem SAUBEREN Stand STILL. Nachweis:
        ein Commit, in dem beide Orte stimmen, erzeugt KEINE Meldung. Eine
        Barriere, die immer warnt, ist nach A-03 nach drei Tagen abgeschaltet —
        und dann ist der Zustand schlechter als vorher, weil sich alle auf sie
        verlassen.
A-26-4  Nur die im Diff BERUEHRTEN Auftraege werden geprueft, nicht alle. Nachweis
        mit einer Zeitmessung: der Tor-Lauf wird durch die Barriere nicht
        merkbar langsamer, gemessen an einem Commit mit und ohne STATUS.md.
A-26-5  Die Barriere WARNT und bricht NICHT ab (Bauform B5). Gegenprobe: ein
        Commit mit Drift laeuft durch, mit Meldung.
A-26-6  Die Fangprobe wird GEFAHREN und belegt: die Normalisierung aus A-26-2
        entfernen und zeigen, dass die Barriere dann JEDE Zeile als Drift meldet —
        also der Fall eintritt, den A-26-3 verbietet. Nicht gefahren heisst
        'nicht gefahren' im Bericht.
A-26-7  Die Rollenmarke bleibt unberuehrt: A-11 hat sie ins Tor gebaut, A-26
        aendert sie nicht. Gegenprobe per Diff.
```

**Nachweisform: Befehl und Trefferzeilen** (B5), **Messung am COMMIT** (E1),
**Nachweis muss rot werden können** (Pflichtprüfung 4), **jede Zahl an zwei Mustern** (Prüfung 7).

```yaml
warum_das_mir_gehoert_und_nicht_dem_release_pruefer: "Er hat alle drei Faelle gefunden und nachgezogen —
        das ist seine Rolle und er hat sie erfuellt, jedes Mal mit assert-Schutzbedingungen statt zu
        raten. Was er NICHT tun kann, ist die Ursache abstellen: die zwei Zustandsorte stammen aus A-20,
        und A-20 ist mein Auftrag. Eine Regel, die eine neue Fehlerklasse erzeugt, gehoert dem, der sie
        geschnitten hat."
warum_eine_barriere_und_keine_scharfere_regel: "Der dritte Fall ist der Beleg: der Verursacher GLAUBTE,
        beide Orte gepflegt zu haben, und schrieb es in die Botschaft; am Diff gemessen fehlte die
        Tafelzeile. Ein Fehler, der nicht im Willen liegt, wird von einer Mahnung nicht behoben. Die
        Bauform steht im Tor schon dreimal — F-14 gegen den Commit ohne Schreibwirkung, B5 gegen die
        Zahl ohne Belegzeile, B6 gegen das dateiweite Muster. A-26 ist die vierte derselben Art."
was_ich_ausdruecklich_NICHT_vorschlage: "Die zwei Orte wieder zu einem zu machen. Das waere die
        Rueckabwicklung von A-20, und A-20 hat einen Grund: die Tafel gibt den Ueberblick, der Datensatz
        die Belege. Wer beides in einen Ort presst, bekommt entweder eine unlesbare Tafel oder verliert
        die Belege. Falls Yama die Zusammenfuehrung dennoch will, ist das seine Entscheidung und ein
        eigener Auftrag — hier steht sie als NICHT-Ziel."
die_abhaengigkeit_ist_kein_formalismus: "A-26 misst Felder in yaml-Bloecken. Solange fuenf Datensaetze in
        EINEM Block liegen, nimmt jedes einfache Muster das letzte Feld des Bereichs — genau daran ist
        der Takt-Scan des Evaluators gescheitert, und er hat es selbst gemeldet (f017b6f9). Eine
        Barriere, die falsch zuordnet, meldet Drift wo keine ist und uebersieht die echte. Deshalb A-25
        zuerst, und deshalb meldet A-26 im Zweifel 'nicht zuordenbar' statt zu raten."
A_26_nimmt_keinen_paragraf3_platz: "ENTWURF, nicht IN_ARBEIT."
```

---

## 6 — Votum des Evaluators (§11)

**ABGENOMMEN.** Bau `c059c019`, Elter `2001eda2` (als git-Elter nachgemessen), drei Dateien,
286 Zeilen. Prüfstände auf beiden, dazu **drei historische Prüfstände** für A-26-1.

**Vorab die Lage, die ich schon im Claim angesagt habe:** dieser Bau ändert
`scripts/commit-pruefen.sh` — das Tor, mit dem ich selbst committe, und ich habe es heute fünfmal
mit der neuen Barriere darin benutzt. Ich kannte ihre Wirkung aus dem Betrieb, bevor ich sie prüfte.
Die Frage, die daraus folgt, war die eigentliche Prüffrage: **war die Barriere bei meinen Commits
richtig still — oder blind?** Beide Fälle sehen von außen gleich aus. Sie ist belegt **richtig
still** (A-26-3 gegen A-26-5, unten).

| Kriterium | Befund | Wie ich es selbst gemessen habe |
|---|---|---|
| **A-26-1** (TRAGEND) | **grün** | Die drei SHAs selbst geprüft statt aus dem Blatt übernommen — alle drei sind Ball-Drift-Nachzüge des Release-Prüfers. Dann **die drei Stände selbst hergestellt** (Worktree auf den Nachzug, `git checkout <sha>^ -- docs/STATUS.md`, Barriere-Skript hineinkopiert) und gefahren: **W-36** → `BALL: Tafel 'plan-pruefer' <-> Datensatz 'planner'`; **W-33** → `BALL: Tafel 'Planner' <-> Datensatz 'generator'`; **W-31** → `nicht zuordenbar — mehrere Datensätze in einem yaml-Block (A-25)`. Drei von drei gemeldet |
| **A-26-2** | **grün** | Alle drei Fallen einzeln geprüft. **(a) Normalisierung:** Tafelzeile auf ` \`IN_ARBEIT\` \| generator ` umgeschrieben — Sterne weg, kleingeschrieben, **Sache unverändert** → Barriere **still**, kein Fehlalarm. **(b) Kommentar:** `s/#.*//` (`:65/:66`), und ich habe es im Betrieb gesehen — `ballbesitz: generator  # nach A-25, nicht davor` wird als `generator` gelesen. **(c) Zuordnung:** der W-31-Lauf meldet „nicht zuordenbar" statt zu raten, genau wie das Kriterium es für den Zustand vor A-25 verlangt |
| **A-26-3** (WIRKSAMKEIT) | **grün** | Beide Orte gleich geändert (6 Zeilen: Tafel **und** Datensatz auf `CODE_FERTIG`/`evaluator`) → **keine Ausgabe, exit 0**. Und am unveränderten Stand ebenso still |
| **A-26-4** | **grün** | Von **70** Aufträgen in der Datei wird nur der berührte gemeldet. Laufzeit mit bash-eigener Zeitnahme: **0,018–0,019 s**. Und der Guard `:618` lässt sie bei Commits ohne `docs/STATUS.md` **gar nicht erst** laufen |
| **A-26-5** | **grün** | Nur **einen** Ort geändert → beide Drifts gemeldet (`ZUSTAND` und `BALL`), Skript-Rückgabe **1**. Das Tor ruft mit `\|\| true` (`:619`), der Kommentar sagt es ausdrücklich: der Rückgabewert wird bewusst verworfen. **Warnung, kein Abbruch** |
| **A-26-6** | **grün** | Fangprobe selbst gefahren, Anker genau 1×, md5 zurückgesetzt: **mit** Normalisierung 0 Ausgabezeilen, **ohne** meldet sie ` **\`CODE_FERTIG\`** ` gegen `CODE_FERTIG` als Drift — der Dauerwarner, den A-26-3 verbietet, tritt ein |
| **A-26-7** | **grün** | `TICKET_ROLLE` an Bau und Elter je 4 Zeilen, zeichengleich |

**Die Antwort auf meine Claim-Frage, und sie ist der Kern der Abnahme.** A-26-3 und A-26-5 sind
zusammen der Beweis, den keines von beiden allein liefert: die Barriere ist **still, wenn beide
Orte stimmen**, und sie **meldet, sobald einer abweicht** — an derselben Datei, im selben Lauf,
mit demselben Skript. Ihre Stille bei meinen fünf Commits heute war also kein Blindflug, sondern
die richtige Antwort auf einen sauberen Stand. Genau diese Unterscheidung verlangt Pflichtprüfung 4,
und sie war hier nicht theoretisch.

**Meine eigenen Messfehler in dieser Runde:**

1. **Ich hätte beinahe einen Fehlbefund gegen eine funktionierende Barriere gemeldet.** Mein erster
   A-26-3-Aufbau wollte „beide Orte gleich ändern", aber beide Ersetzungsmuster griffen ins Leere:
   am Prüfstand steht A-26 auf `IN_ARBEIT`/`Generator`, nicht auf `CODE_FERTIG`/`Evaluator`. Die
   Datei blieb **unverändert**, der Diff war leer — und ich las die Stille der Barriere zuerst als
   „sie hat eine Drift übersehen". Erst `git diff HEAD` hat gezeigt, dass es nichts zu übersehen
   gab. **Der Anker war falsch, nicht der Bau.**
2. **Zeitmessung mit falschem Verfahren:** ich maß 77–92 ms und hätte damit dem Bericht (26 ms)
   widersprochen. Mein Verfahren rief zweimal `python3` auf und maß Fremdprozesse mit. Mit
   bash-eigener Zeitnahme: **18–19 ms** — dieselbe Größenordnung wie seine Zahl, kein Widerspruch.
3. **Ein zu kurzer Edit-Anker hat Text vernichtet**, und zwar in `docs/STATUS.md` selbst: mein
   Claim-Edit endete bei `ballbesitz: evaluator` und löschte den Kommentar dahinter (die Notiz, dass
   die A-25-Sperre gefallen ist). Bemerkt hat es meine eigene Fremdzeilen-Prüfung, die die zwei
   Zeilen als „fremd" meldete — sie waren nicht fremd, sie waren mein Verlust. Vor dem Commit
   wiederhergestellt, entfernte Zeilen 0. **A-20-4 verbietet genau das, und ich habe es in dem
   Commit getan, mit dem ich eine Barriere gegen Statusfehler claime.**

**§15:** keine Datenbankschreibung in dieser Abnahme.

**Weiter an den Release-Prüfer.**
