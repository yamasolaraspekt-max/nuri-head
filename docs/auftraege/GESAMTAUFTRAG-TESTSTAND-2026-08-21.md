# GESAMTAUFTRAG — Hausplaner stabilisieren, Sicherheitswelle abschließen, testbaren Stand herstellen

```yaml
erteilt_von: Yama, 21.08.2026 (Wortlaut nahezu 1:1 übernommen; Abgleich-Anhang vom Dirigenten)
weitergegeben_durch: Dirigent (Vollmacht docs/regelwerk/VOLLMACHT-DIRIGENT.md)
gilt_fuer: alle Rollen (Planner, Plan-Prüfer, Generator, Evaluator, Integrator, Release-Prüfer)
status_steht_in: docs/STATUS.md (je Einzelauftrag); dieses Blatt ist die Klammer
```

## Ziel
Es soll ein sauberer, reproduzierbarer Teststand entstehen, auf dem:
1. die fünf fertigen Hausplaner-Verbesserungen unabhängig abgenommen sind,
2. die kritischen Zugriffs- und Manipulationslücken geschlossen sind,
3. „alle Rechte für alle" über einen rückstellbaren Schalter umgesetzt ist,
4. Eigentums- und Integritätsprüfungen trotzdem wirksam bleiben,
5. automatische Tests und Browserprüfung nachweislich grün sind.

## Verbindliche Grundentscheidung
„Alle Rechte für alle" wird **nicht** durch Massendaten oder `is_admin=1` umgesetzt. Stattdessen:
zentrale Konfiguration `RECHTE_ALLE_FUER_ALLE`, Code-Default `false`, gewünschte lokale/produktive
Einstellung bewusst setzen, `User::hasPermission()` berücksichtigt den Schalter zentral, neues
Permission-Item `Planner`, Tests bei Schalter `false` und `true`.
(→ [ENTSCHEIDUNG-RECHTE-ALLE-FUER-ALLE.md](../regelwerk/ENTSCHEIDUNG-RECHTE-ALLE-FUER-ALLE.md), Auftrag Z2-W0-7)

**Wichtig:** Der Schalter betrifft Berechtigungen zum Sehen und Bedienen. Er darf **nicht** erlauben:
fremde Datensätze zu verfälschen · einen fremden Mitarbeiter als Verfasser einzutragen · fremde
Objekte ohne fachliche Zuordnung zu überschreiben · Authentifizierung oder echten Kontostatus zu umgehen.

## Reihenfolge

### Phase 1 — vorhandenen Hausplaner-Bau abnehmen (Evaluator, unabhängig)
Z1-W1-1 DIN-Badge · Z1-W1-2 Walmdach-Sperre · Z1-W1-3 gemeinsame `polygonM2`-Formel ·
Z1-W1-4 zentrale `dachWerte` · Z1-W1-5 ehrlicher `insulationType`-Zweig.
**Pflicht:** alle Kriterien erneut messen · TypeScript-Prüfung · vollständige Hausplaner-Suite ·
Rot-/Mutationsproben · Browserprüfung der Walmdach-Fehlermeldung.
**Kein gemeinsames Sammelvotum ohne fünf einzeln nachvollziehbare Ergebnisse.**

### Phase 2 — Rechte-Schalter W0-7
Planner vervollständigt den Auftrag, Plan-Prüfer erteilt die DoR, anschließend baut der Generator:
`config/rechte.php` · `.env.example` · zentrale Abfrage in `User::hasPermission()` · Permission-Item
`Planner` · idempotente Migration oder Seeder nach vorhandenem Hausmuster · Tests für beide
Schalterstellungen. Danach unabhängige Abnahme durch den Evaluator.

### Phase 3 — unmittelbare Sicherheitslücken (Reihenfolge)
1. **W0-1**: Gebäudeakte mit `Customer,read` schützen.
2. **W0-3**: `employee_id` serverseitig aus der Sitzung nehmen; keine fremde Identität aus Request oder Query akzeptieren.
3. **W0-2**: Grundriss-Editor absichern. **Vorher muss der Planner die vierte Route `vorschau` in Scope und Kriterien aufnehmen.**
4. **W0-5**: Nuriva-/Planner-API an Zuständigkeit binden.
5. **W0-6**: Token-Abilities tatsächlich durchsetzen oder aus der Zusage entfernen.
6. **W0-4**: Routen-Ratsche erst nach den neuen Gates als korrekte Baseline bauen.

Jeder Auftrag durchläuft einzeln: `Planner → Plan-Prüfer → Generator → Evaluator → Integrator`.

