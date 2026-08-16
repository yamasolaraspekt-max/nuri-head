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
#
#   --fangprobe   haelt das MUSTER gegen zwoelf echte Faelle, bevor es zaehlt.
#   --regelprobe  fragt, ob docs/ARBEITSREGELN.md den Wortlaut traegt (A-41-1, halb fremd).
# ```
#
# **Ohne Argument laeuft `--vergleich`** — der Modus, der nichts schreibt. *Ein Werkzeug, das die
# Statuswahrheit ueberschreiben kann, tut das nicht aus Versehen.*
#
# ## ⚠ EIN REINER ZUSTANDSWECHSEL KOMMT DURCH DAS TOR NICHT DURCH — gemessen, nicht vermutet
#
# **Der erste Versuch, diesen Auftrag auf `CODE_FERTIG` zu melden, wurde vom eigenen Tor
# abgewiesen** — und zwar aus einem Grund, der fuer sich genommen richtig ist:
#
# ```text
#   git status --porcelain           0 offene Dateien
#   commit-pruefen.sh                UNVERAENDERT <pfad> — der Schreibvorgang hat nichts bewirkt
#                                    KEIN COMMIT. F-14: was nicht geschrieben wurde,
#                                    wird auch nicht belegt.
# ```
#
# ***Zwei Regeln, beide vernuenftig, die sich an genau dieser Stelle treffen:*** **F-14** *sagt,
# ein Commit ohne Aenderung belegt nichts.* **Yamas Wortlaut** *sagt, der Zustandswechsel IST der
# Commit.* **Ein Zustandswechsel hat aber keine Datei** — er ist eine Aussage ueber einen Stand,
# nicht eine Aenderung an ihm.
#
# **Heute traegt der Zustands-Commit deshalb die Aenderung, die ihn begruendet** — hier diesen
# Absatz. *Das geht, solange gebaut wird.* **Es geht nicht mehr bei `ABGENOMMEN` oder
# `BETRIEBSBESTAETIGT`:** ein Evaluator, der abnimmt, aendert per Definition nichts. *Dann bleibt
# nur ein `--allow-empty` mit eigener Regel oder ein Zustandswechsel, der doch wieder an einer
# Datei haengt.*
#
# **Gemeldet und nicht selbst entschieden** — die Regel gehoert dem Planner und Yama, nicht diesem
# Skript. *Sie faellt sonst genau dann auf, wenn die erste Rolle abnehmen will.*
#
# ## Rueckgabewerte — nach A-41-10, und KEIN Wert traegt zwei Bedeutungen
#
# ```text
#    0   erzeugt, keine Meldung
#    1   erzeugt, MIT Meldungen            (K2/K4/K6)
#    2   NICHT erzeugt, Widerspruch        (K1)
#    3   Eingang leer, nichts erzeugt
#
#   64   Aufrufsfehler        <- sysexits.h EX_USAGE,    nicht in der Tabelle
#   70   Selbstprobe rot      <- sysexits.h EX_SOFTWARE, nicht in der Tabelle
# ```
#
# **Der Plan-Pruefer hat gemessen, dass der Code vorher nur 0 und 1 kannte** (`55028175`, vier
# Ausstiegsstellen einzeln nachgezaehlt) — *„vier Ursachen auf denselben Wert, wer 1 liest weiss
# nicht was passiert ist."* **Der Befund haelt; er ist behoben.**
#
# ## ⚠ ZWEI FAELLE, DIE DIE TABELLE NICHT KENNT — und warum sie nach oben ausweichen
#
# Beim Umbau faellt eine **zweite** Kollision auf, die der Befund nicht nennt: **der Aufrufsfehler
# stand auf 2** und haette dieselbe Zahl getragen wie der Widerspruch. Dazu die Selbstprobe, die
# auf 1 stand.
#
# **Beide sind keine Erzeugung** — die Tabelle beschreibt Erzeugungslagen und hat fuer sie keinen
# Wert. *Ich waehle keine freie Zahl aus 4 oder 5*, sondern die **belegte Konvention aus
# `sysexits.h`**: `EX_USAGE 64` und `EX_SOFTWARE 70`. **Damit bleibt 0–3 exakt die Tabelle des
# Auftrags, und die zwei Fremdfaelle sind an der Zahl als fremd erkennbar** statt sie zu verlaengern.
#
# **Gemeldet und nicht still entschieden:** ob A-41-10 die zwei Faelle aufnehmen soll, gehoert dem
# Planner — *die Formalisierung, wie bei A-41-8.*
set -uo pipefail
cd "$(dirname "$0")/.."

MODUS="vergleich"
case "${1:-}" in
  --tafel)     MODUS="tafel" ;;
  --bootstrap) MODUS="bootstrap" ;;
  --vergleich|"") MODUS="vergleich" ;;
  --fangprobe) MODUS="fangprobe" ;;
  --regelprobe) MODUS="regelprobe" ;;
  # 64 und nicht 2: die 2 gehoert seit A-41-10 dem Widerspruch. Ein Tippfehler im Aufruf und ein
  # ungeloester Widerspruch sind nicht dasselbe Ereignis und duerfen nicht dieselbe Zahl tragen.
  *) echo "Unbekanntes Argument: $1" >&2; exit 64 ;;
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
KERN = (
    r"zustand:\s+"
    r"(?P<kennung>[A-Z]+-?[0-9]+[A-Za-z]?(?:/[0-9A-Za-z]+)?)\s+·\s+"
    r"(?P<zustand>[A-Z_]+)\s+·\s+"
    r"(?P<rolle>[a-z-]+(?:-[0-9]+)?)"
    r"(?:\s+·\s+(?P<beleg>.*))?"
)
WORTLAUT = re.compile(r"^(?:[a-z-]+(?:-[0-9]+)?:\s+)?" + KERN + r"$")

