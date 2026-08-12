# Baubericht W-33 — Start und Projektwahl, abgelesen

```yaml
auftrag: "W-33"
rolle: "generator"
blatt: docs/auftraege/aktiv/W-33-start-und-projektwahl.md
art: "STUFE 6 · ABLESUNG — der Code existiert, 267 Zeilen, W-39 rendert ihn"
basis_sha: 75ad92eb
befund_vor_dem_ziehen: "f469317d"
gebaut_am: "12.08.2026"
zustand: CODE_FERTIG
ballbesitz: evaluator
```

> **Der schwerste Fund steht vor dem Bau und trifft ein P1-Kriterium:** *AUF-40 Teil B — „die echte
> Projektliste braucht eine Route und liegt bei Yama" — **ist gebaut**.* **W-33-5 verlangt, genau
> diesen überholten Satz wörtlich ins Blatt zu schreiben.** *Wie ich beides erfülle, steht unten.*

## Was gebaut wurde

```text
docs/rollenkette/werkbank/02-WERKZEUGE/W-33-start-und-projektwahl/
  1-ZWECK.md · 2-FUNKTION.md · 3-FORMELN.md · 4-BEDIENUNG.md
  5-CODE/LIESMICH.md · 6-PRUEFUNG.md · 7-GRENZEN.md
REGISTER.md   Zeile 120:  LEER -> BESCHRIEBEN
```

**Kein Produktivcode.** *`StartView.tsx` ist gelesen, nicht geändert — auch die drei überholten
Kommentarstellen nicht.*

## W-33-5 · AUF-40 Teil B ist gebaut, und das Kriterium verlangt das Gegenteil

**Vor dem Ziehen gemessen, weil der Release-Prüfer in `5e9c8b08` bereits von einer halb gebauten
Hälfte gesprochen hatte. Jede Stelle geöffnet:**

```text
HausplanerController.php:101   hausplanerProjekte()
                                 LeadAlternativeAdd, select id/object_name/city/updated_at,
                                 orderByDesc updated_at, limit PROJEKTLISTE_MAX,
                                 map -> id · name · ort · datum · adresse
                          :55   'hpProjekte' => $this->hausplanerProjekte()
objekt.blade.php:141            data-projekte="{{ json_encode($hpProjekte, …) }}"
main.tsx:82                     setProjekte(leseProjekte(mount.dataset[PROJEKTE_ATTRIBUT]))
```

**Und zwei Dateien sagen es selbst:**

- `app/state/projekte.ts`: *„AUF-78 … **Jetzt kommt die echte Liste** — das Blade setzt, `main.tsx`
  liest, der UI-Zustand hält."*
- `__tests__/projektKlick.test.ts`: *„AUF-40 Teil A hat die erfundenen Projekte entfernt, **AUF-78
  die echten geliefert**."*

**Der Satz steht an DREI Stellen und ist an allen dreien überholt:** *`startEhrlich`-Dateikopf,
`StartView.tsx:18`, `StartView.tsx:205`.*

### Warum es niemand gesehen hat

```text
gesucht wurde   eine ROUTE
gebaut ist      eine NAHT ueber das Mount-Attribut, ohne Lade-Fetch
```

**Der Controller sagt es in `:57` wörtlich:** *„dieselbe Naht wie `hpProjekte`, **kein Lade-Fetch aus
der Insel**."*

> **Wer „Route" misst, findet 0 und schließt auf „nicht gebaut".** *Das ist H-9: ein Muster, das eine
> Bauform voraussetzt, misst die Bauform und nicht die Sache.* **Der Satz „braucht eine Route" ist
> nicht falsch geworden — er war von Anfang an eine Aussage über den WEG statt über das ZIEL.**

**Der Release-Prüfer hat mit seiner Hälfte recht:** *für PAKETE hat er drei Routen belegt, für
OBJEKTE gemessen, dass es keine Listen-Route gibt.* **Das trifft. Es braucht auch keine.**

### Wie ich das Kriterium erfülle, ohne es zu ersetzen

**`7-GRENZEN` trägt den geforderten Wortlaut als ZITAT mit Herkunft — und daneben die Messung, dass
er überholt ist.** *Damit ist W-33-5 buchstäblich erfüllt und dieses Blatt wird nicht zur vierten
Stelle, an der ein falscher Stand als Beleg gilt.*

