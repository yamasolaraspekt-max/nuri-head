# ⇒ PLANNER — UX- und IA-Befund Expertenmodus, am echten Rendern gemessen

**25.07.2026** · Linsen: `ux-design` (wie es aussieht) + `info-architektur` (was gezeigt wird) ·
Rahmen: `governance-zyklus` · **Heimat-App:** ticket
**Grundlage:** Browser-Sichtprobe `http://ticket.test/admin/hausplaner/objekt/203`, echter Build.
Nicht am Vorsatz gemessen, sondern am Bild — wie es die Design-Abnahme verlangt.

---

## B1 — Die linke Spalte macht drei Jobs. Das ist der strukturelle Befund.

**Gemessen:** In 220 px übereinander, in **einer** gemeinsam scrollenden Spalte:
**Werkzeuge** (9) → **Fähigkeiten** (37 in 9 Gruppen) → **Projekt** (Wände 7 · Öffnungen 6 · Dächer 1).
Ich musste **zweimal je 10 Scroll-Ticks** fahren, um den Projektbrowser überhaupt zu sehen.

**Regelverstoß, wörtlich:** `info-architektur` — *„jede Fläche hat genau einen Hauptjob. Sobald eine
Fläche zwei Jobs macht, überlädt sie."* Hier sind es drei: **Arbeit** (Werkzeug wählen),
**Navigation** (Fähigkeit ansteuern), **Struktur** (Objekt im Modell finden). Zusätzlich:
*„Sidebar = Navigation, keine Daten"* — der Projektbrowser ist Daten.

**Entscheidung:** Die drei Jobs werden getrennt, **ohne** neue Fläche zu erfinden. Die Spalte bekommt
**drei Abschnitte mit je eigener Scroll-Höhe** (Werkzeuge fix oben, Fähigkeiten und Projekt als
eigene, unabhängig scrollende Blöcke mit Kopf und Zähler) — statt einer 1.500-px-Wurst.
Der Projektbrowser ist damit **ohne Scrollen erreichbar**, was der ganze Sinn von v2.3 war.
**Nicht** entschieden und ausdrücklich offen: ob Projekt später eine eigene Reiter-Ebene bekommt —
das ist v3 (`fahrplan-dashboard-versionen.md` §7/§20), nicht dieser Posten.

---

## B2 — 15 falsche Versprechen in der Fähigkeiten-Navi

**Gemessen:** Die Navi zeigt „Drehen · Skalieren · Freie Transformation · Links/Rechts/Oben
ausrichten · Vertikal zentrieren · Horizontal/Vertikal verteilen · Hand · Zoom · Messen · Ebenen"
mit Badge `in Entwicklung`. Das sind exakt die **15 Werkzeuge der Zone `weitere`** — die einzige
Zone mit einem Verbraucher (`faehigkeiten.ts:96`).

**So-what-Test (`info-architektur`):** *„Ändert das, was der Nutzer als Nächstes tut?"* — **Nein.**
Und schlimmer als „nein": Die Icon-Inventur hat belegt, dass 47 der 54 Katalogeinträge **DTP-Erbe**
aus einem Layout-Programm sind. „Links ausrichten" und „Hand" kommen im Hausplaner **nie**.
`in Entwicklung` ist hier keine ehrliche Fläche, sondern ein **falsches Versprechen** — und
verletzt damit genau die v1-Regel, die es einhalten wollte.

**Entscheidung — der ausführbare Teil, sofort:** Die 15 DTP-Regeln wandern in
`app/tools/toolPresentation.ts` von Zone `weitere` auf **`versteckt`**. Reine Datenänderung, kein
neuer Mechanismus, keine neue Wahrheit. Danach zeigt die Navi **nur noch echte Fach-Fähigkeiten**.
**Der Willensteil bleibt offen:** womit `weitere` künftig belegt wird — Kandidat sind die
Fach-Werkzeuge aus dem 110er-Paket (AUF-21/I2). Das entscheidet Yama, nicht dieser Posten.

**Achtung, Reihenfolge:** `zoneTools('weitere')` ist heute der einzige Verbraucher. Wird die Zone
leer, muss die Navi ihren **Leerzustand** ehrlich zeigen — nicht verschwinden.

