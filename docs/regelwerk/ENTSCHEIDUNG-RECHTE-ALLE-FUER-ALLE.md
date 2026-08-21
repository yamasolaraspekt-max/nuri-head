# ENTSCHEIDUNG — Alle Rechte für alle Nutzer (Yama, 21.08.2026)

> Teil des [Regelwerks](REGISTER.md) — Entscheidung mit Reichweite, Autorität Yama.

## Wortlaut und Hergang

Yama im Gespräch: *„Gib den Planner vollen Zugriff für alle Rechte in das Ticket-System und auch
für dich."* Der Dirigent hat die Lesart **zweimal** gegengeprüft und die Folge ausdrücklich benannt
(„alle 52 Mitarbeiter-Accounts = alle Rechte im ganzen CRM, faktisch jeder ein Admin; hebt die
Sicherheits-Welle 0 für interne Nutzer auf"). **Yama hat genau diese Lesart bestätigt.**
Damit ist es seine Entscheidung; der Einwand ist gehört, dokumentiert und überstimmt.

## Was gilt

1. **Jeder Nutzer des Ticket-Systems hat alle Rechte** — alle Permission-Items mit allen Aktionen
   (read/add/update/delete), einschließlich des neuen Items **`Planner`** (Y-6 entschieden: Item
   wird angelegt) und einschließlich fremder Tagesberichte/GPS im Planner (Y-9 entschieden).
2. Der Dirigent/die Claude-Rollen arbeiten lokal mit dem Admin-Zugang (`is_admin`-Bypass) — nichts
   Zusätzliches nötig.

## Wie es gebaut wird — und warum so (Dirigent, abgewogen)

**Ein Schalter, keine Datenmutation.** `hasPermission()` (`app/Models/User.php`) und damit die
`permission:`-Middleware, Blade-Sichtbarkeit und alle Aufrufer erhalten **eine** vorgeschaltete
Prüfung: ist `RECHTE_ALLE_FUER_ALLE=true` (Config `rechte.alle_fuer_alle`), gilt jede Rechteprüfung
als bestanden. **Gründe:**
- **Rückweg in einer Zeile:** Schalter auf `false` → die vorhandenen Tore wirken sofort wieder;
  keine 52×N `user_rolls`-Zeilen, die man zurückbauen müsste.
- **Eine Wahrheit:** die Entscheidung steht an genau einer Stelle im Code, benannt, mit Verweis auf
  dieses Blatt — statt verteilt in Datenzeilen, die in drei Monaten niemand mehr einer Entscheidung
  zuordnet.
- **Welle 0 bleibt Struktur:** die Gates (W0-1/2/3/4/5/6) werden weiter gebaut und **in beiden
  Schalterstellungen getestet** — damit der Tag, an dem Yama differenzieren will, kein Neubau ist.
- **Gilt auch für künftige Nutzer** („alle"), ohne Seeder-Pflege.

**Nicht gebaut:** kein `is_admin=1` für alle (Admin ist mehr als Rechte: Nutzerverwaltung über
`isSuperAdmin()`-Sonderpfade bleibt den 3 Admins); keine Änderung an Produktionsdaten.

## Was die Entscheidung NICHT aufhebt

- Ownership-Prüfungen, die **Integrität** schützen (fremde Objekte **überschreiben**, W0-2 Schreibpfad;
  Melder-Spoofing W0-5 A-4), bleiben wirksam — Yamas Wort galt dem *Zugriff/Sehen*, nicht dem
  Fälschen von Urheberschaft. Der Dirigent legt diese Abgrenzung offen; widerspricht Yama, fällt auch sie.
- Die Wächter-Ratsche (W0-4) läuft weiter — sie misst Struktur, nicht Wirkung.
- Token-Ablauf (Y-10) und `is_active` (A-7) sind davon unberührt und bleiben offen.

## Rückweg
`RECHTE_ALLE_FUER_ALLE=false` in `.env` → Tore wirken; danach Rechte gezielt vergeben
(Planner-Item an Planer/Monteure, Customer an Vertrieb usw.). Kein Datenverlust.
