# Z1-W2-1 — Der Integrationsabgleich meldet Konflikte, die heute niemand sieht

**ZIEL:** `geometry/integrationAbgleich.ts` erreicht den Benutzer — wer eine Öffnung oder ein Paket
so setzt, dass es mit dem Bestand kollidiert, **sieht die Meldung im Planer**, statt sie nur im Test
zu erzeugen.

```yaml
auftrag: "Z1-W2-1"
spur: W
welle: "Anschlusswelle 1 (Paket 3 — Pruefungen und Warnungen)"
heimat_app: ticket
heimat_code: resources/planner/hausplaner
werkzeug: "W-40 Gueltigkeitsstatus (tragendes Werkzeug; das Modul ist eine PRUEFUNG, kein Leistenwerkzeug)"
modul: "geometry/integrationAbgleich.ts — 135 Zeilen"
registry_kennung: "KEINE. Das Modul bekommt keinen Leisteneintrag (siehe N4)."
art: "ANSCHLUSS — vorhandene, geprüfte Fachlogik bekommt einen Produktivpfad.
      KEINE Aenderung der Fachlogik, KEIN neues Rechnen, KEINE toolRegistry-Aenderung."
mess_sha: d3efc5c8
kennung_geprueft: "Z1-W2-1 gemessen, nicht geraten: docs/ 0 Treffer, git log --all --grep 0,
                   Steuerungsablage nur die Auftragsschablone 'Z1-W2-<n>'. Z1-W1-1..5 existieren
                   (Welle 1), Z1-W2-* ist unbenutzt. Frei."
dor_beleg: "ERTEILT — plan-pruefer 2026-08-22, Beleg 05c26de4 (plan-pruefer-DOR-Z1-W2-1-ERTEILT.yaml)"
basis_sha: d3efc5c8
prioritaet: P0
ballbesitz: "generator (DoR erteilt — baubar)"
regelgrundlage: "ANSCHLUSS-entscheidung-2026-08-22.md (Dirigent in Yamas Namen): Paket 3 zuerst.
                 Kriterium (a) zweigleisig, Praezisierung des Dirigenten 14:15:26."
zielreifegrad: BROWSERABGENOMMEN
```

## Ausgangslage, gemessen am Stand `d3efc5c8`

```
geometry/integrationAbgleich.ts            135 Zeilen
exportiert   pruefeOeffnungsIntegration · pruefePaketIntegration
             KonfliktTyp · KonfliktSchwere ('blocker'|'warnung'|'hinweis')
             IntegrationsKonflikt · IntegrationsErgebnis · OeffnungsZiel
Testdatei    __tests__/integrationAbgleich.test.ts   VORHANDEN
Erreichbar   NEIN — kein Laufzeit-Import im Produktivpfad (BFS ab main.tsx)
Registerzeile W-40 Gueltigkeitsstatus (gemessen ueber die Werkzeugblaetter)
```

**Die Fachlogik ist gebaut und geprüft. Sie ist nur nicht erreichbar.** *Anschließen heißt hier
verdrahten, nicht bauen.*

## N4 — Bedienweg

**Das Modul ist kein Leistenwerkzeug und bekommt keine `toolRegistry`-Kennung.** Es ist eine
**Prüfung, die zu einem Werkzeug gehört** — dem Gültigkeitsstatus (W-40).

| | |
|---|---|
| **Auslöser** | die Bearbeitung selbst: eine Öffnung wird gesetzt/verschoben oder ein Paket zugewiesen |
| **Ort der Meldung** | am Objekt bzw. im Statusbereich — **die Komponente ist im Bau zu benennen und im Browser zu belegen** (Kriterium a) |
| **tragendes Werkzeug** | **W-40** Gültigkeitsstatus |
| **kein** | Leisteneintrag, kein Menüpunkt, keine Registry-Kennung |
| **Zielreifegrad** | `BROWSERABGENOMMEN` |

> **Warum das ausdrücklich hier steht:** „ein Werkzeug = ein Blatt" könnte zu der Annahme verleiten,
> jedes Anschlussblatt brauche einen Leisteneintrag. **Für Prüfungen wäre das falsch** — eine
> Warnung, die man erst anklicken muss, warnt nicht.

---

## Abnahmekriterien

