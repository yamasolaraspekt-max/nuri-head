"""RUECKWEG — Integration in die Rollenbaeume nachziehen, Vorbedingungen je Baum einzeln.

GEBAUT 17.08. 00:2x nach einem eigenen Fehler, den der Fehler 27 des Plan-Pruefers sichtbar
gemacht hat. Er hatte `git log ... 2>/dev/null | wc -l` gefahren und die 0 als "null ungepushte
Commits" gelesen — tatsaechlich war es die Zeilenzahl eines nach /dev/null geleiteten
Fehlerstroms. Seine Lehre: **eine ausgefallene Messung ist KEIN Ergebnis.**

DERSELBE GRIFF STAND IN MEINER RUECKWEG-VORBEDINGUNG:

    G=$(git -C "$P" status --porcelain --untracked-files=no | wc -l)
    if [ "$G" != "0" ] ... -> UEBERSPRUNGEN

Am Objekt geprueft, mit einem Pfad, der kein Repository ist:

    gueltiger Baum         G=0
    KEIN git-Verzeichnis   G=0     <- wc -l zaehlt die leere Ausgabe als 0

**Ein Baum, in dem `git status` fehlschlaegt, galt damit als sauber, und der Merge waere
gelaufen.** Genau das, wogegen die Vorbedingung steht. Die zweite Groesse war zufaellig
fail-safe: `rev-list --count` liefert bei Fehler einen LEEREN String, und `[ "" != "0" ]` ist
wahr, also wurde uebersprungen. Eine von zwei Groessen richtig — aus Versehen, nicht aus Bau.

DIESES WERKZEUG PRUEFT EXIT-CODES, NICHT AUSGABEN. Jede der drei Vorbedingungen kann drei
Ergebnisse haben: erfuellt, nicht erfuellt, ODER nicht messbar — und "nicht messbar" fuehrt
immer zum Ueberspringen, nie zum Merge.

    0  alle erreichbaren Baeume sind auf Stand
    1  mindestens ein Merge ist fehlgeschlagen
    2  mindestens ein Baum war nicht messbar (Umgebung, nicht Bestand)
"""
import os
import subprocess
import sys
import tempfile

