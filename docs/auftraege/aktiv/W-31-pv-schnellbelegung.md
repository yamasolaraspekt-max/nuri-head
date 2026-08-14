# W-31 — PV-Schnellbelegung. Ein gesperrtes Werkzeug, dessen gebauter Teil nicht gesperrt ist

```yaml
auftrag: "W-31"
werkzeug: "W-31 PV-Belegung (vollständig)"
art: "STUFE 6 — Blatt schneiden, Ziel BESCHRIEBEN (Ablesung) für den GEBAUTEN Teil.
      Der Code EXISTIERT: geometry/pvBelegung.ts, 75 Z., und er ist ANGESCHLOSSEN."
spur: A
heimat_app: ticket
dor_beleg: "steht aus — plan-pruefer."
status_steht_in: docs/STATUS.md
basis_sha: 6ace6f3e
prioritaet: P2
ballbesitz: "plan-pruefer (DoR)"
claim: "planner 12.08. — Claim VOR dem Schnitt."
anlass: "Ich habe mehrfach behauptet, nach W-37 sei der Vorrat erschöpft und alles Weitere brauche
         eine Entscheidung Yamas. Diese Behauptung habe ich geprüft statt sie zu wiederholen — sie
         trägt nicht. W-31 steht auf LEER und die Registerzeile nennt den gebauten Code selbst."
grundlage: "geometry/pvBelegung.ts (75 Z., 3 Exporte) · app/dashboard/enginePanels.ts:32/:380/:403 ·
            app/tools/faehigkeiten.ts:80 als Registry-Eintrag · app/dashboard/fachFlaechen.ts:240-258
            als FUENFTE Stelle (nachgetragen nach 94bd30f8) · zwei Wächtertests"
```

## 1 — Der tragende Punkt: die Sperre trifft diesen Teil nicht

**Die Registerzeile sagt es selbst** (`REGISTER.md:98`, wörtlich):

```text
| W-31 | PV-Belegung (vollstaendig) | LEER | W-07, W-08, W-19 |
  gesperrt bis F-028 🟢 — autarke Schnellstufe gebaut (pvBelegung.ts,
  KEIN Azimut -> kein F-028-Fall) |
```

> **Zwei Aussagen in einer Zeile, und sie widersprechen sich nicht:** *die **vollständige** Belegung ist
> bis F-028 gesperrt — die braucht den Azimut, und F-028 liegt bei Yama. Die **Schnellstufe** ist
> gebaut und trägt **keinen Azimut**, ist also ausdrücklich **kein F-028-Fall**. **Eine Ablesung des
> gebauten Teils verletzt die Sperre nicht — sie macht die Grenze erst sichtbar.***

**Was hier NICHT geschieht:** *kein Bau an der vollständigen Belegung, keine Aufhebung der Sperre, kein
Azimut. Das Blatt beschreibt, was IST, und nennt die Sperre als Grenze.*

## 2 — Was das Werkzeug hält, und es ist klein

```text
pvBelegung.ts — 75 Zeilen, DREI Exporte (vor dem Scope gezaehlt):
  :10  PvEingabe          Eingabetyp
  :26  PvBelegung         Ergebnistyp
  :46  pvSchnellBelegung(e: PvEingabe) -> PvBelegung
```

**Und im Unterschied zu W-27/1 ist es ANGESCHLOSSEN** — *jede Stelle geöffnet:*

```text
enginePanels.ts:32    import { pvSchnellBelegung, type PvEingabe }
enginePanels.ts:380   engineId: 'engine-pv'
enginePanels.ts:403   berechne: (werte) => pvSchnellBelegung(alsPvEingabe(werte))
faehigkeiten.ts:80    { id: 'engine-pv', label: 'PV-Schnellbelegung',
                        gruppe: 'energie-pv', art: 'engine',
                        zustand: 'verfuegbar',
                        engineModul: 'geometry/pvBelegung' }
```

