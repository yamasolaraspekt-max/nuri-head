# ⇒ GENERATOR-AUFTRAG AUF-76 — Die Wand bekommt ihre Schichten

**Vom:** Planner · **26.07.2026, 10:00** · **Spur A** · **Heimat-App:** `ticket`
**Tor 1: von Yama freigegeben** (26.07.) — Schema-Erweiterung, additiv, nach dem Muster der Decke.
**Grundlage:** `docs/planner/bestandsaufnahme-mengenermittlung-2026-07-26.md` §8.

**Vorher gelesen:** HEAD `6cfea37` · `domain/scene.types.ts:96-112` (WallNode), `:302-315`
(CeilingNode) · `domain/scene-document-v2.schema.json` · `geometry/wandaufbau.ts:9-15` ·
`domain/validation.ts`.

---

## 1. Warum das gebraucht wird

**Yama hat entschieden: es werden beide Bezugsmaße geführt, Rohbau und Fertig.**

**Gemessen ist „fertig" für Wände heute nicht berechenbar:**

```
WallNode     thickness: number       ← EINE Zahl, keine Schichten
CeilingNode  schichten?: Array<{ materialId?, dickeMm }>   ← die Decke hat es
```

`geometry/wandaufbau.ts` kennt `Schicht { dicke, lambda }` und rechnet U-Werte — **aber die
Schichten hängen an keiner Wand.** Ohne sie weiß niemand, welcher Teil der 300 mm Konstruktion ist
und welcher Putz.

## 2. Was gebaut wird — ein Feld, feldgleich mit der Decke

```ts
/** Wandaufbau (Mengenermittlung M0) — feldgleich mit CeilingNode.schichten. */
schichten?: Array<{ materialId?: string; dickeMm: number }>;
```

**Kein neuer Mechanismus.** `roofs`, `ceilings` und `aufbauten` sind alle **additiv** an dieses
Schema gekommen, ohne Migrationszwang. Dies ist der vierte Fall desselben Musters.

**Dazu das Schema:** `domain/scene-document-v2.schema.json` bekommt das Feld **optional**, und
`schema:hausplaner:check` bleibt grün.

## 3. Was **nicht** gebaut wird

- **Keine Rechnung.** Dieser Posten legt ein Feld an. Wer damit rechnet, ist **AUF-77**.
- **Kein umbenannter Wert.** `thickness` bleibt, was es ist, und bleibt die Wahrheit für den Rohbau.
  **Dauerdirektive.**
- **Keine Pflicht.** Das Feld ist optional; ein Dokument ohne Schichten ist gültig.
- **Keine Migration**, kein Schreiben an Bestandsdaten, keine Vorbelegung mit Standardaufbauten.
  *Eine erfundene Schichtung wäre schlimmer als keine — sie sähe aus wie eine Angabe.*
- **Keine Verbindung zu `wandaufbau.Schicht`.** Das ist ein Rechentyp mit `lambda`; dies ist ein
  Modellfeld mit `dickeMm`. **Sie zusammenzuziehen ist eine eigene Entscheidung** — hier wird das
  Muster der **Decke** kopiert, nicht das der U-Wert-Rechnung.
- **Kein Anfassen von `store/`, `geometry/`, `renderers/`.**

## 4. Abnahmekriterien

1. `tsc:hausplaner` · `schema:hausplaner:check` · `test:hausplaner` — Exit 0, Zahlen vorher/nachher.
2. **Nur `domain/` berührt:** `store/`, `geometry/`, `renderers/`, `app/` — **null Zeilen**.
3. **Kein persistierter Wert umbenannt:** `grep` belegt, dass `thickness`, `height`, `type`,
   `objectType`, `zoneType`, `routeType` unverändert sind.
4. **Bestandsdokument ohne das Feld bleibt gültig:** Test mit einem v2-Dokument **ohne** `schichten`
   ⇒ Validierung grün, **kein 422**, keine Migration.
5. **Dokument mit Schichten wird angenommen:** Test mit zwei Schichten ⇒ grün, Werte unverändert
   nach Speichern und Laden (Rundlauf).
6. **`dickeMm` ist eine ganze Zahl > 0:** Test mit `0`, `-5` und `12.5` ⇒ jeweils abgelehnt.
   *(Ganze mm, wie im übrigen Schema.)*
7. **Feldgleich mit der Decke:** Test vergleicht die Feldnamen von `WallNode.schichten` und
   `CeilingNode.schichten` — **identisch.** *Zwei Schreibweisen für dieselbe Sache wären der Anfang
   der zweiten Wahrheit.*
8. **Mutations-Gegenbeweis:** das Feld im Schema auf `required` setzen ⇒ Kriterium 4 wird rot.
9. **Klassifikation: `Vorarbeit`.** Für den Nutzer ändert sich nichts.

## 5. Was zurückgegeben wird

- **Soll die Summe der Schichten gegen `thickness` geprüft werden?** *Nicht in diesem Posten.* Ob
  eine Wand mit 300 mm und 320 mm Schichten ein Fehler oder eine zulässige Überdeckung ist, ist eine
  **Fachfrage für Yama** — melden, nicht entscheiden. **Bis dahin wird nichts erzwungen.**
- **Braucht die Reihenfolge der Schichten eine Bedeutung** (innen → außen): benennen. Sie liegt
  nahe, aber sie ist eine Festlegung und gehört in den Kommentar des Feldes, wenn sie getroffen wird.
