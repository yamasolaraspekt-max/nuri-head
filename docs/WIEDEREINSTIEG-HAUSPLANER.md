# ⇒ WIEDEREINSTIEG — Hausplaner/Governance in einer neuen Sitzung

**Angelegt:** 25.07.2026 · **Vom:** Planner · **Anlass:** Yama, 25.07.: *„sollen wir ticket hausplaner
qovernance setup zumachen den du kompromierst das ständig wegen gedächtnis und einen neuen chat
aufmachen und die arbeit darüber fortsetzen"*

---

## 0. Warum diese Datei kein Notbehelf ist

Eine Sitzung verliert ihr Gedächtnis — regelmäßig, nicht ausnahmsweise. Das ist kein Defekt, den man
wegorganisiert, sondern die Eigenschaft, gegen die das Setup von Anfang an gebaut wurde:
**der Zustand steht im Commit, nicht im Kopf einer Sitzung** (AUFTRAGSTAFEL §0, Hol-Prinzip).

Diese Datei ist die Tür dazu. Sie enthält **keine** Wahrheit, die nicht anderswo belegt ist — sie
sagt nur, **wo** man in welcher Reihenfolge nachsieht. Weicht sie vom Ledger ab, gilt der Ledger.

**Ein neuer Chat verliert nichts.** Was verloren ginge, wäre nur ein Zustand, den jemand versäumt
hat aufzuschreiben. Genau deshalb wird vor jedem Sitzungsende committet, nicht berichtet.

---

## 1. Der erste Satz in der neuen Sitzung

Yama schreibt drüben sinngemäß:

> Du bist Planner im ticket-Hausplaner. Lies zuerst `docs/WIEDEREINSTIEG-HAUSPLANER.md`,
> dann `docs/auftraege/AUFTRAGSTAFEL.md`. Danach sagst du mir, was Ballbesitz ist —
> ohne vorher irgendetwas zu bauen.

Mehr braucht es nicht. Alles Weitere holt sich die Sitzung selbst.

---

## 2. Leseordnung (in dieser Reihenfolge, nicht quer)

1. **`docs/auftraege/AUFTRAGSTAFEL.md`** — das Register. **§3a** ist der Arbeitsvorrat: der oberste
   Posten trägt ⚡ und ist der einzige, der gezogen werden darf. §3b Abnahme-Stapel, §3c bei Yama,
   Abgeschlossenes in `AUFTRAGSTAFEL-ARCHIV.md`.
2. **`docs/handoff-status.md`** — der Ledger. **Die Wahrheit.** Wellen, Voten, Ballbesitz.
3. **`docs/fahrplan-dashboard-versionen.md`** — der Fahrplan v1–v6 des Werkzeug-Dashboards.
4. **`docs/auftraege/generator-auftrag-dashboard-v2-flaechen.md`** — der aktuell offene Auftrag.
5. Erst dann Code. Vorher **nichts** behaupten (Regel 1 `bauplaner-3d`: messen vor behaupten).

Die Skills tragen den Rahmen: `governance-zyklus` (Rollentrennung + `references/pruefrahmen.md`),
`bauplaner-3d` (Code-Landkarte), `ux-design`, `frontend-entwickler` und die Fach-Linsen.

---

## 3. Ballbesitz am 25.07., 11:35 UTC (gemessen, nicht erinnert)

| Was | Zustand |
|---|---|
| Branch | `auto/hausplaner-integration` — **Hash hier bewusst nicht genannt**, er veraltet. `git log --oneline -5` fragen. |
| **AUF-12 Dashboard v2** | Batch 1 `ERLEDIGT` (freigegeben mit Auflage), Batch 2 gebaut (`5092b10`) — Abnahme offen. |
| **AUF-21 Werkzeug-Paket** | I1 umgesetzt (`7bbf9ff`); **I2/I3 gesperrt** hinter AUF-24. |
| **AUF-24 ID-Umbenennung** | `GESPERRT` — berührt `toolPresentation.ts` (AUF-1-Sperrbereich). |
| **AUF-15a / 16 / 19** | umgesetzt (`2d927fc` · `982384d` · `8587ce7`) — Abnahme offen. |
| **AUF-1** A1-Abnahme | **Der Engpass.** Wartet auf eine **frische Evaluator-Instanz**. Sperrt AUF-24 → I2/I3 → AUF-4. |
| **AUF-3** T1-Abnahme | `OFFEN` — wartet auf **Evaluator**. |
| **AUF-9** T2a | `BERICHTET` (`fbc5308`) — wartet auf **Evaluator**. |
| AUF-5/6/7/8/10/11 | `BEI YAMA` — Willensfragen, blockieren nichts. |
| **Push** | **Nicht erinnern — messen:** `git --no-optional-locks rev-list --count fork/auto/hausplaner-integration..HEAD`. Am 25.07. 13:42 lief ein Push, `fork` und `backup-private` standen danach auf `f60b923`. Push nur nativ, siehe §5. |

