# Schritt 1 · Feststellung: die Azimut-Konvention ist NICHT unklar — sie ist zweifach und korrekt gekennzeichnet

```yaml
art: "Feststellung nach Yamas Prompt 2, Schritt 1. KEIN Bau, keine Änderung an Code oder Daten."
auftrag: "PVRoof.roof_azimuth: Bezugsrichtung feststellen, Werte plausibilisieren, Ergebnis hinschreiben"
gemessen_am: "11.08."
basis_sha: ce30174f
scope_geprueft: "unmittelbar vor dem Schreiben: 0 IN_ARBEIT, Zieldatei in keinem Blatt-Scope"
teil_offen: "die DATEN-Plausibilisierung — Zugriff verweigert, braucht Yamas Freigabe (unten)"
```

## Die Kernfeststellung, und sie korrigiert die Auftragsprämisse

**Der Auftrag sagt:** *„`PVRoof.roof_azimuth` trägt seit 2024 keine dokumentierte
Bezugsrichtung."* — **Das trifft nicht zu, und der Unterschied ändert die Folgearbeit.**

```text
database/migrations/2024_06_04_103808_create_p_v_roofs_table.php:67
  $table->float('roof_azimuth')->nullable();   // 0=N, 90=E, 180=S, 270=W
```

**Die Bezugsrichtung IST dokumentiert — seit dem Tag der Migration, im Kommentar daneben.**
*Präziser gesagt: sie ist **dokumentiert, aber nirgends durchgesetzt und nicht dort sichtbar, wo man
das Feld benutzt.*** Das ist ein anderer Mangel als „keine Bezugsrichtung", und er ist billiger zu
heilen.

## Das Haus hat EINE Konvention, und sie ist durch Tests gesichert

```text
KOMPASS-KONVENTION  0 = Nord · 90 = Ost · 180 = Sued · 270 = West · Bereich 0..360
  Migration p_v_roofs:67          "// 0=N, 90=E, 180=S, 270=W"
  Migration heizlast_bauteile:22  "// 0=N,90=O,180=S,270=W"
  SzeneProjektionService:257      "Nord = +y = 0 Grad, Ost = 90 Grad"
  CanonicalBuildingModelValidator:24  "0 <= azimut < 360 (0=Nord)"
  Hausplaner szene.ts:60          "Kompass-Azimut der Sonne: 180 Grad = Sued"

  UND SIE IST ZUGESAGT, nicht nur beschrieben:
  BuildingModelSchemaContractTest:53   test_azimut_vertrag_0_bis_360()
  SzeneProjektionServiceTest:80        assertSame([0, 90, 180, 270], $azimute)
  GeometrieAbleitungReferenzTest:84    assertSame(180.0, …)   Sued = 180
```

> **Fünf Stellen dokumentieren dieselbe Konvention, drei Tests sichern sie.** *Das ist keine
> unklare Lage — das ist ein Hausstandard.*

## Die zweite Konvention ist die PVGIS-Grenze, und sie ist RICHTIG gekennzeichnet

```text
PVGIS-KONVENTION    0 = Sued · -90 = Ost · +90 = West · Bereich -180..+180
  PvgisErtragService:41    "@param float $aspect  Azimut nach PVGIS-Konvention:
                            0 = Sued, -90 = Ost, 90 = West"
  InverterSizingService:69 "Ausrichtungs-Label -> Azimut (Sued = 0 Grad, Konvention PVGIS)"
  energiekonzept.blade:394 <input min="-180" max="180" name="pv[aspect]">
  pvgis/index.blade:128    <input min="-180" max="180" value="0">
```

> **Das ist kein Fehler, sondern eine korrekt benannte Fremdschnittstelle.** *PVGIS **hat** diese
> Konvention; sie in unsere umzurechnen wäre falsch, sie unbenannt zu lassen wäre der Fehler — und
> sie ist an allen vier Stellen benannt.*

## Was WIRKLICH fehlt: die Umrechnung an der Grenze. Gemessen: sie existiert nicht

```text
grep -nE '\+ *180|180 *-|- *180|azimut.*180|aspect.*180'  app/Services/Energie/*.php
  -> KEIN Treffer.

Und roof_azimuth erreicht die Ertragsrechnung ohnehin nicht:
  grep -rn 'roof_azimuth' app/ --ausser Models  -> 0 funktionale Treffer
  aspect kommt aus  PVToolsController:115  (float) $request->get('aspect')
                    ToolsController:263    $request->aspect
  -> der Nutzer tippt den Winkel von Hand. Der gezeichnete Wert liegt daneben und
     wird nie gelesen.
```

## Der gefährliche Bereich — und warum der Fehler NICHT von selbst auffällt

```text
Wertebereiche ueberlappen:  Kompass 0..360   ·   PVGIS -180..+180
  Wert  270  -> im PVGIS-Feld NICHT eingebbar (max 180)  -> faellt auf
  Wert  -90  -> im Kompass-Sinn ungueltig (< 0)          -> faellt auf
  Wert 0..180 -> in BEIDEN Konventionen gueltig          -> FAELLT NICHT AUF
      0   Kompass NORD   ·  PVGIS SUED     180 Grad Fehler, maximaler Ertragsirrtum
     90   Kompass OST    ·  PVGIS WEST     180 Grad Fehler
    180   Kompass SUED   ·  PVGIS (Nord)   180 Grad Fehler
```

