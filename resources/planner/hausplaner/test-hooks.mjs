// Hausplaner-Test-Hooks: löst endungslose relative TS-Imports auf .ts auf — auch bei
// Dateinamen mit Punkten (scene.types, commands.types). Nur für node:test (strip-types);
// Vite/tsc lösen dieselben Imports über moduleResolution "bundler".
import { existsSync } from 'node:fs';
import { fileURLToPath, pathToFileURL } from 'node:url';

export async function resolve(specifier, context, next) {
  if (specifier.startsWith('.') && !/\.(ts|mts|js|mjs|json)$/.test(specifier) && context.parentURL) {
    const tsPfad = fileURLToPath(new URL(specifier, context.parentURL)) + '.ts';
    if (existsSync(tsPfad)) {
      return next(pathToFileURL(tsPfad).href, context);
    }
  }
  return next(specifier, context);
}
