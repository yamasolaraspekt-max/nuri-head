# W-41 — Abhängigkeitsgraph. „Änderungen propagieren, niemals stille Löschung"

```yaml
auftrag: "W-41"
werkzeug: "W-41 Abhängigkeitsgraph / Invalidierung"
art: "STUFE 6 — Blatt schneiden, Ziel ENTWORFEN (VORGABE). Es gibt KEINEN Code. Die Blätter geben
      vor, was gebaut werden soll — wie W-15, W-23, W-27 und W-40."
spur: A
heimat_app: ticket
dor_beleg: "steht aus — plan-pruefer."
status_steht_in: docs/STATUS.md
basis_sha: c9ac316d
prioritaet: P1
anlass: "Yamas Freigabe 12.08. für W-40, W-41 und W-42 als Vorgabe. W-41 ist der zweite der drei
         und hängt inhaltlich an W-40: outdated ist ein Zustand, Propagierung ist ein Mechanismus."
ballbesitz: "plan-pruefer (DoR)"
claim: "planner 12.08. — Claim VOR dem Schnitt."
grundlage: "docs/BERICHT-PROZESSEBENE-DREI-FRAGEN.md als Quelle · W-40 (gleichzeitig geschnitten)
            als Zustandsvorgabe · REGISTER.md Stufe 6"
```

## 1 — Der Satz, um den es geht, und was er verbietet

**Aus dem Register, wörtlich:** *„Änderungen propagieren, **niemals** stille Löschung."*

```text
PROPAGIEREN      wird eine Angabe geaendert, muss alles was darauf beruht seinen
                 Zustand aendern — nicht seinen INHALT verlieren.

STILLE LOESCHUNG ist der verbotene Fall: ein abgeleiteter Wert verschwindet, weil
                 seine Grundlage sich geaendert hat, und niemand erfaehrt es.
```

> **Das Verbot ist die Aussage, nicht die Propagierung.** *Ein Graph, der Änderungen weiterträgt,
> ist gewöhnliche Technik. **Ein Graph, der nichts stillschweigend wegwirft, ist eine
> Ehrlichkeitskonstruktion** — dieselbe Bauart wie W-20 (die Stückliste schätzte, während die Engine
> die geclippte Geometrie zeichnete), W-34 (`bebauteGeschosse` zählt nur, was etwas trägt) und W-38
> (die Attrappen sind bewacht). **Wer W-41 als Invalidierungs-Cache beschreibt, verfehlt seinen
> Zweck.***

## 2 — W-41 braucht W-40, und die Grenze zwischen beiden ist scharf

```text
W-40 sagt   DASS es outdated gibt und was der Zustand BEDEUTET.
W-41 sagt   WANN er eintritt, WORAUF er sich fortsetzt, und WAS dabei erhalten bleibt.
```

**Ohne diese Grenze schreiben beide Blätter dieselbe Sache zweimal** — *und dann gilt, was A-20 für
den Zustand festgestellt hat: zwei Orte für eine Wahrheit, und beide veralten unabhängig. **W-41
definiert `outdated` nicht neu**, es verweist auf W-40.*

## 3 — Was die Quelle NICHT hergibt (und darum eine Vorgabe ist)

```text
NICHT GEMESSEN, woertlich aus BERICHT-PROZESSEBENE-DREI-FRAGEN.md:
  „Inhalte der elf Schritte · Fortschritt je Geschoss · ABHAENGIGKEITSGRAPH ·
   der ConfigWizard-Test"
```

> **Der Abhängigkeitsgraph steht in der Quelle ausdrücklich unter „nicht gemessen".** *Damit ist
> W-41 die **dünnste** der drei Vorgaben: es gibt den Satz aus dem Register, es gibt `outdated` als
> Anschluss, und es gibt **keine erhobene Abhängigkeitsstruktur**. **Das Blatt muss diese Lage
> benennen, statt sie zu überspielen** — und es muss sagen, was zuerst erhoben werden müsste, damit
> aus der Vorgabe ein Bau werden kann.*

