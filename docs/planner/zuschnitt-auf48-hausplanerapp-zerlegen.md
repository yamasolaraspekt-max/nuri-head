<!-- pfade-pruefen: historisch -->
<!-- Plan von vor dem Bau; die genannten Pfade beschreiben einen vergangenen Zustand (PB-031, 01.08.2026) -->

# AUF-48 — Zuschnitt: wie `HausplanerApp.tsx` zerlegt wird

> **⚠ DIES IST DER PLAN VON VORHER, NICHT DER BAU — Befund PB-031, vermerkt 01.08.2026.**
> **Sechs der hier genannten Dateinamen gibt es nicht** — `app/darstellung/`, `app/darstellung/icons.tsx`,
> `app/geometrie/helfer.ts`, `app/HausplanerApp.ableitungen.ts`, `app/HausplanerApp.bedienung.ts`,
> `app/flaechen/`. **Das ist kein Fehler des Baus, sondern seiner Aufzeichnung:** AUF-48 wurde in acht
> Scheiben gebaut und hat die Dateien anders geschnitten (`app/rahmen/`, `app/ableitungen.ts`, …).
> **Wer wissen will, wie es wirklich liegt, liest den Baum, nicht dieses Papier.** Der Zuschnitt bleibt
> als Beleg stehen, warum so entschieden wurde — er ist Geschichte, keine Landkarte.

*Planner, 30.07.2026, 06:35 CEST. **Kein Auftragsblatt, sondern der Schnitt davor.** Aus ihm
entstehen die Scheiben-Blätter — eines nach dem anderen, nicht alle auf einmal.*

> **Gemessen an `HEAD` (`37d02cc8`), nicht am Arbeitsbaum.** Der Generator baut gerade T3 in
> dieser Datei; ein Maß aus dem Arbeitsbaum wäre bis zum nächsten Speichern falsch.
> **Befehl:** `git show HEAD:resources/planner/hausplaner/app/HausplanerApp.tsx`

---

## 1. Was gemessen ist

```text
2308 Zeilen gesamt
  46 Hook-Aufrufe
2036 Zeilen in EINER Funktion (HausplanerApp, ab :272)
  78 offene Inline-Stellen (AUF-38 Scheibe 7)
   0 className=   ← die Datei kennt die Stilschicht nicht
  22 Testdateien lesen sie ein (R12)
```

**Die Datei hat oberhalb der Hauptfunktion bereits Struktur** — sie ist nicht durchgehend Chaos:

| Zeilen | Was dort steht | Zustand |
|---|---|---|
| 1–74 | Importe | — |
| 75–121 | IDs, Farben, Stilkonstanten | **drei tote Konstanten**, siehe §2 |
| 122–163 | `svgWrap`, `werkzeugIcon`, `opIcon` | rein, ohne Zustand |
| 164–206 | `uuid`, `istWand`, `istOeffnung`, `lotAufWand` | rein, ohne Zustand |
| 207–271 | `KontextOptionenLeiste` | eigene Komponente, schon getrennt |
| **272–2308** | **`HausplanerApp`** | **der eigentliche Posten** |

Und innerhalb der Hauptfunktion liegen die Blöcke bereits in einer Reihenfolge, die dem Schnitt
entgegenkommt:

```text
:299–374   Zustand            ~20 useState, 1 useRef
:375–572   abgeleitete Werte  ~18 useMemo / useCallback
:869–1003  Auswahl-Logik      waehleAn, Mehrfachauswahl
:1004–1180 Tasten und Effekte Escape, Delete, Undo, Palette
:1181–2308 JSX                drei Kopfzeilen, zwei Schienen, Bühne, Panels
```

---

## 2. Ein Fund, der beim Messen abfiel

**Drei Stilkonstanten haben nach T2 keine Verwendung mehr:**

```text
navGrp  :114   0 Verwendungen
navHub  :116   0 Verwendungen
navSub  :117   0 Verwendungen
navItem :115   1 Verwendung  (:1418, Fachplaner-Reiter der Schiene)
```

