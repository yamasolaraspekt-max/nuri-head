# W-36 · Fähigkeiten-Navigation — PRÜFUNG

> **Ablesung, kein Bau.** *Die Kriterien unten prüfen das BLATT. Die Wächterliste darunter ist am
> BAU-STAND erhoben — keine Zahl stammt aus dem Auftragsblatt (W-36-5, Pflichtprüfung 8).*

## Abnahmekriterien dieses Blattes

| Nr | Kriterium | Wodurch wäre es rot | Wie gemessen |
|---|---|---|---|
| K-1 | **Alle VIER Statusachsen JE MIT TRÄGER** und Fundstelle | die Achsen ohne Träger aufzählen | `2-FUNKTION`, alle vier Stellen geöffnet |
| K-2 | Der **Kommentar-Befund** steht in `7-GRENZEN`, mit der Messung; der Code bleibt unberührt | den Kommentar berichtigen statt ihn zu melden | **7 Stellen in 3 Dateien**, gemessen |
| K-3 | Die **drei Typachsen** mit Werten und Fundstellen, die **neun Gruppen**, am Code gezählt | eine Zahl aus dem Auftragsblatt übernehmen | `:17`, `:22`, `:25`, `:46` |
| K-4 | Die **artabhängigen Felder** samt „Export ≠ Modulname, vom Guard-Test verriegelt"; der Test wird benannt | den Guard-Test nur nennen, ohne zu sagen, was er verriegelt | `faehigkeiten.test.ts:38`, Rumpf geöffnet |
| K-5 | **Je Test die ART des Zugriffs und die Zusage.** Die Zahl je Klasse am Bau-Stand | eine Zahl aus dem Blatt übernehmen | unten, alle zwölf einzeln geöffnet |
| K-6 | `WerkzeugAnzeige` als Achse **ohne Registereintrag** | die Lücke diesem Blatt anlasten | `7-GRENZEN` |
| K-7 | Die **Grenze zu W-37** in `2-FUNKTION`: `enginePanelRest` importiert W-36, besitzt es nicht | Besitz behaupten | `2-FUNKTION`, Scope-Block |
| K-8 | **Sieben Blätter**, Gegenprobe `tail -n +2 <blatt> \| md5` | zwei Werkzeuge mit gleichem Hash | Ergebnis im Bericht |

## W-36-5 · Die ZWÖLF Dateien in DREI Zugriffsarten

**Die Grundgesamtheit, am Bau-Stand:**

```text
grep -rlE "FaehigkeitenNavi|faehigkeiten" __tests__/     ->  12 Dateien
```

**Und die Aufteilung — jede der zwölf einzeln geöffnet:**

```text
IMPORT        7        NUR QUELLE    3        WORTZUFALL    2
```

### Klasse IMPORT — 7 Dateien

*Gemessen mit* `grep -lE "^import .*from '.*(tools/faehigkeiten|FaehigkeitenNavi)'"`.

| Datei | Was sie importiert | Die Zusage, die sie hält |
|---|---|---|
| **`faehigkeiten.test.ts`** | `FAEHIGKEITEN`, `FAEHIGKEIT_GRUPPEN`, `faehigkeitenNach`, `doppelteIds` | **der Guard-Test (`:38`)** — *jede Engine importiert REAL und der deklarierte Export existiert*; dazu `:10` „keine doppelten Kennungen" und `:90` „mindestens sechs Gruppen sind nicht leer" |
| `toolPresentation.test.ts` | `faehigkeitenNach`, `doppelteIds` | `:154` **Regressionsanker** — *`faehigkeitenNach('werkzeuge')` bleibt nach der Fach-Umsortierung stabil*; `:179` „`doppelteIds()` bleibt leer" |
| `schienenReiter.test.ts` | `alleFaehigkeiten` | `:95` **die Gesamtzahl in DREI Summanden** — `22 + EIGENE_WERKZEUGE + AUS_PAKET_GEHOBEN`; `:100` die Navi hängt an **genau einer** Stelle im Baum |
| `enginePanelRest.test.ts` | aus `faehigkeiten` | **W-37s Gegenstand** — *er importiert W-36 und besitzt es nicht* |
| `enginePanelSparren.test.ts` | *wortgleiche Importzeile* | ebenso |
| `enginePanelTgaHeizung.test.ts` | *wortgleiche Importzeile* | ebenso |
| `enginePanelTreppe.test.ts` | *wortgleiche Importzeile* | ebenso |

