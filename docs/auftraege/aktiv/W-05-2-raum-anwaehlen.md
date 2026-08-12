# W-05/2 — Raum anwählen. Und der Name ist ausdrücklich NICHT dabei, weil ein erkannter Raum keine Identität hat

```yaml
auftrag: "W-05/2"
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
kennung_berichtigt: "Geschnitten als W-05/1 — die Kennung war VERGEBEN. Der plan-pruefer hat es in
                     fb893d1c gemessen: docs/STATUS.md:1621 traegt W-05/1 als BETRIEBSBESTAETIGT
                     (Die sieben Blaetter von W-05 aus roomDetection.ts ableiten). Zwei Auftraege,
                     eine Kennung, zwei widersprechende Zustaende — §16 im Kern. Und der Suffix /1
                     ist der NACHBESSERUNGS-Suffix (W-40/1, W-27/1, W-09/1, W-13/1); der alte
                     W-05/1 IST die Nachbesserung von W-05, dieser Auftrag ist ein BAU.
                     Neue Kennung W-05/2, gemessen frei; W-07/2 ist der Praezedenzfall fuer einen
                     Zweit-Suffix. MEIN FEHLER: Pflichtpruefung 1 fragt, ob das WERKZEUG schon
                     existiert — ich habe sie auf das Werkzeug angewandt und nicht auf die KENNUNG."
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
W-05/2 IST  die erkannten Raeume anwaehlbar machen: listening frei, Klick waehlt,
            der gewaehlte Raum wird hervorgehoben, und die Auswahl wird
            zurueckgesetzt, wenn sich die Raumliste aendert.
            Das Auswahlmuster wird aus dem Bestand uebernommen (Buehne.tsx:165
            und :190), nicht neu erfunden.

W-05/2 IST NICHT
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
W-05-2-1 (P1, TRAGEND) Die Auswahl wird ZURUECKGESETZT, wenn sich die Raumliste
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
W-05-2-1b (P1) NACHGETRAGEN 13.08. nach dem NACHBESSERN-Votum (76b9ae6f), und die
         UNSCHAERFE WAR MEINE: W-05-2-1 verlangt die Ruecksetzung, wenn sich die
         Raumliste AENDERT — und sagt nicht, WORAN 'geaendert' gemessen wird.
         WAS DARAUS ENTSTAND, vom Evaluator am gebauten Code gefahren:
         app/raumAuswahl.ts bildet den Fingerabdruck aus Anzahl, Flaeche und
         Eckenzahl OHNE DEN ORT, und begruendet es woertlich damit, zwei so
         uebereinstimmende Raeume seien 'fuer die Frage zeigt der Index noch auf
         denselben Raum nicht unterscheidbar'. SIE SIND ES — durch ihren ORT, und
         genau den sieht der Nutzer. Gefahren: zwei Raeume gleicher Flaeche und
         Eckenzahl an verschiedenen Orten, Reihenfolge getauscht, Signatur
         IDENTISCH, gueltigeAuswahl liefert 0 statt null; gewaehlt war x=0,
         hervorgehoben wird x=900. Woertlich die Falschauskunft, die W-05-2-1
         verhindern soll.
         DIE ZUSAGE, und sie schreibt keinen Weg vor: zwei Raumlisten, die sich
         NUR IM ORT ihrer Raeume unterscheiden, muessen fuer die Signatur
         VERSCHIEDEN sein. Wie das erreicht wird — Bbox-Mitte, erster Eckpunkt,
         ganzes Polygon — ist Bauform und gehoert dem Bauenden; der Evaluator
         nennt ausdruecklich zwei zulaessige Wege.
         WAS AUSDRUECKLICH BLEIBT: die Rundung der Flaeche auf ganze mm². Der
         Bauende hat sie begruendet, damit sich zwei Ableitungen desselben
         Grundrisses nicht wegen eines Gleitkomma-Restes unterscheiden — sonst
         setzte die Auswahl sich bei jedem Rendern zurueck und die Auflage waere in
         ihr Gegenteil verkehrt. Das ist richtig und wird nicht angefasst.
W-05-2-1c (P1) DER WAECHTER MUSS DEN NICHT-TRIVIALEN FALL FAHREN, und auch das ist
         meine Luecke: W-05-2-1(b) verlangte 'ein Test, der die Raumliste
         veraendert' — erfuellbar mit zwei Raeumen VERSCHIEDENER Flaeche, dem
         trivialen Fall. Genau den fuhr der gebaute Test, waehrend sein Name
         'die REIHENFOLGE zaehlt' das Gegenteil verspricht; seine Hilfsfunktion
         kennt gar keine Position.
         VERLANGT IST DER FALL, DER WEHTUT: zwei Raeume mit GLEICHER Flaeche und
         GLEICHER Eckenzahl an VERSCHIEDENEN Orten, Reihenfolge getauscht — und
         die Zusage, dass die Auswahl danach leer ist oder auf denselben Raum
         zeigt. Ein Test, der den trivialen Fall fuehrt und den Namen des
         Kriteriums traegt, ist schlimmer als keiner: er behauptet Deckung.
W-05-2-2 (P1) Das Auswahlmuster kommt AUS DEM BESTAND und ist keine zweite
         Wahrheit: Buehne.tsx:165 (Klickbehandlung beim Werkzeug 'auswahl') und
         :190 (ausgewaehlt ? FARBEN.auswahl : …). Der Bau nennt die Stellen, die
         er nachahmt, und begruendet jede Abweichung zeilenweise.
W-05-2-3 (P1, SCHUTZGRENZE) KEIN Feld am Szenendokument, KEIN Zod, KEINE
         Migration, KEIN Name. Nachweis am Bau-Commit: die Schema-Dateien kommen
         darin NULL Mal vor.
         BEGRUENDUNG: ein Raumname ist eine Eigenschaft des Gebaeudes und gehoert
         ins Dokument — aber er braucht vorher eine Identitaet, und die ist eine
         Entscheidung Yamas. Wer den Namen jetzt baut, entscheidet sie still.
W-05-2-4 Die FLAECHENANZEIGE bleibt unveraendert: Buehne.tsx:152 zeichnet sie
         heute, und sie wird nicht angefasst. Gegenprobe per Diff, dass die Zeile
         im Bau-Commit unveraendert ist oder nur durch die Auswahl-Hervorhebung
         beruehrt wird — dann zeilenweise begruendet.
W-05-2-5 Die vorhandenen Waechter bleiben gruen, insbesondere die, die Buehne.tsx
         ueber ihre QUELLE verriegeln. Am Bau-Stand erheben, welche das sind —
         nach W-36, W-37 und W-06 ist die NUR-QUELLE-Klasse in dieser Insel
         verbreitet, und ein Import-Muster findet sie nicht.
W-05-2-6 Die Fangprobe wird GEFAHREN und belegt: die Ruecksetzung aus W-05-2-1
         entfernen und zeigen, dass der Waechter rot wird. Nicht gefahren heisst
         'nicht gefahren' im Bericht.
W-05-2-7 Browserabnahme nach den Arbeitsregeln, weil UI beruehrt wird — und mit
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
die_auflage_ist_der_ganze_auftrag: "Ohne W-05-2-1 waere dies ein Zweizeiler: listening frei und eine
        Hervorhebung. Mit ihr ist es ein Auftrag, der eine Falschauskunft verhindert. Eine Auswahl, die
        einen Wandzug ueberlebt, zeigt auf einen anderen Raum als den gewaehlten — und der Nutzer hat
        keine Chance, das zu merken, weil die Hervorhebung gleich aussieht."
W_05_2_nimmt_keinen_paragraf3_platz: "ENTWURF, nicht IN_ARBEIT. A-25 haelt den Platz."
```

