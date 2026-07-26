# „Ueberall gleich" — was das heisst, wenn man es misst

**26.07.2026, Planner.** Yama gibt den Go: *"Wir koennen es verdrahten, aber Frontend und Layout
muss ueberall gleich sein, darum geht es mir."*

**Der Go ist eingetragen. Die Bedingung ist die eigentliche Arbeit — und sie ist groesser, als sie
klingt.** Ich habe gemessen, wie einheitlich das Frontend heute ist.

---

## 1 · Fuenf Stilwelten nebeneinander

Dateien in `resources/views/`, die den jeweiligen Baustein laden:

| Baustein | Dateien |
|---|---|
| Bootstrap | **62** |
| lucide (Icons) | **71** |
| unpkg (CDN) | 64 |
| cdnjs (CDN) | 37 |
| **Tailwind ueber CDN** | **36** |
| font-awesome | 16 |
| phosphor | 2 |
| **`@vite` (gebauter Weg)** | **8** |

**Bootstrap und Tailwind stehen nebeneinander**, die Icons kommen aus **drei** Familien, und
**nur acht** Dateien gehen ueber den gebauten Weg. `cdn.tailwindcss.com` ist ausserdem der
Entwicklungs-Modus von Tailwind — er uebersetzt im Browser des Nutzers.

**Deine Sorge ist also keine Vorsichtsmassnahme, sondern eine Beschreibung des Ist-Zustands.**

## 2 · Aber: die Kette, um die es geht, ist bereits einheitlich

Genau die Ansichten, die an der Auslegung haengen, liegen **in der Standard-Huelle**:

```
wp_auslegung.blade.php        @extends('admin.layouts.app')
energiekonzept.blade.php      @extends('admin.layouts.app')
fussboden_check.blade.php     @extends('admin.layouts.app')
plan_upload.blade.php         @extends('admin.layouts.app')
```

*(Die `*_dokument.blade.php` haben bewusst keine Huelle — das sind Druckansichten.)*

**Und die Verdrahtung selbst bringt gar keine Oberflaeche mit.** P2-2 ist ein Datenpfad:
`POST /objekt/{objekt}/uebernehmen` schreibt nach `gebaeude_geometrie`. **Kein neues Panel, kein
neues Stylesheet, keine neue Seite.**

> **Der Go kann also genommen werden, ohne die Layoutfrage zu beruehren.** Die Verdrahtung
> vergroessert das Problem nicht — sie ist die einzige der besprochenen Arbeiten, die gar keine
> Oberflaeche anfasst.

## 3 · Wo dein Satz heute wirklich verletzt wird

**Bei den Prototypen.** `roof_config/config.blade.php`, `roof.blade.php`, `roofs.blade.php` und
`layouts/roof.blade.php` laden **Tailwind ueber CDN**, dazu `lucide` bzw. `phosphor` — eine eigene
Welt, ausserhalb von allem anderen.

**Das bestaetigt die Entscheidung von vorhin:** aus diesen Dateien nehmen wir die
**Geometrie-Funktionen** (`makeRafterGeometry`, `basisLatten`, `makePfanneTileGeometry`) und
**nicht die Dateien.** Die Formgeber sind stilfrei — sie liefern Koerper, keine Knoepfe. Wer die
Dateien hineinzoege, zoege die fuenfte Stilwelt mit.

## 4 · Und jetzt die unangenehme Stelle: der Planer ist sauber, aber getrennt

Gemessen:

```
hausplaner/index.blade.php    @extends('admin.layouts.app')
hausplaner/objekt.blade.php   eigenes <!DOCTYPE>
hausplaner/studio.blade.php   eigenes <!DOCTYPE>
geladen: hausplaner.css  (4x)  — sonst NICHTS
```

**Der Planer ist heute die disziplinierteste Oberflaeche im ganzen Repo:** ein eigenes Dokument,
**eine** CSS-Datei, deren Werte aus `studioDaten.ts` abgeleitet sind (AUF-38 Scheibe 1, heute
abgenommen, K5 belegt: die Tokens sind abgeleitet, nicht abgeschrieben). **Kein CDN, kein
Bootstrap, kein zweites Icon-Set.**

**Er ist damit sauber — und er ist nicht dieselbe Huelle wie der Rest.**

Daraus folgt eine Gabelung, und sie ist eine Entscheidung, keine Messung:

| Weg | Was er bedeutet |
|---|---|
| **A — der Planer zieht in `admin.layouts.app`** | „Gleich" im Sinne von *dieselbe Seite*. Er erbt damit Bootstrap, Tailwind-CDN und drei Icon-Familien. **Das Ergebnis waere einheitlicher aussehen und unordentlicher sein.** |
| **B — der Rest zieht zum Planer** | Eine Token-Quelle (`studioDaten.ts`), eine gebaute CSS, kein CDN. **Gross**, aber es ist die Richtung, in die AUF-38 ohnehin schon laeuft — und der einzige Weg, bei dem „ueberall gleich" auch „ueberall gut" heisst. |

**Meine Empfehlung ist B**, und zwar aus einem Grund, der nichts mit Geschmack zu tun hat:
**A macht das Problem groesser, nicht kleiner.** Eine Oberflaeche, die heute aus einer Quelle
gespeist wird, an vier Quellen anzuschliessen, ist keine Vereinheitlichung — es ist die vierte
Quelle.

**Was B konkret zuerst braucht** — und beides ist klein:

1. **Eine Antwort darauf, was die eine Wahrheit ist.** Ich schlage `studioDaten.ts` vor, weil sie
   heute schon eine ist und weil AUF-38 sie gerade zur CSS-Quelle macht.
2. **Ein Zaehler**, der meldet, wie viele Dateien noch an CDN/Bootstrap/zweitem Icon-Set haengen.
   Heute: 62 · 36 · 71. **Ohne Zahl ist „ueberall gleich" ein Gefuehl; mit Zahl ist es ein
   Fortschritt, den man sehen kann.**

## Was ich eintrage und was bei Yama bleibt

**Eingetragen:** P2-2 Go erhalten. Die Verdrahtung darf laufen; sie bringt keine Oberflaeche mit.

**Bei Yama:** Weg A oder Weg B. **Bevor das nicht entschieden ist, schreibe ich keinen Posten, der
Layout anfasst** — auch nicht die AUF-48-Auflage zum Dachform-Feld, denn die sitzt in der
Planer-Huelle und waere unter Weg A anders zu bauen als unter Weg B.
