/**
 * AUF-88-P1 — Hochladen, Kalibrieren, Herkunft zeigen. Der Einstieg sitzt im „Import &
 * Nachzeichnen"-Arbeitsbereich (K-07) — keine neue Kopfleiste, kein neuer Dialog-Rahmen.
 *
 * **Modulebene, nicht im Rumpf von `HausplanerApp`** (Befund B1) — dieselbe Begründung wie bei
 * `GeschossFlaeche`: fokussierbare Felder (Datei-Eingabe, Längen-Eingabe) verlören bei jedem
 * `onMouseMove`-Render den Fokus.
 *
 * **Was hier NICHT passiert:** die Insel rechnet den Maßstab (`kalibrierung.ts`, reine Funktion),
 * aber sie erfindet keine Wahrheit über den Verarbeitungsstand — der kommt vom Server
 * (`unterlagenHinweis`, `PlanUpload::alsUnterlage()`).
 */
import React, { useEffect, useRef, useState } from 'react';
import type Konva from 'konva';
import { GESPERRT_ZEIGER } from '../dashboard/gesperrtStil';
import { berechneMassstab, MASSSTAB_STANDARD, type Punkt } from './kalibrierung';
import { unterlagenHinweis, type UnterlageZustand } from '../state/unterlage';
import { usePlannerUiStore } from '../state/uiState';

interface Props {
  unterlage: UnterlageZustand;
  csrfToken: string;
  stageRef: React.RefObject<Konva.Stage | null>;
  weltPunkt: (e: Konva.KonvaEventObject<MouseEvent>) => Punkt;
}

const ENDUNGEN = '.pdf,.png,.jpg,.jpeg,.tif,.tiff';

