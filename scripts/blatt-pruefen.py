#!/usr/bin/env python3
# ruff: noqa
r"""A-39 — acht Pruefungen, die ein Auftragsblatt gegen sich selbst haelt.

Diese acht Fehlerformen sind KEINE Theorie. Jede ist an diesem Projekt vorgekommen, jede war vor
dem ersten Zeichen Code im Blatt vorhanden, jede wurde erst gefunden, als jemand das Blatt BENUTZEN
wollte, und jede kostete eine Runde. Keine wurde von einer Pruefstation gefunden.

WO DAS LAEUFT: im DoR-Schritt, nicht im Tor. Das Tor prueft Commits, dieser Pruefer misst ein BLATT
— und ein Blatt entsteht, bevor es einen Commit gibt. Im Tor kaeme die Pruefung zu spaet und traefe
den Falschen: der Bauende hat den Blattfehler nicht verursacht.

WAS ER NICHT TUT: er prueft FORM, nicht Wahrheit. Ob die Aussagen eines Blattes stimmen, ist A-40
und eine andere Klasse. Er aendert nie ein Blatt — er meldet, das Beheben gehoert dem Planner.

REICHWEITE, und sie gehoert genannt, damit niemand sie ueberschaetzt: die Pruefungen P1 und P5
brauchen die Struktur "Kantentabelle" bzw. "Code-Tabelle". Ein Lauf ueber alle Blaetter endet
deshalb mit vielen stummen Durchlaeufen. Der Sammellauf nennt beide Zahlen — geprueft und
uebersprungen. Ein Pruefer, der nicht sagt, was er NICHT angesehen hat, wird fuer gruendlicher
gehalten als er ist.
"""
import io
import os
import re
import sys

ZAUN = '`' * 3


# ── Blatt zerlegen ────────────────────────────────────────────────────────────────────────────

def kopf(text):
    """Der erste yaml-Block ist der Blattkopf. Einzugstreu geschnitten wie in A-42 gelernt:
    ein eingerueckter Zaun im Wert eines Feldes schliesst den Block NICHT."""
    zeilen = text.split('\n')
    anfang = None
    einzug = ''
    for i, z in enumerate(zeilen):
        s = z.strip()
        if anfang is None:
            if s.startswith(ZAUN + 'yaml'):
                anfang = i + 1
                einzug = z[:len(z) - len(z.lstrip())]
        elif s == ZAUN and z[:len(z) - len(z.lstrip())] == einzug:
            return '\n'.join(zeilen[anfang:i])
    return ''


def kriterien(text):
    """Abnahmekriterien als (kennung, text). Ein Kriterium beginnt mit '- **<KENN>**' und laeuft
    bis zum naechsten Kriterium oder zur naechsten Ueberschrift.

    DIE SACHE IST DAS AUFZAEHLUNGSZEICHEN, NICHT DER ABSCHNITTSNAME (P8 an mir selbst): Blaetter
    nennen den Abschnitt mal '## Abnahmekriterien', mal '## Nachvollzugs-Matrix'. Gesucht wird die
    FORM eines Kriteriums, der Abschnitt dient nur der Eingrenzung, wenn es ihn gibt.
    """
    ab = text
    m = re.search(r'^##+ *(Abnahmekriterien|Nachvollzugs-Matrix)', text, re.M)
    if m:
        rest = text[m.end():]
        n = re.search(r'^##+ ', rest, re.M)
        ab = rest[:n.start()] if n else rest

    # ZWEI SCHREIBWEISEN, und die zweite hat mich beim Bauen eingeholt:
    #   a)  - **A-37-11** · ...            Aufzaehlung, die haeufigere
    #   b)  A-33-1 (P1, TRAGEND) ...       Zeilenanfang in einem Codeblock (A-33 schreibt so)
    # Meine erste Fassung kannte nur (a) und fand in A-33 NULL Kriterien — sie haette das Blatt
    # stumm durchgewunken. Das ist P8 an mir selbst: ich hatte die FORM kodiert (das
    # Aufzaehlungszeichen) statt die SACHE (eine Zeile, die mit der Kriteriumskennung beginnt).
    # Die Kennung kommt aus dem Blattkopf, damit 'A-30' im Fliesstext nicht als Kriterium zaehlt.
    m2 = re.search(r'^auftrag:\s*"?([A-Za-z0-9/-]+?)"?\s*$', kopf(text), re.M)
    stamm = re.escape(m2.group(1)) if m2 else r'[A-Z][A-Za-z0-9./-]*'
    muster = re.compile(rf'^ *(?:- +\*\*)?({stamm}-[0-9]+[a-z]?)(?:\*\*)?(?=[ \t)·:.]|$)', re.M)
    treffer = list(muster.finditer(ab))
    aus = []
    for i, t in enumerate(treffer):
        ende = treffer[i + 1].start() if i + 1 < len(treffer) else len(ab)
        aus.append((t.group(1), ab[t.start():ende]))
    return aus


