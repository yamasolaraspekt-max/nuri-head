# ⇒ VORSCHLAG — Laufzeiten und Takt (nativ · Cowork)

**Angelegt:** 25.07.2026 · **Vom:** Planner (Cowork) · **Status: VORSCHLAG.**
**Bindet erst mit Yamas Freigabe.** Er ergänzt `00-zyklus.md` bis `03-evaluator.md` und hebt nichts
davon auf; bei Konflikt gelten BETRIEBSORDNUNG und CLAUDE.md.

---

## 0. Was heute schiefging — und was **nicht**

**Nicht schiefgegangen:** die Rollentrennung. Planner, Generator und Evaluator waren sauber getrennt,
niemand hat eigene Arbeit abgenommen, jedes Votum kam von einer anderen Instanz.

**Schiefgegangen:** **zwei Laufzeiten haben um dieselben Dateien gerannt.** Drei
Generator-Läufe brachen ab, weil ein anderer Strang die Datei schon offen hatte:

| Posten | Kollision | Beleg |
|---|---|---|
| N2 (AUF-16) | `HausplanerApp.tsx` bereits umgebaut, fremder untracked Test im Baum | `982384d` |
| AUF-4 / A2 | zweiter Strang zieht denselben Posten und schreibt dieselbe Datei | `a61f10e` |
| AUF-25 / L4 | zweiter Strang zieht L4, schreibt, rollt 40 s später zurück | `a4bc277` |

Kein Byte ging verloren — die Pfadangabe-Regel und die Abbruchklausel haben gehalten. Aber zwei
Stränge auf einer Datei verdoppeln den Fortschritt nicht, sie **halbieren** ihn: einer arbeitet,
einer bricht ab.

**Der Zyklus kennt bisher nur eine Achse: die Rolle.** Es fehlt die zweite: **die Laufzeit.**

---

## 1. Die zwei Laufzeiten — gemessen, nicht behauptet

| Fähigkeit | **nativ** (Claude Code auf Yamas Rechner) | **Cowork** (Cloud, über die Geräte-Brücke) |
|---|---|---|
| `build:hausplaner` | **ja** | **nein** — `@rollup/rollup-linux-arm64-gnu` fehlt auf aarch64 |
| `tsc` · `schema:check` · `test` | ja | ja (~2–3 s, synchron) |
| git schreiben | sauber | **jeder** Schreibvorgang hinterlässt `.git/*.lock`, `rm` ist verboten |
| Browser-Sichtprobe | nein | **ja** — Chrome-Anbindung, echtes Rendern |
| Repo-Skills (`.claude/skills/`) | greifen automatisch | **greifen nicht** — nur Konto-Skills |
| Projekt-`CLAUDE.md` | greift | greift nicht |
| mehrere Agenten parallel | begrenzt | ja |
| Lesen über alle Apps (`~/Herd`, `wissensregister`, Downloads) | ja | ja, wenn Ordner freigegeben |

**Daraus folgt die Zuteilung — sie ist keine Meinung, sondern die Liste oben:**

- **Bauen und Abnehmen laufen nativ.** Generator und Evaluator brauchen Build und Gates und einen
  sauberen git-Baum. Sie gehören auf den Rechner.
- **Planen, Messen, Sichten läuft in Cowork.** Planner-Aufträge, Inventuren, Recherche über App-Grenzen,
  Vorarbeit die man wegwerfen darf — und die **Browser-Sichtprobe**, die nativ nicht möglich ist.
- **Cowork schreibt ausschließlich `docs/`.** Nie `resources/`, nie `app/`, nie `routes/`,
  nie `public/`. **Das ist die Grenze, die heute gefehlt hat.** Braucht Cowork eine Codeänderung,
  schreibt es einen Auftrag — es baut ihn nicht selbst.

**Ausnahme, ausdrücklich:** Ist nativ nicht verfügbar und Yama beauftragt Cowork direkt mit einem
Bau, gilt Spur A, der Posten wird auf der Tafel gezogen, und der Bericht hält fest, dass
`build:hausplaner` **nicht** gelaufen ist.

---

## 2. Der Takt — ein Posten von Anfang bis Ende

