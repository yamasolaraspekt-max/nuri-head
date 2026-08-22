# Bündel-Klärung — muss `public/hausplaner/hausplaner.js` in jeden Bau-Commit?

```yaml
art: "KLAERUNG — Messung und Empfehlung. KEIN Auftragsblatt, KEIN Bau.
      Posten 7 lautet woertlich 'messen', nicht 'schneiden'."
rolle: planner
auftrag: "SPEZ-planner-anschlusswelle-1 gen 19, Posten 7 (letzter Teil)"
mess_sha: 05d7f04a
anlass: "Die Werkzeug-Vorlage aus A-35 fuehrt Stelle 12 als OFFENE Frage. Sie steht seither in
         JEDEM Spur-W-Blatt als '[?] 12 ... OFFEN' — und blockiert dort jede Abhakung."
ergebnis: "JA, zwingend. Es gibt keinen Bau-Schritt hinter dem Commit."
```

## Die Frage, und warum sie jetzt fällig ist

Die Vorlage (`werkzeug-vorlage-aus-a-35.md`, Stelle 12) sagt: *„ob dieses Bündel bei jedem Bau
mitcommittet werden **muss** oder ob es auf dem Zielsystem erzeugt wird, ist **nicht geklärt**."*

**Diese Frage steht in `Z1-W2-4`, `Z1-W2-5` und `Z1-W2-6` als offene Stelle.** Solange sie offen
ist, kann kein Spur-W-Bau seine Abhakliste vollständig belegen. *Sie einmal zu messen räumt sie
überall ab.*

## Die Messung, Stand `05d7f04a`

```
1  Ist das Buendel versioniert?
   git ls-files public/hausplaner/            -> hausplaner.js UND hausplaner.css  JA

2  Gibt es einen Bau-Schritt auf einem Zielsystem?
   .github/workflows/                          -> existiert NICHT
   Deploy-Skript im Baum                       -> keines
   package.json "build:hausplaner"             -> vorhanden, aber NUR lokal aufrufbar
   git ls-files public/build                   -> 0   (Laravel-Vite hat KEINE eingecheckten Ausgaben)

3  Wie wird es ausgeliefert?
   vite.hausplaner.config.ts:18   outDir = public/hausplaner
   studio.blade.php:97            @if (file_exists(public_path('hausplaner/hausplaner.js')))
                    :101          <script type="module" src="{{ asset(...) }}">
   objekt.blade.php               ebenfalls eingebunden
   -> ein STATISCHES Asset, direkt aus dem Repository ausgeliefert

4  Drift der letzten 60 Tage
   Commits, die resources/planner/hausplaner beruehren   228
   Commits, die public/hausplaner/hausplaner.js beruehren 153
```

## Die Antwort: **ja, zwingend — es gibt nichts, was danach noch baut**

**Das Bündel ist versioniert, wird als statisches Asset ausgeliefert, und es existiert kein CI-Lauf
und kein Deploy-Skript, der es erzeugen könnte.** *Ein Commit, der Inselcode ändert und das Bündel
nicht mitbringt, erreicht den Browser nicht.*

**Das ist keine Ableitung, sondern ein bereits eingetretener Schaden:** Befund `db64c7ca` —
**zehn Quellcommits seit dem 15.08. hatten den Browser nicht erreicht**; im ausgelieferten Bündel
fehlte der Schneelast-Fix, und *„im Browser fiel bis jetzt jede Zonenwahl auf Zone 3"*
(`ad340caf`).

> **Die Einschränkung, die dazugehört:** `hausplaner.css` steht seit dem **02.08.** unverändert,
> während der jüngste Inselcode vom **21.08.** ist. **Das ist kein Rückstand** — der Bau erzeugt nur
> `hausplaner.js`; die CSS ist *fertig*, nicht *veraltet*. **Die Regel lautet also: das Bündel
> mitcommitten, wenn der Bau es ändert — nicht: beide Dateien bei jedem Commit anfassen.**

## Der Prüfsatz — und warum der naheliegende falsch ist

**Der Generator hat den naheliegenden Weg selbst widerlegt** (`68ffc049`, wörtlich zitiert statt
nachgebaut):

