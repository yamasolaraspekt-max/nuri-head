# W-15 Stufe 1 — Material und Farbe ENTWERFEN. Das erste Klasse-C-Blatt, und es hat einen Vertrag als Quelle

```yaml
auftrag: "W-15/1"
werkzeug: "W-15 Material und Farbe"
stufe: "1 von 2 — ENTWORFEN (nicht BESCHRIEBEN). Stufe 2 ist der BAU."
titel: "Vier Vertragswerkzeuge ohne Implementierung — und der Vertrag liefert die Blattinhalte"
spur: A
heimat_app: ticket
status: ENTWURF
status_steht_in: docs/STATUS.md
basis_sha: 57e582af
prioritaet: P2
anlass: "Yamas Freigabe 12.08.: Klasse C beginnt mit W-15, weil es das EINZIGE C-Werkzeug ist,
         dessen Abhaengigkeit erfuellt ist (W-13 steht auf BESCHRIEBEN)."
ballbesitz: "plan-pruefer (DoR), danach generator"
claim: "planner 12.08. — Claim VOR dem Schnitt."
neu: "erstes Blatt mit Zielreifegrad ENTWORFEN. Die Stufe ist heute in die Register-Legende
      aufgenommen (Yamas Entscheidung)."
```

## Warum ENTWORFEN und nicht BESCHRIEBEN — die Formfrage, die vor dem Schnitt geklärt war

```text
BESCHRIEBEN   die sieben Blaetter LESEN vorhandenen Code ab.   Quelle: der Bestand.
ENTWORFEN     die sieben Blaetter GEBEN VOR, was zu bauen ist. Quelle: Vertrag,
              Fachregel oder Zielbild — KEIN Code.
```

> **Zwei verschiedene Dinge unter einem Namen wären später nicht mehr unterscheidbar:** *man könnte
> nicht sagen, ob `BESCHRIEBEN` heißt „wir haben es gemessen" oder „wir haben es geplant".*
> **Der Abschlusszähler zählt weiter nur `BESCHRIEBEN`** — sonst misst er Pläne statt Messungen (H-3).
>
> **Und der Übergang trägt eine Pflicht:** *beim Wechsel `ENTWORFEN` → `GEBAUT` ist zu prüfen, ob die
> **Vorgabe** mit der **Ablesung** übereinstimmt. **Ein Entwurf, der gebaut wurde und danach nicht
> nachgemessen wird, ist eine unbelegte Behauptung über den eigenen Code.***

## Ist-Zustand — eine Lage, die die Klassifikation nicht kennt

**Gemessen: W-15 ist nicht „kein Code". Es ist ein VERTRAG ohne Implementierung.**

```text
KEIN Geometrie-Modul       ls geometry/ | grep -iE 'material|farbe|textur'  -> LEER
KEIN Dienst                grep 'services.material|material.execute'
                           (ausser dem Vertrag selbst)                      -> 0 Treffer
KEIN Handler               MaterialCommand kommt im ganzen Repo EINMAL vor —
                           in werkzeugVertrag.ts:887
ABER: VIER VERTRAGSEINTRAEGE, vollstaendig ausformuliert:
  :874  werkzeugId 'material-aufnehmen'
  :886  werkzeugId 'material-zuweisen'   commandId 'MaterialCommand'
  :898  werkzeugId 'textur'              commandId 'TextureCommand'
  :~866 commandId 'PaintCommand'         (Farbe)
```

**Und der Vertrag für `material-zuweisen` im Wortlaut — er ist praktisch ein fertiges `2-FUNKTION`:**

```text
werkzeugId:      'material-zuweisen'
commandId:       'MaterialCommand'
familie:         'assign-or-calculate'
eingaben:        ['objectIds', 'surfaceSlot', 'surfaceMaterialId', 'variantId']
ergebnisse:      ['materialAssignmentIds']
vorbedingungen:  ['project.open', 'selection.count >= 1', 'permission.edit']
seiteneffekte:   ['model.revision.increment', 'autosave.markDirty',
                  'dependentResults.invalidate', 'renderer.refreshAffectedObjects']
umkehrbar:       true
protokollpflichtig: true
dienstMethode:   "services.material.execute('material', input)"
```

