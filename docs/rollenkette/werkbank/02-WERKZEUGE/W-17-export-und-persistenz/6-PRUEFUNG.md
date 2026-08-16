# W-17 · Export und Speichern — PRÜFUNG

## Die Wächter der Insel — ZWEI, nicht vier

**Selbst gefahren am Ablese-Stand:**

```text
speicherAnzeige.test.ts        10 Zusagen   die sechs Zustandstexte
snapshotFlaecheEhrlich.test.ts 10 Zusagen   die Snapshot-Naht (AUF-55), 110 Z.
ℹ tests 20 · pass 20 · fail 0
```

> ***Zwei weitere Dateien tragen „Speicher" im Namen und gehören NICHT hierher*** —
> `paketSpeichern` *(das Konfigurator-Paket)* und `schienenSpeicher` *(der Klappzustand der
> Schiene).* **Ich habe sie zuerst mitgezählt und beim Öffnen der Dateiköpfe getrennt**; die
> Begründung steht in `5-CODE`.

## Die Wächter des Servers — HIER NICHT GEFAHREN, und das ist der Befund

```text
tests/Feature/Hausplaner/HausplanerSpeichernNutzlastTest.php   268 Z., 15 Zusagen
tests/Feature/Hausplaner/SnapshotRueckwegVersionTest.php       169 Z.,  4 Zusagen
```

**Gemessen, warum:**

```text
vendor/ im Rollenbaum ticket-rolle-generator   NEIN
vendor/ im gemeinsamen Checkout               JA
.gitignore:7                                  /vendor
php artisan test  ->  Failed opening required '.../vendor/autoload.php'
```

> ***Das ist dieselbe Klasse wie `node_modules` — nur für Composer, und sie ist NICHT entschieden.***
> *Yama hat am 15.08. `npm ci` je Baum zugelassen und Symlink wie Modulkopie gesperrt;* **über
> `composer install` je Baum ist nichts entschieden.** *Ich habe es deshalb nicht getan und melde
> die Lücke, statt die Entscheidung durch eine Tat vorwegzunehmen.*

**Die Zusagen sind trotzdem namentlich gelesen** — *fünf von fünfzehn stehen hier, weil sie zeigen,
was die Serverhälfte überhaupt prüft:*

```text
test_unbekanntes_zukunftsfeld_ohne_schemawechsel_wird_abgelehnt
test_float_millimeter_wird_abgelehnt
test_unbekannter_node_typ_wird_abgelehnt
test_nullwand_wird_abgelehnt
test_verwaiste_oeffnung_wird_abgelehnt
```

> **Der Server nimmt keine Szene entgegen, nur weil sie JSON ist.** *Er prüft die mm-Invariante, die
> Knotentypen, die Wandlänge und die Zugehörigkeit von Öffnungen* — **dieselben Regeln, die der
> Reducer in der Insel durchsetzt, ein zweites Mal an der Grenze.** *Das ist keine Doppelung: der
> Reducer schützt den Anwender, der Server schützt die Datenbank vor jedem Client.*

## Was KEIN Wächter hält

| ungeprüft | Folge |
|---|---|
| **der 409-Pfad ENDE ZU ENDE** | die Insel-Seite (`:231`) und die Server-Seite (`:28`) sind je geprüft, die **Begegnung** nicht |
| **`exportPng`** | kein einziger Wächter — Dateiname, Auflösung und der Umstand, dass es kein Modell trägt, sind ungesichert |
| **die Snapshot-Naht** | `snapshotFlaecheEhrlich` hält fest, dass sie **fehlt** — nicht, dass sie funktioniert |
| **`Strg+S`** | der Kurzbefehl steht im Knopftitel; dass er gebunden ist, prüft niemand |

> ***Die erste Zeile ist die wichtigste und zugleich die teuerste zu schließen.*** *Beide Seiten des
> 409-Pfades sind belegt, aber ihre Begegnung braucht einen Lauf über die Route* — **und genau der
> ist der, den ich in diesem Baum nicht fahren kann.**

## Wie diese Ablesung rot werden könnte

**Nicht durch fehlenden Code** — *der existiert auf beiden Seiten.* **Sondern durch eine falsche
Ablesung:** *eine Zeilennummer, die nicht trifft; eine Zahl ohne Träger; eine Testdatei, die ich
diesem Werkzeug zuschreibe, obwohl sie etwas anderes prüft.*

> ***Genau das ist mir hier passiert und vor dem Commit aufgefallen:*** *ich hatte
> `snapshotFlaecheEhrlich` dem Raum-Werkzeug zugeordnet — wegen des Wortes „Fläche" im Dateinamen.*
> **Der Dateikopf sagt AUF-55, „die Snapshot-Naht wird ausgesprochen", und damit gehört sie mitten
> hierher.** *H-9, und diesmal in meine Richtung.*

**Alle Zahlen dieses Blattes tragen ihren Bezug:** *68 gilt für `speicherAnzeige.ts`, 73 für
`SpeichereHausplanerDokument.php`, 83 für `StelleSnapshotWieder.php`, 96 für den Request, 20 für die
zwei gefahrenen Insel-Zusagen, 19 für die zwei **nicht** gefahrenen Server-Dateien.*
