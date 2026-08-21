# Y-1 beantwortet: **ablehnen** — und die Kostenseite ist gemessen eine andere als die gestellte

> **Release-Prüfer in Yamas Namen, 21.08. ~10:1x.** Auf `84d94891`. Yama hat Y-1 als „teuersten
> Posten des ganzen Blatts" übergeben und ausdrücklich verlangt, dass er **mit der Zahl** statt ohne
> entschieden wird. **Die Zahl liegt jetzt vor — sie war Messarbeit, also meine.**

---

## Die Antwort auf die eine Zeile

> **Soll die Software so ein Dach ablehnen, oder den First auf die längere Achse drehen?**

**ABLEHNEN.** Vier Gründe, jeder gemessen, keiner geraten.

---

## Vorab, weil es die Lage ordnet: Y-1 war bereits beantwortet

```
11f7c4c3   21.08. 09:54:42   der Fahrplan stellt Y-1
6eddd3a2   21.08. 09:57:27   mein Entscheid beantwortet ihn (als P-1)
                             Differenz 2 min 45 s
Treffer auf meinen Entscheid im Fahrplan:  0
```

**Der Fahrplan kennt die Antwort nicht, weil er 2¾ Minuten älter ist als sie.** Y-1 und P-1 sind
wortgleich dieselbe Frage. **Diese Fassung ersetzt den Entscheid nicht — sie belegt ihn mit den
Zahlen, die ihm damals fehlten, und berichtigt einen eigenen Nebensatz.**

---

## Grund 1 · Der Bestand hat die Frage zweimal beantwortet

```
dachGeometrie.ts:88-93        throw DachGeometrieUngueltig … "KEIN STILLES FALSCHDACH"
                              — fuenfzig Zeilen HOEHER in derselben Datei
dachformVorlagen.ts:336-341   "Rueckgabe DARF <0 sein (Signal fuer Inkonsistenz),
                              wird NICHT still auf 0 geklemmt"
dachGeometrie.ts:136          Math.max(0, laengeM - spannM)   ← klemmt still auf 0
```

**Zweimal dieselbe Haltung — melden statt still korrigieren — und eine Zeile, die als einzige das
Gegenteil tut.** Automatisches Drehen wäre stilles Korrigieren einer Nutzerangabe, und keiner
beliebigen: die Firstrichtung bestimmt Kehlen- und Gratlage, Sparrenrichtung, Entwässerungsrichtung.

---

## Grund 2 · Der Empfänger für die Ablehnung ist bereits gebaut — der fürs Drehen nicht

**Das ist der Fund, der die Frage praktisch entscheidet.** `HausplanerApp.tsx:1023-1031`:

```ts
try {
  dachFlaechen(dach);              // Rueckgabe NICHT zugewiesen
} catch (fehler) {
  if (fehler instanceof DachGeometrieUngueltig) {
    setDachAbsage(fehler.message);  // lesbarer Grund an den Nutzer
    setWerkzeug('auswahl');
    return;                          // KEIN Objekt, KEIN Status, keine Aenderung
  }
  throw fehler;
}
```

**Der Aufruf dient ausschließlich der Gültigkeitsprüfung** — der Kommentar darüber sagt es wörtlich:
*„Deshalb wird sie hier GEFRAGT statt nachgebaut: kein zweiter Rechtecks-Begriff, keine kopierte
Toleranz."*

> ***Ein Wurf aus `dachFlaechen()` erreicht damit sofort eine fertige, geprüfte Absage-Kette mit
> lesbarem Grund.*** **„Ablehnen" ist ein Wurf und fünf Zeilen. „Drehen" bräuchte einen Pfad, den es
> nicht gibt — und träfe die Fachentscheidung aus Punkt 8 bei jedem Aufruf stillschweigend.**

---

## Grund 3 · Die Kostenseite trifft heute nicht — und das ändert die Dringlichkeit, nicht die Antwort

**Yamas Fassung:** *„liefert für jedes Walmdach mit Giebelbreite > Firstlänge stumm eine bis zu 75 %
zu große Fläche in PV-Ertrag und Heizlast."* **Die Zahl +75 % stimmt. Der Weg dorthin ist gemessen
unterbrochen.**

```
Aufrufer von dachGeometrie.dachFlaechen, je einzeln geoeffnet:

  HausplanerApp.tsx:1024     Rueckgabe VERWORFEN — reine Gueltigkeitspruefung (oben)
  dachProjektion.ts:29       verwendet die Flaechen wirklich
     -> Verbraucher von dachProjektion.ts im Produktivbaum:  KEINER
        (einzige Nennung: ein Kommentar in scene.types.ts:394)

  nicht mitgezaehlt: teilKennung.ts:116 ist eine GLEICHNAMIGE andere Funktion
  (HausplanerApp.tsx:27 sagt das ausdruecklich), dachMesh.ts:7 ist ein Kommentar
```

**Die falsche Fläche erreicht heute weder PV noch Heizlast.** Der eine Live-Aufruf verwirft sie; der
Pfad, der sie verwendet, hat keinen Verbraucher — derselbe Befund, den S-1/6 für `dachProjektion.ts`
gemeldet hat.

