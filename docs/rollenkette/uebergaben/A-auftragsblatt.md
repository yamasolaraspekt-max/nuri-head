# A · AUFTRAGSBLATT

> **Von:** Planner · **Über:** Plan-Prüfer · **An:** Generator
> Ein Blatt, drei Rollen. Der Plan-Prüfer schreibt **in dieses Blatt**, nicht daneben.

---

## Kopf — Herkunft (Kausalität)

| Feld | Wert |
|---|---|
| Auftrag | A-nn · <Kurzname> |
| Werkzeug | W-nn (Verweis in `werkbank/02-WERKZEUGE/`) |
| **Basis-SHA** | `<sha>` — **hier beginnt der Bau** |
| Vorgänger | <Befund/Auftrag, aus dem dieser entstand — SHA oder „neu"> |
| Angelegt | <Datum> |

## Warum jetzt

<Zwei Sätze. Welcher Schaden entsteht, wenn es nicht gebaut wird.>

## Kriterien

> **Jedes Kriterium muss vor dem Bau wirksam rot sein.** Ein Kriterium ohne
> Rot-Beleg ist eine Beschreibung des Bestands, keine Anforderung.

| Nr | Kriterium | Rot-Beleg (gemessen an) | Messbefehl |
|---|---|---|---|
| A-nn-1 | | `Datei:Zeile` oder Befehlsausgabe | |

## Grenzen — was NICHT gebaut wird

<Aus `werkbank/02-WERKZEUGE/W-nn/7-GRENZEN.md`. Für jede Grenze der Absagetext.>

| Fall | Fehlername | Anwendertext |
|---|---|---|

## Formeln

| F-Nr | wofür | Grenzfall betrifft uns |
|---|---|---|

---

## Prüfung durch den Plan-Prüfer

*Der Plan-Prüfer trägt hier ein. Er legt kein eigenes Blatt an.*

| Punkt | Befund | selbst gemessen an |
|---|---|---|
| Jedes Kriterium vorher rot? | | |
| Kein Kriterium schon erfüllt? | | |
| Keins unerfüllbar? | | |
| Basis-SHA existiert und ist erreichbar? | | |
| Widerspricht sich das Blatt selbst? | | |
| Machbarkeit **gemessen**, nicht behauptet? | | |

**Votum:** ENTWURF bleibt · **BEREIT** · SPEC_BLOCKED
**Prüf-SHA:** `<sha>`
**Begründung:** <bei ENTWURF/SPEC_BLOCKED: was genau fehlt>

---

## Pflichtangabe seit 16.08.2026 — Fachaussagen

**Nennt das Blatt eine Kennung aus `FORMELSAMMLUNG` oder `SOLAR-REGELWERK` (`F-`, `N-`, `S-`),
gehört in den Kopf:**

```yaml
fachaussagen:
  - kennung: "F-0nn"
    zustand: "NACHGERECHNET"        # ABGESCHRIEBEN | NACHGERECHNET | GEGENGEPRUEFT
    gedeckt_durch: "nachgerechnet_an im Eintrag"   # oder: "Kriterium A-nn-n dieses Blattes"
```

**Ohne Deckung keine DoR.** Der Plan-Prüfer prüft es mechanisch: trägt der Eintrag
`nachgerechnet_an`, oder ist das Nachrechnen ein **Kriterium dieses Blattes**?

**Wirkt die Aussage nach außen** — **Normbezug** oder **Dritter** oder **Bemessung** —, reicht
Nachrechnen nicht: dann `gegengeprueft_an` mit Fundstelle **oder** `geltungsbereich` und **gelb**.

> **Was für einen Test gilt, gilt für eine Formel: was auch ohne sie stimmt, hat sie nicht belegt.**
