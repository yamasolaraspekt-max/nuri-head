# Z1-E2-1 — Etagen-Integrität: keine Etagen-Operation verliert oder verdoppelt ein Bauteil

**ZIEL:** Eine Etage löschen lässt keine verwaiste Decke zurück; eine Etage duplizieren nimmt ihre
Decke mit — **mit eigener `sortOrder` und eigener Elevation.**

```yaml
auftrag: "Z1-E2-1"
scheibe: "E2 — Etagen-Integritaet (schnellster Nutzen)"
spur: A
art: "INTEGRITAET — Wachen und Mitnahme. KEIN Modell, KEIN Schema, KEINE neue Bedienung."
heimat_app: ticket
heimat_code: resources/planner/hausplaner
mess_sha: fd2575ce
konzept: "docs/konzept/etagenweiser-aufbau.md @ 8e4bb918 — Scheibe E2, Luecke L4"
kennung_geprueft: "Z1-E2-1: docs/ 0 Treffer, git log --all --grep 0. Kennungsraum Z1-E* neu."
dor_beleg: "ERTEILT — plan-pruefer 2026-08-22T19:30:30, Beleg 3ddf6a3e
            (plan-pruefer-DOR-Z1-E0-1-und-Z1-E2-1-ERTEILT.yaml, §436), OHNE Halbsaetze.
            Geprueft gegen mess_sha = basis_sha = fd2575ce, Blob identisch auf HEAD und
            rolle/planner — kein Drift. (Kopf hing bis 19:5x auf 'steht aus'.)"
basis_sha: fd2575ce
prioritaet: P0
ballbesitz: "generator (DoR erteilt — baubar; laut Dirigent 19:11:46 VOR Z1-W2-6 und Z1-W2-4)"
zielreifegrad: "ABGENOMMEN (BROWSER)"
```

## Die Lücke L4, am Stand `fd2575ce` selbst nachgemessen

```
1  LOESCHEN ignoriert die Decke        commands/applyCommand.ts:396 'REMOVE_LEVEL'
     :406  const hatDach = Array.isArray(draft.roofs) && draft.roofs.some(r => r.levelId …)
     :407  if (hatNodes || hatDach) { … }
     GEPRUEFT WERDEN nodes UND roofs — 'ceilings' KOMMT IN DER WACHE NICHT VOR.
     -> Eine Etage mit Decke laesst sich loeschen; die CeilingNode bleibt verwaist zurueck.

2  DUPLIZIEREN vergisst die Decke      app/sammelBefehle.ts:122-134
     dup: { level: Level; nodes: SceneNode[]; roof: RoofNode | null }
     Die Signatur kennt level, nodes und roof — KEIN ceiling.
     geometry/geschossVorlage.ts:75    const neuesDach = roof ? {…} : null   — nur das Dach

3  FREMDSCHLUESSEL nur fuer nodes      domain/validation.ts:368-373
     const levelIds = new Set(doc.levels.map(l => l.id));
     for (const n of doc.nodes) { if (!levelIds.has(String(n.levelId))) fehler.push(…) }
     -> Eine verwaiste Decke faellt beim Laden nicht auf.
```

> **Das Muster, an dem der Bau sich orientieren kann, steht direkt daneben:** `hatDach` (`:406`)
> macht für Dächer genau das, was für Decken fehlt. *Es ist keine neue Wache zu erfinden — es ist
> eine vorhandene um einen Bauteiltyp zu erweitern.*

---

## Abnahmekriterien (aus dem Konzept; Messbefehle ergänzt, nichts abgeschwächt)

- **Z1-E2-1-a** · **ETAGE MIT DECKE LÖSCHEN → ABLEHNUNG, SICHTBAR.**

  **Verlangt:** `REMOVE_LEVEL` prüft `ceilings` wie heute `roofs`. Ablehnung mit
  `level_nicht_leer`, **im Browser lesbar** — nicht nur in der Konsole.

  **Messbefehl:**
  ```
  grep -nE 'draft.ceilings' commands/applyCommand.ts   im REMOVE_LEVEL-Block  ->  >= 1
  Browser: Etage mit Decke loeschen -> Meldung sichtbar, Etage BLEIBT, Bildbeleg
  ```

  **Heutiges (rotes) Ergebnis:** `:406-407` prüft `hatNodes || hatDach`; **`ceilings` kommt nicht
  vor** — die Etage verschwindet, die Decke bleibt.

  **Absage-Regel:** Eine Ablehnung ohne sichtbaren Grund erfüllt (a) **nicht.** *Der Benutzer drückt
  sonst zweimal und hält es für einen Fehler.*

