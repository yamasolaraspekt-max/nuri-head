# W-33 · Start und Projektwahl — PRÜFUNG

> **Ablesung, kein Bau.** *Die Kriterien unten prüfen das BLATT. Was den CODE prüft, steht darunter
> als Wächterliste — acht Dateien, jede geöffnet.*

## Abnahmekriterien dieses Blattes

| Nr | Kriterium | Wodurch wäre es rot | Wie gemessen |
|---|---|---|---|
| K-1 | Der **Anlass** steht wörtlich in `1-ZWECK`: erfundene Projekte bei jedem Nutzer, **Falschauskunft über den eigenen Bestand** | eine Kachelwand beschreiben | `startEhrlich.test.ts` Dateikopf, am Bau-Stand gelesen |
| K-2 | **Befund (b)** steht ebenfalls, mit `projektKlick` als heutigem Schutz | die drei Karten als heutigen Stand beschreiben | *gemessen: es sind **zwei**, siehe unten* |
| K-3 | Der **Leerzustand** ist als **Normalfall** beschrieben | ihn als Ausnahme führen | `:206`, Dateikommentar `:15-17` |
| K-4 | **Vier Komponenten** mit Fundstelle, **drei** mit eigenem `hover` | die Zahl aus dem Auftragsblatt übernehmen | `:52`, `:104`, `:165`, `:193` — `hover` in `:53`, `:105`, `:166` |
| K-5 | `7-GRENZEN` trägt **AUF-40 Teil B wörtlich** | den Posten in einer Testdatei verwaisen lassen | Zitat mit Herkunft **plus die Messung, dass er überholt ist** |
| K-6 | **Acht Wächter**, je mit Zusage — `startEhrlich`, `projektKlick`, `rohwertZusage` **wörtlich** | acht Namen aufzählen | jede Datei geöffnet, unten |
| K-7 | Die **Scope-Grenzen** zu W-39, W-38 und W-35 stehen in `2-FUNKTION` | sie neu ziehen statt spiegeln | `2-FUNKTION`, Abschnitt „Die Grenzen" |
| K-8 | **Sieben Blätter**, Gegenprobe `tail -n +2 <blatt> \| md5` | zwei Werkzeuge mit gleichem Hash | Ergebnis im Bericht |

### Zu K-2: der Befund ist behoben, aber anders als beschrieben

```text
Auftrag   „die drei Projektkarten riefen alle onGuided(1)"      der Zustand VORHER
gemessen  grep -c "<Karte " StartView.tsx  ->  2                der Zustand HEUTE
```

