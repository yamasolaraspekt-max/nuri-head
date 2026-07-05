# Navigation: W-Berechnung / Energie-Inventur

Stand: 2026-07-04. Scope: aktuelle ticket-Navi gegen importierte bzw. vorbereitete W-Berechnung/Energie-Funktionen prüfen, Duplikate vermeiden, sichtbare Punkte fachlich sauber einordnen.

## Kurzurteil

Die W-Berechnung darf nicht als Sammlung einzelner "Tools" in der Sidebar erscheinen. Das erzeugt doppelte Begriffe und macht Prototypen groesser, als sie fachlich heute sind. Sichtbare Energie-Funktionen werden deshalb unter einem Bereich gebuendelt: **Energie**.

## Ist-Befund

| Funktion | Vorheriger Ort | Befund |
|---|---|---|
| PVGIS | Planung | nutzbarer Energie-/Auslegungsbaustein |
| Heizkoerper-Konfigurator | Artikel | fachlich Auslegung, nicht Artikelstamm |
| Wirtschaftlichkeit | Route vorhanden, nicht in Sidebar | sinnvoller Energie-/Vertriebsnutzen, aber Controller-Import musste gehaertet werden |
| Foerderungen | Planung mit Finance-Gate | fachlich Energie/BEG-nah, Gate bleibt erhalten |
| Werkzeuge-Uebersicht | Planung | alter Prototyp/Belegungstool, nicht als W-Berechnung-Einstieg geeignet |

## Duplikat-Regeln

- Kein Bereich **Tools** fuer W-Berechnung.
- Kein Menuepunkt **wberechnung**.
- Kein paralleler Heizkoerper-Link unter Artikel und Energie.
- Kein sichtbarer Platzhalter fuer Heizlast, WP-Auslegung oder WR-Auslegung, solange Route, Controller, View und Tests nicht fertig sind.
- Artikel bleibt fuer Katalog/Stammdaten; Auslegung bleibt bei Energie.

## Umgesetzter Ziel-Schnitt

Bereich **Energie**:

- PVGIS
- Heizkoerper-Check
- Wirtschaftlichkeit
- Foerderungen

Aus der Hauptnavigation entfernt:

- Werkzeuge-Uebersicht

Nur verschoben, nicht geloescht:

- Heizkoerper-Konfigurator von Artikel nach Energie, sichtbar als **Heizkoerper-Check**.

## Spaetere Erweiterung

Erst nach technischer Fertigstellung sichtbar machen:

- Heizlast
- WP-Auslegung
- WR-Auslegung
- Sanierung
- Grundriss/Plan-Import

Diese Punkte duerfen vorher hoechstens in Dokumentation oder internen Roadmaps stehen, nicht in der produktiven Sidebar.
