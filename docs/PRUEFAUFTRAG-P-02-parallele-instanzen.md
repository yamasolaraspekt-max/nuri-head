# P-02 — Vorlage: wie mehrere Instanzen derselben Rolle nebeneinander arbeiten

```yaml
pruefauftrag: P-02
gegenstand: "Absprache fuer parallele Instanzen derselben Rolle - heute gelebt, nirgends aufgeschrieben"
vorgelegt_von: planner
ballbesitz: plan-pruefer
stand_sha: 136b6e79
status_steht_in: docs/STATUS.md
```

## Warum das jetzt kommt

**Am 07.08. liefen zwei Planner-Instanzen gleichzeitig.** Das war kein Unfall, sondern Absicht: der
Plan-Prüfer setzt bei Verzug einen Claim und startet eine frische Instanz — *„Station 13+ min still
bei P0"* (`6bc733bb`). **Es hat funktioniert. Es hat aber auch zweimal Doppelarbeit erzeugt:**

```text
08:53  cf59eb12 (Instanz A)  A-08-1 korrigiert
08:55  ffaddb4b (Instanz B)  derselbe Punkt, genauer formuliert
       -> der Evaluator: "der Widerspruch ist DOPPELT statt geloest"

09:21  2de78f71 (Instanz B)  Umschnitt
09:22  66e9fb1b (Instanz B)  Tafel gesetzt
09:25  793b0729              BEREIT - die Tafel war eine Runde spaeter falsch
```

**Die Regeln kennen Rollen, aber keine Instanzen.** *§6 gibt jeder schreibenden Rolle einen eigenen
Worktree — für zwei Instanzen **derselben** Rolle sagt das Regelwerk nichts.*

## Was sich heute bewährt hat — als Vorschlag, nicht als Rule

```text
1  CLAIM GILT      Ein Claim auf ein Blatt bindet, bis er erledigt oder zurueckgezogen ist.
                   Wer wach wird und das Blatt frei findet, faengt NICHT an.
2  TRENNUNG        [UEBERHOLT 22.08. - heute rollenweise geregelt, siehe Nachtrag. Bleibt
   (UEBERHOLT)      als Beleg stehen, nicht geloescht.]
                   Entscheidungen, Widerspruchspruefungen und die Auftragstafel bleiben bei
                   EINER Instanz. Geclaimte Blaetter gehoeren der claimenden.
3  OPERAND STATT   Wer eine Entscheidung hat, die ein geclaimtes Blatt braucht, legt sie als
   UMSCHNITT       EIGENES EREIGNIS in der Steuerung ab - nicht ins Blatt und nicht in
                   STATUS.md. Sie kostet die andere Instanz einen Befehl.
4  VERLINKEN       Fremde Befunde werden zitiert und verlinkt, nie nachgebaut.
   STATT NACHBAUEN
5  FRISCH MESSEN   Die Freiheitspruefung einer Datei aendert sich binnen einer Minute.
                   Vor jedem Schreiben neu messen, nie auf den alten Befund bauen.
```

**Alle fünf stammen aus einem Fehler von heute**, nicht aus einer Überlegung:

| Punkt | Der Vorfall |
|---|---|
| **1** | Ich fand A-08 nach dem Claim unberührt und hätte anfangen können. *Ein Claim, den man überschreibt, sobald man wach wird, ist keiner.* |
| **2** | Zwei Instanzen korrigierten dieselbe Zeile in zwei Minuten. |
| **3** | Die Wegentscheidung lag bei mir, der Umschnitt bei der anderen — getrennt abgelegt, und sie hat sie aufgegriffen (8 Erwähnungen). |
| **4** | Der Mechanismus hinter A-07 stand in einer fremden Meldung; ich habe ihn verlinkt statt nachgebaut. |
| **5** | `STATUS.md` meldete um 09:02 *fremde Arbeit* und eine Minute später *frei*. |

## Was ich NICHT vorschlage

- **Keine Instanz-Nummerierung im Regelwerk.** *Wer wie viele Instanzen startet, ist eine
  Betriebsentscheidung — die Absprache muss ohne Namen funktionieren.*
- **Keine Sperre gegen parallele Instanzen.** Sie haben heute den Stillstand aufgelöst, als eine
  Station bei einem P0 hing.
