# ⇒ PLANNER — Layout-Untersuchung, alle fünf Ebenen (Sichtprobe + Code-Beleg)

**Vom:** Planner · **25.07.2026, 22:30** · **Auftrag:** Yama — *„meinst du wir sollen das Layout einmal
untersuchen … ein paar Icons die sichtbar sind aber sind inaktiv, ausserdem mit den Geschossen das
sieht nicht gut aus, mit Wizard haben wir auch noch nicht gemacht"*.

**Verfahren:** angemeldete Sitzung, eigener Tab (der Evaluator-Tab blieb unberührt),
`/admin/hausplaner/studio`. Breiten 1440 / 1024 / 375 px, die beiden kleineren per iframe fester
Breite (Verfahren des Evaluators, löst das Chrome-Resize-Limit). **Jeder Befund ist im Code belegt.**

> **Warum die Kopplung:** Eine Sichtprobe beweist, **dass** etwas nicht stimmt — nie **warum**. Ich habe
> heute schon einmal aus einem Augenschein auf die Ursache geschlossen (der ~1375-px-Defekt, den ich
> AUF-26 zuschrieb und der zu AUF-34 gehörte). Deshalb steht unter jedem Befund eine Messung, und wo
> die Messung meinem Eindruck widerspricht, gewinnt die Messung — siehe B9.

---

## Ebene 1 · Studio-Rahmen

### B1 — Die Geschosszeile trägt vier verschiedene Aufgaben und nennt das Geschoss zweimal

**Gemessen** (`getBoundingClientRect`, alles in derselben Zeile wie der Geschoss-Wähler):
**13 Bedienelemente**, vier voneinander unabhängige Aufgaben:

```
↰ ↱            Rückgängig / Wiederholen        ← gehört nicht zum Geschoss
◀ [Select] ▶   Geschoss-Navigation (111 px)
[Erdgeschoss]  Textfeld — DERSELBE Wert wie der Select, direkt daneben
+ ⧉ −          Geschoss anlegen / duplizieren / löschen
2D Split 3D    Ansichtsmodus                   ← gehört nicht zum Geschoss
Speichern      
```

**Ursache:** `HausplanerApp.tsx:967-1045` — eine gewachsene Flex-Zeile ohne Gruppierung.

**Warum das mehr ist als Kosmetik:** **Das Geschoss ist das Tor.** Ein angelegtes Geschoss entsperrt
auf einen Schlag **34 von 110 Werkzeugen** (siehe B3). Die folgenreichste Handlung der ganzen
Oberfläche steckt in einem 111-px-Dropdown zwischen „Rückgängig" und „Speichern" — und daneben steht
derselbe Name noch einmal in einem Eingabefeld. Ein Nutzer sieht nicht, dass hier etwas Wichtiges ist.

**Was fehlt zusätzlich:** Höhenlage im Wähler (`elevation` wird geführt, aber nicht gezeigt), kein
Bild vom Stapel, Namen werden automatisch als „Geschoss 3" vergeben.

### B2 — Zwei Knöpfe versprechen dauerhaft etwas, das nicht kommt

