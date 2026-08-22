/**
 * **Z1-E4-1 — das Panel der Bodenplatte.**
 *
 * ---
 *
 * **Kriterium (g), wörtlich: *„Das Panel behauptet nichts, was nicht geprüft ist."***
 *
 * Die Absage-Regel des Blattes nennt den Grund: *„Eine Dickenangabe ohne Herkunft liest sich wie
 * eine Bemessung. Die Statik ist nicht Gegenstand dieses Blattes, und das Panel darf das Gegenteil
 * nicht nahelegen."*
 *
 * **Daraus folgen drei Regeln, die diese Datei einhält:**
 *
 * 1. **Das Wort „geprüft" erscheint nirgends** bei Dicke oder Bewehrung. Es steht auch nicht als
 *    Verneinung da („nicht geprüft") — eine Zusage, die auf die Zeichenkette misst, kann beides
 *    nicht unterscheiden, und wichtiger: wer „nicht geprüft" liest, denkt an ein Prüfverfahren,
 *    das es hier gar nicht gibt.
 * 2. **Jeder Wert nennt seine Herkunft.** `geometrieHerkunft`/`freigabe` stehen sichtbar am
 *    Umriss; die Dicke trägt den Vermerk, dass sie eine Eingabe ist und keine Bemessung.
 * 3. **Ein fehlender Aufbau wird als fehlend gezeigt**, nicht als 0. *Eine Summe von 0 über eine
 *    leere Liste sieht aus wie eine Messung.* Deshalb `fussbodenaufbauErfasst` neben der Summe —
 *    die beiden Fragen sind getrennt (siehe `geometry/hoehenkette.ts`).
 *    **Seit 22.08. 23:12 ist bei ERDBERÜHRTEN Platten daraus ein Mangel geworden**, kein
 *    hinnehmbarer Zustand: der Aufbau ist Pflicht, und der Command lässt eine solche Platte gar
 *    nicht erst entstehen. Bei nicht erdberührten Platten bleibt „nicht erfasst" zulässig.
 *
 * **Bewehrung kommt hier nicht vor** — kein Feld, kein Platzhalter, keine Zeile „folgt später".
 * *Ein leeres Bewehrungsfeld ist eine Einladung, es auszufüllen, und was jemand hineinschreibt,
 * sähe danach aus wie eine Bemessung.*
 */
import React from 'react';
import type { FoundationSlabNode } from '../../domain/scene.types';
import { fussbodenaufbauDickeMm, fussbodenaufbauErfasst } from '../../geometry/hoehenkette';

export interface BodenplattenPanelEigenschaften {
  platte: FoundationSlabNode;
  /** Änderungen laufen über UPDATE_FOUNDATION_SLAB — das Panel führt keinen eigenen Zustand. */
  aendere: (changes: Record<string, unknown>) => void;
}

/** mm → m mit Komma und Vorzeichen, wie eine Höhenkote im Bauplan („−0,18 m“). */
function kote(mm: number): string {
  return `${(mm / 1000).toFixed(2).replace('.', ',')} m`;
}

export function BodenplattenPanel({ platte, aendere }: BodenplattenPanelEigenschaften): React.ReactElement {
  const aufbauMm = fussbodenaufbauDickeMm(platte.schichten);
  const aufbauDa = fussbodenaufbauErfasst(platte.schichten);

  return (
    <div className="hp-panel-block" data-panel="bodenplatte">
      <h4 className="hp-panel-titel">Bodenplatte</h4>

      <label className="hp-feld">
        <span className="hp-feld-name">Dicke</span>
        <input
          type="number" className="hp-feld-eingabe" data-feld="dickeMm"
          value={platte.dickeMm} min={1} step={10}
          onChange={(e) => {
            const n = Math.round(Number(e.target.value));
            if (Number.isFinite(n) && n > 0) aendere({ dickeMm: n });
          }}
        />
        <span className="hp-feld-einheit">mm</span>
        {/* Herkunft der Zahl — sie kommt aus der Eingabe, nicht aus einer Rechnung. */}
        <span className="hp-feld-herkunft">Eingabe · keine Bemessung</span>
      </label>

      <label className="hp-feld">
        <span className="hp-feld-name">Oberkante</span>
        <input
          type="number" className="hp-feld-eingabe" data-feld="oberkanteMm"
          value={platte.oberkanteMm} step={10}
          onChange={(e) => {
            const n = Math.round(Number(e.target.value));
            if (Number.isFinite(n)) aendere({ oberkanteMm: n });
          }}
        />
        <span className="hp-feld-einheit">mm</span>
        <span className="hp-feld-herkunft" data-feld="kote">
          {kote(platte.oberkanteMm)} · bezogen auf ±0,00 = OK Fertigfußboden EG
        </span>
      </label>

      <label className="hp-feld hp-feld--schalter">
        <input
          type="checkbox" data-feld="erdberuehrt"
          checked={platte.erdberuehrt}
          onChange={(e) => aendere({ erdberuehrt: e.target.checked })}
        />
        <span className="hp-feld-name">erdberührt</span>
        {/* Warum ein Schalter und keine Ableitung: eine aufgestelzte Platte oder eine über einer
            Tiefgarage liegt genauso auf der untersten Etage und ist trotzdem nicht erdberührt. */}
        <span className="hp-feld-herkunft">Vorbelegung aus der Geschosslage · änderbar</span>
      </label>

      <div className="hp-feld" data-feld="aufbau">
        <span className="hp-feld-name">Fußbodenaufbau</span>
        {aufbauDa ? (
          <>
            <span className="hp-feld-wert">{aufbauMm} mm</span>
            <span className="hp-feld-herkunft">
              {platte.schichten?.length} Schicht(en) · Summe · Reihenfolge außen → innen (erdseitig zuerst)
            </span>
          </>
        ) : platte.erdberuehrt ? (
          // **Seit der Entscheidung vom 22.08. 23:12 ist das ein MANGEL, kein Zustand.**
          // Der Command laesst eine erdberuehrte Platte ohne Aufbau gar nicht erst entstehen;
          // erreicht dieser Zweig trotzdem den Bildschirm — Altbestand, Import, fremder
          // Schreibweg —, dann ist die Hoehenlage der Platte unbekannt und das muss dastehen.
          // *Frueher stand hier „nicht erfasst" als hinnehmbarer Zustand. Posten 25.6 ist
          // aufgehoben: „Wer den Aufbau nicht kennt, setzt die Platte erst, wenn er ihn kennt."*
          <span className="hp-feld-wert hp-feld-wert--mangel" data-zustand="pflicht-fehlt">
            Fußbodenaufbau fehlt — Pflichtangabe, die Höhenlage ist unbestimmt
          </span>
        ) : (
          // Nicht erdberuehrt: hier ist ein fehlender Aufbau zulaessig. Eine Platte ueber einer
          // Tiefgarage hat keine Bezugshoehe zum Erdreich, und ±0,00 ist fuer sie kein Massstab.
          <span className="hp-feld-wert hp-feld-wert--fehlt" data-zustand="nicht-erfasst">
            Aufbau nicht erfasst
          </span>
        )}
      </div>

      <div className="hp-feld" data-feld="herkunft">
        <span className="hp-feld-name">Umriss</span>
        <span className="hp-feld-wert">{platte.polygon.length} Punkte</span>
        <span className="hp-feld-herkunft">
          {platte.geometrieHerkunft} · {platte.freigabe}
        </span>
      </div>
    </div>
  );
}
