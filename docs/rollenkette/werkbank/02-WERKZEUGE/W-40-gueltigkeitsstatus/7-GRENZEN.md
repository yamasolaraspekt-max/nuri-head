# W-40 · Gültigkeitsstatus — GRENZEN

> **Dieses Blatt ist Pflicht** — *hier steht, was die Quelle NICHT hergibt.* **Und nach W-40/1 steht
> hier auch die Lehre daraus: „die Quelle gibt es nicht her" ist erst dann eine Grenze, wenn auch der
> BESTAND nichts hergibt.** *Vier der Fragen unten waren im Code längst beantwortet.*

## Der Befund, der die Prämisse berührt: die Achse existiert schon — woanders

**Das Auftragsblatt sagt „Es gibt KEINEN Code: die drei Stufen fehlen im Bestand."** *Für
`SchrittStatus` stimmt das. **Für die Insel nicht.*** Gemessen und **jede Stelle geöffnet**, weil
ein Wort kein Beleg ist:

```text
resources/planner/hausplaner/geometry/configuratorPackage.ts

  export type ConfiguratorStatus =
    | 'draft' | 'incomplete' | 'generated' | 'checked'
    | 'approved' | 'integrated' | 'outdated';          SIEBEN Stufen

  export const STATUS_UEBERGAENGE                       vollstaendige Uebergangstabelle
    Grundsatz woertlich im Dateikopf:
    „Bewusst streng: aus approved/integrated geht es nur ueber outdated zurueck
     in die Bearbeitung (Freigabe-Schutz — keine stille Rueckstufung)."

  statusUebergangErlaubt(von, zu)   der Waechter
  kannIntegrieren(paket)            nur approved darf uebernommen werden
  markiereVeraltet(paket, …)        die Invalidierung
```

**Gebaut, getestet und in Gebrauch** — *Testnamen statt Zeilenbereichen, weil Bereiche verrotten
(W-40/1):* `configuratorPackage.test.ts:31` *„Statusübergänge: erlaubte Wege gelten, verbotene
nicht"*, `:41` *„Freigabe-Schutz"*, `:48` *„kannIntegrieren nur bei approved"*, `:54`
*„markiereVeraltet"* — *und `geometry/integrationAbgleich.ts:13` und `:134` benutzt `kannIntegrieren`
außerhalb der Tests.*

> **Drei Folgen, und keine davon entscheide ich:**
>
> 1. **`outdated` existiert bereits samt Übergängen.** *Eine zweite Tabelle daneben wäre genau die
>    zweite Wahrheit, die W-40 laut seinem eigenen tragenden Punkt verhindern soll.*
> 2. **`approved` spielt fachlich die Rolle, die die Quelle `confirmed` zuschreibt** — *nur ein
>    `approved`-Paket darf übernommen werden. Das ist „PV erst nach bestätigter Geometrie", eine
>    Ebene tiefer.*
> 3. **`markiereVeraltet` IST die Invalidierung** — *damit berührt der Befund auch W-41, dessen
>    Blatt ebenfalls „kein Code" sagt.*
>
> **Die Frage an Planner und Plan-Prüfer:** *trägt das Ziel `ENTWORFEN` noch, wenn eine
> Gültigkeitsachse mit Übergängen bereits gebaut ist — oder ist W-40 in Wahrheit eine **Ablesung mit
> Erweiterung**?* **Dieselbe Klasse wie die Abweichung, die der Planner bei W-42 selbst benannt
> hat.** *Gemeldet in `docs/STATUS.md`, W-40-Block.*
>
> **BEANTWORTET (Yama, 12.08.): eine Ablesung mit EINER Erweiterung.** *Der Befund oben traf, und
> zwar weiter als ich ihn gezogen habe — ich habe ihn als Frage an die Prämisse gestellt und
> gleichzeitig die Blätter weiter als Vorgabe geschrieben.* **Berichtigt in `2-FUNKTION.md`; der
> Reifegrad im Register folgt als letzter Schritt von W-40/1.**

## Die Zahlenlücke — ENTSCHIEDEN: es war keine (Yama, 12.08.)

> **Yamas Antwort:** *„`review-required` ist keine Zahlenlücke, weil die vier und die drei **nicht
> auf derselben Achse** liegen — `4 + 3` muss nicht `8` ergeben. `review-required` entspricht
> `checked`, das existiert samt Übergängen und ist der einzige Weg nach `approved`."*