```
   Planner (Cowork)          Generator (nativ)         Evaluator (nativ, frisch)
   ────────────────          ─────────────────         ─────────────────────────
1  Auftrag schreiben
   docs/auftraege/…
   Spur · Nahtstellen
   Kanten · Rückweg
   Abnahmekriterien
   ─ commit nur docs/ ─►
                        2  ZIEHEN auf der Tafel
                           commit nur AUFTRAGSTAFEL.md
                           ─ erst danach die erste Zeile ─
                        3  bauen: Code UND Tests
                        4  Bericht in den Ledger
                           Tafel → BERICHTET
                           meldet „umgesetzt", nie „grün"
                                                   ─►  5  ZIEHEN auf der Tafel
                                                       6  selbst messen, Gegen-Beweis
                                                          Rohausgaben, keine Prosa allein
                                                       7  Votum in den Ledger
                                                          Tafel → ERLEDIGT / zurück
8  einordnen, nächsten  ◄───────────────────────────────
   Auftrag setzen
```

**Was nicht im Takt steht, passiert nicht.** Der Chat ist kein Übergabeweg — sechs Voten, die nur im
Chat liegen, sind nach `00-zyklus.md` nicht übergeben.

---

## 3. Die vier Regeln, die heute Geld gekostet haben

1. **Ziehen vor der ersten Zeile.** Wer den Posten nicht auf der Tafel gezogen hat, ist nicht dran.
   Das Ziehen ist ein eigener Commit, nur `AUFTRAGSTAFEL.md`.
2. **Commit immer mit Pfadangabe:** `git commit -m "…" -- <eigene pfade>`. **Nie `-A`, nie `.`**,
   `-m` **vor** dem `--`. Heute lag zeitweise ein fremder gebauter Bundle gestaged im Index — ein
   pauschales Commit hätte ihn mitgenommen.
3. **Abbruchregel:** fremde Änderung oder fremde untracked Datei **in meinen Pfaden** → nicht
   schreiben, melden, abbrechen. Fremde Arbeit in *anderen* Pfaden ist kein Abbruchgrund.
4. **Ein Posten, ein Strang.** Nie zwei Generatoren auf einer Datei — auch nicht „kurz".

---

## 4. Eine Ergänzung an der Tafel

Die Tafel bekommt neben `Rolle` eine Spalte **`Laufzeit`** mit `nativ` · `cowork` · `egal`.
Dann sieht jede Instanz **beim Ziehen**, ob der Posten überhaupt für sie ist — statt es nach dem
ersten Schreibversuch zu merken.

Faustregel für die Spalte: Posten, deren Abnahme `build:hausplaner` oder eine Sichtprobe braucht →
`nativ`. Posten, die nur `docs/` anfassen → `egal`. Sichtproben und Inventuren → `cowork`.

---

## 5. Was dieser Vorschlag **nicht** ändert

- **Die Rollentrennung bleibt unangetastet.** Planner ≠ Generator ≠ Evaluator, niemand nimmt eigene
  Arbeit ab, der Evaluator läuft in einer anderen Instanz. Daran wird nichts gelockert.
- **Die Spuren A/B bleiben.** Laufzeit und Spur sind zwei verschiedene Achsen.
- **Die zwei Tore bleiben bei Yama.** Fach-Freigabe und Merge/Deploy.
- **Der Ledger bleibt die eine Wahrheit.**

## 6. Offen an Yama

1. **Freigabe dieses Vorschlags** — er bindet erst dann.
2. **Soll die Spalte `Laufzeit` in die Tafel?** Sie kostet einmal Umbau und spart jedes Ziehen.
3. **Darf Cowork in Ausnahmefällen bauen** (wenn nativ nicht läuft), oder nie?

---

## 7. Planner-Sorgfaltspflicht (Yama, 25.07., bindend)

**Anlass:** Am 25.07. hat der Planner gegen seine eigene, im selben Repo committete Messung
geschrieben — er hatte vormittags belegt, dass neun Paket-IDs exakt die neun Registry-IDs treffen,
und nachmittags einen Auftrag verfasst, in dem stand, genau das könne nicht passieren. Der Generator
brach an dieser Stelle ab. Dazu: Arbeit im falschen Repo, ein Auftrag gegen einen drei Minuten alten
fremden Commit, eine doppelt begonnene Tabelle, und eine Regel, die den Generator leerlaufen ließ.

**Gemeinsames Muster: auf ein plausibles Bild hin handeln, statt vorher den Stand zu prüfen.**
Die Gegenmaßnahme ist keine Absichtserklärung, sondern eine Formvorschrift.

