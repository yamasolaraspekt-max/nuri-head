# Fahrplan Frontend-Layout Hausplaner — gemessene Bestandsaufnahme + Reihenfolge

**Rolle:** Planner · **Stand:** 25.07.2026 · **Gemessen an:** `auto/hausplaner-integration @ e5ebc42`
**Anlass:** Yama, 25.07.: *„wir haben ein grosse frontend layout aus führen wieviel davon ist schon
gemacht, hast du mal geschaut, was wir gestern alles zusammen gestellt haben und daraus ein
fahrplan erstellt haben"*

> **Offenlegung zuerst:** Nein — bis zu dieser Frage lag **kein** Fahrplan aus dem Material von
> gestern vor. Ich habe seit 09:22 die Auftragstafel im Takt beobachtet und gewartet, statt das
> Layout zu inventarisieren. Der Ledger enthält die Wellen-Berichte, die Bestandsprüfung enthält
> das Inventar — **zusammengeführt zu einem Layout-Fahrplan war beides nicht.** Dieses Dokument
> holt das nach; die Zahlen unten sind selbst am Code gemessen, nicht aus Berichten übernommen.

**Yamas stehende Layout-Regel, unter der dieser Fahrplan steht:**
*„wir machen erst layout fertig auch wenn die funktion nicht programmiert sind bleiben ohne
funktion da."* — Also: **Fläche vor Funktion.** Ein Schritt gilt als Layout-fertig, wenn er seine
Fläche hat, auch wenn dahinter noch nichts rechnet.

---

## 1. Was „das große Frontend-Layout" konkret ist (gemessen)

Die React-Insel umfasst **3.343 Zeilen in `resources/planner/hausplaner/app/`**, verteilt auf
17 Dateien. Sie besteht aus fünf Ebenen — das ist das Layout:

| Ebene | Datei | Zeilen | was sie trägt |
|---|---|---|---|
| 1 · Studio-Rahmen | `HausplanerStudio.tsx` | 177 | Kopfzeile, Navigation, Bühne, Toast, Overlay |
| 2 · Start/Launcher | `StartView.tsx` | 112 | Zuletzt · 3 Projekt-Karten · 5 Fach-Hub-Karten |
| 3 · Geführte Planung | `GuidedView.tsx` | 145 | 11-Schritt-Stepper + Fokus-Schrittkarte + Seitenpanel |
| 4 · Konfigurator | `ConfigWizard.tsx` | 230 | 4 Arten (Fenster · Tür · Treppe · Heizkörper) |
| 5 · Expertenmodus | `HausplanerApp.tsx` | 1.431 | Werkzeugleisten, 2D-Konva + 3D-three, Eigenschaften-Panel, Statusleiste |
| Daten/Bausteine | `studioDaten.ts` · `studioUi.tsx` · `FaehigkeitenNavi.tsx` · `tools/*` | 1.248 | Tokens `T`, `FACH`, `PROJ`, `ZULETZT`, `STEPS`, Werkzeug-Registry + Präsentationsschicht |

---

## 2. Wieviel davon ist gemacht — je Ebene, mit Beleg

Der ehrliche Schnitt läuft nicht durch „fertig/unfertig", sondern durch **Fläche gebaut** vs.
**Fläche mit echten Daten gefüllt**. Nach Yamas Regel zählt zuerst die erste Spalte.

