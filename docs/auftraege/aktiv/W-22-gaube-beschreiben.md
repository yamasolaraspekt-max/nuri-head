# W-22 Stufe 1 — Gaube BESCHREIBEN: das Modul kann mehr, als das Werkzeug heißt

```yaml
auftrag: "W-22/1"
werkzeug: "W-22 Gaube"
stufe: "1 von 2 — BESCHRIEBEN. Stufe 2 folgt als eigener Auftrag."
titel: "Die sieben Blaetter von W-22 aus gaubeGeometrie.ts ableiten — und die Aufbauten-Nachbarn benennen"
spur: A
heimat_app: ticket
status: ENTWURF
status_steht_in: docs/STATUS.md
basis_sha: 95fe1b88
prioritaet: P1
anlass: "Runde 2 der Klasse A, letztes Blatt — vom Release-Pruefer freigegeben (b9dc3c35)"
ballbesitz: "plan-pruefer (DoR), danach generator"
claim: "planner 11.08. — Claim VOR dem Schnitt. Kein W-22-Blatt lag als Auftrag vor."
muster: "W-01/1, W-02/1, W-04/1, W-05/1, W-08/1, W-11/1, W-21/1"
```

## Ist-Zustand — der Werkzeugname ist enger als das Modul

**Anbindungsmessung an Exporten und Dateikopf gefahren:**

```text
KERN VON W-22
geometry/gaubeGeometrie.ts              498 Zeilen, 26 Exporte
  Geometrie    Vec3 · LokalPunkt · Dreieck · Linie · SurfaceFrame · GaubeEingabe
               surfacePointRein() · AufbauBasisWelt · aufbauBasis() · weltAusLokal()
  Hauptdach    Hauptdach · hauptdachAusFrame() · neigungAusFrame() · signierterAbstand()
  Grenzwerte   MIN_PULT_GRAD · MIN_FLACH_GRAD
  Gauben       PultGaube · pultGaubeGeometrie() · GiebelGaube · giebelGaubeGeometrie()
               fussabdruckUV()
  KAMIN        KaminGeometrie · kaminGeometrie()
  PRUEFUNG     Ampel · PruefBefund · pruefeAufbau()
Registry-Werkzeug                       KEINES (0 Treffer auf gaube/kamin/aufbau)
Zusagen                                 2 dediziert (gaubeGeometrie, dachAufbauten)
                                        + aufbautenStatus.test.ts (Nachbar)
Register                                LEER · braucht W-07 · F-027, F-031
```

> **Der Dateikopf sagt selbst, dass „Gaube" zu eng ist:**
>
> *„REINE, testbare Geometrie für **stehende Dachaufbauten** (Pultgauben: Schlepp/Flach/Trapez,
> Giebel-/Spitzgaube, **Kamin**) und ihren **Anschluss** an die geneigte Hauptdachfläche … und
> **prüft jeden Aufbau numerisch (kein Render verfügbar)**."*
>
> **Damit sind `kaminGeometrie` und `pruefeAufbau` keine Fremdkörper, sondern erklärt.** *Der Kamin
> ist ein stehender Aufbau wie die Gaube — dieselbe Geometrie, andere Form. Und die numerische
> Prüfung existiert, **weil kein Render verfügbar ist**: das Modul kann sich nicht ansehen, also
> rechnet es nach. **Das ist die A-10-Lehre in Reinform, freiwillig und vor A-10 gebaut.***

## Der Anlass des Moduls — mit Zahlen, und er gehört in `1-ZWECK`

```text
Dateikopf woertlich:
"Der bisherige Box-Hack in updateObstacles liess hinten eine vertikale Rueckwand bis
 lokal y=height+rise stehen -> bei 35 Grad Dachneigung ragte sie ~0,36 m UEBER den
 First und ~1,3 m ueber die Hauptdachflaeche (numerisch bestaetigt: Welt-Y 8,336 >
 First 7,978). Eine echte Pultgaube hat hinten KEINE Wand — ihr Pultdach endet auf
 der Hauptdachebene (Anschnitt)."
```

