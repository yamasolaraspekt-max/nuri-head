// Premium-Bauarten (Yamas SVG-Sätze) — Icon-Auswahl im Panel. Getrennt von den Zeichen-Vorlagen
// in oeffnungsTypen.ts (Standardmaße). Reine Katalog-Daten; SVGs unter public/hausplaner/icons/.
import type { OeffnungsArt } from './fensterProdukt';

export interface OeffnungsBauart {
  /** stabile ID = Dateiname ohne Endung, z. B. '05_dreh_kipp_links' */
  id: string;
  /** Dateiname im Icon-Ordner */
  datei: string;
  /** Anzeigename (Panel/Tooltip) */
  label: string;
  /** Vorgabe-Öffnungsart, sofern eindeutig; sonst undefined (Nutzer wählt) */
  oeffnungsArt?: OeffnungsArt;
}

export const FENSTER_BAUARTEN: readonly OeffnungsBauart[] = [
  { id: '01_festverglasung', datei: '01_festverglasung.svg', label: 'Festverglasung', oeffnungsArt: 'fest' },
  { id: '02_drehfenster_links', datei: '02_drehfenster_links.svg', label: 'Drehfenster links', oeffnungsArt: 'dreh' },
  { id: '03_drehfenster_rechts', datei: '03_drehfenster_rechts.svg', label: 'Drehfenster rechts', oeffnungsArt: 'dreh' },
  { id: '04_kippfenster', datei: '04_kippfenster.svg', label: 'Kippfenster', oeffnungsArt: 'kipp' },
  { id: '05_dreh_kipp_links', datei: '05_dreh_kipp_links.svg', label: 'Dreh-Kipp links', oeffnungsArt: 'dreh-kipp' },
  { id: '06_dreh_kipp_rechts', datei: '06_dreh_kipp_rechts.svg', label: 'Dreh-Kipp rechts', oeffnungsArt: 'dreh-kipp' },
  { id: '07_stulpfenster', datei: '07_stulpfenster.svg', label: 'Stulpfenster', oeffnungsArt: 'dreh' },
  { id: '08_oberlicht', datei: '08_oberlicht.svg', label: 'Oberlicht', oeffnungsArt: 'kipp' },
  { id: '09_unterlicht_fest', datei: '09_unterlicht_fest.svg', label: 'Unterlicht (fest)', oeffnungsArt: 'fest' },
  { id: '10_schiebefenster_2teilig', datei: '10_schiebefenster_2teilig.svg', label: 'Schiebefenster 2-teilig' },
  { id: '11_hebeschiebe_2teilig', datei: '11_hebeschiebe_2teilig.svg', label: 'Hebe-Schiebe 2-teilig' },
  { id: '12_hebeschiebe_3teilig', datei: '12_hebeschiebe_3teilig.svg', label: 'Hebe-Schiebe 3-teilig' },
  { id: '13_parallel_schiebe_kipp', datei: '13_parallel_schiebe_kipp.svg', label: 'Parallel-Schiebe-Kipp', oeffnungsArt: 'kipp' },
  { id: '14_falt_schiebe_3teilig', datei: '14_falt_schiebe_3teilig.svg', label: 'Falt-Schiebe 3-teilig' },
  { id: '15_falt_schiebe_4teilig', datei: '15_falt_schiebe_4teilig.svg', label: 'Falt-Schiebe 4-teilig' },
  { id: '16_schiebetuer_1fluegelig', datei: '16_schiebetuer_1fluegelig.svg', label: 'Schiebetür 1-flügelig' },
  { id: '17_schiebetuer_2fluegelig_mitte', datei: '17_schiebetuer_2fluegelig_mitte.svg', label: 'Schiebetür 2-flügelig (Mitte)' },
  { id: '18_eckfenster', datei: '18_eckfenster.svg', label: 'Eckfenster', oeffnungsArt: 'dreh' },
  { id: '19_trapezfenster', datei: '19_trapezfenster.svg', label: 'Trapezfenster', oeffnungsArt: 'fest' },
  { id: '20_rundfenster', datei: '20_rundfenster.svg', label: 'Rundfenster', oeffnungsArt: 'fest' },
  { id: '21_dachfenster', datei: '21_dachfenster.svg', label: 'Dachfenster', oeffnungsArt: 'kipp' },
  { id: '22_balkontuer_dreh_kipp', datei: '22_balkontuer_dreh_kipp.svg', label: 'Balkontür Dreh-Kipp', oeffnungsArt: 'dreh-kipp' },
  { id: '23_pfosten_riegel_element', datei: '23_pfosten_riegel_element.svg', label: 'Pfosten-Riegel-Element', oeffnungsArt: 'fest' },
  { id: '24_oberlichtband', datei: '24_oberlichtband.svg', label: 'Oberlichtband', oeffnungsArt: 'kipp' },
];

