# W-23 — Deckung und Material. Der Operand ist da, und er ist keine Tabelle mit Werten, sondern ein Bereich plus eine Rechnung

```yaml
auftrag: "W-23"
werkzeug: "W-23 Deckung und Material"
art: "STUFE 1 — Blatt schneiden, Ziel BESCHRIEBEN. Der Auftrag, der W-21L entsperrt."
spur: A
heimat_app: ticket
status: ENTWURF
status_steht_in: docs/STATUS.md
basis_sha: e9f370f1
prioritaet: P1
anlass: "Yamas Freigabe 12.08.: 'ich gebe dir frei, du kannst in den Ordner, da sind die
         Ziegeltabellen mit Eindecklatten, bedien dich nach Hersteller Typ Format' —
         und unmittelbar danach seine Fachaussage zur Abhaengigkeit der Lattung."
ballbesitz: "plan-pruefer (DoR), danach generator"
claim: "planner 12.08. — Claim VOR dem Schnitt."
grundlage: "Yamas Fachaussage 12.08. · braas_dachziegel_datenbank_v14.xlsx, Blatt DB_Produkte
            · W-21L (OPERANDEN-GATE, wartet auf genau diesen Auftrag) · F-050 🟡"
```

## 1 · Yamas Fachaussage — sie ändert den Zuschnitt, und zwar grundlegend

**Wörtlich, 12.08.:**

> *„die eindecklattung ist abhängig von dach neigung und dach maße und zulässig überlappung der
> ziegel"*

**Das ist die Antwort auf die Frage, an der W-21L seit Tagen hängt — und sie lautet nicht „hier ist
die Tabelle".** *Die Lattweite ist **keine Konstante je Ziegelmodell**. Sie ist das Ergebnis einer
Rechnung, in die drei Größen eingehen:*

```text
1  DACHNEIGUNG        entscheidet, ob das Modell ueberhaupt zulaessig ist
                      (Regeldachneigung) und wie viel Ueberdeckung noetig ist
2  DACHMASS           die Sparrenlaenge Traufe->First muss in GANZE Reihen aufgehen
3  ZULAESSIGE          der Ziegel gibt einen BEREICH vor, innerhalb dessen man
   UEBERLAPPUNG       schieben darf — das ist das Verschiebespiel
```

> **Deshalb war die Suche nach „der Lattweite" von Anfang an falsch gestellt.** *Ein Auftrag, der
> eine Tabelle mit einem Wert je Modell erwartet, hätte den Operanden nie gefunden — auch wenn er
> vor ihm lag. **Die Tabelle liefert den Bereich, die Rechnung liefert den Wert.***

## 2 · Die Quelle — gefunden, geöffnet, gezählt

```text
Datei    ~/Desktop/Downloads_Aufgeraeumt_2026-05-22/01_Energie_PV_Waermepumpe/Tabellen/
         braas_dachziegel_datenbank_v14.xlsx        718.574 Byte
Blatt    DB_Produkte (sheet11 von 47 Blaettern)
Umfang   127 Datenzeilen · 48 Spalten
```

**Die Spalten, auf die es ankommt — sie sind genau die drei Größen aus Yamas Satz:**

| Spalte | Feld | Rolle in der Rechnung |
|---|---|---|
| 26 · 27 | `Lattmass_min_mm` · `Lattmass_max_mm` | **der erlaubte Bereich** |
| 32 | `Verschiebespiel_mm` | **die zulässige Überlappung** |
| 33 · 34 | `Regeldachneigung_grad` · `Dachneigung_min_system_grad` | **die Neigungsschranke** |
| 22–25 | `Decklaenge_min/max_mm` · `Deckbreite_min/max_mm` | Deckmaße für die Flächenrechnung |
| 37–39 | `Datenstatus` · `Quelle_1_URL` · `Quelle_2_URL` | **Herkunft je Datensatz** |
| 47 | `Eindeckmass_Text` | Klartextregel, wo vorhanden |

**Datenstatus über alle 127 Zeilen, gezählt:**

```text
78x  verifiziert aus PDF
26x  teilweise verifiziert
17x  verifiziert
 6x  offen
```

## 3 · Die Füllquote — und sie ist der wichtigste Befund dieses Blattes

```text
Datenzeilen                          127
mit Lattmass min UND max              9   (= 7 Modelle, zwei Zeilen doppelt)
mit Verschiebespiel_mm               13
mit Regeldachneigung_grad            17
```

