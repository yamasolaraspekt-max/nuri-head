# A-35 — Trimmen: das erste Zwei-Objekt-Werkzeug. Die Mathematik liegt, die Bedienung ist entschieden, es fehlt das Werkzeug

```yaml
auftrag: "A-35"
werkzeug: "trimmen  (erstes Werkzeug nach A7; Muster fuer vier weitere)"
art: "BAU — ein Werkzeug in die Registry, das vorhandene Geradenmathematik ueber die
      vorhandene Mehrfachauswahl bedienbar macht. KEINE neue Mathematik, KEIN neues
      Schema, KEIN neuer Dialog."
spur: A
heimat_app: ticket
dor_beleg: "steht aus — plan-pruefer."
status_steht_in: docs/STATUS.md
basis_sha: 1df82ee1
prioritaet: P2
ballbesitz: "plan-pruefer (DoR)"
claim: "planner 13.08. — Claim VOR dem Schnitt."
kennung_geprueft: "A-35 hat NULL Treffer in docs/STATUS.md und NULL Blaetter in
                   docs/auftraege/aktiv/. A-01 bis A-34 sind vergeben. Frei."
keine_dublette: "Gemessen, BEVOR geschnitten. 'trimmen' kommt in SECHS Dateien vor, alle geoeffnet:
                 werkzeugVertrag.ts (Vertragszeile mit eingaben selectionIds und
                 operationParameters), werkzeugThemen.ts, werkzeugPaket.ts, toolPresentation.ts
                 und werkzeugLandkarte.ts sind Katalog-, Themen- und Selbstauskunftseintraege,
                 geradenGeometrie.ts nennt es im Kopfkommentar als Verbraucher.
                 ENTSCHEIDEND: app/tools/toolRegistry.ts hat NULL Treffer auf 'trimmen' —
                 es gibt kein Werkzeug, nur seine Beschreibung. Dasselbe gilt fuer die
                 vier Geschwister teilen, verbinden, verlaengern, versatz."
anlass: "Yama hat am 13.08. das Bedienmodell bestaetigt (ANFORDERUNGEN.md A7) und auf die Frage
         'naechster Schritt' geantwortet: Bedienmodell entscheiden, dann bauen. Damit faellt das
         Hindernis, das acht Werkzeuge als nicht baubar gefuehrt hat. Das Register sagt 23 von 43
         BESCHRIEBEN und EINS GEBAUT — der Werkzeugkasten ist eine sehr gute Landkarte und noch
         kein Werkzeugkasten. Dies ist der erste Bau nach A7."
grundlage: "ANFORDERUNGEN.md A7 (Bedienmodell, von Yama bestaetigt) ·
            geometry/geradenGeometrie.ts:84 geradenSchnitt (A-32, betriebsbestaetigt, 9 Tests,
            NULL Produktivaufrufer) · store/hausplanerStore.ts:30 selectedNodeIds als string[] ·
            app/tools/auswahlModus.ts (Reihenfolge und primaerId) ·
            app/HausplanerApp.tsx:815 waehleAn (die eine Auswahlstelle) ·
            app/tools/werkzeugVertrag.ts (Vertragszeile trimmen) ·
            store/hausplanerStore.ts executeCommands (A-31, eine Operation = ein Undo-Schritt)"
```

## Warum genau dieses Werkzeug zuerst

**Es ist der einzige Kandidat, bei dem alle Vorbedingungen schon erfüllt sind** — je selbst
nachgemessen am Stand `1df82ee1`:

| Vorbedingung | Stand | Beleg |
|---|---|---|
| Mathematik | **liegt** | `geradenGeometrie.ts:84 geradenSchnitt`, 196 Z., **9 Tests grün** |
| Bedienmuster | **entschieden** | `ANFORDERUNGEN.md` **A7**, von Yama bestätigt 13.08. |
| Auswahl mit Rollen | **gebaut** | `selectedNodeIds: string[]`, `primaerId` = zuletzt geklickt |
| Undo-Klammer | **gebaut** | `executeCommands` (A-31) |
| Werkzeug | **fehlt** | `toolRegistry.ts`: **0 Treffer** |

**Die Lage ist zeichengleich die von W-27/1: die Engine läuft, die Bedienung fehlt.** Dort hat
sich der Bau als tragfähig erwiesen; hier ist er kleiner, weil die Bedienung nicht erst erfunden
werden muss.

