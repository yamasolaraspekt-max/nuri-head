# Z2-W0-5b — `linked` gibt die Master-Sets fremder Auftragspunkte heraus

**ZIEL:** `GET /api/planner/items/{plannerItem}/master-sets` fragt vor der Antwort, ob der Punkt den
Aufrufer etwas angeht — wie seine vier Geschwister im selben Controller-Paar.

```yaml
auftrag: "Z2-W0-5b"
spur: A
heimat_app: ticket
art: "RECHTE-WACHE — ein vorhandener Baustein bekommt einen fuenften Aufrufer.
      KEIN neuer Baustein, KEINE neue Rechtelogik, KEINE Schalter-Aenderung."
befund_quelle: "Evaluator-Votum Z2-W0-5 (639a7a32), Nebenbefund — selbst ausgeloest: bei
                GESCHLOSSENEM Rechte-Schalter HTTP 200 mit den Master-Sets eines FREMDEN Items."
einordnung: "Integritaets-/Auth-Luecke, Kategorie 2 der Regel RECHTE_ALLE_FUER_ALLE (Yama 21.08.):
             bleibt Befund, auch wenn der Schalter true ist. Der Endpunkt FOLGT dem Schalter nicht,
             er UMGEHT ihn."
mess_sha: 97843380
kennung_geprueft: "Z2-W0-5b gemessen: kein Blatt vorhanden. docs/ 2 Treffer — die Dirigenten-Vorgabe
                   im Steuerungs-Spiegel und der Meilensteinplan; git log --all 6 Treffer, saemtlich
                   Erwaehnungen in Befunden und im Gesamtkonzept. Frei, vom Dirigenten zugewiesen."
dor_beleg: "steht aus — plan-pruefer"
basis_sha: 97843380
prioritaet: P0
ballbesitz: "plan-pruefer (DoR)"
regelgrundlage: "Vorgabe des Dirigenten 2026-08-22T13:37:19+0200
                 (auftraege/Z2-W0-5b-linked-wache.vorgabe-dirigent.md); Planner gen 19 Posten 3."
```

## Der Befund am Code, gemessen am Stand `97843380`

```php
// app/Http/Controllers/Planner/PlannerMasterSetController.php:162
public function linked(Request $request, int $plannerItemId)
{
    $rows = \App\Models\PlannerItemMasterSet::query()
        ->where('planner_item_id', $plannerItemId)      // <- keine Frage nach Zustaendigkeit
        ...
    return response()->json(['ok' => true, 'items' => $rows]);
}
```

**Keine Zeile zwischen Eingang und Datenbank.** Kein `authorize`, kein `abort`, kein
Zuständigkeits-Baustein.

**Und ein struktureller Unterschied, der die Lücke erklärt:** `linked` nimmt **`int $plannerItemId`**,
seine Geschwister nehmen **`PlannerItem $item`** (Route-Model-Binding). *Wo kein Modell gebunden
wird, fällt auch nicht auf, dass niemand es prüft.*

## Der Baustein — und die Vorgabe ist an einer Stelle zu korrigieren

**Gemessen:** `app/Support/Planner/PlannerZustaendigkeit.php:189` ·
`verlangeZustaendigkeitFuerItem()` · **vier Aufrufer**

| Aufrufer | Stelle |
|---|---|
| `link` | `PlannerMasterSetController:182` |
| `unlink` | `PlannerMasterSetController:195` |
| `materials/index` | `PlannerItemMaterialController:32` |
| `materials/store` | `PlannerItemMaterialController:71` |

> **Die Vorgabe nennt „link/unlink/addToPlan" — `addToPlan` gehört nicht dazu, und das ist kein
> Versehen der Vorgabe, sondern eine echte Unterscheidung.** Gemessen: `addToPlan`
> (`:205`) ruft **`verlangeZustaendigkeitFuerPlan()`**, einen **zweiten** Baustein. Der Code
> begründet es selbst: *„Bindung an den Plan, nicht an den Punkt — der Punkt entsteht erst."*
> **Es gibt also zwei Bausteine, nicht einen.** Für `linked` ist **`…FuerItem`** der richtige, weil
> es an einem bestehenden Punkt hängt. *Wer hier `…FuerPlan` nähme, hätte eine Wache, die die
> falsche Frage stellt.*

