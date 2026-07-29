/**
 * Hausplaner Studio (v9-Synthese) — Rahmen über der bestehenden App.
 * Kopf mit Modus-Umschalter (Übersicht/Experte) + persistente Navigation; die Bühne zeigt je nach
 * Modus den Start-Launcher, die geführte WizardBase oder die volle HausplanerApp (Experte).
 * Additiv: die HausplanerApp bleibt unverändert (nur ein optionales Flag blendet ihre Markenzeile aus).
 */
import React from 'react';
import { istAusloeser } from './dashboard/dialogFokus';
import { HausplanerApp } from './HausplanerApp';
import { StartView } from './StartView';
import { GuidedView } from './GuidedView';
import { T, FACH, PROJ, type StudioModus } from './studioDaten';
import { ConfigWizard, type KonfigArt } from './ConfigWizard';
import { FachFlaeche as FachFlaecheAnsicht } from './FachFlaeche';
import { ableitenSchritte } from './dashboard/fahrschritte';
import { usePlannerUiStore } from './state/uiState';
import { speicherAnzeige, type AnzeigeArt } from './dashboard/speicherAnzeige';
import { fachFlaecheNach, KONFIGURATOR_NAMEN, type FachFlaeche, type FlaechenHerkunft } from './dashboard/fachFlaechen';
import { Ikon } from './studioUi';
import { useHausplanerStore, type SpeicherStatus } from '../store/hausplanerStore';

