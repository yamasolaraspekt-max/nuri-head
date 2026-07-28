# ⇒ PLANNER — Bestandsaufnahme zum Auftrag „Topbar und Sidebars aufräumen"

**Vom:** Planner · **29.07.2026, 01:00 CEST** · **Auftrag:** Yama —
*„Räume die Oberfläche des Hausplaner-Studios auf. Der Zeichenbereich soll deutlich mehr Platz
erhalten."* · **Punkt 6 des Auftrags verlangt Bestandscode-first — das ist dieses Papier.**

> **Verfahren wie in der Layout-Inventur vom 25.07.:** unter jedem Befund steht eine Messung mit
> Datei und Zeile. Wo die Messung meinem Eindruck widerspricht, gewinnt die Messung. Der Grund steht
> in jenem Papier unter **B9** — ein Augenschein hätte dort einen Defekt gemeldet, den es nicht gab.
>
> **Kein Auftragsblatt in diesem Papier.** Erst die Messung, dann der Schnitt, dann die Blätter.

---

## 0. Die eine Korrektur, die alles andere verschiebt

**Punkt 1 des Auftrags lautet *„Ticket-Navigation beibehalten"*. Sie ist nicht da.**

```
grep -c "@extends" resources/views/admin/hausplaner/studio.blade.php   ->  0
grep -c "@extends" resources/views/admin/hausplaner/objekt.blade.php   ->  0
jede andere Admin-View                                                 ->  @extends('admin.layouts.app')
```

Beide Hausplaner-Blades sind **vollständige eigene HTML-Dokumente** mit eigenem `<head>`, eigenem
`<body>` und eigener Kopfleiste. Sie ziehen aus der Ticket-Anwendung genau **eine** Sache:
`@include('admin.layouts.partials.sa-ui')` — die CI-Farbtokens. Sonst nichts. `studio.blade.php:11`
sagt es selbst: *„STANDALONE-Testfläche"*.

**Damit ist Punkt 1 kein Erhaltungs-, sondern ein Herstellungsauftrag** — und er ist der einzige
Teil des ganzen Vorhabens, der **Spur A** ist: er berührt Layout-Vererbung, Authentifizierung,
Rollen und Routing. Alles andere in deinem Auftrag ist Umbau innerhalb der Insel.

**Und er erklärt zwei deiner Beobachtungen als Folge, nicht als Ursache.** Die doppelte
Bezeichnung und der doppelte Testflächen-Hinweis entstehen nicht, weil jemand zweimal dasselbe
gebaut hat, sondern weil **zwei Schichten unabhängig voneinander eine Kopfleiste zeichnen**: die
Blade eine, die Insel eine. Wer nur in der Insel aufräumt, entfernt jeweils die Hälfte.

---

## 1. Die Doppelungen — gemessen, mit Schicht

### M1 · Der Testflächen-Hinweis steht in zwei Schichten (P1)

| Ort | Beleg | Art |
|---|---|---|
| Blade | `studio.blade.php:34` — `<span class="hp-scratch">Testfläche — wird NICHT gespeichert</span>` | **feste Zeichenkette** |
| Insel | `dashboard/speicherAnzeige.ts:45` — `text: 'Testfläche — wird nicht gespeichert'` | **abgeleitet** aus dem fehlenden Speicherziel |

**Der zu entfernende ist der Blade-Text, nicht der Insel-Text** — und das ist keine
Geschmacksfrage: der Insel-Text hängt daran, ob `data-speichern-url` gesetzt ist, ist
testverriegelt (`__tests__/speicherAnzeige.test.ts:29`) und **sagt auf der Objekt-Fläche
automatisch nichts**. Die Blade-Zeichenkette steht immer da, egal was gilt.

`speicherAnzeige.ts:8` hat den Widerspruch bereits schriftlich festgehalten: die Plakette
„Gespeichert · Rev. 1" stand direkt neben dem Warnhinweis. **Der Befund ist alt und benannt, er
hatte nur nie einen Posten.**

### M2 · Die Hausplaner-Bezeichnung steht dreimal, nicht zweimal (P2)

