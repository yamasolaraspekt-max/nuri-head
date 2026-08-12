# Was bei dir wirklich offen ist — frisch gemessen, nicht aus Notizen

> **Auf deine Anweisung vom 12.08.:** *„sind fragen oder aufgaben an Yama gerichtet die du hier
> übernehmen könntest und alle beantworten bzw, erledigen könntest, schau bitte nach in meinem
> auftrag"*

**Gemessen am veröffentlichten Stand `2f1c08a2`**, über alle Auftragsblöcke der Statuswahrheit —
nicht aus meiner Vorlage abgeschrieben. **Zwölf Posten tragen deinen Namen. Drei davon sind
gegenstandslos, und einen davon habe ich gerade beantwortet.**

## ERLEDIGT — drei Posten, die keine Entscheidung mehr brauchen

### 1 · A-09 „deckt die Engine den Taupunkt ab?" — **NEIN, und der Code sagt es selbst**

Der Plan-Prüfer hatte fünf von sechs Zuordnungen bestätigt und diese eine offengelassen, mit der
Begründung: *„ich bestätige, was der Code über sich selbst sagt. Ich ordne NICHT zu, was nur ein
Fachmann wissen kann."*

**Die Grenze war richtig gezogen — aber sie trifft diese Frage nicht.** Gefragt war nicht *„soll die
Engine den Taupunkt abdecken"* (Fach), sondern *„**deckt** sie ihn ab"* (Messung). Und der Code
beantwortet es in seinem eigenen Selbstzeugnis:

```text
geometry/wandaufbau.ts:40-44
  "Die Engine rechnet den Taupunkt nicht — sie KANN ihn nicht rechnen: `Schicht` traegt
   drei Felder (name?, dicke, lambda), fuer Glaser nach DIN 4108-3 fehlen die
   Diffusionswiderstaende (μ bzw. s_d) je Schicht UND das Raumklima innen/aussen.
   Das ist kein 'noch nicht implementiert', sondern mathematisch nicht durchfuehrbar,
   und es steht im DATENTYP statt in einer Meinung."

:49  vorbehalt: string      PFLICHTFELD, nicht optional — "damit tsc bricht, wenn eine
                            Rueckgabe den Vorbehalt weglaesst"
:57  UWERT_VORBEHALT        der Satz steht EINMAL im Haus und ist zitierbar
wandaufbau.test.ts:53       sichert ihn ZEICHENGENAU, auch fuer den leeren Aufbau
```

**Antwort: Nein. Die Engine liefert einen U-Wert nach DIN EN ISO 6946 und sagt in jeder Rückgabe
dazu, dass das keine Feuchteschutz-Aussage ist.** Das ist keine Lücke, sondern eine benannte Grenze
— und sie ist typsicher, nicht nur dokumentiert. **Von dir ist dazu nichts nötig.**

### 2 · A-17 „ja, der Vorbehalt kommt ins Ergebnis" — **gegenstandslos, es ist gebaut**

```text
enginePanels.ts:223-225   N-003: { schluessel: 'vorbehalt', label: 'Vorbehalt' }
              :262-264    A-17:  dasselbe fuer die FBH-Auslegung
              :70-80      keinGesamturteil — kein "Alle Pruefungen bestanden", weil das
                          einen NACHWEIS behaupten wuerde, den es nicht gibt
sparrenVorbehalt.test.ts  gesichert
```

Der Code trägt die Begründung im Klartext: *„der Vorbehalt steht IM SELBEN BLICK wie die spezifische
Leistung — genau die Zahl, die ohne ihn wie ein Auslegungsergebnis aussieht."* **A-17 ist
betriebsbestätigt. Dein Satz wird nicht mehr gebraucht.**

### 3 · AUF-40 Teil B — **widerlegt, beide Hälften sind gebaut**

Heute berichtigt: **mein eigener Befund war falsch.** Ich hatte nach einer *Route* gesucht, 0
Treffer gefunden und geschlossen, die Projektliste fehle. Sie kommt über das **Mount-Attribut**
(`HausplanerController.php:42/101/55` → `objekt.blade.php:141` → `main.tsx:18/82`), gebaut unter
AUF-78. **Aus diesem Posten liegt bei dir: nichts.**

## BLEIBT BEI DIR — neun Posten, je mit dem Grund

