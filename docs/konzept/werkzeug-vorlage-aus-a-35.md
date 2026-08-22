# Werkzeug-Vorlage — die zwölf Stellen aus A-35 „Trimmen"

```yaml
art: "VORLAGE — Pflichtanhang jedes Spur-W-Blatts. Keine Entscheidung, kein Bau."
rolle: planner
auftrag: "SPEZ-planner-anschlusswelle-1 gen 18, Posten 6 (b)"
quelle: "A-35 Trimmen, Bau-Commit ec12e9b3 (2026-08-15) — das letzte Werkzeug, das vollstaendig
         entstanden ist. Gemessen, nicht erinnert: 12 files changed, 894 insertions, 219 deletions."
mess_sha: 9305198b
zweck: "Wer ein Werkzeug anschliesst oder baut, hakt diese Liste ab. Was hier fehlt, faellt spaeter
        in der Abnahme auf — oder gar nicht, und das ist schlimmer."
```

## Warum diese Vorlage aus einem echten Bau kommt und nicht aus einer Überlegung

**A-35 ist das letzte Werkzeug, das vollständig entstanden ist.** Sein Bau-Commit zeigt, was
tatsächlich anzufassen war — nicht, was man für nötig hält. *Eine Vorlage aus dem Kopf nennt die
drei offensichtlichen Stellen; dieser Commit nennt zwölf.*

## Die zwölf Stellen

### A · Das Werkzeug selbst — 2 Stellen, 515 Zeilen

| # | Datei | ± | Was |
|---|---|---|---|
| 1 | `app/tools/trimmen.ts` | **+224** | **neu** — die Fachlogik des Werkzeugs |
| 2 | `__tests__/trimmen.test.ts` | **+291** | **neu** — seine eigene Suite |

*Die Suite ist größer als das Werkzeug. Das ist kein Ausreißer, sondern das Verhältnis, das ein
Werkzeug mit Grenzfällen erzeugt.*

### B · Der Anschluss — 3 Stellen, 67 Zeilen

| # | Datei | ± | Was |
|---|---|---|---|
| 3 | `app/tools/toolRegistry.ts` | 29 | Eintrag: `id`, `label`, `art`, `shortcut`, `groupId`, `supportedWorkspaces`, `supportedViews`, `helpText`, `disabledReasonDefault` |
| 4 | `app/HausplanerApp.tsx` | **+35** | Verdrahtung in der Insel — hier wird das Werkzeug bedienbar |
| 5 | `app/tools/toolPresentation.ts` | 3 | Darstellung in der Leiste |

**Ohne Stelle 4 gibt es einen Registry-Eintrag und kein Werkzeug.** *Das ist genau der Zustand, den
die Anschlusswelle beseitigt: Fachlogik da, Eintrag da, Weg zum Benutzer fehlt.*

### C · Die Fachlogik darunter — 1 Stelle, 51 Zeilen

| # | Datei | ± | Was |
|---|---|---|---|
| 6 | `geometry/geradenGeometrie.ts` | 51 | die geometrische Grundlage, erweitert |

*Ein Werkzeug bringt selten seine ganze Mathematik mit — es erweitert eine vorhandene Grundlage.*

### D · Die nachziehenden Tests — 5 Stellen, 80 Zeilen · **der Teil, den man vergisst**

| # | Datei | ± | Warum betroffen |
|---|---|---|---|
| 7 | `__tests__/toolRegistry.test.ts` | 10 | zählt Einträge / prüft Vollständigkeit |
| 8 | `__tests__/gehobeneWerkzeuge.test.ts` | 23 | prüft Werkzeugklassen |
| 9 | `__tests__/naechsterSchritt.test.ts` | 35 | Ablauflogik kennt die Werkzeugmenge |
| 10 | `__tests__/rechte.test.ts` | 9 | Rechteprüfung je Werkzeug |
| 11 | `__tests__/toolPresentation.test.ts` | 3 | Darstellungszusagen |

> **Fünf von zwölf Stellen sind Tests, die das neue Werkzeug gar nicht prüfen** — sie sind von
> seinem Hinzukommen *betroffen*. **Wer sie übersieht, hat eine rote Suite und sucht den Fehler im
> neuen Code.** *Das ist der eigentliche Wert dieser Vorlage.*

### E · Das Bundle — 1 Stelle, 400 Zeilen

| # | Datei | ± | Was |
|---|---|---|---|
| 12 | `public/hausplaner/hausplaner.js` | **400** | das gebaute Bündel, mit im Commit |

**Offene Frage, hier benannt und nicht beantwortet:** ob dieses Bündel bei jedem Bau mitcommittet
werden **muss** oder ob es auf dem Zielsystem erzeugt wird, ist **nicht geklärt**. *`gen 18`,
Posten 7 führt das ausdrücklich als „messen".* **Bis dahin: wer ein Werkzeug baut, prüft, ob sein
Commit dieses Bündel enthalten muss — und begründet beide Antworten.**

---

## Die Abhakliste

```
[ ]  1  Fachlogik des Werkzeugs           app/tools/<name>.ts
[ ]  2  eigene Suite                      __tests__/<name>.test.ts
[ ]  3  Registry-Eintrag                  app/tools/toolRegistry.ts
[ ]  4  Verdrahtung in der Insel          app/HausplanerApp.tsx      <- ohne dies kein Werkzeug
[ ]  5  Darstellung                       app/tools/toolPresentation.ts
[ ]  6  Fachliche Grundlage erweitert     geometry/<grundlage>.ts    (falls noetig)
[ ]  7  toolRegistry.test.ts              nachziehen
[ ]  8  gehobeneWerkzeuge.test.ts         nachziehen
[ ]  9  naechsterSchritt.test.ts          nachziehen
[ ] 10  rechte.test.ts                    nachziehen
[ ] 11  toolPresentation.test.ts          nachziehen
[ ] 12  public/hausplaner/hausplaner.js   Bundle — Frage offen, s. o.
```

**Nicht jede Stelle trifft jedes Werkzeug.** Eine Prüfung ohne Leisteneintrag (Paket 3 der
Anschlusswelle) braucht 3 und 5 nicht — **aber sie muss das sagen**, statt die Stelle stillschweigend
auszulassen. *Eine abgehakte Liste mit Begründung ist ein Beleg; eine gekürzte Liste ist keiner.*

## Was diese Vorlage nicht ist

- **Keine Bauanleitung.** Sie sagt **wo**, nicht **wie**.
- **Keine Zusage über Zeilenzahlen.** Die 894 Zeilen von A-35 sind der Umfang *eines* Werkzeugs mit
  Zwei-Objekt-Logik; ein Anschluss ohne neue Fachlogik ist ein Bruchteil davon.
- **Keine Regel.** Sie wird Pflichtanhang, weil `gen 18` Posten 6 (b) es anordnet — nicht, weil sie
  sich selbst dazu erklärt.
