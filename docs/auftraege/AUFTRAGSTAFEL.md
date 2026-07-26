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

**Generator:** 1. **AUF-78** (⚡) · 2. **AUF-79** · 3. **AUF-81** · 4. **AUF-66** · 5. **AUF-76** ·
6. **AUF-77** · 7. **AUF-54/55/56** · 8. **AUF-63** (allein — er ändert den Testläufer selbst).

**Evaluator:** A. Wächter-Blindstelle (Messung) · B. Abnahme AUF-78 · C. Abnahme AUF-79 ·
D. Abnahme AUF-81 · E. Abnahme AUF-66 · F. Abnahme AUF-76 · G. Abnahme AUF-77.

**Leerlauf wird gemeldet, nicht überbrückt** (§13.3): Staffel leer → eine Zeile in den Ledger,
dann warten. Nichts aus dem Vorrat ziehen, was nicht in der Staffel steht.

| Nr | Auftrag | Rolle | Status | Datei / Beleg |
|---|---|---|---|---|
| **AUF-40** | **L6 — Start/Zuletzt an echte Projekte + Konfigurator-Paket serverseitig speichern.** Gemessen offen: `app/StartView.tsx:75` rendert die Demo-Liste `ZULETZT`, `app/ConfigWizard.tsx` lädt das `ConfiguratorPackage` als JSON **herunter**, statt es zu speichern. Hatte bis 25.07. ebenfalls **keinen Posten** (null Treffer auf `L6`). Braucht Backend-Anschluss, blockiert kein Layout **Zielbild aus der UX-Bewertung 26.07.:** je Eintrag Projektname · letzter Zeitpunkt · aktueller Schritt · offene Aufgaben · Speicherstatus · **direkte Fortsetzen-Aktion**. | Yama | **Teil A `ABGENOMMEN`** — FREIGABE (`865a545`) fuer `4cc9f6e` + Bundle `10f7dd7`. Die drei erfundenen Projekte sind **stillgelegt statt geloescht** (Muster `toolCatalogStillgelegt`/`STEPS_STILLGELEGT`) und rendern nichts; ehrlicher Leerzustand statt Beispielzeilen; **drei Karten, drei Ziele** — die zwei ohne Ziel sind **keine Schaltflaeche mehr** (keine Rolle, kein Fokus, kein Zeiger), sonst waeren sie fokussierbar und taeten nichts. Tests 1082 auf **1091**; Sichtprobe: **0 erfundene Namen**, wo sie vorher in **jedem** Screenshot standen. **Rest `GESPERRT` — wartet auf Yama:** (1) die **Zulieferung der echten Projektliste** (eine Variable im Controller, **Tor 1**) — zurueckgegeben statt nebenbei gebaut, siehe `W-Projektliste`; (2) **Teil B** (Konfigurator-Persistenz) ist von Yama am 26.07. **vertagt**, stattdessen laeuft AUF-74 | `generator-auftrag-auf40-start-und-persistenz.md` |
| **AUF-81** | **Konfigurator-Pakete serverseitig** (B7 / AUF-40 Teil B) — **Tor 1 von Yama freigegeben, 26.07.: *wir brauchen Datenbank, Migration, Routing, Pagination*.** Er dreht seine Vormittags-Entscheidung („erst den Satz ehrlich machen") bewusst weiter. Neue Tabelle nach dem **vorhandenen** Migrations-Muster (idempotent, defensiver Fremdschluessel, json, MySQL-Semantik); drei Routen (speichern mit `Hausplaner,add`, Liste **paginiert** und Einzelansicht mit `Hausplaner,read`); `alternative_id` **nullable**, weil der Konfigurator autark laeuft | Generator | `OFFEN` — **Sperre gefallen 26.07., 13:32: der Merge ist durch** (`main` = `f9c837e`, gepusht auf `fork` und `backup-private`). Der Sperrgrund war: Merge-Bedingung 5 lautet **keine Migration**; dieser Posten bringt die erste und macht aus einem reinen Code-Deploy einen mit Datenbank-Anteil. **Wer beides mischt, kann bei einem Fehler nicht sagen, welche Haelfte ihn verursacht hat.** **Sicherheitseigenschaft, auf der alles ruht: neue Tabelle, keine bestehende angefasst** — verlangt die Umsetzung etwas anderes, wird gemeldet, nicht gebaut. Wichtigstes Kriterium: **das Eigentumsgatter** (Nutzer B sieht A's Paket nicht, serverseitig gefiltert). Dazu: **Rueckweg ausgefuehrt, nicht behauptet** — migrate, rollback, migrate mit Ausgabe | `generator-auftrag-auf81-konfigurator-persistenz.md` |
| **AUF-78** | **Die Projektliste erreicht den Startbildschirm** — Rueckgabe aus AUF-40 Teil A. **Tor 1 von Yama an den Planner delegiert** (26.07.: *das kannst du selber entscheiden und die Verantwortung uebernehmen; es darf dadurch kein Fehler passieren*). **Entscheidender Fund: die Naht existiert bereits** — `HausplanerController::index()` fuehrt die Liste seit Langem in Produktion, eager geladen, paginiert, hinter `permission:Hausplaner,read`. Es wird **keine** Abfrage erfunden und **kein** Zugriffsweg geoeffnet | Generator | `⚡ AKTIV` — **Sperre gefallen 26.07., 13:32: der Merge ist durch und beide Suiten sind gezaehlt** (769 PHP, 1102 Insel, beide gruen auf `f9c837e`). Der Sperrgrund war eine Reihenfolge: der Evaluator fuhr die **volle PHP-Suite gegen den damaligen HEAD**. **AUF-78 fasst `app/Http` an** — committet er waehrenddessen, misst die Suite einen Stand, den es nicht mehr gibt (§10.3: *Messwerte aus einem wandernden Baum sind keine Messwerte*). **Deshalb laeuft zuerst AUF-58** (nur `.gitignore`, kein PHP, kein `app/`, keine Insel — der Suite-Lauf bleibt gueltig). Inhalt und Kriterien unveraendert; es ist eine Reihenfolgeentscheidung, keine inhaltliche **Spur A.** **Die eine Fehlerquelle ist gemessen und verriegelt:** die **Studio-Route traegt nur `auth`**, nicht das Hausplaner-Recht — wer die Liste dorthin durchreicht, zeigt sie **jedem angemeldeten Nutzer**. Deshalb verbindlich: **ausschliesslich in `seite()`**, `studio.blade.php` und die Studio-Route unberuehrt, dort bleibt der Grundwert leer. Kriterium 1 prueft es, Kriterium 11 verriegelt es per Mutation. Dazu: **nur die Felder, die StartView anzeigt** (keine Kundendaten vorsorglich), **eine** Abfrage mit harter Grenze (kein N+1 bei 3 000 Objekten), **kein `@php`-Block im Blade** (AUF-64), Leerzustand aus Teil A bleibt Zeichen fuer Zeichen | `generator-auftrag-auf78-projektliste.md` |
| **AUF-79** | **Der Fortschritt schreibt sich selbst** (Yama, 26.07.: *kannst du das nicht auf automatisch einstellen*). Eine Seite `docs/fortschritt.html`, **nach jedem Waechter-Lauf neu geschrieben** — gezaehlt aus **genau zwei Dateien**, Tafel und Archiv. **Kein zweiter Mechanismus:** der Ausloeser existiert seit AUF-75 und laeuft nach jedem Commit. **Keine Uhr** — die Zahlen aendern sich mit Commits, nicht mit der Uhrzeit; und eine Cloud-Aufgabe erreicht dieses Repository nicht und meldete bei jedem Lauf „Bruecke offline", **eine Automatik die immer scheitert ist schlechter als keine** | Generator | `OFFEN` — **Sperre gefallen: AUF-75 ist abgenommen**, die Auflage AUF-75.1 mit AUF-80 (`1a25533`) geschlossen; es baut darauf auf. **Spur B.** **Keine zweite Buchfuehrung:** die Tafel ist die Wahrheit, die Seite ihre Darstellung. Kriterium: **die Summe geht auf** — abgenommen + aktiv + in Pruefung + offen + gesperrt = Gesamtzahl aller AUF-Zeilen; stimmt sie nicht, wurde eine Zeile verschluckt, **und eine Fortschrittsanzeige die Posten verschluckt ist schlimmer als keine**. Nicht nach `public/` — die Seite hat auf einem Kundensystem nichts verloren | `generator-auftrag-auf79-fortschritt-automatisch.md` |
| **AUF-76** | **Die Wand bekommt ihre Schichten** (Mengenermittlung **M0**) — **Tor 1 von Yama freigegeben, 26.07.** Gemessen: `WallNode` traegt **eine einzige** `thickness` und keine Schichtenliste, waehrend `CeilingNode.schichten[]` bereits existiert; `wandaufbau.ts` kennt `Schicht` mit Dicke und Lambda, **aber die Schichten haengen an keiner Wand**. **Damit ist „fertig“ fuer Waende heute nicht berechenbar** — und Yama hat entschieden, dass **beide** Bezugsmasse gefuehrt werden | Generator | `OFFEN` — **Spur A, klein.** Ein optionales Feld, **feldgleich mit der Decke**; vierter Fall desselben additiven Musters nach `roofs`, `ceilings`, `aufbauten`. **Keine Migration, keine Vorbelegung** — eine erfundene Schichtung waere schlimmer als keine, sie saehe aus wie eine Angabe. Kriterien: Bestandsdokument **ohne** das Feld bleibt gueltig (kein 422), Rundlauf mit Schichten, `dickeMm` ganzzahlig > 0, **kein persistierter Wert umbenannt** | `generator-auftrag-auf76-wandschichten.md` |
| **AUF-77** | **Wandflaeche brutto und netto** (Mengenermittlung **M1**) — die eine fehlende Rechnung, auf der Putz, Daemmung, Anstrich, Fassade und Heizlast **alle** aufsetzen. Gemessen: `grep` auf `wandflaeche`/`nettoflaeche`/`bruttoflaeche` ueber die ganze Insel = **kein Treffer**. **Die Oeffnungen liegen im Modell, aber niemand zieht sie ab, weil es keine Wandflaeche gibt** | Generator | `GESPERRT` — **bis AUF-76 abgenommen ist.** **Spur A.** Neue reine Datei `geometry/wandFlaeche.ts`, vorhandene Dateien dort null Zeilen. **Kernkriterium: kein Ergebnis ohne Bezugsmass** — ein Rueckgabewert ohne die Angabe `roh`/`fertig` muss ein **Typfehler** sein, keine Nachlaessigkeit. **Kein Uebermessen** (der volle Abzug ist eindeutig; die Regel gehoert nach M2). **Fuenf Meldefaelle liefern keine Zahl, sondern eine Meldung** — darunter zwei ueberlappende Oeffnungen, der haeufigste Fehler solcher Systeme. **Eine** Rundungsstelle, per `grep` belegt | `generator-auftrag-auf77-wandflaeche.md` |
| **AUF-66** | **Ein Klick zurück in die Arbeit** (UX-Bewertung 26.07., P1) — der Einstieg zeigt heute drei gleichwertige Karten, die **alle zum selben Schritt führen** (gemessen: `onGuided(1)`), und darüber „Neues Vorhaben". **Es fehlt die eine dominante Handlung: „Letztes Projekt fortsetzen."** Vorgeschlagenes Modell aus der Bewertung: **1. Weiterarbeiten** (Projekt, Schritt x von 11, offene Aufgaben, ein Knopf) · **2. Nächste Aufgaben** · **3. Neu starten**. Damit wird aus dem Launcher ein Arbeits-Dashboard. **Messbares Ziel:** bestehendes Projekt fortsetzen in **höchstens einem Klick** | Planner → Generator | `GESPERRT` — **hinter AUF-40 Teil A**: „fortsetzen" braucht echte Projekte; solange die Liste Demo-Daten trägt, führt der Knopf ins Beispiel **Sperrgrund nachgezogen 26.07.:** AUF-40 **Teil A ist abgenommen**; was jetzt fehlt, ist die **Zulieferung der echten Liste** — also **AUF-78**, nicht mehr Teil A. Ohne echte Projekte hat „fortsetzen" nichts, was es fortsetzen koennte | UX-Bewertung 26.07., „Empfohlenes Dashboard-Modell" |
| **AUF-67** | **Die Befehlspalette wird globale Navigation** (UX-Bewertung 26.07., P1) — sie funktioniert (⌘K, Gründe bei gesperrten Befehlen, Kürzel, Sofortsuche) und **erfasst heute nur Werkzeuge**. Sie soll auch Projekte, Geschosse, Wizard-Schritte, Fachplaner, offene Prüfungen und Ansichten finden — „Dach" führt dann zum Dachwerkzeug **und** zum Dachschritt **und** zu vorhandenen Dachobjekten. **Dazu die Lücke aus AUF-49:** Fokusfalle und Fokus-Rückgabe fehlen für **diese** Palette noch (die drei Dialoge sind versorgt) | Generator | `GESPERRT` — **eine Quelle je Art**: die Palette darf nichts eigenes wissen, sie fragt die vorhandenen Register ab. Erst wenn AUF-65 die Aufgaben liefert, gibt es etwas zu finden | UX-Bewertung 26.07., Abschnitt 5 |
| **AUF-63** | **jsdom für den Testlauf** (Bestandsmeldung des Evaluators, 26.07., Frage 3) — AUF-30 hat `.tsx` im Testlauf übersetzbar gemacht, **jsdom fehlt aber weiter**. Folge, von ihm benannt: **Fokusfalle und `getComputedStyle` sind nur im Browser messbar, nicht im Gate.** Nicht blockierend — die iframe-Sichtprobe trägt — aber jedes Fokus- und Zustands-Kriterium hängt damit an einer Person mit offenem Browser statt an einem Testlauf. **Zu prüfen ist auch, ob es sich lohnt:** jsdom ist eine Abhängigkeit mehr, und die Testumgebung läuft heute bewusst schlank | Planner → Generator | `OFFEN` — **Auftrag liegt, Preis vorher gemessen.** jsdom kann **Fokus, `getComputedStyle` und Tastatur** — aber `getBoundingClientRect().width` ist dort **0**: **keine Layout-Engine.** Alle Geometrie bleibt Browser-Sache. Preis 39 Pakete / 27 MB (happy-dom 9 / 23 MB als zulässige Alternative). **Der DOM-Testlauf setzt seine Grenze selbst durch** | `generator-auftrag-auf63-jsdom.md` |
| **AUF-54** | **Farbe als Parameter statt in `geometry/`** (Entscheidung Yama, 25.07., aus AUF-5 und AUF-10) — `geometry/treppeSvg.ts` führt heute sechs rohe Farbwerte, darunter `#93c21c` für die Lauflinie, und wird an **neun Stellen** ohne Farbparameter aufgerufen. Die Farbe wird künftig hereingereicht; `studioDaten.ts` bleibt die einzige Quelle. **Wert- und verhaltenstreu:** der gerenderte Farbwert bleibt zunächst derselbe, nur seine Herkunft ändert sich | Planner → Generator | `OFFEN` — Auftrag liegt. `treppeSvg.ts` führt sechs rohe Werte, neun Aufrufstellen ohne Farbparameter. **Kein gerenderter Wert ändert sich** — nur seine Herkunft. Vorarbeit | `generator-auftrag-auf54-55-56-klein.md` |
| **AUF-56** | **Zwei Elevation-Token einführen** (Entscheidung 25.07., aus AUF-23) — `studioDaten.ts` kennt keine Elevation-Rolle. Gemessen: `rgba(28,40,48,.05)` **9×**, `rgba(28,50,55,.10)` **3×**. Zwei Token lösen diese zwölf Rohwerte **wertgleich** ab. **Ausdrücklich ausgenommen:** die rund acht Werte, die nur „nah dran" an vorhandenen Token liegen — sie anzugleichen wäre eine **sichtbare** Farbänderung und bleibt Yamas Entscheidung | Generator | `OFFEN` — Auftrag liegt. Zwölf Vorkommen der zwei häufigsten Schattenwerte werden wertgleich abgelöst; die acht „nah dran"-Werte **nicht** — das bleibt Yamas Entscheidung. Vorarbeit | `generator-auftrag-auf54-55-56-klein.md` |
| **AUF-55** | **Snapshot-Fläche ehrlich ausweisen** (Entscheidung 25.07., aus AUF-13) — das Blade setzt `data-snapshots-url`, drei Routen existieren, `main.tsx` liest **null** Verweise. Bis die Anbindung kommt, wird die Fläche als `in Entwicklung` gekennzeichnet — mit demselben `ZustandBadge` und demselben Blindtext-Verbot wie die Fachplaner-Flächen aus AUF-25. **Die tote URL im Blade bleibt stehen** und wird nicht entfernt: sie ist die Naht, an der die spätere Anbindung ansetzt | Planner → Generator | `OFFEN` — Auftrag liegt. **Die tote URL im Blade bleibt stehen** (Naht für die spätere Anbindung); `routes/` und `views/` null Zeilen Diff. Sichtbar | `generator-auftrag-auf54-55-56-klein.md` |
| **AUF-52** | **L3 — die übrigen zwölf Rechen-Engines nach dem Treppen-Muster** (`engine-fbh · -heizkoerper · -heizkreis · -abwasser · -kueche · -pv · -uwert · -fensterprodukt · -sparren · -holzmengen · -holzbauteile · -schifter`). Das Muster ist abgenommen (AUF-33 L2, Votum `7293a2d`): `EnginePanel → EngineFlaeche → Fachflächen-Hülle → vorhandene Engine`. **Gruppenweise, nicht alle zwölf auf einmal** — Vorschlag: `dach-zimmerei` zuerst (Sparren · Holzmengen · Holzbauteile · Schifter, vier mit Render-Bezug), dann Haustechnik, dann der Rest. **Die drei Grenzen aus AUF-33 §3 gelten unverändert:** keine Rechenlogik im Panel, kein dynamischer Import, kein Schreiben ins Modell. Jede Engine schaltet erst auf `verfuegbar`, wenn sie wirklich angeschlossen ist | Planner → Generator | `OFFEN` — Auftrag liegt: **drei Scheiben** (dach-zimmerei 4 · tga-heizung 3 · Rest 5), jede einzeln abgenommen. Gemessen: **alle zwölf Module existieren**. Ausdrücklich erwartet, dass nicht alle zwölf anschließbar sind — begründete Rückgabe ist ein gültiges Ergebnis | `generator-auftrag-auf52-l3-zwoelf-engines.md` |
| **AUF-35b** | **Flächen- und Zonenauswahl** — Wandseite und einzelne Dachfläche greifen (fehlt vollständig); Zone markieren für die **6** Schema-Typen, von denen das Paket nur `raum` kennt und die Commands **keinen**. **Die Naht zu den 13 Engines.** Braucht AUF-35a als Fundament | Planner → Generator | `OFFEN` — **Auftrag liegt, Posten geschnitten.** Zonen fallen weg: kein Zone-Command, keine Darstellung — **man kann nicht auswählen, was nicht dargestellt wird.** Kern ist die fehlende **Teil-Identität**: Auswahl ist heute knotenweise (`userData.nodeId`), die zwei Wandseiten sind implizit, `surfaceId` lebt nur **innerhalb** von `dachAusschnitt.ts`. Gebaut wird eine **abgeleitete** Teil-Kennung in der App-Schicht — **kein Schema, kein Command, keine Persistenz** | `generator-auftrag-auf35b-flaechenauswahl.md` |
| **AUF-38** | **Inline-Styles ablösen** (Entscheidung Yama, 25.07., aus AUF-14) — 331 `style={{` in 35 Dateien wandern in eine echte Stilschicht; `build:hausplaner` erzeugt künftig `public/hausplaner/hausplaner.css`, die vorhandene `@if (file_exists(…))`-Bewachung in beiden Blades greift dann von selbst. **In Scheiben, nach Datei geschnitten** (HausplanerApp 132 · GuidedView 41 · ConfigWizard 39 · HausplanerStudio 34 · FachFlaeche 27 · StartView 20 · Rest 38) — nie zwei Scheiben gleichzeitig, weil dieselben Dateien der Werkzeugleiste gehören. **Wert- und verhaltenstreu:** kein gerenderter Farbwert ändert sich, `studioDaten.ts` bleibt die Quelle | Planner → Generator | `OFFEN` — Auftrag liegt: **acht Scheiben**, Scheibe 1 (Grundgerüst) wird eigens abgenommen. **Scheibe 7 ist entsperrt** — AUF-35a ist abgenommen; sie bleibt trotzdem die letzte, weil `HausplanerApp.tsx` auch AUF-43/45/48 trägt | `generator-auftrag-auf38-inline-styles.md` |
| **AUF-42** | **`viewport.ready` ist heute eine Vereinfachung** — die Fähigkeit wird gesetzt, sobald `HausplanerApp` rendert; einen echten Renderer-Bereitschaftszustand führt der Store nicht. Der Generator hat das bei AUF-36 offengelegt statt es für gemessen auszugeben. **Folgenlos, solange es keine Ladeanimation gibt** — der Posten existiert, damit die Vereinfachung nicht später für eine Messung gehalten wird | Planner → Generator | `GESPERRT` — klein, ohne Dringlichkeit; erst wenn ein Ladezustand sichtbar wird | Ledger „GENERATOR-BERICHT AUF-36", Rückgabe 3 |
| **AUF-48** | **`HausplanerApp.tsx` zerlegen** (externe Bewertung Nr. 3, nachgemessen: **2.052 Zeilen**) — bündelt Canvas, Werkzeugleisten, Auswahl, Eigenschaften, Dach, Treppe, Palette, Tastatursteuerung und Layout in einer Datei. Sie trägt zugleich **132 der 331 Inline-Styles** (AUF-38) und ist die Datei, an der heute jeder zweite Posten arbeitet — Kollisions- und Regressionsrisiko | Planner → Generator | `GESPERRT` — **bewusst, nicht aus Bequemlichkeit:** ein Schnitt durch die meistberührte Datei des Projekts während laufender Posten (AUF-33, AUF-38, AUF-43, AUF-45) erzeugt genau die Kollision, die AUF-22 verhindern soll. Erst wenn die Layout-Posten durch sind. Planner schreibt dann den Schnitt | Ledger „Externe Frontend-Bewertung" (25.07.) |
| **AUF-50** | **Die 110 Werkzeuge funktionstüchtig machen — Fahrplan in vier Stufen** (Yama, 25.07.: „sollen wir jetzt coden funktionstüchtig machen"). **Gemessen:** die Verträge nennen **110 verschiedene** `commandId`, das Modell kennt **19** Command-Typen — die IDs sind Absichtserklärungen, keine Zeiger. Aufteilung: **41 rein Ansicht/Auswahl** (kein Command, kein Schema) · **69 modellverändernd**, davon `create` **40** (je ein Knoten-/Objekttyp ⇒ **Zod + `schema:hausplaner` + Bestandsdaten**), `modify` **20**, `workflow` **15**, `assign-or-calculate` **9** (hängt an den Engines, AUF-33 L3), `import` **8** (**hängt an AUF-41**, Yamas Rechte-Entscheidung), `view` 7 · `measurement` 5 · `selection` 4 · `domain` 2. **Yamas Schnitt-Entscheidung 25.07.: nach Aufwand, innerhalb seiner 15 Bereiche** — erst die 41 ohne Modellwirkung, dann `modify`, dann `create` mit Schema-Arbeit, `import`/`assign-or-calculate` zuletzt. **Nicht Bereich 1–15 der Reihe nach**, weil die Bereiche quer zum Aufwand liegen | Planner → Generator | `GESPERRT` — **Yamas Entscheidung 25.07.: erst Layout fertig** (AUF-39 · 43 · 45 · 44 · 47). Planner schreibt danach den Stufenplan; jede Stufe wird ein eigener Posten, nicht dieser hier | Ledger „Werkzeuge funktionstüchtig — die gemessene Lücke" (25.07.) |

---

### 3b. Abnahme-Stapel — berichtet, wartet auf Pruefung

Niemand nimmt eigene Arbeit ab (§1.4).

> **Leer.** Jeder gebaute Posten traegt sein Votum.


### 3c. Bei Yama — Willensfragen

> **Leer.** `W-Projektliste` ist am 26.07. **an den Planner delegiert** und als **AUF-78**
> beauftragt — mit ausgeschriebener Begruendung, wofuer der Planner haftet (§6 des Auftrags).
> Neue Willensfragen gehoeren wieder hierher, nicht in eine Meldung.

### 3d. Abgeschlossen — im Archiv

Abgenommen, entschieden oder entfallen, wortgleich in **`docs/auftraege/AUFTRAGSTAFEL-ARCHIV.md`** —
**49 Posten**: AUF-1 · AUF-2 · AUF-3 · AUF-4 · AUF-9 · AUF-11 · AUF-12 · AUF-14 · AUF-15a · AUF-16 ·
AUF-19 · AUF-20 · AUF-24 · AUF-25 · AUF-21 · AUF-26 · AUF-27 · AUF-28 · AUF-30 · AUF-31 · AUF-32 · AUF-33 · AUF-34 · AUF-35a · AUF-36 · AUF-37. Nicht gelöscht, nur nicht mehr im Weg.

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
