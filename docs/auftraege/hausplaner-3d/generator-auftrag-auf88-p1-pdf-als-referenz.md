# AUF-88-P1 — Der PDF-Grundriss als maßstabsgetreue Referenz

*Planner, 30.07.2026, 06:55 CEST. **Yamas Entscheid von 06:52: der schmale Schnitt vorweg.**
Aus dem Master-Prompt vom 30.07. wird zuerst genau ein Weg gebaut — der, dessen halbe Strecke
schon steht.*

> **Warum genau dieser Schnitt: er kostet keinen neuen Parser.** Gemessen steht serverseitig
> bereits alles Nötige — `ImportServiceClient` kann `extractPdf`, `rasterizePdf`, `ocr`,
> `ocrTexte`; `PlanKlassifizieren` prüft Magic Bytes; `PlanUploadController::bild()` liefert das
> abgeleitete Raster-PNG aus. **Was fehlt, ist die Unterlage in der Zeichenfläche und die
> Kalibrierung.**

```yaml
auftrag:
  id: AUF-88-P1
  strang: hausplaner-3d
  status: gesperrt
  sperrgrund: >
    Wartet auf den BAU von AUF-83-T3 und AUF-83-T5. Grund: §17 des Master-Prompts verlangt
    ausdruecklich *„keine weitere dauerhafte Kopfleiste"* — der Einstieg gehoert in die
    Arbeitszeile, die T3 gerade fertigstellt, und die Unterlage teilt sich die Buehne mit T5.
    **Die Sperre endet mit dem Bau, nicht mit der Abnahme** (R10).
  spur: A
  heimat: ticket
  ziel: >
    Ein hochgeladener PDF-Grundriss erscheint im Hausplaner als gesperrte Unterlage unter der
    Zeichnung, sein Massstab wird ueber eine bekannte Strecke kalibriert, und die Herkunft der
    Referenz ist nachweisbar.
  nicht_ziel: >
    KEIN neues Ablagemodell — `PlanUpload` ist die Heimat (Block A der Aufnahme).
    KEIN DWG-, IFC-, GLB- oder STEP-Weg. KEINE Konvertierung. KEIN Exportweg.
    KEINE Uebernahme des PDF-Inhalts ins BuildingDocument — **das PDF bleibt Referenz.**
    KEIN Import-Wizard mit neun Schritten; ein Dialog reicht fuer einen Weg.
    KEINE Aenderung an `resources/schemas/building-model/v1.schema.json` ohne eigenen Vorgang.

geerbte_zusagen:                       # R17, erste Anwendung
  befehl: "grep -rl 'PlanUpload\\|plan-upload\\|plan_upload' tests/"
  ergebnis: >
    **NULL.** Gemessen 30.07. 06:53: **es gibt keine einzige Zusage zu `PlanUpload`,
    `PlanUploadController` oder `PlanKlassifizieren`.** Ein Endpunkt, der Dateien annimmt, sie
    auf die Platte schreibt und einen Job anstoesst, ist vollstaendig unverriegelt.
  folge: >
    **Nichts verriegelt diese Flaeche — also verriegelt dieser Auftrag sie als Erster.**
    Das ist keine Zugabe: wer als Erster eine ungetestete Flaeche anfasst, hinterlaesst entweder
    Zusagen oder eine groessere ungetestete Flaeche.

scope:
  population_command: >
    grep -n 'ENDUNGEN\|getClientOriginalExtension' app/Http/Controllers/Energie/PlanUploadController.php &&
    grep -c 'Konva.Image\|<Image' resources/planner/hausplaner/app/*.tsx &&
    grep -rn 'massstab_mm_pro_einheit' app/ resources/ --include=*.php --include=*.ts --include=*.tsx
  # ENTFAELLT nach R19 (30.07.) — die folgenden Zeilen sind HERKUNFTSNACHWEIS, KEINE Bedingung.
  # Wer die Zahl braucht, faehrt population_command. Neue Blaetter tragen dieses Feld nicht mehr.
  population_at_writing_ALT: >
    Messung des Planners, 30.07. 06:53, KEINE Bedingung:
    (1) `ImportServiceClient` (139 Z.) kann `extractPdf`, `rasterizePdf`, `ocr`, `ocrTexte`,
        `extractDxf` — und meldet ueber `aktiv()`, ob der Dienst konfiguriert ist.
    (2) `PlanUploadController` (111 Z.): `store` mit `max:51200` (50 MB), `destroy` raeumt das
        abgeleitete PNG mit, `bild()` liefert es mit `abort_unless(... Storage::exists ...)`.
    (3) `PlanUpload.massstab_mm_pro_einheit` EXISTIERT bereits als Feld (float) — die Kalibrierung
        war vorgesehen und ist nie gebaut worden.
    (4) `werkzeugPaket.ts:228-241` fuehrt die Werkzeuge bereits als Vertrag:
        *„PDF, Bild, DWG, DXF, IFC oder SVG laden"* · *„Bild importieren"* ·
        *„Bildmassstab ueber bekannte Strecke bestimmen"* · Einsatz: *„PDF und Bild."*
        **Der Funktionsvertrag steht. Die Funktion fehlt.**
    (5) In der Insel: **0 `Konva.Image`, 0 `<Image`** — es gibt keine Unterlage-Ebene.
    (6) `PlanUpload` haengt heute an `heizlast_projekt_id`, **nicht an einem Hausplaner-Projekt.**
  pfade:
    - app/Http/Controllers/Energie/PlanUploadController.php
    - app/Models/PlanUpload.php
    - database/migrations/<neu>_plan_uploads_projektbezug.php
    - tests/Feature/PlanUploadTest.php                      # NEU — die erste Zusage ueberhaupt
    - resources/planner/hausplaner/app/unterlage/            # NEU
    - resources/planner/hausplaner/__tests__/unterlage.test.ts
  ausschluesse:
    - stelle: "die uebrigen fuenfzehn Ablagemodelle"
      grund: >
        Die Aufnahme hat sechzehn gemessen. Welches die Heimat der Plattform wird, ist Block A
        und gehoert Yama. **Dieser Auftrag benutzt `PlanUpload`, weil es schon da ist — und
        entscheidet damit NICHTS ueber die anderen fuenfzehn.**
      entschieden_von: planner
    - stelle: "der Inhalt des PDF"
      grund: >
        Vektoren, Texte und Masse aus dem PDF ins BuildingDocument zu uebernehmen ist ein eigener
        Vorgang mit eigener Fehlerklasse. **Hier wird ein Bild unter den Plan gelegt, mehr nicht.**
      entschieden_von: planner

sicherheit:
  gemessener_mangel: >
    **P1, gefunden beim Zuschnitt:** die Annahme prueft heute die **Dateiendung** —
    `in_array(strtolower($value->getClientOriginalExtension()), self::ENDUNGEN)`. Die
    Magic-Byte-Pruefung laeuft ERST im Job, **nachdem die Datei auf der Platte liegt.**
    *Genau das verbietet §3 des Master-Prompts: „Die Erkennung darf nicht nur anhand der
    Dateiendung erfolgen."* Und §14 verlangt eine Virenpruefung — **gemessen 0 Treffer im ganzen
    Baum.**
  was_dieser_auftrag_tut: >
    **Er verschiebt die Signaturpruefung VOR das Speichern** — dieselbe Pruefung, die
    `PlanKlassifizieren` schon kann, nur frueher. Mehr nicht.
  was_er_ausdruecklich_NICHT_tut: >
    **Keine Virenpruefung.** Sie braucht einen Dienst, eine Betriebsentscheidung und einen
    Rueckweg — das ist Block C und gehoert Yama. **Sie bleibt als offener Posten stehen und wird
    nicht durch eine Signaturpruefung ersetzt.** *Eine Magic-Byte-Pruefung erkennt eine
    umbenannte EXE. Sie erkennt kein praepariertes PDF.*

rueckweg_und_entdeckung:              # Spur-A-Pflicht
  rueckweg: >
    Die Migration fuegt EINE nullable Spalte hinzu und legt KEINE Daten an. Zurueckdrehbar ohne
    Datenmigration. **Kein Bestandsfeld wird geaendert, keins entfernt.**
  entdeckung: >
    Geht der Projektbezug schief, zeigt der Hausplaner keine Unterlage — sichtbar, sofort, ohne
    stillen Datenschaden. **Der PDF-Bestand in `plan-uploads` bleibt unberuehrt.**

kriterien:
  - id: K-01
    aussage: "Die Signaturpruefung liegt VOR dem Speichern."
    typ: behavioural
    kritikalitaet: P1
    pruefung:
      befehl: "php artisan test --filter=PlanUploadTest"
      erwartet: >
        Eine als `.pdf` benannte Datei mit falschem Header wird **abgelehnt, ohne dass sie in
        `plan-uploads` liegt.** Geprueft wird der Speicherort, nicht die Fehlermeldung.
    beleg: testausgabe + `Storage::fake` Verzeichnisinhalt
    gegenprobe: >
      Die Signaturpruefung entfernen ⇒ MUSS rot werden. **Und die Datei muss dann WIRKLICH auf
      der Platte liegen** — sonst prueft die Zusage die Meldung statt die Wirkung (F-06).

  - id: K-02
    aussage: "Ein PDF-Upload ist einem Hausplaner-Projekt zuzuordnen."
    typ: presence
    kritikalitaet: P1
    pruefung:
      befehl: "php artisan test --filter=PlanUploadTest"
      erwartet: >
        Eine nullable Spalte, ein Ownership-Gate beim Setzen. **Ein fremdes Projekt darf nicht
        zuweisbar sein** — geprueft mit einem zweiten Nutzer, nicht mit einer Behauptung.
    beleg: testausgabe
    gegenprobe: "das Gate entfernen ⇒ MUSS rot werden"
    begruendung: >
      Bauordnung ticket: *keine ID aus dem Request ohne Ownership-Gate.* Die heutige Prueflogik
      fuer `heizlast_projekt_id` ist `Rule::exists` — **das prueft Existenz, nicht Zugehoerigkeit.**
      *Ich stelle das als Beobachtung fest, nicht als Urteil ueber den Bestand: der Evaluator
      soll es nachmessen, bevor jemand es repariert.*

  - id: K-03
    aussage: "Die Unterlage liegt UNTER der Zeichnung und ist gesperrt."
    typ: behavioural
    kritikalitaet: P1
    pruefung:
      befehl: "npm run test:hausplaner -- --filter=unterlage"
      erwartet: >
        Die Unterlage ist die unterste Ebene, faengt keine Klicks und ist nicht auswaehlbar.
        **Sie ist kein Knoten im Modell** — sie erscheint in keiner Auswahl, keiner Mengenliste,
        keinem Export.
    beleg: testausgabe
    gegenprobe: >
      Die Unterlage auswaehlbar machen ⇒ MUSS rot werden.
    grenze: >
      **Sie gehoert nicht ins BuildingDocument.** Das kanonische Schema v1 beschreibt das Gebaeude;
      eine Referenzunterlage ist eine Bedien-Beigabe — dieselbe Grenze wie beim Arbeitsbereich in
      `arbeitsbereichSpeicher.ts`. *Wer sie ins Schema schreibt, migriert Bestandsdaten fuer ein Bild.*

  - id: K-04
    aussage: "Der Massstab wird ueber eine bekannte Strecke kalibriert — und gespeichert."
    typ: behavioural
    kritikalitaet: P1
    pruefung:
      befehl: "npm run test:hausplaner -- --filter=unterlage && php artisan test --filter=PlanUploadTest"
      erwartet: >
        Zwei Punkte auf der Unterlage plus eine eingegebene Laenge ergeben
        `massstab_mm_pro_einheit`. **Das Feld existiert bereits** — es wird benutzt, nicht neu
        erfunden. Die Rechnung ist eine reine Funktion mit eigener Zusage, nicht im Renderer.
    beleg: testausgabe
    gegenprobe: >
      Die Strecke halbieren ⇒ der Massstab MUSS sich verdoppeln. *Eine Zusage, die nur einen Wert
      festhaelt, ginge auch bei einer konstanten Rueckgabe gruen.*
    begruendung: >
      `werkzeugPaket.ts:236` fuehrt das Werkzeug bereits als Vertrag: *„Bildmassstab ueber bekannte
      Strecke bestimmen."* **Der Funktionsvertrag steht seit AUF-36. Hier entsteht die Funktion —
      und KEIN zweiter Vertrag daneben.**

  - id: K-05
    aussage: "Die Herkunft der Unterlage ist nachweisbar."
    typ: presence
    kritikalitaet: P2
    pruefung:
      typ: visuell
      erwartet: >
        Am Plan steht, aus welcher Datei die Unterlage stammt, wann sie hochgeladen wurde und mit
        welchem Massstab sie liegt. **Kein neues Provenienz-System** — `PlanUpload` traegt
        `original_name`, `mime`, `groesse_bytes` und `meta` bereits.
    beleg: Bildschirmfoto + DOM-Auszug

  - id: K-06
    aussage: "Ohne konfigurierten Import-Dienst bricht nichts."
    typ: behavioural
    kritikalitaet: P1
    pruefung:
      befehl: "php artisan test --filter=PlanUploadTest"
      erwartet: >
        Mit leerem `IMPORT_SERVICE_URL` bleibt der Upload moeglich, die Klassifikation endet bei
        `status='klassifiziert'`, **und die Oberflaeche sagt, warum keine Unterlage erscheint.**
    beleg: testausgabe
    begruendung: >
      `ImportServiceClient::aktiv()` und `PlanKlassifizieren` sind bereits *graceful* gebaut.
      **Diese Zusage haelt fest, dass sie es bleiben** — und dass der Nutzer den Grund erfaehrt,
      statt eine leere Flaeche zu sehen.

  - id: K-07
    aussage: "Der Einstieg sitzt in der Arbeitszeile — keine neue Kopfleiste."
    typ: absence
    kritikalitaet: P1
    pruefung:
      typ: visuell
      erwartet: >
        `Importieren` erscheint in der Arbeitszeile aus AUF-83-T3. **Es bleiben genau drei Zeilen.**
    beleg: Bildschirmfoto + Zaehlung der Zeilen
    begruendung: >
      §17 des Master-Prompts, woertlich: *„Keine weitere dauerhafte Kopfleiste anlegen."*
      **Dasselbe Ziel wie AUF-83 — deshalb haengt dieser Auftrag hinter T3.**

  - id: K-08
    aussage: "Gates ohne Regression, nichts ausserhalb des Scopes."
    typ: absence
    kritikalitaet: P1
    pruefung:
      befehl: "git show --name-only --pretty=format: HEAD && npm run tsc:hausplaner && npm run schema:hausplaner:check && npm run test:hausplaner && npm run test:hausplaner:dom && php artisan test"
      erwartet: "genau die Pfade aus scope; Gates 0/0/0/0; PHP-Suite ohne Regression"
    beleg: dateiliste + testzaehler vorher/nachher

selbstnachweis:
  quittung_zuerst: "Readiness-Quittung nach §2, mit der Dateiliste nach R12."
  vorher_wert_pflicht: >
    **F-13-Barriere.** Vor dem Bau festhalten: die Zahl der PHP-Zusicherungen, die Zahl der
    Insel-Zusagen, und `grep -rl 'PlanUpload' tests/ | wc -l` (heute **0**). *Die letzte Zahl ist
    die interessanteste — sie sagt beim Abnehmen, ob dieser Auftrag die Flaeche wirklich
    verriegelt hat.*
  bestandscode_first: >
    **Vor jeder neuen Zeile:** `ImportServiceClient`, `PlanKlassifizieren`,
    `PlanUploadController::bild`, `werkzeugPaket.ts:228-241` lesen. **Was dort steht, wird
    benutzt — nicht nachgebaut.** *Diese Fehlerklasse (F-07) hat heute fuenf Auspraegungen.*
```

