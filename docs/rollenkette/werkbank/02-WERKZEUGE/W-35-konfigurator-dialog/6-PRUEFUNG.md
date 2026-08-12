# W-35 · Konfigurator-Dialog — PRÜFUNG

> **Ablesung, kein Bau.** *Die Kriterien unten prüfen das BLATT. Was den CODE prüft, steht darunter
> als Wächterliste — sechs Dateien, jede geöffnet.*

## Abnahmekriterien dieses Blattes

| Nr | Kriterium | Wodurch wäre es rot | Wie gemessen |
|---|---|---|---|
| K-1 | Die **vier Arten** stehen mit Zeile, und die **Registerzeile ist korrigiert** | drei Arten schreiben, weil der Dateikopf drei nennt | `ConfigWizard.tsx:23`, geöffnet · `REGISTER.md:122` vorher/nachher |
| K-2 | Die **fünf Schritte** aus `:34` **am Code gezählt**, dazu die Navigation | die Zahl aus dem Auftragsblatt übernehmen | `:34`, `:53`, `:86-90` |
| K-3 | **`TYP_MAP` (`:43`)** ist als Abbildung auf `ConfiguratorType` beschrieben | die Bedienung ohne ihr Ergebnis beschreiben | `:43` und die einzige Benutzung `:233` |
| K-4 | Die **vier Zustände** mit **artabhängigen** Vorbelegungen, **vollständig** | nur die drei Werte des Auftragsblatts nennen, die Rückfälle `1010`/`2010` weglassen | `:47-50`, Tabelle in `2-FUNKTION` |
| K-5 | Die **Grenze zu W-42** steht in `2-FUNKTION` und ist **gespiegelt** | den Schreibpfad hier ein zweites Mal beschreiben | W-42s Blatt, Abschnitt „W-35 ist alles bis zur Auswahl" |
| K-6 | **Sechs Wächter**, je mit der Zusage — für `konfiguratorEhrlich` **wörtlich** | sechs Namen aufzählen; ein Name ist keine Aussage | jede Datei geöffnet, Zusagen unten |
| K-7 | `7-GRENZEN` nennt, **was der Dialog nicht kann**, und den `standalone`-Zweig | „keine Grenzen gefunden" | `7-GRENZEN.md` |
| K-8 | **Sieben Blätter**, Gegenprobe je Blatt `tail -n +2 <blatt> \| md5` | zwei Werkzeuge mit gleichem Hash | Ergebnis im Bericht |

## Die sechs Wächter — je mit der Zusage, die sie halten

**Alle sechs lesen `ConfigWizard.tsx`** — *gemessen: `grep -rln "ConfigWizard" __tests__/` liefert
genau diese sechs Dateien.*

### 1 · `konfiguratorEhrlich.test.ts` — 136 Zeilen, **11 Tests**

**Die Zusage, wörtlich aus dem Dateikopf (`:2`):**

> ***„AUF-74 — der Konfigurator sagt, was wirklich passiert."***

**Was er dafür festhält, jede Zusage einzeln:**

```text
K3  drei Stellen nennen das ERGEBNIS und versprechen nichts
    :39  kein „verlustfrei"          :40  „gespeichert in deiner Paketliste" (seit AUF-81 wahr)
    :47  kein „speicherbar"          :54-56 die Meldung haengt am tatsaechlichen Ausgang
K4  der Download bleibt unveraendert — a.download, revokeObjectURL, derselbe Dateiname  :61-64
K5  kein Versprechen auf spaeter: „folgt", „in Kürze", „geplant", „demnächst"           :68-73
K6  der WAHRE Zweig bleibt Zeichen fuer Zeichen stehen — „als ein Command ins
    Gebäudemodell, Undo/Redo inklusive"                                                 :78-79
    und die drei Platzierungsmeldungen                                                  :83-89
4.  der verschluckte Fehlerfall: „Download optional" ist weg, `entstanden` traegt ihn    :97-108
5.  die FUENFTE Stelle — dieselbe Zusage stand auf dem STARTBILDSCHIRM                   :112-122
    „Fenster, Türen, Treppen und Heizkörper setzt der Experte ins Gebäude"
—   und die VIER Arten sind festgenagelt                                                :126
—   kein Umbau: SCHRITTE und die Paketstruktur bleiben                                   :133-135
```

> **Der Dateikopf sagt auch, warum die Prüfungen ENG sind, und der Satz ist lehrreich:** *„Ein
> breiter `grep` auf ‚speichern' findet den Zweig, der die **Wahrheit** sagt … und meldet ihn als
> Fehler. Geprüft wird deshalb jede Stelle einzeln, nicht die Datei im Ganzen."* **Das ist H-8, von
> einem Test aus formuliert.**

### 2 · `configWizardWrite.test.ts` — 85 Zeilen, **3 Tests**

**Zusage:** *dass jede der vier Arten als der richtige Knotentyp im Modell landet* — **einer je
Bauteilart, `window`/`door` zusammen in einem.**

```text
· Fenster mit Bauart landet als OpeningNode auf der Wand
· Treppe landet als ObjectNode(stair) mit typ im Modell
· Heizkoerper landet als ObjectNode(radiator) mit objekt-Parametern
```