| Ebene | Fläche gebaut | modellgetrieben (echte Daten) | Beleg |
|---|---|---|---|
| 1 · Studio-Rahmen | **vollständig** | **teilweise** | Speicherstatus + Revisionsnummer kommen echt aus `useHausplanerStore` (`HausplanerStudio.tsx:24-25,34-40`); Navigation ist eingebaut inkl. Auto-Einklappen unter 900 px (Z. 45-52); Tastatur-Fokusring global gesetzt (Z. 89) |
| 2 · Start/Launcher | **vollständig** | **nein** | `ZULETZT` sind drei Demo-Einträge („EFH Mustermann", „Fenster-Angebot Hahn") in `studioDaten.ts:69`; alle drei Projekt-Karten springen auf denselben Schritt (`StartView.tsx:92-94`) |
| 3 · Geführte Planung | **vollständig** (alle 11 Schritte haben Fläche) | **nein** | `STEPS` trägt im Code selbst den Vermerk *„Präsentativ — echte Zustands-Ableitung folgt aus dem Modell"* (`studioDaten.ts:88`); Status/Checks/Aufgaben sind hartkodiert. Echt sind nur die vier Zähler Geschosse/Fenster/Tür/Treppe (`HausplanerStudio.tsx:26-33`) |
| 4 · Konfigurator | **vollständig** | **3 von 4** | Heizkörper (`ConfigWizard.tsx:163`), Treppe (Z. 184) und Öffnung auf gewählter Wand (Z. 205) schreiben echt via `executeCommand ADD_NODE`. Fenster **ohne** gewählte Wand endet als JSON-Download statt Persistenz (Z. 223) |
| 5 · Expertenmodus | **vollständig** | **ja** | 2D und 3D lesen denselben Store (`HausplanerApp.tsx:790`); Eigenschaften-Panel für Dach/Wand/Öffnung/Treppe/Objekt je als `UPDATE_*`-Command (Z. 197-245); Sicht/Sperre je Node (Z. 1101) |

**Kurzfassung für die Frage „wieviel ist schon gemacht":**
**Das Layout-Gerüst steht zu fünf Fünfteln — alle fünf Ebenen sind gebaut und bedienbar.**
Was fehlt, ist überwiegend **Füllung und Verdrahtung**, nicht Fläche. Die zwei echten Layout-Lücken
sind (a) die **20 Fachplaner-Untermodule**, die heute nur einen Toast „Konfigurator folgt" zeigen
(`HausplanerStudio.tsx:70`), und (b) das **fehlende Panel-Muster** für die 13 fertigen Fach-Engines.

### Der eine Punkt, der als „gebaut" gemeldet ist, aber im Layout noch nicht ankommt

Die Werkzeug-**Präsentationsschicht** aus Welle A1 ist fertig und kuratiert alle **63 Werkzeuge**
in vier Zonen (7 fix · 2 Kontext · 15 weitere · 39 versteckt). **Die Werkzeugleiste liest sie
aber noch nicht.** Gemessen: `zoneTools` wird im gesamten `app/` genau **einmal** aufgerufen,
in `tools/faehigkeiten.ts:96` — **nicht** in `HausplanerApp.tsx`. Das ist Welle A2, und A2 ist
auf der Auftragstafel `GESPERRT`, bis AUF-1 ein Votum hat. **Der kritische Pfad des Layouts läuft
damit heute über den Evaluator, nicht über den Generator.**

### Was aus der Bestandsprüfung von gestern (`docs/bestandspruefung-hausplaner.md`, 24.07. 20:50) hierher gehört

Die Prüfung hält fest: **Modell, Render und Persistenz sind reif und abgenommen** — 7 Node-Typen,
20 undo-fähige Commands, 8 Dachformen, Gehrung, Decke-mit-Treppenauge, 409-Konfliktschutz. Ihr
Kern-Befund lautet wörtlich: der Nachholbedarf ist **Verdrahtung, nicht Neubau**. Konkret warten
**13 fertige, getestete Fach-Engines** (FBH · Heizkörper EN 442 · Heizkreis-Verteiler · Abwasser
DIN 1986-100 · Küchendreieck DIN 18022 · PV-Schnellbelegung · U-Wert ISO 6946 · Fenster Uw/RC/Preis ·
Sparren EC 5 · Treppen DIN 18065 · Holz-Mengen · Holz-Bauteile · Schifter-Liste) auf **je ein Panel**.
Das ist die größte Einzelposition im Layout — und sie ist **13× dieselbe Fläche**, sobald das Muster
einmal steht.

---

## 3. Der Fahrplan — Layout zuerst, in dieser Reihenfolge

Begründung der Reihenfolge: erst was **blockiert** (L1), dann was **vervielfältigbar** ist (L2/L3),
dann die **leeren Flächen** (L4), dann Funktion (L5–L7). Nichts hiervon ist begonnen, bevor Yama
den Fahrplan fachlich freigibt (Tor 1).

