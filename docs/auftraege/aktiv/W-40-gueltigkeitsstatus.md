# W-40 — Gültigkeitsstatus. Die zweite Achse, ohne die „erst nach Bestätigung" nicht prüfbar ist

```yaml
auftrag: "W-40"
werkzeug: "W-40 Gültigkeitsstatus — confirmed · outdated · blocked"
art: "STUFE 6 — Blatt schneiden, Ziel ENTWORFEN (VORGABE). Es gibt KEINEN Code: die drei Stufen
      fehlen im Bestand. Die Blätter geben vor, was gebaut werden soll — wie W-15, W-23, W-27."
spur: A
heimat_app: ticket
dor_beleg: "steht aus — plan-pruefer."
status_steht_in: docs/STATUS.md
basis_sha: c9ac316d
prioritaet: P1
anlass: "Yamas Freigabe 12.08.: 'ich sage zu diesen sachen ja W-40, W-41, W-42 warten auf dich —
         das Angebot des Planners, sie als Vorgabe mit Ziel ENTWORFEN zu schneiden.' Vorgelegt
         hatte ich sie in Abschnitt 7 der Vorlage, nach der Abnahme von W-34."
ballbesitz: "plan-pruefer (DoR)"
claim: "planner 12.08. — Claim VOR dem Schnitt."
grundlage: "docs/BERICHT-PROZESSEBENE-DREI-FRAGEN.md (194 Z.) als Quelle · Yamas Zielbild 3.6 ·
            W-38 (BETRIEBSBESTAETIGT) als vorhandene Fortschrittsachse"
```

## 1 — Der Operand ist lesbar und liegt im Repo (Pflichtprüfung 5)

```text
docs/BERICHT-PROZESSEBENE-DREI-FRAGEN.md   194 Zeilen, Markdown, direkt lesbar
  Z.115-124   die Gegenüberstellung Zielbild 3.6 gegen den Bestand
  Z.127-133   die Einordnung als ZWEI ACHSEN
  Z.179       die drei Stufen als Posten benannt
  Z.190       „Die drei fehlenden (confirmed/outdated/blocked) sind die Gueltigkeitsachse"
```

**Der Bericht sagt ausdrücklich: „Dieser Bericht liefert Zahlen, keine Empfehlung zur Einordnung —
die Entscheidung gehört Yama."** *Sie ist am 12.08. gefallen, und dieser Auftrag ist ihre Umsetzung.*

## 2 — Der tragende Punkt: zwei Achsen, nicht eine längere Liste

**Wörtlich aus der Quelle (`:131-133`):**

```text
Die vier vorhandenen Stufen beschreiben FORTSCHRITT;
die drei fehlenden beschreiben GUELTIGKEIT.
Das sind zwei Achsen, nicht eine laengere Liste.
```

> **Damit ist eine Frage beantwortet, die ich in W-38s Blatt als offenen Anschluss hinterlassen
> habe.** *Dort steht, `confirmed`/`outdated`/`blocked` seien nicht W-38s vier Stufen
> (`ok`/`prog`/`warn`/`open`) und es stehe die Frage im Raum, ob W-40 **ein zweites Statussystem**
> einführt — was der Wächter „keine verwaisten zweiten Wahrheiten" verbieten würde. **Die Quelle
> löst das auf: es sind zwei ACHSEN und keine zwei Wahrheiten.** Ein Schritt kann `ok` sein
> (gerechnet, fertig) und trotzdem nicht `confirmed` (vom Nutzer bestätigt). Das Blatt muss diese
> Unterscheidung tragen, sonst baut jemand die drei Stufen in `SchrittStatus` hinein und erzeugt
> genau die zweite Wahrheit, die hier nicht vorliegt.*

## 3 — Was jede der drei Stufen leistet (aus der Quelle, nicht erfunden)

```text
confirmed   trennt „gerechnet" von „vom Nutzer BESTAETIGT".
            Ohne sie ist L-9 nicht pruefbar: PV erst nach BESTAETIGTER Dachgeometrie.
            Das ist die fachlich schwerste der drei — sie entscheidet, ob eine
            PV-Belegung ueberhaupt starten darf.

outdated    ist die INVALIDIERUNG, also der Kern von
            „Aenderungen propagieren, NIEMALS stille Loeschung".
            Sie ist der Anschluss an W-41 (Abhaengigkeitsgraph).

blocked     ist die SPERRE.
```

**Die Quelle beziffert `blocked` nicht weiter** — *das ist eine Lücke der Vorgabe und keine des
Blattes. Sie gehört in `7-GRENZEN`: **was `blocked` von `DECISION_BLOCKED` im Prozess
unterscheidet, ist nicht belegt** und muss beim Bau von Yama kommen, nicht vom Bauenden.*

## 4 — Eine Zahlenlücke in der Quelle, die ich nicht glätte

```text
Zielbild 3.6      ACHT Stufen
vorhanden         VIER   (open · prog · warn · ok)
fehlend genannt   DREI   (confirmed · outdated · blocked)
                  4 + 3 = 7,  nicht 8.
```

**Die achte ist `review-required`** — in der Tabelle der Quelle mit `—` markiert, nicht mit
„**fehlt**", und in der Einordnung nicht mitgezählt.

