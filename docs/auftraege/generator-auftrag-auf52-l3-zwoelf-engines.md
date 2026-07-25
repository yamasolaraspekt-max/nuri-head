# ⇒ GENERATOR-AUFTRAG AUF-52 — L3: die übrigen zwölf Rechen-Engines

**Vom:** Planner · **25.07.2026** · **Grundlage:** AUF-33 L2 ist abgenommen (`9d0c12a`, Votum `7293a2d`).
Das Muster steht und ist unabhängig bestätigt — auch von der Playwright-Prüfung, die die laufende
Treppen-Fläche gemessen hat: *„die UI ruft die vorhandene Engine auf und rechnet nicht selbst."*

**Vorher gelesen:** HEAD `ab7f2c1` · `git log -5` · Tafelzeile AUF-52 (§3a) ·
`generator-auftrag-auf33-engine-panels.md` (§3 Grenzen, §5 Kriterien) ·
`app/tools/faehigkeiten.ts` · `app/EngineFlaeche.tsx` · `app/dashboard/enginePanels.ts`.

**Dieser Auftrag erfindet nichts Neues.** Er wiederholt zwölfmal, was einmal abgenommen wurde.
Wo er von AUF-33 abweicht, ist es ausdrücklich vermerkt — sonst gilt AUF-33 unverändert weiter.

---

## 1. Gemessener Bestand: alle dreizehn Module existieren

| Engine | Gruppe | Modul · Export |
|---|---|---|
| `engine-sparren` | dach-zimmerei | `geometry/sparrenBerechnung.berechneSparren` |
| `engine-holzmengen` | dach-zimmerei | `geometry/holzMengen.holzMengenAusListe` |
| `engine-holzbauteile` | dach-zimmerei | `geometry/holzBauteile.holzBauteileAusListe` |
| `engine-schifter` | dach-zimmerei | `geometry/schifterListe.klassifiziereSchifter` |
| `engine-fbh` | tga-heizung | `geometry/fbhAuslegung.fbhAuslegung` |
| `engine-heizkoerper` | tga-heizung | `geometry/heizkoerperLeistung.bewerteDeckung` |
| `engine-heizkreis` | tga-heizung | `geometry/heizkreisVerteiler.auslegeVerteiler` |
| `engine-uwert` | bau | `geometry/wandaufbau.berechneUWert` |
| `engine-fensterprodukt` | fenster-tuer | `geometry/fensterProdukt.berechneUw` |
| `engine-abwasser` | sanitaer | `geometry/abwassergefaelle.pruefeAbwasser` |
| `engine-kueche` | kueche | `geometry/kuecheArbeitsdreieck.bewerteArbeitsdreieck` |
| `engine-pv` | energie-pv | `geometry/pvBelegung.pvSchnellBelegung` |

**Alle zwölf Module sind vorhanden** (geprüft, Datei für Datei). `engine-treppe` ist bereits
angeschlossen und bleibt das Vorbild.

## 2. Drei Gruppen, drei Abnahmen — nicht zwölf auf einmal

| Scheibe | Engines | warum diese Reihenfolge |
|---|---|---|
| **A · dach-zimmerei** | Sparren · Holzmengen · Holzbauteile · Schifter (**4**) | vier Engines derselben Gruppe, gemeinsame Eingangsdaten (Holzliste, Dachfläche). Wenn das Muster viermal trägt, trägt es |
| **B · tga-heizung** | FBH · Heizkörper · Heizkreis (**3**) | dein Kerngeschäft; sie hängen fachlich voneinander ab (Heizlast → Auslegung → Verteiler) |
| **C · der Rest** | U-Wert · Fensterprodukt · Abwasser · Küche · PV (**5**) | einzelstehend, keine Reihenfolge untereinander |

**Jede Scheibe ist ein eigener Commit und wird eigens abgenommen.** Scheibe B beginnt erst, wenn A
abgenommen ist. Grund ist nicht Vorsicht, sondern Nutzen: was bei vier Engines schiefgeht, will man
nicht zwölfmal gebaut haben.

## 3. Die drei Grenzen aus AUF-33 §3 gelten unverändert

**(a) Keine Rechenlogik im Panel.** Kein Grenzwert, keine Formel, keine Rundung in der Fläche.
Jede Zahl entsteht in der Engine. **Jede Zahl, die im Panel entsteht, ist ein Defekt** — und nun
einer, der sich zwölffach vervielfältigt.

**(b) Kein dynamischer Import.** `engineModul` ist eine Deklaration, kein Ladepfad. Die Zuordnung
id → `{ felder, aufruf }` bleibt statisch, damit der Bundler alles sieht.

