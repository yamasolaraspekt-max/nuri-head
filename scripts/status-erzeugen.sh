#!/usr/bin/env bash
# ── A-41 · DIE STATUSWAHRHEIT WIRD ERZEUGT, NICHT GESCHRIEBEN ────────────────────────────────
#
# **Yamas Entscheidung vom 16.08.:** *„Der Zustandswechsel IST der Commit."* Keine Rolle bearbeitet
# `docs/STATUS.md` mehr; sie meldet einen Zustandswechsel als **Commit-Betreff** in festem Wortlaut,
# und der Integrator laesst daraus die Tafel erzeugen.
#
# ```text
#   zustand: A-33 · CODE_FERTIG · generator · bau 3e22e61b
#            ^^^^   ^^^^^^^^^^^   ^^^^^^^^^   ^^^^^^^^^^^^
#            Kennung  Zustand      Rolle       Beleg (frei)
#
#   WER   = git-Autor         nicht Prosa
#   WANN  = git-Zeitstempel   nicht Prosa
#   WAS   = Kennung, Zustand, Beleg
#   WO    = im eigenen Rollenzweig, sonst nirgends
# ```
#
# ## Warum das die Divergenz aufloest statt sie zu bewachen
#
# **Divergenz entsteht, weil SECHS Parteien dieselbe Datei BEARBEITEN duerfen.** *Jede Regel dagegen
# ist eine Regel darueber, wer bearbeiten darf — und jede solche Regel ist Disziplin.* **Wird die
# Datei ERZEUGT, gibt es nichts mehr zu bearbeiten:** die Frage faellt weg, statt beantwortet zu
# werden. *Derselbe Griff wie bei den getrennten Baeumen und bei A-33s Skript.*
#
# **Der Commit-Log ist die einzige Quelle, die alle sechs Zweige bereits GEMEINSAM haben** — sie
# teilen die Objektdatenbank. Reihenfolge und Autor sind darin nicht faelschbar.
#
# ## ⚠ GEMESSEN VOR DEM BAU: die Erzeugung hat heute NULL Eingabe
#
# ```text
#   Commits mit Betreff '^zustand:' ueber alle Zweige   0
#   Auftraege mit Datensatz in docs/STATUS.md         162
# ```
#
# **Der Wortlaut ist neu, also gibt es keine Historie in ihm.** *Eine Erzeugung gegen den heutigen
# Stand ergaebe eine leere Tafel, und die 162 Abweichungen waeren kein Befund ueber die Divergenz,
# sondern nur die Auskunft, dass niemand den neuen Wortlaut je benutzt hat.* **Deshalb hat dieses
# Skript einen dritten Modus: `--bootstrap`.**
#
# ## Die drei Betriebsarten
#
# ```text
#   --tafel       erzeugt die Statuswahrheit AUS DEM COMMIT-LOG.
#                 Je Kennung gewinnt der juengste Eintrag.
#                 Gleiche Zeit + verschiedener Zustand -> GEMELDET, nicht aufgeloest.
#
#   --bootstrap   liest die Zustaende aus ALLEN Zweigen (git show, kein Auscheck)
#                 und trennt: EINIG (seed-faehig) gegen UNEINIG (Entscheidung noetig).
#                 Loest NICHTS auf — Regel 4.
#
#   --vergleich   haelt das Erzeugnis gegen den heutigen Bestand und meldet die Abweichung.
#                 Weicht es ab, ist die ABWEICHUNG der Befund, nicht die Erzeugung.
# ```
#
# **Ohne Argument laeuft `--vergleich`** — der Modus, der nichts schreibt. *Ein Werkzeug, das die
# Statuswahrheit ueberschreiben kann, tut das nicht aus Versehen.*
#
# **Rueckgabe:** 0 wenn einig · 1 bei Widerspruch oder Abweichung · 2 bei Aufrufsfehler.
set -uo pipefail
cd "$(dirname "$0")/.."

MODUS="vergleich"
case "${1:-}" in
  --tafel)     MODUS="tafel" ;;
  --bootstrap) MODUS="bootstrap" ;;
  --vergleich|"") MODUS="vergleich" ;;
  --fangprobe) MODUS="fangprobe" ;;
  *) echo "Unbekanntes Argument: $1" >&2; exit 2 ;;
esac

export MODUS
python3 - <<'PY'
import os, re, subprocess, sys, datetime

MODUS = os.environ["MODUS"]

