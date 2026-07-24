---
name: statiker
description: Fach-Linse Tragwerksplanung für tragende Bauteile im Hausplaner (Wände, Stützen, Unterzüge, Träger, Decken, Fundament). Laden bei Tragwerk-Slices — trennt Geometrie (jetzt) von Bemessung (Fach-Freigabe/später).
---

# statiker

## Ziel
Bei Tragwerk-Themen die Grenze sauber ziehen: der Hausplaner stellt **Geometrie** dar; eine **statische
Bemessung** (Nachweis, Querschnitt, Traglast) ist eine Fach-/Rechtsentscheidung — nie aus der Darstellung erfunden.

## Prüf-Linse
- **Modell kennt kein Tragend-Flag.** Wände/Stützen/Träger sind reine Geometrie — KEINE Statik-Aussage
  ableiten. Eine „tragende Wand" ist im Modell nicht markiert; nicht so tun als ob.
- **Lastpfad-Plausibilität (nur qualitativ, als Darstellungs-Check).** Decke liegt auf Wänden/Unterzügen auf;
  Unterzug/Träger überbrückt eine Öffnung/Spannweite; Stütze steht auf Fundament. Frei schwebende tragende
  Teile = Darstellungsfehler, nicht „statisch ok".
- **Operanden-Gate strikt.** Spannweite, Querschnitt, Material, Last: fehlt ein Operand → fragen/markieren,
  NICHT rechnen. Kein Bemessungsergebnis ohne Fach-Freigabe.
- **Höhen-/Auflager-Konsistenz.** Deckenoberkante = Wandoberkante; nächste Etage = elevation + Wandhöhe +
  Deckendicke (eine Ableitung). Kein zweiter Höhenrechenweg.

## Rote Flaggen
- Zahl mit Einheit (kN, cm² Bewehrung, zulässige Spannweite) aus dem Planer ohne Nachweis/Freigabe.
- Tragwerk-Slice ohne **Tor-1-Fach-Freigabe** gebaut.
- „hält schon" ohne Beleg.

## Grenze / Übergabe
Bemessung/Nachweis ist außerhalb des Hausplaners (separater Statik-Nachweis, Yama/Fachplaner). Dieser Skill
sichert nur, dass die **Geometrie** einen plausiblen Lastpfad zeigt und keine statische Scheinsicherheit entsteht.
