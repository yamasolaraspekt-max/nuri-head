# Z1-E4-1 — Die Bodenplatte wird ein eigenes Bauteil, kein zweckentfremdeter Deckeneintrag

**ZIEL:** Das Werkzeug **„Bodenplatte"** an erster Stelle der Leiste erzeugt einen eigenen Knoten
auf der untersten Etage — mit Dicke, Höhenlage, Schichten und `erdberuehrt`. **Sichtbar in 3D und
minimal in 2D.** *Die Zwischendecke bleibt unberührt.*

```yaml
auftrag: "Z1-E4-1"
scheibe: "E4 — Bodenplatte als eigenes Bauteil"
spur: "A mit additivem Modell"
art: "MODELL ADDITIV + Command + Werkzeug + Render. Schema 3 -> 4 ueber das vorhandene
      Generierungsskript. KEINE Aenderung an CeilingNode, KEIN Renderer-Umbau."
heimat_app: ticket
heimat_code: resources/planner/hausplaner
mess_sha: c95e8c33
konzept: "docs/konzept/etagenweiser-aufbau.md @ rolle/dirigent 8254ae84 — Scheibe E4 BERICHTIGT
          (Yamas Wort 22:08 eingearbeitet), neun Kriterien"
modellplan: "docs/konzept/golden-path-gp0-modellplan-bodenplatte.md (Planner, 32 002 Bytes) —
             Typ, Zod-Stellen, Command-Plan, Abhaengigkeitsmatrix. DREI STELLEN UEBERHOLT, s.u."
kennung_geprueft: "Z1-E4-1: docs/ 0 Treffer. git log --all --grep 1 Treffer — 0c8dedb6, eine
                   ERWAEHNUNG in der GP-0-Pruefung des Plan-Pruefers, keine Vergabe. Frei."
dor_beleg: "ERTEILT — plan-pruefer 2026-08-22T22:32:20, Votum plan-pruefer-DOR-ERTEILT-Z1-E4-1.yaml, ergebnis_sha bc61ed71, Blatt-Blob 24bbc90f. (Datensatz noch nicht nachgezogen.)"
basis_sha: c95e8c33
prioritaet: "P0 — VORGEZOGEN auf Yamas Anweisung 22:08: 'ich moechte dass die bodenplatte
             verfuegbar ist, kannst du dafuer sorgen und das vorziehen'. Vor E1 und E3."
ballbesitz: "plan-pruefer (DoR)"
regelgrundlage: "yama-ANWEISUNG-bodenplatte-vorziehen-drei-operanden-entschieden.yaml (22:08:13) ·
                 dirigent-KORREKTUR-yamas-operanden-gehen-vor-... (22:10:41, Posten 27)"
zielreifegrad: "ABGENOMMEN (BROWSER)"
```

## Yamas drei Operanden — entschieden, nicht vorgeschlagen

```
1  BEZUGSHOEHE          plus-minus-0,00 = OK FERTIGFUSSBODEN EG
     -> die Platte liegt um den GESAMTEN Fussbodenaufbau tiefer;
        oberkanteMm ist bei erdberuehrt=true NEGATIV (Bauzeichnungs-Konvention)

2  SCHICHTFOLGE         AUSSEN -> INNEN, fuer ALLE feldgleichen schichten-Felder
     Bodenplatte/Decke/Dach   unten -> oben:  schichten[0] = erdseitig, zuletzt der Belag
     Wand                     aussen -> innen: schichten[0] = aussen
     -> die Setzung des Dirigenten 'Index 0 = Oberseite' (22:01) ist AUFGEHOBEN

3  ANZAHL               EINE BODENPLATTE JE GESCHOSS  (nicht je Gebaeude)
     -> Pruefung auf derselben Ebene wie pruefeDeckeProLevel (applyCommand.ts:112)
        Ein spaeterer Keller mit eigener Sohle passt ohne Modellaenderung.
```

> **Yamas Schichtfolge kostet keine Zeile Nacharbeit** — er hat es selbst gemessen: von 18
> `schichten`-Treffern verarbeiten **zwei** die Reihenfolge (`wandaufbau.ts:75` und `:79`), und
> **beide sind Summen.** *Addition ist kommutativ.* **Die Festlegung schließt eine offene Frage,
> sie ändert keine Rechnung.**