> **Ich glätte das nicht und ich erfinde auch keine Erklärung.** *Entweder ist `review-required`
> bewusst nicht Teil der Gültigkeitsachse, oder die Zahl DREI in der Quelle ist zu niedrig. **Das
> Blatt muss die Frage stellen, nicht beantworten** — und es muss verhindern, dass jemand „drei
> Stufen" baut und die achte stillschweigend verliert. Genau diese Sorte Zahlenlücke hat mich heute
> mehrfach eingeholt: 4 + 3 = 7 ist der Hinweis, dass eine Angabe fehlt.*

## 5 — Scope

```text
W-40 IST      die VORGABE der Gueltigkeitsachse: welche Stufen es gibt, was jede
              bedeutet, wie sie sich zur Fortschrittsachse aus W-38 verhaelt, und
              welche Uebergaenge zulaessig sind.

W-40 IST NICHT
              der BAU. Kein Produktivcode, keine Aenderung an studioDaten.ts —
              W-38 ist BETRIEBSBESTAETIGT und bleibt unberuehrt.
              NICHT die Invalidierungs-MECHANIK: dass outdated propagiert, gehoert
              zu W-41. W-40 sagt nur, DASS es den Zustand gibt und was er bedeutet.
              NICHT die PV-Belegung selbst (W-31) — W-40 liefert die Bedingung,
              nicht die Auswertung.
```

## 6 — Abnahmekriterien

```text
W-40-1  (P1, TRAGEND) 1-ZWECK nennt die ZWEI ACHSEN mit dem Zitat aus der Quelle:
        die vorhandenen Stufen beschreiben Fortschritt, die neuen Gueltigkeit, und es
        sind zwei Achsen und keine laengere Liste. Ohne diesen Satz baut die naechste
        Rolle die drei Stufen in SchrittStatus hinein und erzeugt eine zweite Wahrheit.
        Fundstelle: BERICHT-PROZESSEBENE-DREI-FRAGEN.md, die Einordnung nach der
        Gegenueberstellung — Zeile am Bau-Stand nennen, nicht aus diesem Blatt uebernehmen.
W-40-2  (P1) Je Stufe steht, was sie leistet, mit der Herkunft aus der Quelle:
        confirmed trennt gerechnet von bestaetigt und traegt L-9; outdated ist die
        Invalidierung; blocked ist die Sperre. Kein Satz darf ueber die Quelle
        hinausgehen — was dort nicht steht, steht in 7-GRENZEN als offene Frage.
W-40-3  (P1) 7-GRENZEN nennt die ZAHLENLUECKE: das Zielbild fuehrt acht Stufen, vier
        sind vorhanden, drei sind als fehlend benannt, und review-required faellt aus
        der Rechnung. Die Frage wird GESTELLT und NICHT beantwortet — sie gehoert Yama.
W-40-4  7-GRENZEN nennt ausserdem, dass die Quelle blocked nicht weiter beziffert und
        dass die Abgrenzung zu DECISION_BLOCKED im Prozess nicht belegt ist.
W-40-5  Die zulaessigen UEBERGAENGE sind als Vorgabe beschrieben, oder es steht
        ausdruecklich, dass die Quelle sie nicht hergibt. Eine Statusachse ohne
        Uebergaenge ist eine Wortliste — aber eine erfundene Uebergangstabelle waere
        schlimmer als ein benanntes Fehlen.
W-40-6  Der Bezug zu W-38 steht in 2-FUNKTION mit Fundstelle: SchrittStatus traegt vier
        Stufen, und W-40 tritt NEBEN sie und nicht in sie hinein.
W-40-7  Alle sieben Blaetter gefuellt, Gegenprobe `tail -n +2 <blatt> | md5` je Blatt,
        keine zwei Werkzeuge mit gleichem Hash.
```

**Nachweisform: Befehl und Trefferzeilen** (B5), **jede Fundstelle am Bau-Stand genannt und nicht
aus diesem Blatt übernommen** (Pflichtprüfung 8 — meine Zeilenangaben können bis zum Bau gewandert
sein), **mindestens eine Stelle je Zählung geöffnet** (Pflichtprüfung 7).

```yaml
warum_ENTWORFEN_und_nicht_BESCHRIEBEN: "Es gibt keinen Code. Die drei Stufen fehlen im Bestand —
        genau das ist der Befund, aus dem der Auftrag entstanden ist. Ein BESCHRIEBEN-Blatt liest
        vorhandenen Code ab; hier wird VORGEGEBEN, was gebaut werden soll. Vierter Auftrag dieser
        Art nach W-15, W-23 und W-27."
warum_P1_und_nicht_P2: "confirmed traegt L-9, also die Bedingung, ob eine PV-Belegung starten darf.
        Solange die Stufe fehlt, ist die Regel 'PV erst nach bestaetigter Dachgeometrie' nicht
        pruefbar — sie gilt und kann nicht gemessen werden. Das ist dieselbe Klasse wie E1 vor A-21:
        eine Regel, die niemand belegen kann, wirkt nicht."
was_dieses_blatt_NICHT_entscheidet: "Ob review-required zur Gueltigkeitsachse gehoert, und wie
        blocked sich von DECISION_BLOCKED abgrenzt. Beides ist eine Fachentscheidung und beides
        steht als offene Frage im Blatt statt als stille Annahme."
W_40_nimmt_keinen_paragraf3_platz: "ENTWURF, nicht IN_ARBEIT."
```
