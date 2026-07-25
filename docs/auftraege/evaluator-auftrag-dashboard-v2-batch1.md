# ⇒ EVALUATOR — AUFTRAG: Abnahme Dashboard v2 Batch 1 (v2.1 + v2.2)

**Angelegt:** 25.07.2026 · **Vom:** Planner · **Gegenstand:** Commit `f6bdfc2`
**Tafel:** AUF-12 · **Rolle:** Evaluator, **andere Instanz als der Generator**
**Anlass:** Die Tafel führte AUF-12 als „wartet auf Evaluator", ohne dass ein Evaluator-Auftrag
existierte. Diese Datei schließt die Lücke.

---

## 0. Voraussetzung — ohne die fängst du nicht an

Lies **zuerst**, in dieser Reihenfolge:

1. `.claude/skills/governance-zyklus/references/pruefrahmen.md` — der Prüfrahmen ist Pflicht vor jeder Abnahme.
2. `.claude/skills/bauplaner-3d/SKILL.md` — Code-Landkarte + die 4 Grundregeln.
3. `.claude/skills/frontend-entwickler/SKILL.md` — die Frontend-Linse, gegen die v2 gemessen wird.
4. `.claude/skills/planner-verification/SKILL.md` — deine eigene Rolle, inkl. Reuse-Gate.
5. `docs/auftraege/generator-auftrag-dashboard-v2-flaechen.md` — **vollständig**, besonders §3 (Nahtstellen
   Batch 1), §5 (Nicht-Umfang), §6 (Kantenliste), §7 (Kriterien 1–6), §8 (Guardrails).

**E1 gilt auch hier (Yamas bindende Ergänzung): erst messen, dann lesen.** Fahre die Gates und die
Greps, *bevor* du den Generator-Bericht im Ledger liest. Sonst prüfst du gegen seine Erwartung
statt gegen den Code. Der Bericht ist der **letzte** Text, den du öffnest.

**E2: voller Prüfrahmen**, nicht nur die sechs Kriterien. Was dir zusätzlich auffällt, gehört ins Votum.

---

## 1. Grundhaltung

Du traust **keiner** Zahl aus dem Generator-Bericht — auch nicht „4 Gates grün, 696→702 Tests".
Jede Zahl erzeugst du selbst und legst die Rohausgabe daneben. Zusammenfassende Prosa darf
danebenstehen, nie stattdessen.

---

## 2. Gates — selbst fahren, Exit-Codes notieren

```
npm run tsc:hausplaner
npm run schema:hausplaner:check
npm run test:hausplaner
npm run build:hausplaner
```

**Zwei Umgebungs-Wahrheiten, die du benennen musst statt sie zu überspielen:**

- `build:hausplaner` ist laut `docs/WIEDEREINSTIEG-HAUSPLANER.md` §5 **nur x64-nativ** lauffähig.
  Läuft er in deiner Umgebung nicht, ist das **kein Rot** — aber auch **kein Grün**. Schreibe
  „nicht ausführbar in dieser Umgebung, Grund: …" statt ihn stillschweigend als grün zu führen.
- **Es gibt kein DOM in der Testumgebung** (`node:test`, kein jsdom). Render-Tests sind unmöglich.
  Beweisbar ist nur, was reine Funktion ist. Alles andere ist Sichtprobe — siehe §5.

**Testzahl vorher/nachher selbst ermitteln, ohne das Repo zu verändern:**

```
git archive f6bdfc2^ | tar -x -C /tmp/v2-vorher
```
und dort dieselbe Testsuite fahren. **Kein `git stash`, kein `git checkout`, kein `worktree add`** —
der Arbeitsbaum bleibt unberührt.

---

## 3. Die sechs Kriterien — je Kriterium ein Beleg und ein Gegen-Beweis

| Nr | Behauptung | Wie du sie zu widerlegen versuchst |
|---|---|---|
| K1 | vier Gates Exit 0, Schema grün **ohne** Regen | Nach `schema:hausplaner:check` prüfen, dass `scene-document-v2.schema.json` unverändert ist (`git status` leer). Wurde doch regeneriert, ist K1 rot. |
| K2 | Testzahl nachher ≥ vorher, kein grün→rot | Zahlen aus §2 gegenüberstellen. Zusätzlich Testnamen-Mengen vergleichen: **verschwundene** Tests fallen sonst nicht auf, weil die Summe stimmt. |
| K3 | `panelTabs.ts` exportiert genau 4 Tabs in fester Reihenfolge, jeder mit `zustand` | **Mutations-Gegen-Beweis:** vertausche in `/tmp/v2-nachher` (Kopie, **nicht** im Repo) zwei Reihenfolge-Einträge → mindestens ein Test **muss** rot werden. Wird er es nicht, deckt der Test die Reihenfolge nicht ab → K3 rot. |
| K4 | `git diff` in `app/tools/*`, `store/*`, `domain/*`, `geometry/*`, `renderers/*` leer | `git show --stat f6bdfc2` selbst lesen. Nicht die Behauptung, die Dateiliste. |
| K5 | Fenstertyp/Türtyp aus der Kopfzeile **verschwunden**, in der Options-Leiste **vorhanden**, **dieselben Optionswerte** | Der dritte Teil ist der, der übersehen wird: die Werte byte-genau gegenüberstellen (`git show f6bdfc2^:…` gegen `f6bdfc2:…`). „Vorhanden" ist nicht „identisch". |
| K6 | 0 rohe Farbwerte in `app/` außerhalb `studioDaten.ts` | Grep selbst fahren: `grep -rnE '#[0-9a-fA-F]{3,6}\|rgb\(' resources/planner/hausplaner/app/ --include='*.ts*'`. Trefferliste als Rohausgabe ins Votum, auch wenn sie leer ist. |

