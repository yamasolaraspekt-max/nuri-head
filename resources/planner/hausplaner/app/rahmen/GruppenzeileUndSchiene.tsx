/**
 * AUF-48 Scheibe 4b — **die Zeilen unter dem Kopfrahmen und die linke Schiene.**
 *
 * Zwei Komponenten, weil **eine nicht geht**: die Themen-Gruppen-Zeile und die
 * Kontext-Options-Leiste sind Geschwister *über* der Messreihe, die Planer-Schiene liegt
 * *darin*. Zwischen ihnen steht `<div ref={inhaltRef}>` — das Maßband aus AUF-72, das die
 * Bühnenhöhe bestimmt und in `HausplanerApp` bleiben muss.
 *
 * ```text
 * <ArbeitsbereichZeilen/>        Themen-Gruppen (AUF-34) · Kontext-Optionen (§19/UI-4)
 * <div ref={inhaltRef}>          bleibt in der Hauptfunktion — das Maßband
 *   <PlanerSchiene/>             drei Reiter + Fuss (AUF-27)
 *   <div>…<Stage/>…</div>        Scheibe 4c
 * ```
 *
 * **Was hier NICHT passiert: nichts wird entschieden.** Das Markup ist zeichengleich das, was
 * vorher in `HausplanerApp.tsx` stand; keine Inline-Stelle wurde angefasst (AUF-38 Scheibe 7
 * kommt danach), keine Beschriftung umbenannt, keine Bedingung geändert.
 *
 * **Mitgezogen sind vier Modulebene-Stücke, die je genau EINEN Nutzer hatten** — gemessen, nicht
 * vermutet: `SCHIENE_ID`, `schienenReiterId`, `navItem` und `KontextOptionenLeiste`. Sie hier zu
 * lassen und aus `HausplanerApp` zu importieren wäre ein Ringschluss gewesen.
 */
import React from 'react';
import type { RefObject } from 'react';
import type Konva from 'konva';
import { useHausplanerStore } from '../../store/hausplanerStore';
import { T, FARBEN } from '../studioDaten';
import { werkzeugIcon } from '../reineHelfer';
import { GESPERRT_DECKKRAFT, GESPERRT_ZEIGER } from '../dashboard/gesperrtStil';
import { ReiterLeiste } from '../dashboard/ReiterLeiste';
import { SchienenSchalter } from '../dashboard/SchienenSchalter';
import { SCHIENEN_REITER, schienenReiter, type SchienenReiterId } from '../dashboard/schienenReiter';
import { WerkzeugGruppenMenue } from '../dashboard/WerkzeugGruppenMenue';
import { FaehigkeitenNavi } from '../FaehigkeitenNavi';
import { UnterlagenWerkzeuge } from '../unterlage/UnterlagenWerkzeuge';
import { ZustandBadge } from '../studioUi';
import { PROJEKTBAUM_LEER, projektBaum } from '../dashboard/projektBaum';
import { resolveToolState } from '../tools/activation';
import { toolNach, WORKSPACE_IMPORT } from '../tools/toolRegistry';
import type { Werkzeug } from '../tools/werkzeugArten';
import { brauchtOptionen } from '../tools/werkzeugVertrag';
import { tuerTyp, fensterTyp } from '../../geometry/oeffnungsTypen';
import { TUER_TYPEN, FENSTER_TYPEN, type TuerTyp, type FensterTyp } from '../../geometry/oeffnungsTypen';
import type { Wegweiser } from '../ableitungen';
import type { leisteMitAngehefteten } from '../ableitungen';

/**
 * AUF-27 — dasselbe für die linke Schiene. Eigenes Präfix, damit die drei Schienen-Reiter und die
 * vier Panel-Reiter im selben Dokument keine id teilen (Kante 5, jetzt zweifach relevant).
 */
/** Derselbe Zugriff wie in der Hauptfunktion (`const store = useHausplanerStore`) — ein
 *  Speicher, keine zweite Quelle. Der Hook wird hier nicht aufgerufen, nur `getState()` gelesen. */
const store = useHausplanerStore;