> **Das sind genau die Angaben, die W-07s `2-FUNKTION` als Platzhalter trägt** — Kommandoname,
> Ausführen, Zurücknehmen, Bündelung, Schichtzuordnung. **Der Vertrag liefert sie, für vier
> Werkzeuge, ausformuliert.**
>
> **Damit ist dieses Blatt kein Entwurf aus dem Kopf, sondern ein Entwurf mit Quelle.** *Und das ist
> der Unterschied zwischen `ENTWORFEN` und „erfunden".*

## BEFUND ÜBER DIE GANZE TAFEL — er gehört nicht in dieses Blatt, aber er ist hier entstanden

```text
werkzeugVertrag.ts     1.440 Zeilen · 111 Vertragseintraege ("werkzeugId:")
REGISTER.md               42 Werkzeugzeilen
-> DER VERTRAG KENNT 111 WERKZEUGE, DAS REGISTER 42.
```

**Vorbehalt sofort (H-6, weil eine Zahl allein hier täuscht):** *111 zu 42 ist **nicht** 1:1. Der
Vertrag ist **feiner granuliert** — `material-aufnehmen`, `material-zuweisen` und `textur` sind drei
Verträge für **eine** Registerzeile. **Die 111 sind Kommandos, die 42 sind Werkzeuge.**

> **Aber die Frage bleibt und ist groß:** *es gibt eine **dritte Liste** von Werkzeugen im Haus — nach
> `toolRegistry` und `REGISTER.md` —, und niemand hat sie mit dem Register verglichen.* **Das ist
> möglicherweise der größte Anschlussbefund der ganzen Werkbank, und er ist eine eigene Messung.**
>
> **Zieladresse (H-1), nicht Notiz:** *ein Messauftrag „Vertrag gegen Register", der je Vertragseintrag
> sagt, welche Registerzeile ihn abdeckt — und welche keine hat.* **Nicht dieser Auftrag: er würde W-15
> von einem kleinen Blatt zu einer Tafel-Inventur machen.**

## DECISION

```text
QUELLE        werkzeugVertrag.ts, die VIER Eintraege (material-aufnehmen,
              material-zuweisen, textur, PaintCommand) — woertlich, nicht ausgedacht
ZIELREIFEGRAD ENTWORFEN. NICHT BESCHRIEBEN: es gibt keinen Code abzulesen.
1-ZWECK       aus der Vertrags-Familie 'assign-or-calculate' und den Eingaben.
              WAS FEHLT und im Blatt stehen muss: der Vertrag sagt NICHT, WOZU
              der Anwender Material zuweist. Das ist zu ergaenzen, und die Quelle
              dafuer ist Yamas Zielbild (Navigation 03 Dach: "Eindeckung") — NICHT
              meine Vorstellung.
2-FUNKTION    aus dem Vertrag ableitbar, vollstaendig: Kommandoname, Eingaben,
              Ergebnisse, Vorbedingungen, Seiteneffekte, Umkehrbarkeit.
              -> das erste 2-FUNKTION der Tafel, das NICHT geschaetzt werden muss.
3-FORMELN     KEINE. Material und Farbe rechnen nicht. Das Register nennt fuer W-15
              ebenfalls keine Formel ("—"), und das ist RICHTIG.
              -> W-15/1-4 verlangt, dass das Blatt "keine" schreibt UND begruendet,
                 statt die Spalte leer zu lassen.
7-GRENZEN     DER KERN eines ENTWURFS: was tut das Werkzeug, wenn die Zuweisung
              nicht geht? Der Vertrag nennt drei Vorbedingungen — jede kann fehlen.
```

## Nicht-Ziele

- **Kein Bau.** *Stufe 1 ist der Entwurf; Stufe 2 baut. Und Stufe 2 braucht `services.material`,
  das nicht existiert — das ist im Blatt zu benennen, nicht zu bauen.*
- **Keine Tafel-Inventur.** *Der 111-gegen-42-Befund ist benannt und hat eine Zieladresse. Er wird
  hier nicht abgearbeitet.*
- **Keine Erfindung des Zwecks.** *Wenn der Vertrag nicht sagt, wozu Material zugewiesen wird, wird
  es aus Yamas Zielbild belegt oder als offen benannt — nicht ausgedacht.*
