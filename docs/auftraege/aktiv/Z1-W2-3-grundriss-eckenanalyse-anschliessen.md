# Z1-W2-3 — Die Eckenanalyse weiß, ob die Kontur zur gewählten Grundrissform passt. Niemand fragt sie.

**ZIEL:** `geometry/grundriss.ts` erreicht den Benutzer — wer eine Grundrissform wählt und eine
Kontur erzeugt, deren Innenwinkel nicht dazu passen, **sieht den Hinweis im Planer**.

```yaml
auftrag: "Z1-W2-3"
spur: W
welle: "Anschlusswelle 1 (Paket 3 — Pruefungen und Warnungen)"
heimat_app: ticket
heimat_code: resources/planner/hausplaner
werkzeug: "W-05 Raum erkennen — ENTSCHIEDEN, nicht gemessen (siehe 'Die Zuordnung ist eine Entscheidung')"
modul: "geometry/grundriss.ts — 154 Zeilen"
registry_kennung: "KEINE. Das Modul bekommt keinen Leisteneintrag (siehe N4)."
art: "ANSCHLUSS — vorhandene Fachlogik bekommt einen Produktivpfad.
      KEINE Aenderung der Fachlogik, KEIN neues Rechnen, KEINE toolRegistry-Aenderung."
mess_sha: 4611267e
kennung_geprueft: "Z1-W2-3 gemessen: docs/ 0 Treffer, git log --all --grep 0. Z1-W2-1 und -2 sind
                   von mir selbst vergeben; -3 ist die naechste freie. Frei."
dor_beleg: "steht aus — plan-pruefer"
basis_sha: 4611267e
prioritaet: P0
ballbesitz: "plan-pruefer (DoR)"
regelgrundlage: "ANSCHLUSS-entscheidung-2026-08-22.md: Paket 3 zuerst. Kriterium (a) zweigleisig
                 (Dirigent 14:15:26). N4 Bedienweg (NACHTRAG-1-5-V3, in Kraft 14:20:19)."
zielreifegrad: BROWSERABGENOMMEN
```

## Ausgangslage, gemessen am Stand `4611267e`

```
geometry/grundriss.ts                      154 Zeilen
exportiert   GrundrissForm ('rechteck'|'l-form'|'t-form'|'u-form')
             grundrissPolygon(form, length, width, lengthB?, widthB?)
             eckenAnalyse(poly) -> EckenAnalyse · anzahlInnenwinkel(poly)
Erreichbar   NEIN — 0 Aufrufe im Produktivpfad
             grep -rlE 'grundrissPolygon|eckenAnalyse|anzahlInnenwinkel'
             ohne __tests__/__domtests__ und die Datei selbst  ->  0
EIGENE Testdatei   NEIN
mittelbar geprueft in  __tests__/dachformVorlagen.test.ts · __tests__/dachAusschnitt.test.ts
```

> **Dieses Modul ist das schwächste der drei — und das ist der Unterschied, der sein Kriterium (g)
> anders begründet.** Die beiden Geschwisterblätter schützt eine **eigene** Suite; hier schützen
> nur **zwei fremde**, die das Modul nebenbei benutzen. *Meine Anschluss-Vorlage schrieb „alle drei
> mit Tests" — für dieses Modul trifft das nur mittelbar zu, und ich korrigiere es hier.*

## Die Zuordnung ist eine **Entscheidung**, keine Messung — und ich sage warum

Der Dirigent verlangt (14:15:26): *„Für `grundriss.ts` (mehrdeutig, sechs Blätter): Zuordnung zum
tragenden Werkzeug ist deine Gestaltungsentscheidung im Blatt — benennen, nicht offenlassen."*

**Was die Messung hergibt — und was nicht:**

```
Werkzeugblaetter, die den Wortstamm 'grundriss' nennen   W-05 2 · W-08 2 · W-10 2
                                                          W-11 2 · W-17 3 · W-26 1
Werkzeugblaetter, die grundrissPolygon/eckenAnalyse/
anzahlInnenwinkel NAMENTLICH nennen                       KEINES
```

