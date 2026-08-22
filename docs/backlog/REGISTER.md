# BACKLOG / NACHBESSERUNG — was offen ist

> **Fach 3 von 5.** Landkarte: [`docs/REGISTER.md`](../REGISTER.md)
> Hier steht, was **offen** ist. Ein Eintrag verlässt dieses Fach nur über einen Beleg, nie über
> eine Meinung.

---

## Aufnahmeregel — ohne diese vier Felder kein Eintrag

Ein Eintrag folgt dem Qualitätsraster (`~/.claude/skills/qualitaetsraster/SKILL.md`):

| Feld | Bedeutung | Gegenprobe |
|---|---|---|
| **Beleg** | Befehl + Datei:Zeile + Rohausgabe | Kann ein Fremder ihn nachlaufen lassen? |
| **Beschreibung** | was tatsächlich da ist | Steht da eine Beobachtung oder ein Gefühl? |
| **Erklärung** | warum das ein Mangel ist | Welche Regel/Erwartung wird verletzt? |
| **Erledigt-Kriterium** | woran man das Ende erkennt | Prüfbar formuliert, nicht „ist besser" |

**Ohne Beleg ist es kein Befund, sondern eine Rückfrage** — und wird auch so benannt.

---

## Wer schreibt hier

- **Prüf-Agenten** (dachdeckermeister, statiker, ux-designer, security-reviewer …) **melden** —
  sie tragen nicht selbst ein. Read-only bleibt read-only.
- **Planner** trägt ein und schneidet daraus Aufträge.
- **Evaluator** hakt ab — mit Beleg, dass das Erledigt-Kriterium erfüllt ist.
- **Generator** trägt hier nichts ab. Wer gebaut hat, erklärt nicht selbst für erledigt.

---

## Drei Steuerungslisten (Yama 22.08., aus der Inventur-Bilanz `06642e35`)

| Liste | Posten | Reihenfolge |
|---|---:|---|
| [`abnahmerueckstand-2026-08-22.md`](abnahmerueckstand-2026-08-22.md) — gebaut, unbewiesen | 12 | W0-5 → W0-7/W0-3 → W0-1 → W0-8 → W0-10 → W0-11 → W0-12 → Z1 inkl. Browser; keine Sammelabnahme |
| [`produkt-backlog-2026-08-22.md`](produkt-backlog-2026-08-22.md) — unverändert | 13 (+ Wächter, 2 Yama-Fragen) | zuerst S-2, A-7/W0-9, A-10 B/C, A-5 |
| [`steuerungs-backlog-2026-08-22.md`](steuerungs-backlog-2026-08-22.md) — technisch offene Barrieren | 12 | A-37 (31 Kriterien) → Z0-I1 → Z0-I2 → Z0-I3 |

Jeder Posten trägt: Kennung · Schwere · Bau-SHA · unabhängiges Votum · Browserpflicht · Abhängigkeit · Besitzer ·
nächste konkrete Handlung. Die Bilanz selbst ([`../fortschritt/inventur-bilanz-2026-08-22.md`](../fortschritt/inventur-bilanz-2026-08-22.md))
bleibt unverändert als datierter Nachweis; fortgeschrieben werden nur diese Listen.

## Zustände

```text
OFFEN        aufgenommen, kein Auftrag geschnitten
GESCHNITTEN  Auftrag existiert -> Zustand steht ab jetzt in docs/STATUS.md
NACHBESSERUNG  war abgenommen, ist wieder aufgegangen (mit Verweis auf die alte Abnahme)
ERLEDIGT     Erledigt-Kriterium belegt erfuellt, Beleg verlinkt
VERWORFEN    mit Begruendung; nicht geloescht
```

**Nichts wird gelöscht.** Verworfenes bleibt mit Begründung stehen — sonst taucht dieselbe Frage
in drei Monaten wieder auf und niemand weiß mehr, warum sie damals fiel.

---

## Inventur-Läufe (dieses Fach, aktuell)

| Blatt | Inhalt | Zustand |
|---|---|---|
| [`inventur-2026-08-20-z1.md`](inventur-2026-08-20-z1.md) | Rohbefunde Z1: 7 Befunde (4 Finder), Negativ-Ergebnis Fehler-Linse, Linsen-Übergabe | abgelegt |
| [`fahrplan-2026-08-20.md`](fahrplan-2026-08-20.md) | Fahrplan in 3 Wellen + 5 Yama-Posten (Y-1/Y-2 entschieden 21.08.) | Welle 1 in Regularisierung |
| [`inventur-2026-08-21-z2.md`](inventur-2026-08-21-z2.md) | Rohbefunde Z2 Routen/Rechte: 5 Befunde, davon **3 Rechte-Lücken im LIVE-System** (S-1/S-2/S-5) | abgelegt — **Security-Gegenprobe läuft** |
| [`inventur-2026-08-21-z1-konsistenz.md`](inventur-2026-08-21-z1-konsistenz.md) | Z1 zweite Stufe: Übergabe bestätigt (Ü-1, Y-8 Re-Integration), K-5 Einheiten-Vertrag, K-6 Schreibweisen; 1 Negativ-Ergebnis | abgelegt |
| [`inventur-2026-08-21-z2-folge.md`](inventur-2026-08-21-z2-folge.md) | Z2b `/admin/energie/*` (E-1 Rückfrage Y-7, E-2 toter Redirect) · Z2c **Nuriva-API: 5 Erstfunde A-1..A-5** (GPS/Fotos/Pläne fremder Kollegen per Token) | abgelegt — Gegenprobe A-1..A-4 läuft |
| **Welle 0 (Sicherheit LIVE)** | Aufträge Z2-W0-1…6 in `docs/auftraege/`, Übersicht im [Fahrplan-Kopf](fahrplan-2026-08-20.md) | ENTWURF → DoR → Bau, Vorrang |

## Vorhandene Sammlungen (noch nicht migriert)

| Sammlung | Zeilen | Bereich |
|---|---|---|
| [`docs/sicherheits-backlog.md`](../sicherheits-backlog.md) | 60 | Sicherheit |
| [`docs/backlog-formulare.md`](../backlog-formulare.md) | 55 | Formulare |
| [`docs/backlog-accounting.md`](../backlog-accounting.md) | 49 | Buchhaltung |
| [`docs/backlog-rbac.md`](../backlog-rbac.md) | 10 | Rechte/Rollen |
| [`docs/BEFUND-*.md`](../) | ~50 Blätter lose in `docs/` | gemischt — enthalten offene *und* erledigte Befunde |
| [`docs/auslegungsworkflow-abgleich-luecken.md`](../auslegungsworkflow-abgleich-luecken.md) | — | Auslegung |

> ⚠ **Die losen `BEFUND-*.md` sind nicht sortiert nach offen/erledigt.** Wer sie als „offen" liest,
> liest falsch: mindestens eines trägt eine nachträgliche Erledigung im Text
> (`BEFUND-ZWEI-REGELWERKE.md`, erledigt 13.08.). Vor der Migration ist jedes Blatt einzeln zu
> lesen — Zählen reicht hier nicht.
