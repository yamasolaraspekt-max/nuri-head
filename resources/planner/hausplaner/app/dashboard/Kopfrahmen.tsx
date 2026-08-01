/**
 * AUF-48 Scheibe 4a — **der obere Rahmen der Oberfläche, aus der Hauptfunktion entnommen.**
 *
 * Drei Zeilen, in dieser Reihenfolge und mit diesen Aufgaben:
 *
 * 1. **Werkzeugzeile** — Marke (nur außerhalb des Studios), Geschoss-Fläche, Objektkopf,
 *    Überlauf mit Speicherstatus, Speichern-Knopf.
 * 2. **Arbeitsbereich-Zeile** — die Reiterleiste der Bereiche und der sichtbare Einstieg in die
 *    Befehlspalette (AUF-83-T3/K-05b).
 * 3. **Bedien-Werkzeugleiste** — Verlauf, Ansichtsmodus, Ansicht, Bearbeiten, Export (AUF-68/70).
 *
 * ---
 *
 * **Was diese Scheibe NICHT tut: sie ändert nichts.** Das Markup ist zeichengleich das, was
 * vorher in `HausplanerApp.tsx` zwischen dem Kommentar „Werkzeugleiste — neutral" und dem Ende
 * der Bedien-Werkzeugleiste stand. Keine Inline-Stelle wurde angefasst (das ist AUF-38
 * Scheibe 7 und kommt **danach**), keine Beschriftung umbenannt, keine Bedingung geändert.
 *
 * **Warum `opStil`, `OpBtn`, `opSep` und `OpGruppe` IM Rumpf stehen und nicht auf Modulebene:**
 * dort standen sie vorher auch — innerhalb von `HausplanerApp`. Auf Modulebene gehoben, bekämen
 * sie eine **stabile Funktions-Identität**, und React würde die Knöpfe fortan abgleichen statt
 * sie bei jedem Rendern neu einzuhängen. *Das wäre eine Verbesserung — aber eine sichtbare
 * Änderung* (ein Knopf, der gerade den Fokus trägt, verliert ihn heute bei jedem Rendern und
 * behielte ihn danach). **Dieser Auftrag ist Spur B und verbietet Verhaltensänderungen**, deshalb
 * bleibt der Zustand hier erhalten und der Befund geht als eigener Punkt an den Planner.
 */
import React from 'react';
import type { Level, SceneDocument, WallNode } from '../../domain/scene.types';
import type { HausplanerModus } from '../../store/hausplanerStore';
import type { Achse } from '../../geometry/editierGeometrie';
import { useHausplanerStore } from '../../store/hausplanerStore';
import { T, FARBEN, OP_TOKEN } from '../studioDaten';
import { svgWrap, opIcon, uuid } from '../reineHelfer';
import type { Wegweiser } from '../ableitungen';
import { GESPERRT_ZEIGER, GESPERRT_GRUND, GESPERRT_BESCHRIFTUNG } from './gesperrtStil';
import { opKnopfBild, type OpKnopfBild } from './opKnopfZustand';
import type { SpeicherAnzeige } from './speicherAnzeige';
import { GeschossFlaeche } from './GeschossFlaeche';
import { ObjektkopfUeberlauf } from './ObjektkopfUeberlauf';
import { ReiterLeiste } from './ReiterLeiste';
import { ARBEITSBEREICHE } from './arbeitsbereiche';
import { kopfzeile } from '../state/objektkopf';
import type { Objektkopf } from '../state/objektkopf';

/** Derselbe Zugriff wie in der Hauptfunktion (`const store = useHausplanerStore`) — ein Speicher,
 *  keine zweite Quelle. Der Hook wird hier nicht aufgerufen, nur `getState()` gelesen. */
const store = useHausplanerStore;

/** Die Bereiche als Reiter-Daten — einmal auf Modulebene, nicht bei jedem Render neu gebaut.
 *  `nochNicht` wird **durchgereicht, nicht gesetzt** (AUF-83-T3 / K-05). */
