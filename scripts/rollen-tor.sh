#!/usr/bin/env bash
# ── DAS ROLLEN-TOR — Baum, Zweig und Rolle gehoeren zusammen ─────────────────────────────────
#
# **Wogegen es gebaut ist.** Seit dem Umzug hat jede Rolle ihren eigenen Worktree. Ein Commit im
# falschen Baum ist damit moeglich geworden — und er ist genau der Schaden, gegen den die ganze
# Umstellung gebaut wird: er landet auf einem fremden Zweig, wird dort transportiert, und niemand
# sieht es, weil der Lauf gruen war.
#
# > ***Dieses Tor SPERRT, waehrend A-26, A-27 und A-30 nur melden.*** *Der Unterschied ist gemessen
# > und nicht gefuehlt:* **jene melden Befunde ueber den INHALT** — dort ist ein Fehlalarm teuer und
# > ein Durchlassen billig, weil der naechste Leser den Inhalt ohnehin sieht. **Hier ist es
# > umgekehrt:** ein Commit im falschen Baum wird nicht mehr gesehen, und die Korrektur kostet einen
# > Zweig-Umbau.
#
# ---
#
# ## Die Zuordnung steht in EINER Tabelle
#
# ```text
#   rolle            Verzeichnis                        Zweig
#   integrator       ticket                             auto/hausplaner-integration
#   planner          ticket-rolle-planner               rolle/planner
#   plan-pruefer     ticket-rolle-plan-pruefer          rolle/plan-pruefer
#   generator        ticket-rolle-generator             rolle/generator
#   evaluator        ticket-rolle-evaluator             rolle/evaluator
#   release-pruefer  ticket-rolle-release               rolle/release-pruefer     <- K2
# ```
#
# **K2 ist der Grund fuer die Tabelle.** *Der Release-Baum heisst `ticket-rolle-release`, sein Zweig
# aber `rolle/release-pruefer`.* **Eine Regel „Verzeichnis = ticket-rolle-<rolle>" waere an genau
# dieser Zeile falsch** — und zwar still, weil sie fuer die anderen vier stimmt. Gemessen an
# `git worktree list`, nicht angenommen.
#
# ## Die fuenf Kanten, je mit dem verlangten Verhalten
#
# ```text
# K1  Rolle mit Instanzsuffix (plan-pruefer-2)  -> Rollenstamm vor dem letzten -<ziffer>
# K2  Verzeichnisname weicht vom Rollennamen ab -> die Tabelle entscheidet, nicht das Muster
# K3  Worktree existiert nicht (nicht umgezogen)-> DURCHLASSEN und melden. Der Umzug ist
#                                                  freiwillig getaktet; ein Tor, das ihn
#                                                  erzwingt, haelt die Kette an.
# K4  git rev-parse schlaegt fehl (kein Repo)   -> DURCHLASSEN und melden, mit EIGENER
#                                                  Ursache und NICHT als Rollenfehler.
#                                                  Sonst sucht jemand eine Rollenver-
#                                                  wechslung, die es nicht gibt.
# K5  integrator im gemeinsamen Checkout        -> erlaubt, das ist sein Baum
# --  Zweig stimmt, VERZEICHNIS weicht ab       -> erlaubt MIT HINWEIS. Der Zweig ist der
#                                                  verlaessliche Schluessel, git laesst ihn nur
#                                                  EINMAL auschecken; ein Verzeichnisname nicht.
#                                                  GEHT UEBER DIE SECHS KANTEN HINAUS — gemeldet.
# K6  ANDERE Rolle im gemeinsamen Checkout,     -> erlaubt MIT HINWEIS auf den wartenden Baum.
#     deren eigener Baum SCHON STEHT               EIGENER Fall, KEINE Variante von K3:
#                                                  K3 greift nur wenn das Verzeichnis FEHLT.
#                                                  Ohne K6 erzwingt das Tor genau das, was K3
#                                                  verhindern soll.
# ```
#
# ## Aufruf
#
# ```
#   TICKET_ROLLE=generator bash scripts/rollen-tor.sh
#   TICKET_ROLLE=generator bash scripts/rollen-tor.sh --pruefe   nur melden, nie sperren
# ```
#
# ## Rueckgabewerte — sie stehen im Auftrag und nicht in meiner Wahl
#
# ```text
#   0   Baum und Rolle passen zusammen, oder K3 / K5 / K6
#   1   Rolle und Baum passen NICHT zusammen        <- der Verstoss
#   5   Rollenkennung fehlt beim DIREKTEN Aufruf    <- NICHT 1, sonst nicht unterscheidbar
#   0   kein Repository (K4) — durchgelassen und gemeldet, wie K3/K5/K6
#       (bis 16.08. abends 2; das kollidierte mit dem YAML-Syntaxfehler,
#        seit der Wert durchgereicht wird. Behoben, Begruendung am Code.)
#   6   MODULSTAND: die Marke widerspricht dem Lockfile (A-37-13)
# ```
#
# **Die 6 ist gewaehlt, weil 0/1/5 hier und 0/2/3/4 in `commit-pruefen.sh` schon belegt sind** —
# nachgesehen, nicht angenommen. Fuer die FEHLENDE Marke steht hier bewusst kein Code; die
# Begruendung steht beim Bau selbst und nicht nur hier.
#
# ## ⚠ A-37-5 UND DIE TABELLE WIDERSPRECHEN SICH — im SELBEN Blatt, elf Zeilen auseinander
#
# ```text
#   Z.292  A-37-5 · Negativfall fehlende Kennung: TICKET_ROLLE leer -> exit 3
#   Z.303  | 5 | Rollenkennung fehlt beim direkten Aufruf des Tors | rollen-tor.sh | zu bauen |
#
#   dieses Tor, ohne TICKET_ROLLE gefahren:  5
# ```
#
# **Am 16.08. ueber drei Zweige nachgemessen — Integration, Planner, Plan-Pruefer tragen alle
# dieselben zwei Zeilen.** Es ist also kein Transportstand, sondern der Stand.
#
# ***Der Plan-Pruefer hat den Fall als „A-37-5 ist am gebauten Stand NICHT ERFUELLBAR" gemeldet***
# *(`ea939994`, dreiseitig gemessen).* **Die Messung stimmt, die Deutung greift zu kurz:** das
# Kriterium ist nicht unerfuellbar, sondern das Blatt sagt an zwei Stellen zwei Zahlen fuer
# denselben Fall. **Ein Bau kann nur einer davon folgen** — dieser folgt der Tabelle, weil sie in
# DoR Runde 3 ausdruecklich berichtigt wurde und die Ueberschrift dabei stehenblieb.
#
# **Ich aendere das Blatt nicht** — es gehoert dem Planner, und die Aufloesung ist dort eine Zeile.
# *Bis dahin steht hier, welcher der beiden Saetze gebaut ist, damit niemand es an der 5 raet.*
#
# **Die Zahlen kommen aus der Codetabelle des Auftrags** (berichtigt am 16.08. nach DoR Runde 3).
# *Meine erste Fassung vergab fuer die fehlende Kennung ebenfalls 1 — der Plan-Pruefer hat es
# gemessen und drei Stellen mit drei Zahlen fuer denselben Fall gefunden.* **Die Tabelle liegt seit
# der Berichtigung auf 5, weil ich selbst am 15.08. die 3 fuer `MODUL` belegt hatte** (`374bb851`)
# *und zwei Bedeutungen auf einem Code niemandem aufgefallen waren.*
#
# > ***ERLEDIGT am 16.08. abends:*** *fuer K4 vergab die Tabelle keinen Code, und ich liess
# > die 2 stehen mit dem Hinweis, die Kollision mit dem YAML-Syntaxfehler sei theoretisch.*
# > **Der Plan-Pruefer hat gemessen, dass sie es nicht mehr ist, seit der Wert durchgereicht
# > wird** (`e000f087`). **K4 gibt jetzt 0 und meldet** — der Code ist frei statt neu vergeben.
# ## A-37-17 — ALLE SECHS KANTEN, je einzeln gefahren, Rohausgabe (16.08. abends)
#
# ```text
# K1  Instanznummer wird abgeschnitten
#     TICKET_ROLLE=generator-2   ->  keine Ausgabe            exit 0   (wie 'generator')
#
# K2  unbekannte Rolle steht NICHT in der Tabelle
#     ROLLEN-TOR  unbekannte Rolle 'hausmeister' (Stamm 'hausmeister') — die Tabelle
#                 kennt sie nicht.
#                 Bekannt: integrator planner plan-pruefer generator evaluator
#                          release-pruefer                            exit 1
#
# K3  Rolle ohne eigenen Baum — DURCHGELASSEN
#     ROLLEN-TOR  HINWEIS  'generator' hat noch keinen eigenen Baum
#                 (ticket-rolle-generator) — durchgelassen (K3).       exit 0
#
# K4  kein Repository — KEIN Rollenfehler, DURCHGELASSEN (seit 16.08. abends)
#     ROLLEN-TOR  HINWEIS  kein Git-Repository — keine Zuordnung pruefbar (K4).
#                 Durchgelassen und gemeldet — wie K3, K5 und K6.        exit 0
#
# K5  Integrator IM Integrations-Checkout
#     (nur der Modulstand-Hinweis, keine Rollenmeldung)                exit 0
#
# K6  fremde Rolle im gemeinsamen Checkout — DURCHGELASSEN
#     (nur der Modulstand-Hinweis, keine Rollenmeldung)                exit 0
# ```
#
# **Vier der sechs Kanten LASSEN DURCH und melden; zwei weisen ab.** *Das ist kein Zufall der
# Umsetzung, sondern die Bauabsicht:* **abgewiesen wird, was Schaden anrichtet — der Commit im
# fremden Baum und die Rolle, die es nicht gibt.** *Alles andere ist ein Uebergangszustand, und
# ein Tor, das Uebergaenge sperrt, haelt die Kette an.*
#
# ***Bei K5 und K6 erscheint heute NUR der Modulstand-Hinweis*** — *der gemeinsame Checkout hat
# Module, aber keine Marke.* **Das ist eine Auskunft an den Integrator und kein Mangel dieser
# Probe:** *sie zeigt, dass an dieser Stelle keine ROLLEN-Meldung entsteht, und genau das war zu
# belegen.*
set -uo pipefail

