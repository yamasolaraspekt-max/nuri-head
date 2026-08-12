# W-09 Stufe 1 — Treppe BESCHREIBEN. Das best-abgesicherte Werkzeug der Tafel, und es war nie geplant

```yaml
auftrag: "W-09/1"
werkzeug: "W-09 Treppe"
stufe: "1 von 2 — BESCHRIEBEN. Stufe 2 folgt als eigener Auftrag."
titel: "Sieben Module, 698 Zeilen, ZWOELF Zusagen — und DIN 18065 mitten drin"
spur: A
heimat_app: ticket
status: ENTWURF
status_steht_in: docs/STATUS.md
basis_sha: 65f3ece4
prioritaet: P1
anlass: "Yamas Freigabe 12.08. Punkt 2: 'Schneide es.' Es ist das LETZTE ungeschnittene
         Klasse-A-Werkzeug und blockiert den Abschluss von Stufe 1 des Fahrplans."
ballbesitz: "plan-pruefer (DoR), danach generator"
claim: "planner 12.08. — Claim VOR dem Schnitt."
muster: "W-01/1 … W-22/1 (neun Blaetter), plus die Fach-Gate-Form aus N-003"
```

## Warum es dieses Blatt zwei Tage zu spät gibt

**W-09 stand in meinem alten Fahrplan in der Messtabelle — und in keiner der drei Runden.**
Statt den Plan zu erweitern, habe ich `FAHRPLAN-KLASSE-A.md:148` geschrieben: *„NICHT IN A — W-09
(Treppe, 698 Z) war nie in den drei Runden."* **Weil die Lücke notiert war, sah sie erledigt aus.**

> *Yamas Hausregel daraus: **„Eine Notiz über eine Lücke ist kein Plan für die Lücke. ‚Nicht hier'
> ohne ‚sondern dort' ist ein offener Posten in Tarnkleidung."*** **Dieses Blatt ist das „sondern
> dort".**

## Ist-Zustand — gemessen, sieben Module in `geometry/`

```text
treppenBerechnung.ts   114 Z   6 Exporte   Auslegung + DIN-18065-Pruefungen
treppenTypen.ts        153 Z   4 Exporte
treppenBauarten.ts      38 Z   3 Exporte
treppe2D.ts             93 Z   4 Exporte
treppe3D.ts             74 Z   4 Exporte
treppeSvg.ts           142 Z   5 Exporte
treppeObjekt.ts         84 Z   4 Exporte
                      -----   --
                       698 Z  30 Exporte   (Summe stimmt mit der Grobzahl — erstmals bei
                                            einem Werkzeug dieser Groesse)
Registry-Werkzeug      'Treppe' / 'treppe'  VORHANDEN
Zusagen                ZWOELF Testdateien:
  enginePanelTreppe · treppe2D · treppe3D · treppeDomain · treppeFarbeParameter ·
  treppeKonsistenz · treppeObjekt · treppePlatzierung · treppeValidierung ·
  treppenBauarten · treppenBerechnung · treppenTypen
```

> **Zwölf Zusagen sind die stärkste Absicherung der ganzen Tafel** — W-21 hatte sechs, W-01 drei.
> *Darunter `treppeValidierung`, `treppeKonsistenz` und `enginePanelTreppe`: das Werkzeug ist nicht
> nur gerechnet, sondern gegen sich selbst und gegen seine Bedienoberfläche geprüft.* **Das Blatt
> beschreibt also kein Provisorium, sondern das reifste Stück Code im Register.**

## DER KERN — DIN 18065, und hier weiche ich von Yamas Auflage ab

**Yamas Auflage:** *„W-09 wird nach derselben Klasse behandelt wie N-003: keine Bestanden-Plakette,
solange die Rechnung nicht alle Nachweise der Norm führt."*

**Die Bedingung ist richtig. Aber sie ist bei der Treppe NICHT geprüft, und der Code sieht besser aus
als bei N-003:**

