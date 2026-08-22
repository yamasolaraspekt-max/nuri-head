# Z1-W2-2 — Aufbauten ohne Fläche bleiben unbemerkt, obwohl die Prüfung sie kennt

**ZIEL:** `geometry/aufbautenStatus.ts` erreicht den Benutzer — wer einen prüfpflichtigen Aufbau
ohne zugeordnete Fläche stehen lässt, **sieht die Warnung im Planer**, statt sie nur im Test zu
erzeugen.

```yaml
auftrag: "Z1-W2-2"
spur: W
welle: "Anschlusswelle 1 (Paket 3 — Pruefungen und Warnungen)"
heimat_app: ticket
heimat_code: resources/planner/hausplaner
werkzeug: "W-22 Gaube (tragendes Werkzeug; das Modul ist eine PRUEFUNG, kein Leistenwerkzeug)"
modul: "geometry/aufbautenStatus.ts — 77 Zeilen"
registry_kennung: "KEINE. Das Modul bekommt keinen Leisteneintrag (siehe N4)."
art: "ANSCHLUSS — vorhandene, geprüfte Fachlogik bekommt einen Produktivpfad.
      KEINE Aenderung der Fachlogik, KEIN neues Rechnen, KEINE toolRegistry-Aenderung."
mess_sha: 4611267e
kennung_geprueft: "Z1-W2-2 gemessen: docs/ 0 Treffer, git log --all --grep 0. Z1-W2-1 ist von mir
                   selbst vergeben (4611267e), Z1-W2-2 ist die naechste freie. Frei."
dor_beleg: "steht aus — plan-pruefer"
basis_sha: 4611267e
prioritaet: P0
ballbesitz: "plan-pruefer (DoR)"
regelgrundlage: "ANSCHLUSS-entscheidung-2026-08-22.md: Paket 3 zuerst.
                 Kriterium (a) zweigleisig, Praezisierung des Dirigenten 14:15:26.
                 N4 Bedienweg (ARBEITSREGELN-NACHTRAG-1-5-V3, in Kraft 14:20:19)."
zielreifegrad: BROWSERABGENOMMEN
```

## Ausgangslage, gemessen am Stand `4611267e`

```
geometry/aufbautenStatus.ts                 77 Zeilen
exportiert   AUFBAUTEN_WARNUNG (Text) · aufbautenOhneFlaeche() · istAufbauPruefpflichtig()
             AufbauRef · AufbautenPruefErgebnis
Testdatei    __tests__/aufbautenStatus.test.ts   VORHANDEN
Erreichbar   NEIN — 0 Aufrufe im Produktivpfad
             grep -rlE 'aufbautenOhneFlaeche|istAufbauPruefpflichtig|AUFBAUTEN_WARNUNG'
             ohne __tests__/__domtests__ und die Datei selbst  ->  0
Registerzeile W-22 Gaube (gemessen ueber die Werkzeugblaetter)
```

**Besonderheit gegenüber den Geschwisterblättern:** das Modul bringt den Warntext **selbst mit**
(`AUFBAUTEN_WARNUNG`). *Der Bau muss ihn also nicht formulieren — er muss ihn anzeigen.* **Der
Text ist nicht neu zu erfinden;** eine zweite Formulierung wäre eine zweite Wahrheit.

## N4 — Bedienweg

**Kein Leistenwerkzeug, keine `toolRegistry`-Kennung.** Eine **Prüfung, die zum Gaubenwerkzeug
(W-22) gehört**.

| | |
|---|---|
| **Auslöser** | die Bearbeitung selbst: ein Aufbau wird gesetzt, geändert oder seine Fläche entfernt |
| **Ort der Meldung** | am Objekt bzw. im Statusbereich — **Komponente im Bau zu benennen (Pfad) und im Browser zu belegen** (Kriterium a) |
| **Meldungstext** | `AUFBAUTEN_WARNUNG` **aus dem Modul**, nicht neu formuliert |
| **tragendes Werkzeug** | **W-22** Gaube |
| **kein** | Leisteneintrag, kein Menüpunkt, keine Registry-Kennung |
| **Zielreifegrad** | `BROWSERABGENOMMEN` |

---

## Abnahmekriterien

- **Z1-W2-2-a** · **DIE WARNUNG ERSCHEINT AM OBJEKT ODER IM STATUSBEREICH.**

  **Verlangt:** Der Bau benennt **eine** Komponente mit Pfad, in der die Warnung erscheint, und
  belegt sie im Browser. **Der angezeigte Text ist `AUFBAUTEN_WARNUNG` aus dem Modul.**

  **Messbefehl:** Browserabnahme (siehe `-e`); im Bericht der Komponentenpfad und ein Bildbeleg,
  auf dem der Text **zeichengleich** mit der Konstante ist.

  **Heutiges (rotes) Ergebnis:** Produktivaufrufe → **0**. *Es gibt keine Komponente, die die
  Warnung anzeigt, weil niemand die Prüfung aufruft.*

  **Absage-Regel:** Ein Konsolen-Log erfüllt (a) nicht. **Und ein selbst formulierter Warntext
  erfüllt (a) ebenfalls nicht** — dann stünden zwei Fassungen derselben Warnung im Haus.

