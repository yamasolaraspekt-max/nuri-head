# ZUSTELLUNG — plan-pruefer an planner

**Form nach der STOPP-REGEL** (`ARBEITSREGELN.md` Z.1691 ff., Yamas Anordnung vom 16.08.):
*„in fremder Zuständigkeit → ZUGESTELLT mit Ballbesitz und Soll (nicht: in einer Botschaft
erwähnt)."* Die folgenden Befunde standen bisher nur in Commit-Botschaften und in
`docs/BEFUND-plan-pruefer-rueckweg-und-tor.md`. Damit waren sie **verschoben, nicht behoben**.
Hier sind sie zugestellt.

**Zustellzeit:** 16.08. 20:11 · **Messstand:** a42f165f · **geprüft gegen:** auto/hausplaner-integration
**Ballbesitz für alle folgenden Punkte: planner.**

---

## 1 · A-40-5 misst das alte Merkmal im neuen Suchraum

**Sache:** Der Zählbefehl wurde von `FORMELSAMMLUNG.md` auf `01-MATHEMATIK/*.md` erweitert.
Der Filter sucht weiterhin nur Ampeln (`🟢🟡🔴`). Das SOLAR-REGELWERK führt aber **keine
Ampeln**, sondern die drei Zustände, die A-40 erst einführen will.

**Beleg:** FORMELSAMMLUNG 33 Ampelzeilen / 0 zustand-Felder · SOLAR-REGELWERK 0 Ampelzeilen /
1 zustand-Feld, beide mit 32 Definitionsstellen. Der Lauf meldet dort alle 32 als ampellos —
darunter S-008, den einzigen vollständig nachgerechneten Eintrag des Bestands.
**Heute noch offen:** das Blatt nennt `zustand` im Filter 0-mal.

**Soll:** Filter auf „trägt eine Ampel ODER ein zustand-Feld" erweitern, oder A-40-5
ausdrücklich auf die Formelsammlung beschränken.

---

## 2 · A-40s Menge zählt drei nicht benutzte Formeln mit

**Sache:** Das Blatt nennt 25 benutzte F-Nummern. Das Register markiert nicht benutzte Formeln
durchgestrichen; **drei kommen ausschließlich durchgestrichen vor.**

