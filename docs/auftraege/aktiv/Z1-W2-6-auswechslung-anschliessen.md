# Z1-W2-6 — Die Auswechslung weiß, welche Sparren eine Dachöffnung schneidet. Der Planer zeigt es nicht.

**ZIEL:** `geometry/auswechslung.ts` erreicht den Benutzer — wer einen Dachaufbau setzt, **sieht,
wie viele Sparren betroffen sind, ob Wechselhölzer nötig sind und ob der Fall prüfpflichtig ist.**

```yaml
auftrag: "Z1-W2-6"
spur: W
welle: "Anschlusswelle 1 (Paket 1 — Massenermittlung), Klasse A"
heimat_app: ticket
heimat_code: resources/planner/hausplaner
werkzeug: "W-29 (tragend; alle SIEBEN Blattteile nennen das Modul) — W-21/W-25 nennen es mit"
modul: "geometry/auswechslung.ts — 195 Zeilen"
registry_kennung: "KEINE. Das Modul bekommt keinen Leisteneintrag (siehe N4)."
art: "ANSCHLUSS — vorhandene, geprüfte Fachlogik bekommt einen Produktivpfad.
      KEINE Aenderung der Fachlogik, KEINE statische Bemessung, KEINE toolRegistry-Aenderung."
mess_sha: 281a60f9
nachgemessen_sha: "49141f90 — die Achsenregel (oeffnungVTiefeM, dachOeffnung.ts:52, ObstacleType
                   10 Arten) wurde beim Zuschnitt von Paket 2 nachgemessen, NACH der ersten Fassung.
                   Der Produktivcode war dazwischen unveraendert (0 Code-Commits zwischen beiden
                   Staenden, gemessen) — es ist eine spaetere MESSUNG, keine spaetere LAGE."
kennung_geprueft: "Z1-W2-6 gemessen: docs/ 1 Treffer — das Zuschnittblatt, das die Kennung vergibt;
                   git log --all --grep 0. Frei und ausdruecklich zugewiesen."
dor_beleg: "ERTEILT — plan-pruefer 2026-08-22T16:42:40, Beleg c9466cc0; BERICHTIGUNG (Achsenregel, 49141f90) BESTAETIGT 17:20:45, Beleg b7437e8a"
basis_sha: 281a60f9
prioritaet: P0
ballbesitz: "generator (DoR erteilt — baubar)"
regelgrundlage: "ANSCHLUSS-entscheidung-2026-08-22.md (Paket 1) ·
                 paket-1-zuschnitt-2026-08-22.md (Klasse A, mit Operanden-Praezisierung)."
zielreifegrad: BROWSERABGENOMMEN
fach_linse: "EMPFOHLEN vor der Freigabe — Zimmerer und/oder Dachdecker, siehe Kriterium (b)."
```

## Ausgangslage, gemessen am Stand `281a60f9`

```
geometry/auswechslung.ts                   195 Zeilen
exportiert   FlaecheMasse · Oeffnung · AuswechslungAnalyse
             sparrenPositionenU(breiteM, rafterDistM, rafterWidthM=0.08)
             analysiereAuswechslung(flaeche, oeffnung, rafterDistM, opts?)
Testdatei    __tests__/auswechslung.test.ts           VORHANDEN
Erreichbar   NEIN — 0 Laufzeit-Importe im Produktivpfad
Registerzeile W-29 (alle 7 Blattteile) · W-21 · W-25
```

**Das Modul zieht seine Grenze selbst** (`auswechslung.ts:15-19`): *„Wechselhölzer werden NUR als
echte Bauteile geführt, wenn die angrenzenden tragenden Sparren eindeutig bestimmbar sind und die
Öffnung nicht in einer konstruktiven Sonderzone (First/Traufe/Ortgang) liegt. Sonst
`pruefpflichtig = true` … und KEINE erfundenen Mengen. **Keine statische Bemessung.**"*

