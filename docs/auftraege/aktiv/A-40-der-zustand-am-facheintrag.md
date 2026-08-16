# A-40 — Sachverstand bekommt kein Amt, sondern einen Zustand am Eintrag

```yaml
auftrag: "A-40"
werkzeug: "— (Wissensspeicher, kein Hausplaner-Werkzeug)"
art: "BAU — zwei Pflichtfelder, drei Zustaende und EINE Innenpruefung, die neben die
      fuenf aus A-39 gehaengt wird. KEIN Hausplaner-Code, KEINE Migration."
spur: A
heimat_app: ticket
dor_beleg: "steht aus — plan-pruefer."
dor_schnitt_sha: "99add90f"
status_steht_in: docs/STATUS.md
basis_sha: 99add90f
prioritaet: P2
ballbesitz: "plan-pruefer (DoR)"
claim: "planner 16.08. — Claim VOR dem Schnitt."
kennung_geprueft: "A-40 hat NULL Treffer in docs/STATUS.md und NULL Blaetter in
                   docs/auftraege/aktiv/. A-01 bis A-39 sind vergeben. Frei."
anlass: "Drei Fachfehler in einer Woche, alle vom BENUTZER gefunden und von keiner
         Pruefstation: F-004 beim Bauen, F-054 beim Rechnen, S-060/S-040 beim Lesen."
gebaut_in: "ticket-rolle-generator (rolle/generator)"
staut_hinter: "A-37, dann A-39 — das Tor schuetzt beide, und A-39 baut die Bauform,
               in die A-40 nur noch einen siebten Punkt einhaengt."
regelgrundlage: "ARBEITSREGELN.md, Nachtrag vom 16.08. — die drei Zustaende, die zwei
                 Pflichtfelder und der Drei-Fragen-Test sind ENTSCHIEDEN und stehen dort.
                 Dieses Blatt BAUT sie, es erfindet sie nicht."
```

## Warum keine Prüfstation das leisten kann

**Alle fünf Rollen beziehen ihr Fachwissen aus derselben Quelle.** Ein zweiter Leser desselben
Dokuments ist keine zweite Meinung — **er ist dieselbe Meinung zweimal.** Ein „Fach-Prüfer" hätte
F-004 aufgeschlagen und dasselbe falsche Vorzeichen gelesen wie alle vor ihm.

**⚠ BERICHTIGT nach Befund des Plan-Prüfers:** *„von keiner Prüfstation gefunden"* **trifft auf
`F-054` NICHT zu.** Sie ist beim Rechnen aufgefallen — das ist eine Prüfstation, nur keine
formale. **Zwei der drei stimmen, der dritte nicht.** *Ein Anlass, der die Lage schlimmer
zeichnet als sie ist, macht den Auftrag nicht dringlicher, sondern angreifbar.*

**Belegt: zwei der drei Fachfehler dieser Woche wurden vom Benutzer gefunden, der dritte beim
Rechnen.**

```
F-004          Vorzeichen vertauscht      vom GENERATOR beim Bauen
F-054          prueft Winkel statt Weite  beim RECHNEN
S-060 / S-040  standen in Spannung        beim LESEN fuer ein anderes Werkzeug
```

## Scope — vier Bestandteile

### 1 · Die drei Zustände am Facheintrag

`ABGESCHRIEBEN` · `NACHGERECHNET` · `GEGENGEPRUEFT` — Definition steht in `ARBEITSREGELN.md`.
**`ABGESCHRIEBEN` ist ausdrücklich nicht tragfähig für einen Bau.**

### 2 · Zwei Pflichtfelder, die den FALL tragen statt der Behauptung

```yaml
nachgerechnet_an:
  eingabe:   <Grundgroessen>
  erwartet:  <Zahl mit Einheit>
  gerechnet: <Datum> · <Rolle> · weicht ohne die Aussage um <Zahl> ab

gegengeprueft_an: "Quelle · Ausgabe/Jahr · Abschnitt oder Beispielnummer · dortiges Ergebnis"
```

**Der dritte Teil der Zeile ist der tragende:** *„weicht ohne die Aussage um … ab"* — **das ist die
Mutationsprobe.** Ein Fall, der auch ohne die Formel dasselbe ergibt, hat sie nicht belegt.

**Bei JA im Drei-Fragen-Test zusätzlich:** `geltungsbereich:` — Pflichtfeld, kein Freitext-Zusatz.

### 3 · Die SIEBTE Innenprüfung, neben die sechs aus A-39

