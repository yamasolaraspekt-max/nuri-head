# Z1-E3-1 — Die Decke lässt sich anlegen, aber nicht ändern, nicht löschen, und sie kennt ihre Rolle nicht

**ZIEL:** Eine Decke ist auswählbar, **in der Dicke änderbar**, löschbar, und zeigt ihre **Rolle**
(Zwischendecke / Abschlussdecke) samt Heizlast-Grenzfläche — **abgeleitet aus der Lage,
übersteuerbar, nie als starres Feld gespeichert**.

```yaml
auftrag: "Z1-E3-1"
scheibe: "E3 — Decke bedienbar + Rolle abgeleitet"
spur: "W/A"
art: "Bedienung + Ableitung. Modell NUR, falls die Uebersteuerung ein additives Feld braucht (Kriterium g)."
heimat_app: ticket
heimat_code: resources/planner/hausplaner
mess_sha: 97c610ca
konzept: "docs/konzept/etagenweiser-aufbau.md @ 8e4bb918 (Dirigent, 19:1x) — Scheibe E3, Luecken L5 + L6 + L8"
kennung_geprueft: "Z1-E3-1: KEIN Blatt in docs/auftraege/aktiv/ (0 Dateien mit der Kennung im Namen).
                   Die 4 docs-Treffer sind Steuerungsspiegel und ein Befund, KEINE Blattvergabe —
                   ueber den DATEINAMEN gemessen, nicht ueber den Dateiinhalt."
dor_beleg: "steht aus — plan-pruefer, ein Durchgang je Blatt (Dirigent 23:50:43)"
basis_sha: 97c610ca
prioritaet: P0
ballbesitz: "plan-pruefer (DoR)"
zielreifegrad: "ABGENOMMEN (BROWSER)"
```

## Die Rot-Lage ist **enger** als der Blatt-Titel — und das ist der wichtigste Satz hier

*Der Plan-Prüfer hat mich in §509 (`01154005`, Messstand `05186670`) ausdrücklich davor gewarnt,
die Rot-Lage als „Decke nicht bedienbar" zu fassen. **Er hat recht, und ich habe es am Stand
`97c610ca` selbst nachgemessen:***

```
WAS HEUTE SCHON GEHT — ANLEGEN:
  app/tools/toolRegistry.ts:164        id: 'decke'                     Werkzeug existiert
  app/tools/toolPresentation.ts:82     zone 'fix', ordnung 7           steht in der Leiste
  app/tools/werkzeugLandkarte.ts:181   Marke 'deckt'                   Landkarte kennt es
  app/HausplanerApp.tsx:1120           type: 'ADD_CEILING'             ECHTER Produktivaufruf
  app/sammelBefehle.ts:135             befehle.push({ ADD_CEILING })   zweiter Aufrufer

WAS NICHT GEHT — AENDERN, LOESCHEN, ROLLE:
  UPDATE_CEILING   GENAU ZWEI Codezeilen, beide Deklaration:
                   commands/applyCommand.ts:366  (case)
                   domain/commands.types.ts:30   (Typ)
                   AUFRUFER:  0
  REMOVE_CEILING   GENAU ZWEI Codezeilen, beide Deklaration:
                   commands/applyCommand.ts:381  (case)
                   domain/commands.types.ts:31   (Typ)
                   AUFRUFER:  0
  app/rahmen/EigenschaftenPanel.tsx (618 Zeilen, 38x hp-ep):
                   'ceiling'  ->  0 Treffer
                   'dickeMm'  ->  0 Treffer
                   kennt: door · window · opening · durchgangshoehe · pruefungen · ohne
  deckenLage · Abschlussdecke   in domain/ und geometry/:   je 0 Treffer

  Messmuster (Kommentare AUSGESCHLOSSEN, weil die Zusage von Code handelt):
    grep -rn 'UPDATE_CEILING' --include='*.ts' --include='*.tsx' . | grep -v __tests__ \
      | grep -vE ':[0-9]+: *(\*|//|/\*)'
  Positivkontrolle: dasselbe Muster auf 'dickeMm' -> 52 Treffer (nicht 0)
```

> **Zwei fertige Schreibbefehle, je zwei Zeilen, null Aufrufer.** *Genau der Fall aus Yamas
> Lesesitzungs-Vorlage: „Die billigsten Gewinne liegen dort, wo gebauter Code keinen Verbraucher
> hat."* **E3 schreibt diese Befehle nicht — E3 verdrahtet sie.**

### Der Bestand dokumentiert die Lücke bereits selbst