**Was erhoben werden müsste, benennt das Blatt als Anschlussliste** — *nicht als Vermutung darüber,
wie der Graph aussieht, sondern als Frage: **welche Größe hängt an welcher?** Kandidaten sind im
Bestand messbar (Geometrie → Flächen → PV-Belegung → Stückliste), aber gemessen ist es nicht, und
darum steht es als Aufgabe und nicht als Ergebnis.*

## 4 — Scope

```text
W-41 IST      die VORGABE des Mechanismus: wann Invalidierung eintritt, wie sie sich
              fortsetzt, und die harte Zusage, dass dabei nichts still verschwindet.
              Dazu die Anschlussliste: welche Abhaengigkeiten zuerst zu ERHEBEN sind.

W-41 IST NICHT
              die Definition von outdated — die gehoert W-40.
              der BAU. Kein Produktivcode.
              eine erfundene Abhaengigkeitsstruktur. Was nicht erhoben ist, wird als
              zu erheben benannt und nicht als bekannt behauptet.
```

## 5 — Abnahmekriterien

```text
W-41-1  (P1, TRAGEND) 1-ZWECK nennt das VERBOT als Kern: niemals stille Loeschung.
        Propagieren allein ist gewoehnliche Technik; die Zusage, dass nichts
        stillschweigend wegfaellt, ist die Leistung. Der Satz steht woertlich aus dem
        Register mit Fundstelle am Bau-Stand.
W-41-2  (P1) Die Grenze zu W-40 steht in 2-FUNKTION: W-40 sagt DASS und WAS BEDEUTET,
        W-41 sagt WANN und WORAUF. W-41 definiert outdated NICHT neu, sondern verweist.
        Ohne diese Grenze entstehen zwei Orte fuer eine Wahrheit — der Befund aus A-20.
W-41-3  (P1) 7-GRENZEN nennt woertlich, dass die Quelle den Abhaengigkeitsgraphen unter
        NICHT GEMESSEN fuehrt, und dass W-41 damit die duennste der Vorgaben ist. Wer
        das nicht liest, haelt die Vorgabe fuer eine Erhebung.
W-41-4  (P1) Die ANSCHLUSSLISTE steht im Blatt: welche Abhaengigkeiten zuerst zu erheben
        sind, als FRAGE formuliert. Jede genannte Abhaengigkeit ist entweder mit
        Fundstelle belegt oder ausdruecklich als Kandidat gekennzeichnet. Eine erfundene
        Struktur ist der schwerere Fehler als eine kurze Liste.
W-41-5  Was bei einer Invalidierung ERHALTEN bleiben muss, ist benannt — mindestens: der
        alte Wert, der Zeitpunkt und der Grund. Sonst ist die Zusage gegen stille
        Loeschung nicht pruefbar.
W-41-6  Alle sieben Blaetter gefuellt, Gegenprobe `tail -n +2 <blatt> | md5` je Blatt,
        keine zwei Werkzeuge mit gleichem Hash.
```

**Nachweisform: Befehl und Trefferzeilen** (B5), **Fundstellen am Bau-Stand statt aus diesem Blatt**
(Pflichtprüfung 8), **mindestens eine Stelle je Zählung geöffnet** (Pflichtprüfung 7).

```yaml
warum_P1: "outdated ohne Propagierung ist ein Zustand, den niemand setzt. W-40 und W-41 tragen
        zusammen, was einzeln nicht wirkt — deshalb dieselbe Stufe."
die_ehrlichste_aussage_dieses_auftrags: "Die Quelle fuehrt den Abhaengigkeitsgraphen unter NICHT
        GEMESSEN. Ich schneide die Vorgabe trotzdem, weil Yama sie freigegeben hat, aber das Blatt
        muss sagen, dass hier eine Struktur VORGEGEBEN und keine abgelesen wird — und was zuerst zu
        erheben ist, damit daraus ein Bau werden kann."
was_dieses_blatt_NICHT_tut: "Es erfindet keine Abhaengigkeitsstruktur. Kandidaten wie Geometrie ->
        Flaechen -> PV-Belegung -> Stueckliste sind im Bestand messbar, aber NICHT gemessen; sie
        stehen als Frage und nicht als Ergebnis."
W_41_nimmt_keinen_paragraf3_platz: "ENTWURF, nicht IN_ARBEIT."
```
