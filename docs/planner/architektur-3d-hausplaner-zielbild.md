# Architektur-Zielbild: EIN 3D-Hausplaner als führendes Gebäudemodell
Planner-Dokument (2026-07-19, auf Yamas Richtungsentscheid: Grundriss-Editor + Hausplaner +
PV-Dach zusammenführen; Heizlast/TGA/Stromlaufplan als Ableitungen). Kein Code — Spezifikation.

## Leitsatz (eine Wahrheit)
Das GEBÄUDEMODELL ist die SceneDocument-Szene am Objekt (hausplaner_documents an
LeadAlternativeAdd, mm-Integer, Revision + Snapshots). Alles andere ist entweder eine
SICHT darauf (Editoren) oder eine ABLEITUNG daraus (Fachplanungen). Keine Fachplanung
hält eine eigene Kopie der Geometrie — sie hält nur ihre FACHDATEN + einen Verweis
(Szenen-Hash) auf den Geometrie-Stand, aus dem sie abgeleitet wurde.

## Schichtenmodell
S0 KERN        SceneDocument (levels, nodes: wall/window/door, roofs) — versioniert, eine Wahrheit.
S1 EDITOREN    Sichten auf S0: 2D (Konva) + 3D (Three) + Dach-Werkzeug = der Hausplaner.
               Plan-Import wird UNTERLAGE-LAYER im 2D (Bild unterlegen, nachzeichnen) —
               keine eigene Zeichenfläche.
S2 PROJEKTION  SzeneProjektionService (EXISTIERT, getestet): Szene → Raumerkennung →
               RaumGeometrie-Format (polygon, wand_segmente, oeffnungen), TopologieGate.
               Die EINZIGE Brücke von Geometrie zu Fachwelt. Neue Fachplanungen docken
               HIER an, nie direkt am scene_json.
S3 FACHPLANUNGEN (je: Ableitung lesen + EIGENE Fachdaten + eigener Pflichtschritt)
   a) Heizlast:   UebernehmeSzeneInAuslegung (EXISTIERT, getestet) → gebaeude_geometrie
                  via bestehendem Versionspfad; U-Werte/Konstruktionen = nachgelagerter
                  Pflichtschritt (u_strategie C, 'unbelegt' — nie erfundene Werte).
                  Rechner/WR/WP/Sanierung bleiben unangetastet (gleiche Datenbasis).
   b) PV-Dach:    Dachflächen aus roofs + Sperrzonen (Grate/Kehlen) + Reihenabstände →
                  Belegungslayer. Fachwissen liegt konserviert in
                  docs/planner/pv-belegung-referenz/ (zuerst deren REFERENZTESTS
                  transplantieren, dann Logik). Geometrie-Änderung invalidiert Belegung.
   c) TGA/Strom:  gleiches Muster (Räume/Wände als Träger, eigene Layer + Symbole,
                  eigene Persistenz-Scheibe). Eigenes Konzept, NACH a+b.
S4 AUSGABEN    Materiallisten, Angebots-Operanden, Pläne/PDF — lesen S3, nie S0 direkt.

## Warum die heutige Trennung (ehrlich) und warum sie endet
Historisch: drei getrennte playground-Transplantate; die Heizlast-Kette (Plan-Import →
Grundriss-Editor → Heizlast) rechnet LIVE und wurde bewusst nicht an das junge Modell
gekoppelt. Der Zusammenführungspfad ist aber längst begonnen (S2+S3a gebaut, geprüft,
unverdrahtet) — es fehlt der Auslöser und die Parität. Der Grundriss-Editor ist damit
ein AUSLAUFMODELL mit klarem Abrissdatum: sobald der Hausplaner denselben Grundriss
liefert und die Heizlast-Zahl im Referenzfall GLEICH ist.

## Wellenplan (jede Welle: Planner→Generator→Evaluator, eigener Commit)
W-A  P2-2b Übernehmen-Knopf: Rechte-Gate, Staleness-Anzeige (Szenen-Hash vs. aktive
     Version), expliziter Nutzer-Auslöser im Hausplaner. KLEIN — wartet nur auf Go.
W-B  U-Werte-Pflichtschritt (Heizlast-Heimat): u_strategie-C-Auflösung im Rechner/
     Adapter — betrifft auch die bestehende Grundriss-Linie (eigener Posten, so im
     Code bereits vermerkt).
W-C  Plan-Import als Unterlage-Layer im Hausplaner-2D (Bild skalieren/drehen/pinnen).
     Danach fällt der Navi-Punkt Plan-Import.
W-D  PARITÄT + ABRISS Grundriss-Editor: Referenzfall (identischer Grundriss in beiden
     Editoren → identische gebaeude_geometrie → identische Heizlast) als Test; dann
     Abriss nach Dachplaner-Muster (Redirect, Rückfallpfad, Konserve). Navi: Tools
     = nur noch 'Hausplaner'.
W-E  PV-Dachbelegung als Layer (Konserve: erst Referenztests, dann dachformVorlagen-
     Logik; walm-l/walm-t schließen die Dachform-Lücke des neuen Bundles).
W-F  TGA/Stromlaufplan: eigenes Konzeptpapier, erst nach W-D (stabile Raum-Wahrheit).

## Bauordnungs-Anker
Additive DB (Live, ~3000 Kunden); jede Welle einzeln abgenommen (Gegen-Beweis-fähig:
Paritäts-Referenzfälle); Abriss NIE ohne Redirect + geprobten Rückfallpfad + Konserve;
kein Fachmodul liest scene_json direkt (nur über S2); Navi zeigt nur Anfahrbares
(Roadmap-Posten leben in docs/planner/navi-roadmap-geplante-punkte.md).
