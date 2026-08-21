# VOTUM A-42 — „Befundnotizen ziehen um"

**evaluator · 21.08. · Bau `26c46f31` · Prüfstand-HEAD `1cd33614` · Lease `fencing_token: 1`**

## Ergebnis: NACHBESSERN — EIN Punkt. Der Umzug selbst ist makellos.

Zehn von elf Prüfpunkten tragen, sieben davon zeichengenau. Der eine offene Punkt betrifft **nicht**
den Umzug, sondern eine fehlende Bewertung.

## Vorab: die Bau-Identität selbst geprüft, nicht übernommen

| geprüft | Ergebnis |
|---|---|
| `26c46f31` ist der A-42-Bau | **ja** — `BEFUNDNOTIZEN.md +9470` · `STATUS.md +31/−9210` · `a42-befundnotizen-umzug.py +407` |
| `0f731c22` ist der A-38-Bau | **ja** — zwei Hook-Dateien (`.githooks/commit-msg`, `post-commit`) |
| `e9e6ee5b` ist leer | **ja** — **0** geänderte Dateien |

**Eine Präzisierung zur Warnung meines Auftrags:** Sie sagt, `e9e6ee5b` nenne „als Bau `0f731c22`".
Der Betreff nennt tatsächlich **beide** SHAs (`bau 0f731c22 26c46f31`). Der Fehler ist also nicht
ein falscher SHA, sondern **zwei Kennungen und zwei Bauten in einer Meldung** — genau das, was das
Statusmuster nicht erkennt. Das entlastet den Generator in einem Punkt und ändert nichts am Rest.

## Messtisch — alle elf Punkte

| # | Prüfpunkt | Ergebnis |
|---|---|---|
| 1 | `461 = 289 + 172` | **erfüllt, exakt** — ```yaml-Zäune: vorher **461**, nachher **289** + **172** = **461** |
| 2 | alle 172 Blöcke byte-identisch | **erfüllt** — md5 je Block: **0** fehlend, **0** neu; **172 von 172** stammen byte-identisch aus vorher |
| 3 | keine Inhaltszeile verloren/hinzugefügt | **erfüllt** — **0 verloren**; 284 neue = Kopf der neuen Datei (Titel, Erklärung, Beispiel) |
| 4 | gerade Zaunbilanz in beiden Dateien | **erfüllt** — vorher 1194, nachher 850 und 346: **alle drei gerade** |
| 5 | 104 echte Auftragsdatensätze unverändert | **erfüllt** — eigenes Werkzeug an **beiden** Ständen: 104 Datensätze, 104 Tafelzeilen |
| 6 | Ballbesitz je Rolle vorher = nachher | **erfüllt** — identisch, keine Zustand- und keine Ball-Drift; `evaluator 6` deckt sich mit meinen sechs Bällen |
| 7 | zweiter Lauf idempotent | **erfüllt** — Lauf auf dem umgezogenen Stand: **beide md5 unverändert** (`ab18d347…`, `fa0a8b18…`) |
| 8 | die zwei zurückgebliebenen Überschriften bewusst bewertet | **NICHT erfüllt** — es sind **drei**, und bewertet ist keine |
| 9 | Wacheanweisungen durchsuchen beide Dateien | **teilweise** — `yama-posten.py` ja (5/4); `a26`, `a30`, `a33`, `status-erzeugen` kennen `BEFUNDNOTIZEN` **0×** |
| 10 | Zieldateischutz | **Befund bestätigt** — `BEFUNDNOTIZEN` in `rollen-tor.sh` **0×**, in `commit-pruefen.sh` **0×** |
| 11 | Votum mit Befehl, Rohausgabe, SHA | dieses Blatt |

## Der eine Punkt: A-42-1

**Drei** Überschriften haben ihren yaml-Block verloren und stehen nun ohne Inhalt in `docs/STATUS.md`:

```
## A-17 — MESSBERICHT des Planners (KEIN Zustandsdatensatz — der steht oben, Z. 2138)
## BEFUND — DIE EINE STATUSWAHRHEIT HAT GERADE ZWEI FASSUNGEN (Plan-Prüfer, 14.08. 08:23)
## BERICHTIGUNG DER CODE-ZEIGER AUS DER STATUSWAHRHEIT — Integrator, 21.08.2026
```

Gemessen als: Überschrift, der **vorher** ein ```yaml-Block folgte und **nachher** keiner mehr.
Weder das Blatt noch der Baubericht bewerten sie; der Prüfumfang erwartet die Bewertung von **zwei**.
**Zu tun:** die drei benennen und je entscheiden — mitziehen, mit Verweis versehen oder entfernen.
*Eine Überschrift ohne ihren Block ist keine Kleinigkeit: Sie verspricht Inhalt, den die Datei nicht
mehr hat.*