> **Diese Selbstbeschränkung ist die wertvollste Eigenschaft des Moduls, und der Anschluss muss sie
> sichtbar machen statt sie wegzurunden.** *Eine Anzeige, die „2 Wechselhölzer" sagt, wo das Modul
> „prüfpflichtig" meint, ist schlimmer als keine Anzeige.*

## Die Operanden — einer kommt nicht aus dem Modell, und das steht hier oben

```
FlaecheMasse { breiteM, hoeheM }      ableitbar aus RoofNode (Polygon, Neigung, Traufhoehe)
Oeffnung { xRel, yRel, breiteM, hoeheM }  ableitbar aus RoofAufbau (x,y sind bereits 0..1)
rafterDistM                           NICHT im SceneDocument — 0 Treffer in domain/
```

**Der Sparrenabstand ist ein erfragter Wert, kein Modellwert — und der Bestand erfragt ihn bereits:**

```
enginePanels.ts:184   schluessel 'sparrenabstandM', label 'Sparrenabstand',
                      einheit 'm', pflicht: true, vorgabe: 0.8
dachformVorlagen.ts   Vorlagenwerte je Dachform: 8x 70 cm, 1x 62,5 cm
```

*Der Bau darf ihn erfragen oder aus der gewählten Dachform-Vorlage übernehmen — **er darf ihn nicht
erfinden und nicht verschweigen.*** Kriterium (c).

## ⚠ Der Achsen-Fallstrick — und der Bestand hat ihn bereits gelöst

**Zwei Welten benennen dieselben Wörter verschieden.** Gemessen:

| Modell `RoofAufbau` (`scene.types.ts:265-275`) | Modul `Oeffnung` (`auswechslung.ts:52-61`) |
|---|---|
| `breiteMm` — „Breite **parallel Traufe**" | `breiteM` — „u-Richtung (**parallel Traufe**)" ✔ dieselbe Achse |
| `hoeheMm` — „**vertikale** Aufbau-/Fronthöhe" | ↘ **art-abhängig, siehe unten** |
| `tiefeMm` — „Ausdehnung **entlang der Dachschräge**" | ↗ `hoeheM` — „v-Richtung (**geneigt Traufe→First**)" |

> ## ⚠ BERICHTIGUNG 22.08. — die Zuordnung ist **art-abhängig**, und es gibt sie schon
>
> ~~Die erste Fassung dieses Blattes sagte: `tiefeMm → hoeheM`, `hoeheMm` habe keine Entsprechung.~~
> **Das ist für Gauben und Kamine richtig und für Dachfenster falsch.** Gemessen beim Zuschnitt von
> Paket 2:
>
> ```
> geometry/dachOeffnung.ts:51-54
>   /** Schraegen-Tiefe (v-Ausdehnung) der Oeffnung je Art: Dachfenster -> hoeheM, sonst -> tiefeM. */
>   export function oeffnungVTiefeM(o: { art, hoeheM, tiefeM }): number {
>     return o.art === 'window' ? endlich(o.hoeheM) : endlich(o.tiefeM);
>   }
>
> Nutzer AUSSERHALB der eigenen Datei: 0   (intern ruft sie dachOeffnung.ts:65 selbst)
> geprueft in __tests__/dachformVorlagen.test.ts:1299-1302 — window 1.18 · giebelgaube 2.5 · chimney 0.6
> ObstacleType (scene.types.ts:248-258): 10 Arten, 'window' ist genau EINE davon
> ```
>
> **Die Regel existiert, ist getestet, und niemand ruft sie auf.** *Der Anschluss darf sie nicht neu
> erfinden — das wäre eine zweite Wahrheit über die Achsenzuordnung, an genau der Stelle, an der
> ein Fehler eine plausible falsche Sparrenzahl erzeugt.* **Wiederverwendung vor Neuentwicklung
> (CLAUDE.md).**
>
> **Was bestehen bleibt:** `hoeheMm` blind auf `hoeheM` zu mappen ist für **neun von zehn** Arten
> falsch. Die Warnung war richtig, nur zu absolut.
>
> **Der Grund für die zwei Welten ist gemessen:** der Modulkopf von `auswechslung.ts:6` beschreibt
> Aufbauten als `ObstacleData` — **diesen Typ gibt es im Produktivbaum nicht**; fünf
> `geometry/`-Module vermerken das selbst. *Der Kommentar beschreibt eine ältere Welt; gemappt wird
> gegen `RoofAufbau`.*