> **Neun von 127.** *Die Datenbank ist gross, aber der Operand, den W-21L braucht, steht bei **sieben
> Modellen** — alle von **einem** Hersteller (Braas). **Das ist kein Mangel der Quelle, sondern die
> ehrliche Lage: sie wird gepflegt, und die Lattmasse sind der am wenigsten gepflegte Teil.***

**Die sieben, im Wortlaut aus der Tabelle:**

| Modell | Lattmaß | Verschiebespiel | Regeldachneigung | Datenstatus |
|---|---|---|---|---|
| Braas **Achat 12V** | 330–360 mm | 30 | 16° | verifiziert |
| Braas **Granat 11V** | 338–380 mm | 42 | 25° | verifiziert |
| Braas **Harzer Pfanne 7** | 372–405 mm | — | 22° | verifiziert |
| Braas **Rubin 13V** | 330–360 mm | 30 | — | teilweise verifiziert |
| Braas **Rubin 9V** | 370–400 mm | 30 | 16° | verifiziert |
| Braas **Topas 11V** | 320–380 mm | 60 | 25° | verifiziert |
| Braas **Topas 13V** | 320–360 mm | 40 | 25° | verifiziert |

## 4 · Ein Fund, der die Daten prüfbar macht — das Verschiebespiel ist redundant

```text
Achat 12V         360 - 330 = 30   Verschiebespiel 30   ✓
Granat 11V        380 - 338 = 42   Verschiebespiel 42   ✓
Rubin 13V         360 - 330 = 30   Verschiebespiel 30   ✓
Rubin 9V          400 - 370 = 30   Verschiebespiel 30   ✓
Topas 11V         380 - 320 = 60   Verschiebespiel 60   ✓
Topas 13V         360 - 320 = 40   Verschiebespiel 40   ✓
Harzer Pfanne 7   405 - 372 = 33   Verschiebespiel —    (fehlt, aus dem Bereich ableitbar)
```

> **Sechs von sechs stimmen.** *Das Verschiebespiel ist die Differenz zwischen maximalem und
> minimalem Lattmaß — es sagt dasselbe zweimal. **Und genau deshalb ist es wertvoll: eine
> Doppelangabe, die übereinstimmen muss, ist eine Prüfung.** Wo beide Werte stehen und nicht
> übereinstimmen, ist einer falsch — und wo einer fehlt, ist er ableitbar. Das ist die
> Eingangsprüfung dieses Werkzeugs, und sie kostet eine Subtraktion.*

## 4b · NACHTRAG 12.08. — die Quelle hat MODELL-DUBLETTEN, und der Name ist keine Adresse

*Anlass: der Evaluator hat den Bau auf `NACHBESSERN` gesetzt, weil eine Namenskorrektur des
Generators selbst der Fehler war. Beim Nachmessen ist ein größerer Befund aufgefallen.*

**Fünf Zeilen tragen „Harzer" im Modellnamen — nur EINE hat Lattmaße:**

```text
Zeile   9   'Harzer Pfanne'                  Variante —      Lattmass  fehlt
Zeile  10   'Harzer Pfanne 7'                Variante Big    Lattmass  372-405   <- die EINZIGE
Zeile  11   'Harzer Pfanne F+'               Variante —      Lattmass  fehlt
Zeile 102   'Braas Harzer Pfanne'            Variante —      Lattmass  fehlt
Zeile 104   'Braas Harzer Pfanne 7 (BIG)'    Variante —      Lattmass  fehlt
```

> **Wer mit „Harzer Pfanne" sucht, landet auf Zeile 9 — ohne Lattmaße.** *Genau das war die
> vorgeschlagene „Korrektur": der Modellname `Harzer Pfanne 7` sei falsch, in der Quelle stehe nur
> `Harzer Pfanne`. **Beides steht dort, und nur der längere Name trägt Werte.** Der Evaluator nennt
> es H-9 — richtiges Muster, falsche Zeile.*

**Und das ist kein Einzelfall. Über alle 127 Zeilen gemessen, Namen normalisiert (Braas-Präfix und
Klammerzusätze entfernt):**

```text
114 verschiedene Modellnamen bei 127 Zeilen
  8 Namen haben DUBLETTEN:
      5x  Opal Standard          davon mit Lattmass: 0
      3x  Rubin 11V              davon mit Lattmass: 0
      3x  Rubin 13V              davon mit Lattmass: 2
      2x  Frankfurter Pfanne     davon mit Lattmass: 0
      2x  Harzer Pfanne          davon mit Lattmass: 0
      2x  Harzer Pfanne 7        davon mit Lattmass: 1
      2x  Taunus Pfanne          davon mit Lattmass: 0
      2x  Topas 13V              davon mit Lattmass: 2
```

