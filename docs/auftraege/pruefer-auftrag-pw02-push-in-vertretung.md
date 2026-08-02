# PW-02 — Der Prüfer trägt EINMAL hinaus, in Yamas Vertretung

**Spur B** · **Heimat: ticket** · *Geschnitten 02.08. 13:0x*

```yaml
auftrag:
  id: PW-02
  strang: werkzeuge
  rolle: pruefer
  status: bereit   # AUSNAHME zu B8, benannt statt umgangen - siehe "Warum bereit"
  gegengelesen_von: yama
  gegengelesen_am: "02.08.2026"
  befund: |
    Die Freigabe kommt von Yama selbst und im Wortlaut: "ich bin aber nicht in der lage zu
    pushen bitte gib diese aufgabe den prüfer er soll nur in diesem fall mich vertreten und
    die aufgabe übernehmen". Tor 2 gehoert Yama - er hat es fuer diesen einen Fall geoeffnet.
```

## Warum `bereit` und nicht `entwurf`

**B8 verlangt Gegenlesen durch eine andere Rolle, bevor ein Blatt `bereit` wird. Hier ist die
andere Rolle Yama selbst** — die Instanz, die über dem Regelwerk steht und der Tor 2 gehört.
*Ein Gegenlese-Zyklus zwischen Planner und Evaluator würde eine Freigabe prüfen, die keiner von
beiden erteilen kann.*

**Das ist eine benannte Ausnahme, keine Umgehung.** Sie ist auf diesen einen Vorgang begrenzt und
**erlischt mit dem Vollzug** (K-08). Das nächste Mal gilt wieder B8 — oder PW-01, wenn es gebaut
ist.

## Was der Prüfer übernimmt, und was ausdrücklich NICHT

**Die Trennung stammt aus PW-01 und ist der ganze Grund, warum das hier überhaupt geht:**

| | | Rückweg | Wer |
|---|---|---|---|
| **Sicherungs-Push** | eigener Arbeitszweig → `fork`, additiv, Fast-Forward | keiner nötig — es entsteht nur eine Kopie | **Prüfer, dieses eine Mal** |
| **Veröffentlichung** | `main`, Tags, alles Richtung `upstream`, jedes `--force`, jedes Löschen | **keiner** | **Yama allein, Tor 2 bleibt zu** |

```text
ERLAUBT      git push fork auto/hausplaner-integration
VERBOTEN     upstream · main · --force · --delete · Tags · jeder andere Zweig
```

*Ein Sicherungs-Push kann nichts kaputt machen, was nicht schon lokal ist. Deshalb — und nur
deshalb — darf er delegiert werden.*

## TEIL 0 ZUERST: dürfen ist nicht können

**Am 01.08. haben sich drei Zuordnungen geirrt, weil niemand gefragt hat, ob er die Fähigkeit
überhaupt hat.** Ich, der Planner, habe die Erlaubnis heute bekommen und **konnte trotzdem nicht**
— HTTP 403 vom Proxy, zweimal gemessen.

**Der Prüfer misst deshalb ZUERST, ob sein Kanal offen ist, und pusht erst danach.** Ist er zu,
ist das kein Fehlschlag des Auftrags, sondern **sein Ergebnis** — und zugleich die Antwort auf
P-01 Teil 0, auf die alle warten.

## Kriterien

