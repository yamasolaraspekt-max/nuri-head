// Premium-Treppen-Bauarten (Yamas SVG-Satz, Vogelperspektive fürs 2D-Panel). Reine Katalog-Daten;
// SVGs unter public/hausplaner/icons/treppe/. Getrennt von den Berechnungstypen in treppenTypen.ts.

export interface TreppenBauart {
  /** stabile ID = Dateiname ohne Endung, z. B. '04_u_treppe_halbgewendelt' */
  id: string;
  /** Dateiname im Icon-Ordner (Vogelperspektive) */
  datei: string;
  /** Anzeigename (Panel/Tooltip) */
  label: string;
}

export const TREPPEN_BAUARTEN: readonly TreppenBauart[] = [
  { id: '01_gerade_treppe', datei: '01_gerade_treppe.svg', label: 'Gerade Treppe' },
  { id: '02_l_treppe_links', datei: '02_l_treppe_links.svg', label: 'L-Treppe links' },
  { id: '03_l_treppe_rechts', datei: '03_l_treppe_rechts.svg', label: 'L-Treppe rechts' },
  { id: '04_u_treppe_halbgewendelt', datei: '04_u_treppe_halbgewendelt.svg', label: 'U halbgewendelt' },
  { id: '05_u_treppe_mit_podest', datei: '05_u_treppe_mit_podest.svg', label: 'U mit Podest' },
  { id: '06_u_treppe_rechteckiges_treppenauge', datei: '06_u_treppe_rechteckiges_treppenauge.svg', label: 'U rechteckig' },
  { id: '07_reihenhaus_treppe_mit_mittigem_treppenauge', datei: '07_reihenhaus_treppe_mit_mittigem_treppenauge.svg', label: 'Reihenhaus (Treppenauge)' },
  { id: '08_dreilaeufige_u_treppe_mit_treppenauge', datei: '08_dreilaeufige_u_treppe_mit_treppenauge.svg', label: 'Dreiläufig U' },
  { id: '09_spindeltreppe', datei: '09_spindeltreppe.svg', label: 'Spindeltreppe' },
  { id: '10_wendeltreppe', datei: '10_wendeltreppe.svg', label: 'Wendeltreppe' },
  { id: '11_raumspartreppe', datei: '11_raumspartreppe.svg', label: 'Raumspartreppe' },
  { id: '12_faltwerktreppe', datei: '12_faltwerktreppe.svg', label: 'Faltwerktreppe' },
  { id: '13_kragarmtreppe', datei: '13_kragarmtreppe.svg', label: 'Kragarmtreppe' },
  { id: '14_wangentreppe', datei: '14_wangentreppe.svg', label: 'Wangentreppe' },
  { id: '15_holmtreppe', datei: '15_holmtreppe.svg', label: 'Holmtreppe' },
  { id: '16_kellertreppe', datei: '16_kellertreppe.svg', label: 'Kellertreppe' },
  { id: '17_aussentreppe_gerade', datei: '17_aussentreppe_gerade.svg', label: 'Außentreppe gerade' },
  { id: '18_aussentreppe_mit_podest', datei: '18_aussentreppe_mit_podest.svg', label: 'Außentreppe Podest' },
  { id: '19_aussen_fluchttreppe', datei: '19_aussen_fluchttreppe.svg', label: 'Fluchttreppe' },
  { id: '20_zweilaeufige_gegenlaeufige_podesttreppe', datei: '20_zweilaeufige_gegenlaeufige_podesttreppe.svg', label: 'Zweiläufig gegenläufig' },
];

export function treppenBauartNach(id: string | undefined): TreppenBauart | undefined {
  return id ? TREPPEN_BAUARTEN.find((t) => t.id === id) : undefined;
}
