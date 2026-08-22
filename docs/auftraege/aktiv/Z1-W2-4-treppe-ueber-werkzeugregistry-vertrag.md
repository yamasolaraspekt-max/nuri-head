# Z1-W2-4 — Die Treppe über den `werkzeugRegistry`-Vertrag registrieren: eine Probe mit zwei gültigen Ausgängen

**ZIEL:** Ausgelöst feststellen, ob `geometry/werkzeugRegistry.ts` einen Weg trägt, den der Bestand
noch nicht hat — **an dem Bauteil, das der Vertrag selbst zweimal als Beispiel nennt.**

```yaml
auftrag: "Z1-W2-4"
spur: W
welle: "Anschlusswelle 1 — Posten 7 (Treppe-Probe)"
heimat_app: ticket
heimat_code: resources/planner/hausplaner
werkzeug: "W — die Treppe (toolRegistry id 'treppe', bauteilKind 'stair', shortcut R)"
modul: "geometry/werkzeugRegistry.ts — 68 Zeilen"
registry_kennung: "KEINE neue. Die Treppe steht bereits in TOOL_DEFINITIONS (:150)."
art: "PROBE — beide Ausgaenge sind Ergebnis. KEIN neues Werkzeug, KEINE Fachlogik,
      KEINE toolRegistry-Aenderung, KEINE Stilllegung in diesem Blatt."
mess_sha: 7791920f
kennung_geprueft: "Z1-W2-4 gemessen: docs/ 0 Treffer, git log --all --grep 0.
                   Z1-W2-0..3 sind von mir vergeben, -4 ist die naechste freie. Frei."
dor_beleg: "steht aus — plan-pruefer"
basis_sha: 7791920f
prioritaet: P0
ballbesitz: "plan-pruefer (DoR)"
regelgrundlage: "Planner gen 19 Posten 7 woertlich: 'Kleinblatt Spur W Treppe ueber
                 geometry/werkzeugRegistry-Vertrag registrieren — beide Ausgaenge sind Ergebnis
                 (Weg belegt / Stilllegung begruendet); Stilllegung werkzeugRegistry erst danach.'"
zielreifegrad: "— (Probe; es entsteht keine Bedienung)"
```

## Warum ausgerechnet die Treppe — der Vertrag nennt sie selbst

Nicht ich habe das Bauteil gewählt; **der Entwurf hat es**. Zweimal, in seinen eigenen Kommentaren:

```
werkzeugRegistry.ts:13   "true, wenn alle harten Fachpruefungen bestanden sind
                          (z. B. DIN 18065 bei der Treppe)"
werkzeugRegistry.ts:27   "Kuerzel fuer Werkzeugleiste/Tastatur (z. B. 'R' fuer Treppe)"
```

Und der Bestand hat beides eingelöst — **auf einem anderen Weg**: `toolRegistry.ts:158`
`shortcut: 'R'`, `:159` `helpText: 'Treppe setzen … (DIN 18065)'`.

> **Damit ist die Probe fair.** Wenn ein Entwurf an *diesem* Bauteil keinen Vorteil zeigt, zeigt er
> ihn nirgends. *Und wenn er einen zeigt, dann hier am deutlichsten.*

## Ausgangslage, gemessen am Stand `7791920f`

```
geometry/werkzeugRegistry.ts                68 Zeilen
exportiert   WerkzeugKategorie · Parametrik · WerkzeugNode<T>
             registriereWerkzeug · werkzeug · alleWerkzeuge · _leereRegistry
Verbraucher im Produktivpfad                0
Verbraucher gesamt                          1   — __tests__/werkzeugRegistry.test.ts (45 Z., 4 Faelle)
bauteile/<kind>/  (Kopfkommentar :6)        EXISTIERT NICHT

die Treppe im Bestand
  Fachmodule   treppenBerechnung · treppenTypen · treppenBauarten · treppeObjekt
               treppe2D · treppe3D · treppeSvg                        7 Module
  Testdateien                                                        12
  Leisteneintrag  toolRegistry.ts:150  id 'treppe' · shortcut 'R' · bauteilKind 'stair'
  Produktivaufrufe von berechneTreppe   EigenschaftenPanel · enginePanels
                                        treppe2D · treppe3D · treppenTypen
```

