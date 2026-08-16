#!/usr/bin/env bash
# ── A-33 · DIE TAFELZEILEN AUF DIE KENNUNG IHRES DATENSATZES ZIEHEN ──────────────────────────
#
# **Was es tut.** Elf Tafelzeilen in `docs/STATUS.md` tragen ihre Kennung VERKUERZT (`W-01`),
# waehrend ihr Datensatz die volle Form fuehrt (`W-01/1`). Die Ball-Drift-Barriere
# (`a26-ball-drift.sh`) liest die Kennung woertlich — richtig so, denn eine Kennung, die geraten
# werden muss, ist keine Kennung — und findet zu diesen Zeilen keinen Datensatz. Sie sprang
# frueher still weiter; seit A-30 meldet sie es. **A-33 raeumt die Faelle weg, die sie meldet.**
#
# ---
#
# ## Warum ein SKRIPT und keine Bearbeitung — Yamas Entscheidung vom 15.08.
#
# *Die urspruengliche Fassung war eine Bearbeitung: elf Zeilen von Hand aendern.* **Drei Gruende
# dagegen, und alle drei sind gemessen:**
#
# 1. **`docs/STATUS.md` hat GENAU EINEN Schreiber, den Integrator.** Eine Rolle liefert ihre
#    Aenderung als eigenen Commit. Ein Skript unter `scripts/` liegt im Gebiet des Generators; der
#    Integrator FUEHRT es aus. Damit bleibt der einzige Schreiber der einzige Schreiber.
# 2. **Ein Diff ueber elf Zeilen in der meistumkaempften Datei des Hauses kollidiert.** Zwischen
#    Commit und Uebernahme schreiben vier Rollen ihre Zustandswechsel in dieselbe Tafel, und die
#    elf Zeilen stehen dann nicht mehr dort, wo der Diff sie erwartet. **Ein Skript erzeugt sein
#    Ergebnis aus dem JEWEILS AKTUELLEN Stand** — ein Konflikt ist damit unmoeglich.
# 3. **Die Zielzahl im alten Kriterium war eine fluechtige Messung ohne Zeitstempel.** Sie ist
#    einmal abgelaufen (`A-06` hat seit `086b48bd` einen Datensatz, der Rest waere null statt eins),
#    und ein groeberes Kriterium liefert eine dritte Zahl. **Statt einer Zahl steht jetzt eine
#    INVARIANTE:** nach dem Lauf gibt es keine verkuerzte Tafelzeile mehr, deren Datensatz die volle
#    Form traegt — und ein zweiter Lauf meldet null.
#
# ---
#
# ## Die Zuordnung wird aus den DATEN abgeleitet, nicht aus einer Liste
#
# ```text
# 1  Traegt der Stamm SELBST einen Datensatz?      -> EIGENER AUFTRAG, unberuehrt.
#                                                     So fallen W-27 und W-40 heraus: dort sind
#                                                     beide Formen eigene Auftraege, und ein
#                                                     Zusammenziehen wuerde einen LOESCHEN.
# 2  Genau EIN Kandidat K/x ?                      -> darauf ziehen.
# 3  MEHRERE Kandidaten ?                          -> nur auf K/1, und nur wenn JEDER andere
#                                                     Kandidat bereits eine EIGENE Tafelzeile hat.
#                                                     Das ist der Fall bei W-05 und W-21, deren
#                                                     /2-Zeilen ihren Suffix schon tragen.
# 4  Sonst                                          -> UNKLAR melden, nicht anfassen.
# ```
#
# > ***Regel 3 ist der Riegel gegen das Raten.*** *Ohne sie muesste das Skript bei zwei Kandidaten
# > waehlen. Mit ihr zieht es nur dort, wo die Tafel selbst schon zeigt, dass der andere Kandidat
# > anderswo gefuehrt wird* — **die Entscheidung steht in den Daten, nicht im Skript.**
#
# ## Aufruf
#
# ```
#   bash scripts/a33-kennungen-nachziehen.sh              schreibt
#   bash scripts/a33-kennungen-nachziehen.sh --trocken    meldet nur, schreibt nicht
#   bash scripts/a33-kennungen-nachziehen.sh --fangprobe  prueft das Muster gegen drei Faelle
# ```
#
# **Rueckgabe:** 0 wenn nichts zu tun war oder alles gezogen wurde · 1 bei unklaren Faellen.
set -uo pipefail
cd "$(dirname "$0")/.."

DATEI="docs/STATUS.md"
MODUS="schreiben"
while [ "$#" -gt 0 ]; do
  case "$1" in
    --trocken)   MODUS="trocken"; shift ;;
    --fangprobe) MODUS="fangprobe"; shift ;;
    --datei)     DATEI="${2:-}"; shift 2 ;;
    *) echo "Unbekanntes Argument: $1" >&2; exit 2 ;;
  esac
done

[ -f "$DATEI" ] || { echo "FEHLT      $DATEI" >&2; exit 2; }

node - "$DATEI" "$MODUS" <<'JS'
const { readFileSync, writeFileSync } = require("fs");
const datei = process.argv[2];
const modus = process.argv[3];

