# A-28 — Zwei Typen, eine Wahrheit. Neun Aufbauarten stehen zweimal, zeichengleich

```yaml
auftrag: "A-28"
titel: "AufbauArt und VorlagenAufbauArt sind zeichengleich — ein Typ gehört an einen Ort"
art: "BAU — eine Typ-Dublette auflösen. Kein Verhalten ändert sich, kein Wert."
spur: A
heimat_app: ticket
status_steht_in: docs/STATUS.md
basis_sha: bd0f7e0d
prioritaet: P2
ballbesitz: "plan-pruefer (DoR)"
claim: "planner 13.08. — Claim VOR dem Schnitt."
kennung_geprueft: "A-28 ist frei — 0 Treffer in docs/STATUS.md."
anlass: "Gefunden beim Messen der Grundlage für W-22s Bedien-Schnitt: die Aufbauarten sind zweimal
         deklariert. Kein fremder Befund — beim Öffnen von zwei Typen aufgefallen, die gleich hießen."
grundlage: "geometry/aufbauPlatzierung.ts:21-23 · geometry/dachformVorlagen.ts:173-175 ·
            domain/roofShape.ts:12 als Präzedenzfall · md5 beider Wertelisten selbst verglichen"
```

## 1 — Der Befund: zeichengleich, und das ist gemessen und nicht geschätzt

```text
geometry/aufbauPlatzierung.ts:21   export type AufbauArt =
geometry/dachformVorlagen.ts:173   export type VorlagenAufbauArt =

BEIDE:  'chimney' | 'window' | 'vent' | 'sat' | 'lichtkuppel'
      | 'schleppgaube' | 'trapezgaube' | 'flachgaube' | 'giebelgaube'

md5 der Werteliste, Leerzeichen entfernt:
  AufbauArt          35ed563ca78ba859647d598daab5dac6
  VorlagenAufbauArt  35ed563ca78ba859647d598daab5dac6   -> IDENTISCH
```

> **Das ist eine zweite Wahrheit, und die Bauordnung verbietet sie ausdrücklich** — *CLAUDE.md: „Keine
> verwaisten zweiten Wahrheiten: kein zweiter Ort, der denselben abgeleiteten Wert erneut berechnet."
> **Hier ist es ein Typ statt eines Werts, aber der Schaden ist derselbe und schlimmer verborgen:** wer
> eine zehnte Aufbauart hinzufügt, muss beide Stellen ändern — **und `tsc` merkt es nicht**, weil die
> zwei Typen unabhängig sind. Der Fehler fällt erst auf, wenn eine Vorlage eine Art nennt, die die
> Platzierung nicht kennt.*

**Und ein zweiter Befund im selben Griff, der die Blätter betrifft:** *der Dateikopf von
`aufbauPlatzierung.ts:3` nennt **sechs** Arten — „Kamin/Dachfenster/Lüfter/Sat/Gaube/Lichtkuppel" —, **der
Typ hat neun.** „Gaube" zerfällt in vier: Schlepp, Trapez, Flach, Giebel. **Beide Zahlen stimmen über
verschiedene Mengen**, und genau deshalb gehört die Unterscheidung ins Blatt: sechs *Gattungen*, neun
*Arten*.*

## 2 — Warum die Behebung klein ist

```text
Wer nutzt welchen Typ? Selbst gemessen, Tests ausgenommen:
  AufbauArt           NUR aufbauPlatzierung.ts   (seine eigene Datei)
  VorlagenAufbauArt   NUR dachformVorlagen.ts    (seine eigene Datei)
```

*Kein Import-Geflecht, keine dritte Stelle. **Die Dublette ist bequem entstanden und lässt sich
entsprechend einfach auflösen.***

## 2b — NEU GESCHNITTEN nach `d0c429fa`: es sind VIER Deklarationen, und sie sind NICHT alle gleich

**Der Plan-Prüfer hat den Befund vergrößert, und er hat recht.** *Ich habe zwei Deklarationen gemessen
und nicht weiter gesucht — obwohl ich im Fuß selbst geschrieben habe, dass ich nicht gesucht habe.
**Das war keine Grenze, das war eine Lücke:** wer eine Dublette meldet, muss ALLE Deklarationen derselben
Sache kennen, sonst ist der vorgeschlagene Ort falsch.*