# ── DER WORTLAUT, festgeschrieben ───────────────────────────────────────────────────────────
# Der Mitteltrenner ist U+00B7 mit je einem Leerzeichen. Die Kennung folgt dem Hausmuster
# (A-33, W-05/2, B5, W-21L). Der Beleg ist frei und wird NICHT geprueft — er ist Auskunft,
# keine Bedingung.
# **⚠ DIE ROLLENMARKE STEHT DAVOR, und das ist keine Auslegung, sondern eine Messung.**
# Yamas Vorgabe lautet `zustand: A-33 · …` als BETREFF. Das Tor stellt jedoch die Rollenmarke
# ZWINGEND voran (`commit-pruefen.sh:113-114`, „Keine Rollenmarke … voranstellen"). **Ein Betreff,
# der mit `zustand:` beginnt, ist ueber das Tor nicht erzeugbar** — und der Planner hat es bereits
# umgangen: `planner: zustand: A-41 · ENTWURF · planner · blatt e521bd98`.
#
# *Das Muster nimmt deshalb BEIDE Formen.* **Gemeldet statt still umgeschrieben:** ob die
# Rollenmarke entfallen soll oder Teil des Wortlauts wird, entscheidet nicht dieses Skript.
WORTLAUT = re.compile(
    r"^(?:[a-z-]+(?:-[0-9]+)?:\s+)?zustand:\s+"
    r"(?P<kennung>[A-Z]+-?[0-9]+[A-Za-z]?(?:/[0-9A-Za-z]+)?)\s+·\s+"
    r"(?P<zustand>[A-Z_]+)\s+·\s+"
    r"(?P<rolle>[a-z-]+(?:-[0-9]+)?)"
    r"(?:\s+·\s+(?P<beleg>.*))?$"
)

def lauf(*a):
    return subprocess.run(a, capture_output=True, text=True).stdout

def zeit(ts):
    return datetime.datetime.fromtimestamp(int(ts)).strftime("%d.%m %H:%M")

# ── FANGPROBE: das Muster wird gegen echte Faelle gehalten, BEVOR es zaehlt ──────────────────
if MODUS == "fangprobe":
    faelle = [
        ("zustand: A-33 · CODE_FERTIG · generator · bau 3e22e61b", True,  "der Regelfall"),
        ("zustand: W-05/2 · ABGENOMMEN · evaluator",               True,  "Suffix-Kennung, ohne Beleg"),
        ("zustand: B5 · BETRIEBSBESTAETIGT · release-pruefer · x", True,  "Kennung ohne Bindestrich"),
        ("zustand: W-21L · BEREIT · plan-pruefer-2",               True,  "Rolle mit Instanznummer"),
        ("zustand: A-33 - CODE_FERTIG - generator",                False, "Bindestrich statt · — MUSS scheitern"),
        ("Zustand: A-33 · CODE_FERTIG · generator",                False, "grosses Z — MUSS scheitern"),
        ("generator: A-33 auf CODE_FERTIG gesetzt",                False, "Prosa — MUSS scheitern"),
        ("zustand: A-33 · code_fertig · generator",                False, "Zustand klein — MUSS scheitern"),
        ("planner: zustand: A-41 · ENTWURF · planner · blatt x",   True,  "MIT Rollenmarke — das Tor stellt sie voran"),
        ("release-pruefer: zustand: B5 · ABGENOMMEN · evaluator",  True,  "Rollenmarke mit Bindestrich"),
    ]
    rot = 0
    for text, erwartet, warum in faelle:
        ist = bool(WORTLAUT.match(text))
        ok = ist == erwartet
        rot += 0 if ok else 1
        print(f"  {'✔' if ok else '✖'} {'trifft' if erwartet else 'trifft nicht'}  {warum}")
        print(f"      {text}")
    print(f"\n  Fangprobe: {len(faelle)-rot}/{len(faelle)} wie erwartet")
    sys.exit(1 if rot else 0)

# ── DIE ERZEUGUNG AUS DEM COMMIT-LOG ────────────────────────────────────────────────────────
def aus_dem_log():
    """Je Kennung der juengste Eintrag. Gleiche Zeit + anderer Zustand -> Widerspruch."""
    # Das grep-Muster nimmt beide Formen: mit und ohne vorangestellte Rollenmarke.
    roh = lauf("git", "log", "--all", "--grep=^\\(\\w\\+[a-z-]*: \\)\\?zustand:",
               "--format=%H%x09%at%x09%an%x09%s")
    treffer, verworfen = {}, []
    for zeile in roh.split("\n"):
        if not zeile.strip():
            continue
        sha, ts, autor, betreff = zeile.split("\t", 3)
        m = WORTLAUT.match(betreff)
        if not m:
            verworfen.append((sha[:8], betreff))
            continue
        k = m.group("kennung")
        eintrag = {"ts": int(ts), "sha": sha[:8], "autor": autor,
                   "zustand": m.group("zustand"), "rolle": m.group("rolle"),
                   "beleg": m.group("beleg") or ""}
        treffer.setdefault(k, []).append(eintrag)
    tafel, widerspruch = {}, []
    for k, liste in treffer.items():
        liste.sort(key=lambda e: -e["ts"])
        juengste = [e for e in liste if e["ts"] == liste[0]["ts"]]
        zustaende = {e["zustand"] for e in juengste}
        if len(zustaende) > 1:
            widerspruch.append((k, juengste))   # Regel 4: melden, nicht aufloesen
        tafel[k] = liste[0]
    return tafel, widerspruch, verworfen