**Dieser Wächter gehört fachlich zu W-42** *(dem Schreibpfad)* — *er steht hier, weil er dieselbe
Datei liest und weil W-35s Bedienung ohne ihn wie ein Sackgassen-Dialog aussähe.*

### 3 · `paketSpeichern.test.ts` — 126 Zeilen, **12 Tests**

**Zusage:** *dass Download und Speichern **zwei** Wege sind und jeder einzeln gemeldet wird.*

```text
:88-90   der Download unveraendert
:94      void speicherePaket(art, wahl.label, paket)  — zusaetzlich, nicht statt
:96-97   jeder Weg meldet sich EINZELN
:102-104 kein Erfolg ohne Ergebnis; „Download optional" bleibt weg
:108-114 die Textstellen folgen kannPaketSpeichern() — der Text folgt der Wirklichkeit
```

### 4 · `breiten.test.ts` — 76 Zeilen, **5 Tests**

**Zusage:** *dass der Dialog auf schmalen Geräten **stapelt** statt sich zu überlagern.*

```text
:37    „Die Regel stand als Inline-Stil in ConfigWizard.tsx und steht jetzt als .hp-kw-koerper"
:42    beides wird gelesen und verglichen
:72    ConfigWizard steht in der Liste der Dateien, die keine feste zweite Spalte mehr tragen
```

*Herkunft laut Code-Kommentar `:80-81`: **AUF-46** — „auch hier stand eine feste zweite Spalte
(`1fr 300px`) … Bei 390 px stapeln die Spalten jetzt."*

### 5 · `dialogFokus.test.ts` — 113 Zeilen, **11 Tests**

**Zusage:** *dass der Dialog ein Dialog IST — für Tastatur und Vorlesehilfe.*

```text
:101  „der ConfigWizard hatte NICHTS davon — jetzt hat er alles"
:103   role="dialog"  ·  aria-modal  ·  useDialogFokus      alle drei muessen dastehen
:108  „kein Dialog baut seinen Escape-Handler mehr selbst"
:110   assert.doesNotMatch(q, /addEventListener\('keydown'/)
```

*Herkunft laut Code-Kommentar `:61-64`: **AUF-49** — „Diese Fläche trug bis hierher KEINE
Dialogsemantik … der erste Tab-Sprung landete HINTER dem Dialog."*

### 6 · `stilschicht.test.ts` — 815 Zeilen, **58 Tests**, davon einer für diese Datei

```text
:458  const KONFIG = join(hier, '../app/ConfigWizard.tsx');
:460  „Scheibe 5 (Wirkung): in ConfigWizard bleibt KEINE offene statische Stelle"
```

> **Nur EINER der 58 Tests betrifft W-35.** *Das steht hier ausdrücklich, weil „58 Tests" als Zusage
> für dieses Werkzeug falsch wäre* — **dieselbe Falle, in die ich bei W-39 gelaufen bin, als ich
> `stilschicht` nach seiner Überschrift eingeordnet habe, statt zu messen, welche Tests den
> Gegenstand berühren.**

## Fangprobe (Mutationsprobe)

| Mutation | Muss erkannt werden von | Gemessen? |
|---|---|---|
| `heizkoerper` aus `KonfigArt` entfernen | **`konfiguratorEhrlich.test.ts:126`** — der Typ steht dort Zeichen für Zeichen | **nicht gefahren** — die Zeile ist gelesen, die Mutation nicht gesetzt |
| „gespeichert" schreiben, wo nur heruntergeladen wurde | `konfiguratorEhrlich` K3 (`:54-56`), `paketSpeichern` (`:96-97`) | **nicht gefahren** |
| `role="dialog"` entfernen | `dialogFokus.test.ts:101-104` | **nicht gefahren** |
| eine fünfte `KonfigArt` ohne `TYP_MAP`-Eintrag | **der Typprüfer**, nicht ein Test — `Record<KonfigArt, …>` | **nicht gefahren** |
| eine fünfte `KonfigArt` ohne `katalogFür`-Zweig | **NIEMAND** — `:40` ist ein `return` ohne `if`, sie bekäme den Heizkörper-Katalog | *aus dem Code abgelesen, nicht mutiert* |

> **Keine dieser Mutationen ist gefahren worden, und das steht hier, statt es zu verschweigen.**
> *Eine Fangprobe, die ich nicht gesetzt habe, ist keine Messung — sie ist eine Erwartung.* **Die
> letzte Zeile ist die einzige, deren Aussage ohne Mutation trägt: dort steht kein `if`, und das ist
> am Code ablesbar.**

## Automatische Tests

**`npm run test:hausplaner`** — *die Insel-Suite.* **Nicht gefahren:** *dieses Blatt ändert keine
Zeile Code, und eine Ablesung braucht keinen grünen Lauf, um wahr zu sein.*

## Sichtprüfung und Bestandsprobe

- [ ] **offen** — *drei Punkte für die nächste Browserrunde stehen in `4-BEDIENUNG.md`, aus der
      Ablesung abgeleitet.*
