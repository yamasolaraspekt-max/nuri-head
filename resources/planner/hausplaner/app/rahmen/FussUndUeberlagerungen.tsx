/**
 * AUF-48 Scheibe 4e — **Statusleiste, Befehlspalette und Engine-Fläche.** Die letzte der fünf.
 *
 * Drei Geschwister am Fuß der Hauptansicht, zwei davon Überlagerungen:
 *
 * ```text
 * Statusleiste     Zeigerposition · Zoom · Räume/Fläche · Werkzeug-Hinweis · letzte Ablehnung
 * Befehlspalette   Overlay, `position: fixed` — ausserhalb des Flusses (Kante 10)
 * Engine-Fläche    die Fläche einer Rechen-Engine (AUF-33/L2)
 * ```
 *
 * **Was hier NICHT passiert: nichts wird entschieden.** Das Markup ist zeichengleich das, was
 * vorher in `HausplanerApp.tsx` stand; keine Inline-Stelle wurde angefasst.
 *
 * **Die Palette hält keinen eigenen Zustand.** `paletteOffen`, `paletteFilter` und `paletteIndex`
 * bleiben in der Hauptfunktion; das Schliessen läuft über einen hereingereichten Rückruf. **Der
 * Escape-Weg ist ausdrücklich NICHT hier** — er hängt am Escape-Stapel (`useEscapeEbene`) in der
 * Hauptfunktion, und ein zweiter Weg an dieser Stelle war schon einmal die Ursache eines echten
 * Fehlers (AUF-83-T5 / K-03).
 */
import React from 'react';
import { T, FARBEN } from '../studioDaten';
import { GESPERRT_ZEIGER, GESPERRT_BESCHRIFTUNG } from '../dashboard/gesperrtStil';
import { EngineFlaeche } from '../EngineFlaeche';
import { enginePanel } from '../dashboard/enginePanels';
import { faehigkeitNach } from '../tools/faehigkeiten';
import type { Werkzeug } from '../tools/werkzeugArten';
import type { Punkt } from '../../geometry/wallGeometry';
import type { raeumeAus, palettenGruppenFuer, PaletteEintrag } from '../ableitungen';
import type { palettenFlach } from '../dashboard/palette';

/**
 * **Einzeln benannte Eigenschaften, kein Sammelobjekt.** Die Scheibe zeigt an und meldet zurück;
 * sie hält nichts. *`paletteOffen`, `paletteFilter` und `paletteIndex` bleiben in der
 * Hauptfunktion — sonst gäbe es zwei Orte, an denen der Zustand der Palette wohnt.*
 */
export interface FussEigenschaften {
  // --- Statusleiste ---
  cursor: Punkt;
  zoom: number;
  raeume: ReturnType<typeof raeumeAus>;
  werkzeug: Werkzeug;
  wandStart: Punkt | null;
  treppeStart: { x: number; y: number } | null;
  letzteAblehnung: string | null;
  /** Z-01: der Satz, wenn ein Zug pausiert — `null`, wenn nichts zu sagen ist. */
  pausenHinweis: string | null;
  /** Z-05: was beim Konturzeichnen in der Leiste steht — Fortschritt oder Ablehnungsgrund. */
  konturHinweis: string | null;
  // --- Befehlspalette ---
  paletteOffen: boolean;
  paletteFilter: string;
  setPaletteFilter: (s: string) => void;
  setPaletteIndex: React.Dispatch<React.SetStateAction<number>>;
  paletteGruppen: ReturnType<typeof palettenGruppenFuer>;
  paletteListe: ReturnType<typeof palettenFlach>;
  paletteMarkiert: number;
  /** **Das Schliessen kommt von aussen.** Der Escape-Weg haengt am Escape-Stapel der
   *  Hauptfunktion; ein zweiter Weg hier war schon einmal ein echter Fehler (AUF-83-T5/K-03). */
  schliessePalette: () => void;
  aktivierePaletteEintrag: (eintrag: PaletteEintrag) => void;
  // --- Engine-Flaeche ---
  offeneEngine: string | null;
  setOffeneEngine: (id: string | null) => void;
}

