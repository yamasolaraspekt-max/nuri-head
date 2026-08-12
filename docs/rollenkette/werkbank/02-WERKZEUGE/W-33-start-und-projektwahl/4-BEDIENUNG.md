# W-33 · Start und Projektwahl — BEDIENUNG

## Aufruf

```ts
:193
export function StartView({ onGuided, onKonfigurator, projekte = [] }: Props)
```

| Eingang | Art | Bedeutung |
|---|---|---|
| `onGuided` | `(schritt?: number) => void` | **öffnet die geführte Planung**, wahlweise bei einem Schritt (0-basiert) |
| `onKonfigurator` | `(name: string, fenster?: boolean) => void` | **öffnet einen Konfigurator** (autark), benannt nach dem Modul |
| `projekte` | `readonly ProjektEintrag[]`, **Vorbelegung `[]`** | **die zuletzt bearbeiteten Projekte des Nutzers** |

**Die Vorbelegung ist die Aussage:** *`projekte = []` heißt, dass die leere Liste der Grundzustand
ist und nicht ein Fehlerfall.* **Wer `StartView` ohne Liste rendert, bekommt eine ehrliche Fläche und
keine leere.**

## Was der Anwender sieht — von oben nach unten

```text
:197   Kicker      „Neues Vorhaben"
:198   Titel       „Was möchtest du planen?"
:199   Lead        „Ein ganzes Gebäude — oder nur ein einzelnes Bauteil. Jeder
                    Konfigurator führt dich Schritt für Schritt und läuft auch
                    AUTARK, ganz ohne Gebäude."
:206   ZULETZT     Leerzustand ODER dominante Kachel + Reihe
:231   Projekt     zwei Karten
:253   Schild      „Fachplaner — jeder läuft autark, ohne Gebäude. Fenster, Türen,
                    Treppen und Heizkörper setzt der Experte ins Gebäude; sonst
                    entsteht eine Datei zum Herunterladen"
:258   Fachplaner  FACH.map -> HubKarte
```

> **Die Schildzeile (`:254`) ist an W-35 gebunden** — *sie nennt genau die **vier** Arten des
> Konfigurators und den Rückfall „Datei zum Herunterladen".* **`konfiguratorEhrlich.test.ts:119-121`
> hält sie fest, damit die Zusage hier nicht größer wird als die Funktion dort.** *Eine Fläche
> verspricht, eine andere liefert — und ein Test verbindet beide.*

## Der ZULETZT-Bereich, beide Lagen

### Leer — und das ist der Normalfall

```text
:208   „Noch kein Projekt geöffnet."
:210   „Ein Vorhaben beginnt unten mit Hausplaner — oder mit einem der Fachplaner,
        die auch ohne Gebäude laufen."
```

**Kein Beispiel, keine Vorschau, kein Platzhalter, der wie ein Projekt aussieht** — *stattdessen der
nächste Schritt.*

### Gefüllt

| | dominante Kachel | die übrigen |
|---|---|---|
| Rubrik | **„Weiterarbeiten"** (`:114`) | — |
| Zeile | `Ort · Datum · **zuletzt bearbeitet**` (`:117`) | `Ort · Datum` |
| Pfeil | **ja** (`:121`) | nein |
| Größe | 46 px Bild, Schrift 17 | 38 px, Schrift 13.5 |
| Stelle | **ganz oben, erste in der Tastfolge** | Reihe darunter |

**Der Grund steht im Code (`:218-219`):** *„**Der erste Eintrag ist der Weg zurück in die Arbeit** —
größer, hervorgehoben, als erster in der Tastfolge, und **ein Klick genügt**."*

## Bedienung mit der Tastatur

