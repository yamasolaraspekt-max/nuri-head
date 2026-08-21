#!/usr/bin/env python3
# ruff: noqa
r"""A-42 — Die Befundnotizen ziehen aus docs/STATUS.md in docs/BEFUNDNOTIZEN.md um.

WARUM DIESER LAUF VOR DEM ERSTEN SCHREIBENDEN --tafel-LAUF FERTIG SEIN MUSS:
`status-erzeugen.sh --tafel` erzeugt die Statuswahrheit aus dem Commit-Log, je Kennung
gewinnt der juengste Eintrag. Ein Block ohne Kennung und ohne Zustand kommt darin nicht vor.
Wer die erzeugte Tafel schreibt, bevor diese Bloecke umgezogen sind, entfernt sie lautlos aus
dem lebenden Dokument — und niemand merkt es, weil sie in keiner Tafelzeile stehen.

DREI KLASSEN, und nur eine zieht um:
  1  Block MIT `zustand: <GROSSBUCHSTABE>`  -> echter Auftrag, bleibt unberuehrt
  2  Block MIT `auftrag:` OHNE `zustand:`   -> Befundnotiz, zieht um
  3  Block OHNE beides                      -> dritte Klasse, bleibt, wird nur GEZAEHLT
Die dritte Klasse ist die Grenze dieses Auftrags und keine Nachlaessigkeit: nach dem Umzug
sieht ein Leser Auftraege und daneben Bloecke, die aussehen wie Auftraege und keine sind.
Was mit ihnen geschieht, ist ein eigener Vorgang und gehoert Yama.

DER ERSTE LAUF DIESES WERKZEUGS HAT docs/STATUS.md BESCHAEDIGT — zurueckgenommen, Ursache
hier festgehalten, damit sie niemand wiederholt:
  Ich schnitt die Bloecke ZEICHENWEISE mit ```yaml\n([\s\S]*?)``` . Zehn Zeilen in STATUS.md
  tragen einen Zaun MITTEN in einem yaml-Wert — Befundtexte, die ueber Zaunzeichen schreiben und
  sie dabei zitieren. Eine davon zitiert woertlich genau dieses Muster. Das Muster hielt so ein
  Zitat fuer das Blockende, schnitt sechs Bloecke zu frueh ab, und das Entfernen riss danach
  Prosa mitten aus Saetzen: aus einem Satz ueber Zaeune blieb die Zeile "yaml oeffnet, ```
  schliesst" stehen.
  UND DIE VORGESCHRIEBENEN GEGENPROBEN BLIEBEN ALLE GRUEN. A-42-2 heisst im Blatt "die einzige
  Pruefung, die einen stillen Verlust findet" — sie hat ihn nicht gefunden, weil sie BLOECKE
  zaehlt und der Schaden in der Prosa DAZWISCHEN lag. Gefunden hat ihn `git diff --stat`:
  35 Einfuegungen bei einem reinen Umzug sind eine Unmoeglichkeit, und die war das Signal.
  ZWEI FOLGEN, beide gebaut: der Schnitt ist jetzt ZEILENBASIERT wie scripts/bloecke.py
  Pruefung D (Oeffner = Zeile, die mit ```yaml beginnt; Schliesser = Zeile, die genau ``` ist)
  — ein Zaun mitten in einer Zeile kann damit nichts mehr schliessen. Und es gibt zwei neue
  Gegenproben, die Prosa sehen: der Schnittwaechter (jede entfernte Stelle beginnt und endet
  an einer Zeilengrenze, erste Zeile ```yaml, letzte ```) und die ZEILENBILANZ (die Multimenge
  aller Zeilen vorher = nachher + umgezogen). Die Zeilenbilanz haette den Schaden sofort gemeldet.

NEBENBEFUND, nicht meine Baustelle, aber gemessen: scripts/bloecke.py Pruefung C schneidet
ebenfalls zeichenweise und teilt dieselben sechs Bloecke falsch — sie meldet 23 kaputte, der
zeilenbasierte Schnitt 22. Der Unterschied ist genau ein faelschlich geteilter Block. Gehoert
dem Eigentuemer jenes Werkzeugs, nicht diesem Lauf.

ZWEI ZAEHLBEFEHLE, UND SIE WIDERSPRECHEN SICH — gemessen, nicht angenommen:
  A-42-1 nennt  ```yaml(.*?)```      mit re.S  -> 465 Bloecke
  scripts/bloecke.py nennt ```yaml\\n([\\s\\S]*?)``` -> 461 Bloecke
Die vier Mehrtreffer des Auftragsbefehls sind PROSA UEBER ZAUNZEICHEN (Z.58, 13215, 13253,
13326) — Saetze, die die Zeichenfolge ```yaml zitieren, um ueber sie zu schreiben. Genau der
rekursive Fall, den bloecke.py seit dem 15.08. dokumentiert. Dieses Werkzeug schneidet deshalb
STRENG: ein Muster, das Prosa fuer einen Block haelt, wuerde beim Umzug einen Satz aus der
Mitte eines echten Blocks herausschneiden. Gemeldet werden BEIDE Zahlen, damit A-42-1
buchstaeblich erfuellt ist und der Unterschied trotzdem nicht unter den Tisch faellt.

WAS DER LAUF NICHT TUT: nichts loeschen, nichts kuerzen, nichts umformulieren, keinen kaputten
Block reparieren (K4 — Reparatur unter Bewegung), keinen Zweifelsfall entscheiden (K1),
nichts ausserhalb von docs/ anfassen.
"""
import hashlib
import io
import json
import re
import subprocess
import sys

