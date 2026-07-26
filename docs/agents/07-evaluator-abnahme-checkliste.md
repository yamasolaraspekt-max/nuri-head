# §13 — Evaluator-Abnahme-Checkliste (jedes Mal abhaken)

> Positiv-Gegenstück zu §12 („was der Evaluator nicht darf"). Auf Yamas Anweisung angelegt
> („schaff Kriterien, damit du sorgfältige, gewissenhafte Arbeit sicherstellst — kein Fehler mehr").
> Anlass: eine gemessene Full-CRM-Zahl wurde in den Chat gemeldet, nicht in den Ledger — aus
> Planner-Sicht war die Merge-Bedingung damit offen, obwohl die Arbeit getan war. Diese Liste wird
> vor jedem „fertig" durchgegangen. Kein Punkt wird übersprungen, weil er offensichtlich ist.

## A · Setup (bevor gemessen wird)
- [ ] **Ballbesitz bestätigt:** der Posten ist `BERICHTET`, committet, mit fester SHA. Uncommittete/
      fremde WIP wird nicht abgenommen — nur geflaggt.
- [ ] **Reihenfolge:** erst blind gegen die SHA messen, DANN den Generator-Bericht lesen.
- [ ] **/tmp-Auszug:** `git archive <SHA> | tar -x -C /tmp/…`, node_modules symlinken. Kein
      `checkout`/`stash`/`worktree`. Mutationen laufen ausschließlich auf der Kopie.

## B · Messung (je Slice)
- [ ] **Alle vier Insel-Gates selbst gefahren:** tsc · schema:check (kein Drift) · test · build —
      Exit-Codes + Testzähler notiert.
- [ ] **Klasse benannt:** sichtbar oder Vorarbeit.
- [ ] **sichtbar ⇒ Sichtprobe** (Regel 11): im **ungünstigsten Zustand** messen, den **Zustand im
      Votum nennen**, Ausgangs- **und** Ergebnisgröße, gegen den **ausgelieferten** Stand (serviertes
      Bundle == Quell-SHA geprüft).
- [ ] **Berührt eine Blade/PHP?** → Regel #9: PHP-Suite (`ticket_testing`) + betroffene Route in die
      Sichtprobe + Browser-Konsole. Fehlt der Zugang → Befund, nicht überspringen.
- [ ] **Berührt Schema/Domain?** → additive-only: keine Migration, `schema:hausplaner` regeneriert
      (kein 422-Drift), Bestandsdokumente gültig, fehlendes Feld hat eine ehrliche Bedeutung.
- [ ] **Tor-1-Blick:** eine Datei unter `routes/` · `app/Http/` · `database/migrations/`? → das ist
      Yamas Freigabe, nicht meine.
- [ ] **Gegen-Beweis:** Mutation an der **einen** Quelle → die erwarteten Tests werden **rot**
      (Zähne). Kein grün ohne roten Gegen-Beweis. Nur auf der /tmp-Kopie.
- [ ] **Jede Zahl selbst erzeugt**, Rohausgabe daneben. Keine Zahl aus dem Bericht übernommen.
- [ ] **Eigene Messfehler offengelegt** (Beweis gilt gegen mich): falsche Quelle, zu breiter Grep,
      falscher Reporter, ungemessene Ursache — benannt, nicht überspielt.

## C · Handoff (der Punkt, der schiefging — Pflicht)
- [ ] **Votum in die Abnahme-Datei** (`docs/abnahme-…`), per Pfad committet (kein `git add -A`).
- [ ] **⚠ ÜBERGABE-REGEL:** *Alles, was eine andere Rolle zum Handeln braucht, gehört in den LEDGER
      (`docs/handoff-status.md`) — nicht nur in den Chat.* Das umfasst: Voten-Zusammenfassung,
      **gemessene Suiten-/Merge-Zahlen**, Befunde für den Planner, Rückgaben, offene Auflagen. Der
      Chat an Yama ist **keine** Übergabe. Faustregel: „Wenn der Planner/Yama danach handeln soll und
      es nur im Chat steht — ist es nicht übergeben."
- [ ] **Ballbesitz benannt** (wer als Nächstes am Ball ist).
- [ ] **Klassifikation + Auflagen** eindeutig: FREIGABE · FREIGABE MIT AUFLAGE · NACHBESSERN · ROT —
      und eine Auflage/ein Rot mit **reproduzierbarem Fall** (sonst ist es eine Rückfrage).

## D · Selbst-Check vor „fertig"
- [ ] **Chat-vs-Ledger:** Habe ich etwas nur in den Chat gesagt, wonach eine andere Rolle handelt
      (Zahl, Bedingung, Befund)? → in den Ledger nachtragen, **bevor** ich „fertig" melde.
- [ ] **Frisch gemessen:** Behaupte ich keine Zahl, die ich nicht gegen den **jetzigen** HEAD selbst
      erzeugt habe (Zustand kann seit einer alten Messung gewandert sein).
- [ ] **§6 Baum-Sauberkeit:** `git status` nach der Messung identisch zu vorher; kein Beifang; fremde
      WIP nur gemeldet.
- [ ] **§12 nicht verletzt:** nicht repariert, keinen Posten erfunden, Umfang nicht geweitet, keinen
      Test geschwächt, nichts abgenommen was ich mitentworfen habe, nicht gepusht/gemergt/deployt.

---

**Die eine harte Regel, wenn nur eine bleibt:** *Gemessen ist nicht gemeldet, und Chat ist nicht
Ledger.* Erst wenn das Ergebnis dort steht, wo die nächste Rolle es liest, ist die Arbeit übergeben.
