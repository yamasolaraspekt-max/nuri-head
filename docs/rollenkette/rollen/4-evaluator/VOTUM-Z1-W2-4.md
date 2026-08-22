# VOTUM Z1-W2-4 — Treppe über die Werkzeug-Registry (Vertragsprobe)

**ABGENOMMEN — sechs von sechs Kriterien.**

Dies ist eine **Probe**, kein Bau: Kriterium (e) verlangt ausdrücklich, dass **kein Code** die
Probe verlässt. Deshalb brauchte diese Abnahme weder Bühne noch Arbeitsbaum — nur das Blatt, den
Bestand und den Bericht.

| Feld | Wert |
|---|---|
| Bau | `1c28d529` · Ausgang `ba6fc673` · DoR `7c7b2f63` |
| Blattstand des Baus | `97277281` — Kriterienzeilen **zeichengleich** mit meinem Checkout `418bcb6c` |
| Bericht | `docs/rollenkette/rollen/3-generator/PROBE-Z1-W2-4-treppe-werkzeugregistry.md`, 127 Zeilen |
| gelesen_bis | 2026-08-22T20:35:08+02:00 |
| Reifegrad | probe-abgenommen — keine Codewirkung, kein Browser verlangt |

**Offengelegt:** Der Bau ist **noch nicht transportiert** (`fork` steht auf `57e661bd`, 19:45).
Ich habe gegen die Commit-Objekte gemessen, die bei mir liegen. Das trägt hier, weil die Probe
per Kriterium (e) den Arbeitsbaum gar nicht berühren darf — bei einer Code-Lieferung wäre es
nicht zulässig.

## Die sechs Kriterien

**a · Die echte Treppe, keine Attrappe — ERFÜLLT.**
Der Probeaufbau bindet `berechneTreppe` aus `geometry/treppenBerechnung` ein:

```ts
registriereWerkzeug<TreppenEingabe>({
  kind: 'stair', schemaVersion: 1, kategorie: 'bau', kuerzel: 'R',
  parametrik: (d) => berechneTreppe(d),
  …
});
```

Die Absage-Regel („eine neu geschriebene Treppen-`parametrik` erfüllt (a) nicht") ist gewahrt:
`berechneTreppe` existiert im Bestand genau **einmal** als Export — selbst gemessen.
**Die Rot-Lage habe ich ebenfalls selbst nachgemessen**, statt sie zu übernehmen:
`registriereWerkzeug(` hat im Produktivpfad **0** Aufrufer; **6** Treffer in `__tests__` belegen,
dass der Griff trägt.

**b · Informationsverlust beziffert, Feld für Feld — ERFÜLLT.**
Der Bericht stellt **alle zehn** Felder gegenüber, in zwei Spalten (ohne / mit Adapter):

```
8 x verkuerzt   (die Zahlenfelder — zur Laufzeit da, am Typ unsichtbar)
1 x traegt      (bestanden -> Parametrik.bestanden)
1 x traegt NICHT (pruefungen: TreppenPruefung[]) — auch mit Adapter nicht
"Nicht abbildbare Felder: 1 von 10."
```

**Selbst nachgezählt:** `TreppenErgebnis` (`treppenBerechnung.ts:35-47`) hat **10** Felder —
anzahlSteigungen, anzahlAuftritte, steigungshoehe, auftritt, lauflaenge, schrittmass,
bequemlichkeit, sicherheit, pruefungen, bestanden. 8 + 1 + 1 = 10, die Tabelle ist vollständig.
Die Absage-Regel („passt im Wesentlichen" genügt nicht) ist eingehalten: es steht eine Zahl da
und eine Liste.

**c · Die Probe läuft, oder der Fehler wird zitiert — ERFÜLLT.**
Ausgang 1: `tsc --strict` → **rc 0, 0 Fehler**, ausdrücklich *„direkt gemessen, nicht hinter einer
Pipe"*; Ausführung über `node --experimental-strip-types`, derselbe Läufer wie die bestehenden
Proben. Mit **Gegenprobe**, dass der Befehl nicht generell durchgeht.

**d · Vergleich mit dem benutzten Vertrag — ERFÜLLT.** Zweite Tabelle im Bericht, je Feld mit
Fundstelle im Bestand (`trefferSuche.ts:34`, `validation.ts:308`, `faehigkeiten.ts`,
`toolRegistry.ts`) — nicht behauptet, sondern verortet.

**e · Kein Code verlässt die Probe — ERFÜLLT.**
`git diff --name-only ba6fc673..1c28d529` → **genau eine Datei**, und das ist der Bericht selbst.
Kein Produktcode, kein Test, kein Bündel. Der Aufbau lag unter `$TMPDIR` und ist dort geblieben.

**f · Der Bericht entscheidet nicht, er legt vor — ERFÜLLT.**
Der Abschnitt ist überschrieben *„EMPFEHLUNG (Empfehlung, keine Entscheidung)"*, enthält **einen**
Empfehlungssatz — `STILLLEGUNG BEGRÜNDET` — und schließt mit *„Die Stilllegung selbst ist nicht
Teil dieses Blattes."*

## Der Fund, der die Probe wertvoll macht

Der Generator hält fest, dass der Aufbau **übersetzt und läuft — aber nicht, weil der Vertrag
passt**, sondern weil TypeScript strukturell typisiert: `TreppenErgebnis` hat `bestanden: boolean`
und erfüllt damit zufällig die Mindestform. Genau darin liegt der Wert dieser Probe: „es
kompiliert" wäre hier die falsche Antwort auf die gestellte Frage gewesen. Er nennt den Ausgang
deshalb *„WEG BELEGT — technisch"* und empfiehlt trotzdem Stilllegung.

**Das ist keine Widersprüchlichkeit, sondern die verlangte Trennung** zwischen *geht* und *taugt* —
und sie ist der Grund, warum (f) verlangt, dass der Bericht nicht selbst entscheidet.

**Ball:** Planner/Dirigent (über die Empfehlung entscheiden), Integrator (Transport).
