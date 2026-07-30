# AUF-88 — Dateiplattform: die Bestandsaufnahme, die vor dem ersten Blatt steht

*Planner, 30.07.2026, 06:50 CEST. Grundlage: Yamas Master-Prompt „Import-, Export-,
Dateierkennungs- und Konvertierungsplattform" vom 30.07.*

> **§25 des Prompts heißt „Bestandscode-first" und listet zwanzig Dinge, die zuerst zu prüfen sind.
> Das ist keine Formalie — es ist die teuerste Zeile des ganzen Papiers.** Ich habe eine Stunde
> lang nichts anderes getan, und das Ergebnis ändert den Zuschnitt erheblich:
> **ein erheblicher Teil dessen, was der Prompt zu bauen verlangt, steht bereits — unter anderen
> Namen.**

---

## 1. Was ich gemessen habe (Befehle in §6)

### Es gibt bereits eine Plan-Import-Strecke

```text
app/Models/PlanUpload.php                         Modell
database/migrations/2026_07_08_180006_create_plan_uploads_table.php
app/Jobs/PlanKlassifizieren.php                   Job
app/Services/Import/ImportServiceClient.php       externer Dienst
routes/web.php:5680-5684                          /admin/energie/plan-upload
```

**`PlanUpload` trägt heute schon**, gemessen aus `$fillable`:

```text
user_id · heizlast_projekt_id · original_name · pfad · mime · groesse_bytes
typ · status · massstab_mm_pro_einheit · kandidat_geometrie · konfidenz · meta
```

**Vergleiche das mit `ImportedFile` aus §19 des Prompts.** Es fehlen `checksum`,
`detected_version`, `security_status` und der Projektbezug — **aber `typ`, `status`,
`konfidenz`, `massstab` und `meta` sind bereits da.** Das ist kein leeres Feld, das ist ein
Vorgänger.

**Und `PlanKlassifizieren` tut genau das, was §3 verlangt** — sein eigener Kommentar:

> *„Grobe Klassifikation eines hochgeladenen Plans (A-3a): Typ aus Endung/MIME + einfache
> Magic-Bytes-Prüfung."*

**Die Erkennung „nicht nur nach der Endung" ist im Kern gebaut.** Der Job stößt bei aktivem
`IMPORT_SERVICE_URL` eine DXF/PDF-Vektorextraktion an und bleibt sonst *graceful* bei
`status='klassifiziert'`.

**Der Kommentar nennt auch, was fehlt:** die Folge-Jobs `PlanVektorExtrahieren`,
`PlanPdfExtrahieren` und `PlanBildVermessen` sind aus `wberechnung` **nicht portiert.**

### Das „BuildingDocument" aus §8 existiert — als kanonisches Gebäudemodell v1

```text
resources/schemas/building-model/v1.schema.json     8 863 Byte, Vertrag v1
app/Services/BuildingModel/                         916 Zeilen, 8 Klassen
  CanonicalBuildingModelValidator     rein, ohne DB/Request/Datei — KEINE stillen Korrekturen
  CanonicalHash                       Hash des kanonischen Modells
  DerivedBuildingModelVersionStore    Versionen
  SourceGeometryRef                   Quell-Identitaet + Roh-Hash  ← das ist §15, Provenienz
  BuildingModelVersionImmutableException  Revisionen sind unveraenderlich
  ProjectionConflictException         Konflikt beim Ableiten
```

**§8 des Prompts verlangt: BuildingDocument + Revision + Objekt-IDs + Herkunftsnachweise.
§15 verlangt Provenienz mit Quell-Hash. Beides steht.**

Und die Grenzen, die dort schon gezogen sind, sind genau die richtigen — der Validator sagt es
selbst: *„KEINE stillen Korrekturen — kein Knoten-Merge, kein Polygon-Schließen, keine Defaults,
keine ID-Erzeugung, keine Reparatur ungültiger Geometrie."* Und: *„Es gibt KEIN zweites
Toleranzregime."*

### Was wirklich fehlt