const bereichReiter = ARBEITSBEREICHE.map((b) => ({ id: b.id, label: b.label, hinweis: b.hinweis, nochNicht: b.nochNicht }));

/**
 * **Einzeln benannte Eigenschaften, kein Sammelobjekt** (K-04). Ein `props`-Klumpen verschöbe die
 * Unübersichtlichkeit nur eine Datei weiter — hier steht ablesbar, wovon der Kopfrahmen abhängt.
 */
export interface KopfrahmenEigenschaften {
  /** Im Studio steht keine Marke und kein Objektkopf — dort gibt es kein Objekt. */
  imStudio: boolean;
  // --- Werkzeugzeile: Geschoss ---
  scene: SceneDocument;
  level: Level;
  wegweiser: Wegweiser | null;
  geschossOffen: boolean;
  setGeschossOffen: React.Dispatch<React.SetStateAction<boolean>>;
  dupliziereGeschossJetzt: () => void;
  // --- Werkzeugzeile: Objektkopf, Status, Speichern ---
  objektkopf: Objektkopf | null;
  statusPill: { text: string; farbe: string; grund: string };
  objektkopfMenuOffen: boolean;
  setObjektkopfMenuOffen: React.Dispatch<React.SetStateAction<boolean>>;
  anzeige: SpeicherAnzeige;
  // --- Arbeitsbereich-Zeile ---
  activeWorkspace: string;
  waehleBereich: (id: string) => void;
  /** Die Gruppenzeile, auf die der Bereich-Wähler zeigt — sie bleibt in der Hauptfunktion. */
  panelId: string;
  reiterId: (id: string) => string;
  oeffnePalette: () => void;
  // --- Bedien-Werkzeugleiste ---
  modus: HausplanerModus;
  zoom: number;
  setZoom: React.Dispatch<React.SetStateAction<number>>;
  passeAnsichtEin: () => void;
  rasterAn: boolean;
  setRasterAn: React.Dispatch<React.SetStateAction<boolean>>;
  selectedNodeIds: readonly string[];
  dupliziere: () => void;
  loescheAuswahl: () => void;
  waende: readonly WallNode[];
  spiegeleGrundriss: (achse: Achse) => void;
  exportPng: () => void;
}

