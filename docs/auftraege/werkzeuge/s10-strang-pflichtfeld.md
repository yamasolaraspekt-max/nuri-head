# S-10 · Strukturregel „Strang" im Auftragsvalidator

```yaml
auftrag:
  id: AUF-S10-STRANG
  strang: werkzeuge
  status: entwurf                   # B8: bleibt entwurf bis gegengelesen
  spur: B
  heimat: ticket
  gegengelesen_von:                 # Werkzeug-Blatt -> Evaluator (BESCHLUSS B8)
  gegengelesen_am:
  befund:
  ziel: "scripts/auftrag-pruefen.mjs meldet jedes Auftragsblatt, dessen yaml-Kopf kein gueltiges
         Feld `strang` traegt, als Strukturbefund S-10."
  nicht_ziel: "Keine Aenderung an S-01 bis S-09. Keine Aenderung an Mess- oder Erwartungsbloecken.
               Kein Umschreiben bestehender Auftragsblaetter — das ist ein eigener Posten."

scope:
  population_command: "grep -rLn 'strang:' docs/auftraege/produktdaten docs/auftraege/werkzeuge docs/auftraege/hausplaner-3d | wc -l"
  pfade:
    - scripts/auftrag-pruefen.mjs
    # Ablage: docs/auftraege/<strang>/ — der Ordner ist die Dispatch-Grenze,
    # das Feld die inhaltliche Zusage. K-06 prueft, dass beide uebereinstimmen.
  ausschluesse: []

kriterien:
  # Die drei Fixtures legt der Generator an. Pfade stehen hier ausgeschrieben —
  # Platzhalter in spitzen Klammern liest die Denylist als Umleitung und ueberspringt
  # den Befehl (gemessen 02.08.: K-01/K-02 "enthaelt umleitung").
  - id: K-01
    aussage: "Ein Kopf ohne `strang` erzeugt genau einen S-10-Befund."
    typ: presence
    kritikalitaet: P1
    pruefung:
      befehl: "node scripts/auftrag-pruefen.mjs tests/fixtures/auftraege/ohne-strang.md"
      erwartet: "Ausgabe enthaelt 'S-10', exit != 0"
    beleg: rohausgabe

  - id: K-02
    aussage: "Ein Kopf mit unbekanntem Strangwert erzeugt ebenfalls S-10."
    typ: adversarial
    kritikalitaet: P1
    pruefung:
      befehl: "node scripts/auftrag-pruefen.mjs tests/fixtures/auftraege/strang-unbekannt.md"
      erwartet: "Ausgabe enthaelt 'S-10', exit != 0"
    beleg: rohausgabe

  - id: K-03
    aussage: "Ein Kopf mit gueltigem Strang erzeugt KEINEN S-10-Befund."
    typ: absence
    kritikalitaet: P1
    pruefung:
      befehl: "node scripts/auftrag-pruefen.mjs tests/fixtures/auftraege/strang-gueltig.md"
      erwartet: "Ausgabe enthaelt kein 'S-10'"
    beleg: rohausgabe

  # Der Validator nimmt DATEIEN, kein Verzeichnis - ein Verzeichnisargument stirbt mit
  # EISDIR (readFileSync auf ein Verzeichnis, :619). Gemessen 02.08.; die erste Fassung
  # dieses Blattes hatte genau diesen Fehler in K-04 und K-05, und der Validator hat ihn
  # selbst gefangen, sobald er den Kopf lesen konnte.
  - id: K-04
    aussage: "Die Regel greift ueber alle Blaetter eines Laufs, nicht nur beim Einzelblatt."
    typ: coverage
    kritikalitaet: P1
    pruefung:
      typ: manual
      schritte: "auftrag-pruefen.sh ueber alle Blaetter fahren, S-10-Zeilen zaehlen"
      erwartet: "Zahl == Ausgabe des population_command"
    beleg: zaehlausgabe
    ausgefuehrt_von: generator      # Pipe = Umleitung, die Allowlist fuehrt das nicht aus

  - id: K-05
    aussage: "Die uebrigen Strukturregeln verhalten sich unveraendert."
    typ: behavioural
    kritikalitaet: P1
    pruefung:
      typ: manual
      schritte: "denselben Lauf vor und nach der Aenderung, S-01..S-09 zaehlen"
      erwartet: "identische Zaehlung (Rohausgabe beider Laeufe vorlegen)"
    beleg: diff-der-beiden-laeufe
    ausgefuehrt_von: generator      # Pipe = Umleitung, die Allowlist fuehrt das nicht aus

  - id: K-06
    aussage: "Der Ordner eines Blattes stimmt mit seinem Feld `strang` ueberein."
    typ: adversarial
    kritikalitaet: P1
    pruefung:
      typ: manual
      schritte: "Blatt mit strang: produktdaten nach docs/auftraege/werkzeuge/ legen"
      erwartet: "S-10 meldet die Abweichung Ordner<->Feld"
    beleg: rohausgabe
    ausgefuehrt_von: generator

selbstnachweis:
  preflight: "./scripts/auftrag-pruefen.sh docs/auftraege/werkzeuge/s10-strang-pflichtfeld.md"
  gegenprobe: "strang-Zeile aus einem gueltigen Blatt entfernen — S-10 muss erscheinen; wieder
               einsetzen — S-10 muss verschwinden. Beide Rohausgaben vorlegen."
```

