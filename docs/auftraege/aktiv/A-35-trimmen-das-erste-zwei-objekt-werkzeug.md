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
| K3 | **Schnittpunkt liegt AUSSERHALB der Wandstrecke** | Der Schnitt zweier *Geraden* ist nicht der Schnitt zweier *Strecken*. **ZWEI benannte Größen nötig, nicht eine** — siehe K3-Nachtrag unten. |
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

**⚠ OPERAND — Fachentscheidung, NICHT vom Generator zu setzen.** Nach der Schutzgrenze wird das
nicht still automatisiert. **Mein Vorschlag, ausdrücklich als Vorschlag und mit Begründung:**
der Schnittpunkt muss **innerhalb der Bounding-Box aller Wände des Geschosses zuzüglich eines
Zuschlags** liegen — *selbstskalierend, kommt ohne erfundene Zahl aus und passt sich an Bungalow
wie an Hallenbau an.* **Die Alternative wäre ein fester Faktor** (Verlängerung höchstens n × Wand-
länge), *einfacher, aber willkürlich.* **Der Generator wählt keine der beiden eigenmächtig:** er
setzt die Größe an EINER benannten Stelle, macht sie im Bau-Bericht sichtbar, und ein
Überschreiten wird **gemeldet, nicht stillschweigend geklemmt** (dieselbe Regel wie bei den
geklemmten Öffnungen in `segmentierung.ts`).

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

- **A-35-9** · **K3 trägt ZWEI Größen** (K3-Nachtrag): Verhalten *und* Abstandsgrenze.
  **Messbar:** ein Test mit zwei 6000-mm-Wänden bei **0,001°** Winkeldifferenz — der
  Schnittpunkt liegt dort **286,5 m** entfernt und **passiert K2s Wache**. Das Werkzeug muss
  diesen Fall **erkennen und melden**, nicht stillschweigend verlängern. Die gewählte Grenze
  steht an **einer** benannten Stelle und im Bau-Bericht.

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