**Die Treppe ist nicht unerreichbar — sie ist der am besten angeschlossene Teil des Planers.**
*Dies ist kein Anschlussblatt. Es ist eine Vertragsprobe.*

## Der Kern: es gibt bereits einen zweiten Vertrag, und er ist in Benutzung

**`app/dashboard/enginePanels.ts` tut genau das, was `werkzeugRegistry` verspricht** — eine
datengetriebene Zuordnung Bauteil → Rechenkern, mit der Treppe als erstem Eintrag (`:164`).
**Der Vergleich der beiden Ergebnistypen ist die eigentliche Frage dieses Blattes:**

| | `Parametrik` (unbenutzt) | `EngineErgebnis` (benutzt) |
|---|---|---|
| Ort | `werkzeugRegistry.ts:12` | `enginePanels.ts:89` |
| Gesamturteil | `bestanden: boolean` | `bestanden: boolean` |
| Kennwerte | `kennwerte?: Record<string, number \| string \| boolean>` | `[feld: string]: unknown` |
| **Prüfliste** | **nicht darstellbar** | `pruefungen?: TreppenErgebnis['pruefungen']` |
| Urteil unterdrücken | — | `keinGesamturteil?: boolean` (`EnginePanel:81`) |

**`TreppenErgebnis` (`treppenBerechnung.ts:35-47`) hat zehn Felder.** Acht sind Zahlen und passen in
`kennwerte`. `bestanden` passt. **`pruefungen: TreppenPruefung[]` passt nicht** — ein Array von
Objekten ist in `Record<string, number|string|boolean>` nicht abbildbar.

> **Und das ist kein formaler Mangel, sondern eine gelernte Lehre.** Der Bestand hat den Fall schon
> gehabt: `__tests__/treppenBerechnung.test.ts:70` — *„kein Eingabefeld dafür. Das Badge sagte
> trotzdem ‚DIN 18065 erfüllt'."* Daraus entstand `keinGesamturteil` mit einer ausführlichen
> Begründung (`enginePanels.ts:71-81`): *„eine Plakette ‚Alle Prüfungen bestanden' behauptet einen
> NACHWEIS, und den gibt es hier nicht."*
> **`Parametrik` kennt diese Lehre nicht.** Wer die Treppe dorthin hängt, bekommt genau das Badge
> zurück, das der Bestand bereits einmal richtiggestellt hat.

**Ein dritter Messpunkt, der beim Registrieren sofort auftritt:** der Vertrag verlangt `kind: string`.
Für die Treppe stehen **drei** Namen zur Wahl, und sie sind nicht gleichwertig:

```
'stair'    domain/scene.types.ts, validation.ts, applyCommand.ts, …   15 Produktivdateien
'treppe'   toolRegistry.ts:150 — die WERKZEUG-Kennung, nicht der Bauteiltyp
'treppe'   __tests__/werkzeugRegistry.test.ts:20 — die Attrappe im Test des Vertrags
```

**Der Test des Vertrags registriert einen Namen, den das Datenmodell nicht kennt.** *Wer die echte
Treppe registriert, muss `'stair'` nehmen — und trifft damit auf den ersten Widerspruch zwischen
Entwurf und Bestand, noch bevor eine Zeile Fachlogik im Spiel ist.*

## N4 — Bedienweg

| | |
|---|---|
| **Bedienweg** | **keiner.** Dies ist eine Probe am Vertrag; es entsteht keine Bedienung. |
| **Auslöser** | der Lauf der Probe durch den Generator — einmalig, nicht wiederkehrend |
| **Ort** | Wegwerf-Verzeichnis unter `TMPDIR` (A-37-22d) — **nicht** im Checkout |
| **tragendes Werkzeug** | die Treppe (`toolRegistry` `'treppe'`), **unverändert** |
| **Zielreifegrad** | entfällt — das Ergebnis ist ein **Bericht**, kein Produktstand |

