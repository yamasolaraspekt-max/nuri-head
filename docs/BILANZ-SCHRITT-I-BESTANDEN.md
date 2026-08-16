# Weg 1 ist durch — Schritt I trägt, Schritt J liegt bei Yama

> **Release-Prüfer, 16.08. ~20:0x.** Auf Yamas Entscheidung *„dann bitte weg 1 machen"*.
> Alles am Commit gemessen, das Votum des Evaluators nicht übernommen, sondern gegengeprüft.

## Ergebnis

**Schritt I ist bestanden und gültig. V6 ist vollständig belegt.** Die einzige Voraussetzung, die
ich heute Nachmittag als Ablehnungsgrund genannt habe, trägt jetzt.

## Der Ablauf, den Weg 1 genommen hat

```
19:49:23   Generator baut A-37-19                       (1c36544e)
19:56:56   meine Warnung: Fertigmeldung ueberholt        (bb32131f)
19:57:35   Evaluator: SCHRITT I BESTANDEN               (68b3d7e6)   39 s spaeter
19:59      Plan-Pruefer misst meine Warnung nach        "sie traegt in jeder Zahl"
20:01:55   Generator: neue Fertigmeldung, 19 Kriterien  (ea377567)
20:05      Plan-Pruefer bestaetigt die zweite Meldung   (059198ab)
```

## Was ich am Votum gegengeprüft habe — und was dabei meine eigene Warnung einordnet

Der Evaluator meldet *„am Bau `97f1dd00`"*, und meine Warnung von 19:56 sagte, dieser Stand sei
überholt. **Beides stimmt, und trotzdem trägt sein Votum.** Gemessen:

```
rollen-tor.sh  bei 97f1dd00 (sein genannter Bau)   3c0bc991
rollen-tor.sh  bei 68b3d7e6 (sein Prüfzeitpunkt)   d6487996
rollen-tor.sh  heute (HEAD)                        d6487996   -> UNVERAENDERT

die einzige Aenderung dazwischen (fb59f6cc):  16 Zeilen, ALLE Kommentar (+  #)
A-37-19 (1c36544e) aendert:  scripts/commit-pruefen.sh · rollen-tor.sh 0 mal
```

**Er hat faktisch die heutige Fassung gefahren**, nicht die von 19:14 — die SHA-Angabe im Votum ist
die aus der Fertigmeldung, nicht sein Prüfstand. Und **A-37-19 berührt den Prüfgegenstand nicht**:
es ändert `commit-pruefen.sh`, das Rollen-Tor null mal.

**Meine Warnung war richtig, zielte aber auf etwas anderes.** Sie galt der **Abnahme von A-37**
(neunzehn Kriterien gegen eine Meldung über achtzehn) — nicht **Schritt I**, der nach V6 nur
*„positive und negative Sperrfälle"* verlangt. **Ich hatte beides vermischt.** Für die A-37-Abnahme
bleibt sie gültig; der Generator hat um 20:01 nachgemeldet, damit ist auch das geräumt.

## Was sein Votum belegt

```
positiv   5 von 5    jede Rolle im EIGENEN Baum an docs/STATUS.md -> exit 1, VERSTOSS
negativ   6 von 6    Integrator im Integrations-Checkout laeuft durch;
                     dieselben fuenf Rollen bei ANDEREN Dateien laufen durch
Kanten    K1 … K6    je einzeln gefahren statt am Buchstaben gezaehlt
```

**Und ein eigener Messfehler, den er offenlegt:** seine ersten elf Läufe liefen im
Prüfstand-Worktree, wo bei jeder Rolle K3 greift — *„die negativen Fälle waren damit wertlos und
zeigten die Baum-Kante statt der Pfad-Sperre"*. Neu gefahren in den echten Bäumen je Rolle. **Das
ist der Teil, der sein Votum belastbar macht**: er hat den Fall gefunden, in dem seine eigene
Messung das Falsche gemessen hätte.

Er hat außerdem `docs/STATUS.md` **nicht angefasst** — *„die Sperre gilt auch für mich"*.

## Die sechs Voraussetzungen, Stand jetzt

```
V1  vier Schreibstopps einzeln belegt      nicht am Git ablesbar — das Blatt sagt das selbst
V2  keine alte Instanz schreibt mehr       mit meinen Mitteln nicht belegbar (s. u.)
V3  Arbeitsbaum vollstaendig aufgenommen   ERFUELLT   0 Eintraege
V4  Schreibprozesse ausgeschlossen         ERFUELLT   0 Locks, 0 git-Prozesse
V5  Ruhephase gemessen                     kein Beleg mit Beginn/Ende/HEAD
V6  Rollen-/Checkoutschutz aktiv           ERFUELLT   5/5 + 6/6 + K1-K6, Gegenstand unveraendert
```

**Zwei eigene Fehlmessungen in dieser Runde, beide vor der Meldung gefangen:** V4 meldete zuerst
„3 git-Prozesse" — geöffnet sind es **null**, mein `grep -c` zählte die eigene Suchzeile mit. Und
mein Filter meldete „2 ABGENOMMEN beim Release-Prüfer" — geöffnet sind es A-05 und A-12 mit Ball
`— (geschlossen 12.08. …)`; mein Muster prüfte auf genau `—` und traf den längeren Wert nicht.
**§10 ist nicht fällig.**

**Zu V2 sage ich weiterhin weniger, als eine Zahl hergäbe:** welcher Baum einen Commit erzeugt hat,
steht nicht in der Historie. Seit dem Umzug ist es plausibel erfüllt, belegt ist es nicht — und ich
gebe es nicht als belegt aus.

## Was jetzt bei Yama liegt

**Nur noch Schritt J**, und der Grund, aus dem ich ihn heute dreimal nicht erteilt habe, ist
weggefallen: V6 trägt. Übrig sind V1, V2 und V5 — **keine davon kann ich messen**, alle drei hängen
an Belegen außerhalb von Git (Schreibstopps, Ruhephase) oder an einer Information, die die Historie
nicht führt.

**Ich erteile Schritt J nicht.** Er steht in der Tabelle bei dir, er ist eine Entscheidung über
Vollmacht, und drei der sechs Voraussetzungen kann nur derjenige beurteilen, der die Schreibstopps
erteilt hat. **Was ich beitragen konnte, ist geliefert: V3, V4 und V6 sind gemessen und belegt.**

**Die Blockade kostet weiterhin nichts Messbares** — kein `ABGENOMMEN` wartet auf einen Release, und
Transport, Merges, Blätter und Werkzeuge laufen unbehindert weiter.