```yaml
scope:
  dateien: []
  population_command: "git --no-optional-locks rev-list --count fork/auto/hausplaner-integration..auto/hausplaner-integration"
  ausschluesse:
    - stelle: "upstream, main, Tags, --force"
      grund: "Tor 2. Gehoert Yama, auch in dieser Vertretung."
      entschieden_von: yama
    - stelle: "backup-private"
      grund: "PW-01 nennt es als zweites Ziel. Hier NICHT - eine Vertretung tut genau eine Sache."
      entschieden_von: planner

kriterien:
  - id: K-00
    typ: presence
    kritikalitaet: P1
    aussage: "TEIL 0 - Der Kanal des Pruefers ist offen. Gemessen, BEVOR irgendetwas hinausgeht."
    ausgefuehrt_von: pruefer
    pruefung:
      typ: verfahren
      schritte: |
        timeout 25 git --no-optional-locks ls-remote --exit-code fork HEAD
        Der Exitcode wird OHNE Pipe gelesen - `| tail` und `| grep` schlucken ihn (Falle 11).
          0            der Kanal ist offen  -> weiter mit K-01
          sonst        der Kanal ist zu     -> HIER ENDET DER AUFTRAG, mit der Meldung,
                       nicht mit einem Versuch. Der Planner hat an derselben Stelle
                       HTTP 403 gemessen; ein zweiter Beleg aus einer anderen Umgebung
                       ist das wertvollste Ergebnis, das dieses Blatt haben kann.
        In BEIDEN Faellen in den Ledger: welche Umgebung, welcher Exitcode, welche Uhrzeit.
      erwartet: "Exitcode benannt, nicht geraten"

  - id: K-01
    typ: presence
    kritikalitaet: P1
    aussage: "Der Zweig laeuft vor, nicht auseinander - Fast-Forward ist moeglich."
    pruefung:
      befehl: "git --no-optional-locks rev-list --count auto/hausplaner-integration..fork/auto/hausplaner-integration"
      erwartet: "0"
    ausgangswert: "0 (gemessen 02.08. 13:0x; Partner: die Gegenrichtung -> 6, die Messung ist nicht leer)"
    gegenbeweis: |
      Steht hier etwas anderes als 0, ist der Zweig auseinandergelaufen. Dann waere der Push
      entweder abgelehnt oder er braeuchte `--force` - und `--force` ist Tor 2. In dem Fall
      endet der Auftrag mit einer Meldung an Yama, nicht mit einem Ausweg.

  - id: K-02
    typ: absence
    kritikalitaet: P1
    aussage: "Kein Schluessel, kein .env, kein Geheimnis im Ausgang."
    pruefung:
      befehl: "git --no-optional-locks diff --name-only fork/auto/hausplaner-integration..auto/hausplaner-integration | grep -i 'env\\|pem\\|id_rsa\\|secret' | wc -l"
      erwartet: "0"
    ausgangswert: "0 (4 Dateien im Ausgang, alle .md - gemessen 02.08. 13:0x)"
    gegenbeweis: |
      Was einmal draussen ist, ist draussen. Ein Schluessel im Ausgang ist der einzige Fall,
      in dem ein Sicherungs-Push doch unumkehrbar waere - und damit kein Sicherungs-Push mehr.

  - id: K-03
    typ: presence
    kritikalitaet: P1
    aussage: "Es geht ueberhaupt etwas hinaus - der Auftrag laeuft nicht ins Leere."
    pruefung:
      befehl: "git --no-optional-locks rev-list --count fork/auto/hausplaner-integration..auto/hausplaner-integration"
      erwartet: "mindestens 1"
    ausgangswert: "6 (gemessen 02.08. 13:0x)"

  - id: K-04
    typ: behavioural
    kritikalitaet: P1
    aussage: "DER VORGANG - genau ein Ziel, genau ein Zweig, kein Schalter."
    ausgefuehrt_von: pruefer
    pruefung:
      typ: verfahren
      schritte: |
        Der Befehl steht hier als TEXT und ausdruecklich NICHT unter `befehl:` - ein Blatt ist
        eine Datei, die ein Werkzeug ausfuehrt, und was unter `befehl:` steht, PASSIERT.
        Das ist die Lehre vom 01.08.

            cd <Wurzel des Arbeitsbaums>
            git push fork auto/hausplaner-integration

        Keine Abkuerzung, kein Wrapper, kein Skript mit unbekanntem Inhalt. Kein `-u`,
        kein `--all`, kein `--tags`, kein `--force`, kein zweites Ziel.
        Schlaegt er fehl: die Fehlermeldung WOERTLICH in den Ledger, kein zweiter Versuch
        mit anderen Schaltern.
      erwartet: "ein Befehl, ein Ziel, ein Zweig"

  - id: K-05
    typ: behavioural
    kritikalitaet: P1
    aussage: "NACHGEMESSEN AN DER QUELLE - nicht am lokalen Zeiger."
    ausgefuehrt_von: pruefer
    pruefung:
      typ: verfahren
      schritte: |
        Der lokale `fork/…`-Zeiger sagt nur, was diese Maschine zuletzt gesehen hat. Am 01.08.
        hat genau dieser Unterschied drei falsche Zuordnungen erzeugt.
            git ls-remote https://github.com/yamasolaraspekt-max/nuri-head.git refs/heads/auto/hausplaner-integration
        Der Wert MUSS der lokale HEAD von auto/hausplaner-integration sein.
        Vorher stand dort `aebe57b6` (Planner, 02.08. 12:5x, aus dem Cloud-Sandkasten gemessen).
      erwartet: "der Wert an der Quelle ist der lokale HEAD, beide Zahlen im Ledger"

  - id: K-06
    typ: behavioural
    kritikalitaet: P1
    aussage: "Nichts ausser diesem Zweig hat sich bewegt."
    ausgefuehrt_von: pruefer
    pruefung:
      typ: verfahren
      schritte: |
        Nach dem Push:
          git ls-remote <url> refs/heads/main   -> unveraendert gegenueber vorher
          git ls-remote <url> refs/tags/*       -> unveraendert gegenueber vorher
        Beide Werte VOR dem Push notieren, sonst ist "unveraendert" eine Behauptung.
      erwartet: "main und Tags vorher/nachher gleich, mit Zahlen belegt"

  - id: K-07
    typ: behavioural
    aussage: "Der Bericht ordnet nichts zu, was er nicht gemessen hat."
    ausgefuehrt_von: pruefer
    pruefung:
      typ: verfahren
      schritte: |
        Der Reflog zeigt am 02.08. vier fremde `update by push` (09:15:10, 09:23:46, 10:28:26,
        12:12:28). Aus WELCHER Umgebung, weiss niemand.
        Der Pruefer schreibt in den Ledger, was SEIN Vorgang bewirkt hat - und laesst die
        anderen vier ausdruecklich offen, statt sie sich oder jemandem zuzuschreiben.
        Das ist keine Hoeflichkeit, sondern der Fehler vom 01.08., dreimal gemacht.
      erwartet: "eigener Vorgang belegt, fremde Pushes offen benannt"

  - id: K-08
    typ: presence
    kritikalitaet: P1
    aussage: "Die Vertretung erlischt mit dem Vollzug."
    ausgefuehrt_von: pruefer
    pruefung:
      typ: verfahren
      schritte: |
        Nach dem Push setzt der Pruefer dieses Blatt auf `status: abgenommen` und traegt
        die Uhrzeit ein. Die Erlaubnis gilt fuer DIESEN Vorgang, nicht fuer den naechsten.
        Ein zweiter Push braucht eine zweite Freigabe von Yama - oder PW-01, gebaut.
      erwartet: "status abgenommen, Uhrzeit eingetragen"

  - id: L-01
    typ: presence
    aussage: "KEIN Browsertest - hier wird nichts gezeichnet."
    pruefung:
      typ: verfahren
      schritte: |
        Ausdruecklich benannt statt weggelassen. Der Beleg ist K-05 an der Quelle.
```