## Die Rot-Lage, am Stand `c95e8c33` selbst nachgemessen

```
NICHTS DAVON EXISTIERT — je 0 Treffer ausserhalb der Tests:
  foundationSlabs · FoundationSlabNode · SlabNode · ADD_FOUNDATION_SLAB
  id: 'bodenplatte' · erdberuehrt · oberkanteMm · polygonQuelle

DIE VORLAGEN, die kopiert werden:
  domain/scene.types.ts:54          ceilings?: CeilingNode[]        <- Muster fuer das neue Feld
  domain/scene.types.ts:18          SCHEMA_VERSION = 3 as const     <- geht auf 4
  domain/scene.types.ts:130         "Festlegung und keine Beobachtung … als Frage zurueckgegeben"
                                    <- der Kommentar, den Operand 2 BEANTWORTET
  commands/applyCommand.ts:112      pruefeDeckeProLevel(...)        <- Muster fuer Operand 3
                        :296/:315   seine zwei Aufrufstellen
  scripts/hausplaner-schema.mts     918 Bytes — das Schema-Generierungsskript EXISTIERT
  geometry/hoehenkette.ts:37/:49    E0 gebaut: deckenOberkanteMm · naechsteEtageElevationMm
```

> **Das Schema-Werkzeug ist nicht nur da, es ist ein Tor:** `package.json:7` und `:10` fahren
> `schema:hausplaner:check` **vor** `build:hausplaner` und `test:hausplaner`. *Wer das Schema nicht
> nachzieht, kommt an der Suite nicht vorbei.* **Fachfrage 8 aus GP-0 entfällt.**

---

## Abnahmekriterien (aus Konzept E4 berichtigt; Messbefehle ergänzt, nichts abgeschwächt)

- **Z1-E4-1-a** · **DIE LEISTE BEGINNT MIT „BODENPLATTE", UND EIN KLICK ERZEUGT SIE.**

  **Verlangt:** Registry-Eintrag `bodenplatte` an **Platz 1** der Fix-Zone; ein Klick aus der
  Grundfläche erzeugt die Platte auf der **untersten** Etage. **Bildbeleg 2D und 3D.**

  **Messbefehl:**
  ```
  grep -nE "zone: 'fix'" app/tools/toolPresentation.ts  -> bodenplatte traegt ordnung 1
                                                           (auswahl rueckt nach, Soll-Folge Z1-W2-8 bleibt)
  Browser: Klick in die Grundflaeche -> Platte in 2D UND 3D sichtbar, Bildbeleg beide
  ```

  **Heutiges (rotes) Ergebnis:** `id: 'bodenplatte'` → **0 Treffer**; die Fix-Zone beginnt mit
  `auswahl` (`toolPresentation.ts:72`).

  **Absage-Regel:** Ein Eintrag auf `bauteilKind: 'ceiling'` erfüllt (a) **nicht** — *genau das war
  der Blocker, der `Z1-W2-8-b` gestrichen hat: er sperrt die Zwischendecke desselben Geschosses
  oder ist von ihr ununterscheidbar.* **Die Platte braucht ihren eigenen Knoten.**

- **Z1-E4-1-b** · **ZWEITE PLATTE IM SELBEN GESCHOSS → ABLEHNUNG MIT GRUND.**

  **Verlangt:** Prüfung **wie `pruefeDeckeProLevel`** (`applyCommand.ts:112`), Ablehnung mit
  lesbarem Grund. **Je Geschoss eine — nicht je Gebäude** (Yama 22:08).

  **Messbefehl:**
  ```
  zweiter ADD_FOUNDATION_SLAB auf demselben Level -> CommandAbgelehnt, Grund im Browser lesbar
  ADD_FOUNDATION_SLAB auf einem ANDEREN Level     -> laeuft durch (Keller-Sohle bleibt moeglich)
  ```

  **Heutiges (rotes) Ergebnis:** der Command existiert nicht; `pruefeDeckeProLevel` hat 2
  Aufrufstellen (`:296`, `:315`) und ist das zu kopierende Muster.

  **Absage-Regel:** Eine **gebäudeweite** Sperre erfüllt (b) **nicht** — *sie verbaut den Keller
  mit eigener Sohle, und genau deshalb hat Yama „je Geschoss" entschieden.*

