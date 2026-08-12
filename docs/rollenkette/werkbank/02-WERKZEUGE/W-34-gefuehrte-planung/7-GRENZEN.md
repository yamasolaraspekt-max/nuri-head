# W-34 · Geführte Planung — GRENZEN

> **Dieses Blatt ist Pflicht.**

## Die härteste Grenze: sechs von elf Schritten können nichts bestätigen

**`SCHRITTE_OHNE_GRUNDLAGE` (`fahrschritte.ts:56-68`) — alle sechs, je mit dem Satz aus dem Code:**

| Schritt | Was fehlt, wörtlich |
|---|---|
| **Projektgrundlagen** | *„Bauherr, Adresse und Grundstück stehen im CRM, nicht im Gebäudemodell. Solange der Planer sie nicht liest, kann dieser Schritt nichts bestätigen."* |
| **Import oder Grundriss** | *„Ob eine Vorlage importiert und ihr Maßstab bestätigt wurde, führt das Dokument nicht. Sichtbar ist nur, ob Wände vorhanden sind."* |
| **Räume und Einrichtung** | *„Raumnutzung und Möblierung sind im Schema nicht als Eigenschaft geführt; Räume entstehen abgeleitet aus der Raumerkennung."* |
| **Küche und Bad** | *„Küchen- und Bad-Ausstattung hat keine eigene Objektart; nur Sanitärobjekte sind zählbar."* |
| **Prüfung und Koordination** | *„Es gibt keinen gespeicherten Prüflauf und keine Freigabe im Dokument."* |
| **Dokumentation und Rendering** | *„Erzeugte Pläne, Listen und Renderings werden nicht im Dokument vermerkt."* |

```text
sechs von elf         Object.keys(SCHRITTE_OHNE_GRUNDLAGE).length  = 6
                      Eintraege des return-Arrays in ableitenSchritte = 11
```

**Der Dateikommentar, der begründet, warum sie beieinander stehen** — *`:52-54`, wörtlich:*

> *„Sie stehen hier zusammen und nicht verstreut, damit die Lücke zählbar ist:
> `SCHRITTE_OHNE_GRUNDLAGE.length` ist die Länge der Rückgabe-Liste an den Planner. Jeder Eintrag
> sagt, **was es bräuchte** — das ist der Anfang des nächsten Postens."*

> **Und weil das Kriterium ein WÖRTLICHES Zitat verlangt, muss ich sagen, dass ein Satz darin nicht
> trägt** — *sonst trüge dieses Blatt eine Falschaussage weiter, nur in Anführungszeichen:*
>
> ```text
> :56  export const SCHRITTE_OHNE_GRUNDLAGE: Readonly<Record<string, string>> = {
> ```
>
> **`SCHRITTE_OHNE_GRUNDLAGE` ist ein `Record`, kein Array — `.length` darauf ist `undefined`.**
> *Und selbst wenn es ginge: die Rückgabe-Liste an den Planner hat **elf** Einträge, nicht sechs.*
> **Zwei Fehler in einem Satz.**
>
> *Wirkung: **keine**. Der Ausdruck steht nur im Kommentar; gemessen wird `SCHRITTE_OHNE_GRUNDLAGE`
> außerhalb der Datei allein in `fahrschritte.test.ts`, und dort korrekt mit
> `Object.keys(...).length` (`:91`, `:98`).* **Der Gedanke des Kommentars stimmt — „die Lücke ist
> zählbar" — nur sein Beleg nicht.** *Ein Befund am Kommentar, kein Befund am Verhalten; die
> Berichtigung gehört nicht zu einer Ablesung.*

**Das ist kein Mangel des Werkzeugs, sondern seine Leistung.** *Statt sechs Schritte grün zu zeigen,
sagt jeder von ihnen, welche Angabe im Gebäudemodell fehlt — und ist damit der Anfang eines
möglichen nächsten Postens.*

## Was dieses Werkzeug NICHT kann