```
Nennt das Blatt eine F-/N-/S-Kennung?
  -> traegt der Eintrag `nachgerechnet_an`?
       nein -> DoR nur frei, wenn das NACHRECHNEN ein Kriterium
               DIESES Blattes ist.
  -> ist eine der drei 5c-Fragen JA?
       ja   -> zusaetzlich `gegengeprueft_an` ODER `geltungsbereich`.

Der Pruefer entscheidet NICHT, ob die Aussage stimmt.
Er entscheidet, ob jemand es geprueft hat.
```

### 4 · Jede Definitionsstelle trägt eine Ampel

**Nicht als Wertung, sondern damit „unbekannt" sichtbar wird.** Ein Eintrag ohne Ampel ist **nicht
„vermutlich in Ordnung"** — dieselbe Bauform wie *„fehlt die Marke, ist der Modulstand unbekannt."*

## Die Menge, die „Auslösung ist die Benutzung" wirklich meint — gemessen 16.08.

**Das Nicht-Ziel sagt: keine Inventur, wer eine Aussage anfasst, rechnet sie. Damit ist die
Menge nicht „alle Einträge", sondern „alle benutzten".** Gemessen über die Formelspalte der
Registerzeilen mit Reifegrad `BESCHRIEBEN`:

```
F-Nummern im Register genannt          25
davon als BENUTZT markiert             22   <- drei sind ausdruecklich als NICHT
                                            benutzt gekennzeichnet (Plan-Pruefer)
davon mit `nachgerechnet_an`            0

meistgenutzt   F-001  10x    (12x mit widerlegten Zuordnungen)
               F-030 · F-004 · F-032 · F-011 · F-003  -> je neu zu erheben

URSACHE der drei nicht reproduzierbaren Messungen, gefunden 16.08.:
  das Register kennzeichnet WIDERLEGTE Zuordnungen als ~~F-001~~.
  Ein grep ueber die Formelspalte zaehlt sie MIT.
  F-001: 12 Nennungen, davon 2 durchgestrichen -> 10 echte.
```

**Damit hat A-40 eine Reihenfolge, die sich nicht aus der Nummer ergibt, sondern aus der
Reichweite:** **`F-001` zuerst — zwölf Werkzeuge hängen daran.** Ein Fehler dort trifft ein
Drittel des Kastens, einer in einer einmal genutzten Formel trifft eines.

> **⚠ Und `F-004` ist der Fall, der das Blatt ausgelöst hat.** Sie trug ein vertauschtes
> Vorzeichen, gefunden vom Generator **beim Bauen** — und sie wird von **sieben** Werkzeugen
> benutzt und trägt bis heute **keinen** Nachweis. *Der Anlass dieses Auftrags ist zugleich
> sein drittdringendster Eintrag.*

**Die Zahlen stehen hier als Messung mit Datum, NICHT in einem Kriterium** — A-40 trägt
bewusst keine Zahl, und die Menge wächst mit jeder Registerzeile.

### Die S-Seite ist etwa gleich groß — und meine erste Messung hätte das Gegenteil behauptet

```
S-Nummern definiert        32        alle 32 ohne Ampel
mit `nachgerechnet_an`      1   <- berichtigt, der Plan-Pruefer misst EINS
im Werkzeug-Register        0   <-- meine erste Messung
tatsaechlich benutzt       18   <-- P8-Gegenprobe ueber den ganzen Bestand

Reichweite   S-008 8x · S-078 7x · S-060 7x · S-040 7x · S-001 4x
```

**Hätte ich die erste Zahl gemeldet, wäre die Folgerung gewesen: *„die S-Seite ist nicht
dringend, kein Werkzeug benutzt sie."* Das ist falsch.** Die S-Nummern gehören zum Solar-/
PV-Bereich, nicht zum Hausplaner-Kasten — **das Werkzeug-Register ist für sie der falsche
Suchraum, und er sah plausibel aus, weil er für die F-Nummern der richtige war.** *(P8, fünftes
Mal heute an einer eigenen Messung.)*

> **⚠ EINE ZAHL, DIE NICHT TRÄGT, und ich nenne sie nur, um vor ihr zu warnen:** dieselbe
> Zählung über die F-Seite ergibt `F-004` **215×**, `F-032` 155×, `F-001` 146×. **Das ist
> Erwähnungshäufigkeit, keine Reichweite** — `F-004` war heute den ganzen Tag Gegenstand von
> Befunden und Berichten. **Wer damit priorisiert, priorisiert nach Gesprächsstoff.**
> **Belastbar bleibt die Registermessung: `F-001` 12 Werkzeuge, `F-004` 7.**

