# W-17 · Export und Speichern — BEDIENUNG

## Sechs Zustände, sechs Sätze — und der Knopf ist nicht immer derselbe

**`app/dashboard/speicherAnzeige.ts:42-68`, alle sechs wörtlich:**

```text
kein Speicherziel  „Testfläche — wird nicht gespeichert"                    gesperrt
ungespeichert      „Ungespeicherte Änderungen"                             frei
speichert          „Wird gespeichert …"                                    gesperrt
konflikt           „Konflikt: Plan wurde von anderer Seite geändert
                    (Revision N) — Seite neu laden"                        GESPERRT
fehler             „Speichern fehlgeschlagen — erneut versuchen"           frei
gespeichert        „Gespeichert"                                           frei
```

> ***Die zwei gesperrten Fälle sind die interessanten, und sie sind aus verschiedenen Gründen
> gesperrt.*** *Bei `speichert` läuft der Vorgang gerade — ein zweiter Klick erzeugte zwei Anfragen
> mit derselben `base_revision`, und die zweite bekäme 409 vom eigenen ersten Speichern.*
> **Bei `konflikt` ist die Sperre eine Schutzentscheidung:** *der Titel sagt sie wörtlich —*
> „Erst neu laden — sonst würde der fremde Stand überschrieben."

## `konflikt` und `fehler` sind getrennt, und das ist eine Aussage über den Anwender

| | was passiert ist | was er tun kann | Knopf |
|---|---|---|---|
| **`konflikt`** | jemand anders war schneller | **neu laden** und die Arbeit einordnen | **gesperrt** |
| **`fehler`** | die Leitung, der Server, die Nutzlast | **noch einmal drücken** | frei |

> ***Ein gemeinsamer Zustand „ging nicht" würde beide in denselben Satz zwingen.*** *Beim einen
> hilft Wiederholen, beim anderen richtet es Schaden an* — **derselbe Knopf hätte zwei
> gegensätzliche Bedeutungen.**

## Die Testfläche sagt, dass sie eine ist

**`:43-49`** — *ohne `speichernUrl` ist der Knopf gesperrt und der Text nennt den Grund:*

> „Testfläche — wird nicht gespeichert" · *Titel:* „Diese Fläche hat kein Speicherziel. Der Plan am
> Objekt wird gespeichert, diese Testfläche nicht."

> **Das ist die Kante, an der ein Anwender sonst eine Stunde Arbeit verliert** — *eine Oberfläche,
> die aussieht wie der Planer und nichts sichert, ist die teuerste Art von Attrappe.* **Hier sagt
> sie es von selbst, bevor gezeichnet wird.**

## Der Kurzbefehl

**`Strg+S`** — *im Knopftitel des Zustands `ungespeichert` genannt* (`:53`).

## Was der Anwender beim Export bekommt — und was nicht

```text
app/HausplanerApp.tsx:664  exportPng()
                     :667  stage.toDataURL({ pixelRatio: 2 })    doppelte Aufloesung
                     :670  Dateiname fest: 'grundriss.png'
```

> **Ein Bild in doppelter Auflösung, mit festem Namen, im Download-Ordner.** *Kein Dialog, keine
> Wahl des Ausschnitts, keine Wahl des Namens.*
>
> ***Und ausdrücklich: keine Sicherung.*** *Was hier herausgeht, kann der Planer nicht wieder
> hereinholen* — **wer den Export für ein Backup hält, hat eines, das sich nicht zurückladen
> lässt.** *Siehe `7-GRENZEN`.*

## Was der Anwender NICHT kann

- **Einen alten Stand aus der Oberfläche zurückholen.** *Der Rückweg `StelleSnapshotWieder`
  existiert (`83 Z.`), aber die Bedienung dazu ist nicht Gegenstand dieses Blattes — hier ist nur
  gemessen, dass sie in der Insel nicht liegt.*
- **Nur einen Teil speichern.** *Gespeichert wird immer die ganze Szene* (`2-FUNKTION`).
- **Beim Konflikt zusammenführen.** *Die einzige angebotene Handlung ist neu laden.*