NUR_MELDEN=0
[ "${1:-}" = "--pruefe" ] && NUR_MELDEN=1

ROLLE="${TICKET_ROLLE:-}"
if [ -z "$ROLLE" ]; then
  # Rueckgabe 5, NICHT 1. Die Zahl kommt aus der Codetabelle des Auftrags (berichtigt 16.08. nach
  # DoR Runde 3) und nicht aus meiner Wahl: 1 ist "Rolle und Baum passen nicht zusammen", und die
  # beiden Faelle muessen am Code unterscheidbar bleiben. Dass hier vorher 1 stand, hat der
  # Plan-Pruefer gemessen.
  echo "ROLLEN-TOR  TICKET_ROLLE ist nicht gesetzt — ohne Rolle ist keine Zuordnung pruefbar." >&2
  [ "$NUR_MELDEN" = "1" ] && exit 0
  exit 5
fi

# K1: eine Instanz haengt ihre Nummer an. `plan-pruefer-2` ist die Rolle `plan-pruefer`.
STAMM="$(printf '%s' "$ROLLE" | sed -E 's/-[0-9]+$//')"

# ── A-37-22e — GENERATION UND DIGEST GEGEN DIE ZENTRALE ROLLENQUELLE ────────────────────────
#
# **Yamas Wortlaut:** *vor jedem schreibenden Schritt und unmittelbar im Commit-Gate wird die
# aktuelle Generation samt Digest erneut gegen die zentrale Rollenquelle geprueft; veralteter ACK,
# fehlender ACK oder eine Aktion wie `pausieren` fuehren zur Abweisung — vor jeder Aenderung.*
# **„Beim naechsten Takt lesen" reicht nicht.**
#
# **Der Beleg, dass die Luecke wirkt, ist von heute und entlastet den Bauenden:**
# ```text
#   08:12:54   Pause (gen 6) fuer den Generator veroeffentlicht
#   08:16:37   Generator-Commit 1155709d — trotzdem gesetzt
# ```
# *Er hat gegen keine Regel verstossen: sein Takt hatte die Pause noch nicht gelesen.* **Genau das
# ist der Punkt.** Eine Steuerung, die erst beim naechsten Takt wirkt, hat zwischen Veroeffentlichung
# und Lesen ein Loch, und der Commit faellt hinein. *Ich war dieser Bauende; das Kriterium schliesst
# meinen eigenen Fehler, und deshalb steht die Pruefung hier und nicht in einer Merkregel.*
#
# **ZUR TEILSTRING-FALLE, die das Blatt zu diesem Kriterium ausdruecklich benennt:** `grep -ci 'ack'`
# meldet in `commit-pruefen.sh` FUENF Treffer, und **keiner davon ist ein ACK** — selbst nachgestellt
# sind es `package` (3x) und `ungetrackt` (2x). **Mit Wortgrenze sind es 0.** *Wer die 5 als Beleg
# naehme, meldete ein Tor, das es nicht gibt.*
#
# **Der Ort der Steuerungsstelle ist konfigurierbar** (`TICKET_STEUERUNG`), damit die Proben in
# einer Probe-Steuerungsstelle laufen koennen und die echte Ablage nicht anfassen — A-37-22d gilt
# hier mit. *Der Standardwert bleibt kanonisch; wer nichts setzt, prueft gegen die echte Quelle.*
STEUERUNG="${TICKET_STEUERUNG:-/Users/yamanuri/.ticket-steuerung}"

# Rueckgabe 7 fuer "die Steuerung sagt: nicht jetzt". NEU in der Codetabelle und bewusst nicht 1
# oder 5: 1 heisst "Rolle und Baum passen nicht zusammen", 5 "Rollenmarke fehlt oder ist falsch".
# Beides waere hier gelogen — Rolle und Baum koennen tadellos stimmen, waehrend die Steuerung
# pausiert. Ein eigener Code haelt die Faelle am Rueckgabewert unterscheidbar, so wie der
# Plan-Pruefer es fuer 1 gegen 5 durchgesetzt hat.
# ── A-37-25 — SITZUNGSIDENTITAET: PUNKT 1, 2 UND 6 DER ZIELREGEL ───────────────────────────
#
# **Punkt 1: die stabile Identitaet ist allein die Sitzungs-ID.** Sie ueberlebt den Prozess.
# **Punkt 2: `pid` und `prozess_start` gelten je LAUF**, nicht je Sitzung.
# **Punkt 6: eine alte PID allein erklaert eine Lease NIEMALS fuer verwaist.**
#
# **Der Realfall ist protokolliert, nicht konstruiert:** um 00:17:18 wurde die Planner-Lease mit
# `owner.pid 88928` erteilt — *dieser Prozess war da bereits tot*, waehrend die Sitzung durchgehend
# arbeitete und vier Minuten spaeter ihr Blatt schrieb. **Jede eingetragene PID war zum Zeitpunkt
# ihres Eintrags richtig; sie ist nicht falsch, sondern abgelaufen.**
#
# **Am 22.08. traf es diese Rolle selbst:** der Auftrag nannte PID 88088 als meine Sitzung, und
# `ps` gab dafuer STAT T. Mein tatsaechlicher Lauf war 91834 — dieselbe Sitzung, neuer Lauf.
# *Aufgefallen ist es nur, weil die alte PID gestoppt war.* **Haette sie gelebt, waere die
# Lebendprobe gruen gewesen und der Wechsel unbemerkt geblieben** — genau das ist dem Plan-Pruefer
# am selben Vormittag passiert, der seine tote PID dutzendfach mit exit 0 geprueft hatte.
#
# **Daraus die Regel, die dieses Tor anwendet:** *nicht „lebt die eingetragene PID?", sondern
# „ist sie MEINE?".* Die erste Frage kann gruen sein, waehrend die zweite rot ist.
sitzung_lebt() {
  # $1 = Sitzungs-ID. Gesucht wird der LAUF an seiner Kommandozeile, nicht an einer gespeicherten
  # Zahl. Eine Sitzungs-ID in der Kommandozeile ist ein Lebensnachweis; eine PID in einer Datei
  # ist eine Aussage mit Verfallsdatum, deren Datum niemand kennt.
  [ -z "${1:-}" ] && return 1
  # Ohne Pipe: die Ausgabe wird erst vollstaendig eingelesen und dann geprueft. Ein `grep -q`
  # hinter einer Pipe beendet sich beim ersten Treffer und kann dem Erzeuger SIGPIPE geben —
  # unter `pipefail` wird daraus ein Fehlschlag der ganzen Kette. Der Hausgrundsatz dazu steht im
  # Kopf von rueckweg.py: eine ausgefallene Messung ist KEIN Ergebnis. Hier ist sie einfach
  # vermeidbar, also wird sie vermieden.
  #
  # ⚠ HIER STAND EINE ZUFALLSTREFFER-FALLE, gefunden durch die eigene Negativprobe S3.
  # Die erste Fassung suchte die Sitzungs-ID irgendwo in der GESAMTAUSGABE von `ps`. Gemessen:
  # eine Sitzungs-ID, zu der KEIN Prozess existierte, galt als lebend — weil die Probe selbst
  # `printf ... tote-sitzung-4711 ...` aufgerufen hatte und diese Kommandozeile in `ps` stand.
  # **Das Muster mass, ob die Zeichenkette irgendwo vorkommt, und nicht, ob die Sitzung laeuft.**
  # Ein Editor mit der ID im Dateinamen, ein grep, ein Scratchpad-Pfad — jedes davon haette eine
  # tote Sitzung fuer lebend erklaert, und zwar ausgerechnet in der Richtung, die einen fremden
  # Commit durchgelassen haette.
  #
  # Gezaehlt wird jetzt nur, was ein claude-LAUF ist: das erste Feld der Kommandozeile muss das
  # Programm `claude` sein. Damit fallen bash, printf, sed und der Scratchpad-Pfad heraus —
  # letzterer enthaelt uebrigens selbst die Zeichenfolge `claude`, weshalb eine Suche nach dem
  # blossen Wort ebenfalls nicht genuegt haette.
  _AUS="$(ps -axo command= 2>/dev/null)"
  while IFS= read -r _z; do
    _prog="${_z%% *}"
    case "$_prog" in
      */claude|claude) ;;
      *) continue ;;
    esac
    case "$_z" in
      *"$1"*) return 0 ;;
    esac
  done <<< "$_AUS"
  return 1
}

