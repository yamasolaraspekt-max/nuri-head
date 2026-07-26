# ⇒ GENERATOR-AUFTRAG AUF-75 — Der Wächter: es soll etwas von selbst laufen

**Vom:** Planner · **26.07.2026, 09:30** · **Spur A** · **Heimat-App:** `ticket`
**Anlass:** Yamas Frage nach einem lokalen Qualitätsdienst. **Dies ist die erste und einzige
Scheibe, die ich davon jetzt beauftrage** — der Rest steht bewertet im Ledger.

**Vorher gelesen:** HEAD `88973f9` · `package.json` (7 Skripte, davon 4 Gates) ·
`.github/workflows` = **nicht vorhanden** · `.git/hooks` = **leer** · `playwright.config.*` =
**nicht vorhanden** · `.mcp.json` = **nicht vorhanden** · `docs/agents/06-laufzeiten-und-takt.md`
§8–§11 · Ledger „PLANNER 26.07., 09:30".

---

## 1. Der Befund: die Regeln stehen, aber niemand führt sie aus

**Gemessen:** In diesem Repository läuft **nichts** automatisch. Keine CI, kein Hook, kein Dienst.
**Jede Prüfung heute läuft, weil ein Mensch oder eine Instanz sich erinnert.**

**Was das gekostet hat, gemessen an einem Tag:**

- **AUF-64** — `objekt/203` lag mit einem PHP-Fehler im Hauptzweig. **Vier Gates grün, 1007 Tests
  grün.** Die Abdeckung existierte in der PHP-Suite; **sie wurde nicht gefahren.** Gefunden hat es
  der Browser, Stunden später.
- **§9** verlangt seither die PHP-Suite bei jeder Blade-Änderung. **Durchgesetzt wird sie von
  nichts** — genau wie §1 der Tafel vor §10.

**Eine Regel, deren Einhaltung vom Erinnern abhängt, ist eine Bitte.** Der Wächter macht aus den
Bitten §8–§11 eine Ausführung.

## 2. Was gebaut wird — und was ausdrücklich nicht

**Gebaut wird ein deterministisches Skript.** Kein Sprachmodell, keine Reparatur, keine Bewertung,
kein Dienst, der ständig läuft. **Es führt aus, was schon da ist, und schreibt auf, was dabei
herauskam.**

### (a) `scripts/waechter.sh` — führt die Gates nach Betroffenheit

Aus dem Diff des letzten Commits wird abgeleitet, **was zu prüfen ist**:

| geändert | läuft |
|---|---|
| `resources/planner/hausplaner/**` | `tsc:hausplaner` · `schema:hausplaner:check` · `test:hausplaner` |
| `**/*.blade.php` **oder** `app/**` | zusätzlich `php artisan test tests/Feature/Hausplaner` **(§9)** |
| `public/hausplaner/**` **ohne** Quelländerung | Hinweis „Bundle ohne Code" — **kein Fehler**, eine Beobachtung |

**Die Zuordnung ist die Regel in ausführbarer Form.** Sie erfindet keine neue Prüfung.

### (b) Ein Befund je Lauf, maschinenlesbar und menschenlesbar

Eine Zeile je Lauf in `docs/befunde/waechter.log` (angehängt, nie überschrieben):

```
<zeitstempel> <commit> <ausloeser> <gate>=<exitcode> … <status>
```

**Und bei Rot zusätzlich** eine Datei `docs/befunde/<commit>-<gate>.txt` mit der **Rohausgabe** —
nicht mit einer Zusammenfassung. *Eine Zusammenfassung eines Fehlschlags ist bereits eine
Interpretation, und die gehört nicht in den Wächter.*

### (c) Ein `post-commit`-Hook, der **nicht blockiert**

Der Hook startet das Skript **im Hintergrund** und kehrt sofort zurück.

**Grund, gemessen:** Der Testlauf dauert; ein blockierender Hook macht aus jedem Commit eine Pause
und wird nach dem dritten Mal umgangen. **Ein umgangener Wächter ist schlechter als keiner**, weil
er im Repository steht und Sicherheit vortäuscht.

**Der Hook wird als Datei im Repository abgelegt und muss von Hand eingerichtet werden**
(`.git/hooks` ist nicht versioniert). Der Einrichtungsbefehl gehört in den Bericht — **eine Zeile,
die Yama ausführen kann**, nicht eine Beschreibung.

## 3. Die Kanten — hier bricht so etwas

1. **Der Wächter darf den Baum nicht stören.** Drei Instanzen arbeiten in derselben Arbeitskopie.
   **Jeder `git`-Aufruf des Wächters trägt `--no-optional-locks`**, und er legt **keine** Datei
   außerhalb von `docs/befunde/` an. *(Ich habe heute dreimal auf `index.lock` gewartet; ein
   Wächter, der selbst Locks erzeugt, macht das schlimmer.)*
