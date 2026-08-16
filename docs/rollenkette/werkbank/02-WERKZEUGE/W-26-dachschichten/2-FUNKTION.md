# W-26 · Dachschichten (Aufbau) — FUNKTION

## Was tatsächlich läuft

**Genau eine Kette, und sie betrifft die NEIGUNG, nicht die Schichten:**

```text
applyVorlage(v, prevBuild)              :1264   produktiver Aufrufer
  -> validateVorlage(v)                 :1272
       -> neigungBrauchtZusatzmassnahme(pitch,
              v.dachdecker.mindestneigungGrad,
              v.dachdecker.rdnGrad)     :474
            -> VorlagenWarnung 'NEIGUNG_UNTER_RDN'
            -> VorlagenWarnung 'NEIGUNG_UNTER_MINDEST'
```

> ***Das ist der einzige Weg, auf dem ein Feld des Dachdecker-Blocks je eine Wirkung entfaltet.***
> *Zwei Zahlen von siebzehn Feldern, und beide nur, um zu warnen.*

## Was NICHT läuft, obwohl es dasteht

```text
eindeckungPasstZuKategorie(cover, category)      :422
  PITCHED_COVER  ziegel · schiefer · trapezblech
  FLAT_COVER     bitumen · kunststoff · gruendach · kies
  produktive Aufrufer:   0
  Fundstellen im Test:   6
```

> **Eine vollständig gebaute, viermal zugesagte Funktion ohne einen einzigen produktiven Aufrufer.**
> *Sie ist der Rest der abgeschalteten Eindeckungsprüfung* — **die Prüfung wurde aus
> `validateVorlage` entfernt, die Funktion blieb stehen.**
>
> ***Und das ist kein Fehler, sondern eine offene Frage:*** *wer sie später wieder anschließt, hebt
> die Entscheidung „deckungsneutral" auf — und fällt dabei in den Wächter von* `6-PRUEFUNG`.

## Der Vertrag, den jede Vorlage mitträgt

`VorlagenDachdecker` (`:112-129`) — **siebzehn Felder in fuenf Gruppen:**

| Gruppe | Felder | Art |
|---|---|---|
| **die Entscheidung selbst** | `deckungsHinweis`, `dachdeckungSeparatAuswaehlen` | Text und ein Flag, das immer `true` ist |
| **die Abhängigkeitsaussagen** | `regeldachneigungAbhaengigVonMaterial`, `lattmassAbhaengigVonProdukt` | zwei Wahrheitswerte über das Fach |
| **die Richtwerte** | `rdnGrad`, `mindestneigungGrad` | die zwei, die wirken |
| **der Aufbau** | `battenDistCm`, `konterlattungMm`, `unterdeckungKlasse`, `firstausbildung`, `gratausbildung`, `kehlausbildung`, `ortgangausbildung`, `traufausbildung`, `empfohleneEindeckung` | **das eigentliche W-26 — und komplett ungelesen** |
| **die drei Hinweise** | `entwaesserungHinweis`, `schneefangHinweis`, `lueftungHinweis` | Text je Vorlage, ebenfalls ungelesen — **`entwaesserungHinweis` gehört fachlich zu W-28** |

> ***Die vierte Gruppe IST das Werkzeug, um das es hier geht.*** *Sie ist nicht ungebaut, sie ist
> ungefragt:* **Lattenabstand, Konterlattung, Unterdeckungsklasse, First-, Grat- und
> Kehlausbildung stehen je Vorlage gepflegt da und erreichen niemanden.**

## `dachdeckungSeparatAuswaehlen: true` — ein Feld, das keine Frage stellt

**Der Typ ist das Literal `true`, nicht `boolean`.** *Ein Feld, das nur einen Wert annehmen kann,
trägt keine Information; es ist eine Aussage im Datenkleid.*

> **Als Erinnerung an die Entscheidung ist es sinnvoll — als Datenfeld ist es eine Zeile, die bei
> jeder Vorlage mitgeschleppt und nie geprüft wird.** *Der Wächter prüft nicht das Feld, sondern das
> VERHALTEN* (`6-PRUEFUNG`) — **und das ist die richtige Reihenfolge.**

## Abgrenzung, die dieses Blatt vor einem Fehler bewahrt hat

```text
Dachaufbau    = Schichten (dieses Blatt)
Dachaufbauten = aufgesetzte Bauteile — Gaube, Kamin, Dachfenster, Luefter
                renderers/three-d/dachAufbautenMesh.ts · geometry/aufbauOrientierung.ts
                -> das ist W-29, nicht W-26
```

> ***Zwei Wörter mit demselben Stamm und verschiedener Sache.*** *Ein Muster auf `dachaufbau` findet
> 14 Treffer in 11 Dateien und hätte diesem Blatt einen Bestand angedichtet, der zu W-29 gehört.*
> **Erst das Lesen der Dateiköpfe trennt sie** (H-9).