- **Z1-E4-1-c** · **PLATTE AUF EINER ETAGE MIT GESCHOSS DARUNTER → HINWEIS, KEIN ZWANG.**

  **Verlangt:** Liegt unter der gewählten Etage ein weiteres Geschoss, erscheint ein **Hinweis** —
  **keine Ablehnung.**

  **Messbefehl:** Browser, Platte auf OG bei vorhandenem EG → Hinweis sichtbar, Platte **entsteht**.

  **Heutiges (rotes) Ergebnis:** kein Hinweis, kein Werkzeug.

  **Absage-Regel:** Eine Ablehnung erfüllt (c) **nicht.** *Der Keller ist ein gültiger Fall; die
  Warnung informiert, sie verbietet nicht.*

- **Z1-E4-1-d** · **SPEICHERN UND LADEN — UND DER BESTAND BLEIBT UNBERÜHRT.**

  **Verlangt:** Dokument **mit** Platte speichert und lädt (**PHP 200, nicht 422**);
  Bestandsdokument **ohne** Platte lädt **unverändert**. *Yamas eigene Vorgabe vom 21.08.*

  **Messbefehl:**
  ```
  Schema 3 -> 4 ueber scripts/hausplaner-schema.mts (nicht von Hand)
  Zod validation.ts · scene-document-v2.schema.json (additionalProperties:false!)
  PHP: SceneDocumentValidator.php:12 · SpeichereHausplanerDokumentRequest.php:66
  migriereSzene: EINE Zeile
  Bestandsdokument ohne foundationSlabs laden -> 200, Diff zeigt NUR das neue leere Feld
  ```

  **Heutiges (rotes) Ergebnis:** `SCHEMA_VERSION = 3` (`scene.types.ts:18`), `foundationSlabs` →
  **0 Treffer** in Zod, JSON-Schema und PHP.

  **Absage-Regel:** Ein von Hand gepflegtes `scene-document-v2.schema.json` erfüllt (d) **nicht** —
  *das Skript existiert und läuft als Tor vor Build und Suite; eine Handpflege liefe daran vorbei
  und wäre beim nächsten Lauf überschrieben.*

- **Z1-E4-1-e** · **DIE HÖHENKETTE KENNT DIE PLATTE ALS UNTERES ENDE.**

  **Verlangt:** OK Platte = **±0,00 − Fußbodenaufbau**; `oberkanteMm` bei `erdberuehrt=true`
  **negativ**. Ist der Aufbau nicht erfasst, steht der Vermerk **„Aufbau nicht erfasst"** sichtbar.
  Naht: `geometry/hoehenkette.ts` (E0, gebaut).

  **Messbefehl:**
  ```
  Referenzhaus-Fixture: oberkanteMm der Platte < 0 bei erdberuehrt=true
  Panel zeigt "Aufbau nicht erfasst", solange schichten leer ist
  git diff <basis_sha>..<endstand_sha> -- geometry/hoehenkette.ts  -> nur ADDITIV, keine Aenderung
                                                                      der zwei bestehenden Exporte
  ```

  **Heutiges (rotes) Ergebnis:** `oberkanteMm` → 0 Treffer; die Höhenkette kennt kein unteres Ende.

  **Absage-Regel:** Eine **positive** `oberkanteMm` bei `erdberuehrt=true` erfüllt (e) **nicht** —
  *sie widerspricht Yamas Bezugshöhe und der Bauzeichnungs-Konvention.*

- **Z1-E4-1-f** · **DIE HEIZLAST-PROJEKTION LIEFERT DIE GRENZFLÄCHE ERDREICH.**

  **Messbefehl:** Test — Projektion einer Szene mit Platte liefert Grenzfläche `erdreich`.

  **Heutiges (rotes) Ergebnis:** `RaumGeometrieProjektion.boden` (`scene.types.ts:386`) ist heute
  `null`.

  **Absage-Regel:** Ein Wert, der aus der Deckenprojektion abgeleitet wird, erfüllt (f) **nicht** —
  *Boden und Decke sind im CRM getrennte Bauteiltypen (`UWertService.php:26-27`
  `'decke' => [0.10, 0.04]` gegen `'boden' => [0.17, 0.00]`).*

