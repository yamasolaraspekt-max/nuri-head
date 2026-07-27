# Maus, Tastatur, Touch — die Regeln der Eingabe

*Planner, 27.07.2026. Eines der fünf offenen Papiere aus der Einordnung des Usability- und des
Tester-Masterprompts (§40 Interaktionsmuster, §41 Eingabegeräte). Ich habe dieses zuerst
geschrieben, weil hier der größte Erkenntnisgewinn je Aufwand lag — und weil sich beim Messen
herausstellte, dass meine eigene Vorannahme falsch war.*

## 0. Was ich erwartet hatte, und was dastand

Ich hatte im Kopf notiert: *„zwei Kuerzel-Kollisionen, G und S“*. Das war eine Erinnerung, keine
Messung. Gemessen ist es anders — **die Kollisionen G und S sind bereits behandelt**:
`app/tools/paketAdapter.ts` zählt sie, lässt sie weg und weist sie über `verworfeneKuerzel()`
nach. Wer immer das gebaut hat, hat es sauber gemacht.

Das eigentliche Loch liegt woanders, und es ist größer.

## 1. Die Messung

Alle Zahlen unten stammen aus dem Arbeitsbaum am 27.07., Zweig `auto/hausplaner-integration`,
Basis `e5b061e1`. Jede Zahl ist ein Messwert zum Zeitpunkt des Schreibens — **keine
Abnahmebedingung** (Lehre aus der 20-gegen-34-Abweichung in AUF-38 Scheibe 2).

| Größe | Wert | Befehl |
|---|---|---|
| Maus-Ereignisse in `.tsx` (`onMouseDown/Move/Up`, `onClick`, `onDbl*`) | **91** | `grep -rn ... --include=*.tsx | wc -l` |
| `onTouch*` / `onPointer*` in React-Bauteilen | **0** | dito |
| `PointerEvent` überhaupt | **1 Stelle** | `renderers/three-d/szene.ts:648` |
| Registry-Kuerzel | **9** | `V W F T D K R Delete Ctrl+D` |
| Paket-Werkzeuge gesamt | **101** | `werkzeugPaket.ts` |
| davon mit erklärtem Kuerzel | **16** | |
| davon vom Adapter verworfen | **7** (4 verschiedene: `Ctrl/Cmd+K`, `G`, `R`, `S`) | `verworfeneKuerzel()` |
| Paket-Werkzeuge, die am Ende **kein** Kuerzel tragen | **92 von 101** | |
| `<select>`-Felder in der Oberfläche | **15** | |
| `<textarea>` | **0** | |
| `role="button"` auf `div` | **9** | |
| `aria-label` | **23** | |

Der Tastatur-Verteiler steht an **einer** Stelle: `app/HausplanerApp.tsx:1003–1061`.

## 2. Befund A — das Modifikator-Leck (P1)

Der Verteiler prüft `Strg`/`Cmd` **nur** für vier Tasten: `Z`, `Y`, `S`, `K`. Alles andere fällt
in den Schlusszweig:

```
const tool = toolFuerShortcut(e.key);      // e.key ist bei Strg+F schlicht 'f'
if (tool && tool.art === 'werkzeug') { ... setWerkzeug(tool.id) }
```

`e.key` trägt den Modifikator nicht. **Jede Strg-/Cmd-Kombination, deren Buchstabe zufällig ein
Werkzeug trifft, schaltet das Werkzeug um** — zusätzlich zur Browser-Wirkung, denn im Schlusszweig
steht kein `preventDefault`.

| Griff des Nutzers | Was er will | Was zusätzlich passiert |
|---|---|---|
| `Strg+V` | Einfügen | Werkzeug wechselt auf **Auswahl** |
| `Strg+W` | Reiter schließen | Werkzeug wechselt auf **Wand** |
| `Strg+F` | Im Browser suchen | Werkzeug wechselt auf **Fenster** |
| `Strg+T` | Neuer Reiter | Werkzeug wechselt auf **Tür** |
| `Strg+D` | Lesezeichen | Werkzeug wechselt auf **Dach** |
| `Strg+R` | Neu laden | Werkzeug wechselt auf **Treppe** (dann lädt die Seite) |

Das ist der schlimmste Fehlertyp, den wir kennen: **still, plausibel, falsch.** Der Zeichner
drückt `Strg+V`, sieht nichts, zeichnet weiter — und wundert sich zwei Minuten später, warum
seine Wand ein Auswahlrahmen ist. Genau dasselbe Muster wie die stille 20-m²-Raumliste.

**Behebung:** ein Halbsatz — im Schlusszweig `if (e.ctrlKey || e.metaKey || e.altKey) return;`.
Sechs Worte gegen sechs Fehlbedienungen.

