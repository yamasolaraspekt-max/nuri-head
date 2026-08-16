# W-17 · Export und Speichern — FORMELN

> **Am Code erhoben, nicht aus der Registerzeile übernommen.** *Die Zeile führt in der Formelspalte
> einen Gedankenstrich* (`REGISTER.md`: `| W-17 | Export und Speichern | LEER | alle | — |`).
> **Gemessen ist das richtig** — *aber nicht, weil hier nichts gerechnet würde.*

## Es gibt keine Geometrie — und das ist der Befund, nicht die Lücke

```text
SpeichereHausplanerDokument.php    Math./Trigonometrie   0
StelleSnapshotWieder.php           Math./Trigonometrie   0
speicherAnzeige.ts                 Math./Trigonometrie   0
```

> ***W-17 rechnet nichts über den Grundriss.*** *Es transportiert ihn.* **Eine F-Nummer aus der
> Sammlung wäre hier falsch** — *die Sammlung führt Geometrie, und Persistenz ist keine.*

**Der Gedankenstrich in der Registerzeile trägt also** — *und er trägt aus einem anderen Grund, als
er auf den ersten Blick aussieht: nicht „noch nicht gemessen", sondern „hier gehört keine hin".*

## Zwei Rechnungen gibt es trotzdem, und beide sind tragend

### 1 · Die Revision: `+ 1`, und mehr darf es nicht sein

```text
SpeichereHausplanerDokument.php:32   $neueRevision = (int) $aktuell->revision + 1;
StelleSnapshotWieder.php             derselbe Schritt, +1 auch beim Rueckweg
```

> ***Der ganze 409-Schutz hängt an dieser einen Addition.*** *Sie muss **monoton** sein — eine
> Revision, die springen oder zurücklaufen könnte, wäre als Vergleich wertlos.* **Deshalb erhöht
> auch der Rückweg** (`StelleSnapshotWieder`), *statt die alte Revision wiederherzustellen: sonst
> hätten zwei verschiedene Stände dieselbe Nummer, und `base_revision` vergliche Gleiches mit
> Ungleichem.*

### 2 · Die Prüfsumme: SHA-256 über eine KANONISCHE Fassung

```text
SpeichereHausplanerDokument.php:50  checksum(array $scene): string
                              :52   $kanonisch = self::sortiereRekursiv($scene);
                              :54   hash('sha256', json_encode($kanonisch,
                                      JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
```

> ***Die Sortierung vor dem Hashen ist der ganze Trick.*** *Zwei Szenen mit denselben Inhalten in
> anderer Schlüsselreihenfolge sind **dasselbe Dokument** — ohne `sortiereRekursiv` hätten sie
> verschiedene Prüfsummen, und die Prüfsumme beantwortete die Frage „hat sich etwas geändert"
> falsch.*
>
> **Und die zwei JSON-Schalter gehören dazu:** `JSON_UNESCAPED_UNICODE` *und*
> `JSON_UNESCAPED_SLASHES` *legen die Schreibweise fest.* **Ohne sie hinge die Prüfsumme an der
> PHP-Voreinstellung** — *eine Umgebung, die anders escapet, erzeugte eine andere Summe für
> dasselbe Dokument.*

## Was NICHT gerechnet wird, obwohl man es erwarten könnte

| erwartet | gemessen |
|---|---|
| **eine Größenformel** für die Szene | `SpeichereHausplanerDokumentRequest.php:61` prüft die Größe, aber als **Schwelle**, nicht als Rechnung |
| **eine Differenz** zwischen zwei Ständen | es gibt keine — gespeichert wird die **ganze** Szene, nicht ihr Unterschied |
| **eine Auflösungsrechnung** beim Export | `toDataURL({ pixelRatio: 2 })` ist ein **fester Faktor**, keine Formel |

> ***Die mittlere Zeile ist die folgenreichste.*** *W-17 schreibt jedes Mal das ganze Dokument.*
> **Das ist der Grund, warum die Prüfsumme über die ganze Szene läuft und warum die Revision
> genügt** — *bei einer Differenzspeicherung bräuchte es eine Kette, und ein verlorenes Glied wäre
> nicht bemerkbar.*