export const TUER_BAUARTEN: readonly OeffnungsBauart[] = [
  { id: '01_innentuer_links', datei: '01_innentuer_links.svg', label: 'Innentür links' },
  { id: '02_innentuer_rechts', datei: '02_innentuer_rechts.svg', label: 'Innentür rechts' },
  { id: '03_haustuer', datei: '03_haustuer.svg', label: 'Haustür' },
  { id: '04_wohnungseingangstuer', datei: '04_wohnungseingangstuer.svg', label: 'Wohnungseingangstür' },
  { id: '05_doppelfluegeltuer', datei: '05_doppelfluegeltuer.svg', label: 'Doppelflügeltür' },
  { id: '06_tuer_mit_oberlicht', datei: '06_tuer_mit_oberlicht.svg', label: 'Tür mit Oberlicht' },
  { id: '07_schiebetuer_vorwand', datei: '07_schiebetuer_vorwand.svg', label: 'Schiebetür (vor der Wand)' },
  { id: '08_schiebetuer_in_wand', datei: '08_schiebetuer_in_wand.svg', label: 'Schiebetür (in der Wand)' },
  { id: '09_pendeltuer', datei: '09_pendeltuer.svg', label: 'Pendeltür' },
  { id: '10_falttuer', datei: '10_falttuer.svg', label: 'Falttür' },
  { id: '11_pivottuer', datei: '11_pivottuer.svg', label: 'Pivottür' },
  { id: '12_ganzglas_schiebetuer', datei: '12_ganzglas_schiebetuer.svg', label: 'Ganzglas-Schiebetür' },
  { id: '13_brandschutztuer', datei: '13_brandschutztuer.svg', label: 'Brandschutztür' },
  { id: '14_rauchschutztuer', datei: '14_rauchschutztuer.svg', label: 'Rauchschutztür' },
  { id: '15_schallschutztuer', datei: '15_schallschutztuer.svg', label: 'Schallschutztür' },
  { id: '16_fluchttuer', datei: '16_fluchttuer.svg', label: 'Fluchttür' },
  { id: '17_notausgang_tuer', datei: '17_notausgang_tuer.svg', label: 'Notausgangstür' },
  { id: '18_mehrzwecktuer_mit_verglasung', datei: '18_mehrzwecktuer_mit_verglasung.svg', label: 'Mehrzwecktür (verglast)' },
  { id: '19_garagentor_sektional', datei: '19_garagentor_sektional.svg', label: 'Garagentor (sektional)' },
  { id: '20_haustuer_seitenteil_links', datei: '20_haustuer_seitenteil_links.svg', label: 'Haustür + Seitenteil links' },
  { id: '21_haustuer_seitenteil_rechts', datei: '21_haustuer_seitenteil_rechts.svg', label: 'Haustür + Seitenteil rechts' },
  { id: '22_haustuer_oberlicht_und_seitenteil', datei: '22_haustuer_oberlicht_und_seitenteil.svg', label: 'Haustür + Oberlicht & Seitenteil' },
  { id: '23_terrassentuer', datei: '23_terrassentuer.svg', label: 'Terrassentür' },
  { id: '24_tor_flugelig', datei: '24_tor_flugelig.svg', label: 'Tor (flügelig)' },
];

export function fensterBauartNach(id: string | undefined): OeffnungsBauart | undefined {
  return id ? FENSTER_BAUARTEN.find((t) => t.id === id) : undefined;
}
export function tuerBauartNach(id: string | undefined): OeffnungsBauart | undefined {
  return id ? TUER_BAUARTEN.find((t) => t.id === id) : undefined;
}
