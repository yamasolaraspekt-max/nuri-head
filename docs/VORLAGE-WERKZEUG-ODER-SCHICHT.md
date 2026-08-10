# Vorlage — die Werkbank kennt nur „Werkzeug", und mindestens vier ihrer Einträge sind keine

```yaml
art: "PLANNER-VORLAGE — Entscheidung noetig, bevor W-01 Stufe 2 schneidbar ist"
vorgelegt_von: planner
ballbesitz: "plan-pruefer (Klassifizierung), Yama (ob die Werkbank eine zweite Kategorie bekommt)"
anlass: "Der Generator hat es in W-01s 5-CODE gemessen, nicht behauptet (04f78b73)"
blockiert: "W-01 Stufe 2 — der Reifegrad GEBAUT ist fuer diesen Fall nicht definiert"
```

## Der Befund — vom Generator gemessen, nicht von mir vermutet

**In `W-01/5-CODE/LIESMICH.md` steht nach dem Bau von Stufe 1:**

```text
GEBAUT        die Rechenschicht: Arten, Rangfolge, Toleranzmodell, Zoom-Umrechnung,
              Beschriftung, Fangpunkte aus Waenden
NICHT GEBAUT  die Werkzeugschicht - in der toolRegistry gibt es kein Werkzeug
              fuer Raster und Fang

"Der Fang ist damit kein Werkzeug im Sinne der Werkbank, sondern eine Schicht darunter."
```

> **Das ist wörtlich mein W-12-Einwand — nur hat er ihn gemessen, während ich ihn vermutet habe.**
> *Und es ist der Beleg, dass es kein Einzelfall ist: zwei von 23 Einträgen sind bereits als
> „keine Werkzeuge" benannt, aus zwei verschiedenen Rollen und mit zwei verschiedenen Wegen.*

## Warum das W-01 Stufe 2 blockiert

**Die Reifegrade des Registers definieren `GEBAUT` so:**

```text
GEBAUT    "Code vorhanden und ueber 5-CODE auffindbar"
```

**Für W-01 trifft beides ZU — heute schon:**

```text
Code vorhanden           geometry/fangKern.ts, 276 Zeilen, elf Ausfuhren, 3 Zusagen
ueber 5-CODE auffindbar  seit 04f78b73 steht die Anbindung dort namentlich
Registerstand             BESCHRIEBEN
```

> **Zwei Lesarten, und sie widersprechen sich:**
>
> ```text
> nach dem WORTLAUT    W-01 ist GEBAUT — beide Bedingungen sind erfuellt
> nach der ABSICHT     W-01 ist nicht gebaut — es gibt kein bedienbares Werkzeug
> ```
>
> **Der Generator hat `BESCHRIEBEN` gesetzt und „Stufe 2 folgt" geschrieben** — also der Absicht
> gefolgt. Das ist vertretbar, aber es steht nicht in der Definition. **Ich kann Stufe 2 nicht
> schneiden, solange nicht feststeht, was sie erreichen soll:** ein Werkzeug bauen, das es nach dem
> Befund nicht geben soll — oder die Kriterien aus `6-PRUEFUNG` fahren, was `GEPRÜFT` wäre und
> `GEBAUT` überspringt.

## Die vier Kandidaten — und warum sie zusammengehören

```text
W-01  Raster und Fang       Schicht.  Vom Generator gemessen (5-CODE).
                            fangKern.ts wird von anderen Werkzeugen BENUTZT,
                            nicht vom Nutzer ausgewaehlt.
W-12  Ansicht und Kamera    Schicht.  Das Register nennt es selbst "quer zu allem".
                            Kamerasteuerung waehlt man nicht aus einer Leiste.
W-18  Pruefung Topologie    Schicht.  Es ist die Grenzfrage von W-02 und W-05 —
                            genau das, was 7-GRENZEN je Werkzeug ohnehin verlangt.
W-05  Raum erkennen         OFFEN, nicht entschieden. roomDetection laeuft
                            automatisch aus Waenden; klickt der Nutzer "Raum
                            erkennen" oder entsteht der Raum von selbst?
                            -> das muss gemessen werden, nicht geraten.
```

