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
import { GESPERRT_DECKKRAFT, GESPERRT_ZEIGER } from './gesperrtStil';
import { T } from '../studioDaten';
import { stapel, kurzfassung, type StapelEintrag } from './geschossStapel';
import type { Level } from '../../domain/scene.types';
import { useEscapeEbene } from './escapeStapel';

interface Props {
  levels: readonly Level[];
  aktivId: string | null;
  /**
   * AUF-45: der Wegweiser-Satz, wenn das fehlende Geschoss gerade der größte Hemmschuh ist —
   * sonst `null`. Er steht DORT, wo die Handlung stattfindet, nicht in einem Banner irgendwo,
   * und **verschwindet**, sobald er erfüllt ist (der Aufrufer gibt dann `null`).
   */
  wegweiser?: string | null;
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
  levels, aktivId, wegweiser = null, offen, setOffen, onWechseln, onUmbenennen, onAnlegen, onDuplizieren, onLoeschen,
}: Props): React.ReactElement {
  const s = stapel(levels, aktivId);
  const huelle = useRef<HTMLSpanElement>(null);
  const [name, setName] = useState(s.aktiv?.name ?? '');

  // Wechselt das aktive Geschoss, folgt das Namensfeld — sonst benennt man das falsche um.
  useEffect(() => { setName(s.aktiv?.name ?? ''); }, [s.aktiv?.id, s.aktiv?.name]);

  // AUF-83-T5 / K-03: Escape läuft über den geteilten Stapel (Ebene „menue") — sonst schlösse ein
  // Escape dieses Menü UND z. B. die Befehlspalette gleichzeitig, wenn beide offen sind.
  useEscapeEbene('menue', offen, () => setOffen(false));

  // Klick daneben — unverändert, das ist keine Escape-Frage.
  useEffect(() => {
    if (!offen) return undefined;
    const beiKlick = (e: MouseEvent): void => {
      if (huelle.current && !huelle.current.contains(e.target as Node)) setOffen(false);
    };
    document.addEventListener('mousedown', beiKlick);
    return () => { document.removeEventListener('mousedown', beiKlick); };
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
      <span className="hp-gs-nummer">{e.position}</span>
      <span className="hp-gs-name">{e.name}</span>
      <span style={{ flex: '0 0 auto', fontVariantNumeric: 'tabular-nums', color: e.aktiv ? T.brandInk : T.muted, fontSize: 11.5 }}>
        {e.hoehenLabel}
      </span>
    </button>
  );

  return (
    <span ref={huelle} className="hp-gs-anker">
      <button
        type="button" aria-expanded={offen} aria-haspopup="dialog"
        onClick={() => setOffen(!offen)}
        title="Geschosse — wechseln, anlegen, umbenennen, löschen"
        style={{ ...knopfStil, fontWeight: 600, display: 'inline-flex', alignItems: 'center', gap: 6 }}
      >
        {kurzfassung(s)}
        <span className="hp-gs-zaehler">▾</span>
      </button>

      {offen && (
        <div
          role="dialog" aria-label="Geschosse"
          className="hp-gs-menue"
        >
          {/* 1 · Der Stapel — von oben nach unten, wie ein Gebäudeschnitt gelesen wird. */}
          <div className="hp-gs-rubrik">
            Stapel · {s.anzahl} {s.anzahl === 1 ? 'Geschoss' : 'Geschosse'}
          </div>
          <div className="hp-gs-liste">
            {s.eintraege.map(zeile)}
          </div>
          <div className="hp-gs-lead">
            {s.aktiv
              ? `${s.darueber} darüber · ${s.darunter} darunter`
              : 'Kein Geschoss aktiv.'}
          </div>

          {/* 2 · Umbenennen — DAS eine Namensfeld, sichtbar beschriftet. */}
          <label className="hp-gs-feld">
            <span className="hp-gs-feldlabel">Name des aktiven Geschosses</span>
            <input
              type="text" value={name} disabled={!s.aktiv}
              onChange={(e) => setName(e.target.value)}
              onKeyDown={(e) => { if (e.key === 'Enter') (e.target as HTMLInputElement).blur(); }}
              onBlur={uebernehmen}
              style={{ width: '100%', boxSizing: 'border-box', ...knopfStil, cursor: 'text' }}
            />
          </label>

          {/* AUF-45: der Wegweiser — ein Satz, kein Assistent, keine Tour. */}
          {wegweiser && (
            <div className="hp-gs-wegweiser">
              <span aria-hidden className="hp-gs-symbol">→</span>
              <span className="hp-gs-text">{wegweiser}</span>
            </div>
          )}

          {/* 3 · Verwaltung. Löschen bleibt so vorsichtig wie bisher: Titel nennt den Grund. */}
          <div className="hp-gs-knopfreihe">
            <button type="button" className="hp-gs-knopf" title="Neues Geschoss über dem obersten anlegen" onClick={onAnlegen}>+ Geschoss</button>
            <button type="button" className="hp-gs-knopf" title="Aktuelles Geschoss als Vorlage duplizieren — Wände, Öffnungen und Dach werden ein Stockwerk höher kopiert" onClick={onDuplizieren}>⧉ Duplizieren</button>
            <button
              type="button" disabled={s.anzahl <= 1}
              style={{ ...knopfStil, opacity: s.anzahl <= 1 ? GESPERRT_DECKKRAFT : 1, cursor: s.anzahl <= 1 ? GESPERRT_ZEIGER : 'pointer' }}
              title={s.anzahl <= 1 ? 'Das letzte Geschoss kann nicht gelöscht werden' : 'Aktives Geschoss löschen (muss leer sein)'}
              onClick={onLoeschen}
            >− Löschen</button>
          </div>
        </div>
      )}
    </span>
  );
}