**Die Häufigkeit entscheidet nicht** (alle zwischen 1 und 3), und **die Funktionen werden nirgends
namentlich genannt**. *Die Registerzuordnung „sechs Blätter" beruht allein auf dem Wortstamm.*

**Ein naheliegender Kandidat scheidet aus, und zwar durch Messung:** `W-16` heißt „Grundriss
unterlegen" — das Verzeichnis heißt `W-16-import-grundriss`, sein Zweck lautet wörtlich *„Er hat
einen Grundriss auf Papier oder als PDF und will darüber zeichnen — maßhaltig."* **Das ist eine
Bildvorlage, keine Konturerzeugung.** Nennungen des Moduls: **0**.

> *Beinahe hätte ich W-16 genommen, weil der Name passt. Dieselbe Falle wie heute bei
> `werkzeugRegistry`: der Name war fast mein Kriterium, nicht die Sache.*

**Entschieden: `W-05` Raum erkennen.** Begründung — dort entsteht die Raumgeometrie aus der
Kontur, und `eckenAnalyse`/`anzahlInnenwinkel` beantworten genau die Frage, die dort gestellt wird:
*passt diese Kontur zu der Form, die behauptet wird?* **Eine Warnung gehört an den Ort, an dem die
Kontur entsteht**, nicht dorthin, wo sie später exportiert (W-17) oder bemaßt (W-11) wird.

**Was diese Entscheidung nicht ist:** eine Messung. **Widerspricht der Bau ihr mit einem
Codebefund, gilt der Befund** — dann ist dieses Blatt zu ändern, nicht der Code zu biegen.

## N4 — Bedienweg

| | |
|---|---|
| **Auslöser** | die Bearbeitung selbst: eine Grundrissform wird gewählt oder die Kontur geändert |
| **Ort der Meldung** | am Objekt bzw. im Statusbereich — **Komponente im Bau zu benennen (Pfad) und im Browser zu belegen** |
| **tragendes Werkzeug** | **W-05** Raum erkennen (*entschieden, siehe oben*) |
| **kein** | Leisteneintrag, kein Menüpunkt, keine Registry-Kennung |
| **Zielreifegrad** | `BROWSERABGENOMMEN` |

---

## Abnahmekriterien

- **Z1-W2-3-a** · **DER HINWEIS ERSCHEINT AM OBJEKT ODER IM STATUSBEREICH.**

  **Verlangt:** Der Bau benennt **eine** Komponente mit Pfad und belegt sie im Browser.

  **Messbefehl:** Browserabnahme (siehe `-f`), Komponentenpfad und Bildbeleg im Bericht.

  **Heutiges (rotes) Ergebnis:** Produktivaufrufe → **0**.

  **Absage-Regel:** Ein Konsolen-Log erfüllt (a) nicht.

- **Z1-W2-3-b** · **EINE UNPASSENDE KONTUR ERZEUGT DEN HINWEIS — AUSGELÖST.**

  **Verlangt:** Im Browser wird eine Form gewählt (z. B. `l-form`) und eine Kontur erzeugt, deren
  `anzahlInnenwinkel` nicht dazu passt → Hinweis erscheint. Passende Kontur → **kein** Hinweis.

  **Messbefehl:** zwei Browserläufe, je mit gewählter Form, Innenwinkelzahl und Bildbeleg.

  **Heutiges (rotes) Ergebnis:** nicht durchführbar — die Analyse läuft nicht.

  **Absage-Regel:** Ein Test, der `eckenAnalyse()` direkt aufruft, erfüllt (b) **nicht** — hier
  wird der **Weg** geprüft, nicht die Rechnung.

- **Z1-W2-3-c** · **ROT-PROBE: OHNE DAS MODUL ERSCHEINT NICHTS.**

  **Messbefehl:** derselbe Bedienweg am Stand **vor** dem Bau, Bildbeleg.

  **Heutiges (rotes) Ergebnis:** ist die Rot-Lage selbst.

  **Absage-Regel:** Ohne (c) belegt (b) nur, dass *irgendetwas* erscheint.

