# A-37-18: der neue Adressat kann es auch nicht — und daraus wird ein Rundschluss

> **Release-Prüfer, 16.08. ~16:5x.** Auf `63906bbd` (Planner, 16:44), der A-37-18 vom Generator an
> den Integrator umadressiert. Alles selbst gemessen.

## Was der Planner richtig gemacht hat

Er hat die Messung des Generators **gelesen statt bezweifelt** und seine eigene Fehlerklasse benannt,
dreimal an einem Tag: *„A-41-4 verlangte eine Handlung ohne Berechtigten, A-41-5 eine Eigenschaft
ohne Träger, A-37-18 einen Weg, der dem Adressaten verboten ist."* Und er hat **das Kriterium nicht
abgeschwächt** — das SOLL bleibt, das Tor gehört in alle sechs Bäume. Das ist richtig.

## Was nicht trägt: der neue Adressat

Begründung im Datensatz: *„Ballbesitz auf integrator, denn Transport ist seit heute seine Rolle."*
Drei Messungen dagegen:

**1 · Git lässt einen ausgecheckten Zweig nicht von außen bewegen.** Am eigenen Zweig getestet, um
keinen fremden Baum anzufassen:

```
$ git branch -f rolle/release-pruefer HEAD
fatal: cannot force update the branch 'rolle/release-pruefer'
       used by worktree at '/Users/yamanuri/Documents/ticket-release-pruefung'
```

**2 · Die drei fehlenden Zweige sitzen in drei verschiedenen Bäumen** — es gibt keinen Ort, von dem
aus eine Rolle alle drei erreicht:

```
auto/hausplaner-integration  ->  /Users/yamanuri/Documents/ticket
rolle/planner                ->  /Users/yamanuri/Documents/ticket-rolle-planner
rolle/plan-pruefer           ->  /Users/yamanuri/Documents/ticket-rolle-plan-pruefer
```

**3 · `SCHREIBEND` erlaubt dem Integrator ausdrücklich nur den einen Checkout:** *„integrieren ·
`docs/STATUS.md` schreiben · Integrationsprotokolle ablegen — **ausschließlich im
Integrations-Checkout**"*. Selbst nach Schritt J erreicht er **1 von 3**.

**Und heute erreicht er 0 von 3:** er ist in `NUR_LESEND`, dort ist ihm *„jede Dateiänderung · jeder
Commit · jede Statusänderung"* verboten. Ein `merge --ff-only` ist eine Dateiänderung.

**Damit ist es dieselbe Fehlerklasse zum vierten Mal** — diesmal in dem Commit, der sie beschreibt.
Nicht aus Nachlässigkeit: „Transport ist seine Rolle" stimmt ja für den Vorgang, den der Integrator
künftig führt. Es stimmt nur nicht für *diesen* Vorgang, weil er in fremden Arbeitsbäumen endet.

## Der Rundschluss

```
A-37-18 soll das Tor in alle sechs Baeume bringen
        └─> stellt V6 her  (Rollen- und Checkoutschutz aktiv)
              └─> V6 ist Vorbedingung fuer Schritt J
                    └─> Schritt J gibt SCHREIBEND
                          └─> SCHREIBEND braeuchte der Integrator, um A-37-18 auszufuehren
                                └─────────────────────────────────────────┘
```

Solange A-37-18 bei einer einzigen Rolle liegt, die dafür `SCHREIBEND` bräuchte, kann es nicht
erfüllt werden. **Kein Bau löst das und kein weiterer Ballwechsel** — es ist kein Bauproblem,
sondern eines der Reichweite.

## Die Auflösung: drei Hände statt einer

Der Fehler ist nicht der Adressat, sondern **dass es einen einzigen gibt**. Jeder Baum ist nur von
innen erreichbar:

| Baum | wer kann es | Sonderrecht nötig? |
|---|---|---|
| `ticket-rolle-planner` | **Planner** | nein — eigener Baum |
| `ticket-rolle-plan-pruefer` | **Plan-Prüfer** | nein — eigener Baum |
| `ticket` (Integration) | Integrator, aber erst ab `SCHREIBEND` | **ja** ← hier bricht der Kreis |

Zwei Drittel lassen sich ohne jede Freigabe heute erledigen: `git merge --ff-only`, je eine Zeile,
im eigenen Baum. Für Planner ist es aktuell ein Merge (1 Commit voraus), für den Plan-Prüfer ein
reiner Fast-Forward — beides konfliktfrei, beide Zweige liegen 180 bzw. 185 Commits zurück.

**Übrig bleibt genau eine Handlung, und sie gehört Yama:** entweder er fährt den Fast-Forward im
Integrations-Checkout selbst (`0 voraus`, `188 zurück`, eine Zeile), oder er erteilt dem Integrator
für **diese eine Handlung** eine ausdrückliche Teilfreigabe.

Das ist eine Vollmachtsfrage und keine Messung — deshalb lege ich sie vor und entscheide sie nicht.
Zu bemerken ist nur: die Teilfreigabe wäre ein Vorgriff auf Schritt J für genau den Handgriff, der
Schritt J erst möglich macht. Der Fast-Forward durch Yama umgeht diesen Einwand vollständig und
kostet dasselbe.

## Was das für meine Empfehlung von vorhin ändert

Nichts am Weg, nur an der Hand: ich hatte „drei Fast-Forwards, je einer pro Rolle" empfohlen. Das
bleibt richtig. Neu ist der Beleg, **warum es nicht ein Adressat sein kann** — und dass der dritte
davon ohne Yama nicht geht.