> **BERICHTIGT nach `94bd30f8`: hier stand „der Bedienweg ist damit vollständig belegt".** *Er ist es
> nicht — es gibt eine **fünfte** Stelle, siehe Abschnitt 3a. **Und mein Muster konnte sie nicht finden:**
> ich habe über die **Importe** von `pvBelegung` gemessen, und `fachFlaechen.ts` importiert nichts — es
> nennt Engine und Typen als **Strings** (`'engine-pv'`, `'PvEingabe'`, `'PvBelegung'`). **Eine Verdrahtung
> über Strings ist für ein Import-Muster unsichtbar**, dieselbe Klasse wie die NUR-QUELLE-Wächter in W-36
> und W-37, nur auf der Bedienseite. **Wer „vollständig" behauptet, braucht ein zweites Muster, das
> Strings erfasst.***

> **Der Bedienweg läuft über drei andere Werkzeuge:** *`faehigkeiten.ts`
> ist **W-36**, `enginePanels.ts` ist **W-37**, und `alsPvEingabe` (`:509`) ist einer der **acht
> Adapter**, deren Zahl W-37 zwei Runden gekostet hat. **W-31 ist der erste Beleg, dass die Stufe-6-Kette
> zusammenhängt:** wer W-31 abliest, liest die Naht, die W-36 und W-37 beschreiben, von der anderen
> Seite.*

## 3 — Die Grenze steht im Dateikopf und ist fachlich, nicht technisch

```text
pvBelegung.ts:6-7, woertlich:
  „GRENZE: Ertrag/Verschattung/Strings bleiben der Fach-Engine (wberechnung)
   vorbehalten — hier nur Geometrie/Anzahl/Leistung."
```

> **Das ist eine Aussage über die Arbeitsteilung zwischen zwei Apps** — *`wberechnung` ist Yamas
> Heizlast-/Fach-Rechnung, und der Dateikopf zieht die Linie: **hier Geometrie, dort Fachertrag.**
> Ein Blatt, das das nicht sagt, lässt die nächste Rolle glauben, W-31 rechne PV-Erträge.*

*Und der Kopf nennt die Herkunft: **„Yamas Abschnitt 7: Für PV muss man nicht das ganze Haus
modellieren."** Das ist eine Anforderung Yamas im Code, und sie erklärt, warum die Schnellstufe autark
neben der vollständigen Belegung steht statt als deren Vorstufe.*

## 3a — Die fünfte Stelle nennt eine Ausrichtung, die die Engine nicht entgegennimmt

**Nachgetragen nach `94bd30f8`, jede Stelle selbst geöffnet:**

```text
fachFlaechen.ts:252    Eingang „Ausrichtung und Neigung", Einheit °
pvBelegung.ts:10-24    PvEingabe hat SIEBEN Felder:
                       dachLaenge · dachBreite · modulBreite · modulHoehe
                       modulLeistung · randabstand? · modulabstand?
                       -> KEINE Richtung, KEINE Neigung
FachFlaeche.tsx:208    <h3>Eingangsgroessen ({flaeche.eingaenge.length})</h3>
           :210        rendert je Eingang ein EingangFeld
Dateikopf  :4-6        „Feldstruktur-Vorschau (deaktivierte Ein- und Ausgangsfelder
                       mit sichtbarem Grund)"
```

> **Das ist keine Erklärseite, sondern eine Feldvorschau** — *`FachFlaeche` rendert die Einträge als
> **Eingangsgrößen** mit deaktivierten Feldern. Der Nutzer sieht also ein Feld „Ausrichtung und Neigung
> (°)" für eine Engine, die **weder Ausrichtung noch Neigung entgegennimmt.***

**Und ich nenne es hier ausdrücklich NICHT einen Mangel** — *H-7, Ist ist nicht Soll. Der Dateikopf sagt
selbst „Vorschau … mit sichtbarem Grund": **eine Vorschau darf künftige Felder zeigen**, das ist ihr
Zweck. Was ich messe, ist die **Spannung**, nicht der Fehler:*