**`linked` wird der fünfte Aufrufer von `verlangeZustaendigkeitFuerItem`.**

## Die Grundmenge — und warum sie größer ist, als der Parametername vermuten lässt

```
Route::get(...) mit {plannerItem}                                     1
Route::get(...) unter /items/{...}                                    2      <- die Grundmenge
alle Routen unter /items/{...} (jede Methode)                         6
```

| Route | Controller | Wache |
|---|---|---|
| `GET /items/{plannerItem}/master-sets` `:339` | `linked` | **KEINE** |
| `GET /items/{item}/materials` `:359` | `PlannerItemMaterialController@index` | **`…FuerItem` `:32`** ✓ |

> **Ein Suchlauf nach `{plannerItem}` findet nur eine Route und meldet die Grundmenge als
> erledigt.** Die zweite GET-Route heißt `{item}` — **derselbe Gegenstand, anderer Parametername**.
> Sie ist bereits gesichert (aus Z2-W0-5 / A-4), *aber das weiß nur, wer breiter gesucht hat.*
> **Der Messbefehl dieses Blattes sucht deshalb nach `/items/{`, nicht nach `{plannerItem}`.**

---

## Abnahmekriterien

- **Z2-W0-5b-a** · **`linked` RUFT DEN BAUSTEIN — DENSELBEN WIE SEINE GESCHWISTER.**

  **Verlangt:** `linked` ruft `verlangeZustaendigkeitFuerItem()` **vor** jedem Datenbankzugriff.
  **Kein neuer Baustein, keine eigene Prüflogik.**

  **Messbefehl:**
  ```
  grep -c 'verlangeZustaendigkeitFuerItem' app/Http/Controllers/Planner/PlannerMasterSetController.php
      vorher 2   ->   nachher 3
  im Diff: der Aufruf steht VOR der Query
  ```

  **Heutiges (rotes) Ergebnis:** **2** Aufrufe (`link:182`, `unlink:195`) — `linked:162` fehlt.

  **Absage-Regel:** Eine eigene `if`-Prüfung in `linked` erfüllt (a) **nicht**. *Dann gäbe es zwei
  Wahrheiten darüber, wann ein Punkt jemanden etwas angeht — genau die Doppelung, gegen die der
  Baustein gebaut wurde.*

- **Z2-W0-5b-b** · **FREMD WIRD ABGEWIESEN — BEI BEIDEN SCHALTERSTELLUNGEN.**

  **Verlangt:** Aufruf mit einem **fremden** `plannerItem` → **403 oder 404**, sowohl bei
  `RECHTE_ALLE_FUER_ALLE = true` als auch `= false`. Eigenes Item → **200**.

  **Messbefehl:** vier Läufe (fremd/eigen × Schalter an/aus), je HTTP-Code und Rumpf.

  **Heutiges (rotes) Ergebnis:** **fremd → 200 mit Daten**, ausgelöst belegt durch den Evaluator
  (Votum `639a7a32`).

  **Absage-Regel:** Ein Nachweis nur bei geschlossenem Schalter erfüllt (b) nicht. **Der Endpunkt
  folgt dem Schalter heute nicht — er umgeht ihn**; das muss in **beiden** Stellungen aufhören.

- **Z2-W0-5b-c** · **DER VERTRAGSTEST WÄCHST MIT.**

  **Verlangt:** Der vorhandene Vertragstest zu `Z2-W0-5` bekommt den Fall `linked` — fremd
  abgewiesen, eigen erlaubt.

  **Messbefehl:** Testlauf; im Bericht die Testdatei und der neue Fall namentlich.

  **Heutiges (rotes) Ergebnis:** kein Fall für `linked` vorhanden.

  **Absage-Regel:** Ein neuer, eigener Test neben dem Vertragstest erfüllt (c) nicht — *dann prüfen
  zwei Stellen dieselbe Zusage und altern unterschiedlich.*

