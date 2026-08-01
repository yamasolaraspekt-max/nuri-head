# AUF-83-T1b — Der Hausplaner erbt die Ticket-Shell

*Planner, 29.07.2026, 08:25 CEST. Von Yama am 29.07. um 08:20 freigegeben, zusammen mit dem
Entwurf: „ich möchte dass du die navi von ticket sein wenn ich mich in einen bereich befinde dann
sehe ich dort".*

> **~~GESPERRT~~ — ENTSPERRT am 29.07. um 10:05.** Der Sperrgrund war technisch (*ohne T1a ein
> zweiter Bildlauf*), und **T1a ist seit 09:58 gebaut**. Neue Regel: *eine Sperre endet mit dem BAU
> der Vorbedingung, nicht mit ihrer ABNAHME.*
>
> **NACHTRAG 10:28 — `ProjektlisteTest::k3` wird praezisiert, nicht abgeschwaecht.** Sie prueft
> `assertDontSee('GEHEIM')` auf der **ganzen Seite**; das war deckungsgleich mit der Insel, solange
> die Seite ein eigenes Dokument war. **T1b hebt die Deckungsgleichheit auf, nicht den Schutz.**
> Ab jetzt prueft sie den Teilbaum `#hausplaner-root` samt `data-*`. **Pflicht-Gegenprobe:**
> Kundenname in `data-projekte` ⇒ **rot**; derselbe Name nur im Shell-Auswahlfeld ⇒ **gruen**.
>
> *Der urspruengliche Sperrhinweis:* Der Grund ist gemessen, nicht vorsichtig:
> `#hausplaner-root` setzt heute `min-height: calc(100vh − 46px)`, und `@yield('content')` sitzt in
> `.main-content-scroll`. **Ohne T1a ergäbe dieser Auftrag einen zweiten Bildlauf und einen
> Zeichenbereich unter der Falz** — also das Gegenteil des Ziels. Die Sperre steht in der Marke auf
> der Tafel, nicht nur hier.

