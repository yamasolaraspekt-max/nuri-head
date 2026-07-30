# M2 — `scripts/bestand.sh`: was steht schon da, bevor ich ein Kriterium schreibe

**Spur B** *(ein Leseskript, kein Datenpfad)* · **Heimat: ticket** · *Geschnitten 30.07. 22:27*

**Herkunft:** `massnahmenplan-2026-07-30.md` §M2, dort seit dem **30.07. früh** mit
*„Wer: **Generator baut das Skript**, Planner ist der Pflichtnutzer"* und *„Aufwand: zwei
Stunden"*. **Der Prüfer mahnt ihn seit 11:33 (PB-041).**

## Mein Fehler, der ihn hat liegen lassen

**Ich habe M2 als eigenen Ball geführt.** Der Maßnahmenplan sagt seit heute früh ausdrücklich,
dass der **Generator** ihn baut — ich hätte ihn am selben Tag schneiden müssen.
*Neuntes Vorkommen desselben Musters an diesem Abend: ein Posten, der benannt ist und nie in ein
Blatt kommt. Nicht Papier, sondern ein Werkzeug, das seit zwölf Stunden fehlt.*

## Warum es sich lohnt — heute dreimal belegt

**Die Messgröße aus M2: *„Kriterien, die sich später als bereits erfüllt herausstellen. Heute 4.
Ziel: 0."*** Diesen Abend kamen drei dazu:

```text
L4 / AUF-25   ich meldete "ungebaut, fuenf Tage unsichtbar"
              -> gebaut am 25.07., 17c8be22, mit Tests. Buchhaltung fehlte, nicht der Bau.
AUF-48-S4b    mein Anfangsanker existierte nicht mehr — er war mit S4a umgezogen
F-04-Zelle    fuehrte auftrag-pruefen.sh als "steht aus" — liegt seit 10:51 in HEAD
```

**Jedes Mal hätte ein Blick auf den Bestand vor dem Schreiben gereicht.**

## Was das Skript ausgibt

`scripts/bestand.sh <pfad> [<pfad>…]` — je Pfad:

1. **Zeilenzahl** und **letzter Commit**, der ihn berührt hat (Kurz-SHA, Datum in Ortszeit, Betreff)
2. **die Testdateien, die ihn einlesen** — *das ist R12 als Befehl statt als Vorsatz*
3. **Verträge und Register, die ihn nennen** (`werkzeugVertrag`, `arbeitsbereiche`, `werkzeugLandkarte`, …)
4. **offene Posten der Auftragstafel**, die denselben Pfad führen
5. **existiert der Pfad überhaupt** — und wenn nein: gab es ihn je? (`git log --diff-filter=AD`)

*Punkt 5 steht nicht im Maßnahmenplan. Er kommt aus dem heutigen Abend: zweimal habe ich über
etwas geurteilt, das es längst gab.*

## Kriterien

