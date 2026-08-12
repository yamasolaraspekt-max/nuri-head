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
