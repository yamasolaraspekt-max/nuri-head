# A-12 — MESSAUFTRAG: F-026 ausführen, damit die Ampel gelb nicht bleibt

```yaml
auftrag: A-12
art: "MESSAUFTRAG — kein Produktivcode, kein Bau. Muster: A-05"
titel: "Ein L-Grundriss mit F-026 rechnen, Ergebnis ansehen, Ampel 🟡 -> 🟢 oder 🔴"
spur: A
heimat_app: ticket
status: ENTWURF
status_steht_in: docs/STATUS.md
basis_sha: d1d716c8
prioritaet: P1
anlass: "Yamas Auftrag 10.08., Punkt 6 — der offene Fachschritt aus VORGEHEN.md Schritt 3"
ballbesitz: "plan-pruefer (DoR), danach generator"
claim: "planner 10.08. — Claim VOR dem Schnitt. Kein Blatt zu F-026 lag vor."
zaehlfrage_offen: "alte oder neue Zehnergruppe — entscheidet der Plan-Pruefer, wie bei A-11"
```

## Der Anlass — eine Sperre, nicht ein Wunsch

**F-026 trägt in der Formelsammlung eine Ampel, die jeden Auftrag darauf verbietet:**

```text
F-026  🟡  "noch nicht ausgefuehrt. DARF KEINEN AUFTRAG BEGRUENDEN, bis ein
            L-Grundriss gerechnet und das Ergebnis gesehen wurde"
```

**Und `VORGEHEN.md` Schritt 3 sagt, warum das kein Formalismus ist:**

> *„Schritt 3 ist der wichtigste. Die Behauptung ‚der Code kann L-Grundrisse' stammt bisher aus dem
> **Lesen** des Codes. Das ist genau die Art unbelegter Machbarkeitsaussage, an der Z-07 gescheitert
> ist — nur diesmal in die andere Richtung. **Ausführen, ansehen, dann behaupten.**"*

**Warum es jetzt dran ist:** Die Dachkonstruktion hat **drei** Wege, und zwei davon hängen an dieser
Ampel:

```text
F-020 Straight Skeleton   in W-07 beschrieben, L/T braucht Spalt-Ereignisse — NICHT GEBAUT
F-026 Kantentopologie     Code liegt vor (M-01), soll L und T koennen — AMPEL GELB
roof.anbau (die Insel)    gebaut, aber A-05 hat fuenf Luecken gemessen
```

*Solange F-026 gelb ist, kann niemand die Wegentscheidung treffen — und ohne Wegentscheidung ist
kein Dachkonstruktions-Auftrag schneidbar. **Dieser Messauftrag ist die Sperre, nicht der Umweg.***

## Erfüllbarkeit — SELBST GEMESSEN, vor dem Schnitt

*Z-07 scheiterte, weil niemand geprüft hat, ob die Voraussetzung trägt. Hier ist die Prüfung:*

```text
QUELLE EXISTIERT
~/Desktop/Gemini-Code-Ideen-2026-05-25/03-energie-pv-dach-3d/dachdecker_pro_3d.tsx
132.374 Byte · 2.173 Zeilen · mtime 24.05.

BELEGSTELLEN STIMMEN, alle drei geprueft
:101-131    l-shape-Konturpunkte — ZWEI Varianten (category 'flat' und 'pitched')
:965        buildCompoundPitched(p, mat, type)
:774        buildCompoundPitchedFaces(p, mat, type)     <- die Flaechenrechnung
:1137       die Weiche: (shape l-shape|t-shape) && category pitched -> buildCompoundPitched
```

### Der Machbarkeitsbefund, der den Auftrag zweiteilt

```text
TEIL A — TRIVIAL AUSFUEHRBAR (reine Arithmetik, keine Abhaengigkeit)
  :101-131  Konturerzeugung liefert nur {x, y}-Punkte aus L, W, WB, LB
  :774-790  die ersten ~18 Zeilen der Flaechenrechnung: rad, slopeLen, kerve,
            hPivot, yEaveEdge, uMaxMain — Trigonometrie, kein three.js

TEIL B — VERWOBEN, NICHT ISOLIERT AUFRUFBAR
  :774-970  64 Vorkommen von `THREE.` — new THREE.Vector3/Vector2/Euler
            this.gRafters.add(this.createBeam(...))   <- baut Meshes direkt
            this.mats.wood, this.buildRoofFace(...)   <- Klassenzustand
  -> die Flaechenrechnung ist METHODE einer Klasse mit Szenenzustand.
     "F-026 ausfuehren" ist KEIN Funktionsaufruf.
```

> **Das ist der Grund, warum dieser Auftrag zwei Kriterien mit sehr verschiedenem Preis hat.**
> Wer ihn als „eine Funktion aufrufen" schneidet, meldet nach zwei Stunden `SPEC_BLOCKED`.
> *three.js liegt im Repo (die Insel nutzt es) — Teil B ist also machbar, aber nicht billig.*