**Die dritte Karte („Weiterarbeiten") ist fort, nicht umgehängt** (`:241-249`, AUF-66). *Von den
zwei verbliebenen trägt eine gar kein Ziel und sagt warum.* **Beide Zahlen stehen im Blatt, je mit
dem, was sie messen — ich ersetze das Kriterium nicht still.**

## Die acht Wächter — je mit der Zusage

**Alle acht berühren `StartView`** — *gemessen:* `grep -rln "StartView" __tests__/` **→ genau die
acht des Auftrags.**

### 1 · `startEhrlich.test.ts` — **9 Tests**, der tragende Wächter

**Die Zusage, WÖRTLICH aus dem Dateikopf:**

> ***„AUF-40 Teil A — der Startbildschirm sagt, was es gibt."***
>
> *„**(a) Er zeigte erfundene Projekte.** … bei **jedem** Nutzer, auch beim allerersten Start, auch
> ohne ein einziges eigenes Projekt. **Ein Startbildschirm, der fremde Projekte zeigt, ist keine
> Vorschau; er ist eine Falschauskunft über den eigenen Bestand.**"*
>
> *„**(b) Die drei Projektkarten waren dieselbe Karte.** Alle drei riefen `onGuided(1)` — drei
> Versprechen, ein Ziel."*

**Die neun Zusagen einzeln:**

```text
:34   K3  der erfundene Kundenname steht NUR noch in der stillgelegten Datei
:42       die Demo-Daten sind stillgelegt, nicht geloescht
:49   K4  ohne Projekte kein Listeneintrag — und ein Satz, der nichts verspricht
:58   K4  der Grundzustand IST leer — beim ersten Start der Normalfall
:73   K5  keine zwei Karten rufen dasselbe Ziel auf
:84   K5  eine Karte ohne Ziel ist als `in Entwicklung` ausgewiesen — mit Grund
:95       eine Karte ohne Ziel ist KEINE Schaltflaeche mehr
:111      die Karte MIT Ziel ist unveraendert bedienbar
:118      Teil A hat weder Route noch Controller beruehrt — das ist Teil B
```

> **Die neunte Zusage verdient eine genaue Lesart, weil sie meinen Befund zu berühren SCHEINT und es
> nicht tut.** *Sie prüft:*
>
> ```ts
> assert.doesNotMatch(start, /fetch\(|axios|\/admin\/hausplaner/);
> assert.doesNotMatch(start, /dataset\./);
> ```
>
> **Beides gilt weiter — selbst nachgemessen:** `grep -c "fetch(\|axios\|dataset\." StartView.tsx`
> **→ 0.** *Die Naht läuft über `main.tsx`, nicht über `StartView`.*
>
> **Überholt ist nur der Begleitsatz im Kommentar:** *„Die Zulieferung der Liste bleibt deshalb
> offen."* **Sie ist nicht mehr offen** *(siehe `7-GRENZEN`)*. **Die ZUSAGE ist richtig und soll
> bleiben: `StartView` holt sich nichts selbst, es bekommt.** *Das ist der Unterschied zwischen einem
> überholten Test und einem überholten Kommentar an einem richtigen Test — und wer ihn nicht macht,
> meldet einen grünen Wächter als falsch.*

### 2 · `projektKlick.test.ts` — **15 Tests**

**Die Zusage, WÖRTLICH:**

> ***„AUF-66 — ein Klick zurück in die Arbeit."***
>
> *„**Gemessen wird am echten Render-Pfad** (`react-dom/server`), **nicht am Quelltext**. Ein Test,
> der nur nach Zeichenketten in der Datei sucht, hätte die geteilte Adresse — den häufigsten Fehler
> solcher Listen — nicht gefunden: **die steht im Quelltext genauso richtig da wie die
> getrennte.**"*

> **Seine Begründung ist die beste Beschreibung dessen, was ein Textmuster nicht kann, die ich in
> diesem Repository gefunden habe.** *Er sagt auch, was er nicht kann:* **„Was hier NICHT geprüft
> wird: wie es aussieht … Ob der dominante Eintrag bei 1024×768 ohne Scrollen sichtbar ist,
> entscheidet die Sichtprobe — sie ist Teil der Abnahme, nicht ein Anhang."**
>
> **BERICHTIGT beim Gegenlesen:** *hier stand zuerst „der **einzige** Wächter dieses Werkzeugs, der
> rendert".* **Falsch — `elevationTokens.test.ts:76` rendert `StartView` ebenfalls** *(`:23`
> importiert es namentlich).* **Es sind ZWEI von acht.** *Ich hatte `elevationTokens` nach seinem
> Namen für einen reinen Token-Vergleicher gehalten und nicht geöffnet — genau der Griff, den ich in
> diesem Blatt an drei anderen Stellen anmahne.*

### 3 · `rohwertZusage.test.ts` — **16 Tests**, inselweit

**Die Zusage, WÖRTLICH:**

> ***„AUF-38 — die generische Rohwert-Zusage. Eine für alle Scheiben."***
>
> *„**Was sie zusagt:** eine Rohfarbe darf inline bleiben — aber **nur solange sie keinen Token
> hat**. Bekommt sie einen, ist sie keine Ausnahme mehr, sondern ein Stil, der in die Schicht
> gehört, und der Test geht rot. Das gilt für **alle** Dateien der Insel."*
>
> **Und warum sie generisch ist:** *„Bis hierher trug jede Ausnahme ihre eigene Zusage. Wer eine neue
> anlegt, muss daran denken, auch die Zusage zu schreiben — und genau das ist zweimal unterblieben.
> Der Evaluator hat es benannt: **‚Per-Scheibe-Locks skalieren nicht.'** **Einzelzusagen finden nur,
> was jemand vorher gezählt hat.**"*

> **`rohwertZusage` ist NICHT ein W-33-Wächter, sondern ein inselweiter.** *`StartView` kommt darin
> als Fundstelle vor (`:129` nennt `StartView.tsx:155` mit dem Anführungszeichen im Kommentar, an
> dem der Scanner einmal entgleist ist).* **„16 Tests" als Zusage für dieses Werkzeug wäre falsch —
> dieselbe Unterscheidung wie bei `stilschicht` in W-35.**

### 4 · `elevationTokens.test.ts` — **9 Tests**

**Zusage:** *dass Schattenwerte aus Tokens kommen und nicht abgeschrieben werden* — **und er misst am
gerenderten Markup, nicht am Quelltext** (`:23` importiert `StartView`, `:76` rendert es):

```ts
:73-81   „K1: und das erzeugte Markup zeigt denselben Wert wie vorher"
         „Der eigentliche Beweis. Der Quelltext koennte einen Token nennen und
          trotzdem etwas anderes ausgeben; das Markup kann es nicht."
:118-119 const rest = zaehleInApp('0 10px 30px rgba(28,50,55,.10)');
         assert.equal(rest.length, 1);
         assert.match(rest[0]!, /^StartView\.tsx:/, 'und zwar in der Projektkachel aus AUF-66');
```