**Die Folge für das Werkzeug, und sie ist eine Zusage, keine Bemerkung:**

```text
DER MODELLNAME IST KEINE EINDEUTIGE ADRESSE.
  Ein Zugriff ueber 'Modell_Typ' allein kann auf eine Dublette OHNE Werte treffen.
  Adressiert wird ueber Modell_Typ PLUS Variante_Ausfuehrung — und wo auch das nicht
  eindeutig ist, ueber die ZEILE mit gefuellten Lattmassen.
  Bei fuenf der acht Dubletten traegt KEINE Zeile Lattmasse: dort gibt es keine
  Auswahl, sondern nur die Feststellung "kein Wert vorhanden".
```

> **Warum das der gefährlichere Befund ist als eine fehlende Zahl:** *eine leere Zelle merkt man. Eine
> **gefundene, aber leere Dublette** sieht wie ein Treffer aus — das Werkzeug meldet „Modell bekannt"
> und liefert keinen Bereich, und der Bauende sucht den Fehler in der Rechnung statt in der Adresse.*

## 5 · DECISION

```text
W-23 BESCHREIBT den Deckungskatalog als BEREICHSQUELLE, nicht als Wertetabelle:
     je Modell ein Lattmass-BEREICH, eine Neigungsschranke und die Deckmasse.

DIE RECHNUNG ist ENTSCHIEDEN — Vertretungsentscheid 12.08., Yamas Vollmacht
     ausdruecklich fuer DIESE Frage. Vollstaendig in
     docs/VERTRETUNGSENTSCHEID-F053-LATTMASS.md; hier die Fassung, die gilt:

     SCHRANKE  Dachneigung >= Regeldachneigung, sonst KEINE Rechnung
     TEILUNG   n_min = aufrunden(L / Lattmass_max)
               n_max = abrunden (L / Lattmass_min)
               n_min <= n_max  -> TEILBAR, Lattmass = L/n fuer jedes n im Bereich
               n_min >  n_max  -> KEINE gleichmaessige Teilung; die Formel gibt
                                  KEINEN Wert, sondern diesen Fall zurueck
     AMPEL     🟡 mit Geltungsbereich: rechnet die REGELFLAECHE. Traufreihe,
               Firstanschluss, Ortgang und der Restausgleich sind NICHT erfasst.

     DIE ERSTE FASSUNG DIESES BLATTES IST VERWORFEN. Sie lautete
     'n = aufrunden(L/max), Lattmass = L/n' — gemessen an den sieben Modellen und
     801 Sparrenlaengen je Modell (5.607 Faelle) liefert sie in 2,6 % bis 18,2 %
     einen Wert AUSSERHALB des Bereichs, und zwar leise. Beispiel Harzer Pfanne 7
     bei L=1000: sie rechnet 333,3 mm, der Ziegel erlaubt 372-405.
     Der Grund ist Teilbarkeit, nicht Fachwissen: zwischen zwei Reihenzahlen liegt
     eine Luecke — n=2 gibt 500 mm (zu gross), n=3 gibt 333 mm (zu klein).

QUELLE MIT HERKUNFT: jeder uebernommene Wert traegt Datenstatus und Quelle_1_URL mit.
     Ein Wert ohne Datenstatus wird NICHT uebernommen — das ist F-051s Lehre
     (TIME_VARS: vier Fundorte, null Quellen) angewandt, bevor der Fehler entsteht.

NICHT IN DIESEM AUFTRAG:
     - Kein Import in eine Datenbank. Kein Schema, keine Migration, kein Seeder.
       Die 127 Zeilen bleiben, wo sie sind; W-23 BESCHREIBT das Werkzeug.
     - Keine Ausweitung auf die 118 Zeilen ohne Lattmass. Wer fehlende Werte
       ergaenzt, erfindet sie — das ist der Fall, an dem W-21L blockiert ist.
     - Keine anderen Hersteller. Die Quelle traegt Braas; Creaton, Erlus, Jacobi
       stehen im SQL-Schema als Namen OHNE Masse.
```

## 6 · Was das für W-21L bedeutet — der eigentliche Zweck dieses Auftrags

