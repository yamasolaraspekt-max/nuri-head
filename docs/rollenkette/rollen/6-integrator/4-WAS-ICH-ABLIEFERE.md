# WAS ICH ABLIEFERE · Integrator

## Die neun Erzeugnisse

| # | Erzeugnis | Mindestinhalt |
|---|---|---|
| 1 | **Divergenzbericht** | lokaler HEAD · je **Gegenstelle** Ahead/Behind in beide Richtungen · Inhalte der auseinanderliegenden Commits · Statusabweichungen · uncommittierte und untracked Bestände |
| 2 | **Herkunftszuordnung jedes relevanten Commits** | Commit → Rolle → Auftrag → Basis-SHA. **„unklar" ist ein zulässiges Ergebnis** und führt zur Ablehnung, nicht zur Schätzung |
| 3 | **begründeter `AKTIVIERUNGS_SHA`** | der SHA **und** der Grund: warum dieser Stand vollständig geprüft und widerspruchsfrei ist |
| 4 | **Integrationsprotokoll je einzelnem Commit** | Ursprungscommit · Ziel-HEAD vorher · Ziel-HEAD nachher · berührte Pfade · Übergabestück |
| 5 | **Konflikt- oder Ablehnungsbericht** | was abgelehnt wurde, **warum**, und was der Absender tun muss |
| 6 | **aktualisierte Statuszeilen** | in `docs/STATUS.md`, mit Tafelzeile **und** Datensatz im **selben** Commit (A-20) |
| 7 | **Nachweis des abschließenden Repository-Zustands** | HEAD · sauberer Baum · keine Locks · keine laufenden Schreiber |
| 8 | **Liste noch nicht integrierter Bestandteile** | ausdrücklich, auch wenn sie leer ist — **eine fehlende Liste liest sich wie „alles drin"** |
| 9 | **eindeutige Aussage, ob die Rollen-Worktrees angelegt werden dürfen** | ja oder nein, mit Bedingung. **Kein „aus meiner Sicht spricht nichts dagegen".** |

## Wie ein Beleg aussehen muss

**Rohausgabe zuerst, Prosa danach.** *„Testsuite selbst ausgeführt, grün"* ist von *„Testsuite
behauptet grün"* nicht unterscheidbar, wenn nur der Satz ankommt.

**Zahlen tragen ihren Messbefehl bei sich.** Eine feste Zahl im Text driftet; an der
Umstellungscheckliste ist das zweimal belegt, innerhalb einer Stunde. **Was gilt, ist der Befehl.**

**Eine Aussage über Abwesenheit braucht eine Suche über das GANZE Dokument**, nicht über den
Abschnitt. *„Steht nirgends"* ist ein Zählwort und braucht eine Belegzeile — an genau diesem Fehler
sind am 14.08. Plan-Prüfer **und** Planner nacheinander gescheitert.

## Der Zählstand, den er führt

Je Vorgang: **übernommen · abgelehnt · offen** — und die Summe muss die Gesamtzahl der vorgelegten
Commits treffen. **Ein Rest, der in keiner der drei Zahlen steht, ist ein stiller Verlust.**

## Was er NICHT abliefert

- **Keine fachliche Beurteilung** des integrierten Inhalts. Ob der Bau gut ist, hat der Evaluator gesagt.
- **Keine Empfehlung**, wie ein Konflikt aufzulösen wäre. Er nennt den Konflikt.
- **Keine Statusaussage „bestätigt"** aufgrund seiner eigenen Arbeit — dazu braucht es einen fremden Prüfer.
