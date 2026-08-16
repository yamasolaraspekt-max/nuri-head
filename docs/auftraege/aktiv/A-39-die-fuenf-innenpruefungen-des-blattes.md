# A-39 — Acht Prüfungen, die ein Blatt gegen sich selbst hält

```yaml
auftrag: "A-39"
werkzeug: "— (Werkzeug der Rollenkette, kein Hausplaner-Werkzeug)"
art: "BAU — ein Pruefskript fuer Auftragsblaetter (acht Innenpruefungen). Es laeuft im DoR-SCHRITT, nicht im Tor:
      es misst ein BLATT, keinen Commit. KEINE Aenderung an docs/STATUS.md, KEIN Hausplaner-Code."
spur: A
heimat_app: ticket
dor_beleg: "steht aus — plan-pruefer."
dor_schnitt_sha: "99add90f"
status_steht_in: docs/STATUS.md
basis_sha: 99add90f
prioritaet: P1
ballbesitz: "plan-pruefer (DoR)"
claim: "planner 16.08. — Claim VOR dem Schnitt."
kennung_geprueft: "A-39 hat NULL Treffer in docs/STATUS.md und NULL Blaetter in
                   docs/auftraege/aktiv/. A-01 bis A-38 sind vergeben. Frei."
anlass: "Acht Blattfehler: fuenf an EINEM Tag, drei weitere am 16.08., jeder erst beim Bauen oder Abnehmen gefunden,
         jeder vor dem ersten Zeichen Code vorhanden und maschinell erkennbar."
gebaut_in: "ticket-rolle-generator (rolle/generator)"
staut_hinter: "A-37 — das Tor schuetzt A-39, nicht umgekehrt."
```

## Der Anlass: acht Fehler, alle zu spät gefunden

| # | Fehler | gefunden | Kosten |
|---|---|---|---|
| 1 | **Kantenliste ohne Kriterium** — A-37 nannte sechs Kanten, **kein** Kriterium verlangte sie | vom Plan-Prüfer, **nachdem der Bau lief** | K6 fehlt im Code, das Tor sperrt zwei Rollen aus |
| 2 | **Feste Zahl ohne Standbezug** — A-33-1 („genau EINS"), A-37-11 („Suite 1750") | beim Ziehen und in der DoR | zwei Kriterien abgelaufen, ohne dass der Schreibende es erfuhr |
| 3 | **Geforderte Datei ohne Erzeuger** — A-37-12 verlangte `.aus-lockfile`, **niemand schrieb sie** | in DoR Runde 3 | ein Kriterium, das nie erfüllbar gewesen wäre |
| 4 | **Kriterium gegen den eigenen Blattkopf** — A-33-7 verlangte „`scripts/` null Mal", `art:` verlangte genau dieses Skript | vom Evaluator, in der Abnahme | `SPEC_BLOCKED`, eine Runde verloren |
| 5 | **Rückgabewert doppelt vergeben** — `exit 3` war „Kennung fehlt" **und** `MODUL` | in DoR Runde 3 | zwei Bedeutungen auf einem Code, beide Seiten ahnungslos |
| 6 | **Rot-Lage mit Uhr** — A-38-2 belegte „28 von 32" aus einem wandernden 48-Stunden-Fenster | vom Plan-Prüfer, **6 h 37 min vor Ablauf** | wäre um 22:53 von selbst grün geworden, **ohne dass jemand etwas behob** |
| 7 | **Kriterium ohne gangbaren Weg** — A-41-4, A-41-5, A-37-18, **alle drei am selben Tag vom selben Schreiber** | **jedes Mal vom BAUENDEN**, keiner Prüfung | drei Runden; das Kriterium war gegen eine Vorstellung geschrieben, nicht gegen den Weg |

| 8 | **Der Ort ist das Kriterium, nicht die Sache** — `ls-files` auf **eine** Datei als Aussage über einen Baum mit 7460 · Wächtersuche im falschen Verzeichnis · „release" am toten Gleichnamigen | **drei Rollen unabhängig an einem Tag**, jede von einer anderen | einer der drei stand in der **Prozessquelle** |

