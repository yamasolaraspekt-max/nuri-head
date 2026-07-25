# ⇒ GENERATOR-AUFTRAG AUF-60 — Die Insel muss die Rechte des Nutzers kennen

**Vom:** Planner · **25.07.2026** · **Anlass:** Rückgabe des Generators aus AUF-53, §4 —
*„die Insel kennt keine Nutzerrechte, das Recht wird nicht durchgereicht."*

**Vorher gelesen:** HEAD `bc38649` · `git log -5` · Ledger „GENERATOR-BERICHT AUF-53" ·
`app/tools/vorbedingungen.ts` · `app/HausplanerApp.tsx:370` · `main.tsx:63` ·
`views/admin/hausplaner/objekt.blade.php:93` · `app/Http/Middleware/CheckUserPermission.php` ·
`app/Models/User.php:56-74`.

**Alle Zahlen unten sind am 25.07. gemessen** — Regel seit AUF-45: jede Zahl in einem Kriterium
trägt die Messung, aus der sie stammt.

---

## 1. Warum dieser Posten existiert

AUF-53 hat das Import-Recht korrekt auf **`Hausplaner,add`** abgebildet — getrennt von `update`,
ohne Migration, ohne Eingriff in `hasPermission`. Der Generator hat dabei gemessen und
zurückgegeben, was der eigentliche Mangel ist:

> **Die React-Insel kennt genau ein Recht** (`RECHT_BEARBEITEN`), und es stammt **nicht aus dem
> angemeldeten Nutzer.** Es wird gesetzt, nicht gefragt.

**Damit ist die Zuordnung aus AUF-53 richtig und wirkungslos zugleich.** Ein Werkzeug, das
`Hausplaner,add` verlangt, sperrt oder öffnet nach einem Wert, den die Insel sich selbst gibt.

**Was das ist und was es nicht ist:** Es ist **keine Sicherheitslücke** — die Serverseite schützt
weiterhin jede Route über `CheckUserPermission`; wer kein Recht hat, bekommt vom Server ein 403,
egal was die Oberfläche anzeigt. Es ist eine **Anzeige-Lüge in beide Richtungen**: einem Nutzer
ohne Recht wird ein Werkzeug als bedienbar gezeigt, das der Server ihm verweigern wird — und
umgekehrt kann ein Berechtigter etwas gesperrt sehen, das er dürfte.

**Deshalb ist der Posten `sichtbar` und trotzdem kein Sicherheitsposten.** Wer ihn als Absicherung
verkauft, hat ihn missverstanden.

## 2. Was gebaut wird

**Die Rechte des angemeldeten Nutzers kommen über dieselbe Naht wie alles andere: das Blade setzt
sie, `main.tsx` liest sie, der Store hält sie, `AktivierungsKontext` bekommt sie.**

Dieselbe Naht trägt heute schon `data-speichern-url` (`objekt.blade.php:93`) und
`data-snapshots-url` — es ist ein bekannter, erprobter Weg und **kein neuer Mechanismus**.

1. **Blade:** die Rechte des Nutzers für das Item `Hausplaner` als Datenattribut — die vier, die das
   System kennt (`is_read` · `is_add` · `is_update` · `is_delete`), nicht mehr.
2. **`main.tsx`:** liest sie beim Mount, genau wie `speichernUrl`.
3. **Store:** hält sie unverändert; **kein Ableiten, kein Ergänzen, kein Standardwert „darf alles"**.
4. **`AktivierungsKontext.permissions`:** bekommt die echte Liste statt der gesetzten.

## 3. Was **nicht** gebaut wird

**(a) Keine Rechteprüfung in der Insel.** Die Oberfläche **zeigt** Rechte, sie **entscheidet** sie
nicht. Die Wahrheit bleibt `CheckUserPermission` auf dem Server. Wer in der Insel eine eigene Prüfung
baut, hat eine zweite Wahrheit über Berechtigungen — die gefährlichste Sorte zweiter Wahrheit.

**(b) Kein Standardwert nach oben.** Fehlt das Attribut (alte Blade, Testfläche), gilt **das
Minimum**, nicht das Maximum. Ein fehlender Wert darf nie „darf alles" bedeuten.

**(c) Keine neue Aktion, keine Migration, keine Änderung an `hasPermission`** — dieselbe Grenze
wie in AUF-53. `import` taucht nirgends als Aktion auf.

**(d) Die Testfläche bleibt, wie sie ist.** `studio.blade.php` hat bewusst keine Objekt-Bindung;
dort greift (b) — Minimum, und die Oberfläche sagt es ehrlich (AUF-47 hat die Anzeige dafür schon
richtiggestellt).

## 4. Abnahmekriterien

1. `tsc:hausplaner` · `schema:hausplaner:check` · `test:hausplaner` — Exit 0, Zahlen vorher/nachher.
2. **K4 unberührt:** `store/`, `domain/`, `geometry/`, `renderers/`, `scene.types` — null Zeilen.
   *(Der Rechte-Zustand gehört in die App-Schicht, nicht ins Dokumentmodell.)*
3. **Keine Migration, keine Änderung an `hasPermission`, keine neue Aktion:** null Zeilen in
   `database/migrations/` und `app/Models/User.php`; `grep -r "permission:Hausplaner,import"` = **0**.
4. **Keine Rechteprüfung in der Insel:** `grep` belegt, dass in `resources/planner/` kein
   `hasPermission`, kein `isSuperAdmin` und keine eigene Rechte-Ableitung vorkommt.
5. **Fehlendes Attribut ⇒ Minimum:** Test mit fehlendem und mit leerem Attribut ⇒ die Rechte-Liste
   ist **leer**, nicht voll. *Das ist das wichtigste Kriterium dieses Postens.*
6. **Durchgereicht, nicht abgeleitet:** Test belegt, dass `AktivierungsKontext.permissions` genau
   das enthält, was das Attribut liefert — kein Eintrag mehr, keiner weniger.
7. **Wirkung gemessen:** die Zahl der gesperrten Werkzeuge für **zwei** Nutzerlagen — mit und ohne
   `Hausplaner,add` — im Bericht genannt. Bezug: AUF-53 hat **79 → 71** gemessen, als das Recht
   fest gesetzt war; jetzt muss sich der Unterschied aus dem **Nutzer** ergeben.
8. **Mutations-Gegenbeweis:** den Standardwert auf „darf alles" drehen ⇒ mindestens ein Test rot.
   Zahl nennen.
9. **`public/*` im Code-Commit: null Zeilen**, Bundle-Rebuild als eigener zweiter Commit (§8 2b).
10. **Klassifikation: `sichtbar`.** Sichtprobe **mit zwei verschiedenen Rechtelagen** — sonst ist
    nicht belegt, dass das Recht überhaupt unterscheidet.

## 5. Was zurückgegeben wird statt mitgebaut

- **Berührt der Weg `routes/` oder `app/Http/`** (etwa weil der Controller die Rechte mitgeben muss):
  **melden und zurückgeben.** Das ist Tor 1 und braucht Yamas Freigabe — so wie bei AUF-53, wo sich
  am Ende herausstellte, dass es ohne ging.
- **Fällt beim Messen auf, dass andere Stellen der Insel Rechte annehmen** statt zu fragen:
  benennen, nicht nebenbei mitziehen.