- **Z1-W2-2-b** · **EIN AUFBAU OHNE FLÄCHE ERZEUGT DIE WARNUNG — AUSGELÖST.**

  **Verlangt:** Im Browser wird ein **prüfpflichtiger** Aufbau ohne zugeordnete Fläche erzeugt →
  Warnung erscheint. Derselbe Lauf mit zugeordneter Fläche → **keine** Warnung.

  **Messbefehl:** zwei Browserläufe, je mit Bildbeleg und dem gesetzten Zustand
  (Aufbau-Kennung, Flächenzuordnung ja/nein).

  **Heutiges (rotes) Ergebnis:** nicht durchführbar — die Prüfung läuft nicht (siehe a).

  **Absage-Regel:** Ein Test, der `aufbautenOhneFlaeche()` direkt aufruft, erfüllt (b) **nicht**.
  Er prüft die Fachlogik, die grün ist — hier wird der **Weg** geprüft.

- **Z1-W2-2-c** · **ROT-PROBE: OHNE DAS MODUL ERSCHEINT NICHTS.**

  **Verlangt:** Derselbe Bedienweg am Stand **vor** dem Bau → keine Warnung.

  **Messbefehl:** ein Lauf am Basis-Stand mit demselben Zustand wie in (b), Bildbeleg.

  **Heutiges (rotes) Ergebnis:** ist die Rot-Lage selbst.

  **Absage-Regel:** Ohne (c) belegt (b) nur, dass *irgendetwas* erscheint, nicht dass **dieses
  Modul** es erzeugt.

- **Z1-W2-2-d** · **DIE PRÜFPFLICHT-LISTE KOMMT NICHT AUS DEM ANSCHLUSS.**

  **Verlangt:** `istAufbauPruefpflichtig(id, pruefpflichtigIds)` bekommt seine Liste aus dem
  vorhandenen Datenweg. **Der Bau legt keine eigene Liste an.**

  **Messbefehl:** im Diff zeigen, woher `pruefpflichtigIds` stammt; eine im Anschlusscode
  hartcodierte Liste ist ein Verstoß.

  **Heutiges (rotes) Ergebnis:** keine Aufrufstelle vorhanden, also auch keine Herkunft.

  **Absage-Regel:** *Eine hartcodierte Liste im Anschluss wäre eine zweite Wahrheit über die
  Prüfpflicht — genau der Fehler, den A-43 an drei Wortlisten gezeigt hat.*

- **Z1-W2-2-e** · **KEIN PRODUKTCODE AUSSERHALB DER HAUSPLANER-INSEL.**

  **Messbefehl:**
  ```
  git diff --name-only <basis>..<bau> -- ':!resources/planner/hausplaner'   -> leer
  ```

  **Heutiges (grünes) Ergebnis:** kein Bau vorhanden → leer. **Schutzbeleg** am Bau-Diff.

- **Z1-W2-2-f** · **BROWSERABNAHME, MIT ORT.**

  **Messbefehl:**
  ```
  ORT: die im Repo vorhandene Puppeteer-Buehne, Chrome HEADFUL (headless kann kein WebGL)
  je Lauf: Zustand · Bildbeleg · Konsolenausgabe · Stand-SHA
  ```

  **Heutiges (rotes) Ergebnis:** keine Abnahme vorhanden.

  **Absage-Regel:** `headless` erfüllt (f) nicht — ein leerer Canvas sieht aus wie „keine Warnung".

- **Z1-W2-2-g** · **DIE FACHLOGIK BLEIBT UNVERÄNDERT.**

  **Messbefehl:**
  ```
  git diff --stat <basis>..<bau> -- geometry/aufbautenStatus.ts   -> leer
  Suite __tests__/aufbautenStatus.test.ts                          -> 0 fail
  ```

  **Heutiges (grünes) Ergebnis:** Schutzbeleg. *Wer beim Verdrahten die Logik anfasst, hat zwei
  Änderungen in einem Schritt.*

---

## Nicht-Ziele

- **Kein Leisteneintrag, keine `toolRegistry`-Kennung** (N4).
- **Keine neue Warnung und kein neuer Warntext** — `AUFBAUTEN_WARNUNG` gilt.
- **Keine Änderung an W-22** als Werkzeug; dieses Blatt hängt die Prüfung an.
- **Keine Erweiterung der Prüfpflicht** — welche Aufbauten prüfpflichtig sind, entscheidet dieses
  Blatt nicht.

## Nachvollzugs-Matrix (ARBEITSREGELN §5 / N3)

| Kriterium | Arbeitspaket | Commit-SHA | Testbeleg |
|---|---|---|---|
| Z1-W2-2-a Warnung sichtbar | AP-1 Anzeigekomponente | n.U. | n.U. |
| Z1-W2-2-b Aufbau ohne Fläche löst aus | AP-2 Aufruf im Bearbeitungsweg | n.U. | n.U. |
| Z1-W2-2-c Rot-Probe | AP-3 Vorher/Nachher-Lauf | n.U. | n.U. |
| Z1-W2-2-d Prüfpflicht-Herkunft | AP-2 (Datenweg) | n.U. | n.U. |
| Z1-W2-2-e Inselgrenze | AP-4 Diff-Beleg | n.U. | n.U. |
| Z1-W2-2-f Browserabnahme | AP-3 (Bühne, headful) | n.U. | n.U. |
| Z1-W2-2-g Fachlogik unberührt | AP-4 (Diff + Suite) | n.U. | n.U. |

## Rückweg

**Revert dieses einen Commits.** Die Fachlogik bleibt unverändert (g), der Anschluss ist additiv,
`docs/STATUS.md` wird vom Bau nicht geschrieben.
