# W-26 · Dachschichten (Aufbau) — GRENZEN

## Der Befund in zwei Sätzen

**Der Dachaufbau ist bewusst nicht gebaut, und die Entscheidung ist sieben Mal dokumentiert und
einmal bewacht.** *Was bleibt, ist ein Vertrag von dreizehn Feldern, den jede Vorlage mitträgt und
den außerhalb der Vorlagendatei niemand liest.*

```text
Entscheidung   sieben Modulkoepfe: „KEINE Dacheindeckung"
Waechter       dachformVorlagen.test.ts:561 ueber ALLE Vorlagen
Vertrag        13 Felder · ausserhalb gelesen: 0 · davon wirksam: 2 (in derselben Datei)
Rest           eindeckungPasstZuKategorie — gebaut, 4 Zusagen, 0 produktive Aufrufer
```

## Warum das KEIN Mangel ist — und wo trotzdem ein Risiko liegt

**Kein Anwender bekommt eine falsche Auskunft.** *Die Neigungswarnung nennt sich selbst „Richtwert"
und sperrt nicht; die ungelesenen Felder behaupten nichts, weil sie niemand sieht.*

> ***Das Risiko ist ein anderes und es ist leise:*** *elf gepflegte Felder je Vorlage sind
> Pflegeaufwand ohne Gegenwert.* **Wer eine neue Dachform anlegt, füllt `unterdeckungKlasse`,
> `firstausbildung`, `konterlattungMm` — und niemand merkt, wenn er sie falsch füllt.** *Ein Feld,
> das nie gelesen wird, wird auch nie widerlegt.*

**Beim Flachdach wird der Vertrag mit `konterlattungMm: [0, 0]` gefüllt** (`:1410`) — *ein Wert,
den es dort fachlich nicht gibt.* **Das ist der sichtbare Rand desselben Problems:** *der Vertrag
verlangt Felder, die nicht überall eine Bedeutung haben.*

## Was hier NICHT entschieden wird

**Ob der Dachaufbau je gebaut werden soll.** *Er ist Produktwissen — Deckmaß, Lattung und
Regeldachneigung hängen am gewählten Produkt, und das Haus hat dafür ausdrücklich eine separate
Produktauswahl.*

> **Festgehalten, nicht entschieden:** *ob die elf ungelesenen Felder ANGESCHLOSSEN oder ENTFERNT
> werden, ist eine fachliche Weiche.* **Beides ist vertretbar, das Dritte — sie weiter zu pflegen,
> ohne sie zu lesen — ist die einzige Möglichkeit, die keinen Grund hat.**

## Drei Wege, ohne Empfehlung

```text
A  anzeigen         deckungsHinweis und empfohleneEindeckung erreichen die
                    Oberflaeche. Kleinster Schritt, kein fachliches Risiko,
                    macht vorhandene Pflege sichtbar.

B  ausduennen       die elf ungelesenen Felder entfallen oder werden optional.
                    Beendet die Pflege ohne Gegenwert — und ist eine
                    LOESCHUNG, gehoert also Yama.

C  anschliessen     unterdeckungKlasse schaerft die RDN-Warnung, empfohlene-
                    Eindeckung belegt die Produktauswahl vor. Groesster Nutzen,
                    beruehrt die Belegkette und die Bauregel „deckungsneutral".
```

> **Weg A ist der einzige, der heute ohne Entscheidung möglich wäre.** *Er ändert keine Zahl und
> keine Auskunft — er zeigt einen Text, der zum Anzeigen geschrieben wurde.*

## Nachbarschaft — nur abgegrenzt

```text
W-07   Dach aus Kontur      liefert Form und Neigung, gegen die hier gewarnt wird
W-29   Dachdurchdringungen  „Dachaufbauten" — aufgesetzte Bauteile, NICHT Schichten
W-28   Dachentwaesserung    eigenes Blatt, ebenfalls fast leer, andere Ursache:
                            dort fehlt die Entscheidung, hier ist sie getroffen
FG-02  abwassergefaelle     Vorbild fuer Richtwert-mit-Vorbehalt
```

> ***Der Unterschied zwischen W-26 und W-28 ist der wertvollste Satz dieses Blattes:*** *beide sind
> im Register „leer".* **W-28 ist leer, weil niemand entschieden hat. W-26 ist leer, weil jemand
> entschieden hat.** *Das eine wartet auf eine Weiche, das andere hat sie hinter sich — und ein
> Register, das beide gleich führt, verwischt genau das.*
