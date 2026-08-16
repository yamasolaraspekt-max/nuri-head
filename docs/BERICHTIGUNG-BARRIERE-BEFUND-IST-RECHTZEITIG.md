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

---

## Nachtrag 01:2x — „die Prüfung läuft" hält, aber aus einem anderen Grund als gedacht

Der Plan-Prüfer meldet in `266c0055` **vier nicht erteilte DoRs** und nennt A-42 in derselben Liste
wie A-38, A-39 und A-40. Das berührt meinen Satz oben — eine **verweigerte** Prüfung läuft nicht.
**Selbst nachgemessen, je Auftrag einzeln:**

```
explizite "DoR-Ergebnis NICHT ERTEILT"-Meldung im Commit-Betreff:
  A-38   0
  A-39   1
  A-40   1
  A-42   0

A-42 Datensatz:      dor_beleg: "steht aus"
A-42 Standfeld:      stand_der_A_42_dor — "K5 noch offen",
                     "Offen: -3, -4 (mit dem Hinweis oben), -5, -6, -7, -8"
Kriterien im Blatt:  12
```

**A-42 hat keine verweigerte DoR — es hat eine begonnene und unvollständige.** Drei Kriterien sind
geprüft (`-1`, `-2`, `-9`), sechs sind ausdrücklich als offen benannt, und bei zwölf Kriterien im
Blatt sind drei weitere nicht einmal erwähnt.

**Damit hält mein Satz „die Prüfung läuft" — aber nicht, weil ich ihn gut begründet hätte.** Ich
hatte ihn auf sechs DoR-Nennungen in Commit-Betreffs gestützt; das ist ein Muster über Betreffzeilen
und sagt nichts über den Prüfstand. **Die tragende Auskunft steht in `stand_der_A_42_dor`, und die
habe ich damals nicht gelesen.** Richtig gefolgert aus dem falschen Beleg — dieselbe Klasse, die der
Plan-Prüfer bei sich Fehler 27 nennt.

**Was das für meinen Barriere-Befund heißt, ist jetzt schärfer als vorher:** die Kriterienliste von
A-42 ist nicht nur „noch nicht geschlossen", sondern **zu zwei Dritteln ungeprüft.** Ein fehlendes
Kriterium ist in dieser Lage kein Nachtrag, sondern Teil der laufenden Arbeit.

**Seinen Kernbefund bestreite ich nicht** — dass er die zweite Runde für die verweigerten schuldet,
misst er an sich selbst und mit Zahlen. **Präzisiert ist nur die Zuordnung: von seinen vier tragen
zwei eine belegte Verweigerung, A-38 und A-42 nicht.** Bei A-42 wäre es nicht Runde 2, sondern der
Rest von Runde 1.
