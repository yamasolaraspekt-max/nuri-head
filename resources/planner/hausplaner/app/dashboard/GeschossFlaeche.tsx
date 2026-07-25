/**
 * AUF-43 — die **Geschoss-Fläche**. Sie verlässt die Zeile.
 *
 * **Vorher:** dreizehn Bedienelemente nebeneinander, vier unabhängige Aufgaben in einer Gruppe, der
 * Name zweimal (Select **und** Textfeld), die Höhenlage nirgends, kein Bild vom Stapel. Und das,
 * obwohl **ein angelegtes Geschoss 34 der 110 Werkzeuge entsperrt** — die folgenreichste Handlung
 * des Programms steckte in einem 111-px-Dropdown zwischen „Rückgängig" und „Speichern".
 *
 * **Jetzt:** ein Knopf mit der Kurzfassung (`Erdgeschoss · ±0 mm · 1 von 3`) öffnet eine Fläche, in
 * der der **Stapel von oben nach unten** steht — mit Höhenlage je Geschoss, dem aktiven hervorgehoben,
 * und darunter die Verwaltung (umbenennen · anlegen · duplizieren · löschen).
 *
 * **Der Name steht genau einmal:** Der Stapel ist eine Liste von **Knöpfen**, kein Select. Umbenannt
 * wird im **einen** Textfeld darunter, sichtbar beschriftet — heute war es ein Feld, das niemand als
 * solches erkannte, direkt neben einem Select mit demselben Wert.
 *
 * **Was hier NICHT passiert:** kein neuer Zustand (`setActiveLevel` bleibt die einzige Wahrheit),
 * kein neues Command (`ADD_LEVEL`/`UPDATE_LEVEL`/`REMOVE_LEVEL` reichen), kein Schema-Eingriff
 * (`elevation`, `sortOrder`, `name` werden **gezeigt**, nicht erfunden), keine Sortierumkehr.
 * Rückgängig/Wiederholen und der Ansichtsmodus kommen hier nicht vor — sie haben mit dem Geschoss
 * nichts zu tun.
 *
 * **Modulebene, nicht im Rumpf von `HausplanerApp`** (Befund B1): die Fläche trägt fokussierbare
 * Steuerelemente, und die App rendert in Mausbewegungs-Frequenz.
 */
import React, { useEffect, useRef, useState } from 'react';
import { T } from '../studioDaten';
import { stapel, kurzfassung, type StapelEintrag } from './geschossStapel';
import type { Level } from '../../domain/scene.types';

interface Props {
  levels: readonly Level[];
  aktivId: string | null;
  offen: boolean;
  setOffen: (offen: boolean) => void;
  onWechseln: (levelId: string) => void;
  onUmbenennen: (name: string) => void;
  onAnlegen: () => void;
  onDuplizieren: () => void;
  onLoeschen: () => void;
}

const knopfStil: React.CSSProperties = {
  fontSize: 12.5, padding: '5px 9px', borderRadius: 8, cursor: 'pointer',
  border: `1px solid ${T.controlBorder}`, background: T.surface, color: T.ink,
};

