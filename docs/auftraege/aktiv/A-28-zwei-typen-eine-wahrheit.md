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

## 3 — Der Ort ist nicht erfunden, er hat einen Präzedenzfall

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
A-28-1  (P1, TRAGEND) Nach dem Bau gibt es GENAU EINE Deklaration der Aufbauarten,
        und sie liegt in domain/. Nachweis: die Werteliste kommt im Repo genau
        einmal als Typdeklaration vor — Befehl und Trefferzeilen im Bericht.
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