> **Genau die Werte, die ein deutsches Dach am häufigsten hat, liegen im doppelsinnigen Bereich.**
> *Ein Süddach trägt im Kompass `180`; gibt es jemand unverändert an PVGIS, rechnet PVGIS ein
> **Norddach**. Das ist nicht ein kleiner Fehler, es ist der größtmögliche — und **nichts schlägt
> an**, weil `180` in beiden Systemen eine gültige Zahl ist.*

## DRITTER FUND, nicht im Auftrag und der teuerste für Schritt 5

```text
scene.types.ts:325   firstAzimutGrad: number;   // First-RICHTUNG (Grad);
                                                // Flaechen-Azimute werden daraus abgeleitet
scene.types.ts:280   "Die Flaechen-Azimute werden NIE gepflegt, sondern aus firstAzimutGrad" abgeleitet
```

> **`firstAzimutGrad` ist NICHT der Dachazimut.** *Der First läuft **entlang** des Dachs, die Fläche
> schaut **senkrecht dazu**.* **Wer `firstAzimutGrad` als Dachausrichtung an PVGIS gibt, hat 90°
> Fehler — zusätzlich zu den 180° aus der Konventionsverwechslung.** *Und der Hausplaner speichert
> **nur** die Firstrichtung; die Flächenazimute existieren als abgeleitete Werte, nicht als Feld.
> **Die Brücke aus Schritt 5 muss also ableiten, nicht durchreichen.***

## Was ich NICHT messen konnte, und warum

**Die Datenplausibilisierung („bestehende Werte gegen bekannte Dächer") ist NICHT gelaufen.** *Der
Zugriff auf die Produktivdatenbank wurde verweigert, als ich `php artisan tinker` mit reinen
`count()`/`min()`/`max()`-Abfragen versuchte.* **Ich habe es nicht umgangen.**

```text
WAS ICH STATTDESSEN GEMESSEN HABE, als Ersatz:
  Factory fuer roof_azimuth      keine
  Seeder                         keiner
  Test                           KEINER
  Validierung (Request/Controller) KEINE
  Konventionshinweis am Model    KEINER (nur 'roof_azimuth' in $fillable)
  Eingabefeld in einer View      KEINES — der einzige Treffer
                                 (deal_measurements:2535) reicht den Wert nur durch
  -> roof_azimuth hat KEINE einzige Stelle, die seine Konvention durchsetzt oder
     zusagt. Es ist das einzige der vier Azimut-Felder ohne Test.
WAS DIE DATENMESSUNG NOCH BEANTWORTEN MUESSTE:
  1  wie viele Zeilen tragen ueberhaupt einen Wert (NOT NULL)?
  2  liegt ein Wert unter 0 oder ueber 180?   -> beweist, welche Konvention benutzt wurde
  3  Haeufung bei 180 (Sued, Kompass) oder bei 0 (Sued, PVGIS)?
FREIGABE, die ich brauche: ein LESENDER Zugriff auf p_v_roofs.roof_azimuth.
  Vorschlag: du fuehrst die drei SELECTs selbst aus, oder du gibst mir den Weg frei.
  Ich schreibe nichts in diese Datenbank — die Feststellung braucht nur Zaehlungen.
```

## Ergebnis der Feststellung

```text
1  Die Bezugsrichtung von roof_azimuth IST dokumentiert: 0=Nord, Kompass, 0..360.
   Sie stimmt mit dem Hausstandard ueberein (5 Stellen, 3 Tests).
2  Sie ist NIRGENDS durchgesetzt und am Model nicht sichtbar. roof_azimuth ist das
   einzige Azimut-Feld im Haus ohne Test.
3  Die PVGIS-Konvention (0=Sued, -180..180) ist eine korrekt benannte Fremdgrenze,
   kein Fehler.
4  Es fehlt die UMRECHNUNG an dieser Grenze — gemessen 0 Treffer. Und sie wird
   heute nicht gebraucht, weil der gezeichnete Wert die Ertragsrechnung gar nicht
   erreicht; der Nutzer tippt von Hand.
5  Der doppelsinnige Bereich ist 0..180 — genau dort liegen deutsche Daecher.
   Eine Verwechslung erzeugt den GROESSTMOEGLICHEN Fehler und schlaegt nirgends an.
6  firstAzimutGrad ist die FIRST-Richtung, nicht die Flaechenausrichtung (90 Grad).
   Die Bruecke muss ableiten, nicht durchreichen.
```

> **Für Schritt 5 ist das eine gute Nachricht: die Brücke braucht keine Klärung, sondern eine
> Umrechnung mit einer Zusage.** *Das ist deutlich kleiner als „zwei Systeme mit unklarer Konvention
> verbinden" — und die Warnung im Auftrag („schlimmer als sie getrennt zu lassen") bleibt richtig,
> greift aber jetzt an einer benennbaren Stelle: **eine Funktion, zwei Zusagen (Süd→0, Ost→−90), und
> die Ableitung First→Fläche daneben.***

```yaml
schritt_1_ergebnis: "Konvention festgestellt: 0=Nord, Kompass, Hausstandard. Nicht unklar,
                     sondern undurchgesetzt."
schritt_1_offen: "die Datenplausibilisierung — braucht lesenden DB-Zugriff (Freigabe Yama)"
auftragspraemisse_korrigiert: "'keine dokumentierte Bezugsrichtung' trifft nicht zu — sie steht
                               im Migrationskommentar von 2024"
neuer_fund_fuer_schritt_5: "firstAzimutGrad ist die FIRST-Richtung; die Bruecke muss den
                            Flaechenazimut ABLEITEN. Sonst 90 Grad Fehler zusaetzlich."
kein_bau: "keine Datei in app/ oder resources/ angefasst, keine Daten gelesen oder geaendert"
```
