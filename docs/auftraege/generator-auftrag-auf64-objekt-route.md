# ⇒ GENERATOR-AUFTRAG AUF-64 — 🔴 `objekt/203` lädt wieder

**Vom:** Planner · **26.07.2026, 00:50** · **Dringend:** Die Route liefert seit `e0d1144` einen
PHP-ParseError statt der Seite. **Das ist die Route, die Yama benutzt.** Vorrang vor allem.

**Vorher gelesen:** HEAD `d074c9c` · `git log -5` · Tafelzeile AUF-64 ·
`resources/views/admin/hausplaner/objekt.blade.php:62, 97-103` ·
`storage/framework/views/a3018339….php` (Kompilat) ·
`app/Http/Controllers/Hausplaner/HausplanerController.php:29-38`.

---

## 1. Der Befund — belegt aus dem Kompilat, nicht aus der Fehlermeldung

Ignition zeigt auf Zeile 63 (`<form class="hp-uebernahme">`). **Dort liegt der Fehler nicht.**
Das erzeugte PHP sagt es genau:

```php
<?php($szeneLeer = empty($dokument->scene_json['nodes'] ?? []))
        <form class="hp-uebernahme" method="POST" ...
```

**Kein Semikolon, kein schließendes `?>`.** PHP liest das folgende HTML weiter als Code und stolpert
über das erste Schlüsselwort — `class`.

**Ursache:** Die Datei trägt seit `7b18ed4` in **Zeile 62** ein **inline** `@php(...)`.
`e0d1144` (AUF-60) hat in **Zeile 97–103** zusätzlich einen **Block** `@php … @endphp` eingefügt.
Beide Formen in derselben Datei bringen Blades Compiler durcheinander — das inline-`@php(...)` wird
nicht mehr als Ausdruck kompiliert, sondern als Blockanfang.

**Nicht betroffen:** `/studio` (eigenes Blade, Konsole gemessen leer).
**Betroffen:** jede Objekt-Seite, nicht nur 203.

## 2. Der Weg — entschieden, nicht zur Wahl gestellt

Zwei Wege wären möglich: den neuen Block ebenfalls inline schreiben, **oder** die Rechte im
Controller berechnen. **Es wird der Controller.** Gründe:

1. **Der Fehler kann so nicht wiederkommen** — im Blade bleibt gar kein `@php`-Block übrig.
2. **Logik gehört nicht ins Template.** `auth()->user()`, eine `collect()`-Kette und vier
   `hasPermission`-Aufrufe sind Anwendungslogik. `HausplanerController::seite()` (Z. 29-38) reicht
   ohnehin schon `objekt`, `dokument` und `uebernahme` durch — eine vierte Variable ist dort kein
   neuer Mechanismus, sondern derselbe.
3. **Es ist im Test erreichbar.** Eine Controller-Methode kann geprüft werden; ein `@php`-Block im
   Blade nicht.

**Im Blade bleibt genau eine Zeile:** `data-rechte="{{ $hpRechte }}"`.

## 3. Was **nicht** geändert wird

- **`User::hasPermission` bleibt unberührt.** Die Rechte-Wahrheit ist und bleibt der Server.
- **Die Liste bleibt dieselbe vier** (`read` · `add` · `update` · `delete`), und **ohne
  angemeldeten Nutzer bleibt sie leer** — das Minimum, wie in AUF-60 §3(b) festgelegt.
- **Das inline `@php(...)` in Zeile 62 bleibt stehen.** Es ist nicht der Fehler, es ist das Opfer.
  Wer es „bei der Gelegenheit" umbaut, ändert einen fremden Posten (W-A) mit.
- **Kein Bundle-Rebuild nötig** — es ändert sich kein ausgeliefertes Artefakt. Die Meldung
  „kein Rebuild, weil `public/*` unberührt" ist hier die richtige und erfüllt §8.

## 4. Die Regressionssperre — der eigentliche Wert dieses Postens

**1007 Tests haben das nicht gefunden, weil keiner ein Blade kompiliert.** Genau das gehört dazu:

**Ein Test, der die Hausplaner-Blades durch den Blade-Compiler schickt und fehlschlägt, wenn eines
nicht parst.** Nicht rendern, nicht ausführen — nur kompilieren und syntaktisch prüfen. Das ist
billig, schnell und hätte diesen Fehler in derselben Sekunde gefangen, in der er entstand.

*(Der Hausplaner-Testlauf ist Node, nicht PHP — dieser Test gehört in die PHP-Suite. Ist das nicht
in einem Zug machbar, dann **melden**, nicht improvisieren.)*

## 5. Abnahmekriterien

1. **`objekt/203` lädt.** Belegt durch Aufruf, nicht durch Behauptung — Statuscode und ein Stück des
   erwarteten Inhalts im Bericht.
2. **Kein `@php`-Block mehr im Blade:** `grep -c "@endphp"` in `objekt.blade.php` = **0**.
3. **`data-rechte` trägt denselben Wert wie vorher** — die vier Rechte des angemeldeten Nutzers,
   Leerzeichen-getrennt. Ein Test belegt die Berechnung im Controller.
4. **Ohne Nutzer: leer.** Test mit `auth()->user() === null` ⇒ leerer Wert, **nicht** alle Rechte.
   *Das ist das Kriterium, das AUF-60 seinen Sinn gibt, und es darf beim Umzug nicht verlorengehen.*
5. **Blade-Kompilierungstest greift:** vorgeführt an einem absichtlich kaputten Blade (rot), dann
   zurückgebaut (grün). Zahl der roten Tests nennen.
6. **`tsc:hausplaner` · `test:hausplaner` unverändert grün** — dieser Posten fasst die Insel nicht an.
7. **Klassifikation: `sichtbar`** — im wörtlichsten Sinn: vorher war die Seite weg, nachher ist sie da.

## 6. Zum Ablauf, ohne Vorwurf

Der Fehler ist beim Bauen eines sauber gemessenen Postens entstanden; der Bericht zu AUF-60 war
gründlich. **Was gefehlt hat, ist ein Aufruf der Seite nach der Änderung** — die Blade-Änderung war
Teil eines Postens, dessen Sichtprobe im Expertenmodus stattfand, nicht auf der Objekt-Route.

**Daraus die Ergänzung, die ich mir selbst aufschreibe:** Ändert ein Posten ein Blade, gehört die
**davon betroffene Route** in die Sichtprobe — nicht die, an der man gerade arbeitet.
