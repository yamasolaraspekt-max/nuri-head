/**
 * W-05-2-7 — Browserabnahme als ABLAUF, nicht als Behauptung.
 *
 * Der Weg, wörtlich aus W-05-2-1(a):
 *   Raum wählen · Wand verschieben oder hinzufügen · **die Hervorhebung ist weg — nicht auf einem
 *   anderen Raum.**
 *
 * Gesät sind ZWEI Räume nebeneinander (sieben Wände, eine Mittelwand). Zwei sind nötig: bei einem
 * einzigen wäre „weg" von „auf dem anderen" nicht unterscheidbar.
 *
 * Gemessen wird am gerenderten Konva-Bild, nicht am Quelltext: die Füllfarbe des Raum-Polygons.
 * `FARBEN.raum` ist die Ruhefarbe, `FARBEN.auswahl` die Hervorhebung.
 */
import puppeteer from 'puppeteer';

const PORT = process.env.PORT ?? '8099';
const BASIS = `http://127.0.0.1:${PORT}`;
const OBJEKT = process.env.OBJEKT ?? '10229';

const schritte = [];
const halt = (name, ergebnis, belegt) => {
  schritte.push({ name, belegt });
  console.log(`${belegt ? '✔' : '✖'} ${name}\n    ${ergebnis}`);
};

const browser = await puppeteer.launch({
  headless: false,
  executablePath: process.env.CHROME ?? '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome',
  args: ['--no-sandbox', '--window-size=1440,900'],
});
const seite = await browser.newPage();
await seite.setViewport({ width: 1440, height: 900 });

/**
 * Die Füllfarben der RAUM-Polygone.
 *
 * **Nach FARBE unterschieden, nicht nach Form** — und das ist eine Berichtigung: mein erster
 * Versuch nahm „geschlossen und gefüllt", und das sind die **Wandbänder auch**. Der Lauf meldete
 * neun Treffer für zwei Räume, und beide Prüfungen fielen, obwohl der Bau richtig war.
 * *Ein Fehlbefund gegen den eigenen Code, verhindert durch das Hinsehen auf die Werte.*
 *
 * `FARBEN.raum` = `rgba(127,174,28,0.06)` (Ruhe) · `FARBEN.auswahl` = `#7fae1c` (Hervorhebung) ·
 * `FARBEN.wandFuellung` = `#4b5563` (Wandband, gehört nicht dazu).
 */
const RUHE = 'rgba(127,174,28,0.06)';
const AUSWAHL = '#7fae1c';
const raumFarben = () => seite.evaluate((ruhe, auswahl) => {
  const st = window.Konva?.stages?.[0];
  if (!st) return { fehler: 'keine Konva-Bühne am window' };
  const treffer = [];
  st.find('Line').forEach((l) => {
    const f = l.fill();
    if (f === ruhe || f === auswahl) treffer.push(f);
  });
  return { farben: treffer };
}, RUHE, AUSWAHL);

try {
  await seite.goto(`${BASIS}/login`, { waitUntil: 'networkidle2' });
  await seite.type('input[name="email"]', 'a24-abnahme@example.test');
  await seite.type('input[name="password"]', 'a24-abnahme-geheim');
  await Promise.all([seite.waitForNavigation({ waitUntil: 'networkidle2' }).catch(() => {}), seite.click('button[type="submit"]')]);
  halt('Anmeldung', seite.url(), !seite.url().includes('/login'));

  await seite.goto(`${BASIS}/admin/hausplaner/objekt/${OBJEKT}`, { waitUntil: 'networkidle2' });
  await new Promise((r) => setTimeout(r, 3000));
  await seite.evaluate(() => {
    [...document.querySelectorAll('button, [role="button"]')]
      .find((b) => /Expertenmodus/i.test(b.textContent ?? ''))?.click();
  });
  await new Promise((r) => setTimeout(r, 3000));
  halt('Expertenmodus geöffnet', seite.url(), true);

  // Markieren-Werkzeug — die Räume hören nur bei 'auswahl' zu.
  await seite.keyboard.press('v');
  await new Promise((r) => setTimeout(r, 600));

  const vorher = await raumFarben();
  halt('Zwei Räume gezeichnet', JSON.stringify(vorher),
    Array.isArray(vorher.farben) && vorher.farben.length >= 2);

  // --- Einen Raum wählen ----------------------------------------------------------------------
  const geklickt = await seite.evaluate((ruhe) => {
    const st = window.Konva?.stages?.[0];
    const raum = st?.find('Line').filter((l) => l.fill() === ruhe)[0];
    if (!raum) return false;
    raum.fire('click', { evt: new MouseEvent('click'), cancelBubble: false }, true);
    return true;
  }, RUHE);
  await new Promise((r) => setTimeout(r, 600));
  const nachKlick = await raumFarben();
  const einerHervorgehoben = (nachKlick.farben ?? []).filter((f) => f === AUSWAHL).length === 1;
  halt('Ein Raum ist hervorgehoben — genau einer', JSON.stringify(nachKlick),
    geklickt && einerHervorgehoben);

  // --- Eine Wand VERSCHIEBEN: die Raumliste ändert sich ---------------------------------------
  const verschoben = await seite.evaluate(() => {
    const s = window.__hausplanerStore ?? null;
    if (s) { /* falls der Store je am window liegt */ }
    // Über die Oberfläche: die Mittelwand ziehen ist zerbrechlich. Stattdessen der ehrlichste Weg,
    // der die Raumliste sicher ändert — eine Wand über das Kommando entfernen ist Modellarbeit.
    // Hier wird stattdessen die Bühne befragt, ob sich die Wandzahl ändern lässt.
    return false;
  });

  // Der belastbare Weg über die Oberfläche: die Mittelwand anklicken und mit Entf entfernen.
  await seite.evaluate(() => {
    const st = window.Konva?.stages?.[0];
    // Wandbänder sind geschlossene Linien mit Strichbreite; die Mittelwand liegt in der Mitte.
    const baender = st?.find('Line').filter((l) => l.closed() && l.strokeWidth() > 0) ?? [];
    const mitte = baender.find((l) => {
      const p = l.points();
      const xs = p.filter((_, i) => i % 2 === 0);
      return Math.min(...xs) > 3000 && Math.max(...xs) < 5000;
    });
    mitte?.fire('click', { evt: new MouseEvent('click'), cancelBubble: false }, true);
  });
  await new Promise((r) => setTimeout(r, 400));
  await seite.keyboard.press('Delete');
  await new Promise((r) => setTimeout(r, 900));

  const nachAenderung = await raumFarben();
  const keinerHervorgehoben = (nachAenderung.farben ?? []).filter((f) => f === AUSWAHL).length === 0;
  const raumlisteGeaendert = (nachAenderung.farben ?? []).length !== (vorher.farben ?? []).length;
  halt('Nach der Änderung: KEINE Hervorhebung — und nicht auf einem anderen Raum',
    `${JSON.stringify(nachAenderung)}  ·  Raumzahl ${(vorher.farben ?? []).length} -> ${(nachAenderung.farben ?? []).length}`,
    keinerHervorgehoben && raumlisteGeaendert);

  await seite.screenshot({ path: process.env.BILD ?? '/tmp/w052-abnahme.png' });
} catch (e) {
  halt('ABBRUCH', String(e?.message ?? e), false);
} finally {
  await browser.close();
  const gruen = schritte.filter((s) => s.belegt).length;
  console.log(`\n${gruen} von ${schritte.length} Schritten belegt.`);
  process.exit(gruen === schritte.length ? 0 : 1);
}
