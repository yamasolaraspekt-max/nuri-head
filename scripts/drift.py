"""Driftmessung Tafelzeile <-> Datensatz in docs/STATUS.md.

BERICHTIGT 12.08. (Release-Pruefer): die alte Fassung normalisierte jeden
Auftragsnamen auf seine Wurzel (W-27/1 -> W-27) und liess damit zwei ECHTE
Auftraege auf denselben Schluessel fallen. Der letzte gewann, und der Vergleich
lief gegen die falsche Tafelzeile — gemeldet wurde eine Drift, die es nicht gab.

Jetzt: EXAKTER Name zuerst. Nur wenn es zu einem Datensatz keine gleichnamige
Tafelzeile gibt, wird auf die Wurzel zurueckgefallen (der Fall W-04/1 -> W-04,
der heute frueh der erste Fehlbefund des Tages war).
"""
import re

# ERGAENZT 15.08.: ERLEDIGT und VORLAGE fehlten, obwohl §3 sie seit A-21 (12.08.) fuehrt,
# ebenso ABNAHME und RELEASE_PRUEFUNG aus der Kette. Ein Zustand, den das Muster nicht kennt,
# liest sich als "keine Zustandsangabe" — also als etwas, das man ueberspringen darf.
Z = (r'(ENTWURF|BEREIT|IN_ARBEIT|CODE_FERTIG|ABNAHME|ABGENOMMEN|RELEASE_PRUEFUNG|RELEASE_FREI'
     r'|RELEASE_BLOCKED|DECISION_BLOCKED|SPEC_BLOCKED|ENV_BLOCKED|VERÖFFENTLICHT|VEROEFFENTLICHT'
     r'|BETRIEBSBESTAETIGT|ZURUECKGESTELLT|NACHBESSERN|ERLEDIGT|VORLAGE'
     # ERGAENZT 15.08. nachmittags, ZWEITER Fall derselben Art an EINEM Tag: A-36 traegt
     # ZURUECKGEZOGEN (Yamas Entscheidung V-02 vom 14.08.). Das Wort steht NICHT in §3 —
     # 0 Treffer in ARBEITSREGELN.md, genau wie ZURUECKGESTELLT, das A-21 abgeschafft hat.
     # Solange es hier fehlte, las sich A-36 als "Zeile ohne Zustand" und wurde still
     # uebersprungen: die Driftmessung meldete 0 und hatte den Auftrag nie angesehen.
     # Aufgenommen wird das Wort, WEIL es im Bestand steht — nicht weil es gueltig waere.
     r'|ZURUECKGEZOGEN)')


def norm(x):
    x = re.sub(r'\*\*|`', '', x).strip()
    x = re.sub(r'\s*#.*$', '', x).strip()
    x = re.sub(r'\s*\(.*$', '', x).strip()
    # BERICHTIGT 15.08.: hier stand nur ^" — das fuehrende Anfuehrungszeichen fiel, das
    # abschliessende blieb stehen. A-36 traegt ballbesitz: "—" und wurde damit als '—"'
    # gegen die Tafel-Fassung '—' verglichen: eine gemeldete Ball-Drift, die keine war.
    # Ein Werkzeug, das Zeichensetzung fuer Inhalt haelt, erzeugt Arbeit statt sie zu sparen.
    x = re.sub(r'^"|"$', '', x).strip()
    x = x.lower().replace('ü', 'ue')
    return '—' if x in ('–', '-', '—', '') else x


s = open('docs/STATUS.md').read()
zeilen = s.split('\n')

# BERICHTIGT 13.08. nachts (Release-Pruefer): dieses Muster liest den ARBEITSBAUM.
# Solange der sauber ist, ist das dasselbe wie HEAD. Sobald eine fremde Rolle in
# docs/STATUS.md schreibt und noch nicht committet hat, misst es IHRE UNFERTIGE ARBEIT
# und meldet sie als Tatsache. Heute hat das beinahe ein §10 auf eine Abnahme ausgeloest,
# die es im committeten Stand nicht gab (A-30: HEAD CODE_FERTIG, Arbeitsbaum ABGENOMMEN).
# E1 verlangt die Messung AM COMMIT — also wird der Unterschied jetzt ausgewiesen.
import subprocess as _sp


def _zustaende(text):
    zz = text.split('\n')
    _st = [i for i, l in enumerate(zz) if l.startswith('auftrag: ')] + [len(zz)]
    d = {}
    for _a, _b in zip(_st, _st[1:]):
        _n = re.match(r'auftrag: "?([\w/\-\.]+)', zz[_a]).group(1)
        _zu = next((l.split(': ', 1)[1].strip() for l in zz[_a:_b]
                    if l.startswith('zustand: ')), None)
        if _zu:
            d[_n] = _zu
    return d


_head_txt = _sp.run(['git', 'show', 'HEAD:docs/STATUS.md'],
                    capture_output=True, text=True).stdout
