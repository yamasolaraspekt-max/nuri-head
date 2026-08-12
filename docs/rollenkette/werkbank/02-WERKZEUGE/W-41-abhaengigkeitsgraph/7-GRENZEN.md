# W-41 · Abhängigkeitsgraph — GRENZEN

> **Dieses Blatt ist Pflicht — und bei W-41 ist es das wichtigste von allen.** *W-41 ist die
> **dünnste** der drei Vorgaben, und wer das nicht liest, hält sie für eine Erhebung.*

## Die Quelle führt den Abhängigkeitsgraphen unter NICHT GEMESSEN

**Wörtlich aus `docs/BERICHT-PROZESSEBENE-DREI-FRAGEN.md`, am Bau-Stand gelesen:**

```text
:147   „Ob es einen Abhaengigkeitsgraphen gibt. Ich habe nach status/revision gesucht,
        nicht nach Kanten zwischen Bauteilen."

:191   nicht_gemessen: „Inhalte der elf Schritte · Fortschritt je Geschoss ·
        Abhaengigkeitsgraph · der ConfigWizard-Test"
```

**Und der Verfasser sagt selbst, warum er es so aufgeschrieben hat:**

> *„Vier ungemessene Punkte, ausdrücklich benannt. **Nach fünf Messfehlern an zwei Tagen schreibe
> ich lieber vier Lücken hin als eine Vermutung.**"*

> **Damit hat W-41 genau drei Grundlagen:** *einen Satz aus dem Register, `outdated` als Anschluss
> an W-40 — und **keine erhobene Abhängigkeitsstruktur**.* **Das Blatt benennt diese Lage, statt sie
> zu überspielen.**

## Am Bau-Stand nachgemessen — und meine eigene erste Aussage war zu stark

```text
Kanten · Graph · Propagierung in der Insel        0
markiereVeraltet, Aufrufer ausserhalb der Tests   0
Treffer auf „abhaengig"                            Produktmerkmale in dachformVorlagen.ts
                                                   (regeldachneigungAbhaengigVonMaterial u. a.)
                                                   und ein useMemo-Kommentar — KEINE Kanten
```

> **Ich hatte zunächst gemeldet, W-41s Prämisse „es gibt keinen Code" sei zu weit gefasst, weil
> `markiereVeraltet` die Invalidierung sei.** *Genauer gemessen war das zu stark.* **Markieren und
> propagieren sind zwei Dinge** — *der Zustand existiert, die Markierfunktion existiert ohne
> Aufrufer, der Graph und die Propagierung nicht.* **Die Prämisse trägt für den eigentlichen
> Gegenstand.**

## Die Anschlussliste — als FRAGE, nicht als Struktur

**Die Leitfrage lautet: welche Größe hängt an welcher?** *Was unten steht, ist entweder mit
Fundstelle **belegt** oder ausdrücklich als **Kandidat** gekennzeichnet. Eine erfundene Struktur
wäre der schwerere Fehler als eine kurze Liste.*

| Kante | Stand | Beleg |
|---|---|---|
| **Dachfläche → PV-Belegung** | **BELEGT** | `geometry/pvBelegung.ts:10-14` — `pvSchnellBelegung(e: PvEingabe)` nimmt `dachLaenge` und `dachBreite`, also die Maße der Dachfläche |
| Dachkontur → Dachflächen | **Kandidat** | *nicht gemessen* |
| Geschossgeometrie → Dachkontur | **Kandidat** | *nicht gemessen* |
| Dachflächen → Stückliste / Mengen (W-20) | **Kandidat** | *nicht gemessen* |
| Öffnungen → Wandflächen | **Kandidat** | *nicht gemessen* |
| Konfigurationspaket → Gebäudemodell | **Kandidat**, und ein besonderer | *der Schreibpfad ist selbst noch nicht gebaut — W-42* |

> **Genau EINE Kante ist heute belegt, und sie ist ausgerechnet die, an der Yamas L-9 hängt:** *„PV
> erst nach bestätigter Dachgeometrie".* **Die Bedingung liefert W-40 (`confirmed`), die Kante
> liefert dieser Beleg — der Mechanismus dazwischen fehlt, und das ist W-41.**
>
> **Die übrigen fünf sind Kandidaten und keine Ergebnisse.** *Sie stehen hier, damit die Erhebung
> weiß, wo sie anfangen kann — nicht, damit jemand sie für gemessen hält.*

## Was die Quelle sonst nicht hergibt

```text
DER GRUND einer Invalidierung   „niemals stille Loeschung" sagt, dass nichts verschwindet —
                                nicht, dass jemand erfaehrt WARUM. Als Vorgabe in
                                2-FUNKTION aufgenommen, weil W-41-5 sonst nicht pruefbar
                                waere; als Belegluecke steht es hier.

RUECKNAHME                      ob eine Invalidierung zurueckgenommen wird, wenn die
                                Aenderung rueckgaengig gemacht wird, steht nirgends.

ZYKLEN                          ob die Struktur kreisfrei waere, ist nicht gemessen —
                                sie ist ja nicht erhoben. Siehe 3-FORMELN.

WO der Mechanismus laeuft       Domaene, Anwendung, Speicher? Die Quelle sagt es nicht.

GRANULARITAET des Zeitpunkts    Sekunde, Revision oder Vorgang? Nicht belegt.
```

## Was dieses Blatt ausdrücklich NICHT entscheidet

| Frage | Gehört |
|---|---|
| Welche Abhängigkeiten es wirklich gibt | **eine Erhebung** — der erste Schritt zum Bau |
| Ob `approved` dasselbe ist wie `confirmed` | **Yama** — die offene Frage aus W-40 |
| Wie fein der Zeitpunkt sein muss | **Bau-Entscheidung** |

> **W-41 ist das einzige der drei Vorgabe-Blätter, dessen Bau an einer ERHEBUNG hängt und nicht an
> einer Entscheidung.** *W-40 wartet auf Yamas Antwort; W-41 wartet darauf, dass jemand misst, was
> auf was beruht.*

## Was später kommen könnte

```text
- die ERHEBUNG der Kanten            -> eigener Auftrag, die Liste oben ist ihr Anfang
- der BAU des Mechanismus            -> Vorgaben in 6-PRUEFUNG als B-1..B-5
- eine Anzeige der Invalidierungskette -> erst sinnvoll, wenn es eine Kette gibt
```
