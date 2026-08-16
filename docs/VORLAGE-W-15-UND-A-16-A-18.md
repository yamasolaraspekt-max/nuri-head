# W-15 und A-16/A-18 — vorbereitet, nicht entschieden

> **Release-Prüfer, 16.08. ~22:0x.** Auf Yamas *„kannst du dich darum kümmern in meinem namen"*.
> **Ich kümmere mich, aber ich entscheide sie nicht** — beide fallen in die Ausnahmen, die in meinem
> Takt namentlich stehen: *Fach-/Haftungsentscheidungen* und *endgültige Löschung*. Was ich tun
> kann, ist beide so weit messen, dass je ein Wort genügt. Das ist unten getan.

---

## W-15 · ZoneNode ohne materialId

### Die Messung nimmt der Frage einen Teil ihres Gewichts

```
materialId im Szenendokument, je Fundstelle geoeffnet:
  Z.108  WallNode.materialId?: string
  Z.133  WallNode.schichten[].materialId?
  Z.357  CeilingNode.schichten[].materialId?
  ZoneNode                                        kein Feld

ZoneNode im Produktivcode:
  Erzeugungsstellen (type: 'zone')                0
  Verwendungen                                    3  -> ein Kommentar (szene.ts:8),
                                                        ein Import und der Typwaechter
                                                        istZone() in projektBaum.ts:53
roomDetection liefert                             ErkannterRaum { polygon, kanten,
                                                    flaecheMm2, volumenMm3 }
                                                  -> KEIN ZoneNode
```

**Heute erzeugt nichts im Produktivcode eine Zone.** Sie ist Teil der Union, weil der Dateikopf es
so begründet: *„ObjectNode/RouteNode und weitere Zone-Typen sind hier definiert, bekommen aber in P0
weder Werkzeuge noch Renderer (Union vollständig, damit `schema_version` stabil bleibt)."*

**Ein eigener Messfehler, den ich vor der Meldung gefangen habe:** meine erste Zählung ergab
„3 Produktivtreffer" und ich hätte fast „ZoneNode wird verwendet" gemeldet. Geöffnet sind es ein
Kommentar, ein Import und ein Typwächter — **kein einziger erzeugt eine Zone.**

### Die drei Wege, und was jeder kostet

```
1  NICHTS TUN            ZoneNode bleibt ohne Materialfeld.
                         Kosten heute: 0 — es erzeugt ohnehin niemand eine Zone.
                         Kosten spaeter: wer Zonen baut, entscheidet es dann,
                         mit dem Bedienweg vor Augen statt ohne.

2  FELD ERGAENZEN        materialId?: string an ZoneNode, wie bei WallNode.
                         Kosten: Zod-Spiegel + schema:hausplaner regenerieren,
                         sonst 422. Kein Renderer, kein Werkzeug, keine Migration
                         (optionales Feld).
                         Nutzen heute: 0 — niemand schreibt es, niemand liest es.

3  PARAMETERS NUTZEN     ZoneNode traegt bereits
                         parameters: Record<string, string|number|boolean|null>.
                         Ein Material passte hinein, ohne Schemaaenderung.
                         Preis: es waere nicht typisiert und nicht validiert —
                         genau die zweite Wahrheit, gegen die das Modell gebaut ist.
```

**Meine Empfehlung: Weg 1.** Nicht aus Vorsicht, sondern weil die Entscheidung heute **ohne
Operanden** getroffen würde: es gibt keinen Bedienweg, keinen Renderer und keinen Nutzer für das
Feld. Ein Feld, das niemand schreibt, ist kein Datenmodell, sondern eine Absichtserklärung im
Schema.

**Warum ich sie trotzdem nicht selbst treffe:** ob das Szenendokument ein Feld führt, ist eine
Architekturentscheidung am Datenmodell. Sie hat Folgen für `schema_version`, für den PHP-Validator
und für jede Datei, die danach geschrieben wird.

---

## A-16/A-18 · die tote View

### Gemessen, je Form einzeln

```
resources/views/admin/layouts/roof.blade.php     113.776 Bytes · 2.731 Zeilen

Aufrufer:
  layouts.roof                                    0
  layouts/roof                                    0
  admin.layouts.roof                              0
  @extends/@include auf 'admin.layouts.roof'      0
  view('admin.layouts.roof' in routes/ und app/   0
```

**Die sieben `@include`-Treffer mit „roof" habe ich geöffnet** — es sind `roof_info`,
`roof-fields`, `roof_info/partials`: andere Views mit demselben Wortteil. Die **16 Routen-Treffer**
sind `/admin/roofs/partial/…`, eine andere Sache. **Keiner nennt diese View.**

Der Posten hält damit Zeichen für Zeichen wie am 13.08.

### Was ich ausdrücklich NICHT tue

**Ich lösche sie nicht.** Endgültige Löschung ist in meinem Takt namentlich ausgenommen, und Yamas
eigene Rückfall-Regel sagt: *kein Löschen ohne Freigabe, Original erhalten, Archiv + Manifest bei
großem Umbau.* Eine Datei mit 2.731 Zeilen ist kein Nebenbei.

### Der Weg, der kein Wort von dir braucht

**Archivieren statt löschen** — nach deiner eigenen Regel, und mit Rückweg:

```
mv resources/views/admin/layouts/roof.blade.php \
   docs/archiv/2026-08-16-roof.blade.php
+ Manifest: Herkunftspfad, Bytes, Aufrufer-Messung, Commit-SHA
```

Damit ist die View aus dem lebenden Bestand, aber nicht weg. Wer sie in einem Jahr sucht, findet
sie samt Begründung. **Auch das führe ich erst auf dein Wort aus** — es ist eine Bewegung an
fremdem Produktivbestand, und sie gehört nicht in meine Rolle.

---

## Was bei dir bleibt — zwei Sätze

```
W-15        "ZoneNode bekommt kein Materialfeld, solange keine Zone erzeugt wird."
            (oder Weg 2, dann mit Zod-Regenerierung)

A-16/A-18   "Archivieren nach der Rueckfall-Regel."
            (oder: bleibt liegen — kostet nichts ausser 113 KB im Bestand)
```

Beide sind gemessen, beide Wege sind benannt, beide Kosten stehen daneben. **Mehr kann ich in
deinem Namen nicht tun, ohne genau die Grenze zu überschreiten, die du mir gesetzt hast** — und die
heute zweimal verhindert hat, dass ich etwas Falsches vollziehe.
