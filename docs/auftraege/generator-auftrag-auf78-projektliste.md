# ⇒ GENERATOR-AUFTRAG AUF-78 — Die Projektliste erreicht den Startbildschirm

**Vom:** Planner · **26.07.2026, 10:20** · **Spur A** · **Heimat-App:** `ticket`
**Tor 1: von Yama an den Planner delegiert** — *„das kannst du selber entscheiden und die
Verantwortung übernehmen; es darf dadurch kein Fehler passieren."*
**Grundlage:** Rückgabe des Generators aus AUF-40 Teil A.

**Vorher gelesen und gemessen:** HEAD `0fa118b` ·
`app/Http/Controllers/Hausplaner/HausplanerController.php:37-46` (`seite`), `:122-134` (`index`) ·
`routes/web.php:4988-4997` · `app/Models/LeadAlternativeAdd.php` ·
`resources/views/admin/hausplaner/{objekt,studio,index}.blade.php` · `app/StartView.tsx`.

---

## 1. Warum ich das entscheiden kann — die Naht existiert bereits

**Der entscheidende Fund: die Liste ist nicht neu. Sie läuft seit Langem in Produktion.**

```php
HausplanerController::index()          // Zeile 122-134
    LeadAlternativeAdd::query()
      ->with('lead:id,firma,name,lastname,customer_no')   // eager load — kein N+1
      ->gebaeudeSuche($q)
      ->orderByDesc('id')
      ->paginate(25)
```

**Route dazu:** `permission:Hausplaner,read` (`web.php:4989`).

**Es wird also keine Abfrage erfunden, kein Endpunkt angelegt, kein Zugriffsweg geöffnet.** Es wird
**derselbe** Weg ein zweites Mal benutzt, hinter **demselben** Recht.

## 2. Die eine Stelle, an der ein Fehler entstehen könnte — und wie er ausgeschlossen wird

**Gemessen, und das ist der Kern dieses Auftrags:**

| Route | Middleware |
|---|---|
| `/admin/hausplaner` (index) | `auth` + **`permission:Hausplaner,read`** |
| `/admin/hausplaner/objekt/{objekt}` | `auth` + **`permission:Hausplaner,read`** |
| `/admin/hausplaner/studio` | **nur `auth`** — kein Hausplaner-Recht |

**Die Studio-Route trägt das Recht nicht.** Wer die Objektliste dorthin durchreicht, zeigt sie
**jedem angemeldeten Nutzer** — auch dem, der den Hausplaner nicht sehen darf.

**Das wäre genau der Fehler, der nicht passieren darf. Deshalb, verbindlich:**

> **Die Liste wird ausschließlich in `HausplanerController::seite()` übergeben.**
> **`studio.blade.php` und die Studio-Route werden nicht angefasst.** Dort bleibt der Grundwert
> **leer** — und der ehrliche Leerzustand aus AUF-40 Teil A bleibt dort richtig, weil die Testfläche
> ohnehin nichts speichert.

**Damit ist die Absicherung nicht neu erfunden, sondern geerbt:** dieselbe Middleware, die heute
schon die Liste unter `/admin/hausplaner` schützt.

## 3. Was gebaut wird

1. **`seite()` reicht eine fünfte Variable durch** — nach dem Muster der vier vorhandenen
   (`objekt`, `dokument`, `uebernahme`, `hpRechte`).
2. **Dieselbe Abfrageform wie `index()`**: eager geladen, absteigend sortiert, **hart begrenzt**.
   **Keine Paginierung** — der Startbildschirm zeigt die letzten wenigen, nicht alle.
3. **So wenig Felder wie möglich.** `StartView` braucht Bezeichnung, Ort und Datum. **Alles, was
   darüber hinausgeht, wird nicht übergeben** — insbesondere keine Kundendaten, die die Fläche nicht
   anzeigt. *Was nicht durchgereicht wird, kann nicht versehentlich sichtbar werden.*
4. **Das Blade reicht weiter, es rechnet nicht.** Eine Zeile im `data-`-Attribut, wie
   `data-rechte` — **kein `@php`-Block.** *(AUF-64: genau ein solcher Block hat die Route
   zerbrochen.)*
5. **`StartView` bekommt, was es seit AUF-40 Teil A erwartet** — die Eigenschaft existiert bereits
   mit Grundwert leer.

## 4. Was **nicht** gebaut wird

- **Keine neue Route, kein neuer Endpunkt, keine API.**
- **Keine Migration, kein Schema, keine Änderung an `LeadAlternativeAdd`.**
- **Keine Änderung an `User::hasPermission` oder an einer Middleware.** Die Rechte-Wahrheit bleibt
  unberührt — sie wird benutzt, nicht angefasst.
