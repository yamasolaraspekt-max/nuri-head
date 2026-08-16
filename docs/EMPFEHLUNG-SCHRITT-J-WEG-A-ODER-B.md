# Empfehlung an Yama: welcher der zwei Wege zu Schritt J

> **Release-Prüfer, 16.08. ~16:4x.** Auf Yamas Frage *„welche der zwei vorschläge empfiehlst du
> mir"*. Alle Zahlen nach `fetch --multiple` frisch gemessen, nicht aus Notizen.

## Die Empfehlung in einem Satz

**Weg A** — erst V6 herstellen, dann `SCHREIBEND`. Nicht weil er sicherer ist, sondern weil er
**heute fertig werden kann**: was noch fehlt, sind **drei Fast-Forwards und eine Prüfung**, keine
Bauarbeit.

## Warum Weg B (jetzt freigeben) nicht trägt

`SCHREIBEND` erlaubt zwei Handlungen — integrieren und `docs/STATUS.md` schreiben — und beide würden
heute aus einem Baum laufen, in dem **die Barriere nicht existiert** und dem **151 Commits fehlen**.
Der Integrator hat den ersten Teil selbst gemessen und berichtet (`git ls-files
scripts/rollen-tor.sh` → `0` in seinem Checkout). Ein alleiniger Schreiber ohne Schutz auf altem
Stand ist nicht die Umstellung, sondern ihr Gegenteil: bisher lasen sechs Rollen dieselbe Datei und
korrigierten einander, danach schreibt einer allein — und niemand liest gegen.

## Warum Weg A schnell ist — der Punkt, der die Antwort dreht

Der Generator hat A-37-18 (*„das Tor liegt in jedem der sechs Bäume"*) angenommen, gemessen und
**vor dem Bauen** gemeldet, dass es mit seinen Mitteln nicht erreichbar ist:

> *„Das ist TRANSPORT, und Transport ist mir ausdrücklich untersagt. Ein Kriterium, dessen einziger
> Weg eine Handlung ist, die die adressierte Rolle nicht ausführen darf, kann von ihr nicht erfüllt
> werden — das ist keine Weigerung, sondern eine Messung."*

Er hat recht, und er hat auch den naheliegenden Ausweg geprüft statt vermutet (das Tor in
`commit-pruefen.sh` ziehen — trägt nicht, die drei Bäume ohne Tor tragen auch den Haken nicht).

**Nachgemessen, woran es wirklich hängt** — und das ist die gute Nachricht:

```
                                    rollen-tor.sh   0ee521f7    Weg dorthin
auto/hausplaner-integration  lokal        0            NEIN      FAST-FORWARD (0 voraus)
rolle/planner                            0            NEIN      FAST-FORWARD (0 voraus)
rolle/plan-pruefer                       0            NEIN      FAST-FORWARD (0 voraus)
rolle/generator                          1             JA       —
rolle/evaluator                          1             JA       —
rolle/release-pruefer                    1             JA       —
fork/auto/hausplaner-integration         1             JA       —
```

**Alle drei fehlenden Zweige sind `0 voraus`.** Es ist kein Merge, kein Konflikt, nichts wird
überschrieben — jede der drei Rollen hat exakt nichts, was ich nicht auch habe. Ein
`git merge --ff-only` je Baum, drei Zeilen insgesamt.

**Warum ich es nicht einfach selbst mache.** Die drei Zweige sind in fremden Arbeitsbäumen
ausgecheckt; Git lässt einen ausgecheckten Zweig nicht von außen bewegen, und in fremde Bäume greife
ich nicht. Es ist also nicht meine Hand, sondern **je eine Zeile in je einem Baum** — Integrator,
Planner, Plan-Prüfer. Der Generator hat gefragt, ob der Ball dafür an mich soll; die ehrlichere
Antwort ist, dass er an niemanden einzeln muss.

## Was Weg A konkret kostet

| Schritt | Wer | Aufwand |
|---|---|---|
| `git merge --ff-only` im Integrations-Checkout | Integrator | eine Zeile — räumt zugleich die 151 Commits |
| dasselbe in `ticket-rolle-planner` | Planner | eine Zeile |
| dasselbe in `ticket-rolle-plan-pruefer` | Plan-Prüfer | eine Zeile |
| positive **und** negative Sperrfälle prüfen (Schritt I) | Evaluator | eine Prüfrunde |
| A-37 aus der DoR | Plan-Prüfer | läuft seit heute früh |
| **dann** Schritt J | **Yama** | eine Entscheidung, dann belegt statt gewettet |

Nach den drei Fast-Forwards ist **A-37-18 erfüllt** (ls-tree = 1 in allen sechs) und **Grund 3
meiner Antwort erledigt** (Integrationszweig auf dem Fernstand). Übrig bleibt genau das, was von
Anfang an übrig bleiben sollte: eine unabhängige Prüfung der Barriere.

## Die Grenze meiner Empfehlung

Ich empfehle den *Weg*, nicht die *Entscheidung*. Schritt J ist in der Tabelle ausdrücklich dir
zugeordnet, und er ist eine Entscheidung über Vollmacht — genau die Art, die ich nach meiner eigenen
Regel nicht in deinem Namen treffe. Wenn du Weg B trotzdem willst, ist das deine Entscheidung gegen
eine gemessene Vorbedingung, und ich führe sie aus; ich würde dann nur darum bitten, dass die drei
Fast-Forwards trotzdem vorher laufen, weil sie nichts kosten und den 151-Commit-Rückstand allein
schon rechtfertigt.
