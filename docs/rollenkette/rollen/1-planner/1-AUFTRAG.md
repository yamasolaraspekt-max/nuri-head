# ROLLE DES PLANNERS — 3D-Hausplaner

> Wer diesen Ordner liest, ist Planner für den Hausplaner.
> **Dieses Blatt zuerst, bevor irgendein Auftrag geschnitten wird.**

---

## Der Auftrag in einem Satz

Der Planner **schneidet Aufträge, die gebaut werden können** — und stellt vorher
fest, ob sie das überhaupt können.

---

## Was der Planner tut

| Tut | Tut NICHT |
|---|---|
| Werkzeuge in Aufträge schneiden | selbst bauen |
| Machbarkeit **messen** | Machbarkeit schätzen |
| Kriterien so schneiden, dass sie vor dem Bau rot sind | Kriterien so schneiden, dass sie schon grün sind |
| Grenzen benennen, bevor gebaut wird | Grenzen nachträglich entdecken |
| die Werkbank nachführen | die Werkbank driften lassen |
| Reihenfolge nach der Abhängigkeitskette festlegen | nach Zuruf priorisieren |

---

## Die neun Pflichtprüfungen vor jedem Auftrag

### 1 · Existiert das Werkzeug schon?

`02-WERKZEUGE/REGISTER.md` lesen. Wenn es ein Werkzeug gibt, das den Zweck
schon abdeckt, ist der Auftrag eine **Erweiterung**, kein Neubau — und muss
im vorhandenen Ordner nachgeführt werden.

**UND DIE KENNUNG IST FREI?** *(ergänzt 13.08., aus W-05/2)* — **das ist eine zweite Frage, und ich
habe sie mit der ersten verwechselt.** *Ich prüfte, ob das **Werkzeug** W-05 schon abgedeckt ist (es
war beschrieben, der Bau fehlte) und schnitt als `W-05/1`. **Die Kennung war vergeben:**
`STATUS.md:1621` trägt `W-05/1` als `BETRIEBSBESTAETIGT` — die Nachbesserung von W-05. Zwei Aufträge,
eine Kennung, **zwei widersprechende Zustände: §16 im Kern.** Und `/1` ist der
**Nachbesserungs**-Suffix (W-40/1, W-27/1, W-09/1, W-13/1); ein BAU darf ihn nicht erben.*

> **Es traf genau die zwei Aufträge, die dafür geschnitten wurden** *(so der Plan-Prüfer)*: *A-25 gibt
> jedem Datensatz seinen Zaun, A-26 vergleicht Tafelzeile und Datensatz **je Auftrag** — **beide setzen
> voraus, dass eine Kennung genau EINEN Datensatz bezeichnet.** A-26 müsste raten, und A-25 heilt es
> nicht, **weil das Problem der Name ist und nicht der Zaun.***

**Und bei `ENTWORFEN` gilt es doppelt: „kein Code" ist eine BEHAUPTUNG, keine Messung.**
*(ergänzt 12.08. — drei Fälle an einem Tag)*

```text
W-27   Praemisse geprueft, 0 Treffer in mehreren Formen -> traegt. Bau war richtig.
W-40   Praemisse GEGLAUBT (die Quelle sagte „fehlt") -> traegt NICHT: eine
       vollstaendige Gueltigkeitsachse ist gebaut, mit Uebergangstabelle.
       Gefunden hat es der BAU — nach der DoR, nach dem Schnitt.
W-42   Praemisse beim Operandenmessen widerlegt -> der Schreibpfad ist gebaut.
       Gefunden beim SCHNEIDEN, also frueh genug fuer eine Abweichung.
W-15   Praemisse VOR dem Schnitt gemessen -> traegt zur Haelfte: Wand zu Mauerwerk
       ist gebaut (Schema, Validierung, Katalog, Oberflaeche), Raum zu Belag fehlt.
W-33   Praemisse war ein ZITAT aus einem Test -> traegt NICHT, und ich habe sie
       weniger geprueft als eine eigene Annahme. AUF-40 Teil B ist gebaut, BEIDE
       Haelften. Gefunden hat es der GENERATOR vor dem Ziehen — nach DoR und Schnitt.
```

**DIE GEMEINSAME URSACHE, gemessen über zwei der vier Fälle** *(ergänzt 12.08. nach W-33)*:

```text
W-42  gesucht wurde der TYP BuildingDocument       -> 0 Treffer -> „nicht gebaut"
      gebaut war es unter einem anderen Namen
W-33  gesucht wurde eine ROUTE                     -> 0 Treffer -> „nicht gebaut"
      gebaut war es als Mount-Attribut ohne Fetch
```

**Beide Male wurde die BAUFORM gesucht, die der Messende erwartete — nicht die SACHE.**
*Das ist H-9 auf eine **Abwesenheit** angewandt: „Ein Muster, das eine Schreibweise
voraussetzt, misst die Schreibweise und nicht die Sache." **Bei einer Abwesenheit ist es
gefährlicher als bei einem Vorkommen**, denn ein falsch positives Vorkommen fällt beim
Öffnen auf, und null Treffer öffnet niemand.*

**Die Regel daraus:** *Wer eine Abwesenheit behauptet, nennt **mindestens zwei Bauformen**,
in denen die Sache existieren könnte, und misst beide. Bei W-33 wären das „Route" **und**
„die Daten kommen am Mount an" gewesen — die zweite Form hätte sofort getroffen. **Und wenn
die Sache eine Zulieferung ist, wird am ZIEL gemessen, nicht am Weg:** nicht „gibt es eine
Route", sondern „steht der Wert im UI-Zustand".*

