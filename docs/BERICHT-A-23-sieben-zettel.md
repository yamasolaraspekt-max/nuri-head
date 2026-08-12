# Baubericht A-23 — sieben Zettel an einer erledigten Sperre

```yaml
auftrag: "A-23"
rolle: "generator"
blatt: docs/auftraege/aktiv/A-23-sechs-zettel-an-einer-erledigten-sperre.md
art: "BAU — ueberholte Begleittexte. KEINE Zusage aendert sich."
basis_sha: 59c66eb2
gebaut_am: "13.08.2026"
zustand: CODE_FERTIG
ballbesitz: evaluator
```

> **Der stärkste Beleg dieses Baus ist eine Nicht-Änderung:** *das ausgelieferte Bündel ist
> **byte-identisch** mit HEAD.* **Ein reiner Kommentar-Bau erzeugt dasselbe Erzeugnis — damit ist
> „keine Zusage ändert sich" nicht behauptet, sondern am Artefakt gemessen.**

## Was gebaut wurde

```text
app/StartView.tsx                        2 Stellen
app/studioDaten.ts                       1 Stelle
__tests__/startEhrlich.test.ts           3 Stellen (Dateikopf · TESTNAME · Kommentar im Rumpf)
__tests__/konfiguratorEhrlich.test.ts    1 Stelle
scripts/a23-sieben-stellen.mjs           NEU — die Pruefung zu A-23-2
```

**Kein `assert` geändert, kein Test entfernt, kein Produktivverhalten berührt.**

## A-23-1 (TRAGEND) · Der Wächter behält seine Zusage

**Der Test hieß „Teil A hat weder Route noch Controller berührt — das ist Teil B" und heißt jetzt
„StartView holt sich die Liste NICHT selbst — kein `fetch`, kein `dataset`".**

```text
md5(Rumpf OHNE Kommentare)   vorher 4d0dfba9b151ca1368d4be25c42c8c53
                             nachher 4d0dfba9b151ca1368d4be25c42c8c53   IDENTISCH
Testzahl der Datei           vorher 9   nachher 9
```

> **Eine Feinheit am Nachweis, und sie ist dieselbe Klasse wie die Kollision, die der Plan-Prüfer am
> Kriterium schon behoben hat:** *mein erster md5 lief über den Test **einschließlich Namen** —
> `713a4bbe…`.* **Damit hätte das erlaubte Umbenennen den Nachweis gebrochen.** *Der Nachweis läuft
> deshalb ab `=> {`, also über den Rumpf allein.* **Gemessen, bevor ich die erste Zeile geändert
> habe.**

**Warum der Test bleiben MUSS:** *die Zusage ist richtig.* `grep -c "fetch(\|axios\|dataset\."` **→
0.** **Die Naht läuft über `main.tsx`, nicht über `StartView`** — *der Bildschirm holt sich nichts,
er bekommt.* **Das ist die Architekturgrenze der Insel und kein Übergangszustand.**

## A-23-2 · Sieben Stellen, jede mit Hälfte und Fundstelle

**Der alte Wortlaut steht überall noch da, als Zitat unter „ÜBERHOLT (A-23, 13.08.), und nicht
gelöscht" — AN DERSELBEN STELLE.**

```text
✔ app/StartView.tsx                     „Gefüllt wird sie in …"          PROJEKTLISTE
✔ app/StartView.tsx                     „Die echte Liste braucht …"      PROJEKTLISTE
✔ app/studioDaten.ts                    „Bestand und braucht eine Route" PROJEKTLISTE
✔ __tests__/startEhrlich.test.ts        „der liegt bei Yama"             PROJEKTLISTE
✔ __tests__/startEhrlich.test.ts        der TESTNAME                     PROJEKTLISTE
✔ __tests__/startEhrlich.test.ts        „bleibt deshalb offen"           PROJEKTLISTE
✔ __tests__/konfiguratorEhrlich.test.ts „nicht gestrichen, nur nicht dran"  PAKETSPEICHERUNG
```

**`scripts/a23-sieben-stellen.mjs` prüft alle sieben** — *je Stelle: Kennzeichnung, Hälfte,
Fundstelle, und zwar **im Umkreis von 1200 Zeichen**.* **„An derselben Stelle" heißt nicht
„irgendwo in der Datei"** — *ein Satz, dessen Kennzeichnung einen Absatz weiter steht, wird später
als Beleg gelesen.*

> **Die Prüfung sucht ausdrücklich NICHT „kommt ‚Teil B' noch vor".** *Das wäre die falsche Frage:
> der alte Wortlaut **soll** stehen bleiben.* **Sechs der sieben Stellen tragen die Zeichenfolge
> weiterhin — innerhalb ihres Zitats.**

## A-23-3 und A-23-4 · Fundstelle und Grund

```text
PROJEKTLISTE (AUF-78, keine Route)
  HausplanerController.php:101 -> :55 -> objekt.blade.php:141 -> main.tsx:82
  Grund: Mount-Attribut ohne Lade-Fetch. Der Controller sagt es in :57 woertlich.

PAKETSPEICHERUNG (AUF-81, Tor 1, 26.07. — MIT Route)
  web.php:5016 / :5018 / :5020  -> objekt.blade.php:144 -> main.tsx:89
  -> paketSpeichern.ts:45 (fetch) -> benutzt in ConfigWizard.tsx:255
```

