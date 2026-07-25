# ⇒ GENERATOR — AUFTRAG AUF-27: Die linke Spalte bekommt Reiter

**Vorher gelesen:** HEAD `4f6c1e6` · `git log -3` · Tafelzeile AUF-27 ·
`app/HausplanerApp.tsx:1020` (`FaehigkeitenNavi`) und `:1024` (Kommentar „DRITTER Abschnitt derselben
220-px-Schiene") · `app/tools/faehigkeiten.ts` (22 Einträge) · `app/dashboard/fachFlaechen.ts` (19)

**Angelegt:** 25.07.2026 · **Vom:** Planner · **Tafel:** AUF-27 · **Spur:** **A**
**Vorbedingung erfüllt:** A2 (`acdb987`) und I4 (`4932b36`) sind abgenommen.

**Anlass, wörtlich:** Yama, 25.07.: *„warum fähigkeiten stehen immer noch an den sidebar das ist doch
kein layout"*.

---

## Ziel & Entscheidung

**Gemessen:** Die 220-px-Schiene trägt **drei** Blöcke untereinander in **einer** gemeinsamen
Scroll-Höhe — Werkzeuge (9), Fähigkeiten (22) und Projektbrowser. Der Code sagt es selbst:
`HausplanerApp.tsx:1024` nennt den Projektbrowser den *„DRITTER Abschnitt derselben 220-px-Schiene"*.
Folge in der Sichtprobe: der Projektbrowser war erst nach rund **20 Scroll-Ticks** sichtbar.

**Regelverstoß:** `info-architektur` — *„jede Fläche hat genau einen Hauptjob"* und
*„Sidebar = Navigation, keine Daten"*. Hier sind es drei Jobs: Werkzeug wählen · Fähigkeit ansteuern ·
Objekt im Modell finden.

### Entscheidung: **Reiter statt Stapel.**

Die linke Schiene bekommt **drei Reiter** — `Werkzeuge · Projekt · Fähigkeiten` — von denen immer
**genau einer** sichtbar ist. Jeder Reiter hat seine **eigene** Scroll-Höhe.

**Warum Reiter und nicht Verschieben:** Die Fähigkeiten *ganz* aus der Schiene zu nehmen, wäre erst
richtig, wenn geklärt ist, wohin sie gehören — und dort liegt eine **gemessene Teil-Doppelung**:
22 Fähigkeiten-Einträge (9 Fachgruppen + 13 Rechen-Engines) gegen 19 L4-Fachplaner-Flächen, mit
mindestens drei offensichtlichen Paaren (`engine-fbh`↔`fach-fbh`, `engine-pv`↔`fach-pv-module`,
`engine-kueche`↔`fach-kueche`), aber **keiner** 1:1-Deckung. Diese Zusammenführung ist ein eigener,
zu messender Posten — **nicht Teil dieses Auftrags**. Bis dahin bleibt alles erreichbar, nur nicht
mehr übereinandergestapelt.

**Standard-Reiter ist `Werkzeuge`** — er trägt den häufigsten Job.

---

## Nahtstellen

- `app/HausplanerApp.tsx` — die Schiene (~`:1000`–`:1030`): Reiterzeile oben, darunter genau ein
  Abschnitt. `FaehigkeitenNavi` und der Projektbrowser wandern je in ihren Reiter.
- **Wiederverwenden statt neu bauen:** Das Reiter-Muster existiert bereits im Eigenschaften-Panel
  (`app/dashboard/panelTabs.ts` + Rendering in `HausplanerApp.tsx`, aus Dashboard v2.2, inklusive
  `role="tablist"`, Pfeiltasten und Fokusnachführung aus AUF-19). **Dieses Muster wird verwendet,
  kein zweites erfunden.** Ein eigenes Datenmodul für die drei Schienen-Reiter ist zulässig; ein
  zweiter Tab-Mechanismus ist es nicht.

## Was ausdrücklich NICHT dazugehört

- **Keine Zusammenführung** von Fähigkeiten und L4-Flächen. Eigener Posten.
- **Kein Inhalt wird gelöscht.** Alle 22 Fähigkeiten-Einträge bleiben erreichbar.
- Die obere Gruppenzeile aus I4 wird **nicht** angefasst — sie ist ein eigener Posten (sie läuft bei
  1440 px über drei Zeilen, das ist bekannt und hier nicht Gegenstand).
- `store/*`, `domain/*`, `geometry/*`, `renderers/*`, Zod, Schema, PHP, Migrationen, `public/*`.

## Kantenliste

1. **Reiterwechsel darf die Werkzeugwahl nicht verlieren.** Wer auf `Projekt` wechselt und zurück,
   findet dasselbe aktive Werkzeug vor.
2. **Der gewählte Reiter überlebt einen Neuladen nicht zwingend** — aber wenn er gespeichert wird,
   dann **nicht im Szenendokument**. Kein neues Feld, kein Zod, kein Schema. `localStorage` oder
   Komponenten-Zustand sind zulässig.
3. **Schmale Fenster:** Drei Reiter in 220 px — brechen die Beschriftungen um, oder werden sie
   gekappt? **Umbrechen, nicht kappen** (AUF-26 gilt weiter).
4. **Eingeklappte Schiene** (66 px, `HausplanerStudio` klappt unter 900 px automatisch ein): Was
   passiert mit den Reitern? Entscheide und sag es im Bericht.
5. **Leerer Projektbrowser** (Szene ohne Knoten): der Reiter bleibt sichtbar, sein Inhalt sagt
   ehrlich, dass nichts da ist — `PROJEKTBAUM_LEER` existiert bereits, wiederverwenden.
6. **Fokus:** Reiter sind fokussierbar. Kein fokussierbares Steuerelement in einer im Rumpf von
   `HausplanerApp` definierten Komponente (Befund B1).

## Abnahmekriterien

1. `tsc:hausplaner` · `schema:hausplaner:check` (**ohne Regen**) · `test:hausplaner` — **Exit 0**.
   `build:hausplaner` mit Ergebnis berichten.
2. Testzahl vorher/nachher, **Namen-Mengen verglichen**, kein verschwundener Test.
3. Ein Test belegt: **genau drei** Reiter in fester Reihenfolge `werkzeuge · projekt · faehigkeiten`,
   Standard ist `werkzeuge`, jede id eindeutig.
4. Ein Test belegt: **immer genau ein** Abschnitt sichtbar — nie zwei, nie keiner.
5. Ein Test belegt: die **Anzahl der erreichbaren Fähigkeiten-Einträge ist unverändert 22**.
   Nichts darf beim Umbau verschwinden.
6. Das Reiter-Muster aus v2.2/AUF-19 ist **wiederverwendet**: `role="tablist"`, `aria-controls` auf
   eine existierende `id`, Pfeiltasten-Navigation mit Fokusnachführung. Test.
7. **Gegen-Beweis, selbst geführt:** die Reihenfolge der drei Reiter vertauschen → mindestens ein
   Test **muss** rot werden. Danach zurückbauen, `git diff` leer.
8. **0 rohe Farbwerte in den geänderten Zeilen.**
9. `git diff` zeigt null Zeilen in `store/*`, `domain/*`, `geometry/*`, `renderers/*`, `public/*`.
10. **Spalte „Sieht Yama das?": `sichtbar`.** Deshalb ist die **Browser-Sichtprobe Teil der Abnahme**,
    mit genannter Fensterbreite, bei **1440** und **1024** px.

## Guardrails

- Posten **auf der Tafel ziehen, bevor** die erste Zeile geschrieben wird.
- **Ein Commit**, Pfadangabe zwingend. **Nie `-A`, nie `.`**, `-m` **vor** dem `--`.
- `.git/*.lock` nur per `mv` nach `.git/_locks_beiseite/<datum>/`.
- **Kein Push, kein Merge, kein Deploy. „umgesetzt", nie „abgenommen".**

## Bericht

`## ⇒ GENERATOR-BERICHT — AUF-27 Linke Spalte mit Reitern`, mit den zehn Kriterien als Rohausgabe,
der Entscheidung zu Kante 4, dem Gegen-Beweis aus Kriterium 7 und dem Commit-Hash.