QUELLE = 'docs/STATUS.md'
ZIEL = 'docs/BEFUNDNOTIZEN.md'
ZAUN = '`' * 3
STRENG = re.compile(ZAUN + r'yaml\n([\s\S]*?)' + ZAUN)
WEIT = re.compile(ZAUN + r'yaml(.*?)' + ZAUN, re.S)
ROLLEN = ['plan-pruefer', 'planner', 'generator', 'release-pruefer', 'integrator',
          'evaluator', 'dirigent']
# Eine Kennung ist A-42, W-01, Z2-W0-5, F-010 … — ein Grossbuchstabenkopf, ein Trenner,
# dann Ziffern/Buchstaben. Alles andere ist Freitext (gemessen: 93 kennungsfoermig, 79 frei).
KENNUNGSFORM = re.compile(r'^[A-Z][A-Za-z0-9]*[-–][0-9A-Za-z._–-]+$')


def stand_sha():
    return subprocess.run(['git', 'rev-parse', '--short', 'HEAD'],
                          capture_output=True, text=True).stdout.strip()


def bloecke(text):
    """(nummer, start, ende, innentext) je Block — ZEILENBASIERT, 1-basiert.

    Oeffner ist eine Zeile, die (nach strip) mit ```yaml beginnt; Schliesser eine Zeile, die
    genau ``` ist UND DENSELBEN EINZUG TRAEGT WIE IHR OEFFNER. Alles andere — ein Zaun mitten
    in einer Zeile ebenso wie ein EINGERUECKTER Zaun — ist Inhalt.

    Der Einzug ist nicht Feinschliff, sondern der zweite Fehlschlag dieses Werkzeugs: der
    Datensatz bei Z.19867 traegt im Wert `was_im_blatt_wirklich_steht: |` einen um zwei
    Zeichen eingerueckten ```text-Block. Ohne Einzugsvergleich schliesst dessen Schliesser den
    UMGEBENDEN Block — der Datensatz wird mitten durchgeschnitten, die eine Haelfte zieht um,
    die andere bleibt. Gefunden hat das keine der vorgeschriebenen Gegenproben, sondern die
    ZAUNBILANZ aus scripts/bloecke.py: sie stand nach dem Lauf auf UNGERADE.
    Das ist dieselbe Logik wie scripts/bloecke.py Pruefung D und der einzige Schnitt, der die
    zehn Befundtexte ueberlebt, die ueber Zaunzeichen schreiben und sie dabei zitieren.
    `start` zeigt auf den Zeilenanfang des Oeffners, `ende` hinter den Zeilenumbruch des
    Schliessers — beide also immer auf einer Zeilengrenze.
    """
    aus = []
    versatz = 0
    anfang = None
    kopf = 0
    einzug = ''
    for zeile in text.split('\n'):
        laenge = len(zeile) + 1
        s = zeile.strip()
        if anfang is None:
            if s.startswith(ZAUN + 'yaml'):
                anfang, kopf = versatz, versatz + laenge
                einzug = zeile[:len(zeile) - len(zeile.lstrip())]
        elif s == ZAUN and zeile[:len(zeile) - len(zeile.lstrip())] == einzug:
            aus.append((len(aus) + 1, anfang, versatz + laenge, text[kopf:versatz]))
            anfang = None
        versatz += laenge
    return aus


