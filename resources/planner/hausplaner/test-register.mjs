// Registriert die Hausplaner-Test-Hooks (node --import ./src/hausplaner/test-register.mjs).
import { register } from 'node:module';

register('./test-hooks.mjs', import.meta.url);