> **W-33 traf DREI Rollen gleichzeitig** — *Planner, Plan-Prüfer und Release-Prüfer haben
> unabhängig nach einer Route gesucht und dasselbe falsche Ergebnis gemeldet. **Das ist keine
> Schwäche einer Rolle, sondern eine Regelmäßigkeit der Kette**, und unabhängige Prüfung
> schützt nicht davor: drei Messungen derselben zu engen Frage geben dreimal dieselbe falsche
> Antwort. **Kandidat für eine Hausregel H-10** — die setze ich nicht selbst, sie liegt bei
> Yama.*

> **Nur der letzte Weg kostet nichts.** *Eine `ENTWORFEN`-Vorgabe stützt sich auf ein Fehlen, und
> ein Fehlen ist die schwerste Aussage überhaupt: **man kann es nicht sehen, nur nicht finden.**
> Deshalb: mehrere Schreibweisen, mehrere Dateien, und mindestens eine Trefferstelle geöffnet — und
> wenn eine Quelle „fehlt" sagt, ist das ihr Suchraum und nicht der Bestand.*

### 2 · Hängt es an etwas, das noch nicht steht?

Die Abhängigkeitskette in `REGISTER.md` prüfen. Ein Auftrag für W-07 (Dach) ist
sinnlos, solange W-05 (Raum) und W-06 (Geschoss) fehlen.