- **Keine Aussage über `toolRegistry`.** *Ob der Registry-Eintrag `Material` zu diesen vier Verträgen
  passt, ist ungemessen und gehört zur Tafel-Inventur.*
- **Keine Änderung an `resources/**`.**

## Scope

```text
docs/rollenkette/werkbank/02-WERKZEUGE/W-15-material-und-farbe/1-ZWECK.md … 7-GRENZEN.md
docs/rollenkette/werkbank/02-WERKZEUGE/REGISTER.md   W-15: LEER -> ENTWORFEN
                                                     + werkzeugVertrag.ts als Fundstelle
```

## Wiederverwendungsprüfung (§5)

```text
werkzeugVertrag.ts:874-908   VORHANDEN — VIER Eintraege, vollstaendig. Die Hauptquelle.
                             -> ZITIEREN, nicht umformulieren
neun BESCHRIEBEN-Blaetter    VORHANDEN — Struktur und Kriterienform uebernehmen.
                             ABER: ihr 5-CODE sagt "angebunden aus <Modul>". Hier gibt
                             es kein Modul -> 5-CODE sagt "NOCH NICHT GEBAUT, Vertrag
                             in werkzeugVertrag.ts:886". Das ist die neue Form fuer C.
Register-Legende             HEUTE um ENTWORFEN ergaenzt — dieses Blatt ist ihr Erstnutzer
W-13 (BESCHRIEBEN)           die Abhaengigkeit ist erfuellt. Deshalb ist W-15 ueberhaupt frei.
```

## Auswirkungen (§5)

```text
API · Schema · Migration · Bestandsdaten · Bundle · Produktivcode   KEINE
Datenbank · Testdaten-Ziel                                         KEINES
Prozessbindung                                                     ENTFAELLT
Register                                                           erste ENTWORFEN-Zeile.
                                                                   Der Abschlusszaehler
                                                                   bleibt bei 9 — gemessen.
```

**Erstnutzer:** *der Bau in Stufe 2 — und die Register-Legende, die heute `ENTWORFEN` bekommen hat und
noch kein Werkzeug trägt.*

## Akzeptanzkriterien

**W-15/1-1 (P1, kein Platzhalter):** keiner in den sieben Blättern. *Zählweise `grep -nE '<[^>]+>'`
**ohne Längengrenze** — die Lehre aus W-07, wo `{2,40}` drei echte Treffer verschluckte.*

**W-15/1-2 (P1, der Zielreifegrad ist ENTWORFEN):** Die Registerzeile trägt `ENTWORFEN`, **nicht**
`BESCHRIEBEN`. *Nachweis: der Abschlusszähler `grep -cE '^\| W-[0-9]+ .*BESCHRIEBEN'` bleibt
**unverändert** — heute 9. **Wenn er steigt, ist die Stufe falsch eingetragen.***

**W-15/1-3 (P1, `2-FUNKTION` aus dem Vertrag, nicht geschätzt):** Alle Angaben — Kommandoname,
Eingaben, Ergebnisse, Vorbedingungen, Seiteneffekte, Umkehrbarkeit — stehen **mit Fundstelle
`werkzeugVertrag.ts:Zeile`**. *Das ist das erste `2-FUNKTION` der Tafel, das nicht geschätzt werden
muss; wer hier schätzt, verschenkt die Quelle.*

