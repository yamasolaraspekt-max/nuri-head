# Evaluator-Auftrag — v9-Welle komplett (2026-07-23, aktualisiert)

**Rolle:** Evaluator (unabhängig, Gegen-Beweis — messen, nicht die Generator-Behauptung nachbeten).
**Grün nur mit selbst erhobenem Beleg**, am echten Bundle/Rendern.

## Umfang — sieben Commits (auf cca5949 aufgesetzt)
| Commit | Inhalt |
|---|---|
| `970f0cc` | Bauart-Icon-Raster Fenster/Tür (48 Premium-SVGs, oeffnungsBauarten.ts, produkt.typ additiv) |
| `14c4d0e` | Studio-Rahmen: Modus-Shell + Start + geführte WizardBase + ConfigWizard + Treppen-Bauart |
| `514967c` | Navigation auto-einklappen auf schmalen Viewports |
| `ff707f9` | ConfigWizard „Übernehmen" → autarkes ConfiguratorPackage (JSON-Download) |
| `95720de` | Tastatur-Fokus sichtbar + Konfigurator-Trio im geführten Schritt |
| `533a2e7` | Studio liest Store (echter speicherStatus + scene.revision + Modell-Zahlen); Wizard schreibt Öffnung per ADD_NODE ins Modell bei gewählter Wand |

## Gegen-Beweis-Punkte (bitte selbst erheben)
1. **Additivität**: Öffnung ohne `produkt.typ` und Treppe ohne `typ` bleiben gültig; mit gültig; Bridge round-trip (`treppeZuParametern`↔`parametere ZuTreppe`) erhält `typ`. tsc Exit 0; Suite **285/285**; schema:check.
2. **Katalog-Trennung**: `oeffnungsTypen.ts` (Zeichen-Vorlagen, Standardmaße) unverändert ggü. Vor-970f0cc — bitte `git diff cca5949 -- oeffnungsTypen.ts` = leer prüfen. `oeffnungsBauarten.ts` + `treppenBauarten.ts` sind getrennte Icon-Kataloge.
3. **Asset-Pfad**: Bauart-Icons (Fenster/Tür/Treppe) laden zur Laufzeit über `import.meta.url` — am echten Rendern in Panel UND ConfigWizard prüfen; Öffnungsart-Ableitung messen (Fenster: dreh-kipp→dreh→fest bei Kachelwechsel).
4. **Modus-Shell**: Start/Geführt/Experte umschaltbar; Experte rendert die volle App OHNE doppelte Markenzeile (`imStudio`) und mit funktionierendem **2D/Split/3D**; 0 Pageerrors in allen Modi.
5. **Echter Zustand (533a2e7)**: Studio-Kopf spiegelt `speicherStatus` (gespeichert/ungespeichert/…) + `scene.revision`; geführter Schritt zeigt echte Zahlen aus `scene.nodes`. Nach einer Änderung muss der Kopf auf „Ungespeicherte Änderungen" springen.
6. **Schreibpfad (533a2e7) — Kernprüfung**: Bei AUSGEWÄHLTER Wand setzt „Übernehmen" die Öffnung per `ADD_NODE` auf diese Wand (produkt.typ + oeffnungsArt gesetzt, zentriert, Breite auf Wandlänge geklemmt); Status→ungespeichert; Öffnung liegt in der Wand. OHNE Auswahl: autarker JSON-Download, KEINE Szene-Mutation. Eigener Gegen-Beweis empfohlen (Store init mit Wand → ADD_NODE → Knoten vorhanden; siehe `__tests__/configWizardWrite.test.ts`, aber bitte unabhängig nachziehen).
7. **Mobile/A11y**: <900px klappt die Navigation auf die Icon-Leiste (Inhalt lesbar); `:focus-visible`-Ring sichtbar (Tastatur).
8. **Reproduzierbarkeit**: Bundle aus gepinnten Deps (vite 6.4.1) deterministisch; Bundle-md5 @HEAD = committed.

## Noch NICHT gebaut (nicht prüfen)
- Guided-Schrittstatus/Prüflisten aus dem Modell ABLEITEN (aktuell zeigt der Schritt echte Zahlen, aber die Badge-Status sind noch das designte Narrativ).
- Treppen-Öffnung aus dem Wizard ins Modell (Platzierung braucht Lauflinie/Canvas) — bewusst nur autarker Package-Download.

## Governance-Randbefund (offen deklariert)
- One-Writer: Die v9-Welle wurde auf ausdrückliche Anweisung des Auftraggebers („mach es live, arbeite durch") committet, während die Fenster-Wellen-Review lief bzw. deren offene Scheiben (970f0cc) noch nicht geprüft waren. Alle sieben Scheiben liegen als abgeschlossene Commits vor — bitte als solche unabhängig prüfen.
- Ballbesitz nach Review: Yama.
