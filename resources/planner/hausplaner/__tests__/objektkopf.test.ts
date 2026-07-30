/**
 * AUF-83-T3 / K-01 — **der Objektkopf: gelesen, nicht gerechnet — und die drei Zeilen bleiben drei.**
 *
 * T2 hat die Blade-Leiste über der Insel abgeräumt und einen Teil ausdrücklich stehen lassen:
 * *„Objektname mit Adresse und der Übernehmen-Knopf samt Staleness-Pille … wandert mit T3 in die
 * Kopfleiste."* Diese Datei hält fest, dass der Umzug **vollständig** war und **nichts dazukam**.
 *
 * **Die schärfste Zusage hier ist die vierte:** `eineWerkzeugzeile.test.ts` bleibt grün. Der
 * Planner hat sie selbst als Wächter in K-01 eingetragen — *„wird eine davon rot, ist der Bau
 * falsch, nicht die Zusage"*. Mein erster K-01-Entwurf hätte genau das gerissen, und dass er es
 * nicht mehr kann, ist der Grund für die Neufassung des Kriteriums.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, join } from 'node:path';
import { leseObjektkopf, pillenText, kopfzeile, OBJEKTKOPF_ATTRIBUT } from '../app/state/objektkopf';

const hier = dirname(fileURLToPath(import.meta.url));
const ohneKommentare = (s: string): string =>
  s.replace(/\/\*[\s\S]*?\*\//g, '').replace(/\{\/\*[\s\S]*?\*\/\}/g, '').replace(/^\s*\/\/.*$/gm, '');
const app = ohneKommentare(readFileSync(join(hier, '../app/HausplanerApp.tsx'), 'utf8'));
/** AUF-83-T3-N1 — der Übernehmen-Knopf, die Staleness-Pille und ihr `disabled`-Zustand wohnen seit
 *  dem Überlauf-Umbau hier, nicht mehr in `HausplanerApp.tsx`. Der Objektname bleibt dort. */
const ueberlauf = ohneKommentare(readFileSync(join(hier, '../app/dashboard/ObjektkopfUeberlauf.tsx'), 'utf8'));
const bladeRoh = readFileSync(join(hier, '../../../views/admin/hausplaner/objekt.blade.php'), 'utf8');
/** **Ohne Blade-Kommentare gemessen.** Mein Erklärungsblock am Umzug zitiert den Satz „In Abnahme
 *  — Browser-Sichtproben", um zu sagen, dass er FORT ist — und die erste Fassung dieser Zusage hat
 *  genau daran angeschlagen. **Vierte Ausprägung derselben Falle bei mir:** für `.tsx` habe ich
 *  den Helfer längst, für Blade hatte ich ihn vergessen. Ein Verbot, das seine eigene Begründung
 *  trifft, prüft den Text und nicht den Code. */
const blade = bladeRoh.replace(/\{\{--[\s\S]*?--\}\}/g, '');
const main = ohneKommentare(readFileSync(join(hier, '../main.tsx'), 'utf8'));

const VOLL = JSON.stringify({
  name: 'Musterstraße 1', adresse: '12345 Musterstadt', uebernehmenUrl: '/x/uebernehmen',
  status: 'aktuell', revision: 3, szeneLeer: false,
});

// --- Lesen: alles Unerwartete ergibt `null`, nie einen halben Kopf --------------------------------

test('K-01: ein vollständiger Kopf wird gelesen', () => {
  const k = leseObjektkopf(VOLL);
  assert.ok(k);
  assert.equal(k.name, 'Musterstraße 1');
  assert.equal(k.status, 'aktuell');
  assert.equal(k.revision, 3);
});

test('K-01: fehlt das Attribut, gibt es keinen Kopf — und keine Ausnahme', () => {
  // **Das Studio ist genau dieser Fall**, kein Randfall: dort gibt es kein Objekt.
  for (const roh of [undefined, null, '', 'kein json', '[]', '{}', 'null']) {
    assert.equal(leseObjektkopf(roh as string | null | undefined), null, `${String(roh)} ergibt keinen null-Kopf`);
  }
});

