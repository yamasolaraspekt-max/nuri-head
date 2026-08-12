# Baubericht W-40 — Gültigkeitsstatus, vorgegeben

```yaml
auftrag: "W-40"
rolle: "generator"
blatt: docs/auftraege/aktiv/W-40-gueltigkeitsstatus.md
art: "STUFE 6 · VORGABE (Ziel ENTWORFEN) — kein Produktivcode"
in_arbeit_commit: "als Beifang in 6e9e5579"
gebaut_am: "12.08.2026"
zustand: CODE_FERTIG
ballbesitz: evaluator
```

> **Der wichtigste Teil dieses Berichts ist ein Befund gegen die Prämisse des eigenen Auftrags** —
> *und die Entscheidung, trotzdem zu bauen statt anzuhalten.*

## Was gebaut wurde

```text
docs/rollenkette/werkbank/02-WERKZEUGE/W-40-gueltigkeitsstatus/
  1-ZWECK.md  2-FUNKTION.md  3-FORMELN.md  4-BEDIENUNG.md
  5-CODE/LIESMICH.md  6-PRUEFUNG.md  7-GRENZEN.md
REGISTER.md   Zeile 127:  LEER -> ENTWORFEN
```

**Kein Produktivcode.** *`studioDaten.ts` ist gelesen, nicht geändert — W-38 ist betriebsbestätigt
und bleibt unberührt.*

## W-40-1 · Die zwei Achsen, am Bau-Stand zitiert

**`BERICHT-PROZESSEBENE-DREI-FRAGEN.md:130-132`** — *Zeile am Bau-Stand gemessen, nicht aus dem
Auftragsblatt übernommen (Pflichtprüfung 8):*

> *„Die vier vorhandenen Stufen beschreiben **Fortschritt**; die drei fehlenden beschreiben
> **Gültigkeit**. Das sind zwei Achsen, nicht eine längere Liste."*

**Damit ist der offene Anschluss aus W-38s `7-GRENZEN` beantwortet** — *dort stand die Frage, ob
W-40 ein zweites Statussystem einführt.* **Es ist die zweite Achse, keine zweite Wahrheit: ein
Schritt kann `ok` sein und trotzdem nicht `confirmed`.**

## W-40-2 · Je Stufe nur, was die Quelle sagt

```text
confirmed   trennt „gerechnet" von „vom Nutzer bestaetigt"; traegt L-9      :127-129
outdated    die INVALIDIERUNG, Kern von „niemals stille Loeschung"          :129-130
blocked     die Sperre                                                      :130
```

**Zu `blocked` sagt die Quelle vier Wörter, und mehr steht deshalb auch im Blatt nicht.**

## W-40-3 · Die Zahlenlücke — gestellt, nicht beantwortet

```text
Zielbild acht (:117) · gebaut vier · als fehlend drei  ->  4 + 3 = 7
Die achte ist review-required, in :121 mit einem GEDANKENSTRICH gefuehrt,
nicht mit „fehlt", und in der Einordnung nicht mitgezaehlt.
```

**Die Frage gehört Yama.** *Entweder ist `review-required` bewusst nicht Teil der Achse, oder die
Zahl DREI ist zu niedrig — ich erfinde keine Erklärung.*

## W-40-4 · `blocked` gegen `DECISION_BLOCKED`

**Nicht belegt.** *§3 führt `DECISION_BLOCKED` als „eine ausdrücklich Yama vorbehaltene Entscheidung
fehlt". Ob `blocked` dasselbe eine Ebene tiefer ist, steht nirgends — und wer sperrt und entsperrt,
auch nicht.* **In `7-GRENZEN` als Lücke der Vorgabe benannt.**

## W-40-5 · Übergänge — die Quelle gibt keine her

**Ausdrücklich festgestellt statt erfunden.** *Was die Quelle logisch erzwingt, steht in
`2-FUNKTION` und ist genau zweierlei: `outdated` muss aus einem gültigen Zustand erreichbar sein,
und `confirmed` muss vor einer PV-Belegung prüfbar sein.* **Alles Weitere wäre eine erfundene
Tabelle — und die wäre laut Kriterium schlimmer als ein benanntes Fehlen.**

## W-40-6 · Der Bezug zu W-38, mit Fundstelle