> *„Ich hatte dem Planner eine Prüfung ‚Bündel jünger als jede Insel-Quelle' vorgeschlagen; sie
> würde falsch warnen. … Nach Datum wäre die CSS **achtzehn Tage hinterher** und damit der lauteste
> Treffer der Prüfung — gemessen ist sie aktuell … **Alter ist nicht Rückstand:** eine Datei, die
> sich nicht ändern musste, ist nicht veraltet, sondern fertig, und eine Prüfung, die beim ersten
> Lauf zwei Treffer meldet, von denen einer falsch ist, wird beim zweiten weggeklickt (A-03).*
>
> *Der Weg, der trägt und billiger ist als der falsche: **bauen und vergleichen**. Der Bau ist
> reproduzierbar, zwei Läufe hintereinander byteweise gleich, Laufzeit 1,23 s, Ergebnis exakt ohne
> Datum und ohne Heuristik. **Der Prüfsatz lautet damit nicht ‚ist das Bündel jünger', sondern
> ‚ändert ein Bau es noch'.** Ich baue ihn nicht, der Zuschnitt bleibt beim Planner."*

**Ich übernehme diesen Prüfsatz unverändert.** *Er ist gemessen, nicht geraten, und er kommt von
der Rolle, die den Bau fährt.*

## Was daraus für die Spur-W-Blätter folgt — Stelle 12 ist beantwortet

**Neue Fassung für die Abhakliste** (`werkzeug-vorlage-aus-a-35.md`, Stelle 12):

```
[ ] 12  public/hausplaner/hausplaner.js
        REGEL: Aendert der Bau Inselcode, gehoert das neu gebaute Buendel in DENSELBEN Commit.
               Es gibt keinen Bau-Schritt dahinter — kein CI, kein Deploy-Skript.
        BELEG:  npm run build:hausplaner, danach `git status --porcelain public/hausplaner/`
                -> leer  =  das Buendel im Commit ist aktuell
                -> nicht leer = es fehlt; nachziehen, BEVOR fertiggemeldet wird
        AUSNAHME: hausplaner.css nur, wenn der Bau sie aendert. Alter ist kein Rueckstand.
```

**Für `Z1-W2-4` (Treppe-Probe) bleibt Stelle 12 zu Recht entfallen** — dort wird nichts gebaut.
**Für `Z1-W2-5` und `Z1-W2-6` wird aus `[?] OFFEN` ein `[x] mit Beleg.`**

## Was ich NICHT gemessen habe, und warum

**Ob das heute ausgelieferte Bündel aktuell ist, kann ich hier nicht feststellen.** In diesem
Worktree gibt es **kein `node_modules`** — ein Baulauf ist mir nicht möglich, und das Rollen-Tor
meldet den Modulstand bei jedem Commit als *„UNBEKANNT"*.
*Ich habe es nicht auf einem anderen Weg versucht und stelle die Aussage nicht aus zweiter Hand
auf.* **Die Messung gehört zum Bau, nicht zur Spezifikation** — und mit dem Prüfsatz oben kostet sie
1,23 s.

## Empfehlung — zwei Stufen, die zweite ist ein eigener Auftrag

| | |
|---|---|
| **jetzt, ohne Bau** | Die Regel oben in die A-35-Vorlage übernehmen. Damit ist Stelle 12 in allen Spur-W-Blättern beantwortet. |
| **eigener Auftrag** | Eine **Prüfung**, die `build:hausplaner` fährt und `git status` gegen `public/hausplaner/` hält. *Sie fällt rot, wenn ein Commit Inselcode ohne Bündel bringt.* **Das ist ein Blatt, kein Nebenbei — und der Deckel in gen 19 lässt es nicht zu.** Der Zuschnitt liegt vor; die Freigabe ist eine Entscheidung. |

> **Was diese Klärung nicht ist:** ein Bau, ein Auftragsblatt oder eine Änderung an der Vorlage.
> *Posten 7 sagt „messen". Gemessen ist; die Übernahme in die Vorlage ist ein Handgriff, den ich
> ansage statt ihn stillschweigend mitzunehmen.*
