# ⇒ EVALUATOR-AUFTRAG — Zustands-Inventur: wo lügt die Oberfläche noch über ihre Sperren?

**Vom:** Planner · **26.07.2026, 07:15** · **Anlass:** AUF-70. **Dein Stapel ist leer** — das hier
ist keine Abnahme, sondern eine Messung, die nur du führen kannst.

**Vorher gelesen:** HEAD `373dfe9` · `git log -6` · Tafel §3a/§3b · deine Voten zu AUF-59, AUF-68,
AUF-64 · `docs/planner/ux-befund-layout-alle-ebenen-2026-07-25.md`.

---

## 0. Warum ich dich darum bitte, und nicht den Generator

Gestern Nacht hat Yama gemeldet, Rückgängig und Wiederholen seien „nicht aktiv". **Sie
funktionieren** — ich habe es im laufenden Programm durchgespielt. Was nicht funktioniert, war die
**Auskunft der Oberfläche über sich selbst**: gesperrt und frei waren dort Pixel für Pixel
identisch — Deckkraft 1, Zeiger `pointer`, gleiche Farben.

**Das ist eine Wahrnehmungsfrage, und Wahrnehmungsfragen misst man, statt sie zu vermuten.** Du
hast im AUF-68-Votum den Kontrast des Trennstrichs **selbst gerechnet** (1,09–1,14:1) und damit die
Aussage des Generators bestätigt und zugleich geschärft. Genau diese Fähigkeit brauche ich hier.

**Und der Anlass ist ein Fehler von mir:** AUF-59 hat die drei Zustände hergestellt — für **eine**
Zeile, weil ich den Posten an eine Zeile gebunden habe statt an die Darstellung. **Ich weiß nicht,
wo das sonst noch fehlt. Das ist der ganze Punkt dieses Auftrags.**

## 1. Was ich wissen will

**Für jedes bedienbare Element der Insel: unterscheidet sich sein gesperrter Zustand messbar von
seinem freien?**

Nicht „sieht anders aus" — **gemessen**, mit denselben Größen, die die Frage entschieden haben:

| Größe | warum sie zählt |
|---|---|
| `opacity` | der übliche, aber allein zu schwache Träger |
| `cursor` | ein `pointer` auf einem toten Knopf ist ein Versprechen |
| Schriftfarbe · Rahmen · Hintergrund | die drei, die bei `knopf()` **identisch** waren |
| Kontrast des Unterschieds | dein Verfahren aus AUF-68 — eine Zahl, kein Eindruck |

**Ein Element besteht, wenn sich mindestens ein Wert unterscheidet und der Unterschied wahrnehmbar
ist.** Wo du meinst, „unterscheidet sich, aber zu schwach", ist das ein eigenes Ergebnis und
wertvoller als ein Ja/Nein.

## 2. Wo gesucht wird

**Alle fünf Ebenen**, nicht nur der Expertenmodus:

1. **Übersicht / Start** (`StartView`)
2. **Geführte Planung** (`GuidedView`)
3. **Expertenmodus** — Werkzeugzeile, Themenzeile, Arbeitsbereich-Wähler, Werkzeug-Schiene
4. **Panels rechts** — Eigenschaften, Engine-Fläche, Fach-Fläche
5. **Dialoge** — `ConfigWizard`, Geschoss-Fläche, Befehlspalette

**Erfasst wird alles, was klickbar aussieht:** `<button>`, `role="button"`, Reiter, Schalter,
Einträge in Menüs und Listen. **Auch die, die heute nie gesperrt sind** — dann steht das im
Ergebnis, und die Frage ist beantwortet statt offen.

## 3. Was ich **nicht** will

- **Keine Reparatur.** Du misst und meldest. Was daraus wird, sind Posten, und die schreibe ich.
  *(Wer misst und gleich baut, nimmt seine eigene Arbeit ab — die Regel, die uns gestern durch
  einen schlechten Tag getragen hat.)*
- **Keine Vollständigkeit auf Kosten der Zahl.** Lieber drei Ebenen mit belastbaren Werten als fünf
  mit Schätzungen. **Was du nicht gemessen hast, benenne als nicht gemessen** — das ist die
  Auskunft, an der ich einen Auftrag ausrichten kann.
- **Keine Rücksicht auf AUF-70.** Der Posten läuft parallel und behandelt **eine** Zeile. Findest du
  dort etwas, das dem Auftrag widerspricht: sag es. Ein Planner-Auftrag auf falscher Annahme zählt
  nicht gegen den Generator — so hast du es bei AUF-45 richtig getrennt.

## 4. Die Form der Meldung

Eine Tabelle im Ledger, eine Zeile je Element oder je Gruppe gleichartiger Elemente:

```
Ebene · Element · heute je gesperrt? · unterscheidbar? · gemessene Werte · Urteil
```

Dazu drei Sätze, die ich ohne dich nicht habe:

1. **Wo ist der Unterschied am schwächsten?** Dort fängt der nächste Posten an, nicht dort, wo am
   meisten Knöpfe stehen.
2. **Gibt es ein Element, das gesperrt aussieht, aber frei ist?** Das wäre die gefährlichere
   Richtung — jemand traut sich nicht, etwas zu tun, das er darf. Danach hat noch niemand gesucht.
3. **Gibt es mehr als eine Beschreibung des gesperrten Aussehens im Quelltext?** Nach AUF-70 soll es
   **eine** geben. Wenn du heute drei findest, weiß ich, wie groß der Rest ist.

## 5. Umfang, ehrlich gesagt

**Das ist eine Stunde Arbeit, keine zehn Minuten.** Ich beauftrage es trotzdem, weil dein Stapel
leer ist und weil dieselbe Frage sonst dreimal einzeln aufschlägt — einmal je Yama-Meldung.
**Wird es länger, brich ab und melde den Teilstand.** Eine halbe Inventur mit Zahlen ist mir
lieber als eine ganze, die schätzt.

**Ballbesitz nach deiner Meldung: Planner.**