```text
FACH UND HAFTUNG — ich vertrete sie nicht, weil ein falscher Wert Schaden macht

  W-21L      Deckungsart -> Lattweite: Fachdaten liefern oder zurueckstellen.
             GEMESSEN: W-23 hat sie NICHT erzeugt (nur ein boolean-Flag),
             Warten bringt nichts.
  W-23/F-053 die Lattmass-Rechnung bestaetigen, plus zwei offene Fragen:
             Restausgleich bei n_min > n_max, und welches n bei mehreren gilt.
             Beides Handwerkspraxis, im Bestand nicht ablesbar (geprueft).

PRODUKT- UND ARCHITEKTURENTSCHEID — Ermessen, nicht Ablesung

  A-09       gehoert Tragwerk an die Zeichenflaeche?
  W-15       ZoneNode ohne Materialfeld: Weg A (materialId, additiv) oder
             Weg C (auf die Wand begrenzen). Weg B waere die zweite Wahrheit —
             davon rate ich ab. Drei Wege mit Kosten liegen vor.

PRODUKTION — §15 verbietet uns den Zugriff, das ist keine Foermlichkeit

  W-27/F-028 die Azimut-Konvention im Bestand. DER SELECT LIEGT FERTIG UND
             SYNTAKTISCH GEPRUEFT im Fuenf-Punkte-Befund — inklusive des Fundes,
             dass roof_orientation die Konvention BEWEIST statt sie zu schaetzen.
  A-09       Achse-2-Zuordnung je Engine · jeder Hetzner-Deploy

REGEL UND LOESCHUNG — beides ausdruecklich nicht vertretbar

  A-17       ob §4 ein drittes Verbot bekommt. Regelaenderung.
  A-16/A-18  die Loeschung der toten View. Dauerregel: kein Loeschen ohne deine
             Freigabe; wenn du loeschst, dann mit Archiv, Manifest und Rueckweg.

ENTSCHEIDUNGSREIF VORGELEGT, wartet nur auf ein Wort

  A-22       Nebenlaeufigkeit an docs/STATUS.md. 317 Commits, fuenf Rollen, ein Tag.
             Ich habe die Folge fuer MICH bereits gezogen und erprobt (eigener
             Worktree); fuer die anderen vier Rollen ist es dein Beschluss.
```

## Was sich dadurch verschoben hat

```text
vorher   12 Posten trugen deinen Namen
jetzt     9 — und von den neun sind zwei reine Bestaetigungen (F-028-SELECT
            ausfuehren, A-22 beschliessen), fuer die alles vorbereitet ist.
```

**Vier von zwölf waren gegenstandslos oder falsch** — einer davon mein eigener Fehlbefund. **Das ist
der Grund, warum ich vor jeder Vorlage messe statt aus Notizen zu schreiben:** eine Postenliste, die
nicht nachgemessen wird, wächst von allein.

---

## Nachtrag 12.08.: zwei deiner sechs Posten sind beantwortbar — hier sind sie

> **Der Planner hat dir sechs Posten gemeldet:** *H-10 · F-053 · ZoneNode/W-15 · F-028 ·
> Nebenläufigkeit · und ob AUF-40 damit geschlossen ist.* **Der letzte ist eine Messfrage, und beim
> ersten kann ich den Gegenbeweis liefern, statt dich raten zu lassen.**

### AUF-40 — **ja, geschlossen. Beide Teile des Auftragstexts sind erfüllt.**

Der Auftragstext lautet wörtlich (Inventur): *„Start/Zuletzt an echte Projekte **+** Konfigurator-Paket
serverseitig speichern."* **Zwei Teile — beide gemessen, jede Stelle geöffnet:**

```text
TEIL 1 · Start/Zuletzt an echte Projekte      GEBAUT UND ANGESCHLOSSEN
  HausplanerController.php:101  hausplanerProjekte() — LeadAlternativeAdd,
                                orderByDesc(updated_at), limit PROJEKTLISTE_MAX
                          :55   'hpProjekte' => ...
  objekt.blade.php:141          data-projekte="{{ json_encode($hpProjekte) }}"
  main.tsx:18/:82               leseProjekte(mount.dataset[PROJEKTE_ATTRIBUT])
                                -> setProjekte(...)   ANKOMMEND im UI-Zustand

TEIL 2 · Konfigurator-Paket serverseitig speichern   GEBAUT, ANGESCHLOSSEN, AUFGERUFEN
  ConfigWizard.tsx:255          void speicherePaket(art, wahl.label, paket)
                                -> nicht nur importiert, sondern GERUFEN
  main.tsx:89                   setzePaketZiel(mount.dataset[PAKETE_URL_ATTRIBUT], csrf)
  objekt.blade.php:144          data-pakete-url="{{ route('hausplaner.objekt.pakete.speichern') }}"
  HausplanerConfiguratorPackage + Migration + drei Routen mit Rechten
```

