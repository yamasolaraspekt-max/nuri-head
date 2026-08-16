# W-17/1 — Export und Speichern ablesen: die Insel schreibt nicht, der Server tut es

```yaml
auftrag: "W-17/1"
werkzeug: "W-17 Export und Speichern"
art: "STUFE B — Blatt schneiden, Ziel BESCHRIEBEN (Ablesung). Die Einordnung ist GEMESSEN,
      nicht behauptet: Reifegrad LEER, sieben Blaetter sind reine Vorlagen (249 Z. gesamt),
      und Produktivcode ist reichlich vorhanden. KEIN Bau, KEIN Produktivcode."
spur: A
heimat_app: ticket
dor_beleg: "steht aus — plan-pruefer."
dor_schnitt_sha: "8faca79c"
status_steht_in: docs/STATUS.md
basis_sha: 8faca79c
basis_sha_lage: "⚠ GEMESSEN 16.08.: 8faca79c liegt NICHT auf rolle/planner. Der Plan-Pruefer
  (488186fc) hat den Bruch belegt — zwei Planner-Commits von 14:39/14:40 sind entstanden, ohne
  je auf den Planner-Zweig zu zeigen; der Reflog springt von 14:39:40 auf e913717a direkt zu
  14:49:28 auf 66fa277f. Der Inhalt ist NICHT verloren: cef05ad3 war ein Rueckfluss-Transport
  und seine 166+579 Zeilen stehen im gemeinsamen Baum. Verloren ist die ERREICHBARKEIT vom
  Rollenzweig aus. Wer die Rot-Lagen dieses Blattes nachmisst, muss ueber --all oder den
  Integrations-Checkout gehen; ein Auscheck aus rolle/planner allein findet den Stand nicht.
  Das ist genau der Fall, den A-37 verhindern soll, und er ist an MEINEM Blatt eingetreten."
prioritaet: P2
ballbesitz: "plan-pruefer (DoR)"
claim: "planner 16.08. — Claim VOR dem Schnitt."
kennung_geprueft: "Vergeben ist nur W-17 selbst; W-17/1 hat NULL Treffer in docs/STATUS.md
                   und NULL Blaetter in docs/auftraege/aktiv/. Frei."
anlass: "Yamas Auftrag: vier Blaetter nach dem Muster der sechs B-Ablesungen. GEMESSEN sind
         es NICHT vier — die Begruendung steht unten in Abschnitt 1."
```

## 1 — Warum EIN Blatt und nicht vier: die Auswahl ist gemessen, nicht geraten

**Yamas Auftrag lautete auf vier Blätter, und ich habe ihm W-06, W-07, W-20, W-23 vorgeschlagen.
Die Messung widerlegt meinen eigenen Vorschlag:**

```
Reifegrad im REGISTER          Werkbank-Blaetter        Folge
W-06  BESCHRIEBEN              alle sieben gefuellt     Ziel schon erreicht
W-07  BESCHRIEBEN              alle sieben gefuellt     Ziel schon erreicht
W-20  BESCHRIEBEN              alle sieben gefuellt     Ziel schon erreicht
W-23  BESCHRIEBEN              alle sieben gefuellt     Ziel schon erreicht

W-18 (Muster) VOR seinem Auftrag:  LEER
```

**Die sechs B-Ablesungen brachten Werkzeuge von `LEER` auf `BESCHRIEBEN`.** Bei den vier
vorgeschlagenen wäre das Ziel **bereits erfüllt** — und §5 verbietet genau das. **Mein Vorschlag
war ein Widerspruch in sich:** *„die BESCHRIEBENEN, die noch nie abgelesen wurden"* — `BESCHRIEBEN`
**heißt**, dass sie abgelesen wurden.

**Von den `LEER`-Werkzeugen bleibt genau eines übrig:**

