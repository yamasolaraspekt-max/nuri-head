# Entscheidungen (wberechnung → ticket — B2a/B2b Cut-over)

> Genehmigte Bau-Entscheidungen, die den ursprünglichen Plan präzisieren oder erweitern.
> Grundsatz-Weichen stehen im jeweiligen Pflicht-Stopp; hier die im Bau genehmigten Abweichungen.

## B2b-C — Klima/WW-Adapter (2026-07-05)

- **6 statt 4 Kern-Klassen portiert (Dependency-Folge):** Der Plan nannte KlimaPlz/Höhe/WW/KlimaBin (4).
  Der byte-Port zog zwei transitive Abhängigkeiten mit: **`HeizlastKonstanten`** (WarmwasserService nutzt
  `WW_KW_PRO_PERSON` / `wwKwhPa`) und **`HeizlastEingabe`** (DTO-Eingabe von WarmwasserService). Alle sechs
  byte-genau (Diff=0), keine Logik-Änderung. *Genehmigt (Yama, B2b-C-Freigabe).*
- **Registry-Key `personen_im_haushalt`** (int, kein `wert_num_pflicht`): neu, weil WarmwasserService die
  Personenzahl als Eingabe für den WW-Bedarf braucht (`phi_ww_kw = personen × 0,20 kW`). *Genehmigt.*