```
studio.blade.php:32   <span class="hp-title">Hausplaner · Studio</span>
studio.blade.php:59   <h1>Hausplaner — Studio</h1>            (im #hausplaner-root-Skeleton)
HausplanerStudio.tsx  „Hausplaner" + <span class="hp-marke-zusatz">· Solar Aspekt</span>
```

**Die dritte ist vermutlich harmlos und muss trotzdem gemessen werden:** das `<h1>` liegt im
Skeleton innerhalb `#hausplaner-root` und sollte beim Mount ersetzt werden. **Ob es das tut, ist
nicht belegt** — bleibt es beim Fehlschlag der Insel stehen, ist es genau der Ladeplatzhalter, der
gebraucht wird. Das gehört gemessen, bevor jemand es entfernt.

### M3 · „Experte — alle Werkzeuge …" ist eine eigene Zeile (P2)

`HausplanerStudio.tsx:196` — `<span className="hp-experte-hinweis">Experte — alle Werkzeuge,
Projektbaum und Eigenschaften. Dasselbe Modell …</span>`, gefolgt von
`Z194` *„‹ Zur geführten Planung"* als eigene Zeile darüber der Bühne.

**Beides ist Erklärtext im Dauerbetrieb** — er beantwortet eine Frage, die man einmal hat, und
kostet danach jeden Tag Höhe.

---

## 2. Der technische Kern: eine Zeile entscheidet über Punkt 4

### M4 · Die Canvas-Breite ist fest gerechnet (P1 — hieran hängt das ganze Overlay-Konzept)

```
HausplanerApp.tsx:369
  const breite = (typeof window !== 'undefined' ? window.innerWidth : 1200) - 220 - 268;
                                                                        //  ^Werkzeugleiste ^Panel
HausplanerApp.tsx:1371   <div style={{ width: 220, flex: '0 0 auto', … }}>   linke Werkzeugleiste
HausplanerApp.tsx:1796   <div style={{ width: 268, flex: '0 0 auto', … }}>   rechtes Eigenschaften-Panel
HausplanerApp.tsx:1442   „(innerWidth − 220 − 268) unberührt" — die Formel steht als ZUSAGE im Test
```

**Solange diese Zeile steht, kann kein Panel als Overlay laufen.** Klappt ein Panel zu, erfährt der
Canvas es nicht — er rechnet weiter mit 488 px Abzug. Das ist der Grund, warum der Zeichenbereich
sich beim Aufräumen der Kopfleisten **nicht** vergrößern wird: die Breite kommt nicht aus dem
Layout, sie kommt aus einer Subtraktion.

**Und die Formel ist testverriegelt** (Z1442). Wer sie ändert, muss die Zusage mitziehen — sonst
geht sie rot, ohne dass ein Fehler vorliegt. *Das ist genau der Bautyp, der uns in AUF-38 fünfmal
begegnet ist: eine geerbte Zusage, die den alten Zustand festhält.*

### M5 · Von drei Flächen ist heute **eine** klappbar (P1)

| Fläche | Zustand | Beleg |
|---|---|---|
| Studio-Navigation (links außen) | **klappbar**, 266 ↔ 66 px | `HausplanerStudio.tsx:25` `navZu` |
| Werkzeugleiste (links innen, 220 px) | **kein Zustand** | `HausplanerApp.tsx:1371` |
| Eigenschaften-Panel (rechts, 268 px) | *„immer sichtbar"* | `HausplanerApp.tsx:1793` (Kommentar im Code) |

**Zwei der drei Flächen, die dein Auftrag unabhängig klappbar verlangt, haben heute überhaupt
keinen Zustand.** Die eine, die ihn hat, ist ein gutes Muster zum Abschauen — sie klappt auf 66 px
Icon-Breite statt auf 0, und `HausplanerStudio.tsx:68` klappt sie bei schmalem Fenster selbst zu.

### M6 · Escape existiert, aber ohne Rangfolge (P2)