> *Das ist die beste `1-ZWECK`-Quelle der ganzen Klasse: **ein Fachfehler, eine Fachbegründung
> („eine echte Pultgaube hat hinten keine Wand") und zwei gemessene Zahlen.*** Der Generator muss
> hier nichts erfinden.

## BEFUND — fünf Module bilden „Dachaufbauten", die Werkbank kennt nur „Gaube"

**Die vier Nachbarn, alle gemessen:**

```text
geometry/aufbauOrientierung.ts    61 Z   stehendeAufbauBasis() · istStehenderAufbau()
  "Orientierung aufrecht STEHENDER Dachaufbauten (Gaube, Kamin). Diese stehen
   LOTRECHT (Welt-Hoch), NICHT senkrecht zur Dachschraege."
geometry/aufbauPlatzierung.ts    190 Z   platziereAufbauten() · RAND_M · MIN_GAUBE_M
                                         MAX_BREITE_ANTEIL · AUFBAU_ABSTAND_M
  "FLAECHENABHAENGIGE Platzierung von Standard-Aufbauten (Kamin/Dachfenster/Luefter/
   Sat/Gaube/Lichtkuppel)"
geometry/aufbautenStatus.ts       52 Z   aufbautenOhneFlaeche() · istAufbauPruefpflichtig()
                                         AUFBAUTEN_WARNUNG
  "Statuslogik fuer Dach-Aufbauten (Kamin, Gaube, Dachfenster, sonstige Hindernisse)"
geometry/auswechslung.ts         174 Z   analysiereAuswechslung() · sparrenPositionenU()
  "Auswechslungen/Wechselhoelzer an Dachoeffnungen (Kamin, Dachfenster, Gaube, Lueefter)"
                                 ------
                                 477 Z   Nachbarn, zusaetzlich zu W-22s 498
```

**Meine Entscheidung, und sie ist knapp:**

```text
IM SCOPE      gaubeGeometrie.ts allein (498 Z)
              Begruendung: es ist EIN Modul, sein Kopf beschreibt EINEN Gegenstand
              (stehende Aufbauten samt Anschluss), und es ist zweifach abgesichert.
NICHT IM      die vier Nachbarn (477 Z). Sie gehoeren fachlich zum selben Thema, aber:
SCOPE           - aufbauPlatzierung deckt SECHS Aufbauarten ab, davon eine die Gaube
                - aufbautenStatus ist Statuslogik ueber ALLE Hindernisse
                - auswechslung ist HOLZBAU an einer Oeffnung — in W-21/1 steht es
                  bereits als "verwandt, nicht im Scope"
              Sie in W-22 zu ziehen hiesse, ein Gauben-Blatt ueber Dachfenster,
              Lueefter und Lichtkuppeln schreiben zu lassen.
GEMELDET      Die Werkbank hat kein "Dachaufbauten"-Werkzeug. Fuenf Module (975 Z)
              bilden das Thema, W-22 traegt eines davon. Das ist ein Befund fuer die
              Anschlussmatrix und moeglicherweise ein Argument, W-22 umzubenennen
              oder ein W-24 zu schneiden — NICHT meine Entscheidung.
```

> **`auswechslung.ts` ist der schärfste Fall:** es steht in **W-21/1** als *„verwandt, nicht im
> Scope"* und hier ebenso. **Ein Modul, das in zwei Blättern als Nachbar geführt wird und in keinem
> zuhause ist, hat kein Zuhause.** *Das gehört gemeldet, damit es nicht zwischen den Blättern
> verschwindet — 174 Zeilen Wechselholz-Geometrie mit eigenem Zweck.*

## DECISION

```text
Quelle       gaubeGeometrie.ts (498 Z) + die zwei dedizierten Zusagen
NICHT Quelle die vier Nachbarn — namentlich benannt, mit Begruendung
1-ZWECK      aus dem Dateikopf: der Box-Hack, die 0,36 m ueber dem First, die
             Fachbegruendung "eine echte Pultgaube hat hinten keine Wand"
2-FUNKTION   DREI Gegenstaende trennen: Gaube (Pult/Giebel), KAMIN, und die
             numerische PRUEFUNG. Dazu das LOKALE SYSTEM — der Kopf definiert
             lx/ly/lz ausdruecklich, und ohne das ist keine Zeile verstaendlich.
3-FORMELN    nur F-Nummern. Register nennt F-027 (Gaubenaufbau, aus M-01) und F-031.
             ACHTUNG: F-027 stammt aus Yamas dachdecker_pro_3d.tsx — pruefen, ob der
             HAUSPLANER-Code sie benutzt oder einen eigenen Weg geht. Das ist derselbe
             Fall wie F-020 gegen roof.anbau bei W-07.
5-CODE       "angebunden aus geometry/gaubeGeometrie.ts", mit den vier Nachbarn als
             VERWEIS (nicht als Anbindung)
7-GRENZEN    der Anker ist GEBAUT: pruefeAufbau() liefert eine Ampel und einen
             PruefBefund. Das Blatt liest sie aus. Dazu MIN_PULT_GRAD und
             MIN_FLACH_GRAD — zwei benannte Untergrenzen, die zu messen sind.
```

## Nicht-Ziele

- **Keine vier Nachbarmodule.** Sie sind benannt, nicht beschrieben.
- **Kein Dachaufbauten-Werkzeug schneiden.** Der Befund wird gemeldet; ob W-22 umbenannt oder ein
  W-24 geschnitten wird, ist keine Planner-Entscheidung im Rahmen dieses Blattes.
- **Kein Registry-Eintrag.** W-22 ist eine Schicht (fünfter Fall).
- **Keine Aussage über den Renderer.** `dachAufbautenMesh.ts` zeichnet, dieses Blatt beschreibt die
  Geometrie.
- **Keine Änderung an `gaubeGeometrie.ts`** oder seinen Zusagen.

## Scope

```text
docs/rollenkette/werkbank/02-WERKZEUGE/W-22-gaube/1-ZWECK.md … 7-GRENZEN.md
docs/rollenkette/werkbank/02-WERKZEUGE/REGISTER.md   Reifegrad W-22 LEER -> BESCHRIEBEN
                                                     + gaubeGeometrie.ts als Fundstelle
```

*NICHT im Scope: `resources/**`, die vier Nachbarmodule, die F-Liste des Registers (N1-Frage).*

## Wiederverwendungsprüfung (§5)

```text
gaubeGeometrie.ts       VORHANDEN, 498 Z — Quelle, unangetastet
sein Dateikopf          16 Zeilen mit Fehlerbeschreibung, Fachbegruendung, zwei Zahlen
                        und der Definition des lokalen Systems — beste Quelle der Klasse
pruefeAufbau()          VORHANDEN — der Grenzfall-Melder ist gebaut, mit Ampel
2 dedizierte Zusagen    VORHANDEN — Quelle fuer 6-PRUEFUNG
MIN_PULT_GRAD /         VORHANDEN — benannte Untergrenzen, nicht zu erfinden
  MIN_FLACH_GRAD
W-01/1, W-05/1, W-21/1  Muster fuer SCHICHT-Blaetter
```

## Auswirkungen (§5)

```text
API · Server · Schema · Migration · Bestandsdaten · Bundle   KEINE
Produktivcode                                                KEINER — reine Doku-Stufe
Testdaten-Ziel                                               KEINES
Prozessbindung                                               ENTFAELLT
Werkzeuge                                                    grep/Editor; Insel-Suite bleibt
                                                             unveraendert gruen (ohne Zahl)
```

**Erstnutzer:** *der Generator von W-22 Stufe 2 — und jede Rolle, die einen Aufbau platziert: das
lokale System (`lx`/`ly`/`lz`) und die Aussage „ein stehender Aufbau steht **lotrecht**, nicht
senkrecht zur Dachschräge" sind die zwei Sätze, ohne die man den Box-Hack wieder baut.*

## Akzeptanzkriterien

**W-22/1-1 (P1, kein Platzhalter):** keiner mehr in den sieben Blättern. *Zählweise: alle
`<…>`-Klammern.*

**W-22/1-2 (P1):** `3-FORMELN` nennt nur F-Nummern, keine ausgeschriebene Formel.

**W-22/1-3 (P1, F-027 wird GEPRÜFT, nicht übernommen):** Das Blatt sagt, **ob** der Hausplaner-Code
F-027 (Gaubenaufbau, aus M-01) tatsächlich benutzt oder einen eigenen Weg geht — am Code gemessen.
*Derselbe Fall wie F-020 gegen `roof.anbau` bei W-07: eine Formel aus fremdem Material im Register
heißt nicht, dass der Code sie geht.*

**W-22/1-4 (P1, drei Gegenstände getrennt):** `2-FUNKTION` trennt **Gaube**, **Kamin** und
**numerische Prüfung** — und benennt, dass der Werkzeugname nur den ersten nennt. *Ohne diese
Trennung sucht niemand den Kamin in einem Gauben-Blatt.*

**W-22/1-5 (P1, das lokale System steht im Blatt):** `lx` (parallel Traufe), `ly` (Welt-Hoch),
`lz` (Falllinie, `+lz` = Traufe) — **wörtlich aus dem Kopf übernommen**. *Es ist die Voraussetzung,
um jede andere Zeile zu verstehen; ein Blatt ohne es ist unbrauchbar.*

**W-22/1-6 (P1, `7-GRENZEN` liest `pruefeAufbau()` aus):** Das Blatt nennt, **welche** Ampelstufen
und **welche** `PruefBefund`-Fälle das Modul kennt — am Code gemessen, nicht erfunden. Dazu
`MIN_PULT_GRAD` und `MIN_FLACH_GRAD` mit ihren Werten. *Der Melder ist gebaut; ihn zu erfinden wäre
schlechter als ihn abzulesen.*

**W-22/1-7 (P1, Herkunft):** `5-CODE` sagt „angebunden aus `geometry/gaubeGeometrie.ts`", mit den
vier Nachbarn als **Verweis** und der ausdrücklichen Angabe, dass sie **nicht** angebunden sind.

**W-22/1-8 (P1, der Dachaufbauten-Befund steht im Blatt):** Das Blatt nennt, dass **fünf Module
(975 Zeilen) das Thema Dachaufbauten bilden** und die Werkbank nur „Gaube" führt — und dass
`auswechslung.ts` in **W-21 und W-22** als Nachbar geführt wird und **in keinem zuhause** ist.
*Ohne diesen Satz verschwinden 174 Zeilen zwischen zwei Blättern.*

**W-22/1-9 (`must_preserve`):** `resources/**` byte-identisch, Insel-Suite **unverändert** grün
(ohne Zahl — W-01N-Regel).

**W-22/1-10 (P1, Register mitgeführt):** Reifegrad **und** `gaubeGeometrie.ts` als Fundstelle.

**W-22/1-11 (P1, §3 wird BELEGT):** Befehl mit Ausgabe, an beiden Orten, **mindestens zwei
Befehlszeilen und zwei Ausgabewerte, je Ort einer**. *E2 aus Prüfung 03.*

## Kantenliste

```text
Pultgaube flacher als MIN_PULT_GRAD    -> MESSEN, was pruefeAufbau meldet
Flachdach-Aufbau unter MIN_FLACH_GRAD  -> dito
Gaube ragt ueber den First             -> DER Anlassfall (0,36 m bei 35 Grad).
                                          Das Blatt muss sagen, dass es geprueft WIRD
Aufbau breiter als die Dachflaeche      -> aufbauPlatzierung hat MAX_BREITE_ANTEIL,
                                          aber das ist NICHT im Scope — Verweis
Kamin auf der Kehle                     -> MESSEN oder als ungeprueft benennen
kein Render verfuegbar                  -> ist der GRUND fuer die numerische Pruefung,
                                          keine Einschraenkung. Als Entscheidung schreiben
F-027 aus M-01 nicht benutzt            -> dann sagt das Blatt das (W-22/1-3)
```

## Rückweg und Entdeckung

**Rückweg:** sieben Doku-Dateien und eine Registerzeile, `git revert` genügt.

**Entdeckung:** Baut jemand später wieder eine vertikale Rückwand an eine Pultgaube, hat `1-ZWECK`
nicht gewirkt — *der Anlassfall steht mit Zahlen im Dateikopf, und wenn er im Blatt steht, ist er
nicht mehr nur im Code auffindbar.*

## Konfliktprüfung (§5)

```text
A-12     ENTWURF     FORMELSAMMLUNG + VORGEHEN + BERICHT-A-12    KEINE Beruehrung
W-01N    ENTWURF     W-01-Blatt + FAHRPLAN                       KEINE Beruehrung
W-04/1 · W-05/1 · W-08/1 · W-11/1 · W-13/1 · W-21/1   ENTWURF    werkbank/W-xx/** + REGISTER.md
W-22/1   DIESES      werkbank/W-22/** + REGISTER.md
-> SIEBEN Blaetter teilen REGISTER.md, je eine Zeile plus Fundstellen, zeilenweise disjunkt.
   §3 loest es; belegt in W-22/1-11.
§3 GEMESSEN 11.08. (korrigiert, siehe docs/MELDUNG-ERFUNDENE-SPERRE-A-12.md):
   grep -cE '^\|.*\| *\*{0,2}.?IN_ARBEIT' docs/STATUS.md   -> 0
   A-12 traegt status: ENTWURF, NICHT IN_ARBEIT.
   -> §3 sperrt W-22/1 NICHT. Es darf in IN_ARBEIT, sobald DoR durch ist.
   Der Vorrang von A-12 (F-026 ist gelb, W-07/W-08 haengen fachlich daran) ist eine
   planerische EMPFEHLUNG, kein Verbot. Die Reihenfolge entscheidet der Plan-Pruefer.
FACHLICHE Beruehrungen: W-22 braucht laut Register W-07 (Dach) — ein Aufbau braucht eine
   Dachflaeche. Und auswechslung.ts beruehrt W-21. Kein Dateikonflikt.
```

```yaml
fehlerklasse: keine
prioritaet: P1
warteschlange: "Runde 2 damit vollstaendig: W-05, W-21, W-22 — Klasse A ist geschnitten"
befund_1: "der Werkzeugname ist enger als das Modul: gaubeGeometrie deckt Gaube, KAMIN und
           eine numerische PRUEFUNG ab, laut eigenem Dateikopf"
befund_2: "fuenf Module (975 Z) bilden das Thema Dachaufbauten, die Werkbank fuehrt nur Gaube"
befund_3: "auswechslung.ts (174 Z) steht in W-21 UND W-22 als Nachbar und ist in keinem
           zuhause — es braucht ein eigenes Werkzeug oder eine Zuordnung"
```


## §11 — Bericht W-22/1 (Generator, 12.08.2026)

```yaml
auftrag: "W-22/1"
zustand: CODE_FERTIG
bau_commit: "8a3acb53"
in_arbeit_commit: "6a592b26"
basis: "95fe1b88"

kriterien:
  W-22/1-1:  GEMELDET_MIT_ZWEI_ZAHLEN   # 3 woertlich / 0 nach Muster, s. u.
  W-22/1-2:  GRUEN   # nur Nummern
  W-22/1-3:  GRUEN   # F-027 Merkmal fuer Merkmal geprueft, Ergebnis differenziert
  W-22/1-4:  GRUEN   # Gaube, Kamin, Pruefung als drei Abschnitte
  W-22/1-5:  GRUEN   # lx/ly/lz mit Fundstelle 35, ly = Welt-Hoch ausdruecklich
  W-22/1-6:  GRUEN   # alle fuenf AK mit Zeile, kritisch/nicht-kritisch getrennt
  W-22/1-7:  GRUEN   # "Angebunden aus gaubeGeometrie.ts" + 26 Ausfuhren gruppiert
  W-22/1-8:  GRUEN   # fuenf Module 975 Z selbst nachgezaehlt; auswechslung.ts benannt
  W-22/1-9:  GRUEN   # drei Richtungen 0/0/0, Suite unveraendert gruen (ohne Zahl)
  W-22/1-10: GRUEN   # Register: BESCHRIEBEN + gaubeGeometrie.ts
  W-22/1-11: GRUEN   # 6a592b26: 2 Befehlszeilen, 2 Ausgabewerte

W-22/1-1_zwei_zahlen:
  woertlich: "3"
  muster: "0"
  die_drei: "Falschtreffer aus zitiertem Code — sie beginnen bei '<=' (AK1-Bedingung und
             zweimal die Entwaesserungsschranke) und enden am naechsten '>' im Text."
  zwei_echte_selbst_gefunden: "ich hatte <typ> und AK<n> geschrieben — beides sah wie ein
                               Platzhalter aus und ist ersetzt (<<Bauart>>, AK-Nummer)."

der_fachliche_kern:
  ampel: "haengt an EINER Zeile (491): rot wenn ein KRITISCHES AK verletzt ist, gelb wenn
          alles Kritische ok ist aber irgendein AK verletzt ODER feasible === false, sonst gruen"
  ak4_nicht_kritisch: "der Filter nennt AK1|AK2|AK3|AK5 einzeln — AK4 'Front lotrecht' fehlt darin.
                       Eine schiefe Front macht GELB, kein Rot. Das ist eine Entscheidung:
                       Schoenheitsfehler, kein Anschlussfehler."
  ak1_beim_kamin: "Z.485 setzt AK1 mit ok:true und ist:'lotrecht' — GESETZT, nicht gemessen.
                   Wer es als Messung liest, liest eine Setzung."
  hoehe_wird_geklemmt: "Entwaesserungsschranke h <= d*(tan a - tan(minNeigung)), sonst 'h klemmen'.
                        Der Anwender bekommt moeglicherweise eine andere Hoehe als eingegeben."

f_027_differenziert:
  zuordnung: "STIMMT — der Planner hat sie zu Recht als ✓ bestaetigt; das Modul setzt genau
              diese Gauben auf eine Dachflaeche"
  formel: "DECKT SICH NICHT mit dem Bau"
  merkmale:
    rise: "teilweise — Math.tan 6x, aber halfW statt d (Z.248), beim Kamin d/2 (Z.393)"
    quader: "NEIN — 0 Treffer; das Modul liefert Dreiecke und Linien"
    atan2: "NEIN — 0 Treffer; Ausrichtung ueber ein lokales Dreibein (aufbauBasis, Z.76)"
    vorgabe_15_grad: "NEIN — MIN_PULT_GRAD 5 und MIN_FLACH_GRAD 2"
  belegstelle: "F-027 zeigt selbst auf dachdecker_pro_3d.tsx:1190-1210 — M-01 auf Yamas Desktop.
                Die Datei existiert (132.374 B). Sie ist NICHT der ticket-Code."
  korrektur_an_mir_selbst: "meine erste Fassung sagte pauschal 'trifft nicht zu'. Zu grob —
                            das haette dem Planner widersprochen, wo er recht hat. Geschaerft
                            zu 'Thema ja, Formel nein', im Blatt und im Register."

werkbank_befund:
  fuenf_module_975_zeilen: "gaubeGeometrie 498 · aufbauPlatzierung 190 · auswechslung 174 ·
                            aufbauOrientierung 61 · aufbautenStatus 52 — selbst nachgezaehlt,
                            exakt die Zahl des Auftrags"
  auswechslung: "in W-21 UND W-22 als Nachbar gefuehrt, in keinem zuhause — 174 Zeilen"
  namensfalle: "wandaufbau.ts (72 Z) heisst 'Aufbau', ist aber Wandaufbau/U-Wert und bei W-02
                ausdruecklich Ausschluss. Zweite Namensfalle nach grundriss.ts bei W-05."

nicht_gemessen:
  - "ob das GEKLEMMTE h nach aussen sichtbar wird — steht als offene Frage in 6-PRUEFUNG"
  - "der Inhalt von M-01 (dachdecker_pro_3d.tsx) — nur Existenz und Groesse gemessen"

browserabnahme: "entfaellt — reine Dokumentblaetter"
ballbesitz: evaluator
```

---

## Evaluator-Votum (§11) — 12.08.2026

```yaml
auftrag: W-22/1
commit: 8a3acb53          # Bau; Basis 95fe1b88
votum: ABGENOMMEN
fehlerklasse: KEINE
gegenprobe: "acht Fundstellen einzeln geoeffnet · die Math-Zaehlung des Blattes Funktion fuer
  Funktion nachgezaehlt · die Ampel-Logik gegen Z.491 gehalten"
browser: nicht_anwendbar
datenbank: nicht_anwendbar
befunde: []
```

### Messtisch — ALLE ELF Zeilen

```text
-1   Platzhalter, vier Muster                        0
-2   3-FORMELN: die Treffer auf Math./=/atan2/hypot sind KEINE Rechnungen, sondern
     die gepruefte F-027-Behauptung und die Code-Zaehlung — siehe unten
-3   F-027 wird GEPRUEFT statt uebernommen           6 Nennungen, mit Ergebnis je Zeile
-4   drei Gegenstaende getrennt: 2-FUNKTION traegt "### 1 · GAUBE", "### 2 · KAMIN",
     "### 3 · PRUEFUNG" — und 1-ZWECK:18 sagt "Der Name ist enger als das Modul"
-5   lokales System lx/ly/lz im Blatt                 ja, mit Bedeutung je Achse
-6   7-GRENZEN liest pruefeAufbau() aus               drei Stufen, gegen Z.491 geprueft
-7   Herkunft "angebunden aus gaubeGeometrie.ts"      ja
-8   Dachaufbauten-Befund im Blatt                    ja
-9   resources/ im Bau-Commit 0 Pfade  ·  Suite 1692/1692
-10  Register: gaubeGeometrie.ts als Fundstelle       3 Treffer
-11  §3-Beleg in 6a592b26                             2 Befehlszeilen, 2 Ausgaben
```

### Die Math-Zählung des Blattes — Funktion für Funktion nachgezählt

*Das Blatt behauptet eine Inventur des Codes. Ich habe sie nicht gelesen, sondern wiederholt:*

```text
             Blatt   gemessen              Blatt   gemessen
Math.max      20        20        Math.abs    3         3
Math.tan       6         6        Math.sin    2         2
Math.hypot     4         4        Math.cos    2         2
Math.min       3         3        Math.atan   1         1
Math.atan2  "0 Treffer"            0   <- die Verneinung stimmt
Datei: 498 Zeilen
```

**Neun Zahlen, neun Treffer.** *Deshalb ist `-2` erfüllt und nicht verletzt: was wie eine Formel
aussieht (`rise = d · tan(φ)`, `atan2(fall_x, fall_z)`), ist die **Behauptung aus F-027**, die
`-3` ausdrücklich zu prüfen verlangt — und das Blatt prüft sie und weist zwei davon zurück.*

### Die Ampel — gegen den Code gehalten

```text
BLATT  rot    wenn ein KRITISCHES Kriterium verletzt ist
       gelb   wenn alles Kritische ok, aber ein Kriterium verletzt ODER feasible === false
       gruen  sonst
CODE   :491   const ampel: Ampel = !kritischOk ? 'rot' : (!allesOk || !feasibleFlag) ? 'gelb' : …
```

**Die drei Stufen und ihre Bedingungen stimmen mit der Zeile überein**, einschließlich des
`ODER feasible === false`, das ein flüchtiger Leser übersieht.

> **`-4` ist der stärkste Teil.** *Das Blatt trennt nicht nur, es sagt auch **warum die Trennung
> nötig ist**: „Wer nach ‚Kamin' sucht, findet dieses Blatt nicht — deshalb steht der Satz hier."
> Ein Werkzeugname, der enger ist als sein Modul, ist genau die Falle, in die ein Register läuft.*

**`-11` zum fünften Mal in Folge im ersten Anlauf** (W-04, W-11, W-05, W-21, W-22).

---

## Release-Prüfung (§10, Sammel-Kontrolle 2) — 12.08.2026

```yaml
auftrag: W-22/1
abnahme_commit: 88c70b00   # Evaluator-Votum; gemessen wurde 8a3acb53 (Bau, Basis 95fe1b88)
release_commit: 50e968e9   # HEAD bei dieser Prüfung
votum: RELEASE_FREI
ci: pass                   # npm run test:hausplaner selbst gefahren: tests 1692, pass 1692, fail 0
artefakte_reproduzierbar: nicht_anwendbar   # Doku-Stufe: kein Bundle, kein Build-Artefakt im Scope
migration: nicht_anwendbar
rueckweg: nicht_anwendbar   # nichts veröffentlicht; Rückweg wäre `git revert 8a3acb53`, acht Doku-Dateien
smoke_test_plan: "Entfällt — reine Dokumentblätter, keine sichtbare oder betriebliche Wirkung."
befunde: []
hinweise_ohne_hindernis: 2   # die zwei Typ-Funde des Plan-Prüfers, unten festgehalten
```

### Die Pflichtfrage der Sammel-Kontrolle — gezählt

```text
Kriterien im Blatt (Abschnitt Akzeptanzkriterien)   11   (W-22/1-1 … -11)
Zeilen im Votum-Messtisch                           11   (-1 -2 -3 -4 -5 -6 -7 -8 -9 -10 -11)
                                                    ->  11 von 11, lückenlos
```

**`-1` ist der Prüfstein dieser Zählung.** *Der Generator meldet es nicht als `GRUEN`, sondern als
`GEMELDET_MIT_ZWEI_ZAHLEN`: 3 wörtlich, 0 nach Muster — die drei sind Falschtreffer, die bei `<=`
beginnen und am nächsten `>` enden.* **Ich habe nachgezählt** (`grep -roE '<[^<>]{1,60}>'` über
alle sieben Blätter, `<=`/`>=`/`=>` ausgenommen): **0**. *Die zwei Zahlen waren also nicht
Unsicherheit, sondern Genauigkeit — und der Messtisch trägt die Zeile trotzdem.*

### Kette, Scope, Stichprobe

```text
Kette      dcf0071c (BEREIT) -> 6a592b26 (IN_ARBEIT) -> 8a3acb53 (Bau)
           -> cb727abc (CODE_FERTIG) -> 88c70b00 (ABGENOMMEN) -> HEAD
           je git merge-base --is-ancestor, Exit 0                      5/5
Basis      95fe1b88 -> 6a592b26  Exit 0
Scope      git show 8a3acb53 --name-only: 8 Dateien = 7 Blätter + REGISTER.md
           Pfade unter resources/ oder scripts/:                        0
Votum-SHA  Votum nennt 8a3acb53 = Bau-Commit                            deckungsgleich
Blattstand git diff 8a3acb53..HEAD -- W-22-gaube/                       0 Dateien
Ergebnis   Platzhalter über alle sieben Blätter                         0
Register   Z.44 W-22 BESCHRIEBEN, F-027 „Thema ja, Formel ⚠" · Z.166 gaubeGeometrie.ts
           498 Zeilen / 26 Ausfuhren als Fundstelle
```

### Zwei Typ-Funde des Plan-Prüfers — HINWEIS, kein Release-Hindernis

**Kein Kriterium von W-22/1 verlangt sie**, und der Plan-Prüfer hat sie ausdrücklich als Hinweis
übergeben. *Sie stehen hier, damit sie nicht zwischen den Runden verloren gehen.* **Ich habe beide
nachgemessen, nicht übernommen:**

```text
Vec3 — VIERMAL zeichenweise identisch definiert, kein Import verbindet sie:
  geometry/aufbauOrientierung.ts:22   export interface Vec3 { x: number; y: number; z: number; }
  geometry/gaubeGeometrie.ts:34       export interface Vec3 { x: number; y: number; z: number; }
  geometry/dachVerschneidung.ts:20    export interface Vec3 { x: number; y: number; z: number; }
  geometry/dachUForm.ts:12            export interface Vec3 { x: number; y: number; z: number; }

Dreieck — ZWEIMAL definiert, mit VERSCHIEDENER Bedeutung:
  renderers/three-d/dachMesh.ts:32    export type Dreieck = [WeltPunkt3, WeltPunkt3, WeltPunkt3]
  geometry/gaubeGeometrie.ts:37       export type Dreieck = [LokalPunkt, LokalPunkt, LokalPunkt]
```

> **Beim Nachmessen ist mir eine dritte Zeile aufgefallen, die den `Vec3`-Fund zuspitzt:**
> `gaubeGeometrie.ts:32` **importiert** `Vec3` aus `aufbauOrientierung` unter dem Aliasnamen
> `BasisVec3` — und definiert zwei Zeilen darunter, in `:34`, ein **eigenes** `Vec3`. *Eine Datei
> führt damit zwei Namen für dieselbe Struktur, einen geliehenen und einen eigenen. Wer später
> eines der beiden ändert, ändert in dieser Datei nur die Hälfte.*

**Warum das trotzdem kein Hindernis ist:** *`Vec3` und `Dreieck` sind Produktivcode; W-22/1 ist
eine reine Doku-Stufe und hat `resources/**` als `must_preserve` — der Bau durfte sie gar nicht
anfassen und hat es mit 0 Pfaden auch nicht.* **Es ist dieselbe Klasse wie `Punkt2D` (viermal, aus
W-21/W-08) und `MassPunkt` (zweimal, aus W-11)**, und dieselbe Regel gilt: *benennen, nicht
zusammenlegen* — wer vier Definitionen über drei Werkzeuggrenzen hinweg vereinigt, entscheidet
etwas und räumt nicht auf. **`Dreieck` ist der schärfere der beiden**: ein Name, zwei
Koordinatensysteme (`WeltPunkt3` gegen `LokalPunkt`) — hier divergieren die Bedeutungen nicht
künftig, sie sind es **heute schon**.

**Eigentümer des Hinweises ist der PLANNER**, wie bei `Punkt2D`: er entscheidet, ob daraus ein
Blattzusatz (7-GRENZEN bei W-22 und den Nachbarn) oder ein eigener Auftrag wird. *Aus einem
Release-Vermerk entsteht kein Auftrag.*

**Urteil: `RELEASE_FREI`.** *Ohne Befund; die zwei Funde sind als Hinweise festgehalten.*
