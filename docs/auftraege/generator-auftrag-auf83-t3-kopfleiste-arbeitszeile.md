# AUF-83-T3 — Eine Kopfleiste, eine Arbeitszeile, und die Geschosszeile fällt

*Planner, 29.07.2026, 10:05 CEST. Vierter Schritt von AUF-83. Grundlage:
`docs/planner/entwurf-studio-in-ticket-shell-2026-07-29.html` (Abschnitte 03 und 04),
von Yama am 29.07. um 08:20 freigegeben.*

> **GESPERRT, bis `AUF-83-T2` gebaut ist.** Grund: T3 baut die Leiste, in die T2 die Inhalte
> freiräumt. Beide gleichzeitig hieße, an derselben Datei zwei Posten zu führen — §13.

```yaml
auftrag:
  id: AUF-83-T3
  status: aktiv            # entsperrt: T2 ist gebaut (45656ac1 / 86059540)
  spur: A                  # 21:40 KORRIGIERT, war B — der Evaluator hat es belegt, nicht behauptet
  nachtrag: "29.07. 21:40 — Grundgesamtheit korrigiert · Vorher-Wert-Pflicht · Spur A"
  heimat: ticket
  ziel: >
    Der Planer traegt EINE Kopfleiste (Projekt, Geschoss, Modus, Speichern) und EINE Arbeitszeile
    (Arbeitsbereiche, 2D/Split/3D, Werkzeuge, Suche). Die 13-teilige Geschosszeile im Zeichenbereich
    entfaellt; Anlegen, Duplizieren und Loeschen wandern in das Menue des Geschoss-Waehlers.
  nicht_ziel: >
    KEINE Aenderung an der Ticket-Shell. KEIN neues Bedienmuster (Kontextmenue, Doppelklick) —
    gemessen 0 Fundstellen in der Insel, das ist ein eigener Auftrag.
    KEINE Umstellung von Inline-Stilen in HausplanerApp.tsx — Scheibe 7 bleibt gesperrt.
    ANGEFASST WIRD DORT AUSSCHLIESSLICH DIE GESCHOSSZEILE.

scope:
  population_command: >
    sed -n '1195,1240p' resources/planner/hausplaner/app/HausplanerApp.tsx &&
    node scripts/statische-inline-stile.mjs resources/planner/hausplaner/app/HausplanerApp.tsx
  population_at_writing: >
    KORRIGIERT 29.07., 21:40 — meine Zahl war ueberholt, und ich hatte sie nicht nachgemessen.
    ICH SCHRIEB: 13 Bedienelemente in vier Aufgaben (Layout-Inventur vom 25.07., Befund B1).
    GEMESSEN (Generator, 21:31, unmittelbar vor dem Schreiben nach R14):
      Zeile Z1183-1251 traegt 1 Knopf (Speichern) + <GeschossFlaeche> · 0 <select> · 0 <input>
    AUF-43 hat seither zwei P1-Kriterien dieses Blattes bereits erfuellt.
    DER ABRISS DER GESCHOSSZEILE HAT STATTGEFUNDEN — nur nicht durch diesen Auftrag.
    Scheibe 7: 138 gesamt / 78 statisch / 78 offen, unveraendert zu halten.
  pfade:
    - resources/planner/hausplaner/app/HausplanerStudio.tsx
    - resources/planner/hausplaner/app/HausplanerApp.tsx        # NUR die Geschosszeile
    - resources/planner/hausplaner/app/dashboard/GeschossFlaeche.tsx
    - resources/planner/hausplaner/app/dashboard/ReiterLeiste.tsx   # 30.07. aufgenommen, siehe K-05 auflage_geteilte_leiste
    - resources/planner/hausplaner/app/dashboard/arbeitsbereiche.ts  # 30.07. 06:45 nachgetragen — dein Befund 3: das Merkmal gehoert in die DATEN, nicht in HausplanerApp:105
    - resources/views/admin/hausplaner/objekt.blade.php          # Objektname + Uebernehmen-Knopf wandern (Zusage aus T2)
    - resources/planner/hausplaner/hausplaner.css
  ausschluesse:
    - stelle: "alles in HausplanerApp.tsx ausser der Geschosszeile und der neuen Arbeitszeile"
      grund: >
        **PRAEZISIERT 30.07., 06:20 — auf deine Kollisionsmeldung hin, und du hattest recht.**
        Der alte Wortlaut *„NUR die Geschosszeile"* und das Ziel *„eine Arbeitszeile mit
        2D/Split/3D, Werkzeugen und Suche"* konnten nicht beide stimmen: drei der vier Inhalte
        wohnen in `HausplanerApp` (gemessen: Ansichtsmodus, Werkzeugleiste, `paletteOffen:347` /
        `oeffnePalette:560` / ⌘K-Griff:1037). Nur `arbeitsbereiche.ts` steht frei.
        **Die Arbeitszeile wird DORT gebaut, wo ihre Inhalte wohnen — in `HausplanerApp`.**
        Sie in die Schale zu heben hiesse, Zustand hochzuziehen; das ist AUF-48, nicht diese
        Kopfleiste.
        **Der Ausschluss wird damit nicht aufgehoben, sondern messbar gemacht:** angefasst werden
        die Geschosszeile und die neue Arbeitszeile. Faellt eine FREMDE Inline-Stelle im Weg auf:
        melden, nicht mitnehmen. AUF-48 bleibt unberuehrt.
      entschieden_von: planner
      auflage: >
        **Neues Markup traegt `className`, keine Inline-Stile.** Gemessen: `HausplanerApp.tsx`
        hat heute **0 `className=`** — sie ist vollstaendig inline gestylt, und genau das ist
        Scheibe 7. Die Stilschicht liegt bereit (`resources/planner/hausplaner/hausplaner.css`,
        `build:hausplaner` erzeugt `public/hausplaner/hausplaner.css`).
        **Damit zieht T3 an AUF-38 mit, statt mit ihm zu kollidieren:** die Zahl der offenen
        Stellen muss **fallen oder gleich bleiben (78), sie darf nicht steigen.**
    - stelle: "Kontextmenues und Doppelklick"
      grund: >
        Gemessen 0 `onContextMenu` und 0 `onDoubleClick` in der ganzen Insel. Ein neues
        Bedienmuster ist kein Aufraeumen; eigener Auftrag, sonst wird aus *aufraeumen* ein Neubau.
      entschieden_von: planner

kriterien:
  - id: K-01
    status: NEU GESCHNITTEN 30.07. 06:45 — DER ALTE WORTLAUT WAR FALSCH
    aussage: "Die Kopfleiste traegt das DOKUMENT vollstaendig — und es bleiben genau drei Zeilen."
    typ: presence
    kritikalitaet: P1
    entscheid_30_07_0645: >
      **DEIN BEFUND 1 IST BERECHTIGT, UND DER FEHLER IST MEINER — nicht AUF-70s.** Ich habe
      nachgemessen, was heute steht, statt es aus dem Entwurf abzuleiten:
      Zeile 1 `HausplanerApp.tsx:1184` = Marke/Geschoss/Status/Speichern (das DOKUMENT).
      Zeile 2 `:1256` = `ARBEITSBEREICH` + `ReiterLeiste` mit den fuenf Bereichen (AUF-34/27).
      Zeile 3 `:1270` = die eine Werkzeugzeile mit 16 Knoepfen in 2·3·6·4·1 (AUF-70).
      **Die drei Zeilen, die T3 zu bauen versprach, STEHEN BEREITS — und Zeile 2 IST die
      Arbeitszeile.** Mein K-01 wollte Inhalte aus Zeile 3 nach oben ziehen, die AUF-70 vor drei
      Tagen bewusst nach unten gezogen hat.
      **Und Yamas eigener freigegebener Entwurf gibt AUF-70 recht, nicht mir:** Punkt 3 lautet
      *„Workspace + 2D/Split/3D + kompakte Werkzeugleiste"* — die Modusschalter gehoeren dort
      ausdruecklich in die Arbeitszeile, genau wo AUF-70 sie hat. **Keine der vier Zusagen wird
      gebrochen.**
    pruefung:
      typ: visuell
      schritte: "1440 px, Expertenmodus"
      erwartet: >
        Die Kopfleiste (Zeile 1) traegt links **Projekt UND Geschoss**, rechts Status und
        Speichern. **Heute fehlt das Projekt: gemessen 0 Fundstellen fuer einen Projektnamen in
        `HausplanerApp.tsx`.** Dazu wandern **Objektname mit Adresse und der Uebernehmen-Knopf
        samt Staleness-Pille** aus `objekt.blade.php` hierher — das ist die offene Zusage aus T2
        (`objekt.blade.php:81` sagt es selbst: *„Bleibt … wandert mit T3"*).
      und_zwar_ohne: >
        **Es bleiben GENAU DREI Zeilen.** Keine vierte. Wer Inhalt hinzufuegt, nimmt Platz aus
        Zeile 1 — nicht aus der Hoehe der Buehne.
    beleg: Bildschirmfoto vorher/nachher + DOM-Auszug der drei Zeilen
    ausgefuehrt_von: evaluator
    gegenprobe: >
      `npm run test:hausplaner -- --filter=eineWerkzeugzeile` **MUSS gruen bleiben** — alle vier
      Zusagen von AUF-70 (16 Knoepfe, 2·3·6·4·1, Gruppenreihenfolge, Dokumentzeile ohne Werkzeuge).
      **Wird eine davon rot, ist der Bau falsch, nicht die Zusage.**

  - id: K-01b
    status: ENTSCHIEDEN VON YAMA 30.07. 06:44 — AUF-70 GILT, KEINE AENDERUNG
    aussage: "Rueckgaengig und Wiederholen: Kopfleiste oder Werkzeugzeile?"
    typ: behavioural
    kritikalitaet: P2
    pruefung:
      typ: entscheidung
      erwartet: "keine — der Bau laeuft ohne diese Antwort weiter"
    entscheid_yama_0644: >
      **Yamas Wortlaut: *„ja die Rueckgaengig und Wiederholen sollen dort sein wie gerade ist in
      diese werkzeugzeile"*.** Damit gilt AUF-70 endgueltig, und zwar nicht als Notloesung bis
      jemand anders entscheidet, sondern als Entscheid.
      **Fuer den Bau heisst das: NICHTS zu tun.** Die vier Zusagen von AUF-70 bleiben, wo sie sind,
      und `eineWerkzeugzeile` bleibt gruen — **die Gegenprobe an K-01 ist damit keine Auflage mehr,
      sondern ein Waechter.**
      *Und der Punkt 2 seines Auftrags vom 29.07. ist damit korrigiert, nicht uebergangen: er
      nannte Undo/Redo in der Kopfleiste, er hat es sich angesehen und anders entschieden.
      Das gehoert so festgehalten — sonst liest es beim naechsten Mal jemand als offen.*
    begruendung: >
      **Das war der EINZIGE echte Widerspruch, und er gehoerte nicht mir.** Yamas Auftragspunkt 2
      nennt fuer die Kopfleiste *„rechts Speicherstatus/Speichern/Undo/Redo/Overflow"*.
      AUF-70 hat Rueckgaengig und Wiederholen bewusst in die Werkzeugzeile gezogen, mit der
      Begruendung *„Rueckgaengig zuerst: es ist die Rettungsleine und gehoert an den Anfang"* —
      und mit vier abgenommenen Zusagen verriegelt.
      **Beide sind vertretbar. Bis Yama entscheidet, gilt AUF-70** — der abgenommene Stand
      schlaegt den unentschiedenen. *Ein Auftrag, der eine abgenommene Zusage nebenbei
      zurueckdreht, hat sie nie ernst genommen.*
      Faellt die Entscheidung fuer die Kopfleiste, ist das ein **eigener Auftrag mit Begruendung,
      warum der zweite Entwurf den ersten schlaegt** — nicht ein Nebensatz in diesem hier.

  - id: K-02
    status: BEREITS ERFUELLT DURCH AUF-43      # bestaetigt vom Planner, 21:40
    nachweis: "GeschossFlaeche.tsx:138 — das Eingabefeld sitzt im Menue des Waehlers, nicht in der Zeile"
    hinweis: >
      NICHT als eigene Leistung berichten. Wer das nicht weiss, liest den Bau als Erfolg an einer
      Stelle, an der nichts geschehen ist — und die naechste Inventur schreibt die Zahl aus dem
      Blatt fort statt der gemessenen.
    aussage: "Das Textfeld mit dem doppelten Geschossnamen ist ersatzlos fort."
    typ: absence
    kritikalitaet: P1
    pruefung:
      typ: visuell
      erwartet: >
        Der Geschossname erscheint EINMAL. Heute steht er im Waehler UND direkt daneben in einem
        Eingabefeld — derselbe Wert, zweimal, nebeneinander.
    beleg: DOM-Auszug
    begruendung: >
      Das Umbenennen bleibt moeglich (im Menue des Waehlers). **Was faellt, ist die zweite Anzeige
      desselben Werts** — nicht die Faehigkeit.

  - id: K-03
    status: BEREITS ERFUELLT DURCH AUF-43      # bestaetigt vom Planner, 21:40
    nachweis: "GeschossFlaeche.tsx Z157/158/163 — alle drei sind Knoepfe im Menue"
    hinweis: "NICHT als eigene Leistung berichten. Siehe K-02."
    aussage: "Anlegen, Duplizieren und Loeschen liegen im Menue des Waehlers."
    typ: behavioural
    kritikalitaet: P1
    pruefung:
      typ: visuell
      schritte: "Geschoss-Waehler oeffnen"
      erwartet: >
        Ein Ort, an dem alles zum Geschoss steht: die Liste MIT Hoehenlage, darunter Anlegen,
        Darueber duplizieren, Loeschen. Alle drei Funktionen arbeiten wie vorher
        (`dupliziereGeschossJetzt` bleibt die Wahrheit, sie wird nur anders erreicht).
    beleg: Bildschirmfoto + eine ausgefuehrte Duplizierung
    ausgefuehrt_von: evaluator
    begruendung: >
      Yama: *„das feld etagen einfuegen nicht runter genommen.“* Im ersten Entwurf hatte ich nur
      den Waehler hochgezogen und die Bedienung daneben stehenlassen — die Zeile halb geraeumt und
      behauptet, sie falle weg.

  - id: K-04
    status: BEREITS ERFUELLT DURCH AUF-43
    aussage: "Die Hoehenlage ist sichtbar."
    typ: presence
    kritikalitaet: P2
    entscheid_30_07_0620: >
      **DEINE ZWEITE MESSUNG STIMMT — es ist das DRITTE Kriterium, das AUF-43 schon erledigt hat.**
      Mein Blatt sagte *„der Wert wird berechnet und nirgends gezeigt"*; gemessen stimmt nur die
      erste Haelfte. Gezeigt wird er an genau den zwei Orten, die K-04 verlangt:
      `GeschossFlaeche.tsx:113` (`kurzfassung` am Waehler: „Erdgeschoss · ±0 mm · 1 von 3"),
      `:100` (`hoehenLabel` in jeder Zeile der Liste), `:87` (im Titel-Attribut).
      **Wird als bereits erfuellt gefuehrt, nicht als deine Leistung** — dieselbe Regel wie bei
      K-02 und K-03.
    pruefung:
      typ: visuell
      erwartet: "Am Waehler und in der Liste steht die Hoehenlage (`±0 mm`, `+2 750 mm`)."
    beleg: DOM-Auszug
    begruendung: >
      `geschossStapel.ts` fuehrt `elevation` und `hoehenLabel` bereits — **der Wert wird berechnet
      und nirgends gezeigt.** Zweiter Fall dieser Art in diesem Projekt (der erste: die Griffe in
      `auswahlDarstellung.ts`). Bestandscode-first: anzeigen, nicht bauen.

  - id: K-05
    status: ZU DREI VIERTELN BEREITS ERFUELLT DURCH AUF-34/AUF-27 — GEKUERZT AUF DEN REST
    aussage: "Die Arbeitszeile fuehrt die fuenf Arbeitsbereiche — und Import sagt, dass er noch nicht traegt."
    typ: presence
    kritikalitaet: P1
    entscheid_30_07_0645: >
      **F-04, fuenfte Auspraegung, und wieder hast du sie gemeldet statt abgehakt.** Gemessen:
      `HausplanerApp.tsx:1256-1269` rendert `ARBEITSBEREICH` + `ReiterLeiste` aus
      `bereichReiter` (`:105`), gespeist aus `dashboard/arbeitsbereiche.ts` — **genau die
      geforderte Quelle und genau die fuenf.** Das steht seit AUF-34.
      **Offen ist nur die zweite Haelfte:** *„Import ist ausgegraut und sagt das auch."*
      Gemessen: `arbeitsbereiche.ts` kennt **kein** Feld fuer gesperrt, `ReiterLeiste.tsx`
      **keinen** gesperrten Reiter. Das ist echter Bau, kein Nachtrag.
      **`ReiterLeiste.tsx` wird in `pfade` aufgenommen** — mit Auflage, siehe unten. Es abzutrennen
      waere sauberer im Papier und teurer in der Sache: ein Auftrag, der ein Feld ergaenzt, und ein
      zweiter, der es benutzt, sind zwei Wartezeiten fuer eine Zeile Code.
    auflage_geteilte_leiste: >
      **`ReiterLeiste` hat drei Nutzer** (Panel-Reiter, Schienen-Reiter, Arbeitsbereiche — AUF-27).
      Das gesperrte Merkmal ist **optional**: wer es nicht setzt, bekommt exakt das heutige
      Verhalten. **Eine Zusage belegt, dass die beiden anderen Nutzer unveraendert sind** — nicht
      die Behauptung, sie seien es.
    pruefung:
      typ: visuell
      erwartet: >
        Import & Nachzeichnen · Architektur · Bauphysik · Heizung · Elektro · PV — die Quelle ist
        `arbeitsbereiche.ts`, nicht eine neue Liste. **Import ist ausgegraut** (er besteht heute
        aus Namen, nicht aus Funktion) und sagt das auch.
    beleg: DOM-Auszug + Herkunft der Daten im Diff
    begruendung: >
      **Die Fachplaner SIND die Arbeitsbereiche.** Sie hier zu fuehren ersetzt den Baum aus T2 —
      eine Benennung statt zweier.

  - id: K-06
    status: ABGETRENNT NACH AUF-85 — NICHT IN DIESEM AUFTRAG BAUEN
    aussage: "Die Befehlspalette ist sichtbar erreichbar."
    typ: presence
    kritikalitaet: P2
    entscheid_30_07_0620: >
      **DEIN HARTER FALL IST WIRKLICH HART, UND DU HAST RICHTIG NICHT GERATEN.** Gemessen:
      `HausplanerStudio.tsx:140` haengt `<HausplanerApp imStudio />` **nur im Expertenmodus** ein.
      In *Uebersicht* und *Gefuehrt* gibt es die Palette gar nicht — es ist dort nichts da, das
      man erreichbar machen koennte. Ein Einstieg in der Schale muesste sie also ERZEUGEN, und
      das ist genau die zweite Aktivierungslogik, die die Grenze dieses Kriteriums verbietet.
      **Ein Kriterium, dessen Erfuellung seine eigene Grenze verletzt, ist falsch geschnitten —
      das ist meiner, nicht deiner.**
      **ENTSCHEID: K-06 wird abgetrennt als `AUF-85` und haengt an AUF-48** (Zerlegung von
      `HausplanerApp`), nicht an dieser Kopfleiste. Im Expertenmodus bekommt die Arbeitszeile
      ihren `Suchen ⌘K`-Einstieg **innerhalb von `HausplanerApp`** — das ist K-05b unten und
      braucht keine zweite Logik, weil `oeffnePalette:560` dort lokal liegt.
    pruefung:
      typ: visuell
      erwartet: "Ein Einstieg in der Arbeitszeile (`Suchen ⌘K`), der die vorhandene Palette oeffnet."
    beleg: Bildschirmfoto + der geoeffnete Dialog
    grenze: >
      **Nicht bauen — erreichbar machen.** `dashboard/palette.ts` speist aus der Registry,
      `tools/trefferSuche.ts` sucht. Eine zweite Aktivierungslogik waere ein Fehler.

  - id: K-05b
    aussage: "Die Arbeitszeile traegt den Einstieg `Suchen ⌘K` — im Expertenmodus, in HausplanerApp."
    typ: presence
    kritikalitaet: P2
    pruefung:
      typ: visuell
      erwartet: >
        Ein sichtbarer Einstieg in der Arbeitszeile oeffnet die vorhandene Palette ueber
        `oeffnePalette` (`HausplanerApp:560`). **Kein zweiter Ausloeser, keine zweite Logik** —
        derselbe Aufruf, den der ⌘K-Griff in `:1037` schon benutzt.
    beleg: Bildschirmfoto + der geoeffnete Dialog + Herkunft des Aufrufs im Diff
    grenze: >
      Der Ersatz fuer das abgetrennte K-06, und er ist kleiner: **nur im Expertenmodus**, weil es
      die Palette nur dort gibt. Die Schale bekommt NICHTS.

  - id: K-07
    aussage: "Scheibe 7 ist unberuehrt."
    typ: absence
    kritikalitaet: P1
    pruefung:
      befehl: "node scripts/statische-inline-stile.mjs resources/planner/hausplaner/app/HausplanerApp.tsx"
      erwartet: >
        Die Zahl der offenen Stellen ist unveraendert — ODER die Abweichung ist Zeile fuer Zeile
        begruendet, weil die Geschosszeile selbst Inline-Stellen trug.
    beleg: rohausgabe vorher/nachher + Begruendung je Abweichung
    begruendung: >
      Hier ist eine Abweichung ERLAUBT, anders als in T1a — die Geschosszeile faellt ja weg und
      nimmt ihre Stellen mit. **Was nicht erlaubt ist: eine Stelle umzustellen, die bleibt.**

  - id: K-08
    aussage: "Der Zeichenbereich gewinnt messbar Hoehe."
    typ: coverage
    kritikalitaet: P1
    vorher_wert_pflicht: >
      DER GENERATOR HAELT DEN VORHER-WERT FEST, BEVOR ER BAUT — eine Zeile in der Quittung:
      getBoundingClientRect der LEINWAND (nicht der Wurzel), 1440 px, Expertenmodus.
      OHNE DIESE ZEILE IST DAS KRITERIUM NICHT ABNEHMBAR und der Bau beginnt nicht.
    pruefung:
      typ: visuell
      schritte: "1440 px, getBoundingClientRect der LEINWAND, gegen den Wert aus der Quittung"
      erwartet: "waechst; um wie viel, wird gemessen und berichtet — kein Sollwert"
    beleg: der Vorher-Wert aus der Quittung + die Nachher-Messung
    ausgefuehrt_von: evaluator
    barriere: >
      R9-BARRIERE, 29.07. 21:40 — ZWEITE WIEDERHOLUNG DERSELBEN KLASSE. T1a/K-07 und T2/K-06 sind
      beide unmessbar geworden, weil ihr Vorher-Wert nirgends stand und der Baum weiterlief.
      VORSCHLAG DES EVALUATORS, unveraendert uebernommen: "Ein Kriterium, das einen Vorher-Wert
      braucht, muss ihn im Auftrag festhalten lassen — vom Generator vor dem Bau, in einer Zeile.
      Wer ihn der Abnahme ueberlaesst, verliert ihn in dem Moment, in dem der Commit landet."
      GILT AB SOFORT FUER JEDES KRITERIUM MIT VORHER-BEZUG, in jedem Auftrag.

  - id: K-09
    aussage: "Geerbte Zusagen vollstaendig, nicht nach Muster."
    typ: coverage
    kritikalitaet: P1
    pruefung:
      befehl: "grep -rl 'HausplanerApp\\|HausplanerStudio\\|GeschossFlaeche' resources/planner/hausplaner/__tests__/"
      erwartet: "die LISTE steht in der Quittung, jede Datei angesehen"
    beleg: Dateiliste + je Datei ein Satz
    barriere: >
      R12-Barriere vom 29.07., 10:00. **Korrigiert 30.07. 06:45 auf deine Messung:** der
      `pruefung.befehl` sucht DREI Namen und liefert **27** Dateien — 22 fuer `HausplanerApp`
      allein, 7 fuer `HausplanerStudio`, 3 fuer `GeschossFlaeche`. **Meine 22 war die Zahl eines
      anderen Befehls.** Du hast die gemessene genommen statt meiner, und daraus kam Befund 1.

  - id: K-10
    aussage: "Gates ohne Regression."
    typ: presence
    kritikalitaet: P1
    pruefung:
      befehl: "npm run tsc:hausplaner && npm run schema:hausplaner:check && npm run test:hausplaner && npm run test:hausplaner:dom && npm run build:hausplaner"
      erwartet: "0/0/0/0/0"
    beleg: testzaehler vorher/nachher

auflagen_reihenfolge:
  buendel_still: >
    **R18, 30.07. 06:45 — aus deinem Befund 1, und die Regel ist deine.**
    Solange eine Sichtprobe **beauftragt** ist, bewegt niemand `public/hausplaner/*` und keine
    Blade, die ohne Bau sofort wirkt. **Beauftragt zaehlt wie laufend** — der Pruefende kann
    nicht erraten, wann er zu spaet ist.
    *Du hast es selbst gesehen und zurueckgestellt, bevor es jemand gemerkt haette. Genau deshalb
    steht es jetzt als Regel und nicht als Befund gegen dich.*
  was_du_TROTZDEM_bauen_darfst: >
    **K-05 (die freigegebene Haelfte) und K-05b — in den QUELLEN.** Bauen, Zusagen schreiben,
    Gates fahren. **Nur `build:hausplaner` nicht ausfuehren und nicht committen**, bis Teil A
    des Evaluators gemeldet ist. *Leerlauf ist ein Fehlzustand; ein stilles Buendel ist keiner.*
  k01_wartet: >
    **K-01 wartet auf `EVAL-2026-07-30-A`** — deine Lesart ist richtig und die Reihenfolge meine.
    Sobald die sechs Bilder und die drei Zeilenhoehen liegen, ist der Weg frei.

selbstnachweis:
  quittung_zuerst: "Readiness-Quittung nach §2, mit der Dateiliste aus K-09."
  gegenprobe: >
    Die Geschosszeile wieder einsetzen ⇒ K-01 und K-02 muessen rot werden.
  sichtprobe: >
    REIN VISUELL. Drei Viewports. **Und der Waehler ist ein Menue** — ein Standardbild zeigt es
    nicht. Es muss geoeffnet erfasst werden, wie beim Toast in Scheibe 4 und beim Dialog in
    Scheibe 5.
```

---

## Warum das Geschoss in die Kopfleiste gehört

Die Layout-Inventur nennt es **„das Tor"**: ein angelegtes Geschoss entsperrt auf einen Schlag
**34 von 110 Werkzeugen**. Die folgenreichste Handlung der ganzen Oberfläche steckte in einem
**111-px-Dropdown zwischen „Rückgängig" und „Speichern"** — und daneben stand derselbe Name noch
einmal in einem Eingabefeld.

**Vier unabhängige Aufgaben in einer Zeile** (Verlauf · Geschoss · Ansicht · Speichern) sind kein
Layoutproblem, sondern ein Informationsproblem: eine Fläche mit vier Jobs sagt nicht mehr, was sie
ist. Deshalb wandern sie dorthin, wo sie hingehören — und nicht alle an denselben neuen Ort.

## Was danach noch offen bleibt

**T5** — das Eigenschaften-Panel klappbar, Escape-Stapel, Zustand je Arbeitsbereich. Die
Vorbedingung liegt seit T1a: der Beobachter hängt an den **einzelnen** Schienen, nicht nur an der
Reihe. Das war die Zugabe des Generators, die niemand beauftragt hatte.


---

## Drei Entscheidungen vom 29.07., 21:40 — alle drei kommen von euch, nicht von mir

**1. Die Grundgesamtheit war überholt, und ich hatte sie nicht nachgemessen.** Meine 13
Bedienelemente stammten aus der Layout-Inventur vom 25.07. **AUF-43 hat zwei P1-Kriterien dieses
Blattes seither bereits erfüllt** — das Textfeld sitzt im Menü, Anlegen/Duplizieren/Löschen sind
Knöpfe dort. **Bestätigt.** Sie werden als *bereits erfüllt durch AUF-43* geführt, **nicht als
eigene Leistung** — genau aus dem Grund, den der Generator nennt: sonst liest jemand den Bau als
Erfolg an einer Stelle, an der nichts geschehen ist.

*Das ist F-04, vierte Ausprägung: eine Zahl im Auftrag, die ich nicht selbst gemessen habe.
**R11 hätte es gefangen** — ich habe den `population_command` hingeschrieben und nicht ausgeführt.*

**2. Der Vorher-Wert wird künftig vom Generator festgehalten, nicht von der Abnahme.**
`T1a/K-07` und `T2/K-06` sind beide unmessbar geworden, weil ihr Vorher-Wert nirgends stand.
**Zweite Wiederholung ⇒ R9 verlangt eine Barriere**, und der Vorschlag des Evaluators ist die
richtige: *eine Zeile in der Quittung, vor dem Bau.* **Kostet Sekunden, rettet ein P1.**

**3. Die Spur wird auf A korrigiert — und der Beleg ist eine Messung, keine Meinung.**
Der Evaluator hat es an T2 belegt: **neun Kriterien, sieben P1, zwei ihm zugewiesen — und der
Generator musste sieben Zusagen nachträglich erfinden**, weil die `grep`-Kriterien nichts
verriegelten. *„Eine Sache, die man so prüfen muss, ist keine ‚eine Ledger-Zeile'-Sache."*
**Er stuft nicht ein, ich schon — und er hat recht.** T3 ist ab jetzt **Spur A**.

*Alle drei Korrekturen stehen zuerst hier im Blatt und danach in Tafel und Ledger. Das ist die
Reihenfolge, die ich um 21:20 festgelegt habe, nachdem ich sie genau einmal andersherum gemacht
und damit einen halben Auftrag verursacht hatte.*