---

## 4. Die drei Sätze, die nie neu hergeleitet werden müssen

- **Rollentrennung ist die Bedingung, nicht die Bürokratie.** Planner ≠ Generator ≠ Evaluator.
  Niemand nimmt seine eigene Arbeit ab; der Evaluator läuft in einer **anderen Instanz**, nicht als
  Hut-Wechsel. Der Generator meldet „umgesetzt", nie „abgenommen".
- **Zwei Tore gehören Yama.** Tor 1 = Fach-Freigabe, Tor 2 = Merge/Deploy. `main`-Merge und ein
  echter upstream-/Hetzner-Deploy (~3000 Kunden) bleiben sein bewusster, separater Schritt.
- **Beweis statt Bericht.** Eine Zahl gilt erst, wenn sie selbst erzeugt und gegen einen
  Gegen-Beweis gehalten wurde. Vor jeder Abnahme `pruefrahmen.md` lesen.

---

## 5. Handgriffe, die in dieser Umgebung anders sind (Fallen)

- **Kein Netz im Geräte-Mount.** `git push` scheitert dort mit `HTTP 403 from proxy after CONNECT`
  (Beleg: `push-result.log`, Lauf 11:31 UTC). **Push läuft nur nativ** — über
  `./push-integration-sicher.command` (fork + backup-private). **Nie `upstream`**
  (`raminsadid2021` = fremdes Konto), **nie `--force`**.
- **`rm`/`unlink` ist im Mount verboten.** Jeder git-Befehl hinterlässt `.git/index.lock`,
  `.git/HEAD.lock`, `.git/next-index-*.lock`. Diese **per `mv`** nach
  `.git/_locks_beiseite/<sammel>/` schieben, **niemals löschen**. Vorher mtime gegen `date -u`
  prüfen, damit kein fremder, laufender git-Vorgang gestört wird.
- **Nur nach Pfad stagen.** `git add <pfad>` und `git commit -m "…" -- <pfad>`.
  **Nie `-A`, nie `.`** — `-m` steht **vor** dem `--`. Vor jedem eigenen Commit muss
  `git status` die eigenen Dateien als **einzige** Änderung zeigen.
- **Zod-Änderung ⇒ `npm run schema:hausplaner`.** Ohne Regen wird `schema:hausplaner:check` rot (422).
- **Kein Dev-Server für die Insel.** `npm run dev` startet `vite.config.js` (Vue-Haupt), **nicht**
  `vite.hausplaner.config.ts`; ein `dev:hausplaner` gibt es nicht. Der einzige Weg zur laufenden
  Oberfläche: `npm run build:hausplaner` → `public/hausplaner/hausplaner.js` → Route
  `/admin/hausplaner/studio` bzw. `/admin/hausplaner/objekt/{id}`, beide hinter `auth`.
  Der Build erzeugt **keine** `hausplaner.css` — das Styling liegt inline im TSX (AUF-14).
- **Gates:** `tsc:hausplaner` · `schema:hausplaner:check` · `test:hausplaner` · `build:hausplaner`
  (Build nur x64-nativ). **Es gibt kein DOM in der Testumgebung** (`node:test` mit
  `--experimental-strip-types`, kein jsdom) — Render-Tests sind unmöglich, beweisbar ist nur,
  was eine reine Funktion ist.

---

## 6. Das Einzige, was diese Sitzung mitnimmt: nichts

Wenn beim Lesen dieser Datei etwas fehlt, ist das ein **Befund über die Datei**, nicht über die
neue Sitzung. Dann wird hier ergänzt — und der nächste Wiedereinstieg ist wieder vollständig.