## DECISION — was gemessen wird und wie

```text
A-12-1  TEIL A: Konturen.  Die l-shape-Konturfunktion mit konkreten Zahlen aufrufen
        (L, W, WB, LB benannt im Bericht), die sechs Punkte ausgeben, und PRUEFEN:
        ist das Polygon geschlossen, nicht selbstschneidend, und hat es die
        einspringende Ecke, die ein L ausmacht?
        -> Das belegt oder widerlegt "F-026 kann eine L-KONTUR".

A-12-2  TEIL B: Flaechen.  Fuer denselben L-Grundriss die Dachflaechen erzeugen.
        ZWEI zulaessige Wege, die Wahl trifft der Generator und begruendet sie:
          Weg 1  three.js aus dem Repo einbinden, die Klasse minimal stellen
                 (gRafters, mats, createBeam, buildRoofFace als Attrappen, die
                 nur AUFZEICHNEN was ihnen uebergeben wird) und die echte
                 Funktion laufen lassen.
          Weg 2  die Trigonometrie aus :774-790 NACHRECHNEN und die Ergebnisse
                 gegen die Konstanten im Code stellen.
        Weg 1 belegt "der Code laeuft", Weg 2 belegt nur "die Rechnung stimmt".
        WENN Weg 1 scheitert, ist das ein ERGEBNIS und kein Fehlschlag —
        dann lautet die Antwort: F-026 rechnet, ist aber nicht isolierbar.

A-12-3  DER VERGLEICH.  Dasselbe L (gleiche Masse) durch die INSEL schicken
        (roof mit anbau, wie A-05 es gemessen hat) und die Ergebnisse
        nebeneinanderstellen: Zahl der Flaechen, First-/Grat-/Kehllinien,
        benannte Kantentypen.
        -> Das ist die Grundlage der Wegentscheidung, die DANACH der Planner trifft.

AMPEL   Das Ergebnis SETZT die Ampel: 🟢 wenn ein L-Dach mit benannten Flaechen
        herauskommt, 🔴 wenn nicht, und die Ampel bleibt 🟡 wenn nur Teil A traegt.
        Der Generator SCHLAEGT VOR, der Evaluator bestaetigt, der Planner traegt ein.
```

## Nicht-Ziele

- **Kein Produktivcode.** Nichts unter `resources/**` wird geändert. *Wie A-05.*
- **Kein Kopieren des Fremdcodes ins Repo.** Verweise, keine Kopien — dieselbe Regel wie im
  Wissensregister. Die Probe wird **vor dem Bericht entfernt** und trägt kein Commit. *A-05s
  `zzA05wegwerf.test.ts` ist das Muster, samt `ls`-Beleg im Bericht.*
- **Keine Wegentscheidung.** Dieser Auftrag **misst**; ob F-020, F-026 oder `roof.anbau` gebaut
  wird, entscheidet der Planner danach — und Yama über das Nicht-Ziel von A-01.
- **Kein L-Dach in der Insel bauen.** A-01s Nicht-Ziel gilt unverändert (`bd1383c8`).
- **Keine Aussage über F-050/F-051.** Sie stehen in denselben Schritten von `VORGEHEN.md` (4 und 5),
  sind aber eigene Vorgänge. **F-051 ist 🔴 GESPERRT** und wird hier nicht berührt.

## Scope

```text
docs/BERICHT-A-12-f026.md                              der Messbericht (NEU)
docs/rollenkette/werkbank/01-MATHEMATIK/FORMELSAMMLUNG.md   Ampel F-026 nach dem Ergebnis
docs/rollenkette/werkbank/05-MATERIALQUELLEN/VORGEHEN.md    Schritt 3 als erledigt
```

*NICHT im Scope: `resources/**`, `scripts/**`, jede andere Formel.*

## Wiederverwendungsprüfung (§5)

```text
dachdecker_pro_3d.tsx     VORHANDEN auf dem Schreibtisch, 2.173 Zeilen — die Quelle
three.js                  VORHANDEN im Repo (Insel-Abhaengigkeit) — kein neues Paket
npm run test:hausplaner   VORHANDEN UND IN GEBRAUCH — Runner fuer die Wegwerf-Probe
A-05-Bericht              VORHANDEN — Muster fuer Form, Mess-SHA-Angabe und die
                          Wegwerf-Probe-Disziplin (Probe vor dem Bericht entfernt)
geometry/dachGeometrie.ts VORHANDEN — die Insel-Seite fuer A-12-3
```

**Nichts wird neu erfunden, nichts installiert.**

## Auswirkungen (§5)

```text
API · Server · Schema · Migration · Bestandsdaten · Bundle   KEINE
Produktivcode                                                KEINER
Testdaten-Ziel                                               KEINES
Prozessbindung                                               ENTFAELLT — kein Serverstart,
                                                             keine DB, keine Buehne
Werkzeuge                                                    node-Runner + three.js, beide
                                                             vorhanden und in Gebrauch
```

