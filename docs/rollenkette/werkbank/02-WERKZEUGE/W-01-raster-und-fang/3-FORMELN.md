# W-01 · Raster und Fang — FORMELN

## Benutzte Formeln

| F-Nr | Wofür in diesem Werkzeug | Grenzfall betrifft uns? |
|---|---|---|
| **F-001** Abstand zweier Punkte | Liegt ein Kandidat in Toleranz? | **JA** — der Vergleich `Abstand ≤ Toleranz` ist die einzige Fangbedingung |
| **F-003** Lotfußpunkt | `lotAufGerade()` für `achse` und `verlaengerung` | **JA, und ABWEICHEND** — siehe unten |
| **F-040** Rasterfang | die Art `raster` | **JA** — kaufmännisch runden, sonst ist das Raster links der Null verschoben |
| **F-041** Fangkandidat wählen | die Rangfolge | **JA, und ABWEICHEND** — siehe unten |

## Zwei Abweichungen zur Formelsammlung — im Code belegt, nicht geraten

### F-003 wird OHNE die Begrenzung auf [0,1] gerechnet

```text
Formelsammlung   t' = max(0, min(1, t))   "auf die Strecke begrenzen"
Code             kein Math.max/Math.min/clamp in lotAufGerade()  (0 Treffer)
```

**Das ist Absicht, kein Fehler.** Die Formelsammlung nennt die fehlende Begrenzung einen Grenzfall
(*„landet auf der Verlängerung der Wand statt auf der Wand"*) — **hier ist die Verlängerung genau
das Ziel**: `achse` und `verlaengerung` sind eigene Fangarten. *Wer an der Flucht einer Nachbarwand
ausrichtet, steht meistens **neben** ihr, nicht auf ihr.*

**Grenzfall im Code:** `laenge2 < 1e-9` → `null`. *Nicht „exakt null", sondern mit Epsilon.*

### F-041s Rangfolge stimmt nicht mit der des Codes überein

```text
F-041   Endpunkt > Schnittpunkt > Mittelpunkt > Lot > Verlaengerung > Raster
Code    endpunkt > mittelpunkt > achse > verlaengerung > ortho > raster > keiner
```

**Drei Unterschiede:** der Code kennt **keinen Schnittpunkt**, er kennt **`ortho`**, das F-041 nicht
listet, und er stellt **`mittelpunkt` vor `achse`**, wo F-041 den Schnittpunkt vor den Mittelpunkt
setzt. **Der Code ist die gebaute Wahrheit; F-041 beschreibt eine andere Auswahl.**

> *Diese beiden Abweichungen sind der Grund, warum dieses Blatt aus dem Code abgeleitet wurde und
> nicht aus der Formelsammlung.*
