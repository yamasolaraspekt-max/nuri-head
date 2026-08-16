#!/usr/bin/env python3
# ── DER RUECKSTAND DES EVALUATORS — GEMESSEN STATT JEDEN TAKT NEU GETIPPT ────────────────────
#
# Der Evaluator beginnt jeden Takt mit derselben Frage: liegt etwas bei mir? Bis heute wurde die
# Frage mit einem frisch getippten Einzeiler beantwortet — und drei Fehler dieses Einzeilers sind
# am 16.08. nachgewiesen worden, jeder mit Wirkung:
#
#   E2  Das Kennungsmuster `[AWZ]-[0-9]+(/[0-9]+)?` verfehlt jede Kennung mit Suffixbuchstaben
#       oder ohne Bindestrichziffer: W-07N, W-01N, W-21L, B5, B5N, B6, B7, P-02..P-09.
#       WIRKUNG, gemessen: W-07N wurde als "kein Zustandsfeld in STATUS.md" gemeldet, obwohl der
#       Datensatz auf Z.4264 steht. Ein Ball in einer dieser Kennungen bliebe unsichtbar.
#
#   E3  Der Tafelzustand wurde als ERSTER `WORT` der Zeile gelesen statt aus SPALTE 2.
#       WIRKUNG, gemessen: A-16 heisst "`TIME_VARS` im Produktivbaum" — der Titel traegt einen
#       Backtick-Begriff, und der Zustand wurde als "TIME_VARS" gelesen statt BETRIEBSBESTAETIGT.
#       Zusaetzlich faellt A-06 durch, weil sein Zustand `**ERLEDIGT**` OHNE Backticks steht.
#       Beides erschien als Tafel/Datensatz-Divergenz, die es nicht gibt.
#
#   E4  Ein `auftrag:`-Block ist nicht immer ein Auftragsdatensatz. Rollen legen unter demselben
#       Schluessel BEFUNDBLOECKE ab (Felder titel/rolle/zeit/mess_stand). Wer beide gleich liest,
#       haelt `zustand: BEFUND` fuer einen Auftragszustand — P-04 hat AUSSCHLIESSLICH solche
#       Bloecke, A-40 hat einen Datensatz (ENTWURF) und sechzehn Befunde.
#
#       ERSTE FASSUNG DIESER BEHEBUNG WAR SELBST FALSCH und wurde vor dem Commit verworfen:
#       sie erkannte den Datensatz an blatt/art/spur/prioritaet. Nachgemessen fielen damit
#       ZWANZIG echte Datensaetze durch — A-01..A-05, A-07, A-18, A-19, W-02/1, W-04/1, W-05/1,
#       W-08/1, W-11/1, W-13/1, W-21/1, W-22/1, W-20, W-23, W-27, B5N tragen keines dieser
#       Felder, sondern basis_sha/datei/betriebspruefung. Die tragfaehige Regel steht in den
#       Daten und nicht in der Erwartung: BEFUND = traegt `rolle` UND `zeit`. Ein Datensatz ist,
#       was `zustand` traegt und kein Befund ist — 87 Stueck. Gegenprobe auf einem zweiten,
#       unabhaengigen Weg: die Statustafel vor dem ersten Zaun hat exakt 87 Zeilen.
#
# NICHT behoben, weil GEMESSEN UND WIDERLEGT: die Vermutung, bei mehreren Bloecken einer Kennung
# muesse der SPAETERE gewinnen. In 12 von 12 Faellen ist der ERSTE Block der Datensatz, in 0
# Faellen steht er spaeter. Die "Behebung" haette A-40 von ENTWURF auf BEFUND verfaelscht.
# Der erste Datensatzblock gewinnt — und Befundbloecke zaehlen gar nicht erst mit (E4).
#
# ABGRENZUNG, damit keine zweite Wahrheit entsteht: `scripts/a26-ball-drift.sh` misst die Drift
# zwischen Tafelzeile und Datensatz AM TOR und bleibt dafuer zustaendig. Dieses Werkzeug
# beantwortet eine andere Frage — den Rueckstand EINER Rolle ueber MEHRERE Zweige.
#
#   python3 scripts/evaluator-rueckstand.py                  die sechs Orte, Rolle evaluator
#   python3 scripts/evaluator-rueckstand.py --rolle generator
#   python3 scripts/evaluator-rueckstand.py --ref HEAD --ref fork/auto/hausplaner-integration
#   python3 scripts/evaluator-rueckstand.py --fangprobe      die drei Fehler oben, alt gegen neu
#
# Rueckgabe 0 = gemessen, 1 = Fangprobe rot, 2 = Aufbaufehler (kein Repository, Ref fehlt).