# GEMESSENE WIRKUNG AUF FREMDE BAEUME — die Groessenangabe zu Fehler 28 des Plan-Pruefers,
# 17.08. 00:3x. Er hat beobachtet, dass dieser Rueckweg seinen HEAD mitten in einer Messrunde
# vorgezogen hat: drei Befehle EINES Blocks lieferten drei verschiedene Zustaende, und die
# dritte Zeile war mit der ersten unvereinbar. Seine Einordnung ist richtig und ich uebernehme
# sie: beide Seiten arbeiteten korrekt, seine Arbeit war unversehrt (reiner Fast-forward,
# is-ancestor 0), und die Abhilfe liegt bei ihm (Messstand in eine Variable, Gegenprobe am
# Ende). Was ihm fehlte, war die GROESSE — die liefere ich hier nach.
#
# ERHEBUNGSBEFEHL — nachgetragen 17.08. 00:5x auf einen Ball des Plan-Pruefers, und er trifft:
# hier standen die ERGEBNISSE und nicht die MESSUNG, und die Zahl "4 von 74" trug eine
# Entscheidung, die niemand nachrechnen konnte. Das ist B5, an der Rolle, die diese Regel heute
# Nacht am schaerfsten vertreten hat.
#
#     git -C <baum> reflog --date=unix --format='%gs|%cd|%gd'     # OHNE -N !
#     Paar = jeder 'merge …: Fast-forward'-Eintrag + der naechstaeltere 'commit:'-Eintrag
#     Abstand = Zeit(merge) - Zeit(commit), ungefiltert
#
# WELCHE UHR — es sind ZWEI, und sie antworten verschieden (auch das sein Fund):
#     %cd mit --date=unix   COMMIT-Datum des Ziels        -> die Spalte "commit" unten
#     HEAD@{…} aus %gd      Zeitstempel des Reflog-Eintrags -> die Spalte "reflog" unten
#
# MEINE ERSTE ERHEBUNG WAR FALSCH, und der fehlende Befehl hat es verdeckt: ich hatte die
# Fast-forwards ohne Limit gezaehlt, die Paare aber mit `-60` — 18 FF aus dem vollen Reflog,
# 10 Paare aus den letzten 60 Eintraegen. ZWEI GRUNDMENGEN IN EINEM SATZ. Ohne Limit sind es
# 17 Paare, genau die Zahl, die der Plan-Pruefer gegengemessen hat. Dieselbe Klasse wie F7.
#
#   Baum            FF   eigene   Paare   Med(commit)  <30 s   Med(reflog)  <30 s
#   plan-pruefer    18      241      17            43      7            90       0
#   planner         51      117      51           736      0           867       0
#   generator       45       52      43          2178      1          2379       0
#   evaluator       50       12      48          1877      0          2031       0
#                                          SUMME:        8/159                 0/159
#
# KEIN RUHEFENSTER EINGEBAUT, und das ist eine Entscheidung mit Grund: es fienge **8 von 159**
# Paaren nach der Commit-Uhr und **0 von 159** nach der Reflog-Uhr — und verzoegerte dafuer
# jeden Befundtransport um seine Fensterlaenge. Der Rueckweg ist heute mehrfach nachweislich
# der Weg gewesen, auf dem ein Befund die andere Rolle erreicht hat. Welche Uhr gilt, ist nicht
# entschieden; die Entscheidung gegen das Fenster traegt in beiden.
#
# Unveraendert gilt die Kernzahl: beim Evaluator 50 Fremd-Fast-forwards gegen 12 eigene
# Commits — der Rueckweg bewegt seinen HEAD 4,2-mal so oft, wie er selbst committet.
# A-37-22 — DIE BAUMAUSWAHL GEHT UEBER (PFAD, ZWEIG)-PAARE, NIE UEBER NAMEN.
#
# **Warum der Name nicht genuegt, gemessen:** unter ~/Documents tragen 15 Baeume ein
# `ticket`-Praefix, aber nur SIEBEN sind Rollenbaeume. Das Muster `ticket-rolle-*` liefert SECHS —
# davon ist einer (`ticket-rolle-release`, detached) der tote Rest aus P2H-09, waehrend der
# lebende `ticket-release-pruefung` fehlt. Ein weiterer Gleichnamiger liegt im Scratchpad einer
# fremden Sitzung und steht in `git worktree list` des gemeinsamen Repos; seit dem 22.08. kommt
# `ticket-rolle-generator-beleg-2026-08-21` dazu. **Wer ueber den Namen sucht, erwischt ihn und
# misst dann am falschen Stand.**
#
# **Was das Paar leistet:** vor jedem Merge wird der TATSAECHLICH ausgecheckte Zweig gegen den
# erwarteten gehalten. Weicht er ab, wird der Baum uebersprungen und gemeldet — nie gemergt.
# Vorher wurde ein Baum auf fremdem Zweig `--ff-only` nachgezogen, solange der Merge technisch
# durchging.
#
# **`ticket-release-pruefung` ist jetzt ein VOLLWERTIGER Eintrag.** Vorher kam er genau einmal vor,
# als Quelle des ZIEL-SHA (:118) — der Baum, der das Ziel definiert, wurde selbst nie nachgezogen.
BAEUME = [
    ('ticket',                    'auto/hausplaner-integration'),
    ('ticket-rolle-planner',      'rolle/planner'),
    ('ticket-rolle-plan-pruefer', 'rolle/plan-pruefer'),
    ('ticket-rolle-generator',    'rolle/generator'),
    ('ticket-rolle-evaluator',    'rolle/evaluator'),
    ('ticket-release-pruefung',   'rolle/release-pruefer'),
    ('ticket-rolle-dirigent',     'rolle/dirigent'),
]
WURZEL = '/Users/yamanuri/Documents'