```text
Die Vorschau kuendigt eine RICHTUNG an.
Die Engine nimmt keine.
Und genau diese Richtung ist der Gegenstand von F-028 — der roten Sperre.
```

> **Für dieses Blatt folgt daraus genau eine Pflicht:** *`7-GRENZEN` nennt die Stelle und sagt, dass die
> angekündigte Ausrichtung heute **nicht** im Eingabetyp steht und an F-028 hängt. **Ob die Vorschau ein
> Feld zeigen soll, das an einer roten Sperre hängt, ist eine eigene Frage** — sie gehört nicht in eine
> Ablesung, und ich schneide sie hier nicht mit. Sie steht im Fuß als Vormerkung.*

## 4 — Scope

```text
W-31 IST   der GEBAUTE Teil: pvBelegung.ts mit seinen drei Exporten, der
           Bedienweg ueber enginePanels und den Registry-Eintrag, die
           Zwei-Orientierungs-Wahl, und die Grenze zu wberechnung.

W-31 IST NICHT
           die VOLLSTAENDIGE Belegung -> gesperrt bis F-028, bei Yama.
           Sie wird als GRENZE benannt und nicht beschrieben.
           Ertrag, Verschattung, Strings -> wberechnung, andere App.
           W-19 Sonne und Verschattung -> eigenes Werkzeug, LEER, haengt
           ebenfalls an F-028.
           faehigkeiten.ts -> W-36 (CODE_FERTIG). enginePanels.ts -> W-37.
           Beide werden nur mit Verweis genannt, nicht beschrieben.
           alsPvEingabe -> gehoert zu W-37s acht Adaptern.
```

## 5 — Abnahmekriterien

```text
W-31-1  (P1, TRAGEND) Das Blatt sagt, WELCHER Teil gesperrt ist und welcher nicht,
        mit der Registerzeile als Beleg: die VOLLSTAENDIGE Belegung haengt an F-028
        (Azimut, bei Yama), die SCHNELLSTUFE traegt keinen Azimut und ist kein
        F-028-Fall. Ohne diese Unterscheidung liest die naechste Rolle 'gesperrt'
        und laesst ein gebautes, angeschlossenes Werkzeug unbeschrieben — genau der
        Zustand, den dieses Blatt beendet.
W-31-2  (P1) BERICHTIGT nach 94bd30f8: mein Kriterium sagte 'der Bedienweg steht
        VOLLSTAENDIG' und nannte VIER Stellen. Es gibt eine FUENFTE, sie ist live,
        und sie ist nicht irgendeine.
        DIE VIER, die ich hatte: enginePanels.ts:32 Einfuhr, :380 engineId, :403 der
        Aufruf ueber alsPvEingabe, faehigkeiten.ts:80 Registry mit zustand
        'verfuegbar'.
        DIE FUENFTE, selbst nachgemessen: app/dashboard/fachFlaechen.ts:240-258,
        Eintrag fach-pv-module mit engine 'engine-pv', :248 typ PvEingabe und :255 typ
        PvBelegung — eingefuehrt von app/FachFlaeche.tsx und HausplanerStudio.tsx:18,
        also GERENDERT und nicht nur vorhanden.
        UND WARUM SIE DER KERN DIESES BLATTES BERUEHRT: unter ihren Eingaengen steht
        bei fachFlaechen.ts:252 'Ausrichtung und Neigung' in GRAD. Dieses Blatt sagt
        an seiner tragendsten Stelle 'kein Azimut' — wer den Bedienweg als vollstaendig
        beschreibt und diese Stelle auslaesst, laesst genau die eine Stelle weg, an der
        eine RICHTUNG steht.
        Am Bau-Stand zaehlen, keine Zahl aus diesem Blatt uebernehmen.
W-31-3  (P1) Die GRENZE zu wberechnung steht in 7-GRENZEN, woertlich aus
        pvBelegung.ts:6-7: Ertrag, Verschattung und Strings bleiben der Fach-Engine
        vorbehalten, hier nur Geometrie, Anzahl und Leistung. Das ist eine Aussage
        ueber die Arbeitsteilung zwischen zwei Apps und keine Feinheit.
W-31-4  Yamas Anforderung aus dem Dateikopf steht in 1-ZWECK: 'Fuer PV muss man nicht
        das ganze Haus modellieren'. Sie erklaert, warum die Schnellstufe AUTARK
        neben der vollstaendigen Belegung steht und nicht als deren Vorstufe.
W-31-5  Die drei Exporte mit Fundstelle (:10, :26, :46). Die Zahl ist am Bau-Stand
        zu erheben; sie steht hier, weil ich sie VOR dem Scope gezaehlt habe
        (Pflichtpruefung 7), nicht damit sie uebernommen wird.
W-31-6  Die Waechter, je mit Zugriffsart und Zusage — getrennt nach IMPORT und
        NUR QUELLE, wie in W-36 und W-37. Gemessen an den IMPORTZEILEN:
        pvBelegung.test.ts:6 und enginePanelRest.test.ts:18 importieren
        pvSchnellBelegung. faehigkeiten.ts nennt das Modul als STRING
        ('geometry/pvBelegung') ohne es zu importieren — das ist die
        Registry-Verdrahtung und keine Verriegelung.
        Keine Zahl im Kriterium: am Bau-Stand erheben.
W-31-7  Die Scope-Grenzen zu W-36, W-37 und W-19 stehen in 2-FUNKTION.
W-31-7b (P1) 7-GRENZEN nennt fachFlaechen.ts:252 und sagt: die dort angekuendigte
        'Ausrichtung und Neigung' in Grad steht HEUTE NICHT in PvEingabe (sieben
        Felder, keine Richtung) und haengt an F-028. Ohne diesen Satz behauptet das
        Blatt 'kein Azimut' und laesst die einzige Stelle weg, an der eine Richtung
        steht — und die naechste Rolle findet sie beim ersten grep auf 'Ausrichtung'.
        NICHT VERLANGT: eine Bewertung, ob die Vorschau das Feld zeigen darf. Eine
        Vorschau darf kuenftige Felder zeigen, das ist ihr Zweck (FachFlaeche.tsx:4-6).
        Das Blatt BENENNT die Spannung und entscheidet sie nicht.
W-31-8  Alle sieben Blaetter gefuellt, Gegenprobe `tail -n +2 <blatt> | md5` je Blatt,
        keine zwei Werkzeuge mit gleichem Hash.
```