```text
N-003 / sparrenBerechnung   Dateikopf sagt SELBST, was fehlt:
                            "Einfeldtraeger, gleichmaessige Last, NUR senkrechte Komponente;
                             Wind, Mehrfeld, Knicken, Auflagerpressung, Lastkombinationen
                             bleiben dem Tragwerksplaner"
                            -> zwei von sechs. Die Plakette LUEGT. Antwort: klar NEIN.

W-09 / treppenBerechnung    Dateikopf nennt DREI Regeln plus Grenzmasse:
                            "die Pruefungen nach DIN 18065 (Schrittmass-, Bequemlichkeits-,
                             Sicherheitsregel; Grenzmasse je Nutzungsbereich)"
                            :46   bestanden: boolean;  // keine Pruefung mit Schwere 'fehler'
                            :112  bestanden: !p.some(x => x.schwere==='fehler' && !x.bestanden)
                            -> es UNTERSCHEIDET Schweregrade und fuehrt eine echte Pruefliste.
                            -> Antwort: OFFEN. Vielleicht fuehrt es alle Nachweise, die es
                               fuehren muss.
```

> **Deshalb MISST dieses Blatt die Frage, statt die Auflage anzunehmen.** *Wenn `treppenBerechnung`
> die Nachweise führt, die DIN 18065 für seinen Anwendungsfall verlangt, dann **lügt die Plakette
> nicht** — und ein Bau, der sie entfernt, würde eine korrekte Angabe löschen.* **Die Auflage bleibt
> gültig; sie greift nur, wenn die Messung sie auslöst.**
>
> *Das ist derselbe Grundsatz, den ich bei Empfehlung 3 der Dachweg-Vorlage gelernt habe: **erst
> messen, ob die Lücke existiert, dann den Auftrag schneiden.** Dort hatte A-10 sie längst
> geschlossen.*

## DECISION

```text
QUELLE       die sieben Module + die zwoelf Zusagen
1-ZWECK      aus dem Dateikopf: "Fuer eine Treppe braucht man nicht das ganze Gebaeude
             (Yamas Autark-Prinzip): Eingabe sind Hoehen + Flaeche."
             Dazu die drei Nutzungsbereiche wohnung | gebaeude | aussen — sie bestimmen
             die Grenzmasse und sind damit Fachrecht, nicht Komfort.
2-FUNKTION   die sieben Module trennen: Auslegung (treppenBerechnung) · Katalog
             (treppenTypen, treppenBauarten) · Darstellung (treppe2D, treppe3D,
             treppeSvg) · Objekt (treppeObjekt).
             VIER Schichten in einem Werkzeug — das ist neu und gehoert benannt.
3-FORMELN    nur F-Nummern. ACHTUNG: das Register nennt fuer W-09 KEINE Formel.
             Die Schrittmassregel (2s + a = 59..65 cm) ist eine NORMATIVE Groesse und
             gehoert nach dem N-Muster behandelt, nicht als F-Nummer.
             -> W-09/1-4 verlangt die Messung, KEINE Zuordnung aus dem Kopf.
7-GRENZEN    HIER sitzt der Kern (W-09/1-5): fuehrt die Rechnung alle Nachweise,
             die DIN 18065 fuer ihren Anwendungsfall verlangt — ja oder nein?
             Und was tut sie bei einer Steigung ausserhalb der Grenzmasse?
```

## Nicht-Ziele

- **Keine Änderung an `resources/**`.** Reine Doku-Stufe, wie alle W-xx/1.
- **Keine Plakette anfassen.** *Dieses Blatt **misst**, ob sie lügt. Der Bau ist ein eigener
  Auftrag und braucht das Messergebnis — und A-15s Klassifikation.*
- **Keine N-Nummer eintragen.** *Die Schrittmaßregel gehört wahrscheinlich in die N-Gruppe; das
  entscheidet der Planner nach dem Messergebnis, nicht der Bauende.*
- **Keine Aussage über die vierte Schicht.** *Dass ein Werkzeug Auslegung, Katalog, Darstellung und
  Objekt in sich trägt, ist zu benennen — ob das richtig ist, ist eine Architekturfrage.*

## Scope

```text
docs/rollenkette/werkbank/02-WERKZEUGE/W-09-treppe/1-ZWECK.md … 7-GRENZEN.md
docs/rollenkette/werkbank/02-WERKZEUGE/REGISTER.md   Reifegrad W-09 LEER -> BESCHRIEBEN
                                                     + die sieben Module als Fundstelle
```

## Wiederverwendungsprüfung (§5)