# Die Namen der Liste — nur zum Erkennen von Gleichnamigen ausserhalb der Liste.
NAMEN = {name for name, _ in BAEUME}
PFADE = {f'{WURZEL}/{name}' for name, _ in BAEUME}

# Der kanonische Integrations-Checkout. Er steht EINMAL hier und wird von Preflight und
# Produktiv-Einstieg gelesen — zwei Schreibweisen waeren zwei Wahrheiten, und die zweite altert.
INTEGRATION_PFAD  = f'{WURZEL}/ticket'
INTEGRATION_ZWEIG = 'auto/hausplaner-integration'


# ── A-37-22b — NUR DER INTEGRATOR FAEHRT DEN ECHTEN RUECKWEG ────────────────────────────────
#
# **Das Werkzeug fragte nicht, WER es aufruft — nur, ob der Zielbaum sauber ist.** Gemessen ueber
# drei Staende (762243b9 / 49972884 / 1155709d, je eigene Ausgabedatei):
# `grep -cE 'TICKET_ROLLE|getenv|os.environ|preflight'` gab jedes Mal 0, waehrend derselbe Griff
# in vier anderen Dateien unter scripts/ trifft. **Der Griff war nicht blind, die Datei war leer.**
#
# **Am 22.08. um 08:06 hat genau das gewirkt:** ein Lauf aus der Generator-Rolle zog drei fremde
# Rollenbaeume per Fast-forward nach. Nichts ging kaputt — und unzulaessig war es trotzdem.
# *Das Werkzeug hat keinen Trockenlauf: sein Lauf IST der Rueckweg.*
#
# **Die Reihenfolge ist hier das Kriterium, nicht das Vorhandensein.** Ein Tor, das nach dem
# ersten `merge --ff-only` greift, hat den Schaden bereits zugelassen. Deshalb steht der Preflight
# vor dem Kern und nicht in ihm.
def preflight_authorisierung(wurzel=WURZEL):
    """Darf DIESER Aufruf den echten Rueckweg fahren? Prueft und aendert NICHTS.

    Gibt (True, meldung) oder (False, meldung) zurueck. Es wird kein Baum angefasst, kein Merge
    gefahren und keine Datei geschrieben — deshalb darf diese Funktion im echten Checkout laufen
    (A-37-22b/22d, Aufloesung: 'Preflight bestanden' wird am Bestand gemessen, der Transport nie).

    ZUM PARAMETER `wurzel`, denn er ist eine Angriffsflaeche und gehoert erklaert:
    Der Produktiv-Einstieg ruft OHNE Parameter — dann gilt die kanonische WURZEL, und niemand
    kann den Preflight auf ein selbstgewaehltes Verzeichnis umbiegen. Der Parameter existiert
    allein, damit die POSITIVPROBE im Wegwerf-Repo laufen kann. Sonst gaebe es sie nur im
    gemeinsamen Checkout — und den betritt der Generator nicht (Regel 2, und der Bauauftrag
    verbietet ihn ausdruecklich). *Ein Kriterium, das sich nur durch einen Regelbruch belegen
    laesst, ist nicht belegt, sondern erkauft.*
    """
    rolle = os.environ.get('TICKET_ROLLE', '')
    if rolle != 'integrator':
        wie = f"'{rolle}'" if rolle else 'nicht gesetzt'
        return False, (f'RUECKWEG  ABGEWIESEN  TICKET_ROLLE ist {wie}, verlangt ist integrator.\n'
                       f'          Den Rueckweg faehrt der Integrator. Wer ihn aus einer anderen\n'
                       f'          Rolle startet, zieht fremde Baeume nach, ohne dass jemand widerspricht.')

    hier = os.path.realpath(os.getcwd())
    soll = os.path.realpath(f'{wurzel}/ticket')
    if hier != soll:
        return False, (f'RUECKWEG  ABGEWIESEN  Arbeitsverzeichnis ist nicht der Integrations-Checkout.\n'
                       f'          erwartet: {soll}\n'
                       f'          gefunden: {hier}')

    rc, zweig = git(soll, 'rev-parse', '--abbrev-ref', 'HEAD')
    if rc:
        return False, ('RUECKWEG  ABGEWIESEN  Zweig des Integrations-Checkouts nicht lesbar.\n'
                       '          Eine ausgefallene Messung ist KEIN Ergebnis.')
    if zweig != INTEGRATION_ZWEIG:
        return False, (f'RUECKWEG  ABGEWIESEN  Integrations-Checkout steht auf {zweig}, '
                       f'erwartet {INTEGRATION_ZWEIG}.')

    return True, f'RUECKWEG  Preflight bestanden: integrator in {soll} auf {zweig}.'


