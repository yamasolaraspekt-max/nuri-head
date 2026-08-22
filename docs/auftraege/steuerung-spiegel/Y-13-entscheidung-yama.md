# Y-13 — ENTSCHIEDEN (Yama, 21.08.2026): Datenbankrechte für die vier Testdatenbanken

```yaml
status: "ENTSCHIEDEN — nicht erneut als Rueckfrage an Yama stellen (Yama 21.08. — 'Diese Punkte sind entschieden')"
quelle: "docs/auftraege/generator-auftrag-z0-i1-testdatenbank-isolation.md @ rolle/dirigent (nicht integriert — deshalb hier in der Steuerungsstelle nachgetragen, 22.08. 07:58)"
```

**Wortlaut der Entscheidung:** `ticket_user` erhält **vollständige Rechte** (CREATE, DROP, ALTER, Migrationen,
Daten) auf `ticket_testing_%`. Die vier Testdatenbanken `ticket_testing_evaluator`, `ticket_testing_generator`,
`ticket_testing_security`, `ticket_testing_browser` werden von den Rollen **selbst verwaltet** (anlegen, migrieren,
seeden, leeren). **Parallele DB-Läufe** sind **erst nach erfolgreichem Guard- und Verbindungstest** freigegeben
(`SELECT DATABASE()` vor Migration/Seed/Truncate; `ticket` oder unbekannter Name → Abbruch).
**Produktionsdatenbanken bleiben davon unberührt.**

**Was noch offen ist (keine Entscheidung, eine Ausführung):** Das `GRANT` selbst läuft auf der normalen MySQL-Instanz
(Port 3307) mit **root**, und das root-Passwort hält Yama. Ob das GRANT bereits ausgeführt wurde, ist
**messbar**: `SHOW GRANTS FOR CURRENT_USER()` als `ticket_user` (Kriterium Z0-I1, Teil A). Solange es fehlt,
baut Z0-I1 nur Teil B (Guard, `TEST_ROLLE`, Rollenwahl), und die vier Datenbanken existieren nicht.
**Bitte an Yama (keine Rückfrage, eine Handlung):** einmal als root ausführen, falls noch nicht geschehen:
`GRANT ALL PRIVILEGES ON \`ticket_testing_%\`.* TO 'ticket_user'@'localhost'; FLUSH PRIVILEGES;`
Danach messen die Rollen selbst.