- **Z2-W0-5b-d** · **KEINE WEITERE ROUTE DERSELBEN KLASSE OFFEN.**

  **Verlangt:** Nachweis über die **Grundmenge**, nicht über die eine Route.

  **Messbefehl:**
  ```
  grep -nE "Route::get\('[^']*items/\{" routes/api.php          -> alle GET-Routen am Punkt
  je Treffer: ruft der Controller verlangeZustaendigkeitFuerItem?
  STAND-SHA nennen — die Menge waechst mit jeder neuen Route
  ```

  **Heutiges Ergebnis:** **2 GET-Routen**; `materials/index` ist gesichert (`:32`), `linked` nicht.
  **Nach dem Bau: 2 von 2.**

  **Absage-Regel:** Eine Suche nach `{plannerItem}` erfüllt (d) **nicht** — sie findet nur eine
  Route und meldet die Grundmenge fälschlich als vollständig. *Der Parametername ist nicht die
  Sache.*

- **Z2-W0-5b-e** · **KEIN PRODUKTVERHALTEN AUSSERHALB DER WACHE.**

  **Verlangt:** Bei **eigenem** Item ist die Antwort **zeichengleich** wie heute — dieselben Felder,
  dieselbe Sortierung.

  **Messbefehl:** Antwort vorher/nachher für dasselbe eigene Item, Rümpfe vergleichen.

  **Heutiges (grünes) Ergebnis:** Schutzbeleg am Bau-Diff.

  **Absage-Regel:** *Eine Wache, die nebenbei die Nutzlast ändert, ist zwei Änderungen in einem
  Schritt.*

---

## Nicht-Ziele

- **Kein neuer Zuständigkeits-Baustein.** Der vorhandene bekommt einen fünften Aufrufer.
- **Keine Änderung an `verlangeZustaendigkeitFuerPlan`** — `addToPlan` bleibt, wie es ist.
- **Keine Änderung am Schalter `RECHTE_ALLE_FUER_ALLE`.** Das Blatt prüft gegen **beide**
  Stellungen, es verstellt keine.
- **Kein Umbau auf Route-Model-Binding.** *Wünschenswert (siehe Befund oben), aber ein eigener
  Schnitt — hier würde er die Wache mit einer Signaturänderung vermischen.*

## Nachvollzugs-Matrix (ARBEITSREGELN §5 / N3)

| Kriterium | Arbeitspaket | Commit-SHA | Testbeleg |
|---|---|---|---|
| Z2-W0-5b-a Baustein gerufen | AP-1 Wache in `linked` | n.U. | n.U. |
| Z2-W0-5b-b fremd 403/404, beide Schalter | AP-2 vier Läufe | n.U. | n.U. |
| Z2-W0-5b-c Vertragstest erweitert | AP-3 Testfall | n.U. | n.U. |
| Z2-W0-5b-d Grundmenge 2 von 2 | AP-4 Routen-Nachweis | n.U. | n.U. |
| Z2-W0-5b-e Nutzlast unverändert | AP-4 (Vorher/Nachher) | n.U. | n.U. |

## N4 — Bedienweg

**Bedienweg: keiner.** Dies ist eine **API-Wache**; sie hat keine Oberfläche. Der Anschluss an den
Benutzer läuft über `Z2-W0-5`, das denselben Endpunktbereich bedient.
**Zielreifegrad:** entfällt — es gibt nichts zu bedienen; die Zusage ist der HTTP-Code.

## Rückweg

**Revert dieses einen Commits.** Es entsteht kein Zustand: die Wache ist ein Methodenaufruf, die
Nutzlast bleibt unverändert (e), kein Schema, keine Migration.