- **Z1-W2-1-a** · **DIE MELDUNG ERSCHEINT AM OBJEKT ODER IM STATUSBEREICH.**

  **Verlangt:** Der Bau benennt **eine** Komponente mit Pfad, in der das Prüfergebnis erscheint,
  und belegt sie im Browser. **Kein Leisteneintrag.**

  **Messbefehl:** Browserabnahme (siehe `-e`); im Bericht steht der Pfad der Komponente und ein
  Bildbeleg der sichtbaren Meldung.

  **Heutiges (rotes) Ergebnis:**
  ```
  grep -rlE 'pruefeOeffnungsIntegration|pruefePaketIntegration' --include='*.ts' --include='*.tsx'
    | grep -v '__tests__|__domtests__|integrationAbgleich.ts'          ->  0
  ```
  *Es gibt keine Komponente, die das Ergebnis anzeigt, weil es niemand aufruft.*
  **Der Ausschluss der Definitionsdatei gehört dazu** — ohne ihn zählt der Befehl das Modul
  selbst mit und gäbe **1** statt 0. **Und `-E` gehört dazu, das Escape davor nicht:**
  `grep -rlE '…\|…'` sucht das *literale* Pipe und findet **nie** etwas — auch nach dem Bau.

  **Absage-Regel:** Ein Konsolen-Log erfüllt (a) nicht. Sichtbar heißt: im Planer, ohne
  Entwicklerwerkzeuge.

- **Z1-W2-1-b** · **BEARBEITEN ERZEUGT DIE MELDUNG — AUSGELÖST, NICHT BEHAUPTET.**

  **Verlangt:** Eine Öffnung wird im Browser so gesetzt, dass `pruefeOeffnungsIntegration` einen
  Konflikt der Schwere `blocker` liefert. **Die Meldung erscheint.** Derselbe Lauf mit einer
  konfliktfreien Öffnung: **keine Meldung.**

  **Wizard-Weg — Entscheidung des Dirigenten in Yamas Namen (14:53:40), auf Fachfrage des
  Generators:** Ein mit **„Übernehmen"** gebildetes Paket gilt für den Integrationsabgleich als
  **freigegeben (`approved`)**. Der Statuskonflikt zielt auf Pakete **fremder Herkunft** und greift
  dort, wo ein Zuweisungsweg existiert — **heute gibt es keinen.**
  *Begründung: die Nutzerhandlung „Übernehmen" **ist** die Freigabe dieses Entwurfs; ein
  `draft`-Blocker auf dem einzigen Weg wäre eine Meldung ohne Handlungsmöglichkeit.*
  **Kosten offen benannt:** auf dem Wizard-Weg findet **keine** Statusprüfung statt.

  **Messbefehl:** zwei Browserläufe, je mit Bildbeleg und dem gesetzten Eingabewert.

  **Heutiges (rotes) Ergebnis:** nicht durchführbar — das Modul wird nicht aufgerufen (siehe a).

  **Absage-Regel:** Ein Test, der die Funktion direkt aufruft, erfüllt (b) **nicht**. Er prüft die
  Fachlogik, die längst grün ist — hier wird der **Weg** geprüft.

- **Z1-W2-1-c** · **ROT-PROBE: OHNE DAS MODUL ERSCHEINT NICHTS.**

  **Verlangt:** Wird der Aufruf entfernt (oder das Ergebnis unterdrückt), **verschwindet die
  Meldung** — derselbe Bedienweg, kein Hinweis.

  **Messbefehl:** ein Lauf am Stand **vor** dem Bau mit demselben Eingabewert wie in (b) → keine
  Meldung, Bildbeleg.

  **Heutiges (rotes) Ergebnis:** ist die Rot-Lage selbst — heute erscheint nie eine Meldung.

  **Absage-Regel:** Ohne (c) belegt (b) nur, dass *irgendetwas* erscheint, nicht dass **dieses
  Modul** es erzeugt.

- **Z1-W2-1-d** · **KEIN PRODUKTCODE AUSSERHALB DER HAUSPLANER-INSEL.**

  **Verlangt:** Der Diff berührt ausschließlich `resources/planner/hausplaner/**`.

  **Messbefehl:**
  ```
  git diff --name-only <basis>..<bau> -- ':!resources/planner/hausplaner'   -> leer
  git diff --name-only <basis>..<bau> -- app/ routes/ database/             -> leer
  ```

  **Heutiges (grünes) Ergebnis:** kein Bau vorhanden → leer. **Schutzbeleg**, am Bau-Diff zu messen.

