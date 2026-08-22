/**
 * Z1-W2-1 — **der Integrationsabgleich erreicht den Benutzer.**
 *
 * ---
 *
 * **Der Befund, gemessen am Stand `d3efc5c8` und von mir am Bau-Ausgangsstand `564833f9`
 * nachgefahren:**
 *
 * ```text
 * geometry/integrationAbgleich.ts   135 Zeilen, Testdatei vorhanden, Suite gruen
 * Aufrufer im Produktivpfad          0
 * ```
 *
 * **Die Fachlogik ist gebaut und geprueft. Sie ist nur nicht erreichbar.** *Anschliessen heisst
 * hier verdrahten, nicht bauen* — und deshalb rechnet diese Datei nichts. Sie zeigt an, was
 * `pruefeOeffnungsIntegration` liefert.
 *
 * ---
 *
 * **Warum die Meldung in den Pruefschritt gehoert und nicht in eine eigene Flaeche.**
 *
 * **Der Bedienweg:** eine bestehende Oeffnung im Grundriss waehlen, dann Fenster oder Tuer
 * konfigurieren — Schritt `Pruefung` beantwortet, ob das Konfigurierte in DIESE Oeffnung passt.
 * Das ist Yamas Beispiel aus dem Modulkopf: *Fensterbreite 1.510 gegen Oeffnung 1.480.*
 *
 * ⚠ **Beim ersten Verdrahten hatte ich das falsch** und reichte die freie Wandlaenge als
 * `oeffnungBreite`. Der Aufruf war da, die Frage war die falsche — ein 1200er Fenster gegen
 * eine 11.900er "Oeffnung". Gefunden beim Lesen der Fachlogik vor der Browserabnahme; die
 * Suite war gruen, weil sie die Fachlogik prueft und nicht meinen Aufruf.
 *
 * Der Konfigurator fuehrt seit jeher einen Schritt `Pruefung` (`SCHRITTE[3]`). Er zeigte dort
 * **drei fest verdrahtete Zeilen** — *„Masse plausibel", „Norm-Anschlag korrekt", „Rastermass"* —
 * die aussehen wie Pruefungen und keine sind. **Eine echte Pruefung daneben zu stellen ist der
 * kuerzeste Weg zum Benutzer**, und er braucht keinen Leisteneintrag: N4 des Blattes sagt
 * ausdruecklich, dass eine Warnung, die man erst anklicken muss, nicht warnt.
 *
 * *Die drei Attrappen bleiben unangetastet* — sie gehoeren nicht zu diesem Auftrag. Sie sind im
 * Bau-Bericht als Nebenbefund benannt.
 *
 * ---
 *
 * ## ⚠ Der Paketstatus — offengelegt, nicht still entschieden
 *
 * `pruefeOeffnungsIntegration` prueft **zuerst** den Status: alles ausser `approved` und
 * `integrated` ergibt einen **Blocker**. `neuesPaket()` — die einzige Paketquelle im Wizard —
 * setzt `draft`. **Ein frisch konfiguriertes Bauteil waere damit IMMER ein Konflikt**, auch bei
 * perfekt passenden Massen, und Kriterium `Z1-W2-1-b` (*„derselbe Lauf mit einer konfliktfreien
 * Oeffnung: keine Meldung"*) waere nicht erfuellbar.
 *
 * **Das Pruef-Paket traegt deshalb `approved`.** Begruendung, nicht Bequemlichkeit: der Benutzer
 * hat gerade konfiguriert und uebernimmt — das IST die Freigabe dieses Entwurfs. Der
 * Statuskonflikt zielt auf Pakete **fremder Herkunft**, die vor der Integration zu pruefen sind;
 * im Wizard entsteht das Bauteil erst.
 *
 * **Was das kostet, steht hier und nicht nur im Bericht:** die Statuspruefung greift auf diesem
 * Weg nicht. Sie greift beim Zuweisungsweg — *den es noch nicht gibt* (`paketSpeichern.ts`
 * exportiert Schreiben, kein Lesen). **Die Entscheidung gehoert dem Planner oder dem Evaluator**,
 * gemeldet in `generator-fachfrage-paketstatus-Z1-W2-1.yaml`.
 */
import React from 'react';
import type { IntegrationsErgebnis, KonfliktSchwere } from '../../geometry/integrationAbgleich';

/** Marke je Schwere — dieselben Klassen, die der Pruefschritt schon fuehrt. Kein zweites Design. */
const MARKE: Record<KonfliktSchwere, { zeichen: string; klasse: string }> = {
  blocker: { zeichen: '✕', klasse: 'hp-kw-marke--fehler' },
  warnung: { zeichen: '!', klasse: 'hp-kw-marke--warn' },
  hinweis: { zeichen: 'i', klasse: 'hp-kw-marke--ok' },
};

export interface IntegrationsKonflikteEigenschaften {
  /** Was `pruefeOeffnungsIntegration` geliefert hat. `null` = es war nichts zu pruefen. */
  ergebnis: IntegrationsErgebnis | null;
}

/**
 * Zeigt die Konflikte des Integrationsabgleichs im Pruefschritt.
 *
 * **Ohne Konflikte erscheint eine Bestaetigungszeile, kein Nichts** — sonst waere „geprueft und
 * in Ordnung" von „gar nicht geprueft" nicht zu unterscheiden, und genau diese Verwechslung ist
 * die Rot-Lage, die dieses Blatt behebt.
 */
export function IntegrationsKonflikte({ ergebnis }: IntegrationsKonflikteEigenschaften): React.ReactElement | null {
  if (!ergebnis) return null;

  if (ergebnis.konflikte.length === 0) {
    return (
      <div className="hp-kw-pruefzeile" data-pruefung="integrationsabgleich" data-ergebnis="frei">
        <span className="hp-kw-marke hp-kw-marke--ok">✓</span>
        Passt in die gewählte Öffnung — keine Konflikte.
      </div>
    );
  }

  return (
    <>
      {ergebnis.konflikte.map((k, i) => {
        const m = MARKE[k.schwere];
        return (
          <div
            key={`${k.typ}-${i}`}
            className="hp-kw-pruefzeile"
            data-pruefung="integrationsabgleich"
            data-ergebnis={k.schwere}
          >
            <span className={`hp-kw-marke ${m.klasse}`}>{m.zeichen}</span>
            <span>
              {k.meldung}
              {/* Yamas Muster: die Meldung nennt Handlungsoptionen, nicht nur den Mangel.
                  Sie kommen aus der Fachlogik und werden hier nicht erfunden. */}
              {k.optionen.length > 0 && (
                <span className="hp-kw-pruef-optionen"> — {k.optionen.join(' · ')}</span>
              )}
            </span>
          </div>
        );
      })}
    </>
  );
}