def kanten(text):
    """Kanten aus Tabellenzeilen: | K1 | ... |. K2 des Blattes ist ausdruecklich Grenze —
    eine Kante, die nur im Fliesstext steht, wird NICHT erfasst und das wird gesagt, nicht
    stillschweigend gemacht."""
    return sorted(set(re.findall(r'^\|\s*\*{0,2}(K\d+)\*{0,2}\s*\|', text, re.M)),
                  key=lambda k: int(k[1:]))


def nicht_ziele(text):
    m = re.search(r'^##+ *Nicht-Ziele', text, re.M)
    if not m:
        return ''
    rest = text[m.end():]
    n = re.search(r'^##+ ', rest, re.M)
    return rest[:n.start()] if n else rest


def ueberholt_zitiert(t):
    """Zitiert dieses Kriterium seinen EIGENEN, ueberholten Wortlaut?

    P4 UND P6 sind mir beide an derselben Sache falsch angeschlagen, und das ist kein Zufall:
    ein Blatt, das einen Fehler behebt, SCHREIBT DEN ALTEN WORTLAUT DAZU — "die alte Fassung
    verlangte scripts/ null Mal", "der alte Befehl, nur noch als Beleg — NICHT mehr benutzen".
    Wer nur nach der Zeichenfolge sucht, meldet genau die Blaetter, die den Fehler bereits
    behoben haben, und laesst die unbehobenen in Ruhe. Das ist die Umkehrung des Zwecks.

    Dieselbe rekursive Falle, die scripts/bloecke.py seit dem 15.08. bei Zaunzeichen
    dokumentiert: wer ueber ein Problem schreibt, benutzt seine Merkmale.
    """
    return bool(re.search(r'alte\s+Fassung|NEUGEFASST|nicht\s+mehr\s+benutzen|nur noch als Beleg'
                          r'|ersetzt\s+durch|BERICHTIGT|berichtigt|war\s+.{0,40}unvereinbar'
                          r'|abgesetzt|ueberholt|überholt', t))


def hat_standbezug(t):
    return bool(re.search(r'[0-9a-f]{7,40}', t)               # SHA
                or re.search(r'\d{1,2}\.\d{2}\.', t)          # Datum 16.08.
                or re.search(r'Bau-Stand|Basis-SHA|basis_sha|Stand\s+`', t, re.I))


# ── Die acht Pruefungen ───────────────────────────────────────────────────────────────────────

def p1(text, funde):
    """KANTE OHNE KRITERIUM. Jede Kante muss von mindestens einem Kriterium genannt werden —
    namentlich oder pauschal ('alle sechs Kanten')."""
    ks = kanten(text)
    if not ks:
        return  # K1 des Blattes: kein Kantenliste -> P1 gilt als erfuellt
    krit = kriterien(text)
    volltext = ' '.join(t for _, t in krit)
    pauschal = re.search(r'alle\s+(sechs|sieben|acht|f(ue|ü)nf|vier|drei|\d+)?\s*Kanten|Kanten\s+K\d+\s*[–-]\s*K\d+',
                         volltext, re.I)
    if pauschal:
        return
    for k in ks:
        if not re.search(rf'\b{k}\b', volltext):
            zeile = next((i + 1 for i, z in enumerate(text.split('\n'))
                          if re.match(rf'^\|\s*\*{{0,2}}{k}\*{{0,2}}\s*\|', z)), 0)
            funde.append(('P1', f'Kante {k} steht in einer Tabellenzeile (Z.{zeile}), '
                                f'aber kein Abnahmekriterium nennt sie'))