# ── A-37-22b/22d — DER PROBE-MODUS LEHNT REALE ROLLEN-WORKTREES AKTIV AB ────────────────────
#
# **Die Absage-Regel zielt genau hierher:** ein Transportkern, der ohne Root-Parameter auskommt,
# kann im Probe-Modus doch auf die echten Baeume zeigen — und erfuellt dann weder 22b noch 22d.
# *Nicht woanders hinzeigen genuegt nicht; der Probe-Modus muss den Bestand ABLEHNEN.*
#
# Zwei Bedingungen, beide notwendig: der Root liegt unter dem System-Temp (dem Muster der
# vorhandenen Wegwerf-Proben in scripts/__tests__/*.mjs folgend, `mkdtempSync(join(tmpdir(), ...))`,
# statt ein drittes Verfahren zu erfinden), UND er ist nicht die Produktivwurzel oder ein Pfad
# darunter. Die zweite Bedingung ist nicht ueberfluessig: liegt die Produktivwurzel eines Tages
# selbst im Temp, waere die erste allein erfuellt und der Bestand offen.
def probe_root_pruefen(root):
    """(True, pfad) wenn der Root ein Wegwerf-Root ist, sonst (False, meldung)."""
    echt = os.path.realpath(root)
    temp = os.path.realpath(tempfile.gettempdir())
    wurzel = os.path.realpath(WURZEL)

    if echt == wurzel or echt.startswith(wurzel + os.sep):
        return False, (f'RUECKWEG  PROBE ABGEWIESEN  Der Probe-Root zeigt auf den Bestand.\n'
                       f'          {echt}\n'
                       f'          Reale Rollen-Worktrees werden im Probe-Modus abgelehnt (A-37-22d).')
    if not (echt == temp or echt.startswith(temp + os.sep)):
        return False, (f'RUECKWEG  PROBE ABGEWIESEN  Der Probe-Root liegt nicht unter {temp}.\n'
                       f'          {echt}\n'
                       f'          Proben laufen im Wegwerf-Repository, nicht irgendwo (A-37-22d).')
    if not os.path.isdir(echt):
        return False, f'RUECKWEG  PROBE ABGEWIESEN  Kein Verzeichnis: {echt}'
    return True, echt



