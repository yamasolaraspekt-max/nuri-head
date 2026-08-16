#!/usr/bin/env bash
# ── DIE WECK-RUNDE NACH DEM UMZUG — sie liest ALLE Zweige, nicht einen ───────────────────────
#
# **Der Anlass, und er betrifft jeden im Haus.** Vor dem Umzug gab es EINE Linie; ein Wecker, der
# `docs/STATUS.md` im Arbeitsbaum las, las damit „den Stand". Seit dem Umzug gibt es SECHS Linien,
# und derselbe Wecker liest eine davon — still und mit gruenem Lauf.
#
# **Gemessen am 16.08., als der Defekt auffiel:** `A-33` stand in `rolle/generator` auf
# `CODE_FERTIG` und in **fuenf** anderen Zweigen auf `BEREIT`. *Keiner der sechs Leser mass falsch
# — sie massen alle richtig, nur nicht dasselbe.* Der Generator-Wecker haette den Auftrag ein
# zweites Mal gezogen; die Fortschrittstafel meldete „nur A-33 wartet", obwohl er fertig war.
#
# > ***Und die naheliegende Reparatur ist die falsche.*** *„Ich stelle meinen Wecker auf MEINEN
# > Baum um" behebt die eigene Blindheit und erzeugt fuenf neue* — **statt einer gemeinsamen
# > Blindstelle haetten wir sechs private Halbwahrheiten.** *Dieselbe Klasse wie ein geteiltes
# > `node_modules`, eine Ebene hoeher: der Lauf ist gruen und misst den falschen Stand.*
#
# ---
#
# ## Die vier Regeln, nach denen dieses Werkzeug gebaut ist
#
# ```text
# 1  ALLE Zweige lesen — rolle/* und auto/hausplaner-integration.
#    Ueber `git show <zweig>:docs/STATUS.md`: kein Auscheck, kein Zweigwechsel,
#    KEINE Beruehrung eines fremden Arbeitsbaums.
# 2  Zustand je Auftrag = der aus dem Zweig, der ihn ZULETZT geaendert hat.
#    Nicht der eigene, nicht der des Integrationszweigs.
# 3  Jede Meldung nennt ZWEIG UND SHA. Ohne beides ist sie unvollstaendig.
# 4  WIDERSPRUCH WIRD GEMELDET, NICHT AUFGELOEST. Zwei Zweige, zwei Zustaende
#    -> beide anzeigen, mit Zweig und Zeit. Der Wecker entscheidet nicht.
# ```
#
# **Regel 4 ist Regel 4 der Umzugsentscheidung, auf die Messebene gezogen** — *„Konflikte werden
# sichtbar gemeldet und niemals still aufgeloest."* Das galt fuer den Integrator; seit dem Umzug
# gilt es fuer jeden, der misst.
#
# ## Und die Regel dahinter, die sich durch den Umzug geaendert hat
#
# ```text
# VOR    Unveraenderlich traegt einen SHA.       Eine Linie, ein SHA genuegte.
# SEIT   Unveraenderlich traegt ZWEIG UND SHA.   Sechs Linien; ein SHA ohne Zweig
#        Fluechtig traegt einen Zeitstempel.     sagt nicht, WELCHE Wahrheit gemeint ist.
# ```
#
# ## Aufruf
#
# ```
#   bash scripts/weck-runde.sh --rolle generator      was FUER MICH frei ist, plus die Lage
#   bash scripts/weck-runde.sh                        nur die Lage, ohne Ballfilter
#   bash scripts/weck-runde.sh --einzeilig            eine Zeile, fuer einen Monitor
# ```
#
# **Rueckgabe:** 0 immer — dies ist ein Melder, keine Barriere.
set -uo pipefail
cd "$(dirname "$0")/.."

ROLLE=""
FORM="lang"
while [ "$#" -gt 0 ]; do
  case "$1" in
    --rolle)     ROLLE="${2:-}"; shift 2 ;;
    --einzeilig) FORM="kurz"; shift ;;
    *) echo "Unbekanntes Argument: $1" >&2; exit 2 ;;
  esac
done

# Regel 1: die Zweige werden AUFGEZAEHLT, nicht geraten — und nur die, die es wirklich gibt.
ZWEIGE="$(git for-each-ref --format='%(refname:short)' refs/heads/rolle refs/heads/rolle/* refs/heads/auto/hausplaner-integration 2>/dev/null | sort -u)"
[ -z "$ZWEIGE" ] && { echo "WECK-RUNDE  keine Zweige gefunden" >&2; exit 0; }

export ROLLE FORM
# shellcheck disable=SC2016
python3 - $ZWEIGE <<'PY'
import os, re, subprocess, sys, datetime

ROLLE = os.environ.get("ROLLE", "")
FORM  = os.environ.get("FORM", "lang")
zweige = sys.argv[1:]

def lauf(*a):
    return subprocess.run(a, capture_output=True, text=True).stdout