`fixtures/studioFixtures.ts:201-206`, wörtlich:

```
"Diesen Fall kann heute niemand ueber die Bedienung herstellen — das Deckenwerkzeug setzt
 dickeMm: level.floorThickness, ein Panel-Feld fuer die Deckendicke gibt es nicht
 (dickeMm in EigenschaftenPanel.tsx: 0), und UPDATE_CEILING ruft im App-Code niemand."
```

**Am heutigen Stand sind es zwei Stellen, nicht eine:** `HausplanerApp.tsx:1085` **und**
`HausplanerApp.tsx:1124` setzen `dickeMm: level.floorThickness`. *(Der Kommentar nennt `:1070` —
er stammt aus einem älteren Stand; die Aussage stimmt, die Zeilennummer ist gewandert.)*

> **Das ist die Kante zu E0.** *Die Höhenkette unterscheidet sich vom alten Rechenweg **nur** dort,
> wo `decke.dickeMm` von `level.floorThickness` abweicht — und diesen Fall kann heute niemand über
> die Bedienung herstellen.* **E3 macht die Rot-Probe 2700 → 2740 aus E0 erst bedienbar
> erreichbar.**

## Abnahmekriterien (aus dem Konzept; Messbefehle ergänzt, nichts abgeschwächt)

- **Z1-E3-1-a** · **DECKE ANKLICKEN → PANEL; DICKE ÄNDERN → 3D FOLGT.**

  **Verlangt:** Die Decke ist auswählbar; das **vorhandene** `EigenschaftenPanel` (`app/rahmen/`,
  `hp-ep`-Klassen, AUF-38-P1) bekommt einen Decken-Zweig mit einem Feld für `dickeMm`; die Änderung
  geht über **`UPDATE_CEILING`** und das 3D-Bild folgt.

  **Messbefehl:**
  ```
  grep -c 'ceiling' app/rahmen/EigenschaftenPanel.tsx    ->  > 0
  grep -c 'dickeMm' app/rahmen/EigenschaftenPanel.tsx    ->  > 0
  grep -c 'hp-ep'   app/rahmen/EigenschaftenPanel.tsx    ->  >= 38  (Bestandsklassen bleiben)
  UPDATE_CEILING: Aufrufer ausserhalb applyCommand.ts/commands.types.ts, Kommentare
                  ausgeschlossen  ->  >= 1
  Browser headful: Decke anklicken, dickeMm 200 -> 240, 3D-Hoehe folgt, Bildbeleg vorher/nachher
  ```

  **Heutiges (rotes) Ergebnis:** `ceiling` → **0**, `dickeMm` → **0** im Panel;
  `UPDATE_CEILING`-Aufrufer → **0**.

  **Absage-Regel:** Ein **neues, eigenes** Decken-Panel neben dem vorhandenen erfüllt (a)
  **nicht** — *38 `hp-ep`-Verwendungen sind das Hausmuster; ein zweites Panel wäre die zweite
  Wahrheit in der Oberfläche.*

- **Z1-E3-1-b** · **LÖSCHEN → WEG; UNDO → ZURÜCK.**

  **Verlangt:** Die Decke ist löschbar über **`REMOVE_CEILING`**, und **ein** Undo-Schritt stellt
  sie wieder her.

  **Messbefehl:**
  ```
  REMOVE_CEILING: Aufrufer ausserhalb applyCommand.ts/commands.types.ts, Kommentare
                  ausgeschlossen  ->  >= 1
  Browser: Decke loeschen -> 3D leer; EIN Undo -> Decke zurueck, gleiche dickeMm, gleiche id
  ```

  **Heutiges (rotes) Ergebnis:** `REMOVE_CEILING`-Aufrufer → **0**.

  **Absage-Regel:** Braucht das Rückgängigmachen **zwei** Schritte, erfüllt (b) **nicht**.

