# W-41 · Abhängigkeitsgraph — FUNKTION

## Die Grenze zu W-40 — scharf, und sie ist der Grund für zwei Blätter

```text
W-40 sagt   DASS es outdated gibt und was der Zustand BEDEUTET.
W-41 sagt   WANN er eintritt, WORAUF er sich fortsetzt, und WAS dabei erhalten bleibt.
```

> **W-41 definiert `outdated` NICHT neu — es verweist auf W-40.** *Ohne diese Grenze schreiben beide
> Blätter dieselbe Sache zweimal, und dann gilt, was A-20 für den Zustand festgestellt hat:* **zwei
> Orte für eine Wahrheit, und beide veralten unabhängig.**

**Praktisch heißt das:** *wer wissen will, was `outdated` bedeutet, liest
`W-40-gueltigkeitsstatus/2-FUNKTION.md`.* **Hier steht nur, unter welchen Umständen er eintritt.**

## Eingabe

| Was | Typ | Pflicht | Prüfung |
|---|---|---|---|
| **noch nicht festgelegt** | — | — | *nichts davon ist erhoben* |

> **Die Eingabe eines Abhängigkeitsgraphen sind seine KANTEN — und die gibt es nicht.** *Weder als
> Struktur noch als Erhebung.* **Am Bau-Stand gemessen:**

```text
Kanten / Graph / Propagierung in resources/planner/hausplaner   0
markiereVeraltet (geometry/configuratorPackage.ts)              existiert, 0 Aufrufer
                                                                ausserhalb der Tests
Treffer auf „abhaengig"                                          Produktmerkmale in
                                                                dachformVorlagen.ts, keine Kanten
```

**Markieren und propagieren sind zwei Dinge.** *Der Zustand existiert, die Markierfunktion existiert
ohne Aufrufer — der Graph und die Propagierung nicht.*

## Verarbeitung — der Mechanismus als Vorgabe

```text
1  ANLASS       eine Angabe aendert sich.
2  ERMITTELN    welche abgeleiteten Werte auf ihr beruhen.        <- braucht die KANTEN
3  MARKIEREN    jeder davon wird outdated (W-40).
4  FORTSETZEN   was auf einem outdated-Wert beruht, wird ebenfalls outdated.
5  ERHALTEN     der alte Wert, der Zeitpunkt und der Grund bleiben.
```

> **Schritt 2 ist der einzige, der etwas braucht, das es nicht gibt.** *Die Schritte 1, 3, 4 und 5
> sind Regeln; Schritt 2 ist eine **Erhebung**, und sie steht aus.* **Deshalb ist W-41 eine Vorgabe
> und kein Bauplan.**

**Schritt 4 ist die eigentliche Härte:** *Invalidierung muss sich **fortsetzen**, sonst ist sie eine
einstufige Markierung.* **Ohne ihn bleibt ein Wert gültig, dessen Grundlage schon ungültig ist —
und das ist eine Lüge zweiter Ordnung.**

## Was bei einer Invalidierung ERHALTEN bleiben muss

**Mindestens drei Dinge, und ohne sie ist die Zusage gegen stille Löschung nicht prüfbar:**

| Was | Warum |
|---|---|
| **der alte Wert** | *sonst ist es eine Löschung mit anderem Namen* |
| **der Zeitpunkt** | *ohne ihn lässt sich nicht sagen, ob die Invalidierung vor oder nach einer Bestätigung lag* |
| **der Grund** | *welche Änderung sie ausgelöst hat — sonst ist `outdated` eine Absage ohne Erklärung* |

> **Der Grund ist der Punkt, an dem sich „propagieren" von „löschen" unterscheidet.** *Ein Wert, der
> als ungültig markiert ist und sagt **warum**, ist eine Auskunft. Ein Wert, der verschwindet, ist
> keine.*
>
> **Und die Quelle gibt für den Grund nichts her** — *„niemals stille Löschung" sagt, dass nichts
> verschwindet, nicht dass jemand erfährt, warum.* **Das steht in `7-GRENZEN.md`; hier steht es als
> Vorgabe, weil ohne den Grund das Kriterium W-41-5 nicht prüfbar wäre.**

## Ausgabe

| Was | Wohin |
|---|---|
| der Zustand `outdated` je betroffenem Wert | **W-40s Achse** — nicht eine eigene |
| die Kette der Betroffenen | *offen — hängt an der Erhebung aus Schritt 2* |

## Schichtzuordnung

| Schicht | W-41 | Begründung |
|---|---|---|
| 1 Domäne | **vermutlich** | Abhängigkeiten bestehen zwischen Dokumentinhalten, nicht zwischen Ansichten |
| 2 Geometrie | **nein** | keine Rechnung — siehe `3-FORMELN.md` |
| 3 Anwendung | **offen** | wo der Mechanismus läuft, ist nicht entschieden |
| 4/5 Oberfläche | **ja, mittelbar** | ein `outdated`-Wert muss sichtbar sein, sonst wirkt die Regel nicht |

**Die Unsicherheit ist benannt und nicht überspielt** — *dieselbe Lage wie bei W-40: die Quelle
liefert den Satz, nicht die Verortung.*

## Scope

```text
W-41 IST      die VORGABE des Mechanismus: wann Invalidierung eintritt, wie sie sich
              fortsetzt, und die harte Zusage, dass dabei nichts still verschwindet.
              Dazu die Anschlussliste: welche Abhaengigkeiten zuerst zu ERHEBEN sind.

W-41 IST NICHT
              die Definition von outdated — die gehoert W-40
              der BAU — kein Produktivcode
              eine erfundene Abhaengigkeitsstruktur
```
