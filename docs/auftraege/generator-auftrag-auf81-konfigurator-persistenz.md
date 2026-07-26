# ⇒ GENERATOR-AUFTRAG AUF-81 — Konfigurator-Pakete serverseitig (B7 / AUF-40 Teil B)

**Vom:** Planner · **26.07.2026, 13:15** · **Spur A** · **Heimat-App:** `ticket`
**Tor 1: von Yama freigegeben** (26.07.: *„wir brauchen Datenbank, Migration, Routing,
Pagination"*) — **er hat seine Entscheidung vom Vormittag damit bewusst weitergedreht.**
**GESPERRT bis der Merge nach `main` durch ist** (§1).

**Vorher gelesen:** HEAD `166dc92` ·
`database/migrations/2026_07_16_211128_create_hausplaner_foundation_tables.php` (das Muster) ·
`app/Domain/Hausplaner/Models/HausplanerDocument.php` · `HausplanerController::index()` ·
`routes/web.php:4988-4997` · `geometry/configuratorPackage.ts` · `app/ConfigWizard.tsx:226-239`.

---

## 1. Warum dieser Posten **nach** dem Merge kommt — und nicht davor

**Merge-Bedingung 5 lautet: keine Migration im Merge.** Heute gemessen: **0**. Das macht den Deploy
zu einem **reinen Code-Deploy** — Rückweg: vorherigen Stand ausrollen.

**Dieser Posten bringt die erste Migration.** Damit wechselt der Deploy die Risikoklasse: aus
„vorherigen Stand ausrollen" wird „vorherigen Stand ausrollen **und** die Datenbank betrachten".

**Beides in einem Schritt wäre der Fehler.** Erst den geprüften, migrationsfreien Stand
ausliefern — **dann** die Datenbank anfassen, als eigener, sichtbarer Vorgang. *Wer beides mischt,
kann bei einem Fehler nicht mehr sagen, welche Hälfte ihn verursacht hat.*

## 2. Die Sicherheitseigenschaft, auf der alles ruht

**Es wird eine neue Tabelle angelegt und keine bestehende angefasst.**

Damit gilt: **kein Bestandsdatensatz wird verändert, keine Spalte umbenannt, keine Kette
(Angebot → Auftrag → Rechnung) berührt.** Der Rückweg ist das Verwerfen einer Tabelle, die es vorher
nicht gab — **und dabei geht kein Kundendatensatz verloren, weil in ihr nur Neues steht.**

**Das ist die Bedingung, unter der ich diesen Posten überhaupt schneide.** Verlangt die Umsetzung
eine Änderung an einer bestehenden Tabelle: **melden, nicht bauen.**

## 3. Was gebaut wird

### (a) Migration — nach dem vorhandenen Muster, nicht nach eigenem

Die Vorlage steht in `2026_07_16_211128_create_hausplaner_foundation_tables.php` und wird
**gelesen, nicht neu erfunden**: **idempotent** (`if (Schema::hasTable(…)) return;`), `bigint`-IDs,
`json`-Spalten, **defensiver** Fremdschlüssel (nur wenn die Zieltabelle existiert), MySQL-Semantik
ohne Roh-Abfragen.

**Felder, so wenig wie möglich:**

| Feld | warum |
|---|---|
| `user_id` | **der Besitzer.** Ohne ihn gibt es kein Eigentumsgatter — und ohne das ist die Liste ein Leck |
| `alternative_id` **nullable** | **der Konfigurator läuft autark, ohne Gebäude.** Ein Pflichtfeld hier würde genau den Fall verbieten, der die Fläche stark macht |
| `art`, `titel`, `status` | das, was die Liste anzeigt |
| `schema_version` | `CONFIGURATOR_SCHEMA_VERSION` = 1, für spätere Migration der Inhalte |
| `paket` (json) | das Paket selbst, unverändert wie heute heruntergeladen |
| Zeitstempel | Sortierung |

**Kein `tenant_id`** — die Bestandstabelle hat auch keines; additiv nachrüstbar. **Kein Freitext,
der nicht angezeigt wird. Keine Kundendaten.**

### (b) Routen — drei, nicht mehr

| Route | Recht |
|---|---|
| `POST …/konfigurator-pakete` (speichern) | **`permission:Hausplaner,add`** |
| `GET …/konfigurator-pakete` (Liste, **paginiert**) | `permission:Hausplaner,read` |
| `GET …/konfigurator-pakete/{paket}` (eines) | `permission:Hausplaner,read` |

**Kein Löschen, kein Ändern in diesem Posten.** Beides braucht eine Entscheidung darüber, was mit
einem Paket passiert, das schon in einem Angebot steckt — **das ist eine Fachfrage und keine Route.**

**`Hausplaner,add` zum Speichern** ist die Zuordnung aus AUF-53 und **nicht neu erfunden**:
`User::hasPermission` kennt genau `read · add · update · delete` und schickt jede unbekannte Aktion
auf `is_read` — **eine erfundene Aktion wie `import` hätte nichts geschützt.**

### (c) Das Eigentumsgatter — der Punkt, an dem so etwas leckt

**Ein Nutzer sieht und öffnet ausschließlich seine eigenen Pakete.** Die Kennung aus der Anfrage
wird **nie** ohne Eigentumsprüfung verwendet — Bauordnung `ticket`: *keine ID aus dem Request ohne
Ownership-Gate.*

**Und die Liste filtert am Server, nicht in der Anzeige.** *Eine Liste, die alles lädt und die
Hälfte ausblendet, ist bereits geleakt.*

### (d) Paginierung

**Wie `index()` es schon macht:** `paginate(25)`, `appends`, absteigend nach Zeitstempel. **Kein
eigenes Blätterwerk** — dieselbe Mechanik wie die vorhandene Objektliste.

### (e) Die Insel-Seite

Der ConfigWizard **speichert zusätzlich**, statt herunterzuladen. **Der Download bleibt** — er ist
der Weg für alle, die kein Speicherrecht haben, und er funktioniert heute.

**Und der Satz aus AUF-74 wird nachgezogen:** wo heute „Ergebnis: eine Datei zum Herunterladen"
steht, muss stehen, was **jetzt** passiert. **Genau die Sorgfalt, die AUF-74 hergestellt hat, darf
dieser Posten nicht wieder verlieren** — der Fehlerfall inbegriffen (`catch` verschluckt nichts).

## 4. Was **nicht** gebaut wird

- **Keine Änderung an einer bestehenden Tabelle, Spalte oder Beziehung.**
- **Kein Löschen, kein Überschreiben, keine Versionierung von Paketen.**
- **Keine Übernahme ins Gebäudemodell aus der Liste heraus.** Das geht heute über den Experten und
  bleibt dort — sonst hat der Posten zwei Themen.
- **Kein Anfassen von `store/`, `domain/`, `geometry/`, `renderers/`, `scene.types`** und **keine
  Änderung an `HausplanerDocument`**.
- **Kein Deploy, kein `main`-Merge** (Tor 2 = Yama).

## 5. Abnahmekriterien

1. `tsc:hausplaner` · `schema:hausplaner:check` · `test:hausplaner` · **volle PHP-Suite** — Exit 0,
   Zahlen vorher/nachher. **§9 gilt: die Suite läuft mit.**
2. **Keine bestehende Tabelle berührt:** die Migration enthält **kein** `Schema::table(…)`, kein
   `dropColumn`, kein `rename`. `grep` mit Zahl.
3. **Idempotent wie die Vorlage:** zweimal migrieren ⇒ kein Fehler, kein zweiter Tabellenanlauf.
4. **Der Rückweg ist ausgeführt, nicht behauptet:** `migrate` → `rollback` → `migrate`, alle drei
   Schritte im Bericht mit Ausgabe. **Ein nie erprobter Rückweg ist kein Rückweg.**
5. **Eigentumsgatter, testverriegelt:** Nutzer A legt ein Paket an, Nutzer B ruft Liste **und**
   Einzelansicht ⇒ **A's Paket erscheint nicht und ist nicht abrufbar** (404/403, kein Teilinhalt).
   **Das ist das wichtigste Kriterium dieses Postens.**
6. **Ohne Recht kein Zugriff:** Nutzer ohne `Hausplaner,read` ⇒ Liste abgewiesen; ohne
   `Hausplaner,add` ⇒ Speichern abgewiesen. Je ein Test.
7. **Serverseitig gefiltert:** ein Test belegt, dass die Abfrage **bereits** auf den Besitzer
   eingeschränkt ist — nicht die Anzeige.
8. **Paginierung greift:** 30 Pakete ⇒ 25 auf Seite 1, 5 auf Seite 2, **eine** Abfrage je Seite
   (kein N+1).
9. **Autark bleibt autark:** ein Paket **ohne** `alternative_id` lässt sich speichern und wieder
   abrufen. Testverriegelt.
10. **Der Download bleibt** und funktioniert; der Fehlerfall aus AUF-74 meldet weiterhin **keinen
    Erfolg ohne Datei**. Beide erneut vorgeführt.
11. **Kein `@php`-Block im Blade** (AUF-64); `public/*` im Code-Commit **null Zeilen**,
    Bundle-Rebuild als eigener zweiter Commit.
12. **Klassifikation: `sichtbar`.** Sichtprobe nach §11 mit Zustand: speichern, Liste öffnen, Paket
    wiederfinden — **in einem Konto mit und einem ohne Pakete.**

## 6. Was zurückgegeben wird

- **Verlangt irgendetwas eine Änderung an einer bestehenden Tabelle: melden, nicht bauen.** Das ist
  die Bedingung aus §2, und sie ist nicht verhandelbar.
- **Zeigt sich, dass ein Paket ohne Besitzer entstehen kann** (Hintergrundlauf, Systemvorgang):
  benennen. **Ein Datensatz ohne Eigentümer ist ein Datensatz, den das Gatter nicht schützt.**
- **Braucht die Liste ein Feld, das die Fläche nicht anzeigt:** nicht durchreichen. Melden.