export function HausplanerStudio(): React.ReactElement {
  const [modus, setModus] = React.useState<StudioModus>('start');
  const [schritt, setSchritt] = React.useState(1);
  const [toast, setToast] = React.useState<string | null>(null);
  const [konfig, setKonfig] = React.useState<KonfigArt | null>(null);
  /** L4 (AUF-25): offene Fachplaner-Fläche samt Herkunft — die Herkunft bestimmt die Beschriftung
   *  des Zurück-Wegs (Kante 2: nie pauschal „zur Startseite"). */
  const [fachOffen, setFachOffen] = React.useState<{ flaeche: FachFlaeche; herkunft: FlaechenHerkunft } | null>(null);
  const scene = useHausplanerStore((s) => s.scene);
  const speicherStatus = useHausplanerStore((s) => s.speicherStatus);
  /**
   * AUF-39/L5: die elf Fahrplan-Schritte, **aus dem Dokument abgeleitet** statt hartkodiert.
   * Rein und ohne Nebenwirkung — dieselbe Szene ergibt dieselben Schritte.
   */
  const schritte = React.useMemo(() => ableitenSchritte(scene), [scene]);
  // AUF-78: die Liste kommt aus dem UI-Zustand, den `main.tsx` aus dem Blade befüllt hat.
  // Auf der Studio-Fläche ist sie leer — dorthin reicht der Controller sie bewusst nicht durch.
  const projekte = usePlannerUiStore((st) => st.projekte);
  const modell = React.useMemo(() => {
    const nodes = scene?.nodes ?? [];
    return {
      geschosse: scene?.levels.length ?? 0,
      fenster: nodes.filter((n) => n.type === 'window').length,
      tuer: nodes.filter((n) => n.type === 'door').length,
      treppe: nodes.filter((n) => n.type === 'object' && n.objectType === 'stair').length,
    };
  }, [scene]);
  /**
   * AUF-47: **die zweite Statusanzeige** — und die, die Yama in der Sichtprobe gesehen hat
   * („Gespeichert · Rev. 1"). Sie stand direkt neben dem Hinweis „Testfläche — wird NICHT
   * gespeichert" und widersprach ihm. Sie liest jetzt dieselbe Regel wie die Plakette im Planer
   * (`dashboard/speicherAnzeige.ts`); die Farbe je Gewichtung bleibt hier.
   */
  const kannSpeichern = useHausplanerStore((s) => Boolean(s.speichernUrl));
  const konfliktRevision = useHausplanerStore((s) => s.konfliktRevision);
  const anzeige = speicherAnzeige(speicherStatus, kannSpeichern, konfliktRevision);
  const ART_FARBE: Record<AnzeigeArt, string> = { ok: T.ok, warnung: T.warn, neutral: T.info, fehler: T.err };
  const st = { label: anzeige.text, farbe: ART_FARBE[anzeige.art] };
  const toastTimer = React.useRef<number | undefined>(undefined);

  // AUF-83-T2: **Hier stand der Zuhoerer, der die Studio-Navigation auf schmalen Viewports
  // einklappte.** Mit ihr faellt er weg — die Ticket-Shell bringt ihre eigenen Seitenleisten und
  // deren Verhalten mit. **Ein Fenster-Zuhoerer ohne Wirkung waere genau die Sorte Rest, die
  // spaeter jemand fuer Absicht haelt.**

  const zeigeToast = React.useCallback((t: string) => {
    setToast(t);
    if (toastTimer.current) window.clearTimeout(toastTimer.current);
    toastTimer.current = window.setTimeout(() => setToast(null), 2600);
  }, []);

  const gehGeführt = (s?: number): void => { if (typeof s === 'number') setSchritt(s); setModus('guided'); };
  const öffneKonfigurator = (name: string, fenster?: boolean, herkunft: FlaechenHerkunft = 'navi'): void => {
    // L4 (AUF-25): Welche Module einen echten Konfigurator haben, steht an EINER Stelle —
    // `KONFIGURATOR_NAMEN` in `dashboard/fachFlaechen.ts`. Vorher standen die vier Namen hier
    // ein zweites Mal; damit hätte eine Ergänzung dort still ins Leere gelaufen.
    const art = fenster ? 'fenster' : KONFIGURATOR_NAMEN[name];
    if (art) { setKonfig(art); return; }
    // Kein Toast mehr: das Modul bekommt seine Fläche mit der Feldstruktur des späteren Panels.
    const flaeche = fachFlaecheNach(name);
    if (flaeche) setFachOffen({ flaeche, herkunft });
  };

  const imExperte = modus === 'expert';

  const modeBtn = (m: StudioModus, label: string, ico: string, titel?: string): React.ReactElement => {
    const on = m === modus;
    return (
      <button type="button" onClick={() => setModus(m)} title={titel}
        style={{ border: 0, background: on ? T.accentSoft : 'transparent', color: on ? T.accentInk : T.muted, fontWeight: 600, fontSize: 13, padding: '8px 16px', borderRadius: 9, cursor: 'pointer', display: 'flex', alignItems: 'center', gap: 7 }}>
        <Ikon inhalt={ico} size={16} />{label}
      </button>
    );
  };

  return (
    <div className="hp-studio">
      {/* Sichtbarer Tastatur-Fokus über das ganze Studio (ux-Rubrik: Fokus sichtbar). */}
      <style>{`.hp-studio :focus-visible{outline:2px solid ${T.accent};outline-offset:2px;border-radius:6px;}`}</style>
      {/* Kopfzeile */}
      {/* AUF-46: Die Kopfzeile war auf 62 px Höhe festgenagelt und durfte nicht umbrechen — bei
          390 px schob sie Titel, Status, Moduswechsel und Namenskürzel über den rechten Rand und
          riss die ganze Seite in den waagerechten Überlauf (gemessen: scrollWidth 656 bei 390).
          Jetzt: umbrechen statt schieben, Mindesthöhe statt fester Höhe. */}
      <header className="hp-studio-kopf">
        <span className="hp-status"><span style={{ width: 8, height: 8, borderRadius: '50%', background: st.farbe }} />{st.label}{scene && kannSpeichern ? ` · Rev. ${scene.revision}` : ''}</span>
        <span className="hp-fueller" />
        <div className="hp-modusschalter">
          {modeBtn('start', 'Übersicht', '<path d="M4 5h16M4 12h16M4 19h10"/>')}
          {modeBtn('guided', 'Geführte Planung', '<path d="M15 6l-6 6 6 6"/>')}
          {modeBtn('expert', 'Expertenmodus', '<rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>', 'Experte — alle Werkzeuge, Projektbaum und Eigenschaften. Dasselbe Modell und dieselbe Revision.')}
        </div>
        {/* AUF-38 Scheibe 4: **bleibt bewusst inline.** Diese zwei Farben haben in `T` keinen
            Token. In die CSS geholt waeren sie rohe Farbwerte in einer Regel — Kriterium 4 verbietet
            das; ihnen einen Token zu erfinden waere eine Palette-Entscheidung und nicht meine. */}
        <span style={{ width: 32, height: 32, borderRadius: '50%', background: '#dfe4ea', display: 'grid', placeItems: 'center', fontSize: 12, fontWeight: 700, color: '#5b636d' }}>YS</span>
      </header>

      {/* Bühne */}
      <div className="hp-studio-reihe">
        {/* AUF-83-T2: **Hier stand die zweite Navigation.** Sie zeichnete Projekt-Einstiege und
            Fachplaner als eigenen Baum — neben der Ticket-Navigation, die seit T1b dieselbe
            Aufgabe erfuellt und den Bereich ueber `active_routes` selbst markiert. Zwei
            Navigationsbaeume auf einem Schirm sind Wiederholung, keine Orientierung.
            **Die Daten bleiben:** `PROJ` und `FACH` stehen unveraendert in `studioDaten.ts` und
            wandern mit T3 in die Arbeitszeile — die Fachplaner SIND die Arbeitsbereiche. Hier
            faellt die Darstellung als Baum, nicht der Inhalt. */}
        {/* Inhalt */}
        <div style={{ flex: 1, minHeight: 0, position: 'relative', overflow: imExperte ? 'hidden' : 'auto' }}>
          {modus === 'start' && <StartView onGuided={gehGeführt} onKonfigurator={(n, f) => öffneKonfigurator(n, f, 'start')} projekte={projekte} />}
          {modus === 'guided' && <GuidedView schritt={schritt} setSchritt={setSchritt} onExperte={() => setModus('expert')} onKonfigurator={(art) => setKonfig(art)} modell={modell} schritte={schritte} />}
          {imExperte && (
            <div className="hp-experte">
              {/* AUF-83-T2 / K-04: **Der Erklaertext ist erhalten, aber er kostet keine Zeile
                  mehr.** Er stand als dauerhafte Leiste ueber der Buehne und beantwortete eine
                  Frage, die man genau einmal hat. Jetzt traegt ihn der Modusschalter als Titel.
                  **Der Weg zurueck in die gefuehrte Planung ist nicht verschwunden** — er steht
                  als eigener Schalter im Kopf, sichtbar in jedem Modus (K-05). */}
              <div className="hp-experte-buehne"><HausplanerApp imStudio /></div>
            </div>
          )}
        </div>
      </div>

      {toast && (
        <div style={{ position: 'fixed', left: '50%', bottom: 34, transform: 'translateX(-50%)', background: '#1a262a', color: T.surface, padding: '12px 20px', borderRadius: 12, fontSize: 13.5, boxShadow: T.schattenGehoben, zIndex: 80, maxWidth: 560 }}>{toast}</div>
      )}
      {konfig && (
        <ConfigWizard art={konfig} onClose={() => setKonfig(null)} onÜbernehmen={(nachricht) => { setKonfig(null); zeigeToast(nachricht); }} />
      )}
      {/* L4 (AUF-25): Fachplaner-Fläche statt „Konfigurator folgt"-Toast. Darstellung liegt in
          `FachFlaeche.tsx`, Inhalt in `dashboard/fachFlaechen.ts` — hier nur das Einhängen. */}
      {fachOffen && (
        <FachFlaecheAnsicht flaeche={fachOffen.flaeche} herkunft={fachOffen.herkunft} onZurueck={() => setFachOffen(null)} />
      )}
    </div>
  );
}
