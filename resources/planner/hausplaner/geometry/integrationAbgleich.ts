/**
 * Hausplaner — Integrations-/Konfliktprüfung: autarke Konfiguration → Gesamtprojekt.
 *
 * Setzt Yamas Abschnitt 14 („Import- und Abgleichslogik") um: wenn ein autark konfiguriertes
 * Objekt später in ein Gebäude eingefügt wird, prüft die Software Geometrie, Maße, Anschlüsse,
 * Katalogstand, Regelkompatibilität und Veralterung — und meldet Abweichungen als Konflikte
 * MIT Handlungsoptionen (nie stillschweigend anpassen).
 *
 * Reine, deterministische Prüf-Logik ohne Szene-Zugriff. Strukturelle Minimal-Typen.
 */

import type { ConfiguratorPackage, ConfiguratorStatus } from './configuratorPackage';
import { kannIntegrieren } from './configuratorPackage';

export type KonfliktTyp =
  | 'status' | 'veraltet' | 'geometrie' | 'masse' | 'anschluss'
  | 'kollision' | 'material' | 'katalog' | 'regelwerk';

export type KonfliktSchwere = 'blocker' | 'warnung' | 'hinweis';

export interface IntegrationsKonflikt {
  typ: KonfliktTyp;
  schwere: KonfliktSchwere;
  meldung: string;
  /** Handlungsoptionen für den Nutzer (Yamas Muster: „Öffnung anpassen / Maß ändern / anderes wählen"). */
  optionen: string[];
}

export interface IntegrationsErgebnis {
  integrierbar: boolean;
  konflikte: IntegrationsKonflikt[];
}

/** Ziel-Kontext beim Einsetzen einer autarken Öffnung (Fenster/Tür) in eine Wand. */
export interface OeffnungsZiel {
  oeffnungBreite: number;
  oeffnungHoehe: number;
  /** vorhandene Anschluss-Medien am Zielort (für Ports-Abgleich). */
  vorhandenePorts?: string[];
  /** Katalog-Version, die das Projekt aktuell führt. */
  katalogVersion?: number;
  toleranz?: number; // mm, Standard 5
}

function statusKonflikt(status: ConfiguratorStatus): IntegrationsKonflikt | null {
  if (status === 'approved' || status === 'integrated') return null;
  if (status === 'outdated') {
    return { typ: 'veraltet', schwere: 'blocker',
      meldung: 'Konfiguration ist veraltet (durch spätere Änderung berührt) — vor Übernahme neu prüfen.',
      optionen: ['Neu berechnen', 'Änderungen vergleichen', 'Bestehenden Stand behalten'] };
  }
  return { typ: 'status', schwere: 'blocker',
    meldung: `Konfiguration ist nicht freigegeben (Status: ${status}). Nur freigegebene Pakete sind übernehmbar.`,
    optionen: ['Prüfen und freigeben', 'Als Entwurf weiterbearbeiten'] };
}

/** Katalog-Aktualität: führt das Projekt eine neuere Katalogversion als das Paket? */
function katalogKonflikt(paket: ConfiguratorPackage, projektKatalogVersion?: number): IntegrationsKonflikt | null {
  if (projektKatalogVersion === undefined) return null;
  const paketVersionen = paket.catalogItems.map((c) => c.katalogVersion ?? 0);
  const aeltest = paketVersionen.length ? Math.min(...paketVersionen) : projektKatalogVersion;
  if (aeltest < projektKatalogVersion) {
    return { typ: 'katalog', schwere: 'warnung',
      meldung: `Katalogversion des Pakets (${aeltest}) ist älter als im Projekt (${projektKatalogVersion}).`,
      optionen: ['Auf aktuellen Katalog aktualisieren', 'Version beibehalten'] };
  }
  return null;
}

/**
 * Öffnung (Fenster/Tür) in eine vorhandene Wandöffnung einsetzen. Deckt Yamas konkretes Beispiel
 * ab: Fensterbreite 1.510 vs. Öffnung 1.480 → „30 mm breiter" mit drei Optionen.
 */
export function pruefeOeffnungsIntegration(paket: ConfiguratorPackage, ziel: OeffnungsZiel): IntegrationsErgebnis {
  const konflikte: IntegrationsKonflikt[] = [];
  const tol = ziel.toleranz ?? 5;

  const sk = statusKonflikt(paket.status);
  if (sk) konflikte.push(sk);

  const kk = katalogKonflikt(paket, ziel.katalogVersion);
  if (kk) konflikte.push(kk);

  const b = paket.geometry?.breite;
  const h = paket.geometry?.hoehe;
  if (b === undefined || h === undefined) {
    konflikte.push({ typ: 'geometrie', schwere: 'blocker',
      meldung: 'Paket hat keine Hüllmaße (Breite/Höhe) — Geometrieabgleich nicht möglich.',
      optionen: ['Maße im Konfigurator ergänzen'] });
  } else {
    const dB = b - ziel.oeffnungBreite;
    const dH = h - ziel.oeffnungHoehe;
    if (Math.abs(dB) > tol) {
      konflikte.push({ typ: 'masse', schwere: 'blocker',
        meldung: `Breite ${b} mm passt nicht in die Öffnung ${ziel.oeffnungBreite} mm (${dB > 0 ? '+' : ''}${dB} mm).`,
        optionen: ['Öffnung anpassen', 'Objektbreite ändern', 'anderes Objekt wählen'] });
    }
    if (Math.abs(dH) > tol) {
      konflikte.push({ typ: 'masse', schwere: 'blocker',
        meldung: `Höhe ${h} mm passt nicht in die Öffnung ${ziel.oeffnungHoehe} mm (${dH > 0 ? '+' : ''}${dH} mm).`,
        optionen: ['Öffnung anpassen', 'Objekthöhe ändern', 'anderes Objekt wählen'] });
    }
  }

  // Anschluss-Ports: jeder geforderte Port muss am Ziel vorhanden sein.
  if (ziel.vorhandenePorts) {
    const fehlende = paket.connectionPorts
      .map((p) => p.medium)
      .filter((m) => !ziel.vorhandenePorts!.includes(m));
    for (const m of [...new Set(fehlende)]) {
      konflikte.push({ typ: 'anschluss', schwere: 'warnung',
        meldung: `Benötigter Anschluss „${m}" ist am Zielort nicht vorhanden.`,
        optionen: [`Anschluss ${m} ergänzen`, 'ohne diesen Anschluss übernehmen'] });
    }
  }

  return { integrierbar: !konflikte.some((k) => k.schwere === 'blocker'), konflikte };
}

/**
 * Allgemeiner Paket-Vorabgleich (raumbezogene Module: Küche/Bad/Heizung). Prüft Status,
 * Veralterung, Katalog und ob überhaupt integrierbar. Fachspezifische Geometrie prüfen die
 * jeweiligen Module ergänzend.
 */
export function pruefePaketIntegration(
  paket: ConfiguratorPackage,
  opt?: { projektKatalogVersion?: number },
): IntegrationsErgebnis {
  const konflikte: IntegrationsKonflikt[] = [];
  const sk = statusKonflikt(paket.status);
  if (sk) konflikte.push(sk);
  const kk = katalogKonflikt(paket, opt?.projektKatalogVersion);
  if (kk) konflikte.push(kk);
  return { integrierbar: kannIntegrieren(paket) && !konflikte.some((k) => k.schwere === 'blocker'), konflikte };
}
