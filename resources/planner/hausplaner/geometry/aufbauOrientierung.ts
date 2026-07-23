/**
 * Korrektur (nach Eingabeaufforderung 15/16): Orientierung aufrecht STEHENDER Dachaufbauten
 * (Gaube, Kamin). Diese stehen LOTRECHT (Welt-Hoch), NICHT senkrecht zur Dachschräge.
 *
 * URSACHE des „Schwebens" (vom Inhaber gemeldet): Die Gaube/der Kamin wurde mit der Flächen-
 * Quaternion orientiert (lokales Y = Flächennormale). Bei 40–45° Dachneigung kippt das Bauteil
 * dadurch ~40–45° nach außen/oben weg -> es lehnt von der Dachfläche ab und wirkt schwebend.
 * Eine echte Gaube und ein echter Kamin stehen aber LOTRECHT (Gaubenfront/Schornstein senkrecht).
 *
 * Diese reine Funktion liefert die orthonormale, rechtshändige Basis (xAxis/yAxis/zAxis), die der
 * 3D-Engine als `verticalSurfaceQuaternion` dient:
 *   yAxis = Welt-Hoch (0,1,0)                  -> Aufbau steht lotrecht (plumb)
 *   zAxis = horizontale Falllinie zur Traufe   -> Gaubenfront/Fenster zeigt nach außen (down-slope)
 *   xAxis = yAxis × zAxis                       -> Breite parallel zur Traufe (horizontal)
 *
 * Eingabe `vDown` = gespeicherte V-Achse der Fläche (Traufe->First, zeigt die Schräge HINAUF).
 * Die Falllinie zur Traufe ist daher -vDown; horizontal projiziert ergibt sie die Front-Richtung.
 *
 * Rein (keine THREE-Abhängigkeit, testbar). KEINE Dacheindeckung/Material/Statik.
 */

export interface Vec3 { x: number; y: number; z: number; }

function laenge(v: Vec3): number {
  return Math.hypot(v.x, v.y, v.z);
}
function norm(v: Vec3): Vec3 {
  const l = laenge(v) || 1;
  return { x: v.x / l, y: v.y / l, z: v.z / l };
}
function cross(a: Vec3, b: Vec3): Vec3 {
  return { x: a.y * b.z - a.z * b.y, y: a.z * b.x - a.x * b.z, z: a.x * b.y - a.y * b.x };
}

export interface AufbauBasis { xAxis: Vec3; yAxis: Vec3; zAxis: Vec3; }

/**
 * Lotrechte Basis für aufrecht stehende Aufbauten. `vDown` = V-Achse der Fläche (zeigt die Schräge
 * hinauf). zAxis = horizontale Projektion von -vDown (Falllinie zur Traufe); bei (nahezu) waagerechter
 * Fläche fehlt die Falllinie -> stabiler Fallback (0,0,1), der Aufbau steht trotzdem lotrecht.
 */
export function stehendeAufbauBasis(vDown: Vec3): AufbauBasis {
  const yAxis: Vec3 = { x: 0, y: 1, z: 0 };
  // Falllinie zur Traufe = -vDown, auf die Horizontale projiziert (y-Anteil entfernen).
  let z: Vec3 = { x: -vDown.x, y: 0, z: -vDown.z };
  if (z.x * z.x + z.z * z.z < 1e-9) z = { x: 0, y: 0, z: 1 }; // Flachdach-/Entartungs-Fallback
  const zAxis = norm(z);
  const xAxis = norm(cross(yAxis, zAxis)); // = yAxis × zAxis -> rechtshändig, horizontal
  return { xAxis, yAxis, zAxis };
}

/**
 * true, wenn dieser Aufbautyp LOTRECHT steht (Gaube/Kamin) und daher die lotrechte Orientierung
 * braucht. Liegende Aufbauten (Dachfenster/Lüfter/Lichtkuppel) bleiben in der Dachebene
 * (Flächen-Quaternion) — sie sollen sich mit der Schräge neigen.
 */
export function istStehenderAufbau(art: string): boolean {
  return art === 'chimney'
    || art === 'schleppgaube' || art === 'trapezgaube'
    || art === 'flachgaube' || art === 'giebelgaube' || art === 'spitzgaube';
}