**Was ich NICHT gemessen habe und deshalb nicht behaupte:** *ob die Liste im Browser wirklich
ankommt.* **Ich habe die Naht gelesen, nicht ausgeführt.** *Das ist die einzige offene Messung dieses
Werkzeugs und steht als erster Punkt der Sichtprobe.*

## Eine grüne Zusage, die ich beinahe als falsch gemeldet hätte

**`startEhrlich.test.ts:118` heißt „Teil A hat weder Route noch Controller berührt — das ist Teil B"
und prüft:**

```ts
assert.doesNotMatch(start, /fetch\(|axios|\/admin\/hausplaner/);
assert.doesNotMatch(start, /dataset\./);
```

**Selbst nachgemessen:** `grep -c "fetch(\|axios\|dataset\." StartView.tsx` **→ 0.** *Die Zusage hält,
und sie SOLL halten — die Naht läuft über `main.tsx`, nicht über `StartView`.*

> **Überholt ist nur der Begleitkommentar:** *„Die Zulieferung der Liste bleibt deshalb offen."*
> **Der Test ist richtig, sein Kommentar ist alt.** *Wer den Unterschied nicht macht, meldet einen
> grünen Wächter als falsch — und das wäre bei einem Ehrlichkeitswächter der teuerste Fehlbefund,
> den man hier bauen kann.*

## W-33-2 · Befund (b) ist behoben — anders als das Kriterium beschreibt

```text
Auftrag   „die drei Projektkarten riefen alle onGuided(1)"       der Zustand VORHER
gemessen  grep -c "<Karte " StartView.tsx  ->  2                 der Zustand HEUTE
```

