# A-42 — Die Befundnotizen ziehen um, bevor die Tafel erzeugt wird

```yaml
auftrag: "A-42"
werkzeug: "— (Werkzeug der Rollenkette, kein Hausplaner-Werkzeug)"
art: "UMZUG — Bloecke ohne zustand-Feld wandern aus docs/STATUS.md in eine eigene Datei.
      KEIN Loeschen, KEIN Hausplaner-Code, KEINE Migration."
spur: A
heimat_app: ticket
dor_beleg: "steht aus — plan-pruefer."
dor_schnitt_sha: "e802c1f8"
status_steht_in: docs/STATUS.md
basis_sha: e802c1f8
prioritaet: P1
ballbesitz: "plan-pruefer (DoR)"
claim: "planner 16.08. — Claim VOR dem Schnitt."
kennung_geprueft: "NAMENTLICH ueber alle sechs Baeume geprueft, nicht ueber ein Namensmuster
                   (siehe ARBEITSREGELN, tote Verzeichnisfalle): null Treffer in jedem der
                   sechs docs/STATUS.md, null Blaetter in jedem Zweig. Frei."
gebaut_in: "ticket-rolle-generator (rolle/generator)"
staut_hinter: "NICHTS. Muss VOR dem ersten schreibenden --tafel-Lauf fertig sein."
regelgrundlage: "Auflage 2 der Integrationslauf-Freigabe vom 16.08. (a774e549, d91f1dca).
                 Reihenfolge: 1 Integrationslauf, 2 DIESER Auftrag, 3 erster Schreiblauf."
anlass: "Der erste --tafel-Schreiblauf wuerde diese Bloecke lautlos aus dem lebenden
         Dokument entfernen — sie haben keine Kennung mit Zustand und kommen in einer
         erzeugten Tafel nicht vor."
```

## Warum das vor dem Schreiblauf fertig sein muss

**Gemessen an `status-erzeugen.sh` (Zeile 45, 291):** `--tafel` erzeugt die Statuswahrheit **aus
dem Commit-Log**, *„je Kennung gewinnt der jüngste Eintrag"*. **Ein Block ohne Kennung und ohne
Zustand kommt darin nicht vor.**

> **Wer die erzeugte Tafel schreibt, bevor diese Blöcke umgezogen sind, entfernt sie aus dem
> lebenden Dokument — und niemand merkt es, weil sie in keiner Tafelzeile stehen.** Sie wären
> nur noch in der Git-Historie, also dort, wo niemand sie sucht.

**Sie sind kein Müll.** Es sind Befund-, Antwort- und Berichtigungsnotizen anderer Rollen mit
erkennbarem Sachbezug. **Ihr Inhalt ist gut, ihr Ort ist falsch.** *Dieselbe Klasse wie die
sechzehn Commits ohne Auftragskennung, die der Integrator geöffnet statt gezählt hat
(`a7b2ea65`): „wer den Lauf nach Aufträgen sortiert, verliert diese sechzehn lautlos."*

**Und die Menge wächst.** Der Zählbefehl steht in A-42-1 — **keine feste Zahl in einem Kriterium**
*(P6: eine Rot-Lage mit Uhr ist keine)*. Zur Größenordnung, ausdrücklich **nicht** als Zusage:
16.08. mittags rund ein Viertel aller Blöcke, gut eine Stunde später rund ein Drittel.

## Scope

**Jeder yaml-Block in `docs/STATUS.md`, der ein Feld `auftrag:` trägt, aber kein `zustand:` mit
einem Großbuchstabenwert, wandert nach `docs/BEFUNDNOTIZEN.md`** — **unverändert im Wortlaut**,
mit einer vorangestellten Zeile `herkunft: docs/STATUS.md · Block <n> · <basis-sha>`.

**Zielort ist entschieden und Teil dieses Auftrags:** eine **eigene Datei neben** der
Statuswahrheit. **Nicht** zurück in die Auftragsblätter — dort wären sie über hundertmal
verstreut und die Herkunftskette ginge verloren.

## Nicht-Ziele

- **KEIN Löschen.** Kein Block verschwindet; jeder steht danach vollständig in der Zieldatei.
  *(Yamas Rückfall-Regel: kein Löschen ohne Freigabe — sie ist hier nicht erteilt und wird
  nicht gebraucht.)*
- **Keine inhaltliche Änderung** an einer Notiz. Nicht kürzen, nicht zusammenfassen, nicht
  umformulieren.
- **Kein Block MIT `zustand:` wird angefasst** — die echten Aufträge bleiben, wo sie sind.
- **Kein Hausplaner-Code**, nichts unter `resources/`, `app/`, `database/`, `routes/`.
- **Kein Eingriff in `status-erzeugen.sh`** — dort arbeitet A-41.

## Kanten

