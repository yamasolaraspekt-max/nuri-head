# W-05/1 — Raum anwählen. Und der Name ist ausdrücklich NICHT dabei, weil ein erkannter Raum keine Identität hat

```yaml
auftrag: "W-05/1"
werkzeug: "W-05 Raum erkennen"
art: "BAU — die erkannten Räume anwählbar machen. Kein Name, kein Schema, keine Migration."
spur: A
heimat_app: ticket
dor_beleg: "steht aus — plan-pruefer."
status_steht_in: docs/STATUS.md
basis_sha: c09dcb93
prioritaet: P2
ballbesitz: "plan-pruefer (DoR)"
claim: "planner 13.08. — Claim VOR dem Schnitt."
anlass: "Erster Posten aus der Frontend-Bestandsaufnahme des Release-Prüfers (Vorlage vom 12.08.,
         STATUS.md:5355). Sein Vorschlag: 'Raum anwählen, benennen, Fläche ablesen — kleinster
         Schnitt, größter Nutzen.' Ich habe es gemessen: ein Drittel ist gebaut, ein Drittel ist
         klein, und ein Drittel ist eine Entscheidung Yamas."
grundlage: "geometry/roomDetection.ts:35-40 (der Typ) · app/rahmen/Buehne.tsx:139-156 (die Zeichnung)
            · Buehne.tsx:104/165/174/190 als Auswahlmuster aus W-13 · sechs Produktivaufrufer"
```

## 1 — Der Vorschlag zerfällt in drei ungleiche Teile, und das ist der tragende Punkt

**Selbst gemessen, jede Stelle geöffnet:**

```text
„Flaeche ablesen"   IST GEBAUT. Buehne.tsx:139 traegt den Kommentar
                    „Raeume: Fuellung + Flaeche (m², aus mm² gerundet auf 2 Stellen)",
                    :148 zeichnet das gefuellte Polygon, :152 die Zahl in m².
                    Der Nutzer SIEHT die Flaeche jedes Raums bereits.

„Raum anwaehlen"    FEHLT, und es ist klein: Buehne.tsx:147 setzt
                    listening={false} auf die Raum-Gruppe. Das Auswahlmuster
                    existiert daneben (Waende, :165 und :190: ausgewaehlt ?
                    FARBEN.auswahl : …).

„Raum benennen"     FEHLT UND IST NICHT KLEIN — siehe Abschnitt 2. Der Typ
                    ErkannterRaum (roomDetection.ts:35-40) traegt polygon,
                    kanten, flaecheMm2 und volumenMm3. KEINE id.
```