- **Z1-E4-1-g** · **DAS PANEL BEHAUPTET NICHTS, WAS NICHT GEPRÜFT IST.**

  **Verlangt:** Panel führt Dicke, Höhenlage, `erdberuehrt` und Schichten **mit Herkunft**.
  **Das Wort „geprüft" erscheint nie** bei Dicke oder Bewehrung.

  **Messbefehl:** Wortprobe im Panel — `geprüft` bei Dicke/Bewehrung → **0**.

  **Heutiges (rotes) Ergebnis:** kein Panel für die Platte (`EigenschaftenPanel.tsx` kennt sieben
  `selected*`-Typen, keinen für die Platte).

  **Absage-Regel:** *Eine Dickenangabe ohne Herkunft liest sich wie eine Bemessung. Die Statik ist
  nicht Gegenstand dieses Blattes, und das Panel darf das Gegenteil nicht nahelegen.*

- **Z1-E4-1-h** · **DIE FACH-LINSEN LIEGEN VOR DER DoR.**

  **Verlangt:** Votum **Maurer und Statiker**. **Erfüllt durch die Vorlage der lesenden Sitzung
  vom 18:52** (Maurer/Statiker/Software-Architekt) — *der Dirigent hat ausdrücklich festgelegt:
  keine zweite Runde.*

  **Messbefehl:** Verweis auf die Vorlage im DoR-Votum, mit Stand.

  **Heutiges Ergebnis:** **liegt vor** — dies ist das einzige Kriterium, das beim Zuschnitt bereits
  grün ist.

- **Z1-E4-1-i** · **DIE LIEFERUNG IST GRÜN UND VOLLSTÄNDIG.**

  **Messbefehl:** `tsc:hausplaner` → **0** · `test:hausplaner` → 0 fail · **PHP-Tests** grün ·
  **Bündel gebaut und mitcommittet**.

  **Heutiges (grünes) Ergebnis:** Schutzbeleg — am Bau zu messen, nicht heute. *Der Ausgangsstand
  ist grün; (i) hält fest, dass er es bleibt.* **Besonders hier:** `package.json:7` und `:10`
  fahren `schema:hausplaner:check` **vor** Build und Suite — ein nicht nachgezogenes Schema fällt
  also bereits an diesem Tor, nicht erst in der Abnahme.

  **Absage-Regel:** *Ein neues Modellfeld ohne PHP-Test erreicht den Speicherweg nicht — und der
  Speicherweg ist bei einem Schema-Sprung die riskanteste Stelle.*

---

## ⚠ DREI STELLEN, AN DENEN DER GP-0-PLAN ÜBERHOLT WAR — **seit `6e527e10` dort berichtigt**

> **Stand 22.08. 22:3x:** Der Plan-Prüfer hat GP-0 **freigegeben mit fünf Auflagen** (Votum
> `43761baf`), und ich habe sie in einem Durchgang eingearbeitet (`6e527e10`). **GP-0 trägt die
> Korrekturen jetzt selbst** — die Tabelle bleibt als Nachweis stehen, nicht als offener Posten.
> *Zwei der fünf Auflagen deckten sich mit dem, was ich beim Zuschnitt selbst gefunden hatte; die
> übrigen drei kamen von ihm — darunter die einzige, die man nur beim Durchrechnen sieht
> (Auflage 4, unten).*

**Der Dirigent nennt „Konzept E4 (berichtigt) + GP-0 §7" als Quellen. Sie widersprachen sich an
drei Stellen — es gilt jeweils die jüngere:**

| GP-0 sagt | überholt durch | es gilt |
|---|---|---|
| §7-2 „zweite Bodenplatte im selben **Gebäude**" | Yama 22:08 | **je Geschoss** (Kriterium b) |
| §7-3 nennt `berechneHoehenkette(...)` | E0 gebaut (`ad2ac724`) | die Funktion heißt so **nicht** — gebaut sind `deckenOberkanteMm:37` und `naechsteEtageElevationMm:49` |
| Nicht-Ziel „**kein** 2D-Rendering zwingend" | Konzept E4 + Dirigent 22:10 | **2D minimal ist Gegenstand** (Kriterium a) |

*Der Plan-Prüfer hat zudem GP-0 **Abschnitt 1 und 3** als überholt gemeldet (§483) — sie beschreiben
die IST-Höhenkette vor E0.* **Alles übrige aus GP-0 trägt** und ist die Bauvorlage: Typ, Zod-Stellen,
Command-Plan, Abhängigkeitsmatrix.

