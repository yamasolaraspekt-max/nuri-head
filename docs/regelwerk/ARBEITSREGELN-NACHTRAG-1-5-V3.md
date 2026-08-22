# ARBEITSREGELN — NACHTRAG 1.5 „Bedienweg zuerst" (in Kraft, Dirigent in Yamas Namen, 22.08.2026 14:2x)

```yaml
rang: "Rang 1 — Nachtrag zu docs/ARBEITSREGELN.md Fassung 1.4.2; gilt ab sofort fuer neue Blaetter und neue Meldungen; Einarbeitung in den Haupttext durch den Planner (Pfad ausserhalb des Dirigenten-Bereichs), bis dahin ist dieses Blatt die Quelle"
grundlage: "Yama 22.08. 13:3x ('Gesamtkonzept nach der Bewertung umstellen, damit diese Schwaechen endgueltig erledigt sind') und 14:1x (Delegation: Anweisungen/Fragen an Yama uebernehmen und ausfuehren); Konzept docs/konzept/gesamtkonzept-v3-bedienweg-zuerst.md (26a2bd62), Meilensteinplan docs/konzept/meilensteinplan-v3.md"
nicht_geaendert: "Rollentrennung (niemand nimmt eigene Arbeit ab) · Beleg statt Behauptung · Spur A im Zweifel · Pull/ACK/Lease/Tor (A-37, A-43) · Yamas Freigaberecht fuer Produktion/main · Schutzgrenzen CLAUDE.md · Maximum vier offene Regelbauten"
```

## N4 · Bedienweg-Zeile (Pflichtteil der BEREIT-Liste, neben N3)
Jedes Produkt-Blatt nennt vor dem DoR **entweder** den Bedienweg — `toolRegistry`-Kennung oder tragendes Werkzeug, Menü/Auslöser
im Browser, Zielreifegrad `BROWSERABGENOMMEN` — **oder** ausdrücklich `bedienweg: keiner — Anschluss über <Kennung>`. Für
Prüfungen/Warnungen gilt als Bedienweg „Meldung erscheint am Objekt bzw. im Statusbereich, ausgelöst durch die Bearbeitung; Ort
benannt". Ein Blatt ohne N4 ist nicht BEREIT; der Plan-Prüfer prüft N4 wie N3. Die Brücke ist das **Werkzeug-Register**
(`docs/konzept/werkzeug-register.md` → Regelwerk): Registerzeile ↔ Modul ↔ Kennung ↔ Reifegrad ↔ Blatt; eine Kennung, die dort
nicht steht, kann kein Blatt anschließen.

## Spur W (Werkzeug/Produkt-Slice) — verdient durch vier Eigenschaften, begrenzt durch fünf Zahlen
Eignung: Bedienweg benannt (N4) · kein Rechte-, Geld-, DB-Schema- oder Auth-Bezug · ≤ 8 Kriterien inkl. Browserabnahme und
Rot-Probe „ohne Werkzeug" · Rückweg = Revert eines Commits. Dann: **ein** DoR-Durchgang (ERTEILT / NICHT ERTEILT; Halbsätze
werden im Votum mitgeliefert und gelten als Teil des Blatts — keine Auflagen-Schleife), **eine** Lieferung, Browserabnahme durch
den Evaluator, Zustand aus dem Ereignis, ≤ 15 Commits je Blatt, Generator-Anteil ≥ 40 %. Überschreitung → Rückstufung nach Spur A
mit Protokoll; der Planner begründet im nächsten Blatt, was beim Zuschnitt fehlte. Rechte/Geld/DB/Auth sind nie Spur W.
Der Generator stuft nicht selbst ein; gewechselt wird nur nach oben.

## Abnahme = Bedienbarkeit (Reifegrad im Votum und im Zustand)
Für Produkt-Blätter ist `ABGENOMMEN` im Sinne des Meilensteins erst `ABGENOMMEN (BROWSER)`; Kriterien-grün ohne Browser ist
`ABGENOMMEN (CODE)` und wird getrennt geführt. Der Zustandscommit trägt den Reifegrad (Regelbau V3-9 im Muster; bis dahin
als Feld `reifegrad:` im Block).

## Eigenausrüstung nur aus drei Quellen
Regel-/Werkstattbauten (A-Kennungen, Z0) nur aus: (a) Stopp-Regel (Vorfall gegen Schreibschutz/Integrität), (b) den Z0-Restposten
Z0-I1 → Z0-I2 → Z0-I3 → Z0-I4 in dieser Reihenfolge, (c) Errata an laufenden Regelbauten. Maximum vier offene Regelbauten bleibt.
Ein Tag mit Produktivzeilen = 0 und Werkstatt-Commits > 50 heißt im Lagebericht „Werkstatt-Tag"; der zweite in Folge braucht Yamas
ausdrückliches Wort.

## Berichtigungen stapeln nicht
Eine Berichtigung einer Berichtigung (zweite Ebene zur selben Sache binnen 24 h) ist ein Stopp-Fall: Ursache benennen, bevor der
nächste Schritt fällt. Pflicht in jeder Meldung: `gelesen_bis` (6g), `endstand_sha`/`ergebnis_sha` (6e), Messbefehle zitiert statt
nachgebaut (P-02/4), Grundmenge mit STAND vor jeder Zahl.

## Zustand aus dem Ereignis
Der Integrator erzeugt Zustandscommits ausschließlich mit `status-erzeugen.sh` aus der Abschlussmeldung (Kennung, endstand_sha,
ergebnis_sha, Beleg, Reifegrad); kein Handtext in `docs/STATUS.md`. Beim ersten Transport eines neuen Blatts legt er den Block an
(ENTWURF, `dor_beleg: steht aus`). Neue Prosa gehört ins Votum, nicht in den Datensatz; bestehende Belege werden nicht zurückgebaut.
Erzeuger-Marke als Torprüfung folgt als Regelbau (Spur A) nach Z0-I1.

## Abnahme vor Zuschnitt
Jeder Tag beginnt mit den ältesten Abnahmen in Risikoreihenfolge. Neue Zuschnitte (außer Anschlusswelle und Sicherheitsfolgen) nur,
wenn der BEREIT-Vorrat beim Generator ≤ 6 und das älteste CODE_FERTIG < 24 h ist; sonst arbeitet der Planner an Vorlagen/Register/
Konzept. Eine zweite Evaluator-Sitzung (eigener Worktree, Lease je Auftrag, V2 §8) nur auf Yamas Wort.

## Lagebericht — sechs Messgrößen mit Stopp-Stand (Berichtsregel-Ergänzung)
Bedienweg-Quote (Produktcommits mit Bedienfläche / Produktcommits) · Module ohne Ladeweg · Werkstatt/Produkt-Verhältnis ·
Commits je Lieferung nach Rolle · Berichtigungsquote + Zweit-Ebene · STATUS-Handänderungen ohne Erzeuger-Marke · dazu Vorrat und
Alter des ältesten CODE_FERTIG. Fehlt eine, ist der Bericht unvollständig; kippt eine, steht der benannte Stopp daneben.

## Planungsmodell (Meilensteinplan V3 §1)
Konzept → Meilenstein → Auftrag (ein Ziel) → Aufgabe → Kriterium, mit den dort genannten Pflichtfeldern und Erreicht-Regeln:
ein Ziel ist erreicht, wenn alle seine Kriterien grün sind; Meilensteine zählen nur ABGENOMMEN (Produkt: BROWSER); kein Termin ohne
Erreicht-Bedingung; was kein Kriterium hat, wird nicht gebaut.
