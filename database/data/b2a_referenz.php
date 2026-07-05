<?php

/**
 * B2a-1 Referenz-Kataloge — eingefroren aus wberechnung@b4a9eda (2026-07-05).
 * materials (DIN 4108-4 / ISO 10456) · konstruktionen (DIN EN ISO 6946) · baualtersklassen (IWU/TABULA-Richtwert).
 * verifikations_status setzt der ReferenzKatalogSeeder je Kategorie. NICHT manuell editieren (Re-Export aus wb).
 */

return array (
  'materials' => 
  array (
    0 => 
    array (
      'id' => 1,
      'name' => 'Vollziegel (Mauerziegel)',
      'kategorie' => 'mauerwerk',
      'lambda_w_mk' => 0.68,
      'rohdichte_kg_m3' => 1800,
      'quelle' => 'DIN 4108-4',
    ),
    1 => 
    array (
      'id' => 2,
      'name' => 'Hochlochziegel',
      'kategorie' => 'mauerwerk',
      'lambda_w_mk' => 0.4,
      'rohdichte_kg_m3' => 1200,
      'quelle' => 'DIN 4108-4',
    ),
    2 => 
    array (
      'id' => 3,
      'name' => 'Kalksandstein',
      'kategorie' => 'mauerwerk',
      'lambda_w_mk' => 0.99,
      'rohdichte_kg_m3' => 1800,
      'quelle' => 'DIN 4108-4',
    ),
    3 => 
    array (
      'id' => 4,
      'name' => 'Porenbeton (Gasbeton)',
      'kategorie' => 'mauerwerk',
      'lambda_w_mk' => 0.13,
      'rohdichte_kg_m3' => 500,
      'quelle' => 'DIN 4108-4',
    ),
    4 => 
    array (
      'id' => 5,
      'name' => 'Leichtbeton',
      'kategorie' => 'mauerwerk',
      'lambda_w_mk' => 0.5,
      'rohdichte_kg_m3' => 1200,
      'quelle' => 'DIN 4108-4',
    ),
    5 => 
    array (
      'id' => 6,
      'name' => 'Bimshohlblockstein',
      'kategorie' => 'mauerwerk',
      'lambda_w_mk' => 0.32,
      'rohdichte_kg_m3' => 800,
      'quelle' => 'DIN 4108-4',
    ),
    6 => 
    array (
      'id' => 7,
      'name' => 'Stahlbeton',
      'kategorie' => 'beton',
      'lambda_w_mk' => 2.3,
      'rohdichte_kg_m3' => 2400,
      'quelle' => 'DIN 4108-4',
    ),
    7 => 
    array (
      'id' => 8,
      'name' => 'Normalbeton',
      'kategorie' => 'beton',
      'lambda_w_mk' => 2.1,
      'rohdichte_kg_m3' => 2200,
      'quelle' => 'DIN 4108-4',
    ),
    8 => 
    array (
      'id' => 9,
      'name' => 'Nadelholz (Vollholz)',
      'kategorie' => 'holz',
      'lambda_w_mk' => 0.13,
      'rohdichte_kg_m3' => 500,
      'quelle' => 'DIN 4108-4',
    ),
    9 => 
    array (
      'id' => 10,
      'name' => 'Brettschichtholz',
      'kategorie' => 'holz',
      'lambda_w_mk' => 0.13,
      'rohdichte_kg_m3' => 500,
      'quelle' => 'DIN 4108-4',
    ),
    10 => 
    array (
      'id' => 11,
      'name' => 'OSB-/Spanplatte',
      'kategorie' => 'holz',
      'lambda_w_mk' => 0.13,
      'rohdichte_kg_m3' => 650,
      'quelle' => 'DIN 4108-4',
    ),
    11 => 
    array (
      'id' => 12,
      'name' => 'Gipskartonplatte',
      'kategorie' => 'holz',
      'lambda_w_mk' => 0.25,
      'rohdichte_kg_m3' => 900,
      'quelle' => 'DIN 4108-4',
    ),
    12 => 
    array (
      'id' => 13,
      'name' => 'Gipsputz',
      'kategorie' => 'putz',
      'lambda_w_mk' => 0.51,
      'rohdichte_kg_m3' => 1200,
      'quelle' => 'DIN 4108-4',
    ),
    13 => 
    array (
      'id' => 14,
      'name' => 'Kalkgipsputz',
      'kategorie' => 'putz',
      'lambda_w_mk' => 0.7,
      'rohdichte_kg_m3' => 1400,
      'quelle' => 'DIN 4108-4',
    ),
    14 => 
    array (
      'id' => 15,
      'name' => 'Kalkzementputz (Außenputz)',
      'kategorie' => 'putz',
      'lambda_w_mk' => 1,
      'rohdichte_kg_m3' => 1800,
      'quelle' => 'DIN 4108-4',
    ),
    15 => 
    array (
      'id' => 16,
      'name' => 'Zementestrich',
      'kategorie' => 'estrich',
      'lambda_w_mk' => 1.4,
      'rohdichte_kg_m3' => 2000,
      'quelle' => 'DIN 4108-4',
    ),
    16 => 
    array (
      'id' => 17,
      'name' => 'Anhydritestrich',
      'kategorie' => 'estrich',
      'lambda_w_mk' => 1.2,
      'rohdichte_kg_m3' => 2100,
      'quelle' => 'DIN 4108-4',
    ),
    17 => 
    array (
      'id' => 18,
      'name' => 'EPS (Polystyrol-Hartschaum)',
      'kategorie' => 'daemmung',
      'lambda_w_mk' => 0.035,
      'rohdichte_kg_m3' => 20,
      'quelle' => 'DIN 4108-4',
    ),
    18 => 
    array (
      'id' => 19,
      'name' => 'XPS (extrudiertes Polystyrol)',
      'kategorie' => 'daemmung',
      'lambda_w_mk' => 0.035,
      'rohdichte_kg_m3' => 35,
      'quelle' => 'DIN 4108-4',
    ),
    19 => 
    array (
      'id' => 20,
      'name' => 'Mineralwolle (Glas/Stein)',
      'kategorie' => 'daemmung',
      'lambda_w_mk' => 0.035,
      'rohdichte_kg_m3' => 30,
      'quelle' => 'DIN 4108-4',
    ),
    20 => 
    array (
      'id' => 21,
      'name' => 'PUR/PIR-Hartschaum',
      'kategorie' => 'daemmung',
      'lambda_w_mk' => 0.025,
      'rohdichte_kg_m3' => 35,
      'quelle' => 'DIN 4108-4',
    ),
    21 => 
    array (
      'id' => 22,
      'name' => 'Holzfaserdämmplatte',
      'kategorie' => 'daemmung',
      'lambda_w_mk' => 0.04,
      'rohdichte_kg_m3' => 160,
      'quelle' => 'DIN 4108-4',
    ),
    22 => 
    array (
      'id' => 23,
      'name' => 'Zellulose (Einblasdämmung)',
      'kategorie' => 'daemmung',
      'lambda_w_mk' => 0.04,
      'rohdichte_kg_m3' => 50,
      'quelle' => 'DIN 4108-4',
    ),
  ),
  'konstruktionen' => 
  array (
    0 => 
    array (
      'name' => 'Außenwand Ziegel 24 cm (unsaniert)',
      'typ' => 'aussenwand',
      'schichten' => '[{"material_id":15,"dicke_mm":20,"lambda_override":null},{"material_id":1,"dicke_mm":240,"lambda_override":null},{"material_id":13,"dicke_mm":15,"lambda_override":null}]',
      'u_wert_berechnet' => 1.747,
      'quelle' => 'Typischer Bestand',
      'ist_vorlage' => 1,
    ),
    1 => 
    array (
      'name' => 'Außenwand + WDVS 140 mm EPS',
      'typ' => 'fassade_wdvs',
      'schichten' => '[{"material_id":15,"dicke_mm":15,"lambda_override":null},{"material_id":18,"dicke_mm":140,"lambda_override":null},{"material_id":1,"dicke_mm":240,"lambda_override":null},{"material_id":13,"dicke_mm":15,"lambda_override":null}]',
      'u_wert_berechnet' => 0.219,
      'quelle' => 'WDVS-Sanierung',
      'ist_vorlage' => 1,
    ),
    2 => 
    array (
      'name' => 'Steildach gedämmt 200 mm',
      'typ' => 'dach',
      'schichten' => '[{"material_id":20,"dicke_mm":200,"lambda_override":null},{"material_id":12,"dicke_mm":13,"lambda_override":null}]',
      'u_wert_berechnet' => 0.169,
      'quelle' => 'Zwischensparrendämmung',
      'ist_vorlage' => 1,
    ),
    3 => 
    array (
      'name' => 'Oberste Geschossdecke gedämmt 160 mm',
      'typ' => 'decke',
      'schichten' => '[{"material_id":7,"dicke_mm":160,"lambda_override":null},{"material_id":20,"dicke_mm":160,"lambda_override":null}]',
      'u_wert_berechnet' => 0.209,
      'quelle' => 'Aufdeckendämmung',
      'ist_vorlage' => 1,
    ),
    4 => 
    array (
      'name' => 'Kellerdecke gedämmt 100 mm',
      'typ' => 'boden',
      'schichten' => '[{"material_id":7,"dicke_mm":180,"lambda_override":null},{"material_id":18,"dicke_mm":100,"lambda_override":null}]',
      'u_wert_berechnet' => 0.322,
      'quelle' => 'Unterseitendämmung',
      'ist_vorlage' => 1,
    ),
  ),
  'baualtersklassen' => 
  array (
    0 => 
    array (
      'von_jahr' => 1800,
      'bis_jahr' => 1918,
      'sanierungsstufe' => 'unsaniert',
      'u_wand' => 1.7,
      'u_dach' => 2.6,
      'u_boden' => 1.2,
      'u_fenster' => 5,
      'u_tuer' => 3,
      'quelle' => 'IWU/TABULA (Richtwerte, to_verify)',
    ),
    1 => 
    array (
      'von_jahr' => 1800,
      'bis_jahr' => 1918,
      'sanierungsstufe' => 'saniert',
      'u_wand' => 0.24,
      'u_dach' => 0.2,
      'u_boden' => 0.35,
      'u_fenster' => 1.1,
      'u_tuer' => 1.4,
      'quelle' => 'IWU/TABULA (Richtwerte, to_verify)',
    ),
    2 => 
    array (
      'von_jahr' => 1800,
      'bis_jahr' => 1918,
      'sanierungsstufe' => 'teilsaniert',
      'u_wand' => 0.97,
      'u_dach' => 1.4,
      'u_boden' => 0.77,
      'u_fenster' => 3.05,
      'u_tuer' => 2.2,
      'quelle' => 'IWU/TABULA (Richtwerte, to_verify)',
    ),
    3 => 
    array (
      'von_jahr' => 1919,
      'bis_jahr' => 1948,
      'sanierungsstufe' => 'unsaniert',
      'u_wand' => 1.5,
      'u_dach' => 2,
      'u_boden' => 1.2,
      'u_fenster' => 2.7,
      'u_tuer' => 3,
      'quelle' => 'IWU/TABULA (Richtwerte, to_verify)',
    ),
    4 => 
    array (
      'von_jahr' => 1919,
      'bis_jahr' => 1948,
      'sanierungsstufe' => 'saniert',
      'u_wand' => 0.24,
      'u_dach' => 0.2,
      'u_boden' => 0.35,
      'u_fenster' => 1.1,
      'u_tuer' => 1.4,
      'quelle' => 'IWU/TABULA (Richtwerte, to_verify)',
    ),
    5 => 
    array (
      'von_jahr' => 1919,
      'bis_jahr' => 1948,
      'sanierungsstufe' => 'teilsaniert',
      'u_wand' => 0.87,
      'u_dach' => 1.1,
      'u_boden' => 0.77,
      'u_fenster' => 1.9,
      'u_tuer' => 2.2,
      'quelle' => 'IWU/TABULA (Richtwerte, to_verify)',
    ),
    6 => 
    array (
      'von_jahr' => 1949,
      'bis_jahr' => 1957,
      'sanierungsstufe' => 'unsaniert',
      'u_wand' => 1.4,
      'u_dach' => 2,
      'u_boden' => 1.5,
      'u_fenster' => 2.9,
      'u_tuer' => 3,
      'quelle' => 'IWU/TABULA (Richtwerte, to_verify)',
    ),
    7 => 
    array (
      'von_jahr' => 1949,
      'bis_jahr' => 1957,
      'sanierungsstufe' => 'saniert',
      'u_wand' => 0.24,
      'u_dach' => 0.2,
      'u_boden' => 0.35,
      'u_fenster' => 1.1,
      'u_tuer' => 1.4,
      'quelle' => 'IWU/TABULA (Richtwerte, to_verify)',
    ),
    8 => 
    array (
      'von_jahr' => 1949,
      'bis_jahr' => 1957,
      'sanierungsstufe' => 'teilsaniert',
      'u_wand' => 0.82,
      'u_dach' => 1.1,
      'u_boden' => 0.93,
      'u_fenster' => 2,
      'u_tuer' => 2.2,
      'quelle' => 'IWU/TABULA (Richtwerte, to_verify)',
    ),
    9 => 
    array (
      'von_jahr' => 1958,
      'bis_jahr' => 1968,
      'sanierungsstufe' => 'unsaniert',
      'u_wand' => 1.4,
      'u_dach' => 1.4,
      'u_boden' => 1,
      'u_fenster' => 2.9,
      'u_tuer' => 3,
      'quelle' => 'IWU/TABULA (Richtwerte, to_verify)',
    ),
    10 => 
    array (
      'von_jahr' => 1958,
      'bis_jahr' => 1968,
      'sanierungsstufe' => 'saniert',
      'u_wand' => 0.24,
      'u_dach' => 0.2,
      'u_boden' => 0.35,
      'u_fenster' => 1.1,
      'u_tuer' => 1.4,
      'quelle' => 'IWU/TABULA (Richtwerte, to_verify)',
    ),
    11 => 
    array (
      'von_jahr' => 1958,
      'bis_jahr' => 1968,
      'sanierungsstufe' => 'teilsaniert',
      'u_wand' => 0.82,
      'u_dach' => 0.8,
      'u_boden' => 0.68,
      'u_fenster' => 2,
      'u_tuer' => 2.2,
      'quelle' => 'IWU/TABULA (Richtwerte, to_verify)',
    ),
    12 => 
    array (
      'von_jahr' => 1969,
      'bis_jahr' => 1978,
      'sanierungsstufe' => 'unsaniert',
      'u_wand' => 1,
      'u_dach' => 0.6,
      'u_boden' => 1,
      'u_fenster' => 2.7,
      'u_tuer' => 3,
      'quelle' => 'IWU/TABULA (Richtwerte, to_verify)',
    ),
    13 => 
    array (
      'von_jahr' => 1969,
      'bis_jahr' => 1978,
      'sanierungsstufe' => 'saniert',
      'u_wand' => 0.24,
      'u_dach' => 0.2,
      'u_boden' => 0.35,
      'u_fenster' => 1.1,
      'u_tuer' => 1.4,
      'quelle' => 'IWU/TABULA (Richtwerte, to_verify)',
    ),
    14 => 
    array (
      'von_jahr' => 1969,
      'bis_jahr' => 1978,
      'sanierungsstufe' => 'teilsaniert',
      'u_wand' => 0.62,
      'u_dach' => 0.4,
      'u_boden' => 0.68,
      'u_fenster' => 1.9,
      'u_tuer' => 2.2,
      'quelle' => 'IWU/TABULA (Richtwerte, to_verify)',
    ),
    15 => 
    array (
      'von_jahr' => 1979,
      'bis_jahr' => 1983,
      'sanierungsstufe' => 'unsaniert',
      'u_wand' => 0.8,
      'u_dach' => 0.5,
      'u_boden' => 0.8,
      'u_fenster' => 2.7,
      'u_tuer' => 2.5,
      'quelle' => 'IWU/TABULA (Richtwerte, to_verify)',
    ),
    16 => 
    array (
      'von_jahr' => 1979,
      'bis_jahr' => 1983,
      'sanierungsstufe' => 'saniert',
      'u_wand' => 0.24,
      'u_dach' => 0.2,
      'u_boden' => 0.35,
      'u_fenster' => 1.1,
      'u_tuer' => 1.4,
      'quelle' => 'IWU/TABULA (Richtwerte, to_verify)',
    ),
    17 => 
    array (
      'von_jahr' => 1979,
      'bis_jahr' => 1983,
      'sanierungsstufe' => 'teilsaniert',
      'u_wand' => 0.52,
      'u_dach' => 0.35,
      'u_boden' => 0.58,
      'u_fenster' => 1.9,
      'u_tuer' => 1.95,
      'quelle' => 'IWU/TABULA (Richtwerte, to_verify)',
    ),
    18 => 
    array (
      'von_jahr' => 1984,
      'bis_jahr' => 1994,
      'sanierungsstufe' => 'unsaniert',
      'u_wand' => 0.6,
      'u_dach' => 0.4,
      'u_boden' => 0.6,
      'u_fenster' => 2.6,
      'u_tuer' => 2.5,
      'quelle' => 'IWU/TABULA (Richtwerte, to_verify)',
    ),
    19 => 
    array (
      'von_jahr' => 1984,
      'bis_jahr' => 1994,
      'sanierungsstufe' => 'saniert',
      'u_wand' => 0.24,
      'u_dach' => 0.2,
      'u_boden' => 0.35,
      'u_fenster' => 1.1,
      'u_tuer' => 1.4,
      'quelle' => 'IWU/TABULA (Richtwerte, to_verify)',
    ),
    20 => 
    array (
      'von_jahr' => 1984,
      'bis_jahr' => 1994,
      'sanierungsstufe' => 'teilsaniert',
      'u_wand' => 0.42,
      'u_dach' => 0.3,
      'u_boden' => 0.48,
      'u_fenster' => 1.85,
      'u_tuer' => 1.95,
      'quelle' => 'IWU/TABULA (Richtwerte, to_verify)',
    ),
    21 => 
    array (
      'von_jahr' => 1995,
      'bis_jahr' => 2001,
      'sanierungsstufe' => 'unsaniert',
      'u_wand' => 0.5,
      'u_dach' => 0.3,
      'u_boden' => 0.5,
      'u_fenster' => 1.8,
      'u_tuer' => 2,
      'quelle' => 'IWU/TABULA (Richtwerte, to_verify)',
    ),
    22 => 
    array (
      'von_jahr' => 2002,
      'bis_jahr' => 2008,
      'sanierungsstufe' => 'unsaniert',
      'u_wand' => 0.4,
      'u_dach' => 0.25,
      'u_boden' => 0.4,
      'u_fenster' => 1.5,
      'u_tuer' => 1.8,
      'quelle' => 'IWU/TABULA (Richtwerte, to_verify)',
    ),
    23 => 
    array (
      'von_jahr' => 2009,
      'bis_jahr' => 2015,
      'sanierungsstufe' => 'unsaniert',
      'u_wand' => 0.28,
      'u_dach' => 0.2,
      'u_boden' => 0.35,
      'u_fenster' => 1.3,
      'u_tuer' => 1.6,
      'quelle' => 'IWU/TABULA (Richtwerte, to_verify)',
    ),
    24 => 
    array (
      'von_jahr' => 2016,
      'bis_jahr' => 2099,
      'sanierungsstufe' => 'unsaniert',
      'u_wand' => 0.2,
      'u_dach' => 0.16,
      'u_boden' => 0.3,
      'u_fenster' => 1.1,
      'u_tuer' => 1.4,
      'quelle' => 'IWU/TABULA (Richtwerte, to_verify)',
    ),
  ),
);
