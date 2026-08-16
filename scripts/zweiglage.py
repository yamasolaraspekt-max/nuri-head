"""ZWEIGLAGE — Rueckstand und Widerspruch je Zweig, mit Vorfahr-Test und Ueberfaelligkeit.

IN DEN BESTAND GESTELLT 16.08. auf Anforderung des Planners: "Diese Unterscheidung darf nicht
in meinem Kopf bleiben — sie gehoert in ein Skript, das jeder fahren kann." Bis dahin lief das
Muster nur im Arbeitsverzeichnis des Release-Pruefers.

ABGRENZUNG ZU scripts/weck-runde.sh, GEMESSEN STATT VERMUTET — die beiden ueberschneiden sich:
  weck-runde.sh HAT den Vorfahr-Test bereits (Z.131-135: "ein Zweig, der 0 voraus ist,
  WIDERSPRICHT nicht — er ist nur nicht nachgezogen"). Wer ihn sucht, findet ihn dort.
  weck-runde.sh hat NICHT (je 0 Treffer):
    - die 24-Stunden-Grenze
    - die Unterscheidung der zwei Ueberfaelligkeitsarten A und B
    - die Warnung, dass der BEZUG selbst hinterherhinkt
Dieses Muster fuegt genau diese drei hinzu. Es ersetzt weck-runde.sh nicht und liest dieselben
Zweige; wer beides faehrt, bekommt bei der Zweiglage dasselbe Bild und hier zusaetzlich die
Alterung.

"""

import re
import subprocess
import sys
import time

INTEGRATION = 'auto/hausplaner-integration'
UEBERFAELLIG_STD = 24


def g(*a):
    return subprocess.run(['git', *a], capture_output=True, text=True).stdout.strip()


def zustaende(rev):
    """zustand je Auftrag aus docs/STATUS.md eines Standes."""
    s = subprocess.run(['git', 'show', f'{rev}:docs/STATUS.md'],
                       capture_output=True, text=True).stdout
    z, d = s.split('\n'), {}
    st = [i for i, l in enumerate(z) if l.startswith('auftrag: ')] + [len(z)]
    for a, b in zip(st, st[1:]):
        m = re.match(r'auftrag: "?([\w/\-\.]+)', z[a])
        if not m:
            continue
        zu = next((l.split(': ', 1)[1].split('#')[0].strip()
                   for l in z[a:b] if l.startswith('zustand: ')), None)
        if zu:
            d[m.group(1)] = zu
    return d


