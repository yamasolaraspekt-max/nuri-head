# ANSCHLUSSENTSCHEIDUNG — 22.08.2026 2026-08-22T14:03:22+0200 (Dirigent unter Vollmacht, in Yamas Namen und Auftrag: „entscheide für mich — effizient, zielführend, schnell nutzbar für Benutzer")

```yaml
grundlage: "Anschluss-Vorlage Planner docs/konzept/anschluss-entscheidung-2026-08-22.md @ rolle/planner 7b430d16 (berichtigt: beide VERWERFEN zurueckgezogen, Lesesitzung 13:49 + Planner 13:5x); GESAMTKONZEPT V3 (26a2bd62); Yama 22.08. 13:5x"
massstab_yama: "effizient · zielfuehrend · schnell nutzbar fuer Benutzer"
```

| Paket | Inhalt | Entscheidung | Reihenfolge | Warum (Massstab) |
|---|---|---|---|---|
| 3 | Pruefungen/Warnungen (3 Module, 347 Z.) | **ANSCHLIESSEN** | **1.** | kleinster Schnitt, sofort sichtbarer Nutzen (Warnung im Browser), geringstes Risiko — erster Beweis, dass der Bedienweg-Apparat (Spur W) traegt |
| 1 | Massenermittlung / Holzbau (5 Module, 705 Z.) | **ANSCHLIESSEN** | **2.** | hoechster Nutzen fuer das Geschaeft (Mengen fuer Angebote), Tests liegen; nach dem Beweis aus Paket 3 |
| 2 | Dach (7 Module, 1106 Z. — nachgezaehlt) | **AUFGETEILT** (Zimmerer-Fachlinse via Lesesitzung 14:12): **ANSCHLIESSEN jetzt:** dachProjektion.ts (43 Z., duenner Adapter auf produktives dachFlaechen()), dachOeffnung.ts (96 Z., wired) · **ZURUECKSTELLEN:** sparrenTrennung (kein Aufrufer mit realen Sparren), dachTopologie (braucht EdgeTopologyConfig je Kante = Modellerweiterung), schifterListe (spiegelt eine Sparren-Schleife, die im Renderer nicht existiert: 0 Treffer Sparren/rafter in szene.ts) · **KLAEREN als eigene Posten:** dachAusschnitt (531 Z., gegen ein fremdes Haus gebaut — RoofEngine/buildFlat/ObstacleData 0 Definitionen; rechnet in Metern, RoofAufbau in mm) und dachVorlage (zweite Wahrheit neben dachformVorlagen.ts). Beleg der Fehlerklasse: dachGeometrie.ts:137-149 (Walm-First-Klemmung +16,7 % bis +150 % Flaeche). Zimmerer-Befunde B-1..B-4 werden beim Dirigenten abgelegt (Ball Planner). | **3.** (nur die zwei Verdrahtungen) | zwei echte Verdrahtungen mit sofortigem Nutzen; fuenf Module sind keine Schritte, sondern Modell-/Renderer-Arbeit oder Klaerung — ehrlicher als "sieben in kleinen Schritten" |
| 4 | Einzelstuecke (Treppe, Raumprojektion, Heizkreis) | **PARKEN** | — | geprueft, kostet im Liegen nichts; kein Bedienweg ohne Produktfrage (Yama spaeter) |
| — | `app/tools/toolCatalogStillgelegt.ts` | **BEHALTEN** (Waechter: 1 Export, 3 Verbraucher, Test length==54) | — | Verwerfen braeche einen laufenden Test; Ort ist nicht Wirkung |
| — | `geometry/werkzeugRegistry.ts` | **STILLLEGEN nach Muster toolCatalogStillgelegt** (Fachmessung software-architekt via Lesesitzung 14:01, mess_sha 89368cdb: kein Zwilling von toolRegistry, sondern unbenutzter vierter Entwurf der Bauteil-Schicht — die lebt in domain/scene.types.ts, geometry/*, enginePanels.ts:89, faehigkeiten.ts; 0 Produktivverbraucher, registriereWerkzeug nur in Attrappen). NICHT verwerfen, NICHT anschliessen, auf KEINEN Fall mit toolRegistry zusammenfuehren. Grund steht so im Kopf. Als Kleinblatt NACH Paket 3; bis dahin Register-Zeile "stillzulegen" | — | falscher Grund im Kopf waere schaedlicher als keiner |

## Wie gebaut wird (verbindlich fuer den Zuschnitt)
1. **Ein Werkzeug = ein Blatt, Spur W** (V3-3): Bedienweg benannt (N4: toolRegistry-Kennung, Menue/Auslöser, Zielreifegrad BROWSERABGENOMMEN), ≤ 8 Kriterien inkl. Browserabnahme und Rot-Probe „ohne Werkzeug", kein Rechte/Geld/DB/Auth, Revert als Rueckweg, ein DoR-Durchgang, eine Lieferung, ≤ 15 Commits.
2. **Werkzeug-Register zuerst** (Planner gen 16/17 Posten 1): jede Kennung, die ein Blatt anschliesst, steht vorher in der Tabelle — das ist die Bruecke, die bisher fehlte.
3. **Vorrang:** Anschluss-Blaetter gehen dem alten BEREIT-Vorrat (8 Z2, A-38/39, W-04) im Bau vor — Yamas Massstab. Der BEREIT-Deckel (V3, 6) gilt fuer NEUE andere Zuschnitte, nicht fuer diese Welle.
4. **Abnahme = Browser** (Evaluator, Puppeteer/Chrome headful); Kriterien-gruen ohne Browser ist ABGENOMMEN (CODE), zaehlt nicht als Anschluss.
5. **Erledigt je Paket** (Vorlage e): Werkzeug in der Leiste, im Browser ausgeloest, Ergebnis sichtbar, Reifegrad BROWSERABGENOMMEN in STATUS.md.

## Ziel (Meilensteinplan): M2 bis 29.08. — ≥ 3 Anschlusspakete browserabgenommen (Paket 3 ganz, Paket 1 ganz, Paket 2 begonnen).
