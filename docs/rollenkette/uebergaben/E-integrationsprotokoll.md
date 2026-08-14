# E · INTEGRATIONSPROTOKOLL

> **Von:** Integrator · **An:** Yama
> **Je EINZELNEM Vorgang ein Protokoll.** Eine Sammelübernahme hat kein Protokoll, weil sie
> keine Einzelvorgänge kennt — und ist deshalb verboten.

| Feld | Wert |
|---|---|
| **Vorgangs-ID** | `INT-nnnn` |
| **Integrator-Instanz** | *(welcher Agent, eindeutig benennbar)* |
| **`TICKET_ROLLE`** | `integrator` |
| **Betriebsart** | `NUR_LESEND` \| `BOOTSTRAP` \| `SCHREIBEND` |
| **Vorgangstyp** | A Aktivierungsprüfung \| B Generator-Commit \| C reiner Statusübergang \| D Prüf-/Freigabedokument |
| **Ursprungsrolle** | planner \| plan-pruefer \| generator \| evaluator \| release-pruefer |
| **Auftrag** | `A-nn` / `W-nn` / — |
| **Ursprungscommit** | `<sha>` |
| **Basis-SHA** | `<sha>` *(des Ursprungscommits, nicht des Ziels)* |
| **Ziel-HEAD vorher** | `<sha>` |
| **Ziel-HEAD nachher** | `<sha>` |
| **betroffene Pfade** | *je Pfad eine Zeile — keine Sammelangabe, kein `--stat`* |

## Geprüfte Übergaben

*(Nur die für den Vorgangstyp erforderlichen. Ein Typ C verlangt genau einen Rollenbeleg;
ein Typ D verlangt nicht seine eigene Freigabe.)*

| Übergabe | vorhanden | Beleg |
|---|---|---|
| Planner-Auftrag | ja/nein/n.z. | |
| DoR Plan-Prüfer | ja/nein/n.z. | |
| Generator-Commit + Übergabe | ja/nein/n.z. | |
| Evaluator-Abnahme *(unabhängig)* | ja/nein/n.z. | |
| Release-Votum | ja/nein/n.z. | |

## Entscheidung

| Feld | Wert |
|---|---|
| **Entscheidung** | **ÜBERNOMMEN** \| **ABGELEHNT** |
| **Ablehnungsgrund** | *(bei ABGELEHNT Pflicht — und was der Absender tun muss)* |
| **Konflikte** | *(benannt, nicht gelöst. „keine" ist eine gültige Angabe)* |
| **Statusänderungen** | *(je Kennung: von → nach; Tafelzeile UND Datensatz im selben Commit, A-20)* |
| **nicht integrierte Bestandteile** | *(ausdrücklich, auch wenn leer — eine fehlende Liste liest sich wie „alles drin")* |
| **Push-/`main`-Freigabe** | **liegt vor** \| **liegt NICHT vor** *(ohne sie kein Push, kein Merge, kein Tag)* |
| **Zeitpunkt** | `YYYY-MM-DD HH:MM:SS ±ZZZZ` |

## Prüfkommandos und Rohausgaben

```text
# Rohausgabe zuerst, Prosa danach.
# "selbst geprüft, in Ordnung" ist von "behauptet in Ordnung" nicht unterscheidbar,
# wenn nur der Satz ankommt.
```

## Zwei Pflichtsätze am Ende

1. **Was ich NICHT geprüft habe** — ausdrücklich benannt, nicht ausgelassen.
2. **Ob dieser Vorgang eine fachliche Frage aufgeworfen hat**, die ich **nicht** entschieden habe
   und wer sie entscheiden muss. *(Der Integrator entscheidet nichts fachlich — aber er darf eine
   offene Frage nicht verschweigen.)*