```text
W-21L wartet laut Fahrplan auf "W-23s Ziegeltabelle" — OPERANDEN-GATE.
NACH diesem Auftrag ist die Lage:
  ENTSPERRT fuer SIEBEN Braas-Modelle mit verifiziertem Lattmass-Bereich
  WEITER GESPERRT fuer alles andere — und das ist RICHTIG, nicht bedauerlich:
  eine Lattung ohne belegten Bereich waere genau die erfundene Zahl,
  gegen die das Gate steht.
```

> **Damit ist W-21L nicht „entsperrt", sondern ZUSCHNEIDBAR:** *ein Auftrag über sieben belegte
> Modelle ist möglich; ein Auftrag über „die Lattung" ist es weiterhin nicht. **Der Unterschied
> gehört in W-21Ls Nachschnitt und ist eine Planner-Entscheidung, die nach diesem Auftrag fällt.***

## 7 · Abnahmekriterien

```text
W-23-1  (P1) Die sieben Blaetter von W-23 werden aus der QUELLE abgelesen, nicht
        entworfen — Datei, Blatt, Spaltennummern und Zeilenzahl stehen in 5-CODE.
        Gegenprobe: jede genannte Zahl ist in der Tabelle nachlesbar.

W-23-2  (P1, DER TRAGENDE PUNKT) 3-FORMELN traegt die ENTSCHIEDENE Fassung aus
        Abschnitt 5 — Neigungsschranke, n_min/n_max-Existenzpruefung, und den Fall
        "nicht gleichmaessig teilbar" als ECHTE Ausgabe statt als Zahl. Dazu Yamas
        Fachaussage woertlich als Grundlage und die Ampel 🟡 mit Geltungsbereich.
        AUSDRUECKLICH NICHT die verworfene erste Fassung: wer 'n = aufrunden(L/max)'
        allein einbaut, baut einen Fehler ein, der in bis zu 18,2 % der Faelle eine
        falsche Zahl liefert. Gegenprobe im Bericht: mindestens EIN Fall mit
        n_min > n_max, an dem das Werkzeug KEINEN Wert liefert.

W-23-3  (P1) 7-GRENZEN nennt die Fuellquote UNGESCHOENT: 9 von 127 Zeilen tragen ein
        vollstaendiges Lattmass, alle sieben Modelle sind von EINEM Hersteller.
        Und die Folge: das Werkzeug kann fuer andere Modelle NICHTS sagen.

W-23-4  (P1) Die Eingangspruefung aus Abschnitt 4 steht in 6-PRUEFUNG als Kriterium:
        Verschiebespiel == Lattmass_max - Lattmass_min. Rot-Beleg: die sechs Modelle,
        bei denen es aufgeht, und die Harzer Pfanne, bei der der Wert fehlt.

W-23-5  (P1) KEIN Wert ohne Herkunft: jede uebernommene Zahl traegt Datenstatus und,
        wo vorhanden, Quelle_1_URL. Gegenprobe: die Zahl der uebernommenen Werte ist
        gleich der Zahl der mitgefuehrten Statusangaben.

W-23-6  (must_preserve) resources/** und app/** byte-identisch — reine Doku-Stufe.
        Die Quelldatei auf dem Desktop wird NICHT verandert, nur gelesen.
        Kein Import, kein Schema, keine Migration, kein Seeder.

W-23-8  (P1, NEU 12.08.) Die Adressierung steht in 2-FUNKTION: Modell_Typ ALLEIN ist
        keine eindeutige Adresse. Acht Namen haben Dubletten, bei fuenf davon traegt
        KEINE Zeile Lattmasse. Adressiert wird ueber Modell_Typ plus
        Variante_Ausfuehrung, und die Zeile mit gefuellten Lattmassen entscheidet.
        Gegenprobe im Bericht: der Zugriff auf 'Harzer Pfanne' liefert KEINEN Bereich,
        der auf 'Harzer Pfanne 7' + Variante 'Big' liefert 372-405.

W-23-7  (P1, §3 wird BELEGT) Beide Orte nach ARBEITSREGELN §3, beide Zahlen genannt,
        Messung unmittelbar vor der ersten Aenderung.
```

## 8 · Rückweg & Entdeckung

