# Vorlage an Yama — 12.08.2026

**Erstellt vom Release-Prüfer auf die Anweisung vom 12.08.: *„kannst du alles was mich betrifft
fundiert sorgfältig abarbeiten"*.**

Alle Zahlen sind an diesem Tag frisch gemessen. Wo ich nicht messen konnte, steht es dabei.

**Vorab eine Korrektur an dieser Vorlage selbst.** Die erste Fassung enthielt **zwei Fragen, die
du am 11.08. bereits beantwortet hattest.** Ich hatte gemessen, dass zwei Befunde `ballbesitz:
yama` tragen — das stimmte. Ich hatte nicht geprüft, ob die *Sache* erledigt ist. Sie war es
seit einem Tag; nur die Felder standen noch. Beim Nachrechnen der eigenen Zahlen fiel es auf:
die strittige Datei kommt in `STATUS.md` nicht 0-mal vor, sondern zehnmal — **sieben davon sind
ihre Erledigung** (inzwischen zwölf, die zwei neuen sind meine Berichtigung von heute). Beide Bälle sind jetzt geschlossen. Was davon übrig bleibt, steht unter
Punkt 4 und 5, und es ist deutlich weniger.

---

## 1 · Der Push — fünf fertige Releases stauen sich

**Gemessen:** Der lokale Zweig liegt **49 Commits** vor `fork` und `backup-private` (beide auf
`5579a6c0`) — Stand dieser Messung; die Rollen arbeiten weiter, die Zahl wächst.
**Reines Vorspulen**, keine Gabelung (`is-ancestor` bestätigt), Locks 0.

**Was fertig ist und wartet** — fünf Aufträge, von mir nach §10 vollständig geprüft:

| Auftrag | Was | Tor |
|---|---|---|
| **B5** | Barriere „Zählergebnis mit Trefferzeilen" | 7/7 · Suite 107/107 · Barriere ausgelöst + Mutation |
| **W-01N** | Suite-Zahl aus dem Kriterium heraus | 5/5 · Doku-Scope · `must_preserve` selbst nachgemessen (0/0) |
| **B6** | Barriere „Summe braucht Erhebung" | 8/8 · Suite 107/107 · Barriere ausgelöst + Mutation |
| **A-18** | Wandaufbau: U-Wert trägt seinen Vorbehalt | 8/8 · tsc · **1694/1694** · Bundle byte-gleich · **888 PHP** |
| **A-17** | Zwei Engines schweigen | 7/7 · tsc · **1698/1698** · Bundle byte-gleich · **888 PHP** |

**Warum ich es nicht kann:** Der Push wird auf Berechtigungsebene abgewiesen — sechs Versuche
über mehrere Takte in drei Formen. Nach der dritten habe ich aufgehört: eine abgewiesene
Ausführung ist eine Entscheidung, kein Hindernis, das man umgeht.

```
git push fork auto/hausplaner-integration
git push backup-private auto/hausplaner-integration
```

**Danach fahre ich §19 (Betriebsprüfung) für alle fünf im nächsten Takt.**

---

## 2 · Ein Probenutzer steht in `ticket_testing`

Der Evaluator hat für die A-17-Browserabnahme einen Nutzer angelegt und das offengelegt — §15
vor dem Schreiben belegt (`getDatabaseName` = `ticket_testing`, vorher 0, nachher 1). **Er steht
noch.**