Sie stammen aus der Studio-eigenen Navigation, die T2 entfernt hat. **Sie sind kein Teil des
Zuschnitts, sondern eine Zeile Arbeit** — sie gehören in die erste Scheibe, damit niemand sie
mitschleppt und dabei für tragend hält.

*Das ist derselbe Fall wie die 15 verwaisten CSS-Regeln, die bei T2 mitgefallen sind: Reste, die
nur auffallen, wenn jemand nach ihnen sieht.*

---

## 3. Der Schnitt — vier Scheiben, in dieser Reihenfolge

**Das Prinzip: von außen nach innen, und niemals zwei Scheiben gleichzeitig** (§13, einspurig).
Jede Scheibe ist für sich abnehmbar und lässt die Datei lauffähig zurück.

### Scheibe 1 — das Reine zuerst (`app/darstellung/`)

Icons und Geometrie-Helfer. **Sie haben keinen Zustand, keine Hooks, keinen Bezug zur Komponente.**
Sie herauszunehmen ist eine Verschiebung, keine Umschreibung — und sie liefert den Beweis, dass die
22 geerbten Zusagen den Umzug überstehen, *bevor* etwas Riskantes bewegt wird.

- `svgWrap` · `werkzeugIcon` · `opIcon` → `app/darstellung/icons.tsx`
- `uuid` · `istWand` · `istOeffnung` · `lotAufWand` → `app/geometrie/helfer.ts`
- **Die drei toten `nav*`-Konstanten fallen hier.**

*Erwarteter Gewinn: rund 90 Zeilen. **Der eigentliche Ertrag ist nicht die Zahl, sondern das
Ergebnis der Probe:** halten die 22 Zusagen einen Umzug aus?*

### Scheibe 2 — die abgeleiteten Werte (`app/HausplanerApp.ableitungen.ts`)

Die rund 18 `useMemo`/`useCallback` aus :375–572. **Sie lesen Zustand und rechnen — sie ändern
nichts.** Als Hook `useAbleitungen(...)` mit benannter Rückgabe.

**Grenze, die den Unterschied macht:** *keine* dieser Rechnungen darf im Zuge des Umzugs verändert
werden. Der Nachweis ist eine Zusage je Rückgabewert, die den **alten** Wert festhält — nicht die
Behauptung, es sei dasselbe.

### Scheibe 3 — Tasten und Effekte (`app/HausplanerApp.bedienung.ts`)

:1004–1180. **Hier hängt die Escape-Rangfolge aus AUF-83-T5.** Deshalb kommt diese Scheibe
**nach** T5 — sonst zerlegen zwei Aufträge dieselben Zeilen.

> **Und hier sitzt ein offener P1-Befund, der bisher keinen Posten hat:**
> `Strg+W` zeichnet eine Wand — die Modifikatortasten schlagen durch. Ein Tastaturblock, der als
> eigene Datei mit eigener Zusage steht, macht das prüfbar. **Der Befund wird in dieser Scheibe
> behoben, nicht nebenbei.**

### Scheibe 4 — das JSX (`app/flaechen/`)

:1181–2308, das größte Stück. **Erst hier**, weil bis dahin alles darunter geordnet ist und die
Stilschicht greifen kann.

**Die Verbindung zu AUF-38 ist der Grund, warum diese Scheibe zuletzt kommt und nicht zuerst:** die
78 offenen Inline-Stellen stecken fast alle im JSX. **Wer das JSX zerlegt, löst Scheibe 7 von
AUF-38 mit auf** — und wer es umgekehrt versucht, stellt Stile in einer Datei um, die zwei Wochen
später nicht mehr existiert.

---

## 4. Was der Zuschnitt entsperrt

| Posten | Wartet worauf |
|---|---|
| **AUF-38 Scheibe 7** | 78 Inline-Stellen — fallen mit Scheibe 4 |
| **AUF-85** | Befehlspalette aus jedem Modus — braucht `oeffnePalette` außerhalb der Komponente (Scheibe 3) |
| **AUF-50** | die 110 Werkzeuge — die Werkzeugschicht baut auf zerlegten Dateien auf |
| **offener P1** | `Strg+W` zeichnet Wand (Scheibe 3) |
| **offener P1** | `auswahlDarstellung.griffe` wird berechnet und nie gezeichnet (Scheibe 4) |