# ── DIE ZUSTAENDE, WIE SIE HEUTE IN DEN ZWEIGEN STEHEN ──────────────────────────────────────
def aus_den_zweigen():
    zweige = [z for z in lauf("git", "for-each-ref", "--format=%(refname:short)",
                              "refs/heads/rolle/*", "refs/heads/auto/hausplaner-integration").split("\n") if z]
    je_kennung = {}
    for z in zweige:
        text = lauf("git", "show", f"{z}:docs/STATUS.md")
        cur = None
        for l in text.split("\n"):
            if l.startswith("auftrag:"):
                m = re.match(r'^auftrag: "([^"]+)"', l)
                cur = m.group(1) if m else None
            elif cur and l.startswith("zustand:"):
                je_kennung.setdefault(cur, {}).setdefault(l[8:].split("#")[0].strip(), []).append(z)
                cur = None
    return zweige, je_kennung

if MODUS == "tafel":
    tafel, widerspruch, verworfen = aus_dem_log()
    print(f"# Statuswahrheit — ERZEUGT aus dem Commit-Log, {len(tafel)} Kennungen\n")
    for k in sorted(tafel):
        e = tafel[k]
        print(f"  {k:<10} {e['zustand']:<20} {e['rolle']:<16} {e['sha']}  {zeit(e['ts'])}  {e['beleg'][:40]}")
    if verworfen:
        print(f"\n  NICHT IM WORTLAUT, deshalb nicht gezaehlt: {len(verworfen)}")
        for sha, b in verworfen[:5]:
            print(f"    {sha}  {b[:90]}")
    if widerspruch:
        print(f"\n  WIDERSPRUCH bei gleicher Zeit — GEMELDET, NICHT aufgeloest (Regel 4): {len(widerspruch)}")
        for k, eintraege in widerspruch:
            for e in eintraege:
                print(f"    {k:<10} {e['zustand']:<20} {e['rolle']:<16} {e['sha']}  {zeit(e['ts'])}")
    sys.exit(1 if widerspruch else 0)

if MODUS == "bootstrap":
    zweige, je_kennung = aus_den_zweigen()
    einig = {k: list(v)[0] for k, v in je_kennung.items() if len(v) == 1}
    uneinig = {k: v for k, v in je_kennung.items() if len(v) > 1}
    print(f"# BOOTSTRAP — {len(zweige)} Zweige gelesen, keiner ausgecheckt\n")
    print(f"  EINIG, seed-faehig ohne Entscheidung: {len(einig)} von {len(je_kennung)}")
    print(f"  UNEINIG, brauchen eine Entscheidung:  {len(uneinig)}\n")
    for k in sorted(uneinig):
        print(f"    {k}")
        for zustand, zs in sorted(uneinig[k].items()):
            print(f"      {zustand:<20} laut {', '.join(sorted(z.split('/')[-1] for z in zs))}")
    print(f"\n  Regel 4: hier wird NICHTS aufgeloest. Die {len(uneinig)} Faelle gehoeren dem Integrator.")
    sys.exit(1 if uneinig else 0)

# ── VERGLEICH: Erzeugnis gegen den heutigen Bestand ─────────────────────────────────────────
tafel, widerspruch, verworfen = aus_dem_log()
_, je_kennung = aus_den_zweigen()
print(f"# VERGLEICH — Erzeugnis gegen den heutigen Bestand\n")
print(f"  aus dem Commit-Log erzeugt:      {len(tafel)} Kennungen")
print(f"  im heutigen Bestand vorhanden:   {len(je_kennung)} Kennungen")
fehlend = sorted(set(je_kennung) - set(tafel))
if fehlend:
    print(f"\n  NUR IM BESTAND, nicht im Log: {len(fehlend)}")
    print(f"    -> der Wortlaut ist neu; ohne Bootstrap hat die Erzeugung keine Eingabe.")
    print(f"    -> {', '.join(fehlend[:12])}{' …' if len(fehlend) > 12 else ''}")
neu = sorted(set(tafel) - set(je_kennung))
if neu:
    print(f"\n  NUR IM LOG, nicht im Bestand: {len(neu)}  -> {', '.join(neu[:12])}")
abweichend = [k for k in sorted(set(tafel) & set(je_kennung))
              if list(je_kennung[k]) != [tafel[k]["zustand"]]]
if abweichend:
    print(f"\n  BEIDE kennen sie, ZUSTAND weicht ab: {len(abweichend)}")
    for k in abweichend:
        print(f"    {k:<10} Log: {tafel[k]['zustand']:<20} Bestand: {', '.join(sorted(je_kennung[k]))}")
sys.exit(1 if (fehlend or neu or abweichend or widerspruch) else 0)
PY
