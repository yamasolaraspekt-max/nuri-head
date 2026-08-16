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
            print('  C  konnte nicht geprueft werden:', r.stderr.strip()[:80])
            return 1
        ok, bad = (int(x) for x in r.stdout.split())
    else:
        ok = bad = 0
        for blk in bloecke(text):
            try:
                _y.safe_load(blk)
                ok += 1
            except Exception:
                bad += 1

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
    GRUNDLINIE_D = 2        # Stand 16.08. 23:1x: A-08 (Z.3215) und ein Vorschlagsblock (Z.7876)
    offen_d, start_d = [], None
    for i, l in enumerate(text.split('\n')):
        s = l.strip()
        if s.startswith('```yaml'):
            if start_d is not None:
                offen_d.append((start_d + 1, i + 1))
            start_d = i
        elif s == '```' and start_d is not None:
            start_d = None
    if start_d is not None:
        offen_d.append((start_d + 1, len(text.split('\n'))))
    ueber_d = len(offen_d) - GRUNDLINIE_D
    print(f'  D  Oeffner ohne Schliesser {len(offen_d)} '
          f'(Grundlinie {GRUNDLINIE_D}, {"+" if ueber_d > 0 else ""}{ueber_d})')
    for a_d, b_d in offen_d:
        print(f'       Z.{a_d} bis Z.{b_d} — von A, B und C nicht gesehen')
    if ueber_d > 0:
        print(f'     ⚠ {ueber_d} MEHR als der Altbestand — ein Datensatz koennte '
              f'unsichtbar geworden sein.')

    return 1 if (a % 2 or ueber_b > 0 or ueber > 0 or ueber_d > 0) else 0


if __name__ == '__main__':
    sys.exit(main(sys.argv[1] if len(sys.argv) > 1 else P))
