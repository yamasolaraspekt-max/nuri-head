"""Blockkontrolle fuer docs/STATUS.md — Zaunbilanz UND Parsbarkeit in einem Lauf.

GEBAUT 15.08. nach einem Fehler, den ich zweimal binnen Minuten gemacht habe:

  1  Ich schrieb einen mehrzeiligen Wert als  feld: "text"  und haengte danach weitere
     eingerueckte Zeilen an. Nach dem schliessenden Anfuehrungszeichen ist das kein
     gueltiges YAML. Der Planner musste es reparieren.
  2  Im BEFUND UEBER diesen Fehler zitierte ich den Regex des Tores woertlich — samt
     seiner drei Zaunzeichen. Damit war mein eigener Block mitten im Text geschlossen.

Die alte Zaunbilanz hat BEIDES nicht gesehen, und der Grund ist derselbe:
sie zaehlte nur Zeilen, die MIT einem Zaun BEGINNEN. Eine gerade Zahl hiess "sauber",
obwohl ein Zaun mitten in einer Zeile stand und obwohl Bloecke nicht parsten.

Auch das Tor sieht es nicht: commit-pruefen.sh liest den yaml-Kopf mit t.match(...) OHNE
g-Flag, also genau EINEN Block je Datei. Bei 301 Bloecken sind das 0,3 Prozent.

Dieses Muster prueft daher drei Dinge, nicht eins:
  A  Zaunbilanz ueber ZEILENANFAENGE  (die alte Kontrolle, sie bleibt)
  B  Zaunzeichen MITTEN in einer Zeile innerhalb eines yaml-Blocks  (neu)
  C  Parsbarkeit JEDES Blocks, nicht nur des ersten  (neu)

C meldet eine Grundlinie statt eines Urteils: 25 Bloecke sind seit dem 14.08. kaputt
(Altbestand, stabil). Gemeldet wird, was ueber diese Grundlinie hinausgeht — sonst
verschwindet ein neuer Fehler in einer Zahl, die ohnehin nie null ist.
"""
import re
import subprocess
import sys

P = 'docs/STATUS.md'
# NACHGEZOGEN 16.08.: 25 -> 24. Der Planner hat einen der Altbestands-Bloecke repariert
# (A-33s Datensatz). Eine Grundlinie, die nur nach oben gepflegt wird, verdeckt spaeter genau
# die Verschlechterung, die sie melden soll — kaputte Bloecke duerfen schrumpfen, nie wachsen,
# und die Messlatte muss mitschrumpfen. Gemessen nach dem Merge: 302 parsen, 24 kaputt.
GRUNDLINIE = 24

# AUCH B BRAUCHT EINE GRUNDLINIE, und der Grund ist meine eigene Lehre vom 13.08.:
# "Eine Warnung, die immer kaeme, waere so wertlos wie keine." Beim ersten Lauf meldete B
# SIEBEN Altbestands-Treffer und damit Exit 1 — bei jedem Lauf, fuer immer. Bemerkenswert
# ist, WAS dort steht: mehrere der sieben sind selbst Befunde ueber genau dieses Problem
# (Z.12311, Z.12334, Z.12383 "die_falle_im_pruefmuster"). Wer ueber Zaunzeichen schreibt,
# benutzt Zaunzeichen — das Problem ist rekursiv, und darum darf die Kontrolle nur den
# ZUWACHS melden, nicht den Bestand.
# NACHGEZOGEN 16.08. 16:4x: 7 -> 8. Der Zuwachs wurde GEOEFFNET, nicht geglaubt: Z.10308 ist
# wieder ein Befundtext UEBER die Zaunlogik ("Erst die Abgrenzung am ```-Zaun statt an der
# Ueberschrift trennt sauber") — derselbe rekursive Fall wie die sieben davor. Dass er harmlos
# ist, steht nicht in meinem Urteil, sondern in Pruefung C: kaputte Bloecke unveraendert 24 auf
# der Grundlinie. Haette der Zaun einen Block geschlossen, waere C gestiegen.
# Anders als bei C darf diese Grundlinie WACHSEN — C misst Schaden (der nur schrumpfen darf),
# B misst Erwaehnungen, und ueber ein Problem zu schreiben vermehrt seine Erwaehnungen. Bedingung
# bleibt: jeder Zuwachs wird einzeln geoeffnet, bevor die Latte steigt.
# NACHGEZOGEN 16.08. 20:4x: 8 -> 10. Beide Zuwaechse GEOEFFNET, nicht geglaubt: Z.26553 und
# Z.27401 sind wieder Befundtexte UEBER die Zaunlogik (einer zitiert sogar einen Regex mit
# ```yaml darin). Derselbe rekursive Fall wie die acht davor. Dass sie harmlos sind, steht
# nicht in meinem Urteil, sondern in Pruefung C: kaputte Bloecke unveraendert 24.
GRUNDLINIE_B = 10        # Stand 16.08. 20:4x, je Zeile einzeln geoeffnet
ZAUN = '`' * 3


