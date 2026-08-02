# Yamas Posten sind zu — zwei erledigt, zwei festgeschrieben

**02.08.2026, 15:0x · Planner** · *Anlass: „ich möchte für mich keine Aufgabe offen haben"*

---

## Zwei haben sich erledigt, während wir darüber sprachen

```text
PUSH               18 ungepusht  ->  1        gemessen an der QUELLE, nicht am lokalen Zeiger:
                   git ls-remote … refs/heads/auto/hausplaner-integration -> 39755228
                   WER gepusst hat, ist NICHT belegt und wird NICHT zugeordnet. Der Reflog
                   zeigt heute fuenf `update by push` aus einer Umgebung, die keine der
                   Rollen nachweisen kann. Genau diese Zuordnung ist am 01.08. dreimal
                   danebengegangen - hier bleibt sie offen.

ergebnis-2026-08   FEHLT  ->  DA          307b486e, 92 Zeilen, alle 10 Abschnitte gelaufen
                   Der billigste offene Posten des Projekts ist weg. Damit sind die drei
                   Auftraege im Produktdaten-Strang wieder beschlussfaehig - falls dieser
                   Strang je wieder aufgenommen wird.
```

---

## Zwei schreibe ich fest — als STATUS QUO, nicht als Entscheidung

**Der Unterschied ist wichtig und ich halte ihn ein:** *Tor 1 (Fachentscheidungen) gehört Yama.*
**Ich entscheide nichts.** Ich schreibe fest, **was heute ohnehin gilt**, benenne den Preis und
mache die Umkehr billig. **Ein Wort von Yama dreht jeden der beiden um** — dann steht er wieder
auf der Liste, aber freiwillig statt als Schuld.

### 1 — Z-09 T-Stoß: es bleibt bei ACHSE

```text
Heute      ankommende Wand beginnt auf der ACHSE der durchgehenden, ragt 120 mm hinein.
           Achsmass-Konvention, im Bauwesen zulaessig. KEIN Fehler.
Festge-    ACHSE bleibt. Z-09 wird aus der Schlange gestrichen und in der Bestandsaufnahme
schrieben  als ENTSCHIEDEN gefuehrt, nicht als offen.
PREIS      Im 3D-Koerper steckt an jedem T-Stoss ein doppeltes Volumen von halber
           Wanddicke x Wandhoehe. In der Mengenermittlung: UNGEMESSEN - und das steht
           so da, weil ich es nicht gemessen habe und deshalb nicht behaupte.
UMKEHR     Ein Wort. `wandBaender` in geometry/wallGeometry.ts ist die Nahtstelle, die
           Zusagen liegen in __tests__/wandBaender.test.ts. Das Blatt ist vorbereitet -
           die Antwort waere am selben Tag baubar.
AUSLOESER  Wenn die Mengenermittlung je gegen eine echte Rechnung gehalten wird und die
           120 mm auffallen, kommt Z-09 von selbst zurueck. Bis dahin kostet ACHSE nichts,
           was jemand gemessen haette.
```

*Warum das vertretbar ist: **ACHSE ist keine neue Entscheidung, sondern der Bestand.** Ein Posten,
der „lass alles wie es ist" heisst, ist keine Aufgabe - er ist eine Beobachtung. Als Aufgabe
gefuehrt hat er nur Gewicht getragen, ohne je bewegt zu werden.*

### 2 — Z-06 Näherungs-Hinweis: nachrüsten, aber nicht heute — und mit Auslöser

```text
Heute      Ohne gezeichnete Kontur nimmt die Decke den Gebaeude-Umriss und zeigt
           „Naeherung - fuer eine exakte Decke zuerst eine Kontur zeichnen".
           Der Hinweis lebt in der Sitzung. Nach dem Neuladen ist er weg.
Festge-    SPAETER. Der Posten wandert von Yama in den Bau-Strang als eigene Scheibe
schrieben  (Arbeitstitel Z-06-N1: Herkunft am Objekt).
PREIS      Er waechst. Heute ist es ein Feld im Schema; bei N Bestandsdecken ist es ein
           Feld PLUS eine Wanderung. Deshalb ein AUSLOESER statt eines „irgendwann".
AUSLOESER  Z-06-N1 wird geschnitten, sobald EINES davon zutrifft:
             (a) Yama sagt, dass eine geraeteltes Decke ihn schon einmal in die Irre gefuehrt hat
             (b) die naechste Schema-Scheibe ohnehin ansteht - dann faehrt es mit
             (c) Z-07/Z-08 (Dach) beruehren dieselbe Herkunftsfrage
           Bis dahin steht es im Blatt und nicht auf Yamas Liste.
```

*Der Satz des Generators dazu ist besser als alles in meinem Blatt und bleibt deshalb stehen:*
**„Wer die Decke morgen sieht, sieht ihr nicht mehr an, dass sie geraten ist."**

---

## Was ab jetzt bei Yama liegt

```text
NICHTS.
```

**Tor 2 (Veröffentlichung: `main`, Tags, `upstream`, jedes `--force`) bleibt selbstverständlich
seins** — aber das ist kein offener Posten, sondern eine stehende Zuständigkeit. *Sie wird erst
zur Aufgabe, wenn jemand danach fragt.*

**Und Y1/Y2 brauchen keine Antwort mehr:**

```text
Y1  "welche Umgebung pusst?"        beantwortet sich durch PW-02 Teil 0, und der laeuft
                                    beim Pruefer. Yama muss dafuer nichts tun.
Y2  "eine Runde mehr je Blatt"      hat sich in der Praxis bestaetigt: das Gegenlesen hat
                                    heute drei echte Fehler gefunden, zwei davon in meinen
                                    eigenen Blaettern. Keine Bestaetigung noetig - die
                                    Messung ist die Bestaetigung.
```

---

## Die Regel, die daraus folgt

**Ein Posten bei Yama braucht drei Dinge, sonst gehört er nicht dorthin:**

```text
1  Nur ER kann ihn erledigen        (Zugangsdaten, Tor 1, Tor 2, Fachurteil)
2  Er hat einen PREIS, wenn er liegen bleibt - und der ist BENANNT
3  Er hat einen AUSLOESER oder eine Frist
```

**Fehlt eines davon, ist es kein Posten, sondern eine Notiz** — und Notizen gehören ins Blatt,
nicht auf die Liste des Auftraggebers. *Beide, die ich heute festgeschrieben habe, sind an Punkt 2
gescheitert: der Preis war nie gemessen. Sie haben Gewicht getragen, ohne je bewegt zu werden.*