- **Z1-W2-1-e** · **BROWSERABNAHME, MIT ORT.**

  **Verlangt:** Reale Browserabnahme nach den Arbeitsregeln — **Puppeteer, headful** (WebGL).

  **Messbefehl:**
  ```
  ORT: die im Repo vorhandene Puppeteer-Bühne, Chrome HEADFUL (headless kann kein WebGL)
  je Lauf: Eingabewert · Bildbeleg · Konsolenausgabe · Stand-SHA
  ```

  **Heutiges (rotes) Ergebnis:** keine Abnahme vorhanden.

  **Absage-Regel:** `headless` erfüllt (e) nicht — die Szene rendert dann nicht, und ein leerer
  Canvas sieht aus wie „keine Meldung".

- **Z1-W2-1-f** · **DIE FACHLOGIK BLEIBT UNVERÄNDERT.**

  **Verlangt:** `geometry/integrationAbgleich.ts` wird **nicht** geändert, und
  `__tests__/integrationAbgleich.test.ts` läuft unverändert grün.

  **Messbefehl:**
  ```
  git diff --stat <basis>..<bau> -- geometry/integrationAbgleich.ts   -> leer
  Testlauf der Suite: 0 fail
  ```

  **Heutiges (grünes) Ergebnis:** Schutzbeleg. *Wer beim Verdrahten die Logik anfasst, hat zwei
  Änderungen in einem Schritt und keine davon sauber belegt.*

---

## Nicht-Ziele

- **Kein Leisteneintrag, keine `toolRegistry`-Kennung** — ausdrücklich (N4).
- **Keine neue Konfliktart.** Die vier Typen aus `KonfliktTyp` bleiben, wie sie sind.
- **Keine Änderung an W-40** als Werkzeug — dieses Blatt hängt die Prüfung an, es baut das
  Werkzeug nicht um.
- **Kein Zuweisungsweg für Pakete fremder Herkunft.** Er existiert heute nicht, und dieses Blatt
  baut ihn nicht.

## Folgeposten (benannt, NICHT Teil dieses Auftrags)

**Zuweisungsweg und Statusprüfung** — sobald Pakete *fremder Herkunft* zugewiesen werden können,
greift der Statuskonflikt des Moduls und braucht eine eigene Prüfung. **Eigenes Blatt, im
Werkzeug-Register zu führen, nicht jetzt.** *Der Kostenpunkt aus der Entscheidung von 14:53:40 —
„keine Statusprüfung auf dem Wizard-Weg" — bleibt damit sichtbar, statt in einem Nebensatz zu
verschwinden.*

## Nachvollzugs-Matrix (ARBEITSREGELN §5 / N3)

| Kriterium | Arbeitspaket | Commit-SHA | Testbeleg |
|---|---|---|---|
| Z1-W2-1-a Meldung sichtbar | AP-1 Anzeigekomponente | `1c80a1d8` | `app/rahmen/IntegrationsKonflikte.tsx`; Aufrufer im Produktivpfad **0 → 2**; Bild `z1w21-gruen2-2-blocker.png` |
| Z1-W2-1-b Bearbeiten erzeugt sie | AP-2 Aufruf im Bearbeitungsweg | `1c80a1d8` | Browser, zwei Läufe: 1040 gegen Öffnung 1010 → `blocker`; 1010 gegen 1010 → keine Konfliktmeldung |
| Z1-W2-1-c Rot-Probe | AP-3 Vorher/Nachher-Lauf | `1c80a1d8` | derselbe Weg, Bündel **ohne** den Anschluss: nur die drei Attrappen, `[data-pruefung]` **0×** |
| Z1-W2-1-d Inselgrenze | AP-4 Diff-Beleg | `1c80a1d8` | `git diff --name-only -- ':!resources/planner/hausplaner'` → **leer** |
| Z1-W2-1-e Browserabnahme | AP-3 (Bühne, headful) | `1c80a1d8` | Puppeteer **headful**, Chrome, `?fixture=decke-treppe`, Port 8098; **6 von 6 Schritten belegt** |
| Z1-W2-1-f Fachlogik unberührt | AP-4 (Diff + Suite) | `1c80a1d8` | `git diff --stat -- geometry/integrationAbgleich.ts` → **leer**; Suite **1778/1778**, `tsc` 0 |

## Rückweg

**Revert dieses einen Commits.** Es entsteht kein Zustand, der zurückgebaut werden müsste: die
Fachlogik bleibt unverändert (f), der Anschluss ist additiv, `docs/STATUS.md` wird vom Bau nicht
geschrieben.
