# W-42 · Schreibpfad Wizard → Gebäudemodell — PRÜFUNG

> **Bei einer Ablesung wäre „rot" nicht der fehlende Code, sondern die falsche Ablesung** — *und in
> diesem Fall ist die falsche Ablesung bereits zweimal passiert. Siehe `7-GRENZEN.md`.*

## Abnahmekriterien

| Nr | Kriterium | Wodurch wäre es rot | Wie gemessen |
|---|---|---|---|
| K-1 | Der Schreibpfad ist **gebaut**, mit den drei Stellen und vier Arten | „schreibt nichts ins Modell" | `:184` `:205` `:226`, am Bau-Stand |
| K-2 | **Beide** überholten Quellen wörtlich, mit gemeinsamer Ursache | eine davon weglassen | `ConfigWizard.tsx:6` und `BERICHT-…:184-185` |
| K-3 | Die **zwei Wege** je mit ihrer Bedingung | den Download für den Regelfall halten | `&& scene` (`:174`) gegen `:244-247` |
| K-4 | Die **drei ungemessenen** Punkte stehen als Frage | eine davon beantworten | `7-GRENZEN.md` |
| K-5 | Die Grenze zu W-35 steht in `2-FUNKTION` | sie weglassen — beide leben in **einer** Datei | dort benannt |

## Automatische Tests

> **BERICHTIGT vor der Fertigmeldung — hier stand, kein Test prüfe den `ADD_NODE`-Weg.** *Das war
> falsch, und ich habe es beim Nachmessen selbst gefunden:* **`grep -rl 'ADD_NODE' __tests__/`
> nennt fünf Dateien, darunter `configWizardWrite.test.ts` — den Test genau für diesen Pfad.**
> *Ich hatte drei Paket-Tests aufgezählt und daraus auf eine Lücke geschlossen, statt zu messen,
> welche Datei den Gegenstand berührt.*

**`__tests__/configWizardWrite.test.ts` · 85 Zeilen · 3 Tests — der Schreibpfad selbst:**

```text
· ConfigWizard-Schreiblogik: Fenster mit Bauart landet als OpeningNode auf der …
· ConfigWizard-Schreiblogik: Treppe landet als ObjectNode(stair) mit typ im Mo…
· ConfigWizard-Schreiblogik: Heizkoerper landet als ObjectNode(radiator) mit ob…
```

**Drei Tests, drei Bauteilarten — genau die drei Schreibstellen aus `2-FUNKTION.md`.**

**`__tests__/konfiguratorEhrlich.test.ts` — die Ehrlichkeit der Meldung:**

```text
K3: die Meldung nach dem Klick sagt, was WIRKLICH geschehen ist — Weg fuer Weg
K6: der Uebernahme-Weg ins Modell ist unberuehrt — er war schon wahr
K5: kein „folgt", kein „in Kuerze", kein „geplant", kein „demnaechst"
```

> **K6 ist der Satz, der zu diesem Blatt gehört:** *„der Übernahme-Weg ins Modell ist unberührt — er
> war schon wahr".* **Jemand hat die Ehrlichkeit dieses Pfades bereits geprüft, bevor die Werkbank
> ihn beschrieben hat.**

| Datei | Bezug |
|---|---|
| **`configWizardWrite.test.ts`** | **unmittelbar** — die drei Schreibstellen |
| **`konfiguratorEhrlich.test.ts`** | **unmittelbar** — beide Wege, die Meldung, K6 |
| `paketSpeichern.test.ts` · `configuratorPackage.test.ts` | der **Paket**-Weg — *nicht W-42* |
| `breiten` · `dialogFokus` · `stilschicht` | geteilt, die ganze Insel |

**Die Quelle führt den ConfigWizard-Test unter „nicht gemessen".** *Hier ist er gemessen: er
existiert, er heißt `configWizardWrite`, und er prüft genau den Weg, den beide überholten Quellen
für nicht vorhanden hielten.*

## Fangprobe (Mutationsprobe)

| Mutation | Muss erkannt werden von |
|---|---|
| den Knotentyp einer Bauteilart vertauschen | **`configWizardWrite.test.ts`** — *je Art ein Test auf `OpeningNode` / `ObjectNode(stair)` / `ObjectNode(radiator)`* |
| die Meldung „ins Modell gesetzt" beim `false`-Zweig zeigen | **`konfiguratorEhrlich.test.ts` K3** — *„sagt, was WIRKLICH geschehen ist"* |
| eine Vertröstung in die Meldung schreiben | **`konfiguratorEhrlich.test.ts` K5** |
| `&& scene` aus `:174` entfernen | *unklar — nicht gemessen* |
| `executeCommand` durch einen Direktzugriff ersetzen | *unklar — nicht gemessen* |

> **Alle fünf sind ABGELESEN, nicht gefahren.** *Die ersten drei nennen einen Wächter, den ich
> geöffnet habe; die letzten beiden sagen ausdrücklich **unklar**, weil ich nicht gemessen habe, ob
> ein Test sie fängt.* **Ein „kein Test" ohne Messung ist genau der Fehler, der eine Zeile weiter
> oben schon einmal in diesem Blatt stand.**

## Sichtprüfung

- [ ] Nach dem Übernehmen steht das Bauteil sichtbar im Plan
- [ ] Die Meldung nennt beides: **gesetzt** und **verschiebbar**
- [ ] **Ohne geladenes Gebäude** wird heruntergeladen und **nichts** über einen Einbau behauptet
- [ ] 1440 px · 1024 px · 375 px

> **Die Sichtprüfung ist hier nicht Zierrat, sondern der einzige Nachweis** — *weil kein
> automatischer Test den Weg ins Gebäude berührt.*

## Bestandsprobe

- [ ] **Ein vor der Übernahme gespeichertes Dokument lädt danach mit dem neuen Bauteil**
- [ ] **Rückgängig entfernt es wieder** — *`executeCommand` verspricht das; gemessen ist es nicht*