**Eine vierte Sache, die ich beim Zuschnitt NICHT gefunden hatte** — sie stammt vom Plan-Prüfer und
betrifft das Referenzhaus, gegen das dieses Blatt in (e) misst:

```
Das Referenzhaus setzte floorThickness 200 UND Zwischendecke dickeMm 200.
naechsteEtageElevationMm waehlt genau zwischen diesen beiden (hoehenkette.ts:49-56).
BEI GLEICHEM WERT LIEFERN BEIDE ZWEIGE 2700 — die Fixture kann nicht zeigen,
welcher genommen wurde.  Berichtigt auf floorThickness 180 (6e527e10).
```

> **Kein Rechenfehler: die Kette stimmte, sie war nur nicht unterscheidungsfähig.** *Das sieht man
> erst beim Durchrechnen, nicht beim Lesen — und deshalb steht es hier, wo der Bau (e) misst.*

## Eine technische Entscheidung, die mir gehört

**GP-0 §2 nennt eine Namenskollision:** das Feld `herkunft` kollidiert mit dem vorhandenen
`GeometrieHerkunft`-Enum. *Yama hat sie ausdrücklich dem Planner zugewiesen* („technisch, nicht
fachlich"). **Entschieden: Option A — das Feld heißt `polygonQuelle`.** Additiv, risikoarm, und es
lässt das bestehende Enum unberührt. *Option B hätte ein Enum erweitert, das andere Verbraucher hat.*

## Nicht-Ziele

- **Keine Änderung an `CeilingNode`.** Die Zwischendecke bleibt, was sie ist.
- **Kein Renderer-Umbau.** Die Decken-/Dach-Blöcke (`szene.ts:452-599`) bleiben unangetastet, die
  Platte kommt **additiv** dazu.
- **Keine Vermischung von Fußbodenaufbau und tragender Platte.** `schichten?` und `dickeMm` bleiben
  getrennte Felder mit getrennter Bedeutung.
- **Keine Statik.** Bewehrung, Tragfähigkeit, Bemessung sind **nicht** Gegenstand — siehe (g).
- **Keine Durchbrüche außer ausdrücklich gesetzten.** Keine Automatik.
- **Keine zweite Fach-Linsen-Runde** (Dirigent 22:10).

## Nachvollzugs-Matrix (§5 / N3)

| Kriterium | Arbeitspaket | Commit-SHA | Testbeleg |
|---|---|---|---|
| a Leiste Platz 1, Klick erzeugt, 2D+3D | AP-1 Werkzeug + Render | n.U. | n.U. |
| b zweite Platte je Geschoss abgelehnt | AP-2 Command-Wache | n.U. | n.U. |
| c Geschoss darunter → Hinweis | AP-2 (Hinweis) | n.U. | n.U. |
| d Schema 3→4, Bestand lädt unverändert | AP-3 Modell + Speicherweg | n.U. | n.U. |
| e Höhenkette kennt das untere Ende | AP-3 (Naht E0) | n.U. | n.U. |
| f Grenzfläche erdreich | AP-4 Projektion | n.U. | n.U. |
| g Panel ohne „geprüft" | AP-1 (Panel) | n.U. | n.U. |
| h Fach-Linsen vor DoR | **liegt vor** (Vorlage 18:52) | — | — |
| i tsc/Suite/PHP/Bündel | AP-4 Lieferung | n.U. | n.U. |

## N4 — Bedienweg

| | |
|---|---|
| **Auslöser** | Werkzeug **„Bodenplatte"**, Platz 1 der Leiste |
| **Ort** | `app/tools/toolRegistry.ts` · `toolPresentation.ts` · `app/HausplanerApp.tsx` (Klick-Handler) |
| **neue Kennung** | `bodenplatte` — der erste neue Registry-Eintrag seit `trimmen` (A-35) |
| **Zielreifegrad** | `ABGENOMMEN (BROWSER)`, 2D **und** 3D |

## Rückweg

**Ein Commit zurück plus Schema-Rückbau** (`SCHEMA_VERSION` 4 → 3, Skript neu fahren).
*Das Feld ist optional und additiv — ein Bestandsdokument ohne Platte ist vor und nach dem Rückbau
dasselbe Dokument.* **Deshalb ist (d) das Kriterium, an dem der Rückweg hängt.**