def p2(text, funde):
    """FESTE ZAHL OHNE STANDBEZUG. Eine Zahl, die eine Bestandsaussage traegt, laeuft ab —
    ohne dass der Schreibende es erfaehrt. K3 des Blattes: eine Zahl in einer BEGRUENDUNG ist
    keine Zusage, deshalb wird nur in Kriterien gesucht."""
    zahlwort = re.compile(r'genau\s+(EIN[SE]?|ZWEI|DREI|VIER|F(UE|Ü)NF|\d+)'
                          r'|Suite\s+\d{3,}'
                          r'|\b\d+\s*(Treffer|Zeilen|Dateien|Bloecke|Blöcke)\b'
                          r'|\b\d+\s+von\s+\d+\b', re.I)
    for kenn, t in kriterien(text):
        m = zahlwort.search(t)
        if m and not hat_standbezug(t):
            funde.append(('P2', f'{kenn} bindet die Zahl "{m.group(0).strip()}" an eine '
                                f'Bestandsaussage, ohne SHA, Datum oder Bau-Stand zu nennen'))


_BESTAND = None


def bestand():
    """Alle versionierten Pfade UND ihre nackten Dateinamen, einmal erhoben.

    Blaetter nennen Gegenstaende mal voll (`docs/STATUS.md`), mal nackt (`STATUS.md`,
    `dachformVorlagen.ts`). Wer nur den vollen Pfad prueft, haelt jede verkuerzte Nennung fuer
    eine fehlende Datei — im Sammellauf waren das die haeufigsten Fehlalarme. Gefragt ist die
    SACHE ("gibt es das schon?"), nicht die Schreibweise, mit der sie genannt wird.
    """
    global _BESTAND
    if _BESTAND is None:
        import subprocess
        r = subprocess.run(['git', 'ls-files'], capture_output=True, text=True)
        pfade = r.stdout.split('\n') if r.returncode == 0 else []
        _BESTAND = set(p for p in pfade if p) | set(p.rsplit('/', 1)[-1] for p in pfade if p)
    return _BESTAND


def p3(text, funde):
    """GEFORDERTE DATEI OHNE ERZEUGER. Verlangt ein Kriterium eine Datei als VORHANDEN, muss das
    Blatt sagen, wer sie erzeugt. K4 des Blattes: ein Pfad als NICHT-Ziel braucht keinen Erzeuger."""
    nz = nicht_ziele(text)
    krit = kriterien(text)
    volltext = text
    # Endungen duerfen Bindestriche tragen. Mein erstes Muster liess sie nicht zu und fand
    # `node_modules/.aus-lockfile` NICHT — also genau die Datei, an der der historische P3-Fall
    # haengt. Ein Pruefer, der seinen eigenen Belegfall nicht erkennt, ist keiner.
    pfad = re.compile(r'`([./A-Za-z0-9_-]+\.[A-Za-z0-9][A-Za-z0-9-]{0,20}|\.[a-z][a-z-]+)`')
    for kenn, t in krit:
        if not re.search(r'existiert|vorhanden|liegt vor|traegt|trägt|muss .*(geben|existieren)', t, re.I):
            continue
        for p in set(pfad.findall(t)):
            if p in nz:
                continue                       # K4
            # EXISTIERT der Gegenstand bereits, braucht er keinen Erzeuger — das ist der
            # Unterschied zwischen "die Datei muss noch entstehen, und niemand baut sie" und
            # "die Datei liegt da". Ohne diese Bedingung meldete P3 im Sammellauf HUNDERT Funde,
            # darunter docs/STATUS.md, scripts/rollen-tor.sh und RoofNode.schichten — lauter
            # vorhandene Dinge und ein Symbolname. Ein Pruefer mit dieser Trefferquote wird
            # weggeklickt, und dann faengt er auch die echten Faelle nicht mehr (A-03).
            if os.path.exists(p) or p in bestand() or p.lstrip('./') in bestand():
                continue
            # Ein Punktname ohne Verzeichnis ist meist ein SYMBOL, kein Pfad
            # (`RoofNode.schichten`, `roof.visible`). P3 misst Dateien; wer Symbole meldet,
            # meldet Rauschen. Die Grenze: Symbole tragen keinen Schraegstrich und ihre
            # Endung ist kein bekanntes Dateikuerzel.
            if '/' not in p and not re.search(r'\.(sh|py|ts|tsx|js|mjs|md|json|php|yml|yaml|lock)$', p):
                continue
            umfeld = volltext.replace(t, '')   # der Rest des Blattes
            if re.search(rf'{re.escape(p)}', umfeld) and \
               re.search(r'erzeug|schreib|liefert|baut|legt an|Liefergegenstand|Erzeuger',
                         umfeld, re.I):
                continue
            funde.append(('P3', f'{kenn} verlangt `{p}` als vorhanden, aber kein anderer Ort '
                                f'im Blatt nennt einen Erzeuger'))