---

## Warum das PDF eine Referenz bleibt und kein Gebäudemodell wird

**Der Master-Prompt sagt es selbst** (§6.1): *„PDF bleibt zunächst eine Referenz und wird nicht
automatisch zum BuildingDocument."*

**Das ist die wichtigste Grenze dieses Auftrags.** Ein PDF-Grundriss nachzuzeichnen ist Arbeit
eines Menschen mit den vorhandenen Wandwerkzeugen — und genau dafür liegt er darunter. *Wer
Linien automatisch zu Wänden erklärt, erzeugt ein Gebäudemodell, dem niemand ansieht, welche Wand
gemessen und welche geraten ist.*

## Was dieser Auftrag über die Plattform NICHT entscheidet

- **Nicht, welches der sechzehn Ablagemodelle die Heimat wird.** Er benutzt `PlanUpload`, weil es
  da ist.
- **Nicht, ob es eine Format Registry gibt.** Ein Weg braucht keine Matrix.
- **Nicht, wie die Virenprüfung aussieht.** Sie bleibt offen und steht in der Aufnahme.

*Ein schmaler Schnitt, der nebenbei die Architektur festlegt, ist kein schmaler Schnitt.*

---

## MESSBLOCK nach R19 (neu gefasst) — 30.07., 07:52