**Was sie gemeinsam haben:** sie **rechnen**, aber man **wählt sie nicht**. Ein Werkzeug hat einen
Zustand („aktiv"), einen Abbruch (`ESC`), eine Beschriftung und einen `disabledReason`. Eine Schicht
hat davon nichts — sie hat eine Signatur und Grenzfälle.

> *Deshalb passt die 7-Blatt-Vorlage nur halb: `4-BEDIENUNG` ist für eine Schicht sinnlos, `7-GRENZEN`
> dagegen wichtiger als bei jedem Werkzeug. **Der Generator hat `4-BEDIENUNG` für W-01 trotzdem
> gefüllt — ich habe nicht geprüft, mit welchem Inhalt.** Das gehört zur Entscheidung dazu.*

## Was ich vorschlage — drei Wege, ich empfehle den zweiten

```text
V1  ALLES BLEIBT "WERKZEUG".  Fuer Schichten heisst GEBAUT einfach "Code da und
    verlinkt", Stufe 2 entfaellt und es geht direkt auf GEPRUEFT.
    KOSTET: nichts. LAESST: die Vermischung im Register stehen — der naechste
    Leser fragt wieder, warum man den Fang nicht anklicken kann.

V2  ZWEITE KATEGORIE: "SCHICHT".  Das Register bekommt eine Spalte oder ein
    Praefix (S-01 statt W-01). Fuer Schichten gilt:
      - 4-BEDIENUNG entfaellt ausdruecklich (statt leer zu bleiben)
      - GEBAUT = Code vorhanden + verlinkt  -> fuer W-01 HEUTE erreicht
      - Stufe 2 ist GEPRUEFT, nicht GEBAUT
    KOSTET: eine Registeraenderung und vier Umklassifizierungen.
    BRINGT: die Abhaengigkeitskette wird ehrlich — "W-02 braucht W-01" heisst dann
            "das Wandwerkzeug benutzt die Fangschicht", und das ist der Sachverhalt.

V3  DIE VIER STREICHEN.  Sie sind Infrastruktur und gehoeren nicht in einen
    Werkzeugkatalog.
    KOSTET: die Formelzuordnung (F-040/F-041 haengen an W-01) muesste umziehen.
    RISIKO: dann ist der Fang nirgends beschrieben — und die Beschreibung von
            Stufe 1 ist gut und gemessen. Verwerfen waere Verlust.
```

**Ich empfehle V2.** *V1 verschiebt die Frage nur, V3 wirft eine gute Beschreibung weg. V2 kostet
eine Registerspalte und macht die Kette lesbar: **Werkzeuge hängen an Schichten, nicht an anderen
Werkzeugen** — genau das sagt die Kette schon („W-02 braucht W-01"), nur nennt sie es nicht so.*

## Was NICHT zur Entscheidung gehört

- **Kein Umbau von Code.** Es geht um die Klassifizierung in der Werkbank, nicht um `fangKern.ts`.
- **Keine Aussage über W-15/W-17/W-19/W-20/W-23.** Die sind echte Werkzeuge ohne Code — ein anderer
  Fall (Klasse C der Anschlussmatrix), nicht dieser.
- **W-05 wird nicht mitentschieden**, sondern **gemessen**: läuft `roomDetection` automatisch oder
  auf Klick? *Das ist eine Messung von zehn Minuten und ich habe sie nicht gemacht — sie gehört in
  W-05s eigenen Anschluss, nicht in diese Vorlage.*

```yaml
fehlerklasse: SPEC
verursacher: "die Werkbank-Vorlage — eine Kategorie fuer zwei Arten von Gegenstand"
gefunden_von: generator (04f78b73), unabhaengig vom Planner-Einwand zu W-12
blockiert: "W-01 Stufe 2"
nicht_blockiert: "W-02/1 laeuft (IN_ARBEIT), W-13/1 und A-12 warten auf DoR — alle unberuehrt"
```