def p4(text, funde):
    """KRITERIUM GEGEN BLATTKOPF. Kein Kriterium darf einen Pfad als 'null Mal' fordern, den der
    Kopf als Liefergegenstand nennt."""
    k = kopf(text)
    liefer = ' '.join(re.findall(r'^(?:art|gebaut_in|werkzeug):.*(?:\n(?:\s{2,}).*)*', k, re.M))
    for kenn, t in kriterien(text):
        # Backticks sind optional — A-33 schrieb 'scripts/, resources/ und app/ kommen null
        # Mal vor.' ganz ohne. Wer nur die gesetzte Schreibweise sucht, findet den Fall nicht.
        for m in re.finditer(r'`?([A-Za-z0-9_./-]+/)`?[^.\n]{0,80}?null\s*Mal'
                             r'|null\s*Mal[^.\n]{0,80}?`?([A-Za-z0-9_./-]+/)`?', t):
            p = m.group(1) or m.group(2)
            if not (p and p.rstrip('/') and re.search(re.escape(p.rstrip('/')), liefer)):
                continue
            # Nennt dasselbe Kriterium fuer denselben Pfad eine ANDERE Erwartung als null
            # ("scripts/ 1"), dann ist das "null Mal" das Zitat der ueberholten Fassung und
            # nicht die geltende Forderung. Die geltende Erwartung schlaegt das Zitat.
            if re.search(rf'{re.escape(p)}\s*`?\s*[1-9]', t) and ueberholt_zitiert(t):
                continue
            funde.append(('P4', f'{kenn} fordert `{p}` null Mal, der Blattkopf nennt genau '
                                f'diesen Pfad als Liefergegenstand'))


def p5(text, funde):
    """RUECKGABEWERT DOPPELT. Zwei BEDEUTUNGEN auf einem Code, nicht zwei Nennungen (K5).

    Der Fall wird an der Code-Tabelle gemessen: | <Code> | <Bedeutung> | ... Steht derselbe Code
    zweimal mit verschiedener Bedeutung, ist es ein Fund. Zusaetzlich wird der Fliesstext gelesen:
    liegt ein Code in einer Tabelle UND traegt anderswo im Blatt eine andere Bedeutung, zaehlt das
    ebenso — genau so entstand der historische Fall (die eine Seite las nur ihren eigenen Teil).
    """
    tab = re.findall(r'^\|\s*\*{0,2}\(?(\d)\)?\*{0,2}\s*\|\s*([^|]+)\|', text, re.M)
    gesehen = {}
    for code, bedeutung in tab:
        b = re.sub(r'[*`_]', '', bedeutung).strip().lower()
        if not b or b.startswith('---'):
            continue
        if code in gesehen:
            a = gesehen[code]
            gleich = a == b or a in b or b in a
            if not gleich:
                funde.append(('P5', f'Code {code} traegt zwei Bedeutungen: "{a[:44]}" und "{b[:44]}"'))
        else:
            gesehen[code] = b
    # zweite Achse: Code in der Tabelle gegen eine abweichende Bedeutung im Fliesstext
    for code, b in gesehen.items():
        for m in re.finditer(rf'(?:exit|Rueckgabe|Rückgabe|Code)\s*\**{code}\**\s*[:—-]?\s*'
                             rf'([A-Za-zÄÖÜäöü][^.\n]{{4,60}})', text):
            t = re.sub(r'[*`_]', '', m.group(1)).strip().lower()
            if t and not (t in b or b in t) and not t.startswith('bei '):
                funde.append(('P5', f'Code {code}: Tabelle sagt "{b[:38]}", der Text sagt "{t[:38]}"'))
                break


