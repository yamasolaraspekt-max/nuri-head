# W-09 · Treppe — PRÜFUNG

## Was eine Prüfung hier belegen muss

1. **Dass ein Fehler ein Fehler bleibt.** Drei der sieben Prüfungen sind als `fehler` eingestuft;
   sie allein entscheiden über `bestanden`.
2. **Dass eine fehlende Eingabe nicht als bestanden zählt.** Laufbreite und Durchgangshöhe werden
   **nur geprüft, wenn sie da sind** — siehe `7-GRENZEN`.
3. **Dass der Nutzungsbereich wirklich die Grenzwerte wechselt** — dieselbe Treppe, drei Bereiche,
   drei Ergebnisse.
4. **Dass die Meldung den Ist-Wert nennt**, nicht nur „nicht bestanden".

## Der Prüfpunkt, der leicht übersehen wird

**Das Schrittmaß ist gestaffelt** (`resources/planner/hausplaner/geometry/treppenBerechnung.ts:87`): innerhalb 590–650
bestanden, innerhalb 570–670 **Warnung**, sonst **Fehler**. *Eine Prüfung, die nur den grünen und
den roten Fall kennt, sieht die mittlere Stufe nicht — und die ist der häufigste Ausgang.*

## Was ich NICHT geprüft habe

**Ob DIN 18065 mehr verlangt als diese sieben Regeln.** Das ist eine Fachfrage; am Code ist nur
messbar, **was geprüft wird**, nicht **was die Norm insgesamt fordert**. *Als Frage benannt, nicht
als Zusage.*
