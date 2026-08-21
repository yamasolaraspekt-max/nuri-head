# Probe — sauberes Blatt

```yaml
auftrag: "X-07"
art: "BAU — ein Skript scripts/x07.sh."
```

## Scope
Der Bau erzeugt `scripts/x07.sh`.

## Kanten
| # | Fall | Verlangtes Verhalten |
|---|---|---|
| K1 | Eingabe fehlt | Abbruch mit eigener Meldung |
| K2 | Eingabe doppelt | zweite gewinnt |

## Abnahmekriterien
- **X-07-1** · `scripts/x07.sh` ist vorhanden und ausfuehrbar.
- **X-07-2** · Alle Kanten K1–K2 sind behandelt und je einzeln belegt.
- **X-07-3** · Suite gruen gegen den Bau-Stand, tsc exit 0.