export function Kopfrahmen({
  imStudio, scene, level, wegweiser, geschossOffen, setGeschossOffen, dupliziereGeschossJetzt,
  objektkopf, statusPill, objektkopfMenuOffen, setObjektkopfMenuOffen, anzeige,
  activeWorkspace, waehleBereich, panelId, reiterId, oeffnePalette,
  modus, zoom, setZoom, passeAnsichtEin, rasterAn, setRasterAn,
  selectedNodeIds, dupliziere, loescheAuswahl, waende, spiegeleGrundriss, exportPng,
}: KopfrahmenEigenschaften): React.ReactElement {
  /**
   * AUF-59: Das Aussehen kommt aus `opKnopfBild` (rein, getestet) — nur die Token werden hier zu
   * Farben. Vorher unterschieden sich `bedienbar` und `gesperrt` allein in der Icon-Farbe, und
   * JEDER Knopf trug einen Rahmen; jetzt trägt ihn nur der eingeschaltete Schalter.
   * **An der Sperre selbst ändert sich nichts** — `disabled` wird unverändert von außen gesetzt.
   */
  const opStil = (b: OpKnopfBild): React.CSSProperties => ({
    display: 'grid', placeItems: 'center', width: 32, height: 30, borderRadius: 8,
    border: b.rahmenToken ? `1px solid ${OP_TOKEN[b.rahmenToken]}` : '1px solid transparent',
    background: OP_TOKEN[b.grundToken],
    color: OP_TOKEN[b.iconToken],
    opacity: b.deckkraft,
    cursor: b.cursor,
  });
  const OpBtn = ({ title, onClick, icon, label, disabled, aktiv, geplant, ariaLabel }: { title: string; onClick?: () => void; icon?: string; label?: string; disabled?: boolean; aktiv?: boolean; geplant?: boolean; ariaLabel?: string }): React.ReactElement => (
    <button type="button" title={geplant ? `${title} (geplant)` : title} aria-label={ariaLabel} onClick={geplant ? undefined : onClick} disabled={disabled || geplant}
      style={{
        ...opStil(opKnopfBild(Boolean(aktiv), Boolean(disabled || geplant))),
        ...(label ? { width: 'auto', padding: '0 9px', fontSize: 12, fontWeight: 600 } : null),
      }}>
      {label ?? opIcon(icon ?? '')}
    </button>
  );
  const opSep = (): React.ReactElement => <span className="hp-kr-trenner" />;
  /**
   * AUF-68 — die Gruppen der Bedienleiste. **Der Name bleibt, er wird nur unsichtbar.** Wer die
   * Zeile mit einem Vorleseprogramm bedient, behält die Gruppe als `role="group"` mit `aria-label`.
   */
  const OpGruppe = ({ name, children }: { name: string; children: React.ReactNode }): React.ReactElement => (
    <div role="group" aria-label={name} className="hp-kr-gruppe">{children}</div>
  );

  return (
    <>
      {/* Werkzeugleiste — neutral, Marke nur für Primäraktion */}
      {/* AUF-83-T3-N1: vertikales Innenmass von 10px auf 8px — der einzige Hebel, der Zeile 1
          SELBST betrifft, ohne einen ihrer Inhalte zu verkleinern oder zu verstecken. Gemessen
          gegen `d78c2466` (R22): schliesst die letzten 1,9 px, die trotz Ueberlauf-Umbau noch
          zwischen Vorher und Nachher standen. */}
      <div className="hp-kr-werkzeugzeile">
        {!imStudio && (
          <span className="hp-kr-marke">
            <span style={{ width: 26, height: 26, borderRadius: 7, background: FARBEN.auswahl, display: 'grid', placeItems: 'center', color: T.ink }}>{svgWrap(<><path d="M3 11l9-7 9 7" /><path d="M5 10v10h14V10" /></>)}</span>
            <strong className="hp-kr-markenname">Hausplaner <span style={{ fontWeight: 600, color: FARBEN.gedaempft, fontSize: 11.5 }}>· Solar Aspekt</span></strong>
          </span>
        )}
        {/* AUF-70: Rückgängig und Wiederholen sind in die Werkzeugzeile gezogen. Oben steht das
            DOKUMENT (Geschoss, Status, Speichern), unten das WERKZEUG — vorher standen Werkzeuge
            in zwei Zeilen. */}
        {/* Dashboard v2.1: Die Fenstertyp/Türtyp-Auswahl stand hier und ist byte-treu in die
            Kontext-Options-Leiste unter der Werkzeugleiste gewandert (§19/UI-4). Gleiche States,
            gleiche Optionslisten, gleiches onChange — der Platzierungspfad ist unberührt. */}
        {/* AUF-43: Die Geschoss-Bedienung hat die Zeile verlassen. Vorher standen hier ein
            111-px-Select, ein Textfeld mit DEMSELBEN Namen daneben und drei Verwaltungsknöpfe —
            zusammen mit Rückgängig und dem Ansichtsmodus, mit denen das Geschoss nichts zu tun hat.
            Jetzt: ein Knopf mit der Kurzfassung, dahinter die Fläche mit dem Stapel.
            `setActiveLevel` bleibt die einzige Wahrheit; die Commands sind dieselben. */}
        <GeschossFlaeche
          levels={scene.levels}
          aktivId={level.id}
          wegweiser={wegweiser?.ort === 'geschoss' ? wegweiser.satz : null}
          offen={geschossOffen}
          setOffen={setGeschossOffen}
          onWechseln={(id) => store.getState().setActiveLevel(id)}
          onUmbenennen={(name) => store.getState().executeCommand({ type: 'UPDATE_LEVEL', levelId: level.id, changes: { name } })}
          onAnlegen={() => {
            const oben = scene.levels.reduce((a, b) => (b.sortOrder > a.sortOrder ? b : a));
            const neu = {
              id: uuid(),
              name: `Geschoss ${scene.levels.length + 1}`,
              elevation: oben.elevation + oben.defaultWallHeight + oben.floorThickness,
              defaultWallHeight: oben.defaultWallHeight,
              floorThickness: oben.floorThickness,
              sortOrder: oben.sortOrder + 1,
            };
            if (store.getState().executeCommand({ type: 'ADD_LEVEL', level: neu })) {
              store.getState().setActiveLevel(neu.id);
            }
          }}
          onDuplizieren={dupliziereGeschossJetzt}
          onLoeschen={() => {
            const rest = scene.levels.filter((l) => l.id !== level.id);
            if (store.getState().executeCommand({ type: 'REMOVE_LEVEL', levelId: level.id }) && rest[0]) {
              store.getState().setActiveLevel(rest[0].id);
            }
          }}
        />
        {/* AUF-83-T3 / K-01 — **der Objektkopf, umgezogen aus `objekt.blade.php`.**
            T2 hat den doppelten Teil jener Leiste abgeräumt und diesen ausdrücklich stehen lassen:
            *„Objektname mit Adresse und der Übernehmen-Knopf samt Staleness-Pille … wandert mit T3
            in die Kopfleiste, dorthin, wo ohnehin Projekt · Geschoss steht."*
            **Im Studio steht hier nichts** — dort gibt es kein Objekt (Scratch, kein
            `data-speichern-url`), und `objektkopf` bleibt `null`. Ein erfundener Name wäre genau
            die Anzeige, die AUF-40 entfernt hat. */}
        {objektkopf && (
          <span className="hp-ok-name" title={kopfzeile(objektkopf)}>{kopfzeile(objektkopf)}</span>
        )}
        {/* Der Füller steht EINMAL und außerhalb der Bedingung — er trennt links von rechts in
            beiden Fällen. Ihn in beide Zweige zu kopieren hätte zwei statische Inline-Stellen aus
            einer gemacht und Scheibe 7 von 78 auf 79 gehoben; die Auflage lässt sie fallen oder
            gleich bleiben, nicht steigen. */}
        <span className="hp-kr-fueller" />
        {/* AUF-83-T3-N1 — **der Überlauf.** K-08 war rot: sechs Dinge in einer Reihe kosteten
            20 px, mehr als der Wegfall von `hp-bar` zurückgab. Übernehmen-Knopf, Staleness-Pille
            und Speicherstatus sind jetzt hinter EINEM Knopf, nicht drei eigene Flächen — dasselbe
            Muster wie beim Geschoss-Wähler (`GeschossFlaeche`), nicht neu erfunden. Nichts fällt
            weg: alle drei Elemente stehen unverändert im geöffneten Menü, mit demselben `action`,
            demselben Status und ohne zweite Statusquelle. */}
        {objektkopf ? (
          <ObjektkopfUeberlauf
            objektkopf={objektkopf}
            speicherstatus={statusPill}
            csrfToken={store.getState().csrfToken ?? ''}
            offen={objektkopfMenuOffen}
            setOffen={setObjektkopfMenuOffen}
          />
        ) : (
          // Im Studio gibt es keinen Objektkopf — die Zeile trug hier schon vorher nur den
          // Speicherstatus, drei Dinge insgesamt (Geschoss · Status · Speichern). Unverändert.
          <span style={{ fontSize: 12, fontWeight: 600, padding: '4px 12px', borderRadius: 999, color: statusPill.farbe, background: statusPill.grund }}>{statusPill.text}</span>
        )}
        <button
          type="button"
          onClick={() => void store.getState().save()}
          disabled={anzeige.gesperrt}
          title={anzeige.knopfTitel}
          style={{
            padding: '7px 16px', fontSize: 13, fontWeight: 700, borderRadius: 8, border: 'none',
            /* AUF-71: sechste Sperr-Fläche, in der Inventur nicht enthalten — beim Messen
               aufgefallen. Sie trug bereits genau die Werte der Quelle; jetzt liest sie sie,
               statt sie zu wiederholen. Wertgleich, kein sichtbarer Unterschied. */
            cursor: anzeige.gesperrt ? GESPERRT_ZEIGER : 'pointer',
            background: anzeige.gesperrt ? GESPERRT_GRUND : T.brand,
            color: anzeige.gesperrt ? GESPERRT_BESCHRIFTUNG : T.ink,
          }}
        >
          Speichern (Strg+S)
        </button>
      </div>

      {/* AUF-34: der Arbeitsbereich-Wähler. Er steht ÜBER der Gruppenzeile, weil er sie bestimmt.
          Gerendert von derselben `ReiterLeiste` wie Panel und Schiene (AUF-27): die Bereiche wählen
          aus, welcher Inhalt in der Gruppenzeile steht — genau ein Reiter-Verhalten. Ein dritter
          Mechanismus mit eigener Tastaturbedienung wäre eine zweite Wahrheit. */}
      <div className="hp-kr-objektzeile">
        <span style={{ fontSize: 10.5, fontWeight: 700, letterSpacing: '.06em', textTransform: 'uppercase', color: FARBEN.gedaempft, flex: '0 0 auto' }}>Arbeitsbereich</span>
        <div className="hp-kr-objektzeile-inhalt">
          <ReiterLeiste
            reiter={bereichReiter}
            aktiv={activeWorkspace}
            setAktiv={waehleBereich}
            ariaLabel="Arbeitsbereiche"
            panelId={panelId}
            reiterId={reiterId}
          />
        </div>
        {/* AUF-83-T3 / K-05b — die Befehlspalette bekommt einen sichtbaren Einstieg.
            **Erreichbar machen, nicht bauen:** derselbe `oeffnePalette`-Aufruf, den das Kürzel
            schon benutzt — kein zweiter Auslöser, keine zweite Logik.
            Er steht in DIESER Zeile und nicht in der Werkzeugzeile darunter, weil
            `eineWerkzeugzeile.test.ts` deren sechzehn Knöpfe (2·3·6·4·1) festhält; ein
            siebzehnter dort hätte eine abgenommene Zusage aus AUF-70 gebrochen. */}
        <button
          type="button"
          className="hp-az-suchen"
          onClick={oeffnePalette}
          title="Befehle und Werkzeuge durchsuchen (⌘K / Strg+K)"
        >
          Suchen
          <span className="hp-az-kuerzel">⌘K</span>
        </button>
      </div>

      {/* Bedien-Werkzeugleiste — Icons, jedes mit Tooltip + Funktionsbeschreibung */}
      <div className="hp-kr-bedienzeile">
        {/* AUF-68: die drei Wörter sind weg — der Name lebt als `aria-label` weiter.
            AUF-70: davor stehen jetzt Verlauf und Ansichtsmodus — eine Werkzeugzeile statt zwei.
            Rückgängig zuerst: es ist die Rettungsleine und gehört an den Anfang. Dann der
            Ansichtsmodus, weil er bestimmt, worauf alle folgenden Werkzeuge wirken. */}
        <OpGruppe name="Verlauf">
          <OpBtn title="Rückgängig (⌘Z)" ariaLabel="Rückgängig" icon="undo" onClick={() => store.getState().undo()} disabled={!store.getState().kannUndo()} />
          <OpBtn title="Wiederholen (⌘⇧Z)" ariaLabel="Wiederholen" icon="redo" onClick={() => store.getState().redo()} disabled={!store.getState().kannRedo()} />
        </OpGruppe>
        {opSep()}
        {/* P1c: Modus-Schalter — 3D ist der zweite Renderer DERSELBEN Daten (ein Store).
            AUF-70 §4c: die Wörter bleiben — für drei Ansichtsmodi gibt es keine gängige
            Bildsprache, ein Icon wäre Ratearbeit. */}
        <OpGruppe name="Ansichtsmodus">
          <OpBtn title="2D-Grundriss" label="2D" aktiv={modus === '2d'} onClick={() => store.getState().setModus('2d')} />
          <OpBtn title="2D und 3D nebeneinander" label="Split" aktiv={modus === 'split'} onClick={() => store.getState().setModus('split')} />
          <OpBtn title="3D-Ansicht" label="3D" aktiv={modus === '3d'} onClick={() => store.getState().setModus('3d')} />
        </OpGruppe>
        {opSep()}
        <OpGruppe name="Ansicht">
          <OpBtn title="Vergrößern (Zoom +) — näher an den Grundriss heranzoomen" icon="zoom-in" onClick={() => setZoom((z) => Math.min(1, z * 1.2))} />
          <OpBtn title="Verkleinern (Zoom −) — weiter herauszoomen" icon="zoom-out" onClick={() => setZoom((z) => Math.max(0.02, z / 1.2))} />
          <OpBtn title="Zoom zurücksetzen — Standardmaßstab wiederherstellen" icon="zoom-reset" onClick={() => setZoom(0.12)} />
          {/* AUF-44: BLEIBT. „Ansicht einpassen" hat als einziger der fünf **kein** Werkzeug im
              Katalog — es zu entfernen hieße, die Funktion ganz aus der Oberfläche zu tilgen, nicht
              nur eine Dublette. Ob sie ein Werkzeug bekommt oder bewusst gestrichen wird, ist eine
              Willensfrage und im Bericht zurückgegeben. */}
          <OpBtn title="Ansicht einpassen — gesamten Grundriss ins Bild rücken" icon="einpassen" onClick={passeAnsichtEin} />
          <OpBtn title="Raster ein-/ausblenden — Hintergrund-Hilfslinien" icon="grid" aktiv={rasterAn} onClick={() => setRasterAn((v) => !v)} />
          <OpBtn title="Fang ein-/ausschalten — an Punkten und Raster einrasten" icon="fang" aktiv={scene.settings.snapEnabled} onClick={() => store.getState().executeCommand({ type: 'UPDATE_SETTINGS', changes: { snapEnabled: !scene.settings.snapEnabled } })} />
          {/* AUF-44: Hier stand „Auswahl um 90° drehen (geplant)" — ein Knopf, der nichts tat und es
              nur im Tooltip zugab. Das Werkzeug `drehen` gibt es wirklich: es steht in seiner
              Themen-Gruppe („Bearbeiten & Transformieren") mit ehrlichem Zustand. Entfernt wurde die
              tote Dublette, nicht das Werkzeug. */}
        </OpGruppe>
        {opSep()}
        <OpGruppe name="Bearbeiten">
          <OpBtn title="Auswahl duplizieren — Kopie versetzt daneben einfügen" icon="dup" disabled={selectedNodeIds.length === 0} onClick={dupliziere} />
          <OpBtn title="Auswahl löschen (Entf) — markiertes Objekt entfernen" icon="del" disabled={selectedNodeIds.length === 0} onClick={loescheAuswahl} />
          <OpBtn title="Grundriss links/rechts spiegeln" icon="mirror-h" disabled={waende.length === 0} onClick={() => spiegeleGrundriss('vertikal')} />
          <OpBtn title="Grundriss oben/unten spiegeln" icon="mirror-v" disabled={waende.length === 0} onClick={() => spiegeleGrundriss('horizontal')} />
          {/* AUF-44: Ebenso „Messwerkzeug", „Bemaßung" und „Als PDF-Planblatt exportieren". Alle drei
              existieren als Werkzeuge (`distanz-messen`, `bemassen`, `pdf`) in „Messen & Bemaßen" bzw.
              „System, Suche & Export" — dort mit Zustand und Grund. In der Icon-Zeile waren sie
              Versprechen ohne Deckung, genau die Sorte, die I2 aus dem Katalog entfernt hat. */}
        </OpGruppe>
        {opSep()}
        <OpGruppe name="Messen & Export">
          <OpBtn title="Als PNG-Bild exportieren — aktuelle 2D-Ansicht herunterladen" icon="export" onClick={exportPng} />
        </OpGruppe>
        <span style={{ fontSize: 12, color: FARBEN.gedaempft, marginLeft: 'auto', fontVariantNumeric: 'tabular-nums' }}>Zoom {(zoom * 100).toFixed(0)} %</span>
      </div>
    </>
  );
}