def status_aus(zweig):
    """Regel 1: lesen ueber git show — kein Auscheck, kein fremder Arbeitsbaum."""
    return lauf("git", "show", f"{zweig}:docs/STATUS.md")

def bloecke(text):
    b, cur = {}, None
    for l in text.split("\n"):
        if l.startswith("auftrag:"):
            m = re.match(r'^auftrag: "([^"]+)"', l)
            cur = {"z": None, "k": None}
            if m: b[m.group(1)] = cur
        elif cur is not None:
            if l.startswith("zustand:") and cur["z"] is None: cur["z"] = l[8:].split("#")[0].strip()
            elif l.startswith("ballbesitz:") and cur["k"] is None: cur["k"] = l[11:].split("#")[0].strip()
    return b

def kopf(zweig):
    """Regel 3: Zweig UND SHA, dazu die Zeit des letzten STATUS-Commits auf diesem Zweig."""
    sha = lauf("git", "rev-parse", "--short", zweig).strip()
    ts  = lauf("git", "log", "-1", "--format=%ct", zweig, "--", "docs/STATUS.md").strip()
    return sha, int(ts) if ts.isdigit() else 0

def zuletzt_geaendert(zweig, kennung):
    """Regel 2: wann hat DIESER Zweig zuletzt an DIESEM Auftrag geschrieben?
    Gemessen ueber -G auf die Kennung in docs/STATUS.md — es trifft hinzugefuegte UND
    entfernte Zeilen, also auch einen reinen Zustandswechsel. Die Methode steht hier,
    damit niemand die Zahl fuer genauer haelt, als sie ist."""
    t = lauf("git", "log", "-1", "--format=%ct|%h", "-G", re.escape(kennung), zweig, "--", "docs/STATUS.md").strip()
    if not t or "|" not in t: return 0, "-"
    ts, sha = t.split("|", 1)
    return (int(ts) if ts.isdigit() else 0), sha

def zeit(ts):
    return datetime.datetime.fromtimestamp(ts).strftime("%d.%m %H:%M") if ts else "—"

stand = {}
for z in zweige:
    sha, ts = kopf(z)
    stand[z] = {"sha": sha, "ts": ts, "auftraege": bloecke(status_aus(z))}

# --- Die Lage der Zweige zueinander -----------------------------------------------------------
basis = "auto/hausplaner-integration" if "auto/hausplaner-integration" in stand else zweige[0]
lage = []
for z in zweige:
    vor  = lauf("git", "rev-list", "--count", f"{basis}..{z}").strip() or "0"
    zur  = lauf("git", "rev-list", "--count", f"{z}..{basis}").strip() or "0"
    lage.append((z, stand[z]["sha"], int(vor), int(zur), stand[z]["ts"]))

voraus_je_zweig = {z: v for z, _, v, _, _ in lage}

# --- Regel 4: Widersprueche sammeln, NICHT aufloesen -------------------------------------------
#
# ABER: ein Zweig, der 0 voraus ist, WIDERSPRICHT nicht — er ist nur nicht nachgezogen. Sein
# Zustand ist vollstaendig im Integrationszweig enthalten, er sagt also nichts Eigenes, sondern
# etwas Aelteres. Beides als "Widerspruch" zu melden macht die Meldung unbrauchbar: heute waeren
# es NEUN, davon sieben allein aus zwei stehengebliebenen Zweigen. Nach A-03 wird eine Meldung,
# die zu oft kommt, weggeklickt — und dann faellt der echte Widerspruch mit weg.
alle_kennungen = sorted({k for z in stand for k in stand[z]["auftraege"]})
offen = ("BEREIT", "IN_ARBEIT", "NACHBESSERN", "CODE_FERTIG", "SPEC_BLOCKED", "ENV_BLOCKED")
widerspruch, meins, para3 = [], [], 0