export function FussUndUeberlagerungen({
  cursor, zoom, raeume, werkzeug, wandStart, treppeStart, letzteAblehnung, pausenHinweis, konturHinweis,
  paletteOffen, paletteFilter, setPaletteFilter, setPaletteIndex,
  paletteGruppen, paletteListe, paletteMarkiert, schliessePalette, aktivierePaletteEintrag,
  offeneEngine, setOffeneEngine,
}: FussEigenschaften): React.ReactElement {
  return (
    <>
  {/* Statusleiste */}
  <div style={{ display: 'flex', gap: 16, alignItems: 'center', padding: '7px 14px', background: T.surface, borderTop: `1px solid ${T.hair}`, fontSize: 12, color: FARBEN.gedaempft }}>
    <span>x {cursor.x} mm · y {cursor.y} mm</span>
    <span>Zoom {(zoom * 100).toFixed(0)} %</span>
    <span>Räume: {raeume.length} · Fläche gesamt: {(raeume.reduce((s, r) => s + r.flaecheMm2, 0) / 1_000_000).toFixed(2)} m²</span>
    {werkzeug === 'wand' && <span style={{ color: FARBEN.text }}>{wandStart ? 'Klick = nächster Wandpunkt · Esc beendet den Zug' : 'Klick setzt den Wandanfang'}</span>}
    {(werkzeug === 'fenster' || werkzeug === 'tuer') && <span style={{ color: FARBEN.text }}>Klick nahe einer Wand platziert die Öffnung</span>}
    {werkzeug === 'dach' && <span style={{ color: FARBEN.text }}>Klick legt ein Dach über den Gebäude-Umriss (ein Dach je Geschoss) — dann in 3D umschalten</span>}
    {werkzeug === 'treppe' && <span style={{ color: FARBEN.text }}>{treppeStart ? 'Klick = Ende der Lauflinie (Richtung = aufwärts) · Esc bricht ab' : 'Klick setzt den Anfang der Treppen-Lauflinie'}</span>}
    {/* Z-01: der pausierte Zug wird BENANNT. Schritt 0 hat gemessen, dass die Vorschau stehen
        blieb, ohne dass irgendetwas sagte, dass die Aktion noch laeuft. */}
    {/* Z-05: derselbe Platz und dieselbe Machart wie die uebrigen Werkzeug-Hinweise darueber.
        Der Grund einer abgelehnten Kontur steht HIER und nicht in `letzteAblehnung` — die gehoert
        abgelehnten KOMMANDOS, und Z-05 fuehrt keines aus. */}
    {konturHinweis && <span className="hp-kontur-hinweis">{konturHinweis}</span>}
    {pausenHinweis && <span className="hp-pause-hinweis">{pausenHinweis}</span>}
    <span className="hp-fu-fueller" />
    {letzteAblehnung && <span style={{ color: FARBEN.warnung, fontWeight: 600 }}>✋ {letzteAblehnung}</span>}
    <span className="hp-fu-befehlshinweis">Strg/⌘+K · Befehle</span>
  </div>

  {/* Dashboard v2.5 (§30 / UI-9): Command-Palette. Overlay `position: fixed` — außerhalb des
      Flusses, damit die Studio-Shell nicht überläuft (Kante 10). Bewusst KEINE im Rumpf
      definierte Komponente (Befund B1): das Filterfeld ist fokussierbar und würde sonst bei
      jedem Render neu montiert — der Fokus ginge bei jedem Tastendruck verloren.
      A11y in v2: role=dialog, aria-modal, aria-label, Autofokus, Esc. Ein vollständiger
      Fokus-Käfig (Tab-Zyklus, Fokus-Rückgabe) ist v6 und wird hier NICHT gebaut. */}
  {paletteOffen && (
    <div
      className="hp-fu-palette-flaeche"
      onMouseDown={(e) => { if (e.target === e.currentTarget) schliessePalette(); }}
    >
      <div
        role="dialog" aria-modal="true" aria-label="Befehle suchen"
        onMouseDown={(e) => e.stopPropagation()}
        className="hp-fu-palette-kasten"
      >
        <input
          autoFocus type="text" value={paletteFilter}
          aria-label="Befehl filtern"
          placeholder="Befehl suchen … (↑↓ wählen, Enter ausführen, Esc schließt)"
          onChange={(e) => { setPaletteFilter(e.target.value); setPaletteIndex(0); }}
          onKeyDown={(e) => {
            // AUF-83-T5 / K-03: das Schließen läuft über den Escape-Stapel (`useEscapeEbene
            // ('palette', …)` weiter oben in `HausplanerApp`) — der lauscht auf `document` und
            // wird von JEDEM Escape-Druck erreicht, auch bei Fokus in diesem Feld. Ein zweiter,
            // direkter `schliessePalette()`-Aufruf hier war die Ursache eines echten Fehlers:
            // Er löste einen Render aus, der GeschossFlaeches Stapel-Eintrag ABMELDETE UND NEU
            // ANMELDETE, bevor der Stapel-Listener selbst zum Zug kam — die Palette war dann
            // aus der Rangliste verschwunden, und das rangniedrigere Menü gewann fälschlich.
            // `preventDefault` bleibt: manche Browser leeren ein Textfeld nativ bei Escape.
            if (e.key === 'Escape') { e.preventDefault(); return; }
            if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
              e.preventDefault();
              if (paletteListe.length === 0) return;
              const d = e.key === 'ArrowDown' ? 1 : -1;
              setPaletteIndex((paletteMarkiert + d + paletteListe.length) % paletteListe.length);
              return;
            }
            if (e.key === 'Enter') {
              e.preventDefault();
              const treffer = paletteListe[paletteMarkiert];
              if (treffer) aktivierePaletteEintrag(treffer);
            }
          }}
          style={{ width: '100%', boxSizing: 'border-box', padding: '11px 14px', fontSize: 13.5, border: 'none', borderBottom: `1px solid ${T.hair}`, color: FARBEN.text, background: T.surface }}
        />
        <div className="hp-fu-palette-liste">
          {paletteListe.length === 0 ? (
            /* Kante 7 / AUF-67: kein leerer Kasten — und der Leerzustand spricht JE ART aus,
               was los ist. So lernt man nebenbei, wonach die Palette ueberhaupt sucht. */
            <div className="hp-fu-palette-leer">
              {paletteGruppen.map((g) => (
                <div key={g.art} className="hp-fu-gruppe-leer">{g.leer}</div>
              ))}
            </div>
          ) : (
            paletteGruppen.filter((g) => g.eintraege.length > 0).map((gruppe) => (
              <div key={gruppe.art}>
                {/* AUF-67: die Art steht als Ueberschrift — sonst stuenden Werkzeug und
                    Geschoss ununterscheidbar untereinander. */}
                <div className="hp-fu-gruppenkopf">{gruppe.titel}</div>
                {gruppe.eintraege.map((eintrag) => {
              const i = paletteListe.indexOf(eintrag);
              const markiert = i === paletteMarkiert;
              return (
                <button
                  key={`${eintrag.art}:${eintrag.id}`} type="button" tabIndex={-1}
                  aria-disabled={!eintrag.enabled}
                  title={eintrag.enabled ? eintrag.label : (eintrag.grund ?? eintrag.label)}
                  onMouseEnter={() => setPaletteIndex(i)}
                  onClick={() => aktivierePaletteEintrag(eintrag)}
                  style={{
                    display: 'flex', alignItems: 'center', gap: 8, width: '100%', textAlign: 'left',
                    padding: '7px 10px', border: 'none', borderRadius: 8, fontSize: 12.5,
                    /* Markierung als Hintergrund UND Schriftschnitt, nicht nur farblich. */
                    background: markiert ? T.brandWash : 'transparent',
                    fontWeight: markiert ? 700 : 500,
                    color: eintrag.enabled ? FARBEN.text : GESPERRT_BESCHRIFTUNG,
                    cursor: eintrag.enabled ? 'pointer' : GESPERRT_ZEIGER,
                  }}
                >
                  <span className="hp-fu-eintrag-label">{eintrag.label}</span>
                  {/* Der Grund steht als sichtbarer TEXT, nicht nur als Ausgrauen (§28). */}
                  {!eintrag.enabled && <span className="hp-fu-eintrag-grund">{eintrag.grund}</span>}
                  {eintrag.enabled && <span className="hp-fu-eintrag-zusatz">{eintrag.zusatz ?? ''}</span>}
                  {eintrag.shortcut && <span className="hp-fu-eintrag-kuerzel">{eintrag.shortcut}</span>}
                </button>
              );
                })}
              </div>
            ))
          )}
        </div>
      </div>
    </div>
  )}

  {/* AUF-33/L2: die Fläche einer Rechen-Engine. Sie liegt hier und nicht im Studio, weil der
      Auslöser hier liegt — der Fachplaner-Reiter der linken Schiene. Kopf, Zweck, Zurück und
      Escape kommen aus derselben `FlaechenHuelle` wie die L4-Flächen (AUF-25), kein zweiter
      Rahmen. Unbekannte Engine ⇒ nichts, kein Wurf. */}
  {offeneEngine && enginePanel(offeneEngine) && (
    <EngineFlaeche
      panel={enginePanel(offeneEngine)!}
      gruppe={faehigkeitNach(offeneEngine)?.gruppe ?? 'Fachplaner'}
      zustand={faehigkeitNach(offeneEngine)?.zustand ?? 'in_entwicklung'}
      zurueck="Zurück zum Planer"
      onZurueck={() => setOffeneEngine(null)}
    />
  )}
    </>
  );
}
