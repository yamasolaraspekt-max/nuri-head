/**
 * AUF-87 — **der Validator: ein Blatt, dessen Prüfbefehle ins Leere greifen, ist nicht abgebbar.**
 *
 * Er liest den YAML-Kopf eines Auftragsblatts, fährt jeden darin genannten Prüfbefehl und meldet,
 * welcher fehlschlägt oder nichts findet. **Er prüft AUSFÜHRBARKEIT, nicht Richtigkeit** — ob ein
 * Kriterium fachlich stimmt, entscheidet weiterhin ein Mensch.
 *
 * ## Warum es drei Stufen sind und nicht zwei
 *
 * `grep -c` liefert bei null Treffern `exit 1` — das fällt auf. `sed -n '9999,9999p'` liefert bei
 * einer zu kurzen Datei **nichts bei `exit 0`** — und das sieht aus wie Erfolg. **Der zweite Fall
 * ist der gefährlichere**, und genau so ist dem Planner die Grundgesamtheit von T3 durchgerutscht:
 * der Befehl lief, er beschrieb nur einen Stand von vor vier Tagen.
 *
 * ## Warum eine Denylist
 *
 * Ein Auftragsblatt ist eine Datei, die jede Rolle schreiben darf. Ein Werkzeug, das ihre Inhalte
 * ungefiltert an die Shell gibt, ist ein Hebel, den niemand beabsichtigt hat. *Die Blätter sind
 * heute vertrauenswürdig; das Werkzeug soll es auch dann noch sein, wenn eines es nicht ist.*
 * **Übersprungene Befehle werden gemeldet, nicht verschwiegen.**
 *
 * ## Was er NICHT tut
 *
 * Er repariert nichts. Findet er in einem alten Blatt einen toten Befehl, sagt er es — **sonst
 * wäre der erste Lauf ein Massenumbau von 80 Dateien.**
 */
import { readFileSync } from 'node:fs';
import { execSync } from 'node:child_process';
import yaml from 'js-yaml';

/** Die Meldungsstufen. Reihenfolge = Dringlichkeit, sie bestimmt auch die Zusammenfassung. */
export const STUFEN = /** @type {const} */ ({
  FEHLSCHLAG: 'FEHLSCHLAG',
  NULLTREFFER: 'NULLTREFFER',
  VERDAECHTIG: 'VERDAECHTIG',
  UEBERSPRUNGEN: 'UEBERSPRUNGEN',
  NICHT_MASCHINELL: 'NICHT MASCHINELL',
  OK: 'OK',
});

/**
 * Muster, die einen Befehl vom Ausführen ausschließen.
 *
 * **Sie stehen hier und nicht im Blatt**: eine Liste, die der Prüfling selbst mitbringt, ist keine
 * Grenze. Erweitert wird sie bewusst, nicht nebenbei.
 */
export const DENYLIST = [
  'git commit', 'git push', 'git add', 'git reset', 'git checkout', 'git switch',
  'rm', 'mv', 'chmod', 'truncate', 'dd', 'tee',
  'umleitung', 'npm run build', 'npx vite build', 'curl', 'wget',
];

/**
 * Wie ein Muster erkannt wird — **als Wortform, nicht als Zeichenkette.**
 *
 * **Befund `AUF-87-B2` (P2), und er war berechtigt:** die erste Fassung suchte mit
 * `befehl.includes('rm ')`. Neun realistische Schreibweisen schlüpften durch — `echo x >datei`
 * (Umleitung ohne Leerzeichen), `rm<TAB>-rf`, `git  commit` mit zwei Leerzeichen, und
 * **`git switch`**, der moderne Ersatz für das gelistete `git checkout`.
 *
 * Deshalb steht hier je Muster ein Ausdruck statt einer Zeichenkette:
 * - Wortmuster (`rm`, `mv`, `dd`) greifen nur als **eigenes Wort** — `rm` fängt `rm -rf`,
 *   aber nicht `npm` oder `format`.
 * - Zwei-Wort-Muster erlauben **beliebigen Zwischenraum** — `git  commit` ist `git commit`.
 * - `umleitung` ist kein Wort, sondern der Fall `>` / `>>` in jeder Schreibweise.
 */
function musterAusdruck(muster) {
  if (muster === 'umleitung') return />>?/;
  const teile = muster.split(' ');
  if (teile.length > 1) return new RegExp(`\\b${teile.join('\\s+')}\\b`);
  return new RegExp(`(^|[\\s;&|(])${muster}([\\s;&|)]|$)`);
}