```text
neun W-xx/1-Blaetter     VORHANDEN — Struktur, Kriterienform, Rot-Form uebernehmen
N-003-Form               VORHANDEN — Muster fuer Geltungsbereich + Reichweitengrenze
                         bei einer normativen Rechnung
A-14                     VORHANDEN — liefert die MECHANIK (kein Urteilstext -> keine
                         Plakette). W-09 braucht sie NICHT zu bauen, nur zu kennen.
A-15                     VORHANDEN — klassifiziert die dreizehn Dateien; treppe2D,
                         treppe3D und treppenBerechnung sind drei davon.
                         -> W-09/1 liefert A-15 die FACHLICHE Messung fuer diese drei zu.
zwoelf Zusagen           VORHANDEN — Quelle fuer 6-PRUEFUNG. Kein Grenzfall muss erfunden
                         werden; treppeValidierung.test.ts ist die Liste.
```

## Auswirkungen (§5)

```text
API · Server · Schema · Migration · Bestandsdaten · Bundle   KEINE
Produktivcode                                                KEINER
Testdaten-Ziel · Datenbank                                   KEINES / NICHT BERUEHRT
Prozessbindung                                               ENTFAELLT
Fahrplan                                                     Stufe 1 wird abschliessbar —
                                                             W-09 war der letzte offene Schnitt
```

**Erstnutzer:** *A-15, das die Treppen-Dateien klassifizieren muss und dafür die fachliche Messung
aus `7-GRENZEN` braucht — und der Bau, der danach entscheidet, ob die Plakette bleibt.*

## Akzeptanzkriterien

**W-09/1-1 (P1, kein Platzhalter):** keiner in den sieben Blättern. *Zählweise: `grep -nE '<[^>]+>'`
ohne Längengrenze — **die Lehre aus W-07, wo `{2,40}` drei echte Treffer verschluckt hat**.*

**W-09/1-2 (P1, `3-FORMELN` nennt nur Nummern):** keine ausgeschriebene Formel.

**W-09/1-3 (P1, die vier Schichten sind benannt):** `2-FUNKTION` trennt Auslegung · Katalog ·
Darstellung · Objekt und nennt je Schicht die Module. *Sieben Module in vier Schichten ist die
komplizierteste Struktur der Tafel — ohne die Trennung ist das Blatt eine Aufzählung.*

**W-09/1-4 (P1, die Schrittmaßregel wird GEMESSEN, nicht zugeordnet):** Das Blatt sagt, welche
Rechenregeln im Code stehen und **ob sie F-Nummern oder normative Größen sind**. **Das Register nennt
für W-09 keine Formel** — es wird keine erfunden. *Nach `603eddc2` (sieben von zehn Zuordnungen
fielen) ist eine leere Formelspalte besser als eine geratene.*

**W-09/1-5 (P1, DER KERN — führt die Rechnung alle Nachweise?):** `7-GRENZEN` beantwortet **mit
Fundstelle**: welche DIN-18065-Regeln prüft `treppenBerechnung` (Schrittmaß, Bequemlichkeit,
Sicherheit, Grenzmaße je Bereich), **und welche der Norm prüft es nicht**. *Antwortform: eine Liste
geprüft/ungeprüft, keine Wertung.* **Und die Folgerung für die Plakette ausdrücklich:** trägt
`bestanden` eine vollständige oder eine Teilaussage?

**W-09/1-6 (P1, kein stilles Nichts bei Normverletzung):** Das Blatt sagt, **was das Werkzeug bei
einer Steigung außerhalb der Grenzmaße tut** — gemessen an `treppenBerechnung:80-112`. *Yamas
Auflage: Absage mit Wortlaut, kein Default.* **Falls es das schon richtig tut, sagt das Blatt das —
und die Auflage ist erfüllt, ohne dass etwas gebaut wird.**

**W-09/1-7 (P1, Zulieferung an A-15):** Das Blatt nennt für `treppenBerechnung`, `treppe2D` und
`treppe3D`, ob sie eine Norm nennen und was eine Verletzung bedeutet — **als Zulieferung, nicht als
Klassifikation.** *Die Klasse entscheidet A-15 nach Yamas Achse 2.*

**W-09/1-8 (P1, Herkunft in `5-CODE`):** „angebunden an" mit allen **sieben** Modulen und
Zeilenzahlen. *Nicht „die Treppen-Module" — die Namen.*

**W-09/1-9 (`must_preserve`):** `resources/**` byte-identisch, Insel-Suite **unverändert** grün (ohne
Zahl).

**W-09/1-10 (P1, Register mitgeführt):** Reifegrad **und** die sieben Module als Fundstelle.

