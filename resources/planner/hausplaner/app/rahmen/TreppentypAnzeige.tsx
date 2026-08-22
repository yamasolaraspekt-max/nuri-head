/**
 * Z1-V1-1 Modul 1 — **`geometry/treppenTypen.ts` erreicht den Benutzer.**
 *
 * ```text
 * geometry/treppenTypen.ts   153 Zeilen, Testdatei vorhanden
 * Aufrufer im Produktivpfad  0   (gemessen: treppenTyp\( ohne __tests__ und ohne die Moduldatei)
 * ```
 *
 * ---
 *
 * ## ⚠ Der fehlende Operand — offengelegt, nicht erraten
 *
 * `treppenTyp()` verlangt `typ: TreppenTyp` — **`'gerade' | 'l-podest' | 'u-podest' | 'spindel'`**.
 * Im Modell steht dieser Wert **nicht**. `TreppeParams.typ` (`geometry/treppeObjekt.ts:24`) ist
 * etwas anderes: eine **Bauart-ID aus `TREPPEN_BAUARTEN`**, und deren Dateikopf sagt es selbst —
 * *„Getrennt von den Berechnungstypen in treppenTypen.ts."*
 *
 * **Gemessen:** 20 Bauarten gegen 4 Berechnungstypen; eine Abbildung zwischen beiden gibt es im
 * Bestand nicht (`grep TreppenTyp` außerhalb der Moduldatei → 0).
 *
 * **Sie hier zu erfinden wäre eine Fachentscheidung des Treppenbaus**, keine Verdrahtung.
 * `11_raumspartreppe`, `12_faltwerktreppe` und `13_kragarmtreppe` fallen auf keinen der vier
 * Rechentypen, ohne dass jemand das entscheidet — und eine falsch zugeordnete Treppe rechnet
 * **plausibel falsch**. Deshalb wählt hier **der Benutzer**, mit sichtbarer Vorauswahl `gerade`.
 * Die Bauart wird daneben genannt, damit der Unterschied sichtbar bleibt statt stillzuschweigen.
 *
 * ## Warum das keine zweite Wahrheit neben `berechneTreppe` ist
 *
 * Das Panel rechnet bereits mit `berechneTreppe` (`:510`) — Steigungen, Auftritt, Schrittmaß,
 * DIN 18065. **Diese Anzeige wiederholt das nicht.** Sie zeigt, was nur `treppenTyp` liefert:
 * die **typabhängige Grundfläche** (Bounding-Box des Laufs) und die Stufenverteilung *dieses*
 * Typs. Beide Zahlen tragen ihre Herkunft im Text.
 */
import React from 'react';
import { treppenTyp, type TreppenTyp } from '../../geometry/treppenTypen';
import type { TreppeParams } from '../../geometry/treppeObjekt';
import { treppenBauartNach } from '../../geometry/treppenBauarten';

/** Die vier Rechentypen mit Klartext — Reihenfolge wie in `treppenTypen.ts`. */
const TYPEN: ReadonlyArray<{ wert: TreppenTyp; text: string }> = [
  { wert: 'gerade', text: 'gerade' },
  { wert: 'l-podest', text: 'L mit Podest' },
  { wert: 'u-podest', text: 'U mit Podest' },
  { wert: 'spindel', text: 'Spindel' },
];

export interface TreppentypAnzeigeEigenschaften {
  params: TreppeParams | null;
}

export function TreppentypAnzeige({ params }: TreppentypAnzeigeEigenschaften): React.ReactElement | null {
  const [typ, setTyp] = React.useState<TreppenTyp>('gerade');
  if (!params) return null;

  const erg = treppenTyp({
    typ,
    geschosshoehe: params.geschosshoehe,
    laufbreite: params.laufbreite,
    gewuenschteSteigung: params.gewuenschteSteigung,
    bereich: params.bereich,
  });
  const bauart = treppenBauartNach(params.typ);

  return (
    <div className="hp-ep-abschnitt" data-pruefung="treppentyp" data-typ={typ}>
      <div className="hp-ep-abschnitt-titel">Bauform und Grundfläche</div>
      <label className="hp-ep-feldgruppe">
        Rechentyp
        <select
          data-feld="treppentyp"
          value={typ}
          onChange={(e) => setTyp(e.target.value as TreppenTyp)}
        >
          {TYPEN.map((t) => (
            <option key={t.wert} value={t.wert}>{t.text}</option>
          ))}
        </select>
      </label>
      <div className="hp-ep-kennzahl" data-menge="grundflaeche">
        Grundfläche {(erg.grundflaeche.breiteMm / 1000).toFixed(2)} × {(erg.grundflaeche.tiefeMm / 1000).toFixed(2)} m
      </div>
      <div className="hp-ep-kennzahl" data-menge="stufen">
        {erg.anzahlSteigungen} Steigungen · {erg.anzahlAuftritte} Auftritte in dieser Bauform
      </div>
      {/* Der Vorbehalt reist mit dem Ergebnis (Muster A-14/A-18): der Rechentyp ist gewählt,
          nicht abgeleitet. Ohne diesen Satz liest sich die Grundfläche wie eine Eigenschaft
          der gesetzten Treppe. */}
      <div className="hp-ep-lesehinweis">
        Rechentyp ist gewählt, nicht aus der Bauart abgeleitet
        {bauart ? ` — gesetzt ist „${bauart.label}"` : ' — im Modell ist keine Bauart hinterlegt'}.
        Eine Zuordnung Bauart → Rechentyp gibt es nicht; sie wäre eine Fachentscheidung.
      </div>
    </div>
  );
}