/** Der erste `​```yaml`-Block einer Datei — der Kopf. */
export function lieseKopfRoh(text) {
  return lieseAlleBloecke(text)[0] ?? null;
}

/**
 * **ALLE** `​```yaml`-Blöcke einer Datei, in Reihenfolge.
 *
 * **Befund `AUF-87-N2 / K-06`, und er kam aus meinem eigenen ersten Bestandslauf:** die erste
 * Fassung las nur den Kopf und meldete *„2 yaml-Blöcke, geprüft wurde der ERSTE"*. Ehrlich, aber
 * folgenlos — **die ganze R19-Umstellung des Planners steht in zweiten Blöcken** (`measurements:`),
 * und für den Validator war sie unsichtbar. *Ein Werkzeug, das die Hälfte nicht sieht, meldet
 * Vollständigkeit, die es nicht hat.*
 */
export function lieseAlleBloecke(text) {
  const zeilen = text.split('\n');
  const bloecke = [];
  let start = -1;
  for (let i = 0; i < zeilen.length; i += 1) {
    const z = zeilen[i].trim();
    if (start < 0 && z === '```yaml') { start = i; continue; }
    if (start >= 0 && z === '```') { bloecke.push(zeilen.slice(start + 1, i).join('\n')); start = -1; }
  }
  return bloecke;
}

/** Wie viele YAML-Blöcke die Datei überhaupt hat — für die Meldung, dass es mehr als einen gibt. */
export function zaehleBloecke(text) {
  return text.split('\n').filter((z) => z.trim() === '```yaml').length;
}

/**
 * Alle Prüfbefehle eines Kopfes — **als MENGE, nicht nach Muster.**
 *
 * Gesammelt werden `scope.population_command` **und** jedes `kriterien[].pruefung.befehl`. Wer nur
 * nach einem Muster sucht, findet den nächsten neuen Ort nicht — das ist F-01, die Klasse mit vier
 * Ausprägungen.
 */
export function sammleBefehle(kopf) {
  const gefunden = [];
  const auftrag = kopf?.auftrag ?? kopf ?? {};
  const scope = kopf?.scope ?? auftrag?.scope;
  if (typeof scope?.population_command === 'string' && scope.population_command.trim()) {
    gefunden.push({ id: 'scope.population_command', befehl: scope.population_command.trim(), typ: null });
  }
  const kriterien = kopf?.kriterien ?? auftrag?.kriterien ?? [];
  for (const k of Array.isArray(kriterien) ? kriterien : []) {
    const id = k?.id ?? '(ohne id)';
    const p = k?.pruefung ?? {};
    if (typeof p.befehl === 'string' && p.befehl.trim()) {
      gefunden.push({ id, befehl: p.befehl.trim(), typ: p.typ ?? null, ausgefuehrt_von: k?.ausgefuehrt_von ?? null });
    } else if (p.typ && p.typ !== 'befehl') {
      // Kein Befehl, aber eine Prüfung: der Validator darf NICHT so tun, als hätte er das geprüft.
      gefunden.push({ id, befehl: null, typ: p.typ, ausgefuehrt_von: k?.ausgefuehrt_von ?? null });
    }
  }
  return gefunden;
}

/** Steht der Befehl auf der Denylist? Liefert das Muster, damit die Meldung den Grund nennt. */
export function verbotenesMuster(befehl) {
  return DENYLIST.find((m) => musterAusdruck(m).test(befehl)) ?? null;
}

/** Einen einzelnen Eintrag bewerten — ohne zu werfen. */
export function pruefeEintrag(eintrag, arbeitsverzeichnis) {
  if (!eintrag.befehl) {
    return {
      ...eintrag, stufe: STUFEN.NICHT_MASCHINELL,
      hinweis: `typ: ${eintrag.typ}${eintrag.ausgefuehrt_von ? ` · ausgefuehrt_von: ${eintrag.ausgefuehrt_von}` : ''}`,
    };
  }
  const muster = verbotenesMuster(eintrag.befehl);
  if (muster) {
    return { ...eintrag, stufe: STUFEN.UEBERSPRUNGEN, hinweis: `enthaelt "${muster}"` };
  }
  try {
    const ausgabe = execSync(eintrag.befehl, {
      cwd: arbeitsverzeichnis, encoding: 'utf8', stdio: ['ignore', 'pipe', 'pipe'], timeout: 120_000,
    });
    if (ausgabe.trim() === '') {
      return { ...eintrag, stufe: STUFEN.VERDAECHTIG, hinweis: 'exit 0, aber KEINE Ausgabe' };
    }
    return { ...eintrag, stufe: STUFEN.OK, hinweis: `${ausgabe.trim().split('\n').length} Zeile(n)` };
  } catch (fehler) {
    const code = fehler?.status ?? '?';
    const kette = brechendesGlied(eintrag.befehl, arbeitsverzeichnis);
    if (code === 1 && kette) {
      return {
        ...eintrag, stufe: STUFEN.NULLTREFFER,
        hinweis: `\`${kette.glied}\` findet nichts (exit 1) — ${kette.rest} weitere(s) Glied(er) NICHT gelaufen`,
      };
    }
    return { ...eintrag, stufe: STUFEN.FEHLSCHLAG, hinweis: `exit ${code}` };
  }
}