```
dashboard/dialogFokus.ts:69   Escape schliesst GENAU EINEN Dialog
HausplanerApp.tsx:1011/1017   zwei eigene Escape-Zweige
GeschossFlaeche.tsx:71 · WerkzeugGruppenMenue.tsx:49   je ein eigener Escape-Handler
```

**Fünf unabhängige Escape-Behandlungen, kein Stapel.** *„Escape schließt das oberste Overlay"*
verlangt eine Rangfolge, und die gibt es nicht — heute entscheidet, wer den Ereignis-Empfänger
zuletzt gesetzt hat.

### M7 · Nutzerzustand liegt in `localStorage`, nicht am Nutzer (P1 für Punkt 4)

```
state/angeheftet.ts:25            localStorage — angeheftete Werkzeuge
state/arbeitsbereichSpeicher.ts   localStorage — aktiver Arbeitsbereich
```

Das Muster ist da und sauber gebaut (beide Module fangen ein fehlendes `localStorage` ab). **Aber
es speichert pro Browser, nicht pro Nutzer.** Dein Auftrag verlangt *„Zustand pro Nutzer und
Workspace speichern"* — das ist ein **Backend-Anschluss** und gehört damit zu **Tor 1**, wie schon
die Projektliste (AUF-78) und die Konfigurator-Persistenz (AUF-40 Teil B).

**Vorschlag zur Trennung:** Panel-Zustand zuerst in `localStorage` nach dem vorhandenen Muster —
das ist sofort baubar und liefert 90 % des Nutzens. Die Bindung an den Nutzer wird ein eigener
Posten, wenn du die Zulieferung entscheidest.

---

## 3. Punkt 5 (Notion-Muster) — was es schon gibt und was fehlt

| Muster aus deinem Auftrag | Bestand | Beleg |
|---|---|---|
| Such- und Command-Palette | **vorhanden** als Werkzeug `befehlspalette` | `tools/werkzeugPaket.ts:427`, `werkzeugVertrag.ts:1309` |
| Überlauf für selten Genutztes | **als Begriff vorhanden** | `tools/werkzeugZustand.ts:49` — *„im Überlauf — über die Befehlspalette"* |
| **Klare Kontextmenüs** | **existieren nicht** | `onContextMenu` / `onDoubleClick` in der ganzen Insel: **0 Treffer** |
| Kompakte Popover | teilweise (`WerkzeugGruppenMenue`) | eigener Escape-Handler, siehe M6 |

**Der Kontextmenü-Punkt ist der teuerste in deiner Liste** und deckt sich mit dem Papier vom
27.07. (`interaktionsmuster-inventar`): **0 Doppelklick, 0 Kontextmenü, 0 Rechtsklick** im ganzen
Planer. Er ist kein Aufräumen, sondern ein neues Bedienmuster — **er gehört nicht in diesen
Auftrag**, sonst wird aus „aufräumen" ein Neubau.

---

## 4. Kollisionen — §13 Einspurbetrieb

| Datei | Wer hat sie heute | Konflikt |
|---|---|---|
| `HausplanerStudio.tsx` (217 Z.) | **frei** — AUF-38 Scheibe 4 ist seit 00:45 **abgenommen** | **keiner** |
| `HausplanerApp.tsx` (2305 Z.) | **AUF-38 Scheibe 7** (offen) **und AUF-48** (Zerlegung, gesperrt) | **frontal** |
| `ConfigWizard.tsx` | **AUF-38 Scheibe 5 — läuft gerade** | keiner, wird nicht angefasst |
| `studio.blade.php` / `objekt.blade.php` | frei | keiner |

**Der Teil deines Auftrags, der `HausplanerApp.tsx` anfasst, ist genau der Teil, der an M4 hängt** —
die Canvas-Breite und die beiden Panelbreiten stehen dort. **Und AUF-48 sagt seit dem 25.07., dass
diese Datei zerlegt gehört, bevor mehrere Posten gleichzeitig hineingreifen.**