export function GeschossFlaeche({
  levels, aktivId, offen, setOffen, onWechseln, onUmbenennen, onAnlegen, onDuplizieren, onLoeschen,
}: Props): React.ReactElement {
  const s = stapel(levels, aktivId);
  const huelle = useRef<HTMLSpanElement>(null);
  const [name, setName] = useState(s.aktiv?.name ?? '');

  // Wechselt das aktive Geschoss, folgt das Namensfeld — sonst benennt man das falsche um.
  useEffect(() => { setName(s.aktiv?.name ?? ''); }, [s.aktiv?.id, s.aktiv?.name]);

  // Klick daneben und Esc schließen — wie beim Gruppen-Menü, kein zweites Verhalten.
  useEffect(() => {
    if (!offen) return undefined;
    const beiKlick = (e: MouseEvent): void => {
      if (huelle.current && !huelle.current.contains(e.target as Node)) setOffen(false);
    };
    const beiTaste = (e: KeyboardEvent): void => { if (e.key === 'Escape') setOffen(false); };
    document.addEventListener('mousedown', beiKlick);
    document.addEventListener('keydown', beiTaste);
    return () => { document.removeEventListener('mousedown', beiKlick); document.removeEventListener('keydown', beiTaste); };
  }, [offen, setOffen]);

  const uebernehmen = (): void => {
    const neu = name.trim();
    if (neu && neu !== s.aktiv?.name) onUmbenennen(neu);
    else setName(s.aktiv?.name ?? '');
  };

  const zeile = (e: StapelEintrag): React.ReactElement => (
    <button
      key={e.id} type="button" onClick={() => { onWechseln(e.id); setOffen(false); }}
      aria-current={e.aktiv ? 'true' : undefined}
      title={`${e.name} — Höhenlage ${e.hoehenLabel}`}
      style={{
        display: 'flex', alignItems: 'baseline', gap: 8, width: '100%', textAlign: 'left',
        padding: '7px 10px', borderRadius: 8, cursor: 'pointer', fontSize: 12.5,
        // Hervorhebung als Hintergrund UND Schriftschnitt — nicht nur farblich (WCAG 1.4.1).
        border: `1px solid ${e.aktiv ? T.brandInk : 'transparent'}`,
        background: e.aktiv ? T.brandWash : 'transparent',
        color: e.aktiv ? T.brandInk : T.ink, fontWeight: e.aktiv ? 700 : 500,
      }}
    >
      <span style={{ flex: '0 0 auto', fontVariantNumeric: 'tabular-nums', color: T.muted, fontSize: 11.5 }}>{e.position}</span>
      <span style={{ flex: 1, minWidth: 0, overflowWrap: 'anywhere' }}>{e.name}</span>
      <span style={{ flex: '0 0 auto', fontVariantNumeric: 'tabular-nums', color: e.aktiv ? T.brandInk : T.muted, fontSize: 11.5 }}>
        {e.hoehenLabel}
      </span>
    </button>
  );

  return (
    <span ref={huelle} style={{ position: 'relative', display: 'inline-flex' }}>
      <button
        type="button" aria-expanded={offen} aria-haspopup="dialog"
        onClick={() => setOffen(!offen)}
        title="Geschosse — wechseln, anlegen, umbenennen, löschen"
        style={{ ...knopfStil, fontWeight: 600, display: 'inline-flex', alignItems: 'center', gap: 6 }}
      >
        {kurzfassung(s)}
        <span style={{ fontSize: 10, color: T.muted }}>▾</span>
      </button>

      {offen && (
        <div
          role="dialog" aria-label="Geschosse"
          style={{
            position: 'absolute', top: '100%', left: 0, zIndex: 70, marginTop: 5,
            background: T.surface, border: `1px solid ${T.hair}`, borderRadius: 12,
            boxShadow: `0 10px 28px ${T.canvasWallGhost}`, padding: 10, minWidth: 290, maxWidth: '92vw',
            maxHeight: '70vh', overflowY: 'auto',
          }}
        >
          {/* 1 · Der Stapel — von oben nach unten, wie ein Gebäudeschnitt gelesen wird. */}
          <div style={{ fontSize: 11, fontWeight: 700, letterSpacing: '.06em', textTransform: 'uppercase', color: T.faint, marginBottom: 6 }}>
            Stapel · {s.anzahl} {s.anzahl === 1 ? 'Geschoss' : 'Geschosse'}
          </div>
          <div style={{ display: 'flex', flexDirection: 'column', gap: 2, marginBottom: 4 }}>
            {s.eintraege.map(zeile)}
          </div>
          <div style={{ fontSize: 11.5, color: T.muted, margin: '0 0 10px', overflowWrap: 'anywhere' }}>
            {s.aktiv
              ? `${s.darueber} darüber · ${s.darunter} darunter`
              : 'Kein Geschoss aktiv.'}
          </div>

          {/* 2 · Umbenennen — DAS eine Namensfeld, sichtbar beschriftet. */}
          <label style={{ display: 'block', marginBottom: 10 }}>
            <span style={{ display: 'block', fontSize: 11.5, color: T.muted, marginBottom: 4 }}>Name des aktiven Geschosses</span>
            <input
              type="text" value={name} disabled={!s.aktiv}
              onChange={(e) => setName(e.target.value)}
              onKeyDown={(e) => { if (e.key === 'Enter') (e.target as HTMLInputElement).blur(); }}
              onBlur={uebernehmen}
              style={{ width: '100%', boxSizing: 'border-box', ...knopfStil, cursor: 'text' }}
            />
          </label>

          {/* 3 · Verwaltung. Löschen bleibt so vorsichtig wie bisher: Titel nennt den Grund. */}
          <div style={{ display: 'flex', flexWrap: 'wrap', gap: 6 }}>
            <button type="button" style={knopfStil} title="Neues Geschoss über dem obersten anlegen" onClick={onAnlegen}>+ Geschoss</button>
            <button type="button" style={knopfStil} title="Aktuelles Geschoss als Vorlage duplizieren — Wände, Öffnungen und Dach werden ein Stockwerk höher kopiert" onClick={onDuplizieren}>⧉ Duplizieren</button>
            <button
              type="button" disabled={s.anzahl <= 1}
              style={{ ...knopfStil, opacity: s.anzahl <= 1 ? 0.4 : 1, cursor: s.anzahl <= 1 ? 'not-allowed' : 'pointer' }}
              title={s.anzahl <= 1 ? 'Das letzte Geschoss kann nicht gelöscht werden' : 'Aktives Geschoss löschen (muss leer sein)'}
              onClick={onLoeschen}
            >− Löschen</button>
          </div>
        </div>
      )}
    </span>
  );
}
