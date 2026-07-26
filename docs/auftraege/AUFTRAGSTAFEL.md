# ⇒ AUFTRAGSTAFEL — der Abholplatz für Generator, Evaluator und Repo-Aufsicht

**Angelegt:** 25.07.2026 · **Vom:** Planner · **Anlass:** Yama, 25.07.: *„so kannst du für den
generator aufgaben hinterlegen dass er sich holen kann, ausserdem habe ich fest gestellt dass der
wächter pausiert weil die verbindung nicht da ist."*

**Warum diese Datei existiert:** Bisher lagen Aufträge verstreut in `docs/auftraege/*.md` und wurden
im Ledger *erwähnt*. Wer neu startete, musste 1300 Ledger-Zeilen lesen, um zu wissen, was für ihn
offen ist. Das ist keine Übergabe, das ist Archäologie. Die Tafel ist das **Register**: eine Seite,
auf der jede Rolle in zehn Sekunden sieht, was sie abholen darf.

---

**Neue Sitzung?** Zuerst `docs/WIEDEREINSTIEG-HAUSPLANER.md` lesen — eine Seite, die sagt, in
welcher Reihenfolge nachgesehen wird und was gerade Ballbesitz ist. Dann hierher zurück.

---

## 0. Die Tafel läuft ohne Verbindung — das ist Absicht

