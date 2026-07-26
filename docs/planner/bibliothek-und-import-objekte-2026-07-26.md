# Bibliotheks- und Importobjekte fuer Kueche, Bad, Moebel — Einordnung

**26.07.2026, Planner.** Yama hat die Bibliotheksarchitektur uebergeben: DWG/IFC/GLB-Import, drei
Objektklassen (starr, parametrisch, hybrid), Materialslots, Anschlussports, Bewegungsflaechen,
Herstellerdaten.

**Der Entwurf ist richtig. Der wichtigste Punkt dazu ist aber keine Architekturfrage, und ich
stelle ihn deshalb an den Anfang.**

---

## Der eine Satz, auf den es ankommt: der Produktstamm existiert bereits

Gemessen im CRM:

```
app/Models/*.php gesamt                                    410
davon Produkt-/Artikel-/Preis-Modelle                       34
```

Darunter: `Product` · `ProductType` · `ProductDescription` · `ProductImage` · `ProductDocuments` ·
`ProductFormula` · `ProductHistory` · `ProductMasterSet` · `ProductSubSet` · `ProductPosition` ·
`ArticleGroup` · `SubArticleGroup` · `SupplierArticleMap` · `DistributorPrice` · `Material` —
und fachlich einschlaegig: **`ProductPV`**, **`ProductWP`** (Waermepumpe),
`ProductInstallationCase`.

**Und eines heisst `PlannerItemMaterial`.** Die Bruecke zwischen Planer und Material ist bereits
gedacht.

Yamas `ConfigurableLibraryObject` traegt `manufacturer { id, series, articleNumber }` und
`commercial { price, currency, leadTimeDays }`. **Werden diese Felder mit eigenen Werten gefuellt,
entsteht ein zweiter Produktstamm neben vierunddreissig Modellen — in einem System mit ~3000
echten Kunden.**

Das ist nicht dieselbe Groessenordnung wie unsere bisherigen zweiten Wahrheiten. Ein doppelter
Werkzeugkatalog kostet Verwirrung. **Ein doppelter Produktstamm kostet falsche Preise in Angeboten.**

**Regel, die ich vorschlage:**

> Das Bibliotheksobjekt **verweist** auf `Product`. Es **kopiert** ihn nicht.
> `articleNumber` ist ein Schluessel, kein Wert. Preis, Lieferzeit und Varianten werden zur
> Laufzeit aus dem CRM gelesen — nie im Szenendokument gespeichert.

Damit ist auch die Frage beantwortet, die sonst spaeter kommt: *was passiert, wenn der Hersteller
den Preis aendert?* Wird verwiesen: nichts, es stimmt weiter. Wird kopiert: jedes gespeicherte
Projekt traegt einen alten Preis, und niemand weiss, welche.

*Ausnahme, die ich mitdenke:* ein **Angebot** muss den Preis zum Zeitpunkt der Angebotstellung
einfrieren. Das ist aber die Aufgabe des Angebots im CRM (`OfferProductList` gibt es), nicht die
des Bauplans.

---

## Die drei Objektklassen — im Schema angedeutet, Mechanismus fehlt

Yamas `geometryMode: rigid | axis-scalable | parametric | hybrid | modular` hat bei uns bereits
einen Anker:

```
domain/scene.types.ts:194
  scale: { x: number; y: number; z: number };   // Faktor (placement.allowScaling gate)
```

**Der Gedanke „nicht jedes Objekt darf skaliert werden" steht im Schema — als Kommentar.**
Ein `placement.allowScaling` gibt es nicht. Wie bei fast allem heute: die Absicht ist notiert, der
Mechanismus fehlt. Immerhin heisst das: **`geometryMode` ist keine Fremdidee, sondern die
Ausformulierung einer Stelle, die schon markiert war.**

**Seine Warnung vor globaler Mesh-Skalierung teile ich uneingeschraenkt** und sie hat bei uns eine
scharfe Entsprechung: wir rechnen in **ganzen Millimetern ohne Toleranz**. Ein Unterschrank, der
von 600 auf 800 skaliert wird, bekommt eine Plattenstaerke von 25,33 mm. In einer mm-Ganzzahlwelt
ist das nicht nur haesslich, sondern **nicht darstellbar** — und in einer Mengenermittlung, aus
der bestellt wird, ist es falsch.

## Import ist heute doppelt verschlossen

| Ebene | Stand |
|---|---|
| Rechte | 8 Import-Werkzeuge, alle an `permission.import` — **das Recht existiert im CRM nicht** (`Hausplaner,read` und `Hausplaner,update`) |
| Formate | **kein einziger Treffer** fuer `gltf`, `GLTFLoader`, `.glb`, `dxf`, `ifc` in der ganzen Insel — ausser den Werkzeugnamen im Paket |

**Der Import ist also nicht „teilweise da". Er ist an zwei unabhaengigen Stellen zu.** Das ist
keine schlechte Nachricht: es heisst, dass nichts Halbfertiges im Weg steht.

---

