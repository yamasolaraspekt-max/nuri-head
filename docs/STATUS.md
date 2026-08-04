# STATUS — der eine gültige Arbeitsstand

**Autorität:** [`docs/ARBEITSREGELN.md`](ARBEITSREGELN.md) §16. Diese Seite wird **überschrieben,
nicht angehängt**. Es gibt keine zweite manuelle Statuswahrheit; historische Ledgers und
Statusseiten werden nicht fortgeschrieben, um den aktuellen Zustand zu bestimmen.

**Angelegt:** 04.08.2026 durch den Planner als Teil des Übergangs nach §17.

---

## Aktiver Auftrag

```yaml
auftrag: A-01
titel: "Dach aus Kontur - nicht-rechteckige Kontur bekommt eine lesbare Absage"
datei: docs/auftraege/aktiv/A-01-dach-aus-kontur.md
zustand: ENTWURF
ballbesitz: planner
basis_sha: 16d5bbde
pruef_sha: ""
release_sha: ""
letztes_votum: "plan-pruefer 04.08.: DoR §5 geprueft, ENTWURF bleibt. Bestaetigt: Basis gueltig (Insel-Drift 0), Ist-Beleg wortgleich, A-01-1/-3/-4/-5 wirksam rot, Z-07-Code bleibt als Ist-Zustand. A-01-6 GEGENGEPRUEFT und angenommen (zwei unabhaengige Messungen: Evaluator 3545321a + Planner-Tabelle; Kante-1-Logik ist flaechenbasiert und damit zwischenpunkt-unempfindlich, am Code gelesen). Vier Nachtraege offen, dann BEREIT."
offene_akzeptanz:
  - "Nachtrag 2: A-01-2 ist an der Basis bereits GRUEN (Z-07-Bau) — ausdruecklich als KONTROLLE/must_preserve kennzeichnen und von der Zeile 'jedes P1 wirksam rot' ausnehmen, sonst widerspricht das Blatt sich selbst"
  - "Nachtrag 3: A-01-4 Fixture-Weg benennen — __tests__/fixtures/ existiert nicht (gemessen); Vorschlag: v3-Szenen-JSON mit L-Dach + Seed-Weg fuer objekt.blade in ticket_testing"
  - "Nachtrag 4: Pruefbefehle/Testnamen fuer A-01-1..5 (A-01-6 hat einen)"
  - "Nachtrag 5: benanntes Test-Objekt + Login-Weg fuer die Browserabnahme (§5 Testdaten)"
naechster_schritt: "Planner traegt die vier Nachtraege ein, dann erneute DoR-Pruefung -> BEREIT"
geschlossen_seit_anlage:
  - "Ort/Wortlaut der Absage: die WELCHE-Frage ist entschieden (dachFlaechen wird gefragt, kein zweiter Rechtecks-Begriff), messbar als A-01-6. Der WORTLAUT bleibt bewusst offen - er gehört in die Browserabnahme (§8)."
  - "Doppelführung Z-07 / A-01: A-01 führt. §16 kennt nur eine Statuswahrheit, und das ist diese Seite."
```

`IN_ARBEIT` ist derzeit **kein** Auftrag. Nach §3 darf es höchstens einen geben.

---

## Was aus dem Bestand übernommen wurde — und was nicht

Nach §17 werden alte Statuswerte **nicht** automatisch übernommen. Der fachliche Code bleibt, die
Prozessstände sind neu einzuordnen.

| Vorlauf | fachlicher Stand im Zweig | Prozessstand nach §17 |
|---|---|---|
| Z-07 Dach | Code liegt im Zweig (`herkunftFuerNeuesDach`, 2 Stellen) | **wird A-01**, neu geschnitten — alter P1 war unerfüllbar (SPEC) |
| Z-06 / N1 Herkunft und Freigabe | gebaut, Insel- und Servertests grün | fachlich belegt, **keine Prozessautorität** aus der alten Abnahme |
| N2 Kennzeichnung | nicht gebaut | wartet, bis A-01 abgenommen ist (§3: nur ein aktiver Auftrag) |
| N3 Bestätigen/Zurücksetzen | nicht gebaut; Server-Kette am 04.08. ergänzt (`16d5bbde`) | wartet |
| Z-11 Touch und Stift | nicht gebaut | wartet |
| W-05 Werkzeugleiste | Code liegt im Zweig, Browserabnahme **offen** | wartet; ohne Browserabnahme nach §9 nicht abnehmbar |

---

## Grenzen, die unabhängig vom Prozess gelten

- Kein Push, kein Merge nach `main`, kein Tag, kein Deploy ohne Yamas ausdrückliche Freigabe (§14).
- Tests nur gegen benannte Testdatenbanken, niemals gegen Produktivdaten (§15).
- Generator und Evaluator teilen keine Datenbank und keinen Arbeitsbaum (§6).