**Am Bau-Stand nachgemessen:** *`configuratorPackage.ts:107` führt `checked: ['draft', 'approved',
'generated']`, und `approved` steht in **keiner** anderen Übergangsliste.* **`checked` ist damit
tatsächlich der einzige Weg dorthin.**

> **Was an meiner Messung richtig bleibt:** *die Quelle führt `review-required` tatsächlich mit
> einem Gedankenstrich, und `4 + 3` ergibt tatsächlich `7`.* **Falsch war nur die Folgerung, dass
> daraus eine Lücke folgt** — *ich habe zwei Zahlen verrechnet, die zu verschiedenen Achsen
> gehören.* **Genau die Sorte Fehler, vor der dieses Blatt an anderer Stelle warnt.**

**Der zurückgezogene Abschnitt bleibt stehen, weil er den Weg zeigt:**

## ~~Die Zahlenlücke: acht gegen sieben~~ *(überholt — siehe oben)*

```text
Zielbild 3.6      ACHT Stufen        Quelle :117
gebaut            VIER               open · prog · warn · ok
als fehlend       DREI               confirmed · outdated · blocked
                  4 + 3 = 7,  nicht 8.

Die achte ist review-required — Quelle :121 fuehrt sie mit einem
GEDANKENSTRICH, nicht mit „fehlt", und die Einordnung zaehlt sie nicht mit.
```

> **Die Frage wird GESTELLT und NICHT beantwortet — sie gehört Yama.** *Entweder ist
> `review-required` bewusst nicht Teil der Gültigkeitsachse, oder die Zahl DREI ist zu niedrig.*
> **Beides ist möglich, und ich erfinde keine Erklärung.**
>
> **Warum das Blatt die Frage überhaupt tragen muss:** *wer „drei Stufen" baut, verliert die achte
> stillschweigend.* **4 + 3 = 7 ist der Hinweis, dass eine Angabe fehlt — genau die Sorte
> Zahlenlücke, die heute mehrfach durch die Rollen gelaufen ist.**

## `blocked` gegen `DECISION_BLOCKED` — ENTSCHIEDEN (Yama, 12.08.)

**Der Unterschied ist, WORAUF gewartet wird.** *Alle vier Merkmale:*

| Merkmal | `DECISION_BLOCKED` | `blocked` |
|---|---|---|
| **worauf gewartet wird** | auf einen **MENSCHEN** | auf eine **BEDINGUNG** |
| **Ebene** | **Prozess** | **Produkt** |
| **Ort** | **`docs/STATUS.md`** | **das Gebäudemodell** |
| **Aufhebung** | **nur durch Yama**, nie maschinell | **automatisch**, sobald die Vorbedingung messbar erfüllt ist |

*Und ein fünftes, das Yama zusätzlich nennt:* **Adressat** — *bei `DECISION_BLOCKED` die Rolle, die
entscheidet; bei `blocked` **das nächste Werkzeug**.*

**Yamas Beispiel:** *„PV-Belegung auf einer Dachfläche ohne bestätigte Geometrie — niemand
entscheidet etwas, die Sperre fällt von selbst."* **Das ist genau L-9, eine Ebene tiefer.**

**Yamas ZWEI AUFLAGEN für den Bau, wörtlich:**

> *„`blocked` trägt seinen Grund mit, denn ein `blocked` ohne `blockiert_durch` ist eine **Absage
> ohne Erklärung**."*
>
> *„`blocked` wird **NIE von Hand gesetzt oder gelöst**, wer das will meint `DECISION_BLOCKED`."*

**Und seine Namenswarnung:** *zwei Zustände, die beide „blockiert" heißen und **gegensätzlich**
aufgelöst werden, **werden verwechselt**.* **Wer sie verwechselt, wartet auf einen Menschen, der nie
kommt — oder greift von Hand in etwas ein, das sich selbst löst.**

**Der Satz, den `blocked` beim Bau mitzuführen hat — Yamas Wortlaut:**

```text
„dieser hier loest sich ohne mich"

Er gehoert an blocked, NICHT an DECISION_BLOCKED. Wer ihn am falschen
Zustand anbringt, hat die Warnung in ihr Gegenteil verkehrt.
```

> **Die erste Auflage ist dieselbe, die W-41 für die Invalidierung trägt** — *dort `outdated` mit
> Grund, hier `blocked` mit `blockiert_durch`.* **Beide Male aus demselben Satz: eine Absage ohne
> Erklärung ist die Form des teuersten Fehlers dieses Projekts.**

