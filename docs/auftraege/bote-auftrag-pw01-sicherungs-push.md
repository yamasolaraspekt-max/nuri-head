# PW-01 — Der Bote: eine vierte Rolle, die hinausträgt und vorher prüft

**Spur A** · **Heimat: ticket** · *Entworfen 01.08. 22:5x, auf Yamas Frage: „warum muss ich das machen"*

```yaml
auftrag:
  id: PW-01
  strang: werkzeuge
  status: entwurf   # B8: ohne Gegenlesen wird kein Blatt `bereit`. Dies ist die ERSTE Anwendung
  rolle: bote
  gegengelesen_von:
  gegengelesen_am:
  befund:
```

## Yamas Frage ist berechtigt, und die Antwort lautet: es geht — und es macht das System sicherer

**Der heutige Zustand ist nicht sicher, sondern nur unbequem.** „Niemand pusht außer Yama" ist eine
**Regel (Stufe 3)**. Sie hat heute zweimal nicht gehalten, und ihr Preis war messbar: **55 Commits
lagen ohne Kopie außerhalb der Maschine**, weil der einzige zulässige Weg an Yamas Anwesenheit hing.

**Ein Bote dreht das um.** Wenn genau eine Umgebung den Push-Kanal hat und alle anderen keinen,
wird aus *„niemand darf"* ein *„niemand kann außer einem"* — **Stufe 5 statt Stufe 3.** Das ist der
Kern des Beschlusses vom 01.08., angewandt auf die einzige Fähigkeit, die die Maschine verlässt.

## Die Trennung, die den Knoten löst

**Die harte Regel wirft zwei Dinge zusammen, die nichts miteinander zu tun haben:**

| | Was es ist | Rückweg | Wer |
|---|---|---|---|
| **Sicherungs-Push** | eigener Arbeitszweig → `fork` und `backup-private`, additiv, Fast-Forward | keiner nötig — es entsteht nur eine Kopie | **Bote** |
| **Veröffentlichung** | `main`, Tags, alles Richtung `upstream`, jedes `--force`, jedes Löschen | **keiner** — was draußen ist, ist draußen | **Yama allein, Tor 2** |

*Der Sicherungs-Push kann nichts kaputt machen, was nicht schon lokal ist. Die Veröffentlichung ist
die einzige unumkehrbare Handlung im ganzen Zyklus — und genau deshalb bleibt sie bei Yama.*

## Der Bote ist ein SKRIPT, kein Urteil