- **Z1-E3-1-c** · **DIE ROLLE IST ABGELEITET, NICHT GESPEICHERT — ROT-PROBE ÜBER EIN NEUES GESCHOSS.**

  **Verlangt:** `deckenLage(level, levels)` **neben** `deckenOberkanteMm`. Ein Geschoss **oben
  aufsetzen** ändert die Rolle der bisher obersten Decke von **„Abschlussdecke"** zu
  **„Zwischendecke"** — **ohne jede Benutzeraktion an der Decke**.

  **Messbefehl:**
  ```
  grep -rn 'deckenLage(' --include='*.ts' geometry/ domain/   ->  1 Definition + Aufrufer
  Browser: EG mit Decke -> Panel liest "Abschlussdecke"
           Geschoss oben aufsetzen (Kopfrahmen) -> DIESELBE Decke liest "Zwischendecke"
           Bildbeleg vorher/nachher, ohne Klick auf die Decke
  Modellprobe: git diff -- domain/  zeigt KEIN Feld 'rolle'/'deckenLage' am CeilingNode
  ```

  **Heutiges (rotes) Ergebnis:** `deckenLage` → **0**, `Abschlussdecke` → **0** in `domain/` und
  `geometry/`. **Die Rolle existiert nirgends.**

  **Absage-Regel:** Ein **gespeichertes** Rollenfeld am `CeilingNode` erfüllt (c) **nicht** —
  *das Konzept sagt „abgeleitet aus der Lage … nie als starres Feld gespeichert"; ein gespeichertes
  Feld veraltet in dem Moment, in dem jemand ein Geschoss einfügt.*

- **Z1-E3-1-d** · **DIE ÜBERSTEUERUNG ÜBERLEBT DAS NACHRECHNEN UND TRÄGT IHRE HERKUNFT.**

  **Verlangt:** Die abgeleitete Grenzfläche (**beheizt / unbeheizt / außen**, Posten 25 (2)) ist
  **übersteuerbar**; eine Übersteuerung **bleibt beim Nachberechnen stehen** und ist als solche
  erkennbar — über das **vorhandene** `MitHerkunft` (`domain/scene.types.ts:351`).

  **Messbefehl:**
  ```
  grep -n 'MitHerkunft' domain/scene.types.ts   ->  :351 Definition, :392 CeilingNode extends
  Browser: Grenzflaeche von Hand auf 'unbeheizt' setzen, dann Geschoss oben aufsetzen
           -> Wert bleibt 'unbeheizt', Herkunft liest "manuell", Bildbeleg
  ```

  **Heutiges (rotes) Ergebnis:** `MitHerkunft` ist **gebaut** und `CeilingNode` **erbt es bereits**
  (`:392`) — **eine Grenzfläche, die es tragen könnte, gibt es nicht.**

  > **Auch hier gilt: das Gerüst steht, der Verbraucher fehlt.** *E3 erfindet keine Herkunft, E3
  > benutzt die vorhandene.*

- **Z1-E3-1-e** · **DER HINWEIS ZUM OBERSTEN ABSCHLUSS IST EINE WARNUNG, KEIN ZWANG.**

  **Verlangt:** „Dach/Abschlussdecke nur auf oberster Etage sinnvoll" erscheint als **Warnung am
  Objekt** — *sichtbar, begründet, nicht sperrend* (Lücke L8).

  **Messbefehl:**
  ```
  Browser: Dach auf einer NICHT obersten Etage -> Warnung sichtbar, Vorgang laeuft trotzdem durch
  Wortprobe: der Hinweistext enthaelt KEIN "nicht moeglich"/"gesperrt"
  ```

  **Heutiges (rotes) Ergebnis:** Kein Hinweis; `applyCommand.ts:66-73` lässt das Dach auf jeder
  Etage zu, **kommentarlos**.

  **Absage-Regel:** Eine **Sperre** erfüllt (e) **nicht** — *dieselbe Hausregel wie beim
  Mindestpfeiler: die Prüfung ist ehrlich und sichtbar, sie entmündigt nicht.*

- **Z1-E3-1-f** · **`tsc` 0, Suite grün, Bündel in der Lieferung.**

  **Messbefehl:** `npm run build:hausplaner` · `npm run test:hausplaner` (beide hinter dem
  Schema-Tor `scripts/hausplaner-schema.mts`).

  **Heutiger Wert:** **grün am Ausgangsstand** — *(f) ist kein Fortschrittsmaß, sondern die Zusage,
  dass die Scheibe nichts zerbricht.*