**Die dritte Karte („Weiterarbeiten") ist FORT, nicht umgehängt** (`:241-249`, AUF-66): *„Sie ist
überflüssig geworden: fortsetzen geht jetzt oben, mit einem Klick, am Projekt selbst. **Zwei Wege zu
derselben Handlung sind kein Angebot, sondern eine Frage, die der Nutzer beantworten muss, bevor er
arbeiten darf.**"*

**Von den zwei verbliebenen trägt eine gar kein Ziel** und sagt warum (`:238-239`). **Beide Zahlen
stehen im Blatt, je mit dem, was sie messen.**

**Und der Kommentar `:234-237` trägt die alte Fassung noch** — *„drei Karten, drei Ziele"*. **Er
bleibt stehen: eine Ablesung ändert ihre Quelle nicht.**

## Ein Namenszusammenstoß, gefunden beim Öffnen von `PROJ`

```text
grep -rn "interface ProjektEintrag" resources/planner/hausplaner/
  app/studioDaten.ts:136     { name: string; icon: string; }
  app/state/projekte.ts:22   { id; name; ort; datum; adresse? }
```

**Zwei verschiedene Typen, derselbe Name — und `StartView.tsx` führt BEIDE Module ein** (`:4` und
`:5`). *Es geht heute gut, weil nur `:5` den Namen holt.*

> **Die Gefahr ist der nächste Griff:** *wer `ProjektEintrag` aus `:4` mitzieht, bekommt einen
> Konflikt oder den falschen Typ.* **Einmal eine Startkarte mit Bildzeichen, einmal ein echtes
> Objekt mit Kennung, Ort, Datum und Adresse.** *Dieselbe Klasse wie Yamas Namenswarnung zu `blocked`
> gegen `DECISION_BLOCKED` — nur hier ohne Auflage.* **Benannt, nicht behoben: ein Umbenennen wäre
> ein Bau in zwei fremden Werkzeugen.**

## `PROJ[2]` — zwei richtige Zahlen für dieselbe Sache

```text
studioDaten.ts:137-141   PROJ traegt DREI Eintraege
StartView.tsx            benutzt PROJ[0] und PROJ[1].  PROJ[2] NICHT.
```

**Wer `PROJ` zählt, findet drei Karten; wer die Fläche liest, findet zwei.** *Beide stimmen und
messen Verschiedenes.* **Steht das nicht im Blatt, meldet die nächste Rolle eine fehlende dritte
Karte.**

## W-33-6 · Acht Wächter, drei wörtlich — und zwei eigene Fehlgriffe dabei

**Alle acht berühren `StartView`** — *gemessen:* `grep -rln "StartView" __tests__/` **→ genau die
acht des Auftrags.**

| Wächter | Tests | Zusage |
|---|---|---|
| `startEhrlich` | **9** | **wörtlich:** *„der Startbildschirm sagt, was es gibt"* — inkl. beider Befunde |
| `projektKlick` | **15** | **wörtlich:** *„gemessen wird am echten Render-Pfad, nicht am Quelltext"* |
| `rohwertZusage` | **16** | **wörtlich:** *„eine Rohfarbe darf inline bleiben — aber nur solange sie keinen Token hat"* |
| `elevationTokens` | **9** | **rendert `StartView`** und nagelt den einen 30-px-Rohwert auf **genau ein** Vorkommen fest |
| `konfiguratorEhrlich` | — | die **fünfte Stelle**: die Schildzeile `:254` verspricht nicht mehr, als W-35 liefert |
| `stilschicht` | **4 Stellen** | *„jeder verbliebene Inline-Stil in `StartView` **hat einen Grund**"* |
| `breiten` · `dialogFokus` | — | `StartView` fällt aus keiner inselweiten Regel heraus |

**ZWEI eigene Fehlgriffe, beide beim Gegenlesen gefunden und im Blatt stehengeblieben:**

1. **„`projektKlick` ist der einzige, der rendert"** — *falsch.* **`elevationTokens.test.ts:23`
   importiert `StartView` und `:76` rendert es.** *Es sind zwei von acht. Ich hatte
   `elevationTokens` nach seinem Namen für einen reinen Token-Vergleicher gehalten.*
2. **`stilschicht` pauschal als „inselweit, eine Datei unter mehreren" abgetan** — *für `breiten` und
   `dialogFokus` trifft das, für `stilschicht` nicht:* **vier Stellen, drei davon eigene Tests**
   (`:154`, `:162`, `:173`, `:426`).

> **Beide Male derselbe Griff: eine Testdatei nach ihrer Überschrift eingeordnet, statt sie zu
> öffnen.** *Genau der Fehler, den ich bei W-39 einmal gemacht und in W-35 ausdrücklich vermieden
> habe.* **Er kommt zurück, sobald ich nicht daran denke — deshalb steht er im Blatt und nicht nur
> hier.**

## Eine eigene Zahl, korrigiert bevor sie im Blatt stand

**In `5-CODE` stand zuerst „sieben `import`-Zeilen".** *Gemessen:* `grep -c '^import '` **→ 6.**
*Die Zeilenspanne `:2-7` verführt zur Sieben — derselbe Fehler wie in W-35, wo ich `:9-19` gezählt
habe statt der Zeilen.*

**Dazu eine Feinheit, die im Blatt steht:** *sechs Zeilen, aber **fünf** Module — `:6` und `:7` holen
beide aus `studioUi`.* **Wer Module zählt, bekommt eine andere Zahl als wer Zeilen zählt.**

## W-33-8 · Sieben Blätter, Gegenprobe grün

```text
1-ZWECK 7ff1e6bd · 2-FUNKTION 7722aa08 · 3-FORMELN c8a3fa31 · 4-BEDIENUNG faea0f48
5-CODE/LIESMICH 698dc405 · 6-PRUEFUNG 72ec3810 · 7-GRENZEN 22a92784
tail -n +2 <blatt> | md5, gegen ALLE uebrigen Werkzeugblaetter: 0 Kollisionen
```

**Abschlusszähler:** `grep -cE '^\| W-[0-9]+ .*BESCHRIEBEN'` — **HEAD 19 → jetzt 20.**

## Was nicht gefahren wurde

| | |
|---|---|
| **Mutationsproben** | **keine gesetzt** — die Fangtabelle sagt es bei jeder Zeile einzeln |
| **Insel-Suite** | **nicht gefahren** — kein Produktivcode berührt |
| **Browserabnahme** | **offen, und hier nicht nebensächlich** — `projektKlick` sagt selbst: *„die Sichtprobe ist Teil der Abnahme, nicht ein Anhang"* |

## must_preserve und Rückweg

| Richtung | Ergebnis |
|---|---|
| geändert (`resources/`, `app/`) | **0** |
| hinzugefügt | **0** |
| entfernt | **0** |
| Rückweg | reine Neuanlage plus **eine** Registerzeile; `git revert` genügt |