### 7.1 Jeder Planner-Auftrag und jede Planner-Entscheidung beginnt mit „Vorher gelesen"

Vor dem eigentlichen Text steht ein Block:

```
**Vorher gelesen:** HEAD <hash> · git log -5 · Tafelzeile <AUF-x> · <Datei:Zeile der geprüften Behauptung>
```

**Fehlt der Block, ist der Auftrag ungültig** — jede Rolle darf ihn ohne Diskussion zurückweisen.
Das ist dieselbe Regel, die für den Evaluator gilt („Artefakt statt Behauptung"), angewandt auf den
Planner.

### 7.2 Keine ungeprüfte Behauptung in einem Auftrag

Sätze der Form „das kann nicht passieren", „X existiert nicht", „Y ist eindeutig" stehen **nur** im
Auftrag, wenn sie **selbst gemessen** wurden — mit der Messung daneben. Was nicht geprüft werden
konnte, steht als **offene Frage**, nicht als Tatsache. Eine Kantenliste darf eine Kante beschreiben,
ohne ihr Eintreten auszuschließen.

### 7.3 Jede neue Regel wird einmal durchgespielt, bevor sie geschrieben wird

Ein Satz genügt: „Wenn diese Regel gestern gegolten hätte — was wäre passiert?"
Die AKTIV-Regel hätte den Generator bei jeder Abnahme angehalten. Das fällt in dreißig Sekunden auf.

### 7.4 Kein neues Dokument, das nicht ein bestehendes ersetzt

Jede zusätzliche Datei ist zusätzliche Fläche für Widersprüche — und sie kostet Yama den Überblick,
den der Planner ihm eigentlich verschaffen soll. Ergänzen schlägt Anlegen. Wird doch eine neue Datei
nötig, wird die abgelöste im selben Commit **stillgelegt**, nicht danebengelegt.

### 7.5 Antwortform

Befund · Entscheidung · nächster Schritt. Details nur auf Nachfrage.

---

### 7.6 Bedingungen werden nicht im Moment ausgelegt (Yama, 26.07., bindend)

**Wer eine Bedingung aufschreibt, erfüllt sie oder ändert sie — aber nicht, während er vor ihr
steht.**

Am 26.07. habe ich zwei selbst gesetzte Merge-Bedingungen **ausgelegt statt erfüllt**: den
`main`-Vergleich (durch Ersatzmessungen beantwortet) und „keine offene Auflage" (als
Werkzeug-Sache eingeordnet). **Beide Begründungen waren sachlich haltbar. Yama hat es trotzdem
strenger genommen, und er hat recht:**

**Eine Bedingung, deren Erfüllung derselbe beurteilt, der sie erfüllt sehen will, ist keine
Bedingung — sie ist eine Absichtserklärung.** Die Sachlichkeit der Begründung ändert daran nichts;
sie macht das Auslegen nur bequemer.

**Verbindlich:**

1. **Buchstäblich oder gar nicht.** Ist eine Bedingung nicht erfüllt, ist sie nicht erfüllt — auch
   wenn ihre Frage anders beantwortet wurde.
2. **Ändern nur vorher.** Wer eine Bedingung für falsch geschnitten hält, ändert sie **bevor** der
   Fall eintritt, mit Begründung im Ledger. **Im Moment selbst wird nichts geändert.**
3. **Zweifel gehen an Yama**, nicht an die eigene Gegenprobe. Die Gegenprobe *„hätte ich das
   Argument auch vorher akzeptiert?"* ist besser als nichts — **aber sie wird von dem gestellt, der
   sie bestehen will.**

---

## 8. Die Bundle-Regel (Planner, 25.07., nach dem zweiten Bundle-Loch)

**Anlass:** Der Evaluator hat ein Muster gemeldet, nicht einen Einzelfall. **Zweimal** wurde ein
Posten mit „sichtbar" als umgesetzt gemeldet, ohne dass das gebaute Bundle die Änderung enthielt —
Dashboard Batch 2 (behoben mit `6dde059`) und jetzt AUF-27 (`894954a`). Ein Votum „sichtbar", dessen
Sichtbarkeit nicht ausgeliefert ist, ist keine Freigabe, sondern eine Fehlmeldung.

### Warum nicht „Bundle im selben Commit"

