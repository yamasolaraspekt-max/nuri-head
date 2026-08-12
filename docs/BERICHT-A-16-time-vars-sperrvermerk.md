# Baubericht A-16 — der Sperrvermerk steht jetzt an der Zahl, und keine Ziffer hat sich bewegt

```yaml
auftrag: "A-16"
rolle: "generator"
blatt: docs/auftraege/aktiv/A-16-time-vars-im-produktivcode.md
basis_sha: a2961b42
gebaut_am: "12.08.2026"
weg: "W3 — Warnschild und Sperrvermerk, Datei bleibt (Yamas Weiche, Release-Prüfer in Vertretung)"
zustand: CODE_FERTIG
ballbesitz: evaluator
```

> **Die eine Zahl, an der dieser Auftrag hängt, ist eine Null:** *0 geänderte Ziffern.* **Yamas
> Punkt 4 wörtlich — „KEIN Wert wird geändert, bevor ICH die richtigen genannt habe."**

## A-16-5 · Kein Wert geändert — die Probe, die den Auftrag trägt

```text
die elf Zeitwerte und der Stundensatz, vorher (HEAD) gegen nachher:
  vorher    8 6 5 4 12 2 90 60 45 25 40   ·   * 65
  nachher   8 6 5 4 12 2 90 60 45 25 40   ·   * 65
```

**Zusätzlich strukturell belegt, nicht nur per Augenschein:**

```text
git diff --numstat -- roof.blade.php        ->  43   0     (43 Einfügungen, 0 Löschungen)
eingefügte Zeilen, die KEIN Kommentar sind  ->   0
```

