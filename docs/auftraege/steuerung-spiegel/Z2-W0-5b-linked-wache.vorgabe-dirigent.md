# Z2-W0-5b — `PlannerMasterSetController@linked` ohne Wache (Vorgabe Dirigent, 2026-08-22T13:37:19+0200; Quelle: Evaluator-Votum Z2-W0-5 639a7a32, Nebenbefund; Yama 13:3x)

```yaml
status: "VORGABE fuer den Planner (Kleinblatt), NACH KONZEPT-planner-anschluss (gen 15); Z2-Folge, kein Regelbau"
befund: "GET /api/planner/items/{plannerItem}/master-sets (PlannerMasterSetController@linked) traegt keine Zustaendigkeits-/Rechtepruefung: bei GESCHLOSSENEM Rechte-Schalter HTTP 200 mit den Master-Sets eines FREMDEN Items (selbst ausgeloest, Evaluator). Preisgegeben: die Zuordnung 'fremdes Item -> diese Master-Sets'. Die W0-5-Matrix nannte fuer C nur link/unlink/addToPlan — deshalb kein Mangel an W0-5, sondern Folgeposten (Evaluator hat richtig kein Kriterium nachgeschrieben)."
einordnung: "Integritaets-/Auth-Luecke (Kategorie 2 der Regel RECHTE_ALLE_FUER_ALLE, Yama 21.08.: bleibt Befund, auch bei Schalter true). Der Endpunkt FOLGT dem Schalter nicht, er umgeht ihn."
blatt_mindestens: "linked an denselben Baustein binden wie link/unlink/addToPlan (W0-5 Kriterium E: 'ein Baustein, vier Aufrufer' -> fuenf); Rot (fremd 200) -> Gruen (fremd 403/404, eigen 200) bei BEIDEN Schalterstellungen; Vertragstest erweitert; keine weitere Route gleicher Klasse offen (Grundmenge: alle api/planner/* GET-Routen mit {plannerItem}, STAND-SHA)"
reihenfolge: "Planner (nach gen 15) -> Plan-Pruefer -> Generator -> Evaluator -> Integrator"
```