**W-15/1-4 (P1, „keine Formel" wird geschrieben UND begründet):** `3-FORMELN` sagt **„keine"** und
warum — *Material und Farbe rechnen nicht.* **Eine leere Spalte ist kein Befund, ein begründetes
„keine" ist einer.** *Nach `603eddc2` (sieben von zehn F-Zuordnungen fielen) ist das die richtige
Richtung.*

**W-15/1-5 (P1, `5-CODE` sagt die Wahrheit über den Bau):** Nicht „angebunden aus", sondern **„NOCH
NICHT GEBAUT"** plus die Vertragsfundstelle **plus die Feststellung, dass `services.material` im Repo
nicht existiert** (0 Treffer, gemessen). *Ein `5-CODE`, das einen Vertrag wie eine Implementierung
liest, wäre die schlimmste Form dieses Blattes.*

**W-15/1-6 (P1, `7-GRENZEN` beantwortet die drei Vorbedingungen):** Was tut das Werkzeug, wenn
`project.open`, `selection.count >= 1` oder `permission.edit` **nicht** erfüllt ist? *Drei Fälle, drei
Antworten — Absage mit Wortlaut, kein stilles Nichts.*

**W-15/1-7 (P1, VIER Werkzeuge in einer Zeile werden benannt):** Das Blatt sagt, dass der Vertrag
**vier** Einträge führt (`material-aufnehmen`, `material-zuweisen`, `textur`, `PaintCommand`), und ob
alle vier zu W-15 gehören **oder ob einer davon ein eigenes Werkzeug ist**. *Dieselbe Frage wie bei
W-04 (fenster+tuer) und W-22 (Gaube+Kamin+Prüfung) — sie wird gestellt, nicht übergangen.*

**W-15/1-8 (P1, der Zweck wird belegt oder als offen benannt):** Der Vertrag sagt **nicht**, wozu
Material zugewiesen wird. `1-ZWECK` belegt es aus Yamas Zielbild — **oder sagt, dass es offen ist.**
*Kein erfundener Zweck.*

**W-15/1-9 (`must_preserve`):** `resources/**` byte-identisch, Insel-Suite unverändert grün (ohne
Zahl). **Und: keine andere Registerzeile geändert** — Nachweis: `git diff` auf `REGISTER.md` zeigt
genau eine geänderte Werkzeugzeile.

**W-15/1-10 (P1, §3 wird BELEGT — als SCOPE-Messung):** Befehl mit Ausgabe, an beiden Orten,
**unmittelbar vor der ersten Änderung**, und die Messung fragt **welche Dateien** der laufende Auftrag
hält (H-4).

## Kantenliste

```text
ENTWORFEN wird als BESCHRIEBEN eingetragen  -> W-15/1-2 misst es am Zaehler.
5-CODE liest den Vertrag als Bau            -> W-15/1-5. Der schlimmste Fall.
Zweck wird erfunden                         -> W-15/1-8. Belegen oder offen lassen.
die vier Vertraege werden zu einem verkuerzt -> W-15/1-7 verlangt die Nennung.
die Tafel-Inventur wird "nebenbei" gemacht  -> Nicht-Ziel. 111 gegen 42 ist eigen.
services.material wird gebaut               -> Stufe 2, nicht hier.
```

## Rückweg und Entdeckung

**Rückweg:** sieben Doku-Dateien und eine Registerzeile, `git revert` genügt.

**Entdeckung:** *Steigt der Abschlusszähler nach diesem Blatt, ist `ENTWORFEN` falsch eingetragen —
und dann misst die Zahl Pläne statt Messungen.* **Prüfbar mit einem Befehl, und `W-15/1-2` verlangt
ihn.**

## Konfliktprüfung (§5)

```text
§3 als SCOPE-Messung, unmittelbar gemessen: 0 IN_ARBEIT
W-07N · W-09/1   ENTWURF, teilen REGISTER.md. §3 loest es; belegt in W-15/1-10.
A-14 · A-15      ENTWURF, disjunkt (geometry/ + app/dashboard/ bzw. nur ein Bericht)
W-21L            BLOCKIERT (Operanden), disjunkt
-> keine Datei doppelt belegt. REGISTER.md ist heute frei.
```

```yaml
fehlerklasse: keine
prioritaet: P2
warteschlange: "nach W-07N und W-09/1 — die schliessen Klasse A ab, und der Zaehler
                soll erst 11 erreichen, bevor die erste ENTWORFEN-Zeile dazukommt."
neue_stufe: "ENTWORFEN — erstes Blatt, das sie traegt. Legende heute ergaenzt."
befund_1: "W-15 ist nicht 'kein Code', sondern VERTRAG OHNE IMPLEMENTIERUNG. Vier
           Vertragseintraege in werkzeugVertrag.ts:874-908, vollstaendig ausformuliert —
           aber MaterialCommand kommt im Repo genau EINMAL vor (im Vertrag), und
           services.material existiert nicht."
befund_2_gross: "werkzeugVertrag.ts fuehrt 111 Werkzeuge, REGISTER.md 42. Der Vertrag ist
           FEINER granuliert (drei Vertraege je Registerzeile bei Material), also nicht
           1:1 — aber es ist eine DRITTE Werkzeugliste im Haus, die niemand mit dem
           Register verglichen hat. Eigene Messung, Zieladresse benannt."
kern: "das erste C-Blatt ist kein Entwurf aus dem Kopf, sondern ein Entwurf mit Quelle.
       Das ist der Unterschied zwischen ENTWORFEN und erfunden."
```

## §11 — Votum W-15/1 (Evaluator, Erstinstanz, 12.08.2026)

```yaml
auftrag: "W-15/1"
votum: ABGENOMMEN
fehlerklasse: KEINE   # ein P2, blockiert nicht
abnahme_commit: "72c5a6d6"
elter: "df673fdc"
pruefstand: "worktree --detach auf 72c5a6d6 und df673fdc, node_modules UND vendor"

messtisch_alle_zehn:
  W-15/1-1: GRUEN
    beleg: "Platzhalter in den sieben Blaettern — Elter 21 (seine Zahl, von mir nachgezaehlt:
            21), Bau 0. Beide Zaehlweisen gefahren: roh <[^>]+> und das Auftragsraster mit
            Buchstabe am Anfang — beide 0."
  W-15/1-2: GRUEN — und er meldet die veraltete Zahl selbst
    beleg: "Abschlusszaehler grep -cE '^\\| W-[0-9]+ .*BESCHRIEBEN': Elter 11, Bau 11 —
            UNVERAENDERT, wie das Kriterium verlangt. Die Registerzeile traegt ENTWORFEN,
            und ENTWORFEN-Zeilen gibt es im Bau genau eine."
    seine_meldung: "Das Kriterium sagt 'heute 9'. Gemessen sind es 11, vorher wie nachher.
            Er hat es GEMELDET statt das Blatt anzupassen, mit dem Satz: 'genau die Klasse,
            gegen die ich eine Stunde vorher W-01N gebaut habe. Eine Zahl in einem
            Soll-Kriterium ist eine Zeitbombe, auch wenn sie in einem Blatt steht, das die
            Regel selbst zitiert.' Die Sache ist erfuellt, die Zahl im Blatt ist es nicht."
  W-15/1-3: GRUEN
    beleg: "ACHT Fundstellen selbst geoeffnet, alle exakt:
              :874 werkzeugId: 'material-aufnehmen'   :875 commandId: 'PaintCommand'
              :877 eingaben: ['objectIds','parameters'] :878 ergebnisse: [...]
              :886 werkzeugId: 'material-zuweisen'    :889 eingaben: [...]
              :898 werkzeugId: 'textur'               :901 eingaben: [...]
            Kein Wert geschaetzt — jede Angabe traegt ihre Vertragszeile."
  W-15/1-4: GRUEN
    beleg: "3-FORMELN: 'KEINE — und das ist eine Aussage, kein leeres Feld', mit der
            Begruendung 'Material und Farbe rechnen nicht' und dem Gegenbeleg, wo die
            Mathematik stattdessen liegt (W-23 F-050, W-20 F-011/F-023)."
  W-15/1-5: GRUEN
    beleg: "5-CODE sagt 'NOCH NICHT GEBAUT' als Ueberschrift. Seine Messung selbst
            nachgefahren: services.material hat 3 Treffer, und ich habe die TREFFERZEILEN
            GELESEN — alle drei stehen in werkzeugVertrag.ts:883, :895, :907 als
            dienstMethode-ZEICHENKETTE. Ohne den Vertrag: 0. Der Vertrag nennt eine
            Dienstmethode, die niemand gebaut hat."
  W-15/1-6: GRUEN
    beleg: "Alle drei Vorbedingungen in 7-GRENZEN mit Absage und Fundstelle. Ich habe
            werkzeugVertrag.ts:891 geoeffnet:
              vorbedingungen: ['project.open', 'selection.count >= 1', 'permission.edit'],
            Genau die drei, die das Kriterium nennt. Dazu ein vierter Fall (Textur ohne
            Material), den das Kriterium nicht verlangt hat."
  W-15/1-7: GRUEN — mit einem BEFUND GEGEN DAS BLATT, und er trifft
    seine_richtigstellung: "Das Kriterium nennt VIER Werkzeuge und fuehrt PaintCommand als
            viertes. Er misst DREI werkzeugId-Eintraege; PaintCommand ist die commandId von
            material-aufnehmen."
    selbst_nachgefahren: "grep -c \"werkzeugId: 'paint'\" -> 0 (seine Zahl bestaetigt).
            PaintCommand im ganzen Vertrag: genau EINE Fundstelle, :875, und die steht direkt
            unter werkzeugId: 'material-aufnehmen' (:874). Der Befund haelt."
    und_die_frage_ist_beantwortet: "Das Kriterium wollte wissen, ob alle vier zu W-15 gehoeren
            oder eines ein eigenes Werkzeug ist. Antwort: alle DREI gehoeren zu W-15, und das
            vierte war nie ein Werkzeug. Er hat die Richtigstellung ins Blatt gesetzt, statt
            eine vierte Zeile zu erfinden."
  W-15/1-8: GRUEN
    beleg: "1-ZWECK:39 'Der Zweck der Zuweisung selbst — offen, und das ist eine Messung',
            belegt mit 0 Treffern fuer surfaceMaterialId/materialAssignment ausserhalb des
            Vertrags: niemand verbraucht die Zuweisung. Kein erfundener Zweck."
  W-15/1-9: GRUEN
    beleg: "resources/ und app/ 0 Dateien. Im REGISTER genau EINE geaenderte Werkzeugzeile —
            der Diff zeigt zwei Zeilen, aber gelesen sind es die Minus- und die Plus-Fassung
            DERSELBEN W-15-Zeile (LEER -> ENTWORFEN). Insel-Suite 1698/1698/0."
    seine_zeilenkorrektur_geprueft: "Er berichtigt eine eigene Zeilenangabe: W-13 stehe im
            REGISTER in Zeile 37, nicht 68. Ich habe :37 geoeffnet — dort steht W-13. Stimmt."
  W-15/1-10: GRUEN mit P2
    beleg: "Die Bau-Botschaft traegt: '§3 als Scope-Messung: Tafelzeile 0, Zustandsfeld 0,
            kein Auftrag IN_ARBEIT, also haelt auch keiner Dateien.'"
    p2: "Gegen den COMMITTETEN Elterstand (df673fdc) messe ich A-17 an BEIDEN Orten als
            laufend. Seine Null gilt fuer den Arbeitsbaum. Zweiter Fall derselben Klasse
            heute — bei W-01N habe ich denselben P2 vermerkt."
    warum_kein_rot: "H-4: §3 sperrt die DATEIEN. A-17-Scope war enginePanels.ts, zwei
            geometry-Dateien, das Buendel und eine Testdatei; W-15/1-Scope sind die sieben
            W-15-Blaetter und REGISTER.md. Disjunkt — A-17 hat das Register nicht angefasst,
            von mir gegengeprueft. Und er benennt die Nichtberuehrung von STATUS.md samt der
            vier nachzutragenden Zustaende, statt sie zu verschweigen."

was_diesen_bau_heraushebt:
  - "ZWEI Befunde gegen das eigene Auftragsblatt, beide gemeldet statt angepasst — und beide
     treffen: die vier Werkzeuge sind drei, und die feste Zahl 9 ist elf. Der zweite ist der
     bemerkenswerte: er erkennt an seinem eigenen Auftrag die Fehlerklasse wieder, gegen die
     er eine Stunde vorher W-01N gebaut hat."
  - "Er berichtigt eine EIGENE Zeilenangabe (W-13 im Register), bevor sie jemand findet."
  - "Das erste 2-FUNKTION der Tafel, das nicht geschaetzt werden muss — und er hat die Quelle
     wirklich benutzt: dreizehn Zeilenangaben, von mir acht stichprobenartig geoeffnet, alle exakt."

zusammenfassung: "Zehn von zehn. Das erste ENTWORFEN der Werkbank steht, und es steht ehrlich:
     5-CODE sagt NOCH NICHT GEBAUT, der Zweck ist als offen belegt statt erfunden, und die
     Dienstmethode des Vertrags zeigt nachweislich auf nichts. Zwei Zahlen des Blattes hat er
     widerlegt statt sie zu uebernehmen. Ein P2 am §3-Beleg, dieselbe Klasse wie bei W-01N:
     die Null gilt fuer einen Arbeitsbaum, den niemand nachlesen kann."

ballbesitz: release-pruefer
```
