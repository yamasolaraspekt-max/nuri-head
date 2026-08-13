# W-06 · Geschoss verwalten — ZWECK

## Welches Problem des Anwenders löst dieses Werkzeug?

**Ein Haus hat mehrere Stockwerke, und der Planer muss wissen, in welchem er gerade zeichnet — und
das obere nicht noch einmal von Hand aufbauen müssen.**

Zwei Dinge, die verschiedenen Leuten gehören:

1. **„Wo bin ich, und was liegt darüber und darunter?"** — der Geschoss-Stapel als Bild, mit
   Höhenlage je Geschoss und dem aktiven hervorgehoben.
2. **„Das Obergeschoss hat denselben Grundriss."** — ein Geschoss als Vorlage duplizieren, samt
   Wänden, Öffnungen und Dach.

## Der tragende Punkt: der ID-Remap, und was passiert, wenn ihn jemand bricht

**Wörtlich aus `geometry/geschossVorlage.ts:4-8`:**

> *„Reine Funktion: aus einem Quell-Geschoss + seinen Nodes (+ optional Dach) entsteht ein neues
> Geschoss darüber, mit KOPIERTER Geometrie. **Alle Nodes bekommen neue IDs; Öffnungen werden auf
> die NEUEN Wand-IDs umgehängt (id-Remap), damit Türen/Fenster an ihren kopierten Wänden hängen —
> nicht an den alten.** Kein Schreibpfad, keine Szene-Mutation; das Ergebnis füttert die Commands
> (ADD_LEVEL + ADD_NODE + ADD_ROOF)."*

> ***DER SCHADENSFALL, und er ist der Grund, warum dieser Satz hier oben steht:*** *wer den Remap
> bricht, erzeugt ein dupliziertes Geschoss, dessen Türen und Fenster an den **Wänden des
> Ursprungsgeschosses** hängen. **Beim Duplizieren fällt das nicht auf** — das Obergeschoss sieht
> richtig aus. Es fällt auf, wenn jemand **unten** eine Wand ändert und **oben** das Fenster
> mitwandert. Bis dahin können Wochen vergehen, und der Grundriss ist längst weitergereicht.*

**Ein Blatt, das den Remap nicht nennt, lässt die nächste Rolle glauben, „Geometrie kopieren" sei die
ganze Aufgabe.** *Sie ist der leichtere Teil.*

**Und die Härtung daneben ist gebaut, nicht geplant** (`:67-71`): hängt eine Öffnung an einer Wand,
die **nicht mitkopiert** wird, dann bekommt die Kopie `hostWallId: undefined` — die Referenz wird
**fallengelassen** statt auf die alte id zu zeigen. *Der Kommentar nennt den Grund: „das bände die
Öffnung an eine Wand des Quell-Geschosses."* **Die Lücke ist damit sichtbar statt still falsch.**

## Der zweite Befund (AUF-43): ein Wert, der im Modell lebte und nie erschien

**Wörtlich aus `app/dashboard/geschossStapel.ts:4-8`:**

```text
dreizehn Bedienelemente in einer Zeile, vier voneinander unabhaengige Aufgaben,
und der Geschossname stand ZWEIMAL nebeneinander — einmal als 111-px-Select,
einmal als Textfeld mit demselben Wert.
Die Hoehenlage (elevation) wird im Modell gefuehrt, aber nirgends gezeigt.
Und es gab KEIN Bild vom Stapel.
```

> **Das ist die STILLE Variante der Falschauskunft** — *kein falscher Wert, sondern ein vorhandener,
> der nie erscheint.* **Sie ist schwerer zu finden als ein falscher**, weil nichts widerspricht: der
> Anwender vermisst nicht, was er nie gesehen hat.

**Die Antwort ist `hoehenLabel()` (`geschossStapel.ts:51`), und sie liefert ein benanntes Format —
am Code gelesen, nicht beschrieben:**

```text
elevation === 0     ->  '±0 mm'      kein '+0 mm': das Erdgeschoss hat keine Richtung
elevation > 0       ->  '+2 700 mm'  Vorzeichen explizit
elevation < 0       ->  '−2 800 mm'  U+2212 (Minuszeichen), NICHT der Bindestrich U+002D
Tausendertrennung   ->  U+202F, schmales GESCHUETZTES Leerzeichen
```

*Die zwei Codepunkte sind **am Code gemessen** und nicht abgelesen — ein Bindestrich und ein
Minuszeichen sehen in jedem Blatt gleich aus, und wer sie in einem Test vergleicht, misst sonst die
Schriftart.*

*Der Code sagt selbst, warum das Trennzeichen als Escape geschrieben steht (`:54-56`): **als
sichtbares Zeichen wäre es von einem gewöhnlichen Leerzeichen nicht zu unterscheiden**, und genau
daran ist der erste Test des Erbauers gescheitert.*

**Warum das Format überhaupt eine Aussage ist:** *`+2 700 mm` ist auf einen Blick als Höhe über null
lesbar, `2700` nicht.* Der Wert lag im Modell; **erst das Format macht ihn zur Auskunft.**

## Wann greift der Anwender danach?

Wenn er **das erste Mal ein zweites Geschoss braucht** — und danach jedes Mal, wenn er zwischen den
Geschossen wechselt. *`GeschossFlaeche.tsx:5-7` misst die Folgen: **ein angelegtes Geschoss entsperrt
34 der 110 Werkzeuge**, und diese Handlung steckte vorher „in einem 111-px-Dropdown zwischen
‚Rückgängig' und ‚Speichern'".*

> *Der Nenner **110** ist der Stand jenes Textes; heute sind es **111** — der Kontur-Vertrag kam mit
> `1fba9a1d` hinzu (A-29). **Zitat unverändert gelassen**, weil es einen Stand belegt; der Zähler 34
> ist hier nicht nachgemessen und wird deshalb nicht als heutige Zahl geführt.*

## Woran merkt er, dass es fehlt?

**Er baut das Obergeschoss von Hand nach** — jede Wand, jedes Fenster. Oder er lässt es und plant
eingeschossig weiter, was bei einem Wohnhaus die Aufgabe verfehlt.

## Was ist ausdrücklich NICHT Zweck dieses Werkzeugs

- **Kein Schreibpfad.** Die Geometrieschicht mutiert die Szene nicht; sie liefert Daten, aus denen
  die Befehle gebildet werden. Siehe `2-FUNKTION`.
- **Keine Sortierung ändern.** *`geschossStapel.ts:14-15`: „Die Ordnung bleibt, wie sie ist:
  `sortOrder`, dann `elevation`. Der Auftrag verbietet die Sortierumkehr ausdrücklich — hier wird
  nur **angezeigt**, was ohnehin gilt."*
- **Kein neuer Zustand.** *Welches Geschoss aktiv ist, weiß allein `setActiveLevel` im Store
  (`geschossStapel.ts:17-18`).*
- **Keine Treppe.** Die Verbindung zwischen den Geschossen ist W-09.
