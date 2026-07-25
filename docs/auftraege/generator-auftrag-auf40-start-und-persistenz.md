# ⇒ GENERATOR-AUFTRAG AUF-40 — L6: Start/Zuletzt an echte Projekte + Konfigurator-Paket serverseitig

**Vom:** Planner · **25.07.2026** · **Anlass:** Bestandsaufnahme — **Ebene 2 und Ebene 4 der
Layout-Inventur sind zeilengenau unverändert** (112 bzw. 230 Zeilen, damals wie heute), und L6 hatte
**keinen Posten auf der Tafel**.

**⚠ Dieser Auftrag wird nicht gezogen, bevor Yama den Backend-Teil freigegeben hat.** Er ist der
einzige Layout-Posten, der `app/Http/`, `routes/` und eine Migration berührt — das ist Tor 1.
Der Frontend-Teil (§3) ist davon **unabhängig** und darf vorher laufen.

**Vorher gelesen:** HEAD `52b403f` · `git log -5` · Tafelzeile AUF-40 (§3a) ·
`app/StartView.tsx:88-96` · `app/studioDaten.ts:69` (`ZULETZT`) · `app/ConfigWizard.tsx:205-223` ·
`app/Http/Controllers/Hausplaner/HausplanerController.php:100,124,149` · `routes/web.php:5002-5008`.

---

## 1. Drei gemessene Befunde

**(a) Der Startbildschirm zeigt erfundene Projekte.** `studioDaten.ts:69` führt `ZULETZT` als drei
Demo-Einträge („EFH Mustermann", „Fenster-Angebot Hahn"). Sie erscheinen bei **jedem** Nutzer, auch
beim ersten Start, auch ohne ein einziges eigenes Projekt.

**(b) Die drei Projektkarten sind dieselbe Karte.** `StartView.tsx:92-94` — „Sanierungsplan",
„Hausplaner" und „Weiterarbeiten" rufen alle drei `onGuided(1)`. Drei Versprechen, ein Ziel.
„Weiterarbeiten" öffnet kein Bestandsprojekt, sondern beginnt bei Schritt 1.

**(c) Der Konfigurator wirft sein Ergebnis weg.** `ConfigWizard.tsx:220`:
```
a.href = url; a.download = `konfigurator-${art}-${wahl.id}.json`; a.click();
```
Ohne gewählte Wand endet die Konfiguration als **Datei im Download-Ordner**. Gemessen:
`grep -rl 'ConfiguratorPackage' app/ database/migrations/` = **leer** — serverseitig existiert dafür
nichts. Der Nutzer konfiguriert ein Fenster und bekommt eine JSON-Datei.

**Was es dagegen schon gibt:** `HausplanerController` führt `speichern`, `snapshotErstellen` und
`snapshotListe` auf `LeadAlternativeAdd $objekt`. Projekte **sind** CRM-Objekte — es muss nichts
erfunden werden, nur gelesen.

## 2. Zwei Teile, getrennt zu ziehen

| Teil | Inhalt | Gate |
|---|---|---|
| **A · Frontend** | echte Projektliste statt `ZULETZT`, drei Karten mit drei Zielen, ehrlicher Leerzustand | keins — sofort ziehbar |
| **B · Persistenz** | Konfigurator-Paket serverseitig speichern statt Download | **Yamas Freigabe** (Migration + Route + Controller) |

**Teil B beginnt nicht ohne Freigabe.** Wer A meldet und B mitliefert, hat an einer Datenbank
gearbeitet, die ~3000 Kunden trägt, ohne dass jemand zugestimmt hat.

## 3. Teil A — Schnitt

1. **Projektliste aus dem Bestand.** Die zuletzt bearbeiteten Objekte kommen über die vorhandene
   Naht in die Insel (`main.tsx` liest `dataset.*`, Blade setzt die URL — dasselbe Muster wie
   `speichernUrl`). Kein neuer Mechanismus.
2. **`ZULETZT` stillegen, nicht löschen** — Muster `toolCatalogStillgelegt.ts`.
3. **Ehrlicher Leerzustand.** Kein eigenes Projekt ⇒ „Noch kein Projekt geöffnet." plus dem Weg, eins
   anzulegen. **Keine Beispielzeile**, die wie ein Projekt aussieht.