**Nachweisform: Befehl und Trefferzeilen** (B5), **Fundstellen am Bau-Stand** (Pflichtprüfung 8),
**jede Zahl an zwei Mustern** (Pflichtprüfung 7), **Nachweis muss rot werden können** (Pflichtprüfung 4).

```yaml
warum_dieses_blatt_meine_eigene_behauptung_widerlegt: "Ich habe im Fuss von W-37 geschrieben, nach der
        siebten Ablesung trage jedes Werkzeug mit vorhandenem Code ein Blatt oder einen laufenden
        Auftrag, und alles Weitere brauche eine Entscheidung Yamas. Das habe ich geprueft statt es zu
        wiederholen — und es traegt nicht. W-31 hat gebauten, ANGESCHLOSSENEN Code und steht auf LEER.
        Die Ursache ist dieselbe wie bei AUF-40 heute: ich habe ein WORT gelesen (gesperrt) und nicht
        die Sache gemessen. Die Registerzeile sagt im selben Satz, dass die Schnellstufe gebaut und kein
        F-028-Fall ist."
was_die_messung_noch_ergab_und_hier_NICHT_geschnitten_wird: "Beim Pruefen der 19 LEER-Werkzeuge habe ich
        weitere dedizierte Module gefunden: geometry/geschossVorlage.ts, app/dashboard/geschossStapel.ts
        und GeschossFlaeche.tsx bei W-06 (Geschoss verwalten, LEER), renderers/three-d/deckenMesh.ts bei
        W-10 (Decke und Boden, LEER), geometry/abwassergefaelle.ts. Ich schneide sie NICHT mit — jedes
        braucht seine eigene Messung, und ein Sammelauftrag ueber fuenf Werkzeuge waere genau der
        Zuschnitt, der spaeter nicht abnehmbar ist. Sie stehen hier als Vorrat, nicht als Befund: dass
        eine Datei existiert, ist nach H-8 kein Beleg fuer ein beschreibbares Werkzeug."
warum_die_sperre_nicht_verletzt_wird: "Eine Ablesung beschreibt den Bestand. Die Sperre verhindert den
        BAU der vollstaendigen Belegung, die einen Azimut braucht und damit F-028. Dieses Blatt baut
        nichts, aendert nichts an pvBelegung.ts und nennt die Sperre als Grenze — es macht sie
        SICHTBARER als heute, wo sie in einer Registerzeile steht, die niemand liest, weil das Werkzeug
        LEER ist."
die_fuenfte_stelle_und_was_sie_ueber_mein_messen_sagt: "Ich habe den Bedienweg ueber die IMPORTE von
        pvBelegung gemessen — enginePanels und zwei Tests — und daraus 'vollstaendig' geschlossen. Die
        fuenfte Stelle importiert pvBelegung NICHT: fachFlaechen.ts nennt die Engine als STRING
        ('engine-pv') und die Typen als STRING ('PvEingabe', 'PvBelegung'). Mein Muster konnte sie nicht
        finden, und ich habe trotzdem VOLLSTAENDIG geschrieben. Das ist dieselbe Klasse wie die
        NUR-QUELLE-Waechter in W-36 und W-37, nur auf der Bedienseite: eine Verdrahtung ueber Strings
        ist fuer ein Import-Muster unsichtbar. Wer 'vollstaendig' behauptet, braucht ein zweites Muster,
        das Strings erfasst — und genau das verlangt Pflichtpruefung 7 seit heute."
vormerkung_und_ausdruecklich_KEIN_auftrag: "Die Vorschau kuendigt eine Ausrichtung an, die die Engine
        nicht entgegennimmt, und die Richtung ist der Gegenstand von F-028. Ob eine Feldvorschau ein
        Feld zeigen soll, das an einer roten Sperre haengt, ist eine Produktfrage und keine Ablesung.
        Ich schneide sie NICHT mit, weil sie erstens H-7 beruehrt — die Vorschau tut, was ihr Kopf sagt
        — und zweitens weil ein Auftrag daraus die Sperre selbst beruehren wuerde. Vorgemerkt, damit sie
        nicht in diesem Blatt verwaist."
zur_sperre_und_was_der_plan_pruefer_dazu_gemessen_hat: "Meine Praemisse traegt, und er hat sie
        unabhaengig geprueft: F-028 ist eine ECHTE, noch rote Sperre auf Yamas ausdrueckliche Auflage
        (FORMELSAMMLUNG.md, Anker "### F-028 · Azimut-Konvention an der Systemgrenze"; beim Schreiben
        am 12.08. stand sie auf :522, heute auf :557 — die Zahl BLEIBT hier als Beleg des
        damaligen Standes stehen, der Anker macht sie auffindbar), gesperrt fuer das
        DURCHREICHEN eines Azimut zwischen Kompass- und
        PVGIS-Konvention — und PvEingabe hat keine Richtung. Er hat dabei eine Wortfalle geprueft statt
        sie zu uebersehen: ein Muster auf 'Orientier' liefert VIER Treffer in pvBelegung.ts, und alle
        meinen hochkant gegen quer, also die Modul-LAGE und keine Himmelsrichtung. Wer nach dem Wort
        sucht, findet einen Azimut, der keiner ist. Heute faellt diese Falle zum ersten Mal ZUGUNSTEN
        eines Auftrags aus."
mein_beifang_in_f7c19bee: "Der plan-pruefer haelt fest, dass sein W-31-Beleg ungespeichert im Baum lag,
        als ich f7c19bee (W-06) committet habe, und mitgenommen wurde — Inhalt unveraendert, aber ohne
        Botschaft. Ich habe KEIN -A benutzt, sondern zwei Pfade genannt; der Beifang kam ueber
        docs/STATUS.md, in die wir beide schreiben. Das ist genau der Nebenlaeufigkeits-Befund, den ich
        Yama in Abschnitt 6 vorgelegt habe, jetzt an mir selbst: 317 Commits an einem Tag auf eine
        Datei, und der Pfad allein schuetzt nicht, wenn der Pfad geteilt ist. Es gehoert in die Vorlage
        als gemessener Fall und nicht als Vermutung."
W_31_nimmt_keinen_paragraf3_platz: "ENTWURF, nicht IN_ARBEIT."
votum_evaluator_13_08: "ABGENOMMEN — siehe Abschnitt 'Votum des Evaluators (§11)' am Ende."
```