> **Der eine Rohwert ist FESTGENAGELT, nicht geduldet.** *Der Test verlangt **genau einen** und
> verlangt, dass er in `StartView.tsx` steht.* **Käme ein zweiter dazu, ginge er rot; verschwände
> dieser, ebenfalls.** *Der Kommentar sagt den Grund: „Er ist hier festgehalten, damit er nicht als
> Versehen durchgeht."*
>
> **Und er trägt eine Zahlenkorrektur des Autors an sich selbst** (`:112-113`): *„Der Auftrag nannte
> zwölf Vorkommen, gemessen waren es **vierzehn**. Die zwei zusätzlichen stammen aus AUF-66, also
> von mir."*

### 5 · `konfiguratorEhrlich.test.ts` — die **fünfte Stelle** liegt in dieser Datei

```text
:112   „die FUENFTE Stelle: der Startbildschirm machte dasselbe Versprechen"
:118    kein „verlustfrei" in StartView
:119    „Fenster, Türen, Treppen und Heizkörper setzt der Experte ins Gebäude"
:121    „sonst entsteht eine Datei zum Herunterladen"
```

**Zusage:** *dass die Schildzeile `:254` nicht mehr verspricht, als W-35 liefert.* **Eine Fläche
verspricht, eine andere liefert — und dieser Test verbindet beide.**

### 6 · `breiten.test.ts` · 7 · `dialogFokus.test.ts` · 8 · `stilschicht.test.ts`

| Wächter | Zusage für `StartView` |
|---|---|
| `breiten` | `:52` liest `StartView.tsx` einzeln; `:72` führt es in der Liste der Dateien **ohne feste zweite Spalte** |
| `dialogFokus` | `:80` führt `StartView.tsx` in der Dateiliste — **eine Datei unter vieren**, kein eigener Test |
| `stilschicht` | **VIER Stellen**, drei davon eigene Tests — siehe unten |

**`stilschicht` ist hier KEIN Randbeteiligter, anders als bei W-35** — *dort war es einer von 58
Tests, hier sind es vier Stellen:*

```text
:154-155  „Nachzug Scheibe 2: jede Klasse wird auch benutzt"
:162-163  „Nachzug Scheibe 2 (Wirkung): jeder verbliebene Inline-Stil in
           StartView HAT EINEN GRUND"
:173-174  „Scheibe 2: StartView traegt keine statischen Stil-Objekte mehr"
:426-429  Ziel ist null STATISCHE Inline-Stile, nicht null Inline-Stile —
           assert.match(start, /boxShadow: hover \? T.schattenGehoben : T.schattenFlach/,
                        'der Schwebezustand bleibt inline')
```

> **Die Zeile `:162` ist die tragende:** *nicht „keine Inline-Stile", sondern **„jeder verbliebene
> hat einen Grund"**.* **Das ist die Regel aus dem Kommentarblock `:30-43` in Testform** — *ein
> Inline-Stil darf bleiben, wenn er aus `hover`, `dominant` oder einer Messung kommt.* **`:429`
> verlangt sogar ausdrücklich, dass der Schwebeschatten inline BLEIBT.**
>
> **Ich hatte diese drei zuerst pauschal als „inselweit, eine Datei unter mehreren" abgetan** —
> *für `breiten` und `dialogFokus` trifft das, für `stilschicht` nicht.* **Gemessen statt nach der
> Überschrift eingeordnet; bei W-39 hat mich derselbe Griff einmal einen Fehlbefund gekostet.**

## Fangprobe (Mutationsprobe)

| Mutation | Muss erkannt werden von | Gemessen? |
|---|---|---|
| eine Beispielzeile in den Leerzustand schreiben | `startEhrlich:49`, `:58` | **nicht gefahren** |
| „Mustermann" in `StartView` zurückholen | `startEhrlich:34` | **nicht gefahren** |
| beide Karten auf `onGuided(1)` legen | `startEhrlich:73` | **nicht gefahren** |
| der ziellosen Karte ein `onClick` geben | `startEhrlich:84`, `:95` | **nicht gefahren** |
| allen Kacheln dieselbe `adresse` geben | **`projektKlick`** — *und nur er, weil er rendert* | **nicht gefahren** |
| `StartView` selbst `dataset.` lesen lassen | `startEhrlich:118` | **nicht gefahren** |

> **Keine gefahren, und das steht hier statt zu fehlen.** *Eine Fangprobe, die ich nicht setze, ist
> keine Messung, sondern eine Erwartung.* **Die fünfte Zeile ist die interessanteste: die geteilte
> Adresse ist ein Fehler, den nur ein RENDERNDER Test findet — im Quelltext sieht sie richtig aus.**

## Automatische Tests

**`npm run test:hausplaner`** — **nicht gefahren.** *Dieses Blatt ändert keine Zeile Code.*

## Sichtprüfung und Bestandsprobe

- [ ] **offen** — *vier Punkte in `4-BEDIENUNG.md`.* **Der erste ist die einzige offene MESSUNG des
      Werkzeugs: kommt die echte Liste im Browser an?** *Die Naht ist gelesen, nicht ausgeführt.*
