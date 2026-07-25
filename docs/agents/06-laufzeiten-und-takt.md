# ⇒ VORSCHLAG — Laufzeiten und Takt (nativ · Cowork)

**Angelegt:** 25.07.2026 · **Vom:** Planner (Cowork) · **Status: VORSCHLAG.**
**Bindet erst mit Yamas Freigabe.** Er ergänzt `00-zyklus.md` bis `03-evaluator.md` und hebt nichts
davon auf; bei Konflikt gelten BETRIEBSORDNUNG und CLAUDE.md.

---

## 0. Was heute schiefging — und was **nicht**

**Nicht schiefgegangen:** die Rollentrennung. Planner, Generator und Evaluator waren sauber getrennt,
niemand hat eigene Arbeit abgenommen, jedes Votum kam von einer anderen Instanz.

**Schiefgegangen:** **zwei Laufzeiten haben um dieselben Dateien gerannt.** Drei
Generator-Läufe brachen ab, weil ein anderer Strang die Datei schon offen hatte:

| Posten | Kollision | Beleg |
|---|---|---|
| N2 (AUF-16) | `HausplanerApp.tsx` bereits umgebaut, fremder untracked Test im Baum | `982384d` |
| AUF-4 / A2 | zweiter Strang zieht denselben Posten und schreibt dieselbe Datei | `a61f10e` |
| AUF-25 / L4 | zweiter Strang zieht L4, schreibt, rollt 40 s später zurück | `a4bc277` |

Kein Byte ging verloren — die Pfadangabe-Regel und die Abbruchklausel haben gehalten. Aber zwei
Stränge auf einer Datei verdoppeln den Fortschritt nicht, sie **halbieren** ihn: einer arbeitet,
einer bricht ab.

**Der Zyklus kennt bisher nur eine Achse: die Rolle.** Es fehlt die zweite: **die Laufzeit.**

---

## 1. Die zwei Laufzeiten — gemessen, nicht behauptet

| Fähigkeit | **nativ** (Claude Code auf Yamas Rechner) | **Cowork** (Cloud, über die Geräte-Brücke) |
|---|---|---|
| `build:hausplaner` | **ja** | **nein** — `@rollup/rollup-linux-arm64-gnu` fehlt auf aarch64 |
| `tsc` · `schema:check` · `test` | ja | ja (~2–3 s, synchron) |
| git schreiben | sauber | **jeder** Schreibvorgang hinterlässt `.git/*.lock`, `rm` ist verboten |
| Browser-Sichtprobe | nein | **ja** — Chrome-Anbindung, echtes Rendern |
| Repo-Skills (`.claude/skills/`) | greifen automatisch | **greifen nicht** — nur Konto-Skills |
| Projekt-`CLAUDE.md` | greift | greift nicht |
| mehrere Agenten parallel | begrenzt | ja |
| Lesen über alle Apps (`~/Herd`, `wissensregister`, Downloads) | ja | ja, wenn Ordner freigegeben |

**Daraus folgt die Zuteilung — sie ist keine Meinung, sondern die Liste oben:**

- **Bauen und Abnehmen laufen nativ.** Generator und Evaluator brauchen Build und Gates und einen
  sauberen git-Baum. Sie gehören auf den Rechner.
- **Planen, Messen, Sichten läuft in Cowork.** Planner-Aufträge, Inventuren, Recherche über App-Grenzen,
  Vorarbeit die man wegwerfen darf — und die **Browser-Sichtprobe**, die nativ nicht möglich ist.
- **Cowork schreibt ausschließlich `docs/`.** Nie `resources/`, nie `app/`, nie `routes/`,
  nie `public/`. **Das ist die Grenze, die heute gefehlt hat.** Braucht Cowork eine Codeänderung,
  schreibt es einen Auftrag — es baut ihn nicht selbst.