# ── A-37-22c — DOPPELGAENGER UEBER AEHNLICHKEIT, NICHT UEBER GLEICHHEIT ─────────────────────
#
# **Ein Vergleich auf exakt gleichen Verzeichnisnamen genuegt nicht.** Der Fall, der es zeigt,
# liegt seit dem 22.08. im Bestand: `ticket-rolle-generator-beleg-2026-08-21` heisst NICHT wie
# ein Listenbaum, faengt aber mit einem an. Gleichheit sieht ihn nicht.
#
# **⚠ Dieser Doppelgaenger ist ABSICHT und kein Versehen.** Er entstand in der Nacht zum 22.08.
# als Sicherung dreier Commits gegen eine Nicht-FF-Blockade. **Verlangt ist deshalb nicht, ihn zu
# beseitigen, sondern ihn zu ERKENNEN und namentlich als ausgeschlossen zu MELDEN.** *Ein
# Kriterium, das Doppelgaenger als Fehler behandelt, erklaert eine bewusste Sicherung zum Mangel —
# und beim naechsten Mal sichert niemand mehr.*
#
# **Stiller Ausschluss genuegt nicht:** wer einen Baum uebergeht, ohne ihn zu nennen, erzeugt genau
# die Luecke, die A-37-22 schliesst — eine Liste, die nicht sagt, was sie ausgelassen hat.
def aehnelt(basis, name):
    """Heisst `basis` aehnlich wie der Listenname `name`?

    Vier Formen, absichtlich grob: Gleichheit, Praefix in beide Richtungen und Teilstring.
    **Grob ist hier richtig** — ein Doppelgaenger, den die Regel uebersieht, wird angefasst;
    einer, den sie zu viel meldet, kostet eine Zeile Ausgabe. Die Kosten sind nicht symmetrisch.
    """
    if basis == name:
        return 'gleich'
    if basis.startswith(name):
        return 'praefix'          # ticket-rolle-generator-beleg-2026-08-21 zu ticket-rolle-generator
    if name.startswith(basis):
        return 'verkuerzt'
    if name in basis:
        return 'teilstring'       # faengt auch Scratchpad-Klone mit vorangestelltem Text
    return None


def aehnliche_ausserhalb(wurzel):
    """Baeume, die wie ein Listenbaum HEISSEN oder ihm aehneln, aber nicht in der Liste stehen.

    Erhebung wie im Kriterium festgelegt: `git worktree list --porcelain` im Integrations-Checkout
    der jeweiligen Wurzel. Nur LESEN; dieser Lauf fasst dort nichts an.
    """
    pfade = {f'{wurzel}/{name}' for name, _ in BAEUME}
    rc, aus = git(f'{wurzel}/ticket', 'worktree', 'list', '--porcelain')
    if rc:
        return None                      # nicht erhebbar — der Aufrufer macht daraus 'unmessbar'
    fremde = []
    for zeile in aus.split('\n'):
        if not zeile.startswith('worktree '):
            continue
        pfad = zeile[len('worktree '):].strip()
        if pfad in pfade:
            continue
        basis = pfad.rstrip('/').split('/')[-1]
        for name in NAMEN:
            art = aehnelt(basis, name)
            if art:
                fremde.append((pfad, name, art))
                break
    return fremde


def git(pfad, *args):
    """Gibt (exit, stdout) zurueck. Der Exit-Code ist der ECHTE, nie der einer Pipe."""
    r = subprocess.run(['git', '-C', pfad, *args], capture_output=True, text=True)
    return r.returncode, r.stdout.strip()


def lage(pfad, ziel):
    """(zustand, text) — zustand ist 'bereit', 'halt' oder 'unmessbar'."""
    rc, kopf = git(pfad, 'rev-parse', '--short', 'HEAD')
    if rc:
        return 'unmessbar', 'HEAD nicht lesbar'

    rc, offen = git(pfad, 'status', '--porcelain', '--untracked-files=no')
    if rc:
        return 'unmessbar', 'status nicht lesbar'
    n_offen = len([z for z in offen.split('\n') if z.strip()])

    rc, voraus = git(pfad, 'rev-list', '--count', f'{ziel}..HEAD')
    if rc or not voraus.isdigit():
        return 'unmessbar', 'rev-list nicht lesbar'
    n_voraus = int(voraus)

    if n_offen:
        return 'halt', f'{kopf} · {n_offen} getrackt offen'
    if n_voraus:
        return 'halt', f'{kopf} · {n_voraus} voraus'
    if kopf == ziel[:len(kopf)]:
        return 'bereit', f'{kopf} · bereits auf Stand'
    return 'bereit', kopf