## Kantenliste — jede Zeile mit Zusage oder Grund (B9)

```text
1  Der Pruefer kann gar nicht pushen und versucht es trotzdem.         -> K-00
2  Der Zweig ist auseinandergelaufen, `--force` scheint der Ausweg.    -> K-01, und das ist Tor 2
3  Ein Schluessel liegt im Ausgang.                                    -> K-02
4  `git push` ohne Ziel schiebt nach der Voreinstellung irgendwohin.   -> K-04, Ziel und Zweig genannt
5  Der lokale Zeiger sagt "gepusht", die Quelle sagt etwas anderes.    -> K-05
6  Ein Tag oder `main` wandert unbemerkt mit.                          -> K-06
7  Der fremde Push von 12:12:28 wird dem eigenen Vorgang zugeschlagen. -> K-07
8  Die Vertretung wird zur Dauererlaubnis.                             -> K-08
9  Der Push gelingt, aber der Pruefer meldet ihn nicht.
   OHNE ZUSAGE, mit Grund: dagegen hilft keine Zusage in diesem Blatt, sondern nur die
   Messung von aussen - und die kann jeder jederzeit fahren (K-05, eine Zeile, ohne
   Zugangsdaten). Das ist die Barriere, nicht der Vorsatz.
```

## Was hinausgeht — gemessen 02.08. 13:0x

```text
6 Commits voraus, 0 zurueck, 4 Dateien, alle .md, 0 Schluessel

  1873778c  Falle nachgetragen - die Cloud-Shell behaelt ihr Arbeitsverzeichnis
  e3b444ba  Push mit Erlaubnis versucht und gemessen gescheitert
  d255a917  Befund S-10
  d82c2821  F-18 und F-19 als Klassen
  d1cecdcf  Gegenlese-Verteilung nach B8
  6ea45e05  W-08 geschnitten
```

**Dazu kommen die Commits, die dieses Blatt selbst erzeugt** — der Auftrag und der Ledger-Eintrag.
*Die Zahl 6 ist auf 13:0x gemessen und altert, während dieses Blatt geschrieben wird; K-03 fragt
deshalb nach „mindestens 1" und nicht nach einer festen Zahl.* **Der Prüfer misst zum Zeitpunkt
seines Vorgangs neu — das ist K-03, und dafür steht es da.**

**Kein Code, kein Schema, keine Migration — nur Blätter und Papiere.** *Der harmloseste
Ausgang, den dieser Zweig je hatte, und ein guter erster Fall für eine Vertretung.*