import argparse
import re
import subprocess
import sys

ORTE = [
    "HEAD",
    "fork/auto/hausplaner-integration",
    "rolle/generator",
    "rolle/release-pruefer",
    "rolle/planner",
    "rolle/plan-pruefer",
]

# Ein BEFUNDBLOCK traegt `rolle` UND `zeit` — eine Rolle, die zu einer Zeit etwas gemessen hat.
# Ein DATENSATZ traegt `zustand` und ist kein Befund. An den 258 Bloecken des heutigen Standes
# gemessen: 87 Datensaetze, 171 uebrige. Genau EIN Block traegt beides samt zustand — P-03 mit
# `zustand: BEFUND`, also richtigerweise ein Befund.
BEFUND_FELDER = {"rolle", "zeit"}

# Zustaende, in denen die Abnahme hinter dem Auftrag liegt.
ABGESCHLOSSEN = {"ABGENOMMEN", "FREIGEGEBEN", "BETRIEBSBESTAETIGT", "ERLEDIGT", "ABGESCHLOSSEN"}

ZAUN = ("```", "```yaml")


def status_lesen(ref, datei="docs/STATUS.md"):
    """Den Inhalt von docs/STATUS.md an einem Ref holen. Ohne Ref: der Arbeitsbaum."""
    if ref is None:
        with open(datei, encoding="utf-8") as f:
            return f.read()
    p = subprocess.run(
        ["git", "--no-optional-locks", "show", f"{ref}:{datei}"],
        capture_output=True, text=True,
    )
    if p.returncode != 0:
        return None
    return p.stdout


def bloecke(text):
    """Alle auftrag:-Bloecke als (kennung, zeile, art, felder).

    E4: `art` ist DATENSATZ oder BEFUND — entschieden an den Feldern, nicht am Namen.
    E2: die Kennung wird NICHT gegen ein Muster gefiltert; jeder Datensatz zaehlt.
    """
    L = text.split("\n")
    raus = []
    for i, zeile in enumerate(L):
        m = re.match(r'^auftrag: "?([^"\s]+)"?\s*$', zeile)
        if not m:
            continue
        felder = {}
        for x in L[i + 1:]:
            if x.rstrip() in ZAUN:
                break
            f = re.match(r"^([a-z_0-9]+):(.*)$", x)
            if f and f.group(1) not in felder:
                felder[f.group(1)] = f.group(2).split("#")[0].strip().strip('"')
        befund = BEFUND_FELDER <= set(felder)
        art = "BEFUND" if befund else ("DATENSATZ" if "zustand" in felder else "FRAGMENT")
        raus.append((m.group(1), i + 1, art, felder))
    return raus


def datensaetze(text):
    """Je Kennung der ERSTE Datensatzblock. Befundbloecke zaehlen nicht mit (E4).

    Gemessen: in 12 von 12 Mehrfachfaellen steht der Datensatz VOR den Befunden — die
    naheliegende Gegenannahme "der spaetere gewinnt" ist damit widerlegt, nicht offen.
    """
    raus = {}
    for kennung, zeile, art, felder in bloecke(text):
        if art == "DATENSATZ" and kennung not in raus:
            raus[kennung] = (zeile, felder)
    return raus


def tafelzeilen(text):
    """Je Kennung (zeile, zustand) aus SPALTE 2 der Tafel — nicht aus dem ersten Backtick (E3).

    Die Statustafel steht VOR dem ersten Zaun der Datei. Danach kommen Tabellen im Fliesstext,
    die dieselbe Form haben und sonst mitzaehlen — gemessen fuenf Stueck, darunter
    `| **Generator** |` und `| **1.** |`. Vor dem Zaun: 87 Zeilen, so viele wie es Datensaetze
    gibt; das ist die Gegenprobe auf einem zweiten Weg.
    """
    raus = {}
    L = text.split("\n")
    ende = next((i for i, l in enumerate(L) if l.rstrip() in ZAUN), len(L))
    for i, zeile in enumerate(L[:ende]):
        if not zeile.startswith("| **"):
            continue
        spalten = zeile.split("|")
        if len(spalten) < 3:
            continue
        k = re.match(r"\s*\*\*([^*]+)\*\*", spalten[1])
        if not k:
            continue
        kennung = k.group(1).strip()
        roh = spalten[2].strip().strip("*").strip("`").strip("*").strip()
        raus.setdefault(kennung, (i + 1, roh))
    return raus