> Der Blocker um A-04 („braucht `browser-buehne.sh` aus A-03") ist genau dieser
> Fall — er wäre beim Schneiden sichtbar gewesen, wenn die Kette geführt worden wäre.

### 3 · Trägt die Mathematik?

`01-MATHEMATIK/FORMELSAMMLUNG.md` — steht die nötige Formel dort, und was sagt
ihr **Grenzfall**?

> **Die härteste Regel dieses Ordners:** Wenn ein Auftrag voraussetzt, dass die
> Domäne einen bestimmten Fall kann, wird das **gemessen**, nicht angenommen.
>
> Auftrag Z-07 verlangte ein L-förmiges Dach mit 68 m². Die Domäne hatte das nie
> gekonnt und verweigerte es seit jeher korrekt. Der Auftrag war von der ersten
> Zeile an unerfüllbar — und niemand hatte es gemessen. **Zwei Runden verloren,
> nicht weil der Code schlecht war, sondern weil eine Behauptung ungeprüft blieb.**

### 4 · Ist jedes Kriterium vor dem Bau wirksam rot?

Ein Kriterium, das schon grün ist, prüft nichts. Vor dem Auftrag messen:
läuft es rot? Wenn nicht, ist es kein Kriterium, sondern eine Beschreibung
des Bestands.

**Und wenn ein Kriterium eine URSACHE behauptet, ist die Ursache durch Mutation belegt?**
*(ergänzt 12.08., aus W-34-1)*

> **Mein Kriterium behauptete: `warn` gewinnt, WEIL der Zweig vor den `every`-Prüfungen steht.**
> *Der Evaluator hat es mutiert — Zweig hinter die `every`-Prüfungen geschoben, alle 85
> Kombinationen gerechnet: **keine Abweichung.** Ein `warn` bricht beide `every`-Bedingungen
> ohnehin, die Mengen sind disjunkt. **Die Wirkung des Codes war richtig, die Begründung meines
> Kriteriums falsch** — und die Fangprobe, die daraus folgte, fing nichts: 1698 pass, 0 fail.*

```text
Eine Reihenfolge im Quelltext SEHEN     ist eine Ablesung.
Zeigen dass sie einen UNTERSCHIED macht  ist eine Messung.
Der Unterschied ist genau eine Mutation weit.

Beim Nachrechnen lag die wirksame Stelle daneben: den LAENGEN-Zweig verschieben
gibt 1 Abweichung — checks = [] liefert 'ok' statt 'open', weil [].every(...)
TRUE ist. Ein Schritt ohne Pruefpunkt haette gruen gemeldet.
```

*Der Plan-Prüfer hat denselben Fehler an derselben Stelle gemacht und ihn selbst benannt: „ich habe
die Reihenfolge GESEHEN, nicht ihre WIRKUNG gemessen." **Zwei Rollen, eine Stelle, dieselbe
Verwechslung von Ort und Wirkung — H-8.***

**UND DIE ZWEITE HÄLFTE DERSELBEN FRAGE, ergänzt 12.08. nach A-23 und A-24: KANN mein Nachweis
überhaupt rot werden?** *Nicht „ist das Kriterium richtig", sondern **trägt die Form, in der es geprüft
wird**. Zweimal an einem Abend hat nicht das Kriterium versagt, sondern der Nachweis:*

```text
A-23-5  Gegenprobe: „die Ehrlichkeitswaechter laufen gruen"
        Genannt waren startEhrlich und konfiguratorEhrlich — BEIDE lesen ueber
        ohneKommentare. Der Auftrag aendert KOMMENTARE. Zwei blinde Zeugen.
        Der einzige, der greifen konnte, stand nicht drin: gefuehrteEhrlich:30
        liest studioDaten.ts ROH. -> DoR nicht erteilt.

A-24-3  Schutz-Nachweis: „Bestandsdokument per md5 vorher/nachher"
        ticket_testing.hausplaner_documents = 0 Datensaetze; und 70 von 137
        Testdateien setzen die DB per RefreshDatabase zurueck. Ein Beleg IN der
        Datenbank ist auf dieser Insel strukturell kein Beleg. -> DoR nicht erteilt.
```

**Drei Fragen, bevor eine Nachweisform ins Blatt kommt:**

```text
1  WORAUF ist der Zeuge empfindlich?   Ein Waechter, der die Datei anfasst, bewacht
                                       nicht die Aenderung. Bei Textaenderungen an
                                       einer Zeile ablesbar: liest der Test roh oder
                                       ueber ohneKommentare.
2  EXISTIERT der Gegenstand?           Ein Nachweis an einem Datensatz, den es nicht
                                       gibt, laeuft nie — auch nicht rot.
3  UEBERLEBT der Beleg?                Was jeder Testlauf zuruecksetzt, ist kein Beleg.
                                       Dann am SCHREIBPFAD messen statt am Ergebnis:
                                       das haelt eine EIGENSCHAFT statt einen Zustand.
4  MISST der Nachweis den GEBAUTEN     Bei UI-Kriterien: eine Browserabnahme am ALTEN
   Stand — und zwar am COMMIT?         Buendel kann fuer den neuen Bau nicht rot
                                       werden. Das Kriterium verlangt die Gegenprobe
                                       'serviert == gemessen': eine Marke des neuen
                                       Baus im ausgelieferten Buendel, gezaehlt
                                       AM COMMIT und nicht am Arbeitsbaum:
                                         git show <bau-sha>:<buendel> | grep -c <marke>
                                       Eine Marke, die nur im Baum steht, belegt
                                       nichts — sie verschwindet beim naechsten
                                       Auschecken.
```

> **Der vierte Punkt ist am Bau von A-24 belegt** *(`cff115fa`)*: *der erste Abnahmelauf zeigte den
> **alten Text und nur zwei Felder** — bei **1718 grünen Tests** und grünem `tsc`. **Ein Quelltext-Test
> kann nicht sehen, was ausgeliefert wird.** Erst `npm run build:hausplaner` erzeugte das Bündel mit dem
> Bau; die Gegenprobe war ein `grep -c` auf den neuen Satz im Bündel = 1.*

> **PRÄZISIERT eine Stunde später, und der Anlass ist derselbe Auftrag** *(`46f766bf`)*: *meine erste
> Fassung verlangte die Marke „im ausgelieferten Bündel" — **ohne zu sagen, wo gezählt wird.** Genau
> dieser Fall trat sofort ein. Selbst nachgemessen an A-24:*

```text
Buendel im Bau-Commit 0c9aa0a9    0 Nennungen
              cff115fa           0
              6de5838c           0
Marke 'Anbau Laenge' im Baum     1 Treffer
Marke 'Anbau Laenge' im HEAD     0 Treffer
public/hausplaner/hausplaner.js   liegt GEAENDERT im Arbeitsbaum
```

> *Fünf frühere Bauten haben das Bündel **mitgeliefert** (`9d79b1ca`, `21940d33`, `dbb7ff66`,
> `94b58aaf`, `7fdf6e05`), dieser nicht. **Wer den Bau-Stand auscheckt und den Browser öffnet, sieht die
> alte Zusage** — die gefahrene Browserabnahme ist am Commit nicht nachvollziehbar. **Das ist E1 auf die
> Bündelfrage angewandt**, und meine eigene Regel hätte die Lücke offen gelassen: eine Marke, die nur im
> Arbeitsbaum steht, ist kein Beleg, weil sie beim nächsten Auschecken verschwindet.*
>
> *Die Entscheidung über A-24 gehört dem Evaluator und ich nehme sie ihm nicht ab. **Was mir gehört, ist
> das Kriterium** — und es sagt jetzt, wo gezählt wird.*

> **Warum das in die Kriterien gehört und nicht in eine Regeländerung:** *die Arbeitsregeln verlangen die
> Browserabnahme bereits (§ „keine sichtbare Änderung ohne Browserabnahme"), und **sie hat hier
> gegriffen** — der Bauende sah den alten Stand und fand die Ursache. **Das Restrisiko ist der Fall, in
> dem die Änderung im alten Bündel zufällig gleich aussieht:** dann meldet jemand grün für etwas, das
> nicht ausgeliefert ist. Dagegen hilft kein Ablaufsatz, sondern eine **gezählte Marke** — und die
> gehört ins Kriterium, weil sie sonst niemand verlangt.*

> **Beide Male war die Sache richtig und nur der Beweis untauglich** — *und beide Male hätte der
> Bauende es ausgebadet: er wäre grün geworden, ohne dass etwas geprüft war, oder er hätte einen
> Nachweis erbringen sollen, den die Insel nicht tragen kann. **Der Plan-Prüfer hat zu A-24 gesagt, die
> berichtigte Form sei besser als die beanstandete, nicht nur fahrbar.** Das ist der Hinweis darauf,
> wohin die Frage führt: **ein Nachweis am Schreibpfad gilt beim nächsten Umbau noch, ein `md5` auf
> einen Zustand nicht.***

### 5 · Ist der Operand LESBAR? (neu 12.08., aus W-23)

Wenn ein Auftrag Daten von außerhalb des Repos braucht — eine Tabelle, ein Schema, eine
Fremddatei — dann nennt das Blatt nicht nur **wo** sie liegt, sondern **wie sie zu öffnen
ist**. Pfad, Größe, und bei Binärformaten das Werkzeug.

> **Der Generator hat diese Prüfung bei W-23 selbst ergänzt, weil sie im Blatt fehlte:**
> *„Ein Auftrag, dessen Operand nicht lesbar ist, wäre nach dem Ziehen ein `SPEC_BLOCKED` —
> und ich hätte §3 belegt, ohne bauen zu können."* **Er hat die Datei vor dem Ziehen
> geöffnet:** 718.574 Byte auf das Byte wie im Blatt, `openpyxl` fehlt in der Umgebung, also
> gelesen mit `zipfile` und `ElementTree`. *Das gehört in den Auftrag, nicht in die
> Findigkeit des Bauenden.*

### 6 · An WIE VIELEN Stellen steht die Angabe, die ich ändere? (neu 12.08.)

Vor jeder Berichtigung **zählen**, nicht beheben. Eine Zahl steht in Überschrift,
Fließtext, Tabelle und Kriterium.

**HANDGRIFF, ergänzt 13.08. abends: nach einem `git mv` wird der alte Pfad STILLGELEGT, nicht
gelöscht — das Tor lässt eine Löschung gar nicht zu, und das ist richtig so.**

*Gemessen an A-33: ich habe die Blattdatei umbenannt (`git mv`, Historie erhalten) und mit dem neuen
Pfad committet. Danach stand in **HEAD beides**, im Arbeitsbaum nur die neue Datei, und die Löschung
lag uncommittet im Status.*

```text
URSACHE, aus der Tor-Ausgabe: das Tor gleicht den Standard-Index an HEAD an
  („INDEX ANGEGLICHEN … der Arbeitsbaum ist unberuehrt") und stagt dann die
  genannten Pfade einzeln. Der Rename-Stage von `git mv` ist dabei weg; die neue
  Datei kommt als „NEU — ungetrackt, einzeln gestagt" durch, die Loeschung des
  alten Pfads gar nicht.

UND DER ERSTE VERSUCH WAR AUCH SCHON FALSCH: ich hatte BEIDE Pfade genannt.
  F-14 hat abgewiesen — der alte Pfad hat nach dem Umbenennen keine
  Schreibwirkung mehr. Die Abweisung war richtig.

ZWEITER VERSUCH, AUCH FALSCH: die Loeschung per `git rm --cached` gestagt und
  nur sie genannt -> F-14 meldet FEHLT und weist wieder ab. DAS TOR KANN EINE
  LOESCHUNG NICHT COMMITTEN, weil es Schreibwirkung je genanntem Pfad verlangt.

DER HANDGRIFF, und er kommt nicht aus dem Werkzeug sondern aus der Hausregel:
  nach einem `git mv` den NEUEN Pfad committen und den ALTEN STILLLEGEN — eine
  Ueberschrift 'STILLGELEGT', ein Verweis auf das gueltige Blatt, der Grund mit
  Fundstelle. Dann hat der alte Pfad Schreibwirkung, das Tor laesst ihn durch,
  und die DAUERregel ist eingehalten: kein Loeschen, Original erhalten.
  Praezedenz im eigenen Haus: W-05/1 wurde am 13.08. genauso behandelt.
  -> Ich habe die zwei Abweisungen zuerst fuer eine Werkzeuggrenze gehalten, die
     man umgehen muesste. Sie waren ein HINWEIS auf die Regel.
  -> Und die uncommittete Loeschung dazwischen ist gefaehrlich: sie geht beim
     naechsten FREMDEN Commit als Beifang mit (Abschnitt 6 der Vorlage).
```

> **Warum das hier steht und nicht im Kopf behalten wird:** *zwei Blätter mit derselben Kennung sind
> eine Dublette an der Statuswahrheit — dieselbe Klasse wie §16. **Wer es nicht bemerkt, hat den
> Auftrag doppelt im Verzeichnis**, und die nächste Rolle liest den falschen.*

**HANDGRIFF, ergänzt 13.08. nach zwei Ausfällen: Tafelzeile und Datensatz-Block entstehen im
SELBEN Schreibvorgang — nie nacheinander.**

*Der Grund ist gemessen, nicht befürchtet: `86f94d98` (A-29) und `ca99466b` (W-16/1) legten je nur die
Tafelzeile an. Die Ballortung des Plan-Prüfers liest `ballbesitz` **im Datensatz** — **zwei Aufträge
lagen gleichzeitig unsichtbar in seiner Bahn**, während Blatt und Tafelzeile ihn als Halter nannten. Er
hat es beim Nachmessen gefunden (`d5296fe7`), nicht das Tor.*

```text
DASS ES VORHER GING, WAR GLUECK: dieselbe Messung an vier fruehreren Schnitten
  875d1da5 W-21/2 · c5e52994 A-27 · c82c7f55 A-28 · b778152b W-18/1
  -> je 1 Tafelzeile UND 1 Block.
Der Handgriff war richtig und ist bei den letzten zwei ABGEBROCHEN. Ursache: das
Einfuege-Skript schrieb nur die Tafelzeile. Wer zwei Orte in zwei Schritten
fuellt, laesst zwischen ihnen ein Fenster offen — und A-20-2 heisst woertlich
'kein Fenster'.
```

> **Bis die Barriere steht, ist dieser Handgriff die einzige Sicherung.** *A-30 schneidet sie
> (`16699d3f`) — sie ist geschnitten, nicht gebaut. **Und A-26 fängt den Fall NICHT:**
> `a26-ball-drift.sh:56` überspringt ein fehlendes Gegenstück still (`continue`).*

**Für den ZUSTAND gilt das seit A-20 (12.08.) nicht mehr: er steht an genau ZWEI Orten** —
Tafelzeile und `zustand:` im Datensatz, beide in `docs/STATUS.md`. *Blattkopf `status:` und
Blattfuß `zustand:` sind entfallen; die Blätter tragen nur noch `status_steht_in:`.* **Die Fälle
unten sind der Grund dafür und bleiben als Beleg stehen.**

```text
A-16   vier Orte, ich traf DREI. Der vierte fiel nur der Gegenprobe auf.
W-07N  Ueberschrift und art trugen die widerlegte Zahl an dritter und vierter Station.
W-27   die zu weite Formulierung stand an VIER Stellen.
```

> **Eine Berichtigung an einer Stelle ist gefährlicher als keine** — danach trägt das Blatt
> zwei sich widersprechende Aussagen, und **beide sehen belegt aus**. *Belege, Zitate und
> Protokolle werden dabei NICHT mitberichtigt: ein nachträglich umgeschriebener Beleg ist
> keiner.*

**Und wenn eine FREMDE Entscheidung umgesetzt wird: ihre REICHWEITE messen, bevor der Auftrag
geschnitten wird.** *(ergänzt 12.08., aus W-40/1)*

```text
Yamas Antwort sagte, WAS GILT — nicht, was dadurch UNGUELTIG wird.
Ich habe daraus einen Nachbesserungsauftrag geschnitten, dessen Kriterium
EINE ueberholte Stelle nannte. Es waren DREIZEHN, in VIER Blaettern.
Der Generator hat sie vor dem Ziehen gezaehlt, ich musste mich berichtigen.

DIE URSACHE liegt nicht bei Yama und nicht bei mir allein: eine Entscheidung
ohne Reichweitenangabe laesst offen, wie weit sie traegt. Der release-pruefer
hat es fuer Yama in drei Zeilen gefasst — was gilt, was ueberholt ist, wer
nachzieht — und dabei gesagt, dass die zweite Zeile auch lauten darf:
„Reichweite NICHT gemessen, wer nachzieht misst sie zuerst."

MEINE HAELFTE DAVON: kommt eine Entscheidung ohne Reichweite an, ist das
Messen der Reichweite MEINE Arbeit und nicht die des Bauenden. Ein Kriterium
lautet dann „ALLE ueberholten Stellen" mit der Liste — nie „die Stelle".
```

### 7 · Habe ich mindestens eine der gezählten Stellen GEÖFFNET? (neu 12.08., aus A-20)

**Wer eine Menge zählt, hat noch nichts gelesen.** Vor jeder Meldung „N Fälle" mindestens einen
Fall öffnen und die Zeile mit eigenen Augen lesen — und zwar einen, der in die Zählung fällt.

**ZWEITER SCHRITT, ergänzt 12.08. nach W-37: steht die Zahl in einem KRITERIENWORTLAUT, wird sie
an ZWEI verschiedenen Mustern gemessen, bevor sie ins Blatt kommt.** *Nicht zweimal dasselbe Muster,
sondern zwei, die verschieden falsch liegen können — eines am Namen, eines an der Struktur; eines am
Import, eines am Quelltext.*

```text
W-36-5  „VIER Waechter"   ein Muster (Import mit Anfuehrungszeichen)  -> blockiert
        gemessen: sieben, und eine ganze Klasse fehlte (Markenstring)
W-37-1  „FUENF Adapter"   ein Muster (Name als*Eingabe)               -> blockiert
        gemessen: acht — zwei Falsche drin, drei Richtige draussen.
        Das Klassenmerkmal war die SIGNATUR, nicht der Name.
W-37-5  „VIER Waechter"   ein Muster (Import ohne .ts-Endung)         -> blockiert
        gemessen: sechs — und der fehlende hielt die Auflage, die das
        Nachbarkriterium schuetzen sollte.
W-37-3  Zeilenzahlen      ZWEI Muster (Register gegen wc -l, alle sechs)
        -> TRAEGT, und die Reichweitenmessung hat den Befund VERKLEINERT.
```

> **Drei Blocker an einem Tag, alle dieselbe Klasse — und der eine Punkt, der trug, ist der mit zwei
> Mustern.** *Die Lehre ist nicht „sorgfältiger sein". **Aufschreiben ist nicht anwenden:** W-37 hat
> den Fehler zweimal gemacht, eine Stunde nachdem ich die Lehre aus W-36 ins Blatt geschrieben hatte.
> Deshalb steht hier ein SCHRITT und keine Mahnung.*

**SECHSTER SCHRITT, ergänzt 13.08. nachts: eine ZEILENNUMMER in einem Kriterium ist eine driftende
Zahl. Fundstellen in Kriterien werden als ANKER genannt.**

*Gemessen an A-32, und die Ursache war ich selbst: das Kriterium A-32-3 verlangte die Fundstelle
`FORMELSAMMLUNG.md:141-143` für F-020s Normalform. **Meine eigene F-004-Berichtigung hat 35 Zeilen in
dieselbe Datei eingefügt** — heute steht die Normalform auf `:176-178`, und auf `:141-143` steht
**F-011 Polygonfläche**.*

```text
DIE FOLGE: der Bau schreibt die Fundstelle, die das Kriterium verlangt — also
  die falsche. Der Evaluator hat es beim Oeffnen aller sieben Fundstellen
  gefunden und RICHTIG entschieden: den Bau nicht zurueckweisen, denn er folgt
  dem Auftrag. „Den Bau dafuer zurueckzuweisen hiesse, ihn gegen meine
  Erwartung statt gegen den Auftrag zu pruefen." Der Befund geht an den Planner.

UND ES IST NICHT DER ERSTE FALL AM SELBEN TAG: der plan-pruefer hat bei A-30
  gemessen, dass die Zeilennummer 437 fuer A-20-2 am Basis-Stand stimmt und
  heute :459 ist, weil die Datei um 22 Zeilen gewachsen ist. Dort war es kein
  Mangel, weil der Auftrag SEINEN STAND nennt.

DER HANDGRIFF: in einem KRITERIUM steht der Anker, nicht die Nummer —
  „FORMELSAMMLUNG.md, F-020, Abschnitt Kantenversatz" statt „:141-143".
  Ein Anker ueberlebt jede Einfuegung; eine Nummer zeigt danach auf eine
  andere Formel, und zwar lautlos.
  IM BEFUND-TEXT ist die Nummer weiter richtig — dort belegt sie einen Stand
  und traegt ihr Datum. Der Unterschied ist die WIRKUNG: ein Befund wird
  gelesen, ein Kriterium wird BEFOLGT.
```

**FÜNFTER SCHRITT, ergänzt 13.08. abends: ein MESSMUSTER braucht eine FANGPROBE, bevor seine Zahl in
ein Blatt kommt.** *Bekannte Treffer und bekannte Nicht-Treffer, gegen die das Muster laufen muss.*

*Der Anlass sind **sieben** eigene Fehlmessungen an einem Tag — nicht sieben Nachlässigkeiten, sondern
siebenmal dieselbe Ursache: **das Muster misst die FORM und nicht die SACHE.***

```text
 1  --include=*.ts unquotiert        -> 0 Dateien (zsh expandiert vorher)
 2  Rueckwaerts-Scan zur Schleife     -> 3 von 5, Abbruch bei jedem `const`
 3  Funktionsverfolgung, 2. Versuch   -> 3 von 5, `const` setzte den Kontext zurueck
 4  'transform.rotation' als Pfad     -> 0 Treffer; die Felder stehen VERSCHACHTELT
 5  yaml-Zaun-Zaehler (A-25)          -> 1 Bereich statt 2; ```yaml schliesst nicht
 6  'Aussparung' fuer die Zusage      -> 0 Treffer; die Sache heisst 'Durchbruch'
 7  Praefix [AW]- in der Tafel        -> 12 statt 13; P- und M- fehlten
```

> ***Und der Generator hat den Handgriff vorgemacht, an meinem eigenen Auftrag.*** *A-31-5 verlangte, die
> vier anderen Dateien mitzumessen. Sein erstes Muster fand **sechs** Treffer — und **keiner war ein
> Befund**: alle sechs sind `onDragEnd`/`onChange`-Handler, die pro Benutzer-Geste **einen** Befehl
> auslösen. Seine Worte: „Mein Muster hat die **Verschachtelung** gemessen und nicht die
> **Wiederholung** — dieselbe Klasse wie die Falle des Planners, nur eine Ebene höher." **Hätte er die
> sechs gemeldet, hätte er vier Rollen auf eine Suche geschickt, die es nicht gibt.***

**Was die Fangprobe leistet, und warum sie billig ist:**

```text
Er hat sieben Faelle hingeschrieben — VIER Treffer, DREI Nicht-Treffer —
und das Muster dagegen laufen lassen:
  resources/planner/hausplaner/__proben__/a31-schleifenprobe-fangprobe.txt
  scripts/a31-schleifenprobe.mjs   meldet SELBSTPRUEFUNG BESTANDEN
Fall 4 heisst dort woertlich „while, mit Zwischenzuweisung wie die Falle des
planners" — meine Nummer 2 und 3 von oben, als Probefall festgehalten.

UND DIE SELBSTPRUEFUNG HAT IHN SELBST GEFANGEN: seine Erwartungsliste sagte
19, das Skript meldete GESCHEITERT, nachgezaehlt war SEINE Liste falsch und
nicht das Werkzeug. Genau dafuer ist sie da.
```

> **Der Handgriff, in einem Satz:** *wer eine Zahl aus einem Muster in ein Blatt schreibt, schreibt
> vorher **zwei Fälle hin, die treffen müssen, und einen, der nicht treffen darf** — und lässt das Muster
> dagegen laufen. **Bei den sieben Fällen oben hätte das jeden einzelnen gefangen.** Es kostet Minuten;
> eine falsche Zahl in einem Kriterium kostet eine Runde.*

> ***Und die Grenze gehört dazu:*** *der Generator schreibt sie selbst hin — „die Probe misst
> **lexikalisch**. Ein Aufruf in einer Hilfsfunktion, die ihrerseits aus einer Schleife gerufen wird,
> sieht sie nicht — das ist keine Zusage, die ich hier gebe." **Eine Fangprobe macht ein Muster
> belastbar, nicht allwissend.** Wer das verwechselt, hat nur die Sorte Vertrauen gewechselt.*

**VIERTER SCHRITT, ergänzt 13.08. nach Cluster 3: geöffnet werden muss die Stelle, auf die sich der
TRAGENDE SCHLUSS stützt — und „abgeschnitten gelesen" ist nicht gelesen.**

*Ich hatte an dem Tag mehrere Stellen geöffnet und Schritt 1 damit formal erfüllt. Nur die eine nicht,
an der alles hing.*

```text
DER FALL: die werkzeugLandkarte begruendet je Werkzeug, was fehlt. Ich habe die
Begruendungen mit `cut -c1-158` gelesen und daraus geschlossen, drei Werkzeuge
seien blosse ANSCHLUSSARBEIT, weil die Geometrie „bereits in editierGeometrie"
liege. Beim Oeffnen:
  versetzteWand (editierGeometrie.ts:20)  verschiebt BEIDE Endpunkte um denselben
                                          Vektor = Translation. Ein Parallel-
                                          versatz braucht die Normale. Andere
                                          Rechnung. -> Stuetze weg.
  Geradenschnitt als Funktion             existiert nicht. gehrungsEcken
                                          (wallGeometry.ts:110, nicht exportiert)
                                          loest den Fall mit GEMEINSAMEM Scheitel
                                          ueber die Winkelhalbierende; beim
                                          Trimmen beruehren sich die Waende
                                          gerade NICHT. -> Stuetze weg.
DIE FOLGE: eine falsche Einordnung im Fahrplan UND eine falsche Zusage an Yama
(„laeuft ohne dich, sofort"). Beides gezogen, bevor gebaut wurde.
```

**Die drei Handgriffe, die daraus folgen:**

1. **Kein `cut`/`head` auf einer Zeile, die zum Beleg wird.** *Wer eine Begründung als Stütze
   benutzt, liest sie ganz. Abschneiden ist ein Werkzeug zum Suchen, nicht zum Belegen.*
2. **Eine Selbstauskunft des Codes ist kein Beleg, solange die darin GENANNTE Funktion nicht offen
   war.** *Register, Landkarten, Doc-Kommentare sind Hinweise mit Fundstelle — H-6, Wort ≠ Beleg. Sie
   sind oft richtig; hier war es sogar die Landkarte, die recht hatte, und ich lag daneben.*
3. **Beim Zitieren eines EIGENEN Blattes die Stelle aufmachen.** *Der Kern des Fehlers war, dass ich
   meine W-18-Notiz „F-004 als Gehrungsdetail" als „F-004 ist gebaut" gelesen habe. Das Blatt sagt in
   `W-18-1:128` wörtlich das Gegenteil, und drei weitere Stellen bestätigen es — `REGISTER.md:35`
   streicht die Formel sogar durch. **Der Bestand war einstimmig richtig; falsch war nur meine
   Erinnerung an ihn.** Deshalb gilt die DAUERregel „Postenlisten nur aus frischer Messung" auch für
   Zitate aus eigenen Blättern.*

**DRITTER SCHRITT, ergänzt 12.08. nach dem dritten Vollständigkeitsfund: bevor der Scope-Block
geschrieben wird, werden die EXPORTE der Datei gezählt — nicht die, die ich beschreibe.**

```text
W-36  Scope nannte faehigkeitenNach          von VIER Funktionen
W-37  Scope nannte die Adapter und Typen     von SECHZEHN Exporten; es fehlten
      :522 enginePanel, :527 startwerte, :538 fehlendePflichtfelder — und
      enginePanel ist die Ausfuhr, die ALLE SECHS importierenden Waechter anfassen
```

> **Eine fehlende Ausfuhr fällt nicht auf, und darin liegt der Unterschied zu einer falschen Zahl.**
> *Eine falsche Zahl widerspricht einer Messung. **Eine fehlende Ausfuhr widerspricht nichts — sie
> fehlt nur**, und der nächste Leser hält den Scope für vollständig, weil nichts dagegen spricht.
> Deshalb gehört das in einen Schritt und nicht in die Aufmerksamkeit.*

> **Und der Prüfende ist nicht immun:** *der Plan-Prüfer hat in `d976060f` festgehalten, dass sein
> erstes Muster ebenfalls zu eng war und „vier, alle echt" **bestätigt hätte** — es verlangte das
> Anführungszeichen direkt hinter dem Namen und verfehlte beide `.ts`-Importe. **Zwei Rollen,
> dasselbe zu enge Muster.** Ein zweites Muster ersetzt keine unabhängige Prüfung, aber eine
> unabhängige Prüfung ersetzt auch kein zweites Muster.*

> **Der Beleg ist A-20, und er ist meiner:** *ich meldete **17 widersprüchliche Blätter**, ohne ein
> einziges zu öffnen. **13 der 17 verglichen einen Blattkopf mit einer BAUAUFZEICHNUNG** — einem
> Block mit `auftrag:`, `zustand:` und `bau_commit:` — und nicht mit einer Statuskopie. Der
> Generator hat `A-13-roof-azimuth-absichern.md:227` geöffnet und es gefunden; **beinahe hätte er
> eine datierte Bauaufzeichnung gelöscht, weil mein Auftrag es verlangte.***

```text
Der Unterschied ist nicht Sorgfalt, sondern Methode:

  grep -c '^zustand:'   misst die ZEILE
  gebraucht war         der BLOCK, in dem sie steht
                        Blattfuss = Block OHNE auftrag:   Meldeblock = MIT auftrag:

-> H-9 an mir, am Tag an dem ich H-9 verankert habe. Und die Pruefform von H-9
   haette es gefunden: "Findet der Befehl die Zeile, die ich mit eigenen Augen
   gelesen habe?" — ich hatte keine gelesen.
```

**Zwei andere Rollen haben ihre Zählfehler am selben Tag durch Lesen gefunden:** *der Plan-Prüfer
kam auf 39 von 40, erkannte das als unplausibel und stellte fest, dass er leere Blattköpfe
mitzählte — „eine fehlende Angabe ist keine widersprechende". Der Evaluator hatte einen
`Math`-Treffer, öffnete die Zeile und fand das Wort **SIND** in „Schiftsparren sind
Gemeinsparren". **Beide haben gelesen. Genau daran lag es.***

### 8 · Wächst mein Messgegenstand durch das Messen? (neu 12.08., aus A-21)

**Ein Kriterium, dessen Zahl sich dadurch ändert, dass man sie dokumentiert, konvergiert nie.**

> **Der Beleg ist A-21-3, und er ist meiner:** *ich verlangte „0 Treffer von `ZURUECKGESTELLT` in
> `docs/STATUS.md`". Der Generator hat die Zahl an vier festen Commits gemessen — **13, 14, 14, 15,
> monoton steigend.** Der Grund ist die Messung selbst: jede Rolle, die ihren Befund verbucht,
> schreibt das Wort erneut in dieselbe Datei. **Das Kriterium hätte die eigene Abnahme
> mitgezählt** — es war nicht streng, sondern unerreichbar.*

```text
Volltext ueber die Datei     13 -> 14 -> 14 -> 15      waechst mit jedem Befund
gebundener Zustandsort        2 ->  2 ->  2 ->  2      steht, auch nach vier Stunden
  (zustand: <WORT> + Tafelzeile)                        Schreibverkehr von fuenf Rollen

PRUEFFORM: Wenn ich mein Ergebnis in dieselbe Datei schreibe, die ich gemessen habe —
           aendert sich dann die Zahl? Dann ist es kein Kriterium.
ABHILFE:   nicht eine kleinere Zahl, sondern ein GEBUNDENER Messort.
```

**Und die zweite Hälfte derselben Lehre:** *ich hatte die Abweichung zwischen seiner Zahl und
meiner als „unterschiedliche Zählweise" abgetan. **Es waren unterschiedliche STÄNDE derselben
Datei.** Beide Zahlen waren richtig, verschieden war der Zeitpunkt. Wer eine Abweichung als
Methodenfrage deutet, ohne den Stand zu vergleichen, erklärt sie weg statt sie aufzulösen — und in
einer Datei, in die fünf Rollen schreiben, ist der Stand die häufigere Ursache.*

### 9 · Verbietet mein Kriterium auch das, was der Bauende TUN MUSS? (neu 12.08., aus A-21)

**Ein Kriterium, das den eigenen legitimen Schreibvorgang mitverbietet, erzwingt einen Umweg — und
jeder Umweg erzeugt ein Fenster.**

> **A-21-6 verlangte, der Bau-Commit zeige geänderte `zustand:`-Zeilen ausschließlich bei W-21L.**
> *Der Generator **muss** am Ende aber seine eigene Fertigmeldung setzen, und die wäre eine zweite
> Zustandsänderung gewesen. Er hat richtig getrennt und den Bau ohne `docs/STATUS.md` committet —
> **und genau dadurch entstand ein zweites Zeitfenster.** Der Plan-Prüfer hat es Commit für Commit
> gemessen, ich habe es nachgefahren: **W-34s `CODE_FERTIG` erscheint erst in `559c632a`** — dem
> A-21-Commit des **Release-Prüfers**. Die Fertigmeldung ging als Beifang in einem fremden Commit
> mit. Der Zustand ist richtig, „aber er trägt nicht den Namen dessen, der ihn gesetzt hat".*

```text
DER KONFLIKT IST HAUSGEMACHT, beide Regeln sind meine:
  A-20-2   Blatt, Tafelzeile und Datensatz in EINEM Commit   -> kein Fenster
  A-21-6   im Bau-Commit nur W-21Ls Zustand geaendert        -> erzwingt ZWEI Commits

ABHILFE    Das Kriterium unterscheidet FREMDE von EIGENEN Aenderungen:
           "keine FREMDEN Zustandsaenderungen" statt "keine ausser W-21L".
           Dann braucht der Bauende keinen Umweg und es gibt kein zweites Fenster.

PRUEFFORM  Was muss der Bauende am Ende ohnehin schreiben — und verbietet mein
           Nachweis ihm genau das?
```

**Der Plan-Prüfer nennt es „kein Fehler, sondern ein Preis", und das ist die richtige Einordnung:**
*bei fünf parallel arbeitenden Rollen wird jedes Fenster irgendwann gefüllt — heute mindestens der
vierte Fall. **Wer die Trennung fahren muss, lässt den zweiten Commit unmittelbar folgen.** Besser
ist, sie nicht nötig zu machen.*

---

## Was der Planner niemals behauptet

| Nie sagen | Stattdessen |
|---|---|
| „das müsste gehen" | messen und die Zahl nennen |
| „ungefähr / mehrfach / einige" | zählen und die Zahl nennen |
| „das ist trivial" | die Formel und ihren Grenzfall nennen |
| „der Fehler ist behoben" | den Rot-Beleg vorher und den Grün-Beleg nachher zeigen |
| **„gibt es nicht" / „fehlt vollständig"** | *das Muster fand 0 — und was **existiert**, ist folgende Trefferzeile.* **Null Vorkommen eines MUSTERS ist kein Beleg für die Abwesenheit der SACHE** (W-27, 12.08.: `'ortgang'` als Literal 0 Treffer, während `ortgangFlaechenlaengeM` eine exportierte, getestete Funktion ist). Das ist die Umkehrung von H-8 |
| **eine gekürzte Anzeige als Wert** | *ungekürzt lesen.* Ein `cut -c1-14` oder `[:14]` schnitt bei W-23 die Ziffer aus `Harzer Pfanne 7` — und die Kürzung wurde als Befund gegen das Auftragsblatt gemeldet |
| **„der Rückweg steht im Blatt"** (im Fließtext) | *als eigene §5-Zeile.* Dreimal in Folge gemeldet (A-14, A-15, W-09), bis es als Vorlagen-Mangel erkannt war |
| **„die Kopie liegt außerhalb der Maschine"** | *messen, nicht behaupten.* Ich hatte es in A-16 und B7 zugesagt; gemessen lag der Commit auf **keinem** der drei Fernziele |
| **einen Erklärtext neben eine gemessene Zahl setzen** | *den Text aus der Zahl ableiten oder weglassen.* **Dreimal am 12.08. passiert:** `„IN_ARBEIT: 0 = A-18"` (A-18 war schon `CODE_FERTIG`), `„STATUS.md im Baum: 1 (0 = frei)"` (und ich schrieb trotzdem), `„-> Tafelzeile steht noch auf ENTWURF"` (sie stand auf `BEREIT`). **Die Zahl war jedes Mal richtig gemessen, der Satz daneben war Vorlage aus dem vorigen Durchgang** — und wer die Ausgabe liest, glaubt dem Satz |

---

## Die Absage-Regel

**Jeder Auftrag, der ein Werkzeug baut, baut zwei Dinge:**

1. das Werkzeug
2. **die lesbare Absage für alles, was es nicht kann**

Ein Auftrag ohne benannte Absage ist nicht fertig geschnitten. Der teuerste Fehler
des Projekts war ein Dach, das bei nicht-rechteckiger Kontur unsichtbar verschwand —
die Domäne verweigerte korrekt, der Renderer schluckte die Absage, der Anwender
sah ein Haus ohne Dach und ohne Erklärung.

---

## Die Skills des Planners

| Skill | Wann |
|---|---|
| `SKILL-werkzeug-anlegen.md` | Ein neues Werkzeug entsteht |
| `SKILL-auftrag-schneiden.md` | Aus einem Werkzeug wird ein Auftrag |
| `SKILL-formel-pruefen.md` | Eine Machbarkeitsfrage steht im Raum |

---

## Was NICHT zur Rolle gehört

- **Der Prozess.** Wie Aufträge durch Zustände wandern, wer abnimmt, wann committet
  wird — das steht in `docs/ARBEITSREGELN.md` und gilt unabhängig von diesem Ordner.
- **Der Status.** Wo ein Auftrag gerade steht, steht in `docs/STATUS.md`.
- **Das Bauen.** Der Generator baut. Der Planner schneidet.
