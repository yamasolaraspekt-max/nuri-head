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
/**
 * Z-05: **`kontur` ist der achte Wert** — ein Bauteil-Umriss, kein Zeichen-Primitiv.
 *
 * *Nicht `polygon`:* dieser Name gehoert zu den sechs stillgelegten Primitiven (Linie, Rechteck,
 * Kreis …), die ausdruecklich nicht in einen Bauplaner gehoeren. Sie bleiben stillgelegt.
 *
 * **Diese Union ist reiner UI-Zustand.** Im Zod-Schema kommt `werkzeug` nicht vor (gemessen: 0
 * Treffer in `domain/validation.ts`) — ein achter Wert aendert also KEIN persistiertes Schema.
 */
export type Werkzeug = 'auswahl' | 'wand' | 'fenster' | 'tuer' | 'dach' | 'treppe' | 'decke' | 'kontur';
