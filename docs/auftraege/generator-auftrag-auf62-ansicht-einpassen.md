# ⇒ GENERATOR-AUFTRAG AUF-62 — „Ansicht einpassen" bauen

**Vom:** Planner · **26.07.2026, 07:40** · **Spur A** — der Posten rechnet (Bounding-Box, Maßstab,
Verschub) und entscheidet, was der Nutzer sieht. Das ist Logik, nicht Markup. Voller Zyklus.
**Heimat-App:** `ticket`. **Grundlage:** Entscheidung Yama, 25.07. („bauen", nicht „entfernen"),
aus AUF-61/AUF-44.

**Vorher gelesen:** HEAD `0045ea2` · `git log -5` · Tafelzeile AUF-62 ·
`app/HausplanerApp.tsx:348` (`zoom`), `:354` (`pan`), `:999` (`stageBreite`), `:1210` (der Knopf),
`:1427-1432` (`panAus`, Drag) · `app/dashboard/pan.ts` (ganz) ·
`geometry/editierGeometrie.ts:63-70` (`bbox`) · AUF-51 (`74fdcb4`, abgenommen).

**Alle Zahlen gemessen am 26.07.**

---

## 1. Der Befund: alles, was es braucht, ist schon da

| gebraucht | vorhanden seit | Stelle |
|---|---|---|
| Maßstab als Zustand | immer | `:348` `const [zoom, setZoom] = useState(0.12)` — px pro mm |
| Verschub als Zustand | **AUF-51** | `:354` `const [pan, setPan] = useState<Pan \| null>(null)` |
| Bounding-Box einer Punktmenge | vorhanden | `geometry/editierGeometrie.ts:63` `bbox()` — rein, ohne DOM |
| der Knopf selbst | vorhanden | `:1210`, heute mit `geplant` |

**Es fehlt genau eine Rechnung und ihre Verdrahtung.** Kein Modell, kein Command, kein Schema —
**die Ansicht ist Anzeige, kein Modellzustand.** Nichts wird ins Dokument geschrieben.

**`bbox()` wird gelesen, nicht nachgebaut.** `geometry/` bleibt unverändert (K4) — lesen ist erlaubt,
schreiben nicht. Braucht die Rechnung etwas, das `bbox()` nicht kann: **melden**, nicht dort ergänzen.

## 2. Was gebaut wird

**Eine reine Funktion**, die aus (a) den Knoten des **aktiven Geschosses**, (b) der **tatsächlichen
Bühnengröße** und (c) einem Rand den neuen `zoom` und den neuen `pan` liefert. Der Knopf ruft sie,
setzt beide Zustände, verliert sein `geplant`.

**Rein und ohne DOM** — damit sie im bestehenden Testlauf prüfbar ist und nicht erst im Browser.

## 3. Die Kanten — hier bricht es erfahrungsgemäß

1. **Leeres Geschoss.** Keine Knoten ⇒ **kein Sprung, kein Fehler, keine Division durch Null**,
   sondern der Standardmaßstab und die Standardlage (`standardPan(hoehe)`). *Das ist Yamas eigenes
   Kriterium von der Tafel und steht hier zuoberst, weil es der Fall ist, den man beim Bauen nicht
   vor Augen hat.*
2. **Split-Ansicht.** `:999` — `stageBreite = modus === 'split' ? Math.floor(breite / 2) : breite`.
   **Eingepasst wird in die Fläche, die 2D wirklich hat, nicht in die volle Fensterbreite.** Wer
   `breite` nimmt, passt in Split in eine Fläche ein, die es nicht gibt, und die Hälfte des
   Grundrisses steht außerhalb. **Test mit beiden Modi.**
3. **Die Welt wächst nach oben.** `standardPan` rechnet `y = hoehe - RAND`, die Bühne ist senkrecht
   gespiegelt. Ein Verschub, der das Vorzeichen verwechselt, sieht bei quadratischen Grundrissen
   richtig aus und bei länglichen falsch. **Test mit einem Grundriss, der deutlich höher als breit
   ist, und einem, der deutlich breiter als hoch ist.**
4. **Ein einzelner Knoten / eine Nullfläche.** Eine Wand ohne Ausdehnung in einer Achse ⇒ Breite
   oder Höhe der Box ist **0**. Kein `Infinity`, kein Maßstab jenseits der Grenzen.
5. **Die Maßstabsgrenzen gelten weiter.** `zoom` ist heute auf **0,02 … 1** begrenzt (`:1203-1204`).
   Ein sehr kleiner Grundriss darf nicht über 1 hinausgezoomt, ein sehr großer nicht unter 0,02
   gedrückt werden. **Passt es in den Grenzen nicht ganz hinein: die Grenze gewinnt** — und der Test
   hält fest, dass genau das passiert, statt dass jemand die Grenze aufweicht.
