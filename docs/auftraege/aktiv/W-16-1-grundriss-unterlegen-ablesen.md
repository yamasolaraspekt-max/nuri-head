# W-16/1 — Grundriss unterlegen. Das Register sagt LEER, gebaut ist die ganze Kette von der Migration bis zur Bühne

```yaml
auftrag: "W-16/1"
werkzeug: "W-16 Grundriss unterlegen"
art: "STUFE B — Blatt schneiden, Ziel BESCHRIEBEN (Ablesung). Die Einordnung ist GEMESSEN:
      sechs Routen, Controller, Modell, zwei Migrationen, drei Insel-Dateien, zwei gruene Tests."
spur: A
heimat_app: ticket
dor_beleg: "steht aus — plan-pruefer."
status_steht_in: docs/STATUS.md
basis_sha: 86f94d98
prioritaet: P2
ballbesitz: "plan-pruefer (DoR)"
claim: "planner 13.08. — Claim VOR dem Schnitt."
kennung_geprueft: "W-16/1 und W-16/W haben NULL Treffer in docs/STATUS.md; W-16 selbst drei
                   (Registerzeile und Fahrplan). Keine W-16-Blaetter in docs/auftraege/aktiv/.
                   Frei."
anlass: "Yamas Regel fuer Klasse B: erst die Messung, dann die Einordnung. W-16 stand als
         'Indikation Ablesung' im Fahrplan, gestuetzt auf drei Dateien in app/unterlage/. Beim
         Vollmessen ist der Gegenstand deutlich GROESSER geworden — und die Registerzeile falsch."
grundlage: "app/unterlage/UnterlagenEbene.tsx (66 Z.) · UnterlagenWerkzeuge.tsx (239 Z.) ·
            kalibrierung.ts (44 Z.) · app/Http/Controllers/Energie/PlanUploadController.php (178 Z.) ·
            app/Models/PlanUpload.php:82-83 · routes/web.php:5679-5692 ·
            database/migrations/2026_07_08_180006_create_plan_uploads_table.php und
            2026_07_30_105516_add_projektbezug_to_plan_uploads.php ·
            REGISTER.md:48 · __tests__/kalibrierung.test.ts · __tests__/unterlage.test.ts"
```

## 1 — Der tragende Punkt: „LEER" steht an einer Zeile, unter der eine vollständige Kette liegt

```text
REGISTER.md:48   | W-16 | Grundriss unterlegen | LEER | W-12 | F-032 |

WAS GEMESSEN DASTEHT — die ganze Kette, von unten nach oben:

  DATENBANK   database/migrations/2026_07_08_180006_create_plan_uploads_table.php
              database/migrations/2026_07_30_105516_add_projektbezug_to_plan_uploads.php
  MODELL      app/Models/PlanUpload.php                        88 Zeilen
  ROUTEN      routes/web.php:5679-5692                          SECHS:
                :5679 GET    /admin/energie/plan-upload             index
                :5681 POST   /admin/energie/plan-upload             store
                :5683 DELETE /admin/energie/plan-upload/{id}        destroy
                :5685 GET    /admin/energie/plan-upload/{id}/bild   bild
                :5688 PUT    /admin/energie/plan-upload/{id}/massstab
                :5691 GET    /admin/energie/plan-upload/{id}/status
  CONTROLLER  app/Http/Controllers/Energie/PlanUploadController.php  178 Zeilen
  INSEL       app/unterlage/UnterlagenWerkzeuge.tsx   239 Z.  1 Export
              app/unterlage/UnterlagenEbene.tsx        66 Z.  1 Export
              app/unterlage/kalibrierung.ts            44 Z.  4 Exporte
                                                    ------  ----------
                                                    349 Z.  SECHS Exporte
  ANGESCHLOSSEN, nicht nur vorhanden:
              app/rahmen/Buehne.tsx:36   importiert UnterlagenEbene
                                   :37   importiert MASSSTAB_STANDARD
              app/rahmen/GruppenzeileUndSchiene.tsx:36  importiert
                                        UnterlagenWerkzeuge
  TESTS       __tests__/kalibrierung.test.ts · __tests__/unterlage.test.ts
              selbst gefahren am 13.08.: fail 0.
```

> **Das ist derselbe Fall wie AUF-40, nur umgedreht.** *Dort haben drei Rollen eine **Route** gesucht
> und nicht gefunden, weil die Liste über ein Mount-Attribut kommt. **Hier ist die Route da — gesucht
> wurde am falschen Ort.** Der Bau liegt unter `Energie` (`energie.plan-upload.*`), nicht unter
> Hausplaner, und die Registerzeile hat „LEER" behauptet. **Wer diese Zeile liest, baut ein Feature
> nach, das von der Migration bis zur Bühne fertig ist.***