def parsbarkeit(inhalte):
    """Welche Bloecke sind kaputtes yaml? Ueber js-yaml, wie scripts/bloecke.py.

    PyYAML liegt in dieser Umgebung nicht; js-yaml ist seit A-37-21 eine versionierte
    Abhaengigkeit. UNGEPRUEFT waere hier kein zulaessiges Ergebnis: K4 entscheidet, ob ein
    Block umzieht, und ein Werkzeug, das aus Umgebungsgruenden schweigt, wuerde kaputte
    Bloecke mitnehmen und dabei gruen aussehen. Darum bricht der Lauf ab statt zu raten.
    """
    r = subprocess.run(
        ['node', '-e',
         'const y=require("js-yaml");const a=JSON.parse(require("fs").readFileSync(0,"utf8"));'
         'console.log(JSON.stringify(a.map(s=>{try{y.load(s);return true}catch(e){return false}})));'],
        input=json.dumps(inhalte), capture_output=True, text=True)
    if r.returncode != 0:
        grund = next((l.strip() for l in r.stderr.splitlines()
                      if 'Error' in l or 'Cannot find' in l), 'node lieferte keinen Grund')
        print('  ABBRUCH — die Parsbarkeit ist UNGEPRUEFT, und ungeprueft ist hier kein Ergebnis.')
        print(f'  Grund: {grund[:90]}')
        print('  Abhilfe: aus dem Repo-Verzeichnis fahren (js-yaml liegt in node_modules).')
        sys.exit(2)
    return json.loads(r.stdout)


def klassifiziere(text):
    alle = bloecke(text)
    heil = parsbarkeit([b[3] for b in alle])

    auftrag, notiz, dritte, k1, k4 = [], [], [], [], []
    for (nr, a, e, inhalt), ok in zip(alle, heil):
        eintrag = {'nr': nr, 'start': a, 'ende': e, 'inhalt': inhalt, 'heil': ok}
        hat_auftrag = re.search(r'^auftrag:', inhalt, re.M) is not None
        hat_zustand_gross = re.search(r'^zustand: *[A-Z]', inhalt, re.M) is not None

        if hat_zustand_gross:
            auftrag.append(eintrag)
        elif hat_auftrag:
            if re.search(r'^zustand:', inhalt, re.M):
                k1.append(eintrag)          # K1 — Zweifelsfall bleibt, wo er ist
            elif not ok:
                k4.append(eintrag)          # K4 — kaputt zieht nicht um
            else:
                notiz.append(eintrag)
        else:
            dritte.append(eintrag)
    return alle, auftrag, notiz, dritte, k1, k4


def kennung_von(inhalt):
    m = re.search(r'^auftrag:\s*(.*)$', inhalt, re.M)
    roh = (m.group(1).strip() if m else '').strip('"').strip("'")
    return roh


def bekannte_kennungen(auftrag_bloecke):
    return {kennung_von(b['inhalt']) for b in auftrag_bloecke}


def zaunbilanz(text):
    """Oeffner/Schliesser je Einzugsebene paaren. Liefert die Zahl unpaariger Zaeune.

    DIESE PROBE HAT DEN ZWEITEN FEHLSCHLAG GEFUNDEN, und keine der im Blatt vorgeschriebenen.
    A-42-2 zaehlt Bloecke, A-42-3 hasht sie, die Zeilenbilanz zaehlt Zeilen — alle drei waren
    gruen, waehrend ein Datensatz mitten durchgeschnitten war: seine eine Haelfte zog um, die
    andere blieb, Zeilen und Bloecke gingen auf. Erst die ungerade Zaunbilanz zeigte es.
    Sie laeuft deshalb ab jetzt VOR und NACH dem Umzug, ueber beide Dateien.
    """
    offen = []
    unpaarig = 0
    for zeile in text.split('\n'):
        s = zeile.strip()
        if not s.startswith(ZAUN):
            continue
        ein = zeile[:len(zeile) - len(zeile.lstrip())]
        if s == ZAUN and offen and offen[-1] == ein:
            offen.pop()
        elif s == ZAUN and offen:
            unpaarig += 1
        elif s == ZAUN:
            unpaarig += 1
        else:
            offen.append(ein)
    return unpaarig + len(offen)