Der naheliegende Vorschlag wäre, das Bundle in denselben Commit zu legen. **Einmal durchgespielt,
und er trägt nicht:** Das Bundle ist **1,3 MB minifiziert**. In jedem UI-Commit mitgeführt heißt das
bei zwei parallel arbeitenden Instanzen einen **garantierten Konflikt in einer Datei, die niemand von
Hand auflösen kann**. Und `bauplaner-3d` sagt ausdrücklich: *„Bundle-Artefakt — nie mergen, immer neu
bauen."* Der Vorschlag würde also genau die Regel brechen, die das Bundle schützt.

### Die Regel

**1. Rebuild als eigener Commit, direkt nach dem Code-Commit, vor der Meldung.**
Genau so, wie `6dde059` es beim ersten Mal gelöst hat. Zwei Commits, nicht einer: der Code bleibt
konfliktarm, das Artefakt bleibt getrennt.

**2. Der Bericht trägt den Beleg — drei Zeilen Rohausgabe:**
- `ls -l public/hausplaner/hausplaner.js` (Größe und Zeitstempel)
- **eine Zeichenkette aus dem neuen Slice**, per `grep -c` im Bundle nachgewiesen, mit Trefferzahl
- die Aussage, gegen welchen Quell-Commit gebaut wurde

Die Zeichenkette ist der eigentliche Beweis: Zeitstempel und Größe ändern sich auch bei einem Build
ohne die Änderung. **Ein Treffer > 0 auf einem Text, den es vorher nicht gab, kann nicht lügen.**

**2b. Die Kriterienzeile muss den Rebuild erlauben — sonst erzeugt sie das Loch selbst.**
*Nachtrag 25.07., nach dem dritten Bundle-Loch (AUF-34). Der Generator hat den Widerspruch gemeldet,
nicht überspielt — er gehört mir.* Meine eigenen Auftragsvorlagen führen das Kriterium **„null Zeilen
in `public/*` im Diff"** (K9 in AUF-27, K8 in AUF-34). Das war gegen versehentlich mitgeschleppte
Artefakte gedacht — es **verbietet aber wörtlich genau den Rebuild**, den Regel 1 verlangt. Ein
Generator, der beide Vorgaben befolgt, muss das Loch erzeugen. Dreimal ist es genau so gekommen.

**Die Kriterienzeile lautet ab sofort:**

> *„Der **Code-Commit** enthält null Zeilen in `public/*`. Der Bundle-Rebuild ist ein **eigener,
> zweiter Commit** unmittelbar danach und ausschließlich mit dem Artefakt — er ist nicht optional,
> sondern Teil der Lieferung (§8)."*

**Die Lehre daran ist nicht „besser aufpassen".** Ein Widerspruch zwischen zwei Vorgaben ist ein
Fehler in der Vorlage, nicht in der Disziplin dessen, der sie ausführt. Wer eine Regel schreibt,
spielt sie gegen die anderen Regeln durch, bevor er sie stellt — §7.3 sagt das, und ich habe es hier
selbst nicht getan.

**3. Kann nicht gebaut werden, wird das gemeldet, nicht übergangen.**
Auf aarch64 scheitert `build:hausplaner` an `@rollup/rollup-linux-arm64-gnu`. Dann lautet die Meldung
**„sichtbar — NICHT AUSGELIEFERT"**, und der Posten bleibt für die Sichtprobe gesperrt, bis jemand
nativ baut. Das ist ein zulässiges Ergebnis; es stillschweigend als erledigt zu führen ist es nicht.

**4. Evaluator-Seite: kein Grün für „sichtbar" ohne diesen Beleg.**
Fehlt er, lautet das Urteil **Freigabe mit Auflage** — Code grün, Auslieferung offen. Genau so hat
der Evaluator es bei AUF-27 gehandhabt, bevor diese Regel existierte. **Sie schreibt fest, was er
schon richtig gemacht hat.**

**5. Gilt nur für Posten mit `sichtbar`.** Ein `Vorarbeit`-Posten braucht keinen Rebuild — er ändert
für den Nutzer nichts, und ein Bundle-Commit ohne sichtbare Wirkung wäre nur Rauschen.

**Nicht als Gate im Testlauf**, sondern als Berichtspflicht: Ein Gate müsste bauen können, und genau
das kann die Umgebung nicht immer. Eine Pflicht, die in der Hälfte der Fälle nicht erfüllbar ist,
wird umgangen — und das Umgehen wird zur Gewohnheit.

