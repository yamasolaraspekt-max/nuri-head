# ⇒ PLANNER — T1: Entscheidungsgrundlage statt Frage

**Vom:** Planner · **29.07.2026, 01:10 CEST** · **Anlass:** Yama hat T1 (die Hausplaner-Blades an die
Ticket-App-Shell hängen) nicht entschieden, weil es der einzige Spur-A-Teil mit Auth, Rollen und
Routing ist. **Dieses Papier soll morgen früh eine Entscheidung möglich machen, ohne dass jemand
noch einmal messen muss.**

---

## 1. Was schon da ist — mehr, als ich erwartet hatte

**Die Authentifizierung fehlt nicht.** Beide Hausplaner-Routen liegen bereits hinter `auth`:

```
routes/web.php:4983   Route::middleware(['auth'])->get('/admin/hausplaner/studio', …)
routes/web.php:4988   Route::middleware(['auth'])->get('/admin/hausplaner', …)
                        ->middleware('permission:Hausplaner,read')
```

**Es fehlt also nicht die Absicherung, sondern nur die Layout-Vererbung.** Das nimmt T1 den
größten Teil seines Risikos: wir hängen eine bereits geschützte Seite in eine Hülle, nicht eine
offene.

### Ein Nebenbefund, der nicht zu T1 gehört, aber hierher (P2)

`hausplaner.index` trägt `permission:Hausplaner,read`. **`hausplaner.studio` trägt sie nicht** — nur
`auth`. Jeder angemeldete Benutzer erreicht die Studio-Fläche, auch ohne Hausplaner-Recht.

**Ich stufe das nicht als Sicherheitsloch ein**, und der Grund steht im Code: die Studio-Fläche ist
persistenzfrei (`data-speichern-url` fehlt, `save()` ist ein No-Op), es gibt dort keine Kundendaten
und nichts zu ändern. **Aber es ist eine Asymmetrie ohne begründeten Anlass**, und sie fällt genau
dann auf, wenn die Fläche später doch ein Speicherziel bekommt. Eigener Posten, nicht Teil von T1.

---

## 2. Die Anschlusspunkte — mechanisch, nicht interpretiert

`resources/views/admin/layouts/app.blade.php` ist **11.189 Zeilen** und bietet:

| Anker | Zeile | wofür bei T1 |
|---|---|---|
| `@yield('title')` | 11 | „Hausplaner — Studio" |
| `@yield('style')` + `@stack('style')` | 2535 / 2536 | der eigene `<style>`-Block der Blades |
| `@include('admin.layouts.sidebar')` | 4452 | **die Ticket-Navigation, um die es geht** |
| `@yield('content')` | 4837 | der `#hausplaner-root` |
| `@stack('scripts')` + `@yield('script')` | 11148 / 11149 | `<script type="module" src="hausplaner/hausplaner.js">` |

Der Umbau ist damit mechanisch beschreibbar: `<!DOCTYPE>`/`<head>`/`<body>` entfallen, `@extends`
kommt davor, die vier Blöcke wandern in ihre Sections. **Das ist wenig Arbeit — der Aufwand liegt
woanders.**

---

## 3. Der eigentliche Befund — und er hängt mit M4 zusammen

### M8 · Die Insel misst gegen das Fenster, nicht gegen ihren Behälter (P1)

```
studio.blade.php:24   #hausplaner-root { min-height: calc(100vh - 46px); }
objekt.blade.php:27   #hausplaner-root { min-height: calc(100vh - 46px); }
                                                       ^^^ die Höhe der EIGENEN hp-bar
```

Innerhalb der App-Shell landet `#hausplaner-root` hier:

```
app.blade.php:4554    <main class="main-wrapper">
app.blade.php:4835      </header>
app.blade.php:4836      <div class="main-content-scroll" id="mainContentScroll">
app.blade.php:4837        @yield('content')
```

**`100vh` innerhalb eines Scroll-Containers ist falsch.** Die Insel würde die volle Fensterhöhe
beanspruchen, obwohl über ihr eine Kopfzeile und neben ihr zwei Leisten stehen — Ergebnis: zweiter
Bildlauf, und der Zeichenbereich beginnt unterhalb der Falz. **Genau das Gegenteil von dem, was
Yamas Auftrag will.**

**Und das ist dieselbe Fehlerklasse wie M4 aus der Bestandsaufnahme:**

| | Rechnung | misst gegen |
|---|---|---|
| **M4** | `breite = innerWidth − 220 − 268` (`HausplanerApp.tsx:369`) | das **Fenster** |
| **M8** | `min-height: calc(100vh − 46px)` (beide Blades) | das **Fenster** |

**Beide Male rechnet die Insel ihre Maße aus Fensterkonstanten aus, statt sie von ihrem Behälter zu
nehmen.** Solange das so ist, ist sie nicht einbettbar — nicht in die App-Shell (M8) und nicht
neben klappbare Panels (M4).

**Das ist der Satz, der T1 und T4 verbindet:** es sind nicht zwei Aufgaben, sondern zweimal dieselbe.

---

## 4. Was ich daraus vorschlage

**T1 wird ein Zweischritt, und der erste Schritt ist der, der beide Probleme löst:**

**T1a — Die Insel nimmt ihre Maße vom Behälter.**
`min-height: calc(100vh − 46px)` wird `height: 100%` mit einem Behälter, der seine Höhe führt;
`innerWidth − 220 − 268` wird echtes Flex-Layout. **Kein Blade-Umbau, keine Auth, kein Routing** —
und danach ist die Insel sowohl einbettbar als auch overlay-fähig. **Spur A** (sie ändert
Layoutverhalten und trägt eine testverriegelte Formel), aber ohne Berührung von Rechten.

**T1b — Der Blade-Umbau.** `@extends`, vier Sections, Blade-Kopfleiste entfällt. **Erst danach**,
weil er sonst genau in den doppelten Bildlauf läuft.

**Der Gewinn dieser Reihenfolge:** T1a ist zugleich **T4**, und T4 ist der erste Zerlegungsschritt
von **AUF-48**. Drei Posten, ein Stück Arbeit — und der Teil mit Rechten und Routing (T1b) wird
kleiner, nicht größer.

---

## 5. Was für morgen früh offen bleibt

1. **T1b freigeben?** — Auth ist schon da, es geht um Layout-Vererbung an einer Live-Anwendung.
2. **Die fehlende `permission:Hausplaner,read` auf `hausplaner.studio`** — eigener Posten oder
   bewusst so lassen? (Meine Lesart: eigener Posten, gering, aber ohne Begründung asymmetrisch.)
3. **Die 19 `hp-*`-Klassen der Blades** — sie sind lokal definiert und wandern mit in `@push('style')`.
   Ob sie danach in die Stilschicht der Insel gehören, ist eine AUF-38-Frage und **nicht** Teil von T1.