**Und es ist ein Muster, kein Einzelstück:** `teilen`, `verbinden`, `verlaengern`, `versatz`
tragen im Vertrag **dieselbe** Eingabesignatur (`selectionIds` + `operationParameters`). Was hier
gebaut wird, ist die Vorlage für vier weitere. **Deshalb wiegt jede Festlegung in diesem Auftrag
mehr als ein einzelnes Werkzeug.**

## Scope — was gebaut wird

1. **Ein Registry-Eintrag `trimmen`** in `app/tools/toolRegistry.ts`, der die vorhandene
   Vertragszeile bedient.
2. **Die Rollenzuweisung nach A7:** alle vorgewählten Objekte sind **Schnittkanten**
   (Nebenrolle), das **zuletzt** angeklickte ist das **zu kürzende Objekt** (Hauptrolle,
   `primaerId`).
3. **Die Übersetzung an der Vertragsgrenze** — A7 Konsequenz 2: `selectionIds` (Vertrag) auf
   `selectedNodeIds` (Store), **an genau einer Stelle**, mit erhaltener Reihenfolge.
4. **Der Aufruf von `geradenSchnitt`** und die Anwendung des Ergebnisses als **ein** Kommando
   über `executeCommands` (A-31), damit ein Trimmvorgang **ein** Undo-Schritt ist.
5. **Tests** für die Rollenzuweisung und die Grenzfälle aus Abschnitt „Kanten".

## Nicht-Ziele — ausdrücklich, damit der Scope nicht wandert

- **KEIN `ConfigWizard`-Dialog.** A7 Konsequenz 1 schließt ihn für diese Werkzeugklasse aus.
- **KEINE neue Geometriefunktion.** `geradenSchnitt` ist gebaut und geprüft; wer sie nachbaut,
  erzeugt die zweite Wahrheit. Fehlt etwas, ist das ein **Befund an den Planner**, kein Zubau.
  **Ausgenommen und ausdrücklich erlaubt (K3-Präzisierung):** eine zweite exportierte Funktion in
  `geradenGeometrie.ts`, die `t` und `u` liefert — sie baut nichts nach, sondern gibt eine bereits
  gerechnete Größe heraus. **Die Signatur von `geradenSchnitt` bleibt unverändert.**
- **KEINE der vier Geschwister** (teilen, verbinden, verlängern, versatz). Sie folgen dem Muster,
  aber in eigenen Aufträgen — sonst ist der erste Bau nicht mehr prüfbar.
- **KEINE Änderung an `docs/rollenkette/`.** Die sieben Blätter zu `trimmen` sind eine
  **Ablesung nach dem Bau**, kein Teil dieses Auftrags.
- **KEIN Schema.** Weder `WallNode` noch `SceneDocument` werden erweitert.
- **KEINE Änderung an `auswahlModus.ts` oder `waehleAn`.** Beide können, was A7 verlangt —
  gemessen. Wer sie doch anfassen muss, meldet es **vor** der Änderung.

## Kanten — die Fälle, an denen es erfahrungsgemäß bricht

| # | Fall | Verlangtes Verhalten |
|---|---|---|
| K1 | **Auswahl hat nur EIN Objekt** | Es gibt keine Schnittkante → **abweisen mit Grund**, nicht stillschweigend nichts tun |
| K2 | **Die Geraden sind parallel** | `geradenSchnitt` liefert `null` → **abweisen mit Grund**, kein Absturz, keine erfundene Ecke |
| K3 | **Schnittpunkt liegt AUSSERHALB der Wandstrecke** | Der Schnitt zweier *Geraden* ist nicht der Schnitt zweier *Strecken*. **ABWEISEN mit Grund — es wird NICHT verlängert.** Entschieden 14.08., ohne Zahlenoperanden; Begründung im K3-Nachtrag unten. |
| K4 | **Das zu kürzende Objekt ist auch eine Schnittkante** | `primaerId` ist in `ids` enthalten (`auswahlModus.ts` Fall `add` erlaubt das) → **definiert behandeln** |
| K5 | **Objekt ist gesperrt** | Der Vertrag kennt `sperren`/`entsperren` → **gesperrte Objekte werden nicht getrimmt** |
| K6 | **Mehrere Schnittkanten treffen** | Welche gewinnt? **Benannt festlegen** (Vorschlag: die nächstgelegene zum Klickpunkt), nicht die erste in der Liste |