## 3. Befund B — zwei Kuerzel, die es nur auf dem Papier gibt

`Delete` (Werkzeug `loeschen`) und `Ctrl+D` (Werkzeug `duplizieren`) stehen in der Registry mit
`art: 'aktion'`. Der Verteiler lässt aber nur `art === 'werkzeug'` durch. Beide können die
Registry-Strecke **nie** erreichen:

- `Delete` funktioniert trotzdem — aber über einen **eigenen, früheren Zweig**. Der
  Registry-Eintrag ist Zierde.
- `Ctrl+D` funktioniert **gar nicht**. `toolFuerShortcut('ctrl+d')` würde greifen, aber `e.key`
  ist bei `Strg+D` schlicht `'d'` — und `'d'` gehört dem Dach. Duplizieren ist per Tastatur
  unerreichbar; es gibt es nur als Schaltfläche (`HausplanerApp.tsx:1307`).

Das ist eine **zweite Wahrheit**: die Registry behauptet ein Kuerzel, das Verhalten kennt es nicht.
Der Tooltip zeigt es dem Nutzer sogar an. Wer `Strg+D` drückt, bekommt ein Lesezeichen und ein
Dachwerkzeug — und nirgends eine Kopie.

## 4. Befund C — der Eingabefeld-Schutz kennt nur `INPUT`

```
if ((e.target as HTMLElement)?.tagName === 'INPUT') return;
```

`SELECT` steht nicht in dieser Liste, und es gibt **15** davon — darunter das Dachform-Feld
(`HausplanerApp.tsx:1884`). Ein `<select>` hat eine eingebaute Tipp-Suche: Buchstabe drücken
springt zum passenden Eintrag. Wer im Dachform-Feld `D` tippt, um zu „Doppelpult“ zu springen,
schaltet im Hintergrund auf das Dachwerkzeug um.

`TEXTAREA` und `contenteditable` sind heute nicht betroffen (0 Stellen), gehören aber in dieselbe
Regel — sonst ist der nächste Mehrzeiler wieder ein Fehler.

## 5. Befund D — die Touch-Null (P1, bekannt, hier beziffert)

**91 Maus-Ereignisse, 0 Touch-Ereignisse.** Die einzige Stelle im ganzen Planer, die
zeiger-unabhängig hört, ist die 3D-Szene (`pointerdown`). Der gesamte 2D-Grundriss — zeichnen,
fangen, ziehen, auswählen — hört ausschließlich auf `onMouse*`.

Auf einem Tablet gilt: Browser liefern für einfache Berührungen **Ersatz-Mausereignisse**, also
funktioniert Antippen. **Nicht** funktionieren: Ziehen ohne Verzögerung, zwei Finger (Zoom,
Verschieben), und alles, was von `onMouseMove` **vor** dem Drücken lebt — und davon lebt genau
die Fangvorschau (`onMouseMove={(e) => setCursor(weltPunkt(e))}`, Zeile 1517). Auf dem Tablet gibt
es keinen Zeiger, der schwebt. **Die Fangvorschau ist auf Touch nicht schwach, sie ist abwesend.**

Das ist kein Fehler, den man wegkonfiguriert; das ist ein Bauentscheid. Er gehört zu Yama
(Abschnitt 8).

## 6. Befund E — 92 von 101 Werkzeugen ohne Kuerzel: kein Versaeumnis

Bei 101 Werkzeugen und 26 Buchstaben ist ein Kuerzel je Werkzeug arithmetisch unmöglich. Der
Adapter tut das Richtige, wenn er kollidierende Kuerzel weglässt statt sie doppelt zu vergeben.
**Die Antwort auf 101 Werkzeuge ist nicht mehr Kuerzel, sondern die Befehls-Palette**
(`Strg/Cmd+K`, bereits gebaut). Kuerzel sind für die Hände, die den Weg kennen; die Palette ist
für die 92, die man sucht. Zwei Wege für zwei Nutzungsarten — das ist KISS, nicht Verzicht.

Daraus folgt eine Obergrenze: **Kuerzel bekommt nur, was ein Zeichner mehrmals pro Stunde
braucht.** Alles andere wäre Gedächtnislast ohne Gegenwert.

## 7. Die Regeln

**R1 — Ein Verteiler.** Tastatur-Zuordnung entsteht an genau einer Stelle. Heute erfüllt
(`HausplanerApp.tsx:1003`); bei der Zerlegung in AUF-48 muss sie eine Stelle **bleiben**, sonst
entsteht die zweite Wahrheit beim Umzug.