for k in alle_kennungen:
    sicht = {}
    for z in stand:
        a = stand[z]["auftraege"].get(k)
        if a and a["z"]: sicht.setdefault(a["z"], []).append(z)
    if not sicht: continue
    if len(sicht) > 1:
        eintrag = []
        for zust, zs in sicht.items():
            best_ts, best_z, best_sha = 0, zs[0], "-"
            for z in zs:
                ts, sha = zuletzt_geaendert(z, k)
                if ts > best_ts: best_ts, best_z, best_sha = ts, z, sha
            eintrag.append((zust, best_z, best_sha, best_ts, len(zs)))
        eintrag.sort(key=lambda e: -e[3])
        # ECHT ist der Widerspruch nur, wenn mindestens ZWEI Zustaende von Zweigen kommen,
        # die eigene Commits tragen. Sonst ist es Rueckstand, kein Streit.
        eigenstaendig = [e for e in eintrag if any(voraus_je_zweig.get(z, 0) > 0 for z in sicht[e[0]])]
        widerspruch.append((k, eintrag, len(eigenstaendig) > 1))
        zust_gilt, z_gilt = eintrag[0][0], eintrag[0][1]
    else:
        zust_gilt = next(iter(sicht))
        z_gilt = sicht[zust_gilt][0]
    if zust_gilt == "IN_ARBEIT": para3 += 1
    # FREI ist ein Auftrag nur, wenn ALLE Zweige, die ihn kennen, dasselbe sagen.
    #
    # **Warum nicht "der juengste Zustand gilt".** Genau das war der erste Lauf dieses Werkzeugs:
    # es meldete A-33 als frei, weil ein TRANSPORT die Kennung zuletzt beruehrt und dabei den
    # AELTEREN Zustand mitgebracht hatte — der Auftrag war laengst CODE_FERTIG. Eine Frei-Liste,
    # die sich auf die -G-Heuristik stuetzt, schickt die Rolle in fertige Arbeit zurueck.
    #
    # **Die sichere Richtung ist die strengere:** bei Uneinigkeit gilt der Auftrag als NICHT frei
    # und erscheint stattdessen im Widerspruch. Ein uebersehener freier Auftrag kostet eine Runde
    # Warten; ein doppelt gezogener kostet einen ganzen Bau — und im schlimmsten Fall ueberschreibt
    # er die Arbeit, die schon abgenommen war.
    if ROLLE and zust_gilt in ("BEREIT", "NACHBESSERN") and len(sicht) == 1:
        a = stand[z_gilt]["auftraege"][k]
        if (a["k"] or "").startswith(ROLLE): meins.append((k, zust_gilt, z_gilt))

if FORM == "kurz":
    ew = [x for x in widerspruch if x[2]]
    w = " · ".join(f"{k}:{'/'.join(e[0] for e in ev)}" for k, ev, _ in ew) or "keiner"
    w = f"{w} (+{len(widerspruch)-len(ew)} nur Rueckstand)" if len(widerspruch) > len(ew) else w
    m = ", ".join(f"{k}({z})" for k, z, _ in meins) or "keine"
    rueck = " · ".join(f"{z.split('/')[-1]}+{v}-{r}" for z, _, v, r, _ in lage if v or r) or "alle gleich"
    print(f"WECK-RUNDE | Paragraf3={para3} | fuer {ROLLE or '—'}: {m} | WIDERSPRUCH: {w} | Zweige: {rueck}")
    raise SystemExit

print("WECK-RUNDE — alle Zweige gelesen, keiner ausgecheckt")
print(f"  {'Zweig':<32} {'SHA':<10} {'voraus':>6} {'zurueck':>8}   letzter STATUS-Commit")
for z, sha, vor, zur, ts in sorted(lage, key=lambda x: -x[4]):
    print(f"  {z:<32} {sha:<10} {vor:>6} {zur:>8}   {zeit(ts)}")

print(f"\n  Paragraf 3 (IN_ARBEIT, ueber alle Zweige): {para3}")

if widerspruch:
    echte = [w for w in widerspruch if w[2]]
    print(f"\n  WIDERSPRUCH — {len(widerspruch)} Auftrag/Auftraege, die Zweige sagen Verschiedenes.")
    print(f"  Davon ECHT (mindestens zwei Zweige mit eigenen Commits): {len(echte)}.")
    print(f"  Die uebrigen {len(widerspruch)-len(echte)} kommen aus Zweigen, die 0 voraus sind —")
    print("  die widersprechen nicht, sie sind nur nicht nachgezogen.")
    print("  NICHT aufgeloest: angezeigt wird JEDER Zustand mit Zweig, SHA und Zeit (Regel 4).")
    print("  Sortiert nach der juengsten Schreibung, die die Kennung beruehrt (git log -G).")
    print("  ACHTUNG, die Grenze der Methode: -G trifft auch einen TRANSPORT, der einen aelteren")
    print("  Zustand mitbringt. Die oberste Zeile ist die juengste SCHREIBUNG, nicht zwingend der")
    print("  juengste ZUSTAND — deshalb steht hier keine Aufloesung, sondern die ganze Liste.")
    for k, ev, echt in widerspruch:
        print(f"    {k}   {'ECHT' if echt else '(nur Rueckstand)'}")
        for i, (zust, z, sha, ts, n) in enumerate(ev):
            marke = "juengste Schreibung ->" if i == 0 else "                     "
            weitere = f"  (+{n-1} weitere Zweige)" if n > 1 else ""
            print(f"      {marke} {zust:<14} {z:<30} {sha:<10} {zeit(ts)}{weitere}")
else:
    print("\n  WIDERSPRUCH: keiner — alle Zweige sagen dasselbe.")

if ROLLE:
    print(f"\n  FUER {ROLLE.upper()} FREI:")
    if meins:
        for k, zust, z in meins: print(f"    {k:<10} {zust:<14} laut {z}")
    else:
        print("    nichts")
PY