**Dieses Blatt legt die Zuordnung nicht fest — es macht sie zur belegpflichtigen Zusage** (b).

## N4 — Bedienweg

| | |
|---|---|
| **Auslöser** | ein Dachaufbau wird gesetzt, verschoben oder in der Größe geändert |
| **Ort der Anzeige** | am Aufbau bzw. im Eigenschaften-/Statusbereich — **Komponente im Bau zu benennen (Pfad) und im Browser zu belegen** |
| **tragendes Werkzeug** | **W-29** |
| **kein** | Leisteneintrag, kein Menüpunkt, keine Registry-Kennung |
| **Zielreifegrad** | `BROWSERABGENOMMEN` |

---

## Abnahmekriterien

- **Z1-W2-6-a** · **DIE ANALYSE ERSCHEINT AM DACHAUFBAU.**

  **Verlangt:** Der Bau benennt **eine** Komponente mit Pfad, in der **betroffene Sparren**,
  **Wechsel erforderlich** und **prüfpflichtig** erscheinen, und belegt sie im Browser.

  **Messbefehl:** Browserabnahme (siehe `-g`); Komponentenpfad und Bildbeleg im Bericht.

  **Heutiges (rotes) Ergebnis:**
  ```
  grep -rlE "from '[^']*auswechslung'" --include='*.ts' --include='*.tsx'
    | grep -v '__tests__'                                   ->  0
  ```

  **Absage-Regel:** Ein Konsolen-Log erfüllt (a) nicht.

- **Z1-W2-6-b** · **DIE v-AUSDEHNUNG KOMMT AUS `oeffnungVTiefeM` — NICHT AUS EINER NEUEN REGEL.**

  **Verlangt:** Die Umrechnung `RoofAufbau → Oeffnung` benutzt **`oeffnungVTiefeM`**
  (`geometry/dachOeffnung.ts:52`) für das v-Maß. Der Bericht nennt für **jedes** der vier
  `Oeffnung`-Felder das Modellfeld, aus dem es stammt.

  **Messbefehl:**
  ```
  grep -n 'oeffnungVTiefeM' <die neue Umrechnungsstelle>          -> mindestens 1
  Zuordnungstabelle im Bericht: xRel · yRel · breiteM · hoeheM  <- je Modellfeld + Achse
  Probefall mit ZWEI Arten, weil die Regel art-abhaengig ist:
    typ 'window'      (hoeheMm != tiefeMm)  -> betroffeneSparren = X
    typ 'giebelgaube' (dieselben Masse)     -> betroffeneSparren = Y
    X != Y, beide beziffert — DAS ist der Beleg, dass die Art wirkt
  Gegenprobe: dieselbe Lage mit fest verdrahtetem tiefeMm -> beim Dachfenster ANDERES Ergebnis
  ```

  **Heutiges (rotes) Ergebnis:** `oeffnungVTiefeM` hat **0 Nutzer außerhalb der eigenen Datei**
  (intern ruft `dachOeffnung.ts:65` sie selbst); `auswechslung` wird nicht aufgerufen — **und
  `dachOeffnung` hat ebenfalls keinen Ladeweg** (sein einziger Importeur `dachAusschnitt.ts` ist
  selbst unerreichbar). *Die Regel ist gebaut, geprüft (`dachformVorlagen.test.ts:1299-1302`) — und
  erreicht niemanden.*

  **Absage-Regel:** Eine **eigene** `if (art === 'window')`-Zeile im Anschlusscode erfüllt (b)
  **nicht** — das wäre eine zweite Wahrheit über die Achsenzuordnung. **Und ein Probefall mit nur
  einer Art erfüllt (b) nicht**: er kann nicht zeigen, dass die Unterscheidung überhaupt wirkt.
  *Kommt der Bau zu einer anderen Zuordnung als hier, gilt sein Befund — dann ist dieses Blatt zu
  ändern, nicht der Code zu biegen. Eine Fach-Linse (Zimmerer/Dachdecker) bleibt empfohlen; die
  Achsenfrage ist Handwerkswissen, nicht Typprüfung.*