- **Kein Anfassen der Studio-Route und von `studio.blade.php`.**
- **Keine Suche, kein Filter, keine Sortierwahl** auf dem Startbildschirm. Das ist die Index-Seite,
  und die gibt es.
- **Kein Anfassen von `store/`, `domain/`, `geometry/`, `renderers/`, `scene.types`.**

## 5. Abnahmekriterien — die ersten vier sind die, für die ich hafte

1. **Die Liste erreicht die Studio-Fläche nicht.** `grep` belegt: `studio.blade.php` enthält den
   neuen Feldnamen **nicht**; die Studio-Route in `routes/web.php` ist **unverändert** (Zeile für
   Zeile). **Test: die Studio-Seite gerendert ⇒ leere Liste.**
2. **Ohne das Recht kein Zugriff.** Test: ein Nutzer **ohne** `Hausplaner,read` ruft
   `objekt/{id}` ⇒ **abgewiesen** (kein 200, kein Teilinhalt). *Das gilt heute schon; der Test hält
   fest, dass es so bleibt.*
3. **Nur die nötigen Felder.** Test belegt, dass das übergebene Bündel **genau** die Felder trägt,
   die `StartView` anzeigt — und **keine Kundendaten** darüber hinaus.
4. **Genau eine Abfrage, hart begrenzt.** Test zählt die abgesetzten Abfragen (**1**, kein N+1) und
   belegt die Obergrenze der Zeilen. **Auch bei 3 000 Objekten in der Datenbank.**
5. **Kein `@php`-Block im Blade:** `grep -c "@endphp"` in `objekt.blade.php` = **0** (Stand heute,
   und es bleibt so).
6. **`routes/`, `database/migrations/`, `app/Models/` — null Zeilen.** `app/Http/` trägt **genau
   eine** Methode/Änderung, benannt im Bericht.
7. **K4 unberührt** — `store/`, `domain/`, `geometry/`, `renderers/`, `scene.types` null Zeilen.
8. **Der ehrliche Leerzustand bleibt.** Test: Konto ohne Objekte ⇒ **derselbe** Text wie nach
   AUF-40 Teil A, Zeichen für Zeichen. *Er darf nicht durch eine leere Tabelle ersetzt werden.*
9. **§9 Blade-Regel:** die PHP-Suite läuft mit, Zahlen vorher/nachher. Die Objekt-Route wird
   aufgerufen und der Statuscode genannt.
10. **`public/*` im Code-Commit: null Zeilen**, Bundle-Rebuild als eigener zweiter Commit (§8 2b).
11. **Mutations-Gegenbeweis:** die Liste zusätzlich an die Studio-Fläche hängen ⇒ **Kriterium 1
    rot**. Zahl nennen. *Das ist der Test, der die Sicherheitsentscheidung dieses Postens verriegelt.*
12. **Klassifikation: `sichtbar`.** Sichtprobe nach **§11 mit Zustand**, in **zwei** Konten:
    eines **mit** Objekten und eines **ohne**.

## 6. Meine Verantwortung, ausgeschrieben

**Yama hat mir diese Freigabe übertragen. Das ist die Begründung, an der ich gemessen werden will:**

**Ich gebe frei, weil hier nichts Neues entsteht.** Abfrage, Berechtigung und Route existieren und
laufen; es wird eine vorhandene Liste ein zweites Mal an einer Stelle gezeigt, die **dasselbe Recht**
verlangt. **Der einzige Weg, wie daraus ein Fehler würde, ist die Studio-Route** — und genau dagegen
steht Kriterium 1 mit einem Mutations-Gegenbeweis.

**Was ich nicht freigegeben hätte:** eine neue Route, eine eigene Abfrage im Blade, eine Liste ohne
Obergrenze, oder die Übergabe an eine Fläche mit schwächerem Recht. **Jedes einzelne davon steht
oben unter „wird nicht gebaut" — nicht als Formalie, sondern weil es die Punkte sind, an denen ich
nein gesagt hätte.**

## 7. Was zurückgegeben wird

- **Verlangt die Anzeige mehr Felder, als `StartView` heute zeigt:** melden. **Nicht vorsorglich
  mehr durchreichen** — Daten, die man „vielleicht später braucht", sind der übliche Anfang einer
  Leckage.
- **Zeigt sich, dass `gebaeudeSuche` ohne Suchbegriff etwas anderes tut als erwartet:** benennen und
  die einfache Abfrage nehmen. **Nicht den Scope ändern** — er wird von der Index-Seite mitbenutzt.