- **Z1-E2-1-b** · **EG DUPLIZIEREN → OG HAT DIE DECKE, MIT EIGENER `sortOrder` UND ELEVATION.**

  **Verlangt:** `sammelBefehle.ts` und `geschossVorlage.ts` nehmen die Decke mit — **neue Id, neue
  `levelId`**, wie heute das Dach (`geschossVorlage.ts:75`).

  **Messbefehl:**
  ```
  Browser: EG mit Decke duplizieren -> OG zeigt eine Decke (2D + 3D), Bildbeleg
  im Dokument: zwei CeilingNodes, VERSCHIEDENE ids, VERSCHIEDENE levelIds
  die beiden Level: verschiedene sortOrder UND verschiedene elevation
  ```

  **Heutiges (rotes) Ergebnis:** `dup: { level, nodes, roof }` — **kein `ceiling`**; das duplizierte
  Geschoss hat keine Decke.

  **Absage-Regel:** Dieselbe `sortOrder` für beide Level erfüllt (b) **nicht** — *dann ist die
  Reihenfolge der Etagen nicht mehr entscheidbar, und E0 rechnet auf einer mehrdeutigen Kette.*

- **Z1-E2-1-c** · **EINE VERWAISTE DECKE WIRD BEIM LADEN BENANNT.**

  **Verlangt:** `validation.ts` prüft den Fremdschlüssel `levelId` **auch für `ceilings` und
  `roofs`**, nicht nur für `nodes`. **Benannt, nicht still akzeptiert.**

  **Messbefehl:**
  ```
  Dokument mit einer CeilingNode auf einer nicht existierenden levelId laden
    -> Fehlermeldung nennt die Decke und das unbekannte Level, WOERTLICH im Bericht
  ```

  **Heutiges (rotes) Ergebnis:** `:368-373` läuft nur über `doc.nodes`.

  **Absage-Regel:** Ein stiller Ausschluss der verwaisten Decke erfüllt (c) **nicht** — *ein
  Dokument, das leise repariert wird, verliert seinen Befund.*

- **Z1-E2-1-d** · **BESTANDSDOKUMENTE LADEN UNVERÄNDERT.**

  **Messbefehl:** Referenzhaus-Fixture und ein Bestandsdokument ohne Decke → laden zeichengleich,
  keine neue Meldung.

  **Absage-Regel:** *Eine schärfere Prüfung, die Altbestand ablehnt, ist keine Härtung, sondern ein
  Ausfall.*

- **Z1-E2-1-e** · **DIE LIEFERUNG IST GRÜN UND VOLLSTÄNDIG.**
  `tsc:hausplaner` → **0** · `test:hausplaner` → 0 fail · **Bündel gebaut und mitcommittet.**

- **Z1-E2-1-f** · **KEIN MODELL-, KEIN SCHEMA-DIFF.**

  **Messbefehl:**
  ```
  git diff <basis_sha>..<endstand_sha> -- domain/scene.types.ts             -> LEER
  git diff <basis_sha>..<endstand_sha> -- '*scene-document-v2.schema.json'  -> LEER
  ```
  *`domain/validation.ts` **darf** sich ändern (Kriterium c) — `scene.types.ts` nicht.*

  **Absage-Regel:** `git diff` **ohne beide SHA** erfüllt (f) nicht (§421, Halbsatz 1).

## Nicht-Ziele

- **Kein neues Feld.** Die Wachen arbeiten auf dem, was das Modell heute führt.
- **Keine Bodenplatte** — sie kommt in **E4** und bekommt dort ihre eigene Integritätsregel.
- **Keine Änderung an der Dach-Wache.** `hatDach` ist das **Muster**, nicht der Gegenstand.
- **Kein automatisches Aufräumen verwaister Decken.** *(c) verlangt sie zu **benennen**; sie zu
  löschen wäre eine Datenänderung ohne Auftrag.*

## Nachvollzugs-Matrix (§5 / N3)

| Kriterium | Arbeitspaket | Commit-SHA | Testbeleg |
|---|---|---|---|
| a Löschen lehnt ab, sichtbar | AP-1 `REMOVE_LEVEL`-Wache | n.U. | n.U. |
| b Duplizieren nimmt die Decke mit | AP-2 `sammelBefehle` + `geschossVorlage` | n.U. | n.U. |
| c verwaiste Decke wird benannt | AP-3 `validation.ts` | n.U. | n.U. |
| d Bestand lädt unverändert | AP-3 (Fixture) | n.U. | n.U. |
| e `tsc`/Suite/Bündel | AP-4 Lieferung | n.U. | n.U. |
| f kein Modell-/Schema-Diff | AP-4 Schutzbeleg | n.U. | n.U. |

## Rückweg

**Revert eines Commits.** Bestandsdokumente unberührt: die Wachen sind additiv, es entsteht kein
Zustand und keine Migration.