6. **Ein Rand bleibt.** Der Grundriss klebt nicht an der Kante. Ein benannter Wert, kein Zufall.

## 4. Was **nicht** gebaut wird

- **Kein Schreiben ins Dokument, kein Command, kein Undo.** Die Ansicht ist Anzeige. So hat es
  AUF-35a entschieden, und dasselbe gilt hier.
- **Kein Eingriff in `geometry/`, `store/`, `domain/`, `renderers/`, `scene.types`** — K4.
- **Kein automatisches Einpassen** beim Laden, beim Geschosswechsel oder nach einem Befehl. **Ein
  Knopf, ein Klick, eine Wirkung.** Wer es automatisch macht, nimmt dem Nutzer den Maßstab aus der
  Hand, den er gerade eingestellt hat.
- **Keine Animation.** Ein Sprung ist ehrlich; eine Bewegung ist ein eigener Posten.
- **Kein Anfassen der Werkzeugzeile.** AUF-70 hat sie gerade umgebaut — hier wird **ein** Knopf
  seines `geplant` entledigt, sonst nichts.

## 5. Abnahmekriterien

1. `tsc:hausplaner` · `schema:hausplaner:check` · `test:hausplaner` — Exit 0, Zahlen vorher/nachher.
2. **K4 unberührt:** `store/`, `domain/`, `geometry/`, `renderers/`, `scene.types` — **null Zeilen**.
   *(`geometry/editierGeometrie.ts` wird gelesen; steht dort eine geänderte Zeile, ist der Posten rot.)*
3. **Nach dem Einpassen liegt jeder Knoten des aktiven Geschosses im sichtbaren Bereich** — der Test
   rechnet gegen die Bounding-Box, **kein Screenshot**.
4. **Leeres Geschoss:** kein Sprung, kein Fehler ⇒ Standardmaßstab **0,12** und `standardPan(hoehe)`.
   Testverriegelt.
5. **Split:** eingepasst wird in `stageBreite`, nicht in `breite`. Test mit beiden Modi; im
   Split-Fall liegt der Grundriss vollständig in der **halben** Fläche.
6. **Zwei Seitenverhältnisse:** ein deutlich höherer und ein deutlich breiterer Grundriss, beide
   vollständig sichtbar. *(Das ist der Test, der ein vertauschtes Vorzeichen fängt.)*
7. **Nullfläche und Einzelknoten:** kein `Infinity`, kein `NaN`, Maßstab innerhalb 0,02 … 1.
8. **Die Grenzen gewinnen:** ein Grundriss, der in den Grenzen nicht ganz hineinpasst ⇒ Maßstab
   steht auf der Grenze, **nicht** darüber hinaus. Testverriegelt, damit niemand später „nur ein
   bisschen" lockert.
9. **Nichts wird gespeichert:** `grep` belegt, dass kein Command ausgelöst und kein Feld des
   Dokuments berührt wird; `speicherStatus` bleibt unverändert. *Ein Posten, der die Ansicht ändert
   und das Dokument als „ungespeichert" markiert, hat gelogen.*
10. **Der Knopf ist nicht mehr `geplant`:** `grep` auf `geplant` in dieser Zeile = **0** für
    `einpassen`. Die Zahl der gesperrten Knöpfe der Werkzeugzeile sinkt um **genau eins**
    (nach AUF-70 gemessen), keine andere Sperre ändert sich.
11. **Mutations-Gegenbeweis:** Rand auf 0 setzen **oder** `stageBreite` durch `breite` ersetzen ⇒
    mindestens ein Test rot. Zahl nennen.
12. **`public/*` im Code-Commit: null Zeilen**, Bundle-Rebuild als eigener zweiter Commit (§8 2b).
13. **Klassifikation: `sichtbar`.** Sichtprobe in die Abnahme: Grundriss weit herauszoomen, Knopf
    drücken, sehen, dass der **ganze** Grundriss im Bild steht — **und dasselbe in Split**.

## 6. Was zurückgegeben wird

- **Lässt sich der Verschub nicht setzen, ohne `renderers/` anzufassen:** melden. Dann endet der
  Posten mit dem Maßstab, und der Verschub wird ein eigener — **ein halber Posten mit Begründung
  ist besser als ein ganzer mit gebrochenem K4.**
- **Erweist sich, dass „die Knoten des aktiven Geschosses" nicht eindeutig bestimmbar ist** (z. B.
  weil ein Knoten mehreren Geschossen zugeordnet ist): benennen, nicht raten. Dann ist die
  Zuordnung ein eigener Posten und dieser hier wartet.
