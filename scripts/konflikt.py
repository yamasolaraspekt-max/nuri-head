"""Konfliktaufloesung in docs/STATUS.md — jede ZEILE gegen ihren Datensatz.

GEBAUT NACH EINEM SCHADEN, 13.08. nachts: die Vorgaengerfassung war ein Einzeiler,
den ich je Konflikt neu getippt habe. Sie ging davon aus, dass ein Konflikt TAFELZEILEN
enthaelt. Beim zwoelften Konflikt enthielt er keine — es standen zwei Datensatz-Bloecke
gegeneinander. Die Schleife fand nichts, `neu` blieb leer, und `z[a:e+1] = neu` hat
BEIDE Seiten geloescht: 24 Zeilen meines eigenen A-29-Release-Vermerks, restlos.
Marker 0 und Drift 0 meldeten danach "sauber" — beide messen nicht, ob Inhalt fehlt.

Daher hier die Regeln, die der Einzeiler nicht hatte:
  1  Wenn der Konfliktbereich KEINE Tafelzeile enthaelt, wird NICHT aufgeloest.
     Abbruch mit Meldung — ein Textblock-Konflikt wird von Hand gelesen.
  2  Es wird nie weniger geschrieben als die groessere Seite hergibt: ein Ergebnis
     mit weniger Zeilen als max(oben, unten) ist ein Abbruch, kein Ergebnis.
  3  Jede Kennung MUSS eine Fassung haben, die ihren Datensatz trifft — sonst Abbruch.
"""
import re
import sys

P = 'docs/STATUS.md'


def norm(s):
    s = re.sub(r'\*\*|`', '', s).strip().lower().replace('ü', 'ue')
    return '—' if s in ('–', '-', '—', '') else s


def key(l):
    m = re.match(r'\| \*\*([\w/\-\.]+)\*\*', l)
    return m.group(1) if m else None


def loese(pfad=P):
    z = open(pfad).read().split('\n')
    if not any(l.startswith('<<<<<<<') for l in z):
        print('  kein Konflikt in', pfad)
        return 0
    a = next(i for i, l in enumerate(z) if l.startswith('<<<<<<<'))
    m = next(i for i in range(a, len(z)) if z[i].startswith('======='))
    e = next(i for i in range(m, len(z)) if z[i].startswith('>>>>>>>'))
    oben, unten = z[a + 1:m], z[m + 1:e]

    do = {key(l): l for l in oben if key(l)}
    du = {key(l): l for l in unten if key(l)}

    # REGEL 1 — ohne Tafelzeilen wird nicht automatisch aufgeloest
    if not do and not du:
        print(f'  ABBRUCH: Konfliktbereich enthaelt KEINE Tafelzeile '
              f'({len(oben)} gegen {len(unten)} Zeilen).')
        print('  Das ist ein Textblock-Konflikt — von Hand lesen, nicht automatisch.')
        print('  (Genau hier hat die Vorgaengerfassung 24 Zeilen geloescht.)')
        return 1

    # REGEL 1b — DER MASSSTAB DARF NICHT SELBST STRITTIG SEIN.
    # Ergaenzt 16.08. nach einem Fehlgriff: der Merge hatte DREI Konfliktbereiche — einen mit
    # Tafelzeilen und ZWEI mit den Datensaetzen von A-37/A-38. Dieses Muster loest immer nur den
    # ERSTEN Bereich auf und liest den Datensatz aus dem Rest der Datei. Dort standen aber noch
    # Konfliktmarker, und die HEAD-Fassung (BEREIT/generator) stand zufaellig oben. Ergebnis:
    # die Tafelzeilen wurden auf BEREIT gesetzt, waehrend die Datensaetze auf ENTWURF gehoerten.
    # Von Hand gefunden, weil ich die restlichen Bereiche gelesen habe statt der Erfolgsmeldung
    # zu glauben. Ein Massstab, der selbst im Streit steht, ist kein Massstab.
    if any(l.startswith('<<<<<<<') for l in z[e + 1:]) or any(
            l.startswith('<<<<<<<') for l in z[:a]):
        print('  ABBRUCH: es gibt WEITERE Konfliktbereiche in dieser Datei.')
        print('  Der Datensatz-Massstab kann selbst darin liegen — dann waehle ich gegen eine')
        print('  Seite, die noch strittig ist. Erst die Datensatz-Konflikte von Hand loesen.')
        return 1

    # Datensaetze als Massstab
    st = [i for i, l in enumerate(z) if l.startswith('auftrag: ')] + [len(z)]
    ds = {}
    for x, y in zip(st, st[1:]):
        n = re.match(r'auftrag: "?([\w/\-\.]+)', z[x]).group(1)
        zu = next((l.split(': ', 1)[1].strip() for l in z[x:y]
                   if l.startswith('zustand: ')), None)
        ba = next((l.split(':', 1)[1].split('#')[0].strip() for l in z[x:y]
                   if l.startswith('ballbesitz:')), None)
        if zu:
            ds[n] = (zu, ba)

    neu = []
    for k in sorted(set(do) | set(du), key=lambda x: list(do).index(x) if x in do else 99):
        kand = [x for x in (do.get(k), du.get(k)) if x]
        if k not in ds:
            print(f'  ABBRUCH: {k} hat keinen Datensatz — ohne Massstab wird nicht gewaehlt.')
            return 1
        zs, bs = ds[k]
        tr = [l for l in kand
              if norm(l.split('|')[2]) == zs.lower()
              and norm(l.split('|')[3]) == (bs or '—').lower().replace('ü', 'ue')]
        if not tr:
            print(f'  ABBRUCH: {k} — keine Fassung trifft den Datensatz {ds[k]}.')
            return 1
        w = max(tr, key=len)
        neu.append(w)
        print(f'  {k:8} -> {"HEAD" if w == do.get(k) else "andere":7} '
              f'{norm(w.split("|")[2]):20} {norm(w.split("|")[3])}')

    # REGEL 2 — nie weniger schreiben als die groessere Seite hergibt
    if len(neu) < max(len(do), len(du)):
        print(f'  ABBRUCH: Ergebnis haette {len(neu)} Zeilen, die groessere Seite hat '
              f'{max(len(do), len(du))}. Das waere ein Verlust.')
        return 1

    z[a:e + 1] = neu
    open(pfad, 'w').write('\n'.join(z))
    print(f'  aufgeloest: {len(neu)} Tafelzeile(n) geschrieben, Marker entfernt.')
    return 0


if __name__ == '__main__':
    sys.exit(loese(sys.argv[1] if len(sys.argv) > 1 else P))