**Wer „Route" misst, findet im ersten Fall 0 und schließt falsch; wer „`fetch` in der Insel" misst,
findet im zweiten genau einen und hält ihn für die Ausnahme.** *H-9 zweimal am selben Posten.*

## A-23-5 · Die Falle, und sie ist scharf

**`gefuehrteEhrlich.test.ts:30` liest `studioDaten.ts` ROH** — *`ohneKommentare` steht in derselben
Datei und wird dort **nicht** benutzt.* **`:33-36` verlangt, dass das Wort „Frei"+„gegeben"
**null**mal in der ganzen Datei vorkommt, Kommentare eingeschlossen.**

**A-23-3 verlangt die Herkunft — und die Herkunft von Hälfte 2 ist Yamas Tor 1.** *Wer das mit dem
naheliegenden Wort aufschreibt, lässt den Wächter fallen.*

```text
Geschrieben wurde: „AUF-81, Tor 1, 26.07."   —  Auftragsnummer und Datum, ohne das Wort.
Gefahren, nicht gezaehlt: gefuehrteEhrlich  ->  8 Zusagen, alle gruen.
```

> **Ich habe den Wächter unmittelbar nach der Änderung an `studioDaten.ts` gefahren** — *nicht am
> Ende.* **Ein Kriterium, das einen Bauenden in eine Wortsperre schickt, fängt man nur, wenn man an
> der Stelle misst, an der man sie auslösen würde.**

## A-23-5b · Kein Test verloren

```text
Insel-Suite   vorher 1718 / 0 Fehler      nachher 1718 / 0 Fehler
tsc:hausplaner   ohne Ausgabe
```

## A-23-6 · Die Fangprobe ist GEFAHREN

```text
md5-Anker studioDaten.ts   daa939bc1faa50bb76561d744aa47f4d
Mutation                   die Kennzeichnung „ÜBERHOLT (A-23 …)" an EINER Stelle entfernt
Ergebnis                   6 von 7 Stellen belegt — rot an genau der mutierten
Rueckschrift               md5 gegen den Anker: identisch
```

## Was ich gemessen und NICHT berichtigt habe — eine ACHTE Stelle

```ts
startEhrlich.test.ts:140
assert.doesNotMatch(start, /dataset\./, 'auch nicht über eine Naht, die es noch nicht gibt');
```

**Die Meldung sagt „eine Naht, die es noch nicht gibt".** *Die Naht gibt es —
`main.tsx:82`.* **Der Satz ist überholt.**

> **Sie ist NICHT berichtigt, und der Grund ist ein Kriterienkonflikt, den ich nicht still auflösen
> darf:** *A-23-1 schützt den **Rumpf** des Tests, und eine `assert`-Meldung ist **kein Kommentar**
> — sie steht im Rumpf.* **Wer sie berichtigt, bricht den md5 und fällt an A-23-1; wer A-23-1
> einhält, lässt einen überholten Satz stehen.**
>
> **Das ist dieselbe Kollision, die der Plan-Prüfer am Kriterium bereits einmal gefunden hat**
> *(`2772c198`, `startEhrlich:120`)* — **nur eine Ebene tiefer, in einer Zeichenkette statt in
> einem Kommentar.** *Ich melde sie und entscheide nicht: ob A-23-1 den Nachweis auf „Rumpf ohne
> Kommentare **und ohne Meldungstexte**" erweitern soll, gehört dem Planner.*
>
> **Die ZUSAGE selbst ist davon unberührt** — *`doesNotMatch(start, /dataset\./)` ist richtig und
> soll halten.* **Falsch ist nur ihre Begründung im dritten Argument.**

## Und ein zweiter Fund am Artefakt

```text
git hash-object public/hausplaner/hausplaner.js  ==  git rev-parse HEAD:public/…
```

**Byte-identisch.** *Ich habe die Insel nach dem Bau neu übersetzt — die Lehre aus A-24, wo das
Bündel fehlte — und das Erzeugnis ist unverändert, weil der Minifizierer Kommentare entfernt.*

> **Damit ist „A-23 ändert keine Zusage" nicht mehr eine Behauptung über den Quelltext, sondern eine
> Messung am Ausgelieferten.** *Und es sagt zugleich, wann das Bündel in einen Commit gehört: nur
> dann, wenn es sich ändert.* **Hier gehört es nicht hinein, und deshalb steht es nicht in diesem
> Commit.**

## must_preserve und Rückweg

| Richtung | Ergebnis |
|---|---|
| geändert | **4** — die vier Dateien mit den sieben Stellen |
| hinzugefügt | **1** — `scripts/a23-sieben-stellen.mjs` |
| entfernt | **0** |
| `public/hausplaner/hausplaner.js` | **unverändert** *(byte-identisch)* |
| Rückweg | `git revert`; alle Änderungen sind Kommentare und ein Testname |