---

## Abnahmekriterien

- **Z1-W2-4-a** · **DIE ECHTE TREPPE WIRD REGISTRIERT, NICHT EINE ATTRAPPE.**

  **Verlangt:** Der Versuch bindet **`berechneTreppe`** aus `geometry/treppenBerechnung` als
  `parametrik` ein — die Funktion, die der Bestand tatsächlich benutzt. `kind` ist **`'stair'`**,
  der Name aus dem Datenmodell.

  **Messbefehl:**
  ```
  im Probeaufbau: der Import zeigt auf geometry/treppenBerechnung
  registriereWerkzeug({ kind: 'stair', … parametrik: (d) => berechneTreppe(d) … })
  werkzeug('stair')?.parametrik(<eine echte TreppenEingabe>)  ->  ein Parametrik-Objekt
  ```

  **Heutiges (rotes) Ergebnis:** `registriereWerkzeug` hat **0** Produktivaufrufer; der einzige
  Aufrufer ist der eigene Test mit `bau('treppe')` — **einer Attrappe**, deren `parametrik`
  `{ bestanden: daten.ok }` zurückgibt.

  **Absage-Regel:** Eine neu geschriebene Treppen-`parametrik` erfüllt (a) **nicht**. *Dann prüft
  die Probe eine Funktion, die es nur für die Probe gibt — und beweist über den Bestand nichts.*

- **Z1-W2-4-b** · **DER INFORMATIONSVERLUST WIRD BEZIFFERT, FELD FÜR FELD.**

  **Verlangt:** Der Bericht stellt **alle zehn Felder** von `TreppenErgebnis` dem `Parametrik`-Typ
  gegenüber und sagt je Feld: **trägt / trägt nicht / trägt nur verkürzt.**

  **Messbefehl:**
  ```
  Felder TreppenErgebnis  =  10   (treppenBerechnung.ts:35-47)
  je Feld: in Parametrik abbildbar?  ja / nein / verkuerzt — mit Begruendung am Typ
  Summe der NICHT abbildbaren Felder  ->  Zahl im Bericht
  ```

  **Heutiges (rotes) Ergebnis:** nicht erhoben. **Statisch am Typ ablesbar ist bereits:**
  `pruefungen: TreppenPruefung[]` ist in `Record<string, number|string|boolean>` **nicht**
  abbildbar; acht Zahlenfelder und `bestanden` sind es.

  **Absage-Regel:** *„Passt im Wesentlichen"* erfüllt (b) nicht. **Verlangt ist eine Zahl und eine
  Liste** — ein Vertrag wird nicht im Ganzen bewertet, sondern an den Feldern, die er verliert.

- **Z1-W2-4-c** · **DIE PROBE LÄUFT, ODER DER FEHLER WIRD ZITIERT.**

  **Verlangt:** Entweder läuft der Aufbau (Übersetzung **und** Ausführung) und liefert für eine
  echte Eingabe ein Ergebnis — **oder** der Bericht zitiert die Fehlermeldung **wörtlich** und
  nennt die Zeile. **Beides ist ein gültiger Ausgang.**

  **Messbefehl:**
  ```
  ORT: Wegwerf-Verzeichnis unter TMPDIR (A-37-22d) — der Checkout wird NICHT angefasst
  Laeufer: npm run test:hausplaner:dom   bzw. node --test mit Typen-Strip
           (derselbe Laeufer wie die fuenf bestehenden Proben — KEIN Vitest, es existiert nicht)
  Ausgang 1: Ergebnis + die Werte, die ankamen
  Ausgang 2: Fehlermeldung WOERTLICH + Datei:Zeile
  git status --porcelain  ->  leer
  ```

  **Heutiges (rotes) Ergebnis:** nicht durchgeführt.

  **Absage-Regel:** Eine Zusammenfassung der Fehlermeldung erfüllt (c) **nicht** — *der Wortlaut
  ist der Beleg; eine Nacherzählung ist eine zweite Wahrheit über das, was passiert ist.*

