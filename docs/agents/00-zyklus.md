# 00 — DER ZYKLUS: Drei-Rollen-Agentensystem für CRM/ERP-Optimierung

> **Zweck:** Kontinuierliche, sichere Optimierung des ticket CRM/ERP durch drei getrennte Rollen — **PLANNER** (Strategie) · **GENERATOR** (Umsetzung) · **EVALUATOR** (Kontrolle+Veto). Dies ist das **Team + Regelwerk + der Zyklus** — **noch kein CRM-Umbau**.
> **Verhältnis zur Governance:** setzt `docs/BETRIEBSORDNUNG.md`, `CLAUDE.md`, `docs/architektur/bauordnung.md` und die entschiedenen Weichen **durch**, hebt sie nie auf. Bei Konflikt gilt die Betriebsordnung/CLAUDE.md. Die drei Rollen sind die feinkörnige Optimierungs-Ausprägung der Betriebsordnungs-Rollen BAUER≈Generator / PRÜFER≈Evaluator, mit dem PLANNER als vorgelagerter Strategie-Station.
> **Rollen-Docs:** [`01-planner.md`](01-planner.md) · [`02-generator.md`](02-generator.md) · [`03-evaluator.md`](03-evaluator.md).
> **Claude-Code-Start:** [`04-claude-code-startanweisung.md`](04-claude-code-startanweisung.md) ist der verbindliche Einstiegspunkt, damit dieser Zyklus bei jeder Arbeit im Repo sofort geladen und angewendet wird.

---

## DER ZYKLUS (Ablauf)

```
Yama-Ziel
   │
   ▼
PLANNER  ──►  Strategie-Doc + nummerierte Arbeitspakete
   │          ⛔ STOPP: Yama nimmt die Strategie ab
   ▼
je Paket, in Reihenfolge:
   │
   ▼
GENERATOR ──►  baut kleinste additive Umsetzung + Verhaltens-Test + 10-Fragen-Selbstprüfung
   │           (committet NICHT — Pflicht-Stopp an Evaluator)
   ▼
EVALUATOR ──►  dreifach: (A) Richtigkeit  (B) Bauordnung 10 Fragen  (C) Grundvoraussetzungen
   │
   ├─ FREIGABE   ──►  ⛔ Yama bestätigt  ──►  GENERATOR committet+pusht  ──►  nächstes Paket
   ├─ NACHBESSERN ──►  zurück an GENERATOR (Mängelliste mit Belegen)  ──►  erneut EVALUATOR
   └─ ABLEHNEN    ──►  zurück an PLANNER (Ansatz falsch)  ──►  neu einordnen/planen
```