export function UnterlagenWerkzeuge({ unterlage, csrfToken, stageRef, weltPunkt }: Props): React.ReactElement {
  const [wirdHochgeladen, setWirdHochgeladen] = useState(false);
  const [fehlerText, setFehlerText] = useState<string | null>(null);
  const [kalibrierModus, setKalibrierModus] = useState(false);
  const [ersterPunkt, setErsterPunkt] = useState<Punkt | null>(null);
  const [zweiPunkte, setZweiPunkte] = useState<{ a: Punkt; b: Punkt } | null>(null);
  const [laenge, setLaenge] = useState('');
  const [wirdGespeichert, setWirdGespeichert] = useState(false);
  const aktualisiere = usePlannerUiStore((s) => s.aktualisiereUnterlage);
  const dateiEingabe = useRef<HTMLInputElement>(null);
  const aktuelle = unterlage.aktuelle;

  // --- Hochladen -------------------------------------------------------------------------------

  const pollen = async (statusUrl: string): Promise<void> => {
    for (let versuch = 0; versuch < 12; versuch += 1) {
      await new Promise((r) => { setTimeout(r, 1500); });
      try {
        const antwort = await fetch(statusUrl, { headers: { Accept: 'application/json' } });
        if (!antwort.ok) continue;
        const stand = await antwort.json() as NonNullable<UnterlageZustand['aktuelle']>;
        aktualisiere(stand);
        if (stand.bildUrl || stand.fehler || stand.importDienstNoetig) return;
      } catch {
        // Netzwerkfehler beim Poll: nächster Versuch, kein Absturz — der Nutzer sieht weiterhin
        // „wird verarbeitet".
      }
    }
  };

  const dateiHochladen = async (datei: File): Promise<void> => {
    setWirdHochgeladen(true);
    setFehlerText(null);
    try {
      const form = new FormData();
      form.append('datei', datei);
      form.append('lead_alternative_add_id', String(unterlage.objektId));
      const antwort = await fetch(unterlage.hochladenUrl, {
        method: 'POST', body: form,
        headers: { 'X-CSRF-TOKEN': csrfToken, Accept: 'application/json' },
      });
      if (!antwort.ok) {
        const daten = await antwort.json().catch(() => null) as { errors?: Record<string, string[]> } | null;
        setFehlerText(daten?.errors?.datei?.[0] ?? 'Hochladen fehlgeschlagen.');
        return;
      }
      const stand = await antwort.json() as NonNullable<UnterlageZustand['aktuelle']>;
      aktualisiere(stand);
      void pollen(stand.statusUrl);
    } catch {
      setFehlerText('Hochladen fehlgeschlagen — Verbindung geprüft?');
    } finally {
      setWirdHochgeladen(false);
    }
  };

  // --- Kalibrieren -------------------------------------------------------------------------------

  // AUF-83-T5 / K-03-Muster (escapeStapel) gilt hier nicht — Kalibrieren ist kein Escape-Fall,
  // sondern ein eigener Modus mit eigenem Abbruch (nochmals klicken auf „Kalibrieren").
  useEffect(() => {
    if (!kalibrierModus) return undefined;
    const stage = stageRef.current;
    if (!stage) return undefined;

    const beiKlick = (e: Konva.KonvaEventObject<MouseEvent>): void => {
      const punkt = weltPunkt(e);
      if (!ersterPunkt) {
        setErsterPunkt(punkt);
        return;
      }
      setZweiPunkte({ a: ersterPunkt, b: punkt });
      setErsterPunkt(null);
      setKalibrierModus(false);
    };
    stage.on('click.kalibrieren', beiKlick);
    return () => { stage.off('click.kalibrieren'); };
  }, [kalibrierModus, ersterPunkt, stageRef, weltPunkt]);

  const kalibrierungSpeichern = async (): Promise<void> => {
    if (!zweiPunkte || !aktuelle) return;
    const laengeMm = Number(laenge.replace(',', '.'));
    const alterMassstab = aktuelle.massstabMmProEinheit ?? MASSSTAB_STANDARD;
    const neuerMassstab = berechneMassstab(alterMassstab, zweiPunkte.a, zweiPunkte.b, laengeMm);
    if (neuerMassstab === null) {
      setFehlerText('Länge prüfen — zwei unterschiedliche Punkte und eine Länge über 0 mm.');
      return;
    }
    setWirdGespeichert(true);
    setFehlerText(null);
    try {
      const antwort = await fetch(aktuelle.massstabUrl, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, Accept: 'application/json' },
        body: JSON.stringify({ massstab_mm_pro_einheit: neuerMassstab }),
      });
      if (!antwort.ok) { setFehlerText('Speichern fehlgeschlagen.'); return; }
      aktualisiere({ ...aktuelle, massstabMmProEinheit: neuerMassstab });
      setZweiPunkte(null);
      setLaenge('');
    } catch {
      setFehlerText('Speichern fehlgeschlagen — Verbindung geprüft?');
    } finally {
      setWirdGespeichert(false);
    }
  };

  const hinweis = aktuelle ? unterlagenHinweis(aktuelle) : '';

  return (
    <div className="hp-unterlage-feld">
      <div className="hp-unterlage-rubrik">Referenzunterlage</div>
      <input
        ref={dateiEingabe} type="file" accept={ENDUNGEN} className="hp-unterlage-datei-input"
        onChange={(e) => { const d = e.target.files?.[0]; if (d) void dateiHochladen(d); e.target.value = ''; }}
      />
      <button
        type="button" className="hp-unterlage-knopf"
        disabled={wirdHochgeladen}
        title="PDF oder Bild als Referenz unter die Zeichnung legen"
        onClick={() => dateiEingabe.current?.click()}
        style={wirdHochgeladen ? { cursor: GESPERRT_ZEIGER } : undefined}
      >
        {wirdHochgeladen ? 'Wird hochgeladen …' : aktuelle ? 'Andere Datei laden' : 'Datei laden'}
      </button>

      {aktuelle && (
        <>
          {/* K-05: Herkunft — Dateiname, Datum, Maßstab. Kein neues Provenienz-System, nur Anzeige. */}
          <div className="hp-unterlage-herkunft" title={aktuelle.originalName}>
            {aktuelle.originalName}
            {aktuelle.hochgeladenAm && (
              <span className="hp-unterlage-datum"> · {new Date(aktuelle.hochgeladenAm).toLocaleDateString('de-DE')}</span>
            )}
          </div>
          {aktuelle.massstabMmProEinheit && (
            <div className="hp-unterlage-massstab">Maßstab: {aktuelle.massstabMmProEinheit.toFixed(3)} mm/Einheit</div>
          )}

          {hinweis && <div className="hp-unterlage-hinweis">{hinweis}</div>}

          {aktuelle.bildUrl && !zweiPunkte && (
            <button
              type="button" className="hp-unterlage-knopf" aria-pressed={kalibrierModus}
              title="Zwei Punkte auf der Unterlage anklicken, deren echte Länge bekannt ist"
              onClick={() => { setKalibrierModus((v) => !v); setErsterPunkt(null); }}
            >
              {kalibrierModus ? (ersterPunkt ? 'Zweiten Punkt klicken …' : 'Ersten Punkt klicken …') : 'Kalibrieren'}
            </button>
          )}

          {zweiPunkte && (
            <div className="hp-unterlage-kalibrierung">
              <label className="hp-unterlage-laenge-label">
                Echte Länge dieser Strecke (mm)
                <input
                  type="number" min="1" step="1" value={laenge} autoFocus
                  onChange={(e) => setLaenge(e.target.value)}
                  className="hp-unterlage-laenge-eingabe"
                />
              </label>
              <div className="hp-unterlage-knopfreihe">
                <button type="button" className="hp-unterlage-knopf" disabled={wirdGespeichert} onClick={() => void kalibrierungSpeichern()}>
                  Übernehmen
                </button>
                <button type="button" className="hp-unterlage-knopf" onClick={() => { setZweiPunkte(null); setLaenge(''); }}>
                  Abbrechen
                </button>
              </div>
            </div>
          )}
        </>
      )}

      {fehlerText && <div className="hp-unterlage-fehler">{fehlerText}</div>}
    </div>
  );
}