**Erstnutzer:** *der Planner bei der Wegentscheidung zur Dachkonstruktion — und Yama, dessen Frage
„warum greift ihr auf playground und PV-Dachplaner nicht zurück" hier zum ersten Mal mit einem
ausgeführten Ergebnis beantwortet wird statt mit einer Lesart.*

## Akzeptanzkriterien

**Die Rot-Lage ist die Ampel selbst:** F-026 steht auf 🟡 mit dem ausdrücklichen Vermerk „noch nicht
ausgeführt". *Der Plan-Prüfer bestätigt vor dem Bau, dass die Ampel dort steht — ein `grep` genügt.*

**A-12-1 (P1, Konturen):** Der Bericht nennt die eingesetzten Maße und die **sechs** ausgegebenen
Punkte als Rohausgabe, plus die Prüfung auf geschlossen / nicht-selbstschneidend / einspringende
Ecke. *Rot heute: es existiert kein Bericht und keine ausgeführte Rechnung.*

**A-12-2 (P1, Flächen):** Der Bericht nennt den **gewählten Weg mit Begründung** und das Ergebnis
als Rohausgabe. Scheitert Weg 1, steht **warum** im Bericht — das ist ein Ergebnis, kein Fehlschlag.

**A-12-3 (P1, der Vergleich):** Insel und F-026 stehen für dieselben Maße **nebeneinander**, mit
Zahl der Flächen und benannten Linien. *Ohne diesen Vergleich ist die Messung interessant und für
die Wegentscheidung unbrauchbar.*

**A-12-4 (P1, Ampel-Vorschlag mit Begründung):** Der Bericht **schlägt** 🟢, 🔴 oder „bleibt 🟡" vor
und begründet es an den eigenen Rohausgaben. *Er setzt sie nicht selbst — der Evaluator bestätigt.*

**A-12-5 (`must_preserve`):** `resources/**` und `scripts/**` bleiben byte-identisch, die
Insel-Suite bleibt grün. Die Wegwerf-Probe ist **vor** dem Bericht entfernt und in **keinem** Commit.
*Nachweis wie bei A-05: `ls`-Beleg im Bericht plus `git log --all` leer für den Probendateinamen.*

**A-12-6 (P1, Herkunft der Zahlen):** Jede Zahl im Bericht trägt ihre Fundstelle — `Datei:Zeile` für
Code, Aufruf für Messwerte. *Yamas Punkt 4 und die Planner-Regel: „nie sagen ‚das müsste gehen'".*

## Kantenliste

```text
zwei l-shape-Konturvarianten (flat/pitched)  -> BEIDE nennen, nicht eine waehlen
buildCompoundPitchedFaces braucht Klassen-
  zustand (gRafters, mats, buildRoofFace)    -> Attrappen duerfen nur AUFZEICHNEN,
                                                nichts berechnen — sonst messen wir
                                                die Attrappe
three.js-Version im Repo != die des Fremd-
  codes                                      -> MELDEN, nicht anpassen
Fremdcode wirft / kompiliert nicht           -> ERGEBNIS (Ampel 🔴 oder bleibt 🟡),
                                                kein Abbruch des Auftrags
Insel wirft beim Vergleich (A-05 hat das
  gemessen: dachFlaechen-Wurf bei l-shape)   -> erwartet, gehoert in den Vergleich
Masse, bei denen das L entartet (WB >= W)    -> Grenzfall pruefen und benennen
```

## Rückweg und Entdeckung

**Rückweg:** ein Bericht und zwei Ampel-/Statuszeilen — `git revert` genügt. Kein Code, keine Daten.

**Entdeckung:** Die Ampel ist das Signal. Wird F-026 später in einem Auftrag zitiert, **ohne** dass
dieser Bericht existiert, ist die Sperre umgangen — dann zurück an den Planner. *Genau dafür steht
die Ampel-Bedingung in der Sammlung.*

## Konfliktprüfung (§5)

```text
W-01/1 · W-02/1 · W-13/1   BEREIT/ENTWURF   werkbank/W-xx/** + REGISTER.md
A-12   DIESES              werkbank/01-MATHEMATIK/** + 05-MATERIALQUELLEN/** + docs/BERICHT-*
A-09 · A-11                BEREIT/ENTWURF   scripts/**
-> DISJUNKT. A-12 beruehrt REGISTER.md NICHT (nur FORMELSAMMLUNG und VORGEHEN).
§3: A-12 geht erst IN_ARBEIT, wenn kein anderer Auftrag IN_ARBEIT ist —
    Dateifreiheit ist nicht Ablauffreiheit.
```

```yaml
fehlerklasse: keine
prioritaet: P1
warteschlange: "unabhaengig von der W-Reihe — A-12 entsperrt die Dachkonstruktion,
                die W-Reihe baut das Fundament. Reihenfolge entscheidet Yama."
naechster_schritt: "Plan-Pruefer prueft DoR (Ampel-Rot per grep, Belegstellen per sed)."
```
