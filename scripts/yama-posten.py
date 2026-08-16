"""YAMA-POSTEN — was liegt beim Auftraggeber, vollzaehlig.

GEBAUT 16.08. nach einem eigenen Fehler, der sich VIERMAL wiederholt hat, ohne dass ich ihn
bemerkte: ich habe die Posten bei Yama ueber die `auftrag:`-Bloecke gezaehlt und dabei jeden
Block uebersehen, der KEINE Kennung traegt.

    meine Zaehlung ueber `auftrag:`-Bloecke      4
    tatsaechlich im Bestand                     12

Die acht fehlenden sind Befundnotizen — Bloecke, die mit einem Sachschluessel beginnen
(`auftrag_von_yama:`, `anlass:`, `der_stand:`) statt mit `auftrag:`. Genau die Klasse, die
der Planner als "104 Befundnotizen" fuehrt und die A-42 umziehen soll.

**Der Fehler ist nicht die Zahl, sondern der Zaehlweg.** Wer ueber `auftrag:` iteriert, misst
die AUFTRAEGE und nennt das Ergebnis "alle Posten". Vier Meldungen an Yama trugen deshalb eine
zu kleine Zahl — und keine davon war falsch gerechnet.

DIESES MUSTER GEHT UEBER DIE ZAEUNE, nicht ueber die Kennungen: jeder ```yaml-Block wird
gelesen, unabhaengig davon, womit er beginnt. Ein Block ohne Kennung bekommt seinen ersten
Feldnamen als Bezeichner, damit er ueberhaupt benennbar ist.
"""
import re
import subprocess
import sys

# UMZUGSFEST seit 16.08. 22:3x — der Plan-Pruefer hat es in b43d26a7 vorhergesagt:
# "Dasselbe gilt fuer scripts/yama-posten.py des Release-Pruefers, das ueber dieselbe Datei
# laeuft." A-42 verschiebt jeden Block MIT auftrag und OHNE zustand nach docs/BEFUNDNOTIZEN.md.
# Gemessen an A-42s eigenem Kriterium: von 12 Yama-Posten ziehen 4 um, von meinen fuenf ALLE.
# Ein Werkzeug, das nur STATUS.md liest, meldete danach 8 statt 12 und bei mir 0 statt 5 —
# es waere nicht kaputt, es waere LEISE FALSCH. Deshalb liest es beide Dateien und sagt, aus
# welcher jeder Treffer stammt.
#
# DIESE ZWEI ZAHLEN STANDEN ERST VERTAUSCHT HIER (8 um / 4 bleiben). Gefunden hat es die Probe
# unten, nicht ich: sammle() ueber beide Haelften ergab S 8 + B 4, also bleiben 8. Ich hatte die
# Spalten meiner eigenen Messtabelle verwechselt — dieselbe Klasse wie F4 und F7. Der Kommentar
# eines Werkzeugs wird von niemandem nachgerechnet; deshalb steht der Fehler hier statt still
# berichtigt zu sein.
#
# Probe, jederzeit wiederholbar (schreibt nichts, legt keine Git-Objekte an):
#   STATUS.md nach A-42s Kriterium in zwei Zeilenlisten teilen, sammle() auf beide anwenden,
#   Summe muss der Messung an der ungeteilten Datei gleichen.
#   Ergebnis 16.08. 22:4x:  yama 12 = 8+4 · release-pruefer 5 = 0+5
# Der Ueberspring-Zweig (Quelle fehlt) ist heute im Echtbetrieb belegt: BEFUNDNOTIZEN.md gibt es
# noch nicht, das Werkzeug meldet "gelesen: docs/STATUS.md" und laeuft durch.
QUELLEN = ('docs/STATUS.md', 'docs/BEFUNDNOTIZEN.md')


def bloecke(zeilen):
    """Alle ```yaml-Bloecke als (start, ende) — ueber die Zaeune, nicht ueber Kennungen."""
    out, start = [], None
    for i, l in enumerate(zeilen):
        s = l.strip()
        if s.startswith('```yaml'):
            start = i
        elif s == '```' and start is not None:
            out.append((start, i))
            start = None
    return out


def main(wer='yama', rev='HEAD'):
    treffer = []
    gelesen = []
    for quelle in QUELLEN:
        s = subprocess.run(['git', 'show', f'{rev}:{quelle}'],
                           capture_output=True, text=True).stdout
        if not s:
            continue          # BEFUNDNOTIZEN.md gibt es vor dem Umzug noch nicht
        gelesen.append(quelle)
        z = s.split('\n')
        treffer += sammle(z, wer, quelle)
    if not gelesen:
        print(f'  keine der Quellen {QUELLEN} in {rev} lesbar')
        return 2
    return melde(treffer, wer, rev, gelesen)


def sammle(z, wer, quelle):
    treffer = []
    for a, b in bloecke(z):
        blk = z[a + 1:b]
        ba = next((l.split(':', 1)[1].split('#')[0].strip().strip('"')
                   for l in blk if re.match(r'^ballbesitz: ', l)), None)
        if not ba or ba.strip().split()[0].strip('"').lower() != wer.lower():
            continue
        kenn = next((l.split(':', 1)[1].strip().strip('"')
                     for l in blk if l.startswith('auftrag:')), None)
        ohne_kennung = kenn is None
        if ohne_kennung:
            # Ein Block ohne Kennung wird ueber seinen ersten Feldnamen benannt.
            kenn = next((l.split(':', 1)[0] for l in blk
                         if ':' in l and not l.startswith(' ')), '(leer)')
        titel = next((l.split(':', 1)[1].strip().strip('"')
                      for l in blk if l.startswith('titel:')), '')
        zeit = next((l.split(':', 1)[1].strip().strip('"')
                     for l in blk if l.startswith('zeit:')), '')
        treffer.append((a + 2, kenn, zeit, titel, ohne_kennung, quelle))

    return treffer


def melde(treffer, wer, rev, gelesen):
    print(f'  Posten mit ballbesitz: {wer}   —   {len(treffer)}   (Stand {rev})')
    print(f'    gelesen: {" · ".join(gelesen)}')
    mit = sum(1 for t in treffer if not t[4])
    print(f'    davon mit Kennung {mit} · ohne Kennung {len(treffer) - mit}'
          f'   <- die ohne uebersieht jede Zaehlung ueber auftrag:')
    print()
    for ln, kenn, zeit, titel, ok, quelle in treffer:
        marke = ' ' if not ok else '*'
        q = 'S' if quelle.endswith('STATUS.md') else 'B'
        print(f'  {marke}{q} Z.{ln:<7} {kenn[:34]:36} {zeit[:16]:17} {titel[:50]}')
    if len(treffer) - mit:
        print('\n  * = Block ohne auftrag:-Kennung (Befundnotiz)')
    return 0


if __name__ == '__main__':
    sys.exit(main(sys.argv[1] if len(sys.argv) > 1 else 'yama',
                  sys.argv[2] if len(sys.argv) > 2 else 'HEAD'))