- **Z1-W2-6-c** · **DER SPARRENABSTAND IST SICHTBAR UND STAMMT AUS EINER BENANNTEN QUELLE.**

  **Verlangt:** Die Anzeige nennt den verwendeten Sparrenabstand **mit Einheit**, und der Bericht
  sagt, **woher er kommt**: Nutzereingabe, Dachform-Vorlage oder Vorgabewert. **Ein stiller
  Vorgabewert ist unzulässig.**

  **Messbefehl:**
  ```
  im Browser sichtbar: "Sparrenabstand <wert> m" (oder cm), Bildbeleg
  im Bericht: die Quelle, mit Datei:Zeile — z. B. enginePanels.ts:184 (vorgabe 0.8)
              oder dachformVorlagen.ts (70 / 62,5 cm)
  zwei Laeufe mit verschiedenem Abstand -> betroffeneSparren aendert sich
  ```

  **Heutiges (rotes) Ergebnis:** `rafterDist` hat **0 Treffer in `domain/`**; es gibt keine Anzeige.

  **Absage-Regel:** Ein hartkodiertes `0.8` **ohne sichtbare Angabe** erfüllt (c) nicht. *Eine
  Sparrenzahl, die auf einem unsichtbaren Abstand beruht, ist eine Behauptung mit Nachkommastelle.*

- **Z1-W2-6-d** · **`pruefpflichtig` ERSCHEINT ALS VORBEHALT, NICHT ALS ZAHL.**

  **Verlangt:** Im prüfpflichtigen Fall (Sonderzone First/Traufe/Ortgang oder nicht eindeutig
  bestimmbare Sparren) zeigt die Oberfläche **den Vorbehalt** — und **keine** Wechselholz-Menge.
  Die `hinweise` des Moduls erscheinen im Klartext.

  **Messbefehl:**
  ```
  Browserlauf mit einem Aufbau in der Randzone (yRel nahe 0 oder 1)
    -> 'pruefpflichtig' sichtbar, wechselAnzahl NICHT als Menge dargestellt
    -> die hinweise stehen lesbar da
  Gegenlauf mit einem Aufbau in der Flaechenmitte -> Menge erscheint
  ```

  **Heutiges (rotes) Ergebnis:** nicht durchführbar — keine Anzeige.

  **Absage-Regel:** `wechselAnzahl: 0` als „0 Stück" anzuzeigen erfüllt (d) **nicht.** *Das Modul
  unterscheidet „keine nötig" von „nicht bestimmbar" — eine Oberfläche, die beides als 0 zeigt,
  macht diese Unterscheidung zunichte.* **Und `keine statische Bemessung` bleibt sichtbar:** die
  Anzeige darf nicht wie ein Nachweis aussehen.

- **Z1-W2-6-e** · **ROT-PROBE: OHNE DAS MODUL ERSCHEINT NICHTS.**

  **Messbefehl:** derselbe Bedienweg am Stand **vor** dem Bau, Bildbeleg.

  **Heutiges (rotes) Ergebnis:** ist die Rot-Lage selbst.

- **Z1-W2-6-f** · **KEIN PRODUKTCODE AUSSERHALB DER HAUSPLANER-INSEL.**

  **Messbefehl:** `git diff --name-only <basis>..<bau> -- ':!resources/planner/hausplaner'` → leer.

  **Heutiges (grünes) Ergebnis:** Schutzbeleg am Bau-Diff.