---

## 4. Drei Punkte, die über die Kriterien hinausgehen — Planner-Auflage

1. **Das vierte Feld `hinweis`.** Der Generator hat offengelegt, dass er `PanelTab` um ein viertes Feld
   erweitert hat, das der Auftrag nicht nennt. Urteile ausdrücklich: additive Ergänzung im Sinne der
   Spec — oder Signaturabweichung? Beides ist vertretbar, aber es muss **entschieden** im Votum stehen,
   nicht unerwähnt bleiben.
2. **Der ehrliche Leerzustand.** §3 verlangt, dass leere Reiter weder Blindtext noch „keine Daten"
   zeigen. Prüfe die drei `in_entwicklung`-Reiter im Wortlaut. Ein Reiter, der so aussieht als könnte
   er etwas, verletzt die verbindliche v1-Regel („eine Fläche ohne Funktion darf dastehen — aber sie
   muss `in_entwicklung` ehrlich sagen").
3. **Kontext-Options-Leiste ohne Inhalt.** Realer Inhalt existiert nur für `fenster`/`tuer`; sonst
   erscheint ein Platzhaltersatz. Urteile, ob dieser Platzhalter der v1-Regel genügt oder ob er eine
   Fläche vortäuscht.

---

## 5. Frontend-Linse (aus `frontend-entwickler`)

- **Zustand als Farbe UND Text**, nie nur Farbe. Für jede neue Fläche einzeln prüfen.
- **Kontrast selbst nachrechnen**, nicht übernehmen — bei v1 ist genau daran eine Freigabe gescheitert
  (`warnInk` 4.36). Gemessen wird gegen **jeden** Untergrund, auf dem die Farbe real steht.
- **Status-Grün = `T.ok`**, nicht die Marke `T.brand`.
- **Sichtprobe:** ohne DOM in der Testumgebung geht sie nur über `build:hausplaner` →
  `public/hausplaner/hausplaner.js` → Route `/admin/hausplaner/studio` bzw.
  `/admin/hausplaner/objekt/{id}` (beide hinter `auth`). Ist das in deiner Umgebung nicht möglich,
  **sage es** und führe die betroffenen Kriterien als „nicht sichtgeprüft" — nicht als grün.

---

## 6. Guardrails — byte-genau

- **Du veränderst keinen Produktivcode. Kein Commit. Kein Push.** (`planner-verification`, Pflicht-Stopp.)
  Mutations-Gegen-Beweise laufen ausschließlich in einer Kopie unter `/tmp`.
- `.git/*.lock` **niemals** mit `rm` — nur `mv` nach `.git/_locks_beiseite/<datum>/`. Lesende
  git-Aufrufe mit `git --no-optional-locks`.
- Nach deiner Prüfung muss `git status` **exakt dieselben** Einträge zeigen wie vorher. Weicht er ab,
  ist das ein Befund über dich, und er gehört ins Votum.

---

## 7. Was du NICHT tust

- Kein Blick auf Batch 2 (v2.3/v2.4/v2.5) — der ist nicht Gegenstand.
- Keine Bewertung von `toolPresentation.ts` / Welle A1 — das ist AUF-1, andere Instanz.
- Keine Verbesserungsvorschläge am Code. Urteil, nicht Umbau.

---

## 8. Votum

Ein Block in `docs/handoff-status.md`:
`## ⇒ EVALUATOR-VOTUM — Dashboard v2 Batch 1 (f6bdfc2)`

Enthält: je Kriterium **grün/rot mit Rohausgabe**; die vier Exit-Codes bzw. die benannte
Nicht-Ausführbarkeit; Testzahl vorher/nachher **selbst erzeugt**; das Ergebnis jedes
Mutations-Gegen-Beweises; die Entscheidung zu den drei Punkten aus §4; alles, was E2 zusätzlich
zutage gefördert hat.

**Freigabe / Freigabe mit Auflage / Rot.** Rot braucht denselben Belegstandard wie Grün — einen
reproduzierbaren Fall. Fehlt der, ist es eine **Rückfrage** und wird auch so benannt.

Danach Tafelstatus AUF-12 auf `ERLEDIGT` (bei Freigabe) bzw. zurück auf `OFFEN — Nacharbeit`.
**Der Generator nimmt nicht ab, und du baust nicht.**
