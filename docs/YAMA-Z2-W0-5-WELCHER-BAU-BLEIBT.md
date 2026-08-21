# Z2-W0-5, welcher Bau bleibt — in Yamas Namen entschieden: der transportierte bleibt, der andere wird bewahrt statt verworfen

> **Release-Prüfer in Yamas Namen, 21.08. ~22:0x.** Auf `e1298913`. Der Generator hat den Posten in
> `14e7b7ac` ausdrücklich bei Yama abgelegt: *„welcher der beiden Bauten bleibt. Meiner ist
> untransportiert und lässt sich folgenlos zurücknehmen; ich fasse ihn ohne Freigabe nicht an."*
>
> **Seine tragenden Aussagen habe ich selbst am Code nachgeprüft, nicht übernommen** — und eine
> Messung nachgeliefert, die ihm verwehrt war.

## Die Lage, nachgemessen

```
28ca0834  21:23:13  im Stamm, transportiert, als CODE_FERTIG gemeldet (2a64326a), beim Evaluator
                    Baustein: app/Support/Planner/PlannerZustaendigkeit.php   203 Z.  (im HEAD)
ef7a8c89  21:24:52  in rolle/generator, NICHT transportiert
                    Baustein: app/Traits/PlannerZustaendigkeit.php            189 Z.  (nicht im HEAD)
Abstand 99 Sekunden · beide derselbe Auftrag, dieselben vier Controller, dieselbe Testdatei
```

**Der sachliche Unterschied ist echt und liegt an einer Zeile:**

```
bleibend  PlannerZustaendigkeit.php:86   hasPermission('Planner','read')
seiner    Traits/…:86                    DB::table('employees')…value('supervisor')  (echte Kette)
```

## Die Messung, die er nicht machen konnte — und sie entlastet

Er schreibt: *„`User::hasPermission()` gibt bei gesetztem `RECHTE_ALLE_FUER_ALLE` für jeden `true`
… ob er in der laufenden Installation gesetzt ist, kann ich nicht messen — `.env` zu lesen ist mir
verweigert, und das ist richtig so."*

**Ich kann es, und ich habe es getan — ohne einen Wert zu zitieren:**

```
config/rechte.php:32     'alle_fuer_alle' => env('RECHTE_ALLE_FUER_ALLE', false)
.env                     Eintrag FEHLT  ->  es gilt die Vorgabe: false
User.php:64-66           if (config('rechte.alle_fuer_alle')) return true;   <- greift damit NICHT
```

> **Der Schalter ist in dieser Installation nicht gesetzt.** *Die Lücke, die er als Möglichkeit
> benannt hat, ist heute nicht offen.* **Das entlastet den bleibenden Bau für den heutigen Stand —
> es ändert nichts daran, dass er an einem Schalter hängt, der ihn aushebeln würde.**

## Die Entscheidung

### 1 · Der transportierte Bau bleibt

**`28ca0834` gilt.** Drei Gründe, keiner davon Geschmack:

```
(a) Reihenfolge   99 Sekunden frueher, und bereits als CODE_FERTIG beim Evaluator —
                  ihn zurueckzuziehen entwertet eine LAUFENDE Abnahme
(b) Empfehlung    der Bauende selbst: "Meiner soll deshalb weichen — die Reihenfolge
                  entscheidet, nicht die Meinung"
(c) Messung       der Einwand gegen ihn (hasPermission-Abhaengigkeit) ist heute nicht
                  wirksam, weil der Schalter nicht gesetzt ist
```

### 2 · Sein Bau wird NICHT verworfen, sondern bewahrt

**Und das ist der Teil, der nicht selbstverständlich ist.** Sein Bau trägt die
`employees.supervisor`-Kette **mit Zyklen- und Tiefenschutz** — genau das, was der bleibende Bau
ausdrücklich vertagt (*„die Vorgesetztenkette ist Stufe 2 und hängt an Y-9"*).

> ***Ihn zu verwerfen hieße, die Arbeit wegzuwerfen, die bei Stufe 2 gebraucht wird — und sie ist
> fertig.***

```
ENTSCHIEDEN   ef7a8c89 bleibt in rolle/generator erreichbar. Der Zweig wird bis zur
              Entscheidung ueber Y-9 NICHT transportiert — das ist kein Rueckstand,
              sondern der Aufbewahrungsort.
NICHT         geloescht, nicht zurueckgesetzt, kein reset. Es geht nichts verloren,
              und es kollidiert nichts, solange nicht transportiert wird.
```

**Damit ist auch die Rückfall-Regel gewahrt**, aus der er den Posten überhaupt an Yama gegeben hat:
*Original erhalten.* **Er muss nichts anfassen — genau das war seine Sorge.**

## Was ich ausdrücklich NICHT entscheide

```
Kriterium A mit zwei von drei Faellen erfuellt?     -> EVALUATOR. Er hat den Auftrag,
   und der Generator hat es ihm bereits zugestellt.
Vorgesetztenkette kuenftig ueber hasPermission
   oder ueber employees.supervisor?                 -> Y-9. Das ist eine Rechte- und
   Datenschutzfrage — fremde Mitarbeiterdaten samt latest_location —, und die
   vertrete ich nicht.
```

**Meine Entscheidung betrifft nur, welcher Commit gilt und was mit dem anderen geschieht.** Sie
präjudiziert Y-9 nicht: **fällt Y-9 zugunsten der echten Kette, liegt der Bau dafür bereit** — das
ist der Grund, ihn zu bewahren.

## Ein Befund, der zur Abnahme gehört

**Für den Evaluator, gemessen und hier belegt:** der Schalter ist heute aus, also ist A-1 in dieser
Installation geschlossen. **Die Abhängigkeit bleibt aber im Bau** — wer `RECHTE_ALLE_FUER_ALLE`
jemals setzt, öffnet fremde Mitarbeiterdaten samt Standort, **ohne dass ein Test rot wird**. Der
Generator hat das als eigenen Test festgepinnt (`test_a1_offener_rechte_schalter_oeffnet_das_sehen`)
— **das ist die richtige Bauform: die Folge steht fest, statt verschwiegen zu sein.**

## Ball

**Beim Evaluator** — Kriterium A und die Schalter-Abhängigkeit, mit der Messung oben.

**Beim Planner** — die Ursache, die der Generator selbst benennt: `BEREIT` + `ballbesitz: generator`
sagt nicht, **welche** Instanz übernommen hat. Das ist ein Zuteilungs-, kein Baufehler; er trifft
dieselbe Wurzel wie die Nummernkollisionen vom 19.08.

**Bei Yama** — nur noch Y-9, unverändert und ungekürzt.

**Beim Generator** — nichts. **Er muss seinen Bau nicht anfassen**, und der Zweig darf liegen
bleiben.
