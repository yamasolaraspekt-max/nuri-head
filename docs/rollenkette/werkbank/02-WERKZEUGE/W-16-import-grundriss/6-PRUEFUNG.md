# W-16 · Grundriss unterlegen — PRÜFUNG

## Die Wächter liegen in BEIDEN Hälften — und die Serverhälfte trägt die meisten

| Wächter | Z | Zusagen | Zugriffsart |
|---|---|---|---|
| `__tests__/kalibrierung.test.ts` | 51 | **7** | **IMPORT** aus `app/unterlage/kalibrierung` |
| `__tests__/unterlage.test.ts` | 91 | **11** | **QUELLE** (`readFileSync` 2×) |
| `tests/Feature/PlanUploadTest.php` | 255 | **12** | **PHP-Feature-Test** gegen die Routen |

> ***Die dritte Zeile ist der Fund dieses Blattes.*** *W-16-1-8 nennt die zwei Insel-Tests und sagt,
> sie seien „die zwei vorhandenen". **Gemessen gibt es einen dritten, und er ist der größte** —
> 255 Zeilen, zwölf Zusagen.* **Wer die Wächterfrage nur in `resources/planner/` stellt, findet die
> Hälfte des Werkzeugs nicht — dieselbe Falle wie beim Code (siehe `5-CODE`).**

## Was `kalibrierung.test.ts` festhält — die null-Zusage vollständig

```text
'abstand: Pythagoras, nichts weiter'
'K-04: eine bekannte Strecke ergibt den erwarteten Massstab'
'K-04 (Gegenprobe): die Strecke halbieren => der Massstab MUSS sich verdoppeln'
'K-04: eine zweite Kalibrierung korrigiert den ZULETZT gueltigen Massstab, nicht den Standard'
'K-04 (Kante): identische Punkte liefern null, keine Division durch 0'
'K-04 (Kante): eine Laenge von 0 oder darunter liefert null'
'K-04 (Kante): ein alter Massstab von 0 oder darunter liefert null'
```

> ***Drei Kanten-Zusagen für DREI null-Bedingungen*** — *jede einzeln, keine zusammengefasst.* **Und
> die Gegenprobe ist eine Eigenschaft, kein Beispiel:** *„halbe Strecke ⇒ doppelter Maßstab" fällt
> auf, wenn jemand die Division umdreht — ein einzelner Zahlenvergleich täte das nicht zuverlässig.*

**Die vierte Zusage ist die inhaltlich wichtigste:** *eine zweite Kalibrierung geht vom **zuletzt
gültigen** Maßstab aus, nicht vom Standard.* **Das ist die Entscheidung aus `2-FUNKTION`, als Zusage
verriegelt.**

## Was `unterlage.test.ts` festhält — vier Absenz-Zusagen

```text
K-03  'listening={false}' steht am Bild            die Unterlage ist NICHT auswaehlbar
K-03  die Unterlage traegt KEINEN Klick-Handler
K-03  sie ruehrt weder Befehle noch Auswahl noch das Modell an
K-03  ohne Bild wird NICHTS gerendert — kein Platzhalter, der wie ein Bild aussieht
K-06  importDienstNoetig ergibt einen ERKLAERENDEN SATZ, keine leere Flaeche
K-06  ein Fehler wird GENANNT, nicht verschluckt
      leseUnterlage: vollstaendig · aktuelle null · fehlend/unlesbar · HALBER Datensatz
```

> ***Vier der elf sind Absenz-Zusagen*** — *sie halten fest, was die Unterlage **nicht** tun darf.*
> **Das ist die passende Form für ein „totes" Element:** *dass es nichts anfasst, kann kein
> Modelltest zeigen; es lässt sich nur am Quelltext belegen.*

**Und die letzte Gruppe ist eine Ehrlichkeits-Zusage:** *ein halber Datensatz wird **verworfen**,
nicht halb angezeigt.* **Lieber nichts als etwas Halbes.**

## Was `PlanUploadTest.php` festhält — und es ist keine Insel-Frage

```text
Dateiart   eine UMBENANNTE Datei wird abgelehnt und liegt NICHT auf der Platte
           eine echte PDF-SIGNATUR wird angenommen
           ein BILD MIT PDF-ENDUNG wird abgelehnt
           dwg/dxf haben keine verlaessliche Signatur -> ueber die Endung angenommen
Rechte     mit hausplaner-update darf das Projekt zugewiesen werden
           OHNE hausplaner-update nicht
           ein Upload OHNE Projektbezug bleibt moeglich, ganz ohne Grant
           ein UNBEKANNTES Projekt wird abgelehnt
```

> ***„Wird abgelehnt UND liegt nicht auf der Platte"*** — *die Zusage prüft beides. Eine Ablehnung,
> die die Datei trotzdem speichert, wäre die gefährlichere Hälfte.*

**Die Signaturprüfung ist der Kern:** *die Endung entscheidet nicht, der Dateiinhalt entscheidet* —
außer bei `dwg`/`dxf`, *die keine verlässliche Signatur haben; das steht ausdrücklich im
Testnamen statt als stille Ausnahme.*

## Was NICHT geprüft wird

- **`UnterlagenEbene` hat keinen eigenen Wächter** — *sie wird über `unterlage.test.ts` als Quelle
  mitgeprüft (K-03), aber nichts prüft die Darstellung selbst.*
- **Keine Browserabnahme.** *Ob das Bild maßhaltig unter der Zeichnung liegt, zeigt kein Test.*
- **Die Schwelle 0,3 mm** aus `3-FORMELN` ist von keiner Zusage abgedeckt — *`berechneMassstab`
  liefert dort eine unbrauchbare Zahl statt `null`, und keine Zusage hält das fest.*
