# W-16 · Grundriss unterlegen — CODE

**ZWEI HÄLFTEN.** *Wer nur die erste liest, beschreibt das halbe Werkzeug.*

## Hälfte 1 — die Insel: drei Module, 349 Zeilen, SECHS Ausfuhren

| Modul | Z | Ausfuhren |
|---|---|---|
| `resources/planner/hausplaner/app/unterlage/kalibrierung.ts` | 44 | `Punkt` (16) · `MASSSTAB_STANDARD` (22) · `abstand()` (25) · `berechneMassstab()` (33) |
| `resources/planner/hausplaner/app/unterlage/UnterlagenEbene.tsx` | 66 | `UnterlagenEbene()` (51) |
| `resources/planner/hausplaner/app/unterlage/UnterlagenWerkzeuge.tsx` | 239 | `UnterlagenWerkzeuge()` (29) |

## Hälfte 2 — der Server, und er liegt unter `Energie`

```text
app/Http/Controllers/Energie/PlanUploadController.php   178 Z.
app/Models/PlanUpload.php                                88 Z.
  :81  'bildUrl'     => route('energie.plan-upload.bild', $this)
  :82  'massstabUrl' => route('energie.plan-upload.massstab', $this)
  :83  'statusUrl'   => route('energie.plan-upload.status', $this)

routes/web.php:5679-5692    SECHS Routen
  :5679  GET     energie.plan-upload            :5681  POST    …store
  :5683  DELETE  …destroy                       :5685  GET     …bild
  :5688  PUT     …massstab                      :5691  GET     …status

database/migrations/
  2026_07_08_180006_create_plan_uploads_table.php
  2026_07_30_105516_add_projektbezug_to_plan_uploads.php
```

> **Die Speicherung liegt NICHT in der Insel.** *Wer sie dort sucht, sucht vergeblich — und das ist
> der Grund, warum dieses Blatt mit beiden Hälften anfängt.*

## Die Naht, und die Aussage darüber

```text
SERVER   erzeugt die drei URLs mit route()      PlanUpload.php:81-83
INSEL    ruft sie mit X-CSRF-TOKEN              UnterlagenWerkzeuge.tsx:68 · :155
```

> ***In `app/unterlage/` steht KEINE hartgeschriebene URL*** — *gemessen: `'/admin/`, `"/admin/` und
> `http(s)://` ergeben zusammen **null** Treffer.* **Alle Adressen kommen fertig vom Server.**

**Warum das eine Aussage ist:** *eine hartgeschriebene URL wäre eine zweite Wahrheit neben
`routes/web.php` — sie überlebte keine Umbenennung, und niemand bemerkte es beim Umbenennen.*

## Die Einordnung ist doppelt — festgehalten, nicht entschieden

**Alle sechs Routen heißen `energie.plan-upload.*`, der Controller steht unter
`app/Http/Controllers/Energie/`.** *Das Register führt W-16 als Hausplaner-Werkzeug.*

> **Beides trifft zu.** *Ob die Serverhälfte auf Dauer unter `Energie` bleiben soll, ist eine eigene
> Frage — dieses Blatt hält fest, **wo** sie liegt.*

## Kein eigener Befehl

**Die Unterlage ist kein Modellknoten.** *Sie wird nicht über `executeCommand` gesetzt, taucht in
keiner Historie auf und liegt in `plan_uploads`, nicht im Szenendokument.*