---

## 9. Die Blade-Regel (Planner, 26.07., nach dem toten `objekt/203`)

**Was passiert ist:** AUF-60 hat eine `.blade.php` geändert. Die vier Hausplaner-Gates waren grün —
**alle vier**, mit 1008 von 1008 Tests. Trotzdem lag die Route, die Yama täglich benutzt, mit einem
PHP-ParseError am Boden. Gefunden hat es der Browser, nicht die Kette.

**Warum die Gates es nicht sehen konnten:** `tsc:hausplaner`, `schema:hausplaner:check`,
`test:hausplaner` und `build:hausplaner` prüfen TypeScript, Schema, die Insel-Tests und den
Bundle-Bau. **Keines davon fasst eine Blade-Datei an.** Die Abdeckung fehlte nicht — sie lag in der
PHP-Suite, und die wurde nicht gefahren. Der Generator hat das in seinem Bericht selbst benannt:
beim Mutations-Gegenbeweis fiel **auch der vorhandene `UebernahmeKnopfTest`** um.

### Die Regel

**1. Berührt ein Posten eine `.blade.php`, gehört `php artisan test tests/Feature/Hausplaner` in
seine Gate-Kette** — mit Zahl vorher/nachher im Bericht, wie bei den vier anderen Gates.

**2. Die betroffene Route gehört in die Sichtprobe** — nicht die, an der man gerade arbeitet.
AUF-60s Sichtprobe fand im Expertenmodus statt; die geänderte Datei bediente eine andere Route.

**3. Ist die Route hinter `auth` und der Zugang fehlt, wird der Beleg serverseitig geführt
und die Konsolenprüfung ausdrücklich als offen benannt.** Nicht stillschweigend übersprungen, und
nicht durch Anlegen eines Nutzers auf der Arbeitsdatenbank ersetzt — das wäre ein eigener Posten und
kein Test-Beifang.

**Warum das hier steht und nicht nur im Bericht:** Der Generator hat sich die Konsequenz selbst
aufgeschrieben. Eine Lehre, die nur in einem Bericht steht, gilt für den, der sie geschrieben hat,
und für niemanden sonst. **Diese hier hat den Hauptzweig einen Tag lang eine Route gekostet.**

---

## 10. Kollisionsschutz im geteilten Baum (Planner, 26.07., erledigt AUF-22)

**Was am 25.07. beinahe passiert ist:** Zwei Generator-Instanzen (nativ und Cowork) arbeiteten
gleichzeitig an derselben Nacharbeit. `HausplanerApp.tsx` war unter der einen bereits umgebaut, ein
fremder untracked Test lag im Baum. **Nichts ist überschrieben worden — aber nur, weil beide
freiwillig vorher auf der Tafel gezogen hatten** (`c3249d4`, `ca4153b`).

§1 der Tafel schreibt das Ziehen vor. **Durchgesetzt hat es nichts.** Eine Regel, deren Einhaltung
vom guten Willen abhängt, ist eine Bitte.

### Die Regel

**1. Ziehen ist Vorbedingung, nicht Höflichkeit.** Kein Generator schreibt die erste Zeile, bevor
der Posten auf der Tafel als ⚡ **AKTIV** auf ihn gezogen ist. **Steht dort ein anderer Posten
aktiv, wird nicht gebaut, sondern gefragt.**

**2. Vor dem ersten Schreibzugriff ein `git status`** — und **fremde untracked Dateien oder fremde
Änderungen sind ein Haltesignal, kein Hintergrundrauschen.** Wer sie sieht, meldet sie, statt
danebenzuschreiben. *(Genau dieser Blick hat heute Nacht gezeigt, dass der AUF-64-Fix uncommittet
im Baum lag — er kostet zehn Sekunden und hat schon einmal einen halben Tag gerettet.)*

**3. Wer merkt, dass der HEAD sich unter ihm bewegt hat, hört auf zu messen und meldet es.**
Messwerte aus einem wandernden Baum sind keine Messwerte. *(Mir selbst sind an einem Tag mehrere
Tafel-Skripte mitten im Lauf abgebrochen, weil nebenher committet wurde. In jedem Fall war Abbruch
richtig und Weitermachen falsch.)*