- **Z1-W2-3-d** · **DIE VIER FORMEN BLEIBEN DIE VIER FORMEN.**

  **Verlangt:** `GrundrissForm` bleibt `rechteck|l-form|t-form|u-form`. Der Anschluss fügt **keine**
  Form hinzu und lässt keine weg.

  **Messbefehl:** `git diff <basis>..<bau> -- geometry/grundriss.ts` → leer (siehe g); zusätzlich
  im Browser: die Formauswahl zeigt genau vier Einträge.

  **Heutiges (grünes) Ergebnis:** vier Formen im Typ, gemessen. **Regressionsschutz.**

  **Absage-Regel:** *Eine fünfte Form im Anschlusscode wäre eine zweite Wahrheit über die
  Grundrissformen.*

- **Z1-W2-3-e** · **KEIN PRODUKTCODE AUSSERHALB DER HAUSPLANER-INSEL.**

  **Messbefehl:** `git diff --name-only <basis>..<bau> -- ':!resources/planner/hausplaner'` → leer.

  **Heutiges (grünes) Ergebnis:** Schutzbeleg am Bau-Diff.

- **Z1-W2-3-f** · **BROWSERABNAHME, MIT ORT.**

  **Messbefehl:**
  ```
  ORT: die im Repo vorhandene Puppeteer-Buehne, Chrome HEADFUL (headless kann kein WebGL)
  je Lauf: gewaehlte Form · Innenwinkelzahl · Bildbeleg · Stand-SHA
  ```

  **Heutiges (rotes) Ergebnis:** keine Abnahme vorhanden.

- **Z1-W2-3-g** · **DIE FACHLOGIK BLEIBT UNVERÄNDERT — UND HIER SCHÜTZT SIE NUR EIN FREMDER TEST.**

  **Verlangt:** `geometry/grundriss.ts` wird **nicht** geändert. **Weil es keine eigene Suite hat**,
  müssen die **zwei mittelbaren** Suiten grün bleiben und im Bericht **namentlich** genannt werden.

  **Messbefehl:**
  ```
  git diff --stat <basis>..<bau> -- geometry/grundriss.ts        -> leer
  __tests__/dachformVorlagen.test.ts                             -> 0 fail
  __tests__/dachAusschnitt.test.ts                               -> 0 fail
  ```

  **Heutiges (grünes) Ergebnis:** Schutzbeleg.

  **Absage-Regel:** „Die Suite ist grün" genügt hier **nicht** — es muss dastehen, **welche**
  Suite. *Ein Modul ohne eigene Tests ist beim Verdrahten schlechter geschützt als seine
  Geschwister, und das darf im Bericht nicht verschwinden.*

---

## Nicht-Ziele

- **Kein Leisteneintrag, keine `toolRegistry`-Kennung** (N4).
- **Keine eigene Testdatei nachrüsten.** *Wünschenswert, aber ein anderer Auftrag* — dieses Blatt
  schließt an, es baut keine Testabdeckung.
- **Keine neue Grundrissform** (d).
- **Keine Änderung an W-05** als Werkzeug.

## Nachvollzugs-Matrix (ARBEITSREGELN §5 / N3)

| Kriterium | Arbeitspaket | Commit-SHA | Testbeleg |
|---|---|---|---|
| Z1-W2-3-a Hinweis sichtbar | AP-1 Anzeigekomponente | n.U. | n.U. |
| Z1-W2-3-b unpassende Kontur löst aus | AP-2 Aufruf im Bearbeitungsweg | n.U. | n.U. |
| Z1-W2-3-c Rot-Probe | AP-3 Vorher/Nachher-Lauf | n.U. | n.U. |
| Z1-W2-3-d vier Formen unverändert | AP-4 (Typ + Browser) | n.U. | n.U. |
| Z1-W2-3-e Inselgrenze | AP-4 Diff-Beleg | n.U. | n.U. |
| Z1-W2-3-f Browserabnahme | AP-3 (Bühne, headful) | n.U. | n.U. |
| Z1-W2-3-g Fachlogik unberührt, Suiten benannt | AP-4 (Diff + zwei Suiten) | n.U. | n.U. |

## Rückweg

**Revert dieses einen Commits.** Die Fachlogik bleibt unverändert (g), der Anschluss ist additiv.