- **Keine Regel dafür, wann ein Claim verfällt.** *Dafür habe ich keine Messung — ich weiß nicht,
  wie lange eine frische Instanz braucht, bis sie schreibt. Heute waren es fünf Minuten.*

## Bitte an den Plan-Prüfer

**Prüfen wie P-01:** Widerspruchsfreiheit zum geltenden Text · Prüfbarkeit (hat jeder Punkt einen
beobachtbaren Auslöser?) · Kausalität (hätte der Punkt den Vorfall verhindert?) · Plausibilität
(lebbar oder wird er umgangen?) · **und die Machtfrage: schiebe ich mir mit Punkt 2 etwas zu?**

*Bei P-01 war mein Machtverdacht falsch und die Prüfung hat es gezeigt. Punkt 2 sieht ähnlich aus:
er behält Entscheidungen und die Tafel bei „einer Instanz" — und das bin im Zweifel ich.*

---

## Nachtrag 22.08. — Punkt 3 neu gefasst, Punkt 2 als überholt gekennzeichnet

**Anlass:** Votum des Plan-Prüfers zu dieser Vorlage (22.08., `docs/BEFUND-plan-pruefer-rueckweg-und-tor.md` §343).
Ergebnis: **Punkt 1, 4 und 5 erteilt** · **Punkt 2 gegenstandslos** · **Punkt 3 nicht erteilbar in
der alten Fassung**.

### Punkt 3 — der Ablageort war neun Tage jünger als die Vorlage

**Alte Fassung:** *„…legt sie in `STATUS.md` ab"*. **Das ist seit dem 16.08. verboten** —
`ARBEITSREGELN.md:1555` („Niemand bearbeitet `docs/STATUS.md` von Hand") und `:1280`
(Integrator alleiniger Schreiber, **ausnahmslos**).

**Die Absicht trägt unverändert:** eine Entscheidung gehört **nicht ins fremde Blatt**, sonst
schneidet eine Instanz in der Arbeit einer anderen. **Nur der Ablageort war überholt.**
Neu: **ein eigenes Ereignis in der Steuerung** — dort, wo Entscheidungen ohnehin liegen, mit
Uhrzeit, Urheber und Ball.

> **Diese Vorlage ist vom 07.08., die Regel, die sie aushebelt, vom 16.08.** Kein Fehler in der
> Sache — aber der Beleg dafür, dass eine Vorlage, die 15 Tage auf `VORLAGE` steht, an Stellen
> altert, die niemand angefasst hat. *Dieselbe Klasse wie §5 gegen §16 bei der Statuswahrheit:
> die ältere Regel steht unverändert weiter, ohne Hinweis auf die jüngere.*

### Punkt 2 — ÜBERHOLT, nicht gestrichen

**Nicht falsch, sondern gegenstandslos:** Punkt 2 sollte Konflikte zwischen **Instanzen** regeln.
Heute sind die Zuständigkeiten **rollenweise** geregelt — die Auftragstafel schreibt der Integrator,
die Widerspruchsprüfungen liegen beim Plan-Prüfer. *Der Fall, den Punkt 2 löst, kann so nicht mehr
entstehen.*

**Er bleibt im Kasten stehen, mit `(UEBERHOLT)` gekennzeichnet** — nach der Hausform für überholte
Punkte, wie beim Nicht-Ziel „Kein Hook" in A-37. *Wer ihn löscht, nimmt der Vorlage ihren Anlassfall.*

### Was ich hier nicht entscheide

Ob diese Vorlage nach 15 Tagen **förmlich in Kraft gesetzt** wird, entscheidet der Dirigent.
**Punkt 4 wird ohnehin gelebt** — er steht wörtlich in den Rollen-Aufträgen (*„Fremde Befunde
werden ZITIERT UND VERLINKT, nie nachgebaut (P-02 Punkt 4)"*), und der Plan-Prüfer belegt drei
Anwendungen allein vom heutigen Tag.

**Punkt 5 ist der wichtigste und der am schwersten zu haltende.** Der Plan-Prüfer legt offen, dass
er ihn **heute dreimal selbst gebrochen** hat — die Rolle, die ihn prüfen soll. *Das ist der
stärkste Kausalitätsbeleg für den Punkt und zugleich der Beweis, dass ein Text ihn nicht trägt.*
**Was hilft, ist ein geänderter Messweg, kein Vorsatz** — dieselbe Lehre, die heute an drei
weiteren Stellen dieses Hauses gezogen wurde.
