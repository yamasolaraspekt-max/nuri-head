# W-18 · Topologie prüfen — FORMELN

> **Regel: hier werden nur F-Nummern aus `01-MATHEMATIK/FORMELSAMMLUNG.md` genannt. Keine
> abgeschriebenen Formeln.**

## Benutzte Formeln

| F-Nr | Wofür in diesem Werkzeug | Zustand |
|---|---|---|
| **F-013** Selbstschnitt-Prüfung | die Kontur beim Zeichnen prüfen | **gebaut und angeschlossen** |
| **F-004** Schnittpunkt zweier Geraden | **nicht als Topologie-Formel** — s. u. | gebaut, aber woanders und wofür anderes |

## F-013 — gebaut in `geometry/kontur.ts:109`

```text
schneidetSichSelbst(punkte)   jedes NICHT benachbarte Kantenpaar gegeneinander
                              n < 4 -> false, benachbarte Kanten uebersprungen,
                              letzte Kante ist Nachbar der ersten
```

**Und die Meldung dazu steht als Text bereit** (`:63`, wörtlich):

> *„Die Kontur überschneidet sich selbst — zieh den letzten Punkt so, dass sich keine …"*

## F-004 ist ein GEHRUNGSDETAIL und keine Topologie-Funktion

**Das ist der Satz, ohne den die nächste Rolle eine Funktion sucht, die es so nicht gibt.**

**F-004 ist in `wallGeometry.ts` gebaut — aber für die Wandecke, nicht für die Kontur:**

```text
wallGeometry.ts:62    „Gehrung (mitered): die beiden Bandkanten werden bis zum
                       Schnittpunkt verlaengert"
              :106    „Liefert die beiden Schnittpunkte der Bandkanten (Halbdicke h)
                       oder null"
```

> ***Der Schnittpunkt entsteht dort zwischen den RÄNDERN zweier Wandbänder***, damit die Ecke sauber
> auf Gehrung steht. **Mit der Frage „schneidet sich diese Kontur selbst" hat er nichts zu tun** —
> `kontur.ts` rechnet seinen Schnitt selbst und importiert aus `wallGeometry` nichts.

### Die Gegenprobe des Auftrags — und was sich seit dem Schnitt geändert hat

**Der Auftrag verlangt als Beleg: „kein `achsenSchnitt`, `geradenSchnitt` oder
`schnittZweierGeraden` im Repo". Am Bau-Stand gemessen:**

```text
achsenSchnitt          0 Treffer
schnittZweierGeraden   0 Treffer
geradenSchnitt         2 Treffer   <- NEU seit A-32
                                     geometry/geradenGeometrie.ts:84 + sein Test
```

> ***Die Gegenprobe ist überholt, und zwar durch A-32*** — *dort ist F-004 seit dem 13.08. in
> **reiner** Form gebaut (`geradenSchnitt`, mit normalisiertem Grenzfall).*

**Die Aussage von W-18 wird davon aber nicht schwächer, sondern schärfer — gemessen:**

```text
geradenGeometrie.ts   Produktivverbraucher: KEINER
                      (nur sein eigener Test importiert es; werkzeugLandkarte.ts
                       nennt es in einem Kommentar als kuenftige Grundlage)
kontur.ts             importiert AUSSCHLIESSLICH signierteFlaeche (:39)
```

> **Es gibt jetzt eine allgemeine Geradenschnitt-Funktion — und die Konturprüfung benutzt sie
> nicht.** *Sie rechnet ihren Schnitt weiterhin selbst.* **Damit steht der Satz des Auftrags
> unverändert: F-004 ist für W-18 kein Bestandteil**, weder als Gehrungsdetail in `wallGeometry`
> noch als reine Formel in `geradenGeometrie`.

*Ob die Konturprüfung eines Tages auf `geradenSchnitt` umgestellt werden sollte, ist eine eigene
Frage — hier nur festgehalten, dass sie es heute nicht ist.*

## Normative Größen

**Keine.**
