/**
 * AUF-81 — das Paket wird gespeichert, **zusätzlich** zum Download.
 *
 * **Die Gefahr dieses Postens ist ein Rückfall:** AUF-74 hat den Satz „gespeichert" wahr gemacht,
 * indem er ihn abgeschafft hat. Jetzt wird wirklich gespeichert — und derselbe Satz muss erneut
 * die Wahrheit sagen, diesmal in die andere Richtung. **Ein „gespeichert", das nur den Versuch
 * meldet, wäre genau der alte Fehler mit neuem Vorzeichen.**
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, join } from 'node:path';
import { speicherePaket, setzePaketZiel, kannPaketSpeichern, PAKETE_URL_ATTRIBUT } from '../app/state/paketSpeichern';

const hier = dirname(fileURLToPath(import.meta.url));
const ohneKommentare = (s: string): string =>
  s.replace(/\/\*[\s\S]*?\*\//g, '').replace(/\{\/\*[\s\S]*?\*\/\}/g, '').replace(/^\s*\/\/.*$/gm, '');
const wizard = ohneKommentare(readFileSync(join(hier, '../app/ConfigWizard.tsx'), 'utf8'));
const regel = ohneKommentare(readFileSync(join(hier, '../app/state/paketSpeichern.ts'), 'utf8'));

const echtesFetch = globalThis.fetch;
function mitAntwort(ok: boolean | Error): void {
  globalThis.fetch = (async () => {
    if (ok instanceof Error) throw ok;
    return { ok } as Response;
  }) as typeof fetch;
}

// --- Ohne Ziel wird nichts behauptet ------------------------------------------------------------
test('ohne Speicherziel wird nicht gespeichert — und es wird auch nicht behauptet', () => {
  setzePaketZiel(null, null);
  assert.equal(kannPaketSpeichern(), false);
});

test('ohne Ziel liefert das Speichern `false`, ohne zu werfen', async () => {
  setzePaketZiel(null, null);
  assert.equal(await speicherePaket('fenster', 'X', {}), false);
});

test('ein leeres Attribut ist wie kein Attribut — das Minimum, nicht das Maximum', () => {
  setzePaketZiel('', 'token');
  assert.equal(kannPaketSpeichern(), false);
});

// --- Der Ausgang wird gemeldet, nicht der Versuch -----------------------------------------------
test('angekommen ⇒ true', async () => {
  setzePaketZiel('/pakete', 'token');
  mitAntwort(true);
  assert.equal(await speicherePaket('fenster', 'Festverglasung', { schemaVersion: 1 }), true);
  globalThis.fetch = echtesFetch;
});

test('abgewiesen (403, 422, 500) ⇒ false — nicht „abgeschickt, also gespeichert"', async () => {
  setzePaketZiel('/pakete', 'token');
  mitAntwort(false);
  assert.equal(await speicherePaket('fenster', 'X', {}), false);
  globalThis.fetch = echtesFetch;
});

test('Netz weg ⇒ false statt Wurf — die Fläche darf nicht mit einem Fehler stehenbleiben', async () => {
  setzePaketZiel('/pakete', 'token');
  mitAntwort(new Error('offline'));
  assert.equal(await speicherePaket('fenster', 'X', {}), false);
  globalThis.fetch = echtesFetch;
});

// --- Der Besitzer wird NICHT mitgeschickt -------------------------------------------------------
test('die Anfrage trägt keine Nutzerkennung — sie käme sonst vom Aufrufer', async () => {
  // Eine Kennung, die der Aufrufer mitgibt, wäre das Eigentumsgatter, das man selbst aufsperrt.
  setzePaketZiel('/pakete', 'token');
  let gesendet = '';
  globalThis.fetch = (async (_u: unknown, o: { body?: string }) => {
    gesendet = o?.body ?? '';
    return { ok: true } as Response;
  }) as unknown as typeof fetch;
  await speicherePaket('fenster', 'X', { schemaVersion: 1 });
  globalThis.fetch = echtesFetch;

  const koerper = JSON.parse(gesendet) as Record<string, unknown>;
  assert.deepEqual(Object.keys(koerper).sort(), ['art', 'paket', 'schema_version', 'titel']);
  assert.ok(!('user_id' in koerper));
  assert.doesNotMatch(regel, /user_id/, 'auch im Quelltext taucht sie nicht auf');
});

// --- K10: der Download bleibt -------------------------------------------------------------------
test('K10: der Download ist unverändert vorhanden', () => {
  assert.match(wizard, /a\.href = url; a\.download = dateiname; a\.click\(\);/);
  assert.match(wizard, /URL\.revokeObjectURL\(url\)/);
  assert.match(wizard, /const dateiname = `konfigurator-\$\{art\}-\$\{wahl\.id\}\.json`/);
});

test('K10: gespeichert wird ZUSÄTZLICH, nicht statt', () => {
  assert.match(wizard, /void speicherePaket\(art, wahl\.label, paket\)/);
  // Beide Wege werden einzeln gemeldet — nicht einer für beide.
  assert.match(wizard, /if \(gespeichert\) teile\.push\('in deiner Paketliste gespeichert'\)/);
  assert.match(wizard, /if \(entstanden\) teile\.push\(/);
});

test('K10: der Fehlerfall aus AUF-74 meldet weiterhin keinen Erfolg ohne Ergebnis', () => {
  // Schlägt BEIDES fehl, sagt die Fläche genau das — kein „gespeichert" ins Leere.
  assert.match(wizard, /Es ist nichts entstanden — weder gespeichert noch heruntergeladen/);
  assert.doesNotMatch(wizard, /Download optional/, 'der verschluckte Fehler bleibt weg');
  assert.match(wizard, /let entstanden = true;/);
});

test('der Satz aus AUF-74 ist nachgezogen — er nennt jetzt beide Wege', () => {
  assert.match(wizard, /Ergebnis: gespeichert in deiner Paketliste — und zusätzlich als Datei zum Herunterladen/);
  assert.match(wizard, /'Ergebnis: Paketliste \+ Datei'/);
  // **Und er verspricht das Speichern nur, wenn es überhaupt geht.** Bei der Sichtprobe stand der
  // Satz auch ohne Speicherziel da — das wäre wieder ein Versprechen ohne Deckung gewesen, also
  // genau der Fehler, den AUF-74 beseitigt hat.
  assert.match(wizard, /kannPaketSpeichern\(\)\s*\?/, 'der Text folgt der Wirklichkeit');
  assert.match(wizard, /: 'Ergebnis: eine Datei zum Herunterladen\./, 'ohne Speicherziel nur der Download');
  // Und kein Versprechen auf später, wie in AUF-74 festgelegt.
  for (const wort of ['folgt', 'in Kürze', 'geplant', 'demnächst']) {
    assert.ok(!wizard.includes(wort), `„${wort}" ist ein Versprechen auf später`);
  }
});

// --- Die Naht -----------------------------------------------------------------------------------
test('das Ziel kommt über dieselbe Naht wie die Speichern-URL', () => {
  const einstieg = ohneKommentare(readFileSync(join(hier, '../main.tsx'), 'utf8'));
  assert.match(einstieg, /setzePaketZiel\(mount\.dataset\[PAKETE_URL_ATTRIBUT\] \?\? null, csrf\)/);
  assert.equal(PAKETE_URL_ATTRIBUT, 'paketeUrl');
});