```text
VIER Stellen, selbst geoeffnet:
  aufbauPlatzierung.ts:21   AufbauArt          NEUN Werte
  dachformVorlagen.ts:173   VorlagenAufbauArt  NEUN Werte   (md5 identisch)
  domain/scene.types.ts:248 ObstacleType       ZEHN Werte   (+ 'spitzgaube')
  domain/validation.ts:187  z.enum([…])        ZEHN Werte   (Zod-Laufzeitpruefung)
```

**UND DER SATZ, DER ALLES ERKLÄRT, stand die ganze Zeit im Code** — *`dachformVorlagen.ts:172`,
wörtlich:*

```text
// Aufbau-Auto-Platzierung: Arten exakt wie ObstacleType im Planer
// (nur sicher unterstuetzte).
```

> **Die Neun sind keine Dublette der Zehn — sie sind eine bewusste TEILMENGE.** *Der Klammerzusatz ist
> die Begründung, und sie ist fachlich: das **Szenenmodell** kennt alle Arten, die **Auto-Platzierung**
> nur die, für die sie sicher platzieren kann. **Damit war mein Vorschlag „ein Typ an einem Ort" falsch:**
> in `domain/` liegen die Arten längst, mit einer mehr — wer die Neun dorthin zieht, hat drei Listen mit
> zwei Längen; wer auf Zehn vereinheitlicht, ändert Verhalten und verletzt A-28-2.*

**Meine zwei Kriterien schlossen sich also gegenseitig aus** — *und zwar genau dann, wenn `spitzgaube`
nicht entschieden ist. Das stand im Blatt nicht, weil ich die vierte und dritte Stelle nicht kannte.*

### Die Lösung ist ABLEITUNG statt Verschieben

```text
Die Teilmenge wird AUS der Obermenge gebildet, statt sie zu wiederholen:
  type AufbauArt = Exclude<ObstacleType, 'spitzgaube'>
  (Form ist Bauform — Exclude, Extract oder ein benannter Satz, egal;
   verlangt ist die ABLEITUNG.)

Was das leistet, und was ein Verschieben nicht leisten koennte:
  · die Dublette AufbauArt/VorlagenAufbauArt verschwindet     (eine Aufzaehlung weniger)
  · die Neun bleiben zeichengleich neun                        (A-28-2 haelt)
  · und die TEILMENGEN-BEZIEHUNG ist im TYPSYSTEM verankert:
    verliert ObstacleType eine Art, bricht tsc — heute merkt es niemand.
```

> **Das ist besser als mein erster Vorschlag und nicht nur anders:** *„ein Typ an einem Ort" hätte die
> Wiederholung an einen anderen Platz gelegt. **Die Ableitung macht die Wiederholung unmöglich** — und
> genau das war der Zweck.*

### Und `spitzgaube` bleibt außen — mit Grund, nicht aus Bequemlichkeit

```text
spitzgaube lebt an 21 Stellen. Im Produktivcode, vom plan-pruefer gemessen:
  gaubeGeometrie:247/:375/:418   ECHTE Geometrie
  dachAufbautenMesh:137          Darstellung
  dachAusschnitt:281 · aufbauOrientierung:60
  dachformVorlagen:2379          echte Vorlage 'sattel-spitzgaube'
Das Szenenmodell kennt die Art; Platzierung und Vorlagen-Typ kennen sie nicht.
```

> **Das ist ein eigener Befund und keine Aufgabe dieses Auftrags** — *eine Art mit Geometrie und Vorlage,
> die die Auto-Platzierung nicht platzieren kann. **Ob das ein Mangel ist, ist eine Fachfrage:** der
> Klammerzusatz „nur sicher unterstützte" liest sich wie eine bewusste Auslassung, und H-7 verbietet mir,
> aus dem Ist ein Soll zu machen. **Benannt, nicht entschieden** — und `spitzgaube` hinzuzufügen wäre
> ohnehin eine Verhaltensänderung an sechs Modulen.*

## 3 — Der Präzedenzfall für Domänen-Typen (gilt weiter, aber nicht als Umzugsziel)

