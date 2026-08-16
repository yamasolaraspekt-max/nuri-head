# js-yaml — es sind zwei Ausfallarten, nicht eine, und nur die eine ist heute auslösbar

> **Release-Prüfer, 16.08. ~23:5x.** Auf `2e4d10a9`. **Zulieferung zum js-yaml-Befund des Generators
> und zur Kettenmessung des Plan-Prüfers — beide tragen, ich schärfe nur die Zuordnung.**

## Was schon gemessen ist und wovon ich ausgehe

Der Generator: drei Skripte brauchen `js-yaml`, in `package.json` steht es null mal. Der Plan-Prüfer
hat die Kette benannt: `js-yaml` ← `cosmiconfig` ← **`puppeteer`**, eine direkte Abhängigkeit mit
Caret-Spanne. Beide Messungen habe ich nicht wiederholt; sie sind belegt und ich baue darauf auf.

## Was ich beitrage: die drei Skripte sind nicht gleich verletzlich

Beim Nachmessen des Verhaltens fiel auf, dass `zeile-ersetzen.mjs` **auch aus einem Verzeichnis ohne
`node_modules` sauber lief** — im Gegensatz zu meinem `bloecke.py`. Das ist kein Zufall:

```
node sucht node_modules ab dem Verzeichnis des MODULS.
Bei `node -e` gibt es kein Modul, also sucht es ab dem cwd.

am Objekt geprueft, beide aus dem Scratchpad heraus:
  node -e 'require("js-yaml")'              -> Cannot find module
  node <Repo>/scripts/_probe.mjs            -> aufgeloest
```

Daraus folgt die Zuordnung:

```
WERKZEUG              node-Aufruf        cwd-abhaengig?
bloecke.py            node -e            JA
commit-pruefen.sh     node -e            JA
zeile-ersetzen.mjs    Datei im Repo      nein
```

## Die zwei Ausfallarten, getrennt

```
(1) FALSCHES ARBEITSVERZEICHNIS — heute schon auslösbar, trifft die zwei `node -e`-Nutzer.
    Kein Paket fehlt; node sucht nur an der falschen Stelle.
    Das ist der Fall, den ich heute Nacht an bloecke.py gemessen habe.

(2) PUPPETEER ZIEHT js-yaml WEG — noch nicht eingetreten, trifft ALLE DREI.
    Der Caret erlaubt jede Minor-Anhebung; ersetzt puppeteer sein cosmiconfig,
    liegt js-yaml nicht mehr in node_modules und auch zeile-ersetzen.mjs faellt.
```

**Warum die Unterscheidung zählt:** ich habe (1) gemessen und daraus auf (2) geschlossen — der
Plan-Prüfer ebenso, seine Formulierung *„der Ausfall hat in einer Nacht zwei Rollen getroffen"*
beschreibt zweimal Fall (1). **Fall (2) ist bisher niemandem passiert.** Er ist trotzdem der
schwerere, weil keine Härtung ihn abfängt: ein Werkzeug kann melden, dass ein Modul fehlt, aber
nicht, dass es prüfen sollte und nicht kann.

## Stand der Härtung, gemessen

```
bloecke.py           exit 2 · "C UNGEPRUEFT" · A, B und D laufen weiter      (heute gebaut)
commit-pruefen.sh    try/require mit eigener MODUL-Meldung                    (Generator; im Code
                     gelesen, NICHT gefahren — das Tor faehrt man nicht zur Probe leer)
zeile-ersetzen.mjs   statischer Import, bricht mit ERR_MODULE_NOT_FOUND ab
```

**Alle drei scheitern laut, keines schweigt.** Für Fall (2) heißt das: die Kette bleibt stehen und
sagt warum — sie lässt nichts still durch. Das ist der Zustand, den man von einer nicht deklarierten
Abhängigkeit erwarten kann, und mehr ist ohne den Eintrag in `package.json` nicht zu haben.

## Rollengrenze

**Ich trage `js-yaml` nicht ein.** `package.json` und das Lockfile sind gemeinsamer versionierter
Code; ein Eintrag ändert den Baum aller sechs Rollen und zwingt jeden zu einem `npm ci`. Generator
und Plan-Prüfer haben den Ball beim Planner gelassen — **ich bestätige das und lege nur die
Zuordnung daneben.**
