# Baubericht A-24 — die Panel-Zusage trifft jetzt das Tor

```yaml
auftrag: "A-24"
rolle: "generator"
blatt: docs/auftraege/aktiv/A-24-die-panel-zusage-trifft-das-tor-nicht.md
art: "BAU · P1 — zwei Eingabefelder, ein Hinweistext, eine Bedingung, ein Waechter"
basis_sha: 7b9ad18c
bau_sha: 0c9aa0a9
gebaut_am: "12.08.2026"
zustand: CODE_FERTIG
ballbesitz: evaluator
```

> **Die Browserabnahme hat einen Fehler gefunden, den kein Test finden konnte:** *das ausgelieferte
> Bündel war vom **4. Juli** und kannte meinen Bau nicht.* **Alle 1718 Tests waren grün, und der
> Nutzer hätte weiterhin den alten Text gesehen.** *Abschnitt „Was nur der Browser gefunden hat".*

## Was gebaut wurde

```text
resources/planner/hausplaner/app/rahmen/EigenschaftenPanel.tsx   3 Stellen
resources/planner/hausplaner/__tests__/anbauTorZusage.test.ts    NEU, 9 Zusagen
scripts/a24-browserabnahme.mjs                                   NEU, der Ablauf-Beleg
```

**`dachMesh.ts` ist NICHT dabei** — *A-24-4, das Tor bleibt unberührt.*

## A-24-1 · Die Falschauskunft steckte in der KOPPLUNG

```ts
vorher   const fehlt = !a || !(a.length > 0) || !(a.width > 0)
                       || (istU && (!(a.lengthB && a.lengthB > 0) || !(a.widthB && a.widthB > 0)));
nachher  const fehlt = !a || !(a.length > 0) || !(a.width > 0)
                       || !(a.lengthB && a.lengthB > 0) || !(a.widthB && a.widthB > 0);
```

**Das `istU &&` war der Kern.** *Bei L/T wurden die zwei Anbaumaße nicht geprüft — wer die zwei
genannten füllte, sah die Warnung **verschwinden** und bekam trotzdem kein Dach.*

> **Wer nur den Text ehrlich gemacht hätte, wäre nach A-24-1(a) grün gewesen und der Fehler
> geblieben.** *Das Auftragsblatt sagt es vorab: „die Falschauskunft steckt nicht im Wortlaut
> allein, sondern in der Kopplung von Wortlaut und Sichtbarkeit."* **Beide Richtungen sind gebaut.**

**Und der Text nennt jetzt die tatsächliche Torbedingung:**

```text
vorher   „L/T-Dach braucht Außenmaß Länge und Breite > 0 — sonst rendert es nicht."
nachher  „L/T-Dach braucht alle vier Maße > 0 (Außen Länge/Breite + Anbau Länge/Breite)
          — sonst rendert es nicht."
```

## A-24-2 · Die zwei Felder, mit der Bezeichnung aus dem Bestand

**Vorher standen sie hinter `{istU && (…)}`** — *die Felder existierten, waren für L/T aber
unerreichbar.* **Jetzt stehen sie immer, mit formabhängiger Beschriftung:**

```ts
{istU ? 'Innenhof/Kerbe Länge (mm)' : 'Anbau Länge (mm)'}
{istU ? 'Innenhof/Kerbe Breite (mm)' : 'Anbau Breite (mm)'}
```

**Die Bezeichnung ist nicht erfunden:** *`geometry/dachVerschneidung.ts:25` führt
`lengthB: number; widthB: number;  // L_b, W_b (Anbau)`.* **Für U bleibt „Innenhof/Kerbe"
unverändert — dieselben Werte, andere fachliche Lesart.**

## A-24-5 · Der Wächter hält die KOPPLUNG, nicht den Wortlaut

**`__tests__/anbauTorZusage.test.ts`, neun Zusagen. Die tragende:**

```ts
test('A-24: Panel-Bedingung und Tor-Bedingung nennen DIESELBEN vier Felder', () => {
  const felder = (s) => ['length','width','lengthB','widthB'].filter((f) => new RegExp(`\\.${f}\\b`).test(s));
  assert.deepEqual([...felder(fehltZeile())].sort(), [...felder(torBedingung())].sort());
});
```

> **Ein Test auf den Satz wäre beim nächsten Umbau des Tors NICHT rot geworden** — *er hätte weiter
> einen Text bewacht, während die Bedingung dahinter wegwandert.* **Dieser wird rot, sobald einer
> der beiden Orte ein Maß verliert.**

**Dazu:** *kein `istU` mehr in der Warn-Bedingung; das Tor verlangt weiter vier; vier `onChange`-
Felder; die Beschriftung je Form; und die Schutzgrenze A-24-3 als **Eigenschaft** — jeder
`setzeAnbau`-Aufruf kommt aus `onChange`, `useEffect` kommt 0 mal vor.*

## Zwei Fehler steckten in MEINEM Test, nicht im Bau

**Beide gefangen, weil ich ihn gefahren habe statt ihn für grün zu halten:**