| | Lage | ablesbar? |
|---|---|---|
| **W-17** | Verzeichnis steht, sieben Blätter sind Vorlagen (249 Z.), **Code reichlich** | **JA** |
| W-19 Sonne und Verschattung | Verzeichnis steht, Blätter Vorlagen — **aber: Zeitgleichung/WOZ 0 Treffer, Schattenwurf 1, die 37 „Treffer" sind je EINER pro Datei, also Erwähnungen. PVGIS liegt im CRM, nicht in der Insel** | **nein — Bau, keine Ablesung** |
| W-24/25/26/28/29/30/32 | **kein Werkbank-Verzeichnis** | nein — Neuentwürfe |
| W-43 | — | von Yama ausgeschlossen |

> **Ein Blatt, das ein bereits erfülltes Ziel verlangt, ist schlechter als kein Blatt.** Deshalb
> eines statt vier, und die Abweichung steht hier statt in einer Fußnote.

## 2 — Die Einordnung: ABLESUNG, und der Code liegt an zwei Orten

**Gemessen am Basis-SHA:**

```
Insel   arbeitsbereichSpeicher.ts · schienenSpeicher.ts · paketSpeichern.ts
        speicherAnzeige.ts · __tests__/schienenSpeicher.test.ts
        37 Dateien mit exportieren/downloadJson/speichern

Server  app/Domain/Hausplaner/Actions/SpeichereHausplanerDokument.php
        app/Domain/Hausplaner/Actions/StelleSnapshotWieder.php
        app/Domain/Hausplaner/Actions/ErstelleLeeresSzenenDokument.php
        app/Domain/Hausplaner/Actions/ErmittleUebernahmeStatus.php
```