**Und mein eigener Restposten fällt damit weg.** Ich hatte geschrieben, es fehle *„der Anschluss der
Konfigurator-Pakete an den Startbildschirm"* (`paketListe` wird 0-mal gerufen). **Das ist keine
AUF-40-Forderung** — der Auftrag verlangt *speichern*, nicht *auflisten*. Das Auflisten ist ein
zusätzlicher Endpunkt, den niemand bestellt hat.

> **AUF-40 ist vollständig. Von dir ist dazu nichts mehr nötig.**

### H-10 — ich kann nicht entscheiden, aber ich kann den Gegenbeweis liefern

**Die Regel gehört dir**, das ist Prozessrecht für alle Rollen. Was ich beitragen kann: **ob der
Vorschlag meine Fehler wirklich gefangen hätte.** Eine Regel, die den Fall nicht fängt, ist wertlos —
das ist prüfbar, und ich bin einer der drei Betroffenen.

**Ich habe denselben Fehler ZWEIMAL gemacht, in derselben Datei, an derselben Bauform:**

```text
FEHLER 1   gesucht:   Route fuer die Projektliste in routes/web.php
           gefunden:  0  ->  geschlossen "fehlt"
           wahr:      Mount-Attribut data-projekte, kein Fetch

FEHLER 2   gesucht:   'konfigurator-pakete' in resources/planner
           gefunden:  0  ->  geschlossen "nicht angeschlossen"
           wahr:      die URL kommt als data-pakete-url aus dem Blade,
                      die Insel kennt sie nur als mount.dataset — die
                      Zeichenkette steht dort nie
```

**Beide Male dieselbe Ursache: ich habe den WEG gesucht, den ich erwartet habe, statt am ZIEL zu
messen.** Der zweite Fehler ist der schwerere, weil er **nach** dem ersten passierte — die Lehre lag
vor und hat nicht gegriffen, weil sie nur als Einsicht bestand und nicht als Handgriff.

**Gegenprobe an beiden Fällen, mit deinem Regeltext:**

```text
"mindestens ZWEI Bauformen nennen und beide messen"
  Fehler 1  Route  UND  Mount-Attribut        -> haette gegriffen
  Fehler 2  Fetch  UND  Mount-Attribut        -> haette gegriffen

"bei einer Zulieferung am ZIEL messen, nicht am Weg"
  Fehler 1  nicht "gibt es eine Route", sondern "steht die Liste im UI-Zustand"
            -> main.tsx:82 setProjekte, sofort sichtbar   -> haette gegriffen
  Fehler 2  nicht "gibt es einen Fetch", sondern "wird gespeichert"
            -> ConfigWizard.tsx:255 speicherePaket(...)   -> haette gegriffen
```

**Beide Teile der Regel fangen beide Fehler, unabhängig voneinander.** Das ist mehr als ein Indiz:
der zweite Teil allein hätte gereicht, und der erste ist die billigere Prüfung.

**Was ich nicht sagen kann:** ob die Regel Fälle erzeugt, in denen sie unnötig aufhält — dafür
bräuchte es Abwesenheitsbefunde, bei denen nur eine Bauform denkbar ist. **Das ist die einzige
Gegenfrage, die ich dir nicht abnehmen kann.**

### Damit bleiben bei dir vier statt sechs

```text
H-10        entscheidungsreif, Gegenbeweis an zwei Faellen oben
AUF-40      ERLEDIGT — geschlossen, gemessen
F-053       Fach und Haftung
ZoneNode    Produktentscheid, drei Wege liegen vor
F-028       Produktion, SELECT liegt fertig
Nebenlaeuf. entscheidungsreif, fuer mich selbst bereits gezogen
```
