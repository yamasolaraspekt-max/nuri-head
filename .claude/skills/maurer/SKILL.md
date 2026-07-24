---
name: maurer
description: Fach-Linse Maurerhandwerk/Mauerwerk für Wände und Wandanschlüsse im Hausplaner (Wanddicke, Ecken/Gehrung, Öffnungen, Verband, Anschluss an Decke/Dach). Laden bei Wand-Geometrie.
---

# maurer

## Ziel
Wände und ihre Anschlüsse im Hausplaner fachlich schlüssig halten — Ecken schließen sauber, Öffnungen sitzen
richtig, der Wandkörper stimmt mit dem Grundriss überein.

## Prüf-Linse
- **Ecke = Verband-Stoß, sauber geschlossen.** An einer Ecke teilen beide Wände den gemeinsamen Gehrungspunkt
  (`wandBaender` = eine Wahrheit); im 3D KEIN Überlappen/Klaffen (Gehrungsprisma, Ecken-Dichtheitstest).
- **Wanddicke konstant** über die Wandlänge; Länge ≥ Dicke (keine „Mini-Wand").
- **Öffnungen (Fenster/Tür)** sind wandgebunden: Sturz oben, Brüstung unten, laibungsrichtig; Segmentgrenzen
  an Öffnungen bleiben rechtwinklig (Gehrung nur an den Wand-ENDEN). Öffnung wandert/verschwindet mit der Wand.
- **Anschlüsse.** Wand trägt Decke (Deckenoberkante = Wandoberkante) und Dach (Traufe auf Wandkontur).
  Offenes Wandende ohne Nachbar → stumpfe (rechtwinklige) Endkappe, kein erfundener Miter.

## Rote Flaggen
- Zweite Miter-/Ecken-Rechnung neben `wandBaender`.
- Wandkörper als achsparallele Box an Ecken (überlappt/klafft) statt gehrtem Band.
- Tragend/nicht-tragend behauptet (Modell kennt es nicht → `statiker`-Grenze).

## Norm-Anker
Regeln des Mauerwerksbaus (Verband, Anschluss); zeichnerisch DIN 1356-1 (sauber schließende Ecke).
Darstellung, keine Statik.
