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
