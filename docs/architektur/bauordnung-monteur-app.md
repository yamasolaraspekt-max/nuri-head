# Bauordnung Monteur-App (Entwurf) — „Mein Tag"

**Stand:** 2026-07-16 · Entwurf zur Abnahme durch Yama; danach eigene Welle mit eigenem Strang.
**Bezug:** Einsatzplanungs-Konzept (`konzepte/konzept-einsatzplanung-2026-07-16.md`) — die Monteur-App ist dessen mobile Hälfte: die Plantafel plant, „Mein Tag" führt aus. In der Navi liegt der Platzhalter bereits (EINSATZPLANUNG → Montage → „Mein Tag · geplant").

---

## §1 · Grundsatzentscheidungen (die Weichen, jetzt zu stellen)

1. **PWA statt nativer App** — gleiches Laravel-Backend, gleicher Stack, gleiche Bauordnung, gleiche Agenten, kein App-Store (passt zur Lokal-First-Direktive). Installierbar über „Zum Startbildschirm".
2. **Offline-First ist DIE technische Kernentscheidung** — der Monteur steht ohne Netz im Keller am Zählerschrank. Architektur: lokale Warteschlange (Queue) für alle Schreibaktionen (Bericht, Foto, Checkliste, Unterschrift, Fertigmeldung) → Sync bei Netz, mit Konfliktregel „Server gewinnt bei Stammdaten, Gerät gewinnt bei eigenen Erfassungen".
3. **Versionierte Read/Write-API** als einzige Tür (analog Kundenportal-Konzept). Keine Blade-Wiederverwendung aus dem CRM — eigene, schlanke Oberfläche auf derselben Datenwahrheit. Das Planner-Modul liefert die Grundlage (belegt: `PlannerEmployeeApiController`, `PlannerApiAuthController`, `PlannerMobileCustomerImageController` existieren bereits).
4. **Ein Token-System, zwei Größenskalen:** dieselben sa-ui-Farbtoken wie ticket, nur mit mobiler Skala (größere Schrift, größere Abstände). Die Marke bleibt eine — es entsteht KEIN zweites Design-System.

## §2 · Design-Regeln (eigene Bauordnung, nicht „ticket in schmal")

Die Nutzungsrealität ist eine andere — daraus folgen harte Regeln:

| Realität draußen | Regel |
|---|---|
| Sonnenlicht | hoher Kontrast; keine subtilen Grauabstufungen als Bedeutungsträger |
| Handschuhe / dreckige Finger | Touch-Ziele **mindestens 48px**, großzügige Abstände, keine Hover-Abhängigkeit |
| einhändig auf der Leiter | Hauptaktionen in der **Daumen-Zone unten**; nichts Wichtiges oben rechts |
| kein Netz im Keller | jeder Screen funktioniert offline; Sync-Status immer sichtbar (ein Symbol, ehrlich) |
| wenig Zeit, Fokus auf Arbeit | **lineare Flüsse**, wenige Screens, große Elemente, keine verschachtelten Menüs |

**Farbwelt:** Yamas Palette, aber kontrastverstärkt (Volltöne statt heller Flächen als Primärsignal); Schrift Dunkelgrau `#1f2937` auf Weiß; Statusfarben wie ticket (`#ef4444` / `#f59e0b` / `#10b981`).

## §3 · Kernflüsse (genau diese, in dieser Reihenfolge bauen)

1. **Tagesliste** — meine Einsätze heute (aus der Plantafel), sortiert nach Zeit, mit Adresse/Navigation und Sync-Status.
2. **Einsatz öffnen** — Baustelle, Ansprechpartner, Material, Unterlagen; Anfahrt starten.
3. **Checkliste abarbeiten** — die Checklisten-Formulare des Auftrags, Punkt für Punkt (Operanden-Gate gilt: fehlender Wert wird markiert, nie erfunden).
4. **Fotos** — Baudokumentation mit automatischer Zuordnung zu Einsatz + Checklisten-Punkt.
5. **Unterschrift** — Kunde unterschreibt auf dem Gerät (Abnahme).
6. **Fertig melden** — Status zurück an Plantafel/Baustelle; Ist-Zeiten in die Zeitwirtschaft.

Nicht in V1: Angebote, Preise, Lagerbuchungen, Chat. (Erweiterung erst nach abgenommener V1.)

## §4 · Prüf-Loop (gleiche Mechanik wie ticket, mobile Kriterien)

Playwright mit **Device-Emulation** (iPhone-/Android-Viewports, Touch) klickt die Kernflüsse durch und screenshottet — derselbe Loop wie im CRM (UI-Bauordnung §3), plus mobile Evaluator-Kriterien: **„Geht das mit dem Daumen?"** · alle Touch-Ziele ≥48px? · Fluss ohne Netz durchspielbar (Offline-Simulation)? · lesbar bei simuliertem Sonnenlicht-Kontrast? Extremfälle: Tag mit 8 Einsätzen, Einsatz ohne Material, Foto-Upload bei Funkloch (Queue sichtbar?).

## §5 · Offene Entscheidungen für Yama

**(a)** Reihenfolge: Monteur-App-V1 vor oder nach dem Plantafel-Ausbau (beide hängen am Einsatz-Modell — Empfehlung: Einsatz-Modell zuerst, dann parallel)?
**(b)** Unterschrift rechtlich: reicht Touch-Unterschrift als Abnahme-Beleg für eure Verträge?
**(c)** Geräte-Realität: Firmen-Tablets, private Telefone, oder beides (bestimmt die Test-Viewports)?
