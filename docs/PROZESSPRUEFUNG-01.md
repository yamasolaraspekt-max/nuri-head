# Prozessprüfung 01 — ausgelöst bei Auftrag 3, nicht bei Auftrag 10

**Autorität:** [`docs/ARBEITSREGELN.md`](ARBEITSREGELN.md) §13. Der Zähler steht in
[`AUFTRAGSZAEHLER.md`](AUFTRAGSZAEHLER.md).

**Geschrieben:** 05.08.2026, 00:5x · Planner

---

## Warum jetzt und nicht bei zehn

§13 kennt einen Sofort-Auslöser neben der Zehnergruppe: *„eine übersehene Daten- oder
Sicherheitskante"*. Sie ist eingetreten — in **meinem** Auftrag A-01.

**Ich löse die Prüfung selbst aus, bevor jemand sie einfordert.** *Eine Regel, die nur greift,
wenn ein anderer sie zieht, ist eine Bitte.*

---

## Der Befund — und er ist feiner, als er zuerst aussieht

**Falsch wäre:** *„A-01 hat vergessen, die Testdatenbank zu nennen."* **Das stimmt nicht.**
Gemessen im Blatt:

```text
Zeile 140   TESTEBENE     "Laeuft gegen ticket_testing (§15)"            benannt
Zeile 312   BROWSERPROBE  "dasselbe Seed-Muster einmal gegen             benannt
                           ticket_testing ausfuehren"
Zeile 142   BROWSEREBENE  "Das Dokument wird ueber die Oberflaeche       KEINE Angabe
                           erzeugt"                                       zum SERVER
```

**Der Auftrag nannte die Datenbank dreimal — und traf trotzdem daneben.**

> ### Es sind ZWEI Angaben, und sie sehen aus wie eine
>
> ```text
> WO DIE DATEN LIEGEN     wohin geseedet wird          A-01 sagte es
> WOGEGEN DER SERVER LÄUFT welche DB der Prozess sieht  A-01 sagte es NICHT
> ```
>
> **Ein Blatt, das nur die erste macht, wirkt vollständig.** Es nennt einen Datenbanknamen,
> der Leser hakt „Testdatenbank benannt" ab, und die zweite Frage wird nie gestellt.

**Genau so ist es gelaufen:** Der Generator hat korrekt nach `ticket_testing` geseedet — Objekt
903, revision 1 lagen dort. Der Browser wurde aus `ticket` bedient. **Zwei Datenbanken in einem
Vorgang**, und keine Zeile im Blatt, an der das auffallen musste.

---

## Wer es hätte finden müssen

| Rolle | hatte die Gelegenheit | gefunden? |
|---|---|---|
| **Planner** (ich) | Ich habe den Fixture-Weg geschnitten und die Ebenen getrennt | **nein** |
| **Plan-Prüfer** | Er schrieb den Satz zur Browserprobe selbst — und ebenso unvollständig | **nein** |
| **Generator** | beim Bauen, an der laufenden Bühne | **ja** |

