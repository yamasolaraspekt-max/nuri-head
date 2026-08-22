# ARBEITSREGELN — NACHTRAG 1.6 „Spur V · Verdrahtung" (in Kraft, Dirigent in Yamas Namen, 22.08.2026 18:3x)

```yaml
rang: "Rang 1 — Nachtrag zu Fassung 1.4.2 + Nachtrag 1.5; gilt ab sofort fuer alle neuen Anschluss-Blaetter"
grundlage: "Yama 22.08. 18:2x ('ich moechte das schnell geloest haben, es dauert ewig' / 'du loest sofort das problem, die ursache'); Messung der Lesesitzung 6b369768 am Stand fdbaeced: 451 Commits heute, 3 am Produkt, Apparat:Produkt ~110:1, erste Produktaenderung 15:24 nach sieben Stunden; Ursache = Zahl der Durchlaeufe (fuenf Rollenpaesse je Modul + Berichtigungen), NICHT Kriterienzahl oder Sorgfalt"
nicht_geaendert: "Rollentrennung · Beleg statt Behauptung · Rot-Probe und Browserlauf (V-2/V-3 haben heute echte Fehler gefangen) · Tor/Pull/Lease · Yamas Freigaberecht"
ersetzt: "Bündel-Regel 16:02 fuer Spur-V-Blaetter (V-5: Buendel ist Teil der Lieferung) · 'ein Werkzeug = ein Blatt' (gen 19 Posten 4) wird durch 'ein Sammelblatt je Klasse, Registerzeile je Modul' ersetzt"
```

## Spur V — Verdrahtung eines geprueften Moduls an einen vorhandenen Aufrufer
Eigene Risikoklasse: Fachlogik getestet und unberuehrt, neu ist nur der WEG. **Fester Kriterientext, je Blatt nur die Belege:**

| Nr | Kriterium |
|---|---|
| V-1 | Aufrufer im Produktivpfad 0 → ≥ 1; Komponente/Aufrufstelle mit Pfad benannt |
| V-2 | Wirkung im Browser ausgeloest; Eingabewert und Ergebnis woertlich genannt |
| V-3 | Rot-Probe: derselbe Bedienweg gegen den Stand OHNE das Modul → Wirkung fehlt; Ortsbeleg |
| V-4 | Fachlogik unberuehrt: `git diff` auf das Modul → leer |
| V-5 | Insel-Suite gruen, `tsc` 0, Buendel gebaut und mitcommittet |
| V-6 | Kein Produktcode ausserhalb `resources/planner/hausplaner` |

Der Plan-Pruefer erteilt die DoR **einmal auf den Kriterientext** (nicht je Blatt); je Blatt prueft er nur Vollstaendigkeit der Belege (Modul, Aufrufer, Bedienweg N4, Registerzeile). Faellt V-4 (Fachlogik muss angefasst werden) → kein Spur V, sondern Spur A/W mit eigenem Blatt.

## Sammelblatt je Klasse
Strukturgleiche Module einer Klasse stehen in EINEM Blatt: ein Zuschnitt, eine DoR, ein Bauauftrag, eine Abnahme (je Modul eine Belegzeile gegen V-1..V-6), ein Transport. Ein Modul mit eigenem Blocker faellt heraus und bekommt ein eigenes Blatt (wie Z1-W2-2). Das Sammelblatt verbirgt nie einen Blocker. Das Werkzeug-Register bleibt die Bruecke Modul ↔ Werkzeug ↔ Reifegrad.

## Produkt vor Apparat
Pro Tag mindestens EIN angeschlossenes Modul (ABGENOMMEN (BROWSER) + transportiert), bevor neue Steuerungsauftraege geschnitten werden. Ausgenommen: Sicherheitsbefunde und alles, was einen laufenden Bau blockiert. Gilt auch fuer den Dirigenten (Protokoll/Spiegel hoechstens stuendlich, Entscheidungen gesammelt in einer Datei).

## Lagebericht-Zusatz
Jeder Lagebericht nennt: Commits Produkt / Commits Apparat / Zeit bis zur ersten Produktaenderung / Module heute angeschlossen (BROWSER) / Werkzeugleiste-Eintraege (Quelle `toolRegistry.ts`, heute 13, Stand 15.08.).