def ballbesitz(text):
    return {r: len(re.findall(rf'^ballbesitz: {re.escape(r)}$', text, re.M)) for r in ROLLEN}


def bericht_messen(text):
    alle, auftrag, notiz, dritte, k1, k4 = klassifiziere(text)
    print(f'  Zaehlbefehl A-42-1 (weit):   {len(WEIT.findall(text))} Bloecke')
    print(f'  Zaehlbefehl streng:          {len(alle)} Bloecke   '
          f'(Unterschied {len(WEIT.findall(text)) - len(alle)} = Prosa ueber Zaunzeichen)')
    print()
    print(f'  mit zustand (bleiben)        {len(auftrag):>4}')
    print(f'  Befundnotizen (ziehen um)    {len(notiz):>4}')
    print(f'  K1 Zweifelsfaelle (bleiben)  {len(k1):>4}')
    print(f'  K4 kaputtes yaml (bleiben)   {len(k4):>4}')
    print(f'  dritte Klasse (bleiben)      {len(dritte):>4}')
    print(f'  Summe                        {len(auftrag)+len(notiz)+len(k1)+len(k4)+len(dritte):>4}'
          f'  gegen {len(alle)} gefundene')
    for e in k1:
        print(f'    K1  Block {e["nr"]}  {re.search(r"^zustand:.*", e["inhalt"], re.M).group(0)[:70]}')
    for e in k4:
        zeile = text[:e['start']].count('\n') + 1
        print(f'    K4  Block {e["nr"]}  Z.{zeile}  {kennung_von(e["inhalt"])[:50]}')
    print(f'  Zaunbilanz unpaarig: {zaunbilanz(text)}  (0 = jeder Oeffner hat seinen Schliesser)')
    print()
    print('  Ballbesitz je Rolle, vorher:')
    for r, n in ballbesitz(text).items():
        print(f'    {r:<16} {n}')
    return alle, auftrag, notiz, dritte, k1, k4