## Zwei Befunde ohne Abzug

**(a) Die neue Datei ist nicht geschützt.** `docs/STATUS.md` wird vom Rollen-Tor gesperrt — ich habe
das selbst erlebt (A-37-6 wies meinen Claim-Commit ab). `docs/BEFUNDNOTIZEN.md` kommt in
`rollen-tor.sh` und `commit-pruefen.sh` **0×** vor: **jede Rolle kann sie aus jedem Baum schreiben.**
Der Auftrag nennt das ausdrücklich als Befund ohne Abzug — er gehört in die A-37-Erweiterung.

**(b) Die Wächter sehen die neue Datei nicht.** `a26-ball-drift`, `a30-datensatz-paar`,
`a33-kennungen-nachziehen` und `status-erzeugen` nennen `BEFUNDNOTIZEN` **0×**. **Heute ohne
Wirkung**, denn alle drei arbeiten auf Auftragsdatensätzen und Tafelzeilen — und die sind
vollständig in `STATUS.md` geblieben (104 = 104, Punkt 5). *Die Lücke wirkt erst, wenn dort je ein
Feld mit `zustand` oder `ballbesitz` landet.* Als Vorratsposten notiert, nicht als Mangel gewertet.

## Was besonders sauber ist

Die **Idempotenz** (Punkt 7) ist der stärkste Beleg: Ein Umzugsskript, das beim zweiten Lauf nichts
mehr ändert, kann nicht doppelt umziehen — und der Baubericht nennt selbst zwei Fehlschläge, die
genau daran scheiterten. Dass **172 von 172** Blöcken byte-identisch ankommen und **0**
Inhaltszeilen verloren gingen, macht den Umzug nachprüfbar verlustfrei.

## Weitergabe

**NACHBESSERN → Generator** (A-42-1: drei zurückgebliebene Überschriften bewerten).
Befunde (a) und (b) → **Planner**. Zustand setze ich nicht — `docs/STATUS.md` ist mir gesperrt;
der Nachtrag gehört dem **Integrator**.

---

# NACHPRÜFUNG A-42-1 — 21.08. 23:5x · Nachbesserung `d17d2ddf` · Lease `fencing_token: 2`

## Ergebnis: erneut NACHBESSERN — EIN Punkt bleibt (A-17), und **eine Berichtigung an mir selbst**

## Zuerst der eigene Fehler (Stopp-Regel)

Mein Votum schrieb, die drei Überschriften „stehen nun **ohne Inhalt**". Der Integrator widerspricht,
und **er hat recht** — gemessen, nichtleere Zeilen bis zur nächsten Überschrift:

| Überschrift | nichtleere Zeilen |
|---|---|
| `## A-17 — MESSBERICHT` | **5** |
| `## BEFUND — DIE EINE STATUSWAHRHEIT` | **670** |
| `## BERICHTIGUNG DER CODE-ZEIGER` | **0** |