```yaml
auftrag:
  id: AUF-83-T1b
  status: ruht   # entsperrt 10:05 — T1a ist gebaut
   # PB-B2, 01.08.2026 - Planner: `ruht` heisst, der Zustand ist NICHT nachgemessen.
   # Wer das Blatt zieht, misst zuerst. S-01 erwartet genau EIN aktives Blatt.
  nachtrag: "10:28 — K-08 (Gates): ProjektlisteTest::k3 wird auf #hausplaner-root praezisiert, mit Pflicht-Gegenprobe"
  spur: A
  heimat: ticket
  ziel: >
    Die zwei Hausplaner-Blades erben von admin.layouts.app. Die Ticket-Navigation ist da, der
    Eintrag "Hausplaner" markiert sich selbst, und der Planer sitzt zwischen den beiden
    Ticket-Seitenleisten statt daneben.
  nicht_ziel: >
    KEINE Aenderung an Routen, Middleware oder Rechten — die Absicherung liegt bereits
    (routes/web.php:4983/4988, beide hinter `auth`). KEINE neue Navigation. KEIN Eingriff in
    sidebar.blade.php oder app.blade.php — die Shell wird BENUTZT, nicht geaendert.
    KEINE Aenderung an der Insel selbst — das ist T2/T3.

scope:
  population_command: "grep -c '@extends' resources/views/admin/hausplaner/*.blade.php"
  population_at_writing: >
    Beide Blades: 0x @extends. Vollstaendige HTML-Dokumente mit eigenem <head>, eigenem <style>
    (studio Z17-28, objekt Z19-49) und eigenem Modul-Script. 19 lokale hp-*-Klassen.
    Messung des Planners, KEINE Bedingung.
  pfade:
    - resources/views/admin/hausplaner/studio.blade.php
    - resources/views/admin/hausplaner/objekt.blade.php
  ausschluesse:
    - stelle: "resources/views/admin/layouts/app.blade.php und sidebar.blade.php"
      grund: >
        Die Shell wird benutzt, nicht veraendert. Sie traegt das ganze CRM; eine Aenderung dort
        waere ein Eingriff in alle anderen Ansichten und damit ein voellig anderer Posten.
      entschieden_von: planner
    - stelle: "die fehlende permission:Hausplaner,read auf hausplaner.studio"
      grund: >
        Eigener Posten (P2). Kein Sicherheitsloch — die Flaeche ist persistenzfrei, save() ist ein
        No-Op —, aber eine Asymmetrie ohne Anlass. Sie hier mitzunehmen waere eine Rechteaenderung
        in einem Layout-Auftrag.
      entschieden_von: planner

anschlusspunkte:
  # gemessen in app.blade.php, 11.189 Zeilen
  titel:    "@yield('title')            Z11"
  stil:     "@yield('style') + @stack('style')   Z2535/2536"
  navi:     "@include('admin.layouts.sidebar')   Z4452"
  inhalt:   "@yield('content')          Z4837   (in .main-content-scroll, Z4836)"
  skripte:  "@stack('scripts') + @yield('script') Z11148/11149"

kriterien:
  - id: K-01
    aussage: "Beide Blades erben von der Ticket-Shell."
    typ: presence
    kritikalitaet: P1
    pruefung:
      befehl: "grep -c \"@extends('admin.layouts.app')\" resources/views/admin/hausplaner/studio.blade.php resources/views/admin/hausplaner/objekt.blade.php"
      erwartet: "je 1"
    beleg: grepausgabe
    partner: >
      presence-Partner: `grep -c '<!DOCTYPE' ` auf beiden Blades muss **0** sein — sonst steht das
      @extends neben einem zweiten Dokument und die Seite rendert doppelt.

  - id: K-02
    aussage: "Die Ticket-Navigation ist sichtbar und markiert den Hausplaner als aktiv."
    typ: behavioural
    kritikalitaet: P1
    pruefung:
      typ: visuell
      schritte: "angemeldet /admin/hausplaner/studio und /admin/hausplaner/objekt/203 oeffnen"
      erwartet: >
        Die linke Ticket-Seitenleiste ist da. In der Gruppe "Planung & 3D" traegt der Eintrag
        "Hausplaner" den Aktiv-Zustand (sidebar.blade.php:570 fuehrt active_routes
        ['/admin/hausplaner']). Ein Weg zurueck in die Ticket-Anwendung existiert ueber die
        Navigation, nicht ueber einen Extra-Link.
    beleg: Bildschirmfoto + der berechnete Aktiv-Zustand aus dem DOM
    ausgefuehrt_von: evaluator
    begruendung: >
      Das ist Yamas Kernsatz: "ich moechte dass du die navi von ticket sein wenn ich mich in einen
      bereich befinde dann sehe ich dort." Nicht "eine Navigation da haben" — SEHEN, WO MAN IST.

  - id: K-03
    aussage: "Beide Ticket-Seitenleisten klappen wie ueberall sonst im CRM."
    typ: behavioural
    kritikalitaet: P1
    pruefung:
      typ: visuell
      schritte: >
        Links zuklappen (.sidebar-left.collapsed), rechts ueber toggleRightSidebarDesktop().
        Danach je wieder auf.
      erwartet: >
        Der Plan wird BREITER, nicht abgeschnitten — 229 + 280 px gewinnt er zurueck. Kein
        waagerechter Bildlauf in keinem der vier Zustaende.
    beleg: vier getBoundingClientRect-Ausgaben des Plans
    ausgefuehrt_von: evaluator
    begruendung: >
      Der eigentliche Beweis, dass T1a getragen hat. Faellt K-03, ist nicht dieser Auftrag falsch,
      sondern T1a unvollstaendig — dann geht es dorthin zurueck, nicht in eine Nachbesserung hier.

  - id: K-04
    aussage: "Kein zweiter Bildlauf, die Insel fuellt den Inhaltsbereich."
    typ: absence
    kritikalitaet: P1
    pruefung:
      typ: visuell
      schritte: "1440 / 1024 px, beide Ansichten"
      erwartet: "document.scrollingElement.scrollHeight == clientHeight; der Plan endet sichtbar"
    beleg: scrollHeight/clientHeight je Ansicht
    ausgefuehrt_von: evaluator

  - id: K-05
    aussage: "Die eigenen Stile der Blades sind mitgewandert, nicht verloren."
    typ: coverage
    kritikalitaet: P2
    pruefung:
      befehl: "grep -c 'hp-' resources/views/admin/hausplaner/studio.blade.php"
      erwartet: >
        Die 19 lokalen hp-*-Klassen liegen in `@push('style')`. Welche davon mit T2 wegfallen,
        entscheidet T2 — hier wird nichts geloescht, nur verschoben.
    beleg: Klassenliste vorher/nachher
    grenze: >
      Ob diese Klassen spaeter in die Stilschicht der Insel gehoeren, ist eine AUF-38-Frage und
      NICHT Teil dieses Auftrags.

  - id: K-06
    aussage: "Die Insel mountet und laedt ihre Szene wie vorher."
    typ: behavioural
    kritikalitaet: P1
    pruefung:
      typ: visuell
      schritte: "beide Ansichten oeffnen, Konsole mit aktiver Aufzeichnung"
      erwartet: >
        0 Fehler. Das Szenen-JSON (`<script type="application/json" id="hausplaner-scene">`) wird
        gelesen; auf der Objektseite sind `data-speichern-url`, `data-pakete-url`,
        `data-snapshots-url` und `data-katalog-url` unveraendert am `#hausplaner-root`.
    beleg: Konsolenausgabe + DOM-Auszug der data-Attribute
    ausgefuehrt_von: evaluator
    begruendung: >
      Das ist die Stelle, an der ein Blade-Umbau still etwas kaputtmacht: die Naht zwischen Server
      und Insel haengt an Attributen, die beim Verschieben leicht in der falschen Section landen.

  - id: K-07
    aussage: "Keine Aenderung an Routen, Rechten oder der Shell."
    typ: absence
    kritikalitaet: P1
    pruefung:
      befehl: "git show --name-only --pretty=format: HEAD -- routes app resources/views/admin/layouts"
      erwartet: "leer"
    beleg: rohausgabe

  - id: K-08
    aussage: "Gates ohne Regression."
    typ: presence
    kritikalitaet: P1
    pruefung:
      befehl: "npm run tsc:hausplaner && npm run schema:hausplaner:check && npm run test:hausplaner && npm run test:hausplaner:dom && npm run build:hausplaner && php artisan test"
      erwartet: "0/0/0/0/0, PHP 789"
    beleg: testzaehler vorher/nachher

