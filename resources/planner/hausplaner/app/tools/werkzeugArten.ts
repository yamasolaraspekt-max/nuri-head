/**
 * AUF-48 Scheibe 4b — **die sieben Werkzeugarten des 2D-Editors, umgezogen.**
 *
 * Die Union stand auf Modulebene in `HausplanerApp.tsx` und wird dort 29-mal gebraucht. Mit der
 * Auslagerung der Schiene braucht sie auch `rahmen/GruppenzeileUndSchiene.tsx`. **Ein Import aus
 * `HausplanerApp` wäre ein Ringschluss** — das Kind griffe in sein Elternteil zurück. Deshalb
 * steht sie jetzt bei den übrigen Werkzeug-Modulen.
 *
 * *Umgezogen, nicht verdoppelt: es gibt weiterhin genau eine Definition, und kein Wert hat sich
 * geändert.*
 */
export type Werkzeug = 'auswahl' | 'wand' | 'fenster' | 'tuer' | 'dach' | 'treppe' | 'decke';