**Alle acht waren vor dem ersten Zeichen Code vorhanden und maschinell erkennbar.** Jeder kostete
eine Runde. **Keiner wurde von einer Prüfstation gefunden — alle erst, als jemand das Blatt
benutzen wollte.**

## Warum das in den DoR-Schritt gehört und nicht ins Tor

**Das Tor prüft Commits. Diese acht prüfen ein Blatt.** Ein Blatt wird geschnitten, bevor es einen
Commit gibt, und die Fehler wirken, sobald jemand es liest. **Im Tor käme die Prüfung zu spät und
träfe den Falschen** — der Bauende hätte den Blattfehler nicht verursacht.

## Scope — acht Prüfungen über eine Datei

```
scripts/blatt-pruefen.sh <pfad-zum-blatt>

P1  KANTE OHNE KRITERIUM
    Jede Kennung K<n> in einer Tabellenzeile muss von mindestens einem
    Abnahmekriterium genannt werden (namentlich oder als "alle Kanten").
    Ausgabe je Fund: die Kante und die Zeile, in der sie steht.

P2  FESTE ZAHL OHNE STANDBEZUG
    Ein Kriterium, das eine Zahl mit einer Bestandsaussage verbindet
    ("genau N", "Suite N", "N Treffer"), muss im selben Kriterium einen
    SHA, einen Zeitstempel oder das Wort "Bau-Stand" tragen.
    Sonst: Meldung mit Kriteriumskennung.

P3  GEFORDERTE DATEI OHNE ERZEUGER
    Nennt ein Kriterium einen Dateipfad, den es als vorhanden VERLANGT,
    muss das Blatt an anderer Stelle sagen, WER ihn erzeugt — im Scope,
    in einem anderen Kriterium oder im Kopf.

P4  KRITERIUM GEGEN BLATTKOPF
    Kein Kriterium darf einen Pfad als "null Mal" fordern, den der Kopf
    (`art:`, `gebaut_in:`) als Liefergegenstand nennt. Umgekehrt ebenso.

P5  RUECKGABEWERT DOPPELT
    Wird in einem Blatt mehr als eine Bedeutung auf denselben exit-Code
    gelegt, ist das ein Fund — unabhaengig davon, ob beide Stellen
    denselben Bauteil betreffen.

P6  ROT-LAGE MIT UHR
    Eine Rot-Lage, die aus einem WANDERNDEN Zeitfenster stammt
    (--since='N hours ago', "heute", "seit gestern"), ist ein Fund.
    Sie wird von selbst gruen, ohne dass jemand etwas behoben hat.
    Verlangt: feste SHAs, ein Zeitstempel, oder ein KONSTRUIERTER Fall.
    Belegfall: A-38-2 lief am 16.08. um 22:53 ab — der juengste
    markenlose Merge fiel aus dem 48-Stunden-Fenster, danach haette
    jede Pruefung 0 von 102 gemessen und keinen Beleg mehr gefunden.

P7  KRITERIUM OHNE GANGBAREN WEG
    Zu JEDEM Kriterium muessen drei Fragen beantwortbar sein:
      WER fuehrt die Handlung aus?
      DARF diese Rolle sie ausfuehren?
      EXISTIERT die verlangte Eigenschaft auf dem Messweg?
    Ist eine Antwort nein oder unbekannt, ist es ein Fund.
    UND DIE VIERTE FRAGE, ergaenzt 16.08. nach einem Befund am
    eigenen Werkzeug: DARF SIE ES NOCH, WENN DER BAU FERTIG IST?
    Eine Erlaubnis mit Ablaufdatum ist keine Erlaubnis.
    Belegfall: A-42-8 bescheinigt dem Generator, er duerfe
    docs/STATUS.md anfassen — A-37s Sperre nimmt ihm das Recht,
    sobald sie zuendet. Das Kriterium prueft nur den Moment
    seines Schnitts.
    DREI BELEGFAELLE VOM 16.08., alle aus DEMSELBEN Blattschreiber:
      A-41-4  verlangte SCHREIBEN von docs/STATUS.md — das darf nur
              der Integrator, den es zu dem Zeitpunkt nicht gab.
      A-41-5  verlangte eine COMMIT-Zeit von einer DATEI-Messung —
              eine aus git show gelesene Zeile hat keine.
      A-37-18 verlangte vom Generator einen Zustand, dessen einziger
              Weg TRANSPORT ist, und Transport ist ihm untersagt.
    Alle drei fielen erst dem BAUENDEN auf, keiner einer Pruefung,
    und alle drei kosteten eine Runde. Das ist kein Zufall, sondern
    eine Fehlerform: das Kriterium wird gegen eine VORSTELLUNG
    geschrieben statt gegen den WEG, auf dem es erfuellt wird.

P8  DER ORT IST DAS KRITERIUM, NICHT DIE SACHE
    Eine Messvorschrift, die einen VERZEICHNISPFAD oder einen
    DATEINAMEN als Suchraum festlegt, ist ein Fund, wenn die
    gesuchte Sache auch anderswo liegen kann.
    Verlangt: die SACHE benennen (Funktionsname, Testzweck,
    Wirkung) und den Pfad hoechstens als Beispiel.
    DREI BELEGFAELLE VOM 16.08., aus DREI VERSCHIEDENEN ROLLEN:
      Planner       mass `ls-files <EINE Datei>` und schrieb
                    "ls-files 0" ueber den ganzen Baum — der
                    Baum traegt 7460 Dateien. Stand in der
                    PROZESSQUELLE.
      Generator     suchte den Waechter in tests/Feature/Hausplaner/
                    und fand ihn nicht; er liegt eine Ebene hoeher.
      Planner       mass "release" am Verzeichnis ticket-rolle-release
                    statt am lebenden ticket-release-pruefung.
    Es ist H-8 in der SUCHVORSCHRIFT: nicht der gemessene Ort ist
    falsch, sondern dass ueberhaupt ein Ort gemessen wurde.
    Drei Rollen unabhaengig an einem Tag — belegt, nicht vermutet.
```