- **Z1-W2-4-d** · **DER VERGLEICH MIT DEM BENUTZTEN VERTRAG STEHT IM BERICHT.**

  **Verlangt:** Der Bericht beantwortet: **welchen Zuwachs bringt `WerkzeugNode` gegenüber
  `EnginePanel`/`EngineErgebnis`, das die Treppe heute schon trägt?** Genannt werden die Felder,
  die **nur** `WerkzeugNode` hat (`faehigkeiten`, `migrate`, `schemaVersion`, `kategorie`) — und ob
  der Bestand sie **braucht** oder anderswo bereits führt.

  **Messbefehl:**
  ```
  grep -n 'engine-treppe' app/tools/faehigkeiten.ts                     -> der DEKLARIERTE Vertrag
  grep -n 'engineId: .engine-treppe' app/dashboard/enginePanels.ts      -> der AUFRUFENDE Vertrag
  je Feld aus WerkzeugNode: gibt es das im Bestand schon? WO?
  ```

  **Heutiges (rotes) Ergebnis:** `faehigkeiten.ts:84` führt die Treppe bereits mit `eingang`,
  `ausgang`, `engineModul`, `engineExport`; `enginePanels.ts:164` ruft sie auf. **Zwei Verträge
  beschreiben dieselbe Zuordnung; einer ist verdrahtet, der andere nicht.**

  **Absage-Regel:** Ein Vergleich, der nur `werkzeugRegistry` betrachtet, erfüllt (d) **nicht**.
  *Die Frage ist nicht „taugt der Vertrag", sondern „taugt er **zusätzlich zu dem, der läuft**".*

- **Z1-W2-4-e** · **KEIN CODE VERLÄSST DIE PROBE.**

  **Verlangt:** Nach der Probe ist der Checkout unverändert. **Kein** neues Modul, **keine**
  Änderung an `werkzeugRegistry.ts`, `toolRegistry.ts`, `enginePanels.ts` oder einem Treppen-Modul.

  **Messbefehl:**
  ```
  git diff --stat <basis>..<ende>   ->  hoechstens dieses Blatt und der Bericht
  git status --porcelain            ->  leer
  ```

  **Heutiges (grünes) Ergebnis:** Schutzbeleg, am Ende zu messen.

  **Absage-Regel:** *„Die Registrierung gleich richtig einbauen, wenn sie schon läuft"* erfüllt (e)
  **nicht.** **Das wäre eine Entscheidung, die diese Probe erst vorbereiten soll** — und sie fiele
  ohne DoR, ohne Blatt und ohne Yamas Kenntnis.