```yaml
  - id: K-01
    aussage: "Fuenf Angaben je Pfad, keine fehlt."
    befehl: "bash scripts/bestand.sh resources/planner/hausplaner/app/HausplanerApp.tsx"
    erwartet: >
      Zeilenzahl · letzter Commit · Liste der einlesenden Testdateien · nennende Register ·
      Tafelposten. **Ist eine Angabe leer, steht dort „keine" — nie eine leere Zeile.**
    gegenbeweis: >
      Auf einen Pfad anwenden, den es NICHT gibt (`app/gibtsnicht.tsx`).
      Erwartet: eine klare Meldung samt `git log --diff-filter=AD`-Ergebnis,
      **kein Fehlschlag und keine leere Ausgabe.**

  - id: K-02
    aussage: "Die Testdateien-Liste ist vollstaendig, nicht gemustert."
    gegenbeweis: >
      Fuer `HausplanerApp.tsx` gegen die bekannte Zahl pruefen — R12/F-01 nennt **22**.
      Weicht sie ab, ist entweder die Zahl ueberholt oder das Skript sucht nach einem Muster
      statt nach der Menge. **Beides gehoert gemeldet, nicht stillschweigend angepasst.**
      *F-01 ist genau diese Fehlerklasse.*
    abweichung_geklaert: >
      Der Planner hat die Abweichung am 30.07. 23:20 gemessen, BEVOR quittiert wird — sie
      liegt an der Zahl, nicht am Skript. Belege unten unter „Die 22 und die 35".

  - id: K-03
    aussage: "Das Skript liest, es schreibt nicht."
    befehl: "grep -cE '>|>>|rm |mv |git (add|commit|checkout)' scripts/bestand.sh"
    erwartet: >
      0 ausserhalb von Zeichenketten. *Ein Bestandsskript, das etwas veraendert, ist eine
      Falle — es wird oft und unbedacht aufgerufen.*

  - id: K-04
    aussage: "Es laeuft ohne Netz und ohne Datenbank."
    hinweis: >
      Nur `git`, Dateisystem und `grep`. **Kein `php artisan`, kein `npm`** — sonst haengt
      der Bestand an einer laufenden Umgebung und faellt aus, wenn man ihn braucht.
```

## Nicht in diesem Auftrag

**Der Validator-Anschluss** (M2 Schritt 3: *„ein Blatt ohne Bestandsprotokoll ist
unvollständig"*). *Erst muss das Skript da sein und sich bewaehren; ein Tor davor zu bauen,
bevor jemand hindurchgegangen ist, sperrt nur.* **Eigener Posten danach.**

**Reihenfolge: nach AUF-48**, gern vor den restlichen Papierposten — *es macht jedes weitere
Blatt besser, und heute Abend hat sein Fehlen dreimal gekostet.*

## Die 22 und die 35 — die Abweichung ist geklaert (Planner, 30.07. 23:20)

Das Skript meldet fuer `HausplanerApp.tsx` **16 direkt · 29 indirekt · 35 zusammen**.
Das Blatt nannte **22**. Beide Zahlen sind richtig — sie beschreiben **verschiedene Dateien**.

```text
befehl:  git grep -l 'HausplanerApp' c8ef4a6d -- resources/planner/hausplaner/__tests__ | wc -l
         (c8ef4a6d = letzter Commit vor dem 29.07. 12:00, dem Stand von R12)
ergebnis: 22

befehl:  git cat-file -e c8ef4a6d:resources/planner/hausplaner/__tests__/_zerlegteApp.ts
ergebnis: existiert nicht

befehl:  git show c8ef4a6d:resources/planner/hausplaner/app/HausplanerApp.tsx | wc -l
ergebnis: 2308 Zeilen        (heute: 1130)
```

**AUF-48 hat die Datei in acht Module zerlegt.** Die Tests sind den Modulen gefolgt: was am
29.07. `HausplanerApp.tsx` direkt einlas, liest heute `Kopfrahmen.tsx` oder
`GruppenzeileUndSchiene.tsx`. Die direkte Zahl faellt deshalb von 22 auf 16 — und der Weg,
den es am 29.07. gar nicht gab, kommt hinzu: `__tests__/_zerlegteApp.ts` reicht die Datei an
**29** weitere Tests durch.

*16 und 29 ergeben zusammen nicht 45, sondern 35 — die Ueberschneidung wird abgezogen. Dass
das Skript diese Unterscheidung ueberhaupt trifft, ist der Punkt: eine einzige Zahl haette
hier wieder wie ein Widerspruch ausgesehen.*

**Fuer die Abnahme heisst das:** Die Abweichung ist **kein K-02-Verstoss**. Das Skript sucht
die Menge, nicht ein Muster. Zu korrigieren ist die Zahl **22** im Register R12/F-01 — sie
gilt fuer einen Dateizustand, den es seit dem 30.07. nicht mehr gibt.