# ── DASSELBE MUSTER, ANDERS VERANKERT — und die Verankerung IST der Unterschied ──────────────
#
# **Ein Commit-BETREFF muss der Wortlaut SEIN; eine Regel ZITIERT ihn.** In `ARBEITSREGELN.md`
# steht er als *„Beispiel:  generator: zustand: A-33 · …"* — mit einem Wort davor.
#
# **Meine erste Regelprobe hat genau daran vorbeigemessen und ROT gemeldet, obwohl der Planner
# geliefert hatte.** *Ein Falsch-Negativ an einem Pruefer — dieselbe Richtung, die ich eine
# Stunde vorher bei K2 als die gefaehrlichere uebernommen habe, diesmal an mir selbst.*
#
# ***Zwei Verwendungen, zwei Verankerungen, EIN Kern:*** *der Betreff verankert an Anfang und
# Ende, das Zitat nicht.* **Waeren es zwei Muster, wuerde das zweite still veralten** — deshalb
# steht `KERN` einmal da und beide setzen darauf auf.
IM_TEXT = re.compile(KERN)

# Das Vorfilter fuer `git log`. Es steht EINMAL hier, weil zwei Stellen es brauchen (die Tafel
# und die Zweigzuordnung fuer K6) — und zwei Fassungen desselben Musters waeren zwei Wahrheiten,
# von denen die zweite still veraltet.
MUSTER = "--grep=^\\(\\w\\+[a-z-]*: \\)\\?zustand:"

def lauf(*a):
    return subprocess.run(a, capture_output=True, text=True).stdout

# ── JEDER AUSSTIEG NENNT SEINEN GRUND ───────────────────────────────────────────────────────
# Die Zahl allein war der Mangel: vier Ursachen (fehlend/neu/abweichend/widerspruch) liefen auf
# denselben Wert, und *„wer 1 liest weiss nicht was passiert ist"*. Die Tabelle behebt die
# Verwechslung zwischen den KLASSEN; dieser Satz behebt sie innerhalb einer Klasse.
BEDEUTUNG = {
    0:  "erzeugt, keine Meldung",
    1:  "erzeugt, MIT Meldungen",
    2:  "NICHT erzeugt, Widerspruch",
    3:  "Eingang leer, nichts erzeugt",
    64: "Aufrufsfehler (sysexits EX_USAGE)",
    70: "Selbstprobe rot (sysexits EX_SOFTWARE)",
}

def raus(code, grund, bedeutung=None):
    # Die 0 heisst ueberall „in Ordnung", aber nicht ueberall „erzeugt": die Selbstprobe erzeugt
    # nichts. Ohne diesen Ausweg meldete sie „erzeugt, keine Meldung" und waere an genau der
    # Stelle unehrlich, an der sie die Ehrlichkeit des Werkzeugs bezeugen soll.
    print(f"\n  RUECKGABE {code} — {bedeutung or BEDEUTUNG.get(code, '?')}")
    print(f"  {grund}")
    sys.exit(code)

def zeit(ts):
    return datetime.datetime.fromtimestamp(int(ts)).strftime("%d.%m %H:%M")

