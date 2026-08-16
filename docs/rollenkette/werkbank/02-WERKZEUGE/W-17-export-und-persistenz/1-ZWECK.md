# W-17 · Export und Speichern — ZWECK

> ***EINORDNUNG: W-17 ist eine ABLESUNG und kein Bau*** — *gemessen, nicht angenommen. Nach Yamas
> Verfahren für Klasse B gilt zuerst die Messung, dann die Einordnung; hier steht sie, damit die
> nächste Rolle sie nicht wiederholt.*

```text
SPEICHERN     GEBAUT   store/hausplanerStore.ts:208  save()
                       PUT auf speichernUrl, base_revision, 409-Pfad, Fehlerpfad
SERVER        GEBAUT   Actions/SpeichereHausplanerDokument.php   73 Z.
                       DB::transaction, Revisionsvergleich, Pruefsumme
RUECKWEG      GEBAUT   Actions/StelleSnapshotWieder.php          83 Z.
EINGANG       GEBAUT   Requests/SpeichereHausplanerDokumentRequest.php  96 Z.
ANZEIGE       GEBAUT   app/dashboard/speicherAnzeige.ts          68 Z.
EXPORT (PNG)  GEBAUT   app/HausplanerApp.tsx:664  exportPng()
WAECHTER      GEBAUT   Insel 3 Dateien, 31 Zusagen, alle gruen
                       Server 2 Dateien, 19 Zusagen — HIER NICHT GEFAHREN, s. 6-PRUEFUNG
```

> ***Sieben Schichten, alle gebaut — deshalb war hier nichts zu bauen, sondern zu lesen.***

## Welches Problem des Anwenders löst dieses Werkzeug?

**Seine Arbeit darf nicht verlorengehen** — *und zwar auch dann nicht, wenn jemand anders am selben
Objekt gearbeitet hat, während er zeichnete.*

> ***Das ist der Kern, und er ist der Grund, warum W-17 mehr ist als ein Speichern-Knopf.*** *Ein
> Planer wird selten allein bedient: Büro und Monteur öffnen dasselbe Objekt.* **Ein Speichern, das
> den fremden Stand einfach überschreibt, verliert Arbeit lautlos** — *und lautlos ist die
> teuerste Art, sie zu verlieren.*

## Die zwei Wege hinaus — und sie sind ungleich

| Weg | wohin | Umkehrbar |
|---|---|---|
| **Speichern** | in die Datenbank, als Dokument mit Revision | **ja** — der Rückweg ist `StelleSnapshotWieder` |
| **PNG-Export** | in den Download-Ordner des Anwenders | **nein** — was heruntergeladen ist, kennt der Planer nicht mehr |

> **Nur der erste ist Persistenz.** *Der zweite ist ein Bild und trägt kein Modell:* `toDataURL`
> *liefert Pixel, keine Wände.* **Wer den Export für eine Sicherung hält, hat eine Sicherung, die
> sich nicht zurückladen lässt** — *deshalb stehen sie hier getrennt und nicht als „Ausgabe".*

## Der tragende Punkt: die Revision entscheidet, nicht die Zeit

**`store/hausplanerStore.ts:225` schickt `base_revision` mit** — *die Revision, auf der der Anwender
seine Änderungen aufgebaut hat.* **Der Server vergleicht sie** (`SpeichereHausplanerDokument.php:28`):

```text
Client sendet   base_revision = 7
Server hat      revision      = 9        <- jemand anders war schneller
                              -> ok: false, revision: 9
Client sieht    409  ->  speicherStatus 'konflikt'
```

> ***Kein stiller Verlust.*** *Der Anwender bekommt nicht „gespeichert", obwohl nichts gespeichert
> wurde, und er bekommt auch keinen technischen Fehler — er bekommt die Auskunft, dass sein Stand
> überholt ist,* **samt der Revision, die jetzt gilt** (`konfliktRevision`).

## Was der Zustand dem Anwender sagt — fünf Worte, nicht zwei

```text
store/hausplanerStore.ts:21
  SpeicherStatus = 'gespeichert' | 'ungespeichert' | 'speichert' | 'konflikt' | 'fehler'
```

> **`konflikt` und `fehler` sind ausdrücklich getrennt**, *und das ist eine Aussage über den
> Anwender:* **beim Konflikt hat er etwas zu entscheiden, beim Fehler hat er es nur noch einmal zu
> versuchen.** *Ein gemeinsamer Zustand „ging nicht" würde beide Fälle in denselben Satz zwingen —
> `speicherAnzeige.ts:56-63` gibt ihnen deshalb verschiedene Texte und verschiedene Knöpfe.*