### ⚠ K3-NACHTRAG 14.08. — der Fall braucht ZWEI Größen, nicht eine

**Anlass:** Der Plan-Prüfer hat F-004 durchgerechnet (`907aaba2`) — **die Formel stimmt**, fünf
Fälle je doppelt gerechnet, meine Berichtigung vom 13.08. bestätigt sich. **Aber er hat einen
Fall gefunden, den mein K3 nicht abdeckt, und ich habe ihn selbst nachgerechnet:**

```text
EPS_SINUS = 1e-6 wirkt auf den SINUS, nicht auf den Winkel:
  asin(1e-6) = 5,73e-05 Grad   <- erst darunter blockiert die Wache

Zwei 6000-mm-Waende, 5 mm Versatz — wo liegt der Schnittpunkt?
   0,01     Grad ->     28,6 m   Wache: laesst durch
   0,001    Grad ->    286,5 m   Wache: laesst durch
   0,0001   Grad ->   2864,8 m   Wache: laesst durch
   0,000057 Grad ->   4999,6 m   Wache: laesst durch  (exakt an der Schwelle)
```

**Der Kern: K2s Wache ist eine WINKELschwelle, der Schaden ist eine ABSTANDSgröße.** K2 fängt nur
den *fast exakt* parallelen Fall. **Wer bei K3 „verlängern" wählt, verlängert lautlos eine
6-Meter-Wand auf 2,9 Kilometer** — mathematisch korrekt, baulich Unsinn, und ohne jede Meldung.

**K3 verlangt deshalb ZWEI benannte Größen:**
1. **verlängern oder abweisen** (wie bisher)
2. **bis zu welchem Abstand verlängert wird** — die Größe, die bisher fehlte

**⚠ K3 IST ENTSCHIEDEN — und zwar so, dass es KEINEN Operanden braucht: ABWEISEN.**

> **Liegt der Schnittpunkt außerhalb beider Strecken, wird der Trimmvorgang abgewiesen — mit
> Grund, wie bei K1 und K2. Es wird nicht verlängert.**

**Das ist eine Berichtigung meines eigenen Vorschlags von vor einer Stunde, und der Plan-Prüfer
hat ihn in drei Punkten zerlegt** (`d1792697`) — **alle drei treffen:**

1. **Mein „Bounding-Box **plus Zuschlag**" hatte selbst keinen Wert.** Der Zuschlag kam im ganzen
   Blatt **einmal** vor — ohne Zahl, ohne Bezugsgröße, ohne Entscheidung.
2. **Mein Satz „kommt ohne erfundene Zahl aus" war damit falsch.** Der Zuschlag *ist* genau eine
   solche, nur eine Ebene tiefer versteckt. **Ich habe den Operanden verschoben, nicht vermieden.**
3. **Und mein eigenes Kriterium widersprach meinem eigenen Nachtrag:** es verlangte, die gewählte
   Grenze stehe im Bau-Bericht — **damit hätte sie faktisch der Generator gewählt**, was zwei
   Zeilen darüber ausdrücklich verboten war.

**Warum ABWEISEN der richtige Standard ist und nicht nur der bequeme:** Es ist **messbar ohne
jede Zahl**, es ist **die sichere Richtung** (ein abgewiesener Trimmvorgang kostet einen Klick,
eine 2,9-km-Wand kostet eine Fehlersuche), und es ist **später nachrüstbar** — wer Verlängern
will, ergänzt es dann mit einer entschiedenen Grenze, ohne dass etwas zurückgebaut werden muss.
**Es ist außerdem dieselbe Antwort, die K1 und K2 schon geben** — der Fall reiht sich ein, statt
eine eigene Logik zu erfinden.

### K3-PRÄZISIERUNG 14.08. — welcher der zwei Wege gilt, und warum das keine neue Zahl kostet

**Anlass:** Der Plan-Prüfer meldet (`0672be59`), dass *„liegt auf der Strecke"* **zwei Lesarten**
hat, die **am Rand gegenteilig antworten** — und er hat recht. Selbst nachgemessen an
`geradenGeometrie.ts`:

