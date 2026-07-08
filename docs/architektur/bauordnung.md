# BAU-ORDNUNG — verbindliche Regeln für jeden künftigen Agenten

> **Herkunft:** Abgeleitet aus dem Code-Audit CODE-AUDIT-01 (`docs/audit/code-audit.md`, Teil 2.1 GUT + 2.2 SCHLECHT). Die GUTEN Zonen (FK-Kanban-Hook, Test-Harness, FollowUpCreator, Marker-Seeder, Registry, Migration-`down()`, Rechte-Fundament, saubere Services) sind der **Hausmaßstab**. Die SCHLECHTEN Muster (ungegate Routen, God-Table, Stage-Duplikat mit Fold-Divergenz, Status-Zoo, Inline-JS, Zombie `customers`) sind die **Fehler, die nicht neu entstehen dürfen**.
> **Verhältnis zur Governance:** Diese Bau-Ordnung ist der **Wie-baue-ich-Leitfaden**. Sie steht UNTER `docs/BETRIEBSORDNUNG.md` und den Dauerdirektiven in `CLAUDE.md` (Daten-/Ketten-Schutz, „Eine Wahrheit je Sachverhalt", Weichen). Bei Konflikt gilt die Betriebsordnung/CLAUDE.md. Referenzierbar aus `CLAUDE.md`.
> Jede Regel nennt in Klammern den Audit-Beleg, aus dem sie stammt.

---

## 1. SCHICHTEN-REGEL

1.1 **Geschäftslogik gehört in einen Service oder einen Model-Hook, nicht in den Controller.** Ist→Soll: `NewLeadsController` (14k Z., 267 `DB::table`) ist das Anti-Vorbild; `app/Services/Heizkoerper|Accounting|Form` und `LeadProductList::booted()` sind das Vorbild. *(Audit 1.1, 2.1-1, 2.2c)*

1.2 **Der Controller bleibt dünn:** Request annehmen → validieren → EINEN Service/Action/Model-Aufruf → Response. Keine Query-Ketten + Berechnung + Antwort in einer Methode.

1.3 **Abgeleitete Wahrheiten (FKs aus Status o.ä.) gehören in EINEN Model-`booted()`-Hook** mit deterministischem Fold + benanntem Fallback + Guard gegen stale Overwrites — nie in Controller kopiert. Vorbild: `LeadProductList.php:112-175`. *(Audit 2.1-1)*

1.4 **Blades stellen nur dar.** Keine Fachlogik (Stufen-Übergänge, Preis-/Mengenberechnung) im Inline-`<script>`. Neuer JS wird als externe Datei geschrieben, perspektivisch als Modul — nicht als weiterer Inline-Block. Kein neuer Beitrag zu den ~101k Inline-JS-Zeilen. *(Audit 1.6, 2.2c)*

1.5 **Ein Service = eine fachliche Verantwortung.** Kollaboratoren per Konstruktor-DI (`CompatibilityService.php:17`), Normwerte/Konstanten als `private const`, Rechenkerne DB-frei und als reine Funktion testbar. *(Audit 2.1-8)*

1.6 **Model-Hook vs. Service:** Invarianten, die für JEDEN Schreibweg gelten müssen (FK-Ableitung, Immutabilität, Lösch-Wächter) → Model-`booted()`-Hook (fängt alle Eloquent-Schreiber). Mehrschritt-Orchestrierung / externe Systeme / Erzeugung → Service. Raw-`DB::table`-Schreiber umgehen Hooks — solche Umgehungen sind zu vermeiden; wo unumgänglich, im Code dokumentieren und gezielt absichern (Vorbild-Kommentar `LeadProductList.php:107-110`).

---

## 2. DATEN-REGELN v2

2.1 **Jede Fach-Wahrheit existiert EINMAL.** Keine zweite Ableitungs-/Speicherlogik für denselben Sachverhalt. Anti-Vorbild: `deriveLeadStageId` (Kanon) vs. `normalizeCompanyStage` (Duplikat mit Fold-Divergenz) und die drei `lead_product_lists`-Speichern-Muster mit drei Default-Status. Neue Fach-Wahrheit → prüfen, ob sie schon existiert; wenn ja, dort andocken. Weichen-Änderung nur per Yama-Revision. *(Audit 1.4, 2.2c; CLAUDE.md „Eine Wahrheit")*

2.2 **Mehr-Tabellen-Schreiben immer in `DB::transaction`.** Nur 96/387 Controller klammern heute. Jeder neue Schreibpfad über >1 Tabelle wird geklammert. Vorbild: `BuchungsEngine::festschreiben()`. *(Audit 2.2b)*

2.3 **Neue Spalte/Beziehung: FK-Constraint + Index Pflicht.** Neue `_id`-Spalte bekommt einen echten FK (wo fachlich möglich) und einen Index. Anti-Vorbild: God-Table `lead_alternative_adds` (193 Sp., 1 FK), fehlende Indizes auf `personal_tasks.source_id/controller_id/task_id`. **SoftDelete-FK-Falle beachten:** FK schützt nicht vor soft-gelöschten Eltern → Löschpfad mitdenken. *(Audit 1D-B/C, 2.2b/d)*

2.4 **Werteliste statt Freitext-String.** Neue Status-/Typ-Spalte als `enum` oder mit dokumentierter Werteliste + Konstanten im Model — kein 140. varchar-`status`. Keine neuen Magic-String-Literale im Controller. *(Audit 1D-B Status-Zoo, 2.2c)*

2.5 **Einheiten-Kommentar-Pflicht (F3).** Jede physikalische Zahl-Spalte/JSON-Key trägt die Einheit als Kommentar/Registry-Eintrag (kW vs. W, °C, W/m²K). Vorbild: `UWertService` `private const` + Kommentar. *(Audit 1D-B Einheiten-Ambiguität)*

2.6 **Additive Migration mit echtem `down()`.** Neue Tabellen/Spalten nur additiv (nullable oder Default) — nie Bestandsdaten mutieren (CLAUDE.md-Dauerdirektive). Jede Migration, auch Daten-Migrationen, hat ein reversibles `down()` in FK-sicherer Reihenfolge. Vorbild: `activate_abnahme_lead_stage.php:18`. Kein leeres `down()`. *(Audit 2.1-6; CLAUDE.md Daten-Schutz)*

2.7 **Sensible Daten nur gegated + nie in Logs.** Lohn/Gehalt/Urlaub/Krankheit/Bank/PII: Schreib- UND Leserouten mit HR-/Admin-Gate + Owner-Check. Logs enthalten keine PII (Vorbild: `Log::warning('…', ['status'=>…])` loggt nur Statuscode). *(Audit 2.2a IDOR, 1D-A)*

2.8 **Löschpfad bei neuen PII-Tabellen.** Wer eine neue kundenbezogene Tabelle anlegt, definiert zugleich, wie sie beim Kunden-Löschpfad mitgelöscht/anonymisiert wird (Waisen-Risiko vermeiden). SoftDeletes + dokumentierte Kaskade. *(Audit 1D-A DSGVO)*

2.9 **Neue Tabelle braucht Domänen-Heimat + Namens-Konvention.** Einordnung in eine Domäne (kein herrenloses Streu-Objekt), konsistente Sprache (Alt-Kern englisch/Plural; neue Fach-Domäne deutsch wie Bestand), `created_at`/`updated_at` Pflicht. Keine neue Zombie-/Doppel-Tabelle wie `customers` neben `new_leads`. *(Audit 1D-B/D)*

---

## 3. SICHERHEITS-REGELN

3.1 **Jede Schreibroute ist gegated.** Neue POST/PUT/PATCH/DELETE-Route bekommt explizit `auth` UND eine Berechtigung (`permission:<item>,<action>` oder `is_admin`). Niemals eine `['middleware' => 'web']`-Gruppe mit Schreibroute (die `web`-Gruppe hat kein `auth`). Das Rechte-Fundament (`hasPermission`) existiert — es MUSS benutzt werden. *(Audit 2.2a P0-1/P0-2, 2.1-7)*

3.2 **Server-Validierung ist Pflicht.** Kein `create/update` ohne vorherige Validierung. Bevorzugt eine `FormRequest`-Klasse (heute 0 vorhanden — neue Domänen führen sie ein), mindestens `->validate()`. *(Audit 1.1, 2.2a)*

3.3 **Client-IDs nie ungeprüft übernehmen.** Fremdschlüssel/`{id}` aus dem Request gegen Besitz/Berechtigung prüfen (`abort_unless`). Anti-Vorbild: IDOR auf `salary_sheet/{id}`. *(Audit 2.2a)*

3.4 **Mass-Assignment-Schutz.** Kein `guarded = []` auf Tabellen mit sensiblen/steuernden Spalten; `$fillable` explizit pflegen; `$request->all()` nicht direkt in `create/update` — nur validierte Felder (`$request->validated()` / `$request->only([...])`). *(Audit 2.2a)*

---

## 4. BAU-PROZESS-REGELN

4.1 **Pflicht-Stopp vor Produktiv-Commit.** Vor jedem Commit gegen `main`/produktiv: Selbstprüfung gegen diese Bau-Ordnung + die 10 Fragen (Abschnitt 5). *(BETRIEBSORDNUNG Gates)*

4.2 **Verifikation gegen den Harness.** Neue/geänderte Logik wird durch einen Test belegt, der **Verhalten** prüft (DB-Zustand/berechneter Wert/403), nicht „läuft". Test-DB ist `ticket_testing` (per `phpunit.xml force="true"` gepinnt) — nie die Dev-DB. *(Audit 1.7, 2.1-2)*

4.3 **Marker + Teardown-Beweis bei Seedern/Importen.** Jeder Seed/Import trägt einen Herkunfts-Marker (Konstante), ist idempotent (Upsert, zweiter Lauf ohne Duplikate) und hat einen reversiblen, mehrbesitz-sicheren Teardown (löscht nur eigene Marker; geteilte Stammdaten nur wenn Rest=0). Der Rückbau-Beweis ist Pflicht (Test wie `FoxEssLongiTeardownTest`). *(Audit 2.1-4; CLAUDE.md Katalog-Regeln)*

4.4 **Byte-Beweis bei Ports.** Wird Rechen-/Fachlogik aus einer Fremd-App portiert (wberechnung, playground), im Docblock den byte-genauen Bezug dokumentieren (Vorbild `RadiatorPerformanceService`-Docblock).

4.5 **Zähl-Invariante bei Umbauten.** Bei Migrationen/Cut-overs vorher/nachher zählen (Zeilen, Summen) und die Gleichheit belegen — kein „Beifang" (CLAUDE.md: keine Mutation von Bestandsdaten als Nebeneffekt).

4.6 **Kleine, scoped Commits.** Ein Commit = ein Sachverhalt. `git pull` vor Edit; **nie `git add -A`** — nur die bewusst geänderten Dateien stagen. UPDATE/DELETE auf Bestandsdaten ist ein eigener, explizit beauftragter Posten.

4.7 **Abweichungen offen benennen.** Wo eine Regel bewusst gebrochen wird (z.B. dokumentierte Alpine-Ausnahme, Raw-`DB::table` mit Absicherung), im Code + Commit begründen. Keine stillen Ausnahmen.

---

## 5. NEUBAU-CHECKLISTE — die 10 Fragen vor jedem Commit

1. **Domänen-Heimat?** Hat jede neue Tabelle/Datei eine klare Domäne (kein herrenloses Streu-Objekt)?
2. **Naht definiert?** Docken neue Module über stabile Anker (FK-erzwungene Identität) an, nicht über doppeldeutige Spalten (`customer_id`→?)?
3. **Wahrheit einmalig?** Existiert die Fach-Logik/Ableitung schon irgendwo? Wenn ja: dort andocken statt duplizieren (kein zweites `deriveLeadStageId`).
4. **Gegated?** Trägt jede Schreibroute `auth` + Berechtigung? Sind sensible Daten (Lohn/PII) hinter HR-/Owner-Gate?
5. **Validiert?** Wird serverseitig validiert; wird `$request->all()` vermieden; kein `guarded=[]` auf sensiblen Tabellen?
6. **Transaktion?** Sind Mehr-Tabellen-Schreiben in `DB::transaction` geklammert?
7. **Schema sauber?** Neue Spalten additiv (nullable/Default), mit FK + Index + Einheiten-Kommentar; echtes `down()`; Werteliste statt Freitext-Status?
8. **Getestet?** Gibt es einen Verhaltens-Test gegen `ticket_testing`; bei Seedern Idempotenz- + Teardown-Beweis?
9. **Schicht korrekt?** Logik im Service/Hook, Controller dünn, Blade nur Darstellung, kein neuer Inline-JS-Fachcode?
10. **Bestand unangetastet?** Keine Mutation/kein Löschen von ticket-Bestandsdaten als Beifang; kein `git add -A`; Abweichungen dokumentiert?

---

## Gelesen / Nicht-gelesen (Bilanz)

**Grundlage gelesen:** die GUT- und SCHLECHT-Befunde aus `docs/audit/code-audit.md` (Teil 2.1/2.2), firsthand verifiziert: `LeadProductList`-Hook, `CheckUserPermission`/`Kernel.php`/`bootstrap/app.php`, Route-Gating in `web.php`, `guarded=[]`-Models, Schema-Metriken (FK/Index/Status/`customer_id`-Doppelziel). Die 8 Gold-Zonen als Regel-Quelle, die dokumentierten Anti-Muster als Verbots-Quelle.

**Nicht Gegenstand dieses Dokuments:** Vollständige Neubewertung des Codes (steht im Audit); TABU-Zonen; Runtime-Messungen. Diese Bau-Ordnung ist bewusst kurz und regel-, nicht befund-orientiert.