**4. Gestagt wird nur, was man selbst geschrieben hat** — nie `-A`, nie `.`, immer die eigenen
Pfade. Ein pauschales Commit sammelt die ungestagete Arbeit der anderen mit ein.

**5. Sperrdateien werden erwartet, nicht weggeräumt.** Drei Instanzen teilen eine Arbeitskopie;
`index.lock` bedeutet zunächst **„ein anderer schreibt gerade"**, nicht „kaputt". **Wer auf ein Lock
trifft: warten und erneut versuchen.** Erst wenn eine Sperrdatei **älter als zwei Minuten** ist und
kein git-Prozess läuft, gilt sie als verwaist — dann wird sie **verschoben** (`.git/_locks_beiseite/
<datum>/`), **nie gelöscht**.

*Gemessen an einem Tag: dreimal habe ich auf ein Lock getroffen; zweimal war es echt, einmal
verwaist. Hätte ich beim ersten Mal beiseitegeräumt, hätte ich einem laufenden Commit die Sperre
weggenommen.*

**6. Lesende Prüfungen erzeugen keine Sperren.** Jeder Abgleich, jede Repo-Aufsicht und jede
Messung läuft mit `git --no-optional-locks`. **Ein Beobachter, der den Beobachteten blockiert, ist
Teil des Problems.**

**7. Wer eine Marke wegnimmt, setzt im selben Schritt die nächste.** Die Tafel darf nie ohne ⚡
**AKTIV** dastehen, solange es einen ziehbaren Posten gibt. **Sonst hält Punkt 1 die Kette an — und
zwar genau so, wie er soll, nur ohne Grund.**

*Am 26.07. ist mir das passiert: AUF-40 Teil A ging von AKTIV in die Abnahme, und ich habe keine
neue Marke gesetzt. Fünf fertige Aufträge lagen 35 Minuten ohne Marke; der Generator hat korrekt
nicht gebaut, sondern gemeldet. **Die Regel hat funktioniert. Der Fehler war meiner.***

**Warum keine technische Sperre:** Eine Sperrdatei im Repo wäre selbst ein geteilter Zustand und
müsste aufgeräumt werden, wenn eine Instanz abstürzt — dann steht die Kette wegen des Schutzes still
statt wegen des Fehlers. **Die Tafel ist der Ort, an dem die Belegung ohnehin schon steht;** sie
braucht keine zweite Wahrheit daneben, sondern die Verbindlichkeit, sie zu lesen.

---

## 11. Die Sichtprobe wird im ungünstigsten Zustand gemessen (Planner, 26.07.)

**Was passiert ist:** Der AUF-72-Bericht meldete „Überstand 0" in drei Fenstergrößen. Der Evaluator
maß **konstant 18 px** — in denselben Größen. **Beide haben richtig gemessen.** Die Canvas-*Höhe*
stimmte auf den Pixel überein. Auseinander ging die **Oberkante**: 323 gegen 369.

**Der Unterschied war nicht das Fenster, sondern der Zustand der Oberfläche** — im gewöhnlichen
Arbeitszustand steht die Werkzeug-Optionen-Zeile und nimmt ~46 px, die im leichteren Zustand fehlten.

### Die Regel

**1. Gemessen wird im Zustand mit den **meisten** sichtbaren Leisten**, nicht im nächstbesten. Wer im
leichteren Zustand misst, bekommt eine Zahl, die schmeichelt — **und niemand merkt es, weil sie
stimmt.**

**2. Der Bericht nennt die Ausgangsgrößen, nicht nur das Ergebnis.** Für die Zeichenfläche:
**Oberkante zuerst**, dann Höhe, Fensterhöhe, Überstand. An der Oberkante gingen zwei richtige
Messungen auseinander; sie hätte den Unterschied sofort gezeigt.

**3. Vor jeder Sichtprobe wird geprüft, dass der ausgelieferte Stand der gemessene ist.** Der
Browser-Zwischenspeicher liefert stillschweigend die alte Datei. *Mir ist genau das bei AUF-70
passiert: Ich habe den alten Stand gemessen und hätte ihn beinahe freigegeben.*

**4. Der Zustand steht im Bericht**, nicht nur die Zahl — Ebene, Arbeitsbereich, gewähltes Werkzeug,
Fenstergröße. **Eine Zahl ohne ihren Zustand ist nicht nachprüfbar**, und genau das war hier der Fall.