---

## 6 — Votum des Evaluators (§11)

**NACHBESSERN**, Fehlerklasse **`CODE`** (§12.1), Ball beim **Generator**. Bau `83d6e108` — von mir
gesucht, nicht aus einem Feld genommen —, Elter `424adacc`. Zwei Prüfstände mit `node_modules` und
`vendor`.

**Der Befund: die Signatur ist blind für den Fall, den ihre eigene Begründung ausschließt.**
`raumAuswahl.ts:51` bildet den Fingerabdruck aus **Anzahl, Fläche und Eckenzahl** — ohne den Ort.
Die Begründung darüber (`:44`) sagt: *„zwei Räume, die in Anzahl, Fläche und Eckenzahl
übereinstimmen, sind für die Frage ‚zeigt der Index noch auf denselben Raum?' nicht
unterscheidbar."* **Das trifft nicht zu — sie sind durch ihren Ort unterscheidbar, und der Nutzer
sieht genau den.** Selbst gefahren, gegen den gebauten Code:

```text
zwei Raeume, gleiche Flaeche (10 000 000 mm²), gleiche Eckenzahl (4), verschiedene Orte
  Signatur vorher : 2|10000000:4,10000000:4
  Signatur nachher: 2|10000000:4,10000000:4      (Reihenfolge getauscht)
  gleich? true
  gueltigeAuswahl(...) -> 0        statt null
  gewaehlt war x=0 — hervorgehoben wird x=900
  ZEIGT AUF DENSELBEN RAUM? false
```