**Gemessen gegen den committeten Stand `5d16765c`, nicht gegen den Arbeitsbaum.**

```yaml
measurements:
  - id: M-01
    command: "git show HEAD:app/Http/Controllers/Energie/PlanUploadController.php | grep -c 'ENDUNGEN\\|getClientOriginalExtension'"
    observed_value: 2
    observed_at_commit: "5d16765c"
    observed_at: "2026-07-30T07:52:00+02:00"
    freshness_rule: "must_match_current_head"
    purpose: "gap proof — die Annahme prueft die Dateiendung, die Magic Bytes erst im Job danach"

  - id: M-02
    command: "git grep -l 'massstab_mm_pro_einheit' HEAD -- app resources | wc -l"
    observed_value: 2
    observed_at_commit: "5d16765c"
    observed_at: "2026-07-30T07:52:00+02:00"
    freshness_rule: "must_match_current_head"
    purpose: "reuse proof — das Feld EXISTIERT; die Kalibrierung war vorgesehen und nie gebaut"

  - id: M-03
    command: "git grep -l 'PlanUpload' HEAD -- tests | wc -l"
    observed_value: 0
    observed_at_commit: "5d16765c"
    observed_at: "2026-07-30T07:52:00+02:00"
    freshness_rule: "must_match_current_head"
    purpose: "geerbte_zusagen (R17) — es gibt KEINE. Dieser Auftrag verriegelt die Flaeche als Erster"
```

**M-03 ist die wichtigste der drei.** *Sie ist beim Abnehmen die Antwort auf die Frage, ob dieser
Auftrag getan hat, was er versprochen hat: aus 0 muss eine Zahl größer 0 werden.*