| Verlangt | Gemessener Stand |
|---|---|
| **Virenprüfung** (§14) | **0 Treffer** für `clamav`, `virusscan`, `virustotal` in `app/`, `config/`, `composer.json` — **es gibt keine** |
| **Format Registry** (§5) | existiert nicht; die Klassifikation steckt im Job, nicht in Daten |
| **Konvertierungsmatrix** (§11) | existiert nicht |
| **Import-/Exportwizard** (§9/§10) | existiert nicht |
| **Job-Center** (§18) | existiert nicht; Queue ja, Sichtfläche nein |
| **Bibliotheken** | nur `barryvdh/laravel-dompdf` und `intervention/image`. **Kein `phpspreadsheet`, kein DWG-, IFC-, glTF- oder STEP-Leser** |

### Und ein Befund, der nicht im Prompt steht, aber sein wichtigstes Verbot betrifft

**§25 sagt: „Keine parallele Dateiablage und keine zweite Dokumentenverwaltung entwickeln."**

**Gemessen: es gibt bereits sechzehn.**

```text
AppointmentAttachment · ChatAttachment · CustomerProductInfoMedia · DailyReportAttachment
EmployeeDocument · GoodsReceiptAttachment · InvoiceFile · LearningTopicMedia
MaintenanceDocuments · OfferFolderAttachment · PersonalTaskAttachment · PlanUpload
ProductDocuments · ProjectTaskAttachment · TaskDocument · TicketFile
```

**23 Migrationen mit `attachment`/`document`/`file`/`media` im Namen.**

*Es gibt kein „das Ticket-DMS", auf das man aufsetzen könnte — es gibt sechzehn Halbe.* **Das ist
kein Argument gegen den Auftrag, aber es ändert seine erste Frage:** nicht *„wie hängen wir uns an
das DMS?"*, sondern *„welches der sechzehn ist das, an das wir uns hängen — und wird es dadurch
das siebzehnte oder das erste?"*

---

## 2. Was das für den Zuschnitt heißt

**Die dreizehn Phasen FILE-01 bis FILE-13 stehen so nicht.** Nicht weil sie falsch wären, sondern
weil zwei davon zum Teil erledigt sind und eine im Prompt fehlt.

### Neu geschnitten, sechs Blöcke statt dreizehn Phasen

**A. Die Bestandsentscheidung** *(keine Zeile Code)*
Welches der sechzehn Ablagemodelle wird die Heimat? Wird `PlanUpload` erweitert oder abgelöst?
**Diese Frage gehört Yama, nicht mir** — sie betrifft das ganze CRM, nicht den Hausplaner.

**B. Format Registry und Erkennung als DATEN** *(FILE-01)*
`PlanKlassifizieren` erkennt heute in Code. Der Prompt verlangt eine versionierte Registry mit
Magic Bytes, Versionen, Fähigkeiten und Verlustgraden. **Der Umbau ist: aus Code werden Daten** —
dasselbe Muster wie `arbeitsbereiche.ts`, `panelTabs.ts`, `werkzeugVertrag.ts` im Hausplaner.
*Und `werkzeugVertrag.ts` zeigt auch die Falle: 110 Verträge als Daten, aber kein Dispatcher.
Eine Registry beschreibt — sie führt nicht aus.*

**C. Sicherheit** *(FILE-02)*
**Der einzige Block, der bei null anfängt, und der einzige mit einem echten Risiko.**
Virenprüfung, ZIP-Bomben-Schutz, Entpackgrenzen, XXE, Path Traversal, isolierte Worker.
*Ohne ihn darf FILE-05 bis FILE-07 gar nicht beginnen: DWG-, IFC- und STEP-Leser sind
historisch die verwundbarste Klasse von Parsern, die es gibt.*

**D. Die Wizards** *(FILE-03, FILE-08)*
Import und Export als geführter Prozess. **Hier trifft der Auftrag auf die laufende Layout-Kette:**
§17 sagt ausdrücklich *„Keine weitere dauerhafte Kopfleiste anlegen"* — das ist wortgleich das
Ziel von AUF-83. **Diese Blöcke gehören hinter T3 und T5, nicht daneben.**

