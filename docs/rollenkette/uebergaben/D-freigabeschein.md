# D · FREIGABESCHEIN

> **Von:** Release-Prüfer · **An:** Yama
> Yama veröffentlicht. Dieser Schein ist die Entscheidungsgrundlage, nicht die Freigabe selbst.

| Feld | Wert |
|---|---|
| Auftrag | A-nn |
| **Vorgänger** | Abnahmevotum C, Prüf-SHA `<sha>` — ABGENOMMEN |
| **Release-SHA** | `<sha>` |
| Geprüft in | getrennter Checkout — nicht im Arbeitsbaum |

## Erneute Torprüfung am Abnahme-Commit

| Tor | Ergebnis | Ausgabe |
|---|---|---|
| Typprüfung | | |
| Testsuite | | |
| Insel-Bau | | |
| Bestandsdokumente laden | | |

> **Warum erneut:** Der Evaluator prüfte im Arbeitsbaum. Hier wird geprüft, ob es
> auch dort grün ist, wo nichts anderes herumliegt.

## Zweiglage

| Frage | Antwort |
|---|---|
| Liegt der Bau auf dem Arbeitszweig? | |
| Ist er Vorfahr von HEAD? | |
| Auf welchen Remotes vorhanden? | |

> Belegter Vorfall: zwei abgenommene Baue lagen **nicht** auf dem Arbeitszweig
> und blockierten einen dritten Auftrag (`576b6290`). Diese Prüfung fehlte.

## Rückweg

| Frage | Antwort |
|---|---|
| Wie wird zurückgedreht? | `git revert <sha>` / anders: |
| Was bleibt nach dem Zurückdrehen? | Migrationen, Daten, Dateien |
| Ist der Rückweg **geprobt**? | ja/nein |

## Restrisiko

<Was auch nach grüner Prüfung schiefgehen kann. Ein leeres Feld ist verdächtig.>

## Votum

**RELEASE_FREI** · RELEASE_BLOCKED

**Für Yama zu entscheiden:** <Push · Merge nach main · Tag · Deploy — was genau>
