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
            app/tools/faehigkeiten.ts:80 als Registry-Eintrag · zwei Wächtertests"
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

> **Der Bedienweg ist damit vollständig belegt und läuft über drei andere Werkzeuge:** *`faehigkeiten.ts`
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
W-31-2  (P1) Der Bedienweg steht VOLLSTAENDIG mit Fundstelle: enginePanels.ts:32
        Einfuhr, :380 engineId, :403 der Aufruf ueber alsPvEingabe, und
        faehigkeiten.ts:80 der Registry-Eintrag mit zustand 'verfuegbar'.
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
W_31_nimmt_keinen_paragraf3_platz: "ENTWURF, nicht IN_ARBEIT."
```
