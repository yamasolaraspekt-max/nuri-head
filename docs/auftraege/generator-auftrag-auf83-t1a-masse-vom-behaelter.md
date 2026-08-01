# AUF-83-T1a — Die Breite lernt, was die Höhe schon kann

*Planner, 29.07.2026, 08:35 CEST. **Zweite Fassung.** Die erste hat der Generator mit
`QUITTUNG: TRÄGT NICHT` zurückgewiesen — zwei Prüfverfahren trugen nicht. **Beide Beanstandungen
sind berechtigt, beide Fehler sind meine**, und beim Nachmessen kam heraus, dass der Auftrag
**kleiner** ist als ich ihn geschrieben hatte.*

```yaml
auftrag:
  id: AUF-83-T1a
  status: ruht
   # PB-B2, 01.08.2026 - Planner. Stand bis heute: `aktiv`. 17 Blaetter trugen das,
   # die Struktur-Zusage S-01 erwartet GENAU EINES. `ruht` heisst hier ehrlich:
   # der Zustand ist NICHT nachgemessen. Wer das Blatt zieht, misst zuerst.
  fassung: 2
  spur: A
  heimat: ticket
  ziel: >
    Die BREITE der Zeichenflaeche folgt ihrem Behaelter — nach demselben Muster, mit dem die
    HOEHE es seit AUF-72/73 bereits tut. Danach ist die Insel einbettbar (T1b) und
    overlay-faehig (T5).
  nicht_ziel: >
    KEIN Blade-Umbau, KEIN @extends, keine Route, kein Recht — das ist T1b.
    KEINE neue Optik: bei unveraenderten Panelbreiten bleibt der Bildschirm pixelgleich.
    KEINE Umstellung von Inline-Stilen — AUF-38 Scheibe 7 bleibt gesperrt (78 offen).

vorgeschichte:
  quittung_1: "TRAEGT NICHT (Generator, 29.07. 08:29) — K-05 und K-02 der ersten Fassung"
  planner_bestaetigt: >
    BEIDE BEANSTANDUNGEN SIND BERECHTIGT, UND BEIDE FEHLER SIND SPEZIFIKATIONSMAENGEL.
    K-05 nannte eine geerbte Zusage, DIE ES NICHT GIBT: `grep -rn innerWidth __tests__/` liefert
    exit 1, und `HausplanerApp:1442` ist ein JSX-Kommentar, kein Test. Selbst nachgemessen.
    Ein P1-Kriterium, das heute nichts findet und nach dem Umbau genauso gruen waere — eine
    STUMME ZUSAGE, und zwar in dem Auftrag, der gegen genau diese Fehlerklasse gebaut wird.
    K-02 liess Befehl und Sollzustand auseinanderlaufen: der Befehl griff auch die Insel, wo
    `100vh` legitim ist — und eine der Fundstellen liegt in der GESPERRTEN Datei. Der Sollzustand
    war ohne Regelbruch nicht erreichbar.
  seine_methode: >
    Er hat die echte Zusage mit der 8c-Methode gefunden — "welche Tests LESEN diese Datei" statt
    "wo steht das Wort". Das ist die praezisierte Such-Auflage aus Scheibe 5, angewandt.

scope:
  population_command: >
    grep -n 'innerWidth' resources/planner/hausplaner/app/HausplanerApp.tsx &&
    grep -n "100vh" resources/views/admin/hausplaner/*.blade.php resources/planner/hausplaner/app/HausplanerApp.tsx
  population_at_writing: >
    BREITE: HausplanerApp:369 (`innerWidth - 220 - 268`) plus die zwei Breitenliterale, die sie
    spiegelt (Z1371 `width: 220`, Z1796 `width: 268`).
    HOEHE: HausplanerApp:1180 (`height: imStudio ? '100%' : '100vh'`) — der Studio-Zweig ist
    BEREITS behaelterbezogen, nur der Objekt-Zweig nicht.
    Messung des Planners, KEINE Bedingung.
  pfade:
    - resources/planner/hausplaner/app/HausplanerApp.tsx
    - resources/planner/hausplaner/app/dashboard/buehnenBreite.ts   # neu, Muster: buehnenHoehe.ts
    - resources/planner/hausplaner/__tests__/buehnenBreite.test.ts  # neu
    - resources/planner/hausplaner/__tests__/keineKappung.test.ts
  ausschluesse:
    - stelle: "resources/views/admin/hausplaner/*.blade.php — min-height: calc(100vh - 46px)"
      grund: >
        GEHOERT ZU T1b, nicht hierher. Die 46 px sind die Hoehe der BLADE-Leiste; sie faellt mit
        dem @extends. Sie hier zu entfernen hiesse, den Blade-Umbau halb vorzuziehen.
        Das war ein Zuschnittfehler der ersten Fassung.
      entschieden_von: planner
    - stelle: "hausplaner.css:157 (.hp-studio) und stilschicht.test.ts:257"
      grund: "Legitime Verwendung ausserhalb der Behaelter-Rechnung. Kein Befund."
      entschieden_von: planner
    - stelle: "buehnenHoehe.ts:68"
      grund: "Ein KOMMENTAR, der den Blade-Rest beschreibt. Er dokumentiert, er rechnet nicht."
      entschieden_von: planner
    - stelle: "die 78 offenen Inline-Stellen in HausplanerApp.tsx"
      grund: >
        AUF-38 Scheibe 7, gesperrt. Wer hier eine umstellt, baut Scheibe 7 nebenbei und macht
        beide Posten unpruefbar. Faellt eine im Weg auf: melden, nicht mitnehmen.
      entschieden_von: planner

kriterien:
  - id: K-01
    aussage: "Die Planbreite kommt aus einer Messung, nicht aus Fensterkonstanten."
    typ: absence
    kritikalitaet: P1
    pruefung:
      befehl: "grep -n 'innerWidth' resources/planner/hausplaner/app/HausplanerApp.tsx"
      erwartet: "kein Treffer in der Breitenrechnung"
    beleg: grepausgabe vorher/nachher
    partner: >
      presence-Partner nach R2: `grep -rn 'innerWidth' resources/planner/hausplaner/` MUSS weiter
      Treffer liefern (der Fenster-Zuhoerer bleibt) — sonst prueft der Befehl nur, dass jemand ein
      Wort geloescht hat.

  - id: K-02
    aussage: "Die Breite folgt dem Muster, das die Hoehe bereits hat."
    typ: presence
    kritikalitaet: P1
    pruefung:
      befehl: "npm run test:hausplaner -- --filter=buehnenBreite"
      erwartet: >
        Ein Modul `buehnenBreite.ts` nach dem Vorbild von `buehnenHoehe.ts`: es misst den
        Behaelter und rechnet, was uebrig bleibt. KEINE Pixelkonstante im Modul — dieselbe Regel,
        die `buehnenHoehe.ts` sich selbst gibt und die `buehnenHoehe.test.ts:28` am Quelltext
        nachprueft.
    beleg: testausgabe + der Quelltext des neuen Moduls
    begruendung: >
      DAS IST DER KERN DIESES AUFTRAGS, und er stand in der ersten Fassung nicht drin, weil ich
      `buehnenHoehe.ts` nicht gemessen hatte. Die HOEHE ist seit AUF-72/73 geloest: gemessen statt
      gerechnet, mit Ersatzwert, Mindestwert und abgerundet statt aufgerundet. Der Satz aus jenem
      Modul gilt hier woertlich: *"Wer stattdessen einen festen Betrag abzoege, haette die alte
      Konstante nur durch eine kleinere ersetzt - und saesse in vier Wochen wieder hier."*
      Die Breite bekommt dasselbe. **Bestandscode-first heisst hier nicht wiederverwenden, sondern
      derselben Loesung folgen, die fuer die andere Achse schon abgenommen ist.**

  - id: K-03
    aussage: "Der Objekt-Zweig der Hoehe wird behaelterbezogen wie der Studio-Zweig."
    typ: behavioural
    kritikalitaet: P1
    pruefung:
      befehl: "grep -n \"imStudio ? '100%'\" resources/planner/hausplaner/app/HausplanerApp.tsx"
      erwartet: >
        Kein Ternaer mehr — beide Wege nehmen `100%`. Heute steht dort
        `height: imStudio ? '100%' : '100vh'`: **der Studio-Zweig ist bereits richtig, der
        Objekt-Zweig nicht.**
    beleg: grepausgabe + Sichtprobe der Objektseite
    begruendung: >
      Mit T1b wird auch die Objektseite eingebettet. Bliebe dort `100vh`, entstuende genau der
      zweite Bildlauf, den T1a verhindern soll. **Diese eine Stelle ist Layout-Rechnung und
      damit ausdruecklich Teil dieses Auftrags — nicht Scheibe 7.**

  - id: K-04
    aussage: "Die Zusage, die die Panelbreite liest, ist mitgezogen."
    typ: coverage
    kritikalitaet: P1
    pruefung:
      befehl: "npm run test:hausplaner -- --filter=keineKappung"
      erwartet: >
        `keineKappung.test.ts:48` sucht die Panelzeile heute ueber die Zeichenkette `width: 268,`.
        Nach dem Umbau muss sie die EIGENSCHAFT dort pruefen, wo sie wohnt — nicht die Zahl.
    beleg: testausgabe + Wortlaut vorher/nachher
    korrektur: >
      DAS ERSETZT DAS FALSCHE K-05 DER ERSTEN FASSUNG. Ich hatte `breiten.test.ts` und
      `innerWidth` genannt — beides existiert nicht. Der Generator hat die echte Stelle gefunden.

  - id: K-05
    aussage: "Die vorhandene Hoehen-Zusage bleibt gruen und unveraendert."
    typ: presence
    kritikalitaet: P1
    pruefung:
      befehl: "npm run test:hausplaner -- --filter=buehnenHoehe && git diff --stat -- resources/planner/hausplaner/__tests__/buehnenHoehe.test.ts"
      erwartet: "gruen, und die Datei ist NICHT angefasst"
    beleg: testausgabe + leerer diff
    begruendung: >
      `buehnenHoehe.test.ts` ist die abgenommene Zusage der anderen Achse. Wer sie beim Bauen
      anfasst, aendert einen erteilten Beleg. Bleibt sie gruen, ohne beruehrt zu sein, ist das
      der beste verfuegbare Beweis, dass die Hoehe unbeschaedigt geblieben ist.

  - id: K-06
    aussage: "Die Planbreite folgt einem schmaleren Behaelter."
    typ: behavioural
    kritikalitaet: P1
    pruefung:
      typ: visuell
      schritte: >
        Studio bei 1440 px. Den tragenden Behaelter auf 900 px setzen (DevTools genuegt).
      erwartet: "gemessene Planbreite folgt dem Behaelter, nicht 1440 minus 488"
    beleg: zwei getBoundingClientRect-Ausgaben, Behaelter und Plan
    ausgefuehrt_von: evaluator

  - id: K-07
    aussage: "Bei unveraenderten Panelbreiten ist der Bildschirm pixelgleich."
    typ: behavioural
    kritikalitaet: P1
    pruefung:
      typ: visuell
      schritte: "headful, 1440 / 1024 / 375 px, Studio im Expertenmodus, ganzseitig"
      erwartet: "sha256 der Bildschirmfotos identisch zu vorher"
    beleg: sha256-Paare je Viewport
    ausgefuehrt_von: evaluator
    begruendung: >
      Dieser Auftrag aendert das VERFAHREN, nicht das Bild. Sieht es anders aus, ist etwas anderes
      passiert als beauftragt — dann ist die Abweichung der Befund, nicht der Haken.

  - id: K-08
    aussage: "Scheibe 7 ist unberuehrt."
    typ: absence
    kritikalitaet: P1
    pruefung:
      befehl: "node scripts/statische-inline-stile.mjs resources/planner/hausplaner/app/HausplanerApp.tsx"
      erwartet: "78 offen, unveraendert"
    beleg: rohausgabe vorher/nachher

  - id: K-09
    aussage: "Gates ohne Regression."
    typ: presence
    kritikalitaet: P1
    pruefung:
      befehl: "npm run tsc:hausplaner && npm run schema:hausplaner:check && npm run test:hausplaner && npm run test:hausplaner:dom && npm run build:hausplaner && php artisan test"
      erwartet: "0/0/0/0/0, Insel-Testzahl nicht gefallen, PHP 789"
    beleg: testzaehler vorher/nachher

selbstnachweis:
  quittung_zuerst: "Zweite Readiness-Quittung nach §2, bevor eine Zeile entsteht."
  gegenprobe: >
    Die alte Rechnung wieder einsetzen ⇒ K-01, K-02 und K-06 muessen rot werden. Faellt nur eine,
    prueft mindestens eine der anderen die Gestalt statt der Wirkung.
  such_auflage: >
    Geerbte Zusagen werden ueber die EIGENSCHAFTSNAMEN gesucht und ueber die Frage "welche Tests
    LESEN diese Datei" — nicht ueber die Woerter `style` oder `inline`. Deine 8c-Methode; sie hat
    in dieser Quittung meinen Fehler gefunden.
  rueckweg: >
    Revert ueber vier Dateien, davon zwei neu. Kein Datenpfad, kein Schema, keine Route.
    Zum Zurueckdrehen einer Probe NIE `git checkout` — Kopie beiseite und `cp` zurueck,
    mit `diff -q` als Beleg.
```