Yamas zweite Beobachtung ist die wichtigere: **der Wächter pausiert, weil die Verbindung fehlt**
(so schon im Ledger festgehalten: „Überwacher-Cron pausiert, Aufsicht läuft künftig lokal").
Eine Auftragsverteilung, die einen laufenden Prozess, einen Cron oder eine offene Sitzung braucht,
fällt damit **genau dann aus, wenn sie gebraucht wird**.

Deshalb ist die Tafel bewusst **eine Datei im Repo** und kein Dienst:

- **Hol-Prinzip, nicht Bring-Prinzip.** Niemand muss erreichbar sein. Wer arbeitsfähig ist, liest
  die Tafel und nimmt sich, was für ihn offen ist.
- **Kein Netz nötig.** Die Tafel liegt im Arbeitsbaum. Sie ist auch lesbar, wenn kein Remote
  erreichbar ist, kein Cron läuft und keine andere Instanz wach ist.
- **Der Zustand steht im Commit, nicht im Kopf einer Sitzung.** Fällt eine Instanz aus, geht
  nichts verloren — der letzte Stand ist das, was zuletzt committet wurde.
- **Die Wahrheit bleibt der Ledger.** Die Tafel ist Register und Zeiger, nicht Beleg. Weicht sie
  vom Ledger ab, gilt `docs/handoff-status.md` — und die Abweichung wird gemeldet, nicht geglättet.

---

## 1. Wie ein Auftrag abgeholt wird (vier Schritte, keine Rückfrage nötig)

1. **Lesen:** diese Tafel, dann die verlinkte Auftragsdatei **vollständig**. Der Auftrag ist die
   Spezifikation; die Tafelzeile ist nur die Überschrift.
2. **Ziehen:** in der Tabelle unten den Status von `OFFEN` auf `IN ARBEIT — <Rolle>` setzen und
   **diese eine Datei** committen:
   `git commit -m "Auftragstafel: <AUF-x> gezogen" -- docs/auftraege/AUFTRAGSTAFEL.md`
   Damit sieht jede andere Instanz, dass der Posten vergeben ist. Zwei Instanzen am selben Auftrag
   sind der teuerste Fehler dieser Woche gewesen.
3. **Arbeiten** — streng im Umfang der Auftragsdatei. Taucht Nötiges außerhalb auf: **zurückgeben**,
   nicht heimlich mitbauen (Governance: kein Beifang).
4. **Melden:** Bericht als Block in `docs/handoff-status.md`, dann Tafelstatus auf
   `BERICHTET — wartet auf <Rolle>`. **Niemand setzt seinen eigenen Auftrag auf `ERLEDIGT`** —
   das tut die abnehmende Rolle. Kein Selbst-Abnehmen, das ist die eiserne Regel.

**Staging-Regel für alle:** immer `git commit -m "…" -- <eigene Pfade>` mit ausdrücklicher
Pfadangabe. Nie `-A`, nie `.`. Grund: Am 25.07. hat ein Commit ohne Pfadangabe sechs fremde,
bereits gestagte Dateien mitgenommen — der Fehler ist im Ledger offen ausgewiesen, nicht kaschiert.
Und `-m` steht **vor** dem `--`, sonst frisst der Pathspec den Text.

**Abgleich-Regel (Planner, 26.07., auf Befund des Evaluators):** Der Tafelstatus wird **gegen die
committeten Voten** geführt, **nicht** gegen die Generator-Meldung. Grund, von ihm benannt: die Tafel wurde
aus den Berichten des Bauenden gepflegt — damit sind die Urteile des Prüfenden für die Planung unsichtbar,
bis jemand abgleicht. **Dreimal ist das heute passiert** (die „sieben", dann AUF-45, dann AUF-44/49/53/59).
**Mechanisch, nicht als Vorsatz:** vor jedem Tafel-Schreiben werden die Voten aus `git log` gegen die
offenen Zeilen gehalten; jeder offene Posten mit vorhandenem Votum ist ein Fund, kein Zufall.

**Rohr-Regel (Planner, 25.07., nach zwei zerbrochenen Zeilen):** Ein `|` im Zellentext spaltet die
Tabellenzelle — **auch innerhalb von Backticks**. AUF-47 und AUF-51 hatten dadurch 9 statt 7 Felder, der
Status stand in der falschen Spalte, und die Tafel las sich für jede Instanz falsch. Rohr-Zeichen in
Code-Beispielen **umschreiben**, nicht maskieren (`\|` überlebt das nächste Skript nicht). **Nach jedem
Tafel-Schreiben prüfen: jede `| **AUF-`-Zeile hat genau 7 Felder.**

**Lock-Regel:** liegen `.git/*.lock`-Reste, blockieren sie jeden Commit. Auf der Geräte-VM ist
`unlink` verboten → **`mv` nach `.git/_locks_beiseite/<sammel>/`, niemals `rm`**.

---


## 1b. Zwei Nummernkreise — Legende (damit niemand mehr sucht)

Es laufen **zwei** Bezeichnungen nebeneinander. Sie meinen dasselbe:

| Welle / Slice | Tafelposten | Gegenstand |
|---|---|---|
| **Welle A1** | **AUF-1** | Werkzeug-Präsentationsschicht (`c0ffe31`) — abgenommen 25.07. |
| **Welle A2** | **AUF-4** | Leiste liest die Präsentationsschicht (`acdb987`) — **Abnahme offen** |
| **Welle A3** | noch kein Posten | Kontext-Zone + Anheften, im A2-Auftrag §3 abgetrennt |
| **T1** | AUF-2 / AUF-3 | Token-Konsolidierung + `decke → bau` (`9ec3b25`) |
| **T2a / T2b** | AUF-9 / AUF-10 | Farbwert-Kommentare · Palette in `geometry/` |
| **Dashboard v2** | AUF-12 | Batch 1 (`f6bdfc2`) · Batch 2 (`5092b10`) |
| **N1 / N2 / N3** | AUF-15a / AUF-16 / AUF-19 | Nacharbeit aus dem Batch-1-Votum |
| **I1 / I2 / I3** | AUF-21 | Icons ablegen · Katalog tauschen · Anheften + Zonen |
| **L1 … L7** | Layout-Fahrplan | **L1 = Welle A2 = AUF-4** · L4 = AUF-25 |

**Merksatz: „Welle A2", „L1" und „AUF-4" sind derselbe Posten.**

## 1c. ⚡ AKTIV — genau ein Posten, und nur der wird gezogen

**Regel (Yama, 25.07.):** Auf dieser Tafel trägt **genau ein** Posten die Markierung **`⚡ AKTIV`**.
**Der Generator zieht nur diesen.** Alles andere wartet, auch wenn es kleiner, reizvoller oder
schneller erledigt wäre.

**Warum:** Am 25.07. wurde AUF-30 gezogen — ausdrücklich als *nicht blockierend* eingetragen —
während der Posten, an dem Yamas Ziel hing, offen liegen blieb. Nicht aus Nachlässigkeit, sondern
weil beides gleich aussah. Ein Wort auf der Tafel unterscheidet sie.

**Wer setzt es:** der Planner, nach Yamas Fokus. **Wer verschiebt es:** niemand sonst.
Ist der aktive Posten **berichtet**, rückt die Markierung auf den nächsten der Kette — **nicht erst nach der Abnahme.** Korrektur vom 25.07.: die erste Fassung verlangte die Abnahme, das hätte den Generator bei jeder Abnahme leerlaufen lassen. Der Zweck der Markierung ist, **Themenwechsel** zu verhindern, nicht die Kette zu serialisieren. Abnahmen laufen parallel. **Fällt eine Abnahme rot aus, geht die Markierung zurück** auf den beanstandeten Posten.
**Ist der aktive Posten blockiert**, wird das gemeldet — die Markierung wandert *nicht* still weiter.

**Ausnahme, eng:** Ein Posten, der den aktiven **direkt entsperrt**, darf vorgezogen werden. Er wird
dabei ausdrücklich als solcher benannt („entsperrt AUF-x"), sonst ist es keine Ausnahme, sondern
Themenwechsel.

---

## 1d. Spalte „Sieht Yama das?" — zwei Werte, Pflicht beim Berichten

Jeder Posten trägt beim Bericht einen von zwei Werten:

| Wert | heißt |
|---|---|
| **`sichtbar`** | Nach dem nächsten Build ändert sich für Yama etwas auf dem Schirm. **Dann ist eine Browser-Sichtprobe Teil der Abnahme, nicht danach.** |
| **`Vorarbeit`** | Notwendig, aber unsichtbar. Datenmodell, Adapter, Testinfra, Zustandslogik. |

**Warum:** `ccdc93b` („I3", die sechs Werkzeug-Zustände) war saubere, notwendige Arbeit — und für
Yama auf dem Schirm **null Veränderung**, weil alle 110 Werkzeuge auf `versteckt` blieben. Es hat
drei Runden gedauert, das zu bemerken. Mit dieser Spalte hätte der Bericht `Vorarbeit` sagen müssen,
und es wäre in derselben Minute klar gewesen.

**Kein Urteil, nur eine Tatsache.** `Vorarbeit` ist nicht schlechter als `sichtbar` — die Hälfte
jedes Bauwerks ist Fundament. Aber Yama muss es unterscheiden können, ohne zu fragen.
## 2. Statuswerte (mehr braucht es nicht)

| Status | heißt |
|---|---|
| `OFFEN` | darf sofort gezogen werden |
| `GESPERRT` | Vorbedingung nicht erfüllt — **nicht** ziehen, Sperrgrund steht in der Zeile |
| `IN ARBEIT — <Rolle>` | vergeben |
| `BERICHTET — wartet auf <Rolle>` | umgesetzt und im Ledger belegt, Abnahme steht aus |
| `ERLEDIGT` | abgenommen von der prüfenden Rolle, mit Beleg im Ledger |
| `BEI YAMA` | Willensfrage — keine Instanz entscheidet das in seiner Vertretung |

---

## 3. Die Tafel (Stand 25.07.)

> **Kein HEAD-Hash im Kopf** — er veraltet mit dem nächsten Commit; wer den Stand braucht, misst ihn
> (`git --no-optional-locks log --oneline -5`). Die Regel stammt vom Planner selbst (`2f39924`).

**Vier Tabellen statt einer — geordnet danach, wer als Nächstes handeln muss.** In einer Tabelle mit 39
Zeilen war der eine Posten, der gezogen werden darf, nicht mehr zu finden. Kein Posten ist verschwunden:
**20** Arbeitsvorrat · **0** Abnahme · **0** bei Yama · **49** im Archiv — Summe geprüft, 69.

> **🔴 26.07., 00:45 — `objekt/203` ist kaputt.** Ein `@php`-Block aus AUF-60 hat das inline-`@php(…)`
> derselben Datei zerbrochen; die Route liefert einen PHP-ParseError statt der Seite. **AUF-64 hat
> Vorrang vor allem anderen.** Gefunden hat es nicht das Gate — 1007 Tests prüfen kein Blade — sondern
> ein Blick in den Browser. Das ist der dritte Fall heute, in dem eine Sichtprobe etwas fängt, das
> kein Test sieht.

> **Zum Stapel von vier:** gemessen für den 25.07. **31 Bauten gegen 29 Voten** — der Evaluator ist
> **nicht im Rückstand**. Der Stapel steht bei vier, weil der Generator zwischen 23:27 und 00:01
> **viermal** geliefert hat. Der Auftrag dazu liegt als `evaluator-auftrag-stapel-2026-07-25.md` und
> sagt das ausdrücklich: **ein schnelles Votum ist wertlos.**

> **25.07., 23:15 — vier Fragen in Yamas Vertretung entschieden.** Er hat ausdrücklich delegiert
> („nimm deine Empfehlungen an"). **Das ist die Ausnahme, nicht die Regel** (§5: keine Instanz entscheidet
> eine Willensfrage in seiner Vertretung) — deshalb steht an jeder der vier Zeilen, dass die Entscheidung
> vom Planner stammt und wie sie zurückzudrehen ist. Zwei ändern nichts (AUF-7, AUF-8: keine Aktion),
> zwei werden gebaut (**AUF-55**, **AUF-56**). Bei Yama liegt jetzt **ein** Posten: stopp-1 Teil I,
> der einzige außerhalb des Hausplaners.

> **25.07., 23:00 — fünf Willensfragen entschieden.** AUF-41 (eigenes Import-Recht) · AUF-15b (Farb-Kriterium
> auf die geänderten Zeilen) · AUF-5 und AUF-10 (Farbe als Parameter) · AUF-17 (Strg+K bleibt die Palette).
> Zwei davon brauchen Bau: **AUF-53** und **AUF-54**. Eine (AUF-17) bestätigt den Ist-Zustand und kostet
> nichts. Bei Yama liegen damit noch **fünf** statt zehn.

> **Nachmessung 25.07., 22:50 — der Abnahme-Stapel war leer, nicht voll.** Yama bat darum, den
> Evaluator zur Abarbeitung anzuschreiben. Vor dem Schreiben gemessen: **sieben der sieben Posten
> hatten bereits ein committetes Votum** (`7293a2d` · `5dea3c2` · `a2188e6` · `7fe6627` · `ea61ea1` ·
> `134eb0e` · `33ad6d6`). Die Tafel hinkte hinterher, nicht der Evaluator. **Zum zweiten Mal heute**
> derselbe Fehler von mir — beim ersten Mal waren es acht Posten (`32d2ecc`). Die Mahnung wäre an
> jemanden gegangen, der schneller war als sein Register.

> **Reihenfolge in 3a ist ab hier Yamas Befundliste**, nicht die Nummernfolge: AUF-39 (Wizard erfindet
> Zustände) · AUF-43 (Geschoss-Bedienung) · AUF-45 (niemand sieht, wo man anfängt) — das sind genau die
> drei Dinge, die ihm beim Hinsehen aufgefallen sind. Danach das Kleine und Mechanische.

> **Layout-Untersuchung 25.07., 22:30** (Yama: „schau dir das Layout mal an") — Sichtprobe über alle
> fünf Ebenen der Inventur bei 1440/1024/375 px, jeder Befund im Code belegt:
> **`docs/planner/ux-befund-layout-alle-ebenen-2026-07-25.md`**. Neun Befunde: drei waren bereits
> beauftragt (AUF-39, AUF-40 A+B), vier sind neu (**AUF-43 bis AUF-46**), einer hat sich beim Messen
> **widerlegt** (B9 — der Knopf, den ich abgeschnitten zu sehen glaubte, endet 13 px vor dem Rand).
> Kernsatz: die Oberfläche ist nicht kaputt, sie ist an vier Stellen **unehrlich** und an zwei Stellen
> **stumm** — sie sagt nirgends, wo man anfängt.

> **Sperren-Nachmessung 25.07., 21:40:** Alle sechs `GESPERRT`-Zeilen gegen ihren Sperrgrund gehalten.
> **AUF-28 war seit Stunden zu Unrecht gesperrt** („bis A2 abgenommen" — A2 lag längst frei) und ist
> **inzwischen ganz entfallen**: der Katalog-Tausch hat die 15 DTP-Versprechen mit erledigt. Drei weitere
> Sperren nannten eine Reihenfolge, die nicht gemessen war (AUF-38/39/40). **Nachtrag 22:10: die drei
> Auftragsdateien liegen** — AUF-38 und AUF-39 sind damit `OFFEN`, AUF-40 nur im Frontend-Teil; sein
> Backend-Teil bleibt bei Yama, weil er Migration, Route und Controller berührt.

> **Befund 25.07., 21:05 (Bestandsaufnahme):** **L5 und L6 kamen auf dieser Tafel überhaupt nicht vor** —
> null Treffer, gemessen. Sie standen im Layout-Fahrplan, aber niemand konnte sie ziehen; sie wären nie
> gebaut worden, egal wie lange die Kette läuft. Als **AUF-39** und **AUF-40** nachgetragen. Lehre: ein
> Fahrplanschritt ohne Tafelzeile existiert für die Kette nicht.

> **Nachgeführt 25.07., 20:47:** Der Abnahme-Stapel stand auf 10, obwohl der Evaluator in
> `docs/abnahme-evaluator-haertung-2026-07-25.md` längst **13 Voten** committet hatte. Acht Posten waren
> abgenommen und standen trotzdem als „wartet auf Evaluator". Ursache: die Statusspalte wurde gepflegt,
> statt gegen das Votum gemessen. Ab jetzt gilt für diese Tafel: **ein Status ist erst gepflegt, wenn er
> gegen das Abnahme-Dokument gehalten wurde**, nicht gegen die vorige Tafelzeile.

---

### 3a. Arbeitsvorrat — hier wird gezogen

**Der erste Posten trägt ⚡ und ist der einzige, der gezogen werden darf** (§1c) — alles darunter ist
Reihenfolge, kein Angebot. `GESPERRT`: Vorbedingung nicht erfüllt, Grund steht in der Zeile.

**Die Staffel (Yama, 26.07.: einspurig, kein Parallelbetrieb — §13).** Beide Rollen kennen ihre
Reihenfolge im Voraus und müssen zwischen zwei Posten nicht nachfragen:

**Generator:** 1. **AUF-78** (⚡) · 2. **AUF-82** · 3. **AUF-79** · 4. **AUF-81** · 5. **AUF-66** ·
6. **AUF-76** · 7. **AUF-77** · 8. **AUF-54/55/56** · 9. **AUF-63** (allein — er ändert den
Testläufer selbst). **AUF-82 steht unmittelbar vor AUF-79, weil beide `scripts/waechter.sh`
anfassen** — zwei Posten in derselben Datei gehören nebeneinander.

**Evaluator — ohne Buchstaben, nur Nummern.** *(Grund, 26.07., 17:25: die Buchstaben waren eine
**zweite Benennung** derselben Posten, und der Evaluator hat sie anders zugeordnet als die Tafel —
`AUF-82` fiel dabei aus seiner Zaehlung und er meldete Standby, waehrend ein Votum fehlte. **Eine
zweite Benennung ist eine zweite Wahrheit.** Ab jetzt gilt die AUF-Nummer, sonst nichts.)*
Erledigt: Waechter-Blindstelle · Sichtprobe-Grundlinie · **AUF-78** · **AUF-81**.
**Offen: AUF-82** — dann AUF-66 · AUF-76 · AUF-77. *(AUF-79 ist Spur B, ohne Evaluator.)*

**Leerlauf wird gemeldet, nicht überbrückt** (§13.3): Staffel leer → eine Zeile in den Ledger,
dann warten. Nichts aus dem Vorrat ziehen, was nicht in der Staffel steht.

| Nr | Auftrag | Rolle | Status | Datei / Beleg |
|---|---|---|---|---|
| **AUF-40** | **L6 — Start/Zuletzt an echte Projekte + Konfigurator-Paket serverseitig speichern.** Gemessen offen: `app/StartView.tsx:75` rendert die Demo-Liste `ZULETZT`, `app/ConfigWizard.tsx` lädt das `ConfiguratorPackage` als JSON **herunter**, statt es zu speichern. Hatte bis 25.07. ebenfalls **keinen Posten** (null Treffer auf `L6`). Braucht Backend-Anschluss, blockiert kein Layout **Zielbild aus der UX-Bewertung 26.07.:** je Eintrag Projektname · letzter Zeitpunkt · aktueller Schritt · offene Aufgaben · Speicherstatus · **direkte Fortsetzen-Aktion**. | Yama | **Teil A `ABGENOMMEN`** — FREIGABE (`865a545`) fuer `4cc9f6e` + Bundle `10f7dd7`. Die drei erfundenen Projekte sind **stillgelegt statt geloescht** (Muster `toolCatalogStillgelegt`/`STEPS_STILLGELEGT`) und rendern nichts; ehrlicher Leerzustand statt Beispielzeilen; **drei Karten, drei Ziele** — die zwei ohne Ziel sind **keine Schaltflaeche mehr** (keine Rolle, kein Fokus, kein Zeiger), sonst waeren sie fokussierbar und taeten nichts. Tests 1082 auf **1091**; Sichtprobe: **0 erfundene Namen**, wo sie vorher in **jedem** Screenshot standen. **Rest `GESPERRT` — wartet auf Yama:** (1) die **Zulieferung der echten Projektliste** (eine Variable im Controller, **Tor 1**) — zurueckgegeben statt nebenbei gebaut, siehe `W-Projektliste`; (2) **Teil B** (Konfigurator-Persistenz) ist von Yama am 26.07. **vertagt**, stattdessen laeuft AUF-74 | `generator-auftrag-auf40-start-und-persistenz.md` |
| **AUF-38** | **Inline-Styles ablösen** (Entscheidung Yama, 25.07., aus AUF-14) — 331 `style={{` in 35 Dateien wandern in eine echte Stilschicht; `build:hausplaner` erzeugt künftig `public/hausplaner/hausplaner.css`, die vorhandene `@if (file_exists(…))`-Bewachung in beiden Blades greift dann von selbst. **In Scheiben, nach Datei geschnitten** (HausplanerApp 132 · GuidedView 41 · ConfigWizard 39 · HausplanerStudio 34 · FachFlaeche 27 · StartView 20 · Rest 38) — nie zwei Scheiben gleichzeitig, weil dieselben Dateien der Werkzeugleiste gehören. **Wert- und verhaltenstreu:** kein gerenderter Farbwert ändert sich, `studioDaten.ts` bleibt die Quelle | Generator | **`SCHEIBE 3 ZURUECKGEGEBEN — wieder offen`** (`FachFlaeche.tsx`, **kein Code angefasst**): der K9-Beleg braucht den **geoeffneten Dialog**, und der laesst sich nur im langsamen headful-Lauf oeffnen (headless rendert die Insel nicht — gemessen: null Reiter). **Ohne diesen Beleg waere die Wertgleichheit nur behauptet.** · **`SCHEIBE 2 BERICHTET`** (`StartView.tsx`) — Code `e862b8f2` + Artefakte `8ed190e3`; acht statische Stil-Objekte zu Klassen, Farben als `--hp-*`-Variablen, **null rohe Farbwerte in der CSS**. **Kriterium 9 buchstaeblich belegt: vorher/nachher in drei Viewports, ganzseitig sha256-verglichen — PIXELGLEICH.** Gates 0/0/0/0 + `test:dom` 0; Insel 1288 auf 1292, PHP 789; Mutation 2 rot. **Auftragszahl gewandert: 20 genannt, 34 gemessen** (AUF-56/66 haben dazugelegt) · `SCHEIBE 1 ABGENOMMEN` (Votum `ba47815`, FREIGABE); laeuft erst nach AUF-52, ein Bauender je Posten (§13) — Code `cca1837` + Artefakte `022021f` (`hausplaner.js` **und** die neu entstandene `hausplaner.css`, nach K8 im selben Commit). Gates 0/0/0/0 + `test:dom` 0; Insel 1246 auf 1256, PHP 789; Mutation 2 rot. **Kriterium 9 buchstaeblich belegt: dieselbe Seite mit und ohne die neue CSS, drei Viewports, Bildschirmfotos sha256-verglichen — PIXELGLEICH.** Keine Bau-, keine Blade-Aenderung noetig; **keine einzige `style`-Stelle umgestellt** (Scheiben 2-8 offen) **Er laeuft neben nichts** — acht Oberflaechendateien, darunter `HausplanerApp.tsx`. Auftrag liegt: **acht Scheiben**, Scheibe 1 (Grundgerüst) wird eigens abgenommen. **Scheibe 7 ist entsperrt** — AUF-35a ist abgenommen; sie bleibt trotzdem die letzte, weil `HausplanerApp.tsx` auch AUF-43/45/48 trägt **Marke 26.07., 23:20:** AUF-52 hat fertig gebaut, **Scheibe 2 ist frei und wird gezogen.** **Mit Yamas Entscheid fuer Weg B ist AUF-38 kein Aufraeumposten mehr, sondern der tragende:** die Token-Quelle, zu der der Rest ziehen soll. **Scheibe 2 (StartView) BERICHTET:** acht statische Objekte umgestellt, K9 pixelgleich in drei Viewports, Gates 1292/0. Scheiben 3-8 offen. **FEIERABEND (Yama, 26.07., 23:35): keine neuen Auftraege heute.** **Generator: Scheibe 3 wird NICHT gezogen** — Feierabend nach Scheibe 2. **Evaluator: AUF-38 Scheibe 2 ist die letzte Abnahme des Tages**, danach Schluss. Die Marke ist bewusst entfernt: **es gibt heute keinen aktiven Posten mehr.** **NACHTRAG 23:38 — Scheibe 3 wurde 23:35 gezogen, eine Minute VOR dem Feierabend-Eintrag. Das ist ein Wettlauf, kein Verstoss.** Regel dafuer: **ist noch nichts gebaut, leg sie zurueck** — `SCHEIBE 3 ZURUECKGELEGT` in diese Zelle, fertig. **Ist schon etwas gebaut, bau sie zu Ende und melde sie** — *eine halb umgestellte Datei ueber Nacht ist schlimmer als eine Scheibe mehr.* In beiden Faellen: **danach Schluss, Scheibe 4 wird nicht gezogen.** **TAGESABSCHLUSS 23:42:** **Scheibe 2 ABGENOMMEN** (Votum `265cfe55`, FREIGABE) — der Evaluator hat den kritischen Punkt von Hand am Diff geprueft, *weil kein Gate einen eingefrorenen Zustand faengt*: `cardBase`/`kicker`/`h1` sind Klassen, die hover/State-Stile bleiben inline. **Scheibe 3 ZURUECKGELEGT ohne Codeaenderung** — `FachFlaeche` ist ein **Dialog**, und der K9-Beleg braucht Bildschirmfotos mit **geoeffnetem** Dialog, also einen headful-Lauf; headless rendert die Insel nicht (gemessen: null Reiter). *Ohne ihn waere die Wertgleichheit nur behauptet.* **AUFLAGE fuer die Restscheiben (Planner, 26.07.): vor dem Ziehen pruefen, ob die Datei einen Dialog traegt — wenn ja, gehoert der headful-Lauf in den Auftrag, nicht in die Ueberraschung.** Scheiben 3-8 offen, **kein aktiver Posten.** | `generator-auftrag-auf38-inline-styles.md` |
| **AUF-48** | **`HausplanerApp.tsx` zerlegen** (externe Bewertung Nr. 3, nachgemessen: **2.052 Zeilen**) — bündelt Canvas, Werkzeugleisten, Auswahl, Eigenschaften, Dach, Treppe, Palette, Tastatursteuerung und Layout in einer Datei. Sie trägt zugleich **132 der 331 Inline-Styles** (AUF-38) und ist die Datei, an der heute jeder zweite Posten arbeitet — Kollisions- und Regressionsrisiko | Planner → Generator | `GESPERRT` — **bewusst, nicht aus Bequemlichkeit:** ein Schnitt durch die meistberührte Datei des Projekts während laufender Posten (AUF-33, AUF-38, AUF-43, AUF-45) erzeugt genau die Kollision, die AUF-22 verhindern soll. Erst wenn die Layout-Posten durch sind. Planner schreibt dann den Schnitt **Yama, 26.07.: AUF-48 laeuft VOR AUF-50.** Beide fassen `HausplanerApp.tsx` tief an, gleichzeitig geht nicht; die Werkzeug-Schicht (50.1a) baut danach auf zerlegten Dateien. **AUFLAGE (Planner, 26.07., Entscheid von Yama delegiert):** beim Zerlegen wird das **Dachform-Auswahlfeld** (`HausplanerApp.tsx:1884`) mitgenommen — es wird aus `geometry/dachformVorlagen.ts` gespeist statt aus der festen 8er-Liste. **Probe des Erprobers:** *Er oeffnet die Dachform-Auswahl und findet dort die fertigen Vorlagen mit Deckung und Neigung — „Sattel Schiefer steil", „Pult Blech", „Flach Gruendach" — statt acht nackter Formnamen.* **Grenze:** nur Vorlagen, deren `shapeKey` einer der acht `RoofShape`-Werte ist; die 14 nicht speicherbaren Formen bleiben aussen vor, bis Yama ueber die Schema-Erweiterung entschieden hat. **Gemessen:** 72 Vorlagen, 22 Dachformen, 8 darstellbar. **Vorbehalt aufgeloest (26.07.): Yama folgt der Empfehlung — Weg B.** Die Auflage gilt; gebaut wird gegen die Token-Quelle des Planers. **Die Sperre bleibt** — sie sagt *„erst wenn die Layout-Posten durch sind"*, und AUF-38 hat sieben offene Scheiben. | Ledger „Externe Frontend-Bewertung" (25.07.) |
| **AUF-50** | **Die 110 Werkzeuge funktionstüchtig machen — Fahrplan in vier Stufen** (Yama, 25.07.: „sollen wir jetzt coden funktionstüchtig machen"). **Gemessen:** die Verträge nennen **110 verschiedene** `commandId`, das Modell kennt **19** Command-Typen — die IDs sind Absichtserklärungen, keine Zeiger. Aufteilung: **41 rein Ansicht/Auswahl** (kein Command, kein Schema) · **69 modellverändernd**, davon `create` **40** (je ein Knoten-/Objekttyp ⇒ **Zod + `schema:hausplaner` + Bestandsdaten**), `modify` **20**, `workflow` **15**, `assign-or-calculate` **9** (hängt an den Engines, AUF-33 L3), `import` **8** (**hängt an AUF-41**, Yamas Rechte-Entscheidung), `view` 7 · `measurement` 5 · `selection` 4 · `domain` 2. **Yamas Schnitt-Entscheidung 25.07.: nach Aufwand, innerhalb seiner 15 Bereiche** — erst die 41 ohne Modellwirkung, dann `modify`, dann `create` mit Schema-Arbeit, `import`/`assign-or-calculate` zuletzt. **Nicht Bereich 1–15 der Reihe nach**, weil die Bereiche quer zum Aufwand liegen | Planner → Generator | `OFFEN OHNE AUFTRAG` — **Sperre gefallen, gemessen 26.07., 20:25:** die Bedingung lautete *erst Layout fertig* (AUF-39 · 43 · 45 · 44 · 47) — **alle fuenf liegen im Archiv.** **Es fehlt der Stufenplan, und den schreibt der Planner.** Die Zaehlung liegt seit 20:45 (`docs/planner/bestandsaufnahme-auf50-werkzeuge-2026-07-26.md`): **7 von 101 Werkzeugen haben einen Empfaenger, 94 nicht**; die 110 `commandId` sind **Metadaten, keine Aufrufe** (19 echte Command-Typen an 34 Aufrufstellen). Vier Stufen: **50.1 generischer Empfaenger** · 50.2 `create` (40) · 50.3 `modify`+`selection` (24) · 50.4 `view`+`measurement` (12). **Umfang ~78 statt 110** — `import` gehoert in Phase 2, `assign-or-calculate` an AUF-52. Planner schreibt danach den Stufenplan; jede Stufe wird ein eigener Posten, nicht dieser hier | Ledger „Werkzeuge funktionstüchtig — die gemessene Lücke" (25.07.) |

---

### 3b. Abnahme-Stapel — berichtet, wartet auf Pruefung

Niemand nimmt eigene Arbeit ab (§1.4).

| Nr | Berichtet | Commits | Was der Evaluator zuerst prueft | Ballbesitz |
|---|---|---|---|---|


### 3c. Bei Yama — Willensfragen

> **Leer.** `W-Projektliste` ist am 26.07. **an den Planner delegiert** und als **AUF-78**
> beauftragt — mit ausgeschriebener Begruendung, wofuer der Planner haftet (§6 des Auftrags).
> Neue Willensfragen gehoeren wieder hierher, nicht in eine Meldung.

### 3d. Abgeschlossen — im Archiv

Abgenommen, entschieden oder entfallen, wortgleich in **`docs/auftraege/AUFTRAGSTAFEL-ARCHIV.md`** —
**65 Zeilen** — gezaehlt am 26.07., 17:05 **aus der Datei selbst**. **Die Zahl steht hier nur als
Momentaufnahme; die Wahrheit ist das Archiv.** Eine Aufzaehlung der Nummern stand bis eben hier und
ist entfernt: sie war eine **zweite Buchfuehrung**, sie nannte **49** und listete **26** — und sie lag
gegen ihre eigene Quelle um **16** daneben. **Gefunden hat es der Generator beim Bau von AUF-79**,
also genau der Posten, der das Zaehlen von Hand ersetzt. Nicht geloescht, nur nicht mehr im Weg.

**Zu AUF-9/AUF-10:** Beide stammen aus den zwei Punkten, die der Generator mit AUF-5 zurückgegeben
hat. Sie sind **kein** T1-Nachtrag — T1 gilt für die React-Insel (`app/*`), und die ist mit 0 rohen
Farbwerten erfüllt. `geometry/` und `renderers/` liegen außerhalb dieses Scopes (Token-Scope-ADR),
deshalb eigener Posten T2. Der ausführbare Teil (T2a) ist vom Willensteil (T2b) getrennt, damit der
Generator arbeiten kann, ohne dass jemand in Yamas Vertretung eine Farbentscheidung trifft.

---

## 4. Zwei Richtigstellungen, damit niemand doppelt fragt

**(a) „A2 braucht eine Planner-Spezifikation, die es noch nicht gibt" — das stimmt nicht mehr.**
Der A2-Auftrag liegt seit `d530da3` als Datei vor und wurde mit `78d384d` um §8 erweitert
(Sperre, P9-Memoisierung, drei neue Abnahmekriterien). Wer A2 zieht, findet eine vollständige
Spezifikation vor. Der Punkt aus dem Sammelblock ist damit erledigt.

**(b) „A1 erneut abnehmen — ja oder nein?" — Yama hat das bereits entschieden: ja.**
In seiner eigenen Auftragsdatei steht es wörtlich als Auftrag 1, mit dem Zusatz
„**Kritischer Pfad: A2 bleibt blockiert, bis das durch ist.**" Das ist keine offene Frage mehr,
sondern AUF-1 oben. Der Planner hat die Divergenz zu seiner eigenen, früheren A1-Abnahme im Ledger
als Abweichungsmeldung hinterlegt und sich Yamas jüngerer Anordnung gefügt: **A1 bleibt
abgenommen, A2 wird trotzdem nicht begonnen**, bis das Wiederholungsvotum im Ledger steht. Eine
zusätzliche unabhängige Abnahme kann eine Freigabe nur härten, nie weichmachen.

---

## 5. Was die Tafel ausdrücklich **nicht** tut

- Sie **nimmt nichts ab.** Grün entsteht durch Messung und Gegen-Beweis im Ledger, nie durch einen
  Tabelleneintrag.
- Sie **entscheidet keine Willensfrage.** Was auf `BEI YAMA` steht, bleibt dort stehen, auch wenn
  es bequemer wäre, es selbst zu entscheiden.
- Sie **ersetzt den Ledger nicht.** Wer nur die Tafel liest, hat die Belege nicht gelesen.
- Sie **drängelt nicht.** Ein Auftrag auf `OFFEN` ist ein Angebot, kein Weckruf.