**Vier Posten hängen an einer Datei.** Das ist die eigentliche Begründung für AUF-48 — nicht die
Zeilenzahl.

---

## 5. Was dieser Zuschnitt ausdrücklich NICHT entscheidet

- **Keine Umbenennung persistierter Werte.** `type: wall|window|door|ceiling`, `objectType`,
  `zoneType`, `routeType` bleiben (DAUERDIREKTIVE).
- **Kein neues Zustandssystem.** `uiState.ts` und der Modell-Store bleiben, wie sie sind. *Eine
  Zerlegung, die nebenbei die Zustandsführung ändert, ist zwei Aufträge in einem Commit.*
- **Keine Verhaltensänderung.** Jede Scheibe ist eine Verschiebung mit Nachweis. Fällt beim
  Verschieben ein Fehler auf: **melden, eigener Posten** — die Ausnahme ist der `Strg+W`-Befund,
  und er steht namentlich in Scheibe 3.

---

## 6. Sperre und Reihenfolge

```text
Scheibe 1   gesperrt bis AUF-83-T3 gebaut ist        (dieselbe Datei)
Scheibe 2   gesperrt bis Scheibe 1 abgenommen ist    (sie misst, ob Umzüge tragen)
Scheibe 3   gesperrt bis AUF-83-T5 gebaut ist        (Escape-Rangfolge)
Scheibe 4   gesperrt bis Scheibe 2 und 3 stehen
```

**Scheibe 1 bekommt als Nächstes ein Blatt** — sobald T3 gebaut ist und der Prüfstand des
Evaluators steht. *Vorher wäre es das dritte Blatt in derselben Datei zur selben Zeit, und genau
das verbietet §13.*

---

## 7. Korrektur, 30.07. 09:05 — Schnittkanten als ANKER, nicht als Zeilennummern

**Auf `PB-007` des Prüfers, nachgemessen und bestätigt.** Sein Befund ist genau richtig eingeordnet:

```text
am COMMIT stimmt alles:   git show HEAD:...HausplanerApp.tsx | wc -l   →  2308
im ARBEITSBAUM, Stunden spaeter:
  Gesamtzeilen   2370  (+62)
  Hauptfunktion  :276  (+4)  ← die Kante des groessten Postens ist gewandert
```

**Das Papier war beim Schreiben richtig und ist es beim Lesen nicht mehr.** *Eine Zeilennummer ist
kein Anker, sie ist ein Zeitstempel in anderer Schreibweise.*

> ### Die Schnittkanten gelten ab sofort über NAMEN, nicht über Zeilen
>
> | Scheibe | Anker |
> |---|---|
> | **1** Das Reine | `function svgWrap` · `function werkzeugIcon` · `function opIcon` · `const uuid` · `function istWand` · `function istOeffnung` · `function lotAufWand` — **und die drei toten `navGrp`/`navHub`/`navSub`** |
> | **2** Abgeleitete Werte | alles zwischen `const setWerkzeug` und `const bandVon` |
> | **3** Tasten und Effekte | der `useEffect` mit `function taste` bis einschließlich `Delete`/`Backspace` |
> | **4** Das JSX | ab `return (` der Hauptfunktion bis zum Dateiende |
>
> **Die Zahlen aus §1 bleiben stehen — als `measurement` mit Commit, nicht als Kante.**
> *Sie sagen, wie groß der Posten war, nicht wo er anfängt.*

**Und der Prüfer hat die drei toten Konstanten unabhängig bestätigt:** `navGrp`, `navHub`, `navSub`
je **ein** Vorkommen (nur die Definition), `navItem` **zwei** (Definition + eine Verwendung).
*Zwei Messungen, dasselbe Ergebnis, verschiedene Werkzeuge.*