# Die eigene Sitzungs-ID. Ohne sie kann dieses Tor keine Sitzung unterscheiden — dann prueft es
# nicht und sagt das, statt zu raten.
SITZUNG="${TICKET_SITZUNG:-}"

# ── Committe ich unter einer FREMDEN Lease? ────────────────────────────────────────────────
#
# **Abgrenzung, und sie ist Teil des Kriteriums:** A-37-25 baut ein pre-commit-Tor, KEINE
# Lease-Verwaltung. Hier wird gelesen und verglichen — nie erteilt, nie uebernommen, nie
# entfernt. Heartbeat-Erneuerung, Uebernahme und Fencing sind Mechanik der Claim-Sperre und
# gehoeren nach Z0-I2. *Sie hier zu bauen hiesse, Lease-Verwaltung in einen Commit-Haken zu legen.*
fremde_lease_pruefen() {
  [ -z "$SITZUNG" ] && return 0
  LEASEDATEI="$(ls -1 "$STEUERUNG"/leases/*/active/lease.yaml 2>/dev/null | head -1)"
  [ -f "${LEASEDATEI:-}" ] || return 0
  LSITZUNG="$(sed -n 's/^[[:space:]]*sitzungs_id:[[:space:]]*\([^[:space:]#]*\).*/\1/p' "$LEASEDATEI" | head -1)"
  [ -z "$LSITZUNG" ] && return 0
  [ "$LSITZUNG" = "$SITZUNG" ] && return 0

  # Die Lease gehoert einer anderen Sitzung. Ob sie noch gilt, entscheidet NICHT die eingetragene
  # PID — Punkt 6 der Zielregel verbietet genau diesen Schluss. Gefragt wird, ob JENE SITZUNG lebt.
  if sitzung_lebt "$LSITZUNG"; then
    echo "ROLLEN-TOR  VERSTOSS  eine andere Sitzung haelt die Lease und lebt." >&2
    echo "            Lease: $LEASEDATEI" >&2
    echo "            haelt:  $LSITZUNG" >&2
    echo "            ich:    $SITZUNG" >&2
    return 7
  fi
  # Sie lebt nicht — und trotzdem wird hier NICHTS uebernommen und nichts fuer verwaist erklaert.
  # Das Tor meldet und laesst durch; die Uebernahme verlangt zusaetzlich einen abgelaufenen
  # Heartbeat und gehoert der Claim-Sperre (Z0-I2), nicht einem Commit-Haken.
  echo "ROLLEN-TOR  HINWEIS  die aktive Lease gehoert Sitzung $LSITZUNG, die kein laufender" >&2
  echo "            Prozess traegt. NICHT als verwaist behandelt — das entscheidet die" >&2
  echo "            Claim-Sperre ueber Heartbeat und Fencing, nicht dieses Tor." >&2
  return 0
}

steuerung_pruefen() {
  QUELLE="$STEUERUNG/rollen/$STAMM.yaml"
  DIGESTDATEI="$QUELLE.sha256"

  # Keine Steuerungsstelle: das Tor laeuft auch in Wegwerf-Repos und in Baeumen ohne Anbindung.
  # Durchlassen und MELDEN — dieselbe Bauform wie K3, K4 und K6. Eine Barriere, die ueberall dort
  # sperrt, wo sie nichts messen kann, wird abgeschaltet und schuetzt danach nirgends (A-03).
  if [ ! -f "$QUELLE" ]; then
    echo "ROLLEN-TOR  HINWEIS  keine Rollenquelle unter $STEUERUNG/rollen/ — Generation UNGEPRUEFT." >&2
    return 0
  fi

  # Der Digest wird SELBST gerechnet und nicht uebernommen. Weicht er ab, ist die Quelle nicht die,
  # als die sie sich ausgibt — dann wird nicht committet, und zwar bevor irgendetwas geschieht.
  if [ -f "$DIGESTDATEI" ]; then
    IST="$(shasum -a 256 "$QUELLE" 2>/dev/null | awk '{print $1}')"
    SOLL="$(awk '{print $1}' "$DIGESTDATEI" 2>/dev/null)"
    if [ -n "$SOLL" ] && [ "$IST" != "$SOLL" ]; then
      echo "ROLLEN-TOR  VERSTOSS  Digest der Rollenquelle stimmt nicht." >&2
      echo "            selbst gerechnet: $IST" >&2
      echo "            hinterlegt:       $SOLL" >&2
      echo "            Eine Quelle, die nicht ist, was sie behauptet, traegt keinen Commit." >&2
      return 7
    fi
  fi

  # ── DIE ZAHL WIRD AUS DEM FELD GELESEN, NICHT AUS DER ZEILE ─────────────────────────────
  #
  # **Hier stand `awk -F: '/^generation:/ {gsub(/[^0-9]/,"",$2); ...}'` — und das war falsch.**
  # Gemessen an der echten Rollenquelle, deren generation-Zeile einen Kommentar traegt:
  # ```text
  #   generation: 8            # 22.08. 09:07 — DoR der Nachschaerfung ERTEILT ...
  #   gelesen wurde:  8220809
  # ```
  # *`-F:` trennt auch am Doppelpunkt in `09:07`, und `gsub` sammelt anschliessend JEDE Ziffer
  # der Zeile ein — aus der 8 wurden 8, 22, 08 und 09.* **Die Folge war eine Sperre gegen jeden
  # Commit: 8 ist kleiner als 8220809, also galt mein aktueller ACK als veraltet.**
  #
  # **Warum es die fuenf Proben nicht gefunden haben:** meine Probe-Steuerungsstelle schrieb
  # `generation: 5` ohne Kommentar. **Die Probe war sauberer als die Wirklichkeit** — und hat
  # deshalb genau den Fall nicht gestellt, den die Wirklichkeit sofort stellte. *Gefunden hat
  # ihn die Rueckfallprobe am ECHTEN Tor gegen die ECHTE Quelle, nicht die Probenreihe.*
  #
  # `sed` nimmt jetzt die erste Ziffernfolge unmittelbar hinter dem Doppelpunkt und laesst den
  # Rest der Zeile stehen. Was hinter der Zahl kommt, geht die Zahl nichts an.
  GEN="$(sed -n 's/^generation:[[:space:]]*\([0-9][0-9]*\).*/\1/p' "$QUELLE" | head -1)"
  # Dieselbe Klasse, nur noch nicht eingetreten: ein Kommentar hinter der Aktion darf sie nicht
  # veraendern. Genommen wird das erste Wort hinter dem Doppelpunkt, nicht der Zeilenrest.
  AKTION="$(sed -n 's/^aktion:[[:space:]]*\([^[:space:]#]*\).*/\1/p' "$QUELLE" | head -1)"

  # Die Aktion entscheidet VOR dem ACK-Alter: pausiert die Steuerung, ist ein aktueller ACK
  # ebenso wenig eine Erlaubnis wie ein veralteter.
  case "$AKTION" in
    bauen) ;;
    "")
      echo "ROLLEN-TOR  HINWEIS  Rollenquelle nennt keine aktion — Generation UNGEPRUEFT." >&2
      return 0 ;;
    *)
      echo "ROLLEN-TOR  VERSTOSS  die Steuerung sagt '$AKTION', nicht 'bauen'." >&2
      echo "            Quelle: $QUELLE (generation ${GEN:-?})" >&2
      echo "            Am 22.08. fiel ein Commit 3 Minuten 43 nach einer Pause, weil das Tor" >&2
      echo "            sie nicht gelesen hat. Diese Zeile ist die Antwort darauf." >&2
      return 7 ;;
  esac

  # Fehlender ACK ist eine Abweisung und kein Hinweis: er ist der einzige gueltige Nachweis, dass
  # der Auftrag angekommen ist. Wer ohne ihn baut, baut gegen einen Auftrag, den er nicht gelesen hat.
  ACKDATEI="$(ls -1 "$STEUERUNG"/ereignisse/*/"$STAMM"-ack.yaml 2>/dev/null | head -1)"
  if [ -z "$ACKDATEI" ] || [ ! -f "$ACKDATEI" ]; then
    echo "ROLLEN-TOR  VERSTOSS  kein ACK gefunden fuer Rolle '$STAMM'." >&2
    echo "            Der ACK ist der einzige Nachweis, dass der Auftrag gelesen wurde." >&2
    return 7
  fi
  ACKGEN="$(sed -n 's/^generation:[[:space:]]*\([0-9][0-9]*\).*/\1/p' "$ACKDATEI" | head -1)"

  if [ -z "$GEN" ] || [ -z "$ACKGEN" ]; then
    echo "ROLLEN-TOR  HINWEIS  Generation nicht lesbar (Quelle '${GEN:-}', ACK '${ACKGEN:-}')." >&2
    echo "            Eine ausgefallene Messung ist KEIN Ergebnis — durchgelassen und gemeldet." >&2
    return 0
  fi
  if [ "$ACKGEN" -lt "$GEN" ]; then
    echo "ROLLEN-TOR  VERSTOSS  ACK ist veraltet: quittiert generation $ACKGEN, aktuell ist $GEN." >&2
    echo "            $ACKDATEI" >&2
    echo "            Erst quittieren, dann bauen. Ein Auftrag, den niemand gelesen hat," >&2
    echo "            ist keine Erlaubnis." >&2
    return 7
  fi
  return 0
}

