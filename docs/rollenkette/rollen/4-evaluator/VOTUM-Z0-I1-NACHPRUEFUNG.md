# VOTUM Z0-I1 Stufe 1 — Nachprüfung

**ABGENOMMEN — zehn von zehn. Mein NACHBESSERN (`6916a567`) ist erledigt, dazu der dritte Punkt aus meiner Selbstmeldung.**

| Feld | Wert |
|---|---|
| Nachbesserungen | `ba6fc673` (Kriterien 10 und 12) · `5820cc7d` (Belegweg von -2) |
| Ursprünglicher Bau | `04949151` · Blattstand `7791920f` |
| Mein Stand | `7bcd9f0c` |
| gelesen_bis | 2026-08-22T22:55:12+02:00 |
| Reifegrad | code-geprüft mit Auslösung; kein Browser verlangt |

## Z0-I1-10 · Der Lauf gibt den Namen aus — BEHOBEN

Mein Befund lautete: *„`pruefeVerbindung()` gibt zurück in eine statische Variable, die niemand
liest. Eine Rückgabe an eine Variable ist keine Ausgabe."* Jetzt, selbst ausgelöst:

```
TESTLAUF db=ticket_testing halter=evaluator quelle=SELECT_DATABASE()
.....                                                    5 / 5 (100%)
OK (5 tests, 14 assertions)
```

**Die Zeile nennt die Quelle mit** — `quelle=SELECT_DATABASE()`. Damit steht im Beleg nicht nur
*welcher* Name, sondern auch, dass er aus der **Verbindung** stammt und nicht aus der
Konfiguration. Genau das war der Zweck des Kriteriums: *„Was konfiguriert ist, ist eine Absicht;
was `SELECT DATABASE()` sagt, ist die Tatsache."*

## Z0-I1-12 · Ein Baum ohne `.env` erreicht die Datenbank — BEHOBEN

Mein Befund war `Access denied for user 'forge'@'localhost'` — Host und Port wirkten, der
**Benutzer** fehlte. Jetzt im Wegwerf-Klon (`git archive HEAD`, also **nur Versioniertes**;
`.env` NEIN, `.env.testing` NEIN):

```
Rueckgabe: 0
TESTLAUF db=ticket_testing halter=evaluator quelle=SELECT_DATABASE()
OK (5 tests, 14 assertions)
```

**Und die Trennung, die das Blatt verlangt, hält** — *„Zugangsdaten bleiben ausdrücklich
unversioniert. Verlangt ist der **Weg**, nicht das Kennwort."*

```
phpunit.xml (versioniert):  DB_HOST · DB_PORT · DB_USERNAME=ticket_user   <- der WEG
TESTDB_ZUGANG            :  ${HOME}/.ticket-steuerung/testdb-zugang.env   <- ein ZEIGER
die Zieldatei            :  ausserhalb aller Baeume, NICHT versioniert    <- das GEHEIMNIS
scripts/testdb-zugang-einrichten.sh: umask 077 beim Anlegen, danach chmod 600
```

`TestDbZugang.php` läuft **vor** dem Bootstrap und füllt nur, was fehlt: *„Schon vorhanden? Dann
ist nichts nachzulegen — ein Baum mit eigener `.env` bleibt unberührt."* Kein Wurf, wenn die Datei
fehlt; ein Baum mit `.env` läuft weiter wie bisher.

**Die Schutzfrage habe ich eigens gestellt: Steht jetzt ein Kennwort im Repo?** Sechs Treffer auf
`DB_PASSWORD=` in versionierten Dateien, einzeln geprüft:

```
.env.example:16                     DB_PASSWORD=            leer, Vorlage
docs/stopp-1-…:209                  DB_PASSWORD=***         maskiert
scripts/testdb-zugang-einrichten.sh echo "DB_PASSWORD=$KENNWORT"   Variable
scripts/wberechnung-mysql-check.sh  "$WB_DB_PASS" (2x)      Variablen
docs/audit-playground.md:46         Klartext 'crm2024'      <- ein AUDIT-BEFUND ueber playground,
                                                              Commit 2e97136b vom 28.06., NICHT
                                                              durch Z0-I1 entstanden
```

**Kein neues Geheimnis im Repo.**

## Z0-I1-2 · Der Belegweg — BEHOBEN (dritter Punkt, aus meiner Selbstmeldung)

Der gelieferte Test verband selbst gegen die Produktivdatenbank; ich hatte das im ersten Votum
sogar **gelobt** und den Test viermal ausgeführt. Jetzt:

```
aktive Aufrufe verbindeMit('ticket')   0     (der eine Texttreffer ist der erklaerende Kommentar)
Ziele:  verbindeMit('information_schema')  ·  verbindeMit('ticket_g1b1_testing')
```

Dazu ein zweiter Test, der **ohne jede Verbindung** belegt, dass `ticket` unter dieselbe Abweisung
fällt (`assertSame` auf `ERLAUBT`, `assertNotSame` gegen `'ticket'`). Aus 4 Tests sind 5 geworden.

## Was unverändert gilt

Die acht Kriterien aus `6916a567` bleiben belegt: -1, -2 (Sache), -3, -4, -5 (mit Gegen-Beweis),
-6, -9 (zweifach), -11. Insel-Suite nach allen Nachbesserungen **1785/1785**, `tsc` **0**.

## Zwei Anmerkungen ohne Kriterienwirkung

**1 — Der SHA-Irrläufer ist berichtigt, und die Berichtigung ist die bessere Meldung.**
`generator-CODE_FERTIG-Z0-I1-information-schema.yaml` nannte `ergebnis_sha 7500bb7d`; das ist der
Z1-W2-8-Bündel-Commit, `information_schema` darin **0** Treffer. Die externe Prüfung hat es
gemeldet, der Dirigent weitergegeben, der Generator berichtigt — mit dem richtigen Satz:
*„Ein `ergebnis_sha`, der auf eine fremde Lieferung zeigt, ist schlimmer als keiner: er lädt zur
Messung am falschen Ort ein."* Die Umstellung liegt in `5820cc7d`, die Meldung von 22:19 war eine
**Messung**, kein Bau — auch der Abschlussbegriff wurde von `CODE_FERTIG` auf `GEMESSEN`
richtiggestellt.

**2 — Mein Befund „der Seed schreibt ohne Lease" steht weiter.** Er war kein Kriterium von Stufe 1
und ist hier nicht Gegenstand; ich nenne ihn, damit er nicht mit dieser Abnahme als erledigt gilt.

**Ball:** Integrator (Transport).
