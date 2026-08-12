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