2. **Kein Commit durch den Wächter.** Er schreibt Dateien, er committet sie nicht. Wer misst, greift
   nicht in die Geschichte ein.
3. **Zwei Läufe gleichzeitig.** Committen zwei Instanzen kurz nacheinander, laufen zwei Wächter.
   **Eine Sperrdatei in `docs/befunde/` verhindert das** — der zweite Lauf endet mit einer Zeile
   „übersprungen, Lauf aktiv", statt zu warten.
4. **Fehlende Werkzeuge.** Ist `php` nicht im Pfad, ist das **kein Fehlschlag der Prüfung**, sondern
   die Zeile „php nicht verfügbar — PHP-Suite nicht gelaufen". **Ein nicht gelaufener Test darf nie
   wie ein bestandener aussehen.** Das ist das wichtigste Kriterium dieses Postens.
5. **Kein Wachstum ohne Grenze.** `waechter.log` wird angehängt; die Rohausgaben der Fehlschläge
   bleiben. Ein benanntes Verfahren, was mit alten Dateien passiert — auch wenn es „nichts, sie
   bleiben" lautet.

## 4. Was **nicht** gebaut wird

- **Kein Sprachmodell**, keine Ursachenanalyse, keine Priorisierung, kein Ticket. **Das sind die
  drei Rollen, und die gibt es.**
- **Kein Dauerdienst, kein Dateisystem-Beobachter.** Auslöser ist der Commit, sonst nichts.
- **Kein Dashboard.** Die Fortschrittsübersicht für Yama gibt es bereits.
- **Keine neue Abhängigkeit.** Bash, git, npm, php — mehr nicht. *(Was der Wächter braucht, ist
  installiert; was er nicht braucht, wird nicht installiert.)*
- **Kein Anfassen der Insel.** `resources/planner/hausplaner/**` trägt null Zeilen.
- **Kein GitHub, keine CI, kein MCP.** Das sind eigene Entscheidungen und keine Beifänge.

## 5. Abnahmekriterien

1. **Die vier Gates bleiben unverändert grün** — der Wächter ruft sie, er ändert sie nicht.
   `package.json` trägt höchstens **einen** neuen Eintrag (`waechter`), keine geänderten.
2. **Betroffenheit stimmt:** je ein vorgeführter Lauf mit (a) nur Insel-Änderung, (b) einer
   `.blade.php`-Änderung, (c) nur `public/*`. **Die jeweils gelaufenen Gates im Bericht nennen.**
3. **§9 ist erzwungen:** bei einer Blade-Änderung läuft die PHP-Suite **ohne Zutun**. *Vorführen an
   dem Commit, der `objekt/203` zerbrochen hat* (`e0d1144`) — **der Wächter muss dort rot melden.**
   Das ist der Beweis, dass er den einen Fall fängt, für den er gebaut wird.
4. **Nicht gelaufen ≠ bestanden:** Test mit fehlendem `php` ⇒ die Zeile sagt **„nicht gelaufen"**,
   und der Gesamtstatus ist **nicht** grün.
5. **Er blockiert nicht:** gemessene Zeit zwischen `git commit` und Rückkehr der Eingabeaufforderung,
   vorher/nachher. **Zahl nennen.**
6. **Er stört den Baum nicht:** `grep` belegt `--no-optional-locks` an jedem `git`-Aufruf; nach einem
   Lauf ist `git status --porcelain` außerhalb von `docs/befunde/` **unverändert**.
7. **Zwei gleichzeitige Läufe:** vorgeführt — der zweite endet mit „übersprungen", nicht mit einem
   zweiten Testlauf.
8. **Die Einrichtung ist eine Zeile**, im Bericht wörtlich genannt und selbst ausgeführt.
9. **Klassifikation: `Vorarbeit`.** Für den Nutzer ändert sich nichts. Das ist hier die richtige
   Einstufung und keine Ausrede.

## 6. Was zurückgegeben wird

- **Lässt sich die Betroffenheit nicht sauber aus dem Diff ableiten** (z. B. weil ein Commit beides
  berührt): **im Zweifel alles laufen lassen** und es so melden. Ein Wächter, der zu viel prüft,
  kostet Zeit; einer, der zu wenig prüft, kostet eine Route.
- **Erweist sich der `post-commit`-Hook als unzuverlässig**, weil mehrere Instanzen committen:
  melden. Dann ist der Auslöser eine eigene Frage — das Skript bleibt trotzdem nützlich, weil es von
  Hand aufrufbar ist.