## Votum des Evaluators (§11) — ABGENOMMEN

```yaml
votum: ABGENOMMEN
geprueft_am: "13.08.2026, evaluator"
bau_commit: "584e6a4d (23:02) — GESUCHT, der einzige. Sieben Dateien, alle neu, alle unter
  02-WERKZEUGE/W-31-pv-schnellbelegung/."
elter: "a4c7f415"
```

### Messtisch — jede Kriterienzeile eine Zeile

```text
W-31-1 (P1, TRAGEND)  ERFUELLT — und ich habe die Unterscheidung an der Quelle nachgerechnet
  Die Registerzeile selbst geoeffnet (REGISTER.md:98): "LEER | W-07, W-08, W-19 | gesperrt bis
  F-028 🟢 — autarke Schnellstufe gebaut". Das Blatt zitiert sie richtig.
  DIE UNTERSCHEIDUNG TRAEGT, weil ich sie NICHT aus dem Blatt genommen habe: PvEingabe hat
  SIEBEN Felder (dachLaenge, dachBreite, modulBreite, modulHoehe, modulLeistung, randabstand?,
  modulabstand?) — einzeln gezaehlt, KEINES davon eine Richtung. Und F-028 selbst nachgesehen:
  FORMELSAMMLUNG.md:557 "### F-028 · Azimut-Konvention an der Systemgrenze · 🔴" — Ampel rot.
  Gesperrt ist also das Durchreichen eines Azimut, und den gibt es hier nicht.
  DIE WORTFALLE MITGEPRUEFT: ein Muster auf "Orientier" liefert VIER Treffer in pvBelegung.ts
  (:5, :27, :45, :66) — alle meinen die Modul-LAGE hochkant/quer, keiner eine Himmelsrichtung.
  Wer nach dem Wort sucht, findet einen Azimut, der keiner ist.

W-31-2 (P1)           ERFUELLT — ALLE FUENF Stellen einzeln geoeffnet, jede Zeilennummer trifft
  enginePanels.ts:32   import { pvSchnellBelegung, type PvEingabe } ...
  enginePanels.ts:380  engineId: 'engine-pv'
  enginePanels.ts:403  berechne: (werte) => pvSchnellBelegung(alsPvEingabe(werte)) ...
  faehigkeiten.ts:80   { id: 'engine-pv', ... zustand: 'verfuegbar' ... }
  fachFlaechen.ts:240-258  Eintrag fach-pv-module, engine 'engine-pv',
                           :248 typ 'PvEingabe' · :255 typ 'PvBelegung'
  UND SIE IST WIRKLICH LIVE, von der anderen Seite belegt: FachFlaeche.tsx:34 und
  HausplanerStudio.tsx:18 fuehren fachFlaechen ein — also gerendert, nicht nur vorhanden.

W-31-3 (P1)           ERFUELLT
  pvBelegung.ts:6-7 sagt woertlich: "GRENZE: Ertrag/Verschattung/Strings bleiben der Fach-Engine
  (wberechnung) vorbehalten — hier nur Geometrie/Anzahl/Leistung." Steht so in 7-GRENZEN.

W-31-4                ERFUELLT
  1-ZWECK:10 traegt Yamas Satz woertlich als Zitat: "Für PV muss man nicht das ganze Haus
  modellieren." Am Quelltext gegengelesen — pvBelegung.ts:4 sagt denselben Satz.

W-31-5                ERFUELLT — die Zahl SELBST erhoben, nicht aus dem Blatt genommen
  grep -nE '^export ' geometry/pvBelegung.ts:
    :10 export interface PvEingabe · :26 export interface PvBelegung
    :46 export function pvSchnellBelegung
  DREI Exporte, genau auf den drei genannten Zeilen. Datei 75 Zeilen.

W-31-6                ERFUELLT — Zugriffsart je Waechter selbst gemessen
  pvBelegung.test.ts        import=1  readFileSync=0   -> reiner IMPORT
  enginePanelRest.test.ts   import=1  readFileSync=0   -> reiner IMPORT
  faehigkeiten.ts:80 nennt 'geometry/pvBelegung' als STRING und importiert es NICHT
  (grep auf eine Importzeile: 0 Treffer). Genau die Unterscheidung, die das Kriterium verlangt:
  Registry-Verdrahtung ist keine Verriegelung.

W-31-7                ERFUELLT
  2-FUNKTION:86-88 grenzt einzeln ab: W-36 fuehrt den Registry-Eintrag (faehigkeiten.ts:80),
  W-37 die Adapter (alsPvEingabe), W-19 Ertrag und Verschattung "ausdruecklich ausserhalb".

W-31-7b (P1)          ERFUELLT — der heikelste Punkt, und er ist sauber geloest
  7-GRENZEN:35-36 nennt fachFlaechen.ts:252 und zitiert die Zeile:
    { label: 'Ausrichtung und Neigung', einheit: '°' }
  :30 sagt "hat sieben Felder und keine Richtung" — von mir nachgezaehlt: sieben, stimmt.
  :43 haengt die Verwirklichung an F-028 (Ampel rot, selbst nachgesehen).
  UND ES BEWERTET NICHT, wie das Kriterium ausdruecklich verlangt: die Angabe steht in einer
  VORSCHAU (FachFlaeche.tsx:4-6 nennt sich selbst Feldstruktur-Vorschau), und eine Vorschau darf
  kuenftige Felder zeigen. Die Spannung ist benannt, nicht entschieden.

W-31-8                ERFUELLT — Gegenprobe wieder weiter gefasst als das Kriterium
  Sieben Blaetter: 1-ZWECK 76 · 2-FUNKTION 89 · 3-FORMELN 50 · 4-BEDIENUNG 78 ·
  5-CODE/LIESMICH 41 · 6-PRUEFUNG 54 · 7-GRENZEN 75 Zeilen, sieben verschiedene md5.
  Nicht nur W-31 gegen sich selbst geprueft, sondern ALLE 246 Blattdateien unter 02-WERKZEUGE:
    Blattnamen mit identischem Rumpf in mehreren Werkzeugen: 7   (Altbestand, unveraendert)
    davon betreffen W-31:                                    0

Suite / tsc           1750 / 1750 / fail 0, tsc exit=0. Der Bau fasst 0 Dateien unter
                      resources/ oder app/ an — reiner Dokumentationsbau.
Browser               NICHT GEFAHREN, mit Grund: kein Verhalten, kein Produktivcode.
§15                   Kein Schreibvorgang gegen eine Datenbank im Pruefumfang.
```

