# W-33 · CODE

## Wo der Code wirklich lebt

| Schicht | Datei im Repo | Zweck |
|---|---|---|
| 4/5 Oberfläche | **`app/StartView.tsx`** · **267 Zeilen** | **das ganze Werkzeug** — vier Komponenten, ein Export |
| 3 Anwendung | `app/state/projekte.ts` | **die Form der Liste und ihre Prüfung** — *eigener Gegenstand, siehe Abhängigkeiten* |
| Server | `app/Http/Controllers/Hausplaner/HausplanerController.php:101` | **woher die Liste kommt** — *nicht W-33, aber ohne sie hängt das Blatt in der Luft* |

**Am Bau-Stand gemessen:** `wc -l` → **267**.

## Die Landkarte der Datei

```text
:1       Dateikopf, eine Zeile
:2-7     sieben import-Zeilen
:9-21    Props        onGuided · onKonfigurator · projekte?
:23-28   HAUS_ZEICHEN — das Bildzeichen steht in der INSEL, nicht in den Daten
:30-43   ein Kommentarblock ohne Code: AUF-38 Scheibe 2, die statischen Stile
         sind nach hausplaner.css gewandert
:46-80   Karte           mit und ohne Ziel, zwei getrennte Rueckgaben
:82-163  ProjektKachel   div ohne Adresse, <a> mit Adresse
:165-191 HubKarte        mit Chips fuer die Untermodule
:193-267 StartView       der Export: Kopf · ZULETZT · Projekt · Schild · Fachplaner
```

## Die Kernstelle: eine Bedingung, zwei Gestalten

```ts
:137-139   if (!z.adresse) {
             return <div style={{ ...grund, cursor: 'default' }}>{rumpf}</div>;
           }
:141-161   return <a href={z.adresse} … style={{ ...grund, cursor:'pointer', … }}>{rumpf}</a>;
```

**`rumpf` (`:108-123`) und `grund` (`:125-134`) sind einmal gebaut und werden in beide Zweige
gereicht.** *Der Unterschied ist ausschließlich die Hülle: ein totes `div` oder ein lebendiges `<a>`.*

> **Das ist der Grund, warum „sichtbar, aber keine Schaltfläche" hier billig ist.** *Wer den
> Unterschied über zwei getrennte Komponenten gebaut hätte, müsste jede Änderung am Aussehen
> zweimal machen — und die zweite irgendwann vergessen.*

## Warum das Bildzeichen in der Insel steht und nicht in den Daten

```ts
:23-28
/**
 * AUF-78 — das Bildzeichen der Projektkacheln steht in der Insel, nicht in den Daten.
 * Der Server liefert vier Felder (Bezeichnung, Ort, Datum, Kennung) und **kein Markup**: ein
 * SVG-Pfad aus der Datenbank wäre ein Weg, auf dem Fremdes in die Seite käme.
 */
const HAUS_ZEICHEN = '<path d="M3 21h18M5 21V8l7-4 7 4v13"/>';
```

**Das ist eine Sicherheitsentscheidung mit einer Begründung, und sie gehört ins Blatt** — *wer die
Liste später um ein „eigenes Symbol je Projekt" erweitern will, muss diesen Satz zuerst widerlegen.*

**Der Controller zieht dieselbe Linie von der anderen Seite** (`:88-96` dort): *keine Kundendaten,
nur die vier angezeigten Felder per `select()`, harte Obergrenze statt Paginierung.* **Beide Seiten
begrenzen, was durchgeht.**

## Ein Kommentarblock ohne Code — und er trägt eine Regel

```text
:30-43   AUF-38 Scheibe 2

  „Hier standen acht konstante React.CSSProperties-Objekte. Sie tragen keine
   Messung und keinen Zustand, also gehoeren sie in die Stilschicht …

   Was NICHT gewandert ist: alles, was aus dem Zeiger (hover), aus einem Zustand
   (dominant) oder aus einer Messung kommt. Ziel ist NULL STATISCHE Inline-Stile,
   nicht null Inline-Stile — eine gerechnete Breite in eine Klasse zu pressen
   baut einen Fehler."
```

> **Der Block ist leer und trotzdem die wichtigste Stelle für jeden, der hier aufräumen will.**
> *Er erklärt, warum in `:110`, `:115`, `:126-134` und `:149-160` weiterhin Inline-Stile stehen —
> sie hängen alle an `hover` oder `dominant`.* **`stilschicht` und `rohwertZusage` bewachen genau
> diese Trennung.**

## Abhängigkeiten

| Braucht | Warum | Richtung geprüft? |
|---|---|---|
| `app/studioDaten` | `T`, `FACH`, `PROJ`, `FachHub` — **W-38** | **einseitig**, W-38 kennt `StartView` nicht |
| `app/state/projekte` | `ProjektEintrag` — nur der **Typ** | einseitig |
| `app/dashboard/dialogFokus` | `istAusloeser` — die gemeinsame Tastenregel | einseitig |
| `app/studioUi` | `Ikon`, `ZustandBadge` | einseitig; `ZustandBadge` ist das Muster aus AUF-25 |
| **wer W-33 braucht** | **W-39** rendert es im Modus `start` | **BETRIEBSBESTAETIGT** |

**SECHS `import`-Zeilen** — *gezählt, nicht abgeschätzt:* `grep -c '^import ' StartView.tsx` → **6**.

```text
:2 react · :3 dialogFokus · :4 studioDaten · :5 state/projekte (import type)
:6 ZustandBadge · :7 Ikon
```

> **Die Zeilenspanne `:2-7` verführt zur Sieben, und sie sind sechs.** *Genau der Fehler, den ich bei
> W-35 gemacht und dort korrigiert habe: eine Spanne zählen statt der Zeilen.* **`import type` in
> `:5` ist dabei mitgezählt — `^import ` erfasst sie, weil auch sie mit `import ` beginnt.**
>
> **`:6` und `:7` holen beide aus `studioUi`** — *zwei Zeilen für dasselbe Modul.* **Sechs Zeilen,
> **fünf** Module. Wer Module zählt, bekommt eine andere Zahl als wer Zeilen zählt; beide stimmen und
> messen Verschiedenes.**