- **Z1-W2-6-g** · **BROWSERABNAHME, MIT ORT — UND DIE FACHLOGIK BLEIBT UNVERÄNDERT.**

  **Messbefehl:**
  ```
  ORT: die im Repo vorhandene Puppeteer-Buehne, Chrome HEADFUL (headless kann kein WebGL)
  je Lauf: Dachmasse · Aufbau-Lage und -Masse · Sparrenabstand · abgelesene Werte
           · Bildbeleg · Stand-SHA
  git diff --stat <basis>..<bau> -- geometry/auswechslung.ts   -> leer
  __tests__/auswechslung.test.ts                               -> 0 fail
  ```

  **Heutiges (rotes/grünes) Ergebnis:** keine Abnahme vorhanden; der Diff-Teil ist Schutzbeleg.

---

## Nicht-Ziele

- **Keine statische Bemessung.** Das Modul sagt es selbst; dieses Blatt ändert daran nichts.
- **Kein Leisteneintrag, keine `toolRegistry`-Kennung** (N4).
- **Keine Änderung an `RoofAufbau` oder `RoofNode`.** Der Eingang wird gelesen, nicht erweitert.
- **Kein Sparrenabstand im `SceneDocument`.** *Ihn ins Modell zu heben ist eine eigene Entscheidung
  mit eigenen Folgen (Migration, Vorbelegung, Bestandsdokumente) — hier wird er erfragt.*
- **Keine Mengenliste über mehrere Öffnungen.** Dieses Blatt zeigt **einen** Aufbau.

## Werkzeug-Vorlage aus A-35 — die zwölf Stellen

```
[ ]  1  Fachlogik            ENTFAELLT — auswechslung.ts ist gebaut (195 Z.)
[ ]  2  eigene Suite         ENTFAELLT — __tests__/auswechslung.test.ts vorhanden
[ ]  3  Registry-Eintrag     ENTFAELLT — kein Leistenwerkzeug (N4)
[x]  4  Verdrahtung          NOETIG — Aufruf im Aufbau-Bearbeitungsweg; Kern von (a)
[ ]  5  Darstellung          ENTFAELLT — kein Leisteneintrag
[x]  6  Fachliche Grundlage  NOETIG, aber NUR als Umrechnung: RoofNode/RoofAufbau -> FlaecheMasse/
                             Oeffnung, mm -> m. KEINE neue Geometrie, KEINE Rechnung (Kriterium b)
[ ]  7..11  nachziehende Tests  ENTFAELLT — die Werkzeugmenge aendert sich nicht
[?] 12  public/hausplaner/…js  OFFEN — Buendel-Frage (gen 19 Posten 7); der Bau begruendet beides
```

> **Stelle 6 ist hier der Unterschied zu `Z1-W2-5`:** dort ist der Eingang unmittelbar Modell, hier
> braucht es eine **Umrechnung** — und genau in ihr sitzt der Achsen-Fallstrick.

## Nachvollzugs-Matrix (ARBEITSREGELN §5 / N3)

| Kriterium | Arbeitspaket | Commit-SHA | Testbeleg |
|---|---|---|---|
| Z1-W2-6-a Analyse sichtbar | AP-1 Anzeigekomponente | n.U. | n.U. |
| Z1-W2-6-b v-Maß aus `oeffnungVTiefeM`, zwei Arten | AP-2 Umrechnung + Probefall | n.U. | n.U. |
| Z1-W2-6-c Sparrenabstand sichtbar, Quelle benannt | AP-2 (Operand) | n.U. | n.U. |
| Z1-W2-6-d `pruefpflichtig` als Vorbehalt | AP-1 (Vorbehalt) | n.U. | n.U. |
| Z1-W2-6-e Rot-Probe | AP-3 Vorher/Nachher | n.U. | n.U. |
| Z1-W2-6-f Inselgrenze | AP-3 Diff-Beleg | n.U. | n.U. |
| Z1-W2-6-g Browserabnahme + Fachlogik unberührt | AP-3 (Bühne, Diff, Suite) | n.U. | n.U. |

**Arbeitspakete:** AP-1 Anzeige und Vorbehalt · AP-2 Umrechnung und Operand · AP-3 Belege und
Abnahme.

## Rückweg

**Revert dieses einen Commits.** Die Fachlogik bleibt unverändert (g), der Anschluss ist additiv,
das Datenmodell wird nicht erweitert (Nicht-Ziele) — es entsteht kein Zustand.