def p6(text, funde):
    """ROT-LAGE MIT UHR. Eine Rot-Lage aus einem WANDERNDEN Fenster wird von selbst gruen, ohne
    dass jemand etwas behoben hat. Gesucht wird das Fenster, nicht jede Zeitangabe — sonst meldet
    P6 jede Messvorschrift und wird weggeklickt (A-03)."""
    # NUR DER BEFEHL, NICHT DAS WORT. Die erste Fassung fasste auch die Wendung
    # "48-Stunden-Fenster" — und meldete damit prompt A-39s EIGENES Blatt, wo sie in der
    # Beschreibung dessen steht, was P6 finden soll. Das Blatt hat diesen Fehlalarm woertlich
    # vorhergesagt ("In Worten statt als Befehl — sonst meldet P6 sein eigenes Beispiel") und
    # als Abhilfe die Wortform gewaehlt. Ein Pruefer, der die Abhilfe seines eigenen Auftrags
    # nicht respektiert, macht die Vorsichtsmassnahme wertlos.
    # Der historische Fall bleibt gefasst: A-38 @5bbc55bf traegt den --since-BEFEHL selbst.
    fenster = re.compile(r"--since=['\"]?\d+\s*(hours?|days?|Stunden|Tage)"
                         r"|--since=['\"]?\d+\s+\w+\s+ago"
                         r"|\bseit\s+gestern\b|\bin den letzten\s+\d+\s*(Stunden|Tagen)", re.I)
    for kenn, t in kriterien(text):
        if not re.search(r'\bRot\b|\bRot-Lage\b|\bRot am\b', t, re.I):
            continue
        m = fenster.search(t)
        # Traegt das Kriterium FESTE Staende, ist seine Rot-Lage verankert — dann ist das
        # wandernde Fenster daneben ein Zitat des ueberholten Belegs, kein Fund. Genau so
        # steht A-38-2 heute da: fuenf feste SHAs, und der alte --since-Befehl darunter
        # ausdruecklich als "NICHT mehr benutzen".
        if m and len(re.findall(r'`[0-9a-f]{7,40}`', t)) >= 2 and ueberholt_zitiert(t):
            continue
        if m:
            funde.append(('P6', f'{kenn} belegt seine Rot-Lage aus einem wandernden Fenster '
                                f'("{m.group(0).strip()}") — sie wird von selbst gruen'))
        elif re.search(r'\b\d+\s+von\s+\d+\b', t) and not hat_standbezug(t):
            funde.append(('P6', f'{kenn} belegt seine Rot-Lage mit einer Quote ohne festen Stand'))


