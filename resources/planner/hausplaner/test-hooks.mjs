// Hausplaner-Test-Hooks für node:test.
//
// Zwei Aufgaben:
//
// 1. **Endungslose relative Imports auflösen** — auf `.ts` und (seit AUF-30) auf `.tsx`, auch bei
//    Dateinamen mit Punkten (scene.types, commands.types). Vite/tsc lösen dieselben Imports über
//    moduleResolution "bundler"; im Testlauf muss es dieser Hook tun.
//
// 2. **`.tsx` übersetzen** (AUF-30). `node --experimental-strip-types` entfernt nur Typen und
//    versteht **kein JSX** — deshalb konnte bis heute keine der 80+ Testdateien eine `.tsx`
//    importieren (`ERR_UNKNOWN_FILE_EXTENSION`), und Auflage 3 des A1-Votums („mindestens ein Test
//    durch den echten Render-Pfad") war **nicht erfüllbar**, nicht verletzt. Dieser Hook schließt
//    die Lücke mit `esbuild`, das ohnehin in `node_modules` liegt (Vite-Abhängigkeit) — **keine
//    neue Abhängigkeit**.
//
// Bewusst eng gehalten: übersetzt wird ausschließlich `.tsx`. `.ts` bleibt bei Nodes eigenem
// Type-Stripping — sonst liefe die gesamte Suite plötzlich über einen zweiten Übersetzer, und ein
// Fehler dort sähe aus wie ein Fehler im Code.
import { existsSync, readFileSync } from 'node:fs';
import { fileURLToPath, pathToFileURL } from 'node:url';
import { transformSync } from 'esbuild';

export async function resolve(specifier, context, next) {
  if (specifier.startsWith('.') && !/\.(ts|tsx|mts|js|jsx|mjs|json)$/.test(specifier) && context.parentURL) {
    const basis = fileURLToPath(new URL(specifier, context.parentURL));
    for (const endung of ['.ts', '.tsx']) {
      if (existsSync(basis + endung)) return next(pathToFileURL(basis + endung).href, context);
    }
  }
  return next(specifier, context);
}

export async function load(url, context, next) {
  if (!url.endsWith('.tsx')) return next(url, context);
  const quelle = readFileSync(fileURLToPath(url), 'utf8');
  const { code } = transformSync(quelle, {
    loader: 'tsx',
    format: 'esm',
    target: 'es2022',
    // 'automatic' zieht `react/jsx-runtime` selbst herein. Damit muss am Produktivcode nichts
    // geändert werden, nur damit er testbar wird.
    jsx: 'automatic',
    sourcefile: fileURLToPath(url),
    sourcemap: 'inline',
  });
  return { format: 'module', shortCircuit: true, source: code };
}