const SCHIENE_ID = 'hp-schiene-panel';
const schienenReiterId = (id: string): string => `hp-schiene-tab-${id}`;
// Die folgende Konstante bleibt: sie sieht gleich aus und hat eine echte Verwendung.
const navItem = (aktiv: boolean): React.CSSProperties => ({ display: 'flex', alignItems: 'center', gap: 9, textAlign: 'left', width: 'calc(100% - 12px)', margin: '1px 6px', padding: '8px 8px', border: 'none', borderRadius: 8, cursor: 'pointer', fontSize: 13, background: aktiv ? T.brandWash : 'transparent', color: aktiv ? T.brandInk : T.ink, fontWeight: aktiv ? 700 : 500 });

/**
 * Dashboard v2.1 (§19 / UI-4) — Kontext-Options-Leiste unter der Bedienleiste.
 *
 * Die Zuordnung Werkzeug → Optionen ist EIN `switch` über `activeToolId`: Bedingung und
 * Steuerelement liegen im selben `case` und können nicht auseinanderlaufen. Eine Parallelliste
 * (Werkzeug hier, Option dort) wäre genau die zweite Wahrheit, die wir sonst überall abbauen.
 *
 * **AUF-16 / Befund B1 — warum diese Komponente auf MODULEBENE steht und nicht im Rumpf von
 * `HausplanerApp`:** eine im Rumpf definierte Komponente bekommt bei JEDEM Render eine neue
 * Typ-Identität; React reißt ihren Teilbaum ab und baut ihn neu auf. `onMouseMove` rendert
 * `HausplanerApp` in Mausbewegungs-Frequenz — der `<select>` hier ist fokussierbar, also gingen
 * Fokus und Tastaturbedienung fortlaufend verloren. Bei zustandslosen Rumpf-Komponenten wie
 * `OpBtn` ist dasselbe Muster folgenlos; hier nicht. Deshalb: Modulebene, Werte als **explizite
 * Props** statt über Closure.
 *
 * Erweiterungspunkt (NICHT hier bauen): in v5 wird dieser `switch` durch einen Deskriptor aus
 * der Tool-Registry ersetzt, sobald die Zonen aus `toolPresentation.ts` die Leiste speisen.
 */
function KontextOptionenLeiste({
  werkzeug, fensterTypWahl, tuerTypWahl, setFensterTypWahl, setTuerTypWahl, fremderBereich,
}: {
  werkzeug: Werkzeug;
  fensterTypWahl: FensterTyp;
  tuerTypWahl: TuerTyp;
  setFensterTypWahl: (t: FensterTyp) => void;
  setTuerTypWahl: (t: TuerTyp) => void;
  /** AUF-34 / Kante 3: gesetzt, wenn das aktive Werkzeug im gewählten Arbeitsbereich nicht gilt. */
  fremderBereich?: string;
}): React.ReactElement {
  const aktivesTool = toolNach(werkzeug);
  const optionen = ((): React.ReactElement => {
    switch (werkzeug) {
      case 'fenster':
      case 'tuer': {
        const istFenster = werkzeug === 'fenster';
        return (
          <label style={{ fontSize: 12, color: FARBEN.gedaempft, display: 'flex', alignItems: 'center', gap: 5 }}>
            {istFenster ? 'Fenstertyp' : 'Türtyp'}
            <select
              value={istFenster ? fensterTypWahl : tuerTypWahl}
              onChange={(e) => (istFenster ? setFensterTypWahl(e.target.value as FensterTyp) : setTuerTypWahl(e.target.value as TuerTyp))}
              style={{ fontSize: 12.5, padding: '4px 8px', borderRadius: 8, border: `1px solid ${T.controlBorder}` }}
            >
              {(istFenster ? FENSTER_TYPEN : TUER_TYPEN).map((v) => (
                <option key={v.typ} value={v.typ}>{v.label} · {v.breite}×{v.hoehe} mm</option>
              ))}
            </select>
          </label>
        );
      }
      default:
        // AUF-45/B8: Zwei Fälle, die vorher einer waren. Ein Werkzeug, dessen Vertragseingaben nur
        // Gesten sind (Zeiger, Auswahlmodus), BRAUCHT keine Optionen — es ist nicht unfertig. Der
        // alte Platzhalter sagte „in Entwicklung" und machte aus „braucht nichts" ein Versprechen.
        return brauchtOptionen(werkzeug) ? (
          <span style={{ display: 'inline-flex', alignItems: 'center', gap: 7, fontSize: 12, color: FARBEN.gedaempft }}>
            Für dieses Werkzeug sind noch keine Optionen hinterlegt.
            <ZustandBadge zustand="in_entwicklung" />
          </span>
        ) : (
          <span style={{ fontSize: 12, color: FARBEN.gedaempft }}>
            Dieses Werkzeug braucht keine Optionen.
          </span>
        );
    }
  })();
  return (
    <div style={{ flex: '0 0 auto', display: 'flex', alignItems: 'center', gap: 10, padding: '5px 14px', fontSize: 12, background: T.surface2, borderBottom: `1px solid ${T.hair}` }}>
      <span style={{ fontWeight: 700, color: FARBEN.text }}>{aktivesTool?.label ?? 'Werkzeug'}</span>
      <span style={{ width: 1, height: 16, background: T.hair }} />
      {/* AUF-34 / Kante 3: Ein Bereichswechsel darf das aktive Werkzeug NICHT stillschweigend
          abwählen. Es bleibt gewählt — und sagt hier sichtbar, warum es gerade nicht greift.
          Vorher stand die Begründung nur im `title` der Leiste, also faktisch nirgends. */}
      {fremderBereich ? (
        <span style={{ display: 'inline-flex', alignItems: 'center', gap: 7, fontSize: 12, color: FARBEN.gedaempft, overflowWrap: 'anywhere' }}>
          Gehört zum Arbeitsbereich <strong style={{ color: FARBEN.text }}>{fremderBereich}</strong> — hier nicht verfügbar. Bereich oben wechseln.
          <ZustandBadge zustand="voraussetzung" />
        </span>
      ) : optionen}
    </div>
  );
}