```text
domain/roofShape.ts:12   export type RoofShape = …
                         genutzt von VIER Dateien
domain/ enthaelt:        commands.types.ts · roofShape.ts · scene.types.ts ·
                         validation.ts · scene-document-v2.schema.json
```

> **Ein Domänen-Typ gehört nach `domain/`, nicht in ein Geometriemodul** — *`RoofShape` macht es genau
> so und wird von vier Dateien importiert. **Die ART eines Dachaufbaus ist eine Eigenschaft der
> Domäne**, keine der Platzierung und keine der Vorlagen: beide sind **Verbraucher**. Dieselbe Logik wie
> beim Fang (W-01, Infrastruktur) und beim Wechselholz (W-21/2, Fundament vor Verbraucher).*

## 4 — Scope

```text
A-28 IST   die Aufbauart als EIN Typ an EINEM Ort in domain/, und beide
           bisherigen Deklarationen zeigen darauf.
           Der bisherige Name AufbauArt bleibt der Name — VorlagenAufbauArt
           wird zum Alias oder verschwindet, je nachdem was die Aufrufstellen
           verlangen; der Bauende entscheidet die Form, nicht den Ort.

A-28 IST NICHT
           eine Aenderung der WERTE. Neun Arten bleiben neun Arten, zeichengleich.
           Wer eine hinzufuegt oder streicht, aendert das Verhalten — das ist
           dieser Auftrag nicht.
           die Vereinheitlichung anderer Typen. Nur diese eine Dublette ist
           gemessen; eine Suche nach weiteren ist ein eigener Vorgang.
           der Bedien-Schnitt fuer W-22 -> eigener Auftrag, und er haengt NICHT an
           diesem: die Bedienung kann einen der beiden Typen nutzen.
```

## 5 — Abnahmekriterien

```text
A-28-1  (P1, TRAGEND) NEU GESCHNITTEN nach d0c429fa. Meine erste Fassung verlangte
        GENAU EINE Deklaration in domain/ — das war falsch, weil dort schon zwei
        liegen und sie eine Art MEHR fuehren (ObstacleType und das z.enum in
        validation.ts, je zehn Werte mit 'spitzgaube').
        WAS JETZT GILT: die NEUN werden AUS ObstacleType ABGELEITET statt
        wiederholt. Nach dem Bau gibt es fuer die Aufbauarten genau EINE
        Aufzaehlung im Repo — die in ObstacleType — und AufbauArt entsteht daraus
        durch Ausschluss.
        DIE FORM IST BAUFORM: Exclude, Extract oder ein benannter Satz, das
        entscheidet der Bauende. VERLANGT ist die Ableitung, nicht ein Schlüsselwort.
        WAS DAS LEISTET und ein Verschieben nicht leisten koennte: die Dublette
        AufbauArt/VorlagenAufbauArt verschwindet, die neun bleiben zeichengleich
        neun (A-28-2 haelt), UND die Teilmengen-Beziehung ist im TYPSYSTEM
        verankert — verliert ObstacleType eine Art, bricht tsc. Heute merkt es
        niemand.
        Nachweis: Befehl und Trefferzeilen, dass die Werteliste als Aufzaehlung
        genau einmal vorkommt; das z.enum in validation.ts darf bleiben oder
        abgeleitet werden (Zod braucht Literale) — beides ist zulaessig, die
        Entscheidung gehoert in den Bericht.
A-28-2  (P1, SCHUTZGRENZE) KEIN Wert geaendert. Nachweis: die Werteliste vorher und
        nachher per md5 IDENTISCH, beide Werte im Bericht. Der Anker vorher ist
        35ed563ca78ba859647d598daab5dac6 — am Bau-Stand neu erheben, weil ein
        md5 aus einem Blatt kein Beleg ist.
        BEGRUENDUNG: eine Typ-Dublette aufzuloesen ist eine Umstellung; sobald sich
        ein Wert aendert, ist es eine Verhaltensaenderung an sechs Modulen, die
        Aufbauten platzieren und rendern.
A-28-3  (P1) tsc ist gruen, und das ist hier der eigentliche Waechter: eine
        Typumstellung, die irgendwo nicht passt, faellt dort auf. Zaehler vorher und
        nachher im Bericht.
A-28-4  Die Suite bleibt gruen, Zaehler vorher = nachher. KEIN Test wird geaendert;
        wenn einer faellt, ist die Umstellung falsch und nicht der Test.
A-28-5b (P1) DER SPITZGAUBE-BEFUND steht im Bericht und wird NICHT behoben:
        spitzgaube lebt an 21 Stellen, im Produktivcode gaubeGeometrie:247/:375/:418
        als echte Geometrie, dachAufbautenMesh:137 als Darstellung,
        dachAusschnitt:281, aufbauOrientierung:60 und dachformVorlagen:2379 als
        echte Vorlage 'sattel-spitzgaube'. Das Szenenmodell kennt die Art,
        Platzierung und Vorlagen-Typ kennen sie nicht.
        NICHT HINZUFUEGEN: der Klammerzusatz in dachformVorlagen.ts:172 liest sich
        als bewusste Auslassung ('nur sicher unterstuetzte'), und H-7 verbietet, aus
        dem Ist ein Soll zu machen. Sie hinzuzufuegen waere eine
        Verhaltensaenderung an sechs Modulen und ein eigener Auftrag.
A-28-5  Der ZWEITE BEFUND steht im Bericht: der Dateikopf von aufbauPlatzierung.ts:3
        nennt SECHS Arten, der Typ hat NEUN — sechs Gattungen, neun Arten, weil
        Gaube in Schlepp, Trapez, Flach und Giebel zerfaellt. Beide Zahlen stimmen
        ueber verschiedene Mengen.
        NICHT VERLANGT: den Kopf umzuschreiben. Er ist nicht falsch, er zaehlt
        Gattungen. Verlangt ist, dass die Unterscheidung im Bericht steht, damit
        die naechste Rolle nicht eine der zwei Zahlen fuer falsch haelt — die Lehre
        aus W-33s vierter-gegen-fuenfter Waechterzahl.
A-28-6  Die Fangprobe wird GEFAHREN und belegt: einen Wert aus der Liste entfernen
        und zeigen, dass A-28-2 rot wird (md5 weicht ab) und tsc bricht. Nicht
        gefahren heisst 'nicht gefahren' im Bericht.
A-28-7  Kein Produktivverhalten geaendert: die Aufrufstellen in dachAusschnitt,
        dachformVorlagen, dachOeffnung, linienBauteile und scene.types bleiben
        funktional gleich. Gegenprobe am Diff, zeilenweise begruendet, wo eine
        Zeile sich aendert.
```