- **Z1-W2-4-f** · **DER BERICHT ENTSCHEIDET NICHT — ER LEGT VOR.**

  **Verlangt:** Der Bericht endet mit **einer Empfehlung und ihrer Begründung**, ausdrücklich als
  **Empfehlung** gekennzeichnet: `WEG BELEGT` oder `STILLLEGUNG BEGRÜNDET`. **Die Stilllegung selbst
  ist nicht Teil dieses Blattes** (Posten 7 wörtlich: *„Stilllegung `werkzeugRegistry` erst danach"*).

  **Messbefehl:** der Bericht enthält genau **einen** der beiden Sätze, mit Verweis auf (b) und (d).

  **Heutiges (rotes) Ergebnis:** keine Vorlage vorhanden.

  **Absage-Regel:** *„Der Vertrag wird nicht benutzt, also weg"* erfüllt (f) **nicht** —
  **Nichtbenutzung ist ein Anlass zu messen, kein Ergebnis.** *Genau daran ist meine erste
  Einschätzung zu `toolCatalogStillgelegt` gescheitert: der Ort sagt nichts über die Wirkung.*

---

## Nicht-Ziele

- **Keine Stilllegung.** Sie ist der **Folgeschritt**, ausdrücklich nach dieser Probe.
- **Keine Änderung an `werkzeugRegistry.ts`** — auch nicht, um `kennwerte` auf `unknown` zu
  erweitern. *Das machte ihn `EngineErgebnis` gleich und beantwortete die Doppelungsfrage, indem es
  sie herstellt.*
- **Keine Änderung an der Treppe.** Sieben Fachmodule und zwölf Testdateien bleiben unberührt.
- **Kein `bauteile/<kind>/`-Verzeichnis anlegen.** Der Kopfkommentar nennt es, es existiert nicht —
  **das ist ein Befund der Probe, keine Bauaufgabe.**
- **Keine `toolRegistry`-Änderung.** Ausdrücklich untersagt (gen 19 `verboten`).

## Werkzeug-Vorlage aus A-35 — die zwölf Stellen, abgehakt mit Begründung

*Pflichtanhang jedes Spur-W-Blatts (`docs/konzept/werkzeug-vorlage-aus-a-35.md`, gen 18 Posten 6b).*
**Eine gekürzte Liste ist kein Beleg — deshalb steht jede Stelle da, auch die entfallenden:**

```
 1  Fachlogik des Werkzeugs      ENTFAELLT — die Treppe ist gebaut (7 Module)
 2  eigene Suite                 ENTFAELLT — 12 Testdateien vorhanden
 3  Registry-Eintrag             ENTFAELLT — toolRegistry.ts:150 vorhanden, Aenderung VERBOTEN
 4  Verdrahtung in der Insel     ENTFAELLT — die Treppe ist verdrahtet; die Probe verdrahtet nichts
 5  Darstellung                  ENTFAELLT — kein neuer Leisteneintrag
 6  Fachliche Grundlage          ENTFAELLT — keine neue Geometrie
 7  toolRegistry.test.ts         ENTFAELLT — die Werkzeugmenge aendert sich nicht
 8  gehobeneWerkzeuge.test.ts    ENTFAELLT — dito
 9  naechsterSchritt.test.ts     ENTFAELLT — dito
10  rechte.test.ts               ENTFAELLT — dito
11  toolPresentation.test.ts     ENTFAELLT — dito
12  public/hausplaner/…js        ENTFAELLT — kein Bau, kein Buendel
```

> **Alle zwölf entfallen — und genau das ist die Aussage.** *Eine Probe, die keine der zwölf Stellen
> berührt, kann den Bestand nicht beschädigen. Das ist der Grund, warum sie vor der Stilllegung
> steht und nicht danach.*

## Nachvollzugs-Matrix (ARBEITSREGELN §5 / N3)

| Kriterium | Arbeitspaket | Commit-SHA | Testbeleg |
|---|---|---|---|
| Z1-W2-4-a echte Treppe registriert | AP-1 Probeaufbau | n.U. | n.U. |
| Z1-W2-4-b Verlust je Feld beziffert | AP-2 Feldvergleich | n.U. | n.U. |
| Z1-W2-4-c Lauf oder Fehler wörtlich | AP-1 (Lauf) | n.U. | n.U. |
| Z1-W2-4-d Vergleich mit `EnginePanel` | AP-2 (Vertragsvergleich) | n.U. | n.U. |
| Z1-W2-4-e Checkout unverändert | AP-3 Schutzbeleg | n.U. | n.U. |
| Z1-W2-4-f Empfehlung, keine Entscheidung | AP-3 (Vorlage) | n.U. | n.U. |

**Arbeitspakete:** AP-1 Probeaufbau und Lauf · AP-2 Feld- und Vertragsvergleich · AP-3 Bericht und
Schutzbeleg.

## Rückweg

**Keiner nötig.** Die Probe läuft in einem Wegwerf-Verzeichnis unter `TMPDIR`; der Checkout wird
nicht angefasst (e). *Es gibt keinen Stand, der zurückgebaut werden müsste — der einzige Ausgang ist
ein Bericht.*