```text
:105   const t = n / m;          <- wird GERECHNET
:107   return { x: …, y: … };    <- und NICHT herausgegeben; Signatur ist Punkt | null

Weg A  ueber den Parameter:  0 <= t <= 1   exakt, KEINE Toleranz noetig
Weg B  ueber die Koordinate: Punkt-in-Strecke — braucht in Gleitkomma ein Epsilon

Gerechnet am selben Fall (Wand 0…6000 mm, Schnitt bei t = 1 + 1e-9):
  Weg A  weist ab      Weg B  laesst durch      <- gegenteilig
```

**Und es ist schärfer, als der Befund sagt:** `t` gilt nur für die **erste** Gerade. Für den
Streckentest beim Trimmen braucht es **beide** Parameter — `t` auf dem zu kürzenden Objekt und
`u` auf der Schnittkante. **`u` wird heute gar nicht gerechnet.**

> **ENTSCHEIDUNG: Weg A — über die Parameter `t` und `u`, beide im Band `[0, 1]`.**
> **Kein Epsilon, keine Toleranz, keine neue Zahl.**

**Warum Weg A und nicht B:** Weg B bräuchte ein Abstands-Epsilon — **F-001 führt zwar 0,5 mm, und
genau deshalb wäre es verführerisch**: auf 6000 mm entspricht das einem t-Band von 8,3e-05.
**Aber das ist dieselbe Klasse wie der Zuschlag, nur eine Ebene tiefer — eine Größe, die niemand
benannt hat und die sonst der Generator wählt.** Weg A kommt ohne sie aus, weil `t` und `u`
**dimensionslos** sind: die Frage „liegt der Punkt auf der Strecke" wird zu „liegt die Zahl
zwischen 0 und 1", und die ist exakt beantwortbar.

**Wie `t` und `u` verfügbar werden — additiv, ohne Bestand anzufassen:**
`geradenSchnitt` ist betriebsbestätigt (A-32) und **wird nicht in seiner Signatur geändert**.
Stattdessen kommt **eine zweite exportierte Funktion in dieselbe Datei**, die `t` und `u` liefert;
`geradenSchnitt` ruft sie auf und gibt weiterhin nur den Punkt zurück. **Eine Rechnung, zwei
Sichten — keine zweite Wahrheit, kein Nachrechnen derselben Formel an zweiter Stelle.**

