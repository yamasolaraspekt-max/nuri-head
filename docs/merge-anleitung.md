# Merge nach `main` — die Anleitung, die den 30.07. überlebt hat

*Angelegt 30.07.2026, 22:50, nach dem ersten Merge dieser Woche.*
**Tor 2 gehört Yama.** Der Planner liefert die Befehle, führt sie nie aus und pusht nie.

---

## Die wichtigste Regel dieser Anleitung — **ein Befehl, dann warten**

**Entscheidung Yama, 30.07. 22:52, nach dem Reset-Zwischenfall:**

> *„ab jetzt mergen wir anders damit dieses problem nicht mehr auftaucht, du gibst mir nur
> eins ache die füge ich terminal an, dann schicke ich dir ein kopie hier dann gibst du mir
> den zweiten befehl — nicht viele befehle in einem text, nur eins"*

**Der Ablauf ab sofort:**

1. Der Planner gibt **genau einen** Befehl.
2. Yama führt ihn aus und schickt die Ausgabe zurück.
3. Der Planner liest die Ausgabe, prüft sie **und gibt erst dann den nächsten.**

**Warum das die richtige Lehre ist, und nicht nur Vorsicht:** meine Fassung von 22:34 lieferte
fünf Befehle in einem Block. Yama hat sie als Ganzes eingefügt — und der eine Befehl darin, der
im falschen Verzeichnis stand, lief mit durch, **ohne dass irgendjemand dazwischen etwas hätte
sehen können.** Ein Block ist genau so sicher wie sein schwächster Befehl, und niemand liest
ihn Zeile für Zeile, wenn er als Ganzes zum Einfügen dasteht.

*Ein Befehl je Nachricht kostet ein paar Minuten. Der Zwischenfall vom 30.07. hat vier Minuten
gekostet, in denen 231 Commits nur im Reflog standen — und es hätte auch anders ausgehen können.*

**Zusatz, der daraus folgt:** die Befehlsblöcke weiter unten stehen als **Nachschlagewerk**, nicht
als Kopiervorlage. *Wer sie am Stück einfügt, hat die Regel gebrochen, die über ihnen steht.*

**Und:** ein Befehl, der ein Verzeichnis wechselt, wird **nie** mit einem anderen zusammengefasst.
*Genau dort ist es passiert.*

---

## Die zwei Fehler, die diese Anleitung teuer gemacht haben

**Beide standen in meiner Fassung vom 30.07., 22:34. Beide sind an dem Abend eingetreten.**

### 1. Das `vor-merge`-Etikett gehört ausschliesslich in den Merge-Worktree

> **Der Prüfer, 22:43:** *„Ein `vor-merge`-Etikett sichert den Stand, den man **betritt**,
> nicht den, den man **verlässt**."*

Der Tag zeigt auf **`main` vor dem Merge**. Im Merge-Worktree ist ein
`git reset --hard vor-merge-…` genau richtig — er nimmt den Merge zurück.
**Im Zweigbaum ausgeführt wirft er den ganzen Arbeitstag weg**, weil der Zweig dann auf den
alten `main`-Stand springt.

*Am 30.07. um 22:36:44 ist das passiert: 231 Commits standen vier Minuten lang nur im Reflog,
ohne Zweig und ohne Etikett. Nichts ging verloren — aber nur, weil das Reflog sie hielt.*

**Deshalb steht in dieser Anleitung neben jedem Rückweg-Befehl, in welchem Verzeichnis er gilt.**

### 2. Im frischen Worktree fehlt `node_modules`

`git worktree add` legt **nur die verfolgten Dateien** an. `node_modules` ist gitignored und
fehlt. Die Gates scheitern dort mit *„Cannot find module typescript"* und *„Cannot find package
zod"* — **das ist kein Codefehler und kein Merge-Problem.**

**Und meistens braucht man sie dort gar nicht:** ist `git diff <abgenommener-stand> main` leer,
sind die Gates auf genau diesen Bytes bereits gelaufen.

---

## Vorprüfung — vor jedem Merge, ohne Ausnahme