# Eigener Modus, damit dasselbe Stueck UNMITTELBAR vor dem `git commit` noch einmal laufen kann.
# **Das ist nicht dieselbe Pruefung zweimal, sondern zwei verschiedene Zeitpunkte:** das Tor laeuft
# am Anfang von commit-pruefen.sh, der Commit faellt einige hundert Zeilen spaeter — YAML-Koepfe,
# Modulstand, B5-B8. Eine Pause, die dazwischen veroeffentlicht wird, faende sonst dasselbe Loch
# wieder vor, nur kleiner. Die Absage-Regel des Kriteriums zielt genau darauf: verlangt ist die
# Pruefung IM Commit-Gate, nicht davor.
if [ "${1:-}" = "--steuerung" ]; then
  steuerung_pruefen || exit $?
  fremde_lease_pruefen
  exit $?
fi

steuerung_pruefen || exit $?
fremde_lease_pruefen || exit $?

# K4: kein Repo ist KEIN Rollenfehler und wird auch nicht als einer gemeldet.
BAUM="$(git rev-parse --show-toplevel 2>/dev/null)" || BAUM=""
if [ -z "$BAUM" ]; then
  echo "ROLLEN-TOR  HINWEIS  kein Git-Repository — hier ist keine Zuordnung pruefbar (K4)." >&2
  echo "            Das ist KEIN Rollenfehler. Ursache: git rev-parse --show-toplevel schlug fehl." >&2
  echo "            Durchgelassen und gemeldet — wie K3, K5 und K6." >&2
  # ── K4 GIBT 0 STATT 2 (behoben 16.08. abends) ───────────────────────────────────────────
  #
  # **Der Befund des Plan-Pruefers** (`e000f087`): *„das Tor gibt bei K4 weiterhin exit 2 — und
  # die Tabelle vergibt die 2 an den YAML-Syntaxfehler in `commit-pruefen.sh`. Da der Wert jetzt
  # durchgereicht wird, sieht der Aufrufer in beiden Faellen dieselbe 2."*
  #
  # ***Und er nennt den Grund, warum es vorher unsichtbar war:*** *„solange der Einhaengepunkt
  # alles auf 2 warf, fiel es nicht auf".* **Erst die Behebung eine Ebene hoeher hat den Fall
  # sichtbar gemacht.**
  #
  # **Von seinen zwei Wegen — siebte Tabellenzeile oder 0 mit Meldung — nehme ich den zweiten,
  # und zwar nicht aus Bequemlichkeit:** *eine siebte Zeile waere eine Codevergabe, und die
  # Tabelle gehoert dem Auftrag und nicht mir.* **Die 0 dagegen folgt der eigenen Bauform dieses
  # Tores:** *K3 (kein Baum), K5 (Integrator im Checkout) und K6 (fremde Rolle im gemeinsamen
  # Checkout) lassen ALLE durch und melden.* **K4 sagt selbst „das ist KEIN Rollenfehler" — dann
  # darf es auch nicht wie einer aussehen.**
  #
  # *Sicherheitsverlust: keiner.* **Ohne Repository scheitert der Commit ohnehin an git**, und
  # zwar mit einer eigenen, deutlicheren Meldung als der Rueckgabe eines Rollentors.
  exit 0
fi
VERZ="$(basename "$BAUM")"
ZWEIG="$(git rev-parse --abbrev-ref HEAD 2>/dev/null || echo "?")"

# Der Integrations-Checkout steht EINMAL hier: K5 (der integrator gehoert hinein) und K6 (jede
# andere Rolle darf dort noch arbeiten) lesen dieselben zwei Werte. Zwei Schreibweisen waeren
# zwei Wahrheiten, und die zweite altert still.
INTEGRATION_VERZ="ticket"
INTEGRATION_ZWEIG="auto/hausplaner-integration"

# K2: die Zuordnung steht hier und wird NICHT aus dem Rollennamen gerechnet.
case "$STAMM" in
  integrator)      SOLL_VERZ="$INTEGRATION_VERZ";         SOLL_ZWEIG="$INTEGRATION_ZWEIG" ;;
  planner)         SOLL_VERZ="ticket-rolle-planner";      SOLL_ZWEIG="rolle/planner" ;;
  plan-pruefer)    SOLL_VERZ="ticket-rolle-plan-pruefer"; SOLL_ZWEIG="rolle/plan-pruefer" ;;
  generator)       SOLL_VERZ="ticket-rolle-generator";    SOLL_ZWEIG="rolle/generator" ;;
  evaluator)       SOLL_VERZ="ticket-rolle-evaluator";    SOLL_ZWEIG="rolle/evaluator" ;;
  release-pruefer) SOLL_VERZ="ticket-rolle-release";      SOLL_ZWEIG="rolle/release-pruefer" ;;
  # dirigent: siebter Eintrag, 21.08. (Gesamtauftrag v2 Phase 0/2 — nur der Integrator schreibt im
  # Integrationscheckout; der Dirigent verlaesst ihn in einen eigenen Worktree). Additiv, dieselbe
  # Zuordnungsform wie die sechs davor; Vollmacht docs/regelwerk/VOLLMACHT-DIRIGENT.md.
  dirigent)        SOLL_VERZ="ticket-rolle-dirigent";     SOLL_ZWEIG="rolle/dirigent" ;;
  *)
    echo "ROLLEN-TOR  unbekannte Rolle '$ROLLE' (Stamm '$STAMM') — die Tabelle kennt sie nicht." >&2
    echo "            Bekannt: integrator planner plan-pruefer generator evaluator release-pruefer dirigent" >&2
    [ "$NUR_MELDEN" = "1" ] && exit 0
    exit 1 ;;
