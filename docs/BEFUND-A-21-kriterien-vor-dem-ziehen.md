# Befund zu A-21 — zwei Kriterien, gemessen BEVOR das Blatt gezogen wird

```yaml
auftrag: "A-21"
rolle: "generator"
art: "BEFUND gegen den Auftrag, vor dem Bauen — kein Bau, keine Statusaenderung"
gemessen_am: "12.08.2026"
stand: 92c50794
ich_habe_A-21_NICHT_gezogen: "A-21-7 verlangt A-20 als ABGENOMMEN; A-20 steht auf CODE_FERTIG."
```

> **Warum dieser Befund jetzt kommt und nicht nach dem Ziehen:** *A-21-7 lautet wörtlich „A-20 ist
> abgenommen, **bevor dieses Blatt gezogen wird**. Nachweis: A-20s Zustand **zum Zeitpunkt des
> IN_ARBEIT**."* **Ziehe ich jetzt, ist dieses Kriterium für immer rot** — der Nachweis hängt an
> einem Zeitpunkt, der sich nicht nachholen lässt. *Also ziehe ich nicht und lese stattdessen
> gegen, solange das noch billig ist.*

## Befund 1 — A-21-3 ist wörtlich unerfüllbar, ohne Belege zu vernichten

**Das Kriterium:**

```text
A-21-3  ZURUECKGESTELLT ist abgeschafft: 0 Treffer in docs/STATUS.md, und W-21L
        traegt DECISION_BLOCKED an BEIDEN Orten (Tafelzeile und Datensatz).
```

**Gemessen — `grep -c 'ZURUECKGESTELLT' docs/STATUS.md` → 14, nach Sorte getrennt:**

```text
 2   der ECHTE Zustand von W-21L   die Tafelzeile + das Feld zustand:   <- das will A-21 aendern
 5   IM EIGENEN BLOCK VON A-21     titel: · dor_beleg: · der_kern_des_befunds:
                                   und zwei Zeilen im Fliesstext des dor_beleg
 7   Belege und Fliesstext         zwei vertretungsentscheid: · BEFUND_GEGEN_MICH_der_
                                   dauerlaeufer: · punkt_5_OFFEN_und_das_ist_der_Befund:
                                   eine Vergleichsmessung · eine A-16-Tafelzeilen-Notiz
```

> **Fünf der vierzehn Treffer stehen im Datensatzblock von A-21 selbst** — *darunter sein eigener
> `titel:` („**ZURUECKGESTELLT abschaffen**") und der `dor_beleg:` des Plan-Prüfers.* **„0 Treffer"
> verlangt wörtlich, dass der Auftrag seinen eigenen Titel und seinen eigenen DoR-Beleg löscht.**

**Und die übrigen sieben sind Belege, keine Statusbehauptungen** — *ich habe sie geöffnet und
gelesen, nicht gezählt:* die beiden `vertretungsentscheid:`-Felder zitieren Yamas Anweisung; eine
Zeile ist eine Vergleichsmessung („ZURUECKGESTELLT (W-21L). Zum Vergleich am selben Dokument:
ENTWURF 4 Treffer, BEREIT 9"); eine hält fest, dass eine A-16-Tafelzeile angeglichen wurde.

> **Ohne Zeilennummern, und das ist Absicht.** *Ich hatte sie zuerst notiert und beim Nachprüfen
> schlugen **alle zehn** fehl: `docs/STATUS.md` ist zwischen meiner Messung und der Gegenprobe von
> 6.779 auf 6.815 Zeilen gewachsen, der A-21-Block war um 37 Zeilen gewandert.* **In einer Datei,
> in die fünf Rollen gleichzeitig schreiben, ist eine Zeilennummer kein Beleg, sondern ein
> Verfallsdatum.** *Feldnamen und Inhalte überstehen das; die Zahlen 14 · 2 · 5 · 7 haben nach dem
> Wachstum unverändert gehalten.*

> **Das kollidiert frontal mit A-20-4, das gerade abgenommen wird:** *„Wer nur löscht, ohne zu sagen
> was gegolten hat, vernichtet einen Befund."* **Und mit dem, was der Evaluator in `99fc86cd`
> ausdrücklich bestätigt hat:** *die 17 Meldeblöcke bleiben, weil sie `bau_commit` tragen und Belege
> sind.* **Ein Kriterium, das 0 Treffer über die ganze Datei verlangt, hebt beides auf.**

**Was ich NICHT tue:** *das Kriterium still umdeuten.* **Beide Zahlen stehen hier — 14 gesamt, 2
davon Zustand — und welche Menge A-21-3 meint, entscheidet nicht der Bauende.**

*Ein Vorschlag, ausdrücklich als solcher: „`^zustand: ZURUECKGESTELLT` und Tafelzeilen: 0 Treffer"
träfe genau die 2 und ließe die 12 Belege stehen. Das ist die Formulierung, die A-20-3 benutzt hat,
und sie war dort tragfähig.*

## Befund 2 — A-21-6 verlangt denselben Nachweis, der eben verworfen wurde

**Das Kriterium:**

```text
A-21-6  KEIN anderer Auftragszustand wurde geaendert. Nachweis: git diff auf
        docs/STATUS.md zeigt Zustandsaenderungen ausschliesslich bei W-21L.
```

> **`git diff` misst den ARBEITSBAUM.** *Nach dem Commit ist er zwangsläufig leer — die Messung wäre
> auch dann grün, wenn zwanzig fremde Zustände geändert worden wären.*

**Das ist wörtlich der Mangel, den der Evaluator soeben an meinem A-20-5 verworfen hat**
(`99fc86cd`: *„A-20-5 sachlich erfuellt, Nachweis falsch"*). **Ich habe ihn in `92c50794` ersetzt
durch die Messung am Commit:**

```text
git show <bau-commit> -- docs/STATUS.md | grep -E '^[-+](zustand|ballbesitz):'
git show <bau-commit> -- docs/STATUS.md | grep -E '^[-+]\| \*\*[A-Z]+-?[0-9]+'
```

*Für A-20 ergab das: 4 Zustandszeilen, 1 Tafelzeile, alle dem eigenen Auftrag gehörend, 0 fremde.*

> **E1 sagt genau das, und E1 zu verankern ist A-21s eigener Auftrag.** *Ein Blatt, das E1 ins
> Regelwerk schreibt und sich selbst am Arbeitsbaum misst, widerlegt sich im eigenen
> Kriterienblock.*

## Was ich nicht gemessen habe

**Die übrigen fünf Kriterien** (A-21-1, -2, -4, -5, -7) *habe ich gelesen, aber nicht geprüft* —
**ihre Rot-Lage hat der Plan-Prüfer in `45babc3a` selbst gemessen** (E1 0 Treffer, E3 0, ERLEDIGT
und VORLAGE in §3 0), *und ich habe keinen Anlass, das zu wiederholen.*

**Nichts geändert, nichts geschnitten, kein fremder Pfad angefasst.** *Der Befund gehört dem
Planner und dem Plan-Prüfer; ich baue nach ihrer Entscheidung.*
