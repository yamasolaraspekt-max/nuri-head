# W-42 · Schreibpfad Wizard → Gebäudemodell — BEDIENUNG

## Aufruf

| Weg | Wie |
|---|---|
| **Übernehmen im Konfigurator** | der einzige — W-42 hat keinen eigenen Einstieg |

**W-42 ist ein Handgriff am Ende eines anderen Werkzeugs.** *Der Anwender bedient W-35; W-42 ist,
was beim letzten Klick geschieht.*

## Ablauf am Bildschirm — Weg A, mit geladenem Gebäude

| Schritt | Anwender tut | Bildschirm zeigt |
|---|---|---|
| 1 | *konfiguriert ein Bauteil* | den Dialog (W-35) |
| 2 | *klickt „Übernehmen"* | — |
| 3 | *(sofort)* | **das Bauteil steht im Plan**, an fester Startposition |
| 4 | *(sofort)* | **die Meldung:** *„… ins Modell gesetzt — im Plan verschiebbar."* |

**Schritt 4 ist eine ehrliche Meldung**, *und das ist nicht selbstverständlich:* **sie sagt beides
— dass es gesetzt ist UND dass es noch verschoben werden muss.** *Sie verspricht keine Platzierung,
die nicht stattgefunden hat.*

## Ablauf am Bildschirm — Weg B, ohne Gebäude

| Schritt | Anwender tut | Bildschirm zeigt |
|---|---|---|
| 1–2 | *wie oben* | — |
| 3 | *(sofort)* | **eine JSON-Datei wird heruntergeladen** (`:244-247`) |

> **Der Anwender bekommt eine Datei, die er selbst nirgends einbauen kann.** *Das ist der Rückfall,
> nicht das Ziel — und es ist der Zustand, den der Dateikopf einmal als „die nächste Scheibe"
> beschrieben hat.* **Die Scheibe ist gebaut; der Rückfall bleibt für den Fall ohne Gebäude.**

## Rückmeldungen

| Lage | Anzeige | Ton |
|---|---|---|
| gesetzt | *„… ins Modell gesetzt — im Plan verschiebbar."* | **sachlich, mit Hinweis auf den nächsten Schritt** |
| **nicht gesetzt** (`ok === false`) | *eine zweite Meldung — der Zweig existiert* | erklärend |
| kein Gebäude | Download, **keine Meldung über einen Einbau** | — |

> **Die zweite Zeile ist der Fall, den niemand gemessen hat:** *`executeCommand` liefert `false`,
> die Meldung unterscheidet das — **ob dabei etwas zurückgerollt wird, ist ungeprüft**.* **In
> `7-GRENZEN.md` als offene Frage benannt und nicht beantwortet.**

## Wo das Bauteil landet

```ts
:181   position: { x: 2000, y: 500, z: 0 }
:175   const levelId = store.activeLevelId ?? scene.levels[0]?.id ?? null;
```

**Feste Startposition, Geschoss aus dem Store.** *Es gibt keine Platzierungsregel — das Bauteil
erscheint dort und wird vom Anwender verschoben.* **Genau das sagt die Meldung, und genau deshalb
ist sie ehrlich.**

## Abbruch

- **Der Dialog** hat `onClose` (W-35).
- **Der Schreibvorgang selbst** ist ein `executeCommand` — *also über den Weg, der Rückgängig
  ermöglicht.* **Ob das Rückgängig hier tatsächlich greift, ist nicht gemessen** — `7-GRENZEN.md`.

## Sichtprüfung

- [ ] Nach dem Übernehmen steht das Bauteil sichtbar im Plan
- [ ] Die Meldung erscheint und nennt beides: gesetzt **und** verschiebbar
- [ ] **Ohne geladenes Gebäude** wird heruntergeladen und **nichts** über einen Einbau behauptet
- [ ] 1440 px · 1024 px · 375 px