**Das berührt das Nicht-Ziel „KEINE neue Geometriefunktion" und ist trotzdem gedeckt:** Das
Nicht-Ziel richtet sich gegen das **Nachbauen** vorhandener Mathematik (*„wer sie nachbaut,
erzeugt die zweite Wahrheit"*). Hier wird nichts nachgebaut — **eine bereits berechnete Größe wird
sichtbar gemacht.** Der Weg, sie stattdessen im Werkzeug erneut zu rechnen, wäre genau der
Verstoß, den das Nicht-Ziel verhindern soll.

**Was damit an Yama geht — als Erweiterung, nicht als Blockade:** *Soll das Werkzeug später auch
verlängern können, und bis zu welchem Abstand?* **A-35 wartet darauf nicht.** Solange die Frage
offen ist, weist das Werkzeug ab, und das ist ein vollständiges, prüfbares Verhalten.

**Nicht beanstandet und mitgeprüft:** Die A-32-Normierung ist durch Rechnung gedeckt — bei
0,001 Grad steht der normierte Wert über drei Größenordnungen still, während der rohe Betrag um
den Faktor 10⁸ springt.

## Abnahmekriterien

- **A-35-1** · `app/tools/toolRegistry.ts` enthält einen Eintrag `trimmen`.
  **Messbar:** `grep -c "'trimmen'" app/tools/toolRegistry.ts` liefert **≥ 1**, vorher **0**
  (Zahl vorher/nachher im Bericht nennen).
- **A-35-2** · `geradenGeometrie.ts` hat **mindestens einen Produktivaufrufer**.
  **Messbar:** `grep -rln "geradenGeometrie" --include='*.ts' --include='*.tsx'` ohne
  `__tests__` liefert außer `werkzeugLandkarte.ts` **mindestens eine weitere Datei**. Vorher
  war es **nur** die Landkarte — die Zahl vorher/nachher gehört in den Bericht.
- **A-35-3** · Die Rollenzuweisung folgt A7: **die Hauptrolle ist `primaerId`**, nicht
  `ids[0]`. **Messbar:** ein Test, der bei Auswahlreihenfolge A→B→C das Objekt **C** kürzt
  und A, B als Schnittkanten benutzt.
- **A-35-4** · **Ein** Trimmvorgang ist **ein** Undo-Schritt.
  **Messbar:** ein Test, der nach einem Trimmvorgang **einmal** rückgängig macht und den
  Ausgangszustand wiederherstellt.
- **A-35-5** · Die Übersetzung `selectionIds` ↔ `selectedNodeIds` steht an **genau einer**
  Stelle. **Messbar:** die Fundstellen werden gezählt und einzeln geöffnet; **jede weitere
  Stelle ist ein Mangel**, kein Detail (A7 Konsequenz 2).
- **A-35-6** · **Alle sechs Kanten K1–K6 sind behandelt und je durch einen Test belegt.**
  K3 und K6 verlangen eine **benannte Entscheidung im Bau-Bericht** — eine stille Annahme
  ist ein Mangel, auch wenn sie sich vernünftig verhält.
- **A-35-7** · **Kein Nicht-Ziel berührt.** Messbar: `git show --stat` nennt **keine** Datei
  unter `docs/rollenkette/`, **keine** Schema-Datei (`domain/scene.types.ts`), und
  `auswahlModus.ts` sowie `HausplanerApp.tsx:815 waehleAn` sind **unverändert** — oder die
  Änderung war **vorher gemeldet**.
- **A-35-8** · **Suite grün und Zahl unverändert** (Stand `1df82ee1`: 1750), `tsc exit=0`.
  Neue Tests erhöhen die Zahl — **die Differenz ist zu nennen und muss der Zahl der neuen
  Tests entsprechen.**

- **A-35-9** · **K3 weist ab und verlängert nicht.**
  **Messbar, und der Fall ist genau der, den K2 durchlässt:** ein Test mit zwei
  6000-mm-Wänden bei **0,001°** Winkeldifferenz — der Schnittpunkt liegt **286,5 m**
  entfernt, `geradenSchnitt` liefert ihn, K2s Wache greift **nicht**. Das Werkzeug muss
  **abweisen mit Grund**, nicht verlängern. **Kein Zahlenoperand nötig:** geprüft wird
  **`0 ≤ t ≤ 1` UND `0 ≤ u ≤ 1`** (K3-Präzisierung) — dimensionslos, ohne Epsilon.
  **Zusätzlich messbar:** ein Fall bei `t = 1 + 1e-9` muss **abgewiesen** werden; über die
  Koordinate gerechnet würde er durchlaufen.

## Rückweg und Entdeckung

- **Rückweg:** Der Bau fügt **hinzu** und ändert nichts Bestehendes — ein Registry-Eintrag und
  eine Werkzeugdatei. **Rücknahme = Commit zurückdrehen**, keine Datenmigration, kein Schema.
  Bestandsdokumente sind nicht betroffen, weil kein Feld hinzukommt.
- **Entdeckung:** Wäre die Rollenzuweisung falsch herum, **kürzt das Werkzeug die Schnittkante
  statt des Ziels** — sichtbar beim ersten Gebrauch und durch A-35-3 vorher gefangen.
- **Grenze zur Insel:** React/TypeScript bleibt auf die Hausplaner-Insel begrenzt. Kein PHP,
  keine Migration, keine Route.

## Was dieser Auftrag NICHT beantwortet

**Ob `trimmen` eine eigene Registerzeile bekommt.** Gemessen: `trimmen`, `verlaengern` und
`versatz` haben **keinen** Eintrag im `REGISTER.md`; thematisch gehören sie unter **W-03 Wand
bearbeiten** (das F-003 und F-004 führt). **Das ist eine Registerfrage und Planner-Sache** —
sie wird **nach** diesem Bau entschieden, weil W-03/1 als Ablesung bereits BEREIT liegt und ein
zweiter Auftrag am selben Blattordner kollidieren würde. **Genau deshalb trägt dieser Auftrag
eine A- und keine W-Kennung.**
