// Heizkörper-Bauarten (Schema-Symbole, ersetzbar durch Premium-SVGs). Reine Katalog-Daten;
// SVGs unter public/hausplaner/icons/heizkoerper/. Objekt-Platzierung als ObjectNode 'radiator'.

export interface HeizkoerperTyp {
  /** stabile ID = Dateiname ohne Endung */
  id: string;
  datei: string;
  label: string;
  /** Standard-Baulänge in mm (Grundriss-Footprint) */
  laenge: number;
  /** Standard-Bauhöhe in mm */
  hoehe: number;
}

export const HEIZKOERPER_TYPEN: readonly HeizkoerperTyp[] = [
  { id: '01_kompakt', datei: '01_kompakt.svg', label: 'Kompaktheizkörper', laenge: 1000, hoehe: 600 },
  { id: '02_roehren', datei: '02_roehren.svg', label: 'Röhrenheizkörper', laenge: 600, hoehe: 1800 },
  { id: '03_handtuch_bad', datei: '03_handtuch_bad.svg', label: 'Bad-/Handtuchheizkörper', laenge: 600, hoehe: 1200 },
  { id: '04_konvektor', datei: '04_konvektor.svg', label: 'Konvektor', laenge: 1200, hoehe: 250 },
  { id: '05_design_flach', datei: '05_design_flach.svg', label: 'Design-Flachheizkörper', laenge: 1000, hoehe: 600 },
];

export function heizkoerperTypNach(id: string | undefined): HeizkoerperTyp | undefined {
  return id ? HEIZKOERPER_TYPEN.find((t) => t.id === id) : undefined;
}
