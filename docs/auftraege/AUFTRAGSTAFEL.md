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
| **AUF-38** | **Inline-Styles ablösen** (Entscheidung Yama, 25.07., aus AUF-14) — 331 `style={{` in 35 Dateien wandern in eine echte Stilschicht; `build:hausplaner` erzeugt künftig `public/hausplaner/hausplaner.css`, die vorhandene `@if (file_exists(…))`-Bewachung in beiden Blades greift dann von selbst. **In Scheiben, nach Datei geschnitten** (HausplanerApp 132 · GuidedView 41 · ConfigWizard 39 · HausplanerStudio 34 · FachFlaeche 27 · StartView 20 · Rest 38) — nie zwei Scheiben gleichzeitig, weil dieselben Dateien der Werkzeugleiste gehören. **Wert- und verhaltenstreu:** kein gerenderter Farbwert ändert sich, `studioDaten.ts` bleibt die Quelle | Generator | `⚡ AKTIV` · **`SCHEIBE 3 ABGENOMMEN — FREIGABE (Evaluator, 28.07.)`** fuer `01b9933e` + `2798120f`. Sein Gegen-Beweis war schaerfer als die Beanstandung: **token-wertigen Inline-Stil in `/tmp` eingeschleust ⇒ 2 Wirkungs-Tests rot** — die neue Zusage haelt also nicht nur den gebauten Zustand fest, sie faengt den Rueckfall. Dazu `serviert == gemessen` (Browser-Fetch gegen das Bundle) und **K9 headful am echten Dialog**: `Quelle == DOM == 3 Ausnahmen`. **NEBENBEFUND des Evaluators, nicht nachgeschoben — Planner-Entscheidung:** `ZustandBadge` in `studioUi.tsx` traegt statische token-wertige Inline-Stile, *im Geist von AUF-38, aber eigene Datei und eigener Scope*. **Entscheid: gehoert in Scheibe 8** (die Restdateien) und **nicht in eine eigene Scheibe** — `studioUi.tsx` hat 2 Stellen, ein eigener Posten dafuer waere mehr Verwaltung als Arbeit. Er hat richtig gehandelt, ihn zu melden statt ihn mitzunehmen. · `⚡ AKTIV` · **`>>> SCHEIBE 5 ANGEHALTEN. AKTIV: MESSSKRIPT + GENERISCHE ROHWERT-ZUSAGE (Spur B) <<<`** (Planner, 29.07., 00:06) **Grund 1 — der Evaluator ist blockiert und sagt es:** *„die parallele Scheibe-5-Bauarbeit im geteilten Arbeitsbaum macht den live-headful-K9 fuer Scheibe 4 unfuehrbar (Bundle vorgelaufen, Renderer instabil)“*. **Das ist meine Fehlentscheidung:** ich habe Scheibe 5 aktiv gesetzt, waehrend zwei Scheiben in der Abnahme lagen. §13 verbietet zwei Bauende je Posten — **es fehlte die Regel, dass waehrend einer laufenden Sichtprobe niemand das Buendel bewegt.** **Grund 2 — R9 greift, und der Evaluator hat es vor mir gesehen:** die Fehlerklasse *„Rohwert-Ausnahme ohne beidseitige Verriegelung“* ist **zweimal** aufgetreten (Toast `#1a262a` in Scheibe 4, Gradient `#e9f4f2/#eef3e6` in Scheibe 2). **R9 verlangt eine Barriere, keine zwei Einzelflicken.** Seinem Vorschlag folge ich unveraendert: **EIN generischer Test — fuer jede verbliebene Rohwert-Inline-Stelle ALLER Scheiben pruefen, dass ihre Farbwerte NICHT in `T` stehen.** *Per-Scheibe-Zusagen skalieren nicht; das ist bereits belegt.* **KLASSIFIZIERUNG beider Befunde (AUF38-S4-1, AUF38-NZ2-1): SPEZIFIKATIONSMANGEL, nicht Implementierungsmangel.** Meine Auflage verlangte *„Ausnahmen testverriegelt“*, aber **nie, dass alle Ausnahmen erfasst sind** — es fehlte die Mengenzusage. Der Generator hat verriegelt, was ich benannt habe. **Was er nicht benannt bekam, hat er nicht verriegelt — das ist mein Fehler.** **AUFTRAG (Spur B, ein Messwerkzeug — kein Produktionscode):** `scripts/statische-inline-stile.mjs` mit (a) der Definition aus der Quittung, (b) Ausnahmen-Erkennung Rohwert **und** `GESPERRT_*`, (c) **der generischen Rohwert-Zusage ueber alle Dateien**. Danach loesen beide Befunde mit **einem** Test, nicht mit zweien. **WAEHREND DER SICHTPROBE BEWEGT NIEMAND DAS BUENDEL** — neue Regel, gilt ab sofort: meldet der Evaluator eine laufende headful-Messung, ruht der Bau bis zu seinem Votum. **Scheibe 5 wird NICHT gezogen**, bis Scheiben 4 und 2 re-abgenommen sind. · ~~AUFTRAG SCHEIBE 5 NACHGEBESSERT — QUITTUNG BEANTWORTET (Planner, 28.07., 23:50) <<<`** **Die erste Quittung sagte `TRAEGT NICHT`, und sie hatte recht.** Der Mangel: *„statisch“* stand seit drei Scheiben ohne Definition im Auftrag. **Ich uebernehme seine Definition unveraendert:** **Ein `style={{…}}` ist STATISCH, wenn sein Ausdruck ausschliesslich aus Literalen und `T.*`-Zugriffen besteht** — kein `?:`, kein Spread, kein Aufruf, kein anderer Bezeichner als `T` (Eigenschaftsnamen zaehlen nicht, `${…}` wird vorher aufgeloest). **Zulaessige Ausnahme, bleibt inline:** der Block traegt einen **Rohwert ohne Token** (`#…`/`rgba(`) **oder** stammt aus einem **Ein-Wahrheit-Modul** (`GESPERRT_*`). **Ich habe die Definition unabhaengig nachgeeicht** — und meine Nachpruefung war **falscher als seine**: ich hatte nur Rohwerte als Ausnahme und bekam `FachFlaeche` auf 1 offen statt 0; `Z68` traegt `GESPERRT_GRUND`, also das Ein-Wahrheit-Modul. **Seine Fassung eicht alle drei abgenommenen Dateien auf 0, meine nicht.** Uebernommen wird seine. **SOLLWERT KORRIGIERT: 38, nicht 43** (43 gesamt, 40 statisch, 2 Ausnahmen). Meine 43 und die 39 im AUF-38-Kopf waren **beide richtig, mit verschiedener Zaehlweise** — `grep -c` zaehlt Zeilen, ein Vorkommenszaehler zaehlt Vorkommen. **Die Zaehlweise stand nirgends; das ist P-04 eine Ebene tiefer.** **BARRIERE NACH R9 (dritte Wiederholung derselben Fehlerklasse — improvisierter Massstab):** `scripts/statische-inline-stile.mjs` macht Definition und Ausnahmen ausfuehrbar und wird `population_command`. **Vorgezogen als Spur B** — es ist ein Messwerkzeug, kein Produktionscode, und **ohne Barriere waere Scheibe 5 der vierte Auftrag mit improvisiertem Massstab.** **Reihenfolge: erst das Skript, dann Scheibe 5 mit `population_command` und Sollwert 38.** · ~~ALTER AUFTRAGSTEXT~~ **`>>> AKTIVER AUFTRAG: SCHEIBE 5 (ConfigWizard) <<<` — der Nachzug 2-3-4 ist durch, alle drei berichtet oder abgenommen.** `ConfigWizard.tsx`, **43 Stellen** — Messung des Planners, keine Bedingung. **Sie ist ein echter modaler Dialog** (`role="dialog"`, `aria-modal`, Overlay `position: fixed`, `useDialogFokus`) **und hat mehrere Schritte**: K9 headful mit geoeffnetem Dialog reicht nicht, **die Schritte muessen durchgeklickt werden**, sonst bleibt der groesste Teil der Flaeche ungesehen. Ausloeser: Konfigurator aus dem Studio oeffnen, dann Schritt fuer Schritt. **Ab dieser Scheibe gilt `docs/agents/00-REGELWERK.md`** — mit Readiness-Quittung vor dem Bauen. ~~Nachzug Scheibe 2~~ erledigt:  — die letzte Nachzug-Datei, danach erst Scheibe 5.** Gemessen 20:49: **29 statisch von 36** — Messung des Planners, **keine Bedingung**. **Scheibe 2 ist bereits ABGENOMMEN** (`265cfe55`); die Freigabe wird nicht zurueckgenommen, sie laeuft als **Nachzug** mit demselben Kriterium. **SCHEIBE 4 NACHGEBESSERT und im Stapel** (`37094c5b`): 15 Klassen fuer 17 Stellen, von 27 bleiben 10 (acht dynamisch, zwei Rohwert ohne Token), Gegenprobe 1 rot, Insel 1300→**1302**. **Er hat meine Zahl korrigiert:** ich zaehlte 19 statisch, er misst **17** und benennt die zwei Abweichler (Navigationsspalte `navZu ? 66 : 266` = dynamisch · ein Navi-Eintrag `#3f464e` roh). *Genau so war es beauftragt — Zahlen sind Messungen, er misst nach und meldet.* **Die Wirkungs-Zusage gehoert ab jetzt von vornherein in jede Scheibe**, nicht erst nach einem Votum; das hat der Generator nach dem zweiten gleichen Fehler selbst entschieden. **ZELLE WIEDERHERGESTELLT (Planner, 20:55):** dieser Text war zwischen `f3b30206` und `60d410d9` von **15.691 auf 623 Zeichen** gefallen — die Zelle wurde durch eine Stapel-Zeile **ersetzt statt ergaenzt**, und mit ihr verschwanden Marke, Definition, Vorrang- und Zustandsflaechen-Regel. **Kein Vorwurf, ein Strukturbefund: zwei Schreiber, eine Zeile.**  **Scheibe 3 ist erledigt und in der Abnahme.** Gemessen 20:37 mit der Definition: `FachFlaeche` **2 statisch** (fertig) · `HausplanerStudio` **19 statisch von 27** (offen) · `StartView` **29 von 36** (offen). **AUFTRAGSSCHNITT KORRIGIERT (Planner, 20:37):** ich hatte *„ein Auftrag, drei Dateien“* geschrieben. Der Generator hat eine Datei geliefert und **„Ballbesitz: Evaluator“** gemeldet — beides ist vertretbar, und genau das ist der Fehler: **bei drei Dateien in einem Auftrag ist „fertig“ nicht definiert.** Ab jetzt gilt: **ein Auftrag = eine abnehmbare Einheit.** Die Aufteilung ist auch sachlich besser — drei Dateien auf einmal haetten den Pruefenden wieder mit einem Paket belastet, und der ist der Engpass. **Sonst unveraendert:** Definition (kein Ternaer, kein Bezeichner ausser `T`-Token), Zusage prueft die **Wirkung**, Ausnahmen nur **testverriegelt in beide Richtungen** mit benanntem Grund — so wie er es in der Nachbesserung gemacht hat. **Zahlen sind Messungen, keine Bedingungen.** Scheibe 3 hat `NACHBESSERN` (`198cf391`), Scheibe 4 ist ohne Pruefung angehalten, Scheibe 2 laeuft als Nachzug mit. **Ein Auftrag, drei Dateien, eine Definition:** in `StartView.tsx`, `FachFlaeche.tsx` und `HausplanerStudio.tsx` bleibt **keine statische Stelle** mehr — statisch = **kein Ternaer und kein Bezeichner ausser den Token aus `T`** (`T.muted` ist ein Token, kein Zustand). **Je Datei eine Zusage, die die WIRKUNG prueft** (*„keine statische Stelle mehr“*), nicht die Gestalt (*„diese elf Klassen existieren“*) — eine Gestalt-Zusage geht nie rot, wenn etwas **fehlt**. **Ausnahmen sind erlaubt, aber nur testverriegelt in beide Richtungen** — so wie er es beim Namenskuerzel schon gemacht hat; das ist die Form, die zaehlt. **Zahlen im Auftrag sind Messungen des Planers, keine Bedingungen** (grob gezaehlt: StartView 29/36, FachFlaeche 16/17, Studio 19/27) — **er misst beim Bauen selbst nach und berichtet seine Zahl.** · **`SCHEIBE 4 BERICHTET — wartet auf Evaluator`** (`HausplanerStudio.tsx`) — Code `5c94b68f` + Artefakte `71e1b1fc`; acht Klassen, Eigenschaft fuer Eigenschaft testverriegelt; von 34 Stellen bleiben 26. **Eine statische Stelle bleibt mit Begruendung inline** (zwei Farben ohne Token in `T`). Gates 0/0/0/0 + `test:dom` 0; Insel 1295 auf 1298, PHP 789; Mutation 1 rot · **`SCHEIBE 3 NACHGEBESSERT — wartet auf Evaluator`** (`FachFlaeche.tsx`) — Nachbesserung `01b9933e` + Artefakte `2798120f`; **von 17 Inline-Stilen bleiben 3**, alle drei mit benanntem Grund (Sperrstil-Modul bzw. Rohwert ohne Token). **Der Test prueft jetzt die WIRKUNG statt der Gestalt** — Gegenprobe: eine zurueckgedrehte Stelle ergibt 2 rot. Gates 0/0/0/0 + `test:dom` 0; Insel 1298 auf 1300, PHP 789. Erstbericht — Code `7da45f7c` + Artefakte `a7fc9f39`; elf Klassen, **Eigenschaft fuer Eigenschaft testverriegelt** gegen den vorherigen Inline-Wortlaut; von 27 Stellen bleiben 17 (Zustand/Messung). Gates 0/0/0/0 + `test:dom` 0; Insel 1292 auf 1295, PHP 789; Mutation 1 rot. **Die K9-Sichtprobe faehrt der Evaluator headful** (Blocker `3cc9a018` von ihm aufgeloest) (`FachFlaeche.tsx`, **kein Code angefasst**): der K9-Beleg braucht den **geoeffneten Dialog**, und der laesst sich nur im langsamen headful-Lauf oeffnen (headless rendert die Insel nicht — gemessen: null Reiter). **Ohne diesen Beleg waere die Wertgleichheit nur behauptet.** · **`SCHEIBE 2 BERICHTET`** (`StartView.tsx`) — Code `e862b8f2` + Artefakte `8ed190e3`; acht statische Stil-Objekte zu Klassen, Farben als `--hp-*`-Variablen, **null rohe Farbwerte in der CSS**. **Kriterium 9 buchstaeblich belegt: vorher/nachher in drei Viewports, ganzseitig sha256-verglichen — PIXELGLEICH.** Gates 0/0/0/0 + `test:dom` 0; Insel 1288 auf 1292, PHP 789; Mutation 2 rot. **Auftragszahl gewandert: 20 genannt, 34 gemessen** (AUF-56/66 haben dazugelegt) · `SCHEIBE 1 ABGENOMMEN` (Votum `ba47815`, FREIGABE); laeuft erst nach AUF-52, ein Bauender je Posten (§13) — Code `cca1837` + Artefakte `022021f` (`hausplaner.js` **und** die neu entstandene `hausplaner.css`, nach K8 im selben Commit). Gates 0/0/0/0 + `test:dom` 0; Insel 1246 auf 1256, PHP 789; Mutation 2 rot. **Kriterium 9 buchstaeblich belegt: dieselbe Seite mit und ohne die neue CSS, drei Viewports, Bildschirmfotos sha256-verglichen — PIXELGLEICH.** Keine Bau-, keine Blade-Aenderung noetig; **keine einzige `style`-Stelle umgestellt** (Scheiben 2-8 offen) **Er laeuft neben nichts** — acht Oberflaechendateien, darunter `HausplanerApp.tsx`. Auftrag liegt: **acht Scheiben**, Scheibe 1 (Grundgerüst) wird eigens abgenommen. **Scheibe 7 ist entsperrt** — AUF-35a ist abgenommen; sie bleibt trotzdem die letzte, weil `HausplanerApp.tsx` auch AUF-43/45/48 trägt **Marke 26.07., 23:20:** AUF-52 hat fertig gebaut, **Scheibe 2 ist frei und wird gezogen.** **Mit Yamas Entscheid fuer Weg B ist AUF-38 kein Aufraeumposten mehr, sondern der tragende:** die Token-Quelle, zu der der Rest ziehen soll. **Scheibe 2 (StartView) BERICHTET:** acht statische Objekte umgestellt, K9 pixelgleich in drei Viewports, Gates 1292/0. Scheiben 3-8 offen. **FEIERABEND (Yama, 26.07., 23:35): keine neuen Auftraege heute.** **Generator: Scheibe 3 wird NICHT gezogen** — Feierabend nach Scheibe 2. **Evaluator: AUF-38 Scheibe 2 ist die letzte Abnahme des Tages**, danach Schluss. Die Marke ist bewusst entfernt: **es gibt heute keinen aktiven Posten mehr.** **NACHTRAG 23:38 — Scheibe 3 wurde 23:35 gezogen, eine Minute VOR dem Feierabend-Eintrag. Das ist ein Wettlauf, kein Verstoss.** Regel dafuer: **ist noch nichts gebaut, leg sie zurueck** — `SCHEIBE 3 ZURUECKGELEGT` in diese Zelle, fertig. **Ist schon etwas gebaut, bau sie zu Ende und melde sie** — *eine halb umgestellte Datei ueber Nacht ist schlimmer als eine Scheibe mehr.* In beiden Faellen: **danach Schluss, Scheibe 4 wird nicht gezogen.** **TAGESABSCHLUSS 23:42:** **Scheibe 2 ABGENOMMEN** (Votum `265cfe55`, FREIGABE) — der Evaluator hat den kritischen Punkt von Hand am Diff geprueft, *weil kein Gate einen eingefrorenen Zustand faengt*: `cardBase`/`kicker`/`h1` sind Klassen, die hover/State-Stile bleiben inline. **Scheibe 3 ZURUECKGELEGT ohne Codeaenderung** — `FachFlaeche` ist ein **Dialog**, und der K9-Beleg braucht Bildschirmfotos mit **geoeffnetem** Dialog, also einen headful-Lauf; headless rendert die Insel nicht (gemessen: null Reiter). *Ohne ihn waere die Wertgleichheit nur behauptet.* **AUFLAGE fuer die Restscheiben (Planner, 26.07.): vor dem Ziehen pruefen, ob die Datei einen Dialog traegt — wenn ja, gehoert der headful-Lauf in den Auftrag, nicht in die Ueberraschung.** Scheiben 3-8 offen, **kein aktiver Posten.** **BLOCKER AUFGELOEST (Evaluator, 23:44, `3cc9a018`, kein Bau):** headful gemessen — die Insel rendert (Canvas + 3 Reiter), `FachFlaeche` geht auf. **Der K9-Beleg fuer Dialog-Scheiben ist machbar:** Generator baut, Evaluator faehrt die headful-Sichtprobe wie bei 55.1/56.1. **Das aendert nichts am Feierabend — Scheibe 3 wird heute nicht gezogen.** Der Weg liegt fuer morgen bereit, und damit ist die Auflage zur Pruefbarkeit schon beantwortet, bevor ich sie schreiben musste. **SCHEIBE 3 BERICHTET (27.07., 13:52):** elf Klassen — zwei konstante `React.CSSProperties`-Objekte und neun statische Inline-Stile; **von 27 Stellen bleiben 17**, sie tragen Zustand oder Messung. *Ziel ist null STATISCHE Inline-Stile, nicht null Inline-Stile.* **Kriterium 3 steht als Test statt als Tabelle im Bericht:** elf Klassen mit ihren Deklarationen im Wortlaut, der vorher inline stand, **plus** die Zusage, dass jede Klasse auch benutzt wird — keine Regel ins Leere. Mutation `21px`→`22px`: 1 rot. Null rohe Farbwerte. Gates 0/0/0/0, Insel 1292 auf **1295**, PHP 789. **Ballbesitz Evaluator, einschliesslich der headful-K9.** **VOTUM SCHEIBE 3: NACHBESSERN (Evaluator, 27.07., 19:08, `198cf391`) — das erste Nicht-Freigabe-Votum, und es ist berechtigt. DIE URSACHE IST MEIN AUFTRAG, NICHT SEIN BAU.** Der Evaluator hat nachgemessen: `FachFlaeche` traegt noch **17** `style={{`, davon nur **2** bedingt; belegbar statisch bleiben `EingangFeld`/`AusgangZeile` (Z.55/56/85/92 mit `T.muted/ink/faint`, dazu 64/79). **Der Test wird gruen, weil er die zwei benannten const-Objekte prueft — Gestalt statt Wirkung.** **Warum das mein Fehler ist:** *„statisch“* war im Auftrag **nie mechanisch definiert.** Der Generator las *„traegt Zustand oder Messung“* weit (alles mit `T.*`), der Evaluator eng (`T.x` ist ein Token, kein Zustand). **Beide haben nach ihrer Lesart recht gemessen.** Zwei ehrliche Messungen mit gegensaetzlichem Ergebnis heissen: die Definition fehlt, nicht die Sorgfalt. **DEFINITION AB SOFORT, mechanisch pruefbar:** ein `style={{…}}` ist **statisch**, wenn das Objektliteral **weder einen Ternaer (`?`) noch einen Bezeichner ausser den Token aus `T`** enthaelt. `T.muted` ist ein Token und macht eine Stelle **nicht** dynamisch. **DER TEST PRUEFT DIE WIRKUNG, NICHT DIE GESTALT:** nicht *„diese elf Klassen existieren“*, sondern *„in dieser Datei gibt es keine statische Stelle mehr“*. Eine Zusage, die nur die gebaute Gestalt festhaelt, geht nie rot, wenn etwas **fehlt**. **SCHEIBE 4 WIRD ANGEHALTEN, BEVOR SIE GEPRUEFT WIRD** (Vorrangregel von 18:15). Grobzaehlung des Planers mit derselben Definition: `HausplanerStudio` **19 statisch von 27**, `FachFlaeche` **16 von 17**. **Sie wuerde dasselbe Votum bekommen** — den Evaluator zweimal dasselbe finden zu lassen waere Verschwendung seiner knappsten Ressource. Sie geht **ohne Pruefung zurueck an den Generator** und wird mit Scheibe 3 zusammen nachgezogen. **UND DER UNANGENEHME TEIL: SCHEIBE 2 IST ABGENOMMEN UND ERFUELLT DAS KRITERIUM EBENFALLS NICHT.** Grobzaehlung: `StartView` **29 statisch von 36**. **Die Freigabe wird nicht zurueckgenommen** — sie war nach dem damals geltenden, unscharfen Kriterium korrekt, und ein rueckwirkend geaendertes Mass entwertet jede Abnahme. **Stattdessen wird sie als `SCHEIBE 2 NACHZUG` gefuehrt** und faellt zusammen mit 3 und 4 an. *Der Fehler war meiner; die Kosten traegt der Generator dreifach. Das gehoert benannt, nicht weggeraeumt.* **SCHEIBE 4 BERICHTET (27.07., 18:19):** acht Klassen, **von 34 Stellen bleiben 26**. **Eine statische Stelle bleibt bewusst inline** — das Namenskuerzel traegt `#dfe4ea`/`#5b636d`, zwei Farben **ohne Token in `T`**; in die CSS geholt waeren sie rohe Farbwerte und verletzten Kriterium 4, und einen Token zu erfinden waere ein Palette-Entscheid, der ihm nicht zusteht. **Testverriegelt in beide Richtungen:** Stelle noch inline **und** Farben weiterhin nicht in `T` — bekommen sie einen Token, faellt der Test und die Stelle gehoert umgestellt. *Das ist die sauberste Form, eine offene Entscheidung zu tragen: sie meldet sich selbst, wenn sie faellig wird.* K3 wieder als Test. Mutation `62px`→`60px`: 1 rot. Gates 0/0/0/0, Insel 1295 auf **1298**, PHP 789. **Fuenfte geerbte Zusage desselben Bautyps nachgezogen** (AUF-46): sie las den **Inline-Stil** statt die Eigenschaft dort, wo sie wohnt. **BEFUND DARAUS (Planner): fuenfmal derselbe Bautyp ist kein Zufall, sondern eine Eigenschaft des Bestands.** Die Restscheiben werden weitere solche Zusagen umwerfen. **Das gehoert ab Scheibe 5 in den Auftrag, nicht in die Ueberraschung:** vor dem Bau die Zusagen suchen, die den Inline-Stil der betroffenen Datei lesen, und sie im selben Commit mitziehen. **AUFLAGE FUER SCHEIBE 5 (`ConfigWizard.tsx`, 43 Stellen — Planner, 27.07.):** sie ist ein **echter modaler Dialog** (`role="dialog"`, `aria-modal`, Overlay `position: fixed`, `useDialogFokus`) **und hat mehrere Schritte**. **K9 headful mit geoeffnetem Dialog reicht nicht — die Schritte muessen durchgeklickt werden**, sonst bleibt der groesste Teil der Flaeche ungesehen. Ausloeser benannt: Konfigurator aus dem Studio oeffnen, dann Schritt fuer Schritt. **AUFLAGE FUER SCHEIBE 4 praezisiert (Planner, 27.07.):** naechste ist `HausplanerStudio.tsx`. Sie traegt **keinen Dialog** — aber `Z202` einen **Toast** (`position: 'fixed'`, nur sichtbar wenn `toast` gesetzt ist). **Dieselbe Falle wie der Dialog, nur unauffaelliger:** ein Standard-Screenshot zeigt ihn nie, ein eingefrorener Stil dort faellt in keinem Gate und in keiner Sichtprobe auf. **Die Auflage heisst ab jetzt nicht mehr „traegt die Datei einen Dialog“, sondern „traegt die Datei eine Flaeche, die nur unter Zustand erscheint“** — Dialog, Toast, Aufklappmenue, Fehlerband. Wenn ja, gehoert der Ausloeser in den Auftrag. Beim Toast ist er benannt: Konfigurator oeffnen, uebernehmen, Toast erscheint. ~~**Scheibe 4 wird NICHT gezogen, solange Scheibe 3 in der Abnahme liegt**~~ **AUFGEHOBEN (Planner, 27.07., 18:15) — mein Konstruktionsfehler, nicht sein Ungehorsam.** Der Generator zieht nach §15, indem er die **Marke** liest; die Marke stand auf `⚡ AKTIV`. Meine Sperre stand als **Satz im Fliesstext** derselben Zelle. **Zwei widersprechende Signale am selben Posten — und das lautere gewinnt.** Eine Sperre, die nicht in der Marke steht, ist keine Sperre. **Regel daraus: wer sperren will, nimmt die Marke weg oder setzt sie auf `GESPERRT`. Fliesstext sperrt nicht.** **Sachlich war sein Zug ausserdem der richtige:** Scheibe 3 lag zu dem Zeitpunkt **vier Stunden zwanzig** beim Evaluator ohne Votum. Meine Sperre haette bedeutet, dass **beide** Bauenden auf einen Pruefenden warten, der nicht antwortet. **Der Engpass ist die Abnahme, nicht das Ziehen.** **Es bleibt ein gemessenes Restrisiko:** Scheibe 3 und Scheibe 4 fassen **dieselbe** `hausplaner.css` und **denselben** `stilschicht.test.ts` an. Kommt Scheibe 3 rot zurueck, hat der Generator zwei Baustellen in einer Datei. **Regel dafuer (statt der Sperre):** faellt Scheibe 3 rot, **hat die Nachbesserung Vorrang** — Scheibe 4 wird angehalten, nicht weitergebaut. **Scheibe 4 laeuft zu Ende** (dieselbe Regel wie beim Wettlauf am 26.07.: was schon gebaut ist, wird fertig gebaut) — jede Scheibe schreibt in **dieselbe** `hausplaner.css`; ein rotes Votum zu Scheibe 3 waehrend Scheibe 4 laeuft ist genau die Kollision, die der Einspurbetrieb verhindert. **NEUER TAG, 27.07.: Scheibe 3 ist wieder gezogen — mit dem Weg, den der Evaluator gestern gemessen hat.** `FachFlaeche.tsx`, nur die **statischen** Stil-Objekte, wie in Scheibe 2. **Der K9-Beleg laeuft headful mit geoeffnetem Dialog** (Evaluator-Messung `3cc9a018`: Insel rendert, `FachFlaeche` geht auf) — er gehoert in den Bericht, nicht in die Ueberraschung. **Probe des Erprobers:** *Er oeffnet die Fachplaner-Flaeche und sieht dieselbe Ansicht wie vorher — kein Pixel verschoben, und der Schwebezustand reagiert weiter.* **Grenze wie in Scheibe 2:** was aus Zeiger, Zustand oder Messung kommt, bleibt inline. **Keine Zahl als Bedingung:** wie viele Stellen es sind, wird **gemessen und berichtet**, nicht vorgegeben — die 20-gegen-34-Abweichung aus Scheibe 2 war mein Fehler, nicht seiner. | `generator-auftrag-auf38-inline-styles.md` + **`generator-auftrag-auf38-scheibe3.md`** (Blatt fuer Scheibe 3, 27.07.) |
| **AUF-48** | **`HausplanerApp.tsx` zerlegen** (externe Bewertung Nr. 3, nachgemessen: **2.052 Zeilen**) — bündelt Canvas, Werkzeugleisten, Auswahl, Eigenschaften, Dach, Treppe, Palette, Tastatursteuerung und Layout in einer Datei. Sie trägt zugleich **132 der 331 Inline-Styles** (AUF-38) und ist die Datei, an der heute jeder zweite Posten arbeitet — Kollisions- und Regressionsrisiko | Planner → Generator | `GESPERRT` — **bewusst, nicht aus Bequemlichkeit:** ein Schnitt durch die meistberührte Datei des Projekts während laufender Posten (AUF-33, AUF-38, AUF-43, AUF-45) erzeugt genau die Kollision, die AUF-22 verhindern soll. Erst wenn die Layout-Posten durch sind. Planner schreibt dann den Schnitt **Yama, 26.07.: AUF-48 laeuft VOR AUF-50.** Beide fassen `HausplanerApp.tsx` tief an, gleichzeitig geht nicht; die Werkzeug-Schicht (50.1a) baut danach auf zerlegten Dateien. **AUFLAGE (Planner, 26.07., Entscheid von Yama delegiert):** beim Zerlegen wird das **Dachform-Auswahlfeld** (`HausplanerApp.tsx:1884`) mitgenommen — es wird aus `geometry/dachformVorlagen.ts` gespeist statt aus der festen 8er-Liste. **Probe des Erprobers:** *Er oeffnet die Dachform-Auswahl und findet dort die fertigen Vorlagen mit Deckung und Neigung — „Sattel Schiefer steil", „Pult Blech", „Flach Gruendach" — statt acht nackter Formnamen.* **Grenze:** nur Vorlagen, deren `shapeKey` einer der acht `RoofShape`-Werte ist; die 14 nicht speicherbaren Formen bleiben aussen vor, bis Yama ueber die Schema-Erweiterung entschieden hat. **Gemessen:** 72 Vorlagen, 22 Dachformen, 8 darstellbar. **Vorbehalt aufgeloest (26.07.): Yama folgt der Empfehlung — Weg B.** Die Auflage gilt; gebaut wird gegen die Token-Quelle des Planers. **Die Sperre bleibt** — sie sagt *„erst wenn die Layout-Posten durch sind"*, und AUF-38 hat sieben offene Scheiben. | Ledger „Externe Frontend-Bewertung" (25.07.) |
| **AUF-50** | **Die 110 Werkzeuge funktionstüchtig machen — Fahrplan in vier Stufen** (Yama, 25.07.: „sollen wir jetzt coden funktionstüchtig machen"). **Gemessen:** die Verträge nennen **110 verschiedene** `commandId`, das Modell kennt **19** Command-Typen — die IDs sind Absichtserklärungen, keine Zeiger. Aufteilung: **41 rein Ansicht/Auswahl** (kein Command, kein Schema) · **69 modellverändernd**, davon `create` **40** (je ein Knoten-/Objekttyp ⇒ **Zod + `schema:hausplaner` + Bestandsdaten**), `modify` **20**, `workflow` **15**, `assign-or-calculate` **9** (hängt an den Engines, AUF-33 L3), `import` **8** (**hängt an AUF-41**, Yamas Rechte-Entscheidung), `view` 7 · `measurement` 5 · `selection` 4 · `domain` 2. **Yamas Schnitt-Entscheidung 25.07.: nach Aufwand, innerhalb seiner 15 Bereiche** — erst die 41 ohne Modellwirkung, dann `modify`, dann `create` mit Schema-Arbeit, `import`/`assign-or-calculate` zuletzt. **Nicht Bereich 1–15 der Reihe nach**, weil die Bereiche quer zum Aufwand liegen | Planner → Generator | `OFFEN OHNE AUFTRAG` — **Sperre gefallen, gemessen 26.07., 20:25:** die Bedingung lautete *erst Layout fertig* (AUF-39 · 43 · 45 · 44 · 47) — **alle fuenf liegen im Archiv.** **Es fehlt der Stufenplan, und den schreibt der Planner.** Die Zaehlung liegt seit 20:45 (`docs/planner/bestandsaufnahme-auf50-werkzeuge-2026-07-26.md`): **7 von 101 Werkzeugen haben einen Empfaenger, 94 nicht**; die 110 `commandId` sind **Metadaten, keine Aufrufe** (19 echte Command-Typen an 34 Aufrufstellen). Vier Stufen: **50.1 generischer Empfaenger** · 50.2 `create` (40) · 50.3 `modify`+`selection` (24) · 50.4 `view`+`measurement` (12). **Umfang ~78 statt 110** — `import` gehoert in Phase 2, `assign-or-calculate` an AUF-52. Planner schreibt danach den Stufenplan; jede Stufe wird ein eigener Posten, nicht dieser hier | Ledger „Werkzeuge funktionstüchtig — die gemessene Lücke" (25.07.) |

---

### 3b. Abnahme-Stapel — berichtet, wartet auf Pruefung

Niemand nimmt eigene Arbeit ab (§1.4).

| Nr | Berichtet | Commits | Was der Evaluator zuerst prueft | Ballbesitz |
|---|---|---|---|---|
| **AUF-38** | **Nachzug Scheibe 2** (`StartView.tsx`), 28.07. 23:37 | `5382cb3a` · Artefakte `a2a83e72` | **Zuerst wieder seine Zahlenkorrektur:** Auftrag nannte 29 statische, er zaehlt **28** — Abweichung ist `Z138`, `style={{ ...grund, cursor: 'default' }}`, und `grund` haengt an `dominant`, ist also dynamisch. **Zweite Scheibe in Folge, in der er die Planner-Zahl richtigstellt.** Dann: 22 Klassen fuer 28 Stellen (sechs Bloecke mehrfach), von 36 bleiben **8**. Geerbte AUF-40-Zusage nachgezogen (las `cursor: 'default'` inline, prueft die Eigenschaft jetzt dort wo sie wohnt). Gates 0/0/0/0, Insel 1302 auf **1305**, PHP 789. **Pruefen:** ob die 8 verbliebenen je einen Grund tragen, und **K9 headful** — StartView ist die erste Flaeche, die ein Kunde sieht | **Evaluator** |
| **AUF-38** | **Scheibe 4 NACHGEBESSERT** (`HausplanerStudio.tsx`), 27.07. 22:45 | `37094c5b` · Artefakte `0d5cd975` | **Zuerst seine Zahlenkorrektur pruefen:** Planner zaehlte 19 statisch, er misst **17** — Navigationsspalte ist `navZu ? 66 : 266` (dynamisch), ein Navi-Eintrag traegt `#3f464e` roh (gehoert nach K4 nicht in die CSS). **Wer nachmisst, findet 17.** Dann: 15 Klassen fuer 17 Stellen (zwei Paare wortgleich), von 27 bleiben 10 — acht dynamisch, zwei Rohwert. Gegenprobe: eine zurueckgedrehte Stelle ⇒ 1 rot. Dann **K9 headful mit ausgeloestem Toast** (Konfigurator oeffnen, uebernehmen) | **Evaluator** |


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
