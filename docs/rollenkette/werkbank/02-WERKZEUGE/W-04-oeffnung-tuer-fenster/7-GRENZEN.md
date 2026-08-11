# W-04 · Öffnung (Tür/Fenster) — GRENZEN

## Der wichtigste Fall: eine ID, die es nicht gibt

**Die vier Nachschlagefunktionen antworten gegensätzlich — und beides ist Absicht.**

| Funktion | Zeile | bei unbekannter ID | belegt durch |
|---|---|---|---|
| `fensterBauartNach()` | 70 | **`undefined`** | `return id ? FENSTER_BAUARTEN.find(…) : undefined` |
| `tuerBauartNach()` | 73 | **`undefined`** | dieselbe Form |
| `tuerTyp()` | 42 | **nie `undefined`** → `TUER_TYPEN[0]` = **`dreh1`** | `?? TUER_TYPEN[0]` |
| `fensterTyp()` | 47 | **nie `undefined`** → `FENSTER_TYPEN[0]` = **`drehkipp`** | `?? FENSTER_TYPEN[0]` |

**Beide Rückfallwerte selbst nachgezählt**, nicht aus dem Kommentar übernommen: `TUER_TYPEN[0]` ist
`dreh1` (Zeile 23), `FENSTER_TYPEN[0]` ist `drehkipp` (Zeile 32). *Der Kommentar behauptet genau das,
und diesmal stimmt er.*

### Warum die Bauart `undefined` liefern DARF und der Typ nicht

Eine **Bauart** ist Aussehen — fehlt sie, gibt es nichts zu zeichnen, und `undefined` ist die ehrliche
Antwort. Ein **Typ** trägt die Maße — ohne ihn gäbe es keine Breite und keine Höhe. *Eine Öffnung
ohne Maß ist keine Öffnung.*

### Und wo die Gefahr dabei liegt

**`tuerTyp('gibtsnicht')` liefert eine Drehtür, ohne zu sagen, dass gefallen wurde.** Der Aufrufer
bekommt 875 × 2010 und keinen Hinweis. *Das ist die A-10-Klasse: nicht das leere Ergebnis ist das
Problem, sondern das gefüllte, das seine Herkunft verschweigt.* **Der TypeScript-Typ verhindert den
Fall im Übersetzer — er verhindert ihn nicht bei Daten aus Datei, Netz oder Altstand.**

## Was das Werkzeug sonst nicht kann

| Fall | Warum | Was der Anwender sieht |
|---|---|---|
| Eigene Maße im Katalog speichern | Kataloge sind `readonly`-Konstanten | die Vorlage bleibt; **Maße sind nach dem Setzen frei überschreibbar** |
| Bauart ohne Vorgabe-Öffnungsart | `oeffnungsArt?` ist optional | er wählt selbst — *„sofern eindeutig; sonst undefined"* |
| Brüstung an einer Tür | `bruestung?` gibt es nur bei Fenstern | nichts |
| Türgeometrie | gehört **W-02** | dort beschrieben, `wallGeometry.ts:291` |
| `fensterProdukt.ts` | **Ausschluss** — bis auf den Typ `OeffnungsArt` | eigenes Blatt |