/**
 * Welches Glied einer `&&`-Kette bricht, und wie viele danach nicht mehr laufen?
 *
 * **`AUF-87-N2 / K-07` — meine Wahl, im Voraus gemeldet.** Der T5-Befehl verkettet drei `grep`;
 * der letzte sucht `collapsed|klappZu|schieneZu` und findet **0** — genau das ist das gewünschte
 * Ergebnis, es beweist die Lücke. **`grep` liefert dafür exit 1 und reißt die Kette.**
 *
 * *Der Befehl ist richtig, sein Exitcode ist es nicht.* Die Alternativen hätten die Arbeit auf den
 * Menschen verschoben — `|| true` in 89 Blättern, oder ein Kopffeld, das vorher weiß, welches
 * Glied null liefert. **Das Werkzeug kann es selbst sehen.**
 *
 * **Die Unterscheidung bleibt sichtbar:** nur ein *suchender* Befehl mit exit 1 gilt als
 * Nulltreffer. `exit 127` (Befehl existiert nicht) und jeder andere Code bleiben `FEHLSCHLAG`.
 */
const SUCHENDE = /^\s*(grep|rg|ag|find)\b/;

export function brechendesGlied(befehl, arbeitsverzeichnis) {
  const glieder = befehl.split(/&&/).map((g) => g.trim()).filter(Boolean);
  if (glieder.length < 2) {
    // Kein Kettenfall: ein einzelner suchender Befehl mit exit 1 ist ebenfalls ein Nulltreffer.
    return SUCHENDE.test(befehl) ? { glied: befehl.slice(0, 60), rest: 0 } : null;
  }
  for (let i = 0; i < glieder.length; i += 1) {
    const glied = glieder[i];
    if (!SUCHENDE.test(glied)) continue;
    try {
      execSync(glied, { cwd: arbeitsverzeichnis, encoding: 'utf8', stdio: ['ignore', 'pipe', 'pipe'], timeout: 60_000 });
    } catch (f) {
      if (f?.status === 1) return { glied: glied.slice(0, 60), rest: glieder.length - i - 1 };
      return null;
    }
  }
  return null;
}

/**
 * **Die fünf Strukturprüfungen aus `AUFTRAGSSCHEMA §7` (AUF-87-N2).**
 *
 * `AUF-87` prüft AUSFÜHRBARKEIT — läuft der Befehl. Diese hier prüfen **STRUKTUR** — ob das Blatt
 * überhaupt so gebaut ist, dass eine Abnahme möglich wäre. *„Beides zusammen ergibt erst das Gate."*
 *
 * **Genau diese fünf, keine sechste.** Ein Meta-System, das jede Konvention erzwingt, wird
 * abgeschaltet; diese fünf sind an echten Rückweisungen belegt.
 */