**Das ist wörtlich die Falschauskunft, die W-05-2-1 verhindern soll** („eine Auswahl, die einen
Wandzug überlebt, zeigt auf einen **anderen** Raum als den gewählten"), und dieselbe Klasse wie die
Panel-Zusage in A-24 — der Auftrag sagt das selbst.

**Und der Wächter deckt es nicht, obwohl sein Name das Gegenteil verspricht.**
`raumAuswahl.test.ts:92` heißt *„die REIHENFOLGE zählt — zwei vertauschte Räume sind nicht dieselbe
Liste"* und kommentiert: *„Eine Signatur, die das nicht sieht, wäre blind für genau den Fall."*
Gemessen: er läuft über `ZWEI = [raum(12_000_000), raum(8_000_000)]` — **verschiedene Flächen**, der
triviale Fall. Die Hilfsfunktion `raum(flaeche, ecken)` erzeugt das Polygon aus der *Eckenzahl* und
kennt gar keine Position, kann den kritischen Fall also nicht bilden. **Der Test trägt den Namen
eines Kriteriums und misst etwas anderes** — die wiederkehrende Fehlerklasse, hier an dem Wächter,
der sie fangen soll. Der Bericht führt ihn als `✔` und erwähnt die Lücke nirgends.

**Was ich ausdrücklich NICHT belegt habe:** dass der Fall über die Oberfläche erreichbar ist. Die
Raumreihenfolge entsteht aus einer Halbkanten-Traversierung (`roomDetection.ts:131`, nach Winkel
sortiert), nicht aus der Position — sie *kann* sich also ändern, ohne dass Fläche oder Eckenzahl
sich ändern; ob ein realer Bedienweg das erzeugt, ist offen. **Der Befund steht am Code und an der
Begründung, nicht an einem vorgeführten Bedienweg.** Die Nachbesserung hat deshalb zwei zulässige
Wege, und der Generator wählt: den Ort in die Signatur aufnehmen, **oder** Begründung, Testname und
Testkommentar auf das einschränken, was tatsächlich gemessen wird.

| Kriterium | Befund | Wie ich es selbst gemessen habe |
|---|---|---|
| **W-05-2-1** (TRAGEND) | **ROT im Randfall, grün im Kern** | (a) Der Mechanismus trägt: `gueltigeAuswahl` gibt `null`, sobald die Signatur abweicht. (b) Der Wächter existiert und greift — aber die Signatur ist blind für Vertauschung bei gleicher Fläche **und** Eckenzahl, und der Test, der das prüfen soll, prüft es nicht (oben) |
| **W-05-2-2** | **grün** | Das Muster stammt aus `Buehne.tsx` — Klickbehandlung beim Werkzeug `auswahl` und `ausgewaehlt ? FARBEN.auswahl : …`; keine zweite Wahrheit, die Auswahl ist flüchtiger Zustand in `HausplanerApp.tsx` |
| **W-05-2-3** (SCHUTZGRENZE) | **grün** | Schema/Zod/Migration im Bau-Commit: **0 Dateien**, **0 Code-Treffer**. *(Mein erster Lauf zählte 1 — der Treffer stand in der **Commit-Botschaft**, nicht im Code.)* |
| **W-05-2-4** | **grün** | Die Flächenanzeige ist nicht angefasst; `Buehne.tsx` ändert sich nur an der Auswahl-Hervorhebung |
| **W-05-2-5** | **grün** | Insel-Suite **1728/1728** am Bau gegen **1718/1718** am Elter — **+10** Tests, keine Regression |
| **W-05-2-6** | **grün** | Fangprobe selbst gefahren, Anker genau 1×: die Signaturprüfung aus `gueltigeAuswahl` entfernt → **3 von 10 rot**, alle drei W-05-2-1-Zusagen. md5 zurückgesetzt, identisch |
| **W-05-2-7** | **teils belegt, teils NICHT GEFAHREN** | **Belegt, und es ist der Teil, den A-24 erzwungen hat:** das Bündel liegt im Bau-Commit und ist die ausgelieferte Datei — `43d053ef7e72b6903766ba6822f56749` am Commit **und** von der Bühne geholt, gegen `bec7d900…` am Elter. Bühnen-Wächter vor allem anderen: vier Bühnen, alle `ticket_testing`. Canvas bei 1440/1024/375 im Viewport sichtbar. **NICHT gefahren habe ich den Ablauf** „Raum wählen → Wand verschieben → Hervorhebung weg": ich bekomme über die Oberfläche keinen **erkannten** Raum zustande — nach vier gezogenen Wänden meldet die Bühne `Räume: 0`, und die Konva-Bühne trägt 78 Linien mit ausschließlich der Wandband-Füllung `#4b5563`, keine Raumfarbe. Der Weg des Generators führt über ein vorbereitetes Objekt (`/admin/hausplaner/objekt/10229`); in `ticket_testing` stehen heute **0** `hausplaner_documents` und **0** `projects`. Testdaten anzulegen wäre Bauarbeit, nicht Prüfarbeit |

**Meine eigenen Messfehler in dieser Runde:**

1. **Beinahe „die Insel lädt nicht" gemeldet.** Die Seite kam ohne einen einzigen Knopf. Ursache
   war nicht der Bau: mein Prüfnutzer war weg, `/studio` leitete still auf `/login` um — die
   Testdatenbank ist zwischenzeitlich zurückgesetzt worden (`users gesamt: 1` nach dem Neuanlegen).
2. Mein `zod|migration`-Muster lief über die ganze `git show`-Ausgabe **einschließlich der
   Commit-Botschaft** und meldete 1 Treffer an einer Schutzgrenze, die 0 hat.
3. Relativer Import aus `/tmp` für die Gegenprobe — `MODULE_NOT_FOUND`, derselbe Griff wie bei
   W-34 und A-22. Im Prüfstand gelöst.

**§15 belegt:** `getDatabaseName()` = `ticket_testing`, vor dem Anlegen des Prüfnutzers gemessen.

**Weiter an den Generator**, Umfang **genau dieser Befund** (§12.2).
