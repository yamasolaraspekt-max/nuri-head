# Z1-W2-4 — Probe: die Treppe über den `werkzeugRegistry`-Vertrag

**Art:** Probe mit zwei gültigen Ausgängen. **Es entsteht keine Bedienung, und es bleibt kein Code.**
**Blattstand:** `docs/auftraege/aktiv/Z1-W2-4-treppe-ueber-werkzeugregistry-vertrag.md` @ `rolle/planner 97277281`
**Basis:** `ba6fc673` · **Ort des Aufbaus:** Wegwerf-Verzeichnis unter `TMPDIR` (A-37-22d)

---

## Z1-W2-4-a · Die echte Treppe wurde registriert

`registriereWerkzeug` hat **0 Produktivaufrufer** — selbst gemessen, mit Klammer, ohne Testdateien
und ohne die Moduldatei. Der Probeaufbau bindet **`berechneTreppe`** aus
`geometry/treppenBerechnung` ein, nicht eine Attrappe:

```ts
registriereWerkzeug<TreppenEingabe>({
  kind: 'stair', schemaVersion: 1, kategorie: 'bau', kuerzel: 'R',
  parametrik: (d) => berechneTreppe(d),
  faehigkeiten: { waehlbar: true, ziehbar: true, dupliziert: true, loeschbar: true },
});
```

## Z1-W2-4-c · Der Aufbau LÄUFT — Ausgang 1

**Übersetzung:** `tsc --strict` → **rc 0, 0 Fehler.** *Direkt gemessen, nicht hinter einer Pipe —
ein `$?` nach `| head` ist der Rückgabewert von `head`.*

**Ausführung** (`node --experimental-strip-types`, derselbe Läufer wie die bestehenden Proben;
**kein Vitest, den es hier nicht gibt**), Eingabe `{ geschosshoehe: 2800, laufbreite: 1000,
bereich: 'wohnung' }`:

```
anzahlSteigungen 16 · anzahlAuftritte 15 · steigungshoehe 175 · auftritt 280
lauflaenge 4200 · schrittmass 630 · bequemlichkeit 105 · sicherheit 455
pruefungen [6 Einträge] · bestanden true
```

**Warum es überhaupt übersetzt** — das ist der eigentliche Fund: TypeScript ist **strukturell**
typisiert. `TreppenErgebnis` besitzt `bestanden: boolean`, und damit **erfüllt es `Parametrik`**.
Der Vertrag nimmt die Treppe an, ohne dass jemand etwas anpasst.

## Z1-W2-4-b · Der Verlust, Feld für Feld

**Er liegt nicht in der Laufzeit — dort kommt alles an. Er liegt am Typ.** Ein Konsument, der nur
den Vertrag kennt (`werkzeug('stair')?.parametrik(d)`), bekommt `Parametrik` zurück:

```
konsument.ts(16,21): error TS2339: Property 'anzahlSteigungen' does not exist on type 'Parametrik'.
konsument.ts(17,22): error TS2339: Property 'pruefungen'      does not exist on type 'Parametrik'.
```
*Gegenprobe, dass der Befehl nicht generell fehlschlägt: derselbe Lauf auf `direkt.ts` → rc 0, 0 Fehler.*

| # | Feld aus `TreppenErgebnis` | ohne Adapter | mit Adapter in `kennwerte` |
|---|---|---|---|
| 1 | `anzahlSteigungen: number` | **verkürzt** — zur Laufzeit da, am Typ unsichtbar | trägt |
| 2 | `anzahlAuftritte: number` | **verkürzt** | trägt |
| 3 | `steigungshoehe: number` | **verkürzt** | trägt |
| 4 | `auftritt: number` | **verkürzt** | trägt |
| 5 | `lauflaenge: number` | **verkürzt** | trägt |
| 6 | `schrittmass: number` | **verkürzt** | trägt |
| 7 | `bequemlichkeit: number` | **verkürzt** | trägt |
| 8 | `sicherheit: number` | **verkürzt** | trägt |
| 9 | `pruefungen: TreppenPruefung[]` | **trägt nicht** | **trägt nicht** |
| 10 | `bestanden: boolean` | **trägt** (direkt `Parametrik.bestanden`) | trägt |

