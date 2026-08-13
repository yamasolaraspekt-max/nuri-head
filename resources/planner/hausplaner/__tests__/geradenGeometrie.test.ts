/**
 * A-32 — Geradenschnitt (F-004) und Parallelversatz (F-020 Normalform).
 *
 * ---
 *
 * **Die tragende Zusage ist die Längenunabhängigkeit.** *F-004 nennt für ihren Grenzfall
 * `|m| < ε` keinen Wert, und `m` ist ein Kreuzprodukt in **mm²**.* Eine absolute Schwelle darauf
 * wäre längenabhängig — **dieselbe Winkellage wäre bei kurzen Achsen „parallel" und bei langen
 * nicht.** Der Fehler zeigt sich erst bei einer ungewöhnlichen Wandlänge, also lange nach der
 * Abnahme. *Deshalb ist er hier ein Test und keine Absicht.*
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { geradenSchnitt, parallelVersatz } from '../geometry/geradenGeometrie';
import type { Punkt } from '../geometry/wallGeometry';

const p = (x: number, y: number): Punkt => ({ x, y });

/** Auf ein Zehntel Mikrometer genau — weit unterhalb jeder Zeichenauflösung. */
function nahe(ist: number, soll: number, was: string): void {
  assert.ok(Math.abs(ist - soll) < 1e-7, `${was}: ${ist} statt ${soll}`);
}

test('A-32-1 F-004: der Schnittpunkt liegt, wo er liegt — drei Lagen von Hand nachgerechnet', () => {
  const s1 = geradenSchnitt(p(0, 0), p(10, 0), p(5, -5), p(5, 5));
  assert.ok(s1); nahe(s1.x, 5, 'waagrecht × senkrecht x'); nahe(s1.y, 0, 'y');

  const s2 = geradenSchnitt(p(0, 0), p(4, 0), p(1, -1), p(1, 3));
  assert.ok(s2); nahe(s2.x, 1, 'kurz/versetzt x'); nahe(s2.y, 0, 'y');

  const s3 = geradenSchnitt(p(0, 0), p(2, 2), p(0, 4), p(4, 0));
  assert.ok(s3); nahe(s3.x, 2, 'zwei Diagonalen x'); nahe(s3.y, 2, 'y');
});

test('A-32-1 F-004: die Formelzeile der Sammlung ergibt das GESPIEGELTE Ergebnis', () => {
  // DER BEFUND, ALS ZUSAGE FESTGEHALTEN. FORMELSAMMLUNG.md:80 schreibt n = (A−C) × s.
  // Richtig ist (C−A) × s. Wer die Zeile abschreibt, bekommt den an A gespiegelten Punkt.
  // Diese Zusage wird ROT, sobald jemand geradenSchnitt auf die Fassung der Sammlung umstellt —
  // und sie ist der Grund, warum der Befund nicht nur in einem Kommentar steht.
  const woertlichNachSammlung = (a: Punkt, b: Punkt, c: Punkt, d: Punkt): Punkt => {
    const n = (a.x - c.x) * (d.y - c.y) - (a.y - c.y) * (d.x - c.x);
    const m = (b.x - a.x) * (d.y - c.y) - (b.y - a.y) * (d.x - c.x);
    const t = n / m;

    return { x: a.x + t * (b.x - a.x), y: a.y + t * (b.y - a.y) };
  };

  const faelle: Array<[Punkt, Punkt, Punkt, Punkt]> = [
    [p(0, 0), p(10, 0), p(5, -5), p(5, 5)],
    [p(0, 0), p(4, 0), p(1, -1), p(1, 3)],
    [p(0, 0), p(2, 2), p(0, 4), p(4, 0)],
    [p(3, 1), p(9, 7), p(2, 8), p(8, 0)],
  ];
  for (const [a, b, c, d] of faelle) {
    const richtig = geradenSchnitt(a, b, c, d);
    const nachBlatt = woertlichNachSammlung(a, b, c, d);
    assert.ok(richtig);
    // Der Punkt der Sammlung ist der an A gespiegelte: (S_falsch + S_richtig)/2 == A.
    nahe((nachBlatt.x + richtig.x) / 2, a.x, 'Spiegelung an A, x');
    nahe((nachBlatt.y + richtig.y) / 2, a.y, 'Spiegelung an A, y');
  }
});