### Eine Beobachtung, die MEINE eigene vorige Abnahme betrifft

```text
REGISTER.md:390-398 nimmt meine W-06-Beobachtung auf und macht daraus einen strukturellen Befund:
alle acht damals offenen Ablesungsauftraege nannten das Register in KEINEM Kriterium, der Mangel
lag in den Auftragsblaettern und nicht in der Ausfuehrung. Das ist richtig gemessen und ich habe
es gegengeprueft — bei W-31 nennt ebenfalls kein Kriterium das Register.

DABEI EINE ZAHL, DIE ICH NACHGEZAEHLT HABE, weil sie MEINE Abnahme zitiert: der Abschnitt sagt zu
W-06 "die sieben Blaetter standen (468 Z.)". Ich messe:
  alle SIEBEN Blaetter zusammen        519 Zeilen
  insertions im Bau-Commit a05d6d12    427
  netto (427 - 157 deletions)          270
  die SECHS ohne 5-CODE/LIESMICH.md    468   <- das ist die genannte Zahl
Die Zahl ist also richtig, aber ihr Traeger fehlt: sie meint sechs von sieben Blaettern, nicht
"die sieben Blaetter". KEIN Befund gegen W-31 — der Satz steht in einem fremden Commit des
Planners. Ich melde es, weil es meine eigene Abnahme betrifft und weil es genau die Traeger-Frage
ist, die heute schon zweimal Thema war.
```

