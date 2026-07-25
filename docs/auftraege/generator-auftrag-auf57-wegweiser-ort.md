# ⇒ GENERATOR-AUFTRAG AUF-57 — Der Wegweiser braucht einen Anlass und einen Ort

**Vom:** Planner · **25.07.2026** · **Anlass:** Rückgabe 2 aus AUF-45 — *„Wo ein Hinweis hingehört,
der auf ‚zeichne eine Wand' oder ‚wechsle den Arbeitsbereich' zeigt, ist eine Platzierungsfrage — sie
gehört dem Planner, nicht mir. Der Mechanismus ist bereit; er braucht nur einen Anlass und einen Ort."*

**Vorher gelesen:** HEAD `a2403c4` · `git log -5` · Tafelzeile AUF-57 ·
`app/tools/naechsterSchritt.ts:75-92` · `app/tools/vorbedingungen.ts:129,133,141,228` ·
`app/HausplanerApp.tsx:428-454` **und `:1063`** · Ledger „GENERATOR-BERICHT AUF-45".

**Alle Zahlen gemessen am 25.07.** — Regel seit AUF-45.

---

## 1. Der Befund, und er ist kleiner als erwartet

**Der Mechanismus ist fertig und richtig.** `naechsterSchritt()` zählt, welcher benannte Schritt
gemessen am meisten entsperrt, fragt dafür **dieselbe** Engine ein zweites Mal und schweigt, wenn
nichts etwas löst. Das ist abgenommen (`66128fe`).

**Der Fehler sitzt in einer einzigen Zeile — meiner.** `HausplanerApp.tsx:1063`:

```ts
wegweiser={wegweiser?.grund === 'Kein aktives Geschoss.' ? wegweiser.satz : null}
```

Die Anzeige ist **auf genau einen Grund hartkodiert** — und zwar auf den, der **nie eintritt**, weil
eine Szene immer ein Geschoss trägt. Der Wegweiser findet also durchaus etwas; es wird nur nichts
davon gezeigt außer diesem einen Fall.