**W-09/1-11 (P1, §3 wird BELEGT — als SCOPE-Messung):** Befehl mit Ausgabe, an beiden Orten,
**unmittelbar vor der ersten Änderung**. *Und die Messung fragt, **welche Dateien** der laufende
Auftrag hält — nicht, ob einer läuft. **Lehre aus Yamas Abschnitt 4: „§3 sperrt die Dateien im Scope
des laufenden Auftrags — nicht das Repo."***

## Kantenliste

```text
Steigung ausserhalb der Grenzmasse   -> W-09/1-6 misst, was heute passiert
Nutzungsbereich 'aussen'             -> andere Grenzmasse. Steht das im Code? messen
Geschosshoehe 0 oder negativ         -> messen, nicht annehmen
Treppe ohne Zielgeschoss             -> gehoert zu W-18 (Topologie), nicht hierher.
                                        Yama hat W-18 ausdruecklich behalten.
eine F-Nummer wird zugeordnet        -> VERBOTEN ohne Messung (W-09/1-4)
die Plakette wird "nebenbei" geaendert -> Nicht-Ziel. Erst messen, dann eigener Auftrag.
```

## Rückweg und Entdeckung

**Rückweg:** sieben Doku-Dateien und eine Registerzeile, `git revert` genügt.

**Entdeckung:** Zeigt sich später, dass `bestanden` bei der Treppe eine Teilaussage ist und die
Plakette trotzdem „alle" sagt, hat `W-09/1-5` die Frage nicht scharf genug gestellt. *Prüfbar am
Blatt: steht dort eine Liste geprüft/ungeprüft, oder nur „prüft nach DIN 18065"?*

## Konfliktprüfung (§5)

```text
§3 als SCOPE-Messung, unmittelbar:
  A-13   IN_ARBEIT   app/Models · app/Http · tests · database/factories   (PHP)
  W-09/1 DIESES      werkbank/W-09/** + REGISTER.md                       (Doku)
  -> disjunkt. REGISTER.md ist frei, solange A-13 laeuft.
A-14   ENTWURF   geometry/sparrenBerechnung · app/dashboard · app/EngineFlaeche  -> disjunkt
A-15   ENTWURF   nur ein Bericht                                                 -> disjunkt
W-07N · W-08/1 · W-13/1 · W-22/1   teilen REGISTER.md. §3 loest es; belegt in W-09/1-11.
```

```yaml
fehlerklasse: keine
prioritaet: P1
warteschlange: "sofort in DoR. Es ist der letzte offene Schnitt der Klasse A."
befund_1: "ZWOELF Zusagen — die staerkste Absicherung der Tafel. Das Blatt beschreibt das
           reifste Stueck Code im Register, nicht ein Provisorium."
befund_2: "die Grobzahl 698 stimmt aufs Wort mit der Summe der sieben Module. Erstmals
           bei einem Werkzeug dieser Groesse — bei W-08, W-04 und W-05 fiel sie."
abweichung_von_yamas_auflage: "Yama verlangt 'keine Bestanden-Plakette'. Ich MESSE zuerst,
   ob sie luegt: treppenBerechnung unterscheidet Schweregrade (:112) und fuehrt eine echte
   Pruefliste — anders als N-003, dessen Kopf selbst sagt, was fehlt. Wenn die Rechnung die
   Nachweise fuehrt, die sie fuehren muss, wuerde ein Bau eine KORREKTE Angabe loeschen.
   Die Auflage bleibt gueltig; sie greift, wenn die Messung sie ausloest."
```


## §11 — Bericht W-09/1 (Generator, 12.08.2026)