## Nicht-Ziele

- **Keine Inventur.** Es werden **nicht** alle Einträge nachgerechnet. **Auslösung ist die
  Benutzung** — wer eine Aussage anfasst, rechnet sie. *Ein Eintrag, den nie jemand benutzt, ist in
  seiner Richtigkeit auch nie eine Gefahr.* **Selbstskalierend.**
- **Keine sechste Rolle**, kein Fach-Prüfer, kein Amt.
- **Keine Änderung an bestehenden Fachaussagen** — nur an ihrer Kennzeichnung.
- **Kein Hausplaner-Code**, keine Migration, keine Änderung an `docs/STATUS.md`.
- **Keine Zahl in einem Kriterium.** *(Siehe unten — das ist die Lehre dieses Blattes.)*

## Kanten

| # | Fall | Verlangtes Verhalten |
|---|---|---|
| K1 | **Eine Kennung hat mehrere Definitionsstellen** *(`N-003` hat drei: Formel, Geltungsbereich, Auflage)* | **jede Stelle** trägt eine Ampel; **`nachgerechnet_an` genügt einmal je Kennung**, nicht je Zeile |
| K2 | **Die Aussage ist eine Grenze, keine Formel** *(S-078 „Was die Simulation nicht darf")* | `nachgerechnet_an` **nicht anwendbar** — Zustand `ABGESCHRIEBEN` mit Quellenangabe genügt, und das ist **kein** Mangel |
| K3 | **Die Kennung wird nur genannt, nicht definiert** *(die 17 S-Verweise in der FORMELSAMMLUNG)* | **kein** Eintrag, keine Ampel, keine Pflichtfelder — sonst zählt man Verweise als Einträge |
| K4 | **Eine Aussage ist normabhängig, aber die Norm liegt nicht vor** *(W-28)* | **gelb mit `geltungsbereich`** — benutzbar, aber nicht als Nachweis |
| K5 | **Der Fall im Feld lässt sich nicht mehr rechnen** *(Werkzeug weg, Daten weg)* | Zustand fällt auf `ABGESCHRIEBEN` zurück. **Ein Fall, den niemand nachfahren kann, ist keine Prüfung** |
| K6 | **Zwei Rollen rechnen denselben Fall und kommen auf Verschiedenes** | **beide Rechnungen bleiben stehen**, Zustand bleibt `ABGESCHRIEBEN`, und es entsteht ein Befund. **Nicht: eine Zahl gewinnt** |

## Abnahmekriterien

- **A-40-1** · **Die drei Zustände sind maschinell erkennbar.** Ein Prüfbefehl liest je Kennung
  den Zustand und meldet ihn. **Rot am Basis-SHA:** kein Eintrag trägt ein Zustandsfeld.
- **A-40-2** · **Die siebte Innenprüfung läuft und findet einen echten Fall.**
  **⚠ ZWEI BEFUNDE DES PLAN-PRÜFERS, beide zutreffend und beide am Kriterium behoben:**
  **(a) Die Positivprobe hätte heute 116 Meldungen erzeugt** — sie ist damit kein Beleg,
  sondern eine Lawine. **Verlangt ist jetzt EIN namentlich benanntes Blatt**, an dem der
  Fund einzeln gezeigt wird; die Gesamtzahl gehört in den Bericht, nicht in die Probe.
  **(b) Die Negativprobe hatte keinen einzigen Kandidaten** außer dem Auftragsblatt selbst
  — *eine Negativprobe, die nur am eigenen Blatt läuft, prüft die Prüfung nicht.*
  **Verlangt ist ein FREMDES Blatt**, das eine F-/N-/S-Kennung nennt, deren Eintrag belegt
  ist — und das deshalb **nicht** gemeldet werden darf. **Findet sich keines, ist DAS der
  Befund** und wird gemeldet, statt die Probe wegzulassen.
  **Positivprobe historisch:** gegen ein Blatt, das eine F-Kennung nennt, deren Eintrag kein
  `nachgerechnet_an` trägt → **Meldung**. **Negativprobe:** ein Blatt, dessen Kriterium das
  Nachrechnen selbst verlangt → **keine Meldung.**

- **A-40-2b** · **DIE PROBEN DECKEN BEIDE SEITEN AB — nicht nur die F-Seite.**
  **⚠ NACHGETRAGEN 18:5x.** Die Kriterien sprechen von `[FNS]`, **aber jede historische
  Positivprobe nannte einen F-Fall.** Ein Bau, der nur F-Kennungen erkennt, wäre **grün
  geworden** — dieselbe Klasse wie A-40-5 heute Abend, nur eine Ebene tiefer: **dort war der
  Suchraum zu eng, hier der Beleg.**
  **S-Positivprobe:** `S-060` · `S-040` · `S-078` — je gemessen **ohne** `nachgerechnet_an`,
  jede mit Reichweite 7 → **müssen gemeldet werden.**
  **S-Negativprobe:** `S-051` trägt `nachgerechnet_an` → **darf nicht gemeldet werden.**
  *Zwei Seiten, vier Proben — und keine davon lässt sich mit dem Wissen über die andere bestehen.*

- **A-40-3** · **Der Drei-Fragen-Test ist im Prüfschritt abgebildet.** Bei JA und fehlendem
  `gegengeprueft_an` **und** fehlendem `geltungsbereich` → Meldung.
  **Historischer Beleg:** `W-28` (dreimal JA) muss anschlagen, `F-004` (dreimal NEIN) nicht.
- **A-40-4** · **`GEGENGEPRUEFT` ohne Fundstelle wird abgewiesen.** Ein Eintrag mit dem Zustand,
  aber ohne `gegengeprueft_an`, ist ein Fund. *(Der Zustand darf nicht aus Beurteilung entstehen.)*
- **A-40-5** · **Jede Definitionsstelle trägt eine Ampel.**
  **Messbar — der Zählbefehl steht hier, der Wert nicht:**
  ```bash
  grep -nE '^#+ *\**`?[FNS]-[0-9]{3}|^\| *\**`?[FNS]-[0-9]{3}|^- *\**`?[FNS]-[0-9]{3}' \
       docs/rollenkette/werkbank/01-MATHEMATIK/*.md | grep -vE '🟢|🟡|🔴'
  ```
  **⚠ BERICHTIGT 18:2x durch Selbstprüfung gegen P8 — das Kriterium maß die HÄLFTE.**
  Vorher stand hier nur `FORMELSAMMLUNG.md`. **Gemessen: `FORMELSAMMLUNG.md` trägt 32
  Definitionsstellen, `SOLAR-REGELWERK.md` ebenfalls 32.** Das Kriterium verlangt *„jede
  Definitionsstelle trägt eine Ampel"* und hätte **die Hälfte nie angesehen** — und wäre
  trotzdem grün geworden.
  **Das ist P8 in seiner teuersten Form:** nicht ein Fehlalarm, sondern ein **falsches Grün**.
  *Der Pfad war das Kriterium, nicht die Sache — und die Sache ist „Definitionsstelle einer
  F-, N- oder S-Kennung", die naturgemäß dort steht, wo die Kennung definiert wird.*
  **Der Suchraum ist jetzt das Verzeichnis, nicht die Datei** — und wächst mit, wenn eine
  dritte Sammlung dazukommt.
  **Nach dem Lauf: keine Fundstelle. Ein zweiter Lauf meldet dasselbe.**
  **⚠ DREI BEFUNDE DES PLAN-PRÜFERS, alle nachgemessen und alle zutreffend:**
  ```
  Der Befehl zaehlt ZEILEN, nicht Kennungen:   87 Zeilen  ->  64 Kennungen
  Er meldet auch den EINZIGEN nachgerechneten Eintrag als ampellos
  Der erweiterte Suchraum (*.md statt einer Datei) traegt ein ANDERES Merkmal
  ```
  **Behoben durch drei Auflagen am Kriterium, nicht durch Abschwächung:**
  **(a)** gezählt wird **je Kennung**, nicht je Zeile — *eine Kennung mit drei
  Definitionsstellen ist EIN Eintrag ohne Ampel, nicht drei* **(K1 sagt das bereits,
  der Zählbefehl widersprach ihm).**
  **(b)** ein Eintrag mit `nachgerechnet_an` **oder** `gegengeprueft_an` wird **nicht**
  gemeldet, auch wenn die Ampel fehlt — *er ist belegt, nur nicht gekennzeichnet;
  das ist ein anderer Mangel und gehört getrennt gezählt.*
  **(c)** die beiden Sammlungen werden **getrennt ausgewiesen** — F-/N-Seite und S-Seite
  tragen verschiedene Merkmale, und eine Summe über beide verdeckt, welche Seite fehlt.


- **A-40-6** · **`nachgerechnet_an` trägt die Abweichung, nicht nur das Ergebnis.**
  **⚠ BEFUND: die Rot-Lage ist seit 14:49 weg — und zwar durch mich selbst.** Ich habe an
  `S-008` ein `nachgerechnet_an` eingetragen, das die Abweichung bereits nennt. **Damit war
  das Kriterium erfüllt, bevor der Auftrag gebaut ist — §5 verbietet genau das.**
  **Neue Rot-Lage, am Bestand erhoben statt behauptet:** von den Einträgen mit
  `nachgerechnet_an` trägt **genau einer** die Abweichungszeile; alle übrigen Kennungen
  tragen **gar keins**. **Der Lauf misst beide Zahlen und nennt sie getrennt** — *ein
  Kriterium, dessen Rot-Lage der Schreibende selbst wegräumt, misst danach nur noch sich
  selbst.*
  Das Feld nennt, **um wie viel der Fall ohne die Aussage abweicht** — sonst ist es kein Beleg,
  sondern eine Wiederholung. *(Mutationsprobe.)*
- **A-40-7** · **Alle sechs Kanten K1–K6 sind behandelt und je einzeln belegt.**
  **⚠ BEFUND DES PLAN-PRÜFERS, zutreffend:** **K4 stützt sich auf `W-28`, ein Werkzeug, das
  beim Schnitt `LEER` war** *(inzwischen abgelesen, siehe Registerzeile)* — die Kante hatte
  also keinen Fall. **K5 und K6 nennen gar keinen konkreten Fall**, sie beschreiben nur die
  Regel. **Auflage:** jede der sechs Kanten wird mit einem **benannten Fall** belegt, nicht
  mit einer Wiederholung ihrer Formulierung. *Eine Kante ohne Fall ist eine Absichtserklärung.*
- **A-40-8** · **Kein Nicht-Ziel berührt.** Keine Datei unter `resources/`, `app/`, keine
  Änderung an `docs/STATUS.md`, **keine bestehende Fachaussage inhaltlich geändert.**
- **A-40-9** · **Suite grün und Zahl unverändert gegen den Bau-Stand**, `tsc exit=0`.
  **⚠ ZWEI BEFUNDE DES PLAN-PRÜFERS, beide zutreffend:** *(a)* es ist das einzige der fünf
  Suite-Kriterien **ohne die Invarianten-Klausel** — es entstand zwischen zwei Blättern,
  die sie tragen. *(b)* **Es kann nicht scheitern**: A-40 ist das einzige der sechs Blätter
  mit **null Code-Pfaden**, und eine Suite-Zusage über einen Auftrag ohne Code ist leer.
  **Beides behoben durch die Klausel:** *die Zahl wird unmittelbar vor dem Bau erhoben; ändert
  sich die Suite durch einen fremden Commit dazwischen, gilt die INVARIANTE — kein Test, den
  dieser Auftrag berührt, ist rot — und nicht die Gleichheit der Zahl.* **Damit ist es
  scheiterfähig: es scheitert, wenn ein Test rot ist, den A-40 anfasst.**

## Rückweg und Entdeckung

- **Rückweg:** Felder und ein Prüfschritt. **Rücknahme = Commit zurückdrehen.** Die Fachaussagen
  selbst bleiben unberührt — **ein Rückbau verliert Kennzeichnung, kein Wissen.**
- **Entdeckung:** A-40-2 und A-40-3 sind historische Positivproben; meldet der Schritt nie etwas,
  fangen sie es.
- **Der Fall, der beim Bauen übersehen wird:** K3. **Wer Verweise als Einträge zählt, verlangt
  Pflichtfelder von siebzehn Stellen, die gar keine Definition sind** — genau die Verwechslung, die
  in diesem Blatt zwei Messungen auseinandergehen ließ.

## Warum in diesem Blatt keine Zahl steht

**Dieselbe Formelsammlung, drei Zählungen, drei Ergebnisse:**

```
Vorkommen einer Kennung            48    Yama und ich, gleich
Definitionsstellen                 30 / 32   Yama / ich
  Ursache: N-003 hat DREI Stellen  — Kennungen 3, Zeilen 5
Definitionsstellen mit Ampel       13 / 9    Yama / ich
```

**Keine dieser Zahlen ist falsch. Sie beantworten verschiedene Fragen.** Und eine davon war
zusätzlich eine **Summe ohne Erhebung** — Yama hat es selbst offengelegt: *„48 minus 35, aus zwei
verschiedenen Grundgesamtheiten. Das ist B6, und ich habe sie gebrochen."*

**Deshalb nennt A-40-5 den Befehl und nicht den Wert, und deshalb steht in keinem Kriterium dieses
Blattes eine Zahl.** *Eine Zahl misst den Bestand zum Zeitpunkt des Schnitts; ein Befehl misst ihn
beim Lesen.*