4. **Die drei Karten bekommen drei Ziele.** Führt eine Karte heute nirgendwohin, wird sie **ehrlich
   als `in Entwicklung` ausgewiesen** (vorhandenes `ZustandBadge`, Muster AUF-25) — nicht auf
   `onGuided(1)` umgeleitet, damit sie beschäftigt aussieht.

## 4. Teil B — Schnitt (erst nach Freigabe)

1. **Migration** für das Konfigurator-Paket, an `LeadAlternativeAdd` gehängt wie die Snapshots.
2. **Route + Controller-Methode** neben `snapshotErstellen`, gleiche Rechte-Middleware
   (`permission:Hausplaner,update`), gleicher Konfliktschutz (409) wie beim Dokument-Speichern.
3. **`ConfigWizard`** ruft die Route statt `a.click()`. **Der Download bleibt als Rückfallweg
   erhalten**, wenn das Speichern fehlschlägt — ein Nutzer, der zehn Minuten konfiguriert hat, darf
   sein Ergebnis nicht verlieren, weil das Netz weg war.

## 5. Was **nicht** passiert

- **Kein zweiter Speicherpfad.** Das Paket geht denselben Weg wie das Dokument: Route → Controller →
  Modell. Kein direkter Schreibzugriff, kein eigener Serialisierer.
- **Kein Schema-Eingriff.** `scene-document-v2.schema.json` und die Zod-Quelle bleiben unberührt —
  das Paket ist ein eigenes Datum neben dem Dokument, keine Erweiterung.
- **Keine Rechteänderung.** Ob Import ein eigenes Recht bekommt, ist AUF-41 und gehört Yama.
- **Keine Demo-Daten „nur für die Vorschau".** Der Leerzustand ist der Normalfall beim ersten Start.

## 6. Abnahmekriterien

**Teil A**

1. `tsc:hausplaner` · `schema:hausplaner:check` · `test:hausplaner` — Exit 0, Zahlen vorher/nachher.
2. **K4 unberührt:** `store/`, `domain/`, `geometry/`, `renderers/`, `scene.types` — null Zeilen.
3. **Keine Demo-Daten mehr im Auslieferungspfad:** `grep 'Mustermann'` auf `app/` liefert **nur** in
   der stillgelegten Datei Treffer.
4. **Leerzustand testverriegelt:** ohne Projekte kein Listeneintrag, Text nicht leer, endet nicht auf
   „folgt"/„in Kürze".
5. **Drei Karten, drei Ziele:** Test belegt, dass keine zwei Karten dasselbe Ziel aufrufen — oder,
   wo ein Ziel fehlt, dass die Karte als `in Entwicklung` ausgewiesen ist.
6. **Mutations-Gegenbeweis:** zwei Karten auf dasselbe Ziel legen ⇒ mindestens ein Test rot.

**Teil B**

7. **Rechte greifen:** ein Test belegt, dass die Route ohne `Hausplaner,update` **403** liefert.
8. **Konfliktschutz:** gleiches 409-Verhalten wie beim Dokument-Speichern, mit Test.
9. **Rückfallweg:** schlägt das Speichern fehl, entsteht weiterhin die Datei — testverriegelt.
10. **Migration ist umkehrbar** (`down()` vorhanden und geprüft).

**Beide**

11. **`public/*` im Code-Commit: null Zeilen**, Bundle-Rebuild als eigener zweiter Commit
    (§8 Punkt 2b in `docs/agents/06-laufzeiten-und-takt.md`).
12. **Klassifikation: `sichtbar`.** Sichtprobe in die Abnahme — ausdrücklich **mit einem Konto ohne
    eigene Projekte**, weil dort der heutige Mangel sichtbar ist.

## 7. Zurückgegeben statt mitgebaut

Fehlt für die Projektliste eine Route oder ein Feld, das es geben müsste: **benennen**, nicht
nebenbei anlegen. Alles, was `routes/` oder `app/Http/` berührt, ist in Teil A **verboten** —
es gehört in Teil B und damit hinter Yamas Freigabe.