```text
RUECKWEG      reiner Revert. Sieben Doku-Blaetter plus eine Registerzeile; kein Code,
              kein Datenpfad, keine Migration.
KOPIE AUSSERHALB DER MASCHINE  ZUM BAUZEITPUNKT ZU PRUEFEN. Heute steht der Transport
              (die Berechtigungssperre ist am 12.08. aufgeloest worden, c89e9096/6bbd337a).
ENTDECKUNG    das Signal ist die Fuellquote: nennt ein Blatt mehr als SIEBEN Modelle mit
              Lattmass, sind Werte hinzugekommen, die nicht in der Quelle stehen — dann
              ist W-23-5 gebrochen und jemand hat gerundet, geschaetzt oder erfunden.
```

## 9 · Konfliktprüfung §3 — unmittelbar vor dem Schnitt gemessen (H-4)

```text
Tafelzeile (Spalte 2)   ->  1   (A-19, Generator)
Zustandsfeld            ->  1   deckungsgleich
Index leer · STATUS.md im Arbeitsbaum unveraendert
SCOPE-UEBERSCHNEIDUNG mit A-19: keine. A-19 fasst ARBEITSREGELN.md an, W-23 die
        Werkbank-Blaetter und REGISTER.md.
ACHTUNG: REGISTER.md liegt im Scope MEHRERER W-Blaetter. Wer W-23 baut, prueft §3
        unmittelbar vor der Registerzeile erneut — die Lehre aus ce30174f.
W-23 wird auf ENTWURF geschnitten und nimmt keinen §3-Platz.
```

```yaml
zustand: ENTWURF
ballbesitz: "plan-pruefer (DoR)"
was_yama_bestaetigen_muss: "die Rechnung aus Abschnitt 5. Sie ist die Umsetzung SEINES Satzes,
       aber die Umsetzung ist meine — Reihenanzahl aufrunden und dann gleichmaessig teilen ist
       Handwerksregel, und ich habe sie abgeleitet, nicht abgelesen."
zweiter_posten: "118 der 127 Zeilen haben KEIN Lattmass. Das ist Pflegearbeit an der Quelle und
       gehoert Yama, nicht diesem Auftrag."
was_dieser_auftrag_entsperrt: "W-21L fuer SIEBEN Braas-Modelle. Nicht mehr, und das ist richtig."
```

## §11 — Votum W-23 (Evaluator, Erstinstanz, 12.08.2026)