**Das Rezept dazu** — welcher Bereich, welches Werkzeug, welche Formate — schreibt der Evaluator auf
(`evaluator-auftrag-sichtprobe-standard-2026-07-26.md`). **Eine Regel ohne Rezept ist ein guter
Vorsatz**; das Rezept hat, wer den Fall gefunden hat.

---

## 12. Was der Evaluator nicht darf (Planner, 26.07., auf Yamas Frage)

**Vorbemerkung, damit die Liste richtig gelesen wird:** Der Evaluator hat an einem Tag sechs eigene
Fehler offengelegt, die niemand bemerkt hätte — einen zu breiten `grep`, eine falsche
Ursachenanalyse, eine nicht isolierbare Messung. **Diese Grenzen sind nicht gegen ihn geschrieben,
sondern für die Rolle**, damit sie auch dann trägt, wenn sie jemand anderes ausfüllt.

**1. Er repariert nicht.** Findet er einen Mangel, meldet er ihn. **Wer misst und dann baut, nimmt
seine eigene Arbeit ab** — und ab dem Moment ist sein Urteil über alles Weitere wertlos. Das gilt
auch für „nur eine Zeile".

**2. Er erfindet keine Posten.** Ein Befund ist ein Befund; ob daraus ein Auftrag wird, entscheidet
der Planner. Sonst wächst der Vorrat aus der Prüfung heraus, und niemand hat es je entschieden.

**3. Er weitet den Prüfumfang nicht aus, um etwas zu finden.** Geprüft wird gegen den **Auftrag**.
Fällt ihm daneben etwas auf, gehört es in einen eigenen Absatz „Nebenbefund", nicht in das Votum.

**4. Er lässt keine Sichtprobe aus und gibt trotzdem frei.** Bei `sichtbar` ist die Sichtprobe Teil
der Abnahme. **Eine vertagte Sichtprobe ist eine offene Abnahme** — das hat er selbst so formuliert.

**5. Er meldet keine Zahl ohne ihren Zustand** (§11) und **keine Behauptung ohne Rohbeleg.**
„Testsuite selbst gefahren, grün" ist von „Bericht behauptet grün" nicht unterscheidbar, wenn nur
der Satz ankommt.

**6. Er haftet den Generator nicht für Planner-Fehler.** Steht ein Kriterium auf einer falschen
Annahme, ist das ein Planner-Fehler und **zählt nicht gegen den Bau**. Er sagt es, statt es zu
glätten.

**7. Er ändert, löscht und schwächt keinen Test**, auch nicht „nur zum Messen". Für Gegenbeweise
arbeitet er auf einer Kopie — nie im Arbeitsbaum.

**8. Er schreibt nicht in fremde Pfade.** Sein Ort sind `docs/abnahme-…` und der Ledger. Kein
`resources/`, kein `app/`, kein `routes/`.

**9. Er legt keine Daten an, um prüfen zu können.** Kein Nutzer, kein Objekt, kein Datensatz auf der
Arbeitsdatenbank. **Fehlt ihm ein Zugang, ist das ein Befund für Yama, kein Test-Beifang.**

**10. Er pusht nicht, merged nicht nach `main`, deployt nicht.** Tor 2 gehört Yama.

**11. Er nimmt nichts ab, was er selbst beauftragt oder mitentworfen hat.** Wenn eine Rückgabe von
ihm zu einem Posten wurde, prüft er den Posten — aber er entscheidet nicht, ob seine eigene Rückgabe
richtig war.

**12. Er eilt nicht wegen der Stapelhöhe.** Vier im Stapel heißt vier Urteile, nicht ein Sammelvotum.
**Ein schnelles Votum ist wertlos.**

---

## 13. Einspurbetrieb — Yamas Entscheidung vom 26.07., bindend

**Wortlaut:** *„ich glaube die Gefahr ist zu groß, wir bleiben dabei wie es ist."*

Die Untersuchung `docs/planner/parallelbetrieb-2026-07-26.md` hatte drei Spuren für machbar
gehalten. **Yama hat anders entschieden, und die Entscheidung gilt.** Sie bleibt trotzdem im
Bestand, weil sie die Begründung trägt — und weil eine verworfene Untersuchung, die man später
noch einmal führen muss, doppelt bezahlt wird.

**Was jetzt gilt:**

