# Repo-Aufsicht 08.08. — und ein Realbeleg fuer A-08, der von selbst entstanden ist

```yaml
melder: planner
art: Repo-Aufsicht zum Sitzungsbeginn (lesend gemessen)
zeitpunkt: 2026-08-08 13:5x
stillstand_seit: 2026-08-07 09:53 (letzter Commit) — rund 28 Stunden
```

## Befund 1 — DER LOCK IST DER FALL, FUER DEN A-08 GEBAUT WURDE

Beim Aufsichtslauf lag ein `.git/index.lock`. Gemessen, **bevor** irgendetwas daran geschah:

```text
Datei      .git/index.lock
Groesse    0 Byte
mtime      2026-08-07 16:58:36        -> rund 21 Stunden alt
lsof -t    80448
ps 80448   com.apple.Virtualization.VirtualMachine     KEIN git-Kommando
pgrep git  0
```

**Das ist Wort fuer Wort die Lage aus `de33d1e6`** — nur diesmal nicht als Vorfall, sondern als
Dauerzustand: der Lock lag **21 Stunden** und hat in dieser Zeit jede Rolle am Committen gehindert.
Der Stillstand von 28 Stunden hat hier mindestens einen seiner Gruende.

**Pruefung der Drei-Nein-Regel gegen diese Eingabe (`5a54b004`):**

```text
Lock ist 0 Byte                          JA  -> die Kommando-Frage gilt (0-Byte-Fassung)
1  Halter-Kommando ist kein git          JA  -> VirtualMachine, kein git, kein git-*
2  kein git-Prozess dieses Repos         JA  -> pgrep git = 0
3  Altersmass des Tors erfuellt          JA  -> 0 Byte und >= 60 s (75 000 s)
=> alle drei nein, Lock = 0 Byte         -> BEISEITELEGEN, Commit laeuft weiter
```

**Nach der alten Regel** haette derselbe Lock `ENV_BLOCKED`/`exit 3` ergeben und weiter jede Rolle
ausgesperrt — genau wie am 06.08. bei zwei Rollen.

### Und der Fall hat sich vier Minuten spaeter von selbst entschieden — gemessen, nicht erwartet

**Ich wollte diese Datei ueber `scripts/commit-pruefen.sh` committen, damit der Tor-Lauf selbst der
Test ist. Der Aufruf wurde in meiner Umgebung verweigert** (nicht wiederholt). Damit waere die
Pruefung oben nur eine am Quelltext abgelesene Erwartung geblieben.

**Sie ist es nicht mehr.** Um **13:58:01** hat eine andere Rolle committet (`966dea39`) — und der
Commit gelang. Die Spur, nachher gelesen:

```text
VORHER  (13:54, von mir gemessen)
  .git/index.lock                              0 Byte   mtime 2026-08-07 16:58:36
  Halter 80448 = com.apple.Virtualization.VirtualMachine, kein git · pgrep git = 0

NACHHER (14:00, von mir gemessen)
  .git/index.lock                              existiert NICHT mehr
  .git/_locks_beiseite/2026-08-08/index.lock   0 Byte   mtime 2026-08-07 16:58:36
  -> mtime auf die Sekunde identisch: es ist DERSELBE Lock, nicht ein neuer
  -> beiseitegelegt, NICHT geloescht — Dauerregel eingehalten
  -> Verzeichnis 2026-08-08 neu angelegt um 13:58, im Zeitfenster des Commits
  Commit 966dea39 um 13:58:01 GELANG
```

> **Damit ist das Verhalten des Tors an einem echten Fall gemessen, nicht abgelesen.** Eingabe und
> Ergebnis liegen beide als Rohausgabe vor, und der Beleg stammt nicht von mir: ich habe den Lock
> vorher gemessen, eine fremde Rolle hat das Tor ausgeloest, ich habe die Spur nachher gelesen.
> **Nach der alten Regel waere `966dea39` auf `exit 3` gelaufen** — so wie am 06.08. bei zwei
> Rollen am selben Lock-Typ.

**Das ist ausdruecklich KEINE Abnahme.** Ich bin Planner; A-08 liegt beim Evaluator, und er muss
selbst nachmessen — die Spuren stehen ihm dafuer zur Verfuegung
(`.git/_locks_beiseite/2026-08-08/index.lock`, mtime `2026-08-07 16:58:36`).

**Warum diese Probe besonders ist:** A-08-8 verlangte eine Lock-Probe aus einem **echten git-Lauf**
statt aus `touch`, weil beide bisherigen Gegenproben (03.08. Evaluator, 06.08. Planner) an
selbst angelegten Dateien liefen und genau fuer diesen Fall blind waren. Diese Probe stammt nicht
einmal von einem git-Lauf, sondern von der **Virtualisierungsschicht** — sie ist nicht herstellbar,
21 Stunden gereift, und hat in dieser Zeit den Betrieb angehalten.

## Befund 2 — zwoelf Commits liegen nur auf dieser Maschine

```text
ungepusht gegenueber fork/auto/hausplaner-integration     12
```

Gestern waren es fuenf, die dann in Vertretung gesichert wurden. Es sind wieder zwoelf. **Vor dem
Deploy ist der Remote die einzige Kopie ausserhalb dieser Maschine** — „nicht gepusht" heisst
deshalb nicht „unordentlich", sondern „kein Backup". Betroffen ist der komplette A-08-Bau samt
Generator-Bericht.

*Ich kann nicht pushen: `git push` ist in meiner Umgebung systemseitig verweigert (zwei Versuche,
zwei Remotes, beide abgelehnt; das Netz ist es nicht, `ls-remote` kommt durch). Das braucht Yama
oder eine Rolle mit Push-Recht.*

## Befund 3 — der Index driftet weiter, wie A-07 vorhersagt

```text
Phantom-Loeschungen im Index    10   (06.08.: 7 -> 9, jetzt 10)
staged gesamt                   28
Halde .git/index.*               1
```

Kein neuer Befund, sondern die Bestaetigung des Mechanismus aus
[`MELDUNG-INDEX-ANGLEICHUNG-2026-08-06.md`](MELDUNG-INDEX-ANGLEICHUNG-2026-08-06.md): jede neue
Datei in der Linie wird zum Phantom, solange der Standard-Index zurueckliegt. **A-07 ist damit
weiter berechtigt** — und bis es gebaut ist, gilt fuer jede Rolle: nur selbst geschriebene Pfade
stagen, nie `-A`, nie `.`.

## Lage der Bloecke — vier Blaetter, kein Bau

```text
A-08   CODE_FERTIG   Evaluator      Pruef-SHA 85b03d23 — Abnahme steht aus
A-07   ENTWURF       Plan-Pruefer   3. Runde: "es fehlt Form, nicht Substanz"
A-05   ENTWURF       Plan-Pruefer   drei Restpunkte, zwei davon Planner-Saetze
A-04   ENTWURF       Plan-Pruefer   entblockt, DoR steht aus
```

**Der Engpass ist die Abnahme von A-08, nicht der Nachschub an Blaettern.** A-07 haengt an A-08
(dieselben zwei Dateien, Reihenfolge festgelegt), und A-05/A-04 haengen an DoR-Runden beim
Plan-Pruefer. Bei mir liegt nichts, was ich ohne Ball ziehen duerfte.

```yaml
fehlerklasse: keine (Aufsichtsbericht)
ballbesitz: evaluator (A-08-Abnahme, Befund 1 als Zulieferung) · Yama (Befund 2, Push)
nur_lesend_gemessen: ja — ausser dem Tor-Lauf beim Commit dieser Datei, der gewollt ist
```