**Nachweisform: Befehl und Trefferzeilen** (B5), **Messung am COMMIT** (E1),
**Nachweis muss rot werden können** (Pflichtprüfung 4), **jede Zahl an zwei Mustern** (Prüfung 7).

```yaml
warum_P2_und_nicht_P1: "Es ist heute kein Fehler im Betrieb: beide Typen sind zeichengleich, also
        verhaelt sich nichts falsch. Der Schaden ist LATENT und tritt beim naechsten Hinzufuegen ein —
        und dann still, weil tsc zwei unabhaengige Typen nicht vergleicht. Ein latenter Schaden mit
        klarer Ursache ist P2; P1 waere er, wenn heute etwas falsch waere."
wie_ich_es_gefunden_habe: "Beim Messen der Grundlage fuer W-22s Bedien-Schnitt. Ich suchte, wo die
        Aufbauarten definiert sind, und fand ZWEI Typen mit fast gleichem Namen. Kein fremder Befund und
        keine Suche nach Dubletten — es fiel beim Oeffnen auf, weil ich beide Stellen gelesen habe statt
        die erste zu nehmen. Genau das verlangt Pflichtpruefung 7 (zwei Muster je Zahl); hier hat sie
        einen Befund erzeugt statt einen zu verhindern."
was_ich_NICHT_getan_habe: "Nach weiteren Typ-Dubletten gesucht. Das waere naheliegend und es waere eine
        Verallgemeinerung ohne Messung — dieselbe Falle, die mich heute mehrfach erwischt hat. Wenn
        jemand sie sucht, ist das ein eigener Vorgang mit eigenem Zuschnitt."
A_28_nimmt_keinen_paragraf3_platz: "ENTWURF, nicht IN_ARBEIT."
```