```bash
cd /Users/yamanuri/Documents/ticket

git merge-base --is-ancestor main auto/hausplaner-integration && echo "Fast-Forward moeglich"
git rev-list --count main..auto/hausplaner-integration    # wie viel kommt dazu
git rev-list --count auto/hausplaner-integration..main    # siehe Kasten: 0 nur beim ERSTEN Mal
git status --porcelain                                    # sauber?
```

> **KORREKTUR nach dem zweiten Merge (31.07. 00:0x).** Die Zeile darüber erwartete ursprünglich
> **0**. *Das gilt nur für den ersten Merge.* Gemessen beim zweiten:
>
> ```text
> git rev-list --count auto/hausplaner-integration..main   ->  1
> git log auto/hausplaner-integration..main                ->  d7052aa0, zwei Eltern
> git merge-base --is-ancestor main auto/...               ->  NEIN
> ```
>
> **`d7052aa0` ist der Merge-Commit des ersten Merges selbst.** Bei `--no-ff` entsteht er nur auf
> `main` und wandert nie in den Zweig zurück. **Die Zahl ist also die Anzahl der bisherigen Merges,
> nicht ein Fehler.** *Erwartet: so viele, wie schon gemergt wurde — und jeder davon muss ein
> Merge-Commit mit zwei Eltern sein. Steht dort etwas anderes, ist auf `main` direkt gearbeitet
> worden, und DANN wird angehalten.*
>
> Zugleich fällt damit das Fast-Forward weg — deshalb ist `--no-ff` in Schritt 4 keine Vorliebe,
> sondern ab dem zweiten Merge die einzige Möglichkeit.
>
> *Die Anleitung war am ersten Merge geschrieben und hat ihren eigenen Sonderfall zur Regel
> gemacht. Der Planner hat Yama daraufhin eine falsche Erwartung genannt; er hat richtig
> angehalten und nachgefragt.*

**Und die Frage, die keine Zahl beantwortet:** *trägt der Commit, auf den ich merge, ein
Evaluator-Votum?* Wenn nein — **auf den letzten abgenommenen Stand mergen, nicht auf den Kopf.**

---

## Der Merge

```bash
cd /Users/yamanuri/Documents/ticket

# 1. Rueckweg setzen
git tag vor-merge-JJJJ-MM-TT-HHMM main

# 2. Eigener Worktree — der Hauptbaum bleibt unberuehrt.
#    Wichtig: drei Instanzen arbeiten dort. Ein `checkout main` im Hauptbaum
#    zieht ihnen mitten in der Arbeit den Boden weg.
git worktree add ../ticket-main main
cd ../ticket-main

# 3. Stand pruefen
git status
git log -1 --oneline

# 4. Merge auf den ABGENOMMENEN Stand
git merge --no-ff <sha> -m "<was drin ist>"

# 5. Ergebnis pruefen
git log -1 --oneline
git diff <sha> main        # MUSS LEER SEIN — dann wurde byte-identisch uebernommen
```

**Schritt 5 ist der wichtigste.** *Ist der Diff leer, ist der Merge nachweislich sauber — und die
Gates müssen nicht erneut laufen, weil sie auf denselben Bytes schon grün waren.*

---

## Wenn etwas schiefgeht

**Der Rückweg gilt NUR im Merge-Worktree:**

```bash
cd ../ticket-main          # <- ZWINGEND. Im Hauptbaum wirft derselbe Befehl den Tag weg.
git reset --hard vor-merge-JJJJ-MM-TT-HHMM
```

**Ist der Zweig im Hauptbaum verstellt worden**, holt ihn das Reflog zurück:

```bash
cd /Users/yamanuri/Documents/ticket
git reflog show auto/hausplaner-integration | head -5     # den letzten guten SHA ablesen
git reset --hard <dieser-sha>
```

Danach **gegenprüfen, nicht glauben**: Zeilenzahl der Hauptdatei, Vorhandensein der zuletzt
angelegten Module, `rev-list --count main..auto`.

---

## Danach

```bash
cd /Users/yamanuri/Documents/ticket
git worktree remove ../ticket-main     # oder stehen lassen fuer den naechsten Merge
```

**Kein `git push` in dieser Anleitung.** Der Merge ist lokal; Pushen entscheidet Yama getrennt.
**Und niemals auf `upstream`** — das ist ein fremdes Konto.
