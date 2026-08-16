# Die Freigabe hat den Integrator erreicht — zugestellt, nicht ausgeführt

> **Release-Prüfer, 16.08. ~17:2x, in Yamas Namen.** Auf seinen Auftrag zu der Meldung *„Der
> Integrator wartet auf eine Freigabe, die ihn nicht erreicht… Meine Freigabe von 17:1x gilt
> unverändert und mit beiden Auflagen — sie muss ihn nur erreichen."*

## Es war dieselbe Ursache wie beim Evaluator, zum vierten Mal heute

Gemessen, bevor ich etwas angefasst habe:

```
Integrator-Baum stand auf   a7b2ea65   16.08. 16:56
Rueckstand                  230 Commits      voraus: 0
A-37-18 in seiner Datei     0 Treffer
Baelle, die er sah          0
```

**Die Freigabe war erteilt und für ihn nicht vorhanden.** Sie lag in einer Datei, die auf seinem
Zweig einen Stand von 16:56 hatte — geschrieben wurde sie um 17:13 und 17:19. Er hat nicht
gezögert; er konnte nichts sehen.

## Zugestellt

```
vorher   a7b2ea65   Tor im Baum: 0   Baelle: 0
nachher  2b73e02e   Tor im Baum: 1   Baelle: 1  ->  A-37 (BEREIT)

a774e549  Freigabe fuer den Integrationslauf   liegt jetzt in seinem Baum: JA
d91f1dca  nachgetragene Auflage                liegt jetzt in seinem Baum: JA
```

**Beide Auflagen sind mitgekommen**, nicht nur die Freigabe — das war der Punkt deiner Meldung.

### Vorbedingungen einzeln geprüft, und einmal genauer hingesehen

```
voraus vor dem Transportstand   0    -> reines Vorspulen, kein Commit von ihm geht verloren
uncommittet im Baum             0
HEAD ueber 3 s, dann ueber 4 s  unbewegt
Git-Locks in .git               0
```

**Ein Signal habe ich nicht als Artefakt abgetan:** `ps` meldete vier Prozesse im
Integrations-Verzeichnis. Geöffnet sind es **drei Herd-PHP-Server** — die lokale Anwendung läuft aus
genau diesem Verzeichnis, seit drei bis fünf Tagen. Keine Git-Prozesse, keine Rollenarbeit, aber ein
echter Umstand: **ich bewege Dateien unter einer laufenden App.**

Deshalb habe ich die Zahl des Integrators **nicht übernommen, sondern selbst gemessen**, was der
Sprung anfasst:

```
docs      66 Dateien
scripts    5 Dateien
app 0 · resources 0 · database 0 · routes 0 · config 0 · tests 0 · public 0 · bootstrap 0
                                                              71 Dateien insgesamt
```

Acht Produktivpfade, acht Nullen — unabhängig bestätigt, was er in `a7b2ea65` berichtet hatte. Die
laufende Anwendung ist nicht berührt.

**Rückfallpfad:** `a7b2ea65` liegt gesichert. `git reset --hard a7b2ea65` stellt den Stand von 16:56
wieder her.

## Was ich ausdrücklich NICHT getan habe

**Den Integrationslauf.** Die Freigabe gilt ihm, nicht mir. Ich habe sie zugestellt — ausführen ist
seine Handlung, und die Trennung ist der Grund, warum es beide Rollen gibt. Sein Baum ist jetzt
bereit, das Tor liegt darin, A-37 steht sichtbar auf `BEREIT` mit seinem Namen.

Die beiden Auflagen gelten unverändert und stehen jetzt in seinem Baum:
1. **Konflikte werden gemeldet, nie still aufgelöst** (Regel 4).
2. **Der erste schreibende `--tafel`-Lauf ist NICHT freigegeben.** Verbindliche Reihenfolge: erst
   Integrationslauf, dann Umzug der 104 Befundnotizen als eigener Auftrag ohne Löschen, dann
   Schreiblauf. Der Integrationslauf selbst ist davon nicht betroffen — er ist ein Merge.

## Nebenbei: A-37-18 ist ein Drittel weiter

```
Tor im Baum      heute frueh   jetzt
ticket                    0  ->   1     <- durch diese Zustellung
ticket-rolle-generator    1       1
ticket-rolle-evaluator    1       1
ticket-release-pruefung   1       1
ticket-rolle-planner      0       0
ticket-rolle-plan-pruefer 0       0
                      3 von 6  ->  4 von 6
```

Die Sperre meldet das selbst und zündet weiterhin nicht: *„Das Tor liegt in 4 von 6 Zweigen… Sie
zündet, sobald der Transport das Tor überall hingebracht hat (A-37-18)."*

**Es fehlen genau zwei Bäume: Planner und Plan-Prüfer.** Beide gehen heute *nicht* auf diesem Weg —
sie haben eigene Commits voraus und arbeiten im Minutentakt; das wäre ein Merge in einem fremden
Baum, kein Vorspulen. Sie brauchen je einen eigenen Befehl:

```
git fetch --multiple origin fork backup-private
git merge --ff-only fork/rolle/release-pruefer
```

## Die Regelfrage, die daraus zum vierten Mal folgt

**Ein Ball, den der Adressat nicht sehen kann, ist kein Ball** — heute bei A-41 (Evaluator),
W-17/1 (Generator), A-37 (Integrator) und jetzt bei der Freigabe selbst. Vier von vier Mal war die
Zuweisung formal korrekt und praktisch wirkungslos.

Sie liegt weiterhin bei Yama: **soll ein Ballwechsel erst als vollzogen gelten, wenn der Zielzweig
den Datensatz auch trägt?** Bis dahin gilt der Behelf: *wer einen Ball oder eine Freigabe
ausspricht, prüft, ob der Zielzweig sie tragen kann — sonst spricht er in einen leeren Raum.*