def messen(refs, rolle):
    fehler = 0
    for ref in refs:
        text = status_lesen(ref if ref != "ARBEITSBAUM" else None)
        if text is None:
            print(f"  {ref:34s} REF FEHLT")
            fehler = 2
            continue
        ds = datensaetze(text)
        taf = tafelzeilen(text)
        ball = sorted(k for k, (_, f) in ds.items() if rolle in f.get("ballbesitz", ""))
        # `claim_abnahme` bleibt nach der Abnahme im Datensatz stehen — von 56 Feldern sind 55
        # BETRIEBSBESTAETIGT. Ein Claim ist nur dann OFFEN, wenn der Zustand die Abnahme noch
        # vor sich hat; alles andere als "offen" zu melden waere ein Zaehlwort ohne Deckung.
        claim = sorted(
            k for k, (_, f) in ds.items()
            if f.get("claim_abnahme") and f.get("zustand") not in ABGESCHLOSSEN
        )
        drift = sorted(
            k for k in set(ds) & set(taf)
            if ds[k][1].get("zustand") and taf[k][1] != ds[k][1]["zustand"]
        )
        print(f"  {ref:34s} Datensaetze {len(ds):3d} · Tafel {len(taf):3d} · "
              f"Ball[{rolle}] {ball if ball else 'keiner'} · Drift {drift if drift else 'keine'}")
        if claim:
            print(f"  {'':34s}   offene claim_abnahme: {claim}")
    return fehler


# ── FANGPROBEN ──────────────────────────────────────────────────────────────────────────────
# Jede Probe fuehrt die ALTE Lesart und die NEUE gegen dieselbe Stelle. Rot mit alt und gruen
# mit neu ist der Nachweis; waere die alte Lesart auch gruen, wuerde die Probe nichts belegen.

