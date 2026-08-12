# W-41 · CODE

## Wo der Code wirklich lebt

| Schicht | Datei im Repo | Zweck |
|---|---|---|
| 1–5 | **keine** | *W-41 ist eine Vorgabe. Es gibt keinen Graphen und keine Propagierung.* |

**Am Bau-Stand gemessen, und diesmal genauer als bei meinem ersten Anlauf:**

```text
Kanten · Graph · Propagierung in resources/planner/hausplaner        0
markiereVeraltet  geometry/configuratorPackage.ts                    existiert
  Aufrufer ausserhalb der Tests                                      0
Treffer auf „abhaengig"                                              regeldachneigungAbhaengig-
                                                                     VonMaterial u. a. in
                                                                     dachformVorlagen.ts —
                                                                     Produktmerkmale, keine Kanten
```

> **Ich hatte zunächst behauptet, `markiereVeraltet` sei „die Invalidierung" und W-41s Prämisse
> damit zu weit gefasst.** *Das war zu stark.* **Markieren und propagieren sind zwei Dinge:** *der
> Zustand existiert, die Markierfunktion existiert **ohne Aufrufer**, der Graph und die Propagierung
> existieren nicht.* **Und genau darum geht es in W-41 — die Prämisse trägt.**

## Der Präzedenzfall, der kein Graph ist

```ts
// geometry/configuratorPackage.ts — die vorhandene Gueltigkeitsachse
export function markiereVeraltet<T extends ConfiguratorPackage>(paket: T, jetzt, durch): T {
  if (paket.status !== 'approved' && paket.status !== 'integrated') return paket;
  return { ...paket, status: 'outdated', updatedAt: jetzt, updatedBy: durch };
}
```

**Das ist Schritt 3 aus `2-FUNKTION.md` — MARKIEREN — für genau ein Paket.** *Es fehlt Schritt 2
(welche sind betroffen) und Schritt 4 (fortsetzen).*

> **Und zwei Dinge macht es bereits richtig, die W-41-5 verlangt:** *`updatedAt` ist der
> **Zeitpunkt**, `updatedBy` ist die **Herkunft**.* **Der GRUND — welche Änderung es ausgelöst hat —
> fehlt auch dort.** *Wer den Mechanismus baut, hat hier ein Vorbild für die Form und eine Lücke,
> die er schließen muss.*

## Die Schnittstelle, die gebaut werden soll

```ts
// VORGABE — existiert nicht.
// Was Schritt 2 braucht: eine Struktur, die sagt, WAS auf WAS beruht.
// Diese Struktur ist NICHT erhoben — siehe 7-GRENZEN.md.
// Deshalb steht hier KEINE Signatur: sie zu erfinden waere der schwerere Fehler.
```

> **Hier steht mit Absicht kein Typ.** *Eine Signatur zu schreiben hieße, die Kanten zu erfinden —
> und W-41-4 sagt wörtlich: „Eine erfundene Struktur ist der schwerere Fehler als eine kurze
> Liste."*

## Abhängigkeiten

| Braucht | Warum | Richtung geprüft? |
|---|---|---|
| **W-40** | `outdated` als Zustand — W-41 definiert ihn nicht neu | **ja**, das Register führt `W-41 braucht W-38, W-40` |
| **W-38** | die Fortschrittsachse, neben der W-40 steht | **ja** |
| *die Erhebung der Kanten* | Schritt 2 | **existiert nicht** — das ist die Anschlussliste |

> **W-41 ist damit das einzige der drei Vorgabe-Blätter, dessen Bau an einer ERHEBUNG hängt und
> nicht an einer Entscheidung.** *W-40 wartet auf Yamas Antwort; W-41 wartet darauf, dass jemand
> misst, was auf was beruht.*