1. **Ein bauender Posten zur Zeit. Ein Arbeitsbaum. Ein Zweig.** Kein zweiter `git worktree`,
   keine zweite Test-Datenbank, keine zweite ausgelieferte Anwendung. §1c (**genau eine** Marke
   `⚡ AKTIV`) bleibt unverändert in Kraft und ist damit nicht nur eine Fokus-Regel, sondern die
   Betriebsregel.
2. **Der Ausgleich für die fehlende Parallelität ist die Staffel, nicht die Gleichzeitigkeit.**
   Generator und Evaluator bekommen ihre Reihenfolge **im Voraus** — beide wissen ohne Rückfrage,
   was nach dem aktuellen Posten kommt. **Der Planner ist damit kein Nadelöhr mehr zwischen zwei
   Posten**, und genau dieses Nadelöhr hat am 26.07. fünf fertige Aufträge 35 Minuten liegen
   lassen.
3. **Leerlauf wird gemeldet, nicht überbrückt.** Wer seine Staffel abgearbeitet hat und nichts
   Neues vorfindet, meldet **„Staffel leer"** in den Ledger und wartet. Er zieht sich **nichts**
   aus dem Vorrat, was nicht in seiner Staffel steht — das wäre der Themenwechsel, den §1c
   verhindern soll, nur mit besserer Begründung.
4. **Der Evaluator misst nicht, während der Generator schreibt.** Das war schon §10.3; unter
   Einspurbetrieb ist es die tragende Regel und keine Vorsichtsmaßnahme. Der Evaluator prüft
   **gegen einen benannten Commit**, nicht gegen den Arbeitsbaum — dann ist es gleichgültig, ob
   der Generator inzwischen weitergeschrieben hat.
5. **Der einzige zulässige Parallelfall bleibt: einer baut, einer nimmt einen *anderen,
   bereits committeten* Posten ab.** Das ist keine Ausnahme von Punkt 1, denn abnehmen ist kein
   Bauen — es erzeugt keine Änderung im Baum.

### 13.6 Nachtrag vom 26.07., 16:30 — eine Sichtprobe ist **kein** reines Abnehmen

**§13.5 war zu weit gefasst, und der Fehler ist meiner.** Dort steht: *der einzige zulässige
Parallelfall bleibt — einer baut, einer nimmt einen anderen, bereits committeten Posten ab; denn
abnehmen ist kein Bauen, es erzeugt keine Änderung im Baum.* **Das stimmt für eine Abnahme am
Quelltext. Für eine Sichtprobe stimmt es nicht.**

Eine Sichtprobe misst die **ausgelieferte** Anwendung. Die kommt aus `public/`, und `public/` wird
vom Bauenden **neu gebaut**. Damit hängt sie an Engpass 3 aus
`docs/planner/parallelbetrieb-2026-07-26.md` — *eine ausgelieferte Anwendung, also ein Posten mit
Sichtprobe zur Zeit*. **Ich hatte den Engpass gemessen und ihn drei Absätze später selbst wieder
aufgemacht.**

Eingetreten am 26.07., 16:28: der Evaluator wollte den Worst-Case-Überstand gegen `f9c837e` messen
und fand `public/hausplaner/hausplaner.js` **abweichend** — der Generator hatte für AUF-78 neu
gebaut, uncommittet, HEAD unverändert. Die servierte App war der **AUF-78-WIP**. Er hat die Messung
**abgebrochen und begründet vertagt**, statt eine Zahl zu liefern, die als `f9c837e` etikettiert
gewesen wäre und es nicht war.

**Also, verbindlich:**

1. **Abnahme am Quelltext** (Diff, Tests, `grep`, Mutation) — läuft parallel zum Bauen, wie in
   §13.5, solange gegen einen **benannten Commit** gemessen wird.
2. **Sichtprobe** — **nicht** parallel zum Bauen. Sie verlangt, dass `public/` zu einem benannten
   Commit gehört. **Vor jeder Sichtprobe: `git status` auf `public/*`. Ist dort etwas offen, wird
   nicht gemessen, sondern gemeldet.**
3. **Wer eine Sichtprobe vertagt, weil `serviert != gemessen`, hat richtig gehandelt** — auch wenn
   am Ende keine Zahl herauskommt. **Eine falsch etikettierte Zahl ist schlimmer als keine**, weil
   sie später als Grundlinie zitiert wird.