def fangprobe():
    text = status_lesen(None)
    rot = 0

    # E2 — W-07N: das alte Muster verfehlt die Kennung, das neue findet den Datensatz.
    alt_muster = re.compile(r"^[AWZ]-[0-9]+(?:/[0-9]+)?$")
    ds = datensaetze(text)
    alt_treffer = [k for k in ds if alt_muster.match(k)]
    e2_alt = "W-07N" in alt_treffer
    e2_neu = "W-07N" in ds
    print(f"  E2 Kennungsmuster   W-07N   alt={'gefunden' if e2_alt else 'VERFEHLT'} · "
          f"neu={'gefunden' if e2_neu else 'VERFEHLT'}")
    if e2_alt or not e2_neu:
        rot += 1
    fehlend = sorted(set(ds) - set(alt_treffer))
    print(f"     vom alten Muster verfehlte Datensaetze: {len(fehlend)} {fehlend[:12]}")

    # E3 — A-16 und A-06: erster Backtick der Zeile gegen Spalte 2.
    taf = tafelzeilen(text)
    for kennung, erwartet in (("A-16", "BETRIEBSBESTAETIGT"), ("A-06", "ERLEDIGT")):
        zeile = None
        for z in text.split("\n"):
            if z.startswith(f"| **{kennung}**"):
                zeile = z
                break
        if zeile is None:
            print(f"  E3 Tafelspalte      {kennung}    ZEILE FEHLT")
            rot += 1
            continue
        m = re.search(r"`([A-Z_]+)`", zeile)
        alt = m.group(1) if m else "?"
        neu = taf.get(kennung, (0, "?"))[1]
        print(f"  E3 Tafelspalte      {kennung}    alt={alt:20s} neu={neu:20s} soll={erwartet}")
        if alt == erwartet or neu != erwartet:
            rot += 1

    # E4 — P-04 traegt nur Befundbloecke, A-40 einen Datensatz und sechzehn Befunde.
    alle = bloecke(text)
    p04 = [(z, a, f.get("zustand")) for k, z, a, f in alle if k == "P-04"]
    a40 = [(z, a, f.get("zustand")) for k, z, a, f in alle if k == "A-40"]
    e4_p04_alt = p04[0][2] if p04 else None            # alte Lesart: erster Block, egal welche Art
    e4_p04_neu = ds.get("P-04")
    e4_a40_alt = a40[0][2] if a40 else None
    e4_a40_neu = ds["A-40"][1].get("zustand") if "A-40" in ds else None
    print(f"  E4 Blockart         P-04    {len(p04)} Bloecke, alle {p04[0][1] if p04 else '?'} · "
          f"alt las zustand={e4_p04_alt} · neu={'kein Datensatz' if e4_p04_neu is None else e4_p04_neu}")
    print(f"  E4 Blockart         A-40    {len(a40)} Bloecke · alt={e4_a40_alt} · neu={e4_a40_neu}")
    if e4_p04_neu is not None or e4_a40_neu != "ENTWURF":
        rot += 1

    # E4b — der Fall, an dem meine ERSTE Fassung dieser Behebung selbst gescheitert waere:
    # zwanzig echte Datensaetze tragen kein blatt/art/spur/prioritaet. Faellt einer davon
    # durch, ist die Blockart-Regel wieder zu eng.
    OHNE_BLATTFELDER = ["A-01", "A-02", "A-03", "A-04", "A-05", "A-07", "A-18", "A-19", "B5N",
                        "W-02/1", "W-04/1", "W-05/1", "W-08/1", "W-11/1", "W-13/1", "W-20",
                        "W-21/1", "W-22/1", "W-23", "W-27"]
    verloren = [k for k in OHNE_BLATTFELDER if k not in ds]
    print(f"  E4b Datensatz ohne blatt/art/spur: {len(OHNE_BLATTFELDER)} erwartet · "
          f"{len(OHNE_BLATTFELDER) - len(verloren)} gefunden · verloren={verloren if verloren else 'keiner'}")
    if verloren:
        rot += 1

    # GEGENPROBE AUF EINEM ZWEITEN WEG: Datensatzzahl gegen Tafelzeilen vor dem ersten Zaun.
    # Zwei Wege, die nichts voneinander wissen; treffen sie sich, traegt die Regel.
    print(f"  GP  Datensaetze {len(ds)} gegen Tafelzeilen {len(taf)} — "
          f"{'gleich' if len(ds) == len(taf) else 'UNGLEICH'}")
    if len(ds) != len(taf):
        rot += 1

    # ROT-PROBE: "keine Drift" und "kein Ball" sind nur dann eine Aussage, wenn das Werkzeug
    # ueberhaupt anschlagen KANN. Beide Faelle werden hier im SPEICHER erzeugt — die Datei wird
    # nicht angefasst, es gibt also nichts zurueckzusetzen.
    ziel = "A-16"
    zeile_nr, felder = ds[ziel]
    L = text.split("\n")

    mut = list(L)
    for i, z in enumerate(mut):
        if z.startswith(f"| **{ziel}**"):
            mut[i] = z.replace("`BETRIEBSBESTAETIGT`", "`IN_ARBEIT`", 1)
            break
    d_mut = datensaetze("\n".join(mut))
    t_mut = tafelzeilen("\n".join(mut))
    drift = [k for k in set(d_mut) & set(t_mut)
             if d_mut[k][1].get("zustand") and t_mut[k][1] != d_mut[k][1]["zustand"]]
    print(f"  ROT Drift           {ziel}    Tafel kuenstlich auf IN_ARBEIT · gemeldet={drift}")
    if drift != [ziel]:
        rot += 1

    mut2 = list(L)
    mut2[zeile_nr] = "ballbesitz: evaluator"      # direkt unter die auftrag-Zeile von A-16
    d2 = datensaetze("\n".join(mut2))
    ball2 = sorted(k for k, (_, f) in d2.items() if "evaluator" in f.get("ballbesitz", ""))
    print(f"  ROT Ball            {ziel}    ballbesitz kuenstlich auf evaluator · gemeldet={ball2}")
    if ball2 != [ziel]:
        rot += 1

    print(f"  ---- Fangproben rot: {rot}")
    return 1 if rot else 0


def main():
    ap = argparse.ArgumentParser(add_help=True)
    ap.add_argument("--rolle", default="evaluator")
    ap.add_argument("--ref", action="append")
    ap.add_argument("--fangprobe", action="store_true")
    a = ap.parse_args()

    if subprocess.run(["git", "--no-optional-locks", "rev-parse", "--git-dir"],
                      capture_output=True).returncode != 0:
        print("kein Repository — das ist ein Aufbaufehler, kein Befund", file=sys.stderr)
        return 2

    if a.fangprobe:
        return fangprobe()
    return messen(a.ref if a.ref else ORTE, a.rolle)


if __name__ == "__main__":
    sys.exit(main())
