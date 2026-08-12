# W-09 · Treppe — FORMELN

## Das Register nennt ZWEI Formeln — der Auftrag sagt „keine". Beides gemessen

**Der Auftrag (`W-09/1-4`) sagt: *„Das Register nennt für W-09 keine Formel."*
Die Registerzeile nennt `F-001, F-030`. Der Satz des Auftrags trifft nicht zu** — und er hätte mich
beinahe dazu gebracht, eine leere Spalte zu melden, wo zwei Nummern stehen.

| F-Nr | laut Register | gemessen im Code |
|---|---|---|
| **F-001** Abstand zweier Punkte | ja | **JA, aber nicht in der Auslegung** — `treppe2D.ts:47` und `treppeSvg.ts:125` (je `Math.hypot`); `treppenBerechnung.ts` **0** |
| **F-030** Wand aus Achse extrudieren | ja | **nicht als Aufruf messbar** — `treppe3D.ts` und `treppeObjekt.ts` bilden Körper, aber ohne die Extrusionsform aus F-030 |

**Die Auslegungsschicht rechnet ohne Geometrieformel.** *Dort stehen normative Größen, keine
F-Nummern — und das ist der Teil, den der Auftrag meint.*

**Die Rechenregeln der Auslegung sind normative Größen, keine Geometrieformeln:**

| Regel | Fundstelle | Art |
|---|---|---|
| Schrittmaß `2·Steigung + Auftritt` | `resources/planner/hausplaner/geometry/treppenBerechnung.ts:75` | **normativ** (DIN 18065) |
| Bequemlichkeit `Auftritt − Steigung` | **:76** | **normativ** |
| Sicherheit `Auftritt + Steigung` | **:77** | **normativ** |
| Grenzmaße je Bereich | **:52-55** | **normativ**, drei Nutzungsbereiche |
| lichte Durchgangshöhe 2000 mm | **:58** | **normativ** |

**Keine dieser Regeln hat eine F-Nummer in der Formelsammlung — und sie braucht auch keine.**
*Eine Norm ist keine Geometrieformel, die man nachschlägt; sie ist eine Festlegung.*

> **Eine leere Formelspalte ist besser als eine geratene.** *Nach `603eddc2`, wo sieben von zehn
> Zuordnungen fielen, ist das keine Bequemlichkeit, sondern die Lehre daraus.*

## Was hier NICHT steht

**Die Zahlenwerte der Norm sind nicht Gegenstand dieses Blattes.** Sie stehen im Code (`:52-58`) und
sind dort belegt. *Sie hier zu wiederholen hieße, eine zweite Fassung derselben Festlegung zu führen.*