*Damit ist A-16-2s Gegenprobe („`git diff` zeigt ausschließlich Kommentarzeilen") nicht behauptet,
sondern gezählt — und die 0 gelöschten Zeilen schließen aus, dass eine Zahl durch eine andere
ersetzt wurde.*

## A-16-1 · Aufrufer — abschließend statt stichprobenhaft, und die offene Zeile ist zu

**Der statische Befund war schon da (0/0/0). Offen war die dynamische Lücke** — das Blatt sagt es
selbst: *„Dynamische View-Namen sind nicht ausgeschlossen (`ProductController.php:443`) — offen in
A-16-1."*

```text
view() mit einer VARIABLEN im ganzen Haus:   4 Treffer  ->  davon EINE echte Stelle
  AdminController.php:97        public function draf_view($id)      Methodendefinition
  NewLeadsController.php:1911   public function view($id)           Methodendefinition
  product.blade.php:5903        setProductImagePreview(…)           JavaScript
  ProductController.php:443     view($view, compact(…))             ECHT
```

> **Drei der vier Treffer sind keine `view()`-Aufrufe, sondern Methoden, die zufällig so heißen.**
> *Mein Muster traf den Text und nicht die Sache — **H-9**, an dem Tag gebaut, an dem ich es hier
> brauchte.*

**Die eine echte Stelle, einzeln geöffnet, wie das Kriterium es verlangt:**

```php
// ProductController.php:438-440 — zwei Zeilen über dem Aufruf
$view = $request->input('view_type') === 'card'
      ? 'admin.product.product.partials.product_cards'
      : 'admin.product.product.partials.product_list';
```

**`$view` kann genau zwei Werte annehmen, beide fest, keiner davon `admin.layouts.roof`.**
*Damit lautet die Aussage vollständig und mit ihrer Reichweite:* **kein Aufrufer, statisch UND
dynamisch geprüft.**

## A-16-2 · Der Sperrvermerk am Ort der Zahlen

| Stelle | Inhalt |
|---|---|
| `roof.blade.php:74-101` | über `TIME_VARS`: **F-051 GESPERRT**, die vier Fundorte mit null Herkunftsangaben, was je Wert fehlt (Quelle · Datum · Gewerk), die gemessene Reichweite der Datei, Verweis auf das Auftragsblatt |
| `:1701-1714` | über `laborCost`: der Stundensatz als **eigener Posten** |

*Beide Vermerke sagen ausdrücklich **nicht ändern ohne Yamas Firmenwerte** — mit seiner Begründung
im Wortlaut: „Eine falsche Zahl durch eine andere falsche zu ersetzen ist keine Korrektur."*

## A-16-3 · Der Stundensatz, getrennt geführt

**`* 65` ist keine Zeitannahme, sondern ein Preis** — deshalb ein eigener Vermerk statt einer Zeile
im TIME_VARS-Block. *Er nennt dieselben drei Fragen (Quelle, Datum, Gewerk) und **ausdrücklich keinen
Vorschlagswert**: ein Vorschlag wäre genau die falsche Zahl, die Yamas Auflage verbietet.* Und er
sagt, was die Zeile tut: **aus unbelegten Minuten und einem unbelegten Stundensatz einen
Lohnkostenbetrag — beide Faktoren Platzhalter, das Ergebnis damit auch.**

## A-16-4 · Der falsche Vorbehalt — benannt, NICHT verschoben

*Der vorhandene Vorbehalt hängt an der **Zeit** (`'Zeit & Aufwand'`) statt am **Geld**
(`'Montage (Arbeit)'`, jetzt Zeile 2309) und spricht den **Nutzer** an, obwohl nur ein Entwickler
`TIME_VARS` ändern kann.* **Nicht umgehängt, und die Begründung steht im Kriterium selbst:** *eine
Sichtänderung an einer nicht ausgelieferten Datei ist nicht prüfbar — es gibt keinen Bildschirm, an
dem der Evaluator sie messen könnte.* **Die Umhängung geschieht, wenn die Datei ausgeliefert wird.**

## A-16-6 · F-051 um den vierten Fundort — mit der gemessenen Lage

`FORMELSAMMLUNG.md` trägt den Abschnitt bereits (Planner). **Ergänzt wurde der Bau-Nachtrag:** die
neuen Zeilenlagen (`:74-101`, `:102`, `:1701-1714`, `:1715`, `:2309`) **neben den alten**, damit
niemand zweimal sucht — *und die Auflösung der offenen Zeile zur dynamischen Lücke.*

**Die Ampel bleibt 🔴** — *weder schärfer noch milder: die Werte sind zeichengleich, es kam nur der
Vermerk hinzu.* **Aus W3 wird ohne Umbau W2, sobald Yamas Firmenwerte vorliegen.**

## A-16-7 · Die Belegkette, ausdrücklich freigemessen

```text
fetch(   0        (wörtlich gesucht — mein erster Versuch sprengte den Ausdruck an der Klammer)
axios · $.post · $.ajax · <form · XMLHttpRequest · sendBeacon      je 0
action=  7   ->  ALLE SIEBEN GELESEN: data-action="next|offer|back|layout|clearModules|print|
                 backFromPrint" — Knopfmarken für clientseitiges JavaScript, kein Formularziel
```

> **0 Schreibpfade zum Server. Angebot → Auftrag → Rechnung ist aktenkundig unberührt.** *Die
> sieben `action=`-Treffer sind der Grund, warum das Kriterium „gelesen, nicht gezählt" verlangt:
> eine Zahl allein hätte hier nach sieben Formularen ausgesehen.*

## `must_preserve`, Rückweg, Rückfallpunkt

| | Ergebnis |
|---|---|
| `resources/**` außer der einen Datei | **byte-identisch** |
| hinzugefügt · entfernt (`resources`/`app`) | **0 · 0** |
| Rückweg | `git apply --check -R` → **Exit 0**, Arbeitsbaum unangetastet |
| Rückfallpunkt am Bautag | Basis `a2961b42` **liegt auf `fork/auto/hausplaner-integration`** (`merge-base --is-ancestor` Exit 0) |
| Zerstörungsfrei | die Datei wurde **nicht verschoben und nicht gelöscht** — W1 bleibt jederzeit möglich |

## Nebenbefund — nicht mitbehandelt, damit er nicht als erledigt gilt

```text
roof.blade.php:1716    const misc = 500;
```

**Direkt unter der `laborCost`-Zeile steht ein dritter unbelegter Wert** — *weder Zeit noch
Stundensatz, sondern eine Pauschale.* **Er steht in keinem Kriterium dieses Auftrags**, und ich habe
ihn nicht mitvermerkt: *A-16 nennt genau zwei Stellen, und wer bei dieser Gelegenheit eine dritte
mitnimmt, macht aus einem prüfbaren Auftrag einen unscharfen.* **Hier benannt, damit er nicht
untergeht — Entscheidung beim Planner.**

## Berührte Dateien

```text
resources/views/admin/layouts/roof.blade.php                     +43 / -0   nur Kommentarzeilen
docs/rollenkette/werkbank/01-MATHEMATIK/FORMELSAMMLUNG.md        Bau-Nachtrag zu F-051
docs/BERICHT-A-16-time-vars-sperrvermerk.md                      dieser Bericht
docs/STATUS.md                                                   Zustand an beiden Orten
```