test('A-32-1 TRAGEND: gleicher Winkel, 100-fache Länge — DASSELBE Urteil', () => {
  // DIE ZUSAGE, AN DER EINE ABSOLUTE mm²-SCHWELLE DURCHFAELLT.
  //
  // Gerade 1 laeuft von (0,0) nach (L,0), Gerade 2 von (0,1000) nach (L, 1000 + L·k).
  // Damit ist m = L²·k, waehrend der normalisierte Wert k/sqrt(1+k²) ist — LAENGENFREI.
  //
  //   k = 1e-9  -> Sinus 1e-9  < 1e-6  -> PARALLEL bei jeder Laenge
  //                aber m = 1e-5 mm² (L=100) gegen 0,1 mm² (L=10 000)
  //   k = 1e-3  -> Sinus 1e-3  > 1e-6  -> SCHNITT bei jeder Laenge
  //                aber m = 10 mm² (L=100) gegen 100 000 mm² (L=10 000)
  //
  // Jede absolute Schwelle zwischen 1e-5 und 0,1 zerreisst das erste Paar, jede zwischen
  // 10 und 100 000 das zweite. Zwei Fallen in verschiedene Richtungen — eine allein waere
  // durch die Wahl der Schwelle zu umgehen.
  const urteil = (L: number, k: number): 'schnitt' | 'parallel' =>
    geradenSchnitt(p(0, 0), p(L, 0), p(0, 1000), p(L, 1000 + L * k)) ? 'schnitt' : 'parallel';

  assert.equal(urteil(100, 1e-9), 'parallel', 'kurz und fast parallel');
  assert.equal(urteil(10000, 1e-9), 'parallel', 'lang und fast parallel — DASSELBE Urteil');

  assert.equal(urteil(100, 1e-3), 'schnitt', 'kurz und deutlich schief');
  assert.equal(urteil(10000, 1e-3), 'schnitt', 'lang und deutlich schief — DASSELBE Urteil');

  // Und die Zahlen, auf die eine absolute Schwelle hereinfiele, ausdruecklich benannt:
  nahe(100 * 100 * 1e-9, 1e-5, 'm bei L=100, k=1e-9');
  nahe(10000 * 10000 * 1e-9, 0.1, 'm bei L=10 000, k=1e-9');
});

test('A-32-2 parallel, deckungsgleich und Länge 0 ergeben null — kein NaN, kein Wurf', () => {
  const parallel = geradenSchnitt(p(0, 0), p(10, 0), p(0, 500), p(10, 500));
  assert.equal(parallel, null, 'parallel');

  const deckungsgleich = geradenSchnitt(p(0, 0), p(10, 0), p(2, 0), p(7, 0));
  assert.equal(deckungsgleich, null, 'deckungsgleich');

  const gegenlaeufigDeckungsgleich = geradenSchnitt(p(0, 0), p(10, 0), p(7, 0), p(2, 0));
  assert.equal(gegenlaeufigDeckungsgleich, null, 'deckungsgleich, entgegengesetzte Richtung');

  assert.equal(geradenSchnitt(p(1, 1), p(1, 1), p(0, 0), p(5, 5)), null, 'erste Achse Länge 0');
  assert.equal(geradenSchnitt(p(0, 0), p(5, 5), p(2, 2), p(2, 2)), null, 'zweite Achse Länge 0');
  assert.equal(geradenSchnitt(p(0, 0), p(0, 0), p(1, 1), p(1, 1)), null, 'beide Länge 0');
});

test('A-32-2 Gegenprobe: kein Rückgabewert ist jemals NaN oder Infinity', () => {
  // Die Zusage von A-32-2 lautet „kein NaN, kein Infinity" — das ist mehr als „null bei parallel".
  // Hier laufen Lagen durch, die eine ungeschuetzte Division zerreissen wuerde.
  const lagen: Array<[Punkt, Punkt, Punkt, Punkt]> = [
    [p(0, 0), p(1e9, 0), p(0, 1), p(1e9, 1 + 1e-3)],
    [p(-1e6, -1e6), p(1e6, 1e6), p(-1e6, 1e6), p(1e6, -1e6)],
    [p(0, 0), p(0.001, 0), p(0.0005, -1), p(0.0005, 1)],
  ];
  for (const [a, b, c, d] of lagen) {
    const s = geradenSchnitt(a, b, c, d);
    if (s !== null) {
      assert.ok(Number.isFinite(s.x) && Number.isFinite(s.y), `endlich: ${JSON.stringify(s)}`);
    }
  }
});

