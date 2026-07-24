---
name: zimmermannmeister
description: Fach-Linse Zimmererhandwerk/Holzbau für Dachstuhl und Holzbauteile im Hausplaner (Sparren, Pfetten, Kehl-/Gratsparren, Firstlinie, Anschlüsse). Laden bei Dachstuhl-/Holzbau-Geometrie.
---

# zimmermannmeister

## Ziel
Den Dachstuhl/Holzbau im Hausplaner geometrisch schlüssig halten — Sparren, Pfetten und Verschneidungshölzer
treffen sich an gemeinsamen Punkten, wie es die Zimmerei baut (kein Klaffen/Überlappen).

## Prüf-Linse
- **Sparrenlage.** Sparrenhöhe (Konstruktions-Konstante, cm) wirkt auf First-/Traufhöhe; Sparren senkrecht
  zur Traufe, Firstpfette oben. Regelmäßiger Abstand ist Darstellung, kein Statiknachweis.
- **Kehl-/Gratsparren.** An der Verschneidung endet der Kehl-/Gratsparren am gemeinsamen First-/Kehlpunkt
  (Engine: `pStartL/pStartR → pEndPeak`). Sparren-Mittellinie MIT `hRafterCenter`, Deckungskehle OHNE —
  beide Varianten nicht verwechseln.
- **Pfetten (Fuß/First/Anbau)** liegen auf ihren Auflagerlinien; Anbaufirst trifft die Hauptfläche bei
  yRidgeExt < yRidge_main (schmälerer Anbau).
- **Anschlüsse dicht.** Zwei stoßende Hölzer teilen den Punkt — analog dem Wand-Gehrungsprinzip
  (`wandBaender`): eine Ecken-Wahrheit, kein zweiter Rechenweg.

## Rote Flaggen
- Holz-Geometrie neu erfunden statt aus der byte-treuen Engine gespiegelt.
- Tragfähigkeits-/Querschnitts-Aussage aus dem Modell (kein Tragend-Flag vorhanden → nur Geometrie).
- Kehlsparren, der in den offenen Innenhof zeigt (falsche Orientierung).

## Grenze
Reine Lage/Höhe/Länge — die Bemessung (Querschnitt, Nachweis) ist `statiker`-Sache und außerhalb dieses Skills.