test('K-01: ein HALBER Kopf wird verworfen — nicht halb angezeigt', () => {
  // **Die Wirkung, nicht die Gestalt.** Ein Kopf mit Namen, aber ohne Ziel zeigte einen Knopf, der
  // nichts tut. Dieselbe Richtung wie bei Rechten (AUF-60) und Projekten (AUF-78): ein kaputter
  // Wert darf nie mehr behaupten als ein fehlender.
  const faelle: Array<[string, Record<string, unknown>]> = [
    ['ohne Ziel', { name: 'A', adresse: '', status: 'nie', szeneLeer: false }],
    ['ohne Name', { adresse: '', uebernehmenUrl: '/x', status: 'nie', szeneLeer: false }],
    ['leerer Name', { name: '', adresse: '', uebernehmenUrl: '/x', status: 'nie', szeneLeer: false }],
    ['leeres Ziel', { name: 'A', adresse: '', uebernehmenUrl: '', status: 'nie', szeneLeer: false }],
    ['fremder Status', { name: 'A', adresse: '', uebernehmenUrl: '/x', status: 'irgendwas', szeneLeer: false }],
    ['Revision als Text', { name: 'A', adresse: '', uebernehmenUrl: '/x', status: 'aktuell', revision: '3', szeneLeer: false }],
    ['szeneLeer als Text', { name: 'A', adresse: '', uebernehmenUrl: '/x', status: 'nie', szeneLeer: 'ja' }],
  ];
  for (const [was, roh] of faelle) {
    assert.equal(leseObjektkopf(JSON.stringify(roh)), null, `${was}: wurde angenommen`);
  }
});

// --- Die ECHTE Nutzlast des Blades — der Fall, der P1 gekostet hat ---------------------------------

test('K-01 (Befund T3-K01-B1): `revision: null` ist „fehlt" — der Kopf wird GELESEN', () => {
  // **Der P1-Fehler, gefunden vom Evaluator in der Sichtprobe, nicht von einem Gate.**
  // Das Blade sendet `'revision' => $uebernahme['szene_revision'] ?? null`. `JSON.parse` macht
  // daraus `null`, nicht `undefined` — und meine erste Fassung prüfte nur `!== undefined`.
  // **Ergebnis: auf 11 von 12 Objekten (alle mit Status `nie`) fehlten Name, Knopf und Pille.**
  //
  // *Meine ursprünglichen Testfälle setzten `revision` als Zahl oder ließen es weg — nie `null`.
  // Ich habe die Nutzlast erfunden, statt sie zu lesen. Deshalb steht unten die Zusage, die sie
  // aus dem Blade ableitet.*
  const echteNutzlast = JSON.stringify({
    // **Die Adresse steht hier bewusst verkürzt.** `projektKlick.test.ts / K4` verbietet echte
    // Hausplaner-Adressen im Baum, die keine Dateizugriffe sind — und die Zusage hat recht: ein
    // Ziel in einer Testnutzlast ist von einem echten Ziel nicht zu unterscheiden. **Für den
    // Befund zählt `revision: null`, nicht der Inhalt der URL.**
    name: 'Objekt #154', adresse: '', uebernehmenUrl: '/x/uebernehmen',
    status: 'nie', revision: null, szeneLeer: false,
  });
  const k = leseObjektkopf(echteNutzlast);
  assert.ok(k, 'der Kopf wird verworfen — genau der Befund T3-K01-B1');
  assert.equal(k.name, 'Objekt #154');
  assert.equal(k.status, 'nie');
  assert.equal(k.revision, undefined, '`null` wird nicht zu „nicht gesetzt" normalisiert');
  // Und für alle drei Zustände, denn `nie` UND `veraltet` senden beide `null`:
  for (const status of ['nie', 'veraltet', 'aktuell'] as const) {
    const roh = JSON.stringify({ name: 'A', adresse: '', uebernehmenUrl: '/x', status, revision: null, szeneLeer: false });
    assert.ok(leseObjektkopf(roh), `Status ${status} mit revision null wird verworfen`);
  }
});