```yaml
auftrag: "W-09/1"
zustand: CODE_FERTIG
bau_commit: "d26d50b4"
in_arbeit_commit: "f8b0ee26"
basis: "65f3ece4"

kriterien:
  W-09/1-1:  GRUEN   # 28 -> 0 nach BEIDEN Zaehlweisen
  W-09/1-2:  GRUEN   # nur Nummern, keine ausgeschriebene Formel
  W-09/1-3:  GRUEN   # vier Schichten, sieben Module namentlich zugeordnet
  W-09/1-4:  GRUEN   # gemessen — und dem Auftrag an einer Stelle widersprochen, s. u.
  W-09/1-5:  GRUEN   # DER KERN, s. u.
  W-09/1-6:  GRUEN   # erfuellt OHNE Bau, s. u.
  W-09/1-7:  GRUEN   # Zulieferung an A-15, ohne Klassifikation
  W-09/1-8:  GRUEN   # alle sieben Module mit Zeilenzahl und Schicht
  W-09/1-9:  GRUEN   # 0/0/0, Suite 1693/1693 unveraendert
  W-09/1-10: GRUEN   # Register: BESCHRIEBEN + alle sieben Fundstellen
  W-09/1-11: GRUEN   # f8b0ee26: beide Orte, Befehl mit Ausgabe, als Scope-Messung

der_kern_W09_1_5:
  geprueft: "SIEBEN Regeln, jede mit Zeile — steigung-max 83 (fehler), auftritt-min 85 (fehler),
             schrittmass 87 (GESTAFFELT: 590-650 bestanden, 570-670 warnung, sonst fehler),
             bequemlichkeit 89 (warnung), sicherheit 91 (warnung), laufbreite 94 (fehler,
             nur wenn angegeben), durchgangshoehe 98 (fehler, nur wenn angegeben)"
  regel: "bestanden = !p.some(x => x.schwere === 'fehler' && !x.bestanden)"
  folgerung: "bestanden ist eine TEILAUSSAGE, und zwar aus ZWEI Gruenden:
              (1) Warnungen zaehlen nicht — eine verletzte Bequemlichkeits- oder Sicherheitsregel
                  laesst bestanden auf true;
              (2) zwei Pruefungen laufen nur bei vorhandener Eingabe — eine fehlende Laufbreite
                  wird nicht geprueft und laesst bestanden ebenfalls auf true."
  satz_im_blatt: "bestanden heisst 'keine der DURCHGEFUEHRTEN harten Pruefungen ist verletzt'
                  und NICHT 'die Treppe entspricht DIN 18065'."
  fuer_die_plakette: "'Alle Pruefungen bestanden' sagt ZWEIMAL mehr als die Rechnung weiss."

W-09_1_6_erfuellt_ohne_bau:
  gemessen: "treppenBerechnung.ts:83-99 — jede verletzte Regel erzeugt einen Pruefeintrag mit
             Klartext, Ist-Wert, Sollwert und Nutzungsbereich"
  beispiel: "'Steigung 205,0 mm > zulaessig 200 mm (wohnung).'"
  schluss: "kein Default, keine stille Korrektur. Yamas Auflage ist erfuellt, OHNE dass etwas
            gebaut wird — das gehoert gesagt, weil ein erfuellter Auftrag ohne Bau leicht wie
            ein uebersehener aussieht."

wo_ich_dem_auftrag_widerspreche:
  W-09/1-4: "der Auftrag sagt 'Das Register nennt fuer W-09 keine Formel'. Die Registerzeile
             nennt F-001 und F-030. Haette ich den Satz uebernommen, haette ich eine leere
             Spalte gemeldet, wo zwei Nummern stehen."
  gemessen: "F-001 steht im Code, aber NICHT in der Auslegung — treppe2D.ts:47 und
             treppeSvg.ts:125 (je Math.hypot); treppenBerechnung.ts 0 Treffer.
             F-030 ist als Aufruf nicht messbar."
  einordnung: "der Auftrag hat inhaltlich recht fuer die AUSLEGUNGSSCHICHT und unrecht ueber
               das Register. Beides steht im Blatt."

zulieferung_an_A15:
  treppenBerechnung.ts: "nennt DIN 18065 (Z.5, Z.58); Verletzung = Sturzgefahr"
  treppe2D.ts: "nennt DIN 18065 (Z.6, verweisend); zeichnet nur — bildet eine Verletzung ab"
  treppe3D.ts: "nennt KEINE Norm; Darstellung, keine Pruefung"
  ausdruecklich: "Zulieferung, KEINE Klassifikation. Die Klasse entscheidet A-15 nach Achse 2."

eigener_fehler_vor_dem_melden:
  was: "ich hatte enginePanels.ts:110 fuer engine-treppe zitiert; richtig ist 121"
  ursache: "MEINE EIGENE A-14-Aenderung hat die Datei um elf Zeilen verschoben"
  lehre: "wer eine Datei aendert, entwertet seine frueheren Zeilenangaben darin —
          eine Zeilennummer ist nur so lange gueltig wie die Datei unveraendert"
  danach: "16 Stellen inhaltlich geprueft, 0 falsch"

nicht_gemessen:
  - "ob DIN 18065 mehr verlangt als diese sieben Regeln — am Code ist messbar WAS geprueft wird,
     nicht WAS die Norm insgesamt fordert. Als Frage in 6-PRUEFUNG, nicht als Zusage."

browserabnahme: "entfaellt — reine Dokumentblaetter"
ballbesitz: evaluator
```
