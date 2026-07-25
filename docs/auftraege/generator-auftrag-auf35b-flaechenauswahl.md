# ⇒ GENERATOR-AUFTRAG AUF-35b — Flächenauswahl: Wandseite und Dachfläche

**Vom:** Planner · **26.07.2026** · **Grundlage:** AUF-35a ist abgenommen (`35fbfde`, Votum `134eb0e`).
**Zugeschnitten nach Messung** — der ursprüngliche Posten enthielt auch die Zonenauswahl; sie ist
nicht baubar (§1).

**Vorher gelesen:** HEAD `5886267` · `git log -5` · Tafelzeile AUF-35b ·
`domain/scene.types.ts:96-110` (`WallNode`) · `renderers/three-d/szene.ts:365,398,421,477,566` ·
`store/hausplanerStore.ts:36,38,48` · `app/tools/auswahl{Modus,Darstellung,Uebersicht}.ts` ·
`geometry/dachAusschnitt.ts:34-37`.

**Alle Zahlen gemessen am 26.07.** — Zahlen in Kriterien sind Hypothesen mit Datum, keine Fakten.

---

## 1. Warum der Posten geschnitten ist: Zonen fallen weg

**Gemessen:**

| geprüft | Ergebnis |
|---|---|
| `zoneType` in `commands.types.ts` und `applyCommand.ts` | **0×** — es gibt **kein** Zone-Command |
| `zone` im 3D-Renderer | **0×** — Zonen werden **nicht dargestellt** |
| `scene.types.ts:179` sagt selbst | *„P0 aktiv NUR `zoneType: room` und NUR abgeleitet (Raumerkennung)"* |

**Man kann nicht auswählen, was nicht dargestellt wird.** Die Zonenauswahl braucht zuerst
Darstellung und Commands — beides eigene Posten. **Dieser Auftrag umfasst nur Wandseite und
Dachfläche.**

## 2. Der eigentliche Befund: es fehlt eine Teil-Identität

**Auswahl ist heute knotenweise.** Der 3D-Renderer hängt an jedem Mesh `userData.nodeId`
(Z. 365 Wand · 398 Treppe · 421 Raum · 477 Decke · 566 Dach), der Store hält `selectedNodeIds`.
**Es gibt keine Bezeichnung für einen Teil eines Knotens.**

- **Eine Wand** trägt `start` · `end` · `thickness` · `height`. Ihre **zwei Seiten sind implizit** —
  links und rechts der Achse — und existieren nirgends als Daten.
- **`surfaceId` existiert**, aber nur **innerhalb** von `geometry/dachAusschnitt.ts:37`
  (`AusschnittBefund`). Im Schema kommt es **nicht** vor.

**Daraus folgt der Zuschnitt:** Dieser Posten baut eine **abgeleitete Teil-Kennung** in der
App-Schicht — **kein Schema, kein Command, keine Persistenz.**

```
"<nodeId>#seite:links" | "<nodeId>#seite:rechts" | "<nodeId>#flaeche:<index>"
```

**Warum kein Schema-Eingriff:** Die Dauerdirektive gilt — persistierte Werte werden nicht
umbenannt, und eine neue persistierte Struktur an Bestandsdaten ist ein Datenvorgang, kein
Auswahlwerkzeug. **Die Teil-Kennung ist Anzeige-Zustand**, genau wie die heutige Auswahl: sie
überlebt kein Neuladen, und das ist richtig so.

## 3. Was gebaut wird

1. **Ableitung der Teile aus dem vorhandenen Knoten** — rein, testbar ohne DOM:
   - Wand → zwei Seiten, aus `start`/`end`/`thickness` gerechnet.
   - Dach → seine Flächen, aus der vorhandenen Dachgeometrie.
   **Keine neue Geometrie-Rechnung.** Was `geometry/` schon kann, wird gelesen, nicht nachgebaut.
