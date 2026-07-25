# ⇒ GENERATOR — AUFTRAG I3: Die 110 Werkzeuge werden sichtbar

**Angelegt:** 25.07.2026 · **Vom:** Planner · **Tafel:** AUF-21 / I3 · **Spur:** **A**
**Vorbedingung: AUF-31 muss durch sein** (deutsche IDs) — sonst benennt I3 gegen IDs, die sich
gleich danach ändern. **Nicht parallel zu AUF-31 starten.**

**Yama, 25.07.:** *„ich will alle werkzeugstool icon frontendlayout"* — und auf die Frage, ob nach
den 22 Kategorien gruppiert werden darf: **„ja"**. Das ist die Freigabe für diesen Auftrag.

---

## Ziel & Entscheidung

Nach I2 liegen 110 Fach-Werkzeuge als Daten im Katalog — und **alle 110 stehen auf `versteckt`**.
Die Leiste zeigt weiterhin sieben. **I3 bringt sie auf den Schirm.**

**Entscheidung — wo sie erscheinen:** In der **oberen Werkzeugleiste**, gruppiert nach den
**22 Paket-Kategorien**. Diese Leiste existiert bereits mit den Gruppen `ANSICHT`, `BEARBEITEN`,
`MESSEN & EXPORT` — sie wird **gefüllt**, nicht neu gebaut. Das ist genau die Struktur aus Yamas
Entwurf `dashboard-tools-v1.html` („Ansicht ▾ · Bearbeiten ▾ · Transformieren ▾ · Anordnen ▾ ·
Messen ▾ · Bemaßen ▾") und zugleich Wiederverwendung statt Neubau.

**Entscheidung — was die linke Leiste bleibt:** Die linke Leiste bleibt die **persönliche** Fläche:
die neun Bestands-Werkzeuge als `fix`, dazu was der Nutzer **anheftet**. Sie wird **nicht** mit
110 Einträgen geflutet. Das ist der Kern des Entwurfs — „angeheftet · Kontext · weitere".

**Entscheidung — Ehrlichkeit, und sie ist nicht verhandelbar:** Ein Werkzeug ohne Handler trägt
`in_entwicklung` mit Text, wie jede andere Fläche seit v1. **Der Unterschied zu den 15 DTP-Werkzeugen,
die wir gerade entfernt haben: diese hier kommen wirklich.** Ein Werkzeug, das so aussieht, als
könnte es etwas, ist ein Fehler — auch wenn es hübsch aussieht.

## Umfang

1. **Gruppen-Menüs füllen.** Die 22 Kategorien werden Gruppen der oberen Leiste. Je Eintrag:
   **Icon** (`/hausplaner/icons/tools/<deutsche-id>.svg`), **Label**, **Kürzel** falls vorhanden,
   und der **Zustand** aus `resolveToolState` (verfügbar / voraussetzung / in_entwicklung) als
   Farbe **und** Text.
2. **Anheften (★).** Aus jedem Gruppen-Eintrag lässt sich ein Werkzeug in die linke Leiste heben.
   Der Zustand ist **persönlich** und überlebt einen Neuladen. **Speicherort ist eine Entscheidung
   für dich, aber mit Grenze:** kein neues Feld im Szenendokument, kein Zod, kein Schema — das sind
   Bestandsdaten. `localStorage` oder ein Nutzer-Setting sind zulässig; die Szene ist es nicht.
3. **Zone `versteckt` leeren.** Nach I3 ist jedes der 110 Werkzeuge über seine Gruppe erreichbar.
   Bleibt eines versteckt, muss der Grund in der Regel stehen — nicht stillschweigend.

## Was ausdrücklich NICHT dazugehört

- **Kein Konfigurations-Modal „Leiste anpassen"** — das ist der nächste Posten (I4).
- **Keine Handler bauen.** Die 110 bekommen Fläche, keine Funktion. Wer rechnen soll, kommt in L2/L3.
- Keine Kontext-Empfehlung durch den Wizard (Zone `kontext` bleibt bei ihren zwei Einträgen).
- Kein Zod, kein Schema, keine Migration, kein PHP, `public/hausplaner/hausplaner.js` nicht anfassen.
- Die neun Bestands-IDs bleiben byte-genau.

## Kantenliste

1. **Eine Gruppe mit 15 Einträgen** (Bearbeiten, Architektur) sprengt das Menü → scrollbar oder
   zweispaltig, **nicht kappen**. Bei ~1375 px wird bereits an anderer Stelle gekappt (AUF-26);
   mach den Fehler nicht ein zweites Mal.
2. **Kategorien mit einem Eintrag** (TGA, Sanitär) — eigene Gruppe oder zusammenlegen? Entscheide und
   sag es im Bericht; eine Gruppe mit einem Eintrag ist zulässig, aber nenne die Wahl.
3. **Icon fehlt** → Platzhalter mit Grund, nicht leer und nicht abstürzen.
4. **Angeheftet + gesperrt gleichzeitig:** das Werkzeug bleibt sichtbar, ist deaktiviert und trägt
   den **Grund** im Tooltip. Genau dieser Fall steht in Yamas Entwurf als „◌ gesperrt".
5. **Kürzel:** die zehn kollidierenden Kürzel hat der Adapter in I2 bereits mit Grund verworfen —
   **nicht wieder einführen**.
6. **Fokus:** kein fokussierbares Steuerelement in einer im Rumpf von `HausplanerApp` definierten
   Komponente (Befund B1). Menüeinträge und Anheft-Sterne sind fokussierbar.

## Abnahmekriterien

1. `tsc:hausplaner` · `schema:hausplaner:check` (**ohne Regen**) · `test:hausplaner` — **Exit 0**.
   `build:hausplaner` als „nicht ausführbar" berichten, falls aarch64.
2. Testzahl vorher/nachher, **Namen-Mengen verglichen**, kein verschwundener Test.
3. Ein reines Datenmodul liefert die Gruppen: je Gruppe `id · label · werkzeuge[]`, Reihenfolge
   fest. Ein Test belegt: **22 Gruppen**, Summe der Einträge = **110**, keine Dublette, jedes
   Werkzeug in **genau einer** Gruppe.
4. `zoneTools('versteckt')` = **0** — oder jede verbliebene Ausnahme im Bericht einzeln begründet.
5. Jeder Eintrag trägt Icon-Pfad, Label und Zustand; **kein Eintrag ohne Zustand**. Test.
6. **Anheften:** ein Test belegt, dass Anheften/Lösen den persönlichen Zustand ändert und die linke
   Leiste daraus liest — **ohne** das Szenendokument zu berühren. `git diff` zeigt null Zeilen in
   `domain/` und keine Schema-Änderung.
7. **Gegen-Beweis, selbst geführt:** ein Werkzeug aus seiner Gruppe entfernen → mindestens ein Test
   **muss** rot werden (Summe 110 bzw. „jedes Werkzeug in genau einer Gruppe"). Danach zurückbauen,
   `git diff` leer.
8. **0 rohe Farbwerte in den geänderten Zeilen.**
9. `git diff` zeigt null Zeilen in `store/*`, `domain/*`, `geometry/*`, `renderers/*`, `public/*`,
   PHP, Migrationen.

## Guardrails

- Posten **auf der Tafel ziehen, bevor** die erste Zeile geschrieben wird.
- **Ein Commit**, Pfadangabe zwingend. **Nie `-A`, nie `.`**, `-m` **vor** dem `--`.
- `.git/*.lock` nur per `mv` nach `.git/_locks_beiseite/<datum>/`.
- **Kein Push, kein Merge, kein Deploy. „umgesetzt", nie „abgenommen".**
- Taucht Nötiges außerhalb des Umfangs auf: **zurückgeben**, nicht mitbauen.

## Bericht

`## ⇒ GENERATOR-BERICHT — I3 Werkzeuge sichtbar`, mit den neun Kriterien als Rohausgabe, der
Gruppen-Tabelle (Gruppe → Anzahl), dem Gegen-Beweis aus Kriterium 7, der Entscheidung zu Kante 2
und dem Commit-Hash.

**Danach ist eine Browser-Sichtprobe fällig** — das ist die erste Änderung, die Yama unmittelbar
sieht. Sie gehört in die Abnahme, nicht danach.
