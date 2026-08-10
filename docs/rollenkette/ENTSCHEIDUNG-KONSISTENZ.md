# ENTSCHEIDUNG · Wieviel Konsistenz trägt, und wieviel hindert

> Yamas Frage: „Ist Konsistenz so wichtig oder hindert sie sogar die Arbeit?"
> Gemessen am eigenen Repo, 04.08.–07.08.2026. Keine Meinung, Zahlen.

---

## Die Messung

```
Commits seit 04.08. (Regelwerkswechsel):   132
    davon reine BUCHFÜHRUNG:                32   ← Tafel nachführen, Ballbesitz,
                                                    Zähler, Claim, Kenntnisnahme
    davon BEFUND / SPEC_BLOCKED:            20   ← ein Widerspruch wurde gefunden
    davon mit PRODUKTIVCODE:                 9
```

**32 Commits Buchhaltung gegen 9 Commits Bau.** Dreieinhalb Mal so viel
Nachführen wie Bauen.

---

## Was die 20 Befunde gefunden haben

Nicht Kleinigkeiten. Eine Auswahl mit dem Schaden, der verhindert wurde:

| Befund | Was ohne die Prüfung passiert wäre |
|---|---|
| `45ac9de3` A-07-4 zeigte auf den falschen Index | Der Bau hätte **genau die Dateien angegriffen, die er schützen soll** |
| `3e9b76d8` `GIT_INDEX_FILE` ohne Räumung | Ein Commit hätte **7011 fremde Dateien** mitgenommen |
| `89f373d9` 3D meldet nicht, was sie nicht zeichnen kann | Der A-01-Kernfehler wäre als „behoben" abgenommen worden |
| `4f849606` `lsof` ohne Zeitgrenze | Das Commit-Tor wäre **endlos hängen geblieben** |
| `c43bb788` Stichprobe statt Vollerhebung | Drei Zahlen im Auftrag waren falsch |
| `1747def7` A-03 riegelt die Tür ab, die niemand benutzt | Ein ganzer Auftrag am Ziel vorbei |
| `87e74ba9` zwei verbindliche Regelwerke | Der Generator wartete 16 h — der Widerspruch war echt |
| `0b3d6a10` Prüfbefehl konnte das Kriterium nie belegen | Ein Kriterium, das **nichts prüft**, wäre grün geworden |

**Diese Prüfungen sind kein Aufwand. Sie sind der Grund, warum das System noch steht.**

---

## Was die 32 Buchführungs-Commits gefunden haben

**Null.**

`a9c80f78` ist das Musterbeispiel: *„Ballbesitz auf planner korrigiert — mein Votum
gab den Ball zurück, das Feld stand noch auf plan-pruefer."* Ein Feld stimmte nicht
mit einem anderen Feld überein. Kein Fehler am Produkt. Kein Schaden verhindert.
Ein Commit, um zwei Orte in Übereinstimmung zu bringen, die **nie hätten getrennt
sein dürfen**.

---

## Die Entscheidung

**Konsistenz hindert nicht. Abgleich hindert.** Das sind zwei verschiedene Dinge,
die beide „Konsistenz" heißen — und sie werden ab jetzt getrennt behandelt:

| Art | Was es ist | Kostet | Findet | Entscheidung |
|---|---|---|---|---|
| **1 · Sachliche Konsistenz** | Widerspricht der Auftrag sich selbst? Trägt die Zahl? Deckt der Nachweis den Befund? | einmalig, vor dem Bau | **20 echte Fehler** | **BEHALTEN — uneingeschränkt** |
| **2 · Konsistenz durch Einmaligkeit** | Jede Tatsache steht an genau einem Ort, alles andere verweist | **nichts** | Drift entsteht gar nicht erst | **BEHALTEN — ist der Bauplan** |
| **3 · Konsistenz durch Abgleich** | Zwei Orte müssen von Hand übereinstimmen | 32 Commits | **nichts** | **STREICHEN** |

---

## Was das konkret ändert

### Gestrichen

- **Kein Ballbesitz-Feld mehr.** Wer dran ist, ergibt sich aus dem letzten
  Übergabestück. Ein Feld, das jemand von Hand nachzieht, driftet — belegt.
- **Keine Tafel-Nachführungs-Commits.** Der Zustand ergibt sich aus den vorhandenen
  Übergabestücken. Wenn `C` vorliegt und `D` fehlt, liegt es beim Release-Prüfer.
  Das muss niemand aufschreiben.
- **Keine Kenntnisnahme-Commits.** Wer nach einer Regeländerung startet, liest sie.
  Ein Commit, der bestätigt, dass man gelesen hat, prüft nichts.
- **Keine Zähler von Hand.** `git log | grep` zählt genauer als ein Mensch.

### Behalten

- **Jede sachliche Prüfung vor dem Bau.** Die 20 Befunde rechtfertigen den ganzen
  Rest des Prozesses.
- **SPEC_BLOCKED vor dem Bau.** Der teuerste Fehler ist der, der gebaut wird.
- **Die Rot-Pflicht.** Ein Kriterium, das nicht rot war, prüft nichts — belegt an `0b3d6a10`.

### Neu

- **Buchführung wird abgeleitet, nicht geführt.** Ein Werkzeug liest die
  Übergabestücke und erzeugt die Tafel. Kein Mensch trägt einen Zustand zweimal ein.

---

## Die Regel in einem Satz

> **Prüfe die Sache, nicht die Buchhaltung.**
> Jede Konsistenz, die man *herstellen* muss, ist eine, die man hätte
> *vermeiden* können — durch Einmaligkeit.

---

## Was das für den Aufbau bedeutet

Genau deshalb ist die Rollenkette so gebaut:

- **Ein** Wissensspeicher (Werkbank), nicht fünf → Art 2, kostenlos
- **Ein** Übergabestück für zwei Rollen, nicht zwei abgeglichene Blätter → Art 2
- Die sachliche Prüfung sitzt **in** den Übergabestücken (Feld „gemessen an") → Art 1
- Kein Statusfeld, das jemand nachzieht → Art 3 entfällt

**Der Aufbau ist die Antwort auf die Frage.** Wer Konsistenz erzwingen muss,
hat sie an der falschen Stelle gebaut.