> **Die vier `enginePanel`-Tests tragen WORTGLEICH dieselbe Importzeile.** *Genau daran ist die erste
> Fassung des Auftrags gescheitert: drei davon standen unter NEIN, weil nur acht der zwölf Dateien
> geprüft worden waren.* **Eine Stichprobe ist keine Vollzählung, auch wenn sie zwei Drittel
> abdeckt.**

### Klasse NUR QUELLE — 3 Dateien

*Sie LESEN die Datei und prüfen Zeichenketten darin, ohne zu importieren.*

| Datei | Stelle | Die Zusage |
|---|---|---|
| `keineKappung.test.ts` | `:22` `readFileSync(… '../app/FaehigkeitenNavi.tsx')` | **Beschriftungen werden umgebrochen, nicht gekappt** — *„Horizont…", „Sparren-…" sind informationslos* |
| `gruppenzeileUndSchiene.test.ts` | `:102`, Marke `'<FaehigkeitenNavi'` | **das Markup steht NICHT ein zweites Mal in der Hauptfunktion** — *`assert.ok(schiene.includes(marke))` **und** `assert.ok(!app.includes(marke))`* |
| `stilschicht.test.ts` | `:679`, in der Dateiliste | **keine offene statische Inline-Stelle** — *eine Datei unter vielen* |

> **Die mittlere ist die interessanteste: sie prüft in BEIDE Richtungen.** *Der Kommentar sagt warum:*
> **„Ohne diesen Partner bliebe die Zusage oben grün, wenn beides nebeneinander stünde."** *Ein
> `includes` allein hätte den doppelten Einbau nicht gefunden — das ist B4 (Partner-Pflicht) in
> Testform.*

### Klasse WORTZUFALL — 2 Dateien

*Das Wort steht dort und meint etwas anderes. **Keine Zusage über W-36.***

```text
werkzeugRegistry.test.ts:14   faehigkeiten: { waehlbar, ziehbar, dupliziert, loeschbar }
                              -> ein FELD namens faehigkeiten an einem Werkzeug
ansichtBereit.test.ts:96      const kontext = (faehigkeiten: string[]) => ({ …
                        :101    capabilities: faehigkeiten,
                              -> ein PARAMETERNAME; er landet in `capabilities`
```

> **`ansichtBereit` ist der lehrreichere der beiden:** *dort heißt eine lokale Variable
> `faehigkeiten` und wird auf `capabilities` abgebildet — dasselbe deutsche Wort für eine ganz
> andere Sache.* **Wer nach dem Wort sucht, findet zwei Treffer, die wie Wächter aussehen und keine
> sind.**

## Der Guard-Test — der stärkste Wächter dieser Werkbank

```ts
faehigkeiten.test.ts:38
test('Guard (AP-E): jede Engine-Fähigkeit importiert REAL + der deklarierte Export existiert
      (Export ≠ Modulname)', async () => {
  const engines = FAEHIGKEITEN.filter((f) => f.art === 'engine');
  for (const e of engines) {
    assert.ok(e.engineModul && e.engineExport, …);
    const modul = (await import('../' + e.engineModul)) as Record<string, unknown>;
    assert.equal(typeof modul[e.engineExport as string], 'function', …);
  }
});
```

**Er beweist statt zu lesen** — *dynamischer Import, dann Typprüfung auf `'function'`.*

