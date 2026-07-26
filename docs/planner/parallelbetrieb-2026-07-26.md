# Wie viele Posten können wirklich gleichzeitig laufen

**Planner · 26.07.2026 · Alle Zahlen an diesem Tag gemessen, HEAD `97631f1`**

Yamas Frage: *wieviel Aufgaben können parallel laufen, ohne dass sie Konflikte verursachen — nur
wenn es Sinn macht.* Die Antwort vorweg, damit sie nicht im Kleingedruckten verschwindet:

> **Heute, ohne eine einzige Änderung an der Einrichtung: ein bauender Posten. Nicht zwei.**
> **Mit einem zweiten Arbeitsbaum: drei Spuren. Mehr ist Schein, weil der Engpass dann nicht
> mehr das Schreiben ist, sondern die Abnahme.**

Der übliche Denkfehler ist, Parallelität an **Dateien** zu messen. Das ist die falsche Größe. Zwei
Posten können völlig getrennte Dateien anfassen und sich trotzdem zerstören — weil beide ihre
Abnahmekriterien **gegen den Arbeitsbaum messen**, und der Arbeitsbaum ist nur einmal da.

---

## 1. Der Beweis, dass Datei-Trennung nicht genügt

`tsconfig.hausplaner.json` — gemessen, wörtlich:

```json
"include": ["resources/planner"]
```

**`tsc:hausplaner` übersetzt die ganze Insel, nicht die Dateien eines Postens.** Damit gilt:

Angenommen, AUF-78 (Projektliste, schreibt `StartView.tsx`) und AUF-76 (Wandschichten, schreibt
`domain/`) laufen gleichzeitig im selben Baum. Die Schnittmenge ihrer Schreibpfade ist **leer** —
nach der Datei-Logik also erlaubt. Was passiert:

1. Generator A ist mitten in `StartView.tsx`. Die Datei ist syntaktisch halb.
2. Generator B führt sein Kriterium 1 aus: `npm run tsc:hausplaner`.
3. Der Übersetzer liest `resources/planner` **vollständig** und bricht an A's halber Datei ab.
4. B misst **rot** — an einer Datei, die ihn nichts angeht, für einen Fehler, den er nicht gebaut
   hat.

**Das ist kein Randfall, das ist der Normalfall.** Neun von zehn offenen Posten haben
`tsc:hausplaner` oder `test:hausplaner` als Kriterium 1. Genau davor warnt §10.3 bereits:
*Messwerte aus einem wandernden Baum sind keine Messwerte.* Der Satz stand da, bevor jemand
Parallelität wollte — er ist der Grund, warum sie im geteilten Baum nicht geht.

---

## 2. Die fünf Engpässe, jeder einzeln gemessen

| # | Engpass | Gemessen an | Erlaubt gleichzeitig |
|---|---|---|---|
| 1 | **Ein Arbeitsbaum, ein Übersetzer-Lauf** | `tsconfig.hausplaner.json` `include: ["resources/planner"]` | **1** bauender Posten |
| 2 | **Eine Test-Datenbank** | `phpunit.xml:28` `DB_DATABASE=ticket_testing` **force="true"** | **1** PHP-Suite — auch über getrennte Arbeitsbäume hinweg |
| 3 | **Eine ausgelieferte Anwendung** | `.env` `APP_URL=http://ticket.test` → Herd bedient nur den Hauptbaum | **1** Posten mit Sichtprobe |
| 4 | **Ein Ledger** | `docs/handoff-status.md`, 9418 Zeilen, jede Instanz hängt unten an | **1** Zweig, der anhängt — sonst Konflikt bei **jedem** Zusammenführen |
| 5 | **Ein Evaluator** | eine Instanz, eine Abnahme zur Zeit | **1** Abnahme; drei Bauer erzeugen eine Schlange, keinen Durchsatz |

Engpass 2 und 3 sind **hart**: sie gelten auch dann, wenn jeder Generator seinen eigenen
Arbeitsbaum hat. Engpass 1 fällt mit getrennten Bäumen weg. Engpass 4 ist lösbar, aber nur mit
einer Regel (unten). Engpass 5 ist der, den man nicht wegbaut.

**Von den zehn offenen Posten sind acht `sichtbar`** (brauchen eine Sichtprobe) **und zwei fassen
PHP an** (AUF-78, AUF-81). Gemessen bleiben **genau zwei** übrig, die weder das eine noch das
andere brauchen: **AUF-77** (neue reine Datei in `geometry/`) und **AUF-79** (nur `docs/` und
`scripts/`). Das ist keine Meinung, das ist die Trefferliste.

---

## 3. Was an Werkzeug schon da ist

`git worktree list` meldet **sechs** eingetragene Arbeitsbäume (`ticket-g1b-0`, `ticket-strang-C`,
`-accounting`, `-energie`, `-formulare`, ein Agenten-Baum). Aus der Cowork-Brücke erscheinen sie
als `prunable`, **weil die Brücke die Pfade nicht sieht — nicht weil sie fehlen.** Auf Yamas
Rechner sind sie da; das ist eine Messgrenze meines Blicks, keine Aussage über den Zustand.

Zwei Vorbereitungen kostet ein zusätzlicher Baum, gemessen an `ticket-g1b-0`:

- **`vendor/` ist vorhanden** — PHP läuft dort sofort.
- **`node_modules` fehlt** — vor dem ersten Insel-Testlauf steht ein `npm install`.

Und eine Eigenheit, die man kennen muss: `.git/hooks/post-commit` ist ein **relativer Symlink** auf
`scripts/hooks/post-commit` des Hauptbaums, aber der Hook startet `$WURZEL/scripts/waechter.sh` —
also das Skript **des jeweiligen Baums**. Die Wächter-Sperre liegt unter `docs/befunde/` und ist
damit **pro Baum getrennt**. Die Bäume stören sich beim Wächter nicht.

