# A-16 — `TIME_VARS` im Produktivbaum. Die Zahlen stehen dort, wo Yama sagt — **ausgeliefert wird die Datei nicht**

```yaml
auftrag: "A-16"
titel: "Elf unbelegte Zeitwerte und ein harter Stundensatz rechnen einen Lohnkostenbetrag — in einer Datei ohne Aufrufer"
art: "MESSEN + ENTSCHEIDEN. Kein Bau am Wert (Yamas Punkt 4)."
spur: A
heimat_app: ticket
dor_beleg: "95b4de4f — plan-pruefer 12.08.: 'A-16 DoR BESTANDEN, BEREIT beim Generator: alle
         Zeilenangaben treffen exakt' + 'MACHBARKEIT GEMESSEN, der Punkt an dem eine DoR faellt:
         A-16-1 verlangt jede view()-Stelle mit Variable einzeln zu oeffnen — es sind VIER, machbar.'
         NACHGEZOGEN 12.08. vom Planner auf den Befund des Generators (bfba0419): das Blatt trug
         weiter ENTWURF und ballbesitz YAMA, waehrend Tafelzeile und STATUS-Datensatz BEREIT sagten
         und die DoR seit Stunden belegt war. Der Generator hat NICHT gebaut, und das war richtig —
         er hat die Reihenfolge des Blattes gelesen statt sie zu erraten."
status_steht_in: docs/STATUS.md
basis_sha: 5d88f198
prioritaet: P1
anlass: "Yamas Antwort 12.08. Punkt 1 ('TIME_VARS steht im laufenden Produktivcode'), Vorrang vor der Werkzeugarbeit"
ballbesitz: "GENERATOR — Weiche und DoR sind beide durch. Yama hat W3 gewaehlt (eingetragen in
         95b4de4f), der plan-pruefer hat die DoR im selben Commit bestanden gemeldet."
claim: "planner 12.08. — Claim VOR dem Schnitt."
grundlage: "F-051 🔴 (FORMELSAMMLUNG) · docs/BERICHT-M02-AUSGEWERTET.md · Yamas Fundstelle roof.blade.php:73"
praemisse_geaendert: "ja — siehe Abschnitt 1. Die Fundstelle haelt zeichengenau, der Weg zum Bildschirm nicht."
```

## 1 · Yamas Fundstelle hält. Seine Prämisse hält nicht — und das ist gemessen

**Zuerst die Bestätigung, Zeichen für Zeichen:**

```text
resources/views/admin/layouts/roof.blade.php:73   // time assumptions (minutes) – adjust to your company values
                                            :74   const TIME_VARS = {  … elf Werte …
                                          :1662   const installMinutes = …
                                          :1672   const laborCost = (installMinutes / 60) * 65;
                                          :1675   const totalInvest = … + laborCost + misc;
                                          :2266   'Montage (Arbeit)' ${bom.costs.laborCost.toFixed(2)} €
                                          :2268   'Gesamt (Netto)'   ${bom.costs.totalInvest.toFixed(2)} €
```

*Die Rechnung existiert, sie führt zu einem Euro-Betrag, und `* 65` steht hart im Code ohne Quelle,
Datum und Gewerk. Alles wie beschrieben.*

**Was nicht hält — vier Messungen, jede mit Befehl und Zahl:**

```text
(1) statische View-Referenz auf die Datei, ohne _archiv und vendor:
      grep -r --fixed-strings "admin.layouts.roof"   0 Treffer
      grep -r --fixed-strings "layouts.roof"         0 Treffer
      grep -r --fixed-strings "layouts/roof"         0 Treffer

(2) die Route, die so heisst, zeigt auf eine ANDERE Datei:
      routes/web.php:4755  Route::get('roof', fn() => view('admin.roof_config.roof'));
      resources/views/admin/roof_config/roof.blade.php   existiert, 55.866 Byte
      grep -c TIME_VARS darin                            0

(3) Herkunft der Fundstelle — ein einziger Commit, und der sagt, was sie ist:
      e14cd1ec  2026-06-26  "Checkpoint: save WIP and fix daily_report_time_customers migration"
      2.688 Zeilen, seither nie wieder angefasst

(4) Gegenprobe am Nachbarn im selben Ordner:
      resources/views/admin/layouts/roofsun.blade.php    ebenfalls 0 Aufrufer
      derselbe Ordner enthaelt: "app.blade copy 2.php", "test.blade.php", "test2.blade.php"
```

