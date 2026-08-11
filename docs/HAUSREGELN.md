# Hausregeln — Yamas Sätze, gesammelt. Sie standen verstreut und nirgends zusammen

```yaml
art: "SAMMLUNG, keine Regelaenderung. Der Planner stellt zusammen, was Yama gesetzt hat."
angelegt: "12.08."
anlass: "Yama: 'Nimm den Satz als Hausregel auf.' — dreimal in drei Antworten, jedes Mal
          ein anderer Satz, und keiner hatte einen Ort."
grenze: "Ob diese Regeln in docs/ARBEITSREGELN.md gehoeren, entscheidet Yama oder eine
         Prozesspruefung nach §13. Der Planner sammelt und legt vor — §1."
warum_diese_datei: "genau das Muster, das Yama selbst benannt hat: benannt, mehrfach
   erwaehnt, in keinem Plan zuhause. Es traf W-09, konterlattungMm, auswechslung.ts —
   und es traf seine eigenen Hausregeln."
```

## Warum es diese Datei gibt

**Yama hat in drei aufeinanderfolgenden Antworten drei Sätze ausdrücklich als Hausregel gesetzt.**
*Jeder stand in einer Antwort, keiner in einer Datei.* **Dazu zwei weitere, die er als Entscheidung
formuliert hat und die dieselbe Reichweite haben.**

> *Das ist die Anwendung seiner eigenen Regel auf ihn selbst: **„Eine Notiz über eine Lücke ist kein
> Plan für die Lücke."*** *Ein Satz in einer Antwort ist eine Notiz. Diese Datei ist das „sondern
> dort" — nicht mehr; die Geltung setzt er, nicht ich.*

---

## H-1 · Eine Notiz über eine Lücke ist kein Plan für die Lücke

> *Wer eine Zeile schreibt, die erklärt, **warum** etwas nicht drin ist, hat damit **nichts
> erledigt** — er hat die Lücke nur bequem gemacht. Ein Ausschluss ist erst dann gültig, wenn
> daneben steht, **wo die Sache stattdessen hingeht**. „Nicht hier" ohne „sondern dort" ist ein
> offener Posten in Tarnkleidung.*

**Gesetzt:** 12.08., als Folge des W-09-Befundes.
**Drei Belege, alle aus diesem Repo:**

```text
W-09 Treppe          FAHRPLAN-KLASSE-A.md:148 "NICHT IN A — war nie in den drei Runden"
                     -> zwei Tage lang kein Blatt. Behoben: 6e2949a7
konterlattungMm      definiert, zweimal befuellt, von nichts gelesen — auch von keinem Test
auswechslung.ts      steht in W-21/1 UND W-22/1 als "verwandt, nicht im Scope"
                     -> in keinem Blatt zuhause
```

**Prüfform:** *Ein Ausschluss ohne Zieladresse ist ein Befund.* Wer „nicht hier" schreibt, schreibt
in derselben Zeile, wohin.

---

## H-2 · Ein Bericht, der ein Fachurteil wie eine Messung aussehen lässt, ist gefährlicher als keiner

**Herkunft:** *mein Kriterium `A-15-4`; Yama hat es zur Hausregel erhoben* — „die beste Zeile deines
Berichts".

