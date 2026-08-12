# Befund — AUF-40 Teil B steht an SECHS Stellen als offen, und BEIDE Hälften sind gebaut

```yaml
rolle: "generator"
art: "BEFUND, read-only — nichts geaendert, nichts gezogen, kein Blatt geschnitten"
gemessen_am: "12.08.2026"
stand: e910d13f
anlass: "Nichts steht BEREIT (§3 bei 0/0, kein NACHBESSERN, keine Tafelzeile mit
         ballbesitz generator). Statt Arbeit zu erfinden: die Messung zu Ende fuehren,
         die ich bei W-33 begonnen habe."
warum_das_anderen_arbeit_spart: "Der Posten liegt bei Yama. Solange er als offen gilt, planen
         Planner und Plan-Pruefer um ihn herum, und JEDE weitere Ablesung, die eine dieser sechs
         Stellen zitiert, schreibt den ueberholten Stand in ein neues Blatt — bei W-33 waere
         genau das per Kriterium verlangt gewesen."
```

## Die Ausgangslage

**Bei W-33 habe ich gemessen, dass die Projektliste ankommt** *(`f469317d`)* — **und dabei DREI
Stellen genannt, die das Gegenteil sagen.** *Es sind SECHS:*

```text
grep -rn "Teil B" resources/planner/hausplaner/ app/ resources/views/admin/hausplaner/

resources/planner/hausplaner/app/StartView.tsx:18
resources/planner/hausplaner/app/StartView.tsx:205
resources/planner/hausplaner/app/studioDaten.ts:155
resources/planner/hausplaner/__tests__/konfiguratorEhrlich.test.ts:11
resources/planner/hausplaner/__tests__/startEhrlich.test.ts:16
resources/planner/hausplaner/__tests__/startEhrlich.test.ts:118
```

> **Zwei hatte ich nicht gesehen** — *`studioDaten.ts:155` und `konfiguratorEhrlich:11`.* **Mein
> Suchbereich bei W-33 war die eine Datei, die ich gerade las.** *Das ist dieselbe Unvollständigkeit,
> die mir bei W-40/1 unterlaufen ist, wo meine Fehlerliste vier Blätter nannte und fünf waren.*

## Und die sechs meinen NICHT dasselbe

**Jede Stelle einzeln geöffnet — es sind ZWEI verschiedene Gegenstände unter einem Namen:**

| Stelle | meint | Wortlaut |
|---|---|---|
| `StartView.tsx:18` | **die Projektliste** | *„Gefüllt wird sie in **Teil B** (Route + Controller, bei Yama)."* |
| `StartView.tsx:205` | **die Projektliste** | *„**Die echte Liste braucht eine Route und ist Teil B** (bei Yama)."* |
| `studioDaten.ts:155` | **die Projektliste** | *„Die echte Liste kommt aus dem Bestand und braucht eine Route — das ist **Teil B** und liegt bei Yama."* |
| `startEhrlich:16` | **die Projektliste** | *„Sie braucht eine Route und ist **Teil B** — der liegt bei Yama."* |
| `startEhrlich:118` | **die Projektliste** | Testname: *„Teil A hat weder Route noch Controller berührt — das ist Teil B"* |
| **`konfiguratorEhrlich:11`** | **die PAKET-SPEICHERUNG** | *„Die echte **Speicherung** bleibt als AUF-40 Teil B stehen — nicht gestrichen, nur nicht dran."* |

> **Fünf meinen die Liste, eine meint die Speicherung.** *Der Release-Prüfer hat in `5e9c8b08`
> gemessen, dass AUF-40 „ZWEI Gegenstände in einem Posten" ist — dieser Zettel zeigt, dass die
> Vermischung bis in die Kommentare reicht.* **Wer „Teil B" liest, weiß nicht, welche Hälfte
> gemeint ist.**

## Hälfte 1 — die PROJEKTLISTE ist gebaut

**Die Naht in vier Stufen, jede Stelle geöffnet:**

```text
app/Http/Controllers/Hausplaner/HausplanerController.php
  :101   hausplanerProjekte()
           LeadAlternativeAdd::query()
             ->select(['id','object_name','city','updated_at'])
             ->orderByDesc('updated_at')->limit(self::PROJEKTLISTE_MAX)
             ->get()->map(fn($o) => [ id · name · ort · datum · adresse ])
  :55    'hpProjekte' => $this->hausplanerProjekte()

resources/views/admin/hausplaner/objekt.blade.php
  :141   data-projekte="{{ json_encode($hpProjekte, JSON_UNESCAPED_UNICODE) }}"

resources/planner/hausplaner/main.tsx
  :82    setProjekte(leseProjekte(mount.dataset[PROJEKTE_ATTRIBUT]))
```

**Zwei Dateien sagen es selbst:**

- `app/state/projekte.ts`: *„AUF-78 … **Jetzt kommt die echte Liste** — das Blade setzt, `main.tsx`
  liest, der UI-Zustand hält."*
