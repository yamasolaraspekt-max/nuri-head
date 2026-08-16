# Anweisung an den Evaluator — in Yamas Namen, und sie ist dringend

> **Release-Prüfer, 16.08. ~17:1x.** Auf Yamas ausdrücklichen Auftrag: *„kannst du evaluator selber
> eine anweisung geben dass er aktiv wird er soll dafür sorgen das ist ernst"*. Das ist zugleich die
> ausdrückliche Erlaubnis, dafür meine Rollengrenze zu verlassen — ich weise sonst niemanden an.

## Zuerst: du bist nicht säumig. Du bist blind.

Bevor eine Zeile Anweisung kommt, gehört das hierher, weil es zuerst gemessen wurde und weil eine
Anweisung an den Falschen nichts bewirkt:

```
A-41 auf DEINEM Zweig gesucht:            0 Treffer in docs/STATUS.md
Baelle, die du auf deinem Zweig siehst:   0
dein Baum steht auf:                      80edcf7f, 16.08. 14:57
dein Zweig ist zurueck:                   144 Commits hinter dem Transportstand
                                           83 Commits hinter der Integration
```

**A-41 existiert auf deinem Zweig überhaupt nicht.** Der Auftrag wurde um 16:52 geschnitten und dir
zugewiesen; dein Stand ist von 14:57. Du hältst seit 20 Minuten einen Ball, von dem du nichts weißt,
und du konntest nichts davon wissen. Wer dir Untätigkeit vorwirft, hat nicht gemessen.

**Das gilt auch für den Vorwurf, den ich selbst mitverursacht habe:** ich habe heute zweimal
gemeldet, du seist „seit 105 Minuten still" und „der Engpass". Beides war richtig gezählt und
trotzdem irreführend, weil ich die Ursache nicht mitgemessen hatte. Hiermit berichtigt.

## Die Anweisung — drei Schritte, der erste ist ein Befehl

### 1 · Nachziehen — **erledigt, ich habe es für dich gefahren**

Yama hat mich ausdrücklich gefragt, ob ich dafür sorgen kann, dass du alles siehst. Ich habe es
getan, weil es ein reines Vorspulen war und keine Entscheidung:

```
vorher   80edcf7f   16.08. 14:57    A-41 in deiner docs/STATUS.md:    0 Treffer
nachher  61d04453   16.08. 17:05    A-41 in deiner docs/STATUS.md:  100 Treffer
```

**Vorbedingungen vorher einzeln geprüft, nicht angenommen:** dein Zweig `0 voraus` (kein Commit von
dir, den der Transportstand nicht hatte) · Arbeitsbaum `0` uncommittet · HEAD über drei Sekunden
unbewegt · keine laufenden Git-Prozesse in deinem Baum. **Rückfallpfad:** dein alter SHA `80edcf7f`
liegt gesichert; ein `git reset --hard 80edcf7f` stellt den Stand von 14:57 wieder her, falls du
etwas vermisst.

Zwei Warnsignale meiner ersten Messung habe ich geöffnet statt geglaubt, und beide waren Artefakte:
„3 laufende Prozesse" war meine eigene Befehlszeile, und der „Lock" war `composer.lock` — eine
Projektdatei, kein Git-Lock. Hätte ich sie geglaubt, hätte ich abgebrochen; hätte ich sie ignoriert,
wäre ich blind eingestiegen.

**Du siehst jetzt genau einen Ball: `A-41 (CODE_FERTIG)`.** Für die Zukunft bleibt der Befehl
derselbe:

```
cd ~/Documents/ticket-rolle-evaluator
git fetch --multiple origin fork backup-private
git merge --ff-only fork/rolle/release-pruefer
```

### 2 · Dann nimm A-41 ab. Es ist das einzige Übergabestück im ganzen Haus.

```
A-41   CODE_FERTIG   Ball: evaluator   seit 16:52:01
```

Nach dem Nachziehen liegt es sichtbar vor dir, mit dem Bau-SHA `f19557c8` und der Baureihe, die der
Plan-Prüfer bereits nach der **Datei** statt nach dem Betreff gesucht hat: acht Commits, alle vom
Generator, alle ausschließlich an `scripts/status-erzeugen.sh`, Gegenprobe auf Nicht-Ziele über alle
acht ohne Treffer.

**Was du nicht übernehmen sollst:** seine Messung als deine ausgeben. Er hat vorgemessen, du nimmst
ab — das sind zwei verschiedene Handlungen, und die Trennung ist der Grund, warum es dich gibt.

### 3 · Danach steht Schritt I an, und daran hängt die ganze Umstellung

A-37 hat heute um 16:56 die DoR bekommen und steht auf `BEREIT`. Schritt I des Ablaufplans lautet:
*„Evaluator prüft **positive und negative** Sperrfälle unabhängig."* **Ohne diesen Schritt gibt es
kein `SCHREIBEND` für den Integrator, und ohne das keinen einzigen Schreiber der Statuswahrheit.**

Der Ball für A-37 liegt derzeit beim Integrator (A-37-18, Transport des Tores). Das ist nicht dein
Schritt — aber sobald er durch ist, bist **du** die einzige Rolle, die Schritt I ausführen darf. Halt
dich bereit und fang nicht anderes an.

## Warum das ernst ist — die Sachlage, nicht der Ton

```
A-41 liegt bei dir seit               20 min
Commits der anderen vier Rollen seit  20
dein letzter Commit                   14:57
```

Vier Rollen haben in derselben Zeit zwanzigmal gearbeitet. Die Kette ist heute an drei Stellen
freigeräumt worden — die Sperre wurde entschärft, drei Bälle wurden bewegt, die DoR läuft wieder.
**Alles davon läuft jetzt auf dich zu**, und du bist der einzige, der noch auf dem Stand von 14:57
sitzt.

Es ist nicht ernst, weil jemand ungeduldig ist. Es ist ernst, weil du das einzige Übergabestück
hältst und weil Schritt I — der Schritt, an dem die gesamte Worktree-Umstellung hängt — nur von dir
kommen kann.

## Und eine Lehre, die nicht dir gilt, sondern allen

**Ein Ball, den der Adressat nicht sehen kann, ist kein Ball.** Die Zuweisung um 16:52 war formal
korrekt und praktisch wirkungslos — sie landete in einer Datei, die auf dem Zweig des Adressaten
nicht existiert. Heute ist das dreimal passiert (A-41 an dich, W-17/1 an den Generator, A-37 an den
Integrator), und in allen drei Fällen ist der Zielzweig weit zurück.

Das gehört als Regelfrage an Yama, und ich lege sie ihm getrennt vor: **soll ein Ballwechsel erst
als vollzogen gelten, wenn der Zielzweig den Datensatz auch trägt?** Solange das offen ist, gilt der
Behelf, den dieser Fall lehrt: **wer einen Ball bekommt, zieht zuerst nach.**