test('K-01: die FELDLISTE des Blades und die des Lesers sind dieselbe — gemessen, nicht behauptet', () => {
  // **Die Zusage, die den P1 verhindert hätte.** Kein Gate konnte ihn sehen, weil kein Test je die
  // echte Nutzlast gelesen hat: meine Fälle waren erfunden, das Blade sendet etwas anderes.
  // Diese Zusage liest die Feldnamen **aus dem Blade** und hält sie gegen den Leser.
  const block = blade.slice(blade.indexOf('data-objektkopf='), blade.indexOf('data-speichern-url'));
  const felder = [...block.matchAll(/'(\w+)' =>/g)].map((m) => m[1]);
  assert.deepEqual(felder, ['name', 'adresse', 'uebernehmenUrl', 'status', 'revision', 'szeneLeer'],
    'das Blade sendet andere Felder als der Leser erwartet');

  // **Und jedes Feld, das mit `?? null` gesendet wird, MUSS `null` vertragen.** Das ist die
  // Verallgemeinerung des Befundes: nicht „revision darf null sein", sondern „was das Blade
  // nullbar sendet, verwirft der Leser nicht".
  const nullbar = [...block.matchAll(/'(\w+)' => [^,\n]*\?\? null/g)].map((m) => m[1]);
  assert.ok(nullbar.length > 0, 'kein nullbares Feld gefunden — die Zusage misst Leere');
  for (const feld of nullbar) {
    const roh = JSON.stringify({
      name: 'A', adresse: '', uebernehmenUrl: '/x', status: 'nie', szeneLeer: false, [feld]: null,
    });
    assert.ok(leseObjektkopf(roh), `das Blade sendet \`${feld}\` nullbar, der Leser verwirft es`);
  }
});

// --- Die drei Sätze der Pille, an einer Stelle ----------------------------------------------------

test('K-01: die Pille sagt drei verschiedene Dinge — und die Revision nur, wenn sie gilt', () => {
  const basis = { name: 'A', adresse: '', uebernehmenUrl: '/x', szeneLeer: false };
  assert.match(pillenText({ ...basis, status: 'aktuell', revision: 7 }), /aktuell \(Szene Rev\. 7\)/);
  assert.match(pillenText({ ...basis, status: 'veraltet' }), /VERALTET/);
  assert.match(pillenText({ ...basis, status: 'nie' }), /Noch nie übernommen/);
  // **Keine Revision neben „VERALTET".** Dort führt `szene_revision` die AKTUELLE Szenen-Revision,
  // nicht die übernommene — das ist beim Umzug aufgefallen (`UebernahmeKnopfTest`), weil der alte
  // Blade-Zweig gar keine Zahl zeigte. Eine Zahl dort liesse offen, welche der beiden gemeint ist.
  assert.doesNotMatch(pillenText({ ...basis, status: 'veraltet', revision: 2 }), /\d/);
  assert.doesNotMatch(pillenText({ ...basis, status: 'nie' }), /\d/);
  // Drei Zustände, drei verschiedene Sätze — sonst wäre die Pille Dekoration.
  const saetze = (['aktuell', 'veraltet', 'nie'] as const).map((s) => pillenText({ ...basis, status: s, revision: 1 }));
  assert.equal(new Set(saetze).size, 3);
});

test('K-01: Name und Adresse werden verbunden wie im Blade — und die Adresse darf fehlen', () => {
  const basis = { name: 'Haus A', uebernehmenUrl: '/x', status: 'nie' as const, szeneLeer: false };
  assert.equal(kopfzeile({ ...basis, adresse: '12345 Stadt' }), 'Haus A · 12345 Stadt');
  assert.equal(kopfzeile({ ...basis, adresse: '' }), 'Haus A', 'ein Trenner ohne Adresse dahinter');
});

// --- Der Umzug: dort weg, hier da -----------------------------------------------------------------