**Gemessen:** 62 Bedienelemente sichtbar, **16 gesperrt** (leerer Plan). Die meisten zu Recht
(Rückgängig ohne Verlauf, „Geschoss darunter" beim einzigen Geschoss). **Zwei sind anders** — ihr
`title` sagt es selbst:

```
"Auswahl um 90° drehen (geplant)"
"Als PDF-Planblatt exportieren (geplant)"
```

Das ist dieselbe Sorte falsches Versprechen, die der Katalog-Tausch (I2) aus der Navigation entfernt
hat — nur ist sie hier in der Icon-Zeile stehengeblieben.

**Dazu:** „Grundriss links/rechts spiegeln" und „oben/unten spiegeln" stehen als zwei große Knöpfe im
Eigenschaften-Panel — bei **0 Räumen und 0.00 m²**. Einen leeren Grundriss zu spiegeln ist keine
Handlung. Sie sind zwar gesperrt, nehmen aber den prominentesten Platz im Panel ein.

---

## Ebene 2 · Start / Launcher

### B4 — Drei erfundene Projekte, die echt aussehen — und zweimal auf demselben Schirm

**Gesehen und gemessen:**
```
EFH Mustermann          Rev. 42 · Schritt 2/11
Fenster-Angebot Hahn    ConfiguratorPackage · gestern
Sanierung Musterstr. 5  Rev. 12 · vor 3 Tagen
```
Mit Revisionsnummern, Schrittstand und Zeitangabe — von einem echten Projekt nicht zu unterscheiden.
**Beleg:** `studioDaten.ts:69`, Konstante `ZULETZT`.

**Zusätzlich, erst in der Sichtprobe sichtbar:** dieselben drei Einträge „Sanierungsplan · Hausplaner ·
Weiterarbeiten" stehen **gleichzeitig** links in der Navigation **und** als drei große Karten in der
Mitte. Zwei Wege, ein Ziel — und alle drei Karten rufen `onGuided(1)` (`StartView.tsx:92-94`),
auch „Weiterarbeiten", das kein Bestandsprojekt öffnet, sondern bei Schritt 1 beginnt.

→ **AUF-40 Teil A** deckt das ab.

### B5 — Bei 375 px läuft die Startseite 283 px über

**Gemessen im iframe:** `documentElement.scrollWidth = 658` bei `innerWidth = 375`.
**43 Elemente** ragen über den rechten Rand. Bei **1024 px sauber** (`scrollWidth 1009`).

Damit ist einer der drei Pflicht-Viewports aus L7 hart verletzt. **Neuer Posten.**

---

## Ebene 3 · Geführte Planung — der schwerste Befund

### B6 — Der Schirm widerspricht sich selbst, nebeneinander, in einer Zeile Abstand

**Auf einem einzigen Bildschirm gemessen:**

| erfunden (hartkodiert) | echt (aus dem Modell) |
|---|---|
| ✓ „Datei geladen (PDF)" | |
| ✓ „Maßstab erkannt · 1:50" | |
| ! „4 Prüfstellen offen" | |
| „1 Wand unsicher erkannt." | |
| „5 Räume erkannt." | **„Im Modell: 1 Geschoss · 0 Fenster · 0 Türen · 0 Treppen"** |

Es wurde **keine Datei geladen**. Es gibt **keine 5 Räume** — der Expertenmodus zeigt für dasselbe
Geschoss „Räume: 0 · 0.00 m²". Und **Schritt 1 „Projektgrundlagen" steht auf grün ✓**, ohne dass
jemals etwas eingegeben wurde.

**Dazu die Grundriss-Vorschau darunter:** ein SVG mit vier Kindern und den Texten „Wohnen" und
„Küche" — ein gezeichnetes Beispielhaus, das mit dem Modell nichts zu tun hat.

**Beleg:** `studioDaten.ts:88` sagt es selbst: *„Präsentativ — echte Zustands-Ableitung folgt aus dem
Modell."*

**Warum das der schwerste Befund ist:** Die eine ehrliche Zahl steht **direkt neben** den erfundenen.
Ein Nutzer, der „0 Fenster" liest und zwei Zentimeter weiter „5 Räume erkannt", lernt nicht, dass ein
Teil Platzhalter ist — er lernt, dass die Anzeige nicht stimmt. Das beschädigt jede andere Zahl im
Programm, auch die richtigen.

→ **AUF-39** deckt das ab, Kernkriterium ist genau dieser Fall: *leeres Dokument ⇒ kein grüner Schritt*.

---

## Ebene 4 · Konfigurator

### B7 — Der beste Bildschirm macht das leerste Versprechen

Der Fenster-Konfigurator ist die stärkste Fläche im Programm: 24 Bauarten mit echten Premium-Icons,
Live-Vorschau mit Maßangabe (1010 × 1360 mm), fünf saubere Schritte, autark bedienbar ohne Gebäude.

In der Fußzeile steht: **„Status: Entwurf · als ConfiguratorPackage speicherbar."**

**Gemessen:** `grep -rl 'ConfiguratorPackage' app/ database/migrations/` = **leer**. Serverseitig
existiert dafür nichts. „Speicherbar" bedeutet `a.download = konfigurator-fenster-….json`
(`ConfigWizard.tsx:220`) — eine Datei im Download-Ordner.

Ein Nutzer konfiguriert zehn Minuten und bekommt eine JSON-Datei, die er nirgends wieder öffnen kann.

→ **AUF-40 Teil B** deckt das ab (bei Yama, weil Migration + Route).

---

## Ebene 5 · Expertenmodus

### B8 — Das Standardwerkzeug meldet sich beim Start als unfertig

Beim Öffnen ist „Markieren" aktiv. Die Kontext-Leiste sagt:

> **Markieren** — Für dieses Werkzeug sind noch keine Optionen hinterlegt. ● **in Entwicklung**

Das ist der erste Satz, den ein Nutzer im Expertenmodus liest. Die Aussage ist ehrlich (v2.1 hat den
Platzhalter bewusst so gebaut), aber sie steht am denkbar falschen Ort: **das Standardwerkzeug ist
nicht in Entwicklung — es hat schlicht keine Optionen**, und das ist normal. Der Platzhalter behandelt
„braucht keine Optionen" wie „ist noch nicht fertig".

### B9 — Was ich zu sehen glaubte, und was die Messung sagt

Im Bildschirmfoto sah es aus, als werde der Knopf „↕ Oben/Unten" am rechten Rand abgeschnitten.
**Gemessen:** `right = 1427` bei `innerWidth = 1440` — **nicht abgeschnitten.**

Real ist nur: das Eigenschaften-Panel endet bei `1441`, ragt also **1 px** über den Rand
(Breite fest 268 px). Ohne Folge, kein Bildlauf (`scrollWidth == clientWidth`).

**Ich führe den Befund trotzdem auf**, weil er die Regel belegt: der Augenschein hätte hier einen
Defekt gemeldet, den es nicht gibt.

---

## Was daraus folgt

**Abgedeckt durch bereits geschriebene Aufträge — nichts Neues nötig:**

| Befund | Posten |
|---|---|
| B4 · erfundene Projekte, drei Karten ein Ziel | AUF-40 Teil A |
| B6 · Wizard widerspricht sich | **AUF-39** |
| B7 · „speicherbar" ohne Persistenz | AUF-40 Teil B (bei Yama) |

**Neu — vier Posten:**

| Nr | Befund | warum eigener Posten |
|---|---|---|
| **AUF-43** | B1 · Geschoss-Bedienung | Der größte Einzelhebel: das Tor zu 34 Werkzeugen ist ein 111-px-Dropdown |
| **AUF-44** | B2 · zwei „(geplant)"-Knöpfe, Spiegeln im leeren Plan | klein, mechanisch, sofort machbar |
| **AUF-45** | B3 + B8 · der erste Schritt ist nirgends sichtbar | 71 % grau beim Öffnen ist korrekt und unbrauchbar |
| **AUF-46** | B5 · 375 px läuft 283 px über | einer der drei Pflicht-Viewports aus L7 |

**Und der Satz, der alles zusammenhält:** Die Oberfläche ist **nicht kaputt** — sie ist an drei
Stellen **unehrlich** (B2, B4, B6, B7) und an zwei Stellen **stumm** (B1, B3). Das Unehrliche ist
beauftragt. Das Stumme ist neu und ist das, was Yama beim Hinsehen gestört hat: Sie sagt nirgends,
wo man anfängt.