**„Ohne Inhalt" trifft auf genau eine von dreien zu.** Ich habe von „Block verloren" auf „leer"
geschlossen, ohne den Rest zu messen — dieselbe Klasse, die ich sonst melde: *eine Zusage trägt den
Namen eines Kriteriums und misst etwas anderes.* Richtig ist: **drei Überschriften haben ihren
yaml-Block verloren**; leer steht nur eine. Der Punkt A-42-1 bleibt davon unberührt — er hing nie an
der Leere, sondern am verlorenen Block.

## Der Einwand des Integrators zu A-17 — gemessen, und er trägt nicht

Der Integrator setzte dort **keinen** Verweis, mit der Begründung, `26c46f31^` habe „schon keinen
unmittelbar folgenden yaml-Zaun" gehabt und A-42 habe „nichts genommen". Nachgemessen:

```
## A-17 — MESSBERICHT …                    (Stand 26c46f31^, :7502)
   +1..+7   Berichtigungstext (Blockquote, 12.08.)
   +8       ```yaml            <== der Zaun IST da, nur nicht unmittelbar
   +9       auftrag: "A-17"
```

Und der Block ist **umgezogen** — byte-identisch nachgewiesen:

| Prüfung | Ergebnis |
|---|---|
| Block steht byte-identisch in `docs/BEFUNDNOTIZEN.md` | **ja** |
| Block steht noch in `docs/STATUS.md` | **nein** |
| hat `zustand`-Feld | nein |
| hat `rolle`+`zeit` | nein |

**A-42 hat dort sehr wohl etwas genommen.** Die Begründung trifft einen *anderen* Block — den am
12.08. nach `6d6823dd` entfernten mit den doppelten Zustandsfeldern; der Berichtigungstext darüber
spricht genau von jenem. *Zwei Blöcke unter einer Überschrift, und die Begründung meint den falschen.*

**Zugute zu halten:** Der Integrator hat den Fall **gemeldet statt stillschweigend übergangen** und
die Entscheidung ausdrücklich mir überlassen. Das ist der Grund, warum hier nachgebessert und nicht
beanstandet wird.

## Was die Nachbesserung richtig macht

| Prüfung | Ergebnis |
|---|---|
| zwei Verweise gesetzt, je eine Zeile mit Anker und Bau-SHA | **ja** — `> Block umgezogen nach docs/BEFUNDNOTIZEN.md (A-42, Bau 26c46f31) — Anker: …` |
| Anker zeigen ins Ziel | **ja** — `statuswahrheit_in_zwei_fassungen` 1 Treffer, `P-05` 2 Treffer |
| Punkte 1–7 unverändert | **ja** — STATUS `289 / 850 / 104` und BEFUND `172 / 346 / 172` **vor wie nach identisch** |
| Umfang | **+4 / −0**, nur `docs/STATUS.md` — zwei Leerzeilen, zwei Verweiszeilen |
| nichts entfernt, nichts zurückgezogen | **ja** — 0 Löschungen |

## Der offene Punkt: A-42-1 (Rest)

**`## A-17 — MESSBERICHT des Planners` braucht denselben Verweis wie die beiden anderen**, weil ihr
yaml-Block umgezogen ist. Anker: `auftrag: "A-17"`, Bau `26c46f31`.

**Nebenbefund ohne Abzug:** Der Anker `P-05` trifft in `BEFUNDNOTIZEN.md` **zweimal**. Ein Verweis,
der zwei Blöcke trifft, führt nicht eindeutig — bei Gelegenheit schärfen (z. B. Zeilenangabe oder
zweites Feld). Kein Mangel dieser Nachbesserung; der Verweis findet sein Ziel.

## Weitergabe

**NACHBESSERN → Integrator** (A-17-Verweis; `docs/STATUS.md` schreibt ausschließlich er — die
Zuständigkeitskorrektur des Dirigenten habe ich angenommen und im ACK gen 4 festgehalten).
Danach ist A-42 aus meiner Sicht abgeschlossen.

---

# ZWEITE NACHPRÜFUNG A-42-1 (Rest) — 22.08. 00:1x · `9e5f1ff5` · Lease `fencing_token: 3`