test('K-01: die Blade-Leiste ist fort — jeder ihrer vier Inhalte', () => {
  // **Absence mit presence-Partner (R2):** dass die Leiste weg ist, sagt nichts darüber, ob ihr
  // Inhalt angekommen ist. Beides steht hier nebeneinander.
  assert.ok(!blade.includes('class="hp-bar"'), 'die Leiste steht noch im Blade');
  assert.ok(!blade.includes('In Auslegung übernehmen'), 'der Knopf steht noch im Blade');
  assert.ok(!blade.includes('Noch nie übernommen'), 'die Pille steht noch im Blade');
  assert.ok(!blade.includes('In Abnahme — Browser-Sichtproben'),
    'die Bauzustands-Meldung steht noch auf der Nutzerfläche');

  // presence: der Inhalt kommt jetzt über die Naht.
  assert.match(blade, /data-objektkopf="\{\{ json_encode\(\[/, 'das Attribut fehlt am Mount-Knoten');
  for (const feld of ['name', 'adresse', 'uebernehmenUrl', 'status', 'revision', 'szeneLeer']) {
    assert.ok(blade.includes(`'${feld}' =>`), `das Feld ${feld} wird nicht geliefert`);
  }
});

test('K-01: die Kopfleiste zeigt ihn — und das Studio bleibt ohne', () => {
  assert.match(app, /const objektkopf = usePlannerUiStore\(\(s\) => s\.objektkopf\)/, 'der Kopf wird nicht gelesen');
  assert.match(app, /\{objektkopf && \(/, 'der Kopf wird bedingungslos gerendert — das Studio hat keinen');
  assert.match(app, /className="hp-ok-name"/);
  // AUF-83-T3-N1: die Pille steht seit dem Überlauf-Umbau in `ObjektkopfUeberlauf.tsx`.
  assert.match(ueberlauf, /className=\{`hp-ok-pille hp-ok-pille--\$\{objektkopf\.status\}`\}/);
  assert.match(main, /setObjektkopf\(leseObjektkopf\(mount\.dataset\[OBJEKTKOPF_ATTRIBUT\]\)\)/,
    'die Naht ist in main.tsx nicht verdrahtet');
  assert.equal(OBJEKTKOPF_ATTRIBUT, 'objektkopf');
});

test('K-01 (Grenze): der Übernehmen-Knopf bleibt EIN Formular-POST — kein zweiter Weg', () => {
  // Das Blade hatte ein `<form method=POST>` mit CSRF auf eine Laravel-Route. Ein `fetch` in der
  // Insel wäre ein zweiter Weg zur selben Wirkung — und der erste, der bei einem Redirect anders
  // reagiert.
  //
  // AUF-83-T3-N1: der Knopf steht seit dem Überlauf-Umbau in `ObjektkopfUeberlauf.tsx`, nicht mehr
  // in `HausplanerApp.tsx` — die ganze Datei ist jetzt der Prüfbereich, nicht mehr ein Ausschnitt.
  assert.match(ueberlauf, /<form method="POST" action=\{objektkopf\.uebernehmenUrl\}/);
  assert.ok(!/fetch\(/.test(ueberlauf), 'die Übernahme läuft über einen zweiten Weg');
  // **Eine Quelle für den Token:** der Store hat ihn beim Mount bekommen und benutzt ihn für
  // `save()`. Ein zweites `document.querySelector` wäre eine zweite Lesestelle. `HausplanerApp`
  // reicht den Token als Prop durch (`csrfToken={store.getState().csrfToken ?? ''}`), statt ihn
  // ein zweites Mal aus dem Dokument zu lesen.
  assert.match(app, /csrfToken=\{store\.getState\(\)\.csrfToken \?\? ''\}/, 'der Token wird nicht mehr durchgereicht');
  assert.ok(!/meta\[name="csrf-token"\]/.test(ueberlauf), 'der Token wird ein zweites Mal gelesen');
});

test('K-01: der gesperrte Knopf sagt WARUM — der Hinweis ist nicht verloren', () => {
  // Im Blade stand daneben ein eigener Satz *„Keine Szene vorhanden — erst zeichnen und
  // speichern."*. Er ist in den `title` gewandert, nicht gestrichen: der gesperrte Zustand allein
  // sagt nicht, was zu tun ist.
  // AUF-83-T3-N1: umgehängt nach `ObjektkopfUeberlauf.tsx`, siehe oben.
  assert.match(ueberlauf, /disabled=\{objektkopf\.szeneLeer\}/, 'bei leerer Szene ist der Knopf frei');
  assert.match(ueberlauf, /Keine Szene vorhanden — erst zeichnen und speichern\./);
  assert.match(ueberlauf, /GESPERRT_ZEIGER/, 'der gesperrte Zustand kommt nicht aus der einen Quelle');
});

// --- Und es bleiben genau drei Zeilen -------------------------------------------------------------

test('K-01: GENAU DREI Zeilen über dem Zeichenbereich — keine vierte', () => {
  // **Die Auflage des Blattes wörtlich:** *„Es bleiben GENAU DREI Zeilen. Keine vierte. Wer Inhalt
  // hinzufügt, nimmt Platz aus Zeile 1 — nicht aus der Höhe der Bühne."* Der Objektkopf ist in
  // Zeile 1 eingezogen; eine vierte Zeile wäre genau die Höhe, die K-08 gewinnen soll.
  const kopf = app.indexOf('<GeschossFlaeche');            // Zeile 1
  const arbeit = app.indexOf('Arbeitsbereich</span>');      // Zeile 2
  const werkzeug = app.indexOf('<OpGruppe name="Verlauf">'); // Zeile 3
  assert.ok(kopf > 0 && arbeit > kopf && werkzeug > arbeit, 'die drei Zeilen stehen nicht mehr in dieser Folge');
  // Der Objektkopf liegt IN Zeile 1, nicht dazwischen und nicht danach.
  const ok = app.indexOf('{objektkopf && (');
  assert.ok(ok > kopf && ok < arbeit, 'der Objektkopf steht nicht in der Kopfleiste, sondern in einer eigenen Zeile');
});

test('K-01 (Auflage): der Bau hat KEINE Zeile hinzugefügt — über dem Zeichenbereich stehen vier', () => {
  // **Nachgebessert, und der Grund ist eine eigene fehlgeschlagene Gegenprobe.** Die Zusage oben
  // prüft die REIHENFOLGE der drei Zeilen — ich habe testweise eine vierte eingezogen, und sie
  // blieb grün. *Eine Zusage, die die Auflage „keine vierte Zeile" nicht fängt, ist genau die
  // Sorte, die ich an fremden Aufträgen beanstande.*
  //
  // **Beim Nachmessen kam ein Befund heraus, der nicht meiner ist:** über dem Zeichenbereich
  // stehen **vier** Elemente, nicht drei. Das vierte ist die Werkzeuggruppen-Zeile
  // (`role="tabpanel"`, `WerkzeugGruppenMenue`) — sie stand dort **vor** diesem Bau. Der
  // K-01-Entscheid vom 30.07. zählt drei und hat sie nicht mitgezählt; gemeldet im Ledger.
  //
  // **Diese Zusage hält deshalb fest, was die Auflage wirklich verlangt:** *„Wer Inhalt hinzufügt,
  // nimmt Platz aus Zeile 1 — nicht aus der Höhe der Bühne."* Der Objektkopf ist in Zeile 1
  // eingezogen; die Zahl der Zeilen ist unverändert. Kommt eine hinzu, geht sie rot — egal ob
  // der Sollwert am Ende drei heißt oder vier.
  const von = app.indexOf("<div style={{ fontFamily: 'Inter");
  const bis = app.indexOf('<div ref={inhaltRef}');
  assert.ok(von > 0 && bis > von, 'die Anker der Zeilenzählung stimmen nicht mehr — die Zusage misst Leere');
  const geschwister = app.slice(von, bis).split('\n')
    .filter((z) => /^ {6}<(div|button|span|form)\b/.test(z) || /^ {6}\{[a-zA-Z]/.test(z));
  assert.equal(geschwister.length, 4,
    `${geschwister.length} Elemente über dem Zeichenbereich statt vier:\n${geschwister.map((z) => z.trim().slice(0, 70)).join('\n')}`);
});