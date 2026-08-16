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
               in die A-40 nur noch einen sechsten Punkt einhaengt."
regelgrundlage: "ARBEITSREGELN.md, Nachtrag vom 16.08. — die drei Zustaende, die zwei
                 Pflichtfelder und der Drei-Fragen-Test sind ENTSCHIEDEN und stehen dort.
                 Dieses Blatt BAUT sie, es erfindet sie nicht."
```

## Warum keine Prüfstation das leisten kann

**Alle fünf Rollen beziehen ihr Fachwissen aus derselben Quelle.** Ein zweiter Leser desselben
Dokuments ist keine zweite Meinung — **er ist dieselbe Meinung zweimal.** Ein „Fach-Prüfer" hätte
F-004 aufgeschlagen und dasselbe falsche Vorzeichen gelesen wie alle vor ihm.

**Belegt: alle drei Fachfehler dieser Woche wurden vom Benutzer gefunden.**

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

### 3 · Die sechste Innenprüfung, neben die fünf aus A-39

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
- **A-40-2** · **Die sechste Innenprüfung läuft und findet einen echten Fall.**
  **Positivprobe historisch:** gegen ein Blatt, das eine F-Kennung nennt, deren Eintrag kein
  `nachgerechnet_an` trägt → **Meldung**. **Negativprobe:** ein Blatt, dessen Kriterium das
  Nachrechnen selbst verlangt → **keine Meldung.**
- **A-40-3** · **Der Drei-Fragen-Test ist im Prüfschritt abgebildet.** Bei JA und fehlendem
  `gegengeprueft_an` **und** fehlendem `geltungsbereich` → Meldung.
  **Historischer Beleg:** `W-28` (dreimal JA) muss anschlagen, `F-004` (dreimal NEIN) nicht.
- **A-40-4** · **`GEGENGEPRUEFT` ohne Fundstelle wird abgewiesen.** Ein Eintrag mit dem Zustand,
  aber ohne `gegengeprueft_an`, ist ein Fund. *(Der Zustand darf nicht aus Beurteilung entstehen.)*
- **A-40-5** · **Jede Definitionsstelle trägt eine Ampel.**
  **Messbar — der Zählbefehl steht hier, der Wert nicht:**
  ```bash
  grep -nE '^#+ *\**`?[FNS]-[0-9]{3}|^\| *\**`?[FNS]-[0-9]{3}|^- *\**`?[FNS]-[0-9]{3}' \
       docs/rollenkette/werkbank/01-MATHEMATIK/FORMELSAMMLUNG.md | grep -vE '🟢|🟡|🔴'
  ```
  **Nach dem Lauf: keine Fundstelle. Ein zweiter Lauf meldet dasselbe.**
- **A-40-6** · **`nachgerechnet_an` trägt die Abweichung, nicht nur das Ergebnis.**
  Das Feld nennt, **um wie viel der Fall ohne die Aussage abweicht** — sonst ist es kein Beleg,
  sondern eine Wiederholung. *(Mutationsprobe.)*
- **A-40-7** · **Alle sechs Kanten K1–K6 sind behandelt und je einzeln belegt.**
- **A-40-8** · **Kein Nicht-Ziel berührt.** Keine Datei unter `resources/`, `app/`, keine
  Änderung an `docs/STATUS.md`, **keine bestehende Fachaussage inhaltlich geändert.**
- **A-40-9** · **Suite grün und Zahl unverändert gegen den Bau-Stand**, `tsc exit=0`.

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