/**
 * **Die zwei Zeilen unter dem Kopfrahmen.** Sie sind Geschwister und stehen ÜBER der Messreihe —
 * deshalb tragen sie eine eigene Komponente und nicht dieselbe wie die Schiene.
 *
 * **Einzeln benannte Eigenschaften, kein Sammelobjekt.**
 */
export interface ArbeitsbereichZeilenEigenschaften {
  /** Die Gruppenzeile ist das Ziel der `aria-controls` des Bereich-Wählers im Kopfrahmen. */
  panelId: string;
  bereichReiterId: (id: string) => string;
  activeWorkspace: string;
  offeneGruppe: React.ComponentProps<typeof WerkzeugGruppenMenue>['offen'];
  setOffeneGruppe: React.ComponentProps<typeof WerkzeugGruppenMenue>['setOffen'];
  werkzeugKontext: React.ComponentProps<typeof WerkzeugGruppenMenue>['kontext'];
  werkzeug: Werkzeug;
  angeheftet: React.ComponentProps<typeof WerkzeugGruppenMenue>['angeheftet'];
  heftUm: React.ComponentProps<typeof WerkzeugGruppenMenue>['onAnheften'];
  sichtbareGruppen: React.ComponentProps<typeof WerkzeugGruppenMenue>['gruppen'];
  fensterTypWahl: FensterTyp;
  tuerTypWahl: TuerTyp;
  setFensterTypWahl: (t: FensterTyp) => void;
  setTuerTypWahl: (t: TuerTyp) => void;
  fremderBereich: string | undefined;
}

