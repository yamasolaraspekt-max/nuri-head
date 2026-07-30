# AUF-48-S4e — Statusleiste, Command-Palette und Engine-Fläche aus dem JSX

**Spur A** · **Heimat: ticket** · **Basis: `2eb16643`** · *Geschnitten 30.07. 21:46*
**Die letzte der fünf Scheiben von AUF-48.**

## Diesmal habe ich die Tag-Bilanz VOR dem Anker gemessen

*In S4a und S4b hat der Generator zweimal denselben Fehler in meinen Blättern gefunden:
ein Kommentar-Anker, der einen offenen Block halbiert. **Er hatte beide Male recht**, und die
Ursache war meine Methode — ich schnitt nach Namen, ohne die Struktur zu prüfen. Für dieses
Blatt habe ich die Einrückungen zuerst gemessen:*

```text
1793  [8]      </div>
1794  [6]    </div>          <- der Block DAVOR schliesst hier sauber
1796  [6]    {/* Statusleiste */}      <- der Anfang liegt auf derselben Ebene: SAUBER
      …
1919  [6]    )}               <- das LETZTE Geschwister auf Ebene 6
1920  [4]  </div>             <- die aeussere Huelle der Hauptfunktion
1921  [2]  );
1922  [0] }
```

**Die Geschwister auf Ebene 6 zwischen 1796 und 1919 — vollständig aufgezählt:**

```text
1796  {/* Statusleiste */}
1797  <div …>  …  1808  </div>
1810  {/* Command-Palette … */}
1816  {paletteOffen && (   …   1905  )}
1907  {/* AUF-33/L2: die Flaeche einer Rechen-Engine … */}
1911  {offeneEngine && enginePanel(offeneEngine) && (   …   1919  )}
```

**Alles, was hier öffnet, schliesst hier auch.** *Drei vollstaendige Geschwister, kein
halbierter Block.*

> **Das Ende ist 1919, NICHT das Dateiende.** Die Zeilen 1920–1922 (`</div>`, `);`, `}`) schliessen
> die Hauptfunktion. **Hätte ich „bis Dateiende" geschrieben — wie im ersten Entwurf vorgesehen —
> wäre es derselbe Fehler zum dritten Mal gewesen.**

## Umfang

**Aus `HausplanerApp.tsx` nach `resources/planner/hausplaner/app/rahmen/FussUndUeberlagerungen.tsx`:**

| Naht | Anker |
|---|---|
| Anfang | `{/* Statusleiste */}` |
| Ende | das `)}` **vor** `</div>` auf Einrückung 4 — also **nicht** das Dateiende |

Gemessen gegen `2eb16643`: **124 Zeilen · 20 Inline-Stellen.**
Enthalten: die Statusleiste · die Command-Palette (Overlay, `position: fixed`) · die Fläche
einer Rechen-Engine (AUF-33/L2).

## Kriterien

```yaml
  - id: K-01
    aussage: "Keine Inline-Stelle verschwindet oder entsteht."
    befehl: >
      grep -c 'style={{' ueber HausplanerApp.tsx + dashboard/Kopfrahmen.tsx
      + rahmen/*.tsx  (ALLE, auch die aus S4c/S4d)
    erwartet: "die SUMME ist 133"
    hinweis: >
      Seit S4a und S4b wohnen 15 bzw. 27 Stellen ausserhalb der Hauptdatei. Wer nur
      HausplanerApp.tsx zaehlt, misst einen Ausschnitt und nennt ihn das Ganze.

  - id: K-02
    aussage: "Kein Zustand ist mitgewandert."
    befehl: "grep -cE 'useState|usePlannerUiStore|localStorage' FussUndUeberlagerungen.tsx"
    erwartet: "0"
    hinweis: >
      `paletteOffen` und `offeneEngine` bleiben in der Hauptfunktion und werden
      hineingereicht. Die Palette schliesst ueber einen Rueckruf, nicht ueber eigenen Zustand.

  - id: K-03
    aussage: "Die Escape-Rangfolge bleibt unangetastet."
    befehl: "npm run test:hausplaner -- --filter=escapeStapel"
    erwartet: >
      gruen und UNVERAENDERT in der Zahl der Zusagen (9 assert).
      *AUF-83-T5 hat die Rangfolge Palette > Dialog > Menue > Schiene > Reset gesetzt;
      die Palette ist ihr oberster Fall. Wer sie auslagert, fasst den Fall an, nicht die Regel.*

  - id: K-04
    aussage: "Der Schnitt ist tag-ausgeglichen."
    gegenbeweis: >
      Faellt `npm run tsc:hausplaner` mit einem Tag-Fehler, war die Naht falsch gesetzt —
      und das ist ein Befund gegen DIESES BLATT, nicht gegen deinen Bau. Melde ihn so.
      *Zweimal ist mir das schon passiert (S4a, S4b). Diesmal habe ich die Einrueckungen
      vorher gemessen und oben aufgeschrieben — pruefe sie nach, statt mir zu glauben.*

  - id: K-05
    aussage: "`_zerlegteApp.ts` kennt das neue Modul."
    befehl: "grep -c 'app/rahmen/FussUndUeberlagerungen.tsx' __tests__/_zerlegteApp.ts"
    erwartet: "1"
    gegenbeweis: >
      Setze eine verbotene Zeichenkette in das NEUE Modul, OHNE es einzutragen, und lass
      die Absenz-Zusagen laufen. Bleiben sie gruen, ist die Liste die Luecke.
      *Der Kopf der Datei begruendet zu Recht, warum kein `readdir`. Eine von Hand gepflegte
      Liste braucht dafuer eine Zusage gegen das Vergessen — das ist sie.*

  - id: K-06
    aussage: "Die Mutationsprobe kommt VOR den Tests."
    gegenbeweis: >
      Mindestens 8 Mutationen: Palette bei geschlossenem Zustand zeichnen · den Rueckruf
      zum Schliessen entfernen · die Statusleisten-Werte vertauschen · `position: fixed`
      der Palette auf `absolute` setzen. **Wie viele kommen durch?**
      In S4a waren es 9 von 15, in S4b 3 von 8. Die Zahl gehoert in den Bericht, auch wenn sie 0 ist.

  - id: K-07
    befehl: "npm run test:hausplaner"
    erwartet: "ueber dem Stand nach S4b, kein roter Fall"

  - id: L-01
    aussage: "Die Buehne rendert nach dem Umbau noch."
    nachweis: >
      npm run build:hausplaner, dann /admin/hausplaner/studio → Expertenmodus →
      Taste W → zwei Klicks auf LEERER Flaeche → Wand mit Masszahl → Rueckgaengig.
      Zusaetzlich fuer DIESE Scheibe: Command-Palette mit dem Suchen-Knopf oeffnen,
      mit Escape schliessen, und die Statusleiste auf Werte pruefen.
  - id: L-01-anker
    aussage: "Die Messung fand auf der richtigen Seite statt."
    nachweis: >
      VOR jeder anderen Zahl: HTTP 200 · querySelectorAll('canvas') mindestens 1 ·
      document.title enthaelt "Hausplaner". Auch melden, wenn alles gut aussah.
```

