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
  - "ALLE VIER NACHTRAEGE EINGETRAGEN 04.08. 23:4x (Planner). Erneute DoR-Pruefung steht aus."
naechster_schritt: "Plan-Pruefer prueft die vier Nachtraege und setzt BEREIT oder gibt SPEC_BLOCKED zurueck"
nachtraege_erledigt:
  - "N2 A-01-2 ist jetzt ausdruecklich must_preserve-KONTROLLE und von der Rot-Pflicht AUSGENOMMEN. Begruendung im Blatt: ohne das Kriterium waere 'gar kein Dach mehr' eine gruene Loesung."
  - "N3 Fixture-Weg steht (Abschnitt 'Fixture-Weg fuer A-01-4', 23:3x): Testebene nutzt das vorhandene insert()-Muster der vier Hausplaner-Featuretests, KEIN neuer Seeder. Browserebene erzeugt das Dokument VOR dem Bau. Die REIHENFOLGE ist Teil des Auftrags."
  - "N4 Pruefbefehl und Testname je Kriterium A-01-1..6 eingetragen; A-01-3 ausdruecklich als Browser-Nachweis ohne Unit-Befehl gekennzeichnet (ein console.error erfuellt es NICHT)."
  - "N5 Flaeche objekt.blade (traegt data-speichern-url:157, studio speichert nicht), Rolle is_admin ueber User::factory, Viewports 1440/1024/375. Das Test-OBJEKT wird bewusst NICHT festgeschrieben - der Bauende legt eines an und nennt die id im Bericht."
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
