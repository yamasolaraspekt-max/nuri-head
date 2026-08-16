# A-39 — Sechs Prüfungen, die ein Blatt gegen sich selbst hält

```yaml
auftrag: "A-39"
werkzeug: "— (Werkzeug der Rollenkette, kein Hausplaner-Werkzeug)"
art: "BAU — ein Pruefskript fuer Auftragsblaetter (sechs Innenpruefungen). Es laeuft im DoR-SCHRITT, nicht im Tor:
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
anlass: "Sechs Blattfehler: fuenf an EINEM Tag, der sechste am 16.08., jeder erst beim Bauen oder Abnehmen gefunden,
         jeder vor dem ersten Zeichen Code vorhanden und maschinell erkennbar."
gebaut_in: "ticket-rolle-generator (rolle/generator)"
staut_hinter: "A-37 — das Tor schuetzt A-39, nicht umgekehrt."
```

## Der Anlass: sechs Fehler, alle zu spät gefunden

| # | Fehler | gefunden | Kosten |
|---|---|---|---|
| 1 | **Kantenliste ohne Kriterium** — A-37 nannte sechs Kanten, **kein** Kriterium verlangte sie | vom Plan-Prüfer, **nachdem der Bau lief** | K6 fehlt im Code, das Tor sperrt zwei Rollen aus |
| 2 | **Feste Zahl ohne Standbezug** — A-33-1 („genau EINS"), A-37-11 („Suite 1750") | beim Ziehen und in der DoR | zwei Kriterien abgelaufen, ohne dass der Schreibende es erfuhr |
| 3 | **Geforderte Datei ohne Erzeuger** — A-37-12 verlangte `.aus-lockfile`, **niemand schrieb sie** | in DoR Runde 3 | ein Kriterium, das nie erfüllbar gewesen wäre |
| 4 | **Kriterium gegen den eigenen Blattkopf** — A-33-7 verlangte „`scripts/` null Mal", `art:` verlangte genau dieses Skript | vom Evaluator, in der Abnahme | `SPEC_BLOCKED`, eine Runde verloren |
| 5 | **Rückgabewert doppelt vergeben** — `exit 3` war „Kennung fehlt" **und** `MODUL` | in DoR Runde 3 | zwei Bedeutungen auf einem Code, beide Seiten ahnungslos |

| 6 | **Rot-Lage mit Uhr** — A-38-2 belegte „28 von 32" aus einem wandernden 48-Stunden-Fenster | vom Plan-Prüfer, **6 h 37 min vor Ablauf** | wäre um 22:53 von selbst grün geworden, **ohne dass jemand etwas behob** |

**Alle sechs waren vor dem ersten Zeichen Code vorhanden und maschinell erkennbar.** Jeder kostete
eine Runde. **Keiner wurde von einer Prüfstation gefunden — alle erst, als jemand das Blatt
benutzen wollte.**

## Warum das in den DoR-Schritt gehört und nicht ins Tor

**Das Tor prüft Commits. Diese sechs prüfen ein Blatt.** Ein Blatt wird geschnitten, bevor es einen
Commit gibt, und die Fehler wirken, sobald jemand es liest. **Im Tor käme die Prüfung zu spät und
träfe den Falschen** — der Bauende hätte den Blattfehler nicht verursacht.

## Scope — sechs Prüfungen über eine Datei

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
```

**Ausgabe: je Fund eine Zeile mit Kennung und Fundstelle. Rückgabe 1, wenn ein Fund vorliegt.**

## Nicht-Ziele

- **Keine Fachprüfung.** A-39 prüft **Form**, nicht ob die Sache stimmt. *(Das ist A-40.)*
- **Keine Änderung an `docs/STATUS.md`**, kein Hausplaner-Code, keine Migration.
- **Kein Eingriff in `commit-pruefen.sh`** — dort arbeitet A-37. *(Dieselbe Abgrenzung wie A-38.)*
- **Kein Blatt wird automatisch geändert.** Das Skript **meldet**; das Beheben gehört dem Planner.
- **Keine Prüfung fremder Dokumente** — nur Auftragsblätter unter `docs/auftraege/`.

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
- **A-39-4** · **P3 findet A-37-12 am Stand `7ef8f046`** (vor A-37-16) — die Marke ohne Erzeuger.
  *(A-39-3, -4 und -11 nannten bis 16.08. keinen festen Stand, während A-39-2, -5 und -6 es taten
  — **dieselbe Art Angabe, zwei Handhabungen, in dem Blatt, das genau diesen Fehler prüft.** Der
  Plan-Prüfer hat die drei fehlenden Stände nicht nur bemängelt, sondern **gesucht und geliefert**;
  sie stehen jetzt hier.)*
- **A-39-5** · **P4 findet A-33-7 am Stand vor `5db5f8a9`** — „`scripts/` null Mal" gegen `art:`.
- **A-39-6** · **P5 findet den doppelten `exit 3`** am Stand vor `5bbc55bf`.
- **A-39-11** · **P6 findet die Rot-Lage mit Uhr.** Gegen **`5bbc55bf`** gefahren — den Stand von
  A-38 **vor** der Umstellung —, **muss A-38-2 gemeldet werden**: dort belegte *„28 von 32
  Merges"* aus einem `--since='48 hours ago'`-Fenster.
  **Negativprobe am selben Paar:** die heutige Fassung mit fünf festen SHAs wird **nicht**
  gemeldet. *(Gegenprobe des Plan-Prüfers: in `5bbc55bf` steht „28 von 32" zweimal und feste
  SHAs null Mal, heute umgekehrt — der Stand trägt genau das, was P6 finden soll.)*
  **Und die Probe, die den Sinn trägt:** ein Kriterium, das eine Zahl **mit** Zeitstempel oder
  `Bau-Stand` nennt, ist **kein** Fund — P6 sucht das **wandernde Fenster**, nicht jede Zeitangabe.
  *(Sonst meldet es jede Messvorschrift und wird weggeklickt — A-03.)*
- **A-39-7** · **Positivfall gesamt:** ein Blatt ohne Befund erzeugt **keine Ausgabe** und
  Rückgabe **0**. *(Ohne diesen Beleg ist das Skript von einem kaputten nicht zu unterscheiden.)*
- **A-39-8** · **Alle sechs Kanten K1–K6 sind behandelt und je einzeln belegt.**
  *(Dieses Kriterium fehlte in A-37 und ist der Grund, warum dort eine Kante durchfiel.)*
- **A-39-9** · **Kein Nicht-Ziel berührt.** `git show --stat` nennt weder `resources/`, `app/`,
  `docs/STATUS.md` noch `scripts/commit-pruefen.sh`.
- **A-39-10** · **Suite grün und Zahl unverändert gegen den Bau-Stand**, `tsc exit=0`.
  Zahl **unmittelbar vor dem Bau** erheben, nicht gegen eine feste Zahl prüfen.

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
Ich messe im selben Dokument: **32 Kennungen** (`^### [FNS]-`), **4 Treffer**, **23 ohne Ampel**.

**Die vier stimmen überein, die anderen zwei nicht** — wir zählen verschieden (Überschriften gegen
Nennungen, und die Ampel steht nicht immer in der Überschriftszeile). **Ich übernehme seine Zahl
nicht und behaupte meine nicht:** die Größenordnung ist die Aussage — *die nachgerechneten Einträge
sind einstellig, die abgeschriebenen sind die Regel.* **Die genaue Zahl gehört zu A-40, mit einem
benannten Zählbefehl.**