> **Damit ist er derselben Klasse wie `projektKlick` in W-33 (rendert statt zu lesen) und stärker
> als jede Textprobe:** *ein `grep` auf `engineModul` fände die Deklaration; nur der Import findet,
> **ob es das Modul und die Funktion wirklich gibt**.* **Der Kommentar nennt es „Verriegelung … per
> Beweis (Gegenbeweis)".**

**Und ein zweiter Test daneben hält die Zustandszusage — er ist schärfer, als ich erwartet hatte:**

```ts
faehigkeiten.test.ts:21-34
assert.equal(engines.length, 13);
const verfuegbar = engines.filter((e) => e.zustand === 'verfuegbar');
assert.deepEqual([...verfuegbar.map((e) => e.id)].sort(),
  ['engine-abwasser','engine-fbh','engine-fensterprodukt','engine-heizkoerper',
   'engine-kueche','engine-pv','engine-sp…'],           'genau die angeschlossenen Engines');
for (const e of engines) {
  if (!angeschlossen.has(e.id)) assert.equal(e.zustand, 'in_entwicklung', …);
  assert.ok(e.engineModul?.startsWith('geometry/'), …);
  assert.ok(e.eingang && e.ausgang, …);
}
```

**Er nagelt fest: DREIZEHN Engines, und `verfuegbar` heißt genau „angeschlossen".** *Namentlich, nicht
als Zahl — und sortiert verglichen, mit dem Grund im Kommentar:* **„damit die Zusage nicht an der
Zeilennummer im Register hängt."**

> **Damit ist der Zustand kein Anzeigewort, sondern eine geprüfte Zusage:** *eine Engine ohne Fläche
> **muss** `in_entwicklung` tragen.* **Meine eigene Messung — 8 von 13 `verfuegbar` — ist hier
> unabhängig belegt, und zwar mit Namen statt mit einer Zahl.**
>
> **Und die Fehlermeldung in `:31` benutzt das Phantomwort als Verb:** *„sollte **schlafen** (Fläche
> folgt in einer spaeteren Scheibe)".* **Achte Stelle desselben Begriffs — siehe `7-GRENZEN`.**

## Fangprobe (Mutationsprobe)

| Mutation | Muss erkannt werden von | Gemessen? |
|---|---|---|
| `engineExport` auf einen Namen ändern, den es nicht gibt | **`faehigkeiten.test.ts:38`** — *er importiert wirklich* | **nicht gefahren** |
| zwei Fähigkeiten dieselbe Kennung geben | `faehigkeiten:10`, `toolPresentation:180` | **nicht gefahren** |
| eine Fähigkeit aus `FAEHIGKEITEN` entfernen | **`schienenReiter:95`** — *und die Zerlegung sagt, WELCHE Gruppe* | **nicht gefahren** |
| `<FaehigkeitenNavi` zusätzlich in die Hauptfunktion setzen | **`gruppenzeileUndSchiene:102`**, zweite Richtung | **nicht gefahren** |
| einen fünften Wert in `StudioZustand` eintragen | **NIEMAND** — *die Navi reicht ihn nie durch; siehe `7-GRENZEN`* | *am Code abgelesen* |
| `projectState` auf einen unbekannten Wert setzen | **NIEMAND** — *es ist `string`; `arbeitsbereiche:120` tut es bereits* | *am Code abgelesen* |

> **Keine Mutation gesetzt, und das steht hier statt zu fehlen.** *Eine Fangprobe, die ich nicht
> fahre, ist keine Messung, sondern eine Erwartung.* **Die letzten zwei Zeilen tragen ohne Mutation,
> weil sie aus dem Typ folgen: ein `string` hat keine verbotenen Werte.**

## Automatische Tests

**`npm run test:hausplaner`** — **nicht gefahren.** *Dieses Blatt ändert keine Zeile Code.*

## Sichtprüfung und Bestandsprobe

- [ ] **offen** — *vier Punkte in `4-BEDIENUNG.md`.* **Der erste ist der einzige, der einen Nutzer
      betrifft: steht in der Fußzeile wirklich „schläft"?**