**Der tragende Punkt, und er ist derselbe wie bei W-16/1:** **Die Insel speichert nicht selbst.**
Sie bereitet vor; **geschrieben wird auf dem Server.** Wer nur `resources/planner/` liest, sucht das
Speichern in der Insel und findet es nicht. *(Der Generator hat genau diesen Befund bei W-16/1
gemacht — „die Insel hat drei Module, aber die Speicherung liegt auf dem SERVER".)*

## 3 — Scope

**Die sieben Werkbank-Blätter von `W-17-export-und-persistenz/` werden gefüllt:**
`1-ZWECK` · `2-FUNKTION` · `3-FORMELN` · `4-BEDIENUNG` · `5-CODE` · `6-PRUEFUNG` · `7-GRENZEN`.

**Beide Hälften gehören hinein** — Insel **und** Server. Ein Blatt, das nur die Insel beschreibt,
beschreibt die Hälfte.

## Nicht-Ziele

- **KEIN Produktivcode.** Weder `resources/` noch `app/` werden geändert. **Ablesung heißt lesen.**
- **KEINE Änderung an `docs/STATUS.md`** außer Tafelzeile und Datensatz dieses Auftrags (A-20).
- **Keine Bewertung**, ob die Speicherung gut gebaut ist — das ist ein eigener Vorgang.
- **Kein zweites Werkzeug.** W-19 ist ausdrücklich **nicht** Teil dieses Auftrags.

## Kanten

| # | Fall | Verlangtes Verhalten |
|---|---|---|
| K1 | **Der Code liegt an zwei Orten** (Insel + Server) | **beide** in `5-CODE`, je mit Pfad und Zeilenzahl. Nicht „die Insel macht es" |
| K2 | **Ein Test prüft Dateisignaturen und Rechte** (`tests/Feature/PlanUploadTest.php`, 255 Z.) | gehört in `6-PRUEFUNG` — **wer die Wächterfrage nur in `resources/planner` stellt, findet die Hälfte nicht** |
| K3 | **Eine Formel taucht auf** (Maßstab, Koordinaten) | **F-Nummer nennen, nicht abschreiben.** Fehlt eine, wird sie als **Lücke gemeldet**, nicht erfunden |
| K4 | **Der Snapshot-Weg** (`StelleSnapshotWieder`) ist ein eigener Pfad | eigener Abschnitt in `2-FUNKTION`, nicht unter „Speichern" subsumiert |
| K5 | **Ein Blatt bliebe leer**, weil es zum Werkzeug nichts gibt | **ausdrücklich schreiben, dass es nichts gibt** — ein leeres Blatt ist eine Aussage, eine fehlende Zeile nicht |
| K6 | **Die Registerzeile trägt heute `LEER`** | wird nachgezogen — siehe `W-17-1-4` |

## Abnahmekriterien

- **W-17-1-1** · **Alle sieben Blätter sind gefüllt.** **Messbar:** jedes trägt mehr als die
  Vorlage. **Rot am Basis-SHA:** die sieben zusammen haben **249 Zeilen** — das ist die reine
  Vorlage, gemessen.
- **W-17-1-2** · **Beide Hälften stehen in `5-CODE`** — Insel **und** Server, je mit Pfad und
  Zeilenzahl. **Rot:** `5-CODE` nennt heute **keine** Datei.
- **W-17-1-3** · **`6-PRUEFUNG` nennt `tests/Feature/PlanUploadTest.php`** mit seiner Zahl.
  **Rot:** kommt heute nicht vor. *(K2 — der größte Wächter liegt außerhalb der Insel.)*
- **W-17-1-4** · **DIE REGISTERZEILE WIRD NACHGEZOGEN.** `REGISTER.md` trägt für W-17 danach
  **`BESCHRIEBEN`** statt `LEER`, in **demselben Commit** wie die Blätter.
  **Rot am Basis-SHA:** die Zeile trägt `LEER`.
  **⚠ Dieses Kriterium existiert wegen W-37:** dort stand *„Die REGISTERZEILE wird nachgezogen"*
  im Kriterium, der Bau hat den Befund **gesehen, richtig gemessen und bewusst nicht ausgeführt** —
  `NACHBESSERN`, eine ganze Runde. **Die Handlung fehlt nicht aus Nachlässigkeit, sondern weil sie
  am Ende steht und das Blatt vorher fertig aussieht.**
- **W-17-1-5** · **KEIN PRODUKTIVCODE.** `git show --stat` nennt **keine** Datei unter
  `resources/`, `app/`, `database/`, `routes/`. **Nur** `docs/`.
  *(Das ist eine Ablesung. Wer dabei baut, hat die Stufe verlassen.)*
- **W-17-1-6** · **Jede genannte Formel trägt ihre F-Nummer**, und **fehlende werden als Lücke
  gemeldet statt erfunden.** *(K3 — dieselbe Auflage, unter der die Maßstabsrechnung als F-054
  entstanden ist.)*
- **W-17-1-7** · **Suite grün und Zahl unverändert gegen den Bau-Stand**, `tsc exit=0`.
  Zahl **unmittelbar vor dem Bau** erheben — **keine feste Zahl im Kriterium.**

- **W-17-1-8** · **ALLE SECHS KANTEN K1–K6 sind behandelt und je einzeln belegt.**
  **⚠ NACHGETRAGEN 17:4x — das Blatt trug sechs Kanten und KEIN Kriterium nannte sie.**
  Gefunden durch die Selbstprüfung des Planners gegen die sieben Innenprüfungen aus A-39,
  **P1: Kante ohne Kriterium.** *Es ist derselbe Fehler, an dem A-37 eine Runde verloren hat —
  dort fiel K6 durch, weil kein Kriterium sie verlangte.*
  **Der Bau ist bereits gelaufen** (`d7f0c93d`), das Kriterium kommt also nach dem Bau. **Es
  wird deshalb NICHT abgeschwächt, sondern gegen den vorhandenen Bau gemessen:** trägt eine
  Kante keinen Beleg, ist das ein Befund und keine Formalie. **K2 hat sich bereits als tragend
  erwiesen** — der Wächter `PlanUploadTest.php` lag außerhalb der Insel und wurde übersehen.

## Rückweg und Entdeckung

- **Rückweg:** nur Dokumentation. **Rücknahme = Commit zurückdrehen**, kein Code betroffen.
- **Entdeckung:** W-17-1-5 ist die Schutzgrenze — schlägt sie an, hat jemand gebaut statt gelesen.
- **Der Fall, der am ehesten übersehen wird:** **W-17-1-4.** Die Registerzeile steht am Ende, das
  Blatt sieht vorher fertig aus, **und genau daran ist W-37 gescheitert.**