**Ausgabe: je Fund eine Zeile mit Kennung und Fundstelle. Rückgabe 1, wenn ein Fund vorliegt.**

## Nicht-Ziele

- **Keine Fachprüfung.** A-39 prüft **Form**, nicht ob die Sache stimmt. *(Das ist A-40.)*
- **Keine Änderung an `docs/STATUS.md`**, kein Hausplaner-Code, keine Migration.
- **Kein Eingriff in `commit-pruefen.sh`** — dort arbeitet A-37. *(Dieselbe Abgrenzung wie A-38.)*
- **Kein Blatt wird automatisch geändert.** Das Skript **meldet**; das Beheben gehört dem Planner.
- **Keine Prüfung fremder Dokumente** — nur Auftragsblätter unter `docs/auftraege/`.
  **⚠ GEMESSEN 18:3x gegen P8, nicht angenommen.** Der Pfad ist hier ein Suchraum, also der
  Fall, den P8 meint. **32 Dateien außerhalb tragen ebenfalls `auftrag: "X-NN"`** — sie heißen
  `BEFUND-*` und `BERICHT-*`. **Keine einzige trägt `## Abnahmekriterien`**, also ist keine ein
  Auftragsblatt. *Die Sache ist „Blatt mit Abnahmekriterien", nicht „Datei in einem Ordner" —
  und beide Mengen fallen hier zusammen. Der Pfad ist damit belegt, nicht geraten.*

## Kanten