esac

# ── A-37 TEIL 2 — die Statuswahrheit hat EINEN Schreiber ─────────────────────────────────────
#
# **Der Befund, der es noetig macht** (16.08., ueber alle Zweige gemessen): `docs/STATUS.md` liegt
# in SECHS Fassungen vor — 21.705 / 21.704 / 22.165 / 19.568 / 22.796 / 22.968 Zeilen. **A-33 stand
# gleichzeitig auf CODE_FERTIG und ABGENOMMEN.** Wer HEAD liest, haelt einen abgenommenen Auftrag
# fuer offen und zieht ihn ein zweites Mal.
#
# *Die Umstellung hat die Kollision nicht geloest, sondern verwandelt:* **vorher Beifang beim
# gleichzeitigen Schreiben, sofort sichtbar — heute divergente Wahrheit, unsichtbar bis jemand
# vergleicht.** Nicht schlimmer, aber leiser, und leise Fehler sind die teuren.
#
# ## ⚠ DIE SPERRE ZUENDET ERST, WENN ES EINEN INTEGRATOR GIBT — und das ist eine Abweichung
#
# **A-37-6 verlangt woertlich: sobald `docs/STATUS.md` in der Pfadliste steht und der Baum nicht
# der Integrations-Checkout ist, ABWEISEN.** Die Uebergangsklausel nennt als Umschaltpunkt
# `P2H-06` (alle Rollen umgezogen).
#
# **Gemessen am 16.08.: `P2H-06` ist ERFUELLT und es gibt NULL Commits mit der Rollenmarke
# `integrator`.** Woertlich gebaut haette die Sperre damit heute ALLE FUENF Rollen aus der
# Statuswahrheit ausgesperrt, ohne dass ein Schreiber danebensteht. **Eine andere Rolle hat den
# Deadlock bereits im Datensatz dokumentiert:** *„die Barriere sperrt aus dem RICHTIGEN Grund, aber
# BEVOR der Schreiber existiert. Sie kommt vor ihrem Ersatz."*
#
# **Deshalb ist die Bedingung nicht `P2H-06`, sondern die Existenz des Ersatzes** — am Repository
# gemessen und nicht geraten: gibt es mindestens einen Commit mit der Rollenmarke `integrator`,
# ist der Schreiber da und die Sperre greift. Vorher wird gemeldet statt gesperrt.
#
# ***Das ist derselbe Gedanke wie K3 und K6, eine Ebene hoeher:*** *ein Tor, das eine Handlung
# erzwingt, fuer die es noch keinen Weg gibt, haelt die Kette an, statt sie zu schuetzen.*
#
# **Und es ist eine Abweichung vom Wortlaut des Kriteriums. Sie steht hier und im Bau-Bericht,
# nicht still im Code.**
#
# ## ⚠ DIE SPERRE HAT AM 16.08. UM 16:17 GEZUENDET — ohne dass jemand eine Zeile anfasste
#
# ```text
#   Commits mit Rollenmarke 'integrator'   0  ->  1   (83296554, Yama, 16:17)
#
#   TOR_STATUS_PFAD=1, generator, eigener Baum
#     vor  16:17   HINWEIS  ... noch KEIN Integrator gestartet   exit 0
#     nach 16:17   VERSTOSS ... EINEN Schreiber: den Integrator  exit 1
# ```
#
# ***Das ist der Ertrag der Bedingung.*** *Waere sie ein Datum oder ein Haekchen gewesen, haette
# jemand sie umlegen muessen — und es waere entweder zu frueh oder vergessen worden.* **Sie war
# eine Messung, also hat sie sich selbst umgelegt.**
#
# ## Der Einwand des Integrators, und wo er zutrifft
#
# **Yama misst im Integrations-Checkout** (`83296554`): *„die Barriere ist hier nicht vorhanden …
# sie koennte die Divergenz ohnehin nicht fangen: sie prueft Baum gegen Zweig gegen Rolle, und
# jede Rolle committet in ihrem eigenen Baum regelkonform — die Divergenz entsteht genau dann,
# wenn alle regelkonform arbeiten."*
#
# **Fuer TEIL 1 stimmt das vollstaendig, und es ist der schaerfste Satz, der ueber diesen Bau
# gesagt wurde.** *Teil 1 schuetzt gegen den Commit im FALSCHEN Baum. Die Divergenz kommt aber aus
# dem richtigen.*
#
# **Fuer TEIL 2 stimmt es nicht — und der Grund, warum er ihn nicht sieht, ist die zweite Haelfte
# seines eigenen Satzes:**
#
# ```text
#   Zweig                        Tor  Teil-2-Zeilen  Haken in commit-pruefen.sh
#   auto/hausplaner-integration   0        0            0     <- wo er misst
#   rolle/generator               1        1            3
#   rolle/release-pruefer         1        1            3
# ```
#
# ***Teil 2 prueft nicht Baum gegen Rolle, sondern den PFAD:*** `docs/STATUS.md` *ausserhalb des
# Integrations-Checkouts.* **Er trifft genau den Fall, den er beschreibt** — sechs Rollen, alle
# regelkonform, die dieselbe Datei fortschreiben. *Er liegt seit 15:31 gebaut auf meinem Zweig und
# ist an dem einen Ort nicht angekommen, an dem gemessen wird.*
#
# **Der Einwand ist damit kein Einwand gegen den Bau, sondern die genaueste Beschreibung des
# Transportproblems, die heute vorliegt.**
#
# ## A-37-18 — wo das Tor am 16.08. um 16:30 WIRKLICH liegt, alle sechs Baeume einzeln
#
# ```text
#   Baum                        Zweig                        Tor   Haken
#   ticket                      auto/hausplaner-integration  NEIN    0
#   ticket-rolle-planner        rolle/planner                NEIN    0
#   ticket-rolle-plan-pruefer   rolle/plan-pruefer           NEIN    0
#   ticket-rolle-generator      rolle/generator              JA      3
#   ticket-rolle-evaluator      rolle/evaluator              JA      3
#   ticket-release-pruefung     rolle/release-pruefer        JA      3
# ```
#
# **DREI von sechs, nicht zwei.** *Der Auftrag nennt zwei und fuehrt `release` mit 0.* **Die
# Ursache steht schon weiter oben in dieser Datei:** das Verzeichnis `ticket-rolle-release`
# existiert noch, ist aber laengst nicht mehr der Baum des Release-Pruefers — der arbeitet in
# `ticket-release-pruefung`. *Wer nach dem alten Namen misst, findet eine Null, die es nicht gibt.*
#
# ***Genau daran ist meine eigene Tabelle am 16.08. gescheitert, und genau deshalb entscheidet
# hier seither der ZWEIG und nicht das Verzeichnis.*** **Dieselbe Falle, zweite Runde, anderer
# Messender** — was fuer die Zahl im Kriterium heisst: besser den Zweig erheben als den Ordner.
#
# **Gemeldet, nicht geaendert.** *Das Blatt gehoert dem Planner.*
#
# ### Woran die Verteilung haengt — es ist EIN Commit, und der Weg dorthin ist nicht meiner
#
# ```text
#   eingefuehrt durch   0ee521f7   16.08. 13:38
#
#   Zweig                        enthaelt ihn   ls-tree
#   auto/hausplaner-integration      NEIN          0
#   rolle/planner                    NEIN          0
#   rolle/plan-pruefer               NEIN          0
#   rolle/generator                  JA            1
#   rolle/evaluator                  JA            1
#   rolle/release-pruefer            JA            1
# ```
#
# **Das SOLL von A-37-18 lautet: `git ls-files scripts/rollen-tor.sh` ergibt in JEDEM der sechs
# Baeume 1.** *Der Index eines Worktrees ist der seines Zweiges* — die Datei erscheint dort genau
# dann, wenn `0ee521f7` auf dem Zweig liegt. **Das ist Transport, und Transport ist mir
# ausdruecklich untersagt.**
#
# ***Auch der naheliegende Ausweg traegt nicht:*** *das Tor in `commit-pruefen.sh` hineinzuziehen,
# damit es keine eigene Datei mehr braucht.* **Die drei Baeume ohne Tor tragen auch den HAKEN
# nicht** (`Haken 0`, oben gemessen) — beide Dateien reisen mit demselben Transport. *Kein Bau von
# mir aendert das Ergebnis.*
#
# **Was ich statt dessen gebaut habe, steht in `commit-pruefen.sh`:** die Abwesenheit meldet sich
# jetzt selbst. *Das behebt A-37-18 nicht — es macht nur den Zustand, den A-37-18 beschreibt, in
# jedem betroffenen Baum sichtbar, statt ihn schweigen zu lassen.*
# ── A-37-24 — DER SCHUTZ ZIEHT MIT DEM INHALT UM ────────────────────────────────
#
# **Diese Barriere hiess bis hierher „docs/STATUS.md" und meinte den Ort, nicht die Sache.** Mit
# `A-42` (Bau `26c46f31`) sind 172 Befundbloecke aus `docs/STATUS.md` nach `docs/BEFUNDNOTIZEN.md`
# gezogen. **Der Inhalt ist umgezogen, der Schutz ist geblieben, wo er war** — seither kann jede
# Rolle aus jedem Baum in die Befundnotizen schreiben, und zwar genau die Bloecke, die am Tag
# davor noch verteidigt wurden.
#
# ```text
#   Basis ab9e837c   rollen-tor.sh      BEFUNDNOTIZEN 0   gegen  STATUS.md 8
#                    commit-pruefen.sh  BEFUNDNOTIZEN 0   gegen  STATUS.md 9
# ```
#
# ***Das ist kein Vergessen, sondern die Bauform:*** *die Sperre war an einen Dateinamen genagelt
# und nicht an die Eigenschaft „hat EINEN Schreiber".* **Wer den Namen bewegt, bewegt die Sperre
# nicht mit.** Deshalb steht der Name jetzt EINMAL, in einer Liste, an einer Stelle.
#
# **Welche der geschuetzten Dateien tatsaechlich in der Pfadliste steht, sagt `TOR_STATUS_DATEIEN`.**
# *Ohne diese Auskunft haette die Meldung weiter „docs/STATUS.md" behauptet, waehrend der Commit
# die Befundnotizen anfasst* — **eine Barriere, die den falschen Namen nennt, ist beim Nachlesen
# nicht wiederzufinden.** Fehlt die Variable (Direktaufruf des Tores ohne `commit-pruefen.sh`),
# bleibt die alte Nennung stehen; die Sperre wirkt dann unveraendert.
if [ "${TOR_STATUS_PFAD:-0}" = "1" ] && [ "$STAMM" != "integrator" ]; then
  GESCHUETZT="${TOR_STATUS_DATEIEN:-docs/STATUS.md}"
  INTEGRATOR_DA="$(git log --all --format=%s --grep='^integrator:' 2>/dev/null | head -1)"

  # ── DIE ZWEITE HAELFTE DER ZUENDBEDINGUNG (16.08., nach dem Befund d9fd6471) ──────────────
  #
  # **Die Sperre wirkte VERKEHRT HERUM, und das ist gemessen und nicht gefolgert.** Nach der
  # Zuendung um 16:17 haben `planner` und `plan-pruefer` `docs/STATUS.md` weiter geschrieben —
  # **sie umgehen nichts, das Tor liegt in ihren Baeumen gar nicht.** Gesperrt waren Generator,
  # Evaluator und Release-Pruefer: **genau die drei, die die Barriere HABEN und sich daran halten.**
  #
  # ```text
  #   nach 16:17 schrieben docs/STATUS.md:  plan-pruefer, planner   (Tor im Baum: 0)
  #   gesperrt:                             generator, evaluator,
  #                                         release-pruefer         (Tor im Baum: 1)
  # ```
  #
  # ***Eine Barriere, die nur die Ausgestatteten bindet, schuetzt die Datei nicht — sie waehlt
  # aus, wer sie schreibt.*** *Und sie waehlt genau falsch: die Diszipliniertesten stehen still,
  # die Uebrigen schreiben weiter.* **Der Bestand wird dadurch nicht einheitlicher, sondern
  # einseitiger.**
  #
  # **Meine Zuendbedingung war notwendig und nicht hinreichend.** Sie fragte „gibt es einen
  # Schreiber?" und haette auch fragen muessen „gilt die Regel fuer alle?". *Eine Regel, die nur
  # dort gilt, wo sie zufaellig installiert ist, ist keine Regel, sondern eine Benachteiligung.*
  #
  # **Deshalb zuendet sie erst, wenn das Tor in ALLEN Zweigen liegt** — was genau das SOLL von
  # A-37-18 ist. *Damit schaltet der Transport die Sperre scharf, und wieder ist es eine Messung
  # und kein Datum.*
  #
  # ## ⚠ SIE HAT GEZUENDET — 16.08. abends, ohne dass jemand eine Zeile angefasst hat
  #
  # ```text
  #   16:17   Integrator startet          Haelfte 1 erfuellt
  #   danach  Tor in 3 von 6 Zweigen      HINWEIS „NOCH NICHT scharf"      exit 0
  #           Tor in 4, dann 5 von 6      unveraendert HINWEIS             exit 0
  #   20:0x   plan-pruefer holt den
  #           Integrationszweig -> 6/6    VERSTOSS „EINEN Schreiber"       exit 1
  # ```
  #
  # ***Zweimal an einem Tag hat sich eine Bedingung selbst umgelegt*** — *bei Haelfte 1 der erste
  # Integrator-Commit, bei Haelfte 2 der letzte fehlende Zweig.* **Waere eine der beiden ein Datum
  # oder ein Haekchen gewesen, haette jemand daran denken muessen: zu frueh, oder vergessen.**
  #
  # *Und es war nie Arbeit, die fehlte* — **es war eine Transport-Etappe.**
  TOR_ZWEIGE=0
  TOR_MIT=0
  for _z in $(git for-each-ref --format='%(refname:short)' 'refs/heads/rolle/*' 'refs/heads/auto/hausplaner-integration' 2>/dev/null); do
    TOR_ZWEIGE=$((TOR_ZWEIGE + 1))
    if [ -n "$(git ls-tree -r --name-only "$_z" scripts/rollen-tor.sh 2>/dev/null)" ]; then
      TOR_MIT=$((TOR_MIT + 1))
    fi
  done

  if [ -n "$INTEGRATOR_DA" ] && [ "$TOR_ZWEIGE" -gt 0 ] && [ "$TOR_MIT" -lt "$TOR_ZWEIGE" ]; then
    echo "ROLLEN-TOR  HINWEIS  '$ROLLE' aendert $GESCHUETZT — die Sperre ist NOCH NICHT scharf." >&2
    echo "            Das Tor liegt in $TOR_MIT von $TOR_ZWEIGE Zweigen. Solange es fehlt, wuerde die" >&2
    echo "            Sperre NUR die Baeume binden, die sie haben — und die uebrigen schrieben weiter." >&2
    echo "            Sie zuendet, sobald der Transport das Tor ueberall hingebracht hat (A-37-18)." >&2
  elif [ -n "$INTEGRATOR_DA" ]; then
    echo "ROLLEN-TOR  VERSTOSS  '$ROLLE' aendert $GESCHUETZT ausserhalb des Integrations-Checkouts." >&2
    echo "            Statuswahrheit und Befundnotizen haben EINEN Schreiber: den Integrator." >&2
    echo "            gefunden: $VERZ auf $ZWEIG" >&2
    [ "$NUR_MELDEN" = "1" ] && exit 0
    exit 1
  fi
  # Diese Meldung gilt NUR fuer den Fall ohne Integrator. Beim Umbau auf die zweiteilige
  # Zuendbedingung fiel sie zunaechst mit durch und behauptete „noch KEIN Integrator gestartet",
  # waehrend es seit 16:17 einen gibt — vom ersten Lauf gefangen, nicht vom Nachdenken.
  if [ -z "$INTEGRATOR_DA" ]; then
    echo "ROLLEN-TOR  HINWEIS  '$ROLLE' aendert $GESCHUETZT — noch KEIN Integrator gestartet." >&2
    echo "            Durchgelassen: die Sperre zuendet erst, wenn ein Schreiber existiert." >&2
    echo "            Bis dahin divergiert die Statuswahrheit je Zweig." >&2
  fi