> **Die Datei liegt im Produktivbaum. Sie wird nicht ausgeliefert.** *Es gibt keinen Bildschirm, auf
> dem dieser Euro-Betrag heute erscheint — kein Nutzer, keine Route, kein `@include`.*

**Ehrlich zur Grenze dieser Messung:** *ich kann nur **statische** View-Namen ausschließen. Es gibt
im Haus mindestens eine dynamische Auflösung (`ProductController.php:443  view($view, …)`). Ein
Aufruf über eine Variable würde meinem Grep entgehen. Ich behaupte also **„kein statischer
Aufrufer"**, nicht „technisch unerreichbar" — das ist Kriterium A-16-1.*

## 2 · Was der Befund dadurch wird — kleiner in der Wirkung, größer in der Klasse

**Die Wirkung heute ist null:** kein Angebot, keine Anzeige, kein Ausdruck. `data-action="offer"`
([roof.blade.php:2445-2447](../../../resources/views/admin/layouts/roof.blade.php#L2445)) ruft
`setStep(3)` — eine Druckansicht, kein `fetch`, kein `axios`, kein `<form action>`. In der Datei
existiert **kein einziger Schreibpfad zum Server**. Die Belegkette Angebot → Auftrag → Rechnung ist
nicht berührt.

**Die Klasse ist dieselbe wie bei N-003, mit einem Unterschied, der zählt:**

```text
N-003 / A-14   eine Fachaussage wird durch eine Plakette ueberhoeht     -> sichtbar, wirkt
A-16           ein Geldbetrag ohne Deckung liegt fertig verdrahtet da   -> unsichtbar, wirkt NOCH nicht
```

> **Und genau darin liegt die Gefahr: 2.688 Zeilen fertige PV-Konfigurator-Oberfläche, die auf eine
> Route wartet.** *Wer morgen einen PV-Konfigurator braucht, findet diese Datei, legt drei Zeilen in
> `web.php` — und in derselben Minute ist der Lohnkostenbetrag live, ohne dass jemand die Zahlen je
> geprüft hat. Der Aufwand für „live" ist drei Zeilen. Der Aufwand für „geprüft" ist Yamas
> Firmenkalkulation.*

**Ein Vorbehalt existiert sogar schon — und er ist an die falsche Adresse gerichtet:**

```text
roof.blade.php:2255  'Werte sind Richtwerte (min-basierte Heuristik). Passe TIME_VARS + BOM_DEFAULTS an.'
  steht unter  'Zeit & Aufwand'  (Zeile 2251-2254)
  NICHT unter  'Montage (Arbeit) … €'  /  'Gesamt (Netto) … €'  (Zeile 2266-2268)
```

*Zwei Mängel in einer Zeile: der Vorbehalt hängt an der **Zeit** und nicht am **Geld** — und er sagt
dem **Nutzer**, er solle `TIME_VARS` anpassen, was nur ein Entwickler kann. Ein Vorbehalt, der die
falsche Größe kennzeichnet und den falschen Leser anspricht, ist keiner.*

**Was ich NICHT gefunden habe, obwohl ich gesucht habe:** *einen Einheitenfehler beim Gerüst. Der
Kommentar sagt „per m2 facade area", und [Zeile 1656-1657](../../../resources/views/admin/layouts/roof.blade.php#L1656)
rechnet `(length + width) * 2 * height` — Umfang mal Höhe, also tatsächlich Fassadenfläche. Die
Einheit ist korrekt angewandt. Ich hatte einen Fehler vermutet und keinen gefunden; das steht hier,
damit niemand ihn erneut sucht.*

## 3 · WEICHE — Yama, das ist die Entscheidung, die ich nicht treffen darf

Deine Kriterien 2 und 3 („der Betrag trägt seinen Vorbehalt mit", „der Stundensatz wird benannt oder
er fällt") sind Bauaufträge an eine Datei, **die niemand ausliefert**. Einen Vorbehalt dort
einzubauen wäre Arbeit ohne Wirkung. Drei Wege, ich empfehle den dritten:

| | Weg | Was dafür spricht | Was dagegen |
|---|---|---|---|
| **W1** | **Stilllegen** — Datei nach `_archiv/` verschieben | die Falle verschwindet aus dem Produktivbaum | 2.688 Zeilen brauchbare UI aus dem Blick; Verschieben ist eine Löschung im Sinne der Rückfall-Regel und braucht deine Freigabe |
| **W2** | **Vollbau jetzt** — Vorbehalt an den Geldbetrag, Stundensatz benennen | die Datei wird benutzbar | du müsstest **jetzt** die richtigen Firmenwerte nennen; dein Punkt 4 sagt gerade, dass das nicht geschehen ist |
| **W3** | **Warnschild + Sperrvermerk, Datei bleibt** ✅ | kostet dich keine Zahl, macht die Falle sichtbar für den Nächsten, ist rein additiv und jederzeit zurückdrehbar | die unbelegten Werte bleiben stehen — bewusst, und mit Vermerk warum |

> **Warum W3:** *dein Punkt 4 lautet „**KEIN** Wert wird geändert, bevor ICH die richtigen genannt
> habe. Eine falsche Zahl durch eine andere falsche zu ersetzen ist keine Korrektur." W1 und W2
> verlangen beide etwas von dir, das heute nicht vorliegt — eine Freigabe zum Verschieben oder deine
> Kalkulationswerte. W3 verlangt nichts und schließt die Lücke, die zählt: **dass der Nächste die
> Sperre nicht sieht.** Wenn deine Werte kommen, wird aus W3 ohne Umbau W2.*

## 4 · Abnahmekriterien

```text
A-16-1  AUFRUFER, abschliessend statt stichprobenhaft. Der statische Befund (0/0/0) wird um die
        dynamische Luecke ergaenzt: jede Stelle im Haus, die view() mit einer VARIABLEN aufruft,
        wird einzeln geoeffnet und die moeglichen Namen benannt. Ergebnis ist eine Aussage mit
        Reichweite: "kein Aufrufer, statisch UND dynamisch geprueft" oder "erreichbar ueber X".
        Belegform: Befehl + Trefferzeilen (B5). Zaehlergebnis allein genuegt nicht.

A-16-2  SPERRVERMERK am Ort der Zahlen. Ein Kopfkommentar direkt ueber TIME_VARS (Zeile 73) und
        ueber der laborCost-Zeile (1672) nennt: F-051 GESPERRT, keine Herkunft, nicht verwenden
        ohne Yamas Firmenwerte, und den Verweis auf dieses Blatt. KEIN Zahlenwert wird angefasst.
        Gegenprobe: git diff zeigt ausschliesslich Kommentarzeilen, 0 geaenderte Ziffern.

A-16-3  DER STUNDENSATZ wird als eigener Posten benannt, nicht mitbehandelt: '* 65' ist keine
        Zeitannahme, sondern ein Preis. Er bekommt seine eigene Vermerkzeile mit denselben drei
        Fragen (Quelle, Datum, Gewerk) und ausdruecklich OHNE Vorschlagswert.

A-16-4  DER FALSCHE VORBEHALT wird benannt, nicht verschoben: Zeile 2255 kennzeichnet die Zeit und
        spricht den Nutzer an. Dass er die falsche Groesse traegt, wird im Blatt festgehalten; die
        Umhaengung an den Geldbetrag geschieht ERST, wenn die Datei ausgeliefert wird. Begruendung
        im Bericht: eine Sichtaenderung an einer nicht ausgelieferten Datei ist nicht pruefbar,
        weil es keinen Bildschirm gibt, an dem der Evaluator sie messen koennte.

A-16-5  KEIN WERT GEAENDERT — Yamas Punkt 4 woertlich. Gegenprobe des Evaluators: die elf
        TIME_VARS-Ziffern und die 65 sind vor und nach dem Bau zeichengleich, belegt durch
        git diff der Zeilen 74-86 und 1672 (erwartet: 0 Ziffernaenderung).

A-16-6  F-051 wird um den vierten Fundort ergaenzt, MIT der gemessenen Lage: "im Produktivbaum,
        kein statischer Aufrufer" — nicht "live". Die Ampel bleibt 🔴; ihre Begruendung wird
        breiter (vier Fundorte, null Quellen), nicht schaerfer (keine Auslieferung).

A-16-7  DIE BELEGKETTE wird ausdruecklich freigemessen: 0 Schreibpfade zum Server in der Datei
        (kein fetch/axios/form action), damit aktenkundig ist, dass Angebot → Auftrag → Rechnung
        unberuehrt ist. Ohne diese Zeile bleibt der Verdacht im Raum.
```

## 5 · Rückweg & Entdeckung — als eigene Zeile, nicht im Fließtext

*(Dritter Fall in Folge war der Mangel; hier ist er behoben.)*

```text
RUECKWEG      reiner Revert. Der Bau fuegt ausschliesslich Kommentarzeilen ein — kein Wert, kein
              Datenpfad, keine Migration, kein Template-Ausdruck. Rueckwaerts-Patch via
              git apply --check -R muss Exit 0 liefern, OHNE den Arbeitsbaum anzufassen.
KOPIE AUSSERHALB DER MASCHINE  ZUM BAUZEITPUNKT ZU PRUEFEN, hier NICHT behauptet.
              BERICHTIGUNG meiner ersten Fassung ("vorhanden: fork/main + backup-private/main"):
              selbst nachgemessen, nachdem der Release-Pruefer den abgelehnten Push gemeldet hatte
              (95b98707: PUSH ABGELEHNT, non-fast-forward, nicht forciert):
                7d6c39cf (der Commit, der dieses Blatt traegt)
                  fork/main  NEIN   ·   backup-private/main  NEIN   ·   origin/main  NEIN
              Die Zusage war zum Zeitpunkt des Schreibens falsch — ich habe eine Kopie behauptet
              statt sie zu messen. Der Bau setzt voraus, dass der Rueckfallpunkt AM BAUTAG auf
              einem Fernziel liegt und das mit Befehl belegt ist. origin zaehlt weiterhin NICHT
              als Kopie (Rueckstand).
ENTDECKUNG    woran man merkt, dass es schiefging: die elf Ziffern in Zeile 74-86 und die 65 in
              Zeile 1672 sind das Signal. Weicht EINE davon ab, ist Punkt 4 gebrochen — messbar
              mit einem einzigen git diff, ohne Fachwissen.
ZERSTOERUNGSFREI  die Datei wird nicht verschoben und nicht geloescht (Rueckfall-Regel: kein
              Loeschen ohne Yamas Freigabe). W1 bleibt jederzeit moeglich.
```

## 6 · Konfliktprüfung §3 — unmittelbar vor dem Schnitt gemessen (H-4)

```text
git status --short, Spalten GETRENNT gelesen (die Lehre aus ad8f7314):
  Index (gestaged)   LEER
  Arbeitsbaum        docs/BERICHT-A-15-klassifikation.md  (halber git mv 82d7c31e, Generator-Eigentum)
  unbekannt          1692, zz-unlink-probe  (fremd, nicht angefasst)
§3-Stand           1 IN_ARBEIT: W-09 Treppe (Generator, Schnitt 6e2949a7)
Scope-Ueberschneidung mit W-09   keine — W-09 fasst resources/planner/… an, A-16 nur
                                 resources/views/admin/layouts/roof.blade.php und docs/
A-16 wird auf ENTWURF geschnitten, NICHT auf IN_ARBEIT. Es nimmt keinen §3-Platz.
```

## 7 · Was dieses Blatt über mich selbst belegt

**Ich wäre auf denselben Schluss gekommen wie du** — „steht in `resources/views/`, also ist es live".
Der Ort einer Datei sieht wie ein Beleg für ihre Ausführung aus, und er ist keiner. Das ist deine
neue Hausregel eine Stufe weiter: nicht nur *Mehrfachvorkommen* ist kein Beleg, sondern **der Ort
ist kein Beleg für die Wirkung**. Beides gehört in B7; die Schärfung steht dort.

```yaml
ballbesitz: "GENERATOR — Bau. Weiche und DoR sind durch (95b4de4f)."
ueberholt_12_08: "Hier stand: 'YAMA — Weiche W1/W2/W3. Ohne sie ist A-16-2 nicht schneidbar.'
         Der Satz war richtig, als das Blatt geschnitten wurde, und ist seit Yamas W3-Entscheidung
         ueberholt: A-16-2 IST geschnitten (Sperrvermerk am Ort der Zahlen, Abschnitt 5).
         Der Generator hat ihn gelesen und deshalb NICHT gebaut — die Reihenfolge im Blattfuss
         wiegt fuer ihn schwerer als die Tafelzeile, und das ist richtig herum. Ein ueberholter
         Satz im Fuss hat einen fertigen Auftrag stundenlang aufgehalten."
naechster_schritt: "Yamas Weiche, danach plan-pruefer DoR"
messung_liegt_vor: "Aufrufer 0/0/0 statisch · Route zeigt auf andere Datei · 0 Serverschreibpfade
                    · Einheit Geruest korrekt · Vorbehalt vorhanden aber an falscher Groesse"
kein_bau_ohne: "Yamas Firmenwerte (Punkt 4) — die Zahlen bleiben unberuehrt"
```

## §11 — Votum A-16 (Evaluator, Erstinstanz, 12.08.2026)

```yaml
auftrag: "A-16"
votum: ABGENOMMEN
fehlerklasse: KEINE
abnahme_commit: "ad13b732"
elter: "f37423fa"
pruefstand: "worktree --detach auf ad13b732 und f37423fa"

messtisch_alle_sieben:
  A-16-5: GRUEN — DER KERN, zeichengenau gegen den Elter
    beleg: "roof.blade.php: 43 Einfuegungen, 0 LOESCHUNGEN. Es kann keine Ziffer bewegt worden
            sein, weil keine Zeile ersetzt wurde. Zusaetzlich die elf Werte einzeln extrahiert
            und gehasht: Elter und Bau 4f1ba659… — IDENTISCH, je 11 Eintraege.
            Die laborCost-Zeile ist zeichengleich (nur die Zeilennummer wandert 1672 -> 1715)."
    meine_falle_dabei: "Mein erster Vergleich nahm 'grep -A14 TIME_VARS' und meldete
            verschiedene md5 — weil das Raster die NEUEN Kommentarzeilen mitfing, die selbst
            Ziffern tragen (F-051, 12.08.). Beinahe 'die Ziffern haben sich bewegt' gemeldet.
            Und mein zweites Raster [A-Z_]+ verschluckte SCAFFOLD_M2, weil der Name eine Ziffer
            traegt — 10 statt 11. Erst [A-Z0-9_]+ war richtig. Zweimal mein Raster, keinmal der Bau."
  A-16-1: GRUEN — abschliessend, und ich habe beide Richtungen nachgefahren
    statisch: "grep -rn 'admin.layouts.roof' app/ routes/ resources/views/ -> EIN Treffer, und
            der ist SEIN EIGENER Kommentar in der Datei selbst (roof.blade.php:91). Also 0
            echte Aufrufer."
    dynamisch: "grep nach view() mit Variable im ganzen Haus -> drei Treffer, davon zwei
            FUNKTIONSDEFINITIONEN (draf_view($id), view($id)) und EINE echte Aufrufstelle:
            app/Http/Controllers/Product/ProductController.php:443 'view($view, compact('.
            Dort geoeffnet: $view wird zwei Zeilen davor gesetzt auf genau zwei feste Namen —
            '…partials.product_cards' oder '…partials.product_list'. Keiner davon diese Datei."
    ergebnis: "Seine Aussage 'kein Aufrufer, statisch UND dynamisch geprueft' haelt, und die
            Reichweite steht dabei. Das ist H-8(b) in seiner eigenen Anwendung: der ORT ist
            kein Beleg fuer die Wirkung."
  A-16-2: GRUEN
    beleg: "Sperrvermerk direkt ueber TIME_VARS (:88-101) mit F-051, den vier Fundorten, dem
            was je Wert fehlt, der Reichweite und dem Verweis aufs Blatt.
            Gegenprobe des Kriteriums selbst gefahren: von den 43 neuen Zeilen sind
            NICHT-Kommentarzeilen = 0. Keine geaenderte Ziffer, ausschliesslich Kommentar."
  A-16-3: GRUEN
    beleg: "Eigener Vermerk ueber der laborCost-Zeile (:1705-1714) mit denselben drei Fragen
            (Quelle, Datum, Gewerk) und ausdruecklich OHNE Vorschlagswert — mit der richtigen
            Begruendung: 'ein Vorschlag waere genau die falsche Zahl, die Yamas Auflage
            verbietet'. Und der Satz, der die Sache trifft: '* 65' ist ein PREIS, keine Zeit."
  A-16-4: GRUEN
    beleg: "Die Zeile mit dem falschen Vorbehalt ist ZEICHENGLEICH geblieben — Elter :2255
            gegen Bau :2298, byteweise verglichen: gleich. Nur die Zeilennummer wandert durch
            die eingefuegten Vermerke."
    im_blatt_festgehalten: "Das Blatt nennt sie in Z.99 im Wortlaut und begruendet in Z.106,
            warum sie der falsche Vorbehalt ist. Die Umhaengung geschieht erst bei Auslieferung
            — mit dem richtigen Grund: eine Sichtaenderung an einer NICHT ausgelieferten Datei
            ist nicht pruefbar, weil es keinen Bildschirm gibt, an dem ich sie messen koennte."
  A-16-6: GRUEN
    beleg: "FORMELSAMMLUNG +33/-0. Der Nachtrag nennt den vierten Fundort MIT gemessener Lage
            ('im Produktivbaum, kein statischer Aufrufer'), fuehrt beide Zeilenstaende (vor und
            nach dem Bau, 'damit niemand zweimal sucht') und schliesst ausdruecklich die vorher
            offene Zeile ueber die dynamischen View-Namen. Die Ampel bleibt 🔴 — breiter
            begruendet, nicht schaerfer."
  A-16-7: GRUEN — jeden Begriff selbst nachgezaehlt
    beleg: |
      fetch(  0 · axios 0 · $.post 0 · $.ajax 0 · <form 0 · XMLHttpRequest 0 · sendBeacon 0
      action= 7  -> GELESEN statt gezaehlt: :1841 data-action="next", :1845 data-action="offer",
                    :1852 data-action="back" — Knopf-Attribute, kein Formularziel.
    bewertung: "0 Schreibpfade zum Server. Angebot -> Auftrag -> Rechnung ist aktenkundig
            unberuehrt. Er hat die sieben action-Treffer selbst gelesen statt sie als Befund
            zu melden — B5 in eigener Sache."

must_preserve:
  beleg: "Vier Dateien im Bau: der Bericht, STATUS.md, FORMELSAMMLUNG (+33/-0) und die
          Blade-Datei (+43/-0). Kein anderer Produktivcode, keine Testdatei."

was_diesen_bau_heraushebt:
  - "Die Ziffern sind nicht 'nicht geaendert worden' — sie KONNTEN nicht geaendert werden,
     weil der Bau 0 Loeschungen hat. Das ist die staerkste Form dieses Nachweises."
  - "Er schliesst eine Zeile, die vorher als OFFEN in der Formelsammlung stand, statt sie
     stehen zu lassen: die dynamischen View-Namen sind jetzt ausgeschlossen, mit der
     Aufrufstelle und den zwei moeglichen Namen im Klartext."
  - "Der Sperrvermerk steht AM ORT DER ZAHLEN, nicht nur im Blatt — wer die Datei oeffnet,
     sieht die Sperre, ohne das Auftragswesen zu kennen."

zusammenfassung: "Sieben von sieben. Der Kern ist so belegt, wie ein must_preserve belegt sein
     soll: 0 Loeschungen, elf Werte gehasht und identisch, die 65 zeichengleich. Die
     Aufrufer-Frage ist in beiden Richtungen abschliessend beantwortet und von mir in beiden
     nachgefahren — der einzige dynamische view()-Aufruf des Hauses zeigt nachweislich woanders
     hin. Und die Belegkette ist freigemessen, mit gelesenen statt gezaehlten action-Treffern."

ballbesitz: release-pruefer
```
