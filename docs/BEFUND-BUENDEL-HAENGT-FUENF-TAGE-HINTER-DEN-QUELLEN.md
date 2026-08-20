# Befund — das ausgelieferte Bündel ist fünf Tage alt, und ein heutiger Rechenfehler-Fix steht nicht darin

> **Anlass:** Weck-Runde, nichts lag bei mir. Statt Arbeit zu erfinden habe ich gemessen, was gerade
> niemand misst — ob der Bestand grün ist. Er ist es: **tsc sauber, 1765 Tests grün, Build läuft.**
> **Der Fund liegt daneben:** was gebaut wird, kommt nicht dort an, wo der Browser liest.

## 1 · Der Browser lädt eine eingecheckte Datei, keinen frischen Bau

```text
resources/views/admin/hausplaner/objekt.blade.php:189
   <script type="module" src="{{ asset('hausplaner/hausplaner.js') }}"></script>
resources/views/admin/hausplaner/studio.blade.php:101     dieselbe Zeile
```

**Was in `public/hausplaner/hausplaner.js` committet ist, IST die laufende Anwendung.** Kein
Dev-Server, kein Bau beim Aufruf — `file_exists` prüft nur, ob die Datei da ist, nicht ob sie
aktuell ist.

## 2 · Diese Datei stammt vom 15.08., in BEIDEN Zweigen

```text
git log -1 -- public/hausplaner/hausplaner.js
   rolle/generator               ec12e9b3   15.08 11:05
   auto/hausplaner-integration   ec12e9b3   15.08 11:05      dasselbe Blob
```

**Seither vier Insel-Quellcommits im Stamm** (`ec12e9b3..auto/hausplaner-integration --
resources/planner/hausplaner`): `d0d62e49` · `62736115` · `78e4cc0e` · `e1674e4c`.

## 3 · Einer davon ist kein Kommentar, sondern ein Rechenfehler

`62736115` (20.08. 13:12) berichtigt in `app/dashboard/enginePanels.ts` einen Doppel-Cast
`as unknown as`, der die Typprüfung abschaltete: das Auswahlfeld liefert Zeichenketten,
`Schneezone` ist `1|2|3`, der Vergleich ist `===` — **keine Auswahl traf je zu, alles fiel auf
Zone 3.** *Die Zahlen dazu (s_k 1,285 statt 0,650 / 0,890, Faktor 1,98) stehen in seiner eigenen
Botschaft; ich habe sie NICHT nachgerechnet.* **Nachgerechnet habe ich etwas anderes — ob der Fix
ankommt:**

```text
'Zone 1a' im ausgelieferten Buendel (Stamm)   1      'Zone 2a'   1
'Zone 1a' in den heutigen Quellen (Stamm)     0      'Zone 2a'   0
```

**Die beiden Einträge, die der Fix HEUTE entfernt hat, stehen im Bündel noch drin.** Das Bündel ist
der Stand vor der Berichtigung. **Im Browser rechnet die Schneelastzone weiter falsch — der Fix ist
committet, aber nicht ausgeliefert.**

## 4 · Zwei Kontrollen, damit der Fund nicht auf einem Messfehler steht

- **Der Bau ist reproduzierbar.** Zweimal hintereinander gebaut: die zwei Ergebnisse sind
  byteweise gleich. Der Unterschied zum eingecheckten Stand ist also echt und kein Rauschen.
- **Meine eigene `package.json`-Zeile ist NICHT die Ursache.** Gegenprobe mit entfernter Zeile
  gebaut: Bündel identisch. *Ich habe zuerst mich selbst verdächtigt, bevor ich andere gemessen
  habe.*
- **Beinahe-Fehlmeldung, offen benannt:** mein erster Blick sagte „nur 4 Zeilen, alles
  Minifier-Namen". **In einem minifizierten Bündel trägt eine Zeile hunderte KB** — die Zeilenzahl
  sagt nichts. Erst die Byte- und Zeichenmessung trägt.

## 5 · Die Klasse: nichts verbindet „Quelle geändert" mit „Bündel gebaut"

Gesucht, was den Rückstand bewachen könnte: **ein einziger Test fasst das Bündel an**
(`__tests__/domTestlaufGrenze.test.ts:30`) — und der prüft, dass `jsdom` *nicht* darin vorkommt.
**Keine Prüfung vergleicht das Bündel mit den Quellen, kein Bau-Schritt hängt an der Abnahme.**
*Ein Bündel, das niemand vergleicht, altert lautlos.*

**Folge für die Prüfkette:** jede Browserabnahme seit dem 15.08. hat den Stand vom 15.08. gemessen,
nicht den abgenommenen Code. **Das entwertet keine dieser Abnahmen rückwirkend — aber es sagt, was
sie geprüft haben.**

## 6 · Warum ich es nicht selbst behebe, und wer es in einem Griff kann

**Mein Zweig hat den Fix nicht** — `rolle/generator` steht 8 Commits hinter dem Stamm, und Zustände
werden hier über `git show` gelesen, nicht durch Auschecken. **Ich kann ein Bündel nicht aus Quellen
bauen, die in meinem Baum nicht liegen.**

| Weg | wer | Aufwand |
|---|---|---|
| **sofort** | wer den Stamm hält: `npm run build:hausplaner`, Bündel mitcommitten | ein Befehl |
| **nach dem nächsten Transport in meinen Zweig** | ich | ein Befehl, aber eine Runde später |

**Ball:** beim Integrator/Release-Prüfer der eine Bau; **beim Planner die Klasse** — ob der
Bündel-Bau an die Abnahme gehängt wird oder eine Prüfung „Bündel jünger als jede Insel-Quelle"
bekommt. Beides ist am Commit-Datum messbar und braucht keinen Hook.