**Das ist die Bauart-Entscheidung, und sie ist wichtiger als die Kriterienliste.** Ein Agent, der
„nach Kriterien prüft" und dann entscheidet, ist **Stufe 1 — ein Urteil.** Heute hat genau das
einmal versagt (Generator: *„ich habe das aus Urteil abgeleitet, und das Urteil hat einmal
versagt"*).

```text
Der Bote FUEHRT ein Skript aus. Das Skript prueft und pusht - oder prueft und pusht NICHT.
Der Bote entscheidet nichts. Er liest den Bericht vor und meldet ihn.
Schlaegt ein Kriterium fehl, gibt es keinen Push - auch nicht "weil es diesmal klar ist".
```

## Bestand — gemessen 01.08. 22:5x, alle Kriterien heute grün

```text
Z=auto/hausplaner-integration
git --no-optional-locks rev-list --count fork/$Z..$Z            -> 10   voraus
git --no-optional-locks rev-list --count $Z..fork/$Z            ->  0   zurueck  (Fast-Forward moeglich)
git --no-optional-locks diff --name-only fork/$Z..$Z | wc -l    ->  8   Dateien im Ausgang
  davon .md (Partner, die Messung ist nicht leer)               ->  6
  davon Schluessel/.env                                         ->  0

git --no-optional-locks ls-files | grep -E '(^|/)\.env($|\.)'   -> nur .env.example
git --no-optional-locks ls-files | grep -ciE 'secret|passwo|credential|\.pem$|id_rsa'  -> 17,
  ausnahmslos Laravel-Standarddateien (Passwort-Reset-Controller, Migrationen, Blades)
```

**Das Repo ist an dieser Stelle sauber.** Der eine belegte Fall — `fe47879c` mit Passwort-Historie —
liegt bereits auf beiden Remotes und ist entschärft (Passwort am 30.07. gewechselt). *Er ist der
Grund, warum Kriterium 5 existiert.*

## Die Kriterien — was das Skript prüft, bevor es drückt

```yaml
scope:
  dateien:
    - scripts/bote-push.sh
    - scripts/__tests__/botePush.test.mjs
  population_command: "git --no-optional-locks rev-list --count fork/auto/hausplaner-integration..auto/hausplaner-integration"
  ausschluesse:
    - stelle: "main, Tags, upstream, --force, --delete"
      grund: "Tor 2. Unumkehrbar, gehoert Yama allein. Der Bote lehnt sie ab, er fragt nicht nach."
      entschieden_von: planner
    - stelle: "Ein Votum als Vorbedingung"
      grund: "Ein Backup sichert auch Unabgenommenes, sonst ist es kein Backup. Der Sicherungs-Push haengt NICHT an der Abnahme."
      entschieden_von: planner

kriterien:
  - id: PW-01
    typ: absence
    kritikalitaet: P1
    aussage: "K1 ZIEL - nur fork und backup-private. Niemals upstream."
    pruefung:
      typ: gate
      ausgefuehrt_von: generator
      schritte: |
        B3 - gegen die ENTSCHEIDUNGSFUNKTION, nicht gegen den Ausfuehrer:
          zielErlaubt('upstream')         -> false    (raminsadid2021, fremdes Konto)
          zielErlaubt('fork')             -> true
          zielErlaubt('backup-private')   -> true
          zielErlaubt('irgendwas')        -> false
        Der Remote `upstream` EXISTIERT und bleibt konfiguriert - er ist nur nie ein Ziel.
        Bewusst als Gate und nicht als Zaehler: `git remote -v` steht (noch) nicht auf der
        Allowlist des Validators, und ein Kriterium, das der eigene Validator ueberspringt,
        misst nichts.
      erwartet: "vier Zusagen, davon zwei ROTE"

  - id: PW-02
    typ: presence
    kritikalitaet: P1
    aussage: "K2 RICHTUNG - nur Fast-Forward. Was hinter dem Remote liegt, wird nie ueberschrieben."
    pruefung:
      befehl: "git --no-optional-locks rev-list --count auto/hausplaner-integration..fork/auto/hausplaner-integration"
      erwartet: "0 - sonst kein Push"
    ausgangswert: "0 (gemessen 22:5x)"
    gegenbeweis: |
      Steht hier etwas anderes als 0, liegen auf dem Remote Commits, die lokal fehlen. Ein Push
      waere dann nur mit --force moeglich - und --force ist die Handlung, die Yama gehoert.
      Das Skript bricht ab und meldet die Zahl.

  - id: PW-03
    typ: presence
    kritikalitaet: P1
    aussage: "K3 AUSGANG - was hinausgeht, wird GENANNT, bevor es hinausgeht."
    pruefung:
      befehl: "git --no-optional-locks diff --name-only fork/auto/hausplaner-integration..auto/hausplaner-integration | wc -l"
      erwartet: "mindestens 1 - bei 0 gibt es nichts zu sichern und der Bote schweigt"
    ausgangswert: "8 (Partner: davon 6 mit .md - die Messung ist nicht leer)"

  - id: PW-04
    typ: absence
    kritikalitaet: P1
    aussage: "K4 GEHEIMNIS - kein Schluessel, keine .env im Ausgang."
    pruefung:
      befehl: "git --no-optional-locks diff --name-only fork/auto/hausplaner-integration..auto/hausplaner-integration | grep -iE '\\.pem$|\\.p12$|id_rsa|\\.key$|(^|/)\\.env$' | wc -l"
      erwartet: "0"
    ausgangswert: "0 (gemessen 22:5x)"
    gegenbeweis: |
      Bewusst `| wc -l` und nicht `grep -c`: grep liefert bei null Treffern exit 1, und ein
      Kriterium, das am Nullfall abbricht, prueft nichts. Der Partner-Beleg ist PW-03: dieselbe
      Pipe findet 8 Dateien, also misst sie.
      GRENZE, offen benannt: dies prueft NEUE Dateinamen, nicht Inhalte und nicht die Historie.
      `fe47879c` waere so NICHT gefangen worden - er lag schon im Baum. Der Inhaltstest ist eine
      eigene Scheibe (PW-01-N1), weil er einen Musterkatalog braucht, den es hier noch nicht gibt.

  - id: PW-05
    typ: absence
    kritikalitaet: P1
    aussage: "K5 ZUSTAND - keine Lock-Reste, der Baum hat sich waehrend der Messung nicht bewegt."
    pruefung:
      befehl: "ls .git/ | grep '\\.lock$' | wc -l"
      erwartet: "0"
    ausgangswert: "0"
    gegenbeweis: |
      Dieselbe Logik wie S-10 im Validator: HEAD vor und nach der Messung. Bewegt er sich,
      stammt jede Zahl aus einem Baum, den es nicht mehr gibt - dann kein Push.

  - id: PW-06
    typ: behavioural
    kritikalitaet: P1
    aussage: "K6 ART - das Skript kann gar nicht forcen."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: |
        Zusage gegen die ENTSCHEIDUNGSFUNKTION, nicht gegen den Ausfuehrer (B3):
          zielErlaubt('upstream')                 -> false
          zielErlaubt('fork')                     -> true
          artErlaubt(['--force'])                 -> false
          artErlaubt(['--delete'])                -> false
          refErlaubt('main')                      -> false
          refErlaubt('auto/hausplaner-integration') -> true
        KEIN Testfall ruft die Funktion auf, die wirklich pusht. B1 und B3 gelten hier
        besonders scharf - genau an dieser Stelle ist am 01.08. ein Push entstanden.
      erwartet: "sechs Zusagen, davon drei ROTE Gegenproben"

  - id: PW-07
    typ: behavioural
    aussage: "K7 BERICHT - der Bote meldet, was er getan hat, nicht was er tun wollte."
    ausgefuehrt_von: bote
    pruefung:
      typ: verfahren
      schritte: |
        Nach jedem Lauf: die sechs Kriterienzahlen, die Dateiliste, das Ergebnis je Remote,
        und der Stand VORHER und NACHHER als SHA. Ohne Nachher-SHA gilt der Push als
        unbestaetigt - B5: keine Aussage ueber eine Faehigkeit ohne den Befehl, der sie belegt.
      erwartet: "Bericht mit Vorher- und Nachher-SHA je Remote"
```

## Was das NICHT löst

```text
Der Inhaltstest auf Geheimnisse.   PW-04 prueft NAMEN im Ausgang. Ein Passwort im Klartext in
                                   einer .php-Datei faengt er NICHT. Eigene Scheibe PW-01-N1.
Die Historie.                      Was schon im Baum liegt, geht bei jedem Push mit. `fe47879c`
                                   ist bereits draussen; das ist nicht mehr reparabel ohne
                                   history rewrite - und der braucht --force, also Yama.
Wer heute gepusht hat.             Offen. P-01 Teil 0. Ohne diese Messung weiss der Bote nicht,
                                   wem er den Kanal wegnimmt.
```

## Rückweg und Entdeckung

**Rückweg:** ein Skript und seine Zusagen — zurückdrehbar. **Der Sicherungs-Push selbst braucht
keinen Rückweg: er erzeugt eine Kopie und ändert nichts.** *Das ist der eigentliche Grund, warum
diese Rolle vertretbar ist und Tor 2 nicht.*

**Entdeckung:** der Nachher-SHA je Remote. Steht er nicht im Bericht, hat der Bote nicht
nachgesehen — dann gilt der Push als unbestätigt, auch wenn er gelaufen ist.

## Was Yama entscheiden muss, bevor das gebaut wird

```text
1  Gilt die Trennung Sicherungs-Push / Veroeffentlichung? Dann wird die harte Regel praeziser
   formuliert statt aufgeweicht: "Veroeffentlichung ausschliesslich Yama."
2  Bekommt genau EINE Umgebung den Push-Kanal - und alle anderen keinen? Das ist der Schritt
   auf Stufe 5, und ohne ihn ist der Bote nur eine weitere Regel.
3  Faehrt der Bote auf Zuruf oder nach Takt (z.B. stuendlich, solange etwas voraus ist)?
```