fi

# ── A-37-23 — DER DIRIGENT BEKOMMT EINEN BEREICH, KEINEN SCHLUESSEL ────────────────
#
# **Eine Rolle bekannt zu machen, ohne ihren Bereich zu begrenzen, tauscht eine Sperre gegen ein
# Loch.** Vor diesem Bau wies das Tor `dirigent` mit „unbekannte Rolle" ab — *der Dirigent konnte
# in seinem eigenen Baum nicht committen*, ein Schutz-Rot. Die K2-Zeile oben behebt das.
# **Haette der Bau dort geendet, haette derselbe Handgriff ihm `docs/STATUS.md` geoeffnet** — die
# Datei, die Teil 2 dieses Tores gegen alle uebrigen Rollen verteidigt.
#
# **Erlaubt ist eine Positivliste**, nicht eine Verbotsliste: `docs/konzept/`, `docs/regelwerk/`
# und die Steuerungsblaetter unter `docs/auftraege/`. **Alles uebrige wird abgewiesen** —
# Produktcode unter `app/` und `resources/` ebenso wie `docs/STATUS.md` und
# `docs/BEFUNDNOTIZEN.md`. *Eine Verbotsliste haette jede kuenftige Datei stillschweigend
# erlaubt; sie waechst nicht mit dem Bestand mit, die Positivliste schon.*
#
# **Das Tor kannte bis hierher keine Pfade** — Teil 2 bekam nur die Ja/Nein-Auskunft
# `TOR_STATUS_PFAD`. Fuer eine Bereichsgrenze reicht das nicht, deshalb reicht `commit-pruefen.sh`
# die Liste jetzt zeilenweise als `TOR_PFADE` herein. *Zeilenweise und nicht per Wortzerlegung:
# ein Pfad mit Leerzeichen zerfaellt sonst in zwei, und beide Haelften waeren unerlaubt.*
# **Fuer die sechs uebrigen Rollen aendert sich nichts** — sie lesen die Variable nicht.
#
# ## ⚠ GEMESSEN UND HIER BENANNT: diese Grenze haette `5c9afbc7` abgewiesen
#
# ```text
#   git show --name-only --format='' 5c9afbc7
#     docs/auftraege/GESAMTAUFTRAG-V2-FORTSCHRITTSWAHRHEIT-2026-08-21.md   erlaubt
#     docs/auftraege/generator-auftrag-z0-i1-testdatenbank-isolation.md    erlaubt
#     scripts/rollen-tor.sh                                                ABGEWIESEN
# ```
#
# **`5c9afbc7` ist der Vorgriff, den dasselbe Kriterium mir woertlich zu uebernehmen auftraegt**
# — und sein dritter Pfad faellt durch die Grenze, die dasselbe Kriterium mich bauen heisst.
# *Das ist kein Widerspruch im Bau, sondern einer im Auftrag:* die Positivliste nennt kein
# `scripts/`, und der Dirigent hat genau dort gebaut. **Ich setze den Wortlaut um und ersetze ihn
# nicht still** — ob `scripts/` in die Liste gehoert, ist eine Entscheidung ueber den Bereich
# einer Rolle und liegt beim Evaluator, nicht beim Bauenden.
if [ "$STAMM" = "dirigent" ] && [ -n "${TOR_PFADE:-}" ]; then
  TOR_UNERLAUBT=""
  while IFS= read -r _p; do
    [ -z "$_p" ] && continue
    case "$_p" in
      docs/konzept/*|docs/regelwerk/*|docs/auftraege/*) ;;
      *) TOR_UNERLAUBT="$TOR_UNERLAUBT$_p"$'\n' ;;
    esac
  done <<< "$TOR_PFADE"
  if [ -n "$TOR_UNERLAUBT" ]; then
    echo "ROLLEN-TOR  VERSTOSS  Rolle '$ROLLE' schreibt ausserhalb ihres Bereichs." >&2
    echo "            Erlaubt sind nur: docs/konzept/  docs/regelwerk/  docs/auftraege/" >&2
    while IFS= read -r _p; do
      [ -n "$_p" ] && echo "            abgewiesen: $_p" >&2
    done <<< "$TOR_UNERLAUBT"
    [ "$NUR_MELDEN" = "1" ] && exit 0
    exit 1
  fi
fi

# ── A-37-12 bis A-37-16 — der MODULSTAND ────────────────────────────────────────────────────
#
# **Warum die Pruefung HIER steht und nicht weiter unten:** ab dieser Zeile gibt es nur noch
# Ausgaenge, die DURCHLASSEN (K3 :188, Zweig stimmt :214, K6 :235). Wer die Pruefung hinter einen
# davon setzt, hat sie fuer die anderen zwei nicht gebaut. **Genau diesen Fehler habe ich bei
# Teil 2 schon einmal gemacht** — der Block stand hinter dem `exit 0`, den der Fall zuerst traf,
# und war damit unerreichbar. Einmal reicht.
#
# **Was verglichen wird:** der Hash von `package-lock.json` gegen Feld 2 der Marke. NICHT
# `node_modules/.package-lock.json` — das ist npms eigene Buchfuehrung und fuehrt planmaessig
# weniger Pakete (am 16.08. gemessen: 465 gegen 404). Wer die vergleicht, misst eine Differenz,
# die immer da ist, und schaltet die Pruefung nach dem dritten Fehlalarm ab (A-03).
#
# ## ⚠ DER DRITTE FALL MELDET, ER SPERRT NICHT — und das ist eine Entscheidung, keine Nachlaessigkeit
#
# **A-37-13 verlangt woertlich „Rueckgabe ≠ 0" — aber nur fuer den FALSCHEN Stand.** Fuer die
# FEHLENDE Marke verlangt A-37-14 eine eigene Meldung und dass sie *„nicht stillschweigend als
# gueltig behandelt"* wird. **Einen Rueckgabewert nennt die Tabelle fuer diesen Fall nicht.**
#
# Ich erfinde keinen. *Das ist dieselbe Zurueckhaltung wie bei K4 oben* — und sie hat hier eine
# zweite, gemessene Begruendung: **heute hat KEIN Baum eine Marke** (16.08. nachgesehen). Eine
# Sperre haette in dem Augenblick, in dem sie eingehaengt wird, alle fuenf Rollen vom Commit
# ausgeschlossen, bevor auch nur einer den Schreiber gefahren hat. **Eine Sperre, die vor ihrem
# Ersatzweg kommt, haelt die Kette an, statt sie zu schuetzen** — derselbe Satz wie bei K3, K6 und
# Teil 2, und zum vierten Mal derselbe Fehler waere keiner mehr, sondern Absicht.
#
# **Gemeldet und nicht still entschieden:** wer will, dass der halb installierte Baum SPERRT,
# gibt A-37-14 einen Rueckgabewert. Dann ist es eine Zeile hier.
# ⚠ Diese Funktion stand zuerst MITTEN im if/elif-Block darunter — und wurde damit nur
# definiert, wenn der erste Zweig lief. Der elif rief sie dann ins Leere:
# "marke_feld: command not found", und das Tor gab exit 6 fuer eine Marke, die stimmte.
# Vom eigenen Lauf gefangen, nicht vom Nachdenken — dieselbe Klasse wie der Block, der
# im Kopf dieser Datei einmal hinter einem exit 0 lag und deshalb unerreichbar war.
marke_feld() {
  # $1 = Feldname, $2 = Datei. Leer, wenn der Name fehlt. awk trennt bei Whitespace-FOLGEN,
  # nicht bei jedem einzelnen Zeichen — genau der Unterschied zu cut.
  awk -v n="$1" '{for (i=1; i<NF; i++) if ($i == n) { print $(i+1); exit }}' "$2" 2>/dev/null
}

MARKE="$BAUM/node_modules/.aus-lockfile"
LOCKDATEI="$BAUM/package-lock.json"
if [ -f "$LOCKDATEI" ]; then
  LOCK_HASH="$(git -C "$BAUM" hash-object "$LOCKDATEI" 2>/dev/null)"
  if [ ! -f "$MARKE" ]; then
    if [ ! -d "$BAUM/node_modules" ]; then
      echo "ROLLEN-TOR  HINWEIS  MODULSTAND UNBEKANNT — in $VERZ ist gar kein node_modules." >&2
    else
      # Der gefaehrliche der beiden: es LIEGT etwas da. Es sieht vollstaendig aus, es hat Tausende
      # Dateien, und niemand weiss, ob `npm ci` durchgelaufen ist oder auf halbem Weg abbrach.
      echo "ROLLEN-TOR  HINWEIS  MODULSTAND UNBEKANNT — $VERZ hat Module, aber keine Marke." >&2
      echo "            Ein abgebrochenes 'npm ci' hinterlaesst genau dieses Bild." >&2
    fi
    echo "            Marke schreiben: bash scripts/module-nachziehen.sh  (faehrt npm ci)" >&2
    echo "            Durchgelassen und NICHT als gueltig verbucht — s. Kopf dieser Datei." >&2
# ── DIE MARKE WIRD UEBER DEN FELDNAMEN GELESEN, NICHT UEBER DIE POSITION ───────────────────
#
# **Gefunden beim Gegenpruefen von A-37-13, und der Befund stand in der eigenen Meldung:**
# die Negativprobe gab aus `geschrieben:    zeit` — also den FELDNAMEN statt des Zeitstempels.
#
# **Gemessen, Feld fuer Feld:**
# ```text
#   Marke:  hash <sha>  zeit <stempel>  node <v>  npm <v>
#   cut -f2 -> <sha>        f3 -> (leer)     f4 -> zeit     f5 -> <stempel>
# ```
# *Die Marke trennt ihre Felder mit ZWEI Leerzeichen, und `cut -d' '` zaehlt jedes einzeln.*
# **Jedes Feld ab dem zweiten Trenner liegt um eins daneben** — `-f2` traf nur zufaellig, weil
# davor ein einfaches Leerzeichen steht.
#
# ***A-37-15 verlangt die vier Feldnamen ausdruecklich.*** **Dann ist der Name auch der richtige
# Zugriff.** Eine Position zaehlt Trennzeichen; ein Name liest, was dasteht. *Wer Feldnamen
# schreibt und dann nach Position liest, hat die Namen umsonst.*
#
# **Nicht behoben, weil ausserhalb meiner erlaubten Pfade und deshalb GEMELDET:**
# `scripts/module-nachziehen.sh:142` liest `cut -d' ' -f2` fuer den Hash — dieselbe Bauform,
# heute richtig aus demselben Zufall. Sie bricht, sobald jemand die Feldfolge aendert.

  elif [ "$(marke_feld hash "$MARKE")" != "$LOCK_HASH" ]; then
    echo "ROLLEN-TOR  MODULSTAND  die Module in $VERZ gehoeren nicht zu diesem package-lock.json." >&2
    echo "            Lockfile jetzt: $LOCK_HASH" >&2
    echo "            Marke sagt:     $(marke_feld hash "$MARKE")" >&2
    echo "            geschrieben:    $(marke_feld zeit "$MARKE")" >&2
    echo "            Abhilfe: npm ci in diesem Baum — bash scripts/module-nachziehen.sh" >&2
    echo "            Ein Lauf auf fremden Modulen ist gruen oder rot aus Gruenden, die nicht" >&2
    echo "            im Code stehen. Das ist die Sorte Fehler, die beim naechsten Mal weg ist." >&2
    [ "$NUR_MELDEN" = "1" ] && exit 0
    exit 6
  fi
fi

# K3: wer noch nicht umgezogen ist, wird DURCHGELASSEN und gemeldet. Der Umzug ist freiwillig
# getaktet — ein Tor, das ihn erzwingt, haelt die Kette an, statt sie zu schuetzen.
SOLL_PFAD="$(dirname "$BAUM")/$SOLL_VERZ"
if [ ! -d "$SOLL_PFAD" ]; then
  echo "ROLLEN-TOR  HINWEIS  '$ROLLE' hat noch keinen eigenen Baum ($SOLL_VERZ) — durchgelassen (K3)." >&2
  echo "            gefunden: $VERZ auf $ZWEIG" >&2
  exit 0
fi

# DER ZWEIG ENTSCHEIDET, das Verzeichnis wird nur GEMELDET.
#
# **Gemessen am 16.08., nachdem der Plan-Pruefer den Transporteur gesperrt fand:** der
# Release-Pruefer arbeitet in `ticket-release-pruefung`, meine Tabelle nannte
# `ticket-rolle-release` — dort liegt nur noch ein detached HEAD. Mein Tor gab ihm exit 1, und er
# ist der, der den Transport faehrt.
#
# **Warum der Zweig und nicht das Verzeichnis:** *git erzwingt, dass ein Zweig in HOECHSTENS EINEM
# Worktree ausgecheckt ist* — die Gegenprobe gefahren, `git worktree add` auf einen belegten Zweig
# scheitert mit „already used by worktree at …". **Damit IST der Zweig die Zuordnung**; der
# Verzeichnisname ist eine Setup-Entscheidung, die sich umbenennen laesst, ohne dass jemand es
# merkt. Ein Tor, das am Namen haengt, sperrt beim naechsten Umbenennen wieder.
#
# **Die vorgeschlagene Abhilfe war eine Zeile** — die Tabelle auf `ticket-release-pruefung` ziehen.
# *Sie behebt den Fall und nicht die Klasse.* **Diese hier behebt die Klasse und meldet die
# Abweichung trotzdem**, damit niemand sie fuer beabsichtigt haelt.
if [ "$ZWEIG" = "$SOLL_ZWEIG" ]; then
  if [ "$VERZ" != "$SOLL_VERZ" ]; then
    echo "ROLLEN-TOR  HINWEIS  '$ROLLE' ist auf ihrem Zweig, aber in einem anderen Verzeichnis." >&2
    echo "            erwartet laut Tabelle: $SOLL_VERZ" >&2
    echo "            gefunden:              $VERZ  auf  $ZWEIG" >&2
    echo "            Durchgelassen: der Zweig ist eindeutig (git laesst ihn nur EINMAL auschecken)." >&2
  fi
  exit 0
fi

# K6: die Rolle arbeitet im GEMEINSAMEN Checkout, und ihr eigener Baum STEHT bereits.
#
# **Der Fall, der meinen ersten Bau durchfallen liess, und er ist ein eigener — keine Variante
# von K3.** K3 greift nur, wenn das Verzeichnis FEHLT; steht es, fiel der Fall bis hierher in den
# Schlusszweig und wurde zum Verstoss. Der Plan-Pruefer hat es am gebauten Skript belegt:
# release-pruefer und evaluator bekamen exit 1, und weil das Tor eingehaengt ist, hiess das
# KEIN COMMIT — fuer genau die zwei Rollen, die den Transport und die Abnahme trugen.
#
# **Die Begruendung ist dieselbe wie bei K3, und sie steht schon oben:** ein Tor, das den Umzug
# ERZWINGT, haelt die Kette an, statt sie zu schuetzen. Der Umzug ist freiwillig getaktet, und
# dass ein Baum schon dasteht, ist kein Umzug.
#
# **Was danach noch SPERRT, bleibt scharf:** eine Rolle im Baum einer FREMDEN Rolle. Das ist der
# Schaden, gegen den die Umstellung gebaut wird, und er faellt unten durch.
if [ "$VERZ" = "$INTEGRATION_VERZ" ] && [ "$ZWEIG" = "$INTEGRATION_ZWEIG" ]; then
  echo "ROLLEN-TOR  HINWEIS  '$ROLLE' arbeitet im gemeinsamen Checkout — durchgelassen (K6)." >&2
  echo "            ihr eigener Baum steht bereits und wartet: $SOLL_VERZ auf $SOLL_ZWEIG" >&2
  echo "            gefunden: $VERZ auf $ZWEIG" >&2
  exit 0
fi

# Der Verstoss nennt BEIDE Werte. Eine Meldung, die nur das Erwartete nennt, zwingt den Leser
# zum Nachmessen — und genau dann wird sie weggeklickt.
echo "ROLLEN-TOR  VERSTOSS  Rolle '$ROLLE' arbeitet im falschen Baum." >&2
echo "            erwartet: $SOLL_VERZ  auf  $SOLL_ZWEIG" >&2
echo "            gefunden: $VERZ  auf  $ZWEIG" >&2
echo "            Ein Commit im falschen Baum landet auf einem fremden Zweig und faellt niemandem auf." >&2
if [ "$NUR_MELDEN" = "1" ]; then
  exit 0
fi
exit 1
