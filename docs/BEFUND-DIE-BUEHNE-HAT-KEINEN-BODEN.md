# Vier Befunde sind derselbe — die Prüfbühne hat keinen Boden

> **Auf Yamas Frage vom 13.08.:** *„gibt es aufgaben für Yama, welche du übernehmen kannst du
> erledigen kannst"*

**Frisch gemessen, nicht aus Notizen.** Ich habe alle Yama-Posten mit dem korrigierten Anker gelesen
(`auftrag:` **oder** Überschrift) und dabei einen gefunden, den ich noch nicht kannte — und beim
Nachmessen ist er mit drei anderen zu einer einzigen Sache zusammengefallen.

## Der Posten, wie er dasteht

`A-17`, Block Z.2700, vom Evaluator selbst offengelegt:

> *„`ticket_testing` hatte 0 Nutzer, ohne Anmeldung keine Browserabnahme, also hat er einen
> Probenutzer angelegt (§15 vor dem Schreiben belegt: vorher 0, nachher 1). **Der Nutzer STEHT
> NOCH.** Räumen ist eine produktive Datenoperation und braucht Yamas Freigabe."*

## Nachgemessen — und der Posten trifft nicht mehr zu

```text
ticket_testing, nur lesend, §15-Ziel vor dem Lauf bestaetigt:
  users: 1   ->  id=268  w052-eval@example.test

Die beiden Browserabnahme-Skripte melden sich an mit:
  scripts/a24-browserabnahme.mjs:17    a24-abnahme@example.test
  scripts/w052-browserabnahme.mjs:60   a24-abnahme@example.test
```

**Der Nutzer aus A-17 ist längst weg.** Was dasteht, ist ein anderer — vom W-05/2-Evaluator angelegt.
**Und der Nutzer, den beide Skripte brauchen, existiert nicht:** würde man sie heute starten, käme
keine Anmeldung zustande.

> **Der Posten „Probenutzer räumen" ist in seiner ursprünglichen Form gegenstandslos.** *Nicht weil
> jemand geräumt hätte, sondern weil die Testdatenbank ihn von selbst verloren hat.*

## Und damit fallen vier Befunde zu einem zusammen

```text
1  doc 36 verschwunden (mein Befund, 12.08.)
     jeder Suite-Lauf mit RefreshDatabase leert ticket_testing;
     70 von 137 Testdateien nutzen es.

2  A-17: "der Probenutzer steht noch, Raeumen braucht Yamas Freigabe"
     -> er steht NICHT mehr. Die Suite hat ihn geraeumt, nicht Yama.

3  W-05/2 RELEASE_BLOCKED (meine Entscheidung von heute)
     Browserabnahme nicht fahrbar: 0 Dokumente, 0 Objekte in ticket_testing.

4  das Testkennwort in ZWEI Skripten (mein Befund, 13.08.)
     beide melden sich mit einem Nutzer an, den es nicht gibt.

DASSELBE: die Buehne hat keinen dauerhaften Boden. Jede Rolle legt an,
was sie braucht; der naechste Suite-Lauf raeumt es weg; die uebernaechste
Rolle findet eine leere Datenbank und legt wieder an.
```

## Was ich davon erledigt habe — und was nicht

**Erledigt (Ablesung):** Der A-17-Posten ist beantwortet. *Es gibt nichts zu räumen, was Yamas
Freigabe bräuchte — der damals angelegte Nutzer ist ohne Zutun verschwunden.* **Der Posten kann
geschlossen werden, und zwar mit der Messung als Beleg, nicht mit einer Handlung.**

**Nicht erledigt und ausdrücklich nicht vertreten:** den vorhandenen Nutzer `w052-eval@example.test`
zu löschen. *Das ist eine Datenoperation, und sie steht in meiner Ausschlussliste — unabhängig davon,
dass sie hier harmlos aussieht.* **Und es wäre auch das Falsche:** würde ich ihn räumen, wäre die
nächste Browserabnahme erneut blockiert.

## Die Frage, die stattdessen bei dir liegt

**Nicht „räumen oder stehen lassen", sondern:**

```text
Soll die Pruefbuehne einen DAUERHAFTEN Boden bekommen —
einen Seed mit Pruefnutzer und Pruefobjekt, der jeden Suite-Lauf ueberlebt?

DAFUER   drei Befunde verschwinden auf einmal: kein Raeum-Posten mehr,
         keine blockierte Browserabnahme, kein fest verdrahtetes Kennwort
         in zwei Skripten. Laravel hat dafuer Seeder; das Muster existiert
         im Haus bereits (studioFixtures.ts fuer die Insel-Seite).
DAGEGEN  ein Seed ist Code und will gepflegt werden; und wer ihn falsch
         schneidet, hat eine zweite Wahrheit neben den Fixtures.

WAS ES NICHT IST: eine Fachfrage. Es ist eine Entscheidung ueber die
Pruefinfrastruktur — deshalb lege ich sie vor, statt sie zu treffen.
```

> **Solange das nicht entschieden ist, wird W-05/2 immer wieder an derselben Stelle stehen bleiben.**
> *Der Generator kann heute säen und morgen fahren — aber der übernächste Suite-Lauf räumt es wieder
> weg, und der nächste Auftrag mit sichtbarer Änderung beginnt von vorn.*
