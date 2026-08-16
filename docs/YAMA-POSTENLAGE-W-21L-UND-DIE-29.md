# Yamas Postenlage nach W-21L — und warum „29 Blätter widersprechen der Statuswahrheit" ein Fehlalarm wäre

> **Release-Prüfer, 16.08. ~23:0x.** Auf `93d7eea0`, in Yamas Namen gemessen. Anlass ist der
> W-21L-Fund des Plan-Prüfers (`653d5edb`, noch nicht gepusht — im Integrations-Checkout gelesen).

## Sein Fund trägt, und er betrifft einen Posten, den meine Zählung nie geführt hat

Der Plan-Prüfer meldet: W-21Ls eigene Ausstiegsbedingung ist eingetreten. Das Blatt sagte
*„blockiert_durch OPERANDEN-GATE, keine Deckungsart- und Lattweiten-Daten im Repo"* — **W-23 steht
heute auf `BETRIEBSBESTAETIGT` und führt die geforderten Größen für sieben Braas-Modelle im
Wortlaut.** Der nächste Schritt liegt damit beim Planner (Nachschnitt auf die sieben belegten
Modelle), nicht bei Yama.

**Für Yamas Zahl ändert das nichts, und das ist erklärungsbedürftig:**

```
Yama-Posten in docs/STATUS.md      12   (unveraendert, drei Zaehlwege einig:
                                         vereinigte Form 12 · gross/klein 12 · yama-posten.py 12)
W-21L darunter                      0   <- er stand dort NIE als Yama-Ball
```

W-21Ls Datensatz trägt `ballbesitz: —` mit dem Vermerk *„bis Yama die Fachdaten liefert ODER W-23
sie erzeugt"*. **Das ist kein Ball, sondern eine Wartestellung** — nur sein *Blatt* sagt
`ballbesitz: YAMA`. Der Posten war real, aber er lag nicht in der Statuswahrheit.

## Die naheliegende Alarmmeldung wäre falsch

Beim Nachmessen, ob das ein Muster ist, findet man zunächst:

```
Datensaetze mit Gedankenstrich-Ball                       121
davon mit einem Balltraeger IM BLATT                       29
```

**Das ist die Zahl, die man nicht melden darf, ohne die Form zu lesen.** Nach Zustand getrennt:

```
davon abgeschlossen (BETRIEBSBESTAETIGT/ERLEDIGT/ZURUECKGEZOGEN)   27
davon nicht abgeschlossen                                            2
    A-12    ABGENOMMEN          Blatt sagt "plan-pruefer"
    W-21L   DECISION_BLOCKED    Blatt sagt "YAMA"
```

**Die 27 sind kein Widerspruch, sondern die Bauform eines Auftragsblatts:** es trägt den Ballstand
vom Schnitt und wird nicht fortgeschrieben, wenn der Vorgang durchläuft. Dieselbe Unterscheidung wie
beim dritten Zustandsort — *Tafel und Datensatz sollen aktuell sein, ein Blattkopf nicht*.

Und A-12 ist der geschlossene Messauftrag (`ballbesitz: — (geschlossen 12.08. vom Planner)`).
**Bleibt genau ein echter Fall: W-21L.**

## Was für Yama bleibt

```
zwoelf Posten in der Statuswahrheit   unveraendert
W-21L                                 war nie darunter; liegt jetzt beim Planner
                                      (Nachschnitt auf die sieben belegten Modelle)
weiter bei Yama an W-21L              alles jenseits dieser sieben Modelle —
                                      die Sperre ist NICHT gefallen, nur zugeschnitten
```

Der Plan-Prüfer benennt diesen Rest ausdrücklich und richtig: *„eine Lattung ohne belegten Bereich
wäre die erfundene Zahl, gegen die das Gate steht."* **Dem ist nichts hinzuzufügen — ich bestätige
es und entscheide es nicht**, denn welche Modelle in den Nachschnitt kommen, ist eine Fachfrage.

## Was diese Messung methodisch zeigt

**Ein Ball kann an einem vierten Ort liegen.** Bisher gezählt wurden Tafel, Datensatz und
Wortlaut-Commit; W-21L zeigt das Auftragsblatt als vierten. Für die laufende Buchführung ist er
**nicht** maßgeblich — sonst wären es 29 Widersprüche statt einem. **Maßgeblich ist er nur dort, wo
der Datensatz bewusst schweigt** (`—` mit Wartevermerk): dann steht die einzige Ballangabe im Blatt,
und keine Zählung über `docs/STATUS.md` findet sie.

**Das ist derselbe Mechanismus wie bei A-42**, nur ohne Umzug: nicht weg, sondern unauffindbar.
