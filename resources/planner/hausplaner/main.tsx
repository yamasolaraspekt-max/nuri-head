/**
 * Hausplaner P0 — Standalone-Entry (Muster: src/planer/main.tsx der Dach-Insel).
 * Mountet in die Blade-Seite (#hausplaner-root); die Szene kommt EINGEBETTET aus
 * <script type="application/json" id="hausplaner-scene"> (▲K3 — kein Lade-Fetch).
 * Die Szene wird VOR dem Mount mit Zod validiert: unbekannte Schema-Version oder
 * unbekannter Node-Typ ⇒ Laden wird mit sichtbarer Meldung verweigert (Kante).
 */
// AUF-38 Scheibe 1 — der erste Import der Stilschicht. Er allein laesst den Bau
// `public/hausplaner/hausplaner.css` erzeugen; die bewachten `<link>` in beiden Blades greifen
// daraufhin von selbst. Keine Bau-, keine Blade-Aenderung noetig.
import './hausplaner.css';
import React from 'react';
import ReactDOM from 'react-dom/client';
import { HausplanerStudio } from './app/HausplanerStudio';
import { useHausplanerStore } from './store/hausplanerStore';
import { usePlannerUiStore } from './app/state/uiState';
import { leseRechte, RECHTE_ATTRIBUT } from './app/state/rechte';
import { leseProjekte, PROJEKTE_ATTRIBUT } from './app/state/projekte';
import { leseObjektkopf, OBJEKTKOPF_ATTRIBUT } from './app/state/objektkopf';
import { setzePaketZiel, PAKETE_URL_ATTRIBUT } from './app/state/paketSpeichern';
import { setzeTokenVariablen } from './app/stil/tokenVariablen';
import { sceneDocumentSchema, validateSceneIntegrity, migriereSzene } from './domain/validation';
import { ladeFixture, fixtureNameAusSearch } from './fixtures/studioFixtures';
import type { SceneDocument } from './domain/scene.types';

const mount = document.getElementById('hausplaner-root');
const szenenElement = document.getElementById('hausplaner-scene');

function fehlerAnzeigen(titel: string, details: string[]): void {
  if (!mount) {
    return;
  }
  mount.innerHTML = `
    <div style="max-width:720px;margin:60px auto;background:#fff;border:1px solid #ef4444;border-radius:12px;padding:28px;font-family:Inter,system-ui,sans-serif;color:#1f2937;">
      <h1 style="font-size:16px;margin:0 0 8px;color:#b91c1c;">${titel}</h1>
      <p style="font-size:13px;color:#6b7280;">Der Plan wurde NICHT geladen, damit nichts stillschweigend verloren geht.</p>
      <pre style="font-size:11.5px;background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:10px;white-space:pre-wrap;">${details.slice(0, 12).join('\n')}</pre>
    </div>`;
}

if (mount && szenenElement) {
  // Sicht-Abnahme (additiv, hinter Flag): `?fixture=<name>` lädt eine deterministische Szene STATT der
  // eingebetteten — für den reproduzierbaren Evaluator-Snapshot (mit `?capture=1`). Ohne Flag unverändert.
  const fixture = typeof window !== 'undefined'
    ? ladeFixture(fixtureNameAusSearch(window.location.search))
    : null;

  let roh: unknown;
  if (fixture) {
    roh = fixture;
  } else {
    try {
      roh = JSON.parse(szenenElement.textContent ?? 'null');
    } catch {
      fehlerAnzeigen('Eingebettete Szene ist kein gültiges JSON.', []);
      throw new Error('hausplaner: Szene unlesbar');
    }
  }

  // D-a: v1 → v2 Lade-Migration (roofs: []) VOR der Schema-Prüfung; v1-Inhalt bleibt unverändert.
  const geparst = sceneDocumentSchema.safeParse(migriereSzene(roh));
  if (!geparst.success) {
    fehlerAnzeigen(
      'Szene entspricht nicht dem Schema (Version/Node-Typ unbekannt?).',
      geparst.error.issues.map((i) => `${i.path.join('.')}: ${i.message}`),
    );
  } else {
    const integritaet = validateSceneIntegrity(geparst.data);
    if (integritaet.length > 0) {
      fehlerAnzeigen('Szene ist in sich widersprüchlich.', integritaet);
    } else {
      const scene = geparst.data as SceneDocument;
      const speichernUrl = mount.dataset.speichernUrl ?? '';
      const csrf = document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')?.content ?? '';

      useHausplanerStore.getState().init(scene, speichernUrl, csrf);
      // AUF-60: die Rechte des angemeldeten Nutzers über DIESELBE Naht wie `data-speichern-url`.
      // Fehlt das Attribut, liefert `leseRechte` die leere Liste — das Minimum, nicht das Maximum.
      usePlannerUiStore.getState().setRechte(leseRechte(mount.dataset[RECHTE_ATTRIBUT]));
      // AUF-78: dieselbe Naht — die Liste kommt fertig aus dem Controller, hier wird nur gelesen.
      usePlannerUiStore.getState().setProjekte(leseProjekte(mount.dataset[PROJEKTE_ATTRIBUT]));
      // AUF-83-T3 / K-01: dieselbe Naht — der Objektkopf wandert aus der Blade-Leiste in die
      // Kopfleiste der Insel. Fehlt das Attribut (Studio: kein Objekt), bleibt er `null`.
      usePlannerUiStore.getState().setObjektkopf(leseObjektkopf(mount.dataset[OBJEKTKOPF_ATTRIBUT]));
      // AUF-81: dieselbe Naht — ohne Ziel wird nicht gespeichert und nichts behauptet.
      setzePaketZiel(mount.dataset[PAKETE_URL_ATTRIBUT] ?? null, csrf);
      // AUF-38 Scheibe 1: die Tokens als CSS-Variablen — erzeugt aus `T`, nicht abgeschrieben.
      // Noch benutzt sie niemand; das ist der Boden, auf dem die Scheiben 2-8 stehen.
      setzeTokenVariablen();
      ReactDOM.createRoot(mount).render(
        <React.StrictMode>
          <HausplanerStudio />
        </React.StrictMode>,
      );
    }
  }
} else {
  // eslint-disable-next-line no-console
  console.error('[hausplaner] Mount (#hausplaner-root) oder Szene (#hausplaner-scene) fehlt.');
}