- **Z1-E3-1-g** · **SCHEMA NUR, WENN DIE ÜBERSTEUERUNG EIN FELD BRAUCHT — DANN VOLLSTÄNDIG.**

  **Verlangt:** Braucht (d) ein **optionales** Übersteuerungsfeld, wird es **additiv** ergänzt und
  **alle vier Träger** werden nachgezogen: Zod (`validation.ts`), `scene-document-v2.schema.json`
  (**`additionalProperties: false`!**), PHP-Validator, `migriereSzene`.

  **Messbefehl:**
  ```
  Schema-Aenderung NUR ueber scripts/hausplaner-schema.mts (Tor vor build UND test)
  Wird ein Feld ergaenzt:  Zod · JSON-Schema · PHP · migriereSzene  ->  je >= 1 Treffer
  Bestandsdokument ohne das Feld laden  ->  unveraendert
  ```

  **Heutiger Wert:** **`SCHEMA_VERSION` steht auf 4** (nach E4). *Kein Übersteuerungsfeld am
  `CeilingNode` — `git diff -- domain/` ist am Ausgangsstand leer.* **(g) ist ein bedingtes
  Kriterium: es greift erst, wenn (d) ein Feld verlangt — und dann vollständig.**

  **Absage-Regel:** Ein Feld in **einem** der vier Träger erfüllt (g) **nicht** —
  *`additionalProperties: false` lässt ein Dokument scheitern, das die anderen drei akzeptieren.*
  **Und:** ein von Hand gepflegtes `scene-document-v2.schema.json` liefe am Tor vorbei und wäre beim
  nächsten Lauf überschrieben.

## Nicht-Ziele

- **Schichten-Bearbeitung ist NICHT Gegenstand von E3.**

  > **Begründung, und sie ist inhaltlich, nicht organisatorisch:** die **Reihenfolge** der Schichten
  > ist im Modell **ausdrücklich offen**. `domain/scene.types.ts` sagt wörtlich: *„Die Reihenfolge
  > trägt (noch) keine Bedeutung … bis sie beantwortet ist, darf sich niemand auf die Reihenfolge
  > verlassen."* Und `CeilingNode.schichten` ist **feldgleich** mit `WallNode.schichten`. **Wer in
  > E3 eine Schichten-Bearbeitung baut, entscheidet die offene Frage nebenbei — für Wand und Decke
  > zugleich.**
  >
  > **Offen und dem Dirigenten vorgelegt:** das **Ziel** der Scheibe E3 im Konzept nennt
  > „in Dicke/**Schichten** änderbar", **die Kriterien (1)–(6) des Konzepts nennen die Schichten
  > nicht** — Kriterium (1) sagt „Dicke ändern → 3D folgt". *Ich schwäche also kein Kriterium ab;
  > ich benenne, dass der Zielsatz mehr verspricht als seine Kriterien tragen.* **Soll E3 die
  > Schichten mitnehmen, braucht es zuerst die Reihenfolge-Entscheidung — dann als Halbsatz.**

- **Die Bodenplatte** — **E4** (`Z1-E4-1`, geliefert).
- **Etage einfügen / Höhe ändern mit Folgen** — **E5**.
- **Die Doppelabbildung `werkzeugLandkarte.ts:177`** (`boden` → `ADD_CEILING`) wird **nicht**
  aufgelöst.

  > *Seit E4 gibt es `ADD_FOUNDATION_SLAB`, und die Landkarte führt `boden` weiterhin auf
  > `ADD_CEILING` — zwei Werkzeuge zeigen auf denselben Befehl.* **Keine Fehlfunktion, denn die
  > Landkarte beschreibt und steuert nicht.** Aber es ist dieselbe Fachfrage, die der Generator in
  > seinem E4-Abschluss offengelassen hat: **ist `boden` der Bodenbelag oder die Platte?**
  > *Das entscheidet weder der Plan-Prüfer noch ich — zitiert, nicht nachgebaut.*

## Nachvollzugs-Matrix (§5 / N3)

| Kriterium | Arbeitspaket | Commit-SHA | Testbeleg |
|---|---|---|---|
| a Panel + Dicke ändern, 3D folgt | AP-1 Panel-Zweig + `UPDATE_CEILING` | n.U. | n.U. |
| b löschen + ein Undo | AP-1 `REMOVE_CEILING` verdrahten | n.U. | n.U. |
| c Rolle abgeleitet, Rot-Probe Geschoss oben | AP-2 `deckenLage()` | n.U. | n.U. |
| d Übersteuerung + `MitHerkunft` | AP-2 Grenzfläche | n.U. | n.U. |
| e Warnung, kein Zwang | AP-2 Hinweis | n.U. | n.U. |
| f `tsc`/Suite/Bündel | AP-3 Lieferung | n.U. | n.U. |
| g Schema nur falls nötig, dann vier Träger | AP-3 Schema-Tor | n.U. | n.U. |

## Rückweg

**Ohne (g):** ein Commit zurück, kein Modell, keine Migration.
**Mit (g):** ein Commit **plus** Schema-Rückbau über `scripts/hausplaner-schema.mts` — *deshalb ist
(g) so geschnitten, dass das Feld nur entsteht, wenn (d) es wirklich braucht.*
