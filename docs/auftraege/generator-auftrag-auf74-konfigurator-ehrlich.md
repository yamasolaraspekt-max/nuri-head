# ⇒ GENERATOR-AUFTRAG AUF-74 — Der Konfigurator sagt, was wirklich passiert

**Vom:** Planner · **26.07.2026, 09:10** · **Spur A** — es geht um eine Zusage an den Nutzer über
den Verbleib seiner Arbeit. **Heimat-App:** `ticket`.
**Grundlage:** **Entscheidung Yama, 26.07.** zu Befund **B7** der Layout-Inventur:
*„Noch nicht bauen — erst den Satz ehrlich machen."*

**Vorher gelesen:** HEAD `2ba787e` · `git log -5` · `docs/planner/ux-befund-layout-alle-ebenen-2026-07-25.md`
§B7 · `app/ConfigWizard.tsx:5, 143, 159, 226, 236, 239` · `generator-auftrag-auf40-start-und-persistenz.md` §4.

---

## 1. Der Befund — er ist unverändert und selbst nachgemessen

```
$ grep -rl "ConfiguratorPackage" app/ database/migrations/ routes/
(leer)
```

**Serverseitig existiert dafür nichts.** Was der Konfigurator „speichern" nennt, ist
`a.download = konfigurator-<art>-<id>.json` (`ConfigWizard.tsx:236`) — **eine Datei im
Download-Ordner.**

**Was der Nutzer währenddessen liest:**

| Stelle | Text |
|---|---|
| `:159` | „Status: **Entwurf** · als ConfiguratorPackage **speicherbar**" |
| `:143` | „Als Fachobjekt speichern — autark als ConfiguratorPackage (Vorlage/Angebot), **später verlustfrei ins Projekt**." |
| `:239` | „… als ConfiguratorPackage **gespeichert** (Download)." |

**Der Konfigurator ist die stärkste Fläche im Programm** — 24 Bauarten, Live-Vorschau,
fünf saubere Schritte, autark bedienbar. **Und er macht das leerste Versprechen.** Zehn Minuten
Arbeit, danach eine JSON-Datei, die man nirgends wieder öffnen kann. *„Später verlustfrei ins
Projekt"* beschreibt etwas, das es nicht gibt.

## 2. Die Entscheidung — Yamas, nicht meine

**Es wird nichts gespeichert und nichts migriert.** Kein `routes/`, kein `app/Http/`, kein
`database/`. **Der Satz wird wahr gemacht, nicht die Funktion nachgebaut.**

**Warum das die richtige Reihenfolge ist:** Eine Unehrlichkeit kostet jeden Tag, an dem sie steht.
Die Persistenz kostet eine Migration an einer Datenbank mit echten Kunden. **Das eine ist heute
behebbar, das andere gehört geplant.** Die echte Speicherung bleibt als **AUF-40 Teil B** stehen,
unverändert und ungezogen — *sie ist nicht gestrichen, sie ist nicht dran.*

## 3. Was gebaut wird

**Die drei Stellen sagen, was tatsächlich geschieht — in Yamas Sprache, nicht in Fachbegriffen.**

Maßgeblich ist die Wirkung, nicht das Wort: **der Nutzer muss nach dem Lesen wissen, wo seine
Arbeit landet und was er damit tun kann.** Sinngemäß: *eine Datei zum Herunterladen; sie lässt sich
noch nicht im Programm wieder öffnen.*

**Zwei Dinge sind dabei bindend:**

1. **Kein „noch nicht" ohne Aussage darüber, was stattdessen geht.** Ein Hinweis, der nur eine
   Lücke benennt, macht die Fläche schwächer, statt sie ehrlich zu machen. Der Download **ist** ein
   Ergebnis — er soll als solches dastehen, nicht als Ersatz für etwas Besseres.
2. **Kein Versprechen auf später.** Weder „folgt", noch „in Kürze", noch „geplant". **Genau diese
   Sorte Satz hat AUF-44 aus der Icon-Zeile entfernt**, und sie kommt hier nicht durch die Hintertür
   zurück. Was der Nutzer heute hat, steht da; was morgen kommt, entscheidet Yama.

## 4. Was **nicht** gebaut wird

- **Keine Persistenz, keine Route, keine Migration.** `routes/`, `app/Http/`, `database/` tragen
  **null Zeilen**. *(Das ist hier kein Formalkriterium, sondern der Inhalt der Entscheidung.)*
- **Kein Entfernen des Downloads.** Er funktioniert und ist das einzige Ergebnis, das der Nutzer
  heute mitnehmen kann.
- **Kein Umbau des Konfigurators.** Schritte, Bauarten, Vorschau, Übernahme ins Modell — alles
  unberührt. **Es geht um drei Textstellen und ihre Wahrheit.**
- **Kein Anfassen von `store/`, `domain/`, `geometry/`, `renderers/`, `scene.types`** — K4.
  *(Auch `geometry/configuratorPackage.ts` nicht: die Struktur bleibt, wie sie ist.)*

## 5. Abnahmekriterien

1. `tsc:hausplaner` · `schema:hausplaner:check` · `test:hausplaner` — Exit 0, Zahlen vorher/nachher.
2. **K4 unberührt** — null Zeilen. **Ebenso `routes/`, `app/Http/`, `database/` — null Zeilen.**
3. **Kein Wort behauptet mehr eine Speicherung im Programm:** ein Test belegt, dass in
   `ConfigWizard.tsx` keine der drei Stellen mehr „speicherbar", „gespeichert" oder „verlustfrei ins
   Projekt" im Sinne einer Server-Zusage trägt. **Die Prüfung ist eng zu formulieren** — ein zu
   breiter `grep` findet legitime Vorkommen und meldet Falsches. *(Genau das ist dem Evaluator
   gestern bei AUF-65 passiert; er hat es offengelegt. Diesmal von vornherein eng.)*
4. **Der Download bleibt und wird benannt:** Test belegt, dass die Fläche das tatsächliche Ergebnis
   nennt (eine Datei) und dass `a.download` unverändert funktioniert.
5. **Kein Versprechen auf später:** `grep` = **0** für „folgt", „in Kürze", „geplant", „demnächst"
   in dieser Datei.
6. **Der Übernahme-Weg ins Modell ist unberührt:** der Nicht-autarke Fall („als ein Command ins
   Gebäudemodell, Undo/Redo inklusive") **stimmt** und bleibt Zeichen für Zeichen stehen.
   Testverriegelt — **das ist die Aussage, die wahr ist, und sie darf beim Aufräumen nicht
   mitgehen.**
7. **Mutations-Gegenbeweis:** das alte Wort zurücksetzen ⇒ mindestens ein Test rot. Zahl nennen.
8. **`public/*` im Code-Commit: null Zeilen**, Bundle-Rebuild als eigener zweiter Commit (§8 2b).
9. **Klassifikation: `sichtbar`.** Sichtprobe nach **§11 mit Zustand**: Konfigurator öffnen, bis zur
   Fußzeile, den Satz lesen — und den Download einmal wirklich auslösen.

## 6. Was zurückgegeben wird

- **Findest du eine vierte Stelle, die dasselbe behauptet:** aufnehmen und im Bericht nennen. Drei
  sind gemessen, nicht abgezählt.
- **Liest sich die ehrliche Fassung so, dass die Fläche schwach wirkt:** sag es mit dem Wortlaut.
  **Dann ist die Formulierung eine Willensfrage für Yama** — er hat den Weg entschieden, nicht den
  Satz.