```yaml
auftrag: "W-23"
votum: NACHBESSERN
fehlerklasse: BEWEIS
bau_commit: "2143c5db"
elter: "c2c6bf4e"
in_arbeit_commit: "39270fab"
pruefstand: "worktree --detach auf 2143c5db und c2c6bf4e"
zum_bau_commit: "Der Datensatz nennt 39270fab als Bau. Das ist der IN_ARBEIT-Commit (nur
     docs/STATUS.md); gebaut wurde in 2143c5db (elf Dateien). Ich habe gegen 2143c5db geprueft."
besonderheit: "Die Quelle liegt AUSSERHALB des Repos. Ich habe sie selbst geoeffnet, nicht
     seine Tabelle nachgelesen — das ist bei einer externen Quelle der einzige Weg."

messtisch_alle_sieben:
  W-23-1: ROT — die Zahlen stimmen, ein NAME nicht (s. befund)
    was_stimmt: "Ich habe die Quelle mit Bordmitteln selbst geoeffnet (zipfile + ElementTree)
            und jede Angabe aus 5-CODE nachgemessen:
              Groesse        718.574 Byte        stimmt
              sharedStrings  nicht vorhanden     stimmt (die Texte stehen inline)
              Blatt          sheet11.xml         stimmt
              Umfang         128 Zeilen, 48 Sp.  stimmt
              Lattmass-Zeilen 9 von 127          stimmt, alle Hersteller 'Braas'
              Modelle         neun Zeilen, sieben Modelle (Rubin 13V und Topas 13V je zweimal)
                                                 stimmt"
  W-23-2: GRUEN — und der Kern ist rechnerisch belegt
    beleg: "3-FORMELN traegt die entschiedene Fassung: SCHRANKE (Neigung >= Regeldachneigung),
            n_min/n_max mit Existenzpruefung, und 'n_min > n_max -> KEINE gleichmaessige
            Teilung, KEIN Wert' als eigener Fall. Die verworfene erste Fassung steht daneben,
            ausdruecklich als verworfen."
    selbst_nachgerechnet: |
      Harzer, L=1000, Bereich 372-405:
        n_min = aufrunden(1000/405) = 3
        n_max = abrunden (1000/372) = 2      ->  n_min > n_max, kein Wert          RICHTIG
      die verworfene Fassung:
        n = aufrunden(1000/405) = 3  ->  1000/3 = 333,3 mm  ->  AUSSERHALB 372-405 RICHTIG
    bewertung: "Der geforderte Fall mit n_min > n_max steht ausgeschrieben im Bericht, und
            ich habe ihn nachgerechnet statt ihn zu lesen. Beide Zahlen treffen."
  W-23-3: GRUEN
    beleg: "7-GRENZEN nennt '118 von 127 Zeilen' ohne Lattmass und den Satz 'fuer jedes andere
            Modell kann dieses Werkzeug NICHTS sagen'. Selbst gemessen: 9 mit, 118 ohne — und
            alle neun tragen Hersteller 'Braas'. Ungeschoent, wie verlangt."
  W-23-4: GRUEN
    beleg: "Die Eingangspruefung steht in 6-PRUEFUNG als K-Kriterium. Ich habe sie an der
            Quelle nachgerechnet, Zeile fuer Zeile:
              Achat 12V 360-330=30 · Granat 11V 380-338=42 · Rubin 13V HA 30 · OG 30
              Rubin 9V 400-370=30 · Topas 11V 380-320=60 · Topas 13V HA 40 · OG 40
              Harzer Pfanne 7  405-372=33  ->  Spiel FEHLT in der Quelle
            Acht gehen auf, bei der Harzer fehlt der Wert — genau die Rot-Lage des Kriteriums."
  W-23-5: ROT — dieselbe Stelle wie -1
    was_stimmt: "Alle neun Zeilen tragen Datenstatus (verifiziert / teilweise verifiziert) und
            Quelle. Die Zaehlung geht auf."
    was_nicht: "Ein uebernommener WERT — der Modellname — traegt die falsche Herkunft."
  W-23-6: GRUEN
    beleg: "resources/ und app/ 0 Dateien. Die Quelldatei ist unveraendert: 718.574 Byte,
            Zeitstempel 25. Maerz — der Bau hat nur gelesen."
  W-23-7: GRUEN
    beleg: "Am Elter des Baus steht W-23 an beiden Orten auf IN_ARBEIT (Tafel 1 / Feld 1).
            Reihenfolge stimmt: 39270fab 12:02:10, Bau 12:11:10."

befund_der_modellname:
  klasse: BEWEIS
  schwere: P1
  was_er_meldet: "Als eigene Abweichung (1): 'Das Blatt schreibt Harzer Pfanne 7. In der Quelle
        heisst es Modell_Typ = \"Harzer Pfanne\", Variante_Ausfuehrung = \"Big\" — eine 7 steht
        dort nicht.' Danach traegt 5-CODE:41 den Namen ohne die 7."
  was_ich_messe: |
    Die Quelle hat FUENF Harzer-Zeilen, und nur EINE traegt Lattmasse:
      Zeile   9  Modell_Typ 'Harzer Pfanne'                Z/AA leer
      Zeile  10  Modell_Typ 'Harzer Pfanne 7'  Var 'Big'   Z=372  AA=405   <- diese
      Zeile  11  Modell_Typ 'Harzer Pfanne F+'             Z/AA leer
      Zeile 102  'Braas Harzer Pfanne'                     Z/AA leer
      Zeile 104  'Braas Harzer Pfanne 7 (BIG)'             Z/AA leer
    Gezielt gesucht: die Zeile mit 372/405 ist Zeile 10, und ihr Modell_Typ lautet
    'Harzer Pfanne 7'. DIE 7 STEHT DORT.
  warum_das_zaehlt: "Seine Berichtigung dreht die Sache um: das AUFTRAGSBLATT hatte recht, und
        die Korrektur macht das Werkzeug-Blatt falsch. Wer spaeter mit 'Harzer Pfanne' in die
        Quelle geht, landet auf Zeile 9 — OHNE Lattmasse. Das ist genau die Verwechslung, die
        W-23-1 ('jede genannte Zahl ist in der Tabelle nachlesbar') verhindern soll."
  fehlerklasse_im_haus: "H-9, seine eigene frisch gebaute Regel: das Muster war richtig, es
        setzte an der falschen ZEILE an. Fuenf Kandidaten, einer davon traegt die Masse."
  behebung: "Zwei Stellen: 5-CODE:41 (Modellname) und 5-CODE:54-55 (die Abweichungsmeldung
        streichen oder umdrehen). Kein neuer Inhalt."

zweite_abweichung_bestaetigt:
  was_er_meldet: "'Rubin 13V hat KEINE Regeldachneigung — in beiden Zeilen', und das ist nicht
        kosmetisch, weil die Schranke des Werkzeugs genau gegen diesen Wert prueft."
  selbst_gemessen: |
    Achat 12V 16 · Granat 11V 25 · Harzer Pfanne 7 22 · Rubin 13V HA '' · Rubin 13V OG ''
    Rubin 9V 16 · Topas 11V 25 · Topas 13V HA 25 · Topas 13V OG 25
  bewertung: "Stimmt exakt, in beiden Zeilen leer. Und seine Folgerung trifft: ausgerechnet das
        Modell mit doppelter Datenlage kann die Schranke nicht passieren."

was_diesen_bau_heraushebt:
  - "Die Quelle ist wirklich gelesen worden, nicht zitiert: 5-CODE nennt Datei, Groesse, Blatt,
     Spaltennummern und je Modell die QUELLZEILE. Ich konnte jede Angabe an der Datei nachpruefen
     — das ist bei einer Quelle ausserhalb des Repos die einzige belastbare Form."
  - "Er meldet seinen eigenen ersten Fehlversuch: 'Mein erster Leseversuch setzte
     sharedStrings.xml voraus und brach ab; der zweite sieht nach.' Gemessen: die Mappe hat
     wirklich keine."
  - "Die verworfene Formelfassung steht MIT ihrer Fehlerquote im Blatt, statt weggelassen zu
     werden — 2,6 % bis 18,2 % ueber 5.607 gerechnete Faelle."

zusammenfassung: "Sechs von sieben tragen, und der fachliche Kern ist der staerkste Teil: die
     Teilbarkeitsfalle ist erkannt, ausgerechnet und im Blatt belegt — ich habe sie nachgerechnet
     und komme auf dieselben Zahlen. Zurueck geht der Auftrag wegen EINER Stelle: die als
     Abweichung gemeldete Namenskorrektur ist selbst der Fehler. Die Quelle hat fuenf
     Harzer-Zeilen, und die eine mit den Lattmassen heisst 'Harzer Pfanne 7'. Zwei Zeilen
     Korrektur, kein neuer Inhalt."

ballbesitz: generator
```