Daraus folgt der Schnitt: **was in der Blade und in `HausplanerStudio.tsx` liegt, ist sofort
baubar. Was in `HausplanerApp.tsx` liegt, wartet auf die Zerlegung** — oder zieht sie vor.

---

## 5. Vorschlag für den Schnitt — fünf Einheiten, jede unabhängig abnehmbar

> **Kein Blatt ist geschrieben.** Der Schnitt geht zuerst an dich, weil zwei Entscheidungen darin
> dir gehören (T1 und T5).

| Nr | Einheit | Spur | Dateien | hängt an |
|---|---|---|---|---|
| **T1** | **Die Hausplaner-Blades erben von der Ticket-App-Shell** — `@extends('admin.layouts.app')`, Blade-Kopfleiste entfällt, Ticket-Navigation ist wieder da | **A** | 2 Blades | **Yama** — Layout, Auth, Rollen, Routing |
| **T2** | **Doppelungen entfernen** — Blade-`hp-scratch` und Blade-`hp-title` weg, Insel-Anzeige bleibt einzige Wahrheit; `hp-experte-hinweis` und „Zur geführten Planung" in die kompakte Leiste | B | 2 Blades + `HausplanerStudio.tsx` | T1 |
| **T3** | **Eine kompakte Hausplaner-Topbar** — links Marke/Projekt/Geschoss, Mitte Modus, rechts Speicherstatus/Speichern/Undo/Redo/Überlauf | B | `HausplanerStudio.tsx` | T2 |
| **T4** | **Die Breitenrechnung fällt** — `innerWidth − 220 − 268` wird durch echtes Flex-Layout ersetzt, die geerbte Zusage aus Z1442 wird mitgezogen | **A** | `HausplanerApp.tsx` | **AUF-48** oder ausdrücklicher Vorzug |
| **T5** | **Beide Panels klappbar, als Overlay, Zustand in `localStorage`** — nach dem Muster von `navZu`; Escape-Stapel mit Rangfolge | **A** | `HausplanerApp.tsx` | T4 |

**T1 bis T3 sind sofort baubar und liefern schon den größten Teil des sichtbaren Aufräumens** —
drei Kopfleisten werden eine. **T4 und T5 liefern den Platz**, und beide liegen in der Datei, die
AUF-48 zerlegen soll.

### Die eine Frage, die ich dir vorlegen muss

**AUF-48 (Zerlegung von `HausplanerApp.tsx`) ist seit dem 25.07. gesperrt** mit der Begründung
*„erst wenn die Layout-Posten durch sind"*. Dein neuer Auftrag ist ein Layout-Posten, der die
Datei tief anfasst. **Es gibt zwei Wege, und beide sind vertretbar:**

**(a) AUF-48 vorziehen**, dann T4/T5 auf zerlegten Dateien bauen. Sauberer, aber die Zerlegung ist
der größte offene Posten des Projekts und schiebt den sichtbaren Nutzen nach hinten.

**(b) T4/T5 in der ungeteilten Datei bauen**, AUF-48 danach. Schneller sichtbar, aber wir greifen
ein sechstes Mal in eine 2305-Zeilen-Datei, und AUF-48 wird dadurch nicht kleiner.

**Meine Empfehlung ist (b) mit einer Auflage:** T4 wird so geschnitten, dass die Breitenrechnung
**an einer Stelle** landet (ein Modul, eine Wahrheit) statt verteilt zu bleiben — dann ist die
Arbeit für AUF-48 keine Nacharbeit, sondern Vorarbeit.

---

## 6. Was dieses Papier ausdrücklich nicht tut

- Es **schreibt kein Auftragsblatt.** Der Schnitt steht, die Blätter folgen, wenn du T1 und die
  Frage aus §5 entschieden hast.
- Es **nimmt keine Sichtprobe vorweg.** Punkt 7 deines Auftrags verlangt Screenshots vorher/nachher
  — die fährt der Evaluator headful, wie bei den AUF-38-Scheiben.
- Es **behandelt Kontextmenüs nicht.** Sie fehlen vollständig (0 Treffer) und sind ein eigenes
  Bedienmuster, kein Aufräumen.