# ── DIE GRENZE EINER KENNUNG (K2) ───────────────────────────────────────────────────────────
# Ein Pfadstueck traegt eine Kennung nur, wenn es mit ihr ANFAENGT und danach etwas kommt, das
# keine Ziffer und kein Buchstabe ist. Ohne die zweite Haelfte trifft `a-4` das Blatt `a-41`.
def _trifft(stueck, form):
    return stueck.startswith(form) and (len(stueck) == len(form) or not stueck[len(form)].isalnum())

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
        # K5 — der Beleg fuer diese Kante IST diese Probe und keine Codestelle. Ein Revert stellt
        # `Revert "` voran; das Muster verlangt den Zeilenanfang und trifft deshalb nicht mehr.
        # Beide Richtungen stehen hier, weil eine allein nichts zeigt: dass etwas nicht trifft,
        # ist erst dann eine Aussage, wenn die Gegenprobe trifft.
        ('Revert "generator: zustand: A-33 · CODE_FERTIG · generator"', False,
         "K5 Revert — MUSS scheitern, sonst zaehlt die Ruecknahme als Zustand"),
        ("generator: zustand: A-33 · CODE_FERTIG · generator",     True,
         "K5 Gegenrichtung — genau dieser Betreff OHNE das Revert-Praefix trifft"),
    ]
    rot = 0
    for text, erwartet, warum in faelle:
        ist = bool(WORTLAUT.match(text))
        ok = ist == erwartet
        rot += 0 if ok else 1
        print(f"  {'✔' if ok else '✖'} {'trifft' if erwartet else 'trifft nicht'}  {warum}")
        print(f"      {text}")
    print(f"\n  Fangprobe Wortlaut: {len(faelle)-rot}/{len(faelle)} wie erwartet")

    # ── ZWEITE PROBE: die Blattgrenze (K2) ──────────────────────────────────────────────────
    # Sie prueft eine EIGENSCHAFT an erfundenen Pfadstuecken und NICHT den heutigen Bestand.
    # Eine Probe, die „A-41 hat ein Blatt" festhaelt, wird rot, sobald jemand das Blatt umbenennt
    # — sie fror einen Zustand ein, statt eine Regel zu pruefen (F-06).
    grenz_faelle = [
        ("a-41-die-statuswahrheit.md", "a-41", True,  "das eigene Blatt trifft"),
        ("a-40-etwas-anderes.md",      "a-4",  False, "kurze Kennung NICHT als Praefix — der Befund"),
        ("a-10-und-mehr.md",           "a-1",  False, "dasselbe eine Stelle weiter"),
        ("w-25-pfetten-und-kehlbalken", "w-25", True, "Kennung im VERZEICHNIS, nicht im Dateinamen"),
        ("w05-werkzeug-anschluss.md",  "w05",  True,  "Schreibweise ohne Bindestrich"),
        ("b5-zaehlergebnis.md",        "b5",   True,  "Kennung ohne Bindestrich"),
        ("vorwort-a-41.md",            "a-41", False, "Treffer MITTEN im Namen zaehlt nicht"),
        ("a-41",                       "a-41", True,  "Stueck IST die Kennung"),
    ]
    grot = 0
    for stueck, form, erwartet, warum in grenz_faelle:
        ok = _trifft(stueck, form) == erwartet
        grot += 0 if ok else 1
        print(f"  {'✔' if ok else '✖'} {'trifft' if erwartet else 'trifft nicht'}  {warum}")
        print(f"      '{form}' gegen '{stueck}'")
    print(f"\n  Fangprobe Blattgrenze: {len(grenz_faelle)-grot}/{len(grenz_faelle)} wie erwartet")

    # ── DRITTE PROBE: die zwei Verankerungen desselben Kerns ────────────────────────────────
    # Genau diese Zeile steht seit 16:10 in den Arbeitsregeln, und genau an ihr hat meine erste
    # Regelprobe ROT gemeldet, obwohl geliefert war. Sie steht hier, damit die Unterscheidung
    # nicht wieder verlorengeht: als BETREFF ist sie ungueltig, als ZITAT gueltig.
    zitat = "Beispiel:  generator: zustand: A-33 · CODE_FERTIG · generator · bau 3e22e61b"
    anker_faelle = [
        (bool(WORTLAUT.match(zitat)), False, "als Commit-BETREFF ungueltig (ein Wort davor)"),
        (bool(IM_TEXT.search(zitat)), True,  "als ZITAT in der Regel gueltig"),
    ]
    arot = 0
    for ist, erwartet, warum in anker_faelle:
        ok = ist == erwartet
        arot += 0 if ok else 1
        print(f"  {'✔' if ok else '✖'} {warum}")
    print(f"      {zitat}")
    print(f"\n  Fangprobe Verankerung: {len(anker_faelle)-arot}/{len(anker_faelle)} wie erwartet")

    rot += grot + arot
    faelle = faelle + grenz_faelle + anker_faelle
    # 70 und nicht 1: eine rote Selbstprobe heisst nicht „erzeugt, mit Meldungen", sondern
    # „diesem Werkzeug ist nicht zu trauen". Wer beides auf 1 legt, laesst ein defektes Werkzeug
    # wie ein meldendes aussehen.
    raus(70 if rot else 0, f"Fangprobe {len(faelle)-rot}/{len(faelle)}",
         "Selbstprobe rot (sysexits EX_SOFTWARE)" if rot else "Selbstprobe gruen")