**Beleg:** `~~F-020~~ ⓝ` in W-07 („0 Treffer, skelett in allen acht Modulen"), `~~F-021~~ ⓝ`
in W-07 („kein Skelett zum Anheben"), `~~F-031~~ ⓝ` in W-04 und W-22. Aktiv benutzt: **22**.
Das Prinzip des Blattes — Benutzung, nicht Inventur — schließt sie aus.

**Soll:** Menge auf 22 stützen, die drei ausdrücklich ausnehmen.

---

## 3 · A-40-2s Negativprobe hat keinen Kandidaten

**Sache:** A-40-2 verlangt eine Negativprobe *„ein Blatt, dessen Kriterium das Nachrechnen
selbst verlangt → keine Meldung"* — und benennt, anders als A-40-3, kein Blatt.

**Beleg:** Kriterienzeilen mit nachrechn/nachgerechnet/durchgerechnet: am Basis-Stand 99add90f
**0 Blätter**, heute **genau eines — A-40 selbst**. Zwölf Blätter erwähnen das Nachrechnen, aber
ausnahmslos in Prüfvermerken, nie als Forderung.

**Soll:** Proben so konkret benennen wie in A-40-3 und die Negativprobe erst schaffen, oder sie
streichen und nur die Positivrichtung zusagen.

---

## 4 · A-40-6 hat keine Rot-Lage mehr, A-40-9 kann nicht scheitern

**Sache A-40-6:** Am Schnitt (13:45) trug kein Eintrag ein `nachgerechnet_an` mit Abweichung.
Um 14:49 hat `66fa277f` S-008 genau das gegeben — vollständig, mit `abweichung_ohne_die_regel`.
Das Kriterium ist an 1 von 1 Einträgen erfüllt, bevor gebaut wurde.

**Sache A-40-9:** „Suite grün, `tsc exit=0`" bei einem Auftrag mit **null Code-Pfaden**. Zum
Vergleich: A-37 hat 7, A-41 6, A-39 3, A-38 2, A-42 1 — A-40 als einziges 0. Wenn A-40-8 hält,
sieht weder tsc noch die Suite eine Änderung.

**Soll:** A-40-6 auf den Prüfschritt umformulieren (mit S-008 als Negativprobe), A-40-9
streichen oder als Regressionsschutz kennzeichnen.

---

## 5 · A-40s K5 und K6 nennen keinen Fall

**Sache:** K1 nennt N-003, K2 S-078, K3 die 17 S-Verweise, K4 W-28 — K5 und K6 nennen nichts.
A-40-7 verlangt aber „alle sechs Kanten je einzeln belegt".

**Soll:** je einen Fall benennen oder ausdrücklich als Entwurfsgrenze führen.

---

## 6 · A-42 fehlt die Kante für den verdeckten Block

**Sache:** A-42s Messvorschrift ist `re.findall` auf yaml-Fences. Ein Block, der auf einen
**ungeschlossenen** Fence folgt, fällt heraus — er ist selbst intakt, K4 („kaputtes yaml") fängt
ihn nicht.

**Beleg:** A-18 ist an **allen** Ständen unsichtbar — Basis e802c1f8 163/162, Integration
254/253, mein Baum 250/249. Sein Block trägt `auftrag`, kein `zustand`, gehört also zur
Umzugsmenge. Am Integrationsstand: Zeile 7876 öffnet ohne zu schließen, 7890 beginnt A-18.
**Heute noch offen:** keine K7 im Blatt.

**Soll:** siebte Kante „Block nach ungeschlossenem Fence", mit der Gegenprobe *auftrag-Zeilen im
Volltext gegen erfasste Blöcke* — Differenz heute 1.

---

## 7 · A-39s Nenner ist 89, nicht 85 · und der Dateiname sagt „fünf"

**Sache:** Die Reichweiten-Messung nennt „NEUN von 85". 44 A- plus 41 W-Blätter sind 85, der
Ordner enthält **89** — es fehlen B5, B5N, B6, B7, keines mit Kantentabelle, alle vier laufen
stumm durch. Die Neun selbst habe ich zweimal unabhängig bestätigt.

**Beleg:** 89 gesamt · 9 mit Kantentabelle · 55 mit Abnahmekriterien · 9 mit beidem · **80 stumm**.
**Heute noch offen:** das Blatt sagt „von 85"; der Dateiname lautet weiterhin
`A-39-die-fuenf-innenpruefungen-des-blattes.md`, während Titel und `art` acht Prüfungen führen.

**Soll:** Nenner auf 89 und stumm auf 80 berichtigen; Dateinamen nachziehen oder die Abweichung
im Blatt vermerken.

---

## 8 · Vier Zahlen ohne ihren Zählbefehl

**Sache:** Vier tragende Zahlen sind in keiner meiner Lesarten reproduzierbar. Die **Sache**
stimmt jedes Mal — es fehlt jedes Mal das Muster, mit dem die Zahl entstand.

| Zahl | wo | meine Messungen |
|---|---|---|
| 32 Dateien außerhalb | A-39 Nicht-Ziel | 40 weit · 37 BEFUND/BERICHT · 36 eng |
| F-001 zwölf, F-030 acht, F-004 sieben | A-40 Reichweite | 10 · 5 · 4 (vier Zählwege geprüft) |
| S-008 acht, S-078 sieben, S-040 sieben | A-40 S-Seite | 4 · 11 · 15 (Nennungen) bzw. 0 · 0 · 5 (Abhängigkeiten) |
| 38 Verweise (A-34-Rückfall) | `df61e5bb` | 26 · 31 · 64 · 2 · 2798 |

**Soll:** zu jeder Belegzahl den Zählbefehl nennen — so, wie A-40-5 es für seine eigene Zahl
bereits vorschreibt.

---

## Was ich selbst behoben habe, statt es zuzustellen

Vier eigene Fehlbefunde habe ich in derselben Runde zurückgenommen, in der ich sie fand:
K4/W-28 („existiert nicht" — es existierte seit 16:47), W-25 als „fünfter Fall" (existiert seit
14:01), „die Statuswahrheit ist eingefroren" (die Regel sieht den Commit-Betreff als Träger vor,
Yamas Entscheidung vom 16.08.), und meine A-37-Ballwechsel-Bestätigung von 19:47.
**Nach der Stopp-Regel gehört diese Liste hierher**, damit niemand auf einer zurückgenommenen
Messung weiterbaut.

---

## NACHTRAG 9 · A-37s Ballbesitz steht an zwei Orten verschieden — und einer davon bin ich

*(zugestellt 16.08. 20:20 · Messstand 7909a9ea · gemessen gegen auto/hausplaner-integration)*

**Sache:** Der Integrator hat A-37 um 20:16 mit `15e11078` auf CODE_FERTIG nachgezogen —
sauber als *„TRANSPORT, keine Entscheidung"*, mit beiden Commit-Betreffs des Generators als
Beleg. Dabei hat er einen offenen Punkt ausdrücklich **gemeldet statt genommen**:

> *„NICHT ANGEFASST: ballbesitz. Die Tafelzeile führt Plan-Prüfer, dieser Datensatz integrator
> (aus A-37-18, sachlich erledigt: das Tor liegt in 6 von 6 Arbeitsbäumen). Wer den Ball nach
> CODE_FERTIG trägt, ist eine Zuweisung und keine Übertragung — das entscheide ich nicht."*

**Selbst nachgemessen am Integrationsstand:**

| Ort | Wert |
|---|---|
| Tafelzeile `A-37` | **Plan-Prüfer** |
| Datensatzfeld `ballbesitz` | **integrator** |
| Zustand (beide Orte) | CODE_FERTIG |

Das ist eine A-20-Divergenz an genau der Stelle, die A-20 meint — und ich bin eine der beiden
genannten Rollen, kann sie also nicht selbst auflösen, ohne mir einen Ball zuzuweisen.

**Warum ich sie nicht selbst behebe:** Nach der Stopp-Regel wäre „in eigener Zuständigkeit"
die richtige Form, wenn es meine Zuständigkeit wäre. Ballbesitz nach einem Zustandswechsel ist
aber eine **Zuweisung**, keine Messung — dieselbe Begründung, mit der der Integrator es
liegengelassen hat. Ich messe, dass die beiden Orte auseinanderlaufen; wer den Ball trägt,
entscheidet der, der den Auftrag führt.

**Soll:** Beide Orte auf denselben Halter bringen. Sachlich naheliegend ist der **Evaluator** —
A-37 ist CODE_FERTIG, Schritt I ist gefahren, und die Abnahme ist seine Bahn. Die Tafelzeile
führt mich noch aus der DoR-Phase, der Datensatz den Integrator aus A-37-18, das laut seiner
eigenen Messung sachlich erledigt ist (Tor in 6 von 6 Bäumen).

**Falls die Zuweisung nicht beim Planner liegt:** dann ist dieser Punkt nach der Stopp-Regel
*„nicht behebbar"* und gehört Yama vorgelegt — mit genau diesem Grund.

---

## BERICHTIGUNG zu Punkt 6 — A-18 gehoert NICHT zur Umzugsmenge, der Befund verlagert sich

*(berichtigt 16.08. 20:26 · Messstand e22bc35a · gemessen gegen auto/hausplaner-integration)*

**Mein Fehler, in eigener Sache und in dieser Zustellung.** Punkt 6 sagt: *„Sein Block trägt
`auftrag`, kein `zustand`, gehört also zur Umzugsmenge."* **Das ist falsch.**

**Gemessen:** Der A-18-Block reicht von Zeile 7890 bis 7973. Auf **Zeile 7910** steht
`zustand: BETRIEBSBESTAETIGT`, auf 7911 `ballbesitz: —  # Kette vollstaendig`. Ich hatte um
19:36 nur die ersten Felder des Blocks gelesen (`auftrag`, `datei`, `abnahme_nachgezogen`,
`release_vermerk`) und daraus geschlossen, es gebe kein Zustandsfeld. Der Block ist 84 Zeilen
lang; ich habe vierzehn davon angesehen.

**Was das ändert:** A-18 gehört zu den Blöcken **MIT** `zustand` — also zu denen, die A-42
ausdrücklich **nicht** anfasst (Zeile 65 des Blattes: *„Kein Block MIT `zustand:` wird
angefasst"*). Er fällt damit **nicht** aus der Umzugsmenge, und die Zusage aus Zeile 60 („kein
Block verschwindet") ist an ihm nicht verletzt.

**Was bleibt — und es trifft ein anderes Kriterium:** Der Block ist weiterhin für jede
Fence-Paarung unsichtbar. Gemessen am selben Stand:

| | Volltext | erfasst | unsichtbar |
|---|---|---|---|
| `zustand:`-Zeilen | 91 | 90 | **1** |
| `auftrag:`-Zeilen | 258 | 257 | 1 |

**A-42-6** verlangt: *„Die Blöcke MIT `zustand:` sind unberührt — Anzahl und Inhalt
vorher/nachher gleich, **über Hash belegt**."* Für einen dieser Blöcke lässt sich kein Hash
bilden, weil er von der Paarung nicht erfasst wird. Die Anzahl-Probe zählt 90 statt 91, und
zwar vorher wie nachher — sie fällt also nicht auf, sondern **stimmt stillschweigend über eine
zu kleine Menge**.

**Soll, angepasst:** Die siebte Kante aus Punkt 6 bleibt nötig, greift aber bei **A-42-6**
statt bei A-42-3. Die Gegenprobe ist dieselbe und deckt beide Fälle ab: *Zahl der `auftrag:`-
bzw. `zustand:`-Zeilen im Volltext gegen die in Blöcken erfassten* — Differenz heute je 1.

---

## NACHTRAG 10 · A-39s Nenner ist im Codeblock berichtigt, im Fliesstext nicht

*(zugestellt 16.08. 20:47 · Messstand 27ee1726 · gemessen gegen ce280128)*

**Zuerst: Punkt 7 meiner Zustellung ist behoben, und ich hatte ihn fast als offen gemeldet.**
Meine erste Suche ging auf *„von 85"* und fand nur den Fließtext. Die Berichtigung steht im
Codeblock darüber — Zeile 227–231: *„BERICHTIGT 16.08. nach Gegenmessung des Plan-Prüfers:
Nenner ist 89 und nicht 85 … Die NEUN stimmt zeichengenau. Stumm durchlaufen 80."* Sauber
zugeschrieben und vollständig.

**Der Restbefund:** Dieselbe Seite trägt beide Zahlen.

| Ort | Aussage |
|---|---|
| Codeblock Z.221–225 | `A-Blaetter 44 · W-Blaetter 41 · gesamt **89** · mit Kantentabelle 9` |
| Codeblock Z.227–229 | *„Nenner ist **89** … Stumm durchlaufen **80**"* |
| **Fließtext Z.234–236** | *„Diese Struktur haben 9 von **85** Blättern. Die übrigen **76** laufen durch"* |
| **Fließtext Z.239–240** | *„ein Lauf … endet mit ‚0 Funde in **85** Blättern' und wie eine Unbedenklichkeitsbescheinigung für **85** aussieht"* |

**Warum das zählt, und zwar nach Ihrer eigenen Unterscheidung:** Der Codeblock ist ein
Messprotokoll — ein **Beleg**, der seinen Stand festhält. Der Fließtext darunter ist die
**Aussage**, die das Kriterium trägt und die ein Leser mitnimmt. Hier steht der berichtigte
Wert im Beleg und der überholte in der Aussage — genau die falsche Richtung. Wer A-39s
Reichweite zitiert, zitiert 85 und 76.

**Verschärfend:** Der Satz in Z.239 ist selbst eine Warnung vor einer falschen Zahl
(*„sieht aus wie eine Unbedenklichkeitsbescheinigung für 85"*) — und trägt dabei die falsche
Zahl. Nach der Berichtigung müsste er 89 und 80 nennen.

**Soll:** Die vier Stellen im Fließtext auf 89 und 80 ziehen. Der Codeblock bleibt, wie er ist —
er ist der Beleg der Berichtigung.

---

## NACHTRAG 11 · A-38s neue Zahl war beim Eintragen schon 77 Minuten alt

*(zugestellt 16.08. 21:02 · Messstand 41ba9706 · gemessen gegen e15d3677)*

**Zuerst die Anerkennung:** `e15d3677` (20:57) findet die **Ursache** meines Befundes vom
14:15 — A-38s Messbefehl trug weiterhin `--since 48 hours ago`, und *„genau dieser Befehl
erzeugt die Zahlen, die der Plan-Prüfer in keinem Baum reproduzieren konnte."* Der alte Befehl
steht jetzt darunter, ausdrücklich mit **NICHT MEHR BENUTZEN** gekennzeichnet, nach A-20-4.
Das ist die richtige Behebung: nicht die Zahl korrigiert, sondern den Befehl.

**Der Nachtrag betrifft die neue Zahl.** Das Blatt nennt in Z.85: *„gemessen 16.08. abends: 472
Commits, 188 Merges, Anteil 40 Prozent."*

**Mit dem Befehl aus Z.82/83 selbst gefahren:**

| Zeitpunkt | Commits | Merges |
|---|---|---|
| bis 19:30 | 458 | 180 |
| **≈19:40 — hier passt 472/188** | | |
| bis 20:57:30 (sein Commit) | **623** | **266** |
| 21:00 (meine Messung) | 627 | 268 |

**Die Zahl war beim Eintragen 77 Minuten alt und um 151 Commits zu niedrig.** Seit dem Commit
sind nur 4 Commits dazugekommen — der Zeitversatz erklärt die Lücke also nicht, die Messung
selbst lag früher.

**Warum das mehr ist als eine Kleinigkeit:** Eine `--since <festes Datum>`-Zahl wächst
**monoton** und ist ab der Sekunde ihrer Messung überholt. Der Bestand wuchs heute Abend um
etwa zwei Commits je Minute. Der Vermerk *„16.08. abends"* nennt keine Uhrzeit — und ohne sie
ist nicht entscheidbar, ob eine Abweichung Alterung oder Fehler ist. Genau diese Frage hat mich
gerade zehn Minuten gekostet.

**Das ist P2 in seiner eigenen Form:** eine feste Zahl, deren Standbezug fehlt. Nicht der
Messbefehl — der ist jetzt richtig —, sondern der Zeitstempel daneben.

**Soll:** Den Vermerk auf eine Uhrzeit setzen (*„gemessen 16.08. 19:40"*) oder die Zahl ganz
weglassen und nur den Befehl stehen lassen. **Der Befehl allein wäre ausreichend** — er ist
jederzeit nachfahrbar, die Zahl ist es nicht.

---

## NACHTRAG 12 · N-003s Belegstelle zeigt auf einen Kommentar — an einer Fach-Gate-Stelle

*(zugestellt 16.08. 21:11 · Messstand dc6abbd1 · Vorratsprüfung Posten c und a)*

**Zuerst: die Formel hält.** Ich habe N-003 an einem Fall durchgerechnet — C24-Sparren 80/200,
Spannweite 4,0 m, Dachneigung 30°, Sparrenabstand 0,8 m, gk = s = 1,0 kN/m²:

```
wDesign = (1,35·1,0 + 1,5·1,0)·0,8  = 2,280 kN/m
wPerp   = wDesign · cos30           = 1,9745 N/mm
M       = wPerp·L²/8                = 3.949.076 N·mm
W       = b·h²/6                    = 533.333 mm³
sigma   = M/W                       = 7,40 N/mm²
fmd     = 0,9·24/1,3                = 16,62 N/mm²      -> haelt
I       = b·h³/12                   = 53.333.333 mm⁴
w       = 5·wChar·L⁴/(384·E·I)      = 7,87 mm
zul     = L/300                     = 13,33 mm         -> haelt
```

**Alle fünf Formeln sind die Standardformeln** für den Einfeldträger unter Gleichlast, und
`fmd = kmod·fmk/γM` ist Eurocode 5. **Die Beiwerte im Blatt stimmen zeichengenau mit dem Code**
(Z.26–30): 1,35 · 1,5 · 1,3 · 0,9 · 300. Auch der Geltungsbereich trifft, was der Code kann —
nur die senkrechte Lastkomponente, kein Wind, kein Mehrfeld, kein Knicken.

**Der Befund ist die Belegstelle.** Das Blatt nennt in Z.757:

> `geometry/sparrenBerechnung.ts:86`, `berechneSparren(e)`

**Gemessen:** `berechneSparren` steht heute auf **Zeile 105**. Auf **Zeile 86** steht ein
Kommentarblock über Ausgabewege (*„weil es heute genau EINEN Ausgabeweg gibt und morgen Export,
Stückliste und PDF…"*). Der Zeiger zeigt also nicht ins Leere, sondern **auf etwas anderes** —
die schwerere Form.

**Warum das hier mehr wiegt als anderswo:** N-003 trägt das **Fach-Gate** mit Haftungsbezug —
*„eine Sparrenbemessung, die als geprüft gilt und dann nicht trägt, ist Personenschaden."* Die
Belegstelle ist die Brücke zwischen der Regel und dem Code, den sie beschreibt. Nach der
Wegweiser/Beleg-Unterscheidung ist sie ein **Wegweiser**: sie sagt, wohin jemand gehen soll, der
das Fach-Gate prüfen will. Ein Wegweiser darf nie veralten.

**Soll:** Die Zeilennummer durch die Sache ersetzen — `geometry/sparrenBerechnung.ts`, Funktion
`berechneSparren`, wie es der Planner heute an vier anderen Ankern bereits getan hat. Die
Funktion ist über ihren Namen eindeutig auffindbar, die Zeile ist es nicht.

**Vierter belegter Fall dieser Klasse heute** — nach W-12/1 (`rasterLinien` :1274→:1261),
A-30 (M-02 :5302→:5301) und `raumAuswahl.ts`→`Buehne.tsx` (:147→:162).

---

## NACHTRAG 13 · MEINE eigene Zahl in A-42 ist gealtert — 68 von 77 sind heute 79 von 168

*(zugestellt 16.08. 21:14 · Messstand 4773d0d1 · Vorratsprüfung Posten b)*

**Das ist mein Fehler, und es ist derselbe, den ich vor zwanzig Minuten in Nachtrag 11
zugestellt habe.** A-42 trägt seit `97edfed1` meinen Befund in Z.90/91:

> *„K3 und K6 ebenfalls nicht auslösbar. ABER: **68 von 77** Notizen tragen FREITEXT im
> auftrag-Feld statt einer Kennung."*

**Die Zahl stammt von mir**, gemessen um 17:53 in `30c4a240`. **Sie steht ohne Zeitstempel im
Blatt** — genau die Form, die ich bei A-38s 472/188 beanstandet habe.

**Heute nachgemessen, mit demselben Muster:**

| | 17:53 (meine Messung) | 21:12 (heute) |
|---|---|---|
| Notizen (auftrag ohne zustand) | 77 | **168** |
| davon Freitext | 68 | **79** |
| Anteil | 88 % | **47 %** |

**Die Grundgesamtheit hat sich mehr als verdoppelt**, weil seither viele Blöcke *mit* Kennung
dazugekommen sind — darunter meine eigenen. Der absolute Freitext-Bestand ist nur um elf
gewachsen; der **Anteil** ist von 88 auf 47 Prozent gefallen.

**Die Aussage hält, die Zahl nicht.** Freitext-Kennungen existieren weiterhin (79 Stück), und
A-42-4 muss den Fall weiterhin benennen. Aber *„68 von 77"* liest sich als „fast alle" — heute
ist es knapp die Hälfte. Wer die Dringlichkeit an der Zahl misst, misst falsch.

**Soll:** Entweder Zeitstempel dazu (*„gemessen 16.08. 17:53"*) oder die Zahl durch den
Zählbefehl ersetzen. **Mein Vorschlag ist der Befehl**, weil diese Zahl mit jedem Befundblock
weiterwandert:

```
Bloecke mit auftrag: ohne zustand: — davon solche, deren auftrag-Wert
nicht dem Muster [A-Z]+-?[0-9]+ entspricht.
```

**Ich habe damit heute vier Zahlen ohne Standbezug zugestellt — und die vierte ist meine.**

---

## PRAEZISIERUNG zu NACHTRAG 12 · zwei von drei Zeigern treffen — nur einer ist gewandert

*(zugestellt 16.08. 21:23 · Messstand a61f607f · Vorratsprüfung Posten e)*

**Mein Nachtrag 12 war richtig, aber unvollständig erhoben.** Ich habe **einen** Verweis auf
`sparrenBerechnung.ts` gemeldet, ohne zu prüfen, ob es weitere gibt. Die Formelsammlung nennt
**drei**:

| Zeile im Blatt | Verweis | Funktion | gemessen |
|---|---|---|---|
| 718 | `sparrenBerechnung.ts:33` | `bodenschneelast` | **steht auf 33** ✔ |
| 740 | `sparrenBerechnung.ts:45` | `formbeiwertSchnee` | **steht auf 45** ✔ |
| 757 | `sparrenBerechnung.ts:86` | `berechneSparren` | **steht auf 105** ✘ |

**Zwei von drei treffen zeichengenau.** Nur der dritte ist gewandert — und zwar, weil
`berechneSparren` weiter unten in der Datei steht als die beiden anderen; die eingefügten
Zeilen (ein Kommentarblock) liegen dazwischen. **Was oberhalb der Einfügung steht, hält; was
darunter steht, wandert.**

**Das ändert die Gewichtung meines Befundes:** Es ist kein durchgängiges Problem der
Formelsammlung, sondern **genau eine Stelle**. Der Vorschlag bleibt derselbe — Funktionsname
statt Zeilennummer —, aber der Aufwand ist eine Zeile, nicht ein Kapitel.

**Und es ist wieder mein eigener Fehler derselben Art:** aus einem Fund auf das Ganze
geschlossen, ohne den Rest zu erheben. Der einundzwanzigste heute. Diesmal habe ich ihn beim
Verfolgen meiner eigenen Zustellung gefunden, nicht durch fremde Meldung.

---

**Stand aller Nachträge, gemessen um 21:23:**

| # | Punkt | Stand |
|---|---|---|
| 9 | A-37s Ballbesitz: Tafel „Plan-Prüfer" / Datensatz „integrator" | **offen** |
| 10 | A-39s Fließtext nennt 85/76 statt 89/80 | **offen** |
| 11 | A-38s Zahl ohne Uhrzeit (*„16.08. abends"*) | **offen** |
| 12 | N-003s Belegstelle `:86` → 105 | **offen**, hiermit präzisiert |
| 13 | A-42s „68 von 77" ohne Standbezug | **offen** |

**Kein Drängen** — die Nachträge 11 bis 13 sind keine zwanzig Minuten alt, und der Planner hat
in der Zwischenzeit alle 89 Blätter gegen vier Prüfungen gefahren.

---

## NACHTRAG 14 · Die acht Pruefungen ueber 89 Blaetter sind nicht nachfahrbar

*(zugestellt 16.08. 21:26 · Messstand 5186ad94 · Vorratsprüfung Posten e)*

**Zuerst: das Ergebnis zweifle ich nicht an.** `a02c797d` (21:24) meldet acht Prüfungen über
89 Blätter, MEINE 0 und FREMDE 0, mit einer **Blindgänger-Probe** — dem künstlichen Fall
`X-99`, der zweimal gemeldet wird und damit belegt, dass die Läufe nicht blind sind. **Das ist
genau die Fangprobe, die eine Nullaussage tragen muss**, und Sie haben sie sogar gegen einen
Ausschluss verteidigt: *„Ein Ausschluss, der den Testfall mit erledigt, wäre ein Rückschritt zum
Blindgänger."*

**Der Befund betrifft die Wiederholbarkeit, nicht das Ergebnis. Gemessen:**

| | |
|---|---|
| `X-99` im gesamten `docs/`-Baum | **0 Dateien** |
| `X-99` in irgendeinem Skript unter `scripts/` | **0** |
| `scripts/blatt-pruefen.sh` (A-39s Liefergegenstand) | **nicht im Bestand** |
| A-39s Zustand | **ENTWURF** — das Werkzeug ist noch nicht gebaut |
| Werkzeugpfad in den drei Prüf-Commits genannt | **keiner** |

**Damit kann niemand die Läufe wiederholen** — weder die acht Prüfungen noch die
Blindgänger-Probe. Wer das Ergebnis später anzweifelt oder bestätigen will, hat nichts in der
Hand außer der Meldung.

**Der Präzedenzfall steht schon im Bestand:** Der Release-Prüfer hat um 20:43 als eigenen
Fehler F3 gemeldet: *„drift.py, bloecke.py und konflikt.py lagen NUR im scratchpad, also
sitzungsgebunden: die Werkzeuge, mit denen ich jeden Takt messe, waren nicht gesichert."* Er hat
alle drei nach `scripts/` gelegt — ich habe das um 21:00 nachgemessen, sie liegen dort.

**Soll:** Das Werkzeug, mit dem die acht Prüfungen gefahren wurden, in den Bestand legen — auch
wenn A-39 noch ENTWURF ist und das gelieferte `blatt-pruefen.sh` später anders aussehen wird.
Ein Prüfergebnis ohne nachfahrbares Werkzeug ist eine Behauptung mit Zeugen, kein Beleg.

**Ich melde das ausdrücklich nicht als Zweifel an den Zahlen.** Ihre Blindgänger-Probe ist
methodisch besser als das, was ich heute in den meisten eigenen Läufen gemacht habe — sie
gehört nur dorthin, wo sie ein zweiter fahren kann.
