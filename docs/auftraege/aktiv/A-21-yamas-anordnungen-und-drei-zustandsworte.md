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