### Phase 4 — Anschlussfunde
W0-8: `secure.image` und Geschwister mit Recht und Kunden-/Objektbindung schützen · W0-9: echten
Kontostatus einführen — Login darf einen deaktivierten Benutzer nicht automatisch wieder aktivieren ·
`api/secure/master-sets*` vollständig untersuchen und ggf. eigenen P0/P1-Auftrag erstellen ·
Upload-Größenbegrenzung bewerten · Token-Widerruf, Bereinigung und Ablaufzeit ergänzen.

## Noch benötigte Yama-Entscheidung
**Nuriva-Token-Laufzeit:** `NURIVA_TOKEN_LAUFZEIT_STUNDEN = ___` — Empfehlung: **8 Stunden**,
konfigurierbar, mit serverseitigem Widerruf und regelmäßigem Entfernen abgelaufener Tokens.
*(Dirigent: solange Yama keine Zahl nennt, wird die Empfehlung 8 h als konfigurierbarer Default
eingeplant — rückstellbar per Config, also keine stille Festlegung.)*

## Abnahmekriterien für den Gesamtstand
Der Stand ist erst testbereit, wenn: alle fünf Z1-Aufträge `ABGENOMMEN` sind · W0-7 abgenommen ist ·
W0-1 bis W0-3 abgenommen sind · alle Tests ausschließlich gegen `ticket_testing` liefen · Rechte-
Schalter `false` und `true` geprüft wurden · Nutzer ohne Recht bei `false` zuverlässig 403 erhalten ·
Eigentumsverletzungen unabhängig vom Schalter verhindert werden · deaktivierte Nutzer sich nicht
anmelden können · Browserprüfung mit Testbenutzer und Testobjekt durchgeführt wurde · Arbeitsbaum
sauber und der Test-SHA eindeutig genannt ist.

## Arbeitsgrenzen
Bestehende uncommittete Änderungen nicht überschreiben · pro Auftrag eigener Commit und
nachvollziehbarer Scope · Generator nimmt den eigenen Bau niemals ab · keine Produktionsdaten
verändern · **kein Merge, Push, Release oder Status `VEROEFFENTLICHT` ohne gesonderte Freigabe von
Yama** (Transport-Push des Dirigenten auf `fork` bleibt, wie bisher freigegeben) · offene
Browserprüfung niemals als bestanden melden.

## Erwartetes Ergebnis
Eindeutiger Test-SHA · Liste der enthaltenen Aufträge · Testergebnisse mit echten Zählern ·
Browsernachweis · bekannte Restpunkte · Rückweg für Rechte-Schalter und einzelne
Sicherheitsänderungen · klare Aussage: **`TESTBEREIT` oder `NICHT TESTBEREIT`.**

---

## Abgleich-Anhang (Dirigent, 21.08.) — was die heutigen Messungen zu Phase 4 beitragen

- **`api/secure/master-sets*` ist UNTERSUCHT** (security-reviewer): die Endpunkte HABEN eine Auth
  (`authApi()`, `hash_equals`, anonym → 401; der alte Ledger-Fund #116 „fehlt Authentifizierung" war
  ein falscher Alarm). Offen bleiben: Secret wird auch aus dem **Query-String** akzeptiert (`:28-34`,
  landet in Access-Logs) · `env()` statt `config()` (`:36-37`, unter `config:cache` → 500) · Debug-
  Endpunkt liefert Schema-/Spalten-/Rohzeilen-Dump und ungegateten Exception-Text (`:391-392`) ·
  Payload enthält EK-Preise, Margen, Stundensätze, Mitarbeiter-Klarnamen/Fotos · **kein Konsument im
  Repo** → **Y-11: Wer ruft diese Schnittstelle? Ohne Konsument ist Stilllegen der billigste Fix.**
  → Posten **W0-10** (P1: Query-Zweige streichen, config(), Debug nur local/testing, Exception gaten).
- **CSRF, neu (Stopp-Regel-Kandidat):** `POST ids/callback` steht in den CSRF-Ausnahmen, liegt hinter
  Session-Auth, legt `ImportedIdsItem` an und mit `?auto=1` Produkte/Distributor — `uid` aus der
  Query → Fremdzuschreibung per Auto-Submit-Form von fremder Seite. **ROT** → Posten **W0-11**.
  `ai/chats/{chat}/message` ohne Token (Kosten, niedriger) · 5 von 11 Ausnahmen sind tot (falsche
  Pfade) — aufräumen.
- **W0-2:** vierte Route `vorschau` wird in Scope/Kriterien aufgenommen (Messung s. Blatt).
- **W0-9:** bereits geschnitten (Kontostatus `disabled_at` additiv, Login reaktiviert nicht).
- **Upload-Größe:** A-9 `ImageController:30` ohne `max:` (S, GELB) — Bewertung: nachziehen auf das
  Hausmaß der Nachbarpfade (25 MB).
