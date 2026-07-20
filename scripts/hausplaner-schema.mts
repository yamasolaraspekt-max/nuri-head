import { readFile, writeFile } from 'node:fs/promises';
import { resolve } from 'node:path';
import { z } from 'zod/v4';
import { sceneDocumentSchema } from '../resources/planner/hausplaner/domain/validation.ts';

const ziel = resolve('resources/planner/hausplaner/domain/scene-document-v2.schema.json');
const schema = {
  $id: 'https://ticket.local/schemas/hausplaner/scene-document-v2.schema.json',
  title: 'Hausplaner SceneDocument v2',
  ...z.toJSONSchema(sceneDocumentSchema, {
    target: 'draft-2020-12',
    reused: 'ref',
  }),
};
const inhalt = `${JSON.stringify(schema, null, 2)}\n`;

if (process.argv.includes('--check')) {
  const vorhanden = await readFile(ziel, 'utf8').catch(() => '');
  if (vorhanden !== inhalt) {
    console.error('Hausplaner-JSON-Schema ist nicht aktuell. Fuehre npm run schema:hausplaner aus.');
    process.exitCode = 1;
  }
} else {
  await writeFile(ziel, inhalt, 'utf8');
}