**Die drei benannten Handlungen heute** (`vorbedingungen.ts:129/133/141), gemessen:

| Handlung | Anlass | gemessene Wirkung |
|---|---|---|
| „Lege ein Geschoss an" | `activeLevel.exists` | **feuert nie** — Szene hat immer ein Geschoss |
| „Zeichne eine Wand" | `hostWall.exists` | entsperrt **0** — die betroffenen Werkzeuge hängen zusätzlich am Arbeitsbereich |
| „Wähle ein Bauteil aus" | `selection.count >= 1` | **23** Werkzeuge, der dominante Grund |

**Und der größte Block hat gar keine Handlung:** **28** Werkzeuge sind gesperrt, weil sie einem
**anderen Arbeitsbereich** gehören. Das ist keine fehlende *Fähigkeit*, sondern ein Wechsel — deshalb
findet `handlungZuGrund` dafür nichts, und deshalb schweigt der Wegweiser dort zu Recht.

## 2. Was gebaut wird

### (a) Der Arbeitsbereich wird ein vierter Anlass — ohne zweite Engine

Die hypothetische Bewertung ändert heute **eine Fähigkeit** (`capabilities: [...ctx, faehigkeit]`).
Für den Arbeitsbereich ändert sie stattdessen **ein anderes Feld desselben Kontexts**:

```
{ ...werkzeugKontext, workspace: <anderer Bereich> }
```

**Das ist dieselbe Nachschlage-Operation, nicht eine neue Regel.** Bewertet wird weiter von
`resolveToolState`. Die Guardrail aus AUF-45 („keine zweite Aktivierungsquelle") bleibt damit
unangetastet — und **muss im Bericht erneut belegt werden**.

### (b) Der Ort folgt dem Anlass, statt hartkodiert zu sein

Jede benannte Handlung bekommt **einen** Ort, dort wo die Handlung stattfindet:

| Anlass | Ort |
|---|---|
| Geschoss anlegen | die Geschoss-Fläche (AUF-43) |
| Arbeitsbereich wechseln | der Arbeitsbereich-Wähler (AUF-34) |
| Bauteil auswählen · Wand zeichnen | die Werkzeug-Schiene |

**Kein Hinweisbalken über dem Plan.** Ein Wegweiser, der nicht dort steht, wo man handelt, ist ein
Banner — und Banner werden weggeklickt, nicht gelesen.

**Fällt ein Anlass auf einen Ort, den es nicht gibt: schweigen**, so wie heute. Lieber kein Hinweis
als einer an der falschen Stelle.

## 3. Was **nicht** gebaut wird

- **Keine Sperre wird gelockert.** Wie in AUF-45: die Menge der gesperrten Werkzeuge bleibt
  **identisch**. Dieser Posten ändert nur, was die Oberfläche über sie sagt.
- **Keine zweite Aktivierungsquelle.** Weder für den Arbeitsbereich noch für den Ort.
- **Keine erfundene Handlung.** Ein Grund ohne benannte Handlung bleibt ohne Wegweiser. Wer für
  „Keine Berechtigung" ein „Frag deinen Administrator" erfindet, hat einen Ratschlag gebaut, keinen Schritt.
- **Kein Assistent, keine Tour, kein Mehrschritt-Pfad.** Ein Satz an einem Ort.

## 4. Abnahmekriterien

1. `tsc:hausplaner` · `schema:hausplaner:check` · `test:hausplaner` — Exit 0, Zahlen vorher/nachher.
2. **K4 unberührt:** `store/`, `domain/`, `geometry/`, `renderers/`, `scene.types` — null Zeilen.
3. **Keine zweite Aktivierungsquelle:** `grep` belegt erneut, dass außer `resolveToolState` keine
   Funktion einen Sperrgrund oder eine Entsperrung beurteilt — auch nicht für den Arbeitsbereich.
4. **Die Aktivierung ist unverändert:** Test vergleicht die gesperrten Mengen für drei Kontexte vor
   und nach diesem Posten — **identisch**. (Bezug: **73 / 53 / 28**, gemessen zu AUF-45.)
5. **Der Arbeitsbereich-Anlass zählt richtig:** Test belegt, dass die genannte Zahl die **gemessene
   Differenz** beim Wechsel ist, aus den Daten berechnet, **nicht hartkodiert**.
6. **Kein Grund mehr hartkodiert:** `grep` belegt, dass in `HausplanerApp.tsx` **keine** Zeichenkette
   eines Sperrgrunds mehr als Anzeigebedingung vorkommt. *Das ist die Zeile, die diesen Posten
   ausgelöst hat.*
7. **Ort je Anlass, testverriegelt:** je Handlung ein Test, der belegt, an welcher Fläche der Satz
   erscheint — und dass er **nirgendwo sonst** erscheint.
8. **Schweigen bleibt möglich:** Test mit einem Kontext, in dem kein Schritt etwas löst ⇒ **kein**
   Hinweis, an keinem Ort.
9. **Kein Blindtext:** kein Satz leer, keiner auf „folgt"/„in Kürze", jeder mit einer Zahl.
10. **Mutations-Gegenbeweis:** die Ortszuordnung vertauschen ⇒ mindestens ein Test rot. Zahl nennen.
11. **`public/*` im Code-Commit: null Zeilen**, Bundle-Rebuild als eigener zweiter Commit (§8 2b).
12. **Klassifikation: `sichtbar`.** Sichtprobe in die Abnahme — **mit einem Kontext, in dem der
    Wegweiser wirklich erscheint.** Bei AUF-45 war das der offene Punkt; hier ist es das Kriterium.

## 5. Was zurückgegeben wird

- **Ergibt die Messung, dass auch der Arbeitsbereich-Wechsel gemessen 0 entsperrt**, ist das ein
  gültiges Ergebnis: dann schweigt der Wegweiser weiter, und der Posten endet mit dieser Feststellung.
  **Nichts erfinden, damit etwas erscheint** — genau wie bei AUF-45.
- **Braucht ein Ort eine Fläche, die es nicht gibt:** benennen, nicht bauen.
