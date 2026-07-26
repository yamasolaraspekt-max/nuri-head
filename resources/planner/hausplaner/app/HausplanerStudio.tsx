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
  const [navZu, setNavZu] = React.useState(false);
  const [offeneHubs, setOffeneHubs] = React.useState<Record<string, boolean>>({});
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

  // Schmale Viewports (Handy/Baustelle): Navigation automatisch auf die Icon-Leiste einklappen,
  // damit der Inhalt nicht in einen Reststreifen gedrängt wird (ux-Rubrik: tragfähig auf dem Handy).
  React.useEffect(() => {
    if (typeof window === 'undefined') return undefined;
    const prüfe = (): void => setNavZu(window.innerWidth < 900);
    prüfe();
    window.addEventListener('resize', prüfe);
    return () => window.removeEventListener('resize', prüfe);
  }, []);

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

  const navBreit = navZu ? 66 : 266;
  const imExperte = modus === 'expert';

  const modeBtn = (m: StudioModus, label: string, ico: string): React.ReactElement => {
    const on = (m === 'expert' && imExperte) || (m === 'start' && !imExperte);
    return (
      <button type="button" onClick={() => setModus(m)}
        style={{ border: 0, background: on ? T.accentSoft : 'transparent', color: on ? T.accentInk : T.muted, fontWeight: 600, fontSize: 13, padding: '8px 16px', borderRadius: 9, cursor: 'pointer', display: 'flex', alignItems: 'center', gap: 7 }}>
        <Ikon inhalt={ico} size={16} />{label}
      </button>
    );
  };

  return (
    <div className="hp-studio" style={{ fontFamily: 'Inter, system-ui, sans-serif', color: T.ink, minHeight: '100vh', display: 'flex', flexDirection: 'column', background: T.bg }}>
      {/* Sichtbarer Tastatur-Fokus über das ganze Studio (ux-Rubrik: Fokus sichtbar). */}
      <style>{`.hp-studio :focus-visible{outline:2px solid ${T.accent};outline-offset:2px;border-radius:6px;}`}</style>
      {/* Kopfzeile */}
      {/* AUF-46: Die Kopfzeile war auf 62 px Höhe festgenagelt und durfte nicht umbrechen — bei
          390 px schob sie Titel, Status, Moduswechsel und Namenskürzel über den rechten Rand und
          riss die ganze Seite in den waagerechten Überlauf (gemessen: scrollWidth 656 bei 390).
          Jetzt: umbrechen statt schieben, Mindesthöhe statt fester Höhe. */}
      <header style={{ minHeight: 62, flex: '0 0 auto', display: 'flex', alignItems: 'center', flexWrap: 'wrap', gap: 12, padding: '8px 16px' }}>
        <div style={{ display: 'flex', alignItems: 'center', gap: 11, fontWeight: 700, fontSize: 16, minWidth: 0 }}>
          <span style={{ width: 30, height: 30, borderRadius: 9, background: T.brand, display: 'grid', placeItems: 'center', color: T.surface }}><Ikon inhalt='<path d="M3 11l9-7 9 7"/><path d="M5 10v9h14v-9"/>' size={16} /></span>
          Hausplaner
          <span style={{ fontWeight: 600, color: T.muted, fontSize: 13.5 }}>· Solar Aspekt</span>
        </div>
        <span style={{ display: 'flex', alignItems: 'center', gap: 7, color: T.muted, fontSize: 13 }}><span style={{ width: 8, height: 8, borderRadius: '50%', background: st.farbe }} />{st.label}{scene && kannSpeichern ? ` · Rev. ${scene.revision}` : ''}</span>
        <span style={{ flex: 1 }} />
        <div style={{ display: 'flex', background: T.surface, borderRadius: 12, padding: 4, boxShadow: T.schattenFlach }}>
          {modeBtn('start', 'Übersicht', '<path d="M4 5h16M4 12h16M4 19h10"/>')}
          {modeBtn('expert', 'Expertenmodus', '<rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>')}
        </div>
        <span style={{ width: 32, height: 32, borderRadius: '50%', background: '#dfe4ea', display: 'grid', placeItems: 'center', fontSize: 12, fontWeight: 700, color: '#5b636d' }}>YS</span>
      </header>

      {/* Bühne */}
      <div style={{ flex: 1, minHeight: 0, display: 'flex' }}>
        {/* Navigation (nur außerhalb Experte — Experte hat eigene Werkzeugleiste) */}
        {!imExperte && (
          <nav style={{ width: navBreit, flex: '0 0 auto', background: T.surface, borderRight: `1px solid ${T.hair}`, display: 'flex', flexDirection: 'column', transition: 'width .18s', overflow: 'hidden' }}>
            <div style={{ display: 'flex', alignItems: 'center', gap: 10, padding: '16px 16px 8px' }}>
              {!navZu && <span style={{ fontSize: 11.5, fontWeight: 700, letterSpacing: '.08em', textTransform: 'uppercase', color: T.faint }}>Navigation</span>}
              <button type="button" onClick={() => setNavZu((v) => !v)} title="Ein-/ausklappen" style={{ marginLeft: 'auto', width: 30, height: 30, border: 0, background: T.surface2, borderRadius: 9, color: T.muted, cursor: 'pointer', display: 'grid', placeItems: 'center' }}>
                <Ikon inhalt={navZu ? '<path d="M9 6l6 6-6 6"/>' : '<path d="M15 6l-6 6 6 6"/>'} size={16} />
              </button>
            </div>
            <button type="button" onClick={() => zeigeToast('Neue Anfrage / Lead: Planer wählen → Lead zuordnen → Dokumente → Formulardaten. (folgt)')}
              style={{ margin: '4px 12px 8px', display: 'flex', alignItems: 'center', gap: 10, background: T.accent, color: T.surface, border: 0, borderRadius: 12, padding: navZu ? 12 : '12px 14px', fontWeight: 700, fontSize: 13.5, cursor: 'pointer', justifyContent: navZu ? 'center' : 'flex-start' }}>
              <Ikon inhalt='<path d="M12 5v14M5 12h14"/>' size={16} />{!navZu && <span>Neue Anfrage / Lead</span>}
            </button>
            <div style={{ flex: 1, overflow: 'auto', padding: '4px 10px 12px' }}>
              {!navZu && <div style={{ fontSize: 10.5, fontWeight: 700, letterSpacing: '.07em', textTransform: 'uppercase', color: T.faint, margin: '14px 10px 5px' }}>Projekt</div>}
              {PROJ.map((p) => (
                <div key={p.name} role="button" tabIndex={0} onClick={() => gehGeführt(1)} onKeyDown={(e) => { if (istAusloeser(e)) gehGeführt(1); }}
                  style={{ display: 'flex', alignItems: 'center', gap: 11, padding: '9px 11px', borderRadius: 11, cursor: 'pointer', color: '#3f464e', fontSize: 14, justifyContent: navZu ? 'center' : 'flex-start' }}>
                  <span style={{ color: T.muted, display: 'grid', placeItems: 'center' }}><Ikon inhalt={p.icon} size={19} /></span>{!navZu && <span>{p.name}</span>}
                </div>
              ))}
              {!navZu && <div style={{ fontSize: 10.5, fontWeight: 700, letterSpacing: '.07em', textTransform: 'uppercase', color: T.faint, margin: '14px 10px 5px' }}>Fachplaner</div>}
              {FACH.map((f) => (
                <div key={f.name}>
                  <div role="button" tabIndex={0}
                    onClick={() => (f.sub ? setOffeneHubs((o) => ({ ...o, [f.name]: !o[f.name] })) : öffneKonfigurator(f.name))}
                    onKeyDown={(e) => { if (istAusloeser(e)) (f.sub ? setOffeneHubs((o) => ({ ...o, [f.name]: !o[f.name] })) : öffneKonfigurator(f.name)); }}
                    style={{ display: 'flex', alignItems: 'center', gap: 11, padding: '9px 11px', borderRadius: 11, cursor: 'pointer', color: '#3f464e', fontSize: 14, justifyContent: navZu ? 'center' : 'flex-start' }}>
                    <span style={{ color: T.muted, display: 'grid', placeItems: 'center' }}><Ikon inhalt={f.icon} size={19} /></span>
                    {!navZu && <span>{f.name}</span>}
                    {!navZu && f.sub && <span style={{ marginLeft: 'auto', color: T.faint, transform: offeneHubs[f.name] ? 'rotate(90deg)' : 'none', transition: 'transform .15s' }}><Ikon inhalt='<path d="M9 6l6 6-6 6"/>' size={16} /></span>}
                  </div>
                  {!navZu && f.sub && offeneHubs[f.name] && (
                    <div style={{ display: 'flex', flexDirection: 'column', margin: '2px 0 6px 22px', paddingLeft: 11, borderLeft: `1px solid ${T.hair}` }}>
                      {f.sub.map((sub) => (
                        <div key={sub[0]} role="button" tabIndex={0} onClick={() => öffneKonfigurator(sub[0], sub[1], 'navi')} onKeyDown={(e) => { if (istAusloeser(e)) öffneKonfigurator(sub[0], sub[1], 'navi'); }}
                          style={{ padding: '7px 10px', borderRadius: 9, fontSize: 13, color: T.muted, cursor: 'pointer' }}>{sub[0]}</div>
                      ))}
                    </div>
                  )}
                </div>
              ))}
            </div>
            {/* AUF-56 (Nachtrag Yama, 26.07.): Hier stand **„Erweiterbar — weitere Module
                folgen."** Derselbe Fall wie in der Schiene und wie in AUF-55: ein Versprechen auf
                später, das über den Inhalt nichts sagt. **Jetzt zählt der Fuss, was in der Liste
                darüber wirklich steht** — gerechnet aus `PROJ` und `FACH`, nicht abgeschrieben.
                Eine gezählte Zahl kann nicht veralten; eine abgetippte schon. */}
            {!navZu && <div style={{ padding: '12px 16px', borderTop: `1px solid ${T.hair2}`, fontSize: 12, color: T.faint }}>{PROJ.length} Projekt-Einstiege · {FACH.length} Fachplaner mit {FACH.reduce((n, f) => n + (f.sub?.length ?? 0), 0)} Untermodulen</div>}
          </nav>
        )}

        {/* Inhalt */}
        <div style={{ flex: 1, minHeight: 0, position: 'relative', overflow: imExperte ? 'hidden' : 'auto' }}>
          {modus === 'start' && <StartView onGuided={gehGeführt} onKonfigurator={(n, f) => öffneKonfigurator(n, f, 'start')} projekte={projekte} />}
          {modus === 'guided' && <GuidedView schritt={schritt} setSchritt={setSchritt} onExperte={() => setModus('expert')} onKonfigurator={(art) => setKonfig(art)} modell={modell} schritte={schritte} />}
          {imExperte && (
            <div style={{ position: 'absolute', inset: 0, display: 'flex', flexDirection: 'column' }}>
              <div style={{ display: 'flex', alignItems: 'center', gap: 10, padding: '6px 16px', background: T.surface, borderBottom: `1px solid ${T.hair}`, flex: '0 0 auto' }}>
                <button type="button" onClick={() => setModus('guided')} style={{ border: `1px solid ${T.hair}`, background: T.surface, color: T.ink, fontWeight: 600, fontSize: 13, padding: '7px 14px', borderRadius: 10, cursor: 'pointer', display: 'flex', alignItems: 'center', gap: 7 }}>
                  <Ikon inhalt='<path d="M15 6l-6 6 6 6"/>' size={15} />Zur geführten Planung
                </button>
                <span style={{ fontSize: 13, color: T.muted }}>Experte — alle Werkzeuge, Projektbaum und Eigenschaften. Dasselbe Modell und dieselbe Revision.</span>
              </div>
              <div style={{ flex: 1, minHeight: 0 }}><HausplanerApp imStudio /></div>
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