**Woher die Fehlannahme kommt, ist belegbar und lehrreich:** die Inventur stützt „Live-Pfad" auf den
**Dateikopf** von `dachProjektion.ts` (*„die belastbare Quelle für PV/Heizlast"*). **Das ist eine
Absichtserklärung, kein Wirkungsbeleg** — genau die Klasse, die heute schon zweimal auffiel
(`SzeneProjektionService.php` behauptete das Umgekehrte). **Ort ist nicht Wirkung.**

```
FOLGE FUER DIE ENTSCHEIDUNG   keine. Die Richtung haengt an der Hauslinie, nicht an der Reichweite.
FOLGE FUER DIE DRINGLICHKEIT  erheblich. Y-1 ist heute KEIN blutender Fehler in der Auslegung,
                              sondern eine scharfe Falle fuer den Tag, an dem dachProjektion.ts
                              angeschlossen wird — und dieser Anschluss steht auf der Bauliste.
```

> **Das ist kein Grund zu warten, sondern einer, es JETZT zu tun:** die Ablehnung einzuziehen,
> *bevor* der Verbraucher kommt, kostet fünf Zeilen. Danach kostet es dieselben fünf Zeilen **plus**
> jede Zahl, die bis dahin herausgegangen ist.

---

## Grund 4 · Die Zahl, die Yama eingefordert hat — und sie berichtigt einen eigenen Satz von mir

**Die Bündel-Kante zu `dachformVorlagen.ts`, gemessen am gebauten Bündel** (`grep -oF`, nichts
geraten):

```
Bundle public/hausplaner/hausplaner.js   1.453.603 B, gebaut 16.08. 22:46
  walmIstKonsistent                        0
  walmFirstlaengeM                         0
  regeldachneigungAbhaengigVonMaterial     0
  deckungsHinweis                          0
  EINDECKUNG_KATEGORIE                     0
Gegenprobe aus dachGeometrie.ts (IST im Buendel):
  kontur_nicht_rechteckig                  1
  "Traufkontur ist nicht rechteckig"       1

einzige Produktivkante zu dachformVorlagen:
  dachMesh.ts:13   import TYPE { EngineRoofShape }   ← verschwindet beim Kompilieren
Stand-Kontrolle: dachformVorlagen.ts zuletzt geaendert 13.08., Buendel gebaut 16.08.
  -> die Datei war beim Bau vorhanden und ist trotzdem nicht drin
```

**`dachformVorlagen.ts` (2.402 Zeilen, 157 KB) ist nicht im ausgelieferten Bündel.**

**Damit berichtige ich meinen eigenen Nebensatz von 09:57:** ich hatte geschrieben, *„der Anschluss
von `walmIstKonsistent` ist der kürzere Weg als eine neue Prüfung"*. **Gemessen ist er der teurere
Weg** — er zöge ein 157-KB-Modul in eine Kette, die es heute nicht hat.

**Und die Funktion selbst macht die Sache einfach** (`dachformVorlagen.ts:414-416`):

```ts
export function walmIstKonsistent(lengthM: number, widthM: number): boolean {
  return Number.isFinite(lengthM) && Number.isFinite(widthM) && lengthM > widthM;
}
```

**Drei Terme, keine Abhängigkeit, kein Import.** Ob man sie importiert (und auf Tree-Shaking baut)
oder die Bedingung an Ort und Stelle prüft, ist eine **Bau-Entscheidung mit Build-Wissen — sie
gehört dem Planner, nicht mir.** Ich nenne nur den Zielkonflikt, damit er ihn nicht übersieht:

```
importieren      saubere eine Wahrheit, aber neue Kante zu einem 157-KB-Modul,
                 das heute nicht im Buendel ist
selbst pruefen   keine neue Kante, aber dieselbe Bedingung an zwei Orten
                 -> und genau das ist die Klasse von R-1 (Shoelace privat kopiert)
```

---

## Was gilt, in einer Zeile

**`dachFlaechen()` wirft für `spannM > laengeM` eine `DachGeometrieUngueltig` mit eigener
Ursachen-Kennung, nach dem Muster `:88-93`. Kein automatisches Drehen.**

**Erledigt-Kriterium unverändert aus der Inventur** — *„für `spannM > laengeM` Ausnahme
`DachGeometrieUngueltig` ODER Fläche erfüllt Kontrollrechnung ±1 %, nachgewiesen an beiden
gerechneten Fällen (6×8 m/30° und 4×10 m)"*.

---

## Ball

**Beim Planner** — Posten 1.2 ist entsperrt: **Y-1 ist entschieden.** Dazu drei Messungen, die ihm
Arbeit ersparen oder erspart hätten:

```
1  der Absage-Empfaenger ist gebaut (HausplanerApp.tsx:1023-1031) — nur der Wurf fehlt
2  dachformVorlagen ist NICHT im Buendel; der Import ist der teurere, nicht der kuerzere Weg
3  die Dringlichkeit haengt am Anschluss von dachProjektion.ts, nicht an heutigen Auslegungen
```

**Bei Yama** — von den fünf Yama-Posten sind jetzt **zwei erledigt** (Y-1 hier, Y-2 als
„stilllegen statt löschen" am 21.08. 09:57). **Offen bleiben Y-3, Y-4, Y-5** — und alle drei sind
Fachfragen: Dämmungsbegriff, Herleitung der lichten Höhe, ein neuer Stammdaten-Parameter. **Die
bleiben bei ihm; ich vertrete sie nicht.**
