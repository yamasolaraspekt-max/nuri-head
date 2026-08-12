# W-35 · Konfigurator-Dialog — ZWECK

> **ABLESUNG.** *Der Code existiert: [`app/ConfigWizard.tsx`](../../../../../resources/planner/hausplaner/app/ConfigWizard.tsx), **271 Zeilen**, am Bau-Stand gezählt.* **Es ist keine Abwesenheit zu
> messen** — *anders als bei W-40, W-42 und W-15.*

## Welches Problem des Anwenders löst dieses Werkzeug?

**Der Anwender will ein Bauteil aussuchen und einstellen, ohne den Katalog, die Bauartenliste und die
Maßfelder selbst zu finden.** *Der Dialog führt ihn in fünf Schritten durch Bauart, Maße, Material,
Prüfung und Übernehmen — mit einer Live-Vorschau, die bei jedem Schritt mitläuft.*

## VIER Arten, nicht drei

```ts
ConfigWizard.tsx:23
export type KonfigArt = 'fenster' | 'tuer' | 'treppe' | 'heizkoerper';
```

**Vier.** *Am Bau-Stand gelesen, Zeile geöffnet.*

> **Die Registerzeile sagte drei** — *`W-35 | Konfigurator-Dialog Fenster·Tür·Treppe`, und
> `heizkoerper` fehlte.* **Diese Zeile stammt aus meiner eigenen Erhebung; sie war von Anfang an
> unvollständig.** *Korrigiert im Rahmen dieses Auftrags (W-35-1), mit der Zeile als Beleg.*

**Und die Ursache ist keine Schlamperei, sondern eine Quelle** — *sie steht im Dateikopf, den ich
damals gelesen habe:*

```text
ConfigWizard.tsx:2
„ConfigWizard (v9) — geführter Konfigurator-Dialog für Fenster/Tür/Treppe."
                                                      ^^^^^^^^^^^^^^^^^^^ DREI
```

> **Der Dateikopf trägt denselben Fehler wie die Registerzeile, und er ist der ältere.** *Wer den
> Kopf liest und die Zeile schreibt, erbt ihn.* **H-6 in seiner unangenehmen Form: ich habe nicht
> „ein Wort für einen Beleg gehalten", ich habe einen ganzen Satz für einen Beleg gehalten — und der
> Typ vier Zeilen weiter unten hätte widersprochen.**

**Ein Test hält die Vier bereits fest:**

```text
__tests__/konfiguratorEhrlich.test.ts:126
assert.match(wizard, /export type KonfigArt = 'fenster' \| 'tuer' \| 'treppe' \| 'heizkoerper'/);
```

*Die Zusage der Startseite ist an dieselbe Zahl gebunden (`:119-120`): „Fenster, Türen, Treppen und
Heizkörper setzt der Experte ins Gebäude".* **Die Vier war also seit AUF-74 an zwei Stellen gesichert
— nur nicht im Register.**

## Wann greift der Anwender danach?

| Lage | Was er tut |
|---|---|
| **kein Gebäude offen** *(`standalone`, Vorbelegung `true`, `:45`)* | er konfiguriert autark; am Ende entsteht ein Paket — Paketliste **und/oder** Datei |
| **Gebäude offen, Heizkörper oder Treppe** | das Bauteil geht **direkt ins Modell** (`:184`, `:205`) |
| **Gebäude offen, Wand ausgewählt, Fenster oder Tür** | die Öffnung wird **in die gewählte Wand** gesetzt (`:226`) |

**Der Dialog ist damit beides:** *ein eigenständiges Werkzeug und ein Eingabeweg ins Gebäudemodell.*
**Welcher der beiden greift, entscheidet nicht der Anwender, sondern die Lage** — *ob eine Szene
geladen ist und was ausgewählt wurde.*

## Was er dem Anwender NICHT abnimmt

**Das Bauteil landet im autarken Fall nicht im Gebäude.** *Der Dialog sagt das selbst, an vier
Stellen (`:147`, `:148`, `:164`, `:262`): „Ins Gebäude kommt das Bauteil über den Experten — dort
eine Wand wählen."*

> **Diese Sätze sind das Ergebnis von AUF-74, und `konfiguratorEhrlich.test.ts` hält sie fest.**
> *Vorher stand dort „später verlustfrei ins Projekt übernehmbar" — ein Versprechen auf etwas, das es
> nicht gab.* **Wer dieses Blatt liest, soll wissen: die Ehrlichkeit dieser Fläche ist erkämpft und
> steht unter Bewachung.**