2. **Treffertest auf Teile** — nach dem Muster aus AUF-35a (nach `renderOrder`, dann Distanz, mit
   Toleranz). Ein Teil gewinnt nur, wenn das Werkzeug Flächen wählt; sonst bleibt es beim Knoten.
3. **Darstellung** über die vorhandene `auswahlDarstellung` — **eine** Wahrheit über Hervorhebung,
   erweitert um den Teil-Fall, nicht daneben gestellt.
4. **Die Übersicht** (`auswahlUebersicht`) nennt den Teil im Klartext: „Wand 3 · Seite innen",
   nicht `wall-7#seite:links`.

## 4. Was **nicht** gebaut wird

- **Kein Schema-Eingriff, kein Command, kein Undo.** Auswahl ist kein Modellzustand — so hat es
  AUF-35a entschieden und der Evaluator abgenommen.
- **Keine zweite Auswahlquelle.** `selectedNodeIds` und `primaerId` bleiben die Wahrheit; die
  Teil-Kennung ist eine **Verfeinerung**, kein paralleler Speicher.
- **Kein Verbrauch.** Dieser Posten **wählt und zeigt**. Dass eine Engine „diese Dachfläche" als
  Eingang nimmt, ist die Naht zu L3 — und wird dort gebaut, nicht hier.
- **Keine Zonen** (§1).

## 5. Abnahmekriterien

1. `tsc:hausplaner` · `schema:hausplaner:check` · `test:hausplaner` — Exit 0, Zahlen vorher/nachher.
2. **K4 unberührt:** `store/`, `domain/`, `geometry/`, `renderers/`, `scene.types` — null Zeilen.
   *(`renderers/` ausdrücklich: das Picking darf die Teil-Kennung liefern, ohne dass der Renderer
   sie erfindet — wenn das nicht geht, ist das eine Rückgabe, kein Grund, K4 zu brechen.)*
3. **Kein Schema, keine Persistenz:** `grep` belegt, dass die Teil-Kennung in keinem Command, keinem
   Zod-Schema und keinem gespeicherten Feld vorkommt.
4. **Rein und ohne DOM testbar:** die Ableitung liefert für dasselbe Dokument zweimal dasselbe
   Ergebnis (tiefer Vergleich).
5. **Zwei Seiten je Wand, deterministisch:** links und rechts sind **stabil** benannt — dieselbe Wand
   liefert nach Neuladen dieselbe Zuordnung. Test mit mindestens drei Wandrichtungen, davon eine
   senkrechte.
6. **Der Knoten bleibt wählbar:** mit einem Werkzeug, das keine Flächen wählt, ändert sich am
   Verhalten von AUF-35a **nichts** — testverriegelt.
7. **Klartext, kein Schlüssel:** Test belegt, dass die Übersicht keine rohe Teil-Kennung anzeigt.
8. **Mutations-Gegenbeweis:** die Seitenzuordnung spiegeln ⇒ mindestens ein Test rot. Zahl nennen.
9. **`public/*` im Code-Commit: null Zeilen**, Bundle-Rebuild als eigener zweiter Commit (§8 2b).
10. **Klassifikation: `sichtbar`.** Sichtprobe in die Abnahme: eine Wandseite anklicken, sehen, dass
    **eine** Seite hervorgehoben ist und nicht die ganze Wand.

## 6. Was zurückgegeben wird statt mitgebaut

- **Liefert das Picking keine Teil-Information**, ohne dass `renderers/` geändert wird: **melden.**
  Dann ist der Renderer-Anteil ein eigener Posten — und dieser hier endet mit der Ableitung und der
  2D-Auswahl. **Ein halber Posten mit Begründung ist besser als ein ganzer mit gebrochenem K4.**
- **Zeigt sich, dass „Seite innen/außen" ohne Raumbezug nicht bestimmbar ist** (eine Wand kennt ihre
  Innenseite nicht von allein): benennen. Dann heißen die Seiten geometrisch (links/rechts der
  Achsrichtung), und die fachliche Benennung wird ein eigener Posten mit der Raumerkennung.
