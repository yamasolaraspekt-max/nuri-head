# W-17 · Export und Speichern — CODE

> ***Die Hälfte liegt auf dem SERVER, und das ist bei W-17 nicht die Ausnahme, sondern der
> Gegenstand.*** *Dasselbe Muster wie bei W-16/1: wer nur die Insel liest, beschreibt den Absender
> und nicht die Zustellung.*

## Alle Schichten mit Fundstelle — beide Hälften

| Schicht | Datei | Stelle |
|---|---|---|
| **Auslöser** | `app/HausplanerApp.tsx` · `app/HausplanerStudio.tsx` | je `speicherAnzeige(...)` (`:1244` bzw. `:57`) |
| **Zustand** | `store/hausplanerStore.ts` | `:21` `SpeicherStatus` (fünf Werte) · `:40` Feld · `:208` `save()` |
| **Anzeige** | `app/dashboard/speicherAnzeige.ts` | 68 Z., eine reine Funktion, **zwei** Verbraucher |
| **Route** | `routes/web.php` | `:4996` `PUT /objekt/{objekt}/dokument` |
| **Eingang** | `Requests/SpeichereHausplanerDokumentRequest.php` | 96 Z. |
| **Schreiben** | `Actions/SpeichereHausplanerDokument.php` | 73 Z., `:24` Transaktion, `:50` Prüfsumme |
| **Rückweg** | `Actions/StelleSnapshotWieder.php` | 83 Z., append-only |
| **Controller** | `Controllers/Hausplaner/HausplanerController.php` | `:279` speichern · `:294` 409 · `:346` Snapshot |
| **Modell** | `Models/HausplanerSnapshot.php` | 26 Z. |
| **Wächter Insel** | `__tests__/` | 3 Dateien, **31** Zusagen |
| **Wächter Server** | `tests/Feature/Hausplaner/` | 2 Dateien, **19** Zusagen |

## `speicherAnzeige.ts` — 68 Zeilen, EINE Funktion, ZWEI Verbraucher

```text
:24  AnzeigeArt = 'ok' | 'warnung' | 'neutral' | 'fehler'
:26  SpeicherAnzeige { text, art, gesperrt, knopfTitel }
:42  speicherAnzeige(status, kannSpeichern, konfliktRevision?)
```

> **Sie ist rein und hat deshalb einen eigenen Wächter** (`speicherAnzeige.test.ts`, 10 Zusagen).
> *Der Kommentar am Verbraucher sagt den Grund* (`HausplanerApp.tsx:1241`, AUF-47): *„Text, Gewicht
> und Knopf-Sperre kommen aus `speicherAnzeige` (rein, getestet)".*
>
> ***Zwei Verbraucher, eine Wahrheit:*** `HausplanerApp` *und* `HausplanerStudio` *rufen dieselbe
> Funktion.* **Wären es zwei Fassungen, könnte die Studio-Ansicht „Gespeichert" zeigen, während die
> Hauptansicht „Konflikt" sagt** — *und beide hätten recht.*

## Die Snapshots haben eigene Routen

```text
routes/web.php:5001  POST /objekt/{objekt}/snapshots   snapshotErstellen  permission Hausplaner,update
              :5003  GET  /objekt/{objekt}/snapshots   snapshotListe      permission Hausplaner,read
              :4996  PUT  /objekt/{objekt}/dokument    speichern
```

> **Die Rechte sind getrennt:** *Liste lesen darf, wer lesen darf; erstellen nur, wer schreiben
> darf.* — *Als vorhanden benannt; die Rechteprüfung selbst ist Gegenstand von
> `HausplanerRechteTest.php` und nicht dieses Blattes.*

## Vier Dateien tragen „Speicher" oder „Snapshot" im Namen — ZWEI davon gehören hierher

```text
speicherAnzeige.test.ts       W-17  — die sechs Zustandstexte           10 Zusagen
snapshotFlaecheEhrlich.ts     W-17  — die SNAPSHOT-NAHT (AUF-55)        10 Zusagen, 110 Z.
paketSpeichern.test.ts        NICHT — AUF-81, das KONFIGURATOR-PAKET wird gespeichert,
                                      nicht die Szene
schienenSpeicher.test.ts      NICHT — AUF-83-T5, der KLAPPZUSTAND der Schiene
                                      ueberlebt einen Neuladen; Oberflaechenzustand
```

> ***Und hier habe ich mich selbst korrigiert, bevor es committet war.*** *Meine erste Fassung
> ordnete `snapshotFlaecheEhrlich` dem Raum-Werkzeug zu — „eine Flächenaussage, kein Speicherweg".*
> **Beim Öffnen des Dateikopfs steht das Gegenteil:** *AUF-55, „die Snapshot-Naht wird
> ausgesprochen".* **Es gehört mitten in W-17.**
>
> *Ich war auf das Wort „Fläche" im Dateinamen hereingefallen — dieselbe H-9-Falle, gegen die ich
> im selben Absatz gewarnt habe.* **Der Name trägt ein Wort, die Sache liegt woanders — und diesmal
> in meine Richtung, nicht aus ihr heraus.**

### Und was diese Zusage festhält, ist ein Befund über W-17 selbst

*Aus dem Dateikopf, wörtlich:*

```text
objekt.blade.php setzt data-snapshots-url, drei Routen legen Planungsstaende an,
listen und stellen sie wieder her — und KEIN Zeichen davon erreicht die Insel.
main.tsx liest speichernUrl, rechte, projekte und paketeUrl, aber NICHT die
Snapshot-Adresse.

Es gibt keine wirkungslose Snapshot-Flaeche. Es gibt GAR KEINE.
```

> ***Der Rückweg ist auf dem Server vollständig gebaut und in der Insel nicht angeschlossen.***
> *Das erklärt, warum `4-BEDIENUNG` sagt, der Anwender könne keinen alten Stand zurückholen:* **nicht
> weil es fehlt, sondern weil die Naht nicht gezogen ist.** *Der Dateikopf nennt auch den Grund,
> warum das schlimmer ist als eine leere Fläche:* „sie wird beim nächsten Mal neu erfunden, weil
> niemand weiß, dass sie schon da ist."