def bloecke(text):
    return [m.group(1) for m in re.finditer(ZAUN + r'yaml\n([\s\S]*?)' + ZAUN, text)]


def main(pfad=P):
    text = open(pfad).read()
    zeilen = text.split('\n')

    # A — Zaunbilanz ueber Zeilenanfaenge
    a = sum(1 for l in zeilen if l.strip().startswith(ZAUN))
    print(f'  A  Zaunbilanz {a} · {"gerade" if a % 2 == 0 else "UNGERADE — ABBRUCH"}')

    # B — Zaun mitten in einer Zeile. NUR innerhalb eines yaml-Blocks ist das schaedlich;
    #     im Fliesstext ist ein Zaun in einer Zeile voellig normal.
    b = []
    drin = False
    for i, l in enumerate(zeilen, 1):
        s = l.strip()
        if s.startswith(ZAUN):
            drin = s.startswith(ZAUN + 'yaml')
            continue
        if drin and ZAUN in l:
            b.append((i, l.strip()[:72]))
    ueber_b = len(b) - GRUNDLINIE_B
    print(f'  B  Zaun mitten in einer Zeile: {len(b)} '
          f'(Grundlinie {GRUNDLINIE_B}, {"+" if ueber_b > 0 else ""}{ueber_b})')
    if ueber_b > 0:
        # ALLE auflisten, nicht "die letzten N". Die erste Fassung nahm die hoechsten
        # Zeilennummern und zeigte damit auf die falsche Stelle: im Gegenprobe-Lauf lag der
        # eingebaute Fehler bei Z.1484, gemeldet wurde Z.12385. Welcher Treffer neu ist,
        # weiss dieses Muster nicht — also behauptet es das auch nicht.
        print(f'     ⚠ {ueber_b} ueber der Grundlinie. Welche neu sind, sagt die Liste NICHT '
              f'— alle {len(b)} Fundstellen, die neue ist darunter:')
        for i, l in sorted(b):
            print(f'        Z.{i}  {l}')

    # C — Parsbarkeit ALLER Bloecke
    try:
        import yaml as _y
    except ImportError:
        r = subprocess.run(
            ['node', '-e',
             'const y=require("js-yaml"),f=require("fs");'
             'const t=f.readFileSync(process.argv[1],"utf8");let ok=0,bad=0;'
             'for(const m of t.matchAll(/```yaml\\n([\\s\\S]*?)```/g)){try{y.load(m[1]);ok++}catch(e){bad++}}'
             'console.log(ok+" "+bad);', pfad],
            capture_output=True, text=True)
        if r.returncode != 0:
            # UNGEPRUEFT ist kein BEFUND. Nachgezogen 16.08. 23:2x auf einen Fund des
            # Plan-Pruefers (er mass es an bloecke.py, Ball ausdruecklich bei mir):
            # "ein Pruefer, der aus Umgebungsgruenden schweigt, ist von einem gruenen nicht
            # zu unterscheiden, wenn man nur auf die Zeilen schaut."
            #
            # Die alte Fassung gab hier `return 1` — denselben Wert wie eine echte
            # Verschlechterung. Beim Nachmessen kam ZWEIERLEI heraus, und das zweite ist
            # schwerer als das gemeldete: der vorzeitige return uebersprang PRUEFUNG D,
            # also genau die Kontrolle auf abwesende Datensaetze.
            #
            #   Lauf ohne erreichbares js-yaml, VORHER:  A · B · "C konnte nicht" · exit 1
            #                                            D lief NICHT
            #   Lauf mit js-yaml:                        A · B · C · D · exit 0
            #
            # Jetzt: C meldet UNGEPRUEFT, D laeuft weiter, und der Rueckgabewert ist 2 —
            # unterscheidbar von 0 (alles auf Grundlinie) und 1 (Befund).
            # Den GRUND aus stderr holen, nicht die letzte Zeile: node haengt seine
            # Versionsnummer ans Ende, und "Grund: Node.js v26.5.0" ist keine Auskunft.
            # Selbst bemerkt beim Gegenpruefen, bevor der Text in den Bestand ging.
            _z = [l.strip() for l in r.stderr.splitlines() if l.strip()]
            _grund = next((l for l in _z if 'Error' in l or 'Cannot find' in l),
                          _z[0] if _z else 'node lieferte keinen Grund')
            print(f'  C  UNGEPRUEFT — kein Befund, sondern eine fehlende Voraussetzung.')
            print(f'     Grund: {_grund[:72]}')
            print(f'     Abhilfe: aus dem Repo-Verzeichnis fahren (js-yaml liegt in node_modules)')
            print(f'              oder NODE_PATH auf ein Verzeichnis mit js-yaml setzen.')
            ok = bad = None
        else:
            ok, bad = (int(x) for x in r.stdout.split())
    else:
        ok = bad = 0
        for blk in bloecke(text):
            try:
                _y.safe_load(blk)
                ok += 1
            except Exception:
                bad += 1

    if bad is None:
        ueber = 0                      # ungeprueft zaehlt NICHT als Befund
        c_ungeprueft = True
    else:
        c_ungeprueft = False
        ueber = bad - GRUNDLINIE
        print(f'  C  Bloecke {ok + bad} · parsen {ok} · kaputt {bad} '
              f'(Grundlinie {GRUNDLINIE}, {"+" if ueber > 0 else ""}{ueber})')
        if ueber > 0:
            print(f'     ⚠ {ueber} MEHR als der Altbestand — das ist neu und gehoert geoeffnet.')

    # D — OEFFNER OHNE SCHLIESSER. Nachgeruestet 16.08. 23:1x, weil A, B und C denselben
    # Fall alle drei nicht sehen und dabei "still" melden:
    #
    #   ```yaml-Oeffner in der Datei   444
    #   Bloecke, die C ueberhaupt sieht 442
    #   Differenz                         2
    #
    # Ein Block ohne schliessenden Zaun ist nicht KAPUTT, er ist ABWESEND — das Muster
    # /```yaml\n([\s\S]*?)```/ findet ihn nicht, also kann C ihn auch nicht als kaputt
    # zaehlen. A bleibt still, weil sie nur die GESAMTZAHL aller Zaunzeilen auf Geradheit
    # prueft (1160) und ein fehlender Schliesser von den Schliessern der ```bash-Bloecke
    # rechnerisch ausgeglichen wird. B bleibt still, weil der Zaun am Zeilenanfang steht.
    #
    # Gefunden ueber einen Nebenweg: die Zustandszaehlung ergab 90 ueber grep gegen 89 ueber
    # Bloecke. EINE Zeile Unterschied — und dahinter lag der Datensatz A-08 (Z.3215-3256),
    # fuer jedes blockbasierte Werkzeug unsichtbar. Heute ohne Schaden (BETRIEBSBESTAETIGT,
    # ballbesitz —), aber derselbe Mechanismus haette einen offenen Ball verschluckt.
    # GRUNDLINIE_D ist eine MESSLATTE, keine Erledigung. Der Plan-Pruefer notiert es
    # ausdruecklich, und er hat recht: "Grundlinie 2, 0" heisst, der Waechter schuetzt ab
    # sofort gegen einen DRITTEN — die zwei bestehenden behebt er nicht. Sie liegen beim
    # Integrator, denn wer einen Zaun setzt, entscheidet wo der Block endet.
    GRUNDLINIE_D = 2        # Stand 16.08. 23:1x: A-08 (Z.3215) und ein Vorschlagsblock (Z.7876)
    offen_d, start_d = [], None
    _zeilen = text.split('\n')
    for i, l in enumerate(_zeilen):
        s = l.strip()
        if s.startswith('```yaml'):
            if start_d is not None:
                # BEREICHSENDE: i ist der 0-basierte Index des NAECHSTEN Oeffners, seine
                # 1-basierte Zeilennummer waere i+1 — und die gehoert schon zum naechsten
                # Block. Der offene Block endet eine Zeile davor, also bei i.
                # Berichtigt 16.08. 23:2x: ich hatte i+1 ausgegeben und damit fuer A-08
                # "bis Z.3256" gemeldet, wo in Z.3256 der ```yaml von A-09 steht. Der
                # Plan-Pruefer mass 3255 und nannte es einen Zeichenunterschied ohne Belang
                # — es ist aber MEINE Zeilenangabe, und sie fuehrt auf den falschen Block.
                offen_d.append((start_d + 1, i))
            start_d = i
        elif s == '```' and start_d is not None:
            start_d = None
    if start_d is not None:
        # Dateiende: split('\n') liefert bei abschliessendem Zeilenumbruch ein leeres
        # letztes Element, das keine Zeile ist. Ohne diesen Abzug meldet D bei einer
        # Datei mit vier Zeilen "bis Z.5". Gefunden von der Gegenprobe zum Bereichsende
        # oben — derselbe Off-by-one am anderen Ende, und ich haette ihn ohne die Probe
        # nicht gesehen, weil der Fall im echten Bestand nicht vorkommt.
        _letzte = len(_zeilen) - (1 if _zeilen and _zeilen[-1] == '' else 0)
        offen_d.append((start_d + 1, _letzte))
    ueber_d = len(offen_d) - GRUNDLINIE_D
    print(f'  D  Oeffner ohne Schliesser {len(offen_d)} '
          f'(Grundlinie {GRUNDLINIE_D}, {"+" if ueber_d > 0 else ""}{ueber_d})')
    for a_d, b_d in offen_d:
        print(f'       Z.{a_d} bis Z.{b_d} — von A, B und C nicht gesehen')

    # D2 — WELCHER DATENSATZ GEHT DABEI VERLOREN. Nachgetragen 17.08. 01:4x auf den
    # A-42-DoR-Befund des Plan-Pruefers, und seine Praezisierung ist der eigentliche Punkt:
    #
    #   "ein kaputter Block macht nicht SICH SELBST unsichtbar, sondern den FOLGENDEN"
    #
    # D oben meldet, WO der Zaun fehlt. Das ist die Ursache. Verloren geht aber der Block
    # DANACH: die Vorschrift /```yaml\n([\s\S]*?)```/ paart den ungeschlossenen Oeffner mit
    # dem naechsten Schliesser und frisst alles dazwischen — darunter einen vollstaendigen,
    # voellig intakten Datensatz.
    #
    # Sein Befund ist aelter als mein D (16.08. 19:29 gegen 17.08. 23:1x). Ich hatte die
    # Ursache benannt (abwesend statt kaputt), er die FOLGE (A-18 verschluckt). Beides
    # zusammen ist der ganze Fall, und diese Zeilen sind seine Haelfte.
    #
    # Gegenprobe, die er vorschlaegt und die hier laeuft: auftrag-Zeilen im VOLLTEXT gegen
    # die in erfassten Bloecken. Gemessen 17.08.: 258 gegen 257, Differenz 1, es ist A-18
    # in Z.7891 — der Block direkt hinter dem ungeschlossenen Zaun von Z.7876.
    #
    # WARUM A-42-2s SUMMENPROBE DAS NICHT FAENGT (auch seine Messung): A-18 bliebe beim
    # Umzug in STATUS.md liegen und wuerde auf der Nachher-Seite mitgezaehlt. Die Gleichung
    # geht auf, der Verlust ist unsichtbar. Deshalb muss die Differenzprobe eine ANDERE
    # Groesse messen als die Summenprobe.
    _txt = '\n'.join(_zeilen)
    _bl = re.findall(r'```yaml\n([\s\S]*?)```', _txt)
    _drin = set()
    for _b in _bl:
        for _m in re.finditer(r'^auftrag: (.+)$', _b, re.M):
            _drin.add(_m.group(1).strip())
    _alle = [(i + 1, l.split(':', 1)[1].strip())
             for i, l in enumerate(_zeilen) if l.startswith('auftrag:')]
    _weg = [(ln, k) for ln, k in _alle if k not in _drin]
    print(f'  D2 auftrag-Zeilen im Volltext {len(_alle)} · in Bloecken erfasst '
          f'{len(_alle) - len(_weg)} · verschluckt {len(_weg)}')
    for ln, k in _weg:
        print(f'       Z.{ln} {k} — intakter Datensatz, von der Vorschrift nicht gesehen')
    if len(_weg) > 1:
        print(f'     ⚠ mehr als der bekannte eine Fall — das ist neu.')
    # WARUM D2 EINE EIGENE PRUEFUNG IST UND NICHT EINE ZEILE IN D — und die Regel dazu ist
    # BELEGT, nicht vermutet, weil zwei Gegenproben VERSCHIEDEN ausfielen:
    #
    #   meine  (Schliesser bei Z.1163 entfernt):  D 2 -> 3,  D2 bleibt 1
    #   seine  (Schliesser bei Z.9137 entfernt):  D 2 -> 3,  D2 steigt auf 2
    #                                             unsichtbar dann A-18 UND W-38
    #
    # Beide nachgestellt, seine an einer Kopie im Kratzverzeichnis nachgefahren und
    # zeichengenau bestaetigt. Kein Widerspruch — die Folge haengt an der STELLE:
    #
    #   Ein fehlender Schliesser verschluckt den NAECHSTEN Datensatz, wenn zwischen ihm und
    #   dem naechsten nackten ``` ueberhaupt einer steht. Steht dort keiner, verschmelzen
    #   zwei Zaeune und nichts geht verloren.   (Formulierung des Plan-Pruefers, 17.08. 01:50)
    #
    # Bei Z.7876 folgt A-18s Block, bei Z.9137 folgt W-38s Block — beide werden verschluckt.
    # Bei Z.1163 folgt keiner, deshalb blieb D2 dort stehen.
    #
    # DARAUS: D zaehlt die URSACHEN (fehlende Schliesser), D2 misst die FOLGE (verschluckte
    # Datensaetze). Haette nur ich gemessen, haette ich geschlossen "D2 folgt D nicht";
    # haette nur er gemessen, "D2 folgt D". Erst beide Versuche zusammen zeigen, dass eine
    # Zahl die andere nicht ersetzen kann.
    #
    # ZEILENKONVENTION, damit niemand zwei Angaben fuer einen Widerspruch haelt: D2 nennt die
    # `auftrag:`-Zeile (A-18 -> Z.7891), der Plan-Pruefer nennt den Blockoeffner (Z.7890).
    # Beide richtig, geoeffnet und geprueft.
    if ueber_d > 0:
        print(f'     ⚠ {ueber_d} MEHR als der Altbestand — ein Datensatz koennte '
              f'unsichtbar geworden sein.')

    # Rueckgabewerte, seit 16.08. 23:2x DREI statt zwei:
    #   0  alle vier gefahren, alle auf Grundlinie
    #   1  BEFUND — eine Pruefung meldet eine Verschlechterung
    #   2  UNGEPRUEFT — eine Pruefung konnte nicht gefahren werden (Umgebung, nicht Bestand)
    # Ein Befund schlaegt "ungeprueft", denn er ist die staerkere Aussage.
    if a % 2 or ueber_b > 0 or ueber > 0 or ueber_d > 0:
        return 1
    return 2 if c_ungeprueft else 0


if __name__ == '__main__':
    sys.exit(main(sys.argv[1] if len(sys.argv) > 1 else P))
