/**
 * Z1-W2-5 — **die Wandfläche erreicht den Benutzer.**
 *
 * ---
 *
 * **Der Befund steht im Modul selbst** (`geometry/wandFlaeche.ts:4-7`): *„Die Öffnungen liegen im
 * Modell — aber niemand zieht sie von einer Wandfläche ab, weil es keine Wandfläche gibt. Auf
 * dieser einen fehlenden Rechnung setzen Putz, Dämmung, Anstrich, Fassade und Heizlast alle auf."*
 *
 * **Die Rechnung ist inzwischen da — 253 Zeilen, mit eigener Testdatei. Der Weg zum Benutzer
 * fehlte.** Aufrufer im Produktivpfad: **0**. Diese Datei ist der Weg und rechnet nichts selbst.
 *
 * ---
 *
 * ## Zwei Zusagen des Moduls, die hier nicht verwässert werden
 *
 * **1 · Kein Ergebnis ohne Bezugsmaß.** `WandMengen.bezug` ist Pflichtfeld, und das Modul
 * begründet es: *„Eine Fläche ohne Bezugsmaß ist keine Fläche, sondern eine Zahl, die zu allem
 * passt und für nichts taugt."* **Deshalb steht `roh`/`fertig` hier im Klartext und ist
 * umschaltbar** — ein fest verdrahteter Vorgabewert nähme die Wahl zurück, die das Pflichtfeld
 * gerade erzwingen soll. *Kriterium (b) weist ihn ausdrücklich ab, auch mit Kommentar.*
 *
 * **2 · Ein Zweifelsfall liefert eine Meldung, keine Zahl.** `WandFlaecheErgebnis` ist eine
 * **Vereinigung**: entweder Mengen oder Meldungen, nie beides. Ragt eine Öffnung über die Wand
 * oder überlappen zwei, gibt es **kein** Ergebnis — und dann steht hier auch keine Zahl, keine
 * `0` und kein leeres Feld. *Das Modul warnt selbst davor:* **„plausibel falsch ist schlimmer als
 * offensichtlich fehlend"**, und *„ein Ergebnistyp, der Zahlen und Zweifel gleichzeitig zulässt,
 * wird an der ersten Aufrufstelle halb ausgewertet: die Zahlen nimmt man, die Meldungen übersieht
 * man."* **Diese Stelle wertet ihn ganz aus.**
 *
 * ## Kein neues Aussehen
 *
 * `hp-ep-abschnitt`, `hp-ep-kennzahl` und `hp-ep-befund` führt das Panel bereits; alle drei sind
 * vor dem Bau in der CSS gemessen worden. *Bei Z1-W2-1 hatte ich eine Klasse benutzt, die es nicht
 * gab, und es erst im Bildbeleg gesehen.*
 */
import React from 'react';
import type { OpeningNode, WallNode } from '../../domain/scene.types';
import { wandMengen, type Bezugsmass } from '../../geometry/wandFlaeche';
import { T, FARBEN } from '../studioDaten';

export interface WandflaechenAnzeigeEigenschaften {
  /** Die gewählte Wand. `null` = nichts zu rechnen. */
  wand: WallNode | null;
  /** Alle Öffnungen der Szene — das Modul sortiert die fremden selbst aus und MELDET sie. */
  oeffnungen: readonly OpeningNode[];
}

/** m² und m³ mit fester Stellenzahl — die Rundung selbst liegt im Modul, nicht hier. */
const zahl = (n: number, stellen = 2): string => n.toFixed(stellen).replace('.', ',');

export function WandflaechenAnzeige({ wand, oeffnungen }: WandflaechenAnzeigeEigenschaften): React.ReactElement | null {
  // Die Wahl gehört dem Benutzer und lebt deshalb hier, nicht in einer Konstante.
  const [bezug, setBezug] = React.useState<Bezugsmass>('roh');

  if (!wand) return null;

  const ergebnis = wandMengen(wand, oeffnungen, bezug);

  return (
    <div className="hp-ep-abschnitt" data-pruefung="wandflaeche" data-bezug={bezug}>
      <div className="hp-ep-abschnitt-titel">Mengen</div>

      {/* Kriterium b: sichtbar UND waehlbar. Die Beschriftung nennt das Bezugsmass im Klartext,
          nicht als Kuerzel — wer 'roh' liest, weiss worauf sich die Zahlen beziehen. */}
      <label style={{ display: 'block', fontSize: 12, color: FARBEN.gedaempft, marginBottom: 8 }}>
        Bezugsmaß
        <select
          value={bezug}
          onChange={(e) => setBezug(e.target.value as Bezugsmass)}
          data-feld="bezugsmass"
          style={{
            width: '100%', marginTop: 4, padding: '6px 8px', borderRadius: 8,
            border: `1px solid ${T.controlBorder}`, background: T.surface, color: T.ink, fontSize: 13,
          }}
        >
          <option value="roh">roh (Rohbaumaß)</option>
          <option value="fertig">fertig (mit Schichten)</option>
        </select>
      </label>

      {ergebnis.art === 'meldung' ? (
        <>
          {/* Kriterium c: HIER STEHT KEINE ZAHL. Nicht 0, nicht leer, nicht die letzte gueltige —
              das Modul hat kein Ergebnis geliefert, und das ist die Aussage. */}
          {ergebnis.meldungen.map((m, i) => (
            <div key={`${m.art}-${i}`} className="hp-ep-befund" data-meldung={m.art}>
              <span aria-hidden className="hp-ep-schwere-symbol">!</span>
              <span><strong className="hp-ep-schwere-text">Keine Menge</strong> – {m.text}</span>
            </div>
          ))}
          <div className="hp-ep-kennzahl" style={{ color: FARBEN.gedaempft }}>
            Solange das offen ist, wird keine Fläche ausgewiesen.
          </div>
        </>
      ) : (
        <>
          <div className="hp-ep-kennzahl" data-menge="brutto">
            Brutto: <strong style={{ color: FARBEN.text }}>{zahl(ergebnis.mengen.bruttoM2)} m²</strong>
          </div>
          <div className="hp-ep-kennzahl" data-menge="oeffnungen">
            Öffnungen: <strong style={{ color: FARBEN.text }}>−{zahl(ergebnis.mengen.oeffnungenM2)} m²</strong>
          </div>
          <div className="hp-ep-kennzahl" data-menge="netto">
            Netto: <strong style={{ color: FARBEN.text }}>{zahl(ergebnis.mengen.nettoM2)} m²</strong>
            {' · '}{zahl(ergebnis.mengen.nettoM3, 3)} m³
          </div>
          {/* `rohmassRest` ist die Ehrlichkeit des Moduls: was bei 'fertig' NICHT umgerechnet
              werden konnte, steht da — statt als fertig ausgegeben zu werden. Wer das verschweigt,
              macht aus einem fehlenden Wert eine geschaetzte Zahl. */}
          {ergebnis.mengen.rohmassRest.length > 0 && (
            <div className="hp-ep-kennzahl" data-rohmassrest="ja" style={{ color: FARBEN.gedaempft, marginTop: 4 }}>
              Trotz „fertig" Rohmaß: {ergebnis.mengen.rohmassRest.join(' · ')}
            </div>
          )}
        </>
      )}
    </div>
  );
}
