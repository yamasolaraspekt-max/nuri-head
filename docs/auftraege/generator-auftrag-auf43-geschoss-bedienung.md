# ⇒ GENERATOR-AUFTRAG AUF-43 — Die Geschoss-Bedienung neu ordnen

**Vom:** Planner · **25.07.2026** · **Anlass:** Yama, 25.07.: *„ausserdem mit den Geschossen das sieht
nicht gut aus."* Nachgemessen in der Sichtprobe, Befund B1.

**Vorher gelesen:** HEAD `8dea959` · `git log -5` · Tafelzeile AUF-43 (§3a) ·
`app/HausplanerApp.tsx:967-1067` · `store/hausplanerStore.ts:103` (`setActiveLevel`) ·
`docs/planner/ux-befund-layout-alle-ebenen-2026-07-25.md` B1.

---

## 1. Der gemessene Befund

**13 Bedienelemente in einer Zeile, vier voneinander unabhängige Aufgaben:**

```
↰ ↱             Rückgängig / Wiederholen        ← gehört nicht zum Geschoss
◀ [Select] ▶    Geschoss-Navigation (Select 111 px breit)
[Erdgeschoss]   Textfeld — DERSELBE Wert wie der Select, unmittelbar daneben
+ ⧉ −           anlegen / duplizieren / löschen
2D Split 3D     Ansichtsmodus                   ← gehört nicht zum Geschoss
Speichern
```

**Warum das der größte Einzelhebel im Layout ist:** Ein angelegtes Geschoss entsperrt **34 der 110
Werkzeuge** (gemessen: 78 gesperrt ohne Geschoss, 44 mit). Die folgenreichste Handlung des ganzen
Programms steckt in einem 111-px-Dropdown zwischen „Rückgängig" und „Speichern".

**Drei weitere Messungen:** `elevation` wird im Modell geführt, aber im Wähler **nicht gezeigt**.
Namen werden automatisch als „Geschoss 3" vergeben. Es gibt **kein Bild vom Stapel** — der Nutzer
sieht nie, wie viele Geschosse übereinanderliegen und wo er gerade ist.

## 2. Was gebaut wird

**Eine eigene Geschoss-Fläche, die die Zeile verlässt.** Form ist Sache des Generators; verbindlich
ist, was sie leisten muss:

1. **Die vier Aufgaben trennen.** Rückgängig/Wiederholen und der Ansichtsmodus (2D/Split/3D) haben mit
   dem Geschoss nichts zu tun und gehören nicht in dieselbe Gruppe.
2. **Der Name steht einmal.** Select **oder** Eingabefeld — nicht beides nebeneinander. Umbenennen
   muss möglich sein (heute ist es das Textfeld, das niemand als solches erkennt).
3. **Die Höhenlage wird gezeigt.** `elevation` steht im Modell und ist die Angabe, an der ein
   Architekt Geschosse unterscheidet — nicht der Name.
4. **Der Stapel wird sichtbar.** Wie viele Geschosse, welches ist aktiv, was liegt darüber/darunter.
5. **Löschen bleibt so vorsichtig wie heute** — der Titel „Das letzte Geschoss kann nicht gelöscht
   werden" und die Bedingung „muss leer sein" sind richtig und bleiben.

## 3. Was **nicht** passiert

- **Kein neuer Zustand.** `setActiveLevel` (`hausplanerStore.ts:103`) bleibt die einzige Wahrheit;
  es leert weiterhin Auswahl und `primaerId`. Kein lokaler „aktuelles Geschoss"-Merker daneben.
- **Kein neues Command.** `ADD_LEVEL`, `UPDATE_LEVEL`, `REMOVE_LEVEL` gibt es; sie reichen.
- **Kein Schema-Eingriff.** `elevation`, `sortOrder`, `name` sind vorhanden — sie werden gezeigt,
  nicht erfunden.
- **Keine Sortierumkehr.** Die vorhandene Ordnung (`sortOrder`, dann `elevation`) bleibt.

## 4. Abnahmekriterien

1. `tsc:hausplaner` · `schema:hausplaner:check` · `test:hausplaner` — Exit 0, Zahlen vorher/nachher.
2. **K4 unberührt:** `store/`, `domain/`, `geometry/`, `renderers/`, `scene.types` — null Zeilen.
   *(`store/` ausdrücklich: dieser Posten liest den Store, er ändert ihn nicht.)*
3. **Der Geschossname kommt genau einmal vor:** Test belegt, dass in der Geschoss-Fläche nicht
   gleichzeitig ein `select` und ein `input` denselben Wert tragen.
4. **Höhenlage sichtbar:** Test belegt, dass `elevation` des aktiven Geschosses angezeigt wird.
5. **Umbenennen wirkt über `UPDATE_LEVEL`** und ist undo-fähig — Test.
6. **Trennung belegt:** Rückgängig/Wiederholen und 2D/Split/3D sind nicht mehr Teil der
   Geschoss-Gruppe (Struktur-Test, kein Screenshot).
7. **Mutations-Gegenbeweis:** die Sortierung verfälschen ⇒ mindestens ein Test rot. Zahl nennen.
8. **`public/*` im Code-Commit: null Zeilen**, Bundle-Rebuild als eigener zweiter Commit
   (§8 Punkt 2b in `docs/agents/06-laufzeiten-und-takt.md`).
9. **Klassifikation: `sichtbar`.** Sichtprobe an 1440/1024/375 px in die Abnahme. Bei 375 px gilt der
   Befund aus AUF-46 — wenn die Geschoss-Fläche dort überläuft, gehört das gemeldet, nicht geflickt.

## 5. Nahtstelle zu AUF-45

AUF-45 („niemand sieht, wo man anfängt") wird an dieser Fläche ansetzen: **das Geschoss ist das Tor
zu 34 Werkzeugen**, und genau dort gehört der Hinweis hin. **Dieser Auftrag baut den Hinweis nicht** —
er baut die Fläche, an der er später hängt. Wer beides zusammenzieht, hat zwei Posten in einem Commit.