| Nr | Schritt | warum hier | Vorbedingung | Rolle |
|---|---|---|---|---|
| **L1** | **Werkzeugleiste liest die Präsentationsschicht** (Welle A2): 7 Fix- + 2 Kontext-Werkzeuge in die Topbar, 15 „weitere" in den Überlauf, 39 versteckt | die Kuratierung ist gebaut und wirkt nicht; jede spätere Layout-Arbeit an der Leiste würde doppelt | **AUF-1-Votum** (Evaluator) | Generator |
| **L2** | **Panel-Muster für Fach-Engines** an *einer* risikoarmen Engine (U-Wert oder Sparren): Eingang → Rechnen → Ausgang als Vorlage | eine Fläche, die 13× wiederverwendet wird — der höchste Hebel im ganzen Layout | L1 (gleiche Leiste ruft die Panels) | Planner-Konzept → Generator |
| **L3** | **Die 13 Engine-Panels nach dem Muster ausrollen**, gruppenweise (dach-zimmerei zuerst: 5 Engines mit Render-Bezug) | reine Wiederholung, sobald L2 steht; nach Yamas Regel dürfen sie zunächst ohne Rechenanschluss stehen | L2 abgenommen | Generator |
| **L4** | **Die 20 Fachplaner-Untermodule bekommen je eine Fläche** statt Toast „folgt" (Kopf, Struktur, Leerzustand mit „was hier entsteht") | heute führen 20 Klicks ins Nichts — genau die „toten Elemente", die der UX-Audit als kognitive Last benennt | keine | Generator |
| **L5** | **Wizard-Schritt-Status aus dem Modell ableiten** — `STEPS` verliert seine hartkodierten Status/Checks | erst wenn die Flächen stehen, lohnt die Ableitung; Guardrail: an vorhandene Services andocken, **kein zweiter Snapshot-/Hash-/Projektions-Mechanismus** | L1–L4 | Planner-Konzept → Generator |
| **L6** | **Start/Zuletzt an echte Projekte** (Demo-Daten `ZULETZT` raus) + **ConfiguratorPackage serverseitig persistieren** statt JSON-Download | braucht Backend-Anschluss, blockiert kein Layout | L5 | Generator |
| **L7** | **Abnahme-Runde Layout**: A11y-Kontrast der Token-Paare rechnerisch, 3 Pflicht-Viewports (1440/1024/375), 2D/3D-Selektions-Sync, Aktivierungsgrund als Tooltip | die UI-Bauordnung §2/§3 verlangt Messung und Screenshots, nicht Augenmaß | L1–L6 | Evaluator |

**Nicht im Fahrplan** (bewusst): keine KI-Grundriss-Erzeugung, keine neuen Fach-Rechen-Wahrheiten
(die 13 Engines werden wiederverwendet, nicht nachgebaut), kein Umbau der Blade-CRM-Oberfläche —
der UX-Audit `docs/ux-frontend-audit.md` betrifft die **Blade-Welt**, nicht diese React-Insel, und
gehört in einen eigenen Strang.

---

## 4. Was Yama entscheiden muss, bevor L2 beginnt

1. **Welche Engine wird das Panel-Muster?** Vorschlag: **U-Wert** (reine Funktion, keine Norm-Falle,
   sichtbares Ergebnis) — Alternative Sparren (mehr Bau-Nähe, aber EC-5-Fragen im Schlepptau).
2. **Wie tief soll eine leere Fläche in L4 sein?** Nur Kopf + Hinweis, oder schon die Feldstruktur
   des späteren Panels (mehr Arbeit jetzt, weniger Umbau später)?
3. **Bleibt die Reihenfolge L1 vor L2?** L1 hängt am Evaluator (AUF-1). Wenn das länger dauert,
   könnte L4 (völlig unabhängig) vorgezogen werden, damit niemand wartet.

Diese drei Punkte sind **Willensfragen** und werden nicht in Yamas Vertretung entschieden.