```ts
studioDaten.ts:163   export type SchrittStatus = 'ok' | 'prog' | 'warn' | 'open';
studioDaten.ts:255   export const STATUS_LABEL: Record<SchrittStatus, string>
```

**W-40 tritt NEBEN sie und nicht in sie hinein** — *`2-FUNKTION` zeigt beide Formen nebeneinander,
die falsche und die richtige.* **`Record<SchrittStatus, string>` ist selbst der Beleg: käme
`confirmed` hinzu, müsste es ein Fortschrittswort bekommen, und es ist keines.**

## W-40-7 · Sieben Blätter, Gegenprobe grün

```text
Blatt                W-40      Vorlage   gleich?   Dublette unter 27 Werkzeugen?
1-ZWECK.md           44dcec08  e921aa08  nein      keine
2-FUNKTION.md        ee48b08e  20e1ac73  nein      keine
3-FORMELN.md         8357f620  a7d05b09  nein      keine
4-BEDIENUNG.md       3f319fba  9845bcf1  nein      keine
5-CODE/LIESMICH.md   9bbabcec  619cf07e  nein      keine
6-PRUEFUNG.md        a409f8c5  719012f0  nein      keine
7-GRENZEN.md         927722f6  a5b225f8  nein      keine
```

## Der Befund gegen die eigene Prämisse — und warum ich trotzdem gebaut habe

**Das Auftragsblatt sagt „Es gibt KEINEN Code: die drei Stufen fehlen im Bestand."** *Für
`SchrittStatus` stimmt das. Für die Insel nicht.*

```text
grep -rE "'(confirmed|outdated|blocked)'" resources/planner/hausplaner
  app/studioDaten.ts                 0
  geometry/configuratorPackage.ts    4      <- jede Stelle GEOEFFNET, weil ein Wort kein Beleg ist
  geometry/integrationAbgleich.ts    1
```

**`configuratorPackage.ts` trägt eine vollständige Gültigkeitsachse:** *sieben Stufen,
`STATUS_UEBERGAENGE` als volle Übergangstabelle mit dem Grundsatz „bewusst streng … keine stille
Rückstufung", `statusUebergangErlaubt` als Wächter, `kannIntegrieren` als Tor und `markiereVeraltet`
als Invalidierung.* **Gebaut, getestet (`configuratorPackage.test.ts:32-61`) und in Gebrauch
(`integrationAbgleich.ts:13,134`).**

**Drei Folgen, keine davon entscheide ich:**

1. **`outdated` existiert samt Übergängen** — *eine zweite Tabelle daneben wäre die zweite Wahrheit,
   die W-40 verhindern soll.*
2. **`approved` spielt fachlich die Rolle von `confirmed`** — *nur ein `approved`-Paket darf
   übernommen werden.*
3. **`markiereVeraltet` ist die Invalidierung** — *damit ist auch W-41s „kein Code" zu weit.*

### Ich hatte angehalten, und das war zu viel

> **In der Runde davor habe ich geschrieben: „Ich baue nicht weiter, bevor das geklärt ist."**
> *Das habe ich revidiert, und der Grund ist messbar:* **kein einziges der sieben Kriterien wird
> durch den Befund unerfüllbar.** *Er trifft das **Ziel** (`ENTWORFEN` gegen Ablesung) — eine
> Planner-Frage —, nicht die Kriterien.*
>
> **Einen erfüllbaren Auftrag anzuhalten blockiert §3 für alle fünf Rollen.** *Die richtige Form ist
> die, die W-40-5 selbst vorgibt: bauen, und das Fehlende benennen statt es zu erfinden.* **Die
> Frage steht unverändert offen — sie steht jetzt nur in einem fertigen Blatt statt in einer
> Wartezeit.**

**Die Frage an Planner und Plan-Prüfer, unverändert:** *trägt `ENTWORFEN` noch, wenn eine
Gültigkeitsachse mit Übergängen bereits gebaut ist — oder ist W-40 eine **Ablesung mit
Erweiterung**?* **Dieselbe Klasse wie die Abweichung, die der Planner bei W-42 selbst benannt hat.**

## must_preserve und Rückweg

| | Ergebnis |
|---|---|
| `resources/**` · `app/**` | **0 Dateien geändert** — nur gelesen |
| `docs/STATUS.md` | nur W-40s eigener Zustand |
| Rückweg | reine Neuanlage plus **eine** geänderte Registerzeile; `git revert` genügt |