export function strukturBefunde(kopf) {
  const befunde = [];
  const auftrag = kopf?.auftrag ?? kopf ?? {};
  const kriterien = kopf?.kriterien ?? auftrag?.kriterien ?? [];
  const liste = Array.isArray(kriterien) ? kriterien : [];

  // S-02: jedes Kriterium hat typ UND einen Befehl — oder ist manuell MIT Begründung.
  for (const k of liste) {
    const id = k?.id ?? '(ohne id)';
    const p = k?.pruefung ?? {};
    if (!k?.typ) befunde.push({ regel: 'S-02', id, text: 'kein `typ` am Kriterium' });
    const hatBefehl = typeof p.befehl === 'string' && p.befehl.trim();
    const istManuell = p.typ && p.typ !== 'befehl';
    const hatBegruendung = Boolean(k?.begruendung || k?.grenze || p?.schritte || p?.erwartet);
    if (!hatBefehl && !istManuell) {
      befunde.push({ regel: 'S-02', id, text: 'weder `pruefung.befehl` noch `pruefung.typ`' });
    } else if (!hatBefehl && istManuell && !hatBegruendung) {
      befunde.push({ regel: 'S-02', id, text: `manuell (\`${p.typ}\`) ohne Begruendung/Schritte` });
    }
  }

  // S-03: jedes P0/P1-`absence`-Kriterium braucht einen presence/behavioural-Partner.
  //
  // **Die Prüfung mit dem höchsten Wert.** Bei AUF-83-T2 waren K-01 bis K-04 `absence` mit einem
  // `grep`; der Evaluator hat die entfernte Navigation zurückgeholt — kein Test wurde rot.
  // *Ohne Partner hat man nicht aufgeräumt, sondern entfernt.*
  const hatPartner = liste.some((k) => k?.typ === 'presence' || k?.typ === 'behavioural');
  for (const k of liste) {
    const krit = String(k?.kritikalitaet ?? '');
    if (k?.typ === 'absence' && (krit === 'P0' || krit === 'P1') && !hatPartner) {
      befunde.push({ regel: 'S-03', id: k?.id ?? '(ohne id)', text: `absence ${krit} ohne presence/behavioural-Partner im Blatt` });
    }
  }

  // S-04: jedes `coverage`-Kriterium braucht eine Grundgesamtheit.
  const scope = kopf?.scope ?? auftrag?.scope;
  const hatPopulation = typeof scope?.population_command === 'string' && scope.population_command.trim();
  for (const k of liste) {
    if (k?.typ === 'coverage' && !hatPopulation && !k?.pruefung?.befehl) {
      befunde.push({ regel: 'S-04', id: k?.id ?? '(ohne id)', text: 'coverage ohne `population_command` und ohne eigenen Befehl' });
    }
  }

  // S-05: jeder Ausschluss nennt Grund UND Entscheider.
  const aus = scope?.ausschluesse ?? auftrag?.ausschluesse ?? [];
  for (const [i, a] of (Array.isArray(aus) ? aus : []).entries()) {
    const wo = a?.stelle ? `"${String(a.stelle).slice(0, 40)}"` : `#${i + 1}`;
    if (!a?.grund) befunde.push({ regel: 'S-05', id: wo, text: 'Ausschluss ohne `grund`' });
    if (!a?.entschieden_von) befunde.push({ regel: 'S-05', id: wo, text: 'Ausschluss ohne `entschieden_von`' });
  }
  return befunde;
}

/**
 * S-01 — **genau EIN Blatt trägt `status: aktiv`.**
 *
 * Diese Prüfung ist die einzige, die **über** ein Blatt hinausreicht; sie läuft deshalb über die
 * Menge der geprüften Dateien. **Der Beleg ist frisch:** am 30.07. trug die Auftragstafel sieben
 * Steuerungsmarken statt einer, sechs davon auf abgenommene Posten. *Wer sie nach §1 abholt, zieht
 * einen erledigten Auftrag.*
 */
export function aktiveBlaetter(ergebnisse) {
  return ergebnisse.filter((e) => e.status === 'aktiv').map((e) => e.pfad);
}

/**
 * Ein ganzes Blatt prüfen.
 *
 * **Ein Blatt ohne Kopf ist kein Fehlschlag.** 67 der 80 Blätter im Bestand haben keinen — ein
 * Werkzeug, das bei ihnen rot wird, wird abgeschaltet, und dann fängt es auch die neuen nicht mehr.
 */
export function pruefeBlatt(pfad, arbeitsverzeichnis = process.cwd()) {
  const text = readFileSync(pfad, 'utf8');
  const roh = lieseKopfRoh(text);
  if (roh === null) {
    return { pfad, kopf: false, bloecke: 0, eintraege: [] };
  }
  let kopf;
  try {
    kopf = yaml.load(roh);
  } catch (fehler) {
    return { pfad, kopf: false, unlesbar: String(fehler?.message ?? fehler), bloecke: zaehleBloecke(text), eintraege: [] };
  }
  // K-06: ALLE Blöcke lesen. Der erste ist der Kopf; die weiteren tragen seit R19 die Messungen.
  const weitere = lieseAlleBloecke(text).slice(1).map((roh) => { try { return yaml.load(roh); } catch { return null; } });
  const eintraege = sammleBefehle(kopf).map((e) => pruefeEintrag(e, arbeitsverzeichnis));
  for (const [i, block] of weitere.entries()) {
    if (!block) continue;
    for (const e of sammleBefehle(block)) {
      eintraege.push(pruefeEintrag({ ...e, id: `block${i + 2}.${e.id}` }, arbeitsverzeichnis));
    }
  }
  const messungen = weitere.filter(Boolean).filter((b) => b.measurements || b.messungen).length;
  const auftrag = kopf?.auftrag ?? kopf ?? {};
  return {
    pfad, kopf: true, bloecke: zaehleBloecke(text), eintraege,
    status: auftrag?.status ?? null, struktur: strukturBefunde(kopf), messbloecke: messungen,
  };
}