if _head_txt:
    _h, _b = _zustaende(_head_txt), _zustaende(s)
    _ab = [(k, _h.get(k), _b.get(k)) for k in set(_h) | set(_b) if _h.get(k) != _b.get(k)]
    if _ab:
        print(f'  ⚠ ARBEITSBAUM WEICHT VON HEAD AB — {len(_ab)} Zustand/Zustaende sind NICHT'
              f' committet und damit kein Prüfstand:')
        for k, hh, bb in sorted(_ab):
            print(f'      {k:8} HEAD {hh} <-> Arbeitsbaum {bb}')

# --- Datensaetze: nur Bloecke MIT zustand-Feld sind Auftragsdatensaetze
starts = [i for i, l in enumerate(zeilen) if re.match(r'^auftrag: ', l)] + [len(zeilen)]
dz, db = {}, {}
for a, b in zip(starts, starts[1:]):
    blk = zeilen[a:b]
    zm = next((re.match(r'^zustand: ' + Z, l) for l in blk if re.match(r'^zustand: ' + Z, l)), None)
    if not zm:
        continue
    name = re.match(r'auftrag: "?([\w/\-\.]+)', zeilen[a]).group(1)
    dz[name] = zm.group(1)
    bm = next((l for l in blk if re.match(r'^ballbesitz:', l)), None)
    if bm:
        db[name] = norm(bm.split(':', 1)[1])

# --- Tafelzeilen
tafel = {}
for l in zeilen:
    m = re.match(r'^\| \*\*([A-Za-z0-9\-/]+)\*\*(.*)$', l)
    if not m:
        continue
    sp = l.split('|')
    # ZWEI SCHREIBWEISEN, beide im Bestand belegt: `BETRIEBSBESTAETIGT` in Backticks und
    # **ERLEDIGT** nur fett (A-06). Das Muster verlangte Backticks — A-06 las sich damit als
    # "Zeile ohne Zustand" und fiel aus jeder Auswertung, obwohl sein Zustand dasteht.
    zm = re.search(Z, sp[2]) if len(sp) > 2 else None
    tafel[m.group(1)] = (zm.group(1) if zm else None,
                         norm(sp[3]) if len(sp) > 3 else None)

# --- Zuordnung: EXAKT zuerst, Wurzel nur als Rueckfall
def tafel_fuer(name):
    if name in tafel:
        return name
    wurzel = name.split('/')[0]
    # Rueckfall nur, wenn kein anderer Datensatz denselben exakten Tafelnamen traegt
    if wurzel in tafel and wurzel not in dz:
        return wurzel
    return None

gz = gb = nz = nb = 0
for name, zust in sorted(dz.items()):
    t = tafel_fuer(name)
    if t is None:
        continue
    tz, tb = tafel[t]
    if tz is not None:
        gz += 1
        if tz != zust:
            print(f'  Z-DRIFT {name} · Tafel[{t}] {tz} <-> Datensatz {zust}')
            nz += 1
    if tb is not None and name in db:
        gb += 1
        if tb != db[name]:
            print(f'  B-DRIFT {name} · Tafel[{t}] {tb!r} <-> Datensatz {db[name]!r}')
            nb += 1

print(f'  Tafelzeilen gesamt {len(tafel)} · davon Zustand geprueft {gz}, Ball geprueft {gb}')
print(f'  Datensaetze mit zustand {len(dz)} · mit ballbesitz {len(db)}')
print(f'  Zustands-Drift {nz} · Ball-Drift {nb}')

# GEGENRICHTUNG, ergaenzt 15.08. (Release-Pruefer) — vorher fehlte sie ganz.
# Die Schleife oben laeuft ueber DATENSAETZE und sucht die passende Tafelzeile. Eine
# Tafelzeile OHNE Datensatz wird darin nie angefasst: sie kann keine Drift erzeugen, weil
# nichts sie mit etwas vergleicht. Das Werkzeug meldete darum jahrelang "Drift 0" fuer
# einen Zustand, den niemand begruendet hat — genau der Fehlertyp, den §16 mit den zwei
# Orten verhindern will, und derselbe wie beim Konfliktwerkzeug: Nulltreffer las sich
# als Sauberkeit. A-20 verlangt ausdruecklich einen Datensatz je geschnittenem Auftrag.
#
# Aufgeloest wird ueber KINDER: die Tafelzeile W-01 ist die Sammelzeile fuer den
# Datensatz W-01/1. Wer das nicht aufloest, meldet 15 statt 2 und die Zahl ist wertlos.
ohne = []
for t in tafel:
    if t in dz or any(d.startswith(t + '/') or d.startswith(t + '-') for d in dz):
        continue
    if tafel[t][0] is None:      # keine Zustandsangabe -> keine Auftragszeile
        continue
    ohne.append(t)
if ohne:
    print(f'  ⚠ TAFELZEILE OHNE DATENSATZ: {len(ohne)} — Zustand steht nur an EINEM der zwei'
          f' Orte (§16), niemand hat ihn begruendet:')
    for t in sorted(ohne):
        print(f'      {t:16} Tafel sagt {tafel[t][0]}')
else:
    print('  Tafelzeilen ohne Datensatz 0')