**Taktgeber ist der Pflicht-Stopp:** kein Schritt springt über seinen Stopp. Der Zyklus läuft Paket für Paket, bis das Backlog leer ist (dann: „Strang fertig" melden + stoppen, keine Arbeit erfinden).

---

## REGELN (verbindlich)

1. **Getrennte Instanzen — Generator ≠ Evaluator ZWINGEND.** Der Evaluator ist immer eine frische Instanz, nie dieselbe, die gebaut hat. (Betriebsordnung 3.2: „frische Instanz je Prüfung".)
2. **Jede Übergabe schriftlich mit Belegen.** Planner→Generator: das Paket mit 7 Feldern. Generator→Evaluator: Gelesen-Liste + Änderungen + Test + 10-Fragen-Selbstprüfung. Evaluator→zurück: Votum + Protokoll mit selbst erzeugten Belegen. Kein mündliches „passt schon".
3. **Yama ist finaler Freigeber vor jedem Produktiv-Commit.** Evaluator-FREIGABE ist eine Commit-**Empfehlung**; committet wird erst nach Yama-Bestätigung. (Restgrenze Betriebsordnung 2.2.)
4. **Eskalation an Yama bei Dissens.** Planner↔Evaluator-Konflikt, Weichen-/Direktiven-Zweifel, Restgrenze berührt → Strang stoppt, Yama entscheidet, Antwort wird Entscheidung.
5. **Pflicht-Stopp = Haupt-Taktgeber.** Geschwindigkeit entsteht durch den geschlossenen Kreislauf, nicht durch das Aufweichen von Gates.
6. **Beweis statt Bericht** (Evaluator misst selbst) · **kein `git add -A`** · **kein Beifang** (Bestandsdaten-Mutation nur als eigener Yama-Posten) · **TABU** (Nuriva/Video/Invoice-Zone/Legacy) unberührt · **Testanzahl sinkt nie**.
7. **Evaluator strikt read-only — keine Schreib-/git-Werkzeuge.** Der Evaluator (und jeder Prüf-Subagent) führt KEINE git-Schreibbefehle aus (`add`, `commit`, `push`, `reset`, `rebase`, `checkout`, `stash`, `tag`, `restore`) und ändert KEINE Dateien. Er nutzt nur lesende Werkzeuge und liefert ausschließlich **Befund / Votum (FREIGABE/NACHBESSERN/ABLEHNEN) / Auflagen** — nie einen Commit oder Push. Erlaubt sind nur read-only-Prüfungen (Diff/Log/Show/Grep/Tests).
8. **Kein Push ohne ausdrückliche Yama-Freigabe.** Lokale Commits sind nach Freigabe erlaubt; **Push ist IMMER ein eigener, separater, ausdrücklich freigegebener Schritt.** Kein automatischer Push nach Commit, kein `--force`/force-push ohne explizite Yama-Freigabe.

**Fundament — alle drei Rollen laden es immer (read-only):** `docs/architektur/bauordnung.md` · `docs/architektur-entscheidungen.md` · `docs/glossar.md` · `docs/audit/code-audit.md` · `docs/BETRIEBSORDNUNG.md` · `CLAUDE.md`. Sobald vorhanden zusätzlich: `docs/zielbild-domaenen.md` + die Wächter-Skills.

---

## (a) VORGESCHLAGENE CLAUDE.md-VERANKERUNGS-ZEILE

> **Hinweis:** Der Haupt-Thread setzt diese Zeile in `CLAUDE.md` (um Editier-Kollision zu vermeiden) — dieses Dokument schlägt sie nur als fertigen Text vor. Empfohlene Platzierung: als eigener Abschnitt nahe dem Verweis auf die BETRIEBSORDNUNG.

```markdown
## Optimierungs-Arbeitsmodus: Drei-Rollen-Zyklus (Standard)

Für JEDEN Optimierungs-/Verbesserungs-Auftrag am CRM/ERP gilt der Drei-Rollen-Zyklus
**PLANNER → GENERATOR → EVALUATOR** aus [`docs/agents/00-zyklus.md`](docs/agents/00-zyklus.md)
als verbindlicher Standard-Arbeitsmodus. Bindend: getrennte Instanzen
(**Generator ≠ Evaluator zwingend**), jede Übergabe schriftlich mit Belegen,
Evaluator-Veto (kein Commit ohne Freigabe), **Yama ist finaler Freigeber vor jedem
Produktiv-Commit**. Der Zyklus setzt `docs/BETRIEBSORDNUNG.md` und
`docs/architektur/bauordnung.md` DURCH und hebt sie nicht auf; bei Konflikt gilt die
Betriebsordnung/CLAUDE.md. Rollen-Regelwerke: `docs/agents/01-planner.md`,
`02-generator.md`, `03-evaluator.md`.
```

---

## (b) BEWÄHRUNGSPROBE — ein echtes kleines Ziel einmal durch den vollen Zyklus

**Ziel (Yama-Beispiel):** „Der IDOR-Endpunkt `GET /salary_sheet/{id}` (Gehaltsdaten, heute nur `auth`-gegated) wird gegen fremden Zugriff abgesichert." *(Belegter Befund: `docs/audit/code-audit.md` Teil 2.2a P0-2 + 2.4; `routes/web.php:1684`, `SalarySheetController` ohne `authorize/Gate/is_admin`.)* — Ein kleines, scharf umrissenes Sicherheits-Ziel, nicht der große Status-/God-Table-Umbau.

### PLANNER — Einordnung + Pakete
- **Einordnung:** verletzt Grundvoraussetzung **5 (Berechtigung/DSGVO)** — Personaldaten ohne Owner-/HR-Gate. Hängt an KEINER offenen Weiche → durchplanbar. Kollisions-Check: berührt HR-Routen, nicht den laufenden Status-/IDOR-Fix-Strang doppelt (abstimmen mit `docs/audit/**`).
- **Ist-Beleg:** `routes/web.php:1682-1692` nur `auth`; `SalarySheetController` ohne Berechtigungsprüfung; Rechte-Fundament (`User::hasPermission()`) existiert, aber dormant.
- **Empfehlung:** Owner-/HR-Gate über das bestehende Fundament nachrüsten (Strangler-Punkt-Fix), kein neues Rechte-System.
- **Pakete (nummeriert):**
  - **P1 — Owner-/HR-Gate auf die Lese-Route.** Betroffen: `SalarySheetController@show`, ggf. `routes/web.php:1684`. Abhängigkeit: keine. Risiko live: niedrig (additiver Guard, keine Bestandsdaten). Verifikations-Kriterium: **Nicht-Admin auf fremde `{id}` → HTTP 403; Eigentümer/Admin → 200.** Domänen-Heimat: HR/Personal. Grundvoraussetzung: 5.
  - **P2 (Folge, optional) — Schreib-Routen derselben Gruppe analog gaten.** Eigenes Paket, eigener Test — kein Beifang zu P1.

### GENERATOR — Plan für P1
- **Gelesen-Liste:** `SalarySheetController` (Methode `show`), `routes/web.php:1682-1692`, `User::hasPermission()`, `DealMeasurementPolicyTest.php` (Muster).
- **Bau (kleinste additive Umsetzung):** im Controller `abort_unless($user->isAdmin() || $user->id === $sheet->employee->user_id, 403)` bzw. `permission:hr,read`-Middleware auf die Route — additiv, keine Schema-/Datenänderung.
- **Verhaltens-Test (gegen `ticket_testing`):** Nicht-Admin ruft fremdes `salary_sheet/{id}` → `assertStatus(403)`; Eigentümer → `200` (Muster wie `DealMeasurementPolicyTest`). Prüft **Verhalten** (403), nicht „läuft".
- **10-Fragen-Selbstprüfung:** relevant grün: F4 gegated, F5 validiert/Owner-Check, F8 Verhaltens-Test, F10 kein Beifang (nur diese Route). F6/F7 n/a (keine Migration) — dokumentiert.
- **Übergabe** an Evaluator, **kein Commit**.

### EVALUATOR — Prüfpunkte
- **(A) Richtigkeit — selbst nachmessen:** Route in `route:list` prüfen (`auth`+Gate wirklich dran?); Test-Code lesen (prüft er 403, nicht nur 200?); Suite selbst laufen lassen (0 Fehler, Anzahl ≥ Vorgänger); ausgeloggt/nicht-Eigentümer `GET /salary_sheet/{fremde_id}` → 403 selbst reproduzieren.
- **(B) Bauordnung 10 Fragen:** F4 grün mit Beleg (Route:Zeile trägt Gate), F5 grün (Owner-Check-Zeile), F8 grün (Testname + Ausgabe), F10 grün (Diff nur diese eine Route/Methode, kein `git add -A`, kein Debug-Rest).
- **(C) Grundvoraussetzungen:** #5 Sicherheit/DSGVO jetzt erfüllt; keine PII in Logs neu; keine zweite Wahrheit; Prozesskette unberührt; keine Weichen-Kollision.
- **Votum:** FREIGABE (alle grün) → Commit-Empfehlung an Yama → nach Yama-Bestätigung committet der Generator → nächstes Paket (P2). Sonst NACHBESSERN (Mängelliste) oder ABLEHNEN (falls der Ansatz das Rechte-Fundament umginge).

**→ Trägt das Zusammenspiel:** Der Planner schärft ein belegtes Ziel zu einem prüfbaren Paket, der Generator baut minimal-additiv mit Verhaltens-Test, der Evaluator misst das 403 selbst nach und hält das Veto bis Yama bestätigt. Kleiner Umfang, voller Kreislauf.