### Eigene Messfehler in diesem Durchgang

```text
KEINE, die ein Ergebnis verfaelscht haetten. Anzumerken: beim Suchen der enginePanels-Stellen habe
ich zuerst mit einem Pfadmuster gearbeitet, das zwei Verzeichnistiefen gleichzeitig abdeckte
(app/*/enginePanels.ts app/enginePanels.ts) — das lieferte Treffer, haette aber bei einer anderen
Ablage still nichts gefunden. Die Zeilennummern habe ich deshalb einzeln gegengelesen statt der
Trefferliste zu vertrauen.
```

**Weitergabe:** ABGENOMMEN → **Release-Prüfer**. Die Zahl-ohne-Träger im Register (`468 Z.`)
→ ohne Auflage, zur Kenntnis an den **Planner**.


---

## ⚠ VERMERK 14.08. — Zeiger in einem BETRIEBSBESTAETIGTEN Blatt: Beleg erhalten, Anker ergaenzen

**Anlass:** Der Plan-Prüfer meldete (`974d2bd2`), dass dieses Blatt **dieselbe rote Sperre mit
zwei verschiedenen Zahlen** nennt — `:522` in Abschnitt 240, `:557` in Abschnitt 276.

**Selbst am Entstehungsstand nachgemessen, und das dreht die Einordnung:** bei `b39f3845`
(12.08. 23:41) stand F-028 auf **`:522`**, bei `c11433d2` (13.08. 23:09) auf **`:557`**.
**Beide Zahlen waren zum Zeitpunkt ihres Schreibens richtig.** Das ist **Drift**, nicht die
Schnittfehler-Klasse aus W-10/1 und W-14/1. *Er hatte zuerst das Gegenteil vermutet und es nach
eigener Messung zurückgezogen — ohne die Messung am Entstehungsstand wäre es ein Fehlbefund
geworden.*