| Element | Enter | Leertaste | Fundstelle |
|---|---|---|---|
| **Projektkachel** *(ein `<a>`)* | **vom Verweis selbst** | **ergänzt** — `e.preventDefault(); e.currentTarget.click()` | `:147` |
| **Karte** *(mit Ziel)* | `istAusloeser` | `istAusloeser` | `:70` |
| **Karte** *(ohne Ziel)* | **nichts** — sie ist keine Schaltfläche | — | `:54-65` |
| **Hub-Chip** | `istAusloeser` | `istAusloeser` | `:183` |

> **Die Projektkachel ist die einzige Stelle, die `istAusloeser` NICHT benutzt — mit Begründung im
> Code (`:144-146`):** *„Enter löst der Verweis selbst aus; die Leertaste tut er nicht. Nur sie wird
> hier ergänzt. **Bewusst NICHT `istAusloeser`**: das prüft Enter mit — und Enter käme dann zweimal
> an, einmal vom Verweis und einmal von hier."*
>
> **Das ist eine Abweichung von der gemeinsamen Regel, die ihren Grund mitträgt.** *Genau die Form,
> die dieses Projekt verlangt: nicht „anders gemacht", sondern „anders gemacht, weil sonst Enter
> doppelt ankommt".*

**Der Fokusring ist NICHT eigen** (`:101-102`): *„`.hp-studio :focus-visible` in
`HausplanerStudio.tsx` deckt das ganze Studio ab. **Ein zweiter Ring wäre eine zweite Wahrheit über
dieselbe Sache.**"*

## Die Karte ohne Ziel

```ts
:54-65   if (!onClick) → ein <div>, kein role, kein tabIndex, kein Zeiger
:60      <ZustandBadge zustand="in_entwicklung" />
:56      title={grund}
:63      {grund && <div className="hp-start-fussnote">{grund}</div>}
```

**Der Grund steht doppelt da — als Kurzhinweis am Mauszeiger und als sichtbare Fußnote.** *Für
„Sanierungsplan" (`:239`) lautet er: „Der Sanierungsablauf ist ein eigener Weg — er unterscheidet
sich noch nicht vom Neubau-Ablauf."*

> **Eine Karte, die nirgendwohin führt, sagt WARUM.** *Das ist die Umkehrung dessen, was
> `startEhrlich` beanstandet hat: dort führten drei Karten scheinbar irgendwohin und alle zum selben
> Schritt 1.* **Lieber eine Karte, die stillsteht und es begründet, als eine, die beschäftigt
> aussieht.**

## Abbruch

**Es gibt keinen.** *`StartView` ist kein Dialog — es hat kein `onClose`, keinen Escape-Weg und keinen
Zustand, den man verwerfen könnte.* **Alles, was der Anwender hier tut, führt hinaus: `onGuided`,
`onKonfigurator` oder ein Verweis auf eine Objektseite.**

## Sichtprüfung

- [ ] **offen** — *und für dieses Werkzeug ist sie nicht nebensächlich.*

**`projektKlick.test.ts` sagt selbst, was er nicht prüfen kann:**

> *„**Was hier NICHT geprüft wird:** wie es aussieht. Serverseitiges Rendern liefert Markup, kein
> Bild. Ob der dominante Eintrag bei 1024×768 ohne Scrollen sichtbar ist, entscheidet die
> Sichtprobe — **sie ist Teil der Abnahme, nicht ein Anhang.**"*

**Was eine Sichtprobe zuerst ansehen sollte** — *aus der Ablesung abgeleitet:*

```text
1  Kommt die ECHTE Liste an? Die Naht ist gebaut (7-GRENZEN), aber
   NICHT ausgefuehrt gemessen. Das ist die einzige offene Messung.
2  Ist der dominante Eintrag bei 1024x768 ohne Scrollen sichtbar?
   Woertlich die Frage, die projektKlick offen laesst.
3  Ein Projekt OHNE `adresse`: bleibt es sichtbar und ist es wirklich
   keine Schaltflaeche — kein Zeiger, kein Fokus?
4  Der Leerzustand beim allerersten Start: steht dort wirklich nur
   „Noch kein Projekt geöffnet." und keine Beispielzeile?
```
