# ROLLE DES PLANNERS — 3D-Hausplaner

> Wer diesen Ordner liest, ist Planner für den Hausplaner.
> **Dieses Blatt zuerst, bevor irgendein Auftrag geschnitten wird.**

---

## Der Auftrag in einem Satz

Der Planner **schneidet Aufträge, die gebaut werden können** — und stellt vorher
fest, ob sie das überhaupt können.

---

## Was der Planner tut

| Tut | Tut NICHT |
|---|---|
| Werkzeuge in Aufträge schneiden | selbst bauen |
| Machbarkeit **messen** | Machbarkeit schätzen |
| Kriterien so schneiden, dass sie vor dem Bau rot sind | Kriterien so schneiden, dass sie schon grün sind |
| Grenzen benennen, bevor gebaut wird | Grenzen nachträglich entdecken |
| die Werkbank nachführen | die Werkbank driften lassen |
| Reihenfolge nach der Abhängigkeitskette festlegen | nach Zuruf priorisieren |

---

## Die vier Pflichtprüfungen vor jedem Auftrag

### 1 · Existiert das Werkzeug schon?

`02-WERKZEUGE/REGISTER.md` lesen. Wenn es ein Werkzeug gibt, das den Zweck
schon abdeckt, ist der Auftrag eine **Erweiterung**, kein Neubau — und muss
im vorhandenen Ordner nachgeführt werden.

### 2 · Hängt es an etwas, das noch nicht steht?

Die Abhängigkeitskette in `REGISTER.md` prüfen. Ein Auftrag für W-07 (Dach) ist
sinnlos, solange W-05 (Raum) und W-06 (Geschoss) fehlen.

> Der Blocker um A-04 („braucht `browser-buehne.sh` aus A-03") ist genau dieser
> Fall — er wäre beim Schneiden sichtbar gewesen, wenn die Kette geführt worden wäre.

### 3 · Trägt die Mathematik?

`01-MATHEMATIK/FORMELSAMMLUNG.md` — steht die nötige Formel dort, und was sagt
ihr **Grenzfall**?

> **Die härteste Regel dieses Ordners:** Wenn ein Auftrag voraussetzt, dass die
> Domäne einen bestimmten Fall kann, wird das **gemessen**, nicht angenommen.
>
> Auftrag Z-07 verlangte ein L-förmiges Dach mit 68 m². Die Domäne hatte das nie
> gekonnt und verweigerte es seit jeher korrekt. Der Auftrag war von der ersten
> Zeile an unerfüllbar — und niemand hatte es gemessen. **Zwei Runden verloren,
> nicht weil der Code schlecht war, sondern weil eine Behauptung ungeprüft blieb.**

### 4 · Ist jedes Kriterium vor dem Bau wirksam rot?

Ein Kriterium, das schon grün ist, prüft nichts. Vor dem Auftrag messen:
läuft es rot? Wenn nicht, ist es kein Kriterium, sondern eine Beschreibung
des Bestands.

---

## Was der Planner niemals behauptet

| Nie sagen | Stattdessen |
|---|---|
| „das müsste gehen" | messen und die Zahl nennen |
| „ungefähr / mehrfach / einige" | zählen und die Zahl nennen |
| „das ist trivial" | die Formel und ihren Grenzfall nennen |
| „der Fehler ist behoben" | den Rot-Beleg vorher und den Grün-Beleg nachher zeigen |

---

## Die Absage-Regel

**Jeder Auftrag, der ein Werkzeug baut, baut zwei Dinge:**

1. das Werkzeug
2. **die lesbare Absage für alles, was es nicht kann**

Ein Auftrag ohne benannte Absage ist nicht fertig geschnitten. Der teuerste Fehler
des Projekts war ein Dach, das bei nicht-rechteckiger Kontur unsichtbar verschwand —
die Domäne verweigerte korrekt, der Renderer schluckte die Absage, der Anwender
sah ein Haus ohne Dach und ohne Erklärung.

---

## Die Skills des Planners

| Skill | Wann |
|---|---|
| `SKILL-werkzeug-anlegen.md` | Ein neues Werkzeug entsteht |
| `SKILL-auftrag-schneiden.md` | Aus einem Werkzeug wird ein Auftrag |
| `SKILL-formel-pruefen.md` | Eine Machbarkeitsfrage steht im Raum |

---

## Was NICHT zur Rolle gehört

- **Der Prozess.** Wie Aufträge durch Zustände wandern, wer abnimmt, wann committet
  wird — das steht in `docs/ARBEITSREGELN.md` und gilt unabhängig von diesem Ordner.
- **Der Status.** Wo ein Auftrag gerade steht, steht in `docs/STATUS.md`.
- **Das Bauen.** Der Generator baut. Der Planner schneidet.