> **Ein Drittel des Vorschlags ist erledigt, und das gehört gesagt** — *sonst baut jemand die
> Flächenanzeige ein zweites Mal, oder er hakt sie als Leistung ab, wo nichts geschehen ist. **Der
> Release-Prüfer hat ausdrücklich vorgelegt statt geschnitten** („den Schnitt macht der Planner"); die
> Messung war meine Aufgabe.*

## 2 — Warum der Name eine Entscheidung Yamas ist und nicht ein Eingabefeld

```text
roomDetection.ts:35-40   ErkannterRaum { polygon, kanten, flaecheMm2, volumenMm3 }
                         -> KEINE id, KEIN name
Buehne.tsx:147           key={`raum${i}`}
                         -> die Identitaet ist der INDEX IN DER LISTE
```

> **Räume werden ERKANNT, nicht gezeichnet.** *Sie sind ein **abgeleitetes** Ergebnis aus den Wänden —
> bei jeder Ableitung neu berechnet. **Ein Name ist dauerhaft, ein Index ist es nicht:** wer eine Wand
> verschiebt oder eine hinzufügt, ändert die Liste, und der Name hängt danach am falschen Raum. **Das ist
> kein Umsetzungsdetail, das ist die Frage, woran die Identität eines abgeleiteten Objekts hängt.***

**Und es ist dieselbe Klasse wie die offene ZoneNode-Frage** — *dort geht es um `materialId` an einem
abgeleiteten Knoten, hier um einen Namen an einem abgeleiteten Raum. **Beides liegt bei Yama, und beides
darf nicht still automatisiert werden** (CLAUDE.md: „Fach-, … Datenbankentscheidungen werden nicht still
automatisiert").*

*Ein Name müsste außerdem ins **Szenendokument** — Schema, Zod, Migration an Bestandsdaten. **Der
Klappzustand der Schienen durfte in den `localStorage`, weil er eine Bedienereinstellung ist; ein
Raumname ist eine Eigenschaft des Gebäudes und gehört ins Dokument.** Das ist die Grenze, die
`schienenSpeicher.ts` in seinem Kopf selbst zieht.*

## 3 — Was dieser Auftrag baut, und warum die Auswahl am Index hängen DARF

```text
Die Auswahl ist FLUECHTIG: sie lebt in der Sitzung und wird nicht gespeichert.
Deshalb darf sie am Index haengen — mit EINER Auflage:
  aendert sich die Raumliste, wird die Auswahl ZURUECKGESETZT.
Sonst zeigt eine bestehende Auswahl nach einem Wandzug auf einen ANDEREN Raum,
und der Nutzer sieht eine Hervorhebung, die etwas anderes meint als er wollte.
```

> **Genau diese Auflage ist der Unterschied zwischen „klein" und „falsch".** *Eine flüchtige Auswahl am
> Index ist zulässig; eine, die einen Wandzug überlebt, ist eine Falschauskunft — **dieselbe Klasse wie
> die Panel-Zusage in A-24**, nur an der Auswahl statt am Hinweis.*

## 4 — Scope

```text
W-05/1 IST  die erkannten Raeume anwaehlbar machen: listening frei, Klick waehlt,
            der gewaehlte Raum wird hervorgehoben, und die Auswahl wird
            zurueckgesetzt, wenn sich die Raumliste aendert.
            Das Auswahlmuster wird aus dem Bestand uebernommen (Buehne.tsx:165
            und :190), nicht neu erfunden.

W-05/1 IST NICHT
            der NAME. Er braucht eine Identitaet, die es nicht gibt, und damit
            eine Entscheidung Yamas — als GRENZE benannt, nicht gebaut.
            die FLAECHENANZEIGE -> gebaut (Buehne.tsx:152), wird nicht angefasst.
            ein neues WERKZEUG in der Registry. Raeume sind kein Werkzeug, sie
            sind ein abgeleitetes Ergebnis; die Auswahl gehoert an das
            vorhandene 'auswahl'-Werkzeug (W-13).
            das SZENENDOKUMENT. Kein Feld, kein Zod, keine Migration.
            F-010 und F-011 -> in roomDetection.ts:70 gebaut und im Register mit
            Haken gefuehrt.
```

## 5 — Abnahmekriterien

```text
W-05-1-1 (P1, TRAGEND) Die Auswahl wird ZURUECKGESETZT, wenn sich die Raumliste
         aendert. Nachweis in beide Richtungen:
         (a) Raum waehlen, Wand verschieben oder hinzufuegen, und die
             Hervorhebung ist weg — nicht auf einem anderen Raum.
         (b) und die Zusage haelt als WAECHTER, nicht als Sichtprobe: ein Test,
             der die Raumliste veraendert und belegt, dass der Auswahlzustand
             leer ist.
         BEGRUENDUNG, damit das nicht fuer Vorsicht gehalten wird: die Identitaet
         eines erkannten Raums ist heute der INDEX (Buehne.tsx:147 key raum+i).
         Eine Auswahl, die einen Wandzug ueberlebt, zeigt auf einen anderen Raum
         als den gewaehlten. Das ist eine Falschauskunft, dieselbe Klasse wie die
         Panel-Zusage in A-24 — nur an der Auswahl statt am Hinweis.
W-05-1-2 (P1) Das Auswahlmuster kommt AUS DEM BESTAND und ist keine zweite
         Wahrheit: Buehne.tsx:165 (Klickbehandlung beim Werkzeug 'auswahl') und
         :190 (ausgewaehlt ? FARBEN.auswahl : …). Der Bau nennt die Stellen, die
         er nachahmt, und begruendet jede Abweichung zeilenweise.
W-05-1-3 (P1, SCHUTZGRENZE) KEIN Feld am Szenendokument, KEIN Zod, KEINE
         Migration, KEIN Name. Nachweis am Bau-Commit: die Schema-Dateien kommen
         darin NULL Mal vor.
         BEGRUENDUNG: ein Raumname ist eine Eigenschaft des Gebaeudes und gehoert
         ins Dokument — aber er braucht vorher eine Identitaet, und die ist eine
         Entscheidung Yamas. Wer den Namen jetzt baut, entscheidet sie still.
W-05-1-4 Die FLAECHENANZEIGE bleibt unveraendert: Buehne.tsx:152 zeichnet sie
         heute, und sie wird nicht angefasst. Gegenprobe per Diff, dass die Zeile
         im Bau-Commit unveraendert ist oder nur durch die Auswahl-Hervorhebung
         beruehrt wird — dann zeilenweise begruendet.
W-05-1-5 Die vorhandenen Waechter bleiben gruen, insbesondere die, die Buehne.tsx
         ueber ihre QUELLE verriegeln. Am Bau-Stand erheben, welche das sind —
         nach W-36, W-37 und W-06 ist die NUR-QUELLE-Klasse in dieser Insel
         verbreitet, und ein Import-Muster findet sie nicht.
W-05-1-6 Die Fangprobe wird GEFAHREN und belegt: die Ruecksetzung aus W-05-1-1
         entfernen und zeigen, dass der Waechter rot wird. Nicht gefahren heisst
         'nicht gefahren' im Bericht.
W-05-1-7 Browserabnahme nach den Arbeitsregeln, weil UI beruehrt wird — und mit
         der Buendel-Gegenprobe AM COMMIT: eine Marke des neuen Baus im
         ausgelieferten public/hausplaner/hausplaner.js, gezaehlt per
         `git show <bau-sha>:<buendel> | grep -c <marke>`. Eine Marke, die nur im
         Arbeitsbaum steht, belegt nichts — A-24 hat genau daran gehangen.
```

**Nachweisform: Befehl und Trefferzeilen** (B5), **Messung am COMMIT** (E1),
**Nachweis muss rot werden können** (Pflichtprüfung 4), **jede Zahl an zwei Mustern** (Prüfung 7).

```yaml
was_ich_am_vorschlag_geaendert_habe_und_warum: "Der Release-Pruefer schlug 'Raum anwaehlen, benennen,
        Flaeche ablesen' als EINEN kleinen Schnitt vor. Gemessen ist es kein einheitlicher Schnitt: die
        Flaeche ist GEBAUT (Buehne.tsx:152), das Anwaehlen ist klein (listening={false} an :147), und der
        Name braucht eine Identitaet, die der Typ nicht hat (roomDetection.ts:35-40, keine id). Er hat
        ausdruecklich vorgelegt statt geschnitten und die Rollengrenze benannt — die Messung war meine
        Aufgabe, und sie hat den Auftrag KLEINER gemacht. Das ist die dritte Ablesung heute, bei der eine
        Reichweitenmessung einen Befund verkleinert statt vergroessert."
warum_der_name_zu_yama_gehoert_und_nicht_in_diesen_auftrag: "Raeume sind ein ABGELEITETES Ergebnis aus
        den Waenden, kein gezeichnetes Objekt. Ihre heutige Identitaet ist der Index in der Liste
        (Buehne.tsx:147). Ein Name ist dauerhaft, ein Index nicht: nach einem Wandzug haengt er am
        falschen Raum. Das ist dieselbe Klasse wie die offene ZoneNode-Frage — dort materialId an einem
        abgeleiteten Knoten, hier ein Name an einem abgeleiteten Raum. Dazu muesste er ins
        Szenendokument, also Schema und Zod und Migration an Bestandsdaten. Der Klappzustand der
        Schienen durfte in den localStorage, weil er eine Bedienereinstellung ist; ein Raumname ist eine
        Eigenschaft des Gebaeudes. Diese Grenze zieht schienenSpeicher.ts in seinem eigenen Kopf."
die_auflage_ist_der_ganze_auftrag: "Ohne W-05-1-1 waere dies ein Zweizeiler: listening frei und eine
        Hervorhebung. Mit ihr ist es ein Auftrag, der eine Falschauskunft verhindert. Eine Auswahl, die
        einen Wandzug ueberlebt, zeigt auf einen anderen Raum als den gewaehlten — und der Nutzer hat
        keine Chance, das zu merken, weil die Hervorhebung gleich aussieht."
W_05_1_nimmt_keinen_paragraf3_platz: "ENTWURF, nicht IN_ARBEIT. A-25 haelt den Platz."
```
