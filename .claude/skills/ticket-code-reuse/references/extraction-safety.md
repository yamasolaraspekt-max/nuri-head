# Extraktions-Sicherheit (R3)

Extrahiere vorhandenen Ticket-Code nur, wenn:
1. mindestens zwei Fachbereiche dieselbe Logik benötigen,
2. die gemeinsame Verantwortung eindeutig benannt werden kann,
3. der bestehende Ticket-Vertrag erhalten bleibt,
4. die Änderung separat testbar ist,
5. die Extraktion nicht mit einer großen Fachfunktion vermischt wird.

Vorgehen:
- Verhalten VOR der Extraktion charakterisieren (Characterization-Tests).
- Gemeinsame Modultests erstellen.
- Ticket-Regression ausführen (muss grün bleiben).
- Planner-Integration testen.

Vermeiden: große pauschale „Shared"-Ordner, unklare Helper-Sammlungen, abstrakte Basisklassen
ohne konkreten Nutzen, Massenverschiebungen, Umbenennungen ohne fachlichen Mehrwert,
gleichzeitiger Refactor und Featurebau.

Schutzleitplanken bei Reuse-Arbeit: kein `git push`, kein Force-Push, kein `git reset --hard`,
kein `git clean -fd`, kein DB-Wipe, keine nicht freigegebenen Migrationen, keine Änderungen
außerhalb des Slice-Scopes, keine automatische Fortsetzung.
