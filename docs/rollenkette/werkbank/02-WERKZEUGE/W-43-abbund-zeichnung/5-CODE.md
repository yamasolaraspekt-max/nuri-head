# W-43 · Abbund-Zeichnung — CODE / LIESMICH

## Der Vertrag

```text
geometry/dachformVorlagen.ts
  :98-110   interface VorlagenZimmerer
              dachstuhltyp · flags · querschnittSparrenCm
              querschnittPfetteCm? · querschnittGratsparrenCm?
              materialFestigkeit · holzfeuchteProzent · sparrenabstandCm
              abbundhinweis · spannweiteHinweis · lastabtragsweg
  :107      abbundhinweis: string
  :91-96    ZimmererFlags (13 Flaggen)  -> in W-25 gemessen
```

## Die dreizehn Werte, mit Zeile

```text
:1530  'Sparren auf Pfetten abgebunden; Aufschiebling am Traufueb…'
:1598  'Tragdecke mit Gefaelledaemmung; kein Sparrendach.'
:1664  'Tragdecke mit Gefaelledaemmung; kein Sparrendach. Entwaesse…'
:1719  'Sparren auf First-/Fusspfette geklinkt (Kerve), Aufschieb…'
:1753  'Steile Neigung -> hoehere Laengskraefte; Stuhl/Streben zur A…'
:1790  'Trapezblech-Auflager: Pfettenabstand auf Profil-Tragweit…'
:1836  'Sparren ueber volle Breite, hohe Wand traegt First-, niedr…'
:1881  'Ziegel-Auflattung auf Konterlattung; Sparren ueber volle …'
:1917  'Gratsparren als 3D-Laenge sqrt(dx²+dy²+dz²); Schifter an Grat…'
:1958  'Tragdecke mit Gefaelledaemmung; kein Sparrendach.'
:1991  'Wurzelfeste Abdichtung; Substrat-/Ballastauflast statisc…'
:2054  'Geplant — Abbund erst nach sauberer Geometrie-/Tragwerks…'
```

## Die Befehle, die dieses Blatt belegen

```sh
# Keine Zeichnung
for w in abbund zimmerer Zimmerer werkplan Werkplan; do
  grep -rn "$w" resources/planner/hausplaner/renderers/ | wc -l
  grep -rn "$w" resources/planner/hausplaner/app/       | wc -l
done            #  -> zehnmal 0

# Kein Leser fuer die elf Felder
for f in dachstuhltyp querschnittSparrenCm querschnittPfetteCm \
         querschnittGratsparrenCm materialFestigkeit holzfeuchteProzent \
         sparrenabstandCm abbundhinweis spannweiteHinweis lastabtragsweg; do
  grep -rn "$f" resources/planner/hausplaner --include='*.ts' --include='*.tsx' \
    | grep -v '__tests__' | grep -vc 'geometry/dachformVorlagen.ts'
done            #  -> zehnmal 0

# Zaehlung der Vorlagen
grep -c 'abbundhinweis:' resources/planner/hausplaner/geometry/dachformVorlagen.ts   # 13
```

## Wo das Zugehörige liegt

```text
W-25  geometry/holzBauteile.ts     Mengen — Sparren, Pfetten, Grat, Kehle
      geometry/schifterListe.ts    benennt Schifter (kehle/grat/beidseitig)
W-07  Dachgeometrie                Form, Neigung, Flaechen
W-21  Sparren und Lattung          laut Register Nachbar; Bezug hier NICHT gemessen
```

> ***Zu W-21 sage ich in diesem Blatt nichts*** — *das Register nennt es als Nachbarn, ich habe den
> Bezug nicht nachgemessen und übernehme ihn nicht* (H-6).

## Für den, der hier weiterbaut

- **Zuerst lesen:** `:2054`. *Die Daten sagen selbst, dass der Abbund geplant und nicht fertig
  ist — und zwar je Dachform, nicht pauschal.*
- **Nicht verwechseln:** *`abbundhinweis` ist Prosa; die Bauteil-MENGEN liegen in W-25 und sind
  gerechnet und geprüft.*
- **Der billige Schritt ist die Anzeige**, *nicht die Zeichnung* (`4-BEDIENUNG`).