**Die Naht zwischen den beiden Hälften ist belegt und sauber:** *`app/Models/PlanUpload.php:82-83`
erzeugt `massstabUrl` und `statusUrl` per `route(...)` und gibt sie an die Insel; die Insel ruft sie in
`UnterlagenWerkzeuge.tsx:66/:153` mit `X-CSRF-TOKEN` auf. **Kein zweiter Weg, keine hartgeschriebene
URL** — die Schutzgrenze „React/TypeScript bleibt auf die Insel begrenzt" ist eingehalten, die
Serverseite ist Laravel.*

## 2 — Der zweite Befund: die Formelzuordnung F-032 trägt nicht

```text
REGISTER.md:48 fuehrt fuer W-16 die Formel F-032 (Transformation eines Punktes,
homogene 4x4-Matrix, FORMELSAMMLUNG:218).

WAS kalibrierung.ts WIRKLICH RECHNET, Rumpf geoeffnet:
  :25  abstand(a, b)          return Math.hypot(b.x - a.x, b.y - a.y);
                              -> Pythagoras. Das ist F-001 (Abstand zweier
                                 Punkte), nicht F-032.
  :33  berechneMassstab(alterMassstab, a, b, eingegebeneLaengeMm)
         return alterMassstab * (eingegebeneLaengeMm / gemessen);
                              -> eine VERHAELTNISRECHNUNG. Keine Matrix, keine
                                 homogene Koordinate.
  :22  MASSSTAB_STANDARD = 1  Startwert, und der Kommentar sagt ausdruecklich
                              „Kein Sollwert — ein Startwert."

KEINE MATRIX IM MODUL: der Begriff kommt in den 44 Zeilen nicht vor.
```

> **Die Zuordnung ist nicht knapp daneben, sie ist eine andere Größenklasse.** *F-032 transformiert
> einen Punkt mit einer 4×4-Matrix; hier wird ein Skalar aus zwei Längen gebildet. **Und die
> Maßstabsrechnung selbst könnte in der Sammlung fehlen** — das ist am Bau-Stand zu prüfen und, wenn
> ja, als **gemeldete Lücke** einzutragen. **Eine erfundene F-Nummer ist schlimmer als eine gemeldete
> Lücke** — die Lehre aus W-21s eigenem Befund.*

## 3 — Was das Blatt außerdem sagen muss: die Kalibrierung ist gegen Unsinn abgesichert

```text
berechneMassstab gibt null zurueck (nicht NaN, nicht Infinity), wenn
  eingegebeneLaengeMm <= 0   oder   alterMassstab <= 0   oder
  die zwei Punkte identisch sind (gemessen <= 0)
Der Doc-Kommentar sagt den Grund: „kein NaN, keine Division durch 0 — ein
Aufrufer muss nicht selbst darauf pruefen."
```

> **Das gehört ins Blatt, weil es eine Zusage an die Aufrufer ist.** *Wer später eine zweite
> Kalibrierstelle baut und `null` nicht behandelt, bricht sie. **Eine Zusage, die nur im Kommentar
> steht, ist die Sorte, die beim nächsten Umbau verschwindet.***

## 4 — Scope

```text
W-16/1 IST  die Ablesung des Gebauten, ueber BEIDE Haelften:
            1-ZWECK/2-FUNKTION  was das Werkzeug leistet (Bild hochladen,
                                klassifizieren lassen, unter den Grundriss
                                legen, ueber zwei Punkte kalibrieren)
            5-CODE              die drei Insel-Module mit Zeilenzahl und ALLEN
                                SECHS Exporten, UND die Serverseite: Controller,
                                Modell, sechs Routen, zwei Migrationen
            3-FORMELN           am Code erheben. F-032 ist zu berichtigen;
                                gemessen ist F-001 plus eine Verhaeltnisrechnung.
                                Fehlt die Massstabsrechnung in der Sammlung,
                                wird sie als LUECKE gemeldet, nicht erfunden.
            7-GRENZEN           was es NICHT kann, und die null-Zusage von
                                berechneMassstab als Vertrag an die Aufrufer.
            Und die REGISTERZEILE 48 wird nachgezogen: LEER ist falsch.

W-16/1 IST NICHT
            eine Aenderung an PlanUploadController, PlanUpload, den Routen oder
            den Migrationen. NULL Produktivcode. Es ist eine Ablesung.
            eine Aenderung an app/unterlage/. Ebenso nicht.
            eine Bewertung, ob der Gegenstand richtig unter `Energie` liegt.
            Das ist eine Einordnungsfrage und faellt nicht in eine Ablesung —
            als BEFUND notieren, nicht entscheiden.
            die Klassifizierung im Hintergrund (AUF-88-P1 wird in
            routes/web.php:5690 als Kommentar genannt). Nur benennen; was die
            Queue tut, ist ein eigener Gegenstand.
            W-12 -> steht in derselben Registerzeile als Nachbar und hat sein
            eigenes Blatt (W-12/1, BEREIT). Nur zur Abgrenzung nennen.
```

## 5 — Abnahmekriterien

