# ENTSCHEIDUNG — muss die Referenzunterlage platziert werden können? (Planner, 30.07., 21:48)

**Ball vom Generator**, aus seiner AUF-88-P1-Sichtprobe: *„Die Unterlage ist am Szenen-Ursprung
verankert und reicht nach unten — sie berührt das Gebäude an der Grundlinie, überlappt es aber
nicht. K-03 (‚unter der Zeichnung') ist erfüllt — das meint die Ebenenfolge, und die stimmt.
**Wer darüber nachzeichnen will, braucht ein Platzieren/Verschieben.** Steht in keinem Kriterium
→ Planner-Entscheidung."*

## Gemessen, nicht angenommen

```text
UnterlagenEbene.tsx:58-59      x={0}  y={0}          <- hart verdrahtet
kalibrierung.ts                abstand() · berechneMassstab() · MASSSTAB_STANDARD
                               -> kann MASSSTAB, kann keine Position
scene.types.ts                 kein Feld fuer eine Unterlagen-Position
```

**Die Unterlage sitzt fest am Ursprung, und es gibt nichts, was das ändern könnte.**

## Die Entscheidung

**Ja — und sie ist nicht optional.** *Der Zweck einer Referenzunterlage ist, dass man sie
**nachzeichnet**. Wer sie nicht dorthin bringen kann, wo gezeichnet wird, hat ein Bild in der
Ecke, keine Vorlage.* Heute funktioniert sie nur, wenn der Nutzer sein Gebäude zufällig am
Ursprung beginnt — und der Generator hat gemessen, dass genau das **nicht** der Fall ist.

**Aber nicht als eigenes Werkzeug.** Der teure Weg wäre: Verschieben-Griffe, Drehgriff,
Rasterfang für die Unterlage — ein zweiter Bedienpfad neben dem, der schon da ist.

## Der billigere Weg: dieselben zwei Klicks, drei Ergebnisse

**Die Kalibrierung verlangt bereits zwei Klicks auf der Unterlage plus eine bekannte Länge.**
Aus denselben zwei Punkten folgt mehr als der Maßstab:

```text
zwei Punkte auf der Unterlage  +  die echte Strecke dazwischen
   -> Massstab      (heute schon: berechneMassstab)
   -> Ankerpunkt    (der erste Klick wird zum Bezugspunkt)
   -> Drehung       (der Winkel der Strecke gegen die Achse, auf die der Nutzer sie legt)
```

**Eine Bedienhandlung, die es gibt und die eben erst repariert wurde** *(der Generator hat sie um
20:20 von drei Klicks auf zwei gebracht und die `cancelBubble`-Sperre behoben)*. **Kein zweiter
Rechenweg, kein zweiter Bedienpfad.**

*Die Drehung ist der Teil, bei dem ich am unsichersten bin — ein gescannter Grundriss liegt oft
leicht schief, aber nicht immer, und eine automatische Drehung aus zwei Klicken kann auch das
Gegenteil bewirken. **Deshalb: Ankerpunkt zuerst, Drehung als eigener, abschaltbarer Schritt
danach.** Wer sie nicht will, bekommt sie nicht.*

## Was daraus wird: AUF-93 *(Spur A — es berührt das persistierte Schema)*

```yaml
  - id: K-01
    aussage: "Die Unterlage traegt eine Position, und sie ueberlebt das Neuladen."
    hinweis: >
      Das Schema fuehrt heute KEIN Feld dafuer. Neue Felder sind additiv und nullable
      (Dauerdirektive 1) — `unterlageX`, `unterlageY` in mm, spaeter `unterlageDrehung`.
      **Bestehende Plaene ohne diese Felder verhalten sich wie heute: Ursprung.**
    gegenbeweis: >
      Einen Plan OHNE die neuen Felder laden. Springt die Unterlage irgendwohin oder
      verschwindet sie, ist die Voreinstellung falsch — das ist der Befund.

  - id: K-02
    aussage: "Der Ankerpunkt kommt aus der bestehenden Kalibrierung, nicht aus einem neuen Werkzeug."
    befehl: "grep -c 'onMouseDown\\|draggable' UnterlagenEbene.tsx"
    erwartet: >
      0 — die Unterlage bekommt KEINE eigenen Ziehgriffe. *Sonst stehen zwei Bedienpfade
      nebeneinander, und der zweite wird der sein, den niemand pflegt.*

  - id: K-03
    aussage: "Die Ebenenfolge bleibt: Unterlage ist das ERSTE Kind der Buehne."
    hinweis: >
      AUF-88-P1/K-03 gilt unveraendert weiter. Eine platzierbare Unterlage, die ueber der
      Zeichnung liegt, ist schlimmer als eine feste darunter.

  - id: K-04
    aussage: "Die Drehung ist abschaltbar und NICHT die Voreinstellung."
    gegenbeweis: >
      Kalibriere mit einer leicht schiefen Strecke. Dreht sich die Unterlage ungefragt,
      ist die Voreinstellung falsch herum.
```

## Verhältnis zu AUF-90 (die `nochNicht`-Marke)

**Genau das ist der Grund, warum die Marke stehenbleibt.** Ich habe um 20:27 entschieden, den
Text auf *„Import und Kalibrieren stehen. Das Platzieren der Unterlage fehlt noch."* zu ändern —
**AUF-93 ist dieses Platzieren.** Ist es gebaut, fällt die Marke; vorher nicht.

*Die beiden Aufträge sind damit über eine Bedingung verbunden, nicht über eine Reihenfolge:
**AUF-90 schreibt den ehrlichen Text, AUF-93 macht ihn irgendwann überflüssig.***

---

**Ballbesitz: Generator** (AUF-93, nach AUF-48 und nach AUF-90) · **Planner** — dieser Ball ist zu.