| Fall | Warum nicht | Was der Anwender stattdessen sieht |
|---|---|---|
| bestätigen, dass Bauherrendaten erfasst sind | stehen im CRM, nicht im `SceneDocument` | `Offen` + der Satz, was fehlt |
| bestätigen, dass ein Import maßstäblich bestätigt wurde | führt das Dokument nicht | `Offen`; nur die Wandzahl ist messbar |
| Raumnutzung oder Möblierung bewerten | im Schema keine Eigenschaft | `Offen`; nur erkannte Räume sind zählbar |
| Küchen-/Bad-Ausstattung bewerten | keine eigene Objektart | `Offen`; nur Sanitärobjekte sind zählbar |
| einen Prüflauf oder eine Freigabe anzeigen | wird nicht gespeichert | `Offen` |
| erzeugte Pläne/Listen/Renderings anzeigen | wird nicht vermerkt | `Offen` |

## Die Absagekette

**Sie ist kurz, weil W-34 nicht scheitern kann** — *es liest und zeigt:*

```text
Schicht 1  das Dokument fuehrt die Angabe NICHT
        ↓
Schicht 3  ableitenSchritte gibt den Schritt mit LEERER Pruefpunktliste zurueck
        ↓  statusAus(:44): checks.length === 0  ->  'open'
Schicht 5  die Plakette zeigt „Offen", der Hinweis nennt die fehlende Angabe
```

| Fall | „Fehlername" | Wer fängt | Anwendertext steht in |
|---|---|---|---|
| keine Modellgrundlage | **kein Fehler** — ein leerer Schritt | niemand; es wird nichts geworfen | `4-BEDIENUNG.md`, und im Code selbst |

> **Es gibt kein `catch`, weil es nichts zu fangen gibt.** *Der teuerste Fehler des Projekts — eine
> geschluckte Absage — braucht einen Fehlerpfad.* **W-34s Absage ist kein Fehler, sondern ein
> Zustand: `open` mit einem Satz.** *Das ist die stärkere Bauform, weil sie nicht verschluckt werden
> kann.*

## Fänger-Prüfung

- [x] **Jeder „Fehlerpfad" ist durch einen Test belegt** — `fahrschritte.test.ts` K6 prüft für alle
      sechs: Status `open`, `checks` leer, Hinweis länger als 40 Zeichen
- [x] **Kein `catch { }` ohne Weiterreichen** — *gemessen: `fahrschritte.ts` enthält kein `catch`*
- [x] **Kein stilles `return` bei ungültiger Eingabe** — *`ableitenSchritte(null)` ist ein
      **gültiger** Aufruf und liefert elf offene Schritte; `schrittTitel()` `:201` nutzt genau das*

## Bekannte Ungenauigkeiten

| Größe | Abweichung | Ab wann stört es |
|---|---|---|
| `bebauteGeschosse` | zählt nur `nodes`, `roofs`, `ceilings` (`:84-88`) | **sobald eine vierte Liste hinzukäme**, die ein Geschoss tragen kann — ein solches Geschoss zählte dann als unbebaut. *Heute gibt es diesen Fall nicht.* |

## Was später kommen könnte

*Absichtlich weggelassen, damit es nicht als Fehler gemeldet wird:*

```text
- die sechs Luecken schliessen   -> je eine eigene Modell-Erweiterung, KEIN W-34-Posten
- Fortschritt in Prozent          -> waere eine Zahl ueber Schritte, die nichts bestaetigen koennen
- Schritte ueberspringen/sperren  -> W-34 zeigt an, es fuehrt nicht
```

## Offener Anschluss

**Die sechs Lücken sind eine Liste möglicher nächster Posten, die nicht erfunden wurde, sondern im
Code steht.** *Sie gehört dem Planner vorgelegt — **nach** dieser Ablesung, nicht vorher: erst wenn
das Blatt steht, ist die Liste belegt.*
