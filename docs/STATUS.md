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
zustand: BEREIT
ballbesitz: generator
basis_sha: 16d5bbde
pruef_sha: ""
release_sha: ""
letztes_votum: "plan-pruefer 04.08. 23:5x (3. Runde): BEREIT. Die drei korrigierten Pruefbefehle selbst geprobt — die Runner-Form laeuft (decke.test.ts sauber, gleiche Form wie Planner-Messung 13/0), das blanke node --test faellt belegt. Alle 15 §5-Punkte erfuellt; A-01-2 als benannte must_preserve-Ausnahme von der Rot-Pflicht."
offene_akzeptanz:
  - "BINDENDE REIHENFOLGE fuer den Generator: Browser-Fixture (L-Dach-Dokument + scene_json im Repo) VOR dem ersten Bau-Commit — der Bau zerstoert sonst seinen eigenen Pruefstand (Blatt, Abschnitt Fixture-Weg)."
ballwechsel: "plan-pruefer -> generator, 04.08. 23:5x"
naechster_schritt: "Generator: Readiness-Bestaetigung nach §7, dann Fixture, dann Bau; Meldung CODE_FERTIG mit Basis- und Bau-SHA"
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

## In Planprüfung

```yaml
auftrag: A-02
titel: "Commit-Tor: Halter fragen statt Ruhe raten - und bei Blockade ENV_BLOCKED melden statt raeumen"
datei: docs/auftraege/aktiv/A-02-lock-halter-statt-ruhe.md
zustand: ENTWURF
ballbesitz: planner
basis_sha: 93a9691f
anlass: "P0-Vorfall 04.08. 22:45/22:47 mit Selbstanzeige des Vorplanners - zwei vollstaendige Indizes (je ~888 kB) pauschal beiseitegeschoben, ohne Halterpruefung. Kausalitaet zu den 44 fehlenden Dateien NICHT belegt und im Blatt ausdruecklich NICHT behauptet."
letztes_votum: "plan-pruefer 05.08. (1. DoR-Runde A-02): ENTWURF bleibt, ZWEI Restpunkte. GEMESSEN an HEAD 42904acb (Drift zur Basis = nur der Schnitt-Commit, 0 scripts/-Aenderungen): ||-Bedingung, :103-Kommentar, lsof nur im Kommentar, ENV_BLOCKED 0, 4 beiseite — alle Ist-Belege woertlich bestaetigt. OFFENER PUNKT 1 BEANTWORTET: A-02-1 ist an der Basis GRUEN (Zusagen-Suite selbst gefahren: 23 pass / 0 fail, die Inhalt+300s-Zusage traegt heute) — die Vermutung des Planners stimmt."
offene_akzeptanz:
  - "Rest 1: A-02-1 als must_preserve-KONTROLLE kennzeichnen und von der Rot-Pflicht ausnehmen (gemessen gruen; wie A-01-2)"
  - "Rest 2: ENV_BLOCKED-Festlegung ins Blatt — Empfehlung des Plan-Pruefers: EIGENER Exitcode 3 UND stderr-Zeile 'ENV_BLOCKED: <grund>' (Exitcode fuer Maschinen, Textparsen allein waere F-09-anfaellig; die Zeile fuer Menschen und Logs). Entscheidung ist Planner-Sache, beide Formen sind messbar."
naechster_schritt: "Planner traegt beide Restpunkte ein, dann setzt der Plan-Pruefer BEREIT (alle uebrigen §5-Punkte belegt erfuellt; Browser n.a. korrekt, Rueckweg additiv, Konflikt: keiner)"
kein_konflikt_mit_a01: "getrennte Pfade (scripts/ statt resources/planner/), kein IN_ARBEIT - A-01 behaelt den Vortritt"
```

**Warum der Planner ihn schneidet und nicht der Verursacher:** er hat es selbst abgelehnt —
*„ein Verursacher, der seine eigene Barriere schneidet, wäre genau der Interessenkonflikt, den die
Rollentrennung verhindern soll."* Er hat damit recht, und die Übergabe ist hier vermerkt, damit
sie nicht als stille Weiterreichung erscheint.

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