**Was hier zuvor stand — überholt, nicht gelöscht:** *„NICHT belegt: was `blocked` von
`DECISION_BLOCKED` im PROZESS unterscheidet … Wer sperrt, wer entsperrt, und woran hängt die
Sperre?"* **Die Frage war richtig gestellt; sie ist jetzt beantwortet.**

## Was die Quelle sonst nicht hergibt

```text
RUECKNAHME                ob eine Bestaetigung zurueckgenommen werden kann, sagt die
                          QUELLE nicht — der CODE sagt es: nur ueber outdated zurueck,
                          nie still (configuratorPackage.ts, Grundsatz im Dateikopf).

GRUND bei outdated        „niemals stille Loeschung" sagt, dass nichts verschwindet —
                          nicht, dass der Anwender den GRUND erfaehrt. Ohne Grund waere
                          outdated eine Absage ohne Erklaerung, und das ist der teuerste
                          Fehler dieses Projekts gewesen.
                          GEMESSEN (W-40/1), configuratorPackage.ts:125-128:
                            markiereVeraltet(paket, jetzt, durch)
                            -> { ...paket, status: 'outdated', updatedAt: jetzt,
                                 updatedBy: durch }
                          'durch' landet in updatedBy — das ist WER, nicht WARUM.
                          Der Grund wird also HEUTE NICHT mitgefuehrt. Halb geschlossen:
                          der Urheber steht fest, die Ursache nicht.

GRUND bei blocked         NICHT mehr offen: Yama hat blockiert_durch zur Auflage gemacht.
```

**BERICHTIGT (W-40/1, 12.08.) — zwei Zeilen sind hier weggefallen, weil sie beantwortet sind:**

```text
WEG:  „WORAN der Status haengt — Schritt? Geschoss? Bauteil? Dokument? Die Quelle
       nennt die Stufen, nicht ihren Traeger."
      -> Der TRAEGER ist das PAKET. Und der Satz stand direkt UEBER dem Hinweis
         „Der Praezedenzfall oben haengt am PAKET" — die Antwort stand im selben
         Block wie die Frage, und ich habe sie nicht gezogen.

WEG:  „UEBERGAENGE — keine Tabelle. Alles Weitere waere erfunden."
      -> Es GIBT eine, configuratorPackage.ts:103-111, und sie gilt auch hier.
```

> **Beide Male derselbe Griff:** *ich habe die QUELLE gefragt, wo der CODE geantwortet hätte.* **Ein
> „die Quelle sagt es nicht" ist erst dann eine Grenze, wenn auch der Bestand nichts sagt.**

## Was dieses Blatt ausdrücklich NICHT entscheidet

| Frage | Gehörte | Stand 12.08. |
|---|---|---|
| Trägt `ENTWORFEN` noch, oder ist es eine Ablesung? | **Planner / Plan-Prüfer** | **BEANTWORTET (Yama):** *Ablesung mit **einer** Erweiterung → Reifegrad `BESCHRIEBEN`* |
| Gehört `review-required` zur Gültigkeitsachse? | **Yama** | **BEANTWORTET:** *ja, gebaut als `checked`; keine Zahlenlücke — andere Achse* |
| Wie grenzt sich `blocked` von `DECISION_BLOCKED` ab? | **Yama** | **BEANTWORTET:** *Mensch gegen Bedingung; vier Merkmale + zwei Auflagen, oben* |
| Gilt die gebaute Übergangstabelle auch hier? | **Planner**, nach Prüfung des Präzedenzfalls | **JA** — *`configuratorPackage.ts:103-111`; eine zweite wäre die zweite Wahrheit* |

> **Vier offene Fragen, alle benannt statt still angenommen — und alle vier binnen eines Tages
> beantwortet.** *Ein Blatt, das sie selbst beantwortet hätte, wäre schneller fertig gewesen und
> hätte vier Entscheidungen vorweggenommen, die ihm nicht gehören.* **Der Beleg dafür, dass das
> Stellen billiger ist als das Erfinden: drei der vier Antworten weichen von dem ab, was ich beim
> Bau für wahrscheinlich gehalten hätte.**

## Was später kommen könnte

```text
- der BAU der Achse                    -> eigener Auftrag, Vorgaben in 6-PRUEFUNG als B-1..B-5
- die Invalidierungs-MECHANIK          -> W-41
- die Bedingung fuer die PV-Belegung   -> W-31 liest confirmed, W-40 liefert es nur
```