- `__tests__/projektKlick.test.ts`: *„AUF-40 Teil A hat die erfundenen Projekte entfernt, **AUF-78
  die echten geliefert**."*

## Hälfte 2 — die PAKET-SPEICHERUNG ist ebenfalls gebaut

**Dieselbe Bauform, dieselbe Naht — und hier führt sie zu einem `fetch`:**

```text
routes/web.php
  :5016  POST /konfigurator-pakete  -> paketSpeichern
  :5017    ->middleware('permission:Hausplaner,add')->name('pakete.speichern')
  :5018  GET  /konfigurator-pakete       -> paketListe
  :5020  GET  /konfigurator-pakete/{paket} -> paketZeigen

resources/views/admin/hausplaner/objekt.blade.php
  :144   data-pakete-url="{{ route('hausplaner.objekt.pakete.speichern') }}"

resources/planner/hausplaner/main.tsx
  :89    setzePaketZiel(mount.dataset[PAKETE_URL_ATTRIBUT] ?? null, csrf)

resources/planner/hausplaner/app/state/paketSpeichern.ts
  :32    kannPaketSpeichern()      -> haengt am gesetzten Ziel
  :40    speicherePaket(art, titel, paket)
  :45      const antwort = await fetch(zielUrl, { … })
```

**Der Konfigurator benutzt es** — *`ConfigWizard.tsx:255`:* `void speicherePaket(art, wahl.label,
paket).then(…)`, **und die Fläche meldet Download und Speicherung getrennt** (`:258-263`).

> **Damit ist auch `konfiguratorEhrlich:11` überholt** — *„nur nicht dran" stimmt nicht mehr, AUF-81
> hat es gebaut.* **Der TEST selbst ist trotzdem grün und richtig:** *er prüft, dass die Fläche
> sagt, was wirklich geschieht, und `:40` verlangt inzwischen ausdrücklich „gespeichert in deiner
> Paketliste" als **wahren** Satz.* **Der Kommentar im Kopf ist alt, die Zusagen darunter sind
> nachgezogen.**

## Was das für die Kette bedeutet

```text
GEBAUT     beide Haelften. Die Projektliste ueber ein Mount-Attribut (kein Fetch),
           die Paketspeicherung ueber eine benannte Route mit Rechte-Middleware.

OFFEN      nur noch das, was der release-pruefer in 5e9c8b08 benannt hat: eine
           nutzerweite OBJEKT-Liste gibt es nicht. Fuer den Startbildschirm wird
           sie nicht gebraucht — er bekommt seine Liste bereits.

NICHT      ob beides im BROWSER ankommt. Ich habe alle Naehte GELESEN, keine
GEMESSEN   ausgefuehrt. Das ist eine Sichtprobe und keine Textmessung.
```

## Warum es so lange unentdeckt blieb

**Beide Male wurde nach einer ROUTE gesucht, und beide Male ist die Antwort verschieden:**

```text
Projektliste   es GIBT keine Route — die Liste kommt ueber data-projekte.
               Wer „Route" misst, findet 0 und schliesst auf „nicht gebaut".
Paketspeicherung  es GIBT eine — sie heisst hausplaner.objekt.pakete.speichern,
               steht in routes/web.php:5016 und wird ueber data-pakete-url
               an die Insel gereicht, nicht in der Insel zusammengebaut.
```

> **Das ist H-9 zweimal am selben Posten:** *ein Muster, das eine Bauform voraussetzt, misst die
> Bauform und nicht die Sache.* **Und der Controller sagt seinen Grundsatz wörtlich (`:57`):
> „dieselbe Naht wie `hpProjekte`, **kein Lade-Fetch aus der Insel**."** *Wer das kennt, sucht gar
> nicht erst nach `fetch` in der Insel — außer beim Speichern, wo genau einer steht.*

## Was ich getan habe und was nicht

```text
GETAN      gemessen und aufgeschrieben: sechs Stellen, zwei Gegenstaende, zwei
           vollstaendige Naehte, jede Zeile geoeffnet. Damit die naechste Rolle
           den Posten nicht ein drittes Mal aufrollt und keine weitere Ablesung
           den ueberholten Stand zitiert.

NICHT      keine der sechs Stellen angefasst. Sie stehen in fremden Werkzeugen
           (W-38s studioDaten, W-35s Waechter) und in W-33, das gerade beim
           Evaluator liegt.
           Kein Blatt geschnitten — das ist Planner-Arbeit.
           Und NICHTS entschieden: ob AUF-40 Teil B damit geschlossen ist, sagt
           Yama. Ich lege die Messung vor, nicht das Urteil.
```

> **Mein Beitrag ist eine Liste mit sechs Zeilen und zwei Nähten, kein Vorschlag.** *Die Sätze in
> den sechs Kommentaren waren zum Zeitpunkt ihres Schreibens richtig — dass sie es heute nicht mehr
> sind, macht sie nicht falsch, sondern überholt.*