| # | Fall | Verlangtes Verhalten |
|---|---|---|
| K1 | **Blatt ohne Kantenliste** | P1 gilt als erfüllt, keine Meldung |
| K2 | **Kante nur im Fließtext genannt**, nicht in einer Tabellenzeile | **nicht** erfasst — ausdrücklich benannte Grenze, nicht stillschweigend |
| K3 | **Zahl in einer Begründung**, nicht in einem Kriterium | P2 greift nicht — sie ist keine Zusage |
| K4 | **Ein Kriterium nennt einen Pfad als Nicht-Ziel** (`resources/` null Mal) | P3 greift nicht — Nicht-Ziele brauchen keinen Erzeuger |
| K5 | **Zwei Kriterien nennen denselben Code für dasselbe** | **kein** Fund — P5 sucht zwei **Bedeutungen**, nicht zwei Nennungen |
| K6 | **Das Blatt ist ein Stilllegungs-Wegweiser** *(wie A-33-„zehn")* | überspringen, sobald `zustand_dieses_pfades: STILLGELEGT` im Kopf steht |

## Abnahmekriterien

- **A-39-1** · `scripts/blatt-pruefen.sh` existiert und ist ausführbar.
  **Rot am Basis-SHA:** `ls scripts/ | grep -ciE 'blatt|auftrag'` → **0**.
- **A-39-2** · **P1 findet den echten Fall.** Gegen den Stand `0ee521f7` von A-37 gefahren, **muss
  K6 gemeldet werden** — dort nannte kein Kriterium die Kanten.
  **Positivprobe daneben:** A-35, A-36 und A-38 tragen ein Kanten-Kriterium und werden **nicht**
  gemeldet. *(Gemessen 16.08.: 3 von 4 Blättern mit Kantenliste hatten eines.)*
- **A-39-3** · **P2 findet A-33-1 („genau EINS", Stand **`8559b555`**) und A-37-11 („Suite 1750",
  Stand **`7ef8f046`**, 14.08. 22:35) am jeweils alten
  Stand** — und meldet **nicht** die heutigen Fassungen, die an Invariante bzw. Bau-Stand gebunden
  sind. **Das ist die schärfste Probe: dieselbe Datei, zwei Stände, zwei Antworten.**
- **A-39-4** · **P3 findet A-37-12 an einem Stand, an dem die Marke ohne Erzeuger vorlag.**
  **⚠ BEFUND DES PLAN-PRÜFERS, zutreffend: der SHA `7ef8f046` stammt von mir und an ihm
  existiert der zu findende Fall NICHT.** *Ich habe heute Nachmittag einen Stand nachgetragen,
  ohne zu prüfen, ob der Fall dort steht — genau der Fehler, den P2 verhindern soll, begangen
  beim Beheben von P2.*
  **Verlangt: der Stand wird BEIM BAU ermittelt** — der letzte Commit, an dem `A-37-12` die
  Marke verlangt und kein Kriterium sie erzeugt. **Kein SHA im Kriterium, bevor er am Fall
  geprüft ist.** *(Ein falscher Stand ist schlimmer als keiner: er sieht geprüft aus.)* — die Marke ohne Erzeuger.
  *(A-39-3, -4 und -11 nannten bis 16.08. keinen festen Stand, während A-39-2, -5 und -6 es taten
  — **dieselbe Art Angabe, zwei Handhabungen, in dem Blatt, das genau diesen Fehler prüft.** Der
  Plan-Prüfer hat die drei fehlenden Stände nicht nur bemängelt, sondern **gesucht und geliefert**;
  sie stehen jetzt hier.)*
- **A-39-5** · **P4 findet A-33-7 am Stand vor `5db5f8a9`** — „`scripts/` null Mal" gegen `art:`.
- **A-39-6** · **P5 findet den doppelten `exit 3`.**
  **⚠ BEFUND: der Stand stand in der FALSCHEN RICHTUNG.** Hier hieß es *„am Stand **vor**
  `5bbc55bf`"* — **der doppelte `exit 3` entstand MIT `5bbc55bf`, nicht davor.** *Wer die
  Positivprobe am Elter fährt, findet nichts und hält die Prüfung für kaputt.*
  **Verlangt: `5bbc55bf` selbst, und die Negativprobe am NACHFOLGER**, der ihn behoben hat.
- **A-39-11** · **P6 findet die Rot-Lage mit Uhr.** Gegen **`5bbc55bf`** gefahren — den Stand von
  A-38 **vor** der Umstellung —, **muss A-38-2 gemeldet werden**: dort belegte *„28 von 32
  Merges"* aus einem wandernden 48-Stunden-Fenster. *(In Worten statt als Befehl — sonst meldet P6 sein eigenes Beispiel und erzeugt einen Fehlalarm an sich selbst.)*
  **Negativprobe am selben Paar:** die heutige Fassung mit fünf festen SHAs wird **nicht**
  gemeldet. *(Gegenprobe des Plan-Prüfers: in `5bbc55bf` steht „28 von 32" zweimal und feste
  SHAs null Mal, heute umgekehrt — der Stand trägt genau das, was P6 finden soll.)*
  **Und die Probe, die den Sinn trägt:** ein Kriterium, das eine Zahl **mit** Zeitstempel oder
  `Bau-Stand` nennt, ist **kein** Fund — P6 sucht das **wandernde Fenster**, nicht jede Zeitangabe.
  *(Sonst meldet es jede Messvorschrift und wird weggeklickt — A-03.)*
- **A-39-12** · **P7 findet alle drei Wegfehler vom 16.08.**
  **Positivproben:** `A-41-4` am Stand `a613100e` (verlangt Schreiben ohne Berechtigten) ·
  `A-41-5` am Stand `74cc04d5` (Commit-Eigenschaft von Datei-Messung) · `A-37-18` am Stand
  `78841603` (Weg dem Adressaten untersagt) — **alle drei gemeldet.**
  **Negativproben:** die heutigen Fassungen derselben drei Kriterien **nicht.**
  **Grenze, ausdrücklich:** P7 prüft, ob die drei Fragen **beantwortbar** sind — es beurteilt
  **nicht**, ob die Antwort klug ist. *Ein Prüfer, der Urteile fällt, wird weggeklickt.*
- **A-39-13** · **P8 findet die drei Ortsfehler vom 16.08.**
  **Positivproben:** die Regel-Ergänzung am Stand `e802c1f8` („`ls-files` 0" über einen Baum
  mit 7460 Dateien) · W-17-1-3 vor `d7f0c93d` (Suchraum `tests/Feature/Hausplaner/`) ·
  die Baum-Erhebung in A-37-18 am Stand `78841603`.
  **Negativprobe:** eine Messvorschrift, die einen Pfad **als Beispiel** nennt und die Sache
  benennt, ist **kein** Fund — sonst meldet P8 jede Fundstellenangabe.
  **Grenze:** P8 prüft die **Suchvorschrift**, nicht das Ergebnis. *Ein an der richtigen Stelle
  gefundenes Ergebnis kann trotzdem aus einer zu engen Vorschrift stammen — dann ist es Glück,
  und Glück ist kein Prüfverfahren.*
- **A-39-7** · **Positivfall gesamt:** ein Blatt ohne Befund erzeugt **keine Ausgabe** und
  Rückgabe **0**. *(Ohne diesen Beleg ist das Skript von einem kaputten nicht zu unterscheiden.)*
- **A-39-8** · **Alle sechs Kanten K1–K6 sind behandelt und je einzeln belegt.**
  *(Dieses Kriterium fehlte in A-37 und ist der Grund, warum dort eine Kante durchfiel.)*
- **A-39-9** · **Kein Nicht-Ziel berührt.** `git show --stat` nennt weder `resources/`, `app/`,
  `docs/STATUS.md` noch `scripts/commit-pruefen.sh`.
- **A-39-10** · **Suite grün und Zahl unverändert gegen den Bau-Stand**, `tsc exit=0`.
  Zahl **unmittelbar vor dem Bau** erheben, nicht gegen eine feste Zahl prüfen.

## Die Reichweite dieses Prüfers — gemessen 19:0x, damit sie niemand überschätzt

**Gemessen über alle Blätter unter `docs/auftraege/`:**

```
                 Blaetter   mit Kantentabelle   mit Abnahmekriterien
A-Blaetter          44             8                    8
W-Blaetter          41             1                    1
                    --            --                   --
                    89             9                    9

BERICHTIGT 16.08. nach Gegenmessung des Plan-Pruefers:
  Nenner ist 89 und nicht 85 — meine Zaehlung liess vier Blaetter aus.
  Die NEUN stimmt zeichengenau. Stumm durchlaufen 80.
  Und P1 im engeren Sinn erfasst 8 von 34 Blaettern MIT Kantenliste —
  die Struktur "Kanten UND Kriterien" ist enger als "Kanten".
```

> **A-39 prüft die Struktur „Kanten + Abnahmekriterien". Diese Struktur haben 9 von 85
> Blättern.** Die übrigen 76 laufen durch — **korrekt** (K1 deckt „Blatt ohne Kantenliste" ab),
> aber **stumm**.

**Das ist kein Mangel des Prüfers, sondern seine Reichweite** — und sie gehört genannt, weil ein
Lauf über alle Blätter mit „0 Funde in 85 Blättern" endet und **wie eine Unbedenklichkeits-
bescheinigung für 85 aussieht, während er 76 nie angesehen hat.**

**Und die Proben spiegeln das:** von 17 Nennungen in den Kriterien stammen 15 aus A-Blättern
(A-37 7×, A-38 3×, A-41 2×, A-33 2×). **Das ist kein Zufall — bei den W-Blättern gibt es nur
ein einziges mit dieser Struktur (`W-17/1`), und es kommt in den Proben vor.**

**Auflage daraus:** der Lauf nennt **beide** Zahlen — geprüfte Blätter und übersprungene.
*Ein Prüfer, der nicht sagt, was er nicht angesehen hat, wird für gründlicher gehalten als er ist.*

## Rückweg und Entdeckung

- **Rückweg:** ein neues Skript, sonst nichts. **Rücknahme = Commit zurückdrehen.** Es ist nirgends
  eingehängt und kann keinen Commit verhindern.
- **Entdeckung:** A-39-2 bis A-39-6 und A-39-11 sind **sechs historische Positivproben** — jede an einem Stand,
  an dem der Fehler nachweislich vorlag. **Ein Prüfer, den man nie hat sprechen sehen, ist von
  einem kaputten nicht zu unterscheiden.**
- **Der Fall, der beim Bauen am ehesten übersehen wird:** K5. **P5 sucht zwei *Bedeutungen* auf
  einem Code, nicht zwei Nennungen** — wer nur zählt, meldet jedes Blatt, das `exit 1` zweimal
  erwähnt.

## Was dieser Auftrag nicht beantwortet

**Ob die Blätter fachlich stimmen.** A-39 prüft, ob ein Blatt in sich schlüssig ist — **nicht, ob
seine Aussagen wahr sind.** Das ist A-40 und eine andere Klasse. **Beides in ein Blatt zu legen
wäre genau der Fehler, den A-37 heute kostet: ein Blatt, das während der DoR wächst.**

## Zwei Zahlen, die ich nenne ohne sie zu behaupten

Yama hat gemessen: **48 Kennungen, 4 Treffer auf „nachgerechnet", 13 ohne Ampel.**
**⚠ BERICHTIGT 16.08.: die 32 ist in KEINER Lesart reproduzierbar** — der Plan-Pruefer misst 36, 37 oder 40, je nach Zaehlweise, und ich selbst heute 64 ueber beide Sammlungen. **Die Groessenordnungs-Aussage traegt, die Zahl nicht.** *Sie steht hier nur noch als Beleg dessen, was ich damals gemessen zu haben glaubte.*

Ich mass im selben Dokument: **32 Kennungen** (`^### [FNS]-`), **4 Treffer**, **23 ohne Ampel**.

**Die vier stimmen überein, die anderen zwei nicht** — wir zählen verschieden (Überschriften gegen
Nennungen, und die Ampel steht nicht immer in der Überschriftszeile). **Ich übernehme seine Zahl
nicht und behaupte meine nicht:** die Größenordnung ist die Aussage — *die nachgerechneten Einträge
sind einstellig, die abgeschriebenen sind die Regel.* **Die genaue Zahl gehört zu A-40, mit einem
benannten Zählbefehl.**
