# W-15 · Material und Farbe — BEDIENUNG

> **Dieses Blatt ist ENTWURF.** *Es gibt keinen Werkzeugeintrag in `toolRegistry.ts` und keine
> Oberfläche — die Angaben unten sind **Vorgabe für Stufe 2**, keine Ablesung. Wo der Vertrag
> schweigt, steht „offen" statt einer erfundenen Taste.*

## Aufruf

| Weg | Wie |
|---|---|
| Werkzeugleiste | **offen** — der Vertrag nennt keinen Platz und kein Symbol. Zu entscheiden in Stufe 2, zusammen mit der Frage, ob drei Einträge drei Schaltflächen werden oder eine mit Untermenü |
| Tastenkürzel | **offen** — kein Kürzel im Vertrag, keins in einem vorhandenen Werkzeug belegt |
| Kontextmenü | **ja, sinnvoll** — das Werkzeug setzt eine bestehende Auswahl voraus (`selection.count >= 1`), und genau dort steht der Anwender schon |

## Ablauf am Bildschirm

| Schritt | Anwender tut | Bildschirm zeigt |
|---|---|---|
| 1 | wählt eine oder mehrere Flächen | die Auswahl ist hervorgehoben; das Werkzeug ist erst jetzt aufrufbar |
| 2 | ruft Material, Farbe oder Textur auf | Auswahlliste; die aktuelle Belegung der Fläche ist erkennbar |
| 3 | wählt einen Eintrag | Vorschau an der Fläche, noch nicht übernommen |
| 4 | bestätigt | Zuweisung steht; das Modell hat eine neue Revision, die Ansicht frischt die betroffenen Objekte auf |

*Schritt 4 ist die Stelle, an der die vier Seiteneffekte des Vertrags eintreten
(`werkzeugVertrag.ts:880`, `:892`, `:904`).*

## Rückmeldungen

| Lage | Anzeige | Ton |
|---|---|---|
| Alles gut | „Material zugewiesen — 3 Flächen." | sachlich |
| Eingabe unvollständig | „Bitte zuerst ein Material auswählen." | hinweisend |
| **Kein Projekt offen** | **„Es ist kein Projekt geöffnet. Material kann nur an einem geöffneten Projekt zugewiesen werden — bitte zuerst ein Projekt öffnen oder anlegen."** | **erklärend** |
| **Nichts ausgewählt** | **„Es ist keine Fläche ausgewählt. Wähle zuerst mindestens eine Fläche aus; das Material wird genau den ausgewählten Flächen zugewiesen."** | **erklärend** |
| **Keine Berechtigung** | **„Du darfst dieses Projekt nicht bearbeiten. Die Materialzuweisung ändert das Modell und ist deshalb gesperrt. Wende dich an die Projektleitung, wenn du Bearbeitungsrechte brauchst."** | **erklärend** |
| **Textur ohne Zuweisung** | **„Für diese Fläche ist noch kein Material zugewiesen. Eine Textur wird auf ein bestehendes Material gelegt — weise zuerst ein Material zu, dann die Textur."** | **erklärend** |

> **Pflicht erfüllt:** *für jede der vier Absagen aus `7-GRENZEN.md` steht hier ein Satz, den ein
> Handwerker versteht — mit **was** und **warum** und einem Weg nach vorn.* **Kein
> „PermissionDenied", kein stilles Nichts.**

## Abbruch

- **Esc** bricht ab. Danach ist der Zustand **exakt** wie vorher.
- **Halbfertiges wird nie gespeichert** — die Vorschau aus Schritt 3 ist Anzeige, nicht Modell.
- *Der Vertrag stützt das: `umkehrbar: true` bei allen drei Einträgen (`:881`, `:893`, `:905`).*

## Tastenkürzel während des Werkzeugs

| Taste | Wirkung |
|---|---|
| Esc | abbrechen |
| Eingabe | bestätigen |
| Umschalt | **offen** — keine sinnvolle Zwangsführung erkennbar; Material kennt keine Winkel |
| Alt | **offen** — es gibt keinen Fang, der ausgesetzt werden könnte |

*Die zwei „offen" sind bewusst keine erfundenen Belegungen: die Vorlage fragt nach Winkelzwang und
Fang, und **beides gibt es bei einer Zuweisung nicht.***