---

## Was die Quittung an meinem Auftrag geändert hat

**Der Auftrag ist kleiner geworden, nicht größer.** Ich hatte drei Rechnungen benannt — Breite plus
zwei Blade-Höhen. **Zwei davon gehören nicht hierher:**

- Die **Blade-Höhen** (`calc(100vh − 46px)`) hängen an der Blade-Leiste, die mit **T1b** ohnehin
  fällt. Sie hier zu entfernen hieße, den Blade-Umbau halb vorzuziehen. Zuschnittfehler.
- Die **Höhe der Insel ist längst gelöst.** `buehnenHoehe.ts` (AUF-72/73, 127 Zeilen) misst statt
  zu rechnen, mit Ersatzwert, Mindestwert und der ausdrücklichen Regel, **keine Pixelkonstante**
  zu setzen. `HausplanerApp:1180` nutzt im Studio bereits `100%`. **Offen ist nur der Objekt-Zweig.**

**Was übrig bleibt, ist genau eine Achse: die Breite.** Und für sie existiert bereits das Muster,
nach dem sie gebaut gehört — auf der anderen Achse, abgenommen und testverriegelt.

> *„Kein Ausgleich per fester Zahl. Hier steht keine Pixelkonstante; es wird gerechnet, was gemessen
> ist. Wer stattdessen einen festen Betrag abzöge, hätte die alte Konstante nur durch eine kleinere
> ersetzt — und säße in vier Wochen wieder hier."*
> — `buehnenHoehe.ts`, geschrieben für die Höhe, gilt wörtlich für die Breite.