## Wo das im Fahrplan sitzt — und was der kleinste sinnvolle Anfang ist

**Das ist nicht AUF-50, und es ist auch nicht Phase 2.** Es ist ein eigenes Vorhaben, das auf
50.1 bis 50.3 aufsetzt: ohne Eignungen, ohne Objekt-Registry und ohne generisches
Eigenschaftenpanel gibt es nichts, woran eine Bibliothek andocken koennte.

**Aber der Keim liegt naeher, als es aussieht.** Die 16 Katalog-Objekte aus meiner Zaehlung —
`heizkoerper`, `wc`, `badewanne`, `dusche`, `schrank`, `geraet`, `wallbox`, `pv-modul` … — **sind
bereits die ersten Bibliotheksobjekte.** Nur eben eigene statt importierte.

**Deshalb mein Vorschlag zur Reihenfolge, und er ist eine Empfehlung, keine Entscheidung:**

> **Nicht mit dem Import anfangen.** Zuerst die 16 eigenen Objekte durch die volle Kette schicken —
> Registry, Parameterschema, Materialslots, Maßgrenzen, bei `wc`/`dusche`/`geraet` auch
> Anschlussports und Bewegungsflaechen. **Ein Regal, das traegt, bevor Fremddaten hineinkommen.**

Der Grund ist nicht Bequemlichkeit. Ein Import bringt drei Sorten Fehler gleichzeitig: die des
Formats, die der Zuordnung und die der Bibliothek selbst. **Laeuft die Bibliothek vorher mit
eigenen Daten, ist beim ersten DWG nur noch eine Fehlerquelle offen.**

Und die 16 decken die Klassen bereits ab: `wc` und `badewanne` sind **starr**, `schrank` ist
**parametrisch**, `geraet` in einem Schrank ist **hybrid**. Man muss nichts erfinden, um die
Dreiteilung zu erproben.

---

## Drei Punkte, die keine Architekturfragen sind — und trotzdem entschieden werden muessen

**1. Ein falsches Mass in einer Kuechenplanung wird bestellt.** Sobald Herstellerobjekte im Plan
stehen und der Plan zu einem Angebot fuehrt, ist ein Modellierungsfehler kein Darstellungsfehler
mehr. Das ist derselbe Sprung, den ich heute bei der Raumerkennung gemessen habe — nur teurer.
**Was daraus folgt: die Maßgrenzen (`allowedValues`, `min`/`max`) sind keine Bequemlichkeit,
sondern die Absicherung.** Yamas `editMode: discrete` mit `allowedValues` ist genau richtig; er ist
der Unterschied zwischen „Schrank 620 mm" und „Schrank, den es gibt".

**2. Herstellerdaten gehoeren jemandem.** DWG- und BIM-Kataloge kommen mit Nutzungsbedingungen.
Wer sie in eine eigene Bibliothek uebernimmt, uebernimmt auch die Frage, ob er das darf. **Das ist
keine Planner-Entscheidung** und keine, die man beim ersten Import nebenbei trifft.

**3. Aktualisierung.** Ein Hersteller aendert eine Serie. Was passiert mit Projekten, die das alte
Modell enthalten? **Solange verwiesen und nicht kopiert wird**, ist die Antwort eine Frage der
Versionierung im CRM — und `ProductHistory` gibt es dort bereits. Wird kopiert, gibt es keine
Antwort.

---

## Was ich uebernehme

- **Die Dreiteilung starr / parametrisch / hybrid** und dass hybrid fuer Herstellerprodukte
  meistens die richtige ist.
- **Materialslots mit `editable`** — dass der Nutzer die Front aendern kann, ohne Scharniere
  einzufaerben, ist genau die Sorte Regel, die spaeter niemand mehr nachtraegt.
- **Volumen als berechneter Wert, nicht als Eingabe.** Das entspricht unserem Vorgehen: die
  Raumflaeche wird gerechnet, nicht gespeichert. Und seine Unterscheidung Aussen-, Material-,
  Nutzvolumen ist fuer eine Mengenermittlung nicht Feinheit, sondern Voraussetzung.
- **Anschlussports, die sich mitbewegen** — das ist der Punkt, an dem der Bauplaner und die
  TGA-Planung dieselbe Wahrheit benutzen statt zweier Zeichnungen.
- **Der Import-Freigabeprozess in 17 Schritten.** Er beschreibt, warum ein Import kein Ladevorgang
  ist, sondern eine Aufnahme.

## Was bei Yama liegt

Zu den bisherigen Punkten kommen drei:

8. **Verweist das Bibliotheksobjekt auf `Product`, oder fuehrt der Planer eigene Artikeldaten?**
   *Meine Empfehlung ist deutlich: verweisen.*
9. **Bekommt `permission.import` ein eigenes Recht, oder haengt Import an `Hausplaner,update`?**
   Diese Frage steht seit AUF-36 offen und blockiert acht Werkzeuge.
10. **Beginnen wir mit den 16 eigenen Objekten statt mit dem Import?**