**Planner-Entscheidung, weil die Frage offen war (A-20-4 gegen A-34):**

> **Ein betriebsbestätigtes Blatt wird nicht umgeschrieben — aber ein Anker darf danebentreten.**
> Die alte Zahl **bleibt stehen** und behält, was sie ist: der Beleg eines Standes. **A-20-4 ist
> gewahrt.** Der Anker daneben stellt die Auffindbarkeit her — **A-34s Weg, der beides erhält.**
> **Was NICHT passiert: die Zahl gegen die heutige tauschen.** Das würde einen Beleg fälschen,
> der zum Zeitpunkt seiner Entstehung richtig war.

**Warum es trotz `BETRIEBSBESTAETIGT` zählt:** **die Sperre F-028 ist lebend und rot**, und der
Text benutzt die Zahl als **Fundstelle zum Nachschlagen**. Wer der alten Zahl folgt, landet in
einem anderen Formelblock. Die Datei ist seit dem Schnitt von 996 auf über 1.000 Zeilen gewachsen —
**zwei Zahlen für denselben Anker im selben Blatt sind unabhängig vom Wandern ein Mangel**,
sobald die Datei sich bewegt.

**Abschnitt 276 war bereits richtig gebaut** — er nennt Zahl **und** Ankertext. Das ist die Form,
die hier jetzt an beiden Stellen steht.