test('A-32-3/4 Parallelversatz: die Seite ist benannt, und links ist links der Laufrichtung', () => {
  // Modell mit y nach OBEN (CAD, Buehne.tsx:135-136). Achse nach RECHTS -> links ist +y.
  const rechtslauf = parallelVersatz(p(0, 0), p(1000, 0), 240, 'links');
  assert.ok(rechtslauf);
  nahe(rechtslauf.start.x, 0, 'start x unveraendert'); nahe(rechtslauf.start.y, 240, 'start y');
  nahe(rechtslauf.ende.x, 1000, 'ende x unveraendert'); nahe(rechtslauf.ende.y, 240, 'ende y');

  const rechtsSeite = parallelVersatz(p(0, 0), p(1000, 0), 240, 'rechts');
  assert.ok(rechtsSeite);
  nahe(rechtsSeite.start.y, -240, 'rechts liegt gegenueber');

  // Achse nach LINKS -> links der Laufrichtung ist -y. Die Seite haengt an der RICHTUNG.
  const linkslauf = parallelVersatz(p(1000, 0), p(0, 0), 240, 'links');
  assert.ok(linkslauf);
  nahe(linkslauf.start.y, -240, 'umgekehrte Laufrichtung dreht die Seite mit');

  // Achse nach OBEN -> links ist -x.
  const hoch = parallelVersatz(p(0, 0), p(0, 1000), 240, 'links');
  assert.ok(hoch);
  nahe(hoch.start.x, -240, 'links einer aufwaerts laufenden Achse liegt bei -x');
});

test('A-32-3 Parallelversatz: der Abstand stimmt, die Richtung bleibt, F-020 haelt', () => {
  const start = p(300, -700);
  const ende = p(2100, 1450);
  const versetzt = parallelVersatz(start, ende, 125, 'links');
  assert.ok(versetzt);

  // 1. Die Richtung ist unveraendert — es ist eine PARALLELE.
  const r0 = { x: ende.x - start.x, y: ende.y - start.y };
  const r1 = { x: versetzt.ende.x - versetzt.start.x, y: versetzt.ende.y - versetzt.start.y };
  nahe(r1.x, r0.x, 'Richtung x'); nahe(r1.y, r0.y, 'Richtung y');

  // 2. Der Abstand ist 125 — an BEIDEN Endpunkten gemessen.
  nahe(Math.hypot(versetzt.start.x - start.x, versetzt.start.y - start.y), 125, 'Abstand am Start');
  nahe(Math.hypot(versetzt.ende.x - ende.x, versetzt.ende.y - ende.y), 125, 'Abstand am Ende');

  // 3. F-020 im Wortlaut: a·x + b·y + c = 0 mit a²+b²=1, versetzt a·x + b·y + c − t = 0.
  //    Der vorzeichenbehaftete Abstand der versetzten Punkte MUSS genau t sein.
  const laenge = Math.hypot(r0.x, r0.y);
  const a = -r0.y / laenge;
  const b = r0.x / laenge;
  nahe(a * a + b * b, 1, 'a² + b² = 1');
  const c = -(a * start.x + b * start.y);
  nahe(a * versetzt.start.x + b * versetzt.start.y + c, 125, 'a·x + b·y + c = t am Start');
  nahe(a * versetzt.ende.x + b * versetzt.ende.y + c, 125, 'a·x + b·y + c = t am Ende');
});

test('A-32-2/3 Parallelversatz: Länge 0 ergibt null, negativer Abstand versetzt zur Gegenseite', () => {
  assert.equal(parallelVersatz(p(5, 5), p(5, 5), 240, 'links'), null, 'ohne Richtung keine Seite');
  assert.equal(parallelVersatz(p(0, 0), p(10, 0), Number.NaN, 'links'), null, 'NaN-Abstand');

  const minus = parallelVersatz(p(0, 0), p(1000, 0), -240, 'links');
  const plus = parallelVersatz(p(0, 0), p(1000, 0), 240, 'rechts');
  assert.ok(minus && plus);
  nahe(minus.start.y, plus.start.y, 'negativer Abstand links == positiver rechts');
});

test('A-32-1 Längenunabhängigkeit auch beim Versatz: der Abstand skaliert NICHT mit der Achse', () => {
  // Dieselbe Klasse wie beim Schnitt, andere Funktion: eine Umsetzung, die den Versatz
  // versehentlich mit der Achsenlaenge multipliziert, faellt hier durch.
  for (const L of [1, 100, 10000, 1e6]) {
    const v = parallelVersatz(p(0, 0), p(L, 0), 240, 'links');
    assert.ok(v);
    nahe(v.start.y, 240, `Abstand bei Achsenlaenge ${L}`);
  }
});
