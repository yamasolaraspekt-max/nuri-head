# VOTUM Z1-W2-5 — Nachprüfung nach der Fixture-Nachlieferung

**ABGENOMMEN (BROWSER) MIT VORBEHALT — sieben von sieben. Mein NACHBESSERN ist erledigt.**

| Feld | Wert |
|---|---|
| Nachlieferung | `a0b61ba4` (Fixture `wand-schichten` + Bündel) · Bau `5617dc4c` |
| Mein Stand | `57e661bd` |
| Vorheriges Votum | `7a5ba2d0` — NACHBESSERN, 6 von 7, `-b` unbelegt |
| gelesen_bis | 2026-08-22T20:05:07+02:00 |
| Bühne | Port 8104, Chrome **headful**, DB am Kindprozess `ticket_testing`, DB-Lease Token 27 |
| Vorbehalt | *b per Prüfmittel; Schichten-Erzeugungsweg fehlt (AUF-76)* — Wortlaut des Dirigenten |

## Der eine offene Punkt ist geschlossen

**Z1-W2-5-b — ERFÜLLT.** Zwei Läufe an derselben Wand der Fixture `?fixture=wand-schichten`,
wörtlich abgelesen:

```
roh      Brutto 28,00 m² · Öffnungen −0,00 m² · Netto 28,00 m² · 6,720 m³
fertig   Brutto 28,00 m² · Öffnungen −0,00 m² · Netto 28,00 m² · 5,600 m³
```

**A ≠ B, beziffert: 1,120 m³.** Und die Zahl geht in der Nachrechnung auf, die ich **vor** dem
Lauf aufgestellt hatte: Wand 10,0 × 2,8 m, Dicke 240 mm → 6,720 m³; die Fixture legt zwei
Schichten an (`putz-innen` 15 mm + `putz-aussen` 25 mm = 40 mm), also 200 mm → 5,600 m³;
Differenz **1,120 m³**. Genau der gemessene Wert.

**Dass die Fläche gleich bleibt, ist richtig und kein Mangel:** Schichten wirken auf die *Dicke*,
die Fläche ist Länge × Höhe. Eine Anzeige, bei der sich hier die Quadratmeter änderten, wäre
falsch — das steht so auch im Modul.

**Ein feiner Zusatzbeleg, den ich nicht erwartet hatte:** Im Vorbehaltstext ist bei `fertig` die
Zeile *„dicke: keine Schichten am Knoten hinterlegt (AUF-76)"* **verschwunden** — sie stand dort
noch bei meiner ersten Abnahme an der schichtenlosen Wand. Übrig bleiben nur `laenge`
(Wandverbund) und `hoehe` (Operanden-Gate). Die Anzeige unterscheidet also, *warum* sie
vorbehält, statt einen festen Satz zu zeigen.

Bildbelege: `belege/Z1-W2-5-b-schichten-roh.png`, `belege/Z1-W2-5-b-schichten-fertig.png`.

## Was unverändert gilt

Die sechs übrigen Kriterien bleiben, wie in `7a5ba2d0` belegt — **am Bestand**, nicht am
Prüfmittel: a (drei Flächen am Objekt, über alle acht Wände nachgerechnet), c (Meldungsfall über
den echten Bedienweg, ohne Zahl daneben), d (Rot-Probe aus `171284e9`), e (0 Pfade außerhalb),
f (headful, mit Ort), g (`wandFlaeche.ts` unverändert).

Die Nachlieferung selbst berührt außerhalb Insel + Bündel **0 Dateien**.

## Der Vorbehalt, und warum er bleibt

`b` ist **per Prüfmittel** belegt, nicht durch Bedienung: einen Wandaufbau kann im Bestand
niemand erzeugen (`AUF-76`). Das habe ich in `7a5ba2d0` gemessen und es gilt weiter — die Fixture
ändert daran nichts, sie macht die Rechnung nur *sichtbar*. Der Unterschied zu Z1-W2-6: **das
Werkzeug selbst ist bedienbar** — die Auswahl `roh`/`fertig` steht im Panel und wirkt.

**Ball:** Integrator (Transport). Damit ist Z1-W2-5 abgeschlossen.