def main():
    modus = sys.argv[1] if len(sys.argv) > 1 else '--messen'
    text = io.open(QUELLE, encoding='utf-8').read()
    sha = stand_sha()
    print(f'A-42 · {modus} · Stand-SHA {sha} · {QUELLE}')
    print()

    alle, auftrag, notiz, dritte, k1, k4 = bericht_messen(text)

    if modus == '--messen':
        return 0

    if modus != '--umzug':
        print(f'  unbekannter Modus {modus} — erlaubt sind --messen und --umzug')
        return 2

    bekannt = bekannte_kennungen(auftrag)
    vorher_hash = {e['nr']: hashlib.sha256(e['inhalt'].encode()).hexdigest() for e in notiz}
    auftrag_hash = {e['nr']: hashlib.sha256(e['inhalt'].encode()).hexdigest() for e in auftrag}
    ball_vorher = ballbesitz(text)

    # ---- Zieldatei aufbauen (K6: anhaengen, nicht ueberschreiben) ----
    try:
        vorhanden = io.open(ZIEL, encoding='utf-8').read()
        alt_eintraege = vorhanden.count('\nherkunft: ')
        print(f'  K6  {ZIEL} existiert bereits mit {alt_eintraege} Eintraegen — es wird ANGEHAENGT.')
    except FileNotFoundError:
        vorhanden = ''
        alt_eintraege = 0
        print(f'  K6  {ZIEL} existiert nicht — wird neu angelegt (0 vorhandene Eintraege).')

    teile = []
    if not vorhanden:
        teile.append(KOPF.format(sha=sha))

    frei_n = unbekannt_n = 0
    for e in notiz:
        k = kennung_von(e['inhalt'])
        marken = []
        if not KENNUNGSFORM.match(k):
            marken.append('kennung_nicht_zuordenbar: true   # Freitext statt Kennung — '
                          'uebernommen wie vorgefunden, nicht gedeutet')
            frei_n += 1
        elif k not in bekannt:
            marken.append('kennung_unbekannt: true   # K3 — Kennung ohne Auftragsdatensatz; '
                          'kein Auftrag, aber ein Beleg')
            unbekannt_n += 1
        teile.append(
            f'\nherkunft: {QUELLE} · Block {e["nr"]} · {sha}\n'
            + (''.join(m + '\n' for m in marken))
            + ZAUN + 'yaml\n' + e['inhalt'] + ZAUN + '\n'
        )

    io.open(ZIEL, 'a' if vorhanden else 'w', encoding='utf-8').write(''.join(teile))

    # ---- Aus der Quelle entfernen, von hinten nach vorn (Versatz bleibt gueltig) ----
    neu = text
    for e in sorted(notiz, key=lambda x: x['start'], reverse=True):
        # SCHNITTWAECHTER — die Probe, die beim ersten Lauf gefehlt hat. Jede entfernte
        # Stelle muss an einer Zeilengrenze beginnen und enden, ihre erste Zeile ein
        # ```yaml-Oeffner und ihre letzte ein ```-Schliesser sein. Schneidet das Werkzeug
        # jemals wieder mitten in einen Satz, bricht es hier ab statt es zu tun.
        stueck = neu[e['start']:e['ende']]
        assert e['start'] == 0 or neu[e['start'] - 1] == '\n', \
            f"Block {e['nr']}: Schnittanfang liegt nicht auf einer Zeilengrenze"
        assert stueck.endswith('\n'), f"Block {e['nr']}: Schnittende liegt nicht auf einer Zeilengrenze"
        zs = stueck.split('\n')
        assert zs[0].strip().startswith(ZAUN + 'yaml'), f"Block {e['nr']}: erste Zeile ist kein Oeffner"
        assert zs[-2].strip() == ZAUN, f"Block {e['nr']}: letzte Zeile ist kein Schliesser"

        ende = e['ende']
        while ende < len(neu) and neu[ende] == '\n':
            ende += 1
        neu = neu[:e['start']] + neu[ende:]

    # ZEILENBILANZ — die zweite fehlende Probe. Jede Zeile der Ausgangsdatei muss danach
    # entweder in der Quelle oder im Umzugsgut stehen. Sie sieht Prosa, die A-42-2 nicht sieht.
    import collections
    vorher_z = collections.Counter(text.split('\n'))
    nachher_z = collections.Counter(neu.split('\n'))
    for e in notiz:
        nachher_z.update(text[e['start']:e['ende']].split('\n'))
    verloren = {z: n for z, n in (vorher_z - nachher_z).items() if z.strip()}
    io.open(QUELLE, 'w', encoding='utf-8').write(neu)

    # ---- Gegenproben ----
    print()
    print('  GEGENPROBEN')
    n_alle, n_auftrag, n_notiz, n_dritte, n_k1, n_k4 = klassifiziere(neu)
    ziel_text = io.open(ZIEL, encoding='utf-8').read()
    ziel_bloecke = bloecke(ziel_text)

    zb_q, zb_z = zaunbilanz(neu), zaunbilanz(io.open(ZIEL, encoding='utf-8').read())
    print(f'    ZAUNBILANZ    unpaarig {QUELLE} {zb_q} · {ZIEL} {zb_z}  '
          f'{"OK" if zb_q == 0 and zb_z == 0 else "ZERSCHNITTEN"}')
    print(f'    ZEILENBILANZ  verlorene Zeilen: {len(verloren)}  '
          f'{"OK" if not verloren else "VERLUST: " + str(list(verloren)[:3])}')

    a1 = len(n_notiz)
    print(f'    A-42-1  Notizen in {QUELLE} nachher: {a1}  '
          f'{"OK" if a1 == 0 else "NICHT NULL"}')

    summe = len(n_alle) + len(ziel_bloecke) - alt_eintraege
    print(f'    A-42-2  {len(alle)} vorher = {len(n_alle)} nachher + '
          f'{len(ziel_bloecke) - alt_eintraege} umgezogen -> {summe}  '
          f'{"OK" if summe == len(alle) else "ABWEICHUNG"}')

    nachher_hash = {}
    for nr, _, _, inhalt in ziel_bloecke:
        nachher_hash[hashlib.sha256(inhalt.encode()).hexdigest()] = True
    fehlend = [nr for nr, h in vorher_hash.items() if h not in nachher_hash]
    print(f'    A-42-3  {len(vorher_hash)} Bloecke byte-identisch: '
          f'{"OK" if not fehlend else "ABWEICHUNG bei " + str(fehlend[:5])}')

    herkunft_n = ziel_text.count('\nherkunft: ')
    print(f'    A-42-4  Herkunftszeilen: {herkunft_n - alt_eintraege} zu {len(notiz)} Umzuegen  '
          f'{"OK" if herkunft_n - alt_eintraege == len(notiz) else "ABWEICHUNG"}')
    print(f'            davon Freitext markiert {frei_n}, Kennung ohne Datensatz {unbekannt_n}')

    a6 = {e['nr']: hashlib.sha256(e['inhalt'].encode()).hexdigest() for e in n_auftrag}
    gleich = set(auftrag_hash.values()) == set(a6.values())
    print(f'    A-42-6  Auftragsbloecke {len(auftrag)} -> {len(n_auftrag)}, Inhalte '
          f'{"unveraendert OK" if gleich else "ABWEICHUNG"}')

    ball_nachher = ballbesitz(neu)
    ball_ziel = ballbesitz(ziel_text)
    schief = []
    for r in ROLLEN:
        if ball_vorher[r] != ball_nachher[r] + ball_ziel[r]:
            schief.append(r)
    print(f'    A-42-11 Ballortung ueberlebt: {"OK" if not schief else "ABWEICHUNG " + str(schief)}')
    for r in ROLLEN:
        print(f'            {r:<16} vorher {ball_vorher[r]:>3}  =  '
              f'{QUELLE} {ball_nachher[r]:>3} + {ZIEL} {ball_ziel[r]:>3}')

    print()
    print('    A-42-12 NEUER ORTUNGSBEFEHL je Rolle — der alte wird ab jetzt still falsch:')
    print(f"            grep -cE '^ballbesitz: <rolle>$' {QUELLE} {ZIEL}")
    print('            Falsch werden: die Wacheanweisung des Plan-Pruefers und')
    print('            scripts/yama-posten.py — beide lesen nur docs/STATUS.md.')
    return 0 if (a1 == 0 and summe == len(alle) and not fehlend and not schief
                 and not verloren and zb_q == 0 and zb_z == 0) else 1