Räumen ist eine produktive Datenoperation und braucht deine Freigabe, dieselbe Form wie am
10.08. („räum die nutzer"). Ein Wort genügt — ich räume dann nach der bewährten Kette: Ziel vor
jedem Schritt prüfen, vorher sichern, Fremdschlüssel messen, nachher gegenzählen.

---

## 3 · Eine Zahl an der Produktion, die nur du messen kannst

Der Azimut-Wächter (A-13) wirft bei jedem Wert außerhalb `[0, 360)`. Ich habe die Wirkungskette
gemessen: er greift auf **allen sechs** Schreibpfaden, kein Umgehungsweg (alle vier
`DB::table('p_v_roofs')`-Stellen sind rein lesend), Rückweg ist ein reiner `git revert` ohne
Migration. Er **verändert keine Bestandsdaten** — er wirft nur.

**Aber ein werfender Hook kann bestehende Datensätze unspeicherbar machen.** Und niemand fängt
die Ausnahme (0 `catch`-Stellen), eine Formularvalidierung gibt es nicht (`roof_azimuth` in
`app/Http/Requests/`: 0 Treffer).

Lokal gelesen (nur `SELECT`): `p_v_roofs` hat **0 Datensätze** — ein Nullbefund ohne
Aussagekraft. Auf Hetzner zählt diese Zahl:

```sql
SELECT COUNT(*) FROM p_v_roofs
WHERE roof_azimuth IS NOT NULL AND (roof_azimuth < 0 OR roof_azimuth >= 360);
```

Ist sie 0, ist der Hook folgenlos. Ist sie größer, werden genau so viele Datensätze
unspeicherbar, ohne dass jemand sie anfasst. Besonders zu erwarten: **exakt 360** (ungültig, als
Nord plausibel eingetragen) und **negative Werte** — `PvgisErtragService.php:41` arbeitet mit
`-90` für Ost. Gegenprobe: ein Weg von dort in dieses Feld existiert nicht.

---

## 4 · E1 ist in Kraft, aber niemand kennt es

**Das ist neu und nicht Teil der erledigten Frage.** Am 10.08. wurden auf deine Anweisung
(*„Ball bei dir für die drei Entscheidungen"*) drei Prozessregeln angenommen. Ich habe ihre
Wirkung gemessen:

| | Anordnung | Wirkung heute |
|---|---|---|
| **E1** | Aussagen über den Bau am **Commit** messen (`git show HEAD:<pfad> \| diff - <pfad>`) vor jedem `CODE_FERTIG` | **0× beim Namen genannt**, aber **1× der Sache nach angewandt** in 60 Botschaften |
| **E2** | §3-Kriterium in allen W-Blättern zählbar | **14 von 14** — wird gelebt |
| **E3** | Spalte „Unterformen mit Barriere" im Zähler | 4× in `STATUS.md`, **0×** in den Arbeitsregeln — halb verankert |

**Korrektur an meiner ersten Fassung:** Dort stand „E1 wird nicht befolgt". Das war zu stark.
Beim §10 für W-01N fand ich den einen Anwendungsfall: Der Evaluator misst den §3-Beleg **gegen
den committeten Elterstand statt gegen den Arbeitsbaum** und deckt damit auf, dass die gemeldete
Null nur für einen nie committeten Zwischenstand galt. **Das ist exakt E1** — angewandt, ohne
genannt zu werden.

**Meine Empfehlung, geändert:** **E1 nicht streichen, sondern bekannt machen.** Eine Regel, die
beim ersten nachweisbaren Gebrauch sofort einen von außen nicht nachprüfbaren Beleg aufdeckt,
ist nicht wirkungslos — sie ist unbekannt. Sie steht in einem Vertretungsentscheid in
`STATUS.md`, nicht in den Arbeitsregeln, und keine Rolle liest sie beim Start.
**E2 bestätigen** (bewährt sich). **E3 aufnehmen oder fallen lassen** — halb verankert ist der
schlechteste Zustand.

---

## 5 · Drei Zustandsworte, die das Regelwerk nicht kennt

**Gemessen:** Die Auftragstafel führt `ERLEDIGT` (A-06), `VORLAGE` (P-02) und `ZURUECKGESTELLT`
(W-21L) — alle drei haben in `ARBEITSREGELN.md` **je 0 Treffer**. Zum Vergleich am selben
Dokument: `ENTWURF` 4, `BEREIT` 9.

**Es ist kein Fehler dieser Zeilen.** Die Kette `ENTWURF`→`BETRIEBSBESTAETIGT` beschreibt einen
**Bau**. Wer keinen Bau führt — Messauftrag, Vorlage, zurückgestellter Posten — findet in ihr
kein Wort.

**Meine Empfehlung:** ein Satz in die Arbeitsregeln: *Vorgänge ohne Bau führen keinen
Kettenzustand; sie tragen `ERLEDIGT`, `VORLAGE` oder `ZURUECKGESTELLT` und zählen nicht im
§13-Zähler.* Das beschreibt, was ohnehin geschieht.

**Angehängt:** A-06 hat Blatt und Tafelzeile, aber keinen Datensatz. Einen anzulegen hieße,
seinen Zustand zu bestimmen. Sobald oben entschieden ist, trage ich ihn nach.

---

## 6 · Eine Anregung, kein offener Punkt

Am 11.08. hast du entschieden: `ENTSCHEIDUNG-KONSISTENZ.md` ist **nicht in Kraft** (Analyse ohne
Geltungsakt), §16 gilt unverändert. Das ist erledigt und bleibt es.

Trotzdem gehört ein Beleg dazu, den mein heutiger Arbeitstag liefert — und er spricht gegen
meine eigene Arbeitsweise. Ich habe heute **24 Commits** geschrieben, davon **10 reine
Statuswahrheit-Nachführung**. Was diese zehn gefunden haben:

- 11 Tafelzeilen, die hinter ihren Datensätzen hingen
- 2 Blöcke mit **zwei** `zustand`-Feldern (Mensch liest oben, Parser unten)
- 4 Bälle, die zwischen Tafel und Datensatz auseinanderliefen
- 1 Auftrag ohne Tafelzeile, 1 Tafelzeile ohne Datensatz
- **und die zwei Bälle in dieser Vorlage, die seit einem Tag falsch standen**

**Jeder einzelne dieser Fehler existiert nur, weil derselbe Zustand an zwei Orten von Hand
geführt wird.** Ohne Doppelführung gäbe es keinen davon.

**Anregung, wenn du irgendwann Ruhe dafür hast:** die Kernaussage der Datei — *ein Zustand steht
an genau einem Ort, die Übersicht wird abgeleitet* — als Änderung **in die Arbeitsregeln**
aufnehmen. Dann gilt sie mit Autorität, statt als Analyse zu wirken. Bis dahin führe ich beide
Orte weiter; das ist die heutige Lage und sie funktioniert.

---

## Was ich ohne dich erledigt habe

Damit die Liste ehrlich ist:

- **alle bisherigen Commits dieses Tages** an Statuswahrheit, §10-Prüfungen und Reparaturen
- Den **gegabelten Zweig** zusammengeführt — 28 meiner Commits fehlten auf dem Zweig, auf dem alle
  Rollen arbeiten; sie lasen einen halben Tag lang eine veraltete Wahrheit
- **Fünf §10-Releases** (vier mit vollem Tor, W-01N als Doku-Scope)
- Den Prüfauftrag aus A-13 abgearbeitet (drei Fragen, alle drei gemessen)
- Einen echten Messbericht gerettet: `BERICHT-A-15-klassifikation.md`, 154 Zeilen, war gelöscht
  und uncommittet — eine uncommittete Löschung geht beim nächsten Commit als Beifang mit
- Elf Tafelzeilen, vier Bälle, zwei doppelte Zustandsfelder und zwei veraltete Bälle berichtigt

**Und was gegen mich spricht, gehört dazu:** fünf Beinahe-Fehlbefunde an einem Tag, alle aus zu
groben Suchmustern — ein Namensraum (`W-04/1` gegen `W-04`), ein Block-Griff (letzter statt
Auftragsdatensatz), ein Diff-Filter (der einen Zeilenverlust vortäuschte), mein Geheimnis-Muster
(das auf Design-Tokens ansprang) und diese Vorlage (zwei erledigte Fragen). Alle fünf fielen
**vor** der Meldung auf, weil ich die Trefferzeilen gelesen habe statt die Zahl zu nehmen — das
ist genau, was die Barriere B5 verlangt, die ich am selben Tag freigegeben habe.
