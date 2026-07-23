# Konzept — Hausplaner-Navigation (fundiert sortiert nach Abhängigkeit)

**Rolle:** Planner. **Heimat-App:** ticket. **Datum:** 2026-07-23.

## Ordnungsprinzip
Ein Planer darf erst kommen, wenn seine Eingangsgrößen aus früheren Planern vorliegen (Kette Eingang→Ausgang).
Fünf Phasen, innerhalb nach Vorrang.

## Fachlicher Kipppunkt
Heizlastrechner = Drehscheibe. Verzehrt die komplette Hülle (Wände/Dach/Fenster/Türen/Räume), liefert
raumweise + Gebäude-Heizlast — Pflicht-Eingang für FBH/Heizkörper/Wärmepumpe. Also: erst Hülle, dann
Heizlast, dann Wärmeübergabe/-erzeugung. Der heutige v9-Fahrplan hat KEINEN Heizlast-Schritt -> Lücke schliessen.

## Phasen (13 Schritte)
1 Hülle & Geometrie: (1) Grundriss -> Umriss/Wände/Räume/Geschosse · (2) Dach [braucht Umriss] -> Dachflächen/Neigung/Ausrichtung
  · (3) Fenster [Wände] -> Flächen/U-Werte · (4) Tür [Wände] -> Öffnungen. (+ Räume/Nutzung ans Ende: Normtemp/Profile.)
2 Wärmebedarf: (5) Heizlastrechner (DIN EN 12831) [ganze Hülle] -> Heizlast je Raum+gesamt, Auslegungs-VLT. Drehscheibe.
3 Wärme: (6) FBH [Heizlast/Raum] -> Verlegung/Heizkreise/VLT · (7) Heizkörper [Heizlast/Raum] -> Typ/Leistung
  · (8) Wärmepumpe [Gebäude-Heizlast + niedrigste VLT + PV/HEMS] -> Leistung/JAZ/Speicher. (Verteiler folgt aus 6+7.)
4 Ausbau: (9) Bad [Raum] -> Sanitär/Warmwasser/Abwasser-Gefälle · (10) Küche [Raum] -> Geräte/Arbeitsdreieck/Anschlüsse.
5 Technik & Energie: (11) Elektro [Räume+Bad/Küche+WP+PV] -> Stromkreise/Verteiler · (12) TGA [Heizung+Sanitär+Lüftung]
  -> Routing/Schächte (Koordinationsebene, kein Peer) · (13) PV [Dachflächen+Elektro+WP/HEMS] -> Module/kWp/Ertrag.

## Projektmodi (Rahmen, kein Schritt)
Sanierungsplan (Bestand aufnehmen -> Ist-Heizlast -> Maßnahmen Vorher/Nachher) und Hausplaner-Neubau =
zwei Einstiege in dieselbe Kette. Heizlastrechner = zusätzlich eigenständiges Schnellwerkzeug.

## Abbildung in v9
Zwei Sichten, EINE Registry: (a) Geführter Fahrplan = die 13 Schritte in dieser Reihenfolge (Heizlast NEU
zwischen Hülle und Wärme). (b) Fachplaner-Hubs nach Gewerk: Hülle · Wärme · Ausbau · Technik&Energie.
Jeder Registry-Eintrag trägt Phase/Gewerk/Eingang/Ausgang/Zustand -> daraus später Freigabe-Logik
(Schritt startbar, wenn Eingänge grün).

## Folge-Aufträge (Planner-Ball -> Generator)
1. Registry-Felder Phase/Gewerk/Eingang/Ausgang additiv zum toolCatalog.
2. Fahrplan v9 (STEPS in studioDaten.ts) um Heizlast-Schritt ergänzen; Wärme-Schritte aus Heizlast ableiten
   statt unter TGA verstecken.
3. Fachplaner-Hubs nach Phase gruppieren (Hülle/Wärme/Ausbau/Technik&Energie) -> v9-Navi-Auftrag Batch 0.