```text
W-16-1-1 (P1, TRAGEND) Das Blatt fuehrt BEIDE HAELFTEN mit Fundstelle: die drei
         Insel-Module UND Controller, Modell, Routen, Migrationen. Die
         Registerzeile REGISTER.md:48 sagt danach nicht mehr LEER.
         DER ALTE WORTLAUT WIRD NICHT GELOESCHT, sondern an derselben Stelle als
         ueberholt gekennzeichnet — die Bedingung, die der plan-pruefer an W-33-5
         gesetzt hat (baa785a2). Ein Satz, dessen Kennzeichnung woanders steht,
         wird spaeter als Beleg gelesen.
         WARUM P1: eine LEER-Zeile ueber einem fertigen Feature erzeugt einen
         Nachbau. Das ist die AUF-40-Klasse, und die hat schon einmal drei Rollen
         gekostet.
W-16-1-2 (P1) Die Formelzuordnung ist AM CODE erhoben und F-032 ist berichtigt:
         abstand ist Math.hypot (F-001), berechneMassstab ist eine
         Verhaeltnisrechnung. Wenn die Massstabsrechnung in der FORMELSAMMLUNG
         nicht steht, wird sie als LUECKE gemeldet und keine Nummer erfunden.
         Die Zeilennummern werden am Bau-Stand erhoben, nicht aus diesem Blatt
         uebernommen (E1).
W-16-1-3 (P1) Die null-Zusage von berechneMassstab steht in 7-GRENZEN als Vertrag:
         null bei Laenge <= 0, bei altem Massstab <= 0 und bei identischen
         Punkten — kein NaN, keine Division durch 0. Mit der Begruendung, dass
         Aufrufer sich darauf verlassen.
W-16-1-4 Die Naht zwischen Insel und Server ist benannt: PlanUpload.php:82-83
         erzeugt die URLs per route(), die Insel ruft sie mit X-CSRF-TOKEN
         (UnterlagenWerkzeuge.tsx:66 und :153). Und ausdruecklich der Satz, dass
         es KEINE hartgeschriebene URL in der Insel gibt — sonst sucht die
         naechste Rolle einen zweiten Weg. Am Bau-Stand gegenpruefen.
W-16-1-5 Die SECHS Exporte der drei Module sind vollstaendig genannt, mit
         Fundstelle. Am Bau-Stand zaehlen; die Zahl hier stammt aus meiner
         Messung vom 13.08. und ersetzt die eigene nicht (Pruefung 7).
W-16-1-6 Der BEFUND zur Einordnung steht im Blatt, ohne Entscheidung: der
         Gegenstand liegt unter `Energie` (energie.plan-upload.*), das Register
         fuehrt ihn als Hausplaner-Werkzeug. Ob das so bleiben soll, ist eine
         eigene Frage — hier nur festhalten, dass beides zutrifft und WO.
W-16-1-7 Kein Produktivcode. Gegenprobe: der Bau-Commit fasst weder
         resources/planner/** noch app/** noch routes/** noch
         database/migrations/** an.
W-16-1-8 Die zwei vorhandenen Tests sind benannt (kalibrierung.test.ts,
         unterlage.test.ts) und der Testlauf ist im Bericht als Rohausgabe
         belegt. Am Bau-Stand selbst fahren — nicht meine Zahl uebernehmen.
```

**Nachweisform: Befehl und Trefferzeilen** (B5), **Messung am COMMIT** (E1),
**Nachweis muss rot werden können** (Pflichtprüfung 4), **jede Zahl mit Träger** (Prüfung 7).

```yaml
was_die_pflichtpruefungen_hier_verhindert_haben: "Pruefung 7, die Exporte VOR dem Scope, und Pruefung
        6, die Reichweite. Mein Fahrplan-Eintrag sagte 'Indikation Ablesung, app/unterlage/ mit drei
        Dateien'. Haette ich danach geschnitten, waere ein Blatt entstanden, das 349 Zeilen Insel
        beschreibt und die 178 Zeilen Controller, das Modell, sechs Routen und zwei Migrationen
        NICHT ERWAEHNT — bei einem Werkzeug, dessen halbe Wahrheit auf dem Server liegt. Der Fund
        kam aus einer einzigen Frage: warum nimmt UnterlagenWerkzeuge einen csrfToken?"
was_ich_gemessen_habe_und_was_nicht: "SELBST GEMESSEN: die drei Dateien mit Zeilenzahl und Exporten,
        die sechs Routen, die Zeilenzahlen von Controller und Modell, die zwei Migrationen, die drei
        Import-Stellen der Anschluesse, die Rumpfe von abstand und berechneMassstab, und der
        Testlauf (fail 0). NICHT GEMESSEN: was PlanUploadController INHALTLICH tut — 178 Zeilen, die
        das Blatt beim Bau zu lesen hat; ich habe nur Umfang und Routen erhoben. Und NICHT gemessen,
        was die Queue-Klassifizierung aus AUF-88-P1 leistet; sie ist ausdruecklich ausserhalb."
W_16_1_nimmt_keinen_paragraf3_platz: "ENTWURF, nicht IN_ARBEIT."
```
