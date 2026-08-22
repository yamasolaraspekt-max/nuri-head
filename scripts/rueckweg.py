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
import subprocess
import sys

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


def gleichnamige_ausserhalb():
    """Baeume, die wie ein Listenbaum HEISSEN, aber woanders liegen — gemeldet und ausgeschlossen.

    Erhebung wie im Kriterium festgelegt: `git worktree list --porcelain` im Integrations-Checkout.
    Nur LESEN; dieser Lauf fasst dort nichts an.
    """
    rc, aus = git(f'{WURZEL}/ticket', 'worktree', 'list', '--porcelain')
    if rc:
        return None                      # nicht erhebbar — der Aufrufer macht daraus 'unmessbar'
    fremde = []
    for zeile in aus.split('\n'):
        if not zeile.startswith('worktree '):
            continue
        pfad = zeile[len('worktree '):].strip()
        if pfad in PFADE:
            continue
        if pfad.rstrip('/').split('/')[-1] in NAMEN:
            fremde.append(pfad)
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


def main(ziel=None):
    if ziel is None:
        rc, ziel = git(WURZEL + '/ticket-release-pruefung',
                       'rev-parse', 'fork/auto/hausplaner-integration')
        if rc:
            print('  Ziel nicht lesbar — fork/auto/hausplaner-integration')
            return 2
    kurz = ziel[:8]
    print(f'  Ziel: {kurz}\n')

    fehler = unmessbar = 0

    # A-37-22 — Gleichnamige ausserhalb der Liste: MELDEN und ausschliessen. Sie werden nie
    # angefasst, weil sie nicht in BAEUME stehen; die Meldung sagt, dass sie da sind.
    fremde = gleichnamige_ausserhalb()
    if fremde is None:
        unmessbar += 1
        print('  worktree list im Integrations-Checkout nicht lesbar — Gleichnamige UNGEPRUEFT')
    else:
        for pfad in fremde:
            print(f'  GLEICHNAMIG AUSSERHALB DER LISTE  {pfad} — ausgeschlossen, nicht angefasst')

    for name, soll_zweig in BAEUME:
        pfad = f'{WURZEL}/{name}'

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


if __name__ == '__main__':
    sys.exit(main(sys.argv[1] if len(sys.argv) > 1 else None))