**Das ist kein Versagen des Plan-Prüfers.** Er hat gegen die Frage geprüft, die im Raum stand
(*„ist eine Testdatenbank benannt?"*) — und die war mit Ja zu beantworten. **Eine Prüfung findet
nicht, wonach niemand fragt.** Deshalb ist die Antwort ein neuer Prüfpunkt und keine Ermahnung.

*Bemerkenswert bleibt: gefunden hat es die Rolle, die am wenigsten Zeit zum Nachdenken hat und
den Fehler selbst gemacht hätte — weil sie als einzige die Sache **laufen** ließ.*

---

## ✅ ERLEDIGT — Yama hat beauftragt, die Lücken als Regelwerk zu schreiben (05.08.)

**Aus dem Vorschlag ist Fassung 1.1 geworden.** Nach §1 Rangfolge 1 steht Yamas ausdrückliche
Anweisung über dem Dokument — damit war die Änderung gedeckt. **Sie steht IM Regelwerk, nicht
daneben:** ein zweites Regeldokument wäre genau die zweite Wahrheit, die §1 abgeschafft hat.

```text
§3      IN_ARBEIT wird vor der ersten Scope-Aenderung gesetzt
§5      Testdaten-Ziel UND Prozessbindung getrennt benennen, mit beweisendem Befehl
§5      vorgeschriebene Formen muessen vorhanden UND in Gebrauch sein - beides gemessen
§5      Kriterium oder Nicht-Ziel, kein dritter Zustand
§7      der Auftrag steht auf IN_ARBEIT · kein Kommentar ueber nicht vorhandenes Verhalten
```

**§5 ist von 15 auf 18 Punkte gewachsen**, §19 Änderungsverzeichnis führt jede Regel auf den
Vorfall zurück, der sie erzwungen hat. **Fassung 1.0 bleibt unverändert gültig — 1.1 ergänzt,
streicht nichts.**

*Aus zwei der vier Regeln ist inzwischen mehr geworden als aus diesem Bericht: die `php -S`-Lücke
und die widersprüchliche „OHNE ZUSAGE"-Formulierung sind erst NACH diesem Text aufgetreten. Der
Bericht hat den Anlass geliefert, nicht den vollen Inhalt.*

---

## Der ursprüngliche Vorschlag — zur Nachvollziehbarkeit erhalten

**Ich ändere `ARBEITSREGELN.md` nicht selbst.** Nach §1 steht das Dokument über mir; ein Planner,
der die Regeln nachschärft, an denen er gemessen wird, ist der Interessenkonflikt, den die
Rollentrennung verhindern soll. **Deshalb als Vorschlag, nicht als Änderung:**

> **Neuer §5-Punkt:** *Führt der Auftrag eine Oberfläche aus, nennt er getrennt: **wohin die
> Testdaten gehen** und **wogegen der ausführende Prozess läuft** — mit dem Befehl, der Letzteres
> beweist. Eine Angabe allein erfüllt den Punkt nicht.*

**Warum ein eigener Punkt und nicht ein Satz in einem bestehenden:** §5 wird als Liste abgehakt.
Was kein eigener Punkt ist, wird beim Abhaken nicht gefragt — **und genau das ist der Fehler,
den dieser Punkt behebt.**

---

## Was ohne Yamas Entscheidung schon wirkt

- **A-03** baut den Riegel (`BEREIT`, beim Generator).
- **ANKER-BROWSER** trägt die Regel seit 05.08. samt Messung und dem Vermerk, dass sie bis A-03
  eine Papierregel ist.
- **Ich** nehme beide Angaben ab sofort in jeden Auftrag mit Browseranteil auf. *Das ist die
  einzige der drei Maßnahmen, die von niemandes Zustimmung abhängt — und die schwächste, weil
  sie mit mir steht und fällt.*

---

## Kennzahlen der Gruppe 1 zum Zeitpunkt der Prüfung

**Zählregel, damit die Zahlen etwas heißen:** ein *Spezifikationsfehler* ist eine Aussage im Blatt,
die **falsch oder unvollständig war und vor `BEREIT` korrigiert werden musste.** Ein Punkt, den ich
ausdrücklich als offen gekennzeichnet habe, ist **kein** Fehler — er ist eine erklärte Lücke, und
genau dafür gibt es den Abschnitt.

```text
Auftraege vorgelegt                 3   (A-01, A-02, A-03)
beim ersten Plan-Review BEREIT      0 von 3

Spezifikationsfehler gesamt         8
  vom Plan-Pruefer gefunden         5   A-01: Doppelfuehrung Z-07 · zwei Rechtecks-Begriffe ·
                                        drei Pruefbefehle auf falschem Runner
                                        A-02: A-02-1 als P1 gefuehrt, war an der Basis gruen
                                        A-03: Verankerung in ANKER-BROWSER fehlte
  vom Planner selbst gefunden       2   A-03: "Muster" statt "genau ein Name" · A-03-6 ohne
                                        Pruefbefehl  (beide NACH dem Plan-Review, vor dem Bau)
  vom Generator gefunden            1   die Datenkante dieses Berichts - an der LAUFENDEN Buehne

ausdruecklich offene Punkte         4   (KEINE Fehler: ENV_BLOCKED-Form · A-02-1-Verdacht ·
                                        Testnamen-Liste · P2-Frage an den Pruefer)
Sofort-Ausloeser §13                1   (dieser)
```

> **Die drittletzte Datenzeile ist die, die zählt.** Sieben Planungsfehler wurden **am Papier**
> gefunden — fünf vom Prüfer, zwei von mir. **Einer ist durch beide Siebe gefallen und wurde erst
> an der laufenden Bühne sichtbar.**
>
> **Papier findet Papierfehler.** Diese Kante war keiner: sie entstand erst, als ein Prozess und
> eine Datenbank gleichzeitig existierten. *Kein noch so guter Leser hätte sie im Text gesehen —
> im Text stand dreimal der richtige Datenbankname.*