def p7(text, funde):
    """KRITERIUM OHNE GANGBAREN WEG. Vier Fragen: WER · DARF er · EXISTIERT die Eigenschaft ·
    DARF ER ES NOCH, WENN DER BAU FERTIG IST.

    Was hier maschinell geht und was nicht — ausdruecklich, damit niemand mehr erwartet:
    Ob eine Rolle etwas DARF, steht in den Arbeitsregeln und nicht im Blatt; das kann ein
    Textpruefer nicht entscheiden. Er kann aber die drei Formen finden, in denen der Fehler
    historisch auftrat, und genau die sucht er:
      a) das Kriterium verlangt einen SCHREIBVORGANG an einem Dokument mit benanntem
         Alleinschreiber (docs/STATUS.md gehoert dem Integrator), ohne diesen zu nennen;
      b) das Kriterium verlangt eine COMMIT-Eigenschaft (Zeit, Autor, SHA) von einer
         DATEI-Messung — eine aus einer Datei gelesene Zeile hat keine Commit-Zeit;
      c) das Kriterium verlangt TRANSPORT/Nachziehen, und der Blattkopf nennt als Bauenden
         eine Rolle, der genau das untersagt ist.
    Es beurteilt NICHT, ob die Antwort klug ist. Ein Pruefer, der Urteile faellt, wird weggeklickt.
    """
    k = kopf(text)
    baut = (re.search(r'^(?:baut|gebaut_in):\s*"?([a-z-]+)', k, re.M) or [None, ''])[1]
    # Traegt der Kopf ein Dokument mit BENANNTEM Alleinschreiber? Dann zaehlt auch ein
    # Kriterium, das nur "die Tafel" sagt — der Kopf sagt, welche gemeint ist.
    fremdes_dokument = bool(re.search(r'^status_steht_in:\s*docs/STATUS\.md', k, re.M))
    for kenn, t in kriterien(text):
        # Der Unterschied zwischen Fund und Nicht-Fund ist EIN VERB, und er ist gemessen:
        #   a613100e  "Nach dem Lauf TRAEGT die erzeugte Tafel ... genau eine Zeile"  -> Fund
        #   heute     "Der Lauf WEIST ... genau eine Zeile AUS"                       -> kein Fund
        # Das erste verlangt einen geschriebenen Zustand an einem Dokument, das nur der
        # Integrator schreiben darf; das zweite verlangt nur eine Ausgabe. Dieselbe Zusage,
        # zwei Wege, und nur einer ist fuer den Adressaten gangbar.
        zustand_verlangt = re.search(
            r'(?:Tafel|Statuswahrheit|STATUS\.md)[^.\n]{0,80}?'
            r'(?:tr(?:ae|ä)gt|enth(?:ae|ä)lt|steht)'
            r'|(?:tr(?:ae|ä)gt|enth(?:ae|ä)lt)[^.\n]{0,40}?(?:Tafel|Statuswahrheit)', t, re.I)
        ausgabe = re.search(r'weist[^.\n]{0,60}?aus\b|gibt[^.\n]{0,40}?aus\b|meldet|Rohausgabe', t, re.I)
        nennt_stat = re.search(r'docs/STATUS\.md', t) or fremdes_dokument
        if nennt_stat and zustand_verlangt and not ausgabe and not re.search(r'Integrator', t, re.I):
            funde.append(('P7', f'{kenn} verlangt einen geschriebenen Zustand an docs/STATUS.md '
                                f'("traegt"/"enthaelt"), ohne den einzigen Schreibberechtigten '
                                f'(Integrator) zu nennen — eine Ausgabe waere gangbar, ein '
                                f'Schreibvorgang nicht'))
        if re.search(r'Commit-?(Zeit|Zeitpunkt|Datum|Autor)', t, re.I) and \
           re.search(r'git show|Datei|Zeile|gelesen|liest', t, re.I) and \
           not re.search(r'git log|%c[dI]|committer', t, re.I):
            funde.append(('P7', f'{kenn} verlangt eine Commit-Eigenschaft von einer Datei-Messung '
                                f'— eine gelesene Zeile traegt keine'))
        # (c) VERENGT nach einem eigenen Fehlalarm: die erste Fassung meldete jedes Kriterium,
        # das Transport erwaehnt und den Bauenden nicht nennt — an A-41 schlug sie zweimal
        # falsch an. Ob eine Rolle etwas DARF, steht in den Arbeitsregeln und nicht im Blatt;
        # ein Textpruefer kann es nicht entscheiden. Er meldet nur, wenn das Blatt das Verbot
        # SELBST ausspricht und trotzdem die Handlung verlangt. Findet er damit weniger, ist
        # das richtig so — ein Pruefer, der raet, wird weggeklickt (A-03).
        if re.search(r'Transport|transportier|nachziehen', t, re.I) and \
           re.search(r'untersagt|darf (es )?nicht|nicht erlaubt|verboten|nicht zustaendig', t, re.I):
            funde.append(('P7', f'{kenn} verlangt Transport (Zustand/Ball/Tafelzeile), und das '
                                f'Blatt nennt im selben Kriterium ein Verbot dafuer'))