KOPF = """# Befundnotizen — ausgezogen aus `docs/STATUS.md` (A-42)

Diese Datei traegt **Befund-, Antwort- und Berichtigungsnotizen** anderer Rollen, die als
yaml-Bloecke in der Statuswahrheit lagen: sie fuehren ein Feld `auftrag:`, aber keinen
`zustand:` und sind damit **keine Auftragsdatensaetze**.

**Warum sie hier stehen und nicht mehr dort:** `scripts/status-erzeugen.sh --tafel` erzeugt
`docs/STATUS.md` aus dem Commit-Log — je Kennung gewinnt der juengste Eintrag. Ein Block ohne
Kennung und ohne Zustand kommt in einer erzeugten Tafel nicht vor. Der erste schreibende Lauf
haette sie lautlos entfernt, und niemand haette es bemerkt, weil sie in keiner Tafelzeile
stehen. **Ihr Inhalt ist gut, ihr Ort war falsch.**

**Nichts wurde geloescht, gekuerzt oder umformuliert.** Jeder Block steht hier byte-identisch,
mit einer vorangestellten Herkunftszeile `herkunft: docs/STATUS.md · Block <n> · <sha>`.

**Die Herkunftszeile nennt den Stand-SHA des Laufs, nicht den Basis-SHA des Auftragsblattes** —
die Blocknummer ist nur an dem Stand gueltig, an dem sie erhoben wurde. Ein Zeiger auf einen
mehrere hundert Commits alten Stand wuerde auf einen anderen Block zeigen. Stand: `{sha}`.

**Ballbesitz orten geht ab jetzt ueber BEIDE Dateien:**

```bash
grep -cE '^ballbesitz: <rolle>$' docs/STATUS.md docs/BEFUNDNOTIZEN.md
```

Wer nur `docs/STATUS.md` liest, bekommt eine richtige Antwort auf eine falsche Frage.

---
"""

if __name__ == '__main__':
    sys.exit(main())