# ── A-37-22b/22d — DER TRANSPORTKERN NIMMT SEINE WURZEL ALS PARAMETER ───────────────────────
#
# **Vorher hiess die Wurzel WURZEL und stand fest im Code.** Damit war jeder Lauf ein Lauf am
# Bestand — es gab keinen Ort, an dem man das Werkzeug haette proben koennen, ohne echte Baeume
# zu bewegen. *Genau deshalb ist am 22.08. eine Probe zur Transportfahrt geworden.*
#
# **Der Kern kennt jetzt keine Wurzel mehr, er bekommt sie.** Wer ihn aufruft, sagt, worauf er
# zeigt — und die beiden Einstiege daneben entscheiden, wer das darf: der Produktiv-Einstieg erst
# nach bestandenem Preflight auf den kanonischen Checkout, der Probe-Modus nur auf einen Root, der
# die Pruefung `probe_root_pruefen()` bestanden hat.
def transport_kern(wurzel, ziel=None):
    if ziel is None:
        rc, ziel = git(f'{wurzel}/ticket-release-pruefung',
                       'rev-parse', 'fork/auto/hausplaner-integration')
        if rc:
            print('  Ziel nicht lesbar — fork/auto/hausplaner-integration')
            return 2
    kurz = ziel[:8]
    print(f'  Ziel: {kurz}\n')

    fehler = unmessbar = 0

    # A-37-22 — Gleichnamige ausserhalb der Liste: MELDEN und ausschliessen. Sie werden nie
    # angefasst, weil sie nicht in BAEUME stehen; die Meldung sagt, dass sie da sind.
    fremde = aehnliche_ausserhalb(wurzel)
    if fremde is None:
        unmessbar += 1
        print('  worktree list im Integrations-Checkout nicht lesbar — Gleichnamige UNGEPRUEFT')
    else:
        for pfad, nahe, art in fremde:
            print(f'  AEHNLICH AUSSERHALB DER LISTE  {pfad}\n        (Namensform: {art} zu {nahe}) — ausgeschlossen, nicht in der (Pfad, Zweig)-Liste')

    for name, soll_zweig in BAEUME:
        pfad = f'{wurzel}/{name}'

        # A-37-22 — Absage-Regel: ein Baum der Liste, den es nicht gibt, ist keine Erfolgsmeldung.
        # Er wird gemeldet und uebersprungen, und der Lauf endet mit 'nicht messbar' (2), nie 0.
        rc_z, ist_zweig = git(pfad, 'rev-parse', '--abbrev-ref', 'HEAD')
        if rc_z:
            unmessbar += 1
            print(f'  {name:28} UNMESSBAR      Baum fehlt oder ist kein Repository — nicht angefasst')
            continue

        # A-37-22 — DIE ZWEIGPRUEFUNG. Vorher gab es sie nicht: `lage()` las HEAD, status und
        # rev-list, nie den Zweig. Ein Baum auf fremdem Zweig wurde nachgezogen, solange der
        # ff-only-Merge durchging.
        if ist_zweig != soll_zweig:
            unmessbar += 1
            print(f'  {name:28} UEBERSPRUNGEN  Zweig {ist_zweig} statt {soll_zweig} — nie gemergt')
            continue

        zustand, text = lage(pfad, ziel)
        if zustand == 'unmessbar':
            unmessbar += 1
            print(f'  {name:28} UNMESSBAR      {text} — nicht angefasst')
            continue
        if zustand == 'halt':
            print(f'  {name:28} UEBERSPRUNGEN  {text}')
            continue
        vor = text.split(' ')[0]
        if vor == kurz:
            print(f'  {name:28} bereits auf Stand')
            continue
        rc, _ = git(pfad, 'merge', '--ff-only', ziel)
        if rc:
            fehler += 1
            print(f'  {name:28} MERGE FEHLGESCHLAGEN  {vor}')
            continue
        # DER SECHSTE EXIT-CODE. Gemeldet vom Plan-Pruefer in c207290f, eine Zeile unter der
        # Behebung, gegen die das Werkzeug gebaut wurde: hier stand `rc2, neu = git(...)`,
        # und rc2 kam im ganzen Werkzeug genau einmal vor — bei der Zuweisung. Scheitert
        # rev-parse (exit 128, LEERE Ausgabe), druckte der else-Zweig eine Erfolgszeile mit
        # leerem Ziel, der fehler-Zaehler blieb unberuehrt und main() gab 0 zurueck.
        # "Alle erreichbaren Baeume auf Stand" fuer einen Baum, dessen Ergebnis niemand lesen
        # konnte — genau die Klasse, gegen die dieses Werkzeug steht.
        #
        # SEINE GROESSENANGABE UEBERNEHME ICH: der Fall ist klein, rev-parse unmittelbar nach
        # einem erfolgreichen ff-only-Merge im selben Repository scheitert praktisch nie, und
        # er hat ihn NICHT gestellt (dafuer muesste man einen Arbeitsbaum zerstoeren). Belegt
        # ist die Form und die Folge, nicht die Haeufigkeit. Behoben wird er trotzdem, weil
        # das Werkzeug seinen Wert aus der Zusage zieht, ALLE Exit-Codes zu lesen.
        rc_neu, neu = git(pfad, 'rev-parse', '--short', 'HEAD')
        if rc_neu or not neu:
            unmessbar += 1
            print(f'  {name:28} MERGE LIEF, ERGEBNIS UNMESSBAR  {vor} -> ?')
        else:
            print(f'  {name:28} {vor} -> {neu}')

    print()
    if fehler:
        print(f'  {fehler} Merge(s) fehlgeschlagen')
    if unmessbar:
        print(f'  {unmessbar} Baum/Baeume nicht messbar — UNGEPRUEFT, nicht gruen')
    return 1 if fehler else (2 if unmessbar else 0)