**Ausnahme, ausdrücklich:** Ist nativ nicht verfügbar und Yama beauftragt Cowork direkt mit einem
Bau, gilt Spur A, der Posten wird auf der Tafel gezogen, und der Bericht hält fest, dass
`build:hausplaner` **nicht** gelaufen ist.

---

## 2. Der Takt — ein Posten von Anfang bis Ende

```
   Planner (Cowork)          Generator (nativ)         Evaluator (nativ, frisch)
   ────────────────          ─────────────────         ─────────────────────────
1  Auftrag schreiben
   docs/auftraege/…
   Spur · Nahtstellen
   Kanten · Rückweg
   Abnahmekriterien
   ─ commit nur docs/ ─►
                        2  ZIEHEN auf der Tafel
                           commit nur AUFTRAGSTAFEL.md
                           ─ erst danach die erste Zeile ─
                        3  bauen: Code UND Tests
                        4  Bericht in den Ledger
                           Tafel → BERICHTET
                           meldet „umgesetzt", nie „grün"
                                                   ─►  5  ZIEHEN auf der Tafel
                                                       6  selbst messen, Gegen-Beweis
                                                          Rohausgaben, keine Prosa allein
                                                       7  Votum in den Ledger
                                                          Tafel → ERLEDIGT / zurück
8  einordnen, nächsten  ◄───────────────────────────────
   Auftrag setzen
```

**Was nicht im Takt steht, passiert nicht.** Der Chat ist kein Übergabeweg — sechs Voten, die nur im
Chat liegen, sind nach `00-zyklus.md` nicht übergeben.

---

## 3. Die vier Regeln, die heute Geld gekostet haben

1. **Ziehen vor der ersten Zeile.** Wer den Posten nicht auf der Tafel gezogen hat, ist nicht dran.
   Das Ziehen ist ein eigener Commit, nur `AUFTRAGSTAFEL.md`.
2. **Commit immer mit Pfadangabe:** `git commit -m "…" -- <eigene pfade>`. **Nie `-A`, nie `.`**,
   `-m` **vor** dem `--`. Heute lag zeitweise ein fremder gebauter Bundle gestaged im Index — ein
   pauschales Commit hätte ihn mitgenommen.
3. **Abbruchregel:** fremde Änderung oder fremde untracked Datei **in meinen Pfaden** → nicht
   schreiben, melden, abbrechen. Fremde Arbeit in *anderen* Pfaden ist kein Abbruchgrund.
4. **Ein Posten, ein Strang.** Nie zwei Generatoren auf einer Datei — auch nicht „kurz".

---

## 4. Eine Ergänzung an der Tafel

Die Tafel bekommt neben `Rolle` eine Spalte **`Laufzeit`** mit `nativ` · `cowork` · `egal`.
Dann sieht jede Instanz **beim Ziehen**, ob der Posten überhaupt für sie ist — statt es nach dem
ersten Schreibversuch zu merken.

Faustregel für die Spalte: Posten, deren Abnahme `build:hausplaner` oder eine Sichtprobe braucht →
`nativ`. Posten, die nur `docs/` anfassen → `egal`. Sichtproben und Inventuren → `cowork`.

---

## 5. Was dieser Vorschlag **nicht** ändert

- **Die Rollentrennung bleibt unangetastet.** Planner ≠ Generator ≠ Evaluator, niemand nimmt eigene
  Arbeit ab, der Evaluator läuft in einer anderen Instanz. Daran wird nichts gelockert.
- **Die Spuren A/B bleiben.** Laufzeit und Spur sind zwei verschiedene Achsen.
- **Die zwei Tore bleiben bei Yama.** Fach-Freigabe und Merge/Deploy.
- **Der Ledger bleibt die eine Wahrheit.**

## 6. Offen an Yama

1. **Freigabe dieses Vorschlags** — er bindet erst dann.
2. **Soll die Spalte `Laufzeit` in die Tafel?** Sie kostet einmal Umbau und spart jedes Ziehen.
3. **Darf Cowork in Ausnahmefällen bauen** (wenn nativ nicht läuft), oder nie?