| # | Fall | Verlangtes Verhalten |
|---|---|---|
| K1 | **Ein Block trägt `zustand:` in Kleinschreibung oder als Prosa** | **nicht** umziehen — Grenzfall wird **gemeldet**, nicht entschieden. *Ein Zweifelsfall bleibt, wo er ist* |
| K2 | **Zwei Notizen sind wortgleich** | **beide** ziehen um. **Kein Entdoppeln** — Doppelung ist ein Befund für später, kein Anlass zum Wegwerfen |
| K3 | **Eine Notiz nennt eine Kennung, die es nie gab** | zieht **trotzdem** um, mit dem Vermerk `kennung_unbekannt: true`. *Sie ist kein Auftrag, aber sie ist ein Beleg* |
| K4 | **Ein Block ist kaputtes yaml** *(es gibt 24 solcher Altlasten)* | **nicht** umziehen, **einzeln melden** mit Zeilennummer. **Ein kaputter Block wird beim Umzug nicht repariert** — das wäre Reparatur unter Bewegung |
| K5 | **Während des Umzugs kommen neue Notizen dazu** | Der Lauf misst **einmal** und nennt seinen Stand-SHA. Was danach entsteht, ist **nicht** sein Rückstand, sondern der nächste Lauf |
| K6 | **`docs/BEFUNDNOTIZEN.md` existiert bereits** | anhängen, **nicht** überschreiben — und die vorhandenen Einträge zählen und nennen |

## Abnahmekriterien

- **A-42-1** · **Vorher und nachher gezählt, mit demselben Befehl, im Bericht.**
  ```bash
  python3 - <<'PY'
  import io,re
  bl=re.findall(r"```yaml(.*?)```", io.open("docs/STATUS.md",encoding="utf-8").read(), re.S)
  o=[b for b in bl if re.search(r"^auftrag:",b,re.M) and not re.search(r"^zustand: *[A-Z]",b,re.M)]
  print(len(bl), len(o))
  PY
  ```
  **Nachher muss die zweite Zahl `0` sein** *(bis auf gemeldete K1- und K4-Fälle, je einzeln
  aufgeführt)*. **Rot am Basis-SHA:** die zweite Zahl ist deutlich größer als null — **der Wert
  wird beim Lauf erhoben, nicht hier festgeschrieben.**
- **A-42-2** · **Die Summe stimmt.** `Blöcke vorher = Blöcke nachher in STATUS.md + Einträge in
  BEFUNDNOTIZEN.md + gemeldete K1/K4-Fälle`. **Ohne diese Gleichung ist ein Verlust nicht
  ausgeschlossen.**
- **A-42-3** · **Kein Block hat sich inhaltlich verändert.** Für **jeden** umgezogenen Block ist
  der Text byte-identisch zum Ausgangsstand — **Prüfung über Hash je Block, nicht über
  Augenschein.**
- **A-42-4** · **Jeder Eintrag trägt seine Herkunft** (`herkunft:` mit Blocknummer und
  Basis-SHA). *Ohne Herkunft ist eine umgezogene Notiz ein Fundstück ohne Fundort.*
- **A-42-5** · **Alle sechs Kanten K1–K6 sind behandelt und je einzeln belegt.**
- **A-42-6** · **Die Blöcke MIT `zustand:` sind unberührt** — Anzahl und Inhalt vorher/nachher
  gleich, über Hash belegt.
- **A-42-7** · **Kein Nicht-Ziel berührt.** `git show --stat` nennt keine Datei unter
  `resources/`, `app/`, `database/`, `routes/`, und **nicht** `scripts/status-erzeugen.sh`.
- **A-42-8** · **Der Weg ist gangbar** *(P7)*: **WER** — der Generator, in seinem Baum ·
  **DARF er** — ja, es ist `docs/`, kein Produktivcode, kein Löschen · **EXISTIERT die
  Eigenschaft** — ja, die Blockstruktur ist maschinell erfassbar, der Zählbefehl steht in A-42-1.
- **A-42-10** · **Der Suchraum ist die Sache, nicht der Ort** *(P8)* — **gemessen, nicht
  angenommen.** Die Sache ist *„yaml-Block mit `auftrag:` und ohne `zustand:`"*; dass er nur in
  `docs/STATUS.md` vorkommt, ist eine **Messung**:
  ```
  docs/STATUS.md          77
  docs/handoff-status.md   0
  docs/STAND.md            0
  ```
  **Der Lauf misst diese drei erneut** und meldet jeden Treffer außerhalb von `docs/STATUS.md`
  als Fund, statt ihn stillschweigend auszulassen. *(Die 77 sind der Stand des Planner-Baums;
  im Bestand waren es zur selben Zeit 129 — **deshalb nennt A-42-1 den Befehl und keine Zahl.**)*
  **Ohne dieses Kriterium wäre der Pfad ein geratener Suchraum** — genau der Fehler, den P8
  heute an vier Fällen gefangen hat, drei davon meine.
- **A-42-9** · **Suite grün und Zahl unverändert gegen den Bau-Stand**, `tsc exit=0`.
  Zahl **unmittelbar vor dem Bau** erheben.

## Rückweg und Entdeckung

- **Rückweg:** ein Verschiebevorgang in `docs/`. **Rücknahme = Commit zurückdrehen.** Da nichts
  gelöscht wird, ist der Ausgangsstand vollständig im selben Commit enthalten.
- **Entdeckung:** **A-42-2**, die Summengleichung. Sie schlägt an, sobald ein Block auf dem Weg
  verschwindet — **die einzige Prüfung, die einen stillen Verlust findet.**
- **Der Fall, der beim Bauen am ehesten übersehen wird:** **K4.** Vierundzwanzig Blöcke sind
  kaputtes yaml. **Wer sie beim Umzug „nebenbei richtet", repariert unter Bewegung** — und ein
  Reparaturversuch hat in diesem Haus schon einmal mehr gekostet als der Fehler.