## Ergebnis: **ABGENOMMEN**

Der Rest ist gesetzt, der Nebenbefund ist geschärft, und die Punkte 1–7 sind über **drei** Stände
unverändert geblieben.

| Prüfung | Ergebnis |
|---|---|
| drei Verweiszeilen vorhanden | **ja** — `:7434` (A-17), `:17651` (statuswahrheit), `:18632` (P-05), je mit Anker **und** Bau `26c46f31` |
| Anker treffen ins Ziel | **ja** — `A-17` **1 Treffer (eindeutig)**, `statuswahrheit_in_zwei_fassungen` 1, `P-05` 2 **mit Unterscheidungsmerkmal** |
| Punkte 1–7 unverändert | **ja** — STATUS `289 / 850 / 104` und BEFUND `172 / 346 / 172` bei `26c46f31`, `d17d2ddf` **und** `9e5f1ff5` identisch |
| Umfang | `3 / 1`, nur `docs/STATUS.md` |
| Inhalt verloren? | **nein** — die eine entfernte Zeile ist die **alte** P-05-Verweiszeile, ersetzt durch die geschärfte |

**Die Zahl `3 / 1` gegen die Erwartung „Löschungen 0" — geprüft und in Ordnung.** Es ist keine
Löschung, sondern eine **Ersetzung**: alte P-05-Zeile raus, geschärfte rein, dazu der A-17-Verweis.
Zeilenweise gegengerechnet: 1 verloren, 2 neu, und die verlorene steht wortgleich als Anfang der
neuen. *Eine Ersetzung sieht in `numstat` aus wie eine Löschung; der Unterschied ist nur am Inhalt
zu sehen.*

**Mein Nebenbefund ist aufgenommen worden:** Der P-05-Anker trifft weiterhin zweimal — aber der
Verweis nennt jetzt `mit rolle: integrator (der zweite P-05-Block dort ist vom Plan-Prüfer)`. Damit
ist die Zweideutigkeit **benannt und auflösbar**, ohne eine Zeilennummer zu setzen, die mitwandern
würde. Das ist die bessere Lösung als die, die ich angedeutet hatte.

## Zum Einwand, den der Integrator selbst zurückgenommen hat

Er hat nachgemessen und die eigene Fehlerursache benannt: *„ich fragte nach der UNMITTELBAR
folgenden nichtleeren Zeile … Aus ‚kein Zaun daneben' habe ich ‚nichts umgezogen' geschlossen — Ort
statt Wirkung. Ich habe damit ein engeres Kriterium benutzt als das, gegen das ich argumentiert
habe."* Seine Zahlen decken sich mit meinen (Zaun bei +8, Block 22 Zeilen, byte-identisch in
`BEFUNDNOTIZEN.md`).

**Bemerkenswert ist die Richtung:** In derselben Runde hat er einen Fehler von mir aufgedeckt
(„ohne Inhalt" traf auf eine von dreien) und einen eigenen zurückgenommen. Beide Male entschied die
Messung, nicht die Behauptung — und keiner von uns musste dem anderen glauben.

## Bilanz A-42

| | |
|---|---|
| Prüfumfang | **11 von 11 erfüllt** |
| Kern des Umzugs | `461 = 289 + 172`, **172/172 byte-identisch**, **0** Inhaltszeilen verloren, idempotent |
| Datensätze und Bälle | 104 = 104, keine Drift — über alle vier Stände |
| A-42-1 | drei Verweise, Anker treffen, nichts entfernt |
| offene Befunde (ohne Abzug) | `BEFUNDNOTIZEN.md` ohne Schreibbarriere · Wächter kennen die Datei nicht — beide beim **Planner** (A-37-24) |

## Weitergabe

**ABGENOMMEN → Integrator** für den Zustandscommit A-42 (eine Kennung, ein Bau `26c46f31`, Beleg =
dieser Votum-SHA). Den Zustand setze ich nicht — `docs/STATUS.md` schreibt ausschließlich er.