# ── A-37-22b — DIE BEIDEN EINSTIEGE ────────────────────────────────────────────────────────
#
# **Produktiv-Einstieg:** Preflight zuerst, Kern danach — und der Kern bekommt ausschliesslich die
# kanonische Wurzel. *Die Reihenfolge IST das Kriterium.* Ein Tor nach dem ersten ff-only-Merge
# haette den Schaden schon zugelassen.
#
# **Probe-Modus:** `--probe-root <verzeichnis>` faehrt denselben Kern auf einem Wegwerf-Root. Er
# verlangt KEINE Integrator-Rolle, weil er den Bestand nicht erreichen kann — dafuer sorgt
# `probe_root_pruefen()`, die den Bestand aktiv ABLEHNT statt nur woanders hinzuzeigen.
#
# **Rueckgabewerte:** 5 fuer eine abgewiesene Autorisierung, in derselben Bedeutung wie im
# Rollen-Tor (fehlende oder falsche Rollenmarke); 64 fuer falschen Aufruf (EX_USAGE); darunter
# unveraendert 0/1/2 aus dem Kern.
def main(argv):
    if argv and argv[0] == '--probe-root':
        if len(argv) < 2:
            print('RUECKWEG  --probe-root braucht ein Verzeichnis.')
            return 64
        ok, ergebnis = probe_root_pruefen(argv[1])
        if not ok:
            print(ergebnis)
            return 5
        print(f'  PROBE-MODUS  Wegwerf-Root {ergebnis}')
        print('               Der Bestand wird nicht angefasst und waere abgewiesen worden.')
        # Der Probe-Modus faehrt DENSELBEN Preflight, nur auf dem Wegwerf-Root. Wuerde er ihn
        # ueberspringen, pruefte die Probe genau das Stueck nicht, das den Bestand schuetzt —
        # und die Positivprobe von A-37-22b haette keinen Ort, an dem sie laufen darf.
        ok, meldung = preflight_authorisierung(ergebnis)
        print('  ' + meldung.replace('\n', '\n  '))
        if not ok:
            return 5
        print()
        return transport_kern(ergebnis, argv[2] if len(argv) > 2 else None)

    ok, meldung = preflight_authorisierung()
    print(meldung)
    if not ok:
        return 5
    print()
    return transport_kern(WURZEL, argv[0] if argv else None)


if __name__ == '__main__':
    sys.exit(main(sys.argv[1:]))
