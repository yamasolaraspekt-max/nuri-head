# W-06 · Geschoss verwalten — BEDIENUNG

## Der Einstieg: ein Knopf, der die wichtigste Angabe trägt

**Auf dem Knopf steht die Kurzfassung** — `kurzfassung()` (`geschossStapel.ts:94`):

```text
mit aktivem Geschoss   'Erdgeschoss · ±0 mm · 1 von 3'
ohne aktives Geschoss  '3 Geschosse'
```

*Der Titel des Knopfs nennt den ganzen Umfang:* **„Geschosse — wechseln, anlegen, umbenennen,
löschen"** (`GeschossFlaeche.tsx:113`).

> **Warum überhaupt ein Knopf mit Text und kein Dropdown:** *vorher standen dreizehn Bedienelemente
> in einer Zeile, und die folgenreichste Handlung des Programms — **ein angelegtes Geschoss
> entsperrt 34 Werkzeuge** — steckte in einem 111-px-Select zwischen „Rückgängig" und „Speichern".*

## Die Fläche: drei Teile in einer festen Reihenfolge

```text
1 · DER STAPEL      von OBEN nach UNTEN, so wie ein Gebaeudeschnitt gelesen wird.
                    Je Zeile ein KNOPF (kein Select), Titel:
                    '<Name> — Hoehenlage <±0 mm>'          (GeschossFlaeche.tsx:90)
                    Das aktive Geschoss ist hervorgehoben.
2 · UMBENENNEN      EIN Textfeld, sichtbar beschriftet.
3 · VERWALTUNG      + Anlegen · Duplizieren · − Loeschen   (:160-166)
```

**Der Name steht genau EINMAL.** *Vorher zeigten Select und Textfeld denselben Wert nebeneinander —
„ein Feld, das niemand als solches erkannte, direkt neben einem Select mit demselben Wert"
(`GeschossFlaeche.tsx:13-15`).* **Der Stapel ist eine Liste von Knöpfen; umbenannt wird im einen Feld
darunter.**

## Die drei Verwaltungsknöpfe, mit ihren Titeln am Code gelesen

| Knopf | Titel | Bemerkung |
|---|---|---|
| **+ Anlegen** | „Neues Geschoss über dem obersten anlegen" | *über dem **obersten**, nicht über dem aktiven* |
| **Duplizieren** | „Aktuelles Geschoss als Vorlage duplizieren — Wände, Öffnungen …" | der Weg in `dupliziereGeschoss()` |
| **− Löschen** | s. u. | **gesperrt, wenn nur ein Geschoss da ist** |

**Löschen ist die einzige gesperrte Handlung, und die Sperre erklärt sich selbst** (`:165`):

```text
s.anzahl <= 1   ->  disabled, Titel: 'Das letzte Geschoss kann nicht geloescht werden'
sonst           ->  Titel: 'Aktives Geschoss loeschen (muss …)'
```

> **Der Titel nennt den GRUND, nicht nur den Zustand.** *Ein ausgegrauter Knopf ohne Begründung ist
> eine Sackgasse; hier steht im selben Moment, warum.* Die Ausgrauung nutzt die geteilten Marken
> `GESPERRT_DECKKRAFT` und `GESPERRT_ZEIGER` (`:27`) — **dieselbe Optik wie überall sonst, keine
> Sonderlösung.**

## Der ZWEITE Bedienweg: die Befehlspalette

**Die Geschossnavigation ist nicht nur über die Fläche erreichbar.** *`app/dashboard/palette.ts`
führt den Stapel als eigene Rubrik:*

```text
palette.ts:38    import type { Stapel } from './geschossStapel'
palette.ts:94    stapel?: Stapel | null            — eine der Quellen der Palette
palette.ts:144   'Geschosse: Reihenfolge des Stapels — von oben nach unten'
palette.ts:145   art: 'geschoss', label = Name, zusatz = hoehenLabel
```

**Gesucht wird über drei Felder** (`:146`): **Name, id und Höhenlage.** *Wer `2 700` tippt, findet
das Geschoss über seine Höhe — auch wenn er den Namen nicht weiß.*

> **Dieselbe Reihenfolge wie in der Fläche, und derselbe `hoehenLabel`-Text als Zusatz.** *Zwei
> Bedienwege, eine Datenquelle — es gibt keinen zweiten Ort, an dem die Höhe formatiert wird.*

## Abbruch

**Die Fläche hängt am geteilten Escape-Stapel** (`useEscapeEbene`, `GeschossFlaeche.tsx:31`) — *Esc
schließt sie, und zwar in derselben Rangfolge wie jede andere Ebene.* **Kein eigener
Tastatur-Sonderweg.**
