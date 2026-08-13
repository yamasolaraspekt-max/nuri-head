# W-31 · PV-Schnellbelegung — ZWECK

## Welches Problem des Anwenders löst dieses Werkzeug?

**Er will wissen, wie viele Module auf das Dach passen und wie viel kWp das gibt — ohne vorher das
ganze Haus zu modellieren.**

**Yamas Anforderung steht wörtlich im Dateikopf** (`geometry/pvBelegung.ts:4`):

> *„**Für PV muss man nicht das ganze Haus modellieren.**"*

> ***Das ist der Grund, warum diese Stufe AUTARK neben der vollständigen Belegung steht und nicht
> als deren Vorstufe.*** *Eine Vorstufe müsste man später wegwerfen oder migrieren. Diese hier ist
> ein eigener, abgeschlossener Weg: **Rechteck rein, Modulzahl und Leistung raus.*** Wer das ganze
> Gebäude ohnehin plant, nimmt die vollständige Belegung — wer nur ein Angebot rechnen will, nicht.

## Der tragende Punkt: gesperrt ist nur der ANDERE Teil

**Die Registerzeile trägt `LEER` und den Vermerk „gesperrt bis F-028 🟢" — und sie nennt den
gebauten Code im selben Atemzug** (`02-WERKZEUGE/REGISTER.md:98`).

**Gemessen, was die Sperre wirklich trifft:**

```text
F-028  Azimut-Konvention an der Systemgrenze   FORMELSAMMLUNG.md, Abschnitt
       'F-028 · Azimut-Konvention an der Systemgrenze'   Ampel 🔴
       Aufgenommen 12.08. auf Yamas ausdrueckliche Auflage.
       Gesperrt ist das DURCHREICHEN eines Azimut zwischen zwei Konventionen.

PvEingabe (pvBelegung.ts:10-24)  SIEBEN Felder, selbst gezaehlt:
       dachLaenge · dachBreite · modulBreite · modulHoehe · modulLeistung
       randabstand? · modulabstand?
       -> KEINE Richtung. Kein Azimut. Kein Winkel.
```

> **Die Schnellstufe trägt keinen Azimut und ist damit kein F-028-Fall.** *Gesperrt ist die
> **vollständige** Belegung — die mit Ausrichtung, Verschattung und Ertrag. Die Schnellstufe rechnet
> Rechtecke auf einem Rechteck.*

**Ohne diese Unterscheidung liest die nächste Rolle „gesperrt" und lässt ein gebautes,
angeschlossenes Werkzeug unbeschrieben** — *genau der Zustand, den dieses Blatt beendet.*

## Und die Stelle, an der es doch eine Richtung gibt — benannt, nicht verschwiegen

**`app/dashboard/fachFlaechen.ts:252`** führt unter den Eingängen der Fachfläche:

```text
{ label: 'Ausrichtung und Neigung', einheit: '°' }
```

> ***Das ist die einzige Stelle im ganzen Bedienweg, an der eine RICHTUNG steht — und sie steht in
> einer VORSCHAU, nicht in `PvEingabe`.*** *Wer den Bedienweg als vollständig beschreibt und diese
> Stelle auslässt, lässt genau die eine weg, die dem tragenden Satz „kein Azimut" widerspricht.*

**Das Blatt benennt die Spannung und entscheidet sie nicht** — die Einzelheiten stehen in
`7-GRENZEN`.

## Wann greift der Anwender danach?

**Beim Angebot**, bevor irgendetwas gezeichnet ist: Dachmaß, Modulmaß, Nennleistung — fertig.

## Woran merkt er, dass es fehlt?

**Er rechnet es im Kopf oder in einer Tabelle**, mit einem geschätzten Randabstand und ohne zu
prüfen, ob quer mehr Module ergibt als hochkant. *Die Funktion probiert beide Lagen und nimmt die
bessere (`:52-59`) — das ist der Teil, den eine Handrechnung regelmäßig überspringt.*

## Was ist ausdrücklich NICHT Zweck dieses Werkzeugs

**Wörtlich aus `pvBelegung.ts:6-7`:**

> *„**GRENZE:** Ertrag/Verschattung/Strings bleiben der Fach-Engine (**wberechnung**) vorbehalten —
> hier nur Geometrie/Anzahl/Leistung."*

**Das ist eine Aussage über die Arbeitsteilung zwischen ZWEI Apps und keine Feinheit.** *Siehe
`7-GRENZEN`.*