**R2 — Kein Kuerzel ohne Modifikator-Prüfung.** Der Schlusszweig verwirft jeden Druck mit
`Strg`, `Cmd` oder `Alt`. Ein Werkzeug-Kuerzel ist ein **nackter** Tastendruck.

**R3 — Kuerzel mit Modifikator werden buchstabiert, nicht geraten.** Wer `Ctrl+D` in der Registry
schreibt, muss den Verteiler aus `e.key` **und** `e.ctrlKey/metaKey` bauen. Solange das nicht
gebaut ist, darf `Ctrl+D` **nicht in der Registry stehen** — ein angezeigtes Kuerzel, das nichts
tut, ist eine Lüge an den Nutzer.

**R4 — Der Eingabefeld-Schutz nennt Rollen, keine Tagnamen.** Ausgenommen sind: `INPUT`,
`TEXTAREA`, `SELECT` und alles mit `isContentEditable`. Formuliert als eine Funktion, nicht als
Vergleich an der Verwendungsstelle.

**R5 — Was in der Registry steht, löst aus.** Kein Kuerzel darf über einen Sonderzweig laufen,
während die Registry es ebenfalls führt. Entweder der Zweig verschwindet (`Delete` kommt aus der
Registry) oder der Registry-Eintrag verschwindet. **Zwei Orte, ein Kuerzel — verboten.**

**R6 — Ein Test hält die Kuerzel-Tafel fest.** Nicht „die Zahl ist 9“, sondern: *jedes Kuerzel
der Registry löst sein Werkzeug aus, und kein anderes*, sowie *keine Strg-Kombination löst ein
Werkzeug aus*. Der zweite Satz ist der Gegen-Beweis zu Befund A und muss heute **rot** sein.

**R7 — Zeigerneutral schreiben, wo neu gebaut wird.** Neue Flächen hören auf `onPointer*` statt
`onMouse*`. Das ist kein Umbau des Bestands (Abschnitt 8), sondern die Regel für das Nächste —
sonst wächst die Touch-Null weiter, während wir sie besprechen.

## 8. Was hier **nicht** geregelt wird

- **Touch-Umbau des Grundrisses.** 91 Stellen, davon die Fangvorschau strukturell betroffen. Das
  ist ein eigener Posten mit eigener Entscheidung, kein Nebenprodukt. **Gehört zu Yama:** *soll
  der Planer auf dem Tablet bedienbar sein?* Wenn ja, ist es keine Umstellung von Ereignisnamen,
  sondern ein zweites Bedienmodell (Berühren → Ziel setzen → bestätigen), weil der schwebende
  Zeiger fehlt.
- **Barrierefreiheit** (9 `div role="button"`, 23 `aria-label`) — eigenes Papier.
- **Welche der 92 Werkzeuge ein Kuerzel verdienen** — folgt aus der Nutzung, nicht aus der Liste.

## 9. Probe des Erprobers

Jede Regel mit dem Griff, an dem er sie merkt — ohne Code zu lesen:

| Regel | Er tut | Er sieht heute | Er soll sehen |
|---|---|---|---|
| R2 | zeichnet eine Wand, drückt `Strg+V` | Werkzeug springt auf Auswahl | nichts ändert sich |
| R2 | drückt `Strg+W` | Wandwerkzeug an, Reiter schließt | nur der Reiter schließt |
| R3 | liest den Tooltip „Duplizieren `Strg+D`“, drückt ihn | Lesezeichen + Dachwerkzeug | eine Kopie — oder gar kein Kuerzel im Tooltip |
| R4 | öffnet die Dachform-Auswahl, tippt `D` | springt zum Eintrag **und** schaltet auf Dach | springt nur zum Eintrag |
| R5 | drückt `Entf` mit Auswahl | löscht (über den Sonderzweig) | löscht — aus **einer** Quelle |

Das ist die ganze Abnahme. Sie braucht keinen Debugger und keine Zahl.

## 10. Vorschlag für die Reihenfolge

**Ein Posten, Spur A**, klein und in sich geschlossen: R2 + R3 + R4 + R5 + R6 sitzen alle im selben
Verteiler von 59 Zeilen. Getrennt zu bauen wäre teurer als zusammen, weil jede Teilbehebung
denselben Block anfasst.

**Nicht** jetzt: R1 (fällt bei AUF-48 an), R7 (gilt ab sofort als Regel, kostet nichts), Touch
(wartet auf Yama).

**Reihenfolge im Vorrat:** nach den laufenden AUF-38-Scheiben. Der Verteiler liegt in
`HausplanerApp.tsx`, und **Scheibe 7 fässt dieselbe Datei an** — ein Bauender je Posten (§13).