```text
1  Mein Tor-Muster war  if \(([^)]*)\)  — und `[^)]*` bricht an der geschachtelten
   Klammer `(a.lengthB && a.lengthB > 0)` ab, also MITTEN in der Bedingung.
   Der Test wurde rot und zeigte auf den Bau, waehrend der Fehler im Messmuster stand.
   Behoben: bis zum ersten `return null;` gelesen.

2  Meine Aufrufzaehlung zog eine 1 fuer die Definition ab — die das Muster
   `setzeAnbau\(` gar nicht erfasst, weil dort `setzeAnbau = (` steht.  3 gegen 4.
   Behoben: zwei getrennte Zusagen, vier Aufrufe und vier davon aus onChange.
```

**Beide Ursachen stehen als Kommentar IM Test** — *nicht nur hier.*

## A-24-6 · Die Fangprobe ist GEFAHREN

```text
md5-Anker      c0a7d5d59d04a7211b60244a9489864f
Mutation       die vierte Bedingung (widthB) aus `fehlt` entfernt
Ergebnis       ZWEI Zusagen rot:
                 „die `fehlt`-Bedingung des Panels prüft ALLE VIER Maße"
                 „Panel-Bedingung und Tor-Bedingung nennen DIESELBEN vier Felder"
Rueckschrift   md5 gegen den Anker: identisch
```

## A-24-7 · Die Browserabnahme, als Ablauf

**Bühne:** `scripts/browser-buehne.sh --port 8099`. *Vor der Abnahme am **Kindprozess** gemessen:
`APP_ENV=testing`, und `config('database…')` löst auf **`ticket_testing`** auf — nicht `ticket`.*

**Saat** *(Scratchpad, nicht im Repo)*: ein Admin-Nutzer, ein Objekt, ein Dokument mit **einem
L-Dach ohne `anbau`** — genau der Ausgangszustand des Befundes.

**Der Ablauf, zehn Schritte, alle belegt:**

```text
✔ Anmeldung                          -> /home
✔ Insel geoeffnet                    -> „Hausplaner — A-24 Abnahmeobjekt"
✔ Expertenmodus geoeffnet
✔ L-Dach ausgewaehlt                 -> Klick in die Zeichenflaeche, „Anbau / Verschneidung" da
✔ Warnung OHNE Masse                 -> „…braucht alle vier Maße > 0 (Außen … + Anbau …)"
✔ Die vier Felder sind da            -> Aussenmass L/B  +  Anbau L/B
✔ Nach ZWEI Massen BLEIBT die Warnung
✔ Nach VIER Massen ist die Warnung weg
✔ Alle vier Masse stehen im Modell   -> 10000 · 8000 · 4000 · 3000
✔ Das Tor laesst durch
```

> **Der Kern des Auftrags ist Schritt 7:** *nach zwei Maßen bleibt die Warnung.* **Genau dort log
> die Fläche vorher.**

**Was NICHT belegt ist:** *dass die 3D-Geometrie sichtbar wird.* **Das misst kein Textausleser** —
*der Bildschirmabzug liegt bei, und er zeigt „L-Dach", vier gefüllte Felder und keine Warnung.*

## Was nur der Browser gefunden hat

```text
public/build/manifest.json   4. Juli 01:49
```

**Das ausgelieferte Bündel war fünf Wochen alt.** *Der erste Abnahmelauf zeigte den **alten** Text
und nur **zwei** Felder — bei grüner Suite und grünem `tsc`.*

> **Ein Quelltext-Test kann nicht sehen, was ausgeliefert wird.** *`npm run build:hausplaner`
> erzeugt `public/hausplaner/hausplaner.js`; erst danach trug das Bündel meinen Bau
> (`grep -c` auf den neuen Satz → **1**).* **Das ist der Grund, warum die Arbeitsregeln für
> UI-Arbeit eine reale Browserabnahme verlangen, und diesmal hat sie ihren Preis eingespielt.**
>
> **Und ein Nebenfund dazu:** *mein erster Griff war `npm run build` — der baut die CRM-Seite und
> **nicht** die Insel.* **Die Insel hat einen eigenen Befehl.** *Gemessen statt angenommen:
> `public/build` ist zudem **nicht** versioniert, der Fehlgriff hat also nichts hinterlassen.*

## Drei eigene Fehlgriffe bei der Saat — und die Insel hat jeden benannt

```text
1  lead_id zeigt auf `new_leads`, nicht auf `leads`. Ich hatte den SPALTENNAMEN
   fuer den Tabellennamen gehalten; der Fremdschluessel-Constraint hat es widerlegt.
2  Die Szene lag in `nodes[]`. Daecher stehen in einer EIGENEN Sammlung `roofs[]`
   (scene.types.ts:46) — „additiv, damit die Node-Union unberuehrt bleibt".
3  Meine Ebene trug `visible` und `locked`. `Level` hat GENAU sechs Felder
   (:61-70) und keines davon. Ein Feld zu viel ist so falsch wie eines zu wenig.
```

> **Die Insel hat jeden dieser Fehler EINZELN gemeldet, mit Feldnamen** — *und den Plan
> ausdrücklich nicht geladen:* **„Der Plan wurde NICHT geladen, damit nichts stillschweigend
> verloren geht."** *Das ist das genaue Gegenteil des Fehlers, den dieser Auftrag behebt: hier sagt
> die Fläche, was fehlt, statt eine Bedingung zu nennen, die nicht die Bedingung ist.*

## must_preserve und Rückweg

| Richtung | Ergebnis |
|---|---|
| geändert unter `resources/` | **1** — `EigenschaftenPanel.tsx`, der Gegenstand des Auftrags |
| hinzugefügt | **2** — der Wächter und das Abnahmeskript |
| entfernt | **0** |
| `dachMesh.ts` | **unberührt** (A-24-4) |
| Insel-Suite | **1718 / 0** *(vorher 1709; die neun Zusagen kommen dazu)* |
| `tsc:hausplaner` | **ohne Ausgabe** |
| Rückweg | `git revert` des Bau-Commits; die drei Stellen sind zusammenhängend |