// Das Kennungsmuster. `[A-Z]+` weil es B5, B6, B7 gibt; `-?` weil W-01 und B5 beide vorkommen;
// der Suffix ist optional. Das schliessende `**` MUSS unmittelbar folgen — daran scheitert
// `| **M-02-Kopienzahl** |` aus einer Befundtabelle, und genau das soll es.
const TAFEL = /^\| \*\*([A-Z]+-?[0-9]+[A-Za-z]?(?:\/[0-9A-Za-z]+)?)\*\*/;
const DATENSATZ = /^auftrag: "([^"]+)"/;

if (modus === "fangprobe") {
  // Pruefung 7, fuenfter Schritt: das Muster wird gegen drei Faelle gehalten, BEVOR es zaehlt.
  // Die drei stehen so im Auftragsblatt — zwei davon haben frueheren Auftraegen eine DoR gekostet.
  const faelle = [
    ["| **W-21** Sparren und Lattung | …",        true,  "muss treffen"],
    ["| **P-02** parallele Instanzen | …",        true,  "muss treffen (und faellt spaeter durch Regel 1 raus)"],
    ["| **M-02-Kopienzahl** | drei |",            false, "darf NICHT treffen — Zeile aus einer Befundtabelle"],
    ["| **W-05/2** Raum anwaehlen | …",           true,  "muss treffen (traegt Suffix schon)"],
    ["|  **W-01** falsch eingerueckt | …",        false, "darf NICHT treffen — kein Tafelzeilen-Anfang"],
  ];
  let rot = 0;
  for (const [zeile, erwartet, warum] of faelle) {
    const ist = TAFEL.test(zeile);
    const ok = ist === erwartet;
    if (!ok) rot++;
    console.log(`  ${ok ? "✔" : "✖"} ${erwartet ? "trifft" : "trifft nicht"}  ${warum}`);
    console.log(`      ${zeile}`);
  }
  console.log(`\n  Fangprobe: ${faelle.length - rot}/${faelle.length} wie erwartet`);
  process.exit(rot ? 1 : 0);
}

const roh = readFileSync(datei, "utf8");
const zeilen = roh.split("\n");

// Beide Seiten EINMAL einlesen. Die Tafelseite merkt sich ALLE Zeilennummern je Kennung,
// weil eine Kennung mehrfach in der Tafel stehen kann (Archiv-Abschnitte).
const tafel = new Map();
const daten = new Set();
zeilen.forEach((l, i) => {
  const t = TAFEL.exec(l);
  if (t) {
    if (!tafel.has(t[1])) tafel.set(t[1], []);
    tafel.get(t[1]).push(i);
  }
  const d = DATENSATZ.exec(l);
  if (d) daten.add(d[1]);
});

const ziehen = [];
const unklar = [];
for (const [kennung, nummern] of [...tafel.entries()].sort()) {
  if (kennung.includes("/")) continue;          // traegt ihren Suffix schon
  if (daten.has(kennung)) continue;             // Regel 1: eigener Auftrag
  const kandidaten = [...daten].filter((d) => d.startsWith(kennung + "/")).sort();
  if (kandidaten.length === 0) {
    unklar.push([kennung, nummern, "kein Datensatz — das ist eine andere Frage (A-30 macht sie sichtbar)"]);
  } else if (kandidaten.length === 1) {
    ziehen.push([kennung, kandidaten[0], nummern]);
  } else {
    const eins = kennung + "/1";
    const uebrige = kandidaten.filter((k) => k !== eins);
    if (kandidaten.includes(eins) && uebrige.every((k) => tafel.has(k))) {
      ziehen.push([kennung, eins, nummern]);    // Regel 3
    } else {
      unklar.push([kennung, nummern, `mehrere Kandidaten (${kandidaten.join(", ")}) und nicht jeder hat eine eigene Tafelzeile`]);
    }
  }
}

for (const [kennung, ziel, nummern] of ziehen) {
  for (const i of nummern) {
    // NUR die Kennung. Der Klartext dahinter bleibt wortgleich — deshalb wird genau der
    // eine Treffer am Zeilenanfang ersetzt und nicht global.
    zeilen[i] = zeilen[i].replace(`| **${kennung}**`, `| **${ziel}**`);
  }
}

const zahl = ziehen.reduce((s, [, , n]) => s + n.length, 0);
console.log(`A-33  ${datei}`);
console.log(`  Tafelzeilen ${tafel.size} Kennungen · Datensaetze ${daten.size}`);
if (ziehen.length === 0) {
  console.log(`  NACHZUZIEHEN: keine — die Invariante haelt bereits.`);
} else {
  console.log(`  NACHZUZIEHEN: ${zahl} Zeile(n), ${ziehen.length} Kennung(en)${modus === "trocken" ? "  (TROCKEN — nichts geschrieben)" : ""}`);
  for (const [k, ziel, n] of ziehen) console.log(`    Z.${n.join(",")}  ${k}  ->  ${ziel}`);
}
if (unklar.length) {
  console.log(`  UNKLAR, NICHT angefasst: ${unklar.length}`);
  for (const [k, n, grund] of unklar) console.log(`    Z.${n.join(",")}  ${k}  —  ${grund}`);
}

if (modus !== "trocken" && ziehen.length) {
  writeFileSync(datei, zeilen.join("\n"));
  console.log(`  GESCHRIEBEN.`);
}
process.exit(unklar.length ? 1 : 0);
JS