## Meine zwei Fehler, benannt

**(1) Ich habe ein P1-Kriterium gegen eine Zusage geschrieben, die ich nicht gemessen hatte.**
`breiten.test.ts` mit `innerWidth` — beides existiert nicht. Der Prüfbefehl liefert `exit 1`.
**Das ist eine stumme Zusage in genau dem Auftrag, der gegen stumme Zusagen gebaut wird.** Die ganze
Nacht lautete die Regel *„eine Zahl im Auftrag ist eine Messung, keine Bedingung"* — hier war es
nicht einmal eine Zahl, sondern eine behauptete Datei.

**(2) Befehl und Sollzustand liefen auseinander.** Mein `grep 100vh` griff auch die Insel, wo die
Verwendung legitim ist — und eine Fundstelle liegt in der **gesperrten** Datei. **Der Sollzustand
war ohne Regelbruch nicht erreichbar.** Das ist derselbe Fehler wie eine Grundgesamtheit ohne
Ausschlussliste, nur eine Ebene tiefer.

**Die Readiness-Quittung hat damit zum zweiten Mal einen Mangel gefangen, bevor Code entstand.**
Beim ersten Mal war es eine fehlende Definition, diesmal ein nicht existierender Prüfgegenstand.
Beide hätten ohne sie erst der Evaluator gefunden — nach dem Bauen.

## Reihenfolge

1. **Dieses Blatt (T1a)** — die Breite lernt, was die Höhe kann.
2. **T1b** — Blades an `admin.layouts.app`; die Ticket-Navigation erscheint. **Dort fallen auch die
   `calc(100vh − 46px)` weg**, zusammen mit der Blade-Leiste, zu der die 46 px gehören.
3. **T2** — die zweite und dritte Navigation fallen.
4. **T3** — Kopfleiste und Arbeitszeile; die 13-teilige Geschosszeile verschwindet.
5. **T5** — Eigenschaften-Panel klappbar, Escape-Stapel, Zustand je Arbeitsbereich.