**(c) Kein Schreiben ins Modell.** Rechnen und zeigen. Persistenz ist weiterhin ein eigener,
noch nicht beauftragter Posten.

**(d) Kein zweiter Zustandsbegriff.** Eine Engine schaltet auf `verfuegbar`, **wenn sie wirklich
angeschlossen ist** — nicht vorher, nicht gruppenweise auf Vorrat.

## 4. Was bei zwölf Engines anders ist als bei einer

**Die Eingabefelder unterscheiden sich stark.** `berechneTreppe` nimmt sechs Zahlen; `holzMengenAusListe`
nimmt eine **Liste**, `pvSchnellBelegung` eine **Fläche**. Wo der Eingang nicht aus einfachen Feldern
besteht:

- **Nicht mit Platzhaltern füllen.** Eine Engine, deren Eingang sich heute nicht aus dem Modell oder
  aus Feldern bilden lässt, wird **zurückgegeben** und bleibt `in_entwicklung` — mit der Angabe, was
  fehlt. Das ist ein gültiges Ergebnis dieses Auftrags, kein Versagen.
- **Erwartung:** Von den zwölf werden nicht alle zwölf anschließbar sein. Der Bericht nennt die Zahl
  und die Gründe. Ein Auftrag, der zwölf von zwölf meldet, ist verdächtiger als einer, der neun meldet
  und drei begründet zurückgibt.

**Die Ergebnisdarstellung bleibt dieselbe Hülle.** Zahlen mit Einheit und Klartext-Label, darunter die
Prüfliste mit drei Schweregraden, `fehler` nicht allein durch Farbe unterschieden. `bestanden: false`
bleibt ein gültiger Zustand mit sichtbaren Zahlen.

## 5. Abnahmekriterien (je Scheibe)

1. `tsc:hausplaner` · `schema:hausplaner:check` · `test:hausplaner` — Exit 0, Zahlen vorher/nachher.
2. **K4 unberührt:** `store/`, `domain/`, `renderers/`, `scene.types` — **und `geometry/`**: die Engines
   werden gelesen, nicht geändert. Null Zeilen Diff in jedem `geometry/*`-Modul.
3. **Keine Rechnung im Panel:** `grep` belegt für jede neue Fläche, dass weder Grenzwerte noch
   Rundungen noch Normkonstanten vorkommen.
4. **Wertgleichheit gegen die Engine:** je Engine ruft ein Test die Funktion direkt und vergleicht mit
   dem, was die Fläche anzeigt — **mindestens drei Eingaben, davon eine mit einem verletzten Prüfpunkt**,
   wo die Engine Prüfungen liefert.
5. **Kein dynamischer Import:** `grep` belegt, dass kein `import(` mit Variable vorkommt.
6. **`verfuegbar` genau für das Gebaute:** testverriegelt, dass die Zahl der `verfuegbar`-Engines
   **exakt** der Zahl der angeschlossenen entspricht — nach Scheibe A also 5, nach B 8, nach C 13
   **minus** der begründet zurückgegebenen.
7. **Zurückgegebene Engines sind benannt und bleiben `in_entwicklung`** — mit einem Grund, der weder
   leer ist noch auf „folgt"/„in Kürze" endet (Muster AUF-25).
8. **Mutations-Gegenbeweis je Scheibe:** eine Feldzuordnung verfälschen ⇒ mindestens ein Test rot.
   Zahl nennen.
9. **`public/*` im Code-Commit: null Zeilen.** Bundle-Rebuild als eigener, zweiter Commit
   (§8 Punkt 2b in `docs/agents/06-laufzeiten-und-takt.md`).
10. **Klassifikation: `sichtbar`.** Rebuild-Beleg (`grep -c` auf eine neue Zeichenkette) im Bericht,
    Sichtprobe in die Abnahme — bei 1440 **und 375 px**, weil die Ergebnistabellen schmal werden müssen.

## 6. Was zurückgegeben wird statt mitgebaut

- **Persistenz der Ergebnisse** — weiterhin nicht Teil dieses Postens.
- **Jede Engine ohne bildbaren Eingang** — benennen, nicht behelfen (§4).
- **Fachliche Abhängigkeiten**, die auffallen (Heizlast → Auslegung → Verteiler): melden. Sie sind der
  Anfang der Reihenfolge, in der ein Planer diese Flächen später wirklich benutzt — und damit der
  Anfang eines eigenen Postens, nicht dieses.
