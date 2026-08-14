# W-16 · Grundriss unterlegen — FUNKTION

## Die SECHS Ausfuhren der Insel — am Bau-Stand gezählt

| Modul | Z | Ausfuhren |
|---|---|---|
| `app/unterlage/kalibrierung.ts` | 44 | `Punkt` (16) · `MASSSTAB_STANDARD` (22) · `abstand()` (25) · `berechneMassstab()` (33) |
| `app/unterlage/UnterlagenEbene.tsx` | 66 | `UnterlagenEbene()` (51) |
| `app/unterlage/UnterlagenWerkzeuge.tsx` | 239 | `UnterlagenWerkzeuge()` (29) |

## Der Maßstab wird KORRIGIERT, nicht neu berechnet

**Das ist die Entscheidung, die das ganze Modul trägt** — wörtlich aus `kalibrierung.ts:9-13`:

> *„**Warum eine Korrektur und keine Neuberechnung aus Bildpixeln:** die Unterlage liegt in der Szene
> immer schon mit IRGENDEINEM Maßstab (Standard 1 mm/Einheit vor der ersten Kalibrierung). Die
> beiden Klickpunkte kommen deshalb bereits in Szenen-mm an — **dieselbe Wahrheit, mit der auch
> Wände gezeichnet werden, keine zweite Pixel-Rechnung daneben.** Eine zweite Kalibrierung
> (Nachbessern) korrigiert genauso: sie geht vom zuletzt gültigen Maßstab aus, nicht von 1."*

```text
berechneMassstab(alterMassstab, a, b, eingegebeneLaengeMm)   kalibrierung.ts:33

  :39   alterMassstab <= 0  oder  eingegebeneLaengeMm <= 0   ->  null
  :40   gemessen = abstand(a, b)                             (Math.hypot)
  :41   gemessen <= 0                                        ->  null
  :43   return alterMassstab * (eingegebeneLaengeMm / gemessen)
```

> ***Die Rechnung ist ein VERHÄLTNIS*** — *„eingegeben ÷ gemessen", auf den bisherigen Maßstab
> angewandt.* **Deshalb ist eine zweite Kalibrierung kein Neuanfang, sondern eine Nachbesserung der
> ersten.**

**`MASSSTAB_STANDARD = 1`** (`:22`) ist ausdrücklich *„kein Sollwert — ein Startwert"*.

## Die Naht zwischen Insel und Server

```text
SERVER  app/Models/PlanUpload.php:81   'bildUrl'     => route('energie.plan-upload.bild', $this)
                                :82    'massstabUrl' => route('energie.plan-upload.massstab', $this)
                                :83    'statusUrl'   => route('energie.plan-upload.status', $this)

INSEL   app/unterlage/UnterlagenWerkzeuge.tsx:68   X-CSRF-TOKEN, Accept: application/json
                                            :155   X-CSRF-TOKEN, Content-Type: application/json
```

> ***Die Insel kennt KEINE URL.*** *Gemessen: `'/admin/`, `"/admin/` und `http(s)://` kommen in
> `app/unterlage/` **null Mal** vor.* **Alle drei Adressen kommen fertig vom Server, erzeugt mit
> `route()`.**

**Warum das eine Aussage ist:** *eine hartgeschriebene URL in der Insel wäre eine zweite Wahrheit
neben `routes/web.php` — sie überlebte keine Umbenennung und niemand bemerkte es beim Umbenennen.*

## Die sechs Routen

```text
routes/web.php:5679  GET     /admin/energie/plan-upload                     energie.plan-upload
              :5681  POST    /admin/energie/plan-upload                     …store
              :5683  DELETE  /admin/energie/plan-upload/{planUpload}        …destroy
              :5685  GET     /admin/energie/plan-upload/{planUpload}/bild   …bild
              :5688  PUT     …/massstab                                     …massstab
              :5691  GET     …/status                                       …status
```

**Der Maßstab ist eine eigene Route** (`PUT …/massstab`) — *er wird gespeichert, nicht nur
angezeigt.* **Und `…/status`** trägt den Verarbeitungsstand (PDF-Klassifizierung, Rasterung).

## Ausgabe

| Was | Typ | Wohin |
|---|---|---|
| `abstand(a, b)` | `number` (mm) | intern, für den Maßstab |
| `berechneMassstab(…)` | `number \| null` | → `PUT …/massstab`, dann in die Szene |
| die Unterlagen-Ebene | Konva-Bild | unterste Ebene der Bühne |

## Kommando (für Rückgängig)

**Keines.** *Die Unterlage ist kein Modellknoten — sie wird nicht über `executeCommand` gesetzt und
taucht in keiner Historie auf.* **Ein Undo nach dem Kalibrieren nimmt die letzte Zeichnung zurück,
nicht den Maßstab.**

## Schichtzuordnung

- **Ändert Schicht 1 (Domäne):** *nein* — die Unterlage liegt in `plan_uploads`, nicht im
  Szenendokument.
- **Rechnet in Schicht 2 (Geometrie):** **F-001** (`abstand`) und eine Verhältnisrechnung ohne
  F-Nummer — siehe `3-FORMELN`.
- **Lebt in Schicht 3 (Anwendung):** `app/unterlage/`, Serverhälfte unter `Energie`.
- **Zeigt sich in Schicht 4/5:** `UnterlagenEbene` als unterste Ebene der Bühne.
