# KONZEPT — Governance/Infrastruktur automatisieren: „Kontrollen als Maschine statt als Text und Handarbeit"

```yaml
zustand: "KONZEPT (gedacht, teils in Auftraegen angefangen) — Yama 21.08.2026 spaet, zwoelf Hebel, Wortlaut uebernommen"
spur: "Governance/Infrastruktur — EIGENE Fortschrittsanzeige, getrennt von Hausplaner-Produkt und Sicherheit (Hebel 8)"
einordnung_dirigent: "Die drei groessten Hebel werden als Auftraege G-1/G-2/G-3 geschnitten — NACH Z0-I1 und A-37 (WIP-Regel: Blocker schliessen, abnehmen, dann Neues). Z0-I1 ist bereits die erste Scheibe von G-2; A-41/A-42 sind der Vorlaeufer von G-1; der Stage-Scope-Abbruch und die Routen-Ratsche (W0-4) sind Vorlaeufer von G-3/6."
```

## Yamas Kernaussage
„Das Problem ist nicht zu wenig Kontrolle, sondern dass viele Kontrollen noch als Text und Handarbeit
existieren." — Die drei größten Hebel: **(1)** eine Auftragsdatei als einzige Wahrheit, Berichte
daraus erzeugen · **(2)** komplett automatisierter Rollen-Workspace mit eigener Datenbank ·
**(3)** WIP-Limit im System: erst abnehmen, dann neue Arbeit beginnen.

## Die zwölf Hebel (Yama, 21.08.) — mit Bestandsbezug