---

## B3 — Das rechte Panel wird gekappt, der vierte Reiter ist unsichtbar

**Gemessen bei 1375 px:** Nur **drei** Reiter im Bild (Allgemein · Beziehungen · Prüfungen).
**„Historie" fehlt**, obwohl `PANEL_TABS` vier Einträge hat und sechs Tests das belegen.
Ebenfalls gekappt: die Schaltfläche „↕ Oben/Unten" und der Hinweistext, der mitten im Wort abbricht
(„…brauch", „ein eigener Po").

**Das ist der teuerste Befund des Tages**, weil er zeigt, wozu die Sichtprobe da ist: **Kriterium K3
ist im Test grün und auf dem Schirm nicht erfüllt.** Ein Reiter, den man nicht sehen kann, existiert
für den Nutzer nicht.

**Regelverstoß:** `ux-design` Rubrik 8 (Kontext/Responsive) und Rubrik 1 (Scanbarkeit).

**Entscheidung:** Die Reiterzeile **bricht um oder scrollt horizontal, sie kappt nicht.** Der
Panel-Inhalt bricht um statt abzuschneiden. **Kein `overflow: hidden` auf einer Fläche, die Text
trägt.** Zusätzlich verbindlich für jede weitere Fläche: **1440 · 1024 · 375 px** sind die drei
Pflicht-Viewports (`fahrplan-frontend-layout-hausplaner.md` L7, UI-Bauordnung §2/§3).

---

## B4 — Labels, die man nicht lesen kann, sind keine Labels

**Gemessen:** „Horizont…", „Vertikal z…", „Sparren-…", „Holz-Me…", „Schifter-…" in der 220-px-Rail.

**Entscheidung:** Ein gekapptes Label trägt **zwingend** ein `title`, sonst ist es informationslos.
Wo die Kappung systematisch ist (Fähigkeiten-Gruppen), wird **umgebrochen statt gekappt** — zwei
Zeilen kosten weniger als ein unlesbarer Eintrag. Nach B2 fallen 15 der langen Namen ohnehin weg.

---

## Was die Linsen ausdrücklich **nicht** beanstanden

- **Die drei leeren Panel-Reiter sind in Ordnung.** Sie tragen Farbe **und** Text **und** Punkt, sagen
  im Futur konkret, was kommt, und der Standard-Reiter ist der arbeitende. Yamas stehende Regel
  („erst Layout fertig, auch ohne Funktion") deckt sie, die v1-Ehrlichkeitsregel ist eingehalten.
  **Der Unterschied zu B2:** diese drei Reiter **kommen wirklich** — die 15 DTP-Werkzeuge nicht.
- **Die Kontrastwerte.** Vom Evaluator gegen jeden realen Untergrund nachgerechnet, alle acht neuen
  Textflächen bestehen AA. Engster Wert 4,54:1 — bestanden, ohne Reserve.
- **Marke als Akzent.** Grün ist Aktion und Marken-Moment, Status nutzt `T.ok`, nicht `T.brand`.
  CI-Regel eingehalten.

---

## Umsetzung — Spuren getrennt

| Befund | Spur | warum | Posten |
|---|---|---|---|
| **B3** Panel-Kappung | **B** | nur Umbruch/Overflow, kein Datenpfad, keine Logik | AUF-26 |
| **B4** Label-Kappung | **B** | dito, plus `title`-Attribut | AUF-26 |
| **B1** drei Jobs in einer Spalte | **A** | ändert die Struktur der Hauptarbeitsfläche | AUF-27 |
| **B2** 15 Regeln `weitere` → `versteckt` | **A** | ändert die Kuratierung, die A2 gerade produktiv nimmt | AUF-28 |

**B2 und B1 berühren `HausplanerApp.tsx` bzw. `toolPresentation.ts` — dort arbeitet der native
Strang an A2.** Beide Posten starten erst, wenn A2 abgenommen ist. **B3/B4 (Spur B) können sofort,
sobald A2 committet ist** — sie fassen nur Darstellung an.