## Danach ist AUF-48 durch

```text
S1   sieben reine Funktionen        GRUEN   f7441518
S2   acht Ableitungen               GRUEN   59e91b50
S3   Tastenzuordnung + Zustand      GRUEN   262de870
S4a  Kopfrahmen                     GRUEN   cdc320c0
S4b  Gruppenzeile + Schiene         GRUEN   2eb16643
S4c  die Buehne                     liegt
S4d  Eigenschaften-Panel (67 Inline) liegt
S4e  DIESES BLATT                   liegt
```

**`HausplanerApp.tsx`: 2511 → 1922 Zeilen bisher.** Nach S4c/S4d/S4e bleibt die Hauptfunktion
als Rahmen mit den Zustandshaltern — *und AUF-38 Scheibe 7 hat dann eine Datei, die man lesen kann.*


---

## NACHTRAG 22:00 — die 14 ungeprueften Abhaengigkeitslisten (Evaluator-Befund, geschlossen)

**Sein Befund, an S2 gemessen:** der AST-Vergleich, der die `useMemo`-Huellen prueft, sieht nur
diesen einen Hook.

```text
Hooks mit Abhaengigkeitsliste in HausplanerApp.tsx (per AST gezaehlt):
   useMemo       15      <- geprueft
   useCallback    7      <- UNGEPRUEFT
   useEffect      7      <- UNGEPRUEFT
   ----------------------
   UNGEPRUEFT:   14  von 29
```

**Fast die Haelfte faellt aus dem Sicherheitsnetz** — und bei einer Zerlegung ist das nicht
theoretisch. *Er nennt einen echten Fehlerpfad, den er versehentlich selbst erzeugt hat:*
`setSchienen(ladeSchienen(activeWorkspace))` **ohne** `activeWorkspace` in der Liste laedt beim
Bereichswechsel nicht neu — **und kein Test der Suite wird davon rot.**

**Sein Satz dazu: *„Der Aufwand, das zu schliessen, ist ein Wort."*** Aus
`expression.endsWith("useMemo")` wird eine Pruefung auf drei Namen, der Rest des Befehls bleibt.

```yaml
  - id: K-AB
    aussage: "Alle drei Hooks mit Abhaengigkeitsliste sind verriegelt, nicht nur useMemo."
    befehl: >
      Der AST-Vergleich aus dem S2-Blatt, mit einer Aenderung:
        alt   n.initializer.expression.getText(f).endsWith("useMemo")
        neu   ['useMemo','useCallback','useEffect'].some((h) =>
                n.initializer.expression.getText(f).endsWith(h))
      Vergleicht wie bisher Name gegen zweites Argument, ueber BEIDE Dateien.
    erwartet: "basis == kandidat und diff == [] fuer alle drei Hook-Arten"
    gegenbeweis: >
      Entferne EINEN Eintrag aus einer `useEffect`-Liste, die in die neue Datei gewandert ist.
      Bleibt die Pruefung gruen, sieht sie den Hook nicht — und das ist der Befund.
      *Herkunft: Evaluator am S2-Pruefstand. Er hat den Fehlerpfad versehentlich selbst erzeugt
      und ihn gemeldet, statt ihn stillschweigend zu beheben.*
