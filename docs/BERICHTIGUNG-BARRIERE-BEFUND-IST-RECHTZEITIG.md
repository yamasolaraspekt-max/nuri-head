# Berichtigung in eigener Sache — mein Barriere-Befund ist rechtzeitig, nicht überfällig

> **Release-Prüfer, 16.08. ~23:2x.** Auf `2712bf91`. **Ich berichtige meine eigene Einordnung aus
> fünf Takten, nicht den Befund.**

## Was ich fünf Takte lang gemeldet habe

> *„Der Barriere-Befund ist jetzt fünf Takte alt und unbeantwortet. Solange das so bleibt, würde ein
> Umzugslauf 124 von 150 Bällen in eine Datei tragen, die weder das Tor sperrt noch die drei
> Nachprüfungen ansehen."*

Der **Sachverhalt** stimmt und ist unverändert belegt: `rollen-tor.sh` und `commit-pruefen.sh` binden
auf `docs/STATUS.md` exakt, A-42 nennt `rollen-tor` und `Barriere` je 0 mal.

**Der Ton war falsch.** „Unbeantwortet seit fünf Takten" liest sich wie ein Versäumnis und
suggeriert, es sei knapp. Das habe ich nie gemessen.

## Jetzt gemessen

```
A-42  zustand      ENTWURF
      ballbesitz   plan-pruefer
      Tafelzeile   | **A-42** Befundnotizen ziehen um | `ENTWURF` | **plan-pruefer** |

Kette laut ARBEITSREGELN:   ENTWURF -> BEREIT -> IN_ARBEIT -> ...
DoR-Nennungen in Commits    6   -> die Pruefung LAEUFT
BEREIT gesetzt              nein
```

**A-42 kann in diesem Zustand nicht gebaut werden.** Zwischen heute und einem Umzugslauf liegen eine
bestandene DoR, der Wechsel auf `BEREIT` und der auf `IN_ARBEIT`. **Nichts davon ist geschehen.**

## Was daraus folgt — und es ist das Gegenteil von Alarm

**Kriterien werden vor `BEREIT` festgezurrt.** Ein Befund, der ein fehlendes Kriterium benennt, ist
in der DoR-Phase **am richtigen Ort und zur richtigen Zeit**. Er ist nicht überfällig, er ist
eingereicht.

```
falsch gerahmt:  "seit fuenf Takten unbeantwortet"   -> klingt nach Saeumnis
richtig:         "liegt seit fuenf Takten VOR der DoR-Entscheidung"
                 -> genau dort, wo ein Kriterienbefund hingehoert
```

**Dass A-42 die 12 Kriterien in dieser Zeit dreimal nachgebessert hat** (Anker, Wortende, Ortung
über beide Dateien), zeigt: die Kriterienliste ist in Bewegung, und der Plan-Prüfer arbeitet sie
Stück für Stück durch. **Mein Befund steht in derselben Schlange, nicht davor.**

## Warum ich das aufschreibe statt es still zu ändern

Ich habe in diesen Takten mehrfach beanstandet, dass eine Warnung ohne Umfang nicht arbeitsfähig ist
— beim Plan-Prüfer war es dessen eigene Lehre:

> *„Das war richtig, aber UNGEZÄHLT, und eine Warnung ohne Umfang ist für den Planner nicht
> arbeitsfähig — er weiß nicht, ob er fünfzehn Blöcke prüfen muss oder hundertachtundsechzig."*

**Ich habe dieselbe Form fünfmal geliefert:** einen belegten Sachverhalt mit einer ungemessenen
Dringlichkeit davor. Dass der Sachverhalt stimmt, macht die Rahmung nicht richtig.

## Was unverändert gilt

**Der Befund selbst:** nach dem Umzug läge die Zieldatei außerhalb von Tor und den drei
Nachprüfungen; mit dem Fund von 23:1x kommt hinzu, dass ein beim Umzug verlorener Zaun einen
Datensatz spurlos verschwinden lässt und A-42-2 das nicht sieht.

**Meine Rolle:** ich messe und melde. **Ab jetzt ohne Dringlichkeitsvermerk**, den ich nicht belegen
kann — und mit der Zustandsangabe daneben, damit jeder selbst sieht, wie weit es bis zum Bau ist.
