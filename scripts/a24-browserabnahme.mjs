/**
 * A-24-7 — Browserabnahme als ABLAUF, nicht als Behauptung.
 *
 * Der Weg, wörtlich aus dem Kriterium:
 *   L-Dach anlegen · zwei Maße füllen — Warnung bleibt sichtbar und nennt vier ·
 *   alle vier füllen · Dach erscheint.
 *
 * Das L-Dach ist gesät (Dokument 36, `roofType: 'l-shape'`, `anbau` FEHLT) — „anlegen" heißt hier
 * „vorhanden und geöffnet", weil das Zeichnen einer Kontur ein anderer Gegenstand ist (A-05).
 * Gemessen wird, was auf dem Bildschirm STEHT, nicht was der Quelltext sagt.
 */
import puppeteer from 'puppeteer';

const PORT = process.env.PORT ?? '8099';
const BASIS = `http://127.0.0.1:${PORT}`;
const OBJEKT = process.env.OBJEKT ?? '10229';
const EMAIL = 'a24-abnahme@example.test';
const PASS = 'a24-abnahme-geheim';

const schritte = [];
const halt = (name, ergebnis, belegt) => {
  schritte.push({ name, ergebnis, belegt });
  console.log(`${belegt ? '✔' : '✖'} ${name}\n    ${ergebnis}`);
};

// Puppeteers eigenes Chrome ist nicht installiert (`~/.cache/puppeteer` fehlt) — gemessen, nicht
// nachgeladen. Stattdessen das vorhandene System-Chrome. HEADFUL, weil die Insel WebGL rendert und
// der Kopflos-Modus dort schon einmal ein anderes Bild geliefert hat.
const CHROME = process.env.CHROME
  ?? '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome';
const browser = await puppeteer.launch({
  headless: false,
  executablePath: CHROME,
  args: ['--no-sandbox', '--disable-setuid-sandbox', '--window-size=1440,900'],
});
const seite = await browser.newPage();
await seite.setViewport({ width: 1440, height: 900 });

