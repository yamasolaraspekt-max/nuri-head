# BEFUND — die Auftragstafel driftet gegen die Zustandsfelder, und ich habe sie mitgebaut

**Gemessen:** 12.08.2026 · **Rolle:** Generator, über die eigene Zuarbeit · **Ball:** Planner (§16).

## Der Messwert

`docs/STATUS.md` führt jeden Auftrag an **zwei** Stellen: als Zeile in der Auftragstafel und als
`zustand:` im YAML-Block. **Fünf von acht meiner Aufträge standen auseinander:**

| Auftrag | Zustandsfeld (Wahrheit) | Tafelzeile (Anzeige) |
|---|---|---|
| W-01/1 | `ABGENOMMEN` | `CODE_FERTIG` |
| W-02/1 | `ABGENOMMEN` | `CODE_FERTIG` |
| W-08/1 | `ABGENOMMEN` | `CODE_FERTIG` |
| W-21/1 | `RELEASE_FREI` | `CODE_FERTIG` |
| W-22/1 | `RELEASE_FREI` | `CODE_FERTIG` |

W-05/1, W-11/1 und A-13 stimmten überein — **weil ihre Zeile zufällig zuletzt von derselben Rolle
angefasst wurde, die auch das Feld setzte.**

## Wie es entstanden ist — mein Anteil zuerst

Ich habe diese Tafelzeilen **selbst angelegt**, weil sie fehlten (Befund `a7375a11`: acht Aufträge
ohne Tafelzeile). Das war richtig — der erste §3-Ort meldete sonst „frei", während ein Auftrag lief.

**Aber ich habe eine Anzeige gebaut, die niemand fortschreibt.** Wenn der Evaluator abnimmt oder der
Release-Prüfer freigibt, setzen sie das **Zustandsfeld** — die Zeile, die ich hinzugefügt habe,
kennen sie nicht als ihre Pflicht. *Eine zweite Stelle, die dieselbe Tatsache trägt, wird nur so
lange gepflegt, wie derselbe die Feder führt.*

**Das ist nicht der Fehler der anderen Rollen.** Ich habe die Stelle geschaffen, ohne zu regeln, wer
sie fortschreibt — dieselbe Klasse wie meine §16-Entscheidung vom 10.08., die den Status
unauffindbar machte, und wie die „Notiz über eine Lücke", die der Planner heute an sich selbst
gefunden hat.

## Was ich getan habe — und was ausdrücklich nicht

**Getan:** die fünf Zeilen mechanisch angeglichen. **Nur der Wert aus dem `zustand:`-Feld wurde
kopiert**, dazu die Ballbesitz-Spalte entsprechend (`ABGENOMMEN` → Release-Prüfer, `RELEASE_FREI` →
Yama). **Keine Bewertung, keine neue Aussage, kein fremder Text angefasst.** Gegenprobe: 0 Abweichungen.

**Nicht getan:** die Frage entschieden, wie es künftig zusammenbleibt. **Das ist §16 und gehört dem
Planner.** Drei Wege, ohne Empfehlung von mir, weil ich Partei bin:

1. **Die Tafel wird erzeugt**, nicht gepflegt — aus den Zustandsfeldern, bei jedem Lauf.
2. **Wer das Ereignis erzeugt, schreibt beide Orte** — dann muss es in den Meldepflichten stehen,
   sonst ist es ein Vorsatz und keine Regel.
3. **Die Tafel trägt keinen Zustand mehr**, nur Titel und Verweis — dann fällt der erste §3-Ort weg
   und §3 braucht eine andere Form.

## Der Preis, wenn es bleibt

**Der erste §3-Ort wird unzuverlässig.** Er ist die Schranke, die verhindert, dass zwei Instanzen
gleichzeitig bauen — und er liest genau die Spalte, die hier gedriftet ist. *Heute in die harmlose
Richtung: die Tafel zeigte einen älteren Zustand. Die gefährliche Richtung wäre die umgekehrte.*
