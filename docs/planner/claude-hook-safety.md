# Hook-/Ausführungs-Sicherheit (Leitplanken)

Verbindliche Verbote bei allen Planner-Arbeiten:
- kein `git push`, kein Force-Push
- kein `git reset --hard`, kein `git clean -fd`
- kein Datenbank-Wipe, keine nicht freigegebenen Migrationen (DB additiv-only, Ticket ist live)
- keine Änderungen außerhalb des Slice-Scopes
- keine automatische Fortsetzung (Analyse ≠ Umsetzung)
- keine unnötige Neuerstellung vorhandener Ticket-Komponenten
- keine Testabschwächung

Hinweis: automatische Erkennung jeder Doppelimplementierung ist technisch nicht zuverlässig.
Deshalb verlangt das Verification-Gate zwingend die Reuse-Matrix + Reviewer-Urteil, bevor grün.