selbstnachweis:
  quittung_zuerst: "Readiness-Quittung nach §2."
  rueckweg: >
    Revert ueber genau zwei Dateien. Keine Migration, kein Schema, keine Route, keine Daten.
    Der Rueckweg ist damit ein Commit — und er ist ausserhalb der Maschine gesichert, sobald
    Yama gepusht hat.
  entdeckung: >
    Woran man merkt, dass es falsch ist: zweiter Bildlauf, fehlende Ticket-Navigation, oder eine
    Insel, die nicht mountet. Alle drei sind beim ersten Oeffnen sichtbar — keine stille Wirkung.
```

---

## Was dieser Auftrag NICHT anfasst, und warum das wichtig ist

**Die Ticket-Shell selbst.** `app.blade.php` hat 11.189 Zeilen und trägt jede Ansicht des CRM.
Dieser Auftrag **benutzt** sie über ihre fünf Anschlusspunkte und ändert dort keine Zeile. Wer die
Shell anpasst, um den Hausplaner hineinzubekommen, ändert alle anderen Ansichten mit — das wäre ein
anderer Posten, mit anderem Risiko und anderer Abnahme.

**Die Rechte.** Beide Routen liegen bereits hinter `auth`. Dass `hausplaner.studio` keine
`permission:Hausplaner,read` trägt, ist ein eigener P2-Posten — kein Loch, aber eine Asymmetrie.
**Sie hier mitzunehmen hieße, eine Rechteänderung in einem Layout-Auftrag zu verstecken.**

**Die Blade-Kopfleiste.** `hp-bar` mit Marke, Zurück-Link und Testflächen-Hinweis fällt erst mit
**T2**. Bis dahin steht sie unter der Ticket-Navigation — doppelt und hässlich, aber vollständig.
*Das ist Absicht: ein Auftrag, eine abnehmbare Einheit. Wer beides in einem Zug macht, kann bei
einem roten Votum nicht sagen, welcher Teil rot ist.*

## Der Satz, an dem dieser Auftrag gemessen wird

Yama: *„ich möchte dass du die navi von ticket sein wenn ich mich in einen bereich befinde dann
sehe ich dort."*

Nicht „eine Navigation da haben" — **sehen, wo man ist.** Deshalb ist K-02 kein Vorhandenseins-Test,
sondern ein Zustands-Test: der Eintrag *Hausplaner* in der Gruppe *Planung & 3D* muss den
Aktiv-Zustand tragen. Die Shell kann das bereits; sie wird nur nie danach gefragt.