| # | Hebel | Was Yama verlangt | Bestand / Vorläufer | Künftiger Auftrag |
|---|---|---|---|---|
| 1 | **Maschinenlesbare Auftragswahrheit** | je Auftrag EINE kleine YAML/JSON: Zustand, Ball, Basis-/Bau-/Prüf-SHA, Abhängigkeiten, Blocker, Prüfresultat; `docs/STATUS.md` + HTML daraus erzeugt → „gebaut, aber ENTWURF" verschwindet | STATUS.md trägt je Auftrag einen YAML-Datensatz **in einer 28.000-Zeilen-Datei**; `scripts/status-erzeugen.sh` (A-41) erzeugt die Tafel; A-42 Befundnotizen | **G-1** |
| 2 | **Automatischer Rollen-Workspace** | ein Befehl je Rolle: Worktree+Branch, eigene Test-DB, eigene `.env.testing`, eigener Port, eigenes Testkonto, geprüfter `node_modules`-Stand, Rollenkennung, Ressourcen-Lock | Worktrees existieren von Hand; `rollen-tor.sh` (Zuordnung), `module-nachziehen.sh` (Modulmarke); **Z0-I1** (Test-DB je Rolle + Guard) = erste Scheibe | **G-2** (Z0-I1 zuerst) |
| 3 | **Ressourcen-Vermieter** | evaluator→`ticket_testing_evaluator`→Port 8101→`eval@test` · generator→…→8102 · security→8103 · browser→8104; belegte Ressource nicht übernehmbar | nichts Systematisches | Teil von **G-2** |
| 4 | **Automatisches Abnahmepaket** | Generator erzeugt nach jedem Bau: Basis-/Bau-SHA, Dateiliste, Diffstat, Tests mit Zählern, Rot-/Mutationsprobe, Abweichungen, Rückweg, Browserpflicht ja/nein; Evaluator nutzt dasselbe Format, misst aber neu | Bauberichte stehen als Prosa in Commit-Botschaften; `commit-pruefen.sh` prüft Form | **G-4** |
| 5 | **WIP-Limit im System** | max. ein uncommittierter Produktbau je Rolle; max. drei Aufträge gleichzeitig `CODE_FERTIG`; bei Limit keine neuen Bauten, zuerst Abnahme; P0-Blocker (A-37, Z0-I1) verdrängen Produktarbeit | heute Anweisung per Text (Dirigent, 21.08.) | **G-3** (Tor-Regel aus der Auftragsdatei) |
| 6 | **Bundle-Drift-Prüfung** | Gate: Hausplaner-Quellen seit letztem Bundle geändert? Würde ein Build das Bundle ändern? Reproduzierbar? | „Bündel gebaut — zehn Quellcommits seit 15.08. erreichen jetzt den Browser" (Generator 21.08. 21:10) zeigt genau die Drift | **G-6** |
| 7 | **Browserabnahme reproduzierbar** | je Prüfung: Test-SHA, DB+Fixture, Benutzer, Route, Viewport, exakte Handlung, erwarteter Text/Zustand, Screenshot vorher/nachher | Browserbühne + `scripts/__tests__/browserBuehne.test.mjs` vorhanden; Form nicht verbindlich | **G-7** |
| 8 | **Drei Spuren** | Hausplaner-Produkt · Sicherheit/Rechte · Governance/Infrastruktur — ein Sicherheitscommit erhöht den Produktfortschritt nicht | Fahrplan mischt Welle 0 (Sicherheit) und Welle 1 (Produkt) | Teil von **G-1** (Spur-Feld je Auftrag) |
| 9 | **Aktivierungsmatrix** | je Werkzeug: Code → Registry → UI → Aktivierung → Command → SceneDocument → Speichern → 2D → 3D → Bundle → Browser | Werkzeugregister „43 geklärt" = beschrieben, nicht nutzbar | **Z0-F1** (Phase 7) |
| 10 | **Alterungswarnung** | markieren, wenn `CODE_FERTIG` > 4 h ohne Evaluator, Ball ohne Bewegung altert, Bau-SHA fehlt, Zustand ↔ Historie widersprechen, Browserpflicht offen | Plan-Prüfer misst das heute von Hand (§267) | Teil von **G-1** (Erzeuger warnt) |
| 11 | **Feature-Schalter mit geprüftem Rückweg** | Default, Aktivierung, Rückweg, Tests beide Zustände, Besitzer + Ablaufdatum; vergessene Schalter melden | `RECHTE_ALLE_FUER_ALLE` (W0-7), `master_set_api.aktiv` (W0-10), Token-Laufzeit (W0-12) — je ohne Besitzer/Ablaufdatum | **G-11** (Schalterregister) |
| 12 | **Testdatenbank-Guard trotz voller Rechte** | vor dem ersten Schreibzugriff: Name beginnt `ticket_testing_`, Rollen-DB stimmt exakt, Produktion sicher abgelehnt, paralleler Besitzer = aktuelle Rolle | **Z0-I1 Teil B** (Guard) — Besitzer-Prüfung ergänzen | in **Z0-I1** aufgenommen |

## Reihenfolge (WIP-Regel, Yama)
1. **Jetzt:** Z0-I1 (= G-2 Scheibe 1 + Hebel 12) · A-37 · Abnahmen Z1/Z2 — keine neuen Bauten.
2. **Danach G-1** (Auftragsdatei SSOT → STATUS.md + HTML erzeugt; Spur-Feld; Alterungswarnung) —
   löst A-41/A-42 ein, statt daneben zu bauen.
3. **Dann G-2 komplett** (Workspace-Befehl + Ressourcen-Vermieter) und **G-3** (WIP-Limit als Tor-Regel
   aus den Auftragsdateien).
4. G-4, G-6, G-7, G-11 in dieser Folge; Z0-F1 (Aktivierungsmatrix) läuft als Phase 7 des Gesamtauftrags v2.

**Nicht-Ziel des Konzepts:** kein zweites Status-System neben `docs/STATUS.md` — G-1 **ersetzt** die
Handpflege durch Erzeugung, die Datei bleibt der lesbare Statusträger (ARBEITSREGELN §16: erzeugt,
nicht von Hand).