**Nicht abbildbare Felder: 1 von 10.** Belegt, nicht behauptet:

```
adapter.ts(19,7): error TS2322: Type 'TreppenPruefung[]' is not assignable to type 'string | number | boolean'.
```

**Welches Feld das ist, zählt mehr als die Zahl.** `pruefungen` trägt je Prüfung `id`, `schwere`,
`meldung`, `bestanden` — es ist **die DIN-18065-Begründung selbst**. Was bleibt, ist ein nacktes
`bestanden: true`. *Der Vertrag behält das Urteil und verliert die Begründung* — und genau
diese Begründung ist es, die im Panel als „Steigung 175 mm ≤ zulässig 200 mm (wohnung)" steht.

## Z1-W2-4-d · Der Vergleich mit dem Vertrag, der läuft

Zwei Verträge beschreiben dieselbe Zuordnung. **Einer ist verdrahtet, der andere nicht:**

```
faehigkeiten.ts:84    engine-treppe · eingang TreppenEingabe · ausgang TreppenErgebnis
                      engineModul geometry/treppenBerechnung · engineExport berechneTreppe
enginePanels.ts:121   engineId 'engine-treppe' · Titel, Zweck, Grundlage, Felder mit Vorgaben
```

**Der laufende Vertrag trägt `TreppenErgebnis` als Ganzes** — er nennt es namentlich als `ausgang`
und verliert nichts.

Die vier Felder, die **nur** `WerkzeugNode` hat — und was der Bestand dafür bereits führt:

| Feld | im Bestand vorhanden? | wo |
|---|---|---|
| `faehigkeiten` | **teilweise** | `app/tools/trefferSuche.ts:34` führt `waehlbar?: boolean` |
| `migrate` / `schemaVersion` | **ja, aber szenenweit** | `domain/validation.ts:308` `migriereSzene` |
| `kategorie` | **ja** | `faehigkeiten.ts` führt 16 `gruppe:`-Einträge |
| `kind`-Zuordnung | **ja** | `toolRegistry.ts` führt `bauteilKind` an 7 Stellen |

## Z1-W2-4-e · Kein Code hat die Probe verlassen

`git status --porcelain` → **0 Zeilen.** Kein neues Modul, keine Änderung an `werkzeugRegistry.ts`,
`toolRegistry.ts`, `enginePanels.ts` oder einem Treppen-Modul. Der gesamte Aufbau lag unter
`TMPDIR` und ist dort geblieben.

## Z1-W2-4-f · EMPFEHLUNG (Empfehlung, keine Entscheidung)

### `STILLLEGUNG BEGRÜNDET`

**Sie stützt sich nicht auf die Nichtbenutzung.** Dass `registriereWerkzeug` 0 Aufrufer hat, ist der
**Anlass** dieser Probe, nicht ihr Ergebnis — *der Ort sagt nichts über die Wirkung.* Die Begründung
sind die drei Messungen:

1. **Der Vertrag verliert genau das Feld, dessentwegen es ihn gibt** (b). Sein eigener Kommentar
   nennt als Zweck *„alle harten Fachprüfungen … (z. B. DIN 18065 bei der Treppe)"* — und
   `pruefungen` ist das einzige der zehn Felder, das er nicht aufnehmen kann, auch nicht mit Adapter.
2. **Der laufende Vertrag trägt die Treppe bereits vollständig** (d) — `faehigkeiten.ts:84`
   deklariert `TreppenErgebnis` als `ausgang`, `enginePanels.ts:121` ruft es auf.
3. **Die vier Zusatzfelder haben im Bestand bereits Träger** (d) — teils anders geschnitten, aber
   vorhanden. Keines davon ist ein Zuwachs, den nur `WerkzeugNode` liefern könnte.

**Was dagegen spricht und offen bleibt:** Der Weg **funktioniert** — Übersetzung und Ausführung
sind grün. Wer `kennwerte` auf `unknown` erweiterte, machte den Vertrag tragfähig. **Das Blatt
untersagt das ausdrücklich**, und mit Grund: es machte ihn `EngineErgebnis` gleich und beantwortete
die Doppelungsfrage, indem es sie herstellt.

**Die Stilllegung selbst ist nicht Teil dieses Blattes.** Sie ist der Folgeschritt und braucht ein
eigenes Blatt.