export function ArbeitsbereichZeilen({
  panelId, bereichReiterId, activeWorkspace, offeneGruppe, setOffeneGruppe, werkzeugKontext,
  werkzeug, angeheftet, heftUm, sichtbareGruppen,
  fensterTypWahl, tuerTypWahl, setFensterTypWahl, setTuerTypWahl, fremderBereich,
}: ArbeitsbereichZeilenEigenschaften): React.ReactElement {
  const BEREICH_ID = panelId;
  return (
    <>
      {/* AUF-34: die Themen-Gruppen des GEWÄHLTEN Arbeitsbereichs — in einer EIGENEN Zeile.
          Vorher standen alle 22 Kategorien hinter den Icon-Knöpfen in derselben Zeile; gemessen
          waren das bei 1440 px drei Zeilen, und der waagerechte Überlauf schob das rechte Panel
          aus dem Bild. Jetzt: 15 Themen, davon 7 durchgängig und 8 an einen Bereich gebunden,
          in einer Zeile, die niemandem mehr den Platz nimmt.
          Dieser Bereich ist das Ziel der `aria-controls` des Bereich-Wählers — deshalb trägt er
          die Rolle und die id; ein Verweis ins Leere wäre schlimmer als kein Verweis. */}
      <div
        role="tabpanel" id={BEREICH_ID} aria-labelledby={bereichReiterId(activeWorkspace)}
        style={{ display: 'flex', alignItems: 'center', gap: 2, padding: '3px 14px 6px', background: T.bg, borderBottom: `1px solid ${T.hair}`, flex: '0 0 auto' }}
      >
        <WerkzeugGruppenMenue
          offen={offeneGruppe}
          setOffen={setOffeneGruppe}
          kontext={werkzeugKontext}
          aktivId={werkzeug}
          angeheftet={angeheftet}
          onAnheften={heftUm}
          gruppen={sichtbareGruppen}
        />
      </div>

      {/* Dashboard v2.1 (§19 / UI-4): Kontext-Options-Leiste — zeigt die Optionen des AKTIVEN
          Werkzeugs. Volle Breite, direkt unter der Bedienleiste, vor dem Canvas.
          AUF-16/B1: Die Komponente steht auf MODULEBENE (s. o.); ihre Werte kommen als Props. */}
      <KontextOptionenLeiste
        werkzeug={werkzeug}
        fensterTypWahl={fensterTypWahl}
        tuerTypWahl={tuerTypWahl}
        setFensterTypWahl={setFensterTypWahl}
        setTuerTypWahl={setTuerTypWahl}
        fremderBereich={fremderBereich}
      />
    </>
  );
}

/**
 * **Die linke Planer-Schiene** — drei Reiter (Werkzeuge · Fachplaner · Projekt) und ein Fuss, der
 * sagt, was im gerade sichtbaren Reiter steht.
 *
 * Sie liegt IN der Messreihe (`<div ref={inhaltRef}>`), nicht darüber — deshalb eine eigene
 * Komponente. *Die Reihe selbst bleibt in `HausplanerApp`: sie ist das Maßband aus AUF-72 und
 * umschliesst auch Bühne, 3D-Bereich und Eigenschaften-Panel.*
 */
export interface PlanerSchieneEigenschaften {
  istSchmal: boolean;
  schienen: { links: boolean; rechts: boolean };
  klappeSchiene: (seite: 'links' | 'rechts', offen: boolean) => void;
  schienenTab: SchienenReiterId;
  setSchienenTab: (id: SchienenReiterId) => void;
  wegweiser: Wegweiser | null;
  activeWorkspace: string;
  unterlage: React.ComponentProps<typeof UnterlagenWerkzeuge>['unterlage'] | null;
  stageRef: RefObject<Konva.Stage | null>;
  weltPunkt: React.ComponentProps<typeof UnterlagenWerkzeuge>['weltPunkt'];
  railWerkzeuge: ReturnType<typeof leisteMitAngehefteten>;
  werkzeug: Werkzeug;
  werkzeugKontext: React.ComponentProps<typeof WerkzeugGruppenMenue>['kontext'];
  /** Z-01: der EINE Ort, an dem ein Werkzeug endet. Die Schiene raeumt nicht mehr selbst auf. */
  beendeWerkzeug: (neu: Werkzeug) => void;
  setOffeneEngine: (id: string) => void;
  baum: ReturnType<typeof projektBaum>;
  selectedNodeIds: readonly string[];
  waehleAn: (id: string, ev: MouseEvent) => void;
}

