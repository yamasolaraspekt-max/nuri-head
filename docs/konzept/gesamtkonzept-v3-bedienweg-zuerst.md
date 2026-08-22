# GESAMTKONZEPT V3 — „Bedienweg zuerst" (ENTWURF, Dirigent unter Vollmacht, 22.08.2026 13:45)

```yaml
status: ENTWURF — Konzept, keine Regel. Regeländerungen an docs/ARBEITSREGELN.md trifft ausschließlich Yama (§1); sie stehen hier als VORSCHLAG je Ursache.
anlass: "Yama 22.08. 13:3x: 'das Gesamtkonzept nach dieser Bewertung umstellen, damit diese Schwächen endgültig erledigt sind und nie wieder als Hindernis auftreten' — Bewertung der Entwicklung, Stand 22.08. 13:28, Integration 17d8cb2c, Ursachenmessung 13:12"
mess_sha: "Integration 17d8cb2c (Bewertung) / fafcc882 (dieses Blatt) — eine Uhr: Commit-Zeiten"
spur: "A (Regelwerk/Steuerung) — der Apparat selbst wird umgestellt; Produkt-Bauten daraus laufen danach auf der neuen Werkzeugspur"
heimat: "docs/konzept/ (Entwurf) -> nach Yamas Entscheidung: ARBEITSREGELN-Fassung 1.5 / regelwerk/, Berichtsregeln, Rollenquellen"
```

## 0 · Der Satz, der alles trägt

**Gebaut wird nur, was einen Bedienweg hat oder einen benennt — und abgenommen ist nur, was ein Mensch im Browser
bedienen kann.** Alles andere (Rechenkerne, Infrastruktur, Regelwerk) bleibt erlaubt, aber es trägt ab jetzt
sichtbar das Etikett „kein Bedienweg, Anschluss über …" und zählt im Bericht nicht als Produktfortschritt.

Die Bewertung misst fünf Ursachen. Jede bekommt unten **einen Mechanismus** (was sich ändert), **einen Besitzer**,
**eine Messgröße** (woran man sieht, ob es wirkt) und **einen Stopp-Auslöser** (was passiert, wenn die Zahl kippt).
Eine Schwäche, die gemessen, besessen und mit Stopp versehen ist, kann nicht still zurückkommen — sie kann nur
offen zurückkommen, und dann steht sie im nächsten Lagebericht.

## 1 · Ursache 1 — „Es wird gerechnet, nicht bedient" (7 von 2.996 Commits berühren eine Bedienfläche; 27/160 Module ohne Ladeweg; keine Brücke Register ↔ Registry)

**Mechanismus (drei Teile, alle drei nötig):**
- **N4 Bedienweg-Zeile (Pflichtteil der BEREIT-Liste, neben N3 Matrix):** jedes Produkt-Blatt nennt *vor* dem DoR
  entweder den Bedienweg (Werkzeug-Kennung in `toolRegistry`, Menü/Route, Auslöser im Browser, Zielreifegrad
  `BROWSERABGENOMMEN`) **oder** ausdrücklich `bedienweg: keiner — Anschluss über <Kennung>`. Ein Blatt ohne N4 ist
  nicht BEREIT. Der Plan-Prüfer prüft N4 wie N3.
- **Die Brücke Register ↔ Registry als eine Tabelle** (`docs/konzept/werkzeug-register.md`, danach Regelwerk):
  Registerzeile · Modulpfad · `toolRegistry`-Kennung · Reifegrad · Blatt. Seed = Anschluss-Vorlage des Planners
  (KONZEPT-planner-anschluss, gen 15, heute). Ohne Kennung in dieser Tabelle kann kein Blatt „anschließen" — also
  muss die Tabelle zuerst gefüllt sein. Das ist die **Anschlussentscheidung**: Yama wählt je Paket
  anschließen / parken / verwerfen (Vorlage heute, Empfehlung ausgesprochen, Entscheidung Yama).
