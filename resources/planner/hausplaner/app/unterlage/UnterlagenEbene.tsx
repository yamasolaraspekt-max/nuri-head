/**
 * AUF-88-P1 / K-03 — die Referenzunterlage als unterste Ebene der Zeichenfläche.
 *
 * **Sie ist kein Knoten im Modell.** Kein `SceneNode`, keine `id` in `selectedNodeIds`, kein
 * Eintrag in `kandidat_geometrie` des Gebäudedokuments — das PDF bleibt Referenz (Master-Prompt
 * §6.1: „wird nicht automatisch zum BuildingDocument"). Sie lebt ausschließlich im UI-Zustand
 * (`state/unterlage.ts`), derselben Kategorie wie der Arbeitsbereich oder der Klappzustand der
 * Schienen — eine Bedien-Beigabe, kein Bauteil.
 *
 * **`listening={false}`** — sie fängt keine Klicks. Ein Klick „auf" die Unterlage muss durch sie
 * hindurch zum darunterliegenden Layer greifen (oder, da sie die UNTERSTE Ebene ist, ins Leere) —
 * genau das ist „gesperrt": sichtbar, aber nicht auswählbar.
 *
 * **Warum ein eigenes Modul und keine Inline-Komponente:** dieselbe Begründung wie bei
 * `GeschossFlaeche`/`WerkzeugGruppenMenue` (Befund B1) — auch wenn diese Komponente selbst nicht
 * fokussierbar ist, hielte eine Inline-Definition das geladene `HTMLImageElement` nicht über
 * Render-Zyklen hinweg fest und lüde bei jeder Mausbewegung neu.
 */
import React, { useEffect, useState } from 'react';
import { Image as KonvaImage } from 'react-konva';

/**
 * Lädt ein Bild über eine (ggf. session-authentifizierte) URL. Liefert `null`, solange nichts
 * geladen ist oder das Laden fehlschlägt — der Aufrufer zeigt dann einfach nichts, kein Absturz.
 */
function useGeladenesBild(url: string | null): HTMLImageElement | null {
  const [bild, setBild] = useState<HTMLImageElement | null>(null);

  useEffect(() => {
    if (!url) {
      setBild(null);
      return undefined;
    }
    let lebt = true;
    const img = new window.Image();
    img.onload = () => { if (lebt) setBild(img); };
    img.onerror = () => { if (lebt) setBild(null); };
    img.src = url;
    return () => { lebt = false; };
  }, [url]);

  return bild;
}

interface Props {
  bildUrl: string | null;
  /** mm pro Bild-Pixel — `kalibrierung.ts` liefert diesen Wert. */
  massstabMmProEinheit: number;
}

export function UnterlagenEbene({ bildUrl, massstabMmProEinheit }: Props): React.ReactElement | null {
  const bild = useGeladenesBild(bildUrl);
  if (!bild) return null;

  return (
    <KonvaImage
      image={bild}
      x={0}
      y={0}
      width={bild.naturalWidth * massstabMmProEinheit}
      height={bild.naturalHeight * massstabMmProEinheit}
      listening={false}
      opacity={0.85}
    />
  );
}