def main():
    # DER BEZUG MUSS SELBST GEMESSEN WERDEN — ergaenzt 16.08. nach einem Fehler in genau
    # diesem Muster. Ich hatte dem Integrator vorgehalten, er committe auf einem Stand, dem
    # 151 Commits fehlen. Dieses Werkzeug mass im selben Lauf gegen DENSELBEN veralteten
    # Zweig und meldete daraufhin acht "Widersprueche", von denen die meisten nur die Folge
    # des alten Massstabs waren: A-33 stand auf meinem Zweig BETRIEBSBESTAETIGT (richtig,
    # ich hatte es freigegeben) und auf der "Integration" CODE_FERTIG (alt).
    # Ein Massstab, der hinterherhinkt, erzeugt Widersprueche statt sie zu finden — dieselbe
    # Lehre wie Regel 1b im Konfliktmuster: ein Massstab, der selbst im Streit steht, ist
    # keiner. Gemessen wird ab jetzt gegen den FERNSTAND, und wenn der lokale Zweig
    # zurueckliegt, wird das GEMELDET statt still ersetzt.
    fern = g('rev-parse', '--verify', '--quiet', f'fork/{INTEGRATION}')
    lokal = g('rev-parse', '--verify', '--quiet', INTEGRATION)
    basis, bezugname = (lokal or g('rev-parse', 'HEAD')), INTEGRATION
    if fern and lokal:
        rueck = int(g('rev-list', '--count', f'{lokal}..{fern}') or 0)
        vor = int(g('rev-list', '--count', f'{fern}..{lokal}') or 0)
        if rueck:
            print(f'  ⚠ DER BEZUG SELBST HINKT: {INTEGRATION} liegt {rueck} Commits hinter '
                  f'fork ({vor} voraus).')
            print(f'    Gemessen wird gegen den FERNSTAND fork/{INTEGRATION}. Gegen den '
                  f'lokalen Zweig waeren\n    Widersprueche gemeldet worden, die nur sein '
                  f'Rueckstand sind.')
            basis, bezugname = fern, f'fork/{INTEGRATION}'

    zweige = [b for b in g('branch', '--list', 'rolle/*',
                           '--format=%(refname:short)').split('\n') if b]
    jetzt = int(time.time())

    print(f'  Bezug: {bezugname} @ {basis[:8]}\n')
    print(f'  {"Zweig":24} {"Stand":9} {"vor":>4} {"zurueck":>8}  letzter Commit')

    lebend, ueberfaellig = [], []
    for b in zweige:
        sha = g('rev-parse', b)
        vor = int(g('rev-list', '--count', f'{basis}..{b}') or 0)
        zur = int(g('rev-list', '--count', f'{b}..{basis}') or 0)
        letzt = g('log', '-1', '--format=%ad', '--date=format:%d.%m. %H:%M', b)
        print(f'  {b:24} {sha[:8]:9} {vor:>4} {zur:>8}  {letzt}')
        if vor:
            lebend.append(b)
            # ÜBERFAELLIG A — eigene Arbeit liegt und ist nicht angekommen.
            # BERICHTIGT 16.08.: hier stand log -1, das ist der JUENGSTE nicht integrierte
            # Commit — mein eigener Kommentar sagte "aeltester". Wer den juengsten misst,
            # meldet einen Zweig erst dann, wenn auch die letzte Arbeit alt ist; ein Zweig,
            # der seit Tagen liegt und vor einer Minute noch etwas bekam, faellt durch.
            # Gemessen wird der AELTESTE: so lange wartet die erste Zeile schon.
            ts = g('log', '--format=%ct', '--reverse', f'{basis}..{b}').split('\n')[0]
            if ts and (jetzt - int(ts)) / 3600 > UEBERFAELLIG_STD:
                ueberfaellig.append(('A', b, (jetzt - int(ts)) / 3600, f'{vor} eigene Commits'))
        # ÜBERFAELLIG B — der Zweig hat den Stand nicht uebernommen.
        # ERGAENZT 16.08., weil Yamas Zahl (zwei) und meine (null) auseinandergingen und die
        # Ursache eine Zweideutigkeit war: "nicht integriert" heisst BEIDES.
        #   A  eigene Arbeit ist nicht angekommen        -> sie kann verloren gehen
        #   B  fremder Stand ist nicht uebernommen       -> wer von hier merget, holt ALTE
        #      Zustaende zurueck. Genau der A-35-Fall: BETRIEBSBESTAETIGT waere auf
        #      CODE_FERTIG zurueckgefallen.
        # B trifft auch Zweige mit 0 voraus — die sind fuer A unauffaellig und trotzdem
        # gefaehrlich. Gemessen wird das Alter des aeltesten Commits, den der Zweig NICHT hat.
        if zur:
            ts2 = g('log', '--format=%ct', '--reverse', f'{b}..{basis}').split('\n')[0]
            if ts2 and (jetzt - int(ts2)) / 3600 > UEBERFAELLIG_STD:
                ueberfaellig.append(('B', b, (jetzt - int(ts2)) / 3600, f'{zur} Commits Rueckstand'))

    # --- Zustandsabweichungen, NUR zwischen lebenden Zweigen
    print(f'\n  LEBEND (eigene Commits): {len(lebend)}'
          f'{" — " + ", ".join(lebend) if lebend else ""}')
    tot = [b for b in zweige if b not in lebend]
    if tot:
        print(f'  NUR ALT (0 voraus, kein Widerspruch moeglich): {", ".join(tot)}')

    basis_z = zustaende(basis)
    gefunden = 0
    for b in lebend:
        bz = zustaende(b)
        # NUR ECHTE ZUSTANDSWIDERSPRUECHE — beide Seiten muessen einen Zustand tragen.
        # BERICHTIGT 16.08. beim ersten Lauf aus dem Bestand: die erste Fassung zaehlte auch
        # Auftraege, die der Zweig GAR NICHT KENNT (Zweig None gegen Integration ENTWURF), und
        # meldete sieben Widersprueche, von denen fuenf nur Rueckstand waren. Ein Auftrag, den
        # ein Zweig nicht kennt, widerspricht nicht — er fehlt. Das ist dieselbe Unterscheidung,
        # die der Vorfahr-Test auf Zweigebene macht, eine Ebene tiefer: nicht jede Abweichung
        # ist ein Streit. Sonst waere die Warnung wertlos, weil sie immer kaeme.
        ab = [(k, bz[k], basis_z[k]) for k in sorted(set(bz) & set(basis_z))
              if bz[k] != basis_z[k]]
        fehlt = len((set(basis_z) - set(bz)) | (set(bz) - set(basis_z)))
        if not ab:
            continue
        gefunden += len(ab)
        print(f'\n  ⚠ WIDERSPRUCH {b} gegen {bezugname} — {len(ab)} Auftrag/Auftraege.'
              + (f'  ({fehlt} weitere kennt der Zweig nicht — Rueckstand, kein Streit)'
                 if fehlt else ''))
        print('    ANGEZEIGT, NICHT aufgeloest: welche Fassung gilt, entscheidet die Rolle.')
        for k, x, y in ab:
            print(f'      {k:8} Zweig {str(x):20} Integration {y}')
    if not gefunden:
        print('  Zustandswidersprueche zwischen lebenden Zweigen: 0')

    # --- Ueberfaellig
    print()
    if ueberfaellig:
        print(f'  ⚠ NICHT INTEGRIERT seit mehr als {UEBERFAELLIG_STD} h: {len(ueberfaellig)}')
        print('    A = eigene Arbeit nicht angekommen (kann verloren gehen)')
        print('    B = fremder Stand nicht uebernommen (wer von hier merget, holt ALTE Zustaende)')
        for art, b, std, was in sorted(ueberfaellig):
            print(f'      [{art}] {b:24} {was:24} seit {std:.0f} h')
    else:
        print(f'  Kein Zweig laenger als {UEBERFAELLIG_STD} h nicht integriert.')

    return 1 if (gefunden or ueberfaellig) else 0


if __name__ == '__main__':
    sys.exit(main())