# ── DIE ERZEUGUNG AUS DEM COMMIT-LOG ────────────────────────────────────────────────────────
def aus_dem_log():
    """Je Kennung der juengste Eintrag. Gleiche Zeit + anderer Zustand -> Widerspruch."""
    # Das grep-Muster nimmt beide Formen: mit und ohne vorangestellte Rollenmarke.
    # --no-merges (A-41-8, K7): ein Merge-Commit, dessen BETREFF den Wortlaut traegt, waere ein
    # zweiter Eintrag fuer denselben Zustand — und zwar mit der Zeit des TRANSPORTS statt der der
    # Entscheidung. Der Zustand wanderte bei jedem Transport erneut ein und ueberholte sich selbst.
    # Der urspruengliche Commit bleibt sichtbar; --all findet ihn ueber seinen Zweig.
    roh = lauf("git", "log", "--all", "--no-merges", MUSTER,
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
        eintrag = {"ts": int(ts), "sha": sha[:8], "voll": sha, "autor": autor,
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
    je_kennung, prosa = {}, {}
    for z in zweige:
        text = lauf("git", "show", f"{z}:docs/STATUS.md")
        # K4: was NICHT uebernommen wird, wird gezaehlt statt verschwiegen. Der Parser nimmt
        # `auftrag:` und `zustand:` — jede andere nichtleere Zeile ist Prosa und bleibt liegen.
        # Sie geht nicht verloren: sie steht weiter in der Datei, aus der sie stammt, und diese
        # Zahl sagt, wie viel davon ein spaeterer Vorgang zurueckzuraeumen hat.
        zeilen = text.split("\n")
        genommen = sum(1 for l in zeilen if l.startswith("auftrag:") or l.startswith("zustand:"))
        leer = sum(1 for l in zeilen if not l.strip())
        prosa[z] = {"gesamt": len(zeilen), "genommen": genommen,
                    "prosa": len(zeilen) - genommen - leer}
        cur = None
        for l in zeilen:
            if l.startswith("auftrag:"):
                m = re.match(r'^auftrag: "([^"]+)"', l)
                cur = m.group(1) if m else None
            elif cur and l.startswith("zustand:"):
                je_kennung.setdefault(cur, {}).setdefault(l[8:].split("#")[0].strip(), []).append(z)
                cur = None
    return zweige, je_kennung, prosa

# ── WANN WURDE DIESER ZUSTAND AUF DIESEM ZWEIG GESETZT ───────────────────────────────────────
# A-41-5 verlangt die verdraengten Staende MIT Commit-Zeit, A-41-4 verlangt den JUENGSTEN. Beides
# braucht eine Zeit je (Zweig, Kennung) — und die ist nicht die letzte Aenderung an STATUS.md.
#
# **Die naheliegende Messung waere falsch:** `git log -1 -- docs/STATUS.md` gibt die letzte
# Aenderung an der DATEI. Wer A-33 vor dreissig Commits gesetzt und seither zwanzig andere
# Auftraege gepflegt hat, bekaeme die Zeit des letzten fremden Eintrags. **Das ist H-8 — Ort ist
# nicht Wirkung —, und hier heisst es: Datei ist nicht Eintrag.**
#
# Deshalb wird der Zweig rueckwaerts gegangen, bis der Wert sich AENDERT. Der aelteste Commit,
# der ihn noch traegt, ist der, in dem er gesetzt wurde. Der Gang bricht beim ersten Andersstand
# ab und laeuft deshalb kurz — nicht ueber die ganze Historie.
_STAND = {}
def _wert(sha, kennung):
    if sha not in _STAND:
        d, cur = {}, None
        for l in lauf("git", "show", f"{sha}:docs/STATUS.md").split("\n"):
            if l.startswith("auftrag:"):
                m = re.match(r'^auftrag: "([^"]+)"', l)
                cur = m.group(1) if m else None
            elif cur and l.startswith("zustand:"):
                d[cur] = l[8:].split("#")[0].strip()
                cur = None
        _STAND[sha] = d
    return _STAND[sha].get(kennung)

# ── K2: HAT DIESE KENNUNG UEBERHAUPT EIN BLATT? ─────────────────────────────────────────────
# *„Ein Zustand ohne Auftrag ist ein Befund, kein Filterfall"* — die Zeile wird also erzeugt UND
# gemeldet, nicht weggelassen.
#
# **Die Schreibweisen gehen auseinander, und das ist hier die eigentliche Schwierigkeit:** die
# Werkbank fuehrt `W-05/2`, die Datei heisst `w05-werkzeug-anschluss.md`. **Ein Muster, das eine
# Schreibweise voraussetzt, misst die Schreibweise und nicht die Sache** (H-9) — deshalb werden
# beide Formen gesucht, mit und ohne Bindestrich.
#
# ***Und das bleibt eine Heuristik ueber Dateinamen.*** *Sie kann eine Kennung uebersehen, deren
# Blatt anders heisst.* **Deshalb heisst die Meldung „kein Blatt GEFUNDEN" und nicht „kein Blatt
# vorhanden"** — der Unterschied ist der zwischen einer Messung und einer Behauptung.
#
# ## ⚠ UND NICHT `git ls-files` — der erste Bau hier war falsch, der Lauf hat es gezeigt
#
# `git ls-files` liest den EIGENEN Auscheck. **Der erste Lauf meldete `A-41` als „Zustand ohne
# Auftrag" — das Blatt liegt im Planner-Zweig, mein Baum haengt 73 Commits zurueck.** Ein
# Fehlalarm, und zwar der teuerste: er behauptet einen Befund ueber eine andere Rolle.
#
# **Dieselbe Klasse wie K6 und wie die ganze Erzeugung:** wer ueber alle Zweige urteilt, muss
# ueber alle Zweige lesen. *Der eigene Auscheck ist ein Zweig von sechs und nie die Auskunft.*
# ── DIE GRENZE, an der die erste Fassung gescheitert ist ────────────────────────────────────
#
# **Der Plan-Pruefer hat `blatt_gefunden()` isoliert nachgebaut und den Fehler gefunden, den mein
# Bestand heute zufaellig verdeckt** (`02b5d81c`): der Vergleich war ein reiner Substring-Test,
# **also trifft `A-4` das Blatt `a-41` und `A-1` das Blatt `a-10`.**
#
# ***Und er hat die Richtung richtig gewichtet:*** *der Falsch-Negativ kostet eine ueberfluessige
# Meldung; der Falsch-Positiv laesst K2 **SCHWEIGEN** — und eine Kante, die nicht meldet, sieht aus
# wie eine Kante, die nichts zu melden hatte.*
#
# **Sein Schutzbefund dazu, den ich uebernehme statt ihn zu glaetten:** heute tritt der Fall nicht
# auf, 0 von 79 Kennungen haben eine einstellige Nummer. **Aber das ist Zufall und nicht
# Konstruktion** — die erste Kennung ohne fuehrende Null bricht es.
#
# *Es ist genau die H-9-Klasse, vor der der Kommentar zwei Absaetze weiter oben warnt:* **das
# Muster mass die Zeichenfolge und nicht die Kennung.** Die Grenze macht daraus eine Kennung.
# *Die Funktion `_trifft` steht weiter oben, vor der Fangprobe* — sie wird dort gegen acht
# erfundene Faelle gehalten, und Python liest von oben nach unten.
_BLAETTER = None
def blatt_gefunden(kennung):
    global _BLAETTER
    if _BLAETTER is None:
        _BLAETTER = set()
        for z in lauf("git", "for-each-ref", "--format=%(refname:short)",
                      "refs/heads/rolle/*", "refs/heads/auto/hausplaner-integration").split("\n"):
            if not z.strip():
                continue
            for pfad in lauf("git", "ls-tree", "-r", "--name-only", z, "docs/").split("\n"):
                # JEDES Pfadstueck, nicht nur der Dateiname: die Werkbank fuehrt die Kennung im
                # VERZEICHNIS (`W-25-pfetten-und-kehlbalken/1-ZWECK.md`), die Auftraege im
                # Dateinamen. Wer nur Basisnamen prueft, verliert die halbe Werkbank.
                for stueck in pfad.lower().split("/"):
                    if stueck:
                        _BLAETTER.add(stueck)
    stamm = kennung.split("/")[0].lower()
    formen = {stamm, stamm.replace("-", "")}
    return any(_trifft(stueck, f) for stueck in _BLAETTER for f in formen)

# ── K6: AUF WELCHEN ROLLENZWEIGEN LIEGT DIESER COMMIT? ──────────────────────────────────────
# Sechs `git log` statt eines `git branch --contains` je Commit: dieselbe Auskunft, aber die
# Kosten haengen an der Zahl der ZWEIGE und nicht an der der Commits.
#
# **Was diese Messung NICHT kann, und das steht hier, damit niemand mehr hineinliest:** nach
# einem Transport liegt ein Commit auf mehreren Zweigen, und der Ursprung ist daraus nicht mehr
# rekonstruierbar. Gemeldet wird deshalb der belegbare Fall — die Rollenmarke nennt eine Rolle,
# und auf DEREN Zweig liegt der Commit nicht.
# ── WELCHER ZWEIG GEHOERT ZU WELCHER ROLLE ──────────────────────────────────────────────────
#
# **Nicht `rolle/<name>` fuer alle — der INTEGRATOR hat keinen solchen Zweig.** Er arbeitet im
# Integrations-Checkout, und `rolle/integrator` existiert nicht.
#
# **Gemessen am 16.08.:** die erzeugte Tafel meldete `A-37` als „Zustand im fremden Zweig", weil
# die Rollenmarke `integrator` lautet und meine Zuordnung nach `rolle/integrator` suchte. *Ein
# Fehlalarm, der bei JEDEM Zustands-Commit des Integrators wieder kaeme* — **und ausgerechnet bei
# der Rolle, die als einzige die Statuswahrheit schreibt.**
#
# ***Dieselbe Klasse wie die Verzeichnistabelle im Rollen-Tor:*** *eine Zuordnung, die aus dem
# Namen gerechnet statt nachgeschlagen wird, bricht beim ersten Sonderfall.*
#
# Die Instanznummer faellt weg (`plan-pruefer-2` ist `plan-pruefer`), wie im Tor bei K1.
# ── BEINAHE-MELDUNGEN: gemeint, aber nicht im Wortlaut ──────────────────────────────────────
#
# **Der Anlass ist gemessen und kostete eine Runde:** der Abnahme-Commit `e0df30d7` lautet
# *„evaluator: A-41 ABGENOMMEN — …"*. **Er enthaelt das Wort `zustand:` nicht und ist fuer die
# Erzeugung damit UNSICHTBAR** — nicht einmal in der Liste „nicht im Wortlaut", denn die sieht nur
# Commits, die `zustand:` sagen. *Die Tafel meldete A-41 weiter als `CODE_FERTIG`, und der
# Release-Pruefer musste die Luecke von Hand finden.*
#
# ***Eine Meldung, die knapp danebenliegt, ist teurer als eine, die fehlt:*** *ihr Autor glaubt,
# gemeldet zu haben.*
#
# ## Warum die Zeitgrenze und warum sie GEMESSEN wird
#
# ```text
#   Beinahe-Treffer VOR dem Wortlaut:   202   <- die alte, gueltige Form. KEIN Befund.
#   Beinahe-Treffer NACH dem Wortlaut:    2
# ```
#
# **Ohne Grenze meldete diese Kante 202 Zeilen und waere nach dem ersten Blick abgeschaltet**
# (A-03). **Die Grenze ist der aelteste Commit, der den Wortlaut trifft** — *aus dem Log erhoben,
# nicht als Datum eingetragen.* **Ein fester Zeitpunkt im Code altert; eine Messung nicht.**
#
# ***Und die Kante meldet, sie sperrt nicht:*** *einer der zwei Treffer ist ein Bericht UEBER eine
# Zustandsmeldung („A-41s CODE_FERTIG-Meldung geprueft") und keine.* **Bei zwei Zeilen entscheidet
# der Leser in einer Sekunde; bei zweihundert entscheidet niemand mehr.**
BEINAHE = re.compile(
    r"^[a-z-]+(?:-[0-9]+)?:\s+"
    r"(?P<kennung>[A-Z]+-?[0-9]+[A-Za-z]?(?:/[0-9A-Za-z]+)?)\s+"
    r"(?P<zustand>ENTWURF|BEREIT|IN_ARBEIT|CODE_FERTIG|ABGENOMMEN|BETRIEBSBESTAETIGT"
    r"|SPEC_BLOCKED|NACHBESSERN|RELEASE_FREI)\b"
)

def beinahe_meldungen(ab_ts):
    if ab_ts is None:
        return []
    out = []
    for zeile in lauf("git", "log", "--all", "--no-merges", "--format=%H%x09%at%x09%s").split("\n"):
        if not zeile.strip():
            continue
        sha, ts, betreff = zeile.split("\t", 2)
        if WORTLAUT.match(betreff) or int(ts) < ab_ts:
            continue
        m = BEINAHE.match(betreff)
        if m:
            out.append((sha[:8], int(ts), m.group("kennung"), m.group("zustand"), betreff))
    return sorted(out, key=lambda e: -e[1])

def zweig_der_rolle(rolle):
    stamm = re.sub(r"-[0-9]+$", "", rolle)
    return "auto/hausplaner-integration" if stamm == "integrator" else f"rolle/{stamm}"

_ZWEIG_VON = None
def zweige_mit(sha, muster):
    global _ZWEIG_VON
    if _ZWEIG_VON is None:
        _ZWEIG_VON = {}
        for z in lauf("git", "for-each-ref", "--format=%(refname:short)",
                      "refs/heads/rolle/*", "refs/heads/auto/hausplaner-integration").split("\n"):
            if not z.strip():
                continue
            for s in lauf("git", "log", z, "--no-merges", muster, "--format=%H").split("\n"):
                if s.strip():
                    _ZWEIG_VON.setdefault(s, []).append(z)
    return _ZWEIG_VON.get(sha, [])

def wann_gesetzt(zweig, kennung, zustand):
    gesetzt = None
    for zeile in lauf("git", "log", "--format=%H%x09%at", zweig, "--", "docs/STATUS.md").split("\n"):
        if not zeile.strip():
            continue
        sha, ts = zeile.split("\t")
        if _wert(sha, kennung) == zustand:
            gesetzt = int(ts)          # noch derselbe Wert -> weiter zurueck
        else:
            break                      # hier hat er gewechselt: der vorige ist der Setzzeitpunkt
    return gesetzt

if MODUS == "tafel":
    tafel, widerspruch, verworfen = aus_dem_log()
    if not tafel and not verworfen:
        raus(3, "Kein einziger Commit im Wortlaut ueber alle Zweige — nichts zu erzeugen.")

    # DER WIDERSPRUCH KOMMT VOR DER TAFEL, und die Tafel wird dann NICHT gedruckt.
    #
    # Vorher stand sie oben und der Widerspruch darunter — mit dem juengsten Eintrag als Gewinner.
    # **Das war eine stille Aufloesung mit einer Warnung daneben.** Bei gleicher Zeit gibt es aber
    # keinen juengsten; wer trotzdem einen waehlt, entscheidet per Sortierreihenfolge. Genau das
    # verbietet Regel 4, und A-41-10 gibt dem Fall mit der 2 seinen eigenen Ausgang: NICHT erzeugt.
    if widerspruch:
        print(f"# KEINE Tafel erzeugt — WIDERSPRUCH bei gleicher Zeit ({len(widerspruch)})\n")
        for k, eintraege in widerspruch:
            for e in eintraege:
                print(f"    {k:<10} {e['zustand']:<20} {e['rolle']:<16} {e['sha']}  {zeit(e['ts'])}")
        raus(2, "GEMELDET, NICHT aufgeloest (Regel 4). Die Entscheidung gehoert dem Integrator.")

    print(f"# Statuswahrheit — ERZEUGT aus dem Commit-Log, {len(tafel)} Kennungen\n")
    for k in sorted(tafel):
        e = tafel[k]
        print(f"  {k:<10} {e['zustand']:<20} {e['rolle']:<16} {e['sha']}  {zeit(e['ts'])}  {e['beleg'][:40]}")
    if verworfen:
        print(f"\n  NICHT IM WORTLAUT, deshalb nicht gezaehlt: {len(verworfen)}")
        for sha, b in verworfen[:5]:
            print(f"    {sha}  {b[:90]}")

    # K2 — die Zeile steht schon oben in der Tafel. Hier wird sie GEMELDET, nicht entfernt.
    ohne_blatt = [k for k in sorted(tafel) if not blatt_gefunden(k)]
    if ohne_blatt:
        print(f"\n  K2 · ZUSTAND OHNE AUFTRAG — Zeile erzeugt UND gemeldet: {len(ohne_blatt)}")
        for k in ohne_blatt:
            print(f"    {k:<10} {tafel[k]['zustand']:<20} kein Blatt gefunden unter docs/")

    # K6 — Rollenmarke und Zweig passen nicht zusammen. Auch hier: erzeugt UND gemeldet.
    fremd = []
    for k in sorted(tafel):
        e = tafel[k]
        liegt = zweige_mit(e["voll"], MUSTER)
        if liegt and zweig_der_rolle(e["rolle"]) not in liegt:
            fremd.append((k, e, liegt))
    if fremd:
        print(f"\n  K6 · ZUSTAND IM FREMDEN ZWEIG — Zeile erzeugt UND gemeldet: {len(fremd)}")
        for k, e, liegt in fremd:
            print(f"    {k:<10} Rollenmarke '{e['rolle']}' — liegt auf {', '.join(sorted(liegt))}")

    # Die Grenze ist der aelteste Eintrag IM Wortlaut — gemessen, nicht eingetragen.
    aeltester = min((e["ts"] for e in tafel.values()), default=None)
    beinahe = beinahe_meldungen(aeltester)
    if beinahe:
        print(f"\n  BEINAHE — sieht aus wie eine Zustandsmeldung, trifft den Wortlaut aber nicht: {len(beinahe)}")
        for sha, ts, k, zu, betreff in beinahe:
            print(f"    {sha}  {zeit(ts)}  {k:<8} {zu:<20} {betreff[:60]}")
        print("    (nur Commits ab dem ersten Wortlaut-Eintrag; davor ist es die alte Form)")

    meldungen = len(verworfen) + len(ohne_blatt) + len(fremd) + len(beinahe)
    if meldungen:
        raus(1, f"nicht im Wortlaut {len(verworfen)} · ohne Auftrag {len(ohne_blatt)} · fremder Zweig {len(fremd)} · beinahe {len(beinahe)}")
    raus(0, f"{len(tafel)} Kennungen, keine Meldung.")

# ── A-41-1 · STEHT DER WORTLAUT IN DEN ARBEITSREGELN? ───────────────────────────────────────
#
# **Das Kriterium hat zwei Haelften, und nur eine gehoert mir.** *„Der Wortlaut steht in
# `docs/ARBEITSREGELN.md`"* — **das schreibt der Planner**, denn die Arbeitsregeln sind die
# Prozessquelle und keine Werkzeugdatei. *„… und ist maschinell pruefbar"* — **das ist der
# Pruefer, und der ist meiner.**
#
# ## ⚠ ICH HABE DIESES KRITERIUM SELBST FALSCH GRUEN GEMELDET
#
# ```text
#   grep -c 'zustand:' docs/ARBEITSREGELN.md              8   <- so habe ich gemessen
#   davon im neuen Wortlaut (mit Mitteltrenner)           0   <- so haette ich messen muessen
# ```
#
# **Die acht Treffer sind das ALTE Feld in `STATUS.md`, nicht der Commit-Betreff.** *Ich habe die
# Schreibweise gezaehlt und nicht die Sache* — **H-9, an mir selbst.** Ein `grep` auf ein
# Teilwort trifft die Vorgeschichte des Wortes mit.
#
# **Deshalb prueft dieser Modus nicht auf das Wort, sondern gegen `WORTLAUT`** — dasselbe Muster,
# das auch die Commits liest. *Ein Beispiel in der Regel, das die Erzeugung nicht laese, waere
# eine Regel, die ihr Werkzeug nicht kennt.*
if MODUS == "regelprobe":
    zweige = [z for z in lauf("git", "for-each-ref", "--format=%(refname:short)",
                              "refs/heads/rolle/*", "refs/heads/auto/hausplaner-integration").split("\n") if z]
    INTEGRATION = "auto/hausplaner-integration"
    print("# REGELPROBE — traegt docs/ARBEITSREGELN.md den Wortlaut, und liest ihn mein Muster?\n")
    treffer_je_zweig = {}
    for z in zweige:
        text = lauf("git", "show", f"{z}:docs/ARBEITSREGELN.md")
        # Der Wortlaut steht in einer Regel als BEISPIEL: eingerueckt, in einem Block, evtl. mit
        # Aufzaehlungszeichen davor. Deshalb wird die Zeile abgeraeumt, bevor sie geprueft wird —
        # aber NICHT der Betreff selbst veraendert.
        treffer = [l.strip() for l in text.split("\n") if IM_TEXT.search(l)]
        treffer_je_zweig[z] = treffer
        print(f"  {z.split('/')[-1]:<24} {len(treffer):>2} Zeile(n) im Wortlaut")
        for t in treffer[:2]:
            print(f"      {t[:96]}")
    im_integrationszweig = len(treffer_je_zweig.get(INTEGRATION, []))
    anderswo = sum(len(v) for k, v in treffer_je_zweig.items() if k != INTEGRATION)
    # Auch hier traegt die 0 nicht die Bedeutung „erzeugt": die Regelprobe erzeugt nichts, sie
    # prueft. Dieselbe Stelle wie bei der Fangprobe, und derselbe Ausweg.
    if im_integrationszweig:
        raus(0, "Der Wortlaut steht in den Arbeitsregeln und mein Muster liest ihn.",
             "Regelprobe gruen")
    if anderswo:
        raus(1, "Der Wortlaut steht in einem Rollenzweig, aber noch nicht im Integrationszweig.",
             "Regelprobe: steht, aber nicht transportiert")
    raus(3, "Kein Zweig traegt den Wortlaut in docs/ARBEITSREGELN.md — A-41-1 ist offen "
            "und gehoert dem Planner, nicht diesem Skript.",
         "Regelprobe rot — die Regel nennt den Wortlaut nicht")

if MODUS == "bootstrap":
    zweige, je_kennung, prosa = aus_den_zweigen()
    einig = {k: list(v)[0] for k, v in je_kennung.items() if len(v) == 1}
    uneinig = {k: v for k, v in je_kennung.items() if len(v) > 1}
    print(f"# BOOTSTRAP — {len(zweige)} Zweige gelesen, keiner ausgecheckt\n")
    # K4 — was NICHT uebernommen wird, je Zweig. Ohne diese Zahl saehe der Bootstrap aus, als
    # haette er die Datei vollstaendig gelesen; tatsaechlich nimmt er zwei Zeilenarten und laesst
    # den groesseren Teil liegen. Die Prosa geht nicht verloren — sie bleibt, wo sie steht.
    print("  K4 · PROSA je Zweig — nicht uebernommen, aber protokolliert:")
    for z in sorted(prosa):
        p = prosa[z]
        print(f"    {z.split('/')[-1]:<24} {p['gesamt']:>6} Zeilen · Datensatz {p['genommen']:>4} · Prosa {p['prosa']:>6}")
    print()
    print(f"  EINIG, seed-faehig ohne Entscheidung: {len(einig)} von {len(je_kennung)}")
    print(f"  UNEINIG, brauchen eine Entscheidung:  {len(uneinig)}\n")
    for k in sorted(uneinig):
        print(f"    {k}")
        for zustand, zs in sorted(uneinig[k].items()):
            print(f"      {zustand:<20} laut {', '.join(sorted(z.split('/')[-1] for z in zs))}")
    # ── A-41-4 UND A-41-5: JE KENNUNG EINE ZEILE, UND DIE VERDRAENGTEN EINZELN ──────────────
    #
    # **Hier korrigiere ich meine eigene Zeile von vorhin.** Sie lautete *„Regel 4: hier wird
    # NICHTS aufgeloest"* und gab bei JEDER Uneinigkeit die 2. **A-41-4 verlangt aber woertlich
    # eine Zeile je Kennung und namentlich `A-33 = BETRIEBSBESTAETIGT, den juengsten der fuenf`.**
    # Beides zusammen ging nicht.
    #
    # **Aufgeloest hat es das Lesen der Kanten, nicht das Nachgeben:** K1 ist *„gleiche Zeit,
    # verschiedener Zustand"*. **Verschiedene Zeiten sind kein Widerspruch, sondern eine
    # Reihenfolge** — und der juengste gewinnt, exakt wie im Log-Pfad. Ein echter Widerspruch
    # bleibt nur der Gleichstand.
    #
    # ***Das ist keine stille Aufloesung:*** *jeder verdraengte Stand wird mit Zweig, Zustand und
    # Zeit einzeln ausgewiesen* (A-41-5). **Regel 4 verbietet das stille Aufloesen, nicht das
    # Anwenden einer angesagten Ordnung.**
    verdraengt, gleichstand, seed = [], [], dict(einig)
    for k in sorted(uneinig):
        kandidaten = []
        for zustand, zs in uneinig[k].items():
            for z in zs:
                kandidaten.append((wann_gesetzt(z, k, zustand) or 0, z, zustand))
        kandidaten.sort(key=lambda t: -t[0])
        juengste = [c for c in kandidaten if c[0] == kandidaten[0][0]]
        if len({c[2] for c in juengste}) > 1:
            gleichstand.append((k, juengste))       # K1 — und NUR das ist einer
            continue
        seed[k] = kandidaten[0][2]
        for ts, z, zustand in kandidaten[1:]:
            verdraengt.append((k, z, zustand, ts))

    if verdraengt:
        print(f"\n  VERDRAENGTE STAENDE, einzeln mit Zweig, Zustand und Commit-Zeit: {len(verdraengt)}")
        for k, z, zustand, ts in verdraengt:
            print(f"    {k:<8} {z.split('/')[-1]:<16} {zustand:<20} {zeit(ts)}")
        print(f"\n  Es gewinnt je Kennung der juengste Stand — die obigen sind ihm gewichen.")
    if gleichstand:
        print(f"\n  GLEICHSTAND (K1) — GEMELDET, NICHT aufgeloest: {len(gleichstand)}")
        for k, js in gleichstand:
            for ts, z, zustand in js:
                print(f"    {k:<8} {z.split('/')[-1]:<16} {zustand:<20} {zeit(ts)}")

    print(f"\n  SEED — eine Zeile je Kennung: {len(seed)}")
    for k in sorted(seed):
        print(f"    {k:<10} {seed[k]}")

    if not je_kennung:
        raus(3, "Kein Zweig traegt einen Datensatz — der Bootstrap hat keine Eingabe.")
    if gleichstand:
        raus(2, f"{len(gleichstand)} Kennung(en) im Gleichstand — kein Seed ohne Entscheidung.")
    if verdraengt:
        raus(1, f"{len(seed)} Kennungen seed-faehig, {len(verdraengt)} Staende verdraengt.")
    raus(0, f"Alle {len(einig)} Kennungen einig — seed-faehig ohne Entscheidung.")

# ── VERGLEICH: Erzeugnis gegen den heutigen Bestand ─────────────────────────────────────────
tafel, widerspruch, verworfen = aus_dem_log()
_, je_kennung, _ = aus_den_zweigen()
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
# DIE VIER URSACHEN AUF EINEM WERT — der schaerfere Teil des Befundes.
#
# Die Reihenfolge ist nicht beliebig: der Widerspruch schlaegt die Abweichung, weil er die
# Erzeugung selbst verhindert, waehrend Abweichungen ein ERGEBNIS haben, das nur nicht zum
# Bestand passt. Und jeder Ausstieg nennt jetzt, WELCHE der Ursachen zutraf.
if widerspruch:
    raus(2, f"{len(widerspruch)} Kennung(en) im Widerspruch — der Vergleich hat keine Grundlage.")
if not tafel and not je_kennung:
    raus(3, "Weder Log noch Bestand tragen einen Zustand — nichts zu vergleichen.")
# ── A-41-6: JEDE ABWEICHUNG BEKOMMT IHRE URSACHE ────────────────────────────────────────────
#
# Der Auftrag nennt vier: verdraengter Stand (K3), Prosa (K4), Zustand ohne Auftrag (K2), oder
# ungeklaert. **Gemessen deckt keine davon den heutigen Hauptfall ab** — und das wird gemeldet
# statt in „ungeklaert" verschoben, wo es als Raetsel erschiene, obwohl die Ursache bekannt ist.
#
# ***Der Wortlaut ist erst heute entstanden.*** *Fast jede Kennung steht im Bestand und hat noch
# keinen Zustands-Commit; das ist keine Divergenz, sondern das Fehlen einer Historie.* **Eine
# Ursache, die 85 von 86 Faellen traegt, gehoert benannt und nicht unter „ungeklaert" gebucht.**
def ursache(k, art):
    staende = je_kennung.get(k, {})
    if art == "fehlend":
        if len(staende) > 1:
            return "K3 verdraengter Stand"
        return "Wortlaut neu — kein Zustands-Commit"
    if art == "neu":
        return "K2 kein Blatt gefunden" if not blatt_gefunden(k) else "Zustand ohne Datensatz im Bestand"
    return "K3 verdraengter Stand" if len(staende) > 1 else "UNGEKLAERT"

if fehlend or neu or abweichend:
    zaehler = {}
    for art, liste in (("fehlend", fehlend), ("neu", neu), ("abweichend", abweichend)):
        for k in liste:
            zaehler.setdefault(ursache(k, art), []).append(k)
    print("\n  URSACHE je Abweichung (A-41-6):")
    for u in sorted(zaehler):
        ks = zaehler[u]
        print(f"    {u:<34} {len(ks):>3}   {', '.join(ks[:6])}{' …' if len(ks) > 6 else ''}")
    offen = len(zaehler.get("UNGEKLAERT", []))
    if offen:
        print(f"\n  ⚠ {offen} UNGEKLAERT — das ist ein Fund und keine Nebensache.")
    raus(1, f"fehlend {len(fehlend)} · neu {len(neu)} · abweichend {len(abweichend)} · ungeklaert {offen}")
raus(0, "Erzeugnis und Bestand stimmen ueberein.")
PY