def p8(text, funde):
    """DER ORT IST DAS KRITERIUM, NICHT DIE SACHE. Eine Messvorschrift, die einen Verzeichnispfad
    oder Dateinamen als SUCHRAUM festlegt, ist ein Fund, wenn die Sache auch anderswo liegen kann.

    Grenze, ausdruecklich: geprueft wird die SUCHVORSCHRIFT, nicht das Ergebnis. Ein an der
    richtigen Stelle gefundenes Ergebnis kann aus einer zu engen Vorschrift stammen — dann ist es
    Glueck, und Glueck ist kein Pruefverfahren. Und ein Pfad, der ausdruecklich als BEISPIEL steht,
    ist kein Fund; sonst meldet P8 jede Fundstellenangabe.
    """
    suchverb = re.compile(r'grep|ls-files|\bfind\b|such|Suchraum|z(ae|ä)hl|messen in|gemessen (in|an)',
                          re.I)
    verzeichnis = re.compile(r'`([A-Za-z0-9_./-]*/)`')
    for kenn, t in kriterien(text):
        if not suchverb.search(t):
            continue
        if re.search(r'als Beispiel|beispielhaft|z\.\s?B\.|die Sache|nicht der Ort', t, re.I):
            continue
        for p in set(verzeichnis.findall(t)):
            if p in ('/',):
                continue
            funde.append(('P8', f'{kenn} legt `{p}` als Suchraum fest — die Sache kann auch '
                                f'anderswo liegen; Sache benennen, Pfad hoechstens als Beispiel'))
            break
        else:
            if re.search(r'ls-files\s+[^\s|]+', t) and not re.search(r'\ball\b|--', t):
                funde.append(('P8', f'{kenn} misst ls-files an einem einzelnen Gegenstand und '
                                    f'spricht ueber den Baum'))


PRUEFUNGEN = [p1, p2, p3, p4, p5, p6, p7, p8]


def blatt_pruefen(text, name):
    if re.search(r'^zustand_dieses_pfades:\s*STILLGELEGT', kopf(text), re.M):
        return None                                   # K6: Stilllegungs-Wegweiser
    funde = []
    for f in PRUEFUNGEN:
        try:
            f(text, funde)
        except Exception as e:
            funde.append(('!!', f'{f.__name__} ist gescheitert: {type(e).__name__}: {e} '
                                f'— das ist ein Fehler DES PRUEFERS, kein Blattfehler'))
    return funde


def main():
    if len(sys.argv) < 2:
        print('Aufruf: scripts/blatt-pruefen.sh <pfad-zum-blatt> [weitere ...]', file=sys.stderr)
        return 2
    gesamt = 0
    geprueft = uebersprungen = 0
    for pfad in sys.argv[1:]:
        try:
            text = io.open(pfad, encoding='utf-8').read()
        except OSError as e:
            print(f'{pfad}: nicht lesbar — {e}', file=sys.stderr)
            gesamt += 1
            continue
        funde = blatt_pruefen(text, pfad)
        if funde is None:
            uebersprungen += 1
            continue
        geprueft += 1
        for kenn, satz in funde:
            print(f'{pfad}: {kenn}  {satz}')
        gesamt += len(funde)
    if len(sys.argv) > 2:
        print(f'— {geprueft} Blaetter geprueft, {uebersprungen} uebersprungen (stillgelegt), '
              f'{gesamt} Funde', file=sys.stderr)
    return 1 if gesamt else 0


if __name__ == '__main__':
    sys.exit(main())