export function PlanerSchiene({
  istSchmal, schienen, klappeSchiene, schienenTab, setSchienenTab, wegweiser, activeWorkspace,
  unterlage, stageRef, weltPunkt, railWerkzeuge, werkzeug, werkzeugKontext,
  beendeWerkzeug, setOffeneEngine, baum, selectedNodeIds, waehleAn,
}: PlanerSchieneEigenschaften): React.ReactElement {
  return (
    <>
    {/* L1: Planer-Schiene — AUF-27: DREI REITER statt drei gestapelter Blöcke.
        Vorher trugen Werkzeuge, Fachplaner und Projektbrowser eine gemeinsame Scroll-Höhe;
        der Projektbrowser war erst nach rund 20 Scroll-Ticks sichtbar. Jetzt ist immer genau
        ein Abschnitt sichtbar, und die Scroll-Höhe gehört dem Abschnitt, nicht der Spalte:
        `overflow` sitzt am Inhaltsbereich, NICHT mehr an dieser Spalte.
        Die Reiterleiste ist die gemeinsame `ReiterLeiste` (dasselbe Muster wie im
        Eigenschaften-Panel, AUF-19) — kein zweiter Tab-Mechanismus.
        AUF-83-T5 / K-01/K-02/K-05: klappbar. **`data-schiene` fehlt bewusst, wenn diese
        Schiene gerade als Overlay liegt** (schmales Fenster, offen) — dort nimmt sie keinen
        Platz aus der Reihe, und `buehnenBreite.ts` darf ihre Breite dann nicht abziehen; siehe
        die Begründung bei `useIstSchmal`. */}
    <div
      {...(istSchmal && schienen.links ? {} : { 'data-schiene': true })}
      className={istSchmal && schienen.links ? 'hp-schiene-overlay hp-schiene-overlay--links' : undefined}
      style={{
        width: schienen.links ? 220 : 32, flex: '0 0 auto', background: T.surface,
        borderRight: `1px solid ${T.hair}`, display: 'flex', flexDirection: 'column', overflow: 'hidden',
      }}
    >
      <div className="hp-schiene-kopf">
        {schienen.links && <div className="hp-schiene-kopf-reiter"><ReiterLeiste
          reiter={SCHIENEN_REITER}
          aktiv={schienenTab}
          setAktiv={(id) => setSchienenTab(id as SchienenReiterId)}
          ariaLabel="Planer-Bereiche"
          panelId={SCHIENE_ID}
          reiterId={schienenReiterId}
        /></div>}
        <SchienenSchalter seite="links" offen={schienen.links} label="Planer-Bereiche" onClick={() => klappeSchiene('links', !schienen.links)} />
      </div>
      {/* Zugeklappt bleibt nur der Schalter stehen — der Inhalt kostet dann weder Höhe noch
          Fokus-Stopps, die niemand sieht (dieselbe Überlegung wie bei GeschossFlaeche/K-06:
          gesperrter Inhalt wird nicht unsichtbar mitgerendert). */}
      {schienen.links && (
      <>
      {/* DER Inhaltsbereich der drei Reiter: eine Rolle, eine id, eigene Scroll-Höhe.
          `aria-labelledby` nennt den gerade aktiven Reiter. */}
      <div
        role="tabpanel" id={SCHIENE_ID} aria-labelledby={schienenReiterId(schienenTab)}
        style={{ flex: 1, minHeight: 0, overflowY: 'auto', display: 'flex', flexDirection: 'column' }}
      >
      {schienenTab === 'werkzeuge' && (<>
      {/* AUF-57: der Wegweiser an SEINEM Ort. „Wähle ein Bauteil aus" gehört dorthin, wo die
          Werkzeuge stehen — nicht in einen Balken über dem Plan. Er erscheint nur, wenn die
          Messung sagt, dass dieser Schritt wirklich etwas löst, und verschwindet danach. */}
      {wegweiser?.ort === 'schiene' && (
        <div style={{
          display: 'flex', alignItems: 'flex-start', gap: 8, margin: '8px 10px 4px',
          padding: '8px 10px', borderRadius: 9, background: T.brandWash,
          border: `1px solid ${T.brandInk}`, fontSize: 12, color: T.brandInk,
          lineHeight: 1.35, overflowWrap: 'anywhere',
        }}>
          <span aria-hidden style={{ flex: '0 0 auto' }}>→</span>
          <span style={{ flex: '1 1 120px', minWidth: 0 }}>{wegweiser.satz}</span>
        </div>
      )}
      {/* AUF-88-P1 / K-07 — der Einstieg sitzt HIER, im „Import & Nachzeichnen"-Arbeitsbereich
          aus AUF-83-T3, nicht in einer neuen Kopfleiste (Master-Prompt §17). Ohne Objekt
          (Studio) gibt es keine Unterlage — `unterlage` bleibt dann `null`. */}
      {activeWorkspace === WORKSPACE_IMPORT && unterlage && (
        <UnterlagenWerkzeuge
          unterlage={unterlage}
          csrfToken={store.getState().csrfToken ?? ''}
          stageRef={stageRef}
          weltPunkt={weltPunkt}
        />
      )}
      {/* Welle A2 (§19/UI-4): Die Leiste bezieht ihre Zugehörigkeit AUSSCHLIESSLICH aus der
          Präsentationsschicht — `zoneTools('fix')` statt `werkzeugTools()`. Vorher entschieden
          zwei Mechanismen unabhängig über dieselbe Frage (`art` in der Registry UND `zone` in den
          Regeln); sie stimmten nur zufällig überein. Jetzt ist es eine Wahrheit: wer ein Werkzeug
          aus der Leiste nehmen will, ändert eine Regel in `toolPresentation.ts`.
          Aktivierung bleibt `resolveToolState` — kein zweiter Filter. */}
      {railWerkzeuge.map((tool) => {
        const zustand = resolveToolState(tool, werkzeugKontext);
        const aktiv = werkzeug === tool.id;
        return (
          <button key={tool.id} type="button"
            title={zustand.enabled ? `${tool.label} (${tool.shortcut ?? ''}) — ${tool.helpText}` : `${tool.label} — ${zustand.reason}`}
            aria-disabled={!zustand.enabled}
            aria-pressed={aktiv}
            onClick={() => { if (!zustand.enabled) return; beendeWerkzeug(tool.id as Werkzeug); }}
            style={{ ...navItem(aktiv), ...(zustand.enabled ? {} : { opacity: GESPERRT_DECKKRAFT, cursor: GESPERRT_ZEIGER }) }}>
            <span style={{ width: 18, height: 18, display: 'grid', placeItems: 'center', flex: '0 0 auto' }}>{werkzeugIcon(tool.id)}</span>
            <span style={{ flex: 1 }}>{tool.label}</span>
            {tool.shortcut && <span style={{ fontSize: 10.5, color: T.muted, border: `1px solid ${T.controlBorder}`, borderRadius: 4, padding: '1px 5px' }}>{tool.shortcut}</span>}
          </button>
        );
      })}
      </>)}

      {/* AUF-27 / NACHTRAG: Der Reiter heisst „Fachplaner", nicht „Fähigkeiten" — derselbe
          Begriff wie in Ebene 2 der Inventur. ÜBERGANGSLÖSUNG BIS L2/L3 (AUF-33): dass die
          Fachgewerke hier in der Werkzeug-Schiene liegen, ist ein Zwischenstand, keine Absicht.
          Wohin sie gehören, ist ungeklärt — es gibt eine gemessene Teil-Doppelung mit den 19
          L4-Fachplaner-Flächen (mind. `engine-fbh`↔`fach-fbh`, `engine-pv`↔`fach-pv-module`,
          `engine-kueche`↔`fach-kueche`), aber keine 1:1-Deckung. Die Zusammenführung ist ein
          eigener, zu messender Posten. Bis dahin bleiben alle 22 Einträge erreichbar. */}
      {schienenTab === 'fachplaner' && (
        <FaehigkeitenNavi
          activeToolId={werkzeug}
          onAktivieren={(id) => beendeWerkzeug(id as Werkzeug)}
          onEngine={(id) => setOffeneEngine(id)}
        />
      )}

      {/* Dashboard v2.3 (§32 / UI-8): Projektbrowser — seit AUF-27 ein eigener REITER derselben
          220-px-Schiene, keine neue Spalte. Damit wächst die Schiene nicht, und die Bühne
          behält ihren Platz — seit AUF-83-T1a wird der ohnehin gemessen (`buehnenBreite.ts`)
          statt aus Fensterkonstanten gerechnet. Er ist ohne Scrollen erreichbar.
          Inhalt kommt als DATEN aus `projektBaum` (rein, getestet); das Markup bleibt dünn.
          Bewusst KEINE eigene, im Rumpf definierte Komponente (Befund B1 aus dem Batch-1-Votum):
          die Einträge sind fokussierbare Knöpfe und dürften sonst bei jedem Render neu montiert
          werden. Klick nutzt das vorhandene `selectNodes([id])` — keine zweite Auswahl-Wahrheit. */}
      {schienenTab === 'projekt' && (<>
      {baum.length === 0 ? (
        <div style={{ display: 'flex', flexDirection: 'column', alignItems: 'flex-start', gap: 6, padding: '2px 12px 10px', fontSize: 11.5, color: T.muted }}>
          <span>{PROJEKTBAUM_LEER}</span>
          {/* Ehrlich: es fehlt eine VORAUSSETZUNG (Bauteile), nicht eine Funktion. */}
          <ZustandBadge zustand="voraussetzung" />
        </div>
      ) : (
        baum.map((gruppe) => (
          <div key={gruppe.gruppe} style={{ marginBottom: 2 }}>
            <div style={{ display: 'flex', alignItems: 'center', gap: 6, padding: '6px 12px 2px', fontSize: 11, fontWeight: 700, letterSpacing: '.04em', color: T.muted }}>
              <span style={{ flex: 1 }}>{gruppe.gruppe}</span>
              <span style={{ fontVariantNumeric: 'tabular-nums' }}>{gruppe.anzahl}</span>
            </div>
            {gruppe.eingeklappt ? (
              /* Kante 6: zu viele Einträge — Kopf + Anzahl statt ungebremster Liste.
                 Virtuelles Scrollen ist v6 und wird hier nicht vorweggenommen. */
              <div style={{ padding: '0 12px 6px 20px', fontSize: 11, color: T.muted }}>
                Zusammengeklappt – {gruppe.anzahl} Einträge. Auswahl über den Plan.
              </div>
            ) : (
              gruppe.eintraege.map((eintrag) => {
                const gewaehlt = selectedNodeIds.includes(eintrag.id);
                return (
                  <button
                    key={eintrag.id} type="button"
                    title={`${eintrag.label} – im Plan auswählen`}
                    aria-current={gewaehlt ? 'true' : undefined}
                    // AUF-35a: auch der Projektbrowser geht durch `waehleAn` — Shift/Strg
                    // wirken hier wie im Plan. Zwei Auswahl-Wege mit verschiedenen Regeln
                    // wären genau die zweite Wahrheit, die dieser Posten beseitigt.
                    onClick={(e) => waehleAn(eintrag.id, e.nativeEvent)}
                    style={{
                      display: 'block', textAlign: 'left', width: 'calc(100% - 12px)', margin: '1px 6px',
                      padding: '4px 8px 4px 14px', border: 'none', borderRadius: 6, cursor: 'pointer', fontSize: 12,
                      /* Hervorhebung als Hintergrund UND Schriftschnitt — nicht nur farblich (WCAG 1.4.1). */
                      background: gewaehlt ? T.brandWash : 'transparent',
                      color: gewaehlt ? T.brandInk : T.ink,
                      fontWeight: gewaehlt ? 700 : 500,
                    }}
                  >
                    {eintrag.label}
                  </button>
                );
              })
            )}
          </div>
        ))
      )}
      </>)}
      </div>
      {/* Fuss der Schiene: steht UNTER dem Inhaltsbereich, gehört also keinem Reiter und
          scrollt nicht mit — er gilt für alle drei.

          AUF-56 (Nachtrag Yama, 26.07.): Hier stand **„Erweiterbar – Module folgen."** — genau
          die Vertröstung, die AUF-55 eine Fläche weiter entfernt hat. Sie sagte nichts über den
          Inhalt und alterte zu einer Unwahrheit, sobald niemand sie einlöst. **Jetzt sagt der
          Fuss, was im gerade sichtbaren Reiter steht** — und zwar mit dem Satz, den
          `SCHIENEN_REITER` ohnehin führt. Der lag bis heute nur im Tooltip, also faktisch
          nirgends; sichtbar gemacht statt neu erfunden. Kein zweiter Text, keine zweite
          Wahrheit. */}
      <div style={{ padding: '10px 12px', fontSize: 11, color: T.muted, borderTop: `1px solid ${T.canvasGrid}`, flex: '0 0 auto' }}>{schienenReiter(schienenTab)?.hinweis}</div>
      </>
      )}
    </div>
    </>
  );
}