- **Abnahme = Bedienbarkeit:** für Produkt-Blätter ist `ABGENOMMEN` erst mit Browserabnahme (Reifegrad 3) zulässig;
  Kriterien-grün ohne Browser heißt `ABGENOMMEN (CODE)` und wird im Lagebericht getrennt gezählt. Evaluator führt
  beides getrennt; der Zustandscommit trägt den Reifegrad.

**Besitzer:** Planner (N4 je Blatt, Tabelle), Plan-Prüfer (N4 im DoR), Evaluator (Reifegrad im Votum), Yama
(Anschlussentscheidung). **Messgröße:** Bedienweg-Quote = Produktcommits mit Bedienfläche / Produktcommits
(heute 7/46 = 15 %; Ziel > 50 % ab der ersten Anschlusswelle) · Module ohne Ladeweg (heute 27, Ziel: nur noch die
ausdrücklich geparkten). **Stopp-Auslöser:** zwei Lageberichte in Folge ohne Anstieg der Bedienweg-Quote → Zuschnitt
neuer Rechen-Blätter pausiert, bis ein Anschluss-Blatt gebaut ist („Anschluss vor Rechnung").

## 2 · Ursache 2 — Eigenausrüstung verbraucht zwei Drittel (227 Werkstatt- vs. 107 Produkt-Commits seit 19.08.)

**Mechanismus:** Eigenausrüstung ist ab jetzt **nur noch aus drei Quellen** zulässig: (a) Stopp-Regel (ein Vorfall,
der Schreibschutz/Integrität verletzt — wie A-37), (b) die vier benannten Z0-Restposten (Z0-I1 Test-DB, Z0-I2
Claim, Z0-I3 Identität, Z0-I4 Dispatcher) in dieser Reihenfolge, (c) Errata an laufenden Regelbauten. Kein neuer
Regelbau ohne einen dieser Anlässe; Yamas Maximum (vier offene Regelbauten) bleibt und wird im Lagebericht gezählt.
**Besitzer:** Dirigent (Zuschnitt-Freigabe), Yama (Maximum). **Messgröße:** Werkstatt-/Produkt-Verhältnis je Tag
(heute 2,1 : 1; Ziel ≤ 1 : 1 ab 23.08., ≤ 1 : 2 ab Anschlusswelle). **Stopp-Auslöser:** ein Tag mit Produktivzeilen
= 0 **und** Werkstatt-Commits > 50 → der Lagebericht trägt „Werkstatt-Tag", der zweite in Folge braucht Yamas
ausdrückliche Freigabe (die Bewertung: „Ein Tag ist vertretbar. Zwei wären eine Frage.").

## 3 · Ursache 3 — Ein Auftrag kostet zehnmal mehr Abstimmung als Bau (A-37: Generator 10 % der Commits)

**Mechanismus — zwei Spuren, jetzt mit harten Grenzen statt Gefühl:**
- **Spur A (Sicherheit/Regelwerk/Infrastruktur):** bleibt, wie sie ist — DoR mit Plan-Prüfer, Errata-Bündelung,
  volle Negativproben, Nachprüfung. Für A-37 war das richtig (Tor, 31 Kriterien, 7/7 wirksam).
- **Spur W (Werkzeug/Produkt-Slice), „verdient" durch vier Eigenschaften:** Bedienweg benannt (N4) · kein Rechte-,
  Geld-, DB-Schema- oder Auth-Bezug · ≤ 8 Kriterien inkl. Browserabnahme · Rückweg = Revert eines Commits. Dann gilt:
  **ein** DoR-Durchgang (ERTEILT / NICHT ERTEILT, keine Auflagen-Schleife — Halbsätze werden im DoR-Votum selbst
  mitgeliefert und gelten als Teil des Blatts), Generator baut in **einer** Lieferung, Evaluator nimmt mit Browser
  ab, Integrator setzt den Zustand automatisch (Ursache 5). Errata an Spur-W-Blättern nur gebündelt **nach** der
  Abnahme. Der Generator stuft nicht selbst ein; gewechselt wird nur nach oben (bestehende Regel).
- **Kommentar-Budget:** Spur-W-Lieferungen tragen Belege als Matrixzeilen (Befehl · Rohausgabe · SHA), nicht als
  Prosa; Erklärtexte gehören ins Votum, nicht in den Skriptkopf. (Keine Zensur — ein Maß: die Nachvollzugs-Matrix
  ist der Beleg, der Rest ist Zusatz.)

**Besitzer:** Planner (Einstufung im Blatt, begründet in einem Halbsatz), Plan-Prüfer (bestätigt die Spur),
Dirigent (routet). **Messgröße:** Commits je Lieferung nach Rollenpräfix (Spur W: Generator-Anteil ≥ 40 %,
Gesamt ≤ 15 Commits je Blatt). **Stopp-Auslöser:** ein Spur-W-Blatt überschreitet 15 Commits oder braucht eine
zweite DoR-Runde → es ist keine Spur W; Rückstufung nach A wird protokolliert, und der Planner begründet im nächsten
Blatt, was beim Zuschnitt fehlte.

## 4 · Ursache 4 — Jeder sechste Commit korrigiert einen früheren (17 % Berichtigung/Errata)

**Mechanismus:** die Selbstkorrektur bleibt (sie ist die Stärke), aber **Berichtigungen stapeln nicht**: eine
Berichtigung einer Berichtigung (zweite Ebene zur selben Sache binnen 24 h) ist ein Stopp-Fall — Ursache vor dem
nächsten Schritt, nicht Korrektur der Korrektur. Drei Regeln, die die häufigsten Klassen schließen (alle heute
belegt): `gelesen_bis` vor jeder Meldung (6g), Messbefehle zitieren statt nachbauen (P-02/4), Grundmenge mit STAND
vor jeder Zahl. Ein Lagebericht nennt die Berichtigungsquote **und** die Zahl der Zweit-Ebenen-Berichtigungen.
**Besitzer:** jede Rolle für sich; Plan-Prüfer misst. **Messgröße:** Berichtigungsquote (heute 17 %, gesund ≤ 15 %),
Zweit-Ebene (Ziel 0). **Stopp-Auslöser:** Zweit-Ebene > 0 → der Dirigent hält die betroffene Bahn an, bis die
Ursache benannt ist (Stopp-Regel „Fehler zuerst").

## 5 · Ursache 5 — STATUS.md ist der größte Textproduzent; „der Zustandswechsel IST der Commit" ist nicht wirksam (16/16 Datensätze von Hand nachgetragen)

**Mechanismus — der Zustand entsteht aus dem Ereignis, nicht aus der Hand:**
- Jede Abschlussmeldung (CODE_FERTIG, ABGENOMMEN/NACHBESSERN, TRANSPORTIERT …) trägt bereits alle Felder
  (Kennung, endstand_sha, ergebnis_sha, Beleg). Der Integrator **erzeugt** den Zustandscommit daraus mit
  `status-erzeugen.sh` im Transportlauf — kein Handtext in `docs/STATUS.md`. Block-Anlage bei Schnitt (heute
  eingeführt: Integrator legt beim ersten Transport eines Blatts den Block an). Später: der Dispatcher (Z0-I4)
  löst den Zustandslauf aus, sobald ein Abschlussereignis erscheint.
- **Textbudget für STATUS.md:** ein Datensatz = Felder, keine Prosa (Beleg-SHA statt Begründungsabsatz; Absätze
  gehören ins Votum). Bestehende Prosa bleibt (kein Rückbau), neue kommt nicht dazu. Das Tor kann das prüfen
  (Regelbau, Spur A, nach Z0-I1: „STATUS.md-Änderung trägt die Erzeuger-Marke von `status-erzeugen.sh`").
- **Keine zweite Statuswahrheit:** Tafel, Backlogs, Ereignisse bleiben Belege; STATUS.md bleibt der Träger.

**Besitzer:** Integrator (Erzeugung), Planner (Regelbau „Erzeuger-Marke"), Dirigent (Textbudget im Lagebericht).
**Messgröße:** Handänderungen an STATUS.md ohne Erzeuger-Marke (Ziel 0) · Zeilenbewegung STATUS.md je Woche
(heute 10.618 seit 19.08.; Ziel < Auftragsblätter). **Stopp-Auslöser:** eine Handänderung → Befund, Rückweg
(Revert) und Erzeugung aus dem Ereignis.

## 6 · Der Stau (Zuschnitt > Bau > Abnahme: 15 BEREIT beim Generator, 5 Z1 seit 18 h beim Evaluator)

**Mechanismus:** **Abnahme vor Zuschnitt** — jeder Tag beginnt mit den ältesten Abnahmen (Risikoreihenfolge),
neue Zuschnitte nur, wenn die BEREIT-Zahl beim Generator unter dem Deckel liegt (**Deckel 6**; heute 15).
Überschreitet der Vorrat den Deckel, arbeitet der Planner an Anschluss-/Konzeptvorlagen statt an neuen Blättern.
Die Abnahmekapazität ist der Engpass — deshalb die **Browserabnahme als Pflicht nur für Spur W** (kurz, zielgenau)
und ein zweiter Evaluator-Lauf (eigene Sitzung, eigener Worktree, Lease je Auftrag) **nur auf Yamas Entscheidung**
(V2 §8 lässt zwei Sitzungen derselben Rolle an verschiedenen Aufträgen zu). **Besitzer:** Dirigent (Deckel,
Reihenfolge), Yama (zweite Evaluator-Sitzung). **Messgröße:** BEREIT-Vorrat, Alter des ältesten CODE_FERTIG
(heute 18 h; Ziel < 24 h, dann < 8 h). **Stopp-Auslöser:** ältestes CODE_FERTIG > 24 h → kein neuer Zuschnitt
bis es abgenommen ist.

## 7 · Was sich am Apparat konkret ändert — als Vorschlag an Yama (Regeln ändert nur er)

| # | Änderung | Ort | Art |
|---|---|---|---|
| V3-1 | **N4 Bedienweg-Zeile** als Pflichtteil der BEREIT-Liste; `bedienweg: keiner — Anschluss über <Kennung>` als zulässige, sichtbare Ausnahme | ARBEITSREGELN §5 (neben N3) | Regel (Yama) |
| V3-2 | **Werkzeug-Register** (Registerzeile ↔ Modul ↔ `toolRegistry`-Kennung ↔ Reifegrad ↔ Blatt) als eine Tabelle; Seed = Anschluss-Vorlage | `docs/konzept/werkzeug-register.md` → regelwerk | Konzept → Regel |
| V3-3 | **Spur W** mit den vier Eignungsmerkmalen und den harten Grenzen (ein DoR-Durchgang, eine Lieferung, ≤ 15 Commits, Browserabnahme) | ARBEITSREGELN (Spur-Abschnitt) | Regel (Yama) |
| V3-4 | **Eigenausrüstung nur aus drei Quellen** (Stopp-Regel, Z0-I1..I4, Errata) + Maximum vier | ARBEITSREGELN / VOLLMACHT-Grenze | Regel (Yama) |
| V3-5 | **Berichtigungen stapeln nicht** (Zweit-Ebene = Stopp) | Stopp-Regel „Fehler zuerst" (bestehend) — Präzisierung | Regel (Yama) |
| V3-6 | **Zustand aus dem Ereignis**: Integrator erzeugt, kein Handtext; Erzeuger-Marke als Torprüfung (Regelbau nach Z0-I1) | Integrator-Rollenquelle sofort; Tor später | Steuerung (Dirigent) + Regelbau |
| V3-7 | **Abnahme vor Zuschnitt**, BEREIT-Deckel 6, Alter-Grenze 24 h | Steuerungsregel (README Steuerung) | Steuerung (Dirigent) |
| V3-8 | **Lagebericht** trägt die sechs Messgrößen mit Stopp-Stand: Bedienweg-Quote · Module ohne Ladeweg · Werkstatt/Produkt · Commits je Lieferung nach Rolle · Berichtigungsquote + Zweit-Ebene · STATUS-Handänderungen · Vorrat/Alter | BERICHTSREGELN-FORTSCHRITT §4/§6 | Regel (Yama) — Entwurf liegt bei |
| V3-9 | **Reifegrad im Zustandscommit**: `ABGENOMMEN (CODE)` vs `ABGENOMMEN (BROWSER)` | status-erzeugen.sh (Regelbau klein) | Regelbau |

**Was ausdrücklich NICHT geändert wird:** Rollentrennung (niemand nimmt eigene Arbeit ab) · Beleg statt Behauptung ·
Pull-System, ACK, Lease, Tor (A-37) · Yamas Freigaberecht · Hausplaner-Insel-Grenze · Schutzgrenzen aus CLAUDE.md.

## 8 · Was sofort läuft, ohne Regeländerung (Vollmacht)

1. **Die fünf Z1-Bauten:** W1-3/W1-4 abgenommen → Zustandscommits (Integrator gen 10, heute); W1-5 Nachprüfung
   (Evaluator gen 11, heute); W1-1/W1-2 brauchen Browser + Test-DB → **Y-13 GRANT durch Yama als root** ist der
   einzige Blocker (zwei SQL-Zeilen, Vorlage liegt in `auftraege/Y-13-entscheidung-yama.md`).
2. **Anschluss-Vorlage** (Planner gen 15, heute) → Yama entscheidet je Paket → erste Anschluss-Blätter auf Spur W.
3. **Integrator erzeugt Zustände aus Ereignissen** (gen 10: elf Z-Posten); Block-Anlage bei Schnitt (gen 9) gilt.
4. **Deckel und Reihenfolge** ab sofort in den Rollenquellen: kein neuer Produkt-Zuschnitt, bis der Vorrat ≤ 6
   und das älteste CODE_FERTIG < 24 h ist; der Planner arbeitet bis dahin an Vorlagen/Konzept.
5. **Lagebericht** nächste Fassung mit den sechs Messgrößen (Dirigent), Mess-SHA einer.

## 9 · Rückweg & Entdeckung

Rückweg: dieses Blatt ist ein Entwurf; jede übernommene Regel steht als eigener Commit im Regelwerk und ist
einzeln revertierbar; Rollenquellen sind generationsversioniert. Entdeckung: die sechs Messgrößen stehen in jedem
Lagebericht — fehlt eine, ist der Bericht unvollständig (Berichtsregel); kippt eine, greift der benannte Stopp.

## 10 · Kanten

- Spur W kann missbraucht werden, um Prüfung zu sparen → deshalb die vier Eignungsmerkmale **und** die Rückstufung
  mit Protokoll; Rechte/Geld/DB/Auth sind nie Spur W.
- Der Bedienweg-Zwang kann Rechenkerne „anschlusslos" erscheinen lassen, die absichtlich Bausteine sind → die
  ausdrückliche Ausnahme `bedienweg: keiner — Anschluss über <Kennung>` hält sie sichtbar, statt sie zu verstecken.
- Ein zweiter Evaluator-Lauf ohne Lease-Disziplin erzeugt Doppelabnahmen → nur mit Z0-I2/§8-Leases, nur auf Yamas Wort.
- Textbudget für STATUS.md darf nicht zum Rückbau bestehender Belege führen → nur neue Einträge, nie Löschung.