## §11 — Votum W-23 Runde 2 (Evaluator, Erstinstanz, 12.08.2026)

```yaml
auftrag: "W-23"
votum: ABGENOMMEN
fehlerklasse: KEINE   # ein P2, blockiert nicht
abnahme_commit: "53060551"
runde_1: "2143c5db, von mir NACHBESSERN (30cc04c5)"
pruefstand: "worktree --detach auf 53060551"
neues_kriterium: "W-23-8 kam vom PLANNER (3a94fe59), nicht vom Bauenden — §12.2 gewahrt.
     Ich habe es mitgemessen, weil §12.4 alle Kriterien der HEUTIGEN Fassung verlangt."

der_p1_ist_behoben:
  was_war: "Seine als Abweichung gemeldete Namenskorrektur war selbst der Fehler: die Zeile
        mit 372/405 heisst in der Quelle 'Harzer Pfanne 7'."
  jetzt: "Alle Blaetter tragen den richtigen Namen — 1-ZWECK:22, 3-FORMELN:36, 6-PRUEFUNG:16
        und :40, 5-CODE:41 je 'Harzer Pfanne 7' (Variante 'Big'). An der Quelle gegengeprueft."
  die_form_ist_die_richtige: "Der falsche Vermerk wurde NICHT geloescht, sondern als
        ZURUECKGEZOGEN stehen gelassen, mit Ursache und Wirkung — dieselbe Form, die ich bei
        A-15 als vorbildlich bezeichnet habe."
  seine_ursache_nachgemessen: "Er sagt: 'mein Ausleseskript kuerzte die Spalte auf 14 Zeichen,
        und Harzer Pfanne 7 hat 15'. Selbst gezaehlt: 'Harzer Pfanne' 13, 'Harzer Pfanne 7' 15.
        Bei Kuerzung auf 14 bleibt 'Harzer Pfanne ' — nach dem Strippen genau der falsche Name.
        Die Erklaerung traegt, und sie erklaert auch, warum die Variante 'Big' richtig war:
        drei Zeichen, nicht betroffen."
  und_er_nennt_die_klasse: "'H-9 an dem Tag, an dem ich die Regel formuliert habe.' Dieselbe
        Einordnung, die ich im Runde-1-Votum getroffen habe — unabhaengig."

messtisch_alle_acht_nach_§12_4:
  W-23-1: GRUEN   # Quellangaben unveraendert, Name jetzt richtig
  W-23-2: GRUEN   # n_min/n_max dreimal genannt, verworfene Fassung zweimal als verworfen
  W-23-3: GRUEN   # Fuellquote ungeschoent, unveraendert
  W-23-4: GRUEN   # Verschiebespiel-Kriterium steht, in Runde 1 an der Quelle nachgerechnet
  W-23-5: GRUEN   # neun Statusangaben fuer neun uebernommene Zeilen
  W-23-6: GRUEN   # resources/app 0 Dateien; Quelldatei unveraendert 718.574 Byte
  W-23-7: GRUEN   # am Elter des Nachbesserungs-Baus steht W-23 an beiden Orten auf IN_ARBEIT
  W-23-8: GRUEN in der Sache — P2 zum ORT (s. befund)

w23_8_zahlen_selbst_nachgemessen:
  seine_angabe: "114 verschiedene Modellnamen bei 127 Zeilen · 8 Namen mit Dubletten ·
        5 davon ohne jede gefuellte Zeile — Namen normalisiert (Braas-Praefix und
        Klammerzusaetze entfernt)."
  meine_messung: |
    verschiedene Namen  114   stimmt
    mit Dubletten         8   stimmt
    davon 0 gefuellt      5   stimmt
    und die acht einzeln, mit Zeilenzahl und Fuellstand:
      Frankfurter Pfanne 2/0 · Harzer Pfanne 2/0 · Harzer Pfanne 7 2/1 · Opal Standard 5/0
      Rubin 11V 3/0 · Rubin 13V 3/2 · Taunus Pfanne 2/0 · Topas 13V 2/2
    Alle acht Namen, alle Zahlen deckungsgleich.
  meine_eigene_falle: "Mein erster Lauf zaehlte DREI Dubletten statt acht — ich hatte die
        NORMALISIERUNG uebersehen, die er im Klammerzusatz ausdruecklich nennt. Beinahe haette
        ich 'die Zahlen stimmen nicht' gemeldet. Erst mit seiner Definition (Braas-Praefix und
        Klammern entfernt) kam ich auf dieselben acht. Sein Beleg war vollstaendig, mein Lesen
        nicht."

befund_p2_der_ort:
  klasse: BEWEIS
  schwere: P2
  was: "W-23-8 sagt woertlich: 'Die Adressierung steht IN 2-FUNKTION.' Gemessen steht sie in
        5-CODE/LIESMICH.md (:60, :71-77); 2-FUNKTION.md ist im Nachbesserungs-Commit
        0-mal angefasst und traegt 0 Treffer fuer Modell_Typ, Variante, Adresse oder Dublette."
  warum_kein_rot: "Die Sache ist vollstaendig belegt, und die vom Kriterium verlangte
        Gegenprobe steht ausgeschrieben: 'Zugriff Harzer Pfanne -> Zeile 9, Lattmass LEER,
        KEIN Bereich' gegen 'Harzer Pfanne 7 + Big -> Zeile 10, 372-405'. Es fehlt nicht der
        Inhalt, sondern der Ort."
  warum_der_ort_zaehlt: "2-FUNKTION ist das Blatt, das ein Bauender von Stufe 2 liest, wenn er
        die Eingaben umsetzt. Dort steht heute nur 'modellId muss ein Modell mit beiden
        Lattmassen sein' (:12) — nicht, dass der Name allein keine Adresse ist. Genau dieser
        Leser braucht den Satz."
  behebung: "Zwei Zeilen in 2-FUNKTION bei der modellId-Zeile, Inhalt existiert bereits."

zusammenfassung: "Acht von acht in der Sache. Der P1 ist behoben, und zwar in der besten Form:
     der falsche Vermerk steht als ZURUECKGEZOGEN da, mit Ursache (14-Zeichen-Kuerzung, von mir
     nachgezaehlt) und mit der Wirkung, die er gehabt haette. Die Zahlen des neuen Kriteriums
     habe ich alle acht an der Quelle nachgemessen — deckungsgleich, nachdem ich meine eigene
     Zaehlfalle behoben hatte. Ein P2 zum ORT der Adressierung: sie steht in 5-CODE statt in
     2-FUNKTION, wo das Kriterium sie verlangt und wo Stufe 2 sie braucht."

ballbesitz: release-pruefer
```
