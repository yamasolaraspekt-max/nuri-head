# W-26 · Dachschichten (Aufbau) — CODE / LIESMICH

## Alles liegt in EINER Datei

`geometry/dachformVorlagen.ts` — *die Vorlagendatei der Dachformen.* **Es gibt kein
Dachschichten-Modul.**

```text
:112-125   interface VorlagenDachdecker        der Vertrag, 13 Felder
:113-114   die Bauregel im Kommentar           „KEINE feste Dacheindeckung ...
                                                separate Produktauswahl"
:119-120   rdnGrad / mindestneigungGrad        RICHTWERT, produktabhaengig zu pruefen
:418-424   PITCHED_COVER / FLAT_COVER
           eindeckungPasstZuKategorie          gebaut, 0 produktive Aufrufer
:432-452   neigungBrauchtZusatzmassnahme       die einzige wirkende Rechnung
:463       validateVorlage                     sammelt die Warnungen
:474       ruft neigungBrauchtZusatzmassnahme
:1272      applyVorlage ruft validateVorlage   der produktive Weg
:1384      konterlattungMm: [24, 48]
:1410      konterlattungMm: [0, 0]             Flachdach — Vertrag ohne Sinn gefuellt
:1723      pitchedDachdecker({ empfohleneEindeckung: 'ziegel', rdnGrad: 22, ... })
```

## Die Befehle, die dieses Blatt belegen

```sh
# Der Kern: kein Feld des Vertrags wird ausserhalb der Datei gelesen
for f in deckungsHinweis dachdeckungSeparatAuswaehlen empfohleneEindeckung \
         rdnGrad mindestneigungGrad battenDistCm konterlattungMm \
         unterdeckungKlasse firstausbildung gratausbildung kehlausbildung; do
  grep -rn "$f" resources/planner/hausplaner --include='*.ts' --include='*.tsx' \
    | grep -v '__tests__' | grep -vc 'geometry/dachformVorlagen.ts'
done          #  -> dreizehnmal 0

# Kein eigenes Modul
grep -riE 'dachschicht|schichtaufbau|unterspannbahn' resources/planner/hausplaner
              #  -> keine Ausgabe
```

## Die Wächter

```text
__tests__/dachformVorlagen.test.ts
  :240-244   eindeckungPasstZuKategorie — Kategorie-Trennung, 4 Zusagen
  :561-566   „Deckungsneutral: validateVorlage erzeugt KEINE
              EINDECKUNG_KATEGORIE-Warnung mehr" — ueber ALLE Vorlagen
  21 Fundstellen zu 'eindeckung' insgesamt
```

## Was hier NICHT liegt, obwohl der Name danach klingt

```text
renderers/three-d/dachAufbautenMesh.ts   Gauben, Kamin, Dachfenster    -> W-29
geometry/aufbauOrientierung.ts           lotrechte Aufbauten           -> W-29
commands/applyCommand.ts  ADD_ROOF_AUFBAU                              -> W-29
```

> ***„Dachaufbau" und „Dachaufbauten" sind im Deutschen zwei Sachen.*** **Wer im Code nach dem
> Stamm sucht, findet W-29 und hält es für W-26.** *In W-29 ist bereits gemessen, dass
> `ADD_ROOF_AUFBAU` keinen Aufrufer hat; das gehört dorthin und nicht hierher.*

## Für den, der hier weiterbaut

- **Nicht suchen nach:** `dachschicht`, `schichtaufbau`, `unterspannbahn` — *je 0.*
- **Zuerst lesen:** `dachformVorlagen.ts:113-114`. *Dort steht, warum es nichts gibt.*
- **Nicht anfassen ohne Entscheid:** `eindeckungPasstZuKategorie` wieder anzuschließen **hebt die
  Bauregel auf** und macht `dachformVorlagen.test.ts:561` rot. *Das ist beabsichtigt und der
  Wächter ist die Bremse, nicht der Fehler.*
