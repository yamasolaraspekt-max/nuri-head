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

## B2b-A — UWert-Adapter (2026-07-05)

- **Abweichung von Stopp-1-Weiche A1 (genehmigt):** A1 nannte „UWertService + 3 Models". Real sind es **4 Models
  + KonstruktionTyp-Enum** — `FensterSpec` kommt als Diff=0-Import-Pflicht hinzu (UWertService `use`t + type-hint't es
  in `ausFensterSpec()`). `FensterSpec` ist eine **tote Referenz ohne Tabelle** (kein aktiver Pfad instanziiert sie);
  **fenster_specs-Migration/Seeder verworfen** → **kein Tag-X-Posten** ausstehend. Guard-Test belegt die Nicht-Berührung.
- **byte-Port:** `UWertService` + 4 Models (`Baualtersklasse`/`Material`/`Konstruktion`/`FensterSpec`) +
  `KonstruktionTyp`-Enum, alle Diff=0. Nutzt die B2a-1-Tabellen (baualtersklassen/materials/konstruktionen).
- **fenster_specs KORREKTUR (Prämisse revidiert):** Zwei Scope-Weichen („fenster_specs mit" + „Entkopplung")
  wurden auf der Annahme gestellt, `fenster()` nutze `FensterSpec`. Tatsächlich nutzt `fenster()` die
  **Konstante `FENSTER_U`** (`'3fach'=>0.8`, pure); `FensterSpec` wird **nur** in der Zusatzmethode
  `ausFensterSpec()` gebraucht, die **nicht** in den 6 Ankern liegt. → **fenster_specs-Tabelle + Seeder sind
  unnötig** und wurden verworfen; nur das `FensterSpec`-Model bleibt (byte-genau, für den Type-Hint in
  `ausFensterSpec`; die `artikel()`-Relation ist tot/ungenutzt). Kein neuer Migrations-/Seeder-Posten. *Ehrlich korrigiert.*
- **Anker:** 6 harte über UWertService — ausSchichten Ziegel **1,45** / gedämmt **0,28**, fenster **0,8**/einfachglas **5,0**,
  ausBaualter(2020,fenster) **1,10**, + Adapter-Integration (baujahr→u_wert, `datenlage='tabula_richtwert'`). Zähler **14/28**.