**Und er hat es sofort an sich selbst angewandt:** *„Das ist genau das, was mir bei der
Normnennungs-Achse passiert ist: ich habe ein Urteil (‚Norm = Verantwortung') als Kriterium
ausgegeben."*

**Prüfform:** *Jede Zeile, die eine Bewertung trägt, sagt sichtbar, ob sie gemessen oder geurteilt
ist.* Bei Fachurteilen steht **„vorgeschlagen, nicht entschieden"** oder der Name dessen, der
urteilt.

---

## H-3 · Die Tafel ist kein Zeugnis. Sie ist das Instrument. Ein Instrument, das schont, zeigt falsch

**Gesetzt:** 11.08., als Folge meines Satzes *„Zurückstufen wäre eine Strafe für einen Altstand."*

**Der Kern ist eine Trennung, die ich zusammengelegt hatte:**

```text
SCHULDFRAGE     niemand hat eine Regel gebrochen (W-07 entstand 43 Minuten vor dem
                Kriterium) -> das entschuldigt den Entstehungsweg
ZUSTANDSFRAGE   die Tafel sagt BESCHRIEBEN, das Blatt ist es nicht
                -> das ist davon UNABHAENGIG falsch
```

**Prüfform:** *Ein Reifegrad ist eine Ablesung, keine Bewertung.* Und: eine Kennzahl, die sich durch
Papier bewegen lässt, misst Papier — **der Abschlusszähler steigt nur durch Bauten, nicht durch
Schnitte.**

---

## H-4 · §3 sperrt die Dateien im Scope des laufenden Auftrags — nicht das Repo

**Gesetzt:** 12.08., nachdem ich dreimal auf eine Datei verzichtet hatte, die frei war.

> *„‚Ein Auftrag läuft' und ‚meine Datei ist gesperrt' sind zwei verschiedene Messungen. Wer die
> erste macht und die zweite meint, verliert Zeit; wer es umgekehrt macht, verliert Arbeit."*

```text
FALSCH in eine Richtung   11.08.: §3 drei Minuten vor dem Schreiben gemessen, dazwischen
                          ging W-05/1 auf IN_ARBEIT -> in fremden Scope geschrieben (ce30174f)
FALSCH in die andere      12.08.: dreimal REGISTER.md liegen gelassen, obwohl der laufende
                          Auftrag app/ und tests/ hielt -> Arbeit verzoegert
RICHTIG                   die Scope-Sektion des IN_ARBEIT-Auftrags lesen, unmittelbar
                          vor dem Schreiben
```

---

## H-5 · Ein Werkzeug darf nur urteilen, wenn es alle Bedingungen kennt, von denen das Urteil abhängt

**Gesetzt:** 12.08., als Achse 3 der Engine-Klassifikation — und sie hat Yamas eigenes vorheriges
Kriterium („nennt eine Norm") ersetzt.

> *Nur dann darf eine Engine ein Urteil fällen. **Sonst rechnet sie Werte und schweigt.***

```text
BELEGT, drei Faelle: eine Engine benennt ihre Grenze SELBST und urteilt trotzdem
  sparrenBerechnung    "Ersetzt KEINE prueffaehige Statik"
  fbhAuslegung:6-7     "GRENZE: hydraulischer Abgleich … bleiben Fach-Engine"
  heizkreisVerteiler:6 "GRENZE: hydraulischer Abgleich/Rohrnetz bleibt Fach-Engine"
GEGENFALL, zulaessiges Urteil
  kuecheArbeitsdreieck  drei Punkte, drei Wege, feste Grenzen — die Aufgabe ist
                        durch die drei Abstaende vollstaendig definiert
```

**Prüfform:** *Bedingungen der Aufgabe auflisten, gerechnete zählen, vergleichen.* **Und die
Begründung ist NICHT die Schwere der Folge, sondern die Unvollständigkeit** — Yama ausdrücklich:
*„selbst wenn sie harmlos wäre: die Engine darf nicht ‚bestanden' sagen, was sie nicht geprüft hat."*

---

## H-6 · Ein Wort ist kein Beleg; erst die Stelle ist einer

**Gesetzt:** 12.08., als Folge des `bewerteDeckung`-Fundes.

```text
bewerteDeckung() existiert — in heizkoerperLeistung.ts:53, als LEISTUNGSdeckung eines
Heizkoerpers, NICHT als Dachdeckung.
-> haette ich den Treffer fuer W-23 gezaehlt, waere die Ziegel-Luecke unsichtbar geblieben.
Weitere Faelle desselben Tages:
  'material' traf jedes THREE.Material            -> 61 Dateien fuer W-17
  'export'   traf jedes export function
  'GEG'      traf "gegeben" und "gegen"           -> drei Fehlalarme
  '< 1 mm²'  wurde als Platzhalter gezaehlt       -> ein Vergleichsoperator
```

**Gehört zu B5 als Beispiel ins Blatt.** *B5 verlangt, die Trefferzeilen zu lesen — H-6 sagt, warum:
das Wort steht überall, der Gegenstand nur an einer Stelle.*

---

## Zwei Regeln, die schon in ARBEITSREGELN stehen und hier nur verwiesen werden

```text
§3      hoechstens ein Auftrag IN_ARBEIT — siehe H-4 fuer die Auslegung
§12.5   Nachbesserung ohne rueckwirkende Wirkung
```

*Diese Sammlung wiederholt sie nicht. **Zwei Fassungen einer Regel wären eine zweite Wahrheit** — und
das ist der Fehler, den die Sammlung verhindern soll, nicht der, den sie machen darf.*

```yaml
regeln: 6
alle_von: yama
gesetzt_zwischen: "11.08. und 12.08."
offen_an_yama: "gehoeren H-1 bis H-6 in docs/ARBEITSREGELN.md? Der Planner legt vor,
                er schreibt keine Regel ein (§1). Und: soll diese Sammlung bleiben
                oder aufgehen?"
kern: "Yama hat dreimal 'nimm das als Hausregel auf' gesagt. Ein Satz in einer Antwort
       ist eine Notiz — H-1 sagt, dass das nicht genuegt. Diese Datei ist die Anwendung
       von H-1 auf H-1."
```