try {
  // --- Anmelden -----------------------------------------------------------------------------
  await seite.goto(`${BASIS}/login`, { waitUntil: 'networkidle2' });
  await seite.type('input[type="email"], input[name="email"]', EMAIL);
  await seite.type('input[type="password"], input[name="password"]', PASS);
  await Promise.all([
    seite.waitForNavigation({ waitUntil: 'networkidle2' }).catch(() => {}),
    seite.click('button[type="submit"]'),
  ]);
  halt('Anmeldung', `URL nach dem Absenden: ${seite.url()}`, !seite.url().includes('/login'));

  // --- Die Insel öffnen ---------------------------------------------------------------------
  await seite.goto(`${BASIS}/admin/hausplaner/objekt/${OBJEKT}`, { waitUntil: 'networkidle2' });
  await new Promise((r) => setTimeout(r, 2500));
  const titel = await seite.title();
  halt('Insel geöffnet', `Titel: „${titel}" · URL: ${seite.url()}`, !seite.url().includes('/login'));

  // --- In den Expertenmodus, dort lebt das Eigenschaften-Panel -------------------------------
  // Der Store hängt NICHT am `window` (gemessen mit der Sonde) — also wird über die Oberfläche
  // bedient, so wie ein Nutzer es täte. Das ist ohnehin die ehrlichere Abnahme.
  const inModus = await seite.evaluate(() => {
    const knopf = [...document.querySelectorAll('button, [role="button"]')]
      .find((b) => /Expertenmodus/i.test(b.textContent ?? ''));
    if (!knopf) return false;
    knopf.click();
    return true;
  });
  await new Promise((r) => setTimeout(r, 2500));
  halt('Expertenmodus geöffnet', inModus ? 'Knopf „Expertenmodus" geklickt' : 'Knopf nicht gefunden', inModus);

  // --- Das L-Dach auswählen — über die FLÄCHE, wie das Panel es verlangt ---------------------
  // Das Eigenschaften-Panel sagt selbst: „Objekt anklicken (Auswahl-Werkzeug)". Also erst das
  // Markieren-Werkzeug (Kürzel V), dann in die Zeichenfläche klicken. Das Dach misst 10 × 8 m bei
  // 12 % Zoom, füllt die Fläche also weit über die Mitte hinaus.
  await seite.keyboard.press('v');
  await new Promise((r) => setTimeout(r, 500));

  const flaeche = await seite.evaluate(() => {
    const c = document.querySelector('canvas, svg[class*="hp-"], [class*="buehne"] canvas');
    if (!c) return null;
    const r = c.getBoundingClientRect();
    return { x: Math.round(r.x + r.width / 2), y: Math.round(r.y + r.height / 2), w: Math.round(r.width), h: Math.round(r.height) };
  });
  if (!flaeche) throw new Error('keine Zeichenfläche gefunden');
  await seite.mouse.click(flaeche.x, flaeche.y);
  await new Promise((r) => setTimeout(r, 1200));

  const gewaehlt = await seite.evaluate(() => {
    const t = document.body.innerText;
    return { anbauAbschnitt: /Anbau \/ Verschneidung/.test(t), zahlfelder: document.querySelectorAll('input[type="number"]').length };
  });
  halt('L-Dach ausgewählt', `Klick auf ${flaeche.x}/${flaeche.y} (Fläche ${flaeche.w}×${flaeche.h}) → ${JSON.stringify(gewaehlt)}`,
    gewaehlt.anbauAbschnitt === true);

  // --- Schritt 1: OHNE Maße — die Warnung muss stehen und VIER nennen ------------------------
  const lies = () => seite.evaluate(() => {
    const txt = document.body.innerText;
    const m = txt.match(/⚠[^\n]*/g) ?? [];
    const felder = [...document.querySelectorAll('label')]
      .map((l) => l.textContent?.trim().split('\n')[0] ?? '')
      .filter((t) => /Länge|Breite/.test(t));
    return { warnungen: m, felder };
  });

  const vorher = await lies();
  const warnung = vorher.warnungen.find((w) => /L\/T-Dach/.test(w)) ?? '';
  halt('Warnung ohne Maße', `„${warnung}"`,
    /alle vier Maße/.test(warnung) && /Anbau/.test(warnung));

  halt('Die vier Felder sind da', JSON.stringify(vorher.felder),
    vorher.felder.some((f) => /Anbau Länge/.test(f)) && vorher.felder.some((f) => /Anbau Breite/.test(f)));

  // --- Schritt 2: ZWEI Maße füllen — die Warnung muss BLEIBEN --------------------------------
  const setze = async (n, wert) => {
    const eingaben = await seite.$$('input[type="number"]');
    // Die Anbau-Felder sind die letzten vier Zahlenfelder des Panels.
    const ziel = eingaben[eingaben.length - 4 + n];
    if (!ziel) throw new Error(`Feld ${n} nicht gefunden (${eingaben.length} Zahlenfelder)`);
    await ziel.click({ clickCount: 3 });
    await ziel.type(String(wert));
    await seite.evaluate((el) => el.blur(), ziel);
    await new Promise((r) => setTimeout(r, 400));
  };

  await setze(0, 10000);   // Außenmaß Länge
  await setze(1, 8000);    // Außenmaß Breite
  const nachZwei = await lies();
  const warnung2 = nachZwei.warnungen.find((w) => /L\/T-Dach/.test(w)) ?? '';
  halt('Nach ZWEI Maßen bleibt die Warnung', `„${warnung2}"`, warnung2.length > 0);

  // --- Schritt 3: alle VIER füllen — die Warnung muss VERSCHWINDEN ---------------------------
  await setze(2, 4000);    // Anbau Länge
  await setze(3, 3000);    // Anbau Breite
  const nachVier = await lies();
  const warnung3 = nachVier.warnungen.find((w) => /L\/T-Dach/.test(w)) ?? '';
  halt('Nach VIER Maßen ist die Warnung weg', `verbliebene Warnungen: ${JSON.stringify(nachVier.warnungen)}`,
    warnung3.length === 0);

  // --- Alle vier Maße stehen im MODELL --------------------------------------------------------
  // Der Store hängt nicht am `window` — gemessen wird deshalb an der Stelle, die AUS dem Modell
  // rendert: die vier Felder sind an `a?.length`, `a?.width`, `a?.lengthB`, `a?.widthB` gebunden.
  // Steht dort ein Wert, steht er im Knoten. (Mein erster Versuch griff auf einen Store am
  // `window` zu und war deshalb rot — der Fehler stand in der PRÜFUNG, nicht im Bau.)
  const werte = await seite.evaluate(() => {
    const paare = [...document.querySelectorAll('label')]
      .filter((l) => /Außenmaß|Anbau/.test(l.textContent ?? ''))
      .map((l) => [l.textContent?.trim().split('\n')[0], l.querySelector('input')?.value]);
    return Object.fromEntries(paare);
  });
  const alleGefuellt = Object.values(werte).every((v) => Number(v) > 0) && Object.keys(werte).length === 4;
  halt('Alle vier Maße stehen im Modell', JSON.stringify(werte), alleGefuellt);

  // --- Und das Dach erscheint -----------------------------------------------------------------
  // „Dach erscheint" heißt: das Tor lässt durch. Belegt wird es an der Fläche, nicht am Quelltext —
  // die Warnung ist weg UND die vier Maße stehen, also liefert `anbauZuEingabe` kein `null` mehr.
  // Was hier NICHT belegt wird: dass die 3D-Geometrie sichtbar ist. Das misst kein Textausleser;
  // dafür liegt der Bildschirmabzug bei.
  halt('Das Tor lässt durch (Warnung weg + vier Maße)', 'siehe die zwei Schritte darüber',
    alleGefuellt);

  await seite.screenshot({ path: process.env.BILD ?? '/tmp/a24-abnahme.png', fullPage: false });
} catch (e) {
  halt('ABBRUCH', String(e?.message ?? e), false);
} finally {
  await browser.close();
  const gruen = schritte.filter((s) => s.belegt).length;
  console.log(`\n${gruen} von ${schritte.length} Schritten belegt.`);
  process.exit(gruen === schritte.length ? 0 : 1);
}