---

## Warum dieser Auftrag

`docs/auftraege/AUFTRAGSSCHEMA.md` §1a macht `strang` seit dem 02.08.2026 zum Pflichtfeld.
**Ohne S-10 ist das eine Regel, die gelesen wird — und das Schema selbst sagt in Abschnitt 0,
warum das nicht genügt:**

> Ich habe die Lehre zu Punkt 4 aufgeschrieben und drei Stunden später denselben Fehler gemacht.
> Das ist ein Beleg gegen das Medium, nicht gegen den Willen. **Deshalb dieses Schema: es wird
> geprüft, nicht gelesen.**

Dieselbe Begründung gilt eine Ebene tiefer für das neue Feld.

## Die erlaubten Werte

Abschließend. Ein neuer Wert wird von Yama vergeben, nicht erfunden:

| Wert | Gegenstand |
|---|---|
| `hausplaner-3d` | Planer, Studio, Geometrie, Szene, Werkzeugleiste, Dashboard-Flächen |
| `produktdaten` | Artikelidentität, Preise, IDS, Open Masterdata, DATANORM, Lieferanten |
| `werkzeuge` | Validatoren, `commit-pruefen`, `auftrag-pruefen`, Gates, Messskripte |

Der Wert **muss** aus dieser Liste stammen. Ein Tippfehler (`produktdate`) ist derselbe Fehler wie
ein fehlendes Feld und muss ebenso rot werden — deshalb Kriterium K-02.

## Kantenliste

1. Blatt **ohne** yaml-Kopf → bestehende Regel greift (`aktivOhneKopf`), **kein** zusätzliches
   S-10. Nicht doppelt melden.
2. `strang` steht als Fließtext irgendwo im Blatt, aber nicht im Kopf → gilt als **fehlend**.
   Das ist die Lehre aus PB-019, wo `status:` als reiner Text über die ganze Datei gesucht wurde.
3. `strang` mit anderer Groß-/Kleinschreibung (`Produktdaten`) → **rot**. Werte sind
   kleingeschrieben; hier zu tolerieren hieße raten.
4. Mehrere yaml-Blöcke in einer Datei → jeder mit eigenem Kopf braucht `strang`.
5. `AUFTRAGSSCHEMA.md` selbst → der Beispielblock steht bewusst als ```text, nicht ```yaml, und
   darf **nicht** als Auftrag gezählt werden. Diese Ausnahme besteht bereits; S-10 darf sie nicht
   aushebeln.
6. Die **131 Bestandsblätter** tragen noch kein `strang`. Nach K-04 werden sie alle rot. **Das ist
   gewollt und kein Fehlschlag** — die Nachrüstung ist ein eigener, danach zu schneidender Posten.
   Der Validator soll den Rückstand sichtbar machen, nicht verschweigen.

## Rückweg und Entdeckung

**Rückweg:** Eine neue Prüffunktion in einer Datei; Commit zurückdrehbar, kein Schema, keine Daten.

**Entdeckung:**
```bash
grep -rLn 'strang:' docs/auftraege/*/*.md | wc -l   # muss ueber die Zeit fallen
```
Steigt die Zahl, entstehen weiter Blätter ohne Strang — dann greift die Regel nicht dort, wo
geschrieben wird.

## Rollen

**Strang:** `werkzeuge`. **Generator:** die Instanz dieses Strangs.
**Evaluator:** frische Instanz, Rot-Probe aus dem Selbstnachweis ist Pflichtbeleg.
**Nicht** der Planner des Strangs `produktdaten`, der diesen Auftrag geschrieben hat.