**E. Die Formate, einzeln** *(FILE-04 bis FILE-07, FILE-09 bis FILE-11)*
**Jedes Format ist ein eigener Vorgang mit eigener Bibliothek, eigener Lizenzfrage und eigenem
Verlustbericht.** Reihenfolge nach Yamas eigener Freigabetabelle: PDF und Bilder zuerst, dann
SVG, dann DXF/DWG, dann IFC, dann GLB, dann STEP.

**F. Quellaktualisierung und Job-Center** *(FILE-12, FILE-13)*
Zuletzt, weil sie alles davor voraussetzen.

---

## 3. Die Frage, die ich Yama vorlegen muss

**§13 gilt: einspurig, kein Parallelbetrieb.**

**Diese Plattform ist ein Vielfaches der Layout-Kette.** Allein Block C und E sind Wochen, nicht
Tage — und sie brauchen Bibliotheken, die heute nicht im Baum sind, teils mit Lizenzfragen
(DWG-Leser sind der klassische Fall).

> ### Willensfrage an Yama
> **Was hat Vorrang?**
> 1. **Frontend zuerst fertig** — T3, T5, dann AUF-48 und AUF-50. Die Dateiplattform beginnt mit
>    Block A und B (beide ohne Produktivcode) und wartet ab da.
> 2. **Dateiplattform zuerst** — dann steht die Layout-Kette bei T3 still, und die 110 Werkzeuge
>    rücken erheblich nach hinten.
> 3. **Ein schmaler Schnitt vorweg** — nur PDF-Import als Referenz, mit Maßstabs-Kalibrierung.
>    *Das ist der Teil, den ein Planer täglich braucht, und `PlanKlassifizieren` erkennt PDF
>    bereits.*
>
> **Meine Empfehlung ist 3, und der Grund ist gemessen, nicht gefühlt:** ein PDF-Grundriss als
> kalibrierte Referenz macht den Hausplaner für den ersten echten Auftrag brauchbar, er kostet
> keinen neuen Parser, und er ist der einzige Formatweg, dessen halbe Strecke schon steht.
> **Alles andere setzt Block C voraus — und Block C ist Sicherheitsarbeit, die man nicht
> zwischendurch macht.**

---

## 4. Was ich ausdrücklich NICHT getan habe

**Ich habe keine dreizehn Auftragsblätter geschrieben.** Das wäre F-07 im größten Maßstab —
dreizehn Blätter gegen einen Bestand, den ich vor einer Stunde noch nicht kannte. *Fünf Mal hat
diese Fehlerklasse heute schon zugeschlagen, zuletzt an einem einzigen Kriterium.*

**Ich habe keinen Aufwand geschätzt.** Er käme vor Block A und wäre geraten.

**Und ich habe nichts über die sechzehn Ablagemodelle entschieden.** Das ist eine CRM-Frage mit
~3000 Kunden dahinter — *dieselbe Klasse wie AUF-84, und die liegt aus demselben Grund bei Yama.*

---

## 5. Was noch nicht gemessen ist

**Damit niemand diese Aufnahme für vollständig hält:**

- die **Storage-Konfiguration** (Platten, Sichtbarkeit, Signaturen) — nicht angesehen
- die **Berechtigungslogik** an den sechzehn Ablagemodellen — nicht angesehen
- der **`ImportServiceClient`** im Detail: welcher Dienst, welche Formate, welche Verfügbarkeit
- die **Queue-Konfiguration** (Treiber, Worker, Zeitlimits)
- ob `wberechnung` die drei nicht portierten Jobs noch trägt und in welchem Zustand

**Das gehört in Block A** und ist der Grund, warum Block A kein Nebensatz ist.

---

## 6. Messbefehle

```text
ls app/Models/ | grep -i "file|document|media|attach|upload"          → 16
ls database/migrations/ | grep -ic "attachment|document|file|media"   → 23
grep -rli "clamav|virusscan|virustotal" app/ config/ composer.json    →  0
wc -l app/Services/BuildingModel/*.php                                → 916
wc -c resources/schemas/building-model/v1.schema.json                 → 8863
sed -n '15,20p' app/Models/PlanUpload.php                             → die 12 Felder
head -30 app/Jobs/PlanKlassifizieren.php                              → Magic-Byte-Pruefung + Portluecke
```

**Alle sechs sind am 30.07. zwischen 06:46 und 06:50 gefahren worden.**
