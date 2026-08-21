# Probe — P3 Gegenprobe

```yaml
auftrag: "X-02"
art: "BAU — ein Skript."
```

## Scope
Der Lauf erzeugt die Marke: `npm ci && git hash-object package-lock.json > node_modules/.aus-lockfile`.

## Abnahmekriterien
- **X-02-1** · Die Marke `node_modules/.aus-lockfile` ist vorhanden und wird vom Tor gelesen.