---

## 4. Die Empfehlung: drei Spuren, benannt

**Spur 1 — Hauptbaum. Alles, was gesehen oder mit PHP geprüft wird.**
`AUF-78` → `AUF-66` → `AUF-81`. Diese drei **müssen** hintereinander, und zwar dreifach begründet:
sie teilen die Test-Datenbank, sie teilen die ausgelieferte Anwendung, und AUF-66 wartet
inhaltlich auf die Zulieferung aus AUF-78.

**Spur 2 — zweiter Arbeitsbaum. Reine Insel, kein PHP, keine Sichtprobe.**
`AUF-76` → `AUF-77`. Beide fassen ausschließlich `domain/` bzw. eine **neue** Datei in `geometry/`
an; die Schnittmenge mit Spur 1 ist leer, und im eigenen Baum ist auch der Übersetzer-Lauf getrennt.

**Spur 3 — Hauptbaum, aber ohne Baum-Messung.**
`AUF-79`. Schreibt `docs/fortschritt.html` und `scripts/waechter.sh` — **kein** `tsc`, **kein**
`test:hausplaner`, kein PHP. Sein Kriterium ist eine Zählung, die man an zwei Textdateien prüft.
Deshalb darf er neben Spur 1 im selben Baum laufen. **Eine Auflage:** `scripts/waechter.sh` wird in
**einem** Commit geändert, nicht in dreien — sonst feuert Spur 1 dazwischen den Hook auf eine halbe
Fassung des Wächters.

### Was ausdrücklich **nicht** parallel läuft

- **AUF-63 (jsdom)** — er ändert den **Testläufer selbst**. Wer daneben misst, misst mit einem
  Messgerät, das gerade umgebaut wird. **Allein, oder gar nicht.**
- **AUF-38 (Inline-Styles)** — fasst `HausplanerApp.tsx` (2229 Zeilen), `studioDaten.ts`,
  `GuidedView`, `ConfigWizard`, `HausplanerStudio`, `FachFlaeche`, `StartView` und `main.tsx` an.
  Er kollidiert mit **AUF-78** (`StartView.tsx`), **AUF-54** (`studioDaten.ts`) und **AUF-52**
  (`EngineFlaeche`). Er ist kein Posten, der neben etwas läuft — er ist der Posten, neben dem
  nichts läuft.
- **AUF-81 neben AUF-78** — zwei PHP-Suiten auf **einer** Datenbank, dazu beide in `app/Http`.

---

## 5. Die Regel, die daraus folgt (Vorschlag §14)

1. **Parallelität wird an Messwegen entschieden, nicht an Dateien.** Zwei Posten dürfen nur dann
   gleichzeitig laufen, wenn sie **nicht denselben Messweg** benutzen: derselbe Übersetzer-Lauf,
   dieselbe Test-Datenbank, dieselbe ausgelieferte Anwendung.
2. **Ein Posten je Messweg.** `tsc`/`test:hausplaner` im selben Baum: einer. PHP-Suite:
   einer, über alle Bäume. Sichtprobe: einer.
3. **Der Ledger bleibt einspurig.** Wer in einem zweiten Baum auf einem eigenen Zweig arbeitet,
   schreibt seinen Bericht in **eine eigene Datei** unter `docs/berichte/`; der Planner führt sie
   in den Ledger. Sonst gibt es bei **jedem** Zusammenführen einen Konflikt an derselben Stelle:
   der letzten Zeile.
4. **Die Marke `⚡ AKTIV` gilt je Spur, nicht je Tafel.** §1c bleibt in Kraft — aber sie verhindert
   Themenwechsel, nicht Parallelität. Bei mehreren Spuren trägt **jede Spur genau eine** Marke,
   und die Spur steht in der Zeile.
5. **Mehr Spuren als Abnahmen bringt nichts.** Wächst der Abnahme-Stapel auf mehr als zwei, wird
   **keine** neue Spur eröffnet, bis er wieder leer ist. Ein Bauer, dessen Arbeit ungeprüft liegt,
   hat nichts fertiggestellt — er hat nur Vorrat erzeugt, der veraltet.

---

## 6. Gegenprobe

Ich habe versucht, die eigene Empfehlung zu widerlegen, und den stärksten Einwand ausformuliert:

> *„AUF-76 und AUF-78 haben null gemeinsame Dateien. Warum sollen sie nicht beide im Hauptbaum
> laufen? Das ist überängstlich."*

Der Einwand ist **falsch**, und zwar nachweisbar: `tsconfig.hausplaner.json` übersetzt
`resources/planner` **als Ganzes**. AUF-76 hätte einen roten Übersetzer-Lauf an AUF-78's halb
geschriebener `StartView.tsx` — ein Fehler, den er weder verursacht noch reparieren darf. Genau
dieser Fall hat am 26.07. schon einmal Arbeit gekostet, in umgekehrter Richtung: AUF-78 wurde
gesperrt, **weil** der Evaluator gerade die volle PHP-Suite fuhr.

Der zweite Einwand hält allerdings stand, und ich schreibe ihn auf, statt ihn zu verschweigen:

> *„Drei Spuren bringen keinen dreifachen Durchsatz, solange ein Evaluator prüft."*

**Stimmt.** Der ehrliche Gewinn liegt bei etwa **anderthalb bis zwei**, nicht bei drei — deshalb
steht Punkt 5 in der Regel. Wer mehr will, braucht nicht mehr Bauer, sondern eine zweite
prüfende Instanz — und **das** wäre ein Vorschlag an Yama, keine Entscheidung des Planners.