/** Ein Bericht als Text. **Nichts wird gekappt** — eine Liste, die bei zehn abschneidet, liest sich
 *  wie „zehn Probleme" und ist „mindestens zehn". */
export function bericht(ergebnis) {
  const zeilen = [`── ${ergebnis.pfad}`];
  if (!ergebnis.kopf) {
    zeilen.push(ergebnis.unlesbar ? `   KOPF UNLESBAR: ${ergebnis.unlesbar}` : '   KEIN KOPF (kein Fehler — aeltere Blaetter haben keinen)');
    return zeilen.join('\n');
  }
  if (ergebnis.bloecke > 1) {
    zeilen.push(`   ${ergebnis.bloecke} yaml-Bloecke gelesen`
      + (ergebnis.messbloecke ? ` (davon ${ergebnis.messbloecke} mit Messungen)` : ''));
  }
  for (const b of ergebnis.struktur ?? []) {
    zeilen.push(`   ${('STRUKTUR ' + b.regel).padEnd(16)} ${String(b.id).padEnd(24)} ${b.text}`);
  }
  if (ergebnis.eintraege.length === 0) {
    zeilen.push('   KEIN PRUEFBEFEHL im Kopf gefunden');
  }
  for (const e of ergebnis.eintraege) {
    zeilen.push(`   ${e.stufe.padEnd(16)} ${String(e.id).padEnd(24)} ${e.hinweis}`);
    if (e.befehl && e.stufe !== STUFEN.OK) {
      zeilen.push(`   ${''.padEnd(16)} ${''.padEnd(24)} $ ${e.befehl.replace(/\s+/g, ' ').slice(0, 150)}`);
    }
  }
  const zahl = (s) => ergebnis.eintraege.filter((e) => e.stufe === s).length;
  zeilen.push(`   ── ${ergebnis.eintraege.length} Eintrag/Eintraege: `
    + `${zahl(STUFEN.OK)} OK · ${zahl(STUFEN.VERDAECHTIG)} verdaechtig · ${zahl(STUFEN.FEHLSCHLAG)} Fehlschlag · `
    + `${zahl(STUFEN.NULLTREFFER)} nulltreffer · `
    + `${zahl(STUFEN.UEBERSPRUNGEN)} uebersprungen · ${zahl(STUFEN.NICHT_MASCHINELL)} nicht maschinell`
    + `${(ergebnis.struktur?.length ?? 0) > 0 ? ` · ${ergebnis.struktur.length} STRUKTUR-Befund(e)` : ''}`);
  return zeilen.join('\n');
}

// --- CLI -----------------------------------------------------------------------------------------

const istDirekterAufruf = process.argv[1] && import.meta.url.endsWith(process.argv[1].split('/').pop());
if (istDirekterAufruf) {
  const dateien = process.argv.slice(2);
  if (dateien.length === 0) {
    console.error('Aufruf: auftrag-pruefen.sh <blatt.md> [weitere.md ...]');
    process.exit(2);
  }
  let fehlschlaege = 0;
  const alle = [];
  for (const pfad of dateien) {
    const e = pruefeBlatt(pfad);
    alle.push(e);
    console.log(bericht(e));
    fehlschlaege += e.eintraege.filter((x) => x.stufe === STUFEN.FEHLSCHLAG).length;
    fehlschlaege += (e.struktur ?? []).length;
  }
  // S-01 gilt über die Menge, nicht je Blatt — sie kann erst am Ende beantwortet werden.
  const aktive = aktiveBlaetter(alle);
  if (dateien.length > 1) {
    if (aktive.length === 1) {
      console.log(`\n── STRUKTUR S-01  genau ein aktiver Auftrag: ${aktive[0]}`);
    } else {
      console.log(`\n── STRUKTUR S-01  ${aktive.length} Blaetter mit \`status: aktiv\` — erwartet genau EINES`);
      for (const p of aktive) console.log(`                  ${p}`);
      if (aktive.length > 1) fehlschlaege += 1;
    }
  }
  // **Der Exitcode meldet nur FEHLSCHLAG.** „Verdaechtig" und „nicht maschinell" sind Hinweise an
  // einen Menschen, keine Gruende, eine Kette abzubrechen — sonst wird das Werkzeug abgeschaltet.
  process.exit(fehlschlaege > 0 ? 1 : 0);
}
