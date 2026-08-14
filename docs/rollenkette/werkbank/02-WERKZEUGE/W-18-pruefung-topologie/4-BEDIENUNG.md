# W-18 · Topologie prüfen — BEDIENUNG

## Es gibt nichts zu bedienen — und das ist die Bauform

**Die Prüfung läuft mit, während der Anwender zeichnet.** *Kein Knopf, kein Menüeintrag, kein
eigener Arbeitsschritt.* **Was er sieht, ist eine Statuszeile, die sich mit jedem Punkt ändert.**

## Der Text, den er liest — `konturStatusText()` (`kontur.ts:156`)

```text
kein Punkt gesetzt        'Klick setzt den ersten Punkt der Kontur'
n Punkte gesetzt          'n Punkte · Klick auf den ersten schließt …'
ein Fehler liegt vor      KONTUR_MELDUNG[fehler]   — der Satz zum Grund
gerade geschlossen        'Kontur geschlossen — n Punkte. Klick setzt den ersten
                           Punkt der naechsten …'
```

> ***Der letzte Fall ist der bemerkenswerte, und der Code begründet ihn selbst*** (`:165-166`):
>
> *„**Der Erfolg wird BENANNT.** Ohne diesen Satz sieht ein geschlossener Zug genauso aus wie ein
> verworfener: die Vorschau ist in beiden Fällen weg."*
>
> **Zwei Zustände mit demselben Bild.** *Ohne die Meldung wüsste der Anwender nicht, ob seine Kontur
> angekommen ist oder verlorenging — und beides sähe gleich aus.*

**Auch die Einzahl ist gepflegt** (`:174`): `'1 Punkt'` gegen `'2 Punkte'`. *Eine Kleinigkeit, die
auffällt, wenn sie fehlt.*

## Die drei Fehlermeldungen, wörtlich

| Grund | Satz |
|---|---|
| `zu-wenig-punkte` | „Eine Fläche braucht mindestens drei Punkte — **setze noch einen**." |
| `selbstschnitt` | „Die Kontur überschneidet sich selbst — **zieh den letzten Punkt so**, dass sich keine …" |
| `keine-flaeche` | „Alle Punkte liegen auf einer Linie — das umschließt keine Fläche." |

> ***Zwei der drei nennen den nächsten Handgriff*** — *„setze noch einen", „zieh den letzten Punkt".*
> **Der dritte nennt ihn nicht, weil es keinen einzelnen gibt:** *wenn alle Punkte auf einer Linie
> liegen, hilft kein bestimmter Zug, sondern nur ein anderer Verlauf.*

## Die Reihenfolge der Prüfung ist Bedienung, nicht Technik

**Zuerst „zu wenig Punkte", dann Selbstschnitt, dann Fläche** (`pruefeKontur`, Begründung in
`:132-133`).

> *Bei zwei Punkten wäre „umschließt keine Fläche" formal richtig und für den Anwender wertlos — er
> ist ja noch mitten im Zeichnen.* **Die Reihenfolge sorgt dafür, dass immer der Satz erscheint, der
> zum Stand der Arbeit passt.**

## Abbruch

**Verwerfen ist jederzeit möglich und hinterlässt nichts** — *die Prüfung hält keinen Zustand.*
**Und der Unterschied zwischen „verworfen" und „geschlossen" ist am Text erkennbar**, nicht am Bild
(s. o.).
